<?php
get_header();

$pt = (class_exists('RG_Robot_Profile_Combined')) ? RG_Robot_Profile_Combined::detect_post_type() : (post_type_exists('roboter') ? 'roboter' : 'robo_robot');

function rg_img_url($post_id){
  // 1) Featured image
  if (has_post_thumbnail($post_id)){
    $src = get_the_post_thumbnail_url($post_id,'large');
    if ($src) return $src;
  }

  // 2) Known meta gallery keys (comma-separated IDs)
  foreach(['_rg_gallery','rg_gallery','rg_gallery_ids','_rg_gallery_ids','_rf_gallery','_rf_gallery_ids'] as $k){
    $raw = (string) get_post_meta($post_id, $k, true);
    $ids = array_filter(array_map('trim', explode(',', $raw)));
    if (!empty($ids)){
      $src = wp_get_attachment_image_url((int)$ids[0], 'large');
      if ($src) return $src;
    }
  }

  // 3) First attached image
  $children = get_children([
    'post_parent' => $post_id,
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'numberposts' => 1,
  ]);
  if (!empty($children)){
    foreach($children as $att){
      $src = wp_get_attachment_image_url((int)$att->ID, 'large');
      if ($src) return $src;
    }
  }

  return '';
}
function rg_short_specs($post_id){
  $specs = [];
  $ww = get_post_meta($post_id,'_rf_working_width',true);
  $m2h = get_post_meta($post_id,'_rf_m2h',true);
  $noise = get_post_meta($post_id,'_rf_noise',true);
  if($ww) $specs[] = ['Arbeitsbreite', $ww];
  if($m2h) $specs[] = ['m²/h', $m2h];
  if($noise) $specs[] = ['dB', $noise];
  return $specs;
}
function rg_meta($pid,$k){ $v=get_post_meta($pid,$k,true); return is_string($v)?trim($v):$v; }

$compare_raw = isset($_GET['compare']) ? sanitize_text_field($_GET['compare']) : '';
$compare_ids = array_values(array_filter(array_map('intval', explode(',', $compare_raw))));
$compare_ids = array_slice($compare_ids, 0, 3);

$base = is_page('roboter') ? get_permalink() : get_post_type_archive_link($pt);
?>
<div class="rg-wrap">

<?php if(!empty($compare_ids)): ?>
  <?php $posts = get_posts(['post_type'=>$pt,'post__in'=>$compare_ids,'orderby'=>'post__in','posts_per_page'=>3]); ?>
  <div class="rg-card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
      <div>
        <h1 class="rg-title" style="margin-bottom:6px">Vergleich</h1>
        <div class="rg-muted">Vergleich von bis zu 3 Robotern.</div>
      </div>
      <a class="rg-pill" href="<?php echo esc_url(remove_query_arg('compare')); ?>">← Zurück zur Übersicht</a>
    </div>

    <div class="rg-divider"></div>

    <table class="rg-table">
      <tr><th>Modell</th><?php foreach($posts as $p): ?><td><strong><?php echo esc_html(get_the_title($p)); ?></strong></td><?php endforeach; ?></tr>
      <tr><th>Hersteller</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_manufacturer')); ?></td><?php endforeach; ?></tr>
      <tr><th>Kategorie</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_segment')); ?></td><?php endforeach; ?></tr>
      <tr><th>Flächenleistung</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_m2h')); ?></td><?php endforeach; ?></tr>
      <tr><th>Akkulaufzeit</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_battery_hours')); ?></td><?php endforeach; ?></tr>
      <tr><th>Arbeitsbreite</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_working_width')); ?></td><?php endforeach; ?></tr>
      <tr><th>Reinwasser</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_clean_water')); ?></td><?php endforeach; ?></tr>
      <tr><th>Abwasser</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_dirty_water')); ?></td><?php endforeach; ?></tr>
      <tr><th>Geräuschpegel</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_noise')); ?></td><?php endforeach; ?></tr>
      <tr><th>Abmessungen</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_dimensions')); ?></td><?php endforeach; ?></tr>
      <tr><th>Navigation</th><?php foreach($posts as $p): ?><td><?php echo esc_html(rg_meta($p->ID,'_rf_nav')); ?></td><?php endforeach; ?></tr>
      <tr><th>Links</th><?php foreach($posts as $p): ?><td><a class="rg-pill" href="<?php echo esc_url(get_permalink($p)); ?>">Details →</a></td><?php endforeach; ?></tr>
    </table>
  </div>

