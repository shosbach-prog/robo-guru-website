<?php
/**
 * Plugin Name: RG Fix BuddyBoss Headings
 * Description: Fixes heading structure issues caused by BuddyBoss templates (empty headings, duplicates, hierarchy gaps)
 * Author: robo-guru.de
 * Version: 1.0
 * 
 * Fixes these Seobility findings (170 pages affected):
 * - Empty heading: <h4 class="bb-card-heading"></h4> and <h4 class="bb-rl-card-heading"></h4>
 * - Duplicate heading: "Melden" and "Mitglied blocken?" H4 tags on every page
 * - Empty <h2></h2> tags on community pages
 * - Structural problem: H4 popup headings breaking heading hierarchy
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Don't run in admin
if ( is_admin() ) return;

/**
 * Start output buffering early
 */
add_action( 'template_redirect', function() {
    ob_start( 'rg_fix_heading_structure' );
}, 1 );

/**
 * Process the final HTML output and fix heading issues
 */
function rg_fix_heading_structure( $html ) {
    if ( empty( $html ) ) return $html;

    // 1. Remove empty BuddyBoss card headings (appear on every page, 2x)
    //    <h4 class="bb-card-heading"></h4>
    //    <h4 class="bb-rl-card-heading"></h4>
    $html = preg_replace(
        '/<h4\s+class="bb-(?:rl-)?card-heading">\s*<\/h4>/i',
        '',
        $html
    );

    // 2. Convert "Melden" popup heading to <div> (appears on every BuddyBoss page)
    //    <h4>Melden <span class="bp-reported-type"></span></h4>
    $html = preg_replace(
        '/<h4>(\s*Melden\s*<span[^>]*>.*?<\/span>\s*)<\/h4>/is',
        '<div class="rg-fixed-heading">$1</div>',
        $html
    );

    // 3. Convert "Mitglied blocken?" popup heading to <div>
    //    <h4>Mitglied blocken?</h4>
    $html = preg_replace(
        '/<h4>(\s*Mitglied blocken\?\s*)<\/h4>/i',
        '<div class="rg-fixed-heading">$1</div>',
        $html
    );

    // 4. Remove completely empty <h2></h2>, <h3></h3>, <h4></h4> tags
    //    (community pages: /documents/, /videos/, /photos/)
    $html = preg_replace(
        '/<(h[2-6])(\s[^>]*)?>(\s*)<\/\1>/i',
        '',
        $html
    );

    // 5. Convert any remaining BuddyBoss UI h4 tags (report/block popups) to divs
    //    These are not content headings but UI elements
    $html = preg_replace(
        '/<h4(\s+class="bp-report[^"]*"[^>]*)>(.*?)<\/h4>/is',
        '<div$1>$2</div>',
        $html
    );

    return $html;
}
