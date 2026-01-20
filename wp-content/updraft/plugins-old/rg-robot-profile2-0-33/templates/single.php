<?php
get_header();

$pt = (class_exists('RG_Robot_Profile_197')) ? RG_Robot_Profile_197::detect_post_type() : (post_type_exists('roboter') ? 'roboter' : 'robo_robot');
if (get_post_type() !== $pt){ get_footer(); exit; }

$pid = get_the_ID();

function rg_meta($pid,$k){
  $v = get_post_meta($pid, $k, true);
  return is_string($v) ? trim($v) : $v;
}


function rg_youtube_id($url){
  $url = trim((string)$url);
  if (!$url) return '';
  // youtu.be/<id>
  if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $url, $m)) return $m[1];
  // youtube.com/watch?v=<id>
  $parts = wp_parse_url($url);
  if (!empty($parts['query'])){
    parse_str($parts['query'], $q);
    if (!empty($q['v'])) return preg_replace('~[^A-Za-z0-9_-]~','', $q['v']);
  }
  // youtube.com/embed/<id>
  if (preg_match('~/embed/([A-Za-z0-9_-]{6,})~', $url, $m)) return $m[1];
  // youtube.com/shorts/<id>
  if (preg_match('~/shorts/([A-Za-z0-9_-]{6,})~', $url, $m)) return $m[1];
  return '';
}
function rg_lines_to_ul($txt){
  $txt = is_string($txt) ? trim($txt) : '';
  if (!$txt) return '';
  $lines = preg_split('/\R+/', $txt);
  $lines = array_values(array_filter(array_map('trim', $lines)));
  if (empty($lines)) return '';
  $out = '<ul class="rg-bullets">';
  foreach($lines as $l){ $out .= '<li>'.esc_html($l).'</li>'; }
  $out .= '</ul>';
  return $out;
}
function rg_faq_parse($raw){
  $raw = is_string($raw) ? trim($raw) : '';
  if (!$raw) return [];
  $blocks = preg_split("/\R{2,}/", $raw);
  $items = [];
  foreach($blocks as $b){
    $b = trim($b);
    if(!$b) continue;
    $lines = preg_split('/\R+/', $b);
    $q = trim(array_shift($lines) ?? '');
    $a = trim(implode("\n", $lines));
    if($q && $a) $items[] = ['q'=>$q,'a'=>$a];
  }
  return $items;
}
function rg_gallery_urls($pid){
  $urls = [];

  // 1) Featured image first
  if (has_post_thumbnail($pid)){
    $src = get_the_post_thumbnail_url($pid, 'large');
    if ($src) $urls[] = $src;
  }

  // 2) Known meta keys (comma-separated attachment IDs)
  $meta_keys = ['_rg_gallery','rg_gallery','rg_gallery_ids','_rg_gallery_ids','_rf_gallery','_rf_gallery_ids'];
  foreach($meta_keys as $k){
    $raw = (string) get_post_meta($pid, $k, true);
    $ids = array_filter(array_map('trim', explode(',', $raw)));
    foreach($ids as $id){
      $u = wp_get_attachment_image_url((int)$id, 'large');
      if ($u) $urls[] = $u;
    }
  }

  // 3) Gutenberg/Classic gallery in content (IDs)
  $content = get_post_field('post_content', $pid);
  if ($content){
    if (preg_match_all('/wp:image\s+\{\"id\":(\d+)/', $content, $m)){
      foreach($m[1] as $id){
        $u = wp_get_attachment_image_url((int)$id,'large');
        if($u) $urls[] = $u;
      }
    }
    if (preg_match_all('/\[gallery[^\]]*ids=\"([0-9,\s]+)\"[^\]]*\]/', $content, $g)){
      foreach($g[1] as $list){
        $ids = array_filter(array_map('trim', explode(',', $list)));
        foreach($ids as $id){
          $u = wp_get_attachment_image_url((int)$id,'large');
          if($u) $urls[] = $u;
        }
      }
    }
  }

  // 4) Attached images (Media uploaded to this post)
  $children = get_children([
    'post_parent' => $pid,
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'numberposts' => 50,
  ]);
  if (!empty($children)){
    foreach($children as $att){
      $u = wp_get_attachment_image_url((int)$att->ID,'large');
      if($u) $urls[] = $u;
    }
  }

  // Unique + keep order
  $urls = array_values(array_unique(array_filter($urls)));
  return $urls;
}

