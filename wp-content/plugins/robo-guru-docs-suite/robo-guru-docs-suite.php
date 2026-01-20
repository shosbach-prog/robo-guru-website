<?php
/**
 * Plugin Name: Robo-Guru Dokumente Suite
 * Description: Kombiniert (1) Dokumente/Downloads je Robo-Robot (Post-Type: robo_robot) per Medien-Auswahl und (2) einen eigenen Dokumente-CPT mit Kategorien + Shortcode-Ausgabe.
 * Version: 2.8.0
 * Author: Robo-Guru
 * Text Domain: robo-guru-docs-suite
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class RG_Docs_Suite {
  const VERSION = '2.8.0';

  // --- Feature A (robo_robot attachments) -----------------------------
  const META_ROBOT_DOC_IDS = '_rf_doc_ids';
  const META_ROBOT_CPT_DOC_IDS = '_rg_robot_doc_post_ids';
  const META_ROBOT_DOC_MODE = '_rg_robot_doc_mode'; // bb | cpt | media | both | all
  const META_ROBOT_BB_DOC_IDS = '_rg_robot_bb_doc_ids'; // BuddyBoss Document IDs
  const META_ROBOT_BB_USER_ID = '_rg_robot_bb_user_id'; // Source BuddyBoss user id (optional)
  const META_ROBOT_BB_FOLDER_IDS = '_rg_robot_bb_folder_ids'; // BuddyBoss Folder IDs
  const META_ROBOT_BB_FOLDER_RECURSIVE = '_rg_robot_bb_folder_recursive'; // 1|0 include subfolders


  // --- Feature B (CPT documents) --------------------------------------
  const CPT = 'robot_documents';
  const TAX = 'document_category';
  const META_DOC_FILE = '_rg_document_file';

  public function __construct() {
    // Feature A
    add_action('add_meta_boxes', array($this, 'add_robot_metabox'));
    add_action('save_post_robo_robot', array($this, 'save_robot_docs'), 10, 2);
    add_action('admin_enqueue_scripts', array($this, 'robot_admin_assets'));
    add_action('wp_enqueue_scripts', array($this, 'robot_frontend_assets'));
    add_shortcode('rg_robot_documents', array($this, 'shortcode_robot_documents'));
    add_filter('the_content', array($this, 'append_robot_documents_to_content'), 25);

    // BuddyBoss docs search (admin ajax)
    add_action('wp_ajax_rg_bb_doc_search', array($this, 'ajax_bb_doc_search'));
    add_action('wp_ajax_rg_bb_folder_search', array($this, 'ajax_bb_folder_search'));
    // Secure download proxy (avoids WAF/403 on direct upload URLs)
    add_action('wp_ajax_rg_docs_download', array($this, 'ajax_docs_download'));
    add_action('wp_ajax_nopriv_rg_docs_download', array($this, 'ajax_docs_download'));
    // Cache / stats / admin pages
    add_action('wp_ajax_rg_docs_clear_cache', array($this, 'ajax_clear_cache'));
    add_action('admin_menu', array($this, 'register_admin_pages'));
    add_action('admin_post_rg_docs_export_csv', array($this, 'handle_export_csv'));


    // Feature B
    add_action('init', array($this, 'register_cpt_and_tax'));
    add_action('add_meta_boxes', array($this, 'add_document_metabox'));
    add_action('save_post_' . self::CPT, array($this, 'save_document_file'));
    add_action('wp_enqueue_scripts', array($this, 'docs_frontend_assets'));
    add_shortcode('rg_documents', array($this, 'shortcode_documents'));

    register_activation_hook(__FILE__, array($this, 'on_activate'));
  }

  public function on_activate() {
    // Ensure CPT/tax exist before inserting terms
    $this->register_cpt_and_tax();
    $this->ensure_default_terms();
    flush_rewrite_rules(false);
  }

  // =========================================================
  // Feature A: Attach Media docs to robo_robot
  // =========================================================
  public function add_robot_metabox(){
    add_meta_box(
      'rg_robot_documents',
      'Dokumente (Downloads)',
      array($this, 'render_robot_metabox'),
      'robo_robot',
      'side',
      'default'
    );
  }

  public function robot_admin_assets($hook){
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'robo_robot') return;

    wp_enqueue_media();

    wp_enqueue_script(
      'rg-robot-docs-admin',
      plugin_dir_url(__FILE__) . 'assets/admin-docs.js',
      array('jquery','jquery-ui-sortable'),
      self::VERSION,
      true
    );

    wp_enqueue_style(
      'rg-robot-docs-admin',
      plugin_dir_url(__FILE__) . 'assets/admin-docs.css',
      array(),
      self::VERSION
    );
  }

  public function render_robot_metabox($post){
    $ids = get_post_meta($post->ID, self::META_ROBOT_DOC_IDS, true);
    $ids = is_string($ids) ? trim($ids) : '';

    $mode = get_post_meta($post->ID, self::META_ROBOT_DOC_MODE, true);
    $mode = is_string($mode) ? trim($mode) : '';
    if (!in_array($mode, array('bb','cpt','media','both','all'), true)) $mode = 'all';

    $bb_doc_ids = get_post_meta($post->ID, self::META_ROBOT_BB_DOC_IDS, true);
    $bb_doc_ids = is_string($bb_doc_ids) ? trim($bb_doc_ids) : '';

    $bb_user_id = (int) get_post_meta($post->ID, self::META_ROBOT_BB_USER_ID, true);
    if ($bb_user_id <= 0) $bb_user_id = get_current_user_id();

    $bb_folder_ids = get_post_meta($post->ID, self::META_ROBOT_BB_FOLDER_IDS, true);
    $bb_folder_ids = is_string($bb_folder_ids) ? trim($bb_folder_ids) : '';

    $bb_recursive = get_post_meta($post->ID, self::META_ROBOT_BB_FOLDER_RECURSIVE, true);
    $bb_recursive = ($bb_recursive === '' || $bb_recursive === null) ? '1' : (string)$bb_recursive;
    $bb_recursive = ($bb_recursive === '0') ? '0' : '1';

    wp_nonce_field('rg_robot_docs_save', 'rg_robot_docs_nonce');
    ?>
    <div class="rg-docs-admin">
      <p class="description">Wähle PDFs/Anleitungen/Datenblätter aus der Mediathek. Diese erscheinen im Frontend als Download-Links.</p>

      <input type="hidden" id="rg_doc_ids" name="rg_doc_ids" value="<?php echo esc_attr($ids); ?>" />

      <div id="rg_doc_list" class="rg-doc-list">
        <?php
          if ($ids) {
            $arr = array_filter(array_map('absint', preg_split('/[,\s]+/', $ids)));
            foreach ($arr as $aid) {
              $url = wp_get_attachment_url($aid);
              if (!$url) continue;
              $title = get_the_title($aid);
              $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
              ?>
              <div class="rg-doc" data-id="<?php echo esc_attr($aid); ?>">
                <span class="rg-doc__drag">↕</span>
                <span class="rg-doc__name"><?php echo esc_html($title); ?></span>
                <?php if ($ext): ?><span class="rg-doc__meta"><?php echo esc_html(strtoupper($ext)); ?></span><?php endif; ?>
                <a href="#" class="button button-small rg-doc__remove">Entfernen</a>
              </div>
              <?php
            }
          }
        ?>
      </div>

      <div class="rg-docs-actions">
        <button type="button" class="button button-primary" id="rg_add_docs">Dokument(e) auswählen</button>
        <button type="button" class="button" id="rg_clear_docs">Alle entfernen</button>

        <div style="margin-top:10px; padding-top:10px; border-top:1px dashed rgba(0,0,0,.12);">
          <div style="font-weight:700; margin-bottom:6px;">Ausgabe-Modus</div>
          <label style="display:block; margin:0 0 6px;"><input type="radio" name="rg_doc_mode" value="bb" <?php checked($mode, 'bb'); ?> /> Nur BuddyBoss (Mitglieder &rarr; Documents)</label>
          <label style="display:block; margin:0 0 6px;"><input type="radio" name="rg_doc_mode" value="cpt" <?php checked($mode, 'cpt'); ?> /> Nur CPT (Dokumente-Bereich)</label>
          <label style="display:block; margin:0 0 6px;"><input type="radio" name="rg_doc_mode" value="media" <?php checked($mode, 'media'); ?> /> Nur Media (Mediathek)</label>
          <label style="display:block; margin:0 0 6px;"><input type="radio" name="rg_doc_mode" value="both" <?php checked($mode, 'both'); ?> /> CPT + Media</label>
          <label style="display:block; margin:0 0 0;"><input type="radio" name="rg_doc_mode" value="all" <?php checked($mode, 'all'); ?> /> Alles (BuddyBoss + CPT + Media)</label>
          <p class="description" style="margin:8px 0 0;">Tipp: Wenn du deine Ordner bereits in <strong>BuddyBoss &raquo; Documents</strong> pflegst, wähle „Nur BuddyBoss“ und verknüpfe dort die Dateien.</p>
        </div>
      

<hr id="rg_docs_divider_bb" style="margin:14px 0;" />


        <div style="margin-top:10px;">
          <button type="button" class="button" id="rg_docs_clear_cache" data-post="<?php echo esc_attr($post->ID); ?>">Cache löschen</button>
          <span class="description" style="margin-left:8px;">Falls neue Dateien/Ordner nicht sofort erscheinen.</span>
          <input type="hidden" id="rg_docs_clear_cache_nonce" value="<?php echo esc_attr(wp_create_nonce('rg_docs_clear_cache')); ?>" />
        </div>

<div id="rg_bb_docs_block" class="rg-docs-section rg-docs-section--bb">
  <p class="description" style="margin-top:0;">
    <strong>BuddyBoss-Quelle:</strong> Suche und verknüpfe Dokumente aus <em>Mitglieder → Documents</em> (inkl. Ordner). Im Frontend wird der Ordnerpfad angezeigt.
  </p>

<input type="hidden" id="rg_bb_folder_ids" name="rg_bb_folder_ids" value="<?php echo esc_attr($bb_folder_ids); ?>" />
<div style="margin:10px 0 8px; padding:10px 0 0; border-top:1px dashed rgba(0,0,0,.12);">
  <div style="font-weight:700; margin-bottom:6px;">Ordner verknüpfen (statt einzelner Dokumente)</div>
  <label style="display:block; margin:0 0 8px;">
    <input type="checkbox" id="rg_bb_folder_recursive" name="rg_bb_folder_recursive" value="1" <?php checked($bb_recursive, '1'); ?> />
    Unterordner automatisch mit einbeziehen
  </label>

  <input type="text" id="rg_bb_folder_search" placeholder="BuddyBoss Ordner suchen…" style="width:100%;" autocomplete="off" />
  <div id="rg_bb_folder_results" class="rg-bb-results" style="margin-top:8px;"></div>

  <div id="rg_bb_folder_list" class="rg-doc-list" style="margin-top:10px;">
    <?php
      $bb_folders_sel = array_filter(array_map('absint', preg_split('/[,\s]+/', $bb_folder_ids)));
      if ($bb_folders_sel && class_exists('BP_Document_Folder')) {
        foreach ($bb_folders_sel as $fid) {
          $f = new BP_Document_Folder($fid);
          if (empty($f->id)) continue;
          $path = $this->bb_folder_path((int)$f->id);
          $privacy = !empty($f->privacy) ? (string)$f->privacy : '';
          ?>
          <div class="rg-doc rg-doc--bb-folder" data-id="<?php echo esc_attr($f->id); ?>" data-privacy="<?php echo esc_attr($privacy); ?>">
            <span class="rg-doc__drag">↕</span>
            <span class="rg-doc__name"><?php echo esc_html($f->title); ?></span>
            <?php if ($path): ?><span class="rg-doc__meta"><?php echo esc_html($path); ?></span><?php endif; ?>
            <?php if ($privacy): ?><span class="rg-doc__meta"><?php echo esc_html($privacy); ?></span><?php endif; ?>
            <a href="#" class="button button-small rg-doc__remove">Entfernen</a>
          </div>
          <?php
        }
      }
    ?>
  </div>
</div>


  <input type="hidden" id="rg_bb_doc_ids" name="rg_bb_doc_ids" value="<?php echo esc_attr($bb_doc_ids); ?>" />

  <div style="margin:10px 0 8px;">
    <label style="display:block; font-weight:700; margin-bottom:6px;">BuddyBoss User-ID (Quelle)</label>
    <input type="number" min="1" name="rg_bb_user_id" id="rg_bb_user_id" value="<?php echo esc_attr($bb_user_id); ?>" style="width:100%;" />
    <p class="description" style="margin:6px 0 0;">Standard ist dein aktueller Benutzer. Wenn deine Dokumente zentral unter „admin“ liegen, trage hier dessen User-ID ein.</p>
  </div>

  <?php $bb_nonce = wp_create_nonce('rg_bb_doc_search'); ?>
  <div class="rg-docs-actions" style="margin-top:10px;">
    <input type="text" id="rg_bb_search" placeholder="BuddyBoss Dokumente suchen…" style="width:100%;" autocomplete="off" />
    <input type="hidden" id="rg_bb_nonce" value="<?php echo esc_attr($bb_nonce); ?>" />
    <div id="rg_bb_results" class="rg-bb-results" style="margin-top:8px;"></div>
    <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
      <button type="button" class="button" id="rg_bb_clear">Alle entfernen</button>
    </div>
  </div>

  <div id="rg_bb_doc_list" class="rg-doc-list" style="margin-top:10px;">
    <?php
      $bb_selected = array_filter(array_map('absint', preg_split('/[,\s]+/', $bb_doc_ids)));
      if ($bb_selected && class_exists('BP_Document')) {
        foreach ($bb_selected as $bbid) {
          $doc = new BP_Document($bbid);
          if (empty($doc->id)) continue;
          $label = $doc->title ? $doc->title : ('Dokument #' . $doc->id);
          $folder_path = $this->bb_folder_path((int)$doc->folder_id);
          ?>
          <div class="rg-doc rg-doc--bb" data-id="<?php echo esc_attr($doc->id); ?>" data-folder="<?php echo esc_attr($folder_path); ?>">
            <span class="rg-doc__drag">↕</span>
            <span class="rg-doc__name"><?php echo esc_html($label); ?></span>
            <?php if ($folder_path): ?><span class="rg-doc__meta"><?php echo esc_html($folder_path); ?></span><?php endif; ?>
            <a href="#" class="button button-small rg-doc__remove">Entfernen</a>
          </div>
          <?php
        }
      }
    ?>
  </div>
</div><!-- /#rg_bb_docs_block -->

<hr id="rg_docs_divider" style="margin:14px 0;" />

<div id="rg_cpt_docs_block" class="rg-docs-section rg-docs-section--cpt">

<?php
  $doc_post_ids = get_post_meta($post->ID, self::META_ROBOT_CPT_DOC_IDS, true);
  $doc_post_ids = is_string($doc_post_ids) ? trim($doc_post_ids) : '';
  $selected_posts = array_filter(array_map('absint', preg_split('/[,\s]+/', $doc_post_ids)));
  $all_docs = get_posts(array(
    'post_type' => self::CPT,
    'post_status' => array('publish','draft','pending','private'),
    'numberposts' => 200,
    'orderby' => 'title',
    'order' => 'ASC',
  ));
?>
<p class="description" style="margin-top:0;">
  <strong>Alternative:</strong> Verknüpfe Dokumente aus dem Dokumente-Bereich (CPT). Vorteil: eigene Kategorien, zentrale Pflege.
</p>

<input type="hidden" id="rg_doc_post_ids" name="rg_doc_post_ids" value="<?php echo esc_attr($doc_post_ids); ?>" />

<div class="rg-docs-actions" style="margin-top:10px;">
  <select id="rg_doc_post_select" style="width:100%; max-width:100%;">
    <option value="">— Dokument auswählen —</option>
    <?php foreach($all_docs as $d): ?>
      <option value="<?php echo esc_attr($d->ID); ?>">
        <?php echo esc_html(get_the_title($d) ?: ('Dokument #' . $d->ID)); ?>
      </option>
    <?php endforeach; ?>
  </select>
  <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
    <button type="button" class="button button-primary" id="rg_add_doc_post">Hinzufügen</button>
    <button type="button" class="button" id="rg_clear_doc_posts">Alle entfernen</button>
  </div>
</div>

<div id="rg_doc_post_list" class="rg-doc-list" style="margin-top:10px;">
  <?php
    if ($selected_posts) {
      foreach ($selected_posts as $did) {
        $p = get_post($did);
        if (!$p || $p->post_type !== self::CPT) continue;
        $file = get_post_meta($did, self::META_DOC_FILE, true);
        $ext = $file ? pathinfo(parse_url($file, PHP_URL_PATH), PATHINFO_EXTENSION) : '';
        ?>
        <div class="rg-doc rg-doc--post" data-id="<?php echo esc_attr($did); ?>">
          <span class="rg-doc__drag">↕</span>
          <span class="rg-doc__name"><?php echo esc_html(get_the_title($did)); ?></span>
          <?php if ($ext): ?><span class="rg-doc__meta"><?php echo esc_html(strtoupper($ext)); ?></span><?php endif; ?>
          <a href="#" class="button button-small rg-doc__remove">Entfernen</a>
        </div>
        <?php
      }
    }
  ?>
</div>
</div>
</div><!-- /#rg_cpt_docs_block -->
    </div>
    <?php
  }

  public function save_robot_docs($post_id, $post){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision($post_id) ) return;
    if ( ! current_user_can('edit_post', $post_id) ) return;

    if ( ! isset($_POST['rg_robot_docs_nonce']) || ! wp_verify_nonce($_POST['rg_robot_docs_nonce'], 'rg_robot_docs_save') ) return;

    $ids = isset($_POST['rg_doc_ids']) ? sanitize_text_field($_POST['rg_doc_ids']) : '';
    $ids = preg_replace('/[^0-9,]/', '', $ids);
    $ids = trim($ids, ',');

    if ($ids) update_post_meta($post_id, self::META_ROBOT_DOC_IDS, $ids);
    else delete_post_meta($post_id, self::META_ROBOT_DOC_IDS);

    // BuddyBoss-linked documents (BuddyBoss Document IDs)
    $bb_doc_ids = isset($_POST['rg_bb_doc_ids']) ? sanitize_text_field($_POST['rg_bb_doc_ids']) : '';
    $bb_doc_ids = preg_replace('/[^0-9,]/', '', $bb_doc_ids);
    $bb_doc_ids = trim($bb_doc_ids, ',');
    if ($bb_doc_ids) update_post_meta($post_id, self::META_ROBOT_BB_DOC_IDS, $bb_doc_ids);
    else delete_post_meta($post_id, self::META_ROBOT_BB_DOC_IDS);

    $bb_user_id = isset($_POST['rg_bb_user_id']) ? absint($_POST['rg_bb_user_id']) : 0;
    if ($bb_user_id > 0) update_post_meta($post_id, self::META_ROBOT_BB_USER_ID, $bb_user_id);
    else delete_post_meta($post_id, self::META_ROBOT_BB_USER_ID);


$bb_folder_ids = isset($_POST['rg_bb_folder_ids']) ? sanitize_text_field($_POST['rg_bb_folder_ids']) : '';
$bb_folder_ids = preg_replace('/[^0-9,]/', '', $bb_folder_ids);
$bb_folder_ids = trim($bb_folder_ids, ',');
if ($bb_folder_ids) update_post_meta($post_id, self::META_ROBOT_BB_FOLDER_IDS, $bb_folder_ids);
else delete_post_meta($post_id, self::META_ROBOT_BB_FOLDER_IDS);

$bb_recursive = isset($_POST['rg_bb_folder_recursive']) ? '1' : '0';
update_post_meta($post_id, self::META_ROBOT_BB_FOLDER_RECURSIVE, $bb_recursive);


// CPT-linked documents (robot_documents)
$doc_post_ids = isset($_POST['rg_doc_post_ids']) ? sanitize_text_field($_POST['rg_doc_post_ids']) : '';
$doc_post_ids = preg_replace('/[^0-9,]/', '', $doc_post_ids);
$doc_post_ids = trim($doc_post_ids, ',');

if ($doc_post_ids) update_post_meta($post_id, self::META_ROBOT_CPT_DOC_IDS, $doc_post_ids);
else delete_post_meta($post_id, self::META_ROBOT_CPT_DOC_IDS);

    // Output mode: cpt | media | both
    $mode = isset($_POST['rg_doc_mode']) ? sanitize_key($_POST['rg_doc_mode']) : 'all';
    if (!in_array($mode, array('bb','cpt','media','both','all'), true)) $mode = 'all';
    update_post_meta($post_id, self::META_ROBOT_DOC_MODE, $mode);
  }

  public 
function robot_frontend_assets(){
    if (!is_singular('robo_robot')) return;

    $css = '
    .rg-docs-wrap{margin-top:22px;padding:16px;border:1px solid rgba(0,0,0,.10);border-radius:16px;background:#fff;box-shadow:0 10px 24px rgba(0,0,0,.05);overflow:visible}
    .rg-docs-title{margin:0 0 12px;font-size:18px;letter-spacing:-.01em}
    .rg-docs-guest-note{
      margin:0 0 12px;
      padding:12px 14px;
      border:1px solid rgba(0,0,0,.10);
      border-radius:14px;
      background:rgba(255,255,255,.85);
      line-height:1.35;
    }
    .rg-docs-guest-note strong{font-weight:800}
    .rg-docs-guest-actions{margin-top:10px;display:flex;gap:10px;flex-wrap:wrap}
    .rg-docs-guest-actions .rg-guest-btn{
      display:inline-flex;align-items:center;justify-content:center;
      padding:10px 14px;border-radius:12px;
      font-weight:750;text-decoration:none!important;
      border:1px solid rgba(0,0,0,.18);
      background:#fff;color:#111;
      line-height:1;
    }
    /* Primary = Robo-Guru Türkis */
    .rg-docs-guest-actions .rg-guest-btn.rg-guest-btn--primary{background:#23c6c8;color:#fff;border-color:#23c6c8}

    .rg-docs-folder{border:1px solid rgba(0,0,0,.08);border-radius:14px;overflow:hidden;margin:10px 0;background:rgba(0,0,0,.015)}
    .rg-docs-folder__sum{cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;font-weight:600}
    .rg-docs-folder__count{font-weight:600;font-size:12px;opacity:.7}
    .rg-docs-list{padding:10px 12px;display:flex;flex-direction:column;gap:10px}
    .rg-docs-item{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:10px 12px;border:1px solid rgba(0,0,0,.08);border-radius:14px;background:#fff}
    .rg-docs-item__main{display:flex;gap:10px;align-items:flex-start}
    .rg-docs-item__icon{width:34px;height:34px;border-radius:10px;background:rgba(0,0,0,.06);position:relative;flex:0 0 auto}
    .rg-docs-item__thumb{width:44px;height:56px;border-radius:12px;overflow:hidden;background:rgba(0,0,0,.04);flex:0 0 auto;border:1px solid rgba(0,0,0,.10);position:relative}
    .rg-docs-item__thumb img{width:100%;height:100%;object-fit:cover;display:block}
    /* Guest lock: blur preview + lock overlay */
    .rg-docs-item.is-locked .rg-docs-item__thumb img{filter:blur(7px);transform:scale(1.06)}
    .rg-docs-item.is-locked .rg-docs-item__thumb:after{content:"🔒";position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:18px;background:rgba(255,255,255,.25)}
    .rg-docs-item[data-ext="pdf"] .rg-docs-item__icon:after{content:"PDF";position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700}
    .rg-docs-item[data-ext="doc"] .rg-docs-item__icon:after,.rg-docs-item[data-ext="docx"] .rg-docs-item__icon:after{content:"DOC";position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700}
    .rg-docs-item[data-ext="xls"] .rg-docs-item__icon:after,.rg-docs-item[data-ext="xlsx"] .rg-docs-item__icon:after{content:"XLS";position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700}
    .rg-docs-item[data-ext="ppt"] .rg-docs-item__icon:after,.rg-docs-item[data-ext="pptx"] .rg-docs-item__icon:after{content:"PPT";position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700}
    .rg-docs-item__title{font-weight:650;line-height:1.25}
    .rg-docs-item__meta{margin-top:4px;display:flex;flex-wrap:wrap;gap:6px;font-size:12px;opacity:.75}
    .rg-docs-meta{border:1px solid rgba(0,0,0,.10);border-radius:999px;padding:2px 8px;background:rgba(0,0,0,.02)}
    .rg-docs-meta--privacy{font-weight:600}
    .rg-docs-badge{margin-left:8px;font-size:11px;font-weight:700;border-radius:999px;padding:2px 8px;display:inline-block}
    .rg-docs-badge--new{background:rgba(0,0,0,.08)}
    .rg-docs-badge--locked{background:rgba(35,198,200,.18);border:1px solid rgba(35,198,200,.38);color:#0b3b3c}
    /* Locked cards should feel clickable */
    .rg-docs-item.is-locked{cursor:pointer}
    .rg-docs-item__actions{display:flex;gap:10px;flex:0 0 auto;align-items:center}
    /* Buttons: override theme oversized buttons */
    /* Strongly override BuddyBoss/BuddyBoss-Theme button sizing (prevents "giant" buttons + whitespace) */
    .rg-docs-wrap a.button.rg-docs-btn,
    .rg-docs-wrap .rg-docs-btn.button{
      display:inline-flex!important;align-items:center!important;justify-content:center!important;
      padding:10px 14px!important;line-height:1!important;font-size:14px!important;
      border-radius:12px!important;
      min-height:40px!important;height:42px!important;
      box-shadow:none!important;
    }
    .rg-docs-wrap .rg-docs-btn.button:before,.rg-docs-wrap .rg-docs-btn.button:after{display:none!important}
    .rg-docs-btn{white-space:nowrap}
    /* Register CTA button in docs (keep consistent turquoise) */
    .rg-docs-wrap .rg-docs-btn--register{background:#23c6c8!important;border-color:#23c6c8!important;color:#fff!important}
    .rg-docs-wrap .rg-docs-btn--login{background:#fff!important;color:#111!important}
    /* Responsive: prevent cut-off buttons on small screens / narrow containers */
    .rg-docs-item{flex-wrap:wrap;min-height:unset!important;height:auto!important}
    .rg-docs-item__main{min-width:0;flex:1 1 320px}
    .rg-docs-item__text{min-width:0}
    .rg-docs-item__actions{flex:1 1 220px;justify-content:flex-end;flex-wrap:wrap}
    .rg-docs-item__actions .rg-docs-btn{max-width:100%}
    /* Mobile/Tablet: stack actions + remove any "space-between" inflation */
    @media (max-width: 920px){
      .rg-docs-modal{padding:12px;padding-top:calc(12px + env(safe-area-inset-top));padding-bottom:calc(12px + env(safe-area-inset-bottom))}
      /* iOS Safari: use dvh and safe-areas to avoid cropped/white PDF area */
      .rg-docs-modal__box{width:100%;height:calc(100dvh - 24px - env(safe-area-inset-top) - env(safe-area-inset-bottom));max-height:none;border-radius:14px}

      /* Reduce mobile whitespace + thinner buttons */
      .rg-docs-item{flex-direction:column!important;align-items:stretch!important;justify-content:flex-start!important;align-content:flex-start!important;padding:10px 12px!important}
      .rg-docs-item__actions{width:100%!important;justify-content:stretch!important;gap:8px!important;margin-top:10px!important;flex:0 0 auto!important}
      .rg-docs-wrap a.button.rg-docs-btn,
      .rg-docs-wrap .rg-docs-btn.button{padding:9px 12px!important;font-size:13px!important;min-height:38px!important;height:40px!important;border-radius:12px!important;line-height:1!important}
      .rg-docs-item__actions .rg-docs-btn{flex:1 1 auto;text-align:center}

      /* Slightly smaller thumbs on mobile to reduce height */
      .rg-docs-item__thumb{width:40px;height:52px;border-radius:12px}

      /* kill accidental "full-card" height from theme */
      .rg-docs-item__main{flex:0 0 auto!important}
    }
    /* Preview modal */
    .rg-docs-modal{position:fixed;inset:0;background:rgba(0,0,0,.55);display:none;align-items:center;justify-content:center;z-index:99999;padding:18px}
    .rg-docs-modal.is-open{display:flex}
    .rg-docs-modal__box{width:min(1600px, 96vw);max-height:calc(100dvh - 24px);height:min(760px, calc(100dvh - 24px));background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,.25);display:flex;flex-direction:column}
    .rg-docs-modal__top{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border-bottom:1px solid rgba(0,0,0,.08);min-width:0}
    /* prevent close button from being pushed off-screen by a long title */
    .rg-docs-modal__title{font-weight:650;flex:1 1 auto;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .rg-docs-modal__close{cursor:pointer;border:1px solid rgba(0,0,0,.15);border-radius:10px;background:#fff!important;color:#111!important;padding:8px 10px!important;line-height:1!important;min-height:36px!important;position:relative;z-index:2;pointer-events:auto;flex:0 0 auto;white-space:nowrap}
    /* Modal content: sidebar (doc list) + viewer */
    .rg-docs-modal__content{flex:1 1 auto;min-height:0;display:flex;overflow:hidden}
    .rg-docs-modal__sidebar{flex:0 0 280px;max-width:34vw;border-right:1px solid rgba(0,0,0,.08);background:rgba(0,0,0,.02);overflow:auto}
    .rg-docs-modal__sidehead{padding:10px 12px;font-weight:750;border-bottom:1px solid rgba(0,0,0,.08)}
    .rg-docs-modal__sidelist{list-style:none;margin:0;padding:8px;display:flex;flex-direction:column;gap:6px}
    .rg-docs-modal__sidebtn{display:block;width:100%;text-align:left;padding:10px 10px;border-radius:12px;border:1px solid rgba(0,0,0,.12);background:#fff;cursor:pointer;font-weight:650;line-height:1.2}
    .rg-docs-modal__sidebtn.is-active{outline:2px solid rgba(0,0,0,.25)}
    .rg-docs-modal__viewer{flex:1 1 auto;min-width:0;min-height:0;overflow:hidden;display:flex}
    /* iOS Safari flex bug: min-height:0 is required to avoid partial/white PDF view */
    .rg-docs-modal__viewer iframe,.rg-docs-modal__viewer object{width:100%;height:100%;min-height:100%;border:0;display:block;flex:1 1 auto;background:#fff}
    @media (max-width: 860px){
      .rg-docs-modal__sidebar{display:none}
    }
    ';
    wp_register_style('rg-docs-suite-frontend', false, array(), self::VERSION);
    wp_enqueue_style('rg-docs-suite-frontend');
    wp_add_inline_style('rg-docs-suite-frontend', $css);

    $js = "
    (function(){
      function qs(sel, el){ return (el||document).querySelector(sel); }
      function qsa(sel, el){ return Array.prototype.slice.call((el||document).querySelectorAll(sel)); }

      var modal = document.createElement('div');
      modal.className = 'rg-docs-modal';
      modal.innerHTML = '<div class=\"rg-docs-modal__box\">'
        + '<div class=\"rg-docs-modal__top\">'
        + '<div class=\"rg-docs-modal__title\">Vorschau</div>'
        + '<button type=\"button\" class=\"rg-docs-modal__close\">Schließen</button>'
        + '</div>'
        + '<div class=\"rg-docs-modal__content\">'
        +   '<div class=\"rg-docs-modal__sidebar\">'
        +     '<div class=\"rg-docs-modal__sidehead\">Dokumente</div>'
        +     '<ul class=\"rg-docs-modal__sidelist\"></ul>'
        +   '</div>'
        +   '<div class=\"rg-docs-modal__viewer\"><iframe loading=\"lazy\"></iframe></div>'
        + '</div>'
        + '</div>';
      document.body.appendChild(modal);

      function close(){
        modal.classList.remove('is-open');
        qs('iframe', modal).src = 'about:blank';
      }

      function normHref(href){
        if (!href) return href;
        // Keep browser PDF UI (toolbar/sidebar) available; just set a sane default view
        if (href.indexOf('#') === -1) return href + '#view=FitH';
        return href;
      }

      function setActive(url){
        qsa('.rg-docs-modal__sidebtn', modal).forEach(function(b){
          b.classList.toggle('is-active', b.getAttribute('data-url') === url);
        });
      }

      function setSrc(url, title){
        qs('.rg-docs-modal__title', modal).textContent = title || 'Vorschau';
        qs('iframe', modal).src = normHref(url);
        setActive(url);
      }
      qs('.rg-docs-modal__close', modal).addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); close(); });
      document.addEventListener('click', function(e){ var btn = e.target.closest('.rg-docs-modal__close'); if(!btn) return; e.preventDefault(); e.stopPropagation(); close(); });
      modal.addEventListener('click', function(e){ if (e.target === modal) close(); });
      document.addEventListener('keydown', function(e){ if (e.key === 'Escape') close(); });

      // Click-to-login for locked docs (guest): click anywhere on the card -> go to login
      document.addEventListener('click', function(e){
        var card = e.target.closest('.rg-docs-item.is-locked');
        if (!card) return;
        // allow explicit links/buttons to do their own navigation
        if (e.target.closest('a')) return;
        var wrap = card.closest('.rg-docs-wrap');
        var url = wrap ? wrap.getAttribute('data-rg-login-url') : '';
        if (url) window.location.href = url;
      });

      document.addEventListener('keydown', function(e){
        if (e.key !== 'Enter' && e.key !== ' ') return;
        var card = e.target && e.target.closest ? e.target.closest('.rg-docs-item.is-locked') : null;
        if (!card) return;
        e.preventDefault();
        var wrap = card.closest('.rg-docs-wrap');
        var url = wrap ? wrap.getAttribute('data-rg-login-url') : '';
        if (url) window.location.href = url;
      });

      document.addEventListener('click', function(e){
        var a = e.target.closest('a[data-rg-preview=\"1\"]');
        if (!a) return;
        e.preventDefault();
        var wrap = a.closest('.rg-docs-wrap');
        var list = qs('.rg-docs-modal__sidelist', modal);
        if (list) list.innerHTML = '';

        // Build a switcher list from all preview links in this docs block
        if (wrap && list){
          qsa('a[data-rg-preview=\"1\"]', wrap).forEach(function(link){
            var item = link.closest('.rg-docs-item');
            var tEl = item ? qs('.rg-docs-item__title', item) : null;
            var t = tEl ? tEl.textContent.trim() : (link.textContent || 'PDF');
            var u = link.href;

            var li = document.createElement('li');
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'rg-docs-modal__sidebtn';
            btn.textContent = t;
            btn.setAttribute('data-url', u);
            btn.addEventListener('click', function(){ setSrc(u, t); });
            li.appendChild(btn);
            list.appendChild(li);
          });
        }

        var titleEl = a.closest('.rg-docs-item') ? qs('.rg-docs-item__title', a.closest('.rg-docs-item')) : null;
        var title = titleEl ? titleEl.textContent.trim() : 'Vorschau';
        setSrc(a.href, title);
        modal.classList.add('is-open');
      });
    })();
    ";
    wp_register_script('rg-docs-suite-frontend', '', array(), self::VERSION, true);
    wp_enqueue_script('rg-docs-suite-frontend');
    wp_add_inline_script('rg-docs-suite-frontend', $js);
  }


  private function render_robot_documents_html($post_id){
    $data = $this->collect_robot_documents($post_id);
    $is_guest = !is_user_logged_in();
    if (empty($data['groups'])){
      // If guest can't see any docs due to privacy, still show a prominent hint if docs exist in backend.
      if ($is_guest && $this->robot_has_any_docs($post_id)){
        $wrap_id = 'rg-docs-' . absint($post_id);
        $reg_url   = function_exists('bp_get_signup_page') ? bp_get_signup_page() : wp_registration_url();
        $login_url = function_exists('bp_get_login_page') ? bp_get_login_page() : wp_login_url(get_permalink($post_id));
        ob_start();
        ?>
        <div class="rg-docs-wrap" id="<?php echo esc_attr($wrap_id); ?>">
          <h3 class="rg-docs-title">Downloads &amp; Dokumente</h3>
          <div class="rg-docs-guest-note">
            <strong>Registrieren Sie sich jetzt um alle Anleitungen und PDF Dokumente zu sehen!</strong><br>
            <strong>Sind Sie schon Mitglied melden Sie sich an um alle Dokumente zu sehen.</strong>
            <div class="rg-docs-guest-actions">
              <a class="rg-guest-btn rg-guest-btn--primary" href="<?php echo esc_url($reg_url); ?>">Registrieren</a>
              <a class="rg-guest-btn" href="<?php echo esc_url($login_url); ?>">Anmelden</a>
            </div>
          </div>
        </div>
        <?php
        return ob_get_clean();
      }
      return '';
    }

    $wrap_id = 'rg-docs-' . absint($post_id);

    $redirect_to = get_permalink($post_id);
    $reg_url_raw   = function_exists('bp_get_signup_page') ? bp_get_signup_page() : wp_registration_url();
    $login_url_raw = function_exists('bp_get_login_page') ? bp_get_login_page() : wp_login_url($redirect_to);
    // Always keep a redirect back to the robot page (conversion friendly)
    $reg_url   = add_query_arg('redirect_to', $redirect_to, $reg_url_raw);
    $login_url = add_query_arg('redirect_to', $redirect_to, $login_url_raw);

    ob_start();
    ?>
    <div class="rg-docs-wrap" id="<?php echo esc_attr($wrap_id); ?>" data-rg-login-url="<?php echo esc_attr($login_url); ?>" data-rg-register-url="<?php echo esc_attr($reg_url); ?>">
      <h3 class="rg-docs-title">Downloads &amp; Dokumente</h3>
      <?php if ($is_guest): ?>
        <div class="rg-docs-guest-note">
          <strong>Registrieren Sie sich jetzt um alle Anleitungen und PDF Dokumente zu sehen!</strong><br>
          <strong>Sind Sie schon Mitglied melden Sie sich an um alle Dokumente zu sehen.</strong>
          <div class="rg-docs-guest-actions">
            <a class="rg-guest-btn rg-guest-btn--primary" href="<?php echo esc_url($reg_url); ?>">Registrieren</a>
            <a class="rg-guest-btn" href="<?php echo esc_url($login_url); ?>">Anmelden</a>
          </div>
        </div>
      <?php endif; ?>


      <div class="rg-docs-accordions">
        <?php foreach ($data['groups'] as $group_label => $items): ?>
          <details class="rg-docs-folder" open>
            <summary class="rg-docs-folder__sum">
              <span class="rg-docs-folder__name"><?php echo esc_html($group_label); ?></span>
              <span class="rg-docs-folder__count"><?php echo esc_html(count($items)); ?></span>
            </summary>

            <div class="rg-docs-list">
              <?php foreach ($items as $it): ?>
                <?php
                  $title = $it['title'];
                  $url   = $it['url'];
                  $ext   = $it['ext'];
                  $size  = $it['size_h'];
                  $date  = $it['date_h'];
                  $is_new = !empty($it['is_new']);
                  $path  = $it['path'];
                  $privacy = $it['privacy_label'];
                  $is_locked = $is_guest ? true : false;
                  $download_url = $this->build_download_url($it, $post_id);
                  $preview_url = ($ext === 'pdf') ? $this->build_download_url($it, $post_id, true) : '';
                ?>
                <div class="rg-docs-item<?php echo $is_locked ? ' is-locked' : ''; ?>" data-ext="<?php echo esc_attr($ext); ?>"<?php echo $is_locked ? ' tabindex="0" role="button" aria-label="Anmelden um Dokument zu sehen"' : ''; ?>>
                  <div class="rg-docs-item__main">
                    <?php if (!empty($it['thumb'])): ?>
                      <span class="rg-docs-item__thumb" aria-hidden="true"><img loading="lazy" src="<?php echo esc_url($it['thumb']); ?>" alt="" /></span>
                    <?php else: ?>
                      <span class="rg-docs-item__icon" aria-hidden="true"></span>
                    <?php endif; ?>
                    <div class="rg-docs-item__text">
                      <div class="rg-docs-item__title">
                        <?php echo esc_html($title); ?>
                        <?php if ($is_new): ?><span class="rg-docs-badge rg-docs-badge--new">Neu</span><?php endif; ?>
                        <?php if ($is_locked): ?><span class="rg-docs-badge rg-docs-badge--locked">🔒 Nur für Mitglieder</span><?php endif; ?>
                      </div>
                      <div class="rg-docs-item__meta">
                        <?php if ($path): ?><span class="rg-docs-meta"><?php echo esc_html($path); ?></span><?php endif; ?>
                        <?php if ($privacy): ?><span class="rg-docs-meta rg-docs-meta--privacy"><?php echo esc_html($privacy); ?></span><?php endif; ?>
                        <?php if ($ext): ?><span class="rg-docs-meta"><?php echo esc_html(strtoupper($ext)); ?></span><?php endif; ?>
                        <?php if ($size): ?><span class="rg-docs-meta"><?php echo esc_html($size); ?></span><?php endif; ?>
                        <?php if ($date): ?><span class="rg-docs-meta"><?php echo esc_html($date); ?></span><?php endif; ?>
                      </div>
                    </div>
                  </div>

                  <div class="rg-docs-item__actions">
                    <?php if (!$is_locked): ?>
                      <?php if ($preview_url): ?>
                        <a href="<?php echo esc_url($preview_url); ?>" class="button rg-docs-btn rg-docs-btn--preview" data-rg-preview="1">Vorschau</a>
                      <?php endif; ?>
                      <a href="<?php echo esc_url($download_url); ?>" class="button button-primary rg-docs-btn rg-docs-btn--download">Download</a>
                    <?php else: ?>
                      <a href="<?php echo esc_url($login_url); ?>" class="button rg-docs-btn rg-docs-btn--login" data-rg-login="1">Anmelden</a>
                      <a href="<?php echo esc_url($reg_url); ?>" class="button rg-docs-btn rg-docs-btn--register" data-rg-register="1">Registrieren</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
}

  // ---------------------------------------------------------
  // Collect + cache robot documents (BuddyBoss folder/doc, CPT, Media)
  // ---------------------------------------------------------
  private function robot_has_any_docs($post_id){
    $post_id = absint($post_id);
    if (!$post_id) return false;

    $media_ids  = (string) get_post_meta($post_id, self::META_ROBOT_DOC_IDS, true);
    $bb_ids     = (string) get_post_meta($post_id, self::META_ROBOT_BB_DOC_IDS, true);
    $bb_folders = (string) get_post_meta($post_id, self::META_ROBOT_BB_FOLDER_IDS, true);
    $cpt_ids    = (string) get_post_meta($post_id, self::META_ROBOT_CPT_DOC_IDS, true);

    $media_ids  = trim(preg_replace('/[^0-9,]/', '', $media_ids), ',');
    $bb_ids     = trim(preg_replace('/[^0-9,]/', '', $bb_ids), ',');
    $bb_folders = trim(preg_replace('/[^0-9,]/', '', $bb_folders), ',');
    $cpt_ids    = trim(preg_replace('/[^0-9,]/', '', $cpt_ids), ',');

    return (bool) ($media_ids || $bb_ids || $bb_folders || $cpt_ids);
  }

  private function collect_robot_documents($post_id){
    $post_id = absint($post_id);
    if (!$post_id) return array('groups'=>array(), 'flat'=>array());

    $mode = get_post_meta($post_id, self::META_ROBOT_DOC_MODE, true);
    $mode = is_string($mode) ? trim($mode) : '';
    if (!in_array($mode, array('bb','cpt','media','both','all'), true)) $mode = 'all';

    $cache_key = 'rg_docs_robot_' . $post_id . '_' . md5($mode . '|' . get_current_user_id());
    $ttl = (int) apply_filters('rg_docs_cache_ttl', 600, $post_id); // default 10 min
    if ($ttl > 0) {
      $cached = get_transient($cache_key);
      if (is_array($cached) && isset($cached['groups'])) return $cached;
    }

    $new_days = (int) apply_filters('rg_docs_new_badge_days', 30, $post_id);
    $new_cutoff = time() - max(1,$new_days) * 86400;

    $groups = array();
    $flat = array();
    $hidden_count = 0; // docs user cannot see (privacy / not logged-in)


    // --- BuddyBoss (folder-linked and/or manual doc IDs)
    if (($mode === 'bb' || $mode === 'all') && function_exists('buddypress') && class_exists('BP_Document')) {
      $bb_doc_ids = get_post_meta($post_id, self::META_ROBOT_BB_DOC_IDS, true);
      $bb_doc_ids = is_string($bb_doc_ids) ? trim($bb_doc_ids) : '';
      $bb_selected = $bb_doc_ids ? array_filter(array_map('absint', preg_split('/[\s,]+/', $bb_doc_ids))) : array();

      $bb_folder_ids = get_post_meta($post_id, self::META_ROBOT_BB_FOLDER_IDS, true);
      $bb_folder_ids = is_string($bb_folder_ids) ? trim($bb_folder_ids) : '';
      $bb_folder_arr = $bb_folder_ids ? array_filter(array_map('absint', preg_split('/[\s,]+/', $bb_folder_ids))) : array();

      $bb_recursive = get_post_meta($post_id, self::META_ROBOT_BB_FOLDER_RECURSIVE, true);
      $bb_recursive = ($bb_recursive === '0') ? false : true;

      // 1) Folder-linked
      if (!empty($bb_folder_arr)) {
        $rows = $this->bb_get_docs_by_folders_cached($bb_folder_arr, $bb_recursive);
        foreach ($rows as $row) {
          if (!$this->bb_is_doc_visible($row)) { $hidden_count++; continue; }
          $it = $this->normalize_bb_row_to_item($row, $new_cutoff);
          if (!$it) continue;
          $flat[] = $it;
        }
      }

      // 2) Manually picked doc IDs
      if (!empty($bb_selected)) {
        $bb_missing = array();
        foreach ($bb_selected as $bbid) {
          $row = $this->bb_get_doc_row_cached($bbid);
          if (empty($row) || empty($row['id'])) { $bb_missing[] = (int)$bbid; continue; }
          if (!$this->bb_is_doc_visible($row)) { $hidden_count++; continue; }
          $it = $this->normalize_bb_row_to_item($row, $new_cutoff);
          if (!$it) continue;
          $flat[] = $it;
        }

        // Lazy cleanup: if BuddyBoss docs were deleted, remove stale IDs from robot meta.
        // This avoids "ghost" docs when BuddyBoss items were removed but the robot still references their IDs.
        if (!empty($bb_missing)) {
          $bb_selected_clean = array_values(array_diff($bb_selected, $bb_missing));
          $new_val = implode(',', array_map('absint', $bb_selected_clean));
          if ($new_val) update_post_meta($post_id, self::META_ROBOT_BB_DOC_IDS, $new_val);
          else delete_post_meta($post_id, self::META_ROBOT_BB_DOC_IDS);
          $this->clear_robot_cache($post_id);
        }
      }
    }

    // --- CPT documents
    if ($mode === 'cpt' || $mode === 'both' || $mode === 'all') {
      $doc_post_ids = get_post_meta($post_id, self::META_ROBOT_CPT_DOC_IDS, true);
      $doc_post_ids = is_string($doc_post_ids) ? trim($doc_post_ids) : '';
      $doc_arr = $doc_post_ids ? array_filter(array_map('absint', preg_split('/[\s,]+/', $doc_post_ids))) : array();

      foreach ($doc_arr as $did) {
        $url = get_post_meta($did, self::META_DOC_FILE, true);
        if (!$url) continue;

        $title = get_the_title($did);
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $ts = get_post_time('U', true, $did);

        // If CPT stores a media URL, try to resolve attachment id for thumbnail/size.
        $cpt_aid = ($ext === 'pdf') ? (int) attachment_url_to_postid($url) : 0;
        $thumb = ($ext === 'pdf' && $cpt_aid) ? $this->get_pdf_thumb_url($cpt_aid) : '';
        $file_path = $cpt_aid ? get_attached_file($cpt_aid) : '';
        $size_b = ($file_path && file_exists($file_path)) ? @filesize($file_path) : 0;

        $it = array(
          'source' => 'cpt',
          'id' => $did,
          'aid' => $cpt_aid,
          'bbid' => 0,
          'title' => $title ? $title : ('Dokument #' . $did),
          'url' => $url,
          'ext' => $ext,
          'thumb' => $thumb,
          'path' => $this->cpt_get_doc_path($did),
          'privacy' => '',
          'privacy_label' => '',
          'date_ts' => $ts ? (int)$ts : 0,
          'date_h' => $ts ? date_i18n(get_option('date_format'), $ts) : '',
          'size_b' => $size_b,
          'size_h' => $size_b ? size_format($size_b) : '',
          'is_new' => $ts ? ($ts >= $new_cutoff) : false,
        );
        $flat[] = $it;
      }
    }


    // --- Media attachments (legacy)
    if ($mode === 'media' || $mode === 'both' || $mode === 'all') {
      $ids = get_post_meta($post_id, self::META_ROBOT_DOC_IDS, true);
      $ids = is_string($ids) ? trim($ids) : '';
      $arr = $ids ? array_filter(array_map('absint', preg_split('/[,\s]+/', $ids))) : array();

      foreach ($arr as $aid) {
        $url = wp_get_attachment_url($aid);
        if (!$url) continue;
        $title = get_the_title($aid);
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        $ts = get_post_time('U', true, $aid);

        $file_path = get_attached_file($aid);
        $size_b = ($file_path && file_exists($file_path)) ? @filesize($file_path) : 0;

        $it = array(
          'source' => 'media',
          'id' => 0,
          'aid' => $aid,
          'bbid' => 0,
          'title' => $title ? $title : ('Datei #' . $aid),
          'url' => $url,
          'ext' => $ext,
          'thumb' => ($ext === 'pdf' && $aid) ? $this->get_pdf_thumb_url($aid) : '',
          'path' => '',
          'privacy' => '',
          'privacy_label' => '',
          'date_ts' => $ts ? (int)$ts : 0,
          'date_h' => $ts ? date_i18n(get_option('date_format'), $ts) : '',
          'size_b' => $size_b,
          'size_h' => $size_b ? size_format($size_b) : '',
          'is_new' => $ts ? ($ts >= $new_cutoff) : false,
        );
        $flat[] = $it;
      }
    }

    // de-duplicate by (source,id/aid/bbid,url)
    $seen = array();
    $uniq = array();
    foreach ($flat as $it) {
      $k = $it['source'] . ':' . (int)$it['id'] . ':' . (int)$it['aid'] . ':' . (int)$it['bbid'] . ':' . md5($it['url']);
      if (isset($seen[$k])) continue;
      $seen[$k] = 1;
      $uniq[] = $it;
    }
    $flat = $uniq;

    // grouping (folder path)
    foreach ($flat as $it) {
      $g = trim((string)$it['path']);
      if ($g === '') $g = 'Downloads';
      if (!isset($groups[$g])) $groups[$g] = array();
      $groups[$g][] = $it;
    }

    // stable sort by date desc
    foreach ($groups as $g => $items) {
      usort($items, function($a,$b){
        return (int)$b['date_ts'] <=> (int)$a['date_ts'];
      });
      $groups[$g] = $items;
    }
    ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);

    $out = array('groups'=>$groups, 'flat'=>$flat, 'mode'=>$mode, 'cached_at'=>time(), 'hidden_count'=>$hidden_count);
    if ($ttl > 0) set_transient($cache_key, $out, $ttl);
    return $out;
  }

  private function normalize_bb_row_to_item($row, $new_cutoff){
    $attachment_id = !empty($row['attachment_id']) ? absint($row['attachment_id']) : 0;
    $url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';
    if (!$url) return null;

    $title = !empty($row['title']) ? (string)$row['title'] : ('Dokument #' . absint($row['id']));
    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

    $folder_id = !empty($row['folder_id']) ? absint($row['folder_id']) : 0;
    $folder_path = $this->bb_folder_path($folder_id);
    $privacy = !empty($row['privacy']) ? (string)$row['privacy'] : '';
    $privacy_label = $privacy ? ('BB: ' . $privacy) : '';

    $ts = !empty($row['date_created']) ? strtotime($row['date_created']) : 0;
    if (!$ts) {
      $ts = $attachment_id ? get_post_time('U', true, $attachment_id) : 0;
    }

    $file_path = $attachment_id ? get_attached_file($attachment_id) : '';
    $size_b = ($file_path && file_exists($file_path)) ? @filesize($file_path) : 0;

    return array(
      'source' => 'bb',
      'id' => 0,
      'aid' => $attachment_id,
      'bbid' => absint($row['id']),
      'title' => $title,
      'url' => $url,
      'ext' => $ext,
          'thumb' => ($ext === 'pdf' && $attachment_id) ? $this->get_pdf_thumb_url($attachment_id) : '',
      'path' => $folder_path ? $folder_path : 'Downloads',
      'privacy' => $privacy,
      'privacy_label' => $privacy_label,
      'date_ts' => $ts ? (int)$ts : 0,
      'date_h' => $ts ? date_i18n(get_option('date_format'), $ts) : '',
      'size_b' => $size_b,
      'size_h' => $size_b ? size_format($size_b) : '',
      'is_new' => $ts ? ($ts >= $new_cutoff) : false,
    );
  }

  private function build_download_url($item, $robot_id, $preview=false){
    $args = array(
      'action' => 'rg_docs_download',
      'type'   => $item['source'],
      'rid'    => absint($robot_id),
      'preview'=> $preview ? 1 : 0,
    );

    if ($item['source'] === 'bb') {
      $args['aid'] = absint($item['aid']);
      $args['bbid'] = absint($item['bbid']);
    } elseif ($item['source'] === 'media') {
      $args['aid'] = absint($item['aid']);
    } elseif ($item['source'] === 'cpt') {
      $args['did'] = absint($item['id']);
    }
    $url = admin_url('admin-ajax.php') . '?' . http_build_query($args);
    $url = wp_nonce_url($url, 'rg_docs_download_' . absint($robot_id) . '_' . $item['source']);
    return $url;
  }

  private function bb_get_docs_by_folders_cached($folder_ids, $recursive){
    $key = 'rg_docs_bb_folders_' . md5(json_encode(array(array_values($folder_ids),(int)$recursive)));
    $ttl = (int) apply_filters('rg_docs_cache_ttl_bb', 600);
    if ($ttl > 0) {
      $cached = get_transient($key);
      if (is_array($cached)) {
        // If any attachment was removed since caching (BuddyBoss doc deleted), invalidate the cache.
        $dirty = false;
        foreach ($cached as $r) {
          $aid = !empty($r['attachment_id']) ? absint($r['attachment_id']) : 0;
          if ($aid && !get_post_status($aid)) { $dirty = true; break; }
        }
        if (!$dirty) return $cached;
        delete_transient($key);
      }
    }
    $rows = $this->bb_get_docs_by_folders($folder_ids, $recursive);
    // Filter out rows with missing attachments (can happen after deletes).
    if (!empty($rows)) {
      $rows = array_values(array_filter($rows, function($r){
        $aid = !empty($r['attachment_id']) ? absint($r['attachment_id']) : 0;
        return !$aid || (bool)get_post_status($aid);
      }));
    }
    if ($ttl > 0) set_transient($key, $rows, $ttl);
    return $rows;
  }

  private function bb_get_doc_row_cached($doc_id){
    $doc_id = absint($doc_id);
    if (!$doc_id) return array();
    $key = 'rg_docs_bb_doc_' . $doc_id;
    $ttl = (int) apply_filters('rg_docs_cache_ttl_bb', 600);
    if ($ttl > 0) {
      $cached = get_transient($key);
      if (is_array($cached) && isset($cached['id'])) {
        // If the underlying attachment no longer exists (e.g. BuddyBoss document deleted),
        // drop the cache so the doc disappears immediately.
        $aid = !empty($cached['attachment_id']) ? absint($cached['attachment_id']) : 0;
        if ($aid && get_post_status($aid)) return $cached;
        delete_transient($key);
      }
    }
    $row = $this->bb_get_doc_row($doc_id);
    if ($ttl > 0) set_transient($key, $row, $ttl);
    return $row;
  }

  private function cpt_get_doc_path($doc_post_id){
    $terms = get_the_terms($doc_post_id, self::TAX);
    if (!$terms || is_wp_error($terms)) return '';
    // Choose deepest term (has parent chain)
    usort($terms, function($a,$b){ return (int)$b->parent <=> (int)$a->parent; });
    $t = $terms[0];
    $parts = array($t->name);
    while (!empty($t->parent)) {
      $t = get_term($t->parent, self::TAX);
      if (!$t || is_wp_error($t)) break;
      array_unshift($parts, $t->name);
    }
    return implode(' / ', $parts);
  }


  public function shortcode_robot_documents($atts=array()){
    if (!is_singular('robo_robot')) return '';
    return $this->render_robot_documents_html(get_the_ID());
  }

  public function append_robot_documents_to_content($content){
    if (is_admin() || !is_singular('robo_robot')) return $content;
    $html = $this->render_robot_documents_html(get_the_ID());
    if (!$html) return $content;
    return $content . $html;
  }

  // =========================================================
  // Feature B: Dedicated Documents CPT + taxonomy + shortcode
  // =========================================================
  public function register_cpt_and_tax() {
    register_post_type(self::CPT, array(
      'labels' => array(
        'name' => 'Dokumente',
        'singular_name' => 'Dokument',
      ),
      'public' => false,
      'show_ui' => true,
      'menu_icon' => 'dashicons-media-document',
      'supports' => array('title'),
      'show_in_rest' => true,
    ));

    register_taxonomy(self::TAX, array(self::CPT), array(
      'label' => 'Dokument-Kategorien',
      'hierarchical' => true,
      'show_ui' => true,
      'show_admin_column' => true,
      'rewrite' => array('slug' => 'dokumente', 'hierarchical' => true),
      'show_in_rest' => true,
    ));
  }

  private function ensure_default_terms() {
    // Create a small set of commonly used categories (can be edited later).
    $defaults = array(
      'downloads-dokumente' => 'Downloads',
    );

    foreach ($defaults as $slug => $name) {
      if (!term_exists($slug, self::TAX)) {
        wp_insert_term($name, self::TAX, array('slug' => $slug));
      }
    }
  }

  public function add_document_metabox() {
    add_meta_box(
      'rg_document_file',
      'Dokument-Datei',
      array($this, 'render_document_metabox'),
      self::CPT,
      'normal',
      'default'
    );
  }

  public function render_document_metabox($post) {
    $file = get_post_meta($post->ID, self::META_DOC_FILE, true);
    wp_nonce_field('rg_document_file_nonce', 'rg_document_file_nonce');
    ?>
    <p>
      <input type="file" name="rg_document_file" />
    </p>
    <?php if ($file): ?>
      <p><strong>Aktuelle Datei:</strong> <a href="<?php echo esc_url($file); ?>" target="_blank" rel="noopener">Download</a></p>
    <?php endif; ?>
    <p class="description">Tipp: Lege Kategorien als Ordnerstruktur an (z.B. „Gausium“ → „Phantas-S 1.3“) und weise jedes Dokument zu. Falls keine gesetzt ist, wird automatisch „Downloads &amp; Dokumente“ vergeben.</p>
    <?php
  }

  public function save_document_file($post_id) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision($post_id) ) return;
    if ( ! current_user_can('edit_post', $post_id) ) return;

    if (!isset($_POST['rg_document_file_nonce'])) return;
    if (!wp_verify_nonce($_POST['rg_document_file_nonce'], 'rg_document_file_nonce')) return;

    if (!empty($_FILES['rg_document_file']['name'])) {
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      $uploaded = wp_handle_upload($_FILES['rg_document_file'], array('test_form' => false));
      if (isset($uploaded['url'])) {
        update_post_meta($post_id, self::META_DOC_FILE, esc_url_raw($uploaded['url']));
      }
    }

    // If no category assigned, auto-assign Downloads & Dokumente (ensure term exists)
    $this->ensure_default_terms();
  }

  public function docs_frontend_assets() {
    // Dashicons + inline CSS for the buttons
    wp_enqueue_style('dashicons');
    wp_register_style('rg-docs-suite-frontend', false, array(), self::VERSION);
    wp_enqueue_style('rg-docs-suite-frontend');

    $css = '
      /* Buttons-layout for the CPT shortcode only (avoid collisions with the Robo-Robot docs card) */
      .rg-docs-wrap.rg-docs-wrap--buttons{display:flex;flex-wrap:wrap;gap:10px;margin:12px 0}
      .rg-doc-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid rgba(0,0,0,.12);
          border-radius:10px;text-decoration:none;line-height:1;font-weight:600}
      .rg-doc-btn:hover{transform:translateY(-1px)}
      .rg-doc-btn .dashicons{font-size:18px;width:18px;height:18px}
      .rg-doc-group{margin:16px 0 10px}
      .rg-doc-group-title{margin:0 0 8px;font-size:1.05em}
      /* dezente Farbcodierung je Kategorie */
      .rg-cat-garantie{border-color:rgba(255,140,0,.35);background:rgba(255,140,0,.08)}
      .rg-cat-wartungsvertrag{border-color:rgba(0,160,220,.35);background:rgba(0,160,220,.08)}
      .rg-cat-datensicherheit{border-color:rgba(120,120,120,.35);background:rgba(120,120,120,.08)}
      .rg-cat-angaben-zur-produktsicherheit{border-color:rgba(0,170,90,.35);background:rgba(0,170,90,.08)}
      .rg-cat-downloads-dokumente{border-color:rgba(90,90,255,.25);background:rgba(90,90,255,.06)}
    .rg-docs__meta{display:flex;flex-direction:column;gap:2px}.rg-docs__cat{font-size:12px;opacity:.75}.rg-docs__group{margin-top:12px}.rg-docs__group h4{margin:14px 0 8px;font-size:16px}';
    wp_add_inline_style('rg-docs-suite-frontend', $css);
  }

  
  // =========================================================
  // Helper: Build hierarchical term path like "Gausium / Phantas-S 1.3"
  // =========================================================
  private function docs_term_path($term) {
    if (is_numeric($term)) {
      $term = get_term((int)$term, self::TAX);
    }
    if (!$term || is_wp_error($term)) return '';
    $parts = array($term->name);
    $parent = (int) $term->parent;
    while ($parent) {
      $pt = get_term($parent, self::TAX);
      if (!$pt || is_wp_error($pt)) break;
      array_unshift($parts, $pt->name);
      $parent = (int) $pt->parent;
    }
    return implode(' / ', $parts);
  }

  // Pick the deepest term (most ancestors) as "primary" for display/grouping.
  private function docs_pick_primary_term($terms) {
    if (empty($terms) || !is_array($terms)) return null;
    $best = null;
    $best_depth = -1;
    foreach ($terms as $t) {
      if (!$t || is_wp_error($t)) continue;
      $depth = 0;
      $p = (int)$t->parent;
      while ($p) {
        $depth++;
        $pt = get_term($p, self::TAX);
        if (!$pt || is_wp_error($pt)) break;
        $p = (int)$pt->parent;
      }
      if ($depth > $best_depth) { $best_depth = $depth; $best = $t; }
    }
    return $best;
  }

  // =========================================================

  /**
   * Build a safe download URL that streams the attachment through WP (helps with host/WAF 403 on direct upload URLs).
   */
  private function build_download_url_legacy($attachment_id, $type = 'media', $bb_doc_id = 0) {
    $attachment_id = absint($attachment_id);
    $type = sanitize_key($type);
    $bb_doc_id = absint($bb_doc_id);
    $nonce = wp_create_nonce('rg_docs_download_' . $attachment_id . '_' . $type . '_' . $bb_doc_id);
    return add_query_arg(
      array(
        'action' => 'rg_docs_download',
        'aid'    => $attachment_id,
        'type'   => $type,
        'bbid'   => $bb_doc_id,
        '_wpnonce' => $nonce,
      ),
      admin_url('admin-ajax.php')
    );
  }


  private function get_pdf_thumb_url($attachment_id){
    $attachment_id = absint($attachment_id);
    if (!$attachment_id) return '';
    $file = get_attached_file($attachment_id);
    if (!$file || !file_exists($file)) return '';
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if ($ext !== 'pdf') return '';

    $up = wp_upload_dir();
    $dir = trailingslashit($up['basedir']) . 'rg-pdf-thumbs';
    $url_base = trailingslashit($up['baseurl']) . 'rg-pdf-thumbs/';
    if (!wp_mkdir_p($dir)) return '';

    $mtime = @filemtime($file);
    if (!$mtime) $mtime = time();
    $name = 'pdf-' . $attachment_id . '-' . $mtime . '.jpg';
    $dest = trailingslashit($dir) . $name;

    // Cleanup old thumbs for this attachment
    foreach (glob(trailingslashit($dir) . 'pdf-' . $attachment_id . '-*.jpg') as $old) {
      if ($old !== $dest) { @unlink($old); }
    }

    if (file_exists($dest)) return $url_base . $name;

    // 1) Imagick (best)
    if (class_exists('Imagick')) {
      try {
        $im = new Imagick();
        $im->setResolution(144, 144);
        $im->readImage($file . '[0]');
        $im->setImageFormat('jpeg');
        $im->setImageCompressionQuality(82);
        $im->thumbnailImage(240, 0);
        $im->writeImage($dest);
        $im->clear();
        $im->destroy();
        if (file_exists($dest)) return $url_base . $name;
      } catch (Exception $e) {
        // fall through
      }
    }

    // 2) pdftoppm fallback (poppler-utils)
    $disabled = array_map('trim', explode(',', (string)ini_get('disable_functions')));
    $can_shell = function_exists('shell_exec') && !in_array('shell_exec', $disabled, true);
    if ($can_shell) {
      $pdftoppm = trim((string)@shell_exec('command -v pdftoppm'));
      if ($pdftoppm) {
        $tmp_base = trailingslashit($dir) . 'tmp-' . $attachment_id;
        $cmd = $pdftoppm . ' -f 1 -l 1 -jpeg -singlefile -scale-to-x 240 -scale-to-y -1 ' . escapeshellarg($file) . ' ' . escapeshellarg($tmp_base);
        @shell_exec($cmd . ' 2>/dev/null');
        $gen = $tmp_base . '.jpg';
        if (file_exists($gen)) {
          @rename($gen, $dest);
          if (file_exists($dest)) return $url_base . $name;
        }
      }
    }

    return '';
  }

  /**
   * Download handler (streams file via PHP). Enforces BuddyBoss privacy when type=bb.
   */
  public 