<?php else: ?>

  <?php
    $ids = get_posts(['post_type'=>$pt,'posts_per_page'=>-1,'fields'=>'ids']);
    $mfgs=[]; $segs=[];
    foreach($ids as $pid){
      $man = get_post_meta($pid,'_rf_manufacturer',true);
      $segment = get_post_meta($pid,'_rf_segment',true);
      if($man) $mfgs[$man]=true;
      if($segment) $segs[$segment]=true;
    }
    $mfgs=array_keys($mfgs); sort($mfgs);
    $segs=array_keys($segs); sort($segs);
  ?>

  <h1 class="rg-title" style="margin-bottom:10px">Roboter</h1>
  <div class="rg-muted" style="margin-bottom:16px">Filtere & suche – vergleiche bis zu 3 Roboter.</div>

  <div class="rg-card">
    <div class="rg-filterbar">
      <div class="rg-filter-top">
        <div class="rg-search">
          <span style="font-weight:950;color:#111">🔎</span>
          <input id="rg_search" type="search" placeholder="Suchen… (z.B. MT1, Kehrsaugmaschine, 65 dB)" autocomplete="off">
        </div>

        <div class="rg-sort">
          <label for="rg_sort">Sortierung:</label>
          <select id="rg_sort">
            <option value="title_asc">A–Z (Modell)</option>
            <option value="title_desc">Z–A (Modell)</option>
            <option value="mfg_asc">Hersteller A–Z</option>
            <option value="seg_asc">Kategorie A–Z</option>
          </select>
        </div>

        <button id="rg_clear" class="rg-clear" type="button">Filter zurücksetzen</button>
        <div id="rg_count" class="rg-count">0 Treffer</div>
      </div>

      <?php if(!empty($mfgs)): ?>
      <div class="rg-chiprow">
        <span class="rg-chiplabel"><span class="rg-ico">🏭</span>Hersteller:</span>
        <?php foreach($mfgs as $t): $slug=sanitize_title($t); ?>
          <button class="rg-pill" type="button" data-rg-filter-mfg="<?php echo esc_attr($slug); ?>" aria-pressed="false"><?php echo esc_html($t); ?></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if(!empty($segs)): ?>
      <div class="rg-chiprow">
        <span class="rg-chiplabel"><span class="rg-ico">🧩</span>Kategorie:</span>
        <?php foreach($segs as $t): $slug=sanitize_title($t); ?>
          <button class="rg-pill" type="button" data-rg-filter-seg="<?php echo esc_attr($slug); ?>" aria-pressed="false"><?php echo esc_html($t); ?></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="rg-divider"></div>

  <?php $q = new WP_Query(['post_type'=>$pt,'posts_per_page'=>500,'orderby'=>'title','order'=>'ASC']); ?>
  <div class="rg-cardgrid" data-rg-grid>
    <?php while($q->have_posts()): $q->the_post();
      $pid = get_the_ID();
      $man = rg_meta($pid,'_rf_manufacturer');
      $segment = rg_meta($pid,'_rf_segment');
      $tagline = rg_meta($pid,'_rf_tagline');
      $h1 = rg_meta($pid,'_rf_highlight_1');
      $h2 = rg_meta($pid,'_rf_highlight_2');
      $h3 = rg_meta($pid,'_rf_highlight_3');
      $highlights = array_values(array_filter([$h1,$h2,$h3]));
      $img = rg_img_url($pid);
      $mfg_slug = $man ? sanitize_title($man) : '';
      $seg_slug = $segment ? sanitize_title($segment) : '';
      $specs = rg_short_specs($pid);
      $hay = trim(get_the_title().' '.$tagline.' '.$man.' '.$segment.' '.implode(' ', array_map(fn($x)=>$x[1], $specs)));
    ?>
      <div class="rg-card" data-rg-card
           data-id="<?php echo esc_attr($pid); ?>"
           data-title="<?php echo esc_attr(mb_strtolower(get_the_title())); ?>"
           data-title-label="<?php echo esc_attr(get_the_title()); ?>"
           data-mfg="<?php echo esc_attr($mfg_slug); ?>"
           data-seg="<?php echo esc_attr($seg_slug); ?>"
           data-mfg-label="<?php echo esc_attr($man); ?>"
           data-seg-label="<?php echo esc_attr($segment); ?>"
           data-hay="<?php echo esc_attr($hay); ?>">

        <div class="rg-tile">
          <?php if($img): ?>
            <div class="rg-media">
              <a href="<?php the_permalink(); ?>" style="display:block;height:100%"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>"></a>
            </div>
          <?php else: ?>
            <div class="rg-media" style="display:flex;align-items:center;justify-content:center;color:#6b7280;font-weight:900">Kein Bild</div>
          <?php endif; ?>

          <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
            <div class="rg-badges" style="margin-bottom:0">
              <?php if($man): ?><a class="rg-badge-link" href="<?php echo esc_url(add_query_arg(['mfg'=>$mfg_slug], $base)); ?>"><span class="rg-badge"><?php echo esc_html($man); ?></span></a><?php endif; ?>
              <?php if($segment): ?><a class="rg-pill" href="<?php echo esc_url(add_query_arg(['seg'=>$seg_slug], $base)); ?>"><?php echo esc_html($segment); ?></a><?php endif; ?>
            </div>
            <label class="rg-compare-toggle" title="Zum Vergleich hinzufügen">
              <input type="checkbox" data-rg-compare="<?php echo esc_attr($pid); ?>"> Vergleichen
            </label>
          </div>

          <h3><?php the_title(); ?></h3>
          <?php if($tagline): ?><div class="rg-desc"><?php echo esc_html($tagline); ?></div><?php else: ?><div class="rg-desc rg-muted">Kurzbeschreibung folgt.</div><?php endif; ?>

