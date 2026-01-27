<?php
/**
 * Archive template for Robo Robots (CPT: robo_robot)
 */
get_header();

$paged = max(1, (int) get_query_var('paged'));

$search  = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
$budget  = isset($_GET['budget']) ? sanitize_text_field(wp_unslash($_GET['budget'])) : '';
$usecase = isset($_GET['usecase']) ? sanitize_text_field(wp_unslash($_GET['usecase'])) : '';
$brand   = isset($_GET['brand']) ? sanitize_text_field(wp_unslash($_GET['brand'])) : '';
$sort    = isset($_GET['sort']) ? sanitize_text_field(wp_unslash($_GET['sort'])) : 'new';

$meta_query = ['relation' => 'AND'];

if ($budget !== '') {
  $meta_query[] = ['key' => '_robo_budget_class', 'value' => $budget, 'compare' => '='];
}
if ($usecase !== '') {
  $meta_query[] = ['key' => '_robo_usecase', 'value' => $usecase, 'compare' => 'LIKE'];
}
if ($brand !== '') {
  $meta_query[] = ['key' => '_robo_brand', 'value' => $brand, 'compare' => '='];
}

$order_by = 'date';
$order    = 'DESC';
$meta_key = '';

switch ($sort) {
  case 'name':
    $order_by = 'title'; $order = 'ASC';
    break;
  case 'roi':
    $order_by = 'meta_value_num'; $meta_key = '_robo_roi_score'; $order = 'DESC';
    break;
  case 'area':
    $order_by = 'meta_value_num'; $meta_key = '_robo_area_m2h'; $order = 'DESC';
    break;
  case 'new':
  default:
    $order_by = 'date'; $order = 'DESC';
}

$args = [
  'post_type'      => 'robo_robot',
  'post_status'    => 'publish',
  's'              => $search,
  'paged'          => $paged,
  'posts_per_page' => 12,
  'meta_query'     => count($meta_query) > 1 ? $meta_query : [],
  'orderby'        => $order_by,
  'order'          => $order,
];

if ($meta_key) {
  $args['meta_key'] = $meta_key;
}

$qry = new WP_Query($args);

function rg_opt($key, $label, $selected) {
  $sel = ($key === $selected) ? ' selected' : '';
  return '<option value="'.esc_attr($key).'"'.$sel.'>'.esc_html($label).'</option>';
}