function ajax_docs_download() {
    $type = isset($_GET['type']) ? sanitize_key($_GET['type']) : 'media';
    $rid  = isset($_GET['rid']) ? absint($_GET['rid']) : 0;
    $preview = !empty($_GET['preview']);

    $aid  = isset($_GET['aid']) ? absint($_GET['aid']) : 0;
    $bbid = isset($_GET['bbid']) ? absint($_GET['bbid']) : 0;
    $did  = isset($_GET['did']) ? absint($_GET['did']) : 0;

    // Nonce (nopriv friendly)
    $nonce_action = 'rg_docs_download_' . $rid . '_' . $type;
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], $nonce_action)) {
      wp_die('Invalid token.', 403);
    }

    // Resolve to an attachment + URL
    $file_path = '';
    $download_name = '';
    $url = '';
    $attachment_id = 0;

    if ($type === 'bb') {
      if (!$bbid || !function_exists('buddypress')) wp_die('BuddyBoss missing.', 403);
      $row = $this->bb_get_doc_row_cached($bbid);
      if (empty($row)) wp_die('Forbidden.', 403);
      if (!$this->bb_is_doc_visible($row)) wp_die('Forbidden.', 403);
      $attachment_id = !empty($row['attachment_id']) ? absint($row['attachment_id']) : 0;
      if (!$attachment_id) wp_die('Invalid attachment.', 403);
    } elseif ($type === 'media') {
      $attachment_id = $aid;
      if (!$attachment_id) wp_die('Invalid attachment.', 403);
    } elseif ($type === 'cpt') {
      if (!$did) wp_die('Invalid document.', 403);
      $url = (string) get_post_meta($did, self::META_DOC_FILE, true);
      if (!$url) wp_die('Missing file.', 404);
      // For CPT docs, try to map to local file if it's inside uploads
      $attachment_id = attachment_url_to_postid($url);
    } else {
      wp_die('Invalid type.', 403);
    }

    if (!$url && $attachment_id) {
      $url = wp_get_attachment_url($attachment_id);
    }
    if (!$url) wp_die('Missing file.', 404);

    // Track download (global + per robot)
    $this->track_download($type, array(
      'rid' => $rid,
      'aid' => $attachment_id,
      'bbid'=> $bbid,
      'did' => $did,
      'url' => $url,
    ), $preview);

    // Attempt to stream locally (avoids WAF/hotlink)
    if ($attachment_id) {
      $file_path = get_attached_file($attachment_id);
      $download_name = get_the_title($attachment_id);
      $download_name = $download_name ? $download_name : ('download-' . $attachment_id);
    } else {
      $download_name = ($type === 'cpt') ? (get_the_title($did) ?: 'download') : 'download';
    }

    if ($file_path && file_exists($file_path) && is_readable($file_path)) {
      // Stream file
      $mime = function_exists('mime_content_type') ? @mime_content_type($file_path) : '';
      if (!$mime) $mime = 'application/octet-stream';

      if ($preview && strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) === 'pdf') {
        $disposition = 'inline';
        $mime = 'application/pdf';
      } else {
        $disposition = 'attachment';
      }

      nocache_headers();
      header('Content-Type: ' . $mime);
      header('Content-Length: ' . filesize($file_path));
      header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($download_name) . '"');
      header('X-Content-Type-Options: nosniff');

      // Clean buffers
      while (ob_get_level()) { @ob_end_clean(); }
      readfile($file_path);
      exit;
    }

    // Fallback: redirect to URL (may trigger WAF in some environments)
    wp_redirect($url);
    exit;
  }


  // BuddyBoss helpers (Documents + Folders)
  // =========================================================
  private function bb_folder_path($folder_id) {
    $folder_id = absint($folder_id);
    if (!$folder_id || !class_exists('BP_Document_Folder')) return '';
    $parts = array();
    $guard = 0;
    while ($folder_id && $guard < 25) {
      $folder = new BP_Document_Folder($folder_id);
      if (empty($folder->id)) break;
      array_unshift($parts, $folder->title);
      $folder_id = absint($folder->parent);
      $guard++;
    }
    return $parts ? implode(' / ', $parts) : '';
  }

  public function ajax_bb_doc_search() {
    if (!current_user_can('edit_posts')) {
      wp_send_json_error(array('message' => 'forbidden'), 403);
    }

    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'rg_bb_doc_search')) {
      wp_send_json_error(array('message' => 'bad_nonce'), 400);
    }

    if (!class_exists('BP_Document')) {
      wp_send_json_error(array('message' => 'buddyboss_missing'), 400);
    }

    $term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
    $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
    if ($user_id <= 0) $user_id = get_current_user_id();

    $res = BP_Document::get(array(
      'user_id' => $user_id,
      'search_terms' => $term ? $term : false,
      'per_page' => 30,
      'page' => 1,
      'fields' => 'all',
      'order_by' => 'date_created',
      'sort' => 'DESC',
    ));

    $docs = !empty($res['documents']) ? (array) $res['documents'] : array();
    $out = array();

    foreach ($docs as $d) {
      if (empty($d->id)) continue;
      $attachment_id = !empty($d->attachment_id) ? absint($d->attachment_id) : 0;
      $url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';
      if (!$url) continue;
      $folder_path = $this->bb_folder_path(!empty($d->folder_id) ? absint($d->folder_id) : 0);
      $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
      $out[] = array(
        'id' => absint($d->id),
        'title' => $d->title ? (string) $d->title : ('Dokument #' . absint($d->id)),
        'folder' => $folder_path,
        'url' => $url,
        'ext' => $ext ? strtoupper($ext) : '',
      );
    }

    wp_send_json_success(array('documents' => $out));
  }


