<?php
/**
 * Plugin Name: RG Robot Profile + Forum Design
 * Description: Kombiniertes Plugin: Terra-inspirierte Produktseite (Galerie, KPIs, Specs, Video-Lightbox, FAQ-Accordion) + Roboter-Archiv mit Suche, Sortierung & Vergleich (bis 3) + Forum-Sidebar mit Produktkachel (BuddyBoss/bbPress kompatibel).
 * Version: 3.0.0
 * Author: Robo-Guru
 */
if (!defined('ABSPATH')) exit;

/* =========================================================
 * TEIL 1: RG Robot Profile – Terra Layout + Archive Compare
 * ========================================================= */

class RG_Robot_Profile_Combined {
  const VERSION = '3.0.0';

  public static function detect_post_type(){
    if (post_type_exists('roboter')) return 'roboter';
    if (post_type_exists('robo_robot')) return 'robo_robot';
    return 'roboter';
  }

  public function __construct(){
    add_action('wp_enqueue_scripts', [$this,'assets']);

    add_action('wp_enqueue_scripts', [$this,'dequeue_rankmath_frontend'], 999);
    add_action('wp_ajax_rg_forum_meta', [$this,'ajax_forum_meta']);
    add_action('wp_ajax_nopriv_rg_forum_meta', [$this,'ajax_forum_meta']);

    add_action('admin_enqueue_scripts', [$this,'admin_assets']);
    add_action('wp_ajax_rg_robot_profile_save_meta', [$this,'ajax_save_meta']);
    add_action('template_redirect', [$this,'redirect_single_filters']);
    add_filter('template_include', [$this,'template_include']);
    add_action('add_meta_boxes', [$this,'add_metaboxes']);
    add_action('save_post', [$this,'save_metabox'], 10, 2);
  }

  public function assets(){
    $pt = self::detect_post_type();

    // Robust: CSS/JS immer laden auf Single/Archive/Page roboter
    if (
      (is_singular() && get_post_type() === $pt) ||
      is_post_type_archive($pt) ||
      is_page('roboter')
    ){
      wp_enqueue_style('rg-ui', plugin_dir_url(__FILE__).'assets/rg-ui.css', [], self::VERSION);
      wp_enqueue_script('rg-ui', plugin_dir_url(__FILE__).'assets/rg-ui.js', [], self::VERSION, true);
      wp_localize_script('rg-ui','rgAjax',[ 'ajaxurl'=>admin_url('admin-ajax.php') ]);
    }
  }

  public function dequeue_rankmath_frontend(){
    $pt = self::detect_post_type();
    if (!(is_singular() && get_post_type() === $pt)) return;

    // Remove RankMath front-end assets on robot profiles (prevents extra JS/CSS + conflicts)
    global $wp_scripts, $wp_styles;
    if ($wp_scripts && !empty($wp_scripts->queue)){
      foreach($wp_scripts->queue as $h){
        if (stripos($h,'rank-math') !== false || stripos($h,'rank_math') !== false){
          wp_dequeue_script($h);
          wp_deregister_script($h);
        }
      }
    }
    if ($wp_styles && !empty($wp_styles->queue)){
      foreach($wp_styles->queue as $h){
        if (stripos($h,'rank-math') !== false || stripos($h,'rank_math') !== false){
          wp_dequeue_style($h);
          wp_deregister_style($h);
        }
      }
    }
  }

