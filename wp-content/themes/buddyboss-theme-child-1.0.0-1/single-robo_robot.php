<?php
/**
 * Single template for Robo Robots (CPT: robo_robot)
 * Robo-Guru Style: Gallery left, Facts right, suitable/not ideal, FAQ, CTA
 */

get_header();

if ( have_posts() ) : while ( have_posts() ) : the_post();
  $id = get_the_ID();

  // --- Helpers
  $get_meta = function($key, $default = '') use ($id) {
    $v = get_post_meta($id, $key, true);
    if ($v === '' || $v === null) return $default;
    return $v;
  };

  $split_list = function($raw) {
    $raw = (string) $raw;
    $raw = trim($raw);
    if ($raw === '') return [];
    // Support newline, semicolon, comma
    $items = preg_split('/\r\n|\r|\n|;|,/', $raw);
    $items = array_map('trim', $items);
    $items = array_filter($items, function($x){ return $x !== ''; });
    return array_values($items);
  };

  $tagline = $get_meta('_robo_tagline', '');
  $brand   = $get_meta('_robo_brand', '');
  $model   = $get_meta('_robo_model', '');
  $budget  = $get_meta('_robo_budget_class', '');
  $usecase = $get_meta('_robo_usecase', '');
  $cta_url = $get_meta('_robo_cta_url', site_url('/kontakt/'));
  $cta_txt = $get_meta('_robo_cta_text', 'Demo buchen');

  // Highlights: JSON array OR newline list
  $highlights = [];
  $hl_raw = $get_meta('_robo_highlights', '');
  if ($hl_raw) {
    $decoded = json_decode($hl_raw, true);
    if (is_array($decoded)) $highlights = $decoded;
    else $highlights = $split_list($hl_raw);
  } else {
    // fallback: 3 classic keys
    for ($i=1; $i<=3; $i++){
      $h = $get_meta('_robo_highlight_'.$i, '');
      if ($h) $highlights[] = $h;
    }
  }
  $highlights = array_slice($highlights, 0, 6);

  // Suitable / Not ideal
  $suitable = $split_list($get_meta('_robo_suitable_for', ''));
  $notideal = $split_list($get_meta('_robo_not_ideal_for', ''));

  // Facts (meta keys can be adjusted later; these are sane defaults)
  $facts = [
    ['Flächenleistung', '_robo_area_m2h', 'm²/h'],
    ['Akkulaufzeit', '_robo_battery_h', 'h'],
    ['Arbeitsbreite', '_robo_working_width_mm', 'mm'],
    ['Geräuschpegel', '_robo_noise_db', 'dB'],
    ['Frischwasser', '_robo_clean_water_l', 'L'],
    ['Schmutzwasser', '_robo_dirty_water_l', 'L'],
    ['Tank gesamt', '_robo_tank_total_l', 'L'],
    ['Ladezeit', '_robo_charge_h', 'h'],
    ['Navigation', '_robo_navigation', ''],
    ['Autonomie', '_robo_autonomy', ''],
    ['Service/Support', '_robo_service', ''],
  ];

  // Gallery: featured + attached images
  $gallery_ids = [];
  if (has_post_thumbnail($id)) $gallery_ids[] = get_post_thumbnail_id($id);

  $attached = get_attached_media('image', $id);
  if (!empty($attached)) {
    foreach ($attached as $att) {
      if (!in_array($att->ID, $gallery_ids, true)) $gallery_ids[] = $att->ID;
    }
  }
  // Also support a custom meta gallery list: comma-separated attachment IDs
  $meta_gallery = $get_meta('_robo_gallery_ids', '');
  if ($meta_gallery) {
    $ids = array_filter(array_map('intval', explode(',', $meta_gallery)));
    foreach ($ids as $gid) {
      if ($gid && !in_array($gid, $gallery_ids, true)) $gallery_ids[] = $gid;
    }
  }
  $gallery_ids = array_slice($gallery_ids, 0, 8);

  // FAQ: JSON array in _robo_faq OR q/a fields
  $faqs = [];
  $faq_raw = $get_meta('_robo_faq', '');
  if ($faq_raw) {
    $decoded = json_decode($faq_raw, true);
    if (is_array($decoded)) {
      foreach ($decoded as $row) {
        if (!is_array($row)) continue;
        $q = isset($row['q']) ? trim((string)$row['q']) : '';
        $a = isset($row['a']) ? trim((string)$row['a']) : '';
        if ($q && $a) $faqs[] = ['q'=>$q,'a'=>$a];
      }
    }
  }
  if (empty($faqs)) {
    for ($i=1; $i<=10; $i++){
      $q = $get_meta('_robo_faq_q'.$i, '');
      $a = $get_meta('_robo_faq_a'.$i, '');
      if ($q && $a) $faqs[] = ['q'=>$q,'a'=>$a];
    }
  }

  $rg_text = $get_meta('_robo_robo_guru_text', '');
  if (!$rg_text) {
    // fallback: use excerpt or content (short)
    $rg_text = has_excerpt() ? get_the_excerpt() : '';
  }