public function ajax_bb_folder_search() {
  if (!current_user_can('edit_posts')) {
    wp_send_json_error(array('message' => 'forbidden'), 403);
  }

  $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
  if (!wp_verify_nonce($nonce, 'rg_bb_doc_search')) {
    wp_send_json_error(array('message' => 'bad_nonce'), 400);
  }

  if (!class_exists('BP_Document_Folder')) {
    wp_send_json_error(array('message' => 'buddyboss_missing'), 400);
  }

  $term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
  $user_id = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
  if ($user_id <= 0) $user_id = get_current_user_id();

  $res = BP_Document_Folder::get(array(
    'user_id' => $user_id,
    'search_terms' => $term ? $term : false,
    'per_page' => 30,
    'page' => 1,
    'fields' => 'all',
    'order_by' => 'date_created',
    'sort' => 'DESC',
  ));

  $folders = !empty($res['folders']) ? (array)$res['folders'] : array();
  $out = array();

  foreach ($folders as $f) {
    if (empty($f->id)) continue;
    $path = $this->bb_folder_path((int)$f->id);
    $privacy = !empty($f->privacy) ? (string)$f->privacy : '';
    $out[] = array(
      'id' => absint($f->id),
      'title' => $f->title ? (string)$f->title : ('Ordner #' . absint($f->id)),
      'path' => $path,
      'privacy' => $privacy,
    );
  }

  wp_send_json_success(array('folders' => $out));
}

