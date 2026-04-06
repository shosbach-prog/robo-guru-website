<?php
/**
 * Plugin Name: RG Blog Grid
 * Description: Shortcode [rg_blog_grid] zeigt Blogbeiträge im robo-guru Karten-Design. Kategorie und Anzahl im Gutenberg einstellbar.
 * Version: 1.0.0
 * Author: robo-guru.de
 *
 * Verwendung im Gutenberg-Editor:
 *   [rg_blog_grid category="news-trends" count="6"]
 *   [rg_blog_grid category="roboter-vergleiche" count="4"]
 *   [rg_blog_grid category="tips-tricks" count="3"]
 *   [rg_blog_grid] ← zeigt alle Kategorien, 6 Beiträge
 *
 * Ablage: /wp-content/plugins/rg-blog-grid/rg-blog-grid.php
 *    oder: /wp-content/mu-plugins/rg-blog-grid.php
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'rg_blog_grid', 'rg_blog_grid_render' );

function rg_blog_grid_render( $atts ) {

    $atts = shortcode_atts( array(
        'category' => '',
        'count'    => 6,
        'columns'  => 3,
        'title'    => '',
        'subtitle' => '',
    ), $atts, 'rg_blog_grid' );

    $count   = intval( $atts['count'] );
    $columns = intval( $atts['columns'] );

    // Query-Argumente
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $count,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( ! empty( $atts['category'] ) ) {
        $args['category_name'] = sanitize_text_field( $atts['category'] );
    }

    $query = new WP_Query( $args );

    if ( ! $query->have_posts() ) {
        return '<p style="text-align:center;color:#94A3B8;">Keine Beiträge gefunden.</p>';
    }

    // Kategorie-Label Mapping
    $cat_labels = array(
        'news-trends'        => 'News & Trends',
        'roboter-vergleiche' => 'Vergleich',
        'praxisberichte'     => 'Praxisbericht',
        'tips-tricks'        => 'Tipps & Tricks',
    );

    // Min-Width pro Spalte berechnen
    $min_widths = array( 2 => '400px', 3 => '300px', 4 => '260px' );
    $min_w = isset( $min_widths[ $columns ] ) ? $min_widths[ $columns ] : '300px';

    ob_start();
    ?>

    <style>
    .rg-blog-grid-section {
        --rg-cyan: #00BCD4;
        --rg-cyan-dark: #0097A7;
        --rg-cyan-light: #E0F7FA;
        --rg-dark: #1A2332;
        --rg-slate: #64748B;
        --rg-muted: #94A3B8;
        --rg-border: #E2E8F0;
        --rg-light: #F8FAFC;
        font-family: 'Plus Jakarta Sans', 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        padding: 48px 0;
    }
    .rg-blog-grid-section * { box-sizing: border-box; }
    .rg-blog-grid-header {
        text-align: center;
        margin-bottom: 36px;
    }
    .rg-blog-grid-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: var(--rg-dark);
        margin: 0 0 8px;
        line-height: 1.3;
    }
    .rg-blog-grid-header p {
        font-size: 16px;
        color: var(--rg-slate);
        margin: 0;
    }
    .rg-blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(<?php echo esc_attr( $min_w ); ?>, 1fr));
        gap: 28px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .rg-blog-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--rg-border);
        overflow: hidden;
        transition: all 0.3s ease;
        text-decoration: none !important;
        color: inherit;
        display: flex;
        flex-direction: column;
    }
    .rg-blog-card:hover {
        border-color: var(--rg-cyan);
        box-shadow: 0 8px 30px rgba(0,188,212,0.08), 0 2px 8px rgba(0,0,0,0.04);
        transform: translateY(-4px);
    }
    .rg-blog-card:hover .rg-blog-thumb img {
        transform: scale(1.05);
    }
    .rg-blog-thumb {
        position: relative;
        width: 100%;
        aspect-ratio: 16/9;
        overflow: hidden;
        background: var(--rg-light);
    }
    .rg-blog-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .rg-blog-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(26,35,50,0.85);
        backdrop-filter: blur(8px);
        color: #fff;
        font-size: 11.5px;
        font-weight: 600;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 6px;
    }
    .rg-blog-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .rg-blog-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .rg-blog-date {
        font-size: 13px;
        color: var(--rg-muted);
        font-weight: 500;
    }
    .rg-blog-tag {
        font-size: 12px;
        font-weight: 600;
        color: var(--rg-cyan-dark);
        background: var(--rg-cyan-light);
        padding: 3px 10px;
        border-radius: 4px;
    }
    .rg-blog-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 17px;
        font-weight: 700;
        color: var(--rg-dark);
        line-height: 1.4;
        margin: 0 0 10px;
    }
    .rg-blog-card:hover .rg-blog-title {
        color: var(--rg-cyan-dark);
    }
    .rg-blog-excerpt {
        font-size: 14px;
        color: var(--rg-slate);
        line-height: 1.65;
        margin: 0 0 16px;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .rg-blog-readmore {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        font-weight: 600;
        color: var(--rg-cyan-dark);
        text-decoration: none;
        margin-top: auto;
    }
    .rg-blog-readmore svg {
        width: 16px;
        height: 16px;
        transition: transform 0.2s ease;
    }
    .rg-blog-card:hover .rg-blog-readmore svg {
        transform: translateX(4px);
    }
    @media (max-width: 768px) {
        .rg-blog-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .rg-blog-grid-header h2 { font-size: 22px; }
    }
    </style>

    <div class="rg-blog-grid-section">

        <?php if ( ! empty( $atts['title'] ) ) : ?>
        <div class="rg-blog-grid-header">
            <h2><?php echo esc_html( $atts['title'] ); ?></h2>
            <?php if ( ! empty( $atts['subtitle'] ) ) : ?>
                <p><?php echo esc_html( $atts['subtitle'] ); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="rg-blog-grid">

            <?php while ( $query->have_posts() ) : $query->the_post();
                $cats = get_the_category();
                $cat_slug = ! empty( $cats ) ? $cats[0]->slug : '';
                $cat_name = ! empty( $cats ) ? $cats[0]->name : '';
                $cat_label = isset( $cat_labels[ $cat_slug ] ) ? $cat_labels[ $cat_slug ] : $cat_name;
                $thumb = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                $excerpt = get_the_excerpt();
                if ( empty( $excerpt ) ) {
                    $excerpt = wp_trim_words( get_the_content(), 25, '…' );
                }
                $date_formatted = get_the_date( 'd. M. Y' );
            ?>

            <a href="<?php the_permalink(); ?>" class="rg-blog-card">
                <div class="rg-blog-thumb">
                    <?php if ( $thumb ) : ?>
                        <img src="<?php echo esc_url( $thumb ); ?>"
                             alt="<?php echo esc_attr( get_the_title() ); ?>"
                             loading="lazy" width="624" height="357">
                    <?php else : ?>
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,#1A2332,#2A3444);display:flex;align-items:center;justify-content:center;color:#00BCD4;font-size:32px;font-weight:700;">RG</div>
                    <?php endif; ?>
                    <span class="rg-blog-badge"><?php echo esc_html( $cat_label ); ?></span>
                </div>
                <div class="rg-blog-body">
                    <div class="rg-blog-meta">
                        <span class="rg-blog-date"><?php echo esc_html( $date_formatted ); ?></span>
                    </div>
                    <h3 class="rg-blog-title"><?php the_title(); ?></h3>
                    <p class="rg-blog-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                    <span class="rg-blog-readmore">
                        Weiterlesen
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>

            <?php endwhile; wp_reset_postdata(); ?>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