$man = rg_meta($pid,'_rf_manufacturer');
$seg = rg_meta($pid,'_rf_segment');
$tagline = rg_meta($pid,'_rf_tagline');
$price = rg_meta($pid,'_rf_price_month');

$h1 = rg_meta($pid,'_rf_highlight_1');
$h2 = rg_meta($pid,'_rf_highlight_2');
$h3 = rg_meta($pid,'_rf_highlight_3');
$highlights = array_values(array_filter([$h1,$h2,$h3]));

$dock = get_post_meta($pid,'rg_docking_options',true);
if(!is_array($dock)) $dock = [];
$dock_labels = [
  'dockingstation'=>'🔌 Dockingstation',
  'charging_station'=>'⚡ Charging Station',
  'self_cleaning_dock'=>'🧼 Selbstreinigende Dockingstation',
  'mobile_water_tank'=>'💧 Mobile Wassertank',
];

$cta = rg_meta($pid,'_rf_cta_url');
$ds = rg_meta($pid,'_rf_datasheet_url');

// SureForms: optional per robot (opens in modal)
$sureform_sc = rg_meta($pid,'rg_sureforms_shortcode');
if(!$sureform_sc){ $sureform_sc = (string) get_option('rg_sureforms_global_shortcode',''); }


$ideal = rg_lines_to_ul(rg_meta($pid,'rg_ideal_for'));
$notideal = rg_lines_to_ul(rg_meta($pid,'rg_not_ideal_for'));

$video = rg_meta($pid,'rg_video_url');
$faq_items = rg_faq_parse(rg_meta($pid,'rg_faq_raw'));

$gallery = rg_gallery_urls($pid);
$fallback = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1200' height='700'%3E%3Crect width='100%25' height='100%25' fill='%23f3f4f6'/%3E%3Ctext x='50%25' y='50%25' font-size='44' text-anchor='middle' fill='%236b7280' font-family='Arial'%3EKein Bild%3C/text%3E%3C/svg%3E";

$spec_tiles = [
  ['Flächenleistung', rg_meta($pid,'_rf_m2h')],
  ['Akkulaufzeit', rg_meta($pid,'_rf_battery_hours')],
  ['Arbeitsbreite', rg_meta($pid,'_rf_working_width')],
  ['Geräuschpegel', rg_meta($pid,'_rf_noise')],
  ['Reinwasser', rg_meta($pid,'_rf_clean_water')],
  ['Abwasser', rg_meta($pid,'_rf_dirty_water')],
  ['Tank gesamt', rg_meta($pid,'_rf_tank_liters')],
  ['Ladezeit', rg_meta($pid,'_rf_charge_time')],
];

$table_rows = [
  ['Abmessungen (L×B×H)', rg_meta($pid,'_rf_dimensions')],
  ['Navigation/Sensorik', rg_meta($pid,'_rf_nav')],
];

$content_sections = [
  ['Aufgabenprofil & Kernkompetenzen', rg_meta($pid,'_rf_tasks_profile')],
  ['Besonderheiten', rg_meta($pid,'_rf_features')],
  ['Einsatzbereiche', rg_meta($pid,'_rf_use_cases')],
  ['Wirtschaftlichkeit', rg_meta($pid,'_rf_economics')],
  ['Digital & Updates', rg_meta($pid,'_rf_digital')],
  ['Zubehör & Features', rg_meta($pid,'_rf_accessories')],
];