private function bb_get_descendant_folder_ids($folder_ids) {
  $folder_ids = array_filter(array_map('absint', (array)$folder_ids));
  if (!$folder_ids || !function_exists('buddypress')) return $folder_ids;
  $bp = buddypress();
  if (empty($bp->document) || empty($bp->document->table_name_folder)) return $folder_ids;

  global $wpdb;
  $all = $folder_ids;
  $frontier = $folder_ids;
  $guard = 0;

  while (!empty($frontier) && $guard < 25) {
    $placeholders = implode(',', array_fill(0, count($frontier), '%d'));
    $sql = "SELECT id FROM {$bp->document->table_name_folder} WHERE parent IN ($placeholders)";
    $rows = $wpdb->get_col($wpdb->prepare($sql, $frontier));
    $rows = array_filter(array_map('absint', (array)$rows));
    $rows = array_values(array_diff($rows, $all));
    if (empty($rows)) break;
    $all = array_merge($all, $rows);
    $frontier = $rows;
    $guard++;
  }

  return array_values(array_unique($all));
}

private function bb_get_docs_by_folders($folder_ids, $recursive = true) {
  $folder_ids = array_filter(array_map('absint', (array)$folder_ids));
  if (!$folder_ids || !function_exists('buddypress')) return array();
  if ($recursive) {
    $folder_ids = $this->bb_get_descendant_folder_ids($folder_ids);
  }

  $bp = buddypress();
  if (empty($bp->document) || empty($bp->document->table_name)) return array();
  global $wpdb;

  $placeholders = implode(',', array_fill(0, count($folder_ids), '%d'));
  $sql = "SELECT id, attachment_id, title, folder_id, privacy, user_id, group_id FROM {$bp->document->table_name} WHERE folder_id IN ($placeholders) ORDER BY date_created DESC";
  $rows = $wpdb->get_results($wpdb->prepare($sql, $folder_ids), ARRAY_A);
  return is_array($rows) ? $rows : array();
}

