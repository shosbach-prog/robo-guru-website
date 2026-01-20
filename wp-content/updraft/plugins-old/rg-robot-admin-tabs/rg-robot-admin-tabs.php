<?php
/**
 * Plugin Name: Robo Robot Admin Tabs (Robo-Guru)
 * Description: Zentraler Tab-Editor für alle Robo-Roboter-Parameter (Post Type: robo_robot) inkl. Galerie.
 * Version: 1.0.0
 * Author: Robo-Guru
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class RG_Robot_Admin_Tabs {
  const VERSION = '1.0.0';
  const META_GALLERY_IDS = '_rf_gallery_ids';

  public function __construct() {
    add_action('add_meta_boxes', array($this, 'add_metaboxes'));
    add_action('save_post_robo_robot', array($this, 'save'), 10, 2);
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
  }

  public function enqueue_admin_assets($hook){
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'robo_robot') return;

    wp_enqueue_style('rg-robot-admin-tabs', plugin_dir_url(__FILE__) . 'assets/admin-tabs.css', array(), self::VERSION);
    wp_enqueue_script('rg-robot-admin-tabs', plugin_dir_url(__FILE__) . 'assets/admin-tabs.js', array('jquery'), self::VERSION, true);

    // Gallery
    wp_enqueue_media();
    wp_enqueue_script('rg-robot-admin-gallery', plugin_dir_url(__FILE__) . 'assets/admin-gallery.js', array('jquery','jquery-ui-sortable'), self::VERSION, true);
    wp_enqueue_style('rg-robot-admin-gallery', plugin_dir_url(__FILE__) . 'assets/admin-gallery.css', array(), self::VERSION);
  }

  public function add_metaboxes(){
    // Main tabbed details box
    add_meta_box(
      'rg_robot_details_tabs',
      'Roboter-Details (zentral)',
      array($this, 'render_details_tabs'),
      'robo_robot',
      'normal',
      'high'
    );

    // Gallery box
    add_meta_box(
      'rg_robot_gallery',
      'Roboter-Galerie (mehrere Bilder)',
      array($this, 'render_gallery'),
      'robo_robot',
      'side',
      'default'
    );
  }

  private function meta($post_id, $key, $default=''){
    $v = get_post_meta($post_id, $key, true);
    if ($v === '' || $v === null) return $default;
    return $v;
  }

  private function field_text($name, $label, $value, $placeholder=''){
    ?>
    <div class="rg-field">
      <label class="rg-label" for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label); ?></label>
      <input class="rg-input" type="text" id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
    </div>
    <?php
  }

  private function field_textarea($name, $label, $value, $placeholder=''){
    ?>
    <div class="rg-field">
      <label class="rg-label" for="<?php echo esc_attr($name); ?>"><?php echo esc_html($label); ?></label>
      <textarea class="rg-textarea" id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>" rows="6" placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea($value); ?></textarea>
      <div class="rg-help">Tipp: 1 Zeile = 1 Bullet im Frontend.</div>
    </div>
    <?php
  }

  public function render_details_tabs($post){
    wp_nonce_field('rg_robot_details_save', 'rg_robot_details_nonce');

    $id = $post->ID;

    // Load values
    $v = array(
      '_rf_manufacturer'   => $this->meta($id, '_rf_manufacturer'),
      '_rf_segment'        => $this->meta($id, '_rf_segment'),
      '_rf_tagline'        => $this->meta($id, '_rf_tagline'),
      '_rf_price_month'    => $this->meta($id, '_rf_price_month'),
      '_rf_cta_url'        => $this->meta($id, '_rf_cta_url'),
      '_rf_datasheet_url'  => $this->meta($id, '_rf_datasheet_url'),

      '_rf_m2h'            => $this->meta($id, '_rf_m2h'),
      '_rf_battery_hours'  => $this->meta($id, '_rf_battery_hours'),
      '_rf_charge_time'    => $this->meta($id, '_rf_charge_time'),

      '_rf_tank_liters'    => $this->meta($id, '_rf_tank_liters'),
      '_rf_clean_water'    => $this->meta($id, '_rf_clean_water'),
      '_rf_dirty_water'    => $this->meta($id, '_rf_dirty_water'),

      '_rf_dimensions'     => $this->meta($id, '_rf_dimensions'),
      '_rf_working_width'  => $this->meta($id, '_rf_working_width'),
      '_rf_noise'          => $this->meta($id, '_rf_noise'),

      '_rf_nav'            => $this->meta($id, '_rf_nav'),

      '_rf_highlight_1'    => $this->meta($id, '_rf_highlight_1'),
      '_rf_highlight_2'    => $this->meta($id, '_rf_highlight_2'),
      '_rf_highlight_3'    => $this->meta($id, '_rf_highlight_3'),

      '_rf_tasks_profile'  => $this->meta($id, '_rf_tasks_profile'),
      '_rf_features'       => $this->meta($id, '_rf_features'),
      '_rf_use_cases'      => $this->meta($id, '_rf_use_cases'),
      '_rf_economics'      => $this->meta($id, '_rf_economics'),
      '_rf_digital'        => $this->meta($id, '_rf_digital'),
      '_rf_accessories'    => $this->meta($id, '_rf_accessories'),
    );

    ?>
    <div class="rg-tabs" data-rg-tabs>
      <div class="rg-tabs__bar" role="tablist" aria-label="Roboter-Details Tabs">
        <button type="button" class="rg-tab is-active" data-rg-tab="basis" role="tab">Basis</button>
        <button type="button" class="rg-tab" data-rg-tab="leistung" role="tab">Leistung</button>
        <button type="button" class="rg-tab" data-rg-tab="wasser" role="tab">Wasser & Tank</button>
        <button type="button" class="rg-tab" data-rg-tab="masse" role="tab">Maße & Betrieb</button>
        <button type="button" class="rg-tab" data-rg-tab="navigation" role="tab">Navigation</button>
        <button type="button" class="rg-tab" data-rg-tab="highlights" role="tab">Highlights</button>
        <button type="button" class="rg-tab" data-rg-tab="inhalte" role="tab">Inhalte</button>
      </div>

      <div class="rg-tabs__panes">
        <section class="rg-pane is-active" data-rg-pane="basis" role="tabpanel">
          <div class="rg-grid2">
            <?php
              $this->field_text('_rf_manufacturer','Hersteller',$v['_rf_manufacturer'],'z. B. Pudu, Gausium, Nexaro');
              $this->field_text('_rf_segment','Segment',$v['_rf_segment'],'z. B. Scheuersaugroboter / Kehrsauger / Service');
              $this->field_text('_rf_tagline','Tagline (kurzer Claim)',$v['_rf_tagline'],'z. B. “Der wendige Allrounder für…”');
              $this->field_text('_rf_price_month','Preis/Leasing pro Monat (optional)',$v['_rf_price_month'],'z. B. 399 €');
              $this->field_text('_rf_cta_url','CTA-Link (Beratung anfragen)',$v['_rf_cta_url'],'https://...');
              $this->field_text('_rf_datasheet_url','Produktinfos / Datenblatt URL (optional)',$v['_rf_datasheet_url'],'https://...');
            ?>
          </div>
        </section>

        <section class="rg-pane" data-rg-pane="leistung" role="tabpanel">
          <div class="rg-grid2">
            <?php
              $this->field_text('_rf_m2h','Flächenleistung (m²/h)',$v['_rf_m2h'],'z. B. 1200');
              $this->field_text('_rf_battery_hours','Akkulaufzeit (h)',$v['_rf_battery_hours'],'z. B. 4');
              $this->field_text('_rf_charge_time','Ladezeit (optional)',$v['_rf_charge_time'],'z. B. 2.5 h');
            ?>
          </div>
        </section>

        <section class="rg-pane" data-rg-pane="wasser" role="tabpanel">
          <div class="rg-grid2">
            <?php
              $this->field_text('_rf_tank_liters','Tank gesamt (l)',$v['_rf_tank_liters'],'z. B. 30');
              $this->field_text('_rf_clean_water','Reinwasser (l)',$v['_rf_clean_water'],'z. B. 20');
              $this->field_text('_rf_dirty_water','Abwasser (l)',$v['_rf_dirty_water'],'z. B. 20');
            ?>
          </div>
        </section>

        <section class="rg-pane" data-rg-pane="masse" role="tabpanel">
          <div class="rg-grid2">
            <?php
              $this->field_text('_rf_dimensions','Abmessungen (L×B×H)',$v['_rf_dimensions'],'z. B. 540×440×617 mm');
              $this->field_text('_rf_working_width','Arbeitsbreite',$v['_rf_working_width'],'z. B. 430 mm');
              $this->field_text('_rf_noise','Geräuschpegel',$v['_rf_noise'],'z. B. < 65 dB');
            ?>
          </div>
        </section>

        <section class="rg-pane" data-rg-pane="navigation" role="tabpanel">
          <div class="rg-grid1">
            <?php
              $this->field_text('_rf_nav','Navigation / Sensorik',$v['_rf_nav'],'z. B. LiDAR + 3D-Kamera + Ultraschall');
            ?>
          </div>
        </section>

        <section class="rg-pane" data-rg-pane="highlights" role="tabpanel">
          <div class="rg-grid1">
            <?php
              $this->field_text('_rf_highlight_1','Highlight 1',$v['_rf_highlight_1'],'z. B. “Sehr kompakt & wendig”');
              $this->field_text('_rf_highlight_2','Highlight 2',$v['_rf_highlight_2'],'z. B. “Starke Kantenreinigung”');
              $this->field_text('_rf_highlight_3','Highlight 3',$v['_rf_highlight_3'],'z. B. “Gute App & Flottenfähigkeit”');
            ?>
          </div>
        </section>

        <section class="rg-pane" data-rg-pane="inhalte" role="tabpanel">
          <div class="rg-grid2">
            <?php
              $this->field_textarea('_rf_tasks_profile','Aufgabenprofil & Kernkompetenzen',$v['_rf_tasks_profile'],"z. B.\nUnterhaltsreinigung\nPunktuelle Nachreinigung\n…");
              $this->field_textarea('_rf_features','Besonderheiten',$v['_rf_features'],"z. B.\nAuto-Docking\nKI-Hinderniserkennung\n…");
              $this->field_textarea('_rf_use_cases','Einsatzbereiche',$v['_rf_use_cases']);
              $this->field_textarea('_rf_economics','Wirtschaftlichkeit',$v['_rf_economics']);
              $this->field_textarea('_rf_digital','Digital & Updates',$v['_rf_digital']);
              $this->field_textarea('_rf_accessories','Zubehör & Features',$v['_rf_accessories']);
            ?>
          </div>
        </section>
      </div>

      <div class="rg-note">
        <strong>Hinweis:</strong> Du pflegst hier nur die technischen/strukturierten Daten. Der Gutenberg-Inhalt oben bleibt für Fließtext, Bilder, FAQ etc.
      </div>
    </div>
    <?php
  }

  public function render_gallery($post){
    wp_nonce_field( 'rg_robot_gallery_save', 'rg_robot_gallery_nonce' );
    $ids = get_post_meta( $post->ID, self::META_GALLERY_IDS, true );
    $ids = is_string($ids) ? trim($ids) : '';
    ?>
    <div class="rg-admin-gallery">
      <p class="description">Diese Bilder erscheinen im Frontend als Galerie/Slider (zusätzlich zum Beitragsbild).</p>

      <input type="hidden" id="rg_gallery_ids" name="rg_gallery_ids" value="<?php echo esc_attr($ids); ?>" />

      <div id="rg_gallery_preview" class="rg-gallery-preview">
        <?php
          if ( $ids ) {
            $arr = array_filter(array_map('absint', preg_split('/[,\s]+/', $ids)));
            foreach ( $arr as $aid ) {
              $thumb = wp_get_attachment_image_url($aid, 'thumbnail');
              if ( ! $thumb ) continue;
              echo '<div class="rg-thumb" data-id="' . esc_attr($aid) . '"><img src="' . esc_url($thumb) . '" alt=""><button type="button" class="rg-remove" title="Entfernen">×</button></div>';
            }
          }
        ?>
      </div>

      <p>
        <button type="button" class="button button-primary" id="rg_add_gallery">Bilder hinzufügen</button>
        <button type="button" class="button" id="rg_clear_gallery">Leeren</button>
      </p>

      <p class="description">Tipp: Drag & Drop zum Sortieren.</p>
    </div>
    <?php
  }

  public function save($post_id, $post){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( ! current_user_can('edit_post', $post_id) ) return;

    // Save details
    if ( isset($_POST['rg_robot_details_nonce']) && wp_verify_nonce($_POST['rg_robot_details_nonce'], 'rg_robot_details_save') ) {
      $keys = array(
        '_rf_manufacturer','_rf_segment','_rf_tagline','_rf_price_month','_rf_cta_url','_rf_datasheet_url',
        '_rf_m2h','_rf_battery_hours','_rf_charge_time',
        '_rf_tank_liters','_rf_clean_water','_rf_dirty_water',
        '_rf_dimensions','_rf_working_width','_rf_noise',
        '_rf_nav',
        '_rf_highlight_1','_rf_highlight_2','_rf_highlight_3',
        '_rf_tasks_profile','_rf_features','_rf_use_cases','_rf_economics','_rf_digital','_rf_accessories',
      );

      foreach ($keys as $k){
        $val = isset($_POST[$k]) ? wp_unslash($_POST[$k]) : '';
        if (in_array($k, array('_rf_cta_url','_rf_datasheet_url'), true)) {
          $val = esc_url_raw($val);
        } else {
          $val = sanitize_textarea_field($val);
        }
        if ($val !== '') update_post_meta($post_id, $k, $val);
        else delete_post_meta($post_id, $k);
      }
    }

    // Save gallery
    if ( isset($_POST['rg_robot_gallery_nonce']) && wp_verify_nonce($_POST['rg_robot_gallery_nonce'], 'rg_robot_gallery_save') ) {
      $ids = isset($_POST['rg_gallery_ids']) ? sanitize_text_field($_POST['rg_gallery_ids']) : '';
      $ids = preg_replace('/[^0-9,]/', '', $ids);
      $ids = trim($ids, ',');
      if ($ids) update_post_meta($post_id, self::META_GALLERY_IDS, $ids);
      else delete_post_meta($post_id, self::META_GALLERY_IDS);
    }
  }
}

new RG_Robot_Admin_Tabs();
