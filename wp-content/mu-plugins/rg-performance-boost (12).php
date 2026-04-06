<?php
/**
 * Plugin Name: RG Performance Boost
 * Description: Performance + SEO fuer robo-guru.de – v4 mit konsolidiertem Output Buffer
 * Version: 4.0.0
 * Author: robo-guru.de
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// =====================================================================
//  HELPER
// =====================================================================

function rg_is_community_page() {
    if ( ! function_exists( 'bp_is_active' ) ) return false;
    return (
        ( function_exists( 'bp_is_activity_component' ) && bp_is_activity_component() ) ||
        ( function_exists( 'bp_is_user' )               && bp_is_user() ) ||
        ( function_exists( 'bp_is_groups_component' )    && bp_is_groups_component() ) ||
        ( function_exists( 'bp_is_forums_component' )    && bp_is_forums_component() ) ||
        ( function_exists( 'bp_is_messages_component' )  && bp_is_messages_component() ) ||
        ( function_exists( 'bp_is_register_page' )       && bp_is_register_page() ) ||
        ( function_exists( 'bp_is_activation_page' )     && bp_is_activation_page() ) ||
        ( function_exists( 'bp_is_media_component' )     && bp_is_media_component() ) ||
        ( function_exists( 'bp_is_document_component' )  && bp_is_document_component() ) ||
        ( function_exists( 'bp_is_video_component' )     && bp_is_video_component() )
    );
}

// =====================================================================
//  1. UNGENUTZTE INTEGRATIONS-SCRIPTS (immer)
// =====================================================================
add_action( 'wp_enqueue_scripts', function() {
    foreach ( array( 'bb-meprlms-frontend','bb-tutorlms-admin','bb-poll-script',
                     'bb-schedule-posts','bb-sso','boss-jssocials-js' ) as $h ) {
        wp_dequeue_script( $h ); wp_deregister_script( $h );
    }
    foreach ( array( 'bb-meprlms-frontend','bb-tutorlms-admin','bb-polls',
                     'bb-schedule-posts','bb-sso-login','bb-access-control' ) as $h ) {
        wp_dequeue_style( $h ); wp_deregister_style( $h );
    }
}, 999 );

// =====================================================================
//  2. CLEANUP AUF STATISCHEN SEITEN (~47 Scripts)
// =====================================================================
add_action( 'wp_enqueue_scripts', function() {
    if ( is_admin() || rg_is_community_page() ) return;

    $js = array(
        'bp-nouveau','bp-nouveau-activity','bp-nouveau-activity-post-form',
        'bb-activity-post-feature-image','bb-reaction',
        'bp-media-dropzone','bp-nouveau-media','bp-nouveau-video',
        'bp-media-videojs','bp-media-videojs-seek-buttons',
        'bp-media-videojs-flv','bp-media-videojs-flash',
        'bp-exif','bp-nouveau-magnific-popup',
        'bp-nouveau-moderation','bp-nouveau-search',
        'bp-mentions','jquery-caret','jquery-atwho',
        'bp-nouveau-codemirror','bb-cropper-js','guillotine-js',
        'bp-widget-members','bp-jquery-query','bp-jquery-cookie',
        'bp-jquery-scroll-to','bp-livestamp',
        'bp-zoom-mask-js','bp-zoom-js','bb-countdown-js',
        'buddyboss-theme-bbp-scrubber-js',
        'backbone','wp-backbone','underscore','wp-util','wp-hooks','wp-i18n',
        'jquery-ui-core','jquery-ui-mouse','jquery-ui-sortable',
        'jquery-ui-menu','jquery-ui-autocomplete','wp-dom-ready','wp-a11y',
        'draggabilly-js','isInViewport','moment',
        'masonry','imagesloaded','boss-fitvids-js','boss-slick-js',
        'boss-validate-js',
        'select2-js','progressbar-js','mousewheel-js',
        'sib-front-js','comment-reply',
    );
    $css = array(
        'bp-media-videojs','bp-nouveau-magnific-popup','bp-nouveau-codemirror',
        'bb-cropper','bp-mentions','buddyboss-platform-pro-index',
        'buddyboss-theme-magnific-popup',
    );
    foreach ( $js  as $h ) { wp_dequeue_script( $h ); }
    foreach ( $css as $h ) { wp_dequeue_style( $h ); }
}, 999 );

// =====================================================================
//  3. PDF/DOC PREVIEW NUR WO NOETIG
// =====================================================================
add_action( 'wp_enqueue_scripts', function() {
    if ( is_admin() || rg_is_community_page() ) return;
    global $post;
    $needs = $post && is_singular() && (
        strpos( $post->post_content, 'rg-doc-preview' ) !== false ||
        strpos( $post->post_content, 'rg_document_preview' ) !== false
    );
    if ( ! $needs ) {
        wp_dequeue_script( 'pdfjs' );
        wp_dequeue_script( 'rg-doc-preview' );
        wp_dequeue_style( 'rg-doc-preview' );
    }
}, 999 );

// =====================================================================
//  4. EMOJIS DEAKTIVIEREN
// =====================================================================
add_action( 'init', function() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'emoji_svg_url', '__return_false' );
});
add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_script( 'bb-twemoji' );
    wp_dequeue_script( 'bb-emoji-loader' );
    wp_deregister_script( 'bb-twemoji' );
}, 999 );

// =====================================================================
//  5. HEARTBEAT FRONTEND + DASHICONS + PRECONNECT + VER CLEANUP
// =====================================================================
add_action( 'init', function() {
    if ( ! is_admin() ) wp_deregister_script( 'heartbeat' );
}, 1 );

add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_user_logged_in() ) {
        wp_dequeue_style( 'dashicons' );
        wp_deregister_style( 'dashicons' );
    }
}, 999 );

add_action( 'wp_head', function() {
    echo '<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>' . "\n";
}, 1 );

function rg_strip_ver( $src ) {
    return strpos( $src, 'ver=' ) ? remove_query_arg( 'ver', $src ) : $src;
}
add_filter( 'script_loader_src', 'rg_strip_ver', 15 );
add_filter( 'style_loader_src', 'rg_strip_ver', 15 );

// =====================================================================
//  6. CANONICAL DUPLICATE: WordPress Core entfernen
// =====================================================================
add_action( 'template_redirect', function() {
    remove_action( 'wp_head', 'rel_canonical' );
    remove_action( 'bp_head', 'bp_rel_canonical' );
    remove_action( 'wp_head', 'bp_rel_canonical' );
}, 1 );

// =====================================================================
//  7. DATENSCHUTZ-SEITEN 301 REDIRECT
// =====================================================================
add_action( 'template_redirect', function() {
    if ( is_page( 'datenschutzrichtlinien' ) || is_page( 15261 ) ||
         is_page( 'privacy-policy-2' )       || is_page( 13409 ) ) {
        wp_redirect( home_url( '/datenschutzerklaerung/' ), 301 );
        exit;
    }
}, 1 );

// =====================================================================
//  8. DOWNLOAD-LINKS: robots.txt + noindex + nofollow
// =====================================================================
add_filter( 'robots_txt', function( $output ) {
    $output .= "\n# Block document download endpoints\n";
    $output .= "Disallow: /wp-admin/admin-ajax.php?action=rg_docs_download\n";
    return $output;
}, 99 );

add_action( 'admin_init', function() {
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'rg_docs_download' ) {
        header( 'X-Robots-Tag: noindex, nofollow', true );
    }
});

add_filter( 'the_content', function( $content ) {
    return preg_replace(
        '/(<a[^>]*admin-ajax\.php\?action=rg_docs_download[^>]*)>/',
        '$1 rel="nofollow noopener">',
        $content
    );
});

// =====================================================================
//  9. KONSOLIDIERTER OUTPUT BUFFER
//     Ein einziger Buffer fuer ALLE HTML-Transformationen.
//     Vorher: 4 verschachtelte ob_start() → jetzt: 1
// =====================================================================
add_action( 'template_redirect', function() {
    if ( is_admin() ) return;

    ob_start( function( $html ) {

        // --- A) Canonical: nur den ERSTEN behalten ---
        $canon_count = 0;
        $html = preg_replace_callback(
            '/<link[^>]*rel=[\'"]canonical[\'"][^>]*>\s*/',
            function( $m ) use ( &$canon_count ) {
                $canon_count++;
                return $canon_count === 1 ? $m[0] : '';
            },
            $html
        );

        // --- B) Entry-Title H1→H2 auf bestimmten Seiten ---
        // Pruefe NUR die <body> class, nicht das gesamte HTML
        $h1_pages = array( 2206 ); // ROI-Rechner
        if ( preg_match( '/<body[^>]*class="([^"]*)"/', $html, $body_match ) ) {
            $body_class = $body_match[1];
            foreach ( $h1_pages as $pid ) {
                if ( strpos( $body_class, 'page-id-' . $pid ) !== false ) {
                    $html = preg_replace(
                        '/<h1(\s+class="[^"]*entry-title[^"]*")>/',
                        '<h2$1>',
                        $html, 1
                    );
                    $html = preg_replace( '/<\/h1>/', '</h2>', $html, 1 );
                    break;
                }
            }
        }

        // --- C) "Details ansehen" → Robotername ---
        $slug_names = array(
            'gausium-omnie'=>'Gausium Omnie','gausium-phantas-1-3'=>'Gausium Phantas',
            'gausium-scrubber-50'=>'Gausium Scrubber 50','gausium-scrubber-75'=>'Gausium Scrubber 75',
            'nexaro-nr-1700'=>'Nexaro NR 1700','orionstar-nova-greetingbot'=>'GreetingBot Nova',
            'pudu-bellabot-pro'=>'Pudu BellaBot Pro','pudu-bg1'=>'Pudu BG1',
            'pudu-cc1'=>'Pudu CC1','pudu-cc1-pro'=>'Pudu CC1 Pro',
            'pudu-holabot'=>'Pudu HolaBot','pudu-mt1'=>'Pudu MT1',
            'pudu-mt1-max'=>'Pudu MT1 Max','pudu-mt1-vac'=>'Pudu MT1 Vac',
            'pudu-sh1'=>'Pudu SH1','pudu-t300'=>'Pudu T300','pudu-t600'=>'Pudu T600',
        );
        $arrow = "\xe2\x86\x92";
        foreach ( $slug_names as $slug => $name ) {
            $html = str_replace(
                'href="https://robo-guru.de/roboter/' . $slug . '/">Details ansehen ' . $arrow . '</a>',
                'href="https://robo-guru.de/roboter/' . $slug . '/">' . $name . ' ansehen ' . $arrow . '</a>',
                $html
            );
        }

        // --- D) Forum-Buttons individualisieren ---
        $forum_names = array(
            'gausium-omnie'=>'Gausium Omnie','gausium-phantas-s1-pro'=>'Gausium Phantas',
            'nexaro-1700'=>'Nexaro NR 1700','pudu-t600'=>'Pudu T600',
            'pudu-t300'=>'Pudu T300','pudu-cc1'=>'Pudu CC1',
            'pudu-cc1-pro'=>'Pudu CC1 Pro','pudu-bg1'=>'Pudu BG1',
        );
        foreach ( $forum_names as $slug => $name ) {
            $html = str_replace(
                'forum/reinigungsroboter/' . $slug . '/" target="_blank" rel="noopener">Diskussion im Forum',
                'forum/reinigungsroboter/' . $slug . '/" target="_blank" rel="noopener">' . $name . ' im Forum',
                $html
            );
        }

        return $html;
    });
}, 2 );