  public function ajax_forum_meta(){
    // Returns lightweight meta for a forum topic URL (bbPress / CPT topic / comments fallback)
    $url = isset($_POST['url']) ? esc_url_raw(wp_unslash($_POST['url'])) : '';
    if (!$url){
      wp_send_json_error(['message'=>'missing url']);
    }
    $post_id = url_to_postid($url);
    if (!$post_id){
      wp_send_json_success(['found'=>false]);
    }
    $post = get_post($post_id);
    if (!$post){
      wp_send_json_success(['found'=>false]);
    }

    $data = ['found'=>true,'post_id'=>$post_id,'post_type'=>$post->post_type];

    // bbPress topic?
    if (function_exists('bbp_get_topic_reply_count') && $post->post_type === 'topic'){
      $data['replies'] = (int) bbp_get_topic_reply_count($post_id);
      $last_reply_id = function_exists('bbp_get_topic_last_reply_id') ? (int) bbp_get_topic_last_reply_id($post_id) : 0;
      $last_id = $last_reply_id ?: $post_id;
      $data['last_title'] = get_the_title($last_id);
      $data['last_time']  = get_the_date('d.m.Y', $last_id);
      $data['last_url']   = get_permalink($last_id);
      wp_send_json_success($data);
    }

    // Comments fallback (if forum uses comments)
    $c_count = (int) get_comments_number($post_id);
    $data['replies'] = max(0, $c_count - 1);
    $last = get_comments(['post_id'=>$post_id,'number'=>1,'status'=>'approve','orderby'=>'comment_date_gmt','order'=>'DESC']);
    if ($last){
      $data['last_time'] = mysql2date('d.m.Y', $last[0]->comment_date);
      $data['last_title']= wp_trim_words(strip_tags($last[0]->comment_content), 8, '…');
      $data['last_url']  = get_permalink($post_id).'#comment-'.$last[0]->comment_ID;
    } else {
      $data['last_time'] = get_the_date('d.m.Y', $post_id);
      $data['last_title']= get_the_title($post_id);
      $data['last_url']  = get_permalink($post_id);
    }
    wp_send_json_success($data);
  }


  public function template_include($template){
    $pt = self::detect_post_type();

    if (is_singular() && get_post_type() === $pt){
      return plugin_dir_path(__FILE__).'templates/single.php';
    }

    // /roboter kann Page oder CPT-Archive sein
    if (is_post_type_archive($pt) || is_page('roboter')){
      return plugin_dir_path(__FILE__).'templates/archive.php';
    }

    return $template;
  }

public function redirect_single_filters(){
  $pt = self::detect_post_type();
  if (!(is_singular() && get_post_type() === $pt)) return;

  // If user hits single with filter params, redirect to archive instead
  $keys = ['mfg','seg','q','sort','compare'];
  $has = false;
  foreach($keys as $k){
    if (isset($_GET[$k]) && $_GET[$k] !== '') { $has = true; break; }
  }
  if (!$has) return;

  $archive = get_post_type_archive_link($pt);
  if (!$archive){
    $page = get_page_by_path('roboter');
    if ($page) $archive = get_permalink($page->ID);
  }
  if (!$archive) return;

  // Keep only known params
  $args = [];
  foreach($keys as $k){
    if (isset($_GET[$k]) && $_GET[$k] !== '') $args[$k] = sanitize_text_field(wp_unslash($_GET[$k]));
  }

  $url = add_query_arg($args, $archive);
  wp_safe_redirect($url, 301);
  exit;
}


  public function add_metaboxes(){
    $pt = self::detect_post_type();
    add_meta_box('rg_robot_recommend_faq','Robo-Guru: Ideal / FAQ / Video',[$this,'render_metabox'],$pt,'normal','high');
  }

