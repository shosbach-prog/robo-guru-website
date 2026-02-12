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

      // ROI fields
      '_rf_list_price'       => $this->meta($id, '_rf_list_price'),
      '_rf_service_basic'    => $this->meta($id, '_rf_service_basic'),
      '_rf_service_standard' => $this->meta($id, '_rf_service_standard'),
      '_rf_service_premium'  => $this->meta($id, '_rf_service_premium'),
      '_rf_power_watts'      => $this->meta($id, '_rf_power_watts'),

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
        <button type="button" class="rg-tab" data-rg-tab="roi" role="tab" style="background:#e7f3ff;border-color:#72aee6;">ROI Daten</button>
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
              $this->field_text('_rf_tagline','Tagline (kurzer Claim)',$v['_rf_tagline'],'z. B. "Der wendige Allrounder für…"');
              $this->field_text('_rf_price_month','Preis/Leasing pro Monat (optional)',$v['_rf_price_month'],'z. B. 399 €');
              $this->field_text('_rf_cta_url','CTA-Link (Beratung anfragen)',$v['_rf_cta_url'],'https://...');
              $this->field_text('_rf_datasheet_url','Produktinfos / Datenblatt URL (optional)',$v['_rf_datasheet_url'],'https://...');
            ?>
          </div>
        </section>

        <section class="rg-pane" data-rg-pane="roi" role="tabpanel">
          <div class="rg-roi-info" style="background:#e7f3ff;border:1px solid #72aee6;padding:12px 16px;border-radius:4px;margin-bottom:20px;">
            <strong>ROI-Kalkulator Daten</strong><br>
            Diese Werte werden im ROI-Kalkulator für die automatische Berechnung verwendet.<br>
            Die Leasingrate wird automatisch berechnet (5% Restwert, 6% p.a. Verzinsung).
          </div>

          <h4 style="margin-top:0;">Kaufpreis</h4>
          <div class="rg-grid2">
            <?php
              $this->field_text('_rf_list_price','Listenpreis / Kaufpreis (€ netto)',$v['_rf_list_price'],'z. B. 25000');
            ?>
          </div>

          <h4>Servicekosten pro Monat (€)</h4>
          <div class="rg-grid2">
            <?php
              $this->field_text('_rf_service_basic','Basic',$v['_rf_service_basic'],'z. B. 99');
              $this->field_text('_rf_service_standard','Standard',$v['_rf_service_standard'],'z. B. 179');
              $this->field_text('_rf_service_premium','Premium',$v['_rf_service_premium'],'z. B. 255');
            ?>
          </div>

          <h4>Stromverbrauch</h4>
          <div class="rg-grid2">
            <?php
              $this->field_text('_rf_power_watts','Leistungsaufnahme (Watt)',$v['_rf_power_watts'],'z. B. 800');
            ?>
          </div>
          <?php
          // Calculate yearly power cost based on watts
          // Assumptions: 4h usage/day, 260 days/year, 0.30€/kWh average
          $power_watts = floatval($v['_rf_power_watts']);
          $battery_hours = floatval($v['_rf_battery_hours']) ?: 4; // Use battery hours or default 4h
          $days_per_year = 260;
          $price_per_kwh = 0.30; // €/kWh average

          if ($power_watts > 0) {
            $kwh_per_day = ($power_watts / 1000) * $battery_hours;
            $kwh_per_year = $kwh_per_day * $days_per_year;
            $power_cost_yearly = round($kwh_per_year * $price_per_kwh, 2);
            ?>
            <div class="rg-power-calc" style="background:#fff3cd;border:1px solid #ffc107;padding:15px;border-radius:4px;margin-top:10px;">
              <strong>Berechnete Stromkosten:</strong>
              <ul style="margin:10px 0 0 20px;">
                <li>Verbrauch: <?php echo number_format($power_watts, 0, ',', '.'); ?> W × <?php echo $battery_hours; ?>h/Tag × <?php echo $days_per_year; ?> Tage = <strong><?php echo number_format($kwh_per_year, 0, ',', '.'); ?> kWh/Jahr</strong></li>
                <li>Bei <?php echo number_format($price_per_kwh, 2, ',', '.'); ?> €/kWh: <strong><?php echo number_format($power_cost_yearly, 2, ',', '.'); ?> €/Jahr</strong></li>
              </ul>
              <p class="description" style="margin-top:10px;">Annahme: <?php echo $battery_hours; ?>h Betrieb/Tag, <?php echo $days_per_year; ?> Arbeitstage/Jahr</p>
            </div>
            <?php
          }

          // Show calculated leasing rates if list price is set
          $list_price = floatval($v['_rf_list_price']);
          if ($list_price > 0) {
            $residual = 0.05;
            $interest = 0.06 / 12;
            $residual_value = $list_price * $residual;
            $depreciation = $list_price - $residual_value;
            $avg_balance = ($list_price + $residual_value) / 2;

            $lease36 = round(($depreciation / 36) + ($avg_balance * $interest), 2);
            $lease48 = round(($depreciation / 48) + ($avg_balance * $interest), 2);
            $lease60 = round(($depreciation / 60) + ($avg_balance * $interest), 2);
            ?>
            <div class="rg-leasing-calc" style="background:#f0f0f1;padding:15px;border-radius:4px;margin-top:15px;">
              <strong>Berechnete Leasingraten bei <?php echo number_format($list_price, 0, ',', '.'); ?> € Listenpreis:</strong>
              <ul style="margin:10px 0 0 20px;">
                <li>36 Monate: <strong><?php echo number_format($lease36, 2, ',', '.'); ?> €/Monat</strong></li>
                <li>48 Monate: <strong><?php echo number_format($lease48, 2, ',', '.'); ?> €/Monat</strong></li>
                <li>60 Monate: <strong><?php echo number_format($lease60, 2, ',', '.'); ?> €/Monat</strong></li>
              </ul>
            </div>
            <?php
          }
          ?>
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
        '_rf_list_price','_rf_service_basic','_rf_service_standard','_rf_service_premium','_rf_power_watts', // ROI fields
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