$archive_link = (is_page('roboter') ? get_permalink(get_page_by_path('roboter')) : get_post_type_archive_link($pt));
if (!$archive_link) $archive_link = get_post_type_archive_link($pt);
?>
<div class="rg-wrap">

  <div class="rg-prodgrid">
    <div>
      <div class="rg-gallery" data-rg-gallery='<?php echo esc_attr(wp_json_encode($gallery)); ?>'>
        <div class="rg-gallery-main">
          <img src="<?php echo esc_url($gallery[0] ?? $fallback); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
        </div>

        <?php if(count($gallery)>1): ?>
          <button class="rg-gbtn prev" type="button" aria-label="Vorheriges Bild">‹</button>
          <button class="rg-gbtn next" type="button" aria-label="Nächstes Bild">›</button>
          <div class="rg-thumbs">
            <?php foreach($gallery as $idx=>$u): ?>
              <img src="<?php echo esc_url($u); ?>" class="<?php echo $idx===0?'active':''; ?>" alt="">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

	      <?php if($ideal || $notideal): ?>
	        <div class="rg-fitgrid">
	          <?php if($ideal): ?>
	            <div class="rg-fitbox ideal">
	              <div class="t">Ideal für</div>
	              <?php echo $ideal; ?>
	            </div>
	          <?php endif; ?>
	          <?php if($notideal): ?>
	            <div class="rg-fitbox notideal">
	              <div class="t">Nicht ideal für</div>
	              <?php echo $notideal; ?>
	            </div>
	          <?php endif; ?>
	        </div>
	      <?php endif; ?>
    </div>

    <div class="rg-headbox">
      <div class="rg-badges">
        <?php if($man): ?><a class="rg-badge-link" href="<?php echo esc_url(add_query_arg(['mfg'=>sanitize_title($man)], $archive_link)); ?>"><span class="rg-badge"><?php echo esc_html($man); ?></span></a><?php endif; ?>
        <?php if($seg): ?><a class="rg-pill" href="<?php echo esc_url(add_query_arg(['seg'=>sanitize_title($seg)], $archive_link)); ?>"><?php echo esc_html($seg); ?></a><?php endif; ?>
      </div>

      <h1 class="rg-title"><?php the_title(); ?></h1>