/**
 * Fetch a single BuddyBoss document row by document id.
 *
 * BuddyBoss Documents are stored in the BuddyBoss document table.
 * We use this low-level fetch so the download proxy can validate:
 * - the attachment_id belongs to the document
 * - privacy rules
 */
private function bb_get_doc_row($doc_id) {
  $doc_id = absint($doc_id);
  if (!$doc_id || !function_exists('buddypress')) return array();
  $bp = buddypress();
  if (empty($bp->document) || empty($bp->document->table_name)) return array();

  global $wpdb;
  $sql = "SELECT id, attachment_id, title, folder_id, privacy, user_id, group_id FROM {$bp->document->table_name} WHERE id = %d LIMIT 1";
  $row = $wpdb->get_row($wpdb->prepare($sql, $doc_id), ARRAY_A);
  return is_array($row) ? $row : array();
}

private function bb_is_doc_visible($doc) {
  // Accept either array row or BP_Document object.
  $privacy = '';
  $user_id = 0;
  $group_id = 0;

  if (is_object($doc)) {
    $privacy = !empty($doc->privacy) ? (string)$doc->privacy : '';
    $user_id = !empty($doc->user_id) ? (int)$doc->user_id : 0;
    $group_id = !empty($doc->group_id) ? (int)$doc->group_id : 0;
  } elseif (is_array($doc)) {
    $privacy = !empty($doc['privacy']) ? (string)$doc['privacy'] : '';
    $user_id = !empty($doc['user_id']) ? (int)$doc['user_id'] : 0;
    $group_id = !empty($doc['group_id']) ? (int)$doc['group_id'] : 0;
  }

  $privacy = $privacy ? $privacy : 'public';

  // Allow override.
  $allowed = apply_filters('rg_docs_suite_bb_doc_visible', null, $privacy, $user_id, $group_id, $doc);
  if ($allowed !== null) return (bool)$allowed;
  // Admin/moderator override: show all BuddyBoss docs in Robo listings (privacy is handled elsewhere).
  if (current_user_can("manage_options") || current_user_can("bp_moderate")) return true;


  // Admin/moderator override: allow viewing all BuddyBoss docs in Robo backend/frontend listings.
  if (current_user_can('manage_options') || current_user_can('bp_moderate')) {
    return true;
  }

  if ($privacy === 'public') return true;

  if ($privacy === 'loggedin') return is_user_logged_in();

  if ($privacy === 'onlyme') {
    return is_user_logged_in() && (get_current_user_id() === (int)$user_id);
  }

  if ($privacy === 'friends') {
    if (!is_user_logged_in()) return false;
    if (get_current_user_id() === (int)$user_id) return true;
    if (function_exists('friends_check_friendship') && function_exists('bp_is_active') && bp_is_active('friends')) {
      return (bool)friends_check_friendship(get_current_user_id(), (int)$user_id);
    }
    return false;
  }

  if ($privacy === 'grouponly') {
    if (!is_user_logged_in()) return false;
    if ($group_id <= 0) return false;
    if (function_exists('groups_is_user_member') && function_exists('bp_is_active') && bp_is_active('groups')) {
      return (bool)groups_is_user_member(get_current_user_id(), (int)$group_id);
    }
    return false;
  }

  // Other privacies like message/forums: conservative default.
  return false;
}