  public function render_metabox($post){
    wp_nonce_field('rg_robot_recommend_faq_save','rg_robot_recommend_faq_nonce');

    $ideal = (string) get_post_meta($post->ID,'rg_ideal_for',true);
    $not_ideal = (string) get_post_meta($post->ID,'rg_not_ideal_for',true);
    $faq = (string) get_post_meta($post->ID,'rg_faq_raw',true);
    $video = (string) get_post_meta($post->ID,'rg_video_url',true);
    $dock = get_post_meta($post->ID,'rg_docking_options',true);
    if (!is_array($dock)) $dock = [];

    echo '<style>
      .rgmb-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
      .rgmb-grid textarea{width:100%;min-height:140px;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace}
      .rgmb-full{grid-column:1 / -1}
      .rgmb-help{color:#6b7280;margin:6px 0 0;font-size:12px}
      input.rgmb-input{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:10px}
      @media(max-width:980px){.rgmb-grid{grid-template-columns:1fr}}
    </style>';

    $dock_options = [
      'dockingstation' => 'Dockingstation',
      'charging_station' => 'Charging Station',
      'self_cleaning_dock' => 'Selbstreinigende Dockingstation',
      'mobile_water_tank' => 'Mobile Wassertank',
    ];

    echo '<div class="rgmb-grid">';
    echo '<div><label><strong>Ideal für</strong> (je Zeile ein Punkt)</label><textarea name="rg_ideal_for">'.esc_textarea($ideal).'</textarea><div class="rgmb-help">Zeilen werden als Bulletpoints ausgegeben.</div></div>';
    echo '<div><label><strong>Nicht ideal für</strong> (je Zeile ein Punkt)</label><textarea name="rg_not_ideal_for">'.esc_textarea($not_ideal).'</textarea><div class="rgmb-help">Zeilen werden als Bulletpoints ausgegeben.</div></div>';
    echo '<div class="rgmb-full"><label><strong>Video-URL</strong> (YouTube oder Vimeo)</label><input class="rgmb-input" type="url" name="rg_video_url" value="'.esc_attr($video).'" placeholder="https://..."><div class="rgmb-help">Wird nach „Aufgabenprofil & Kernkompetenzen" angezeigt und öffnet in einer Lightbox.</div></div>';
echo '<div class="rgmb-full"><label><strong>Docking / Stationen</strong> (Multi-Select)</label>
  <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:8px">';
foreach($dock_options as $key=>$label){
  $checked = in_array($key, $dock, true) ? 'checked' : '';
  echo '<label style="display:flex;gap:8px;align-items:center;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:10px 12px">
          <input type="checkbox" name="rg_docking_options[]" value="'.esc_attr($key).'" '.$checked.'>
          <span>'.esc_html($label).'</span>
        </label>';
}
echo '</div><div class="rgmb-help">Wird im Frontend als Chips angezeigt.</div></div>';
    echo '<div class="rgmb-full"><label><strong>FAQ (Rohtext)</strong> – Frage in Zeile 1, Antwort darunter. Q/A-Blöcke durch Leerzeile trennen.</label><textarea name="rg_faq_raw" style="min-height:220px">'.esc_textarea($faq).'</textarea><div class="rgmb-help">Format: Frage ↵ Antwort ↵↵ nächste Frage ↵ Antwort</div></div>';
    echo '</div>';
  }

  public function save_metabox($post_id, $post){
    if (!is_object($post)) return;
    $pt = self::detect_post_type();
    if ($post->post_type !== $pt) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (!isset($_POST['rg_robot_recommend_faq_nonce']) || !wp_verify_nonce($_POST['rg_robot_recommend_faq_nonce'], 'rg_robot_recommend_faq_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, 'rg_ideal_for', sanitize_textarea_field(wp_unslash($_POST['rg_ideal_for'] ?? '')));
    update_post_meta($post_id, 'rg_not_ideal_for', sanitize_textarea_field(wp_unslash($_POST['rg_not_ideal_for'] ?? '')));
    update_post_meta($post_id, 'rg_faq_raw', sanitize_textarea_field(wp_unslash($_POST['rg_faq_raw'] ?? '')));
    update_post_meta($post_id, 'rg_video_url', esc_url_raw(wp_unslash($_POST['rg_video_url'] ?? '')));
  }

public function admin_assets($hook){
  $pt = self::detect_post_type();
  if (!in_array($hook, ['post.php','post-new.php'], true)) return;
  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if (!$screen || $screen->post_type !== $pt) return;

  wp_enqueue_script('rg-admin', plugin_dir_url(__FILE__).'assets/rg-admin.js', ['jquery'], self::VERSION, true);
  wp_localize_script('rg-admin','RGRobotProfile',[
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('rg_robot_profile_ajax'),
  ]);
}

public function ajax_save_meta(){
  if (!current_user_can('edit_posts')) wp_send_json_error(['msg'=>'forbidden'], 403);
  check_ajax_referer('rg_robot_profile_ajax','nonce');

  $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;
  if (!$post_id || !current_user_can('edit_post', $post_id)) wp_send_json_error(['msg'=>'no access'], 403);

  $pt = self::detect_post_type();
  if (get_post_type($post_id) !== $pt) wp_send_json_error(['msg'=>'wrong post type'], 400);

  $ideal = isset($_POST['rg_ideal_for']) ? sanitize_textarea_field(wp_unslash($_POST['rg_ideal_for'])) : '';
  $not_ideal = isset($_POST['rg_not_ideal_for']) ? sanitize_textarea_field(wp_unslash($_POST['rg_not_ideal_for'])) : '';
  $faq = isset($_POST['rg_faq_raw']) ? sanitize_textarea_field(wp_unslash($_POST['rg_faq_raw'])) : '';
  $video = isset($_POST['rg_video_url']) ? esc_url_raw(wp_unslash($_POST['rg_video_url'])) : '';

  $dock_in = isset($_POST['rg_docking_options']) ? (array) wp_unslash($_POST['rg_docking_options']) : [];
  $dock_in = array_values(array_filter(array_map('sanitize_key', $dock_in)));

  update_post_meta($post_id, 'rg_ideal_for', $ideal);
  update_post_meta($post_id, 'rg_not_ideal_for', $not_ideal);
  update_post_meta($post_id, 'rg_faq_raw', $faq);
  update_post_meta($post_id, 'rg_video_url', $video);
  update_post_meta($post_id, 'rg_docking_options', $dock_in);

  wp_send_json_success(['saved'=>true]);
}

}
new RG_Robot_Profile_Combined();

add_action('add_meta_boxes', function(){
  add_meta_box('rg_sureforms_shortcode', 'SureForms Shortcode (Beratung)', function($post){
    $val = get_post_meta($post->ID, 'rg_sureforms_shortcode', true);
    echo '<p style="margin:0 0 8px;color:#666;">Beispiel: <code>[sureforms id=&quot;13734&quot;]</code></p>';
    echo '<input type="text" style="width:100%;" name="rg_sureforms_shortcode" value="'.esc_attr($val).'" placeholder="[sureforms id=&quot;13734&quot;]">';
  }, 'roboter', 'side', 'default');
});

add_action('save_post_roboter', function($post_id){
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if(!current_user_can('edit_post', $post_id)) return;
  if(isset($_POST['rg_sureforms_shortcode'])){
    update_post_meta($post_id, 'rg_sureforms_shortcode', sanitize_text_field(wp_unslash($_POST['rg_sureforms_shortcode'])));
  }
});


/* =========================================================
 * TEIL 2: RG Forum Design Extension
 * ========================================================= */

class RG_Forum_Design_Extension {

    const OPT = 'rgfde_settings';

    public static function defaults() {
        return [
            'scope_path' => '/community/forum/',
            'enable_product_tile' => 1,
            'enable_slug_match' => 1,
            'slug_match_post_type' => 'robo_robot',
            'linked_robot_meta_key' => '_rgfde_linked_robot_id',
        ];
    }

    public static function settings() {
        $d = self::defaults();
        $s = get_option(self::OPT, []);
        return wp_parse_args($s, $d);
    }

    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue']);
        add_action('add_meta_boxes', [__CLASS__, 'metabox']);
        add_action('save_post_forum', [__CLASS__, 'save_metabox']);
    }

    public static function enqueue() {
        if (!is_singular('forum')) return;

        $s = self::settings();
        $path = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($path, $s['scope_path']) === false) return;

        wp_enqueue_style('rgfde', plugins_url('assets/rgfde.css', __FILE__), [], '3.0.0');
        wp_enqueue_script('rgfde', plugins_url('assets/rgfde.js', __FILE__), ['jquery'], '3.0.0', true);

        wp_localize_script('rgfde', 'RGFDE', [
            'html' => self::render_product_tile(),
        ]);
    }

    /* ---------- Forum Helpers ---------- */

    public static function forum_id() {
        if (function_exists('bbp_get_forum_id')) {
            $id = bbp_get_forum_id();
            if ($id) return (int)$id;
        }
        return get_queried_object_id();
    }

    public static function forum_slug() {
        $id = self::forum_id();
        if ($id) {
            $p = get_post($id);
            if ($p) return $p->post_name;
        }
        return '';
    }

    /* ---------- Product Tile ---------- */

    public static function render_product_tile() {
        $s = self::settings();
        if (!$s['enable_product_tile']) return '';

        $fid = self::forum_id();
        if (!$fid) return '';

        // Option A: linked robot ID
        $rid = (int)get_post_meta($fid, $s['linked_robot_meta_key'], true);
        if ($rid) {
            $p = get_post($rid);
            if ($p && $p->post_status === 'publish') {
                return self::tile_from_post($p);
            }
        }

        // Option B: slug match
        if ($s['enable_slug_match']) {
            $slug = self::forum_slug();
            if ($slug) {
                $p = get_page_by_path($slug, OBJECT, $s['slug_match_post_type']);
                if ($p && $p->post_status === 'publish') {
                    return self::tile_from_post($p);
                }
            }
        }

        return '';
    }

    private static function extract_highlights($p, $max = 3) {
    $high = [];
    if (!empty($p->post_excerpt)) {
        $sent = preg_split('/[\.\!\?]+/', wp_strip_all_tags($p->post_excerpt));
        foreach ($sent as $s) {
            $s = trim($s);
            if ($s) $high[] = esc_html($s);
            if (count($high) >= $max) break;
        }
    }
    if (count($high) < $max && !empty($p->post_content)) {
        if (preg_match('/<ul[^>]*>(.*?)<\/ul>/is', $p->post_content, $m)) {
            if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $m[1], $lis)) {
                foreach ($lis[1] as $li) {
                    $li = trim(wp_strip_all_tags($li));
                    if ($li) $high[] = esc_html($li);
                    if (count($high) >= $max) break;
                }
            }
        }
    }
    return array_slice($high, 0, $max);
}