<?php if(!empty($highlights)): ?>
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:12px">
    <?php foreach($highlights as $h): ?>
      <span class="rg-hchip">✓ <?php echo esc_html($h); ?></span>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
      <?php if($tagline): ?><div class="rg-muted" style="margin-top:8px"><?php echo esc_html($tagline); ?></div><?php endif; ?>
      <?php if($price): ?><div class="rg-price" style="margin-top:14px"><?php echo esc_html($price); ?></div><?php endif; ?>

      <div class="rg-ctaRow">
        <?php if($sureform_sc): ?>
          <button class="rg-btn primary" type="button" data-rg-open-form="1">Beratung anfragen</button>
        <?php elseif($cta): ?>
          <a class="rg-btn primary" href="<?php echo esc_url($cta); ?>">Beratung anfragen</a>
        <?php endif; ?>
        <?php if($ds): ?><a class="rg-btn" href="<?php echo esc_url($ds); ?>" target="_blank" rel="noopener">Datenblatt</a><?php endif; ?>
        <?php if($archive_link): ?><a class="rg-btn" href="<?php echo esc_url($archive_link); ?>">Alle Roboter</a><?php endif; ?>
      </div>

      <?php if(!empty($dock)): ?>
        <div class="rg-headbox-block rg-headbox-docking">
          <div class="rg-headbox-subtitle">Docking & Stationen</div>
          <div class="rg-chiprow" style="margin-top:10px">
            <?php foreach($dock as $k): if(!isset($dock_labels[$k])) continue; ?>
              <span class="rg-chip"><?php echo esc_html($dock_labels[$k]); ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="rg-headbox-block rg-headbox-forum">
        <div class="rg-headbox-subtitle">Diskussion im Forum</div>
        <div id="rg_forum_mount" class="rg-forum-mount"></div>
      </div>
    </div>
  </div>

  <?php
    $pc = get_post_field('post_content', $pid);
    if ($pc):
  ?>
    <div class="rg-divider"></div>
    <div class="rg-section rg-maintext">
      <h2 class="rg-h2">Über den <?php echo esc_html(get_the_title($pid)); ?></h2>
      <div class="rg-editor">
        <?php echo apply_filters('the_content', $pc); ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Docking moved into rg-headbox -->

  <div class="rg-divider"></div>

  <div class="rg-section soft">
    <h2 class="rg-h2">Technische Daten</h2>
    <div class="rg-specgrid">
      <?php foreach($spec_tiles as $t): if(!$t[1]) continue; ?>
        <div class="rg-spec">
          <div class="k"><?php echo esc_html($t[0]); ?></div>
          <div class="v"><?php echo esc_html($t[1]); ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if(($table_rows[0][1] ?? '') || ($table_rows[1][1] ?? '')): ?>
      <div class="rg-divider"></div>
      <table class="rg-table">
        <?php foreach($table_rows as $r): if(!$r[1]) continue; ?>
          <tr><th><?php echo esc_html($r[0]); ?></th><td><?php echo esc_html($r[1]); ?></td></tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>

  <div class="rg-divider"></div>

  <?php
    $inserted_video = false;
    foreach($content_sections as $sec):
      $title = $sec[0];
      $raw = is_string($sec[1]) ? trim($sec[1]) : '';
      if(!$raw) continue;

      $body = rg_lines_to_ul($raw);
      if(!$body) $body = wpautop(esc_html($raw));
  ?>
    <div class="rg-section">
      <h2 class="rg-h2"><?php echo esc_html($title); ?></h2>
      <div class="rg-card nohead"><?php echo $body; ?></div>
    </div>

    <?php if(!$inserted_video && $video && $title==='Aufgabenprofil & Kernkompetenzen'): $inserted_video=true; ?>
      <div class="rg-section">
        <h2 class="rg-h2">Video</h2>
        <div class="rg-video" data-rg-video="<?php echo esc_attr($video); ?>" role="button" tabindex="0" aria-label="Video laden">
          <?php $ytid = rg_youtube_id($video); $thumb = $ytid ? "https://img.youtube.com/vi/".$ytid."/hqdefault.jpg" : ""; ?>
          <img src="<?php echo esc_url($thumb ?: 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='); ?>" alt="">
          <div class="play"><span>▶</span></div>
          <div class="rg-video-consent" hidden>
            <div class="rg-video-consent-card">
              <div class="t">Datenschutz-Hinweis</div>
              <div class="d">Beim Laden des Videos werden ggf. Daten an den Anbieter (z. B. YouTube/Vimeo) übertragen. Erst nach deiner Zustimmung wird das Video geladen.</div>
              <div class="a">
                <button type="button" class="rg-btn primary" data-rg-video-accept="1">Video laden</button>
                <button type="button" class="rg-btn" data-rg-video-decline="1">Abbrechen</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

  <?php endforeach; ?>

  <?php if(!empty($faq_items)): ?>
    <div class="rg-divider"></div>
    <div class="rg-section rg-faq-section soft">
      <h2 class="rg-h2">FAQ</h2>
      <div class="rg-faq-wrap">
        <?php foreach($faq_items as $it): ?>
          <div class="rg-faq-item">
            <div class="rg-faq-q">
              <div><?php echo esc_html($it['q']); ?></div>
              <div class="icon">+</div>
            </div>
            <div class="rg-faq-a"><?php echo wpautop(esc_html($it['a'])); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if($sureform_sc): ?>
    <div id="rg_form_tpl" style="display:none">
      <?php echo do_shortcode($sureform_sc); ?>
    </div>
  <?php endif; ?>

</div>

<div id="rg_modal" class="rg-modal" aria-hidden="true" data-mode="media">
  <div class="rg-modal-inner">
    <div class="rg-modal-bar">
      <button id="rg_modal_close" class="rg-modal-close" type="button" aria-label="Schließen">×</button>
    </div>
    <div id="rg_modal_content" class="rg-modal-content"></div>
  </div>
</div>

<?php get_footer(); ?>