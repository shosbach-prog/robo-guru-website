<?php
/**
 * Plugin Name: RG Forum Design Extension
 * Description: Saubere Forum-Sidebar mit Produktkachel (nur bei Verknüpfung), BuddyBoss/bbPress kompatibel.
 * Version: 2.1.2
 * Author: Robo-Guru
 */

if (!defined('ABSPATH')) exit;

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

        wp_enqueue_style('rgfde', plugins_url('assets/rgfde.css', __FILE__), [], '2.1.2');
        wp_enqueue_script('rgfde', plugins_url('assets/rgfde.js', __FILE__), ['jquery'], '2.1.2', true);

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