<?php if(!empty($highlights)): ?>
  <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px">
    <?php foreach(array_slice($highlights,0,2) as $h): ?>
      <span class="rg-hchip" style="font-size:12px;padding:7px 10px">✓ <?php echo esc_html($h); ?></span>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

          <?php if(!empty($specs)): ?><div class="rg-quick"><?php foreach($specs as $sp): ?><span class="rg-qspec">⚙️ <?php echo esc_html($sp[0].': '.$sp[1]); ?></span><?php endforeach; ?></div><?php endif; ?>

          <a class="cta" href="<?php the_permalink(); ?>">Details ansehen →</a>
        </div>
      </div>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>

<?php endif; ?>

</div>

<div id="rg_modal" class="rg-modal" aria-hidden="true">
  <div class="rg-modal-inner">
    <div class="rg-modal-bar"><button id="rg_modal_close" class="rg-modal-close" type="button">Schließen</button></div>
    <div id="rg_modal_content" class="rg-modal-content"></div>
  </div>
</div>

<div id="rg_comparebar" class="rg-comparebar" aria-live="polite">
  <div class="inner">
    <strong>Vergleich:</strong>
    <div id="rg_comparechips" class="rg-comparechips"></div>
    <div class="rg-compare-actions">
      <a id="rg_compare_btn" class="primary" href="#">Vergleichen</a>
      <button id="rg_compare_clear" type="button">Vergleich leeren</button>
    </div>
  </div>
</div>

<?php get_footer(); ?>