private static function tile_from_post($p) {
    $title = esc_html(get_the_title($p));
    $url = esc_url(get_permalink($p));
    $img = has_post_thumbnail($p) ? esc_url(get_the_post_thumbnail_url($p, 'large')) : '';

    $high = self::extract_highlights($p, 3);

    $forum_url = '';
    $fid = self::forum_id();
    if ($fid) $forum_url = esc_url(get_permalink($fid));
    $new_topic_url = $forum_url ? $forum_url . '#new-post' : '';

    ob_start(); ?>
    <aside class="rgfde-sidebar" aria-label="Produktkachel">
      <div class="rgfde-card">
        <?php if ($img): ?><img src="<?php echo $img; ?>" alt="<?php echo $title; ?>"><?php endif; ?>
        <div class="rgfde-kicker">Passender Roboter zur Diskussion</div>
        <h3 class="rgfde-title"><?php echo $title; ?></h3>

        <?php if ($high): ?>
          <ul class="rgfde-highlights">
            <?php foreach ($high as $h): ?><li><?php echo $h; ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <div class="rgfde-actions">
          <a class="rgfde-btn primary" href="<?php echo $url; ?>"><span class="rgfde-btn-ico" aria-hidden="true">🔎</span><span>Zur Roboter-Detailseite</span></a>
          <?php if ($new_topic_url): ?>
            <a class="rgfde-btn secondary" href="<?php echo esc_url($new_topic_url); ?>"><span class="rgfde-btn-ico" aria-hidden="true">💬</span><span>Forum-Thema öffnen</span></a>
          <?php endif; ?>
        </div>
      </div>
    </aside>
    <?php
    return ob_get_clean();
}

    /* ---------- Metabox ---------- */

    public static function metabox() {
        add_meta_box(
            'rgfde_link',
            'RG: Verknüpfter Roboter',
            [__CLASS__, 'metabox_html'],
            'forum',
            'side'
        );
    }

    public static function metabox_html($post) {
        $s = self::settings();
        $val = (int)get_post_meta($post->ID, $s['linked_robot_meta_key'], true);
        wp_nonce_field('rgfde_save', 'rgfde_nonce');

        $robots = get_posts([
            'post_type' => $s['slug_match_post_type'],
            'post_status' => 'publish',
            'numberposts' => 200,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        echo '<select style="width:100%" name="rgfde_robot">';
        echo '<option value="0">— Kein Roboter verknüpft —</option>';
        foreach ($robots as $r) {
            printf(
                '<option value="%d"%s>%s (%s)</option>',
                $r->ID,
                selected($val, $r->ID, false),
                esc_html($r->post_title),
                esc_html($r->post_name)
            );
        }
        echo '</select>';
        echo '<p class="description">Produktkachel erscheint nur bei Verknüpfung.</p>';
    }

    public static function save_metabox($post_id) {
        if (!isset($_POST['rgfde_nonce']) || !wp_verify_nonce($_POST['rgfde_nonce'], 'rgfde_save')) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $s = self::settings();
        $rid = isset($_POST['rgfde_robot']) ? (int)$_POST['rgfde_robot'] : 0;

        if ($rid) update_post_meta($post_id, $s['linked_robot_meta_key'], $rid);
        else delete_post_meta($post_id, $s['linked_robot_meta_key']);
    }
}

RG_Forum_Design_Extension::init();