?>

<div class="rg-single">
  <header class="rg-single__hero">
    <div class="rg-single__inner">
      <div class="rg-single__kicker">
        <?php if ($brand) : ?><span class="rg-pill"><?php echo esc_html($brand); ?></span><?php endif; ?>
        <?php if ($model) : ?><span class="rg-pill"><?php echo esc_html($model); ?></span><?php endif; ?>
        <?php if ($budget) : ?><span class="rg-pill rg-pill--soft"><?php echo esc_html($budget); ?></span><?php endif; ?>
        <?php if ($usecase) : ?><span class="rg-pill rg-pill--soft"><?php echo esc_html($usecase); ?></span><?php endif; ?>
      </div>

      <h1 class="rg-single__title"><?php the_title(); ?></h1>

      <?php if ($tagline) : ?>
        <p class="rg-single__tagline"><?php echo esc_html($tagline); ?></p>
      <?php endif; ?>

      <?php if (!empty($highlights)) : ?>
        <div class="rg-single__highlights">
          <?php foreach ($highlights as $h) : ?>
            <span class="rg-chip"><?php echo esc_html($h); ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="rg-single__ctaRow">
        <a class="rg-btn rg-btn--primary" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_txt); ?></a>
        <a class="rg-btn rg-btn--ghost" href="<?php echo esc_url(site_url('/roboter/')); ?>">← Zur Übersicht</a>
      </div>
    </div>
  </header>

  <main class="rg-single__inner rg-single__main">
    <section class="rg-single__grid">
      <!-- Left: Gallery + Suitable -->
      <div class="rg-single__left">
        <div class="rg-card rg-card--gallery">
          <?php if (!empty($gallery_ids)) : ?>
            <div class="rg-gallery" data-rg-gallery>
              <div class="rg-gallery__main">
                <?php
                  $main_id = $gallery_ids[0];
                  echo wp_get_attachment_image($main_id, 'large', false, ['class'=>'rg-gallery__img','data-rg-main'=>1]);
                ?>
              </div>
              <?php if (count($gallery_ids) > 1) : ?>
                <div class="rg-gallery__thumbs">
                  <?php foreach ($gallery_ids as $idx => $gid) :
                    $thumb = wp_get_attachment_image($gid, 'thumbnail', false, [
                      'class' => 'rg-gallery__thumb'.($idx===0?' is-active':''),
                      'data-rg-thumb' => $gid,
                      'alt' => esc_attr(get_the_title($gid)),
                    ]);
                    echo '<button class="rg-gallery__thumbBtn" type="button" aria-label="Bild auswählen">'.$thumb.'</button>';
                  endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php else : ?>
            <div class="rg-gallery__placeholder">Kein Bild hinterlegt</div>
          <?php endif; ?>
        </div>

        <?php if (!empty($suitable) || !empty($notideal)) : ?>
          <div class="rg-suitWrap">
            <?php if (!empty($suitable)) : ?>
              <div class="rg-suit rg-suit--good">
                <h3>Für wen geeignet</h3>
                <ul>
                  <?php foreach ($suitable as $it) echo '<li>'.esc_html($it).'</li>'; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if (!empty($notideal)) : ?>
              <div class="rg-suit rg-suit--bad">
                <h3>Für wen nicht ideal</h3>
                <ul>
                  <?php foreach ($notideal as $it) echo '<li>'.esc_html($it).'</li>'; ?>
                </ul>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if ($rg_text) : ?>
          <div class="rg-card">
            <h2>Robo-Guru Einschätzung</h2>
            <p><?php echo wp_kses_post(wpautop($rg_text)); ?></p>
          </div>
        <?php endif; ?>

        <?php
          // Optional: render main content below
          $content = get_the_content();
          $content = trim((string)$content);
          if ($content !== '') :
        ?>
          <div class="rg-card">
            <h2>Details</h2>
            <div class="rg-content"><?php the_content(); ?></div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Right: Facts + CTA -->
      <aside class="rg-single__right">
        <div class="rg-card rg-card--facts">
          <div class="rg-card__head">
            <h2>Fakten auf einen Blick</h2>
            <span class="rg-pill rg-pill--outline">Praxisnah</span>
          </div>

          <dl class="rg-facts">
            <?php foreach ($facts as $row) :
              [$label, $key, $unit] = $row;
              $val = $get_meta($key, '');
              if ($val === '' || $val === null) continue;
              $val = is_string($val) ? trim($val) : $val;
              if ($val === '') continue;
              $out = esc_html($val);
              if ($unit && is_numeric($val)) $out .= ' <span class="rg-unit">'.esc_html($unit).'</span>';
              elseif ($unit && !is_numeric($val)) $out .= ' <span class="rg-unit">'.esc_html($unit).'</span>';
            ?>
              <div class="rg-fact">
                <dt><?php echo esc_html($label); ?></dt>
                <dd><?php echo wp_kses_post($out); ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>

          <div class="rg-ctaCard">
            <div class="rg-ctaCard__txt">
              <strong>Passt der Roboter zu deinem Objekt?</strong>
              <p>Ich sag dir ehrlich, ob das Modell für Fläche, Personal & Budget wirklich Sinn macht.</p>
            </div>
            <a class="rg-btn rg-btn--primary rg-btn--full" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_txt); ?></a>
            <a class="rg-btn rg-btn--ghost rg-btn--full" href="<?php echo esc_url(site_url('/roboter-vergleich/')); ?>">Zum Vergleich</a>
          </div>
        </div>
      </aside>
    </section>

    <?php if (!empty($faqs)) : ?>
      <section class="rg-faq rg-card">
        <h2>FAQ</h2>
        <div class="rg-faq__list">
          <?php foreach ($faqs as $i => $row) : ?>
            <details class="rg-faq__item" <?php echo $i===0 ? 'open' : ''; ?>>
              <summary><?php echo esc_html($row['q']); ?></summary>
              <div class="rg-faq__answer"><?php echo wp_kses_post(wpautop($row['a'])); ?></div>
            </details>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <?php if (!empty($faqs)) : ?>
      <?php
        $faq_schema = [
          '@context' => 'https://schema.org',
          '@type' => 'FAQPage',
          'mainEntity' => array_map(function($row) {
            return [
              '@type' => 'Question',
              'name' => wp_strip_all_tags($row['q']),
              'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => wp_strip_all_tags($row['a']),
              ],
            ];
          }, $faqs),
        ];
      ?>
      <script type="application/ld+json">
        <?php echo wp_json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
      </script>
    <?php endif; ?>

  </main>
</div>

<script>
(function(){
  const root = document.querySelector('[data-rg-gallery]');
  if(!root) return;
  const main = root.querySelector('[data-rg-main]');
  const buttons = root.querySelectorAll('.rg-gallery__thumbBtn');
  if(!main || !buttons.length) return;

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const img = btn.querySelector('img');
      if(!img) return;
      const src = img.getAttribute('src');
      const srcset = img.getAttribute('srcset');
      const sizes = img.getAttribute('sizes');

      // Build a larger image url if available: use srcset last entry
      let bestSrc = src;
      if (srcset) {
        const parts = srcset.split(',').map(s => s.trim()).filter(Boolean);
        const last = parts[parts.length-1];
        if (last) {
          bestSrc = last.split(' ')[0];
        }
      }
      main.setAttribute('src', bestSrc);
      if (srcset) main.setAttribute('srcset', srcset);
      if (sizes) main.setAttribute('sizes', sizes);

      root.querySelectorAll('.rg-gallery__thumb').forEach(t => t.classList.remove('is-active'));
      const thumb = btn.querySelector('.rg-gallery__thumb');
      if(thumb) thumb.classList.add('is-active');
    });
  });
})();
</script>

<?php
endwhile; endif;

get_footer();