?>
<style>
.rg-wrap{max-width:1200px;margin:0 auto;padding:24px 16px}
.rg-head{display:flex;gap:16px;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;margin-bottom:16px}
.rg-title{margin:0}
.rg-filters{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr 1fr;gap:10px;width:100%}
@media (max-width: 960px){.rg-filters{grid-template-columns:1fr 1fr}.rg-head{align-items:stretch}}
.rg-filters input,.rg-filters select{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:12px}
.rg-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
@media (max-width: 980px){.rg-grid{grid-template-columns:repeat(2,1fr)}}
@media (max-width: 640px){.rg-grid{grid-template-columns:1fr}}
.rg-card{border:1px solid #eee;border-radius:16px;padding:14px;background:#fff;box-shadow:0 1px 10px rgba(0,0,0,.04)}
.rg-card h3{margin:8px 0 6px;font-size:18px}
.rg-meta{font-size:13px;opacity:.8;display:flex;flex-wrap:wrap;gap:8px}
.rg-actions{display:flex;gap:10px;align-items:center;margin-top:12px}
.rg-btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 12px;border-radius:12px;border:1px solid #ddd;text-decoration:none}
.rg-btn.primary{border-color:#111;background:#111;color:#fff}
.rg-compare{margin-left:auto;display:flex;gap:8px;align-items:center;font-size:13px}
.rg-pagination{margin:22px 0;display:flex;justify-content:center}
.rg-badge{font-size:12px;padding:3px 8px;border-radius:999px;background:#f4f4f4;border:1px solid #eee}
</style>

<div class="rg-wrap">
  <div class="rg-head">
    <div>
      <h1 class="rg-title">Roboter</h1>
      <div class="rg-meta">
        <span class="rg-badge"><?php echo (int) $qry->found_posts; ?> Modelle</span>
        <span class="rg-badge">Filter + Vergleich</span>
      </div>
    </div>

    <form method="get" class="rg-filters">
      <label class="rg-filter">
        <span class="rg-filter__label">Suche</span>
        <input type="text" name="q" placeholder="z. B. CC1, Nexaro, Phantas …" value="<?php echo esc_attr($search); ?>" aria-label="Roboter suchen">
      </label>
      <label class="rg-filter">
        <span class="rg-filter__label">Hersteller</span>
        <select name="brand" aria-label="Hersteller filtern">
        <option value="">Hersteller</option>
        <?php
        $brands = ['Pudu'=>'Pudu','Gausium'=>'Gausium','Nexaro'=>'Nexaro','Kärcher'=>'Kärcher','Sonstiges'=>'Sonstiges'];
        foreach ($brands as $k=>$v) echo rg_opt($k, $v, $brand);
        ?>
        </select>
      </label>
      <label class="rg-filter">
        <span class="rg-filter__label">Einsatz</span>
        <select name="usecase" aria-label="Einsatz filtern">
        <option value="">Einsatz</option>
        <?php
        $usecases = ['Halle'=>'Hallen/Industrie','Retail'=>'Retail/Supermarkt','Hotel'=>'Hotel','Klinik'=>'Klinik','Logistik'=>'Logistik'];
        foreach ($usecases as $k=>$v) echo rg_opt($k, $v, $usecase);
        ?>
        </select>
      </label>
      <label class="rg-filter">
        <span class="rg-filter__label">Budget</span>
        <select name="budget" aria-label="Budget filtern">
        <option value="">Budget</option>
        <?php
        $budgets = ['S'=>'S (Budget)','M'=>'M (Standard)','L'=>'L (Premium)'];
        foreach ($budgets as $k=>$v) echo rg_opt($k, $v, $budget);
        ?>
        </select>
      </label>
      <label class="rg-filter">
        <span class="rg-filter__label">Sortierung</span>
        <select name="sort" aria-label="Sortierung wählen">
        <?php
        echo rg_opt('new','Sort: Neueste',$sort);
        echo rg_opt('name','Sort: Name A-Z',$sort);
        echo rg_opt('roi','Sort: ROI-Score',$sort);
        echo rg_opt('area','Sort: Flächenleistung',$sort);
        ?>
        </select>
      </label>
    </form>
  </div>

  <?php if ($qry->have_posts()): ?>
    <div class="rg-grid">
      <?php while ($qry->have_posts()): $qry->the_post();
        $id = get_the_ID();
        $brand_v = get_post_meta($id, '_robo_brand', true);
        $area    = get_post_meta($id, '_robo_area_m2h', true);
        $noise   = get_post_meta($id, '_robo_noise_db', true);
        $roi     = get_post_meta($id, '_robo_roi_score', true);
      ?>
        <article class="rg-card">
          <a href="<?php the_permalink(); ?>" style="display:block; border-radius:14px; overflow:hidden;">
            <?php if (has_post_thumbnail()): the_post_thumbnail('medium_large'); else: ?>
              <div style="aspect-ratio:16/10;background:#f2f2f2;display:flex;align-items:center;justify-content:center;">Kein Bild</div>
            <?php endif; ?>
          </a>

          <h3><a href="<?php the_permalink(); ?>" style="text-decoration:none;"><?php the_title(); ?></a></h3>

          <div class="rg-meta">
            <?php if ($brand_v) echo '<span class="rg-badge">'.esc_html($brand_v).'</span>'; ?>
            <?php if ($area) echo '<span class="rg-badge">bis '.esc_html($area).' m²/h</span>'; ?>
            <?php if ($noise) echo '<span class="rg-badge">'.esc_html($noise).' dB</span>'; ?>
            <?php if ($roi) echo '<span class="rg-badge">ROI '.esc_html($roi).'</span>'; ?>
          </div>

          <div class="rg-actions">
            <a class="rg-btn primary" href="<?php the_permalink(); ?>">Details</a>

            <label class="rg-compare">
              <input type="checkbox" class="rg-compare-cb" data-id="<?php echo esc_attr($id); ?>">
              vergleichen
            </label>
          </div>
        </article>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <div class="rg-pagination">
      <?php
        echo paginate_links([
          'total' => $qry->max_num_pages,
          'current' => $paged,
        ]);
      ?>
    </div>

    <div style="display:flex;justify-content:center;margin-top:10px;">
      <a href="/roboter-vergleich/" class="rg-btn" id="rg-compare-link" style="opacity:.6;pointer-events:none;">
        Vergleich öffnen (0)
      </a>
    </div>

  <?php else: ?>
    <p>Keine passenden Modelle gefunden.</p>
  <?php endif; ?>
</div>

<div class="rg-compare-bar" id="rg-compare-bar" aria-live="polite">
  <div class="rg-compare-bar__inner">
    <div class="rg-compare-bar__text">
      <strong>Vergleich</strong>
      <span class="rg-compare-bar__count" id="rg-compare-count">0 ausgewählt</span>
    </div>
    <a href="/roboter-vergleich/" class="rg-btn rg-btn--primary" id="rg-compare-bar-link" style="opacity:.6;pointer-events:none;">
      Vergleich öffnen
    </a>
  </div>
</div>

<script>
(function(){
  const key = 'rg_compare_ids';
  const cbs = document.querySelectorAll('.rg-compare-cb');
  const link = document.getElementById('rg-compare-link');
  const bar = document.getElementById('rg-compare-bar');
  const barLink = document.getElementById('rg-compare-bar-link');
  const barCount = document.getElementById('rg-compare-count');

  function load(){
    try { return JSON.parse(localStorage.getItem(key) || '[]'); } catch(e){ return []; }
  }
  function save(ids){
    localStorage.setItem(key, JSON.stringify(ids));
  }
  function updateUI(){
    const ids = load();
    cbs.forEach(cb => cb.checked = ids.includes(parseInt(cb.dataset.id,10)));
    if(link){
      link.textContent = `Vergleich öffnen (${ids.length})`;
      link.style.opacity = ids.length ? '1' : '.6';
      link.style.pointerEvents = ids.length ? 'auto' : 'none';
      link.href = '/roboter-vergleich/?ids=' + encodeURIComponent(ids.join(','));
    }
    if(bar && barLink && barCount){
      barCount.textContent = `${ids.length} ausgewählt`;
      barLink.style.opacity = ids.length ? '1' : '.6';
      barLink.style.pointerEvents = ids.length ? 'auto' : 'none';
      barLink.href = '/roboter-vergleich/?ids=' + encodeURIComponent(ids.join(','));
      bar.classList.toggle('is-active', ids.length > 0);
    }
  }
  cbs.forEach(cb => cb.addEventListener('change', () => {
    const ids = load();
    const id = parseInt(cb.dataset.id,10);
    const next = cb.checked ? Array.from(new Set(ids.concat([id]))) : ids.filter(x => x !== id);
    save(next);
    updateUI();
  }));
  updateUI();
})();
</script>

<?php get_footer(); ?>