private function docs_get_dashicon($category_slug, $file_url) {
    $ext = strtolower(pathinfo(parse_url($file_url, PHP_URL_PATH), PATHINFO_EXTENSION));

    switch ($category_slug) {
      case 'garantie': return 'dashicons-shield';
      case 'wartungsvertrag': return 'dashicons-clipboard';
      case 'datensicherheit': return 'dashicons-lock';
      case 'angaben-zur-produktsicherheit': return 'dashicons-yes-alt';
      case 'downloads-dokumente': return 'dashicons-download';
    }

    if ($ext === 'pdf') return 'dashicons-media-document';
    if (in_array($ext, array('doc', 'docx'), true)) return 'dashicons-media-text';
    if (in_array($ext, array('xls', 'xlsx', 'csv'), true)) return 'dashicons-media-spreadsheet';
    if (in_array($ext, array('jpg', 'jpeg', 'png', 'webp'), true)) return 'dashicons-format-image';
    return 'dashicons-media-default';
  }

  public function shortcode_documents($atts) {
    $atts = shortcode_atts(array(
      'category' => '',
      'group'    => '1',   // 1 = nach Kategorien gruppieren
      'limit'    => 50,
    ), $atts, 'rg_documents');

    $tax_query = array();
    if (!empty($atts['category'])) {
      $tax_query[] = array(
        'taxonomy' => self::TAX,
        'field'    => 'slug',
        'terms'    => sanitize_title($atts['category']),
      );
    }

    $q = new WP_Query(array(
      'post_type'      => self::CPT,
      'post_status'    => 'publish',
      'posts_per_page' => intval($atts['limit']),
      'orderby'        => 'title',
      'order'          => 'ASC',
      'tax_query'      => $tax_query,
      'no_found_rows'  => true,
    ));

    if (!$q->have_posts()) return '';

    $docs_by_cat = array();

    while ($q->have_posts()) {
      $q->the_post();
      $id  = get_the_ID();
      $url = get_post_meta($id, self::META_DOC_FILE, true);
      if (!$url) continue;

      $terms = wp_get_post_terms($id, self::TAX);
      $primary = $this->docs_pick_primary_term($terms);

      if (!$primary) {
        $key = 'Downloads';
        if (!isset($docs_by_cat[$key])) $docs_by_cat[$key] = array('name' => $key, 'items' => array());
        $docs_by_cat[$key]['items'][] = array(
          'id' => $id,
          'title' => get_the_title($id),
          'url' => $url,
          'cat_slug' => '',
          'cat_name' => $key,
          'cat_path' => $key,
        );
        continue;
      }

      $path = $this->docs_term_path($primary);
      $key  = $path ? $path : $primary->name;

      if (!isset($docs_by_cat[$key])) {
        $docs_by_cat[$key] = array('name' => $key, 'items' => array());
      }
      $docs_by_cat[$key]['items'][] = array(
        'id' => $id,
        'title' => get_the_title($id),
        'url' => $url,
        'cat_slug' => $primary->slug,
        'cat_name' => $primary->name,
        'cat_path' => $path,
      );
    }
    wp_reset_postdata();

    $group = ($atts['group'] === '1');

    $out = '';
    if ($group) {
      foreach ($docs_by_cat as $slug => $bucket) {
                $out .= '<div class="rg-doc-group">';
        $out .= '<h4 class="rg-doc-group-title">' . esc_html($bucket['name']) . '</h4>';
        $out .= '<div class="rg-docs-wrap rg-docs-wrap--buttons">';

        foreach ($bucket['items'] as $doc) {
          $icon = $this->docs_get_dashicon($doc['cat_slug'], $doc['url']);
          $classes = 'rg-doc-btn rg-cat-' . esc_attr($doc['cat_slug']);
          $out .= '<a class="' . $classes . '" href="' . esc_url($doc['url']) . '" target="_blank" rel="noopener nofollow">';
          $out .= '<span class="dashicons ' . esc_attr($icon) . '" aria-hidden="true"></span>';
          $out .= '<span>' . esc_html($doc['title']) . ( !empty($doc['cat_path']) ? ' <small style="opacity:.7">(' . esc_html($doc['cat_path']) . ')</small>' : '' ) . '</span>';
          $out .= '</a>';
        }

        $out .= '</div></div>';
      }
    } else {
      $out .= '<div class="rg-docs-wrap rg-docs-wrap--buttons">';
      foreach ($docs_by_cat as $slug => $bucket) {
        foreach ($bucket['items'] as $doc) {
          $icon = $this->docs_get_dashicon($doc['cat_slug'], $doc['url']);
          $classes = 'rg-doc-btn rg-cat-' . esc_attr($doc['cat_slug']);
          $out .= '<a class="' . $classes . '" href="' . esc_url($doc['url']) . '" target="_blank" rel="noopener nofollow">';
          $out .= '<span class="dashicons ' . esc_attr($icon) . '" aria-hidden="true"></span>';
          $out .= '<span>' . esc_html($doc['title']) . ( !empty($doc['cat_path']) ? ' <small style="opacity:.7">(' . esc_html($doc['cat_path']) . ')</small>' : '' ) . '</span>';
          $out .= '</a>';
        }
      }
      $out .= '</div>';
    }

    return $out;
  }


  // =========================================================
  // Admin: Cache clear + pages (Mass-Editor + Download Stats)
  // =========================================================
  public function ajax_clear_cache() {
    if (!current_user_can('edit_posts')) wp_die('Forbidden', 403);
    check_ajax_referer('rg_docs_clear_cache', 'nonce');

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    if (!$post_id) wp_send_json_error(array('message'=>'Missing post_id'));

    $this->clear_robot_cache($post_id);
    wp_send_json_success(array('message'=>'Cache cleared'));
  }

  private function clear_robot_cache($post_id){
    $post_id = absint($post_id);
    if (!$post_id) return;

    // Clear robot cache for common modes and users (best effort).
    foreach (array('bb','cpt','media','both','all') as $mode) {
      $key = 'rg_docs_robot_' . $post_id . '_' . md5($mode . '|' . get_current_user_id());
      delete_transient($key);
    }
    // Also clear BuddyBoss caches (folder/doc)
    // (We can't enumerate all keys easily; keep short TTL by default.)
  }

  public function register_admin_pages() {
    // Mass editor
    add_submenu_page(
      'edit.php?post_type=robo_robot',
      'Dokumente Mass-Editor',
      'Dokumente Mass-Editor',
      'edit_posts',
      'rg-docs-mass-editor',
      array($this, 'render_mass_editor_page')
    );

    // Stats
    add_submenu_page(
      'edit.php?post_type=robo_robot',
      'Download-Statistik',
      'Download-Statistik',
      'manage_options',
      'rg-docs-stats',
      array($this, 'render_stats_page')
    );
  }

  public function render_mass_editor_page() {
    if (!current_user_can('edit_posts')) wp_die('Forbidden', 403);

    // Save action
    if (!empty($_POST['rg_mass_apply']) && check_admin_referer('rg_docs_mass_editor')) {
      $robot_ids = !empty($_POST['robot_ids']) ? array_filter(array_map('absint', (array)$_POST['robot_ids'])) : array();
      $folder_ids = !empty($_POST['bb_folder_ids']) ? array_filter(array_map('absint', preg_split('/[\s,]+/', sanitize_text_field($_POST['bb_folder_ids'])))) : array();
      $recursive = !empty($_POST['bb_recursive']) ? '1' : '0';

      foreach ($robot_ids as $rid) {
        update_post_meta($rid, self::META_ROBOT_BB_FOLDER_IDS, implode(',', $folder_ids));
        update_post_meta($rid, self::META_ROBOT_BB_FOLDER_RECURSIVE, $recursive);
        update_post_meta($rid, self::META_ROBOT_DOC_MODE, 'bb');
        $this->clear_robot_cache($rid);
      }

      echo '<div class="notice notice-success"><p>Zuordnung gespeichert.</p></div>';
    }

    // Robots list
    $robots = get_posts(array(
      'post_type' => 'robo_robot',
      'post_status' => array('publish','draft','private'),
      'numberposts' => 200,
      'orderby' => 'title',
      'order' => 'ASC',
    ));

    $selected = array();
    ?>
    <div class="wrap">
      <h1>Dokumente Mass-Editor</h1>
      <p>Wähle BuddyBoss-Ordner (IDs) und ordne sie mehreren Robotern zu. Tipp: Ordner-IDs bekommst du über die Ordner-Suche im Robo-Robot Editor (oder aus der URL im BuddyBoss Dokumente-Bereich).</p>

      <form method="post">
        <?php wp_nonce_field('rg_docs_mass_editor'); ?>

        <table class="form-table">
          <tr>
            <th scope="row">BuddyBoss Ordner-IDs</th>
            <td>
              <input type="text" name="bb_folder_ids" class="regular-text" placeholder="z.B. 12,34,56" />
              <label style="margin-left:10px;"><input type="checkbox" name="bb_recursive" value="1" checked> Unterordner einbeziehen</label>
            </td>
          </tr>
        </table>

        <h2>Roboter auswählen</h2>
        <div style="max-height:420px; overflow:auto; border:1px solid #ccd0d4; background:#fff; padding:10px;">
          <?php foreach ($robots as $r): ?>
            <label style="display:block; margin:6px 0;">
              <input type="checkbox" name="robot_ids[]" value="<?php echo esc_attr($r->ID); ?>">
              <?php echo esc_html($r->post_title); ?> <small style="opacity:.7;">(#<?php echo esc_html($r->ID); ?>)</small>
            </label>
          <?php endforeach; ?>
        </div>

        <p class="submit">
          <button type="submit" name="rg_mass_apply" value="1" class="button button-primary">Zuordnungen anwenden</button>
        </p>
      </form>
    </div>
    <?php
  }

  public function render_stats_page() {
    if (!current_user_can('manage_options')) wp_die('Forbidden', 403);

    $index = get_option('rg_docs_stats_index', array());
    if (!is_array($index)) $index = array();

    // Sort by count desc
    uasort($index, function($a,$b){
      return (int)($b['count'] ?? 0) <=> (int)($a['count'] ?? 0);
    });

    $export_url = wp_nonce_url(admin_url('admin-post.php?action=rg_docs_export_csv'), 'rg_docs_export_csv');

    ?>
    <div class="wrap">
      <h1>Download-Statistik</h1>
      <p><a class="button" href="<?php echo esc_url($export_url); ?>">CSV exportieren</a></p>

      <table class="widefat striped">
        <thead>
          <tr>
            <th>Dokument</th>
            <th>Quelle</th>
            <th>Downloads</th>
            <th>Letzter Download</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$index): ?>
            <tr><td colspan="4">Noch keine Downloads getrackt.</td></tr>
          <?php else: ?>
            <?php foreach (array_slice($index, 0, 200, true) as $key => $row): ?>
              <tr>
                <td><?php echo esc_html($row['title'] ?? $key); ?></td>
                <td><?php echo esc_html($row['type'] ?? ''); ?></td>
                <td><?php echo esc_html((int)($row['count'] ?? 0)); ?></td>
                <td><?php echo !empty($row['last']) ? esc_html(date_i18n(get_option('date_format').' '.get_option('time_format'), (int)$row['last'])) : ''; ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>

      <p style="margin-top:10px; color:#666;">Hinweis: Preview-Aufrufe (PDF Vorschau) werden nicht als Download gezählt.</p>
    </div>
    <?php
  }

  public function handle_export_csv() {
    if (!current_user_can('manage_options')) wp_die('Forbidden', 403);
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'rg_docs_export_csv')) wp_die('Invalid token', 403);

    $index = get_option('rg_docs_stats_index', array());
    if (!is_array($index)) $index = array();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="rg-doc-download-stats.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, array('key','type','title','count','last_timestamp','url'), ';');

    foreach ($index as $key => $row) {
      fputcsv($out, array(
        $key,
        $row['type'] ?? '',
        $row['title'] ?? '',
        (int)($row['count'] ?? 0),
        (int)($row['last'] ?? 0),
        $row['url'] ?? '',
      ), ';');
    }
    fclose($out);
    exit;
  }

  // =========================================================
  // Tracking
  // =========================================================
  private function track_download($type, $ctx, $preview=false){
    if ($preview) return; // don't count previews

    $type = sanitize_key($type);
    $rid = !empty($ctx['rid']) ? absint($ctx['rid']) : 0;
    $aid = !empty($ctx['aid']) ? absint($ctx['aid']) : 0;
    $bbid= !empty($ctx['bbid']) ? absint($ctx['bbid']) : 0;
    $did = !empty($ctx['did']) ? absint($ctx['did']) : 0;
    $url = !empty($ctx['url']) ? esc_url_raw($ctx['url']) : '';

    $key = $type . ':' . ($aid ?: $did ?: $bbid) . ':' . md5($url);

    // Global index in option (lightweight, capped)
    $index = get_option('rg_docs_stats_index', array());
    if (!is_array($index)) $index = array();
    if (empty($index[$key])) $index[$key] = array('type'=>$type, 'title'=>'', 'count'=>0, 'last'=>0, 'url'=>$url);

    $index[$key]['count'] = (int)($index[$key]['count'] ?? 0) + 1;
    $index[$key]['last']  = time();
    $index[$key]['url']   = $url;

    // Title best-effort
    if ($type === 'cpt' && $did) $index[$key]['title'] = get_the_title($did);
    if (($type === 'bb' || $type === 'media') && $aid) $index[$key]['title'] = get_the_title($aid);

    // Cap to 2000 entries
    if (count($index) > 2000) {
      uasort($index, function($a,$b){ return (int)($b['last'] ?? 0) <=> (int)($a['last'] ?? 0); });
      $index = array_slice($index, 0, 2000, true);
    }
    update_option('rg_docs_stats_index', $index, false);

    // Per-attachment count (if we have one)
    if ($aid) {
      $c = (int) get_post_meta($aid, '_rg_download_count', true);
      update_post_meta($aid, '_rg_download_count', $c + 1);
      update_post_meta($aid, '_rg_download_last', time());
    }
    // Per-robot count
    if ($rid) {
      $arr = get_post_meta($rid, '_rg_robot_download_counts', true);
      if (!is_array($arr)) $arr = array();
      $arr[$key] = (int)($arr[$key] ?? 0) + 1;
      update_post_meta($rid, '_rg_robot_download_counts', $arr);
      $this->clear_robot_cache($rid); // ensure "New" badges, etc. can refresh (cheap)
    }
  }

}

new RG_Docs_Suite();
