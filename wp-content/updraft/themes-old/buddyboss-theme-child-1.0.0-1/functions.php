<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss-theme/inc/theme/functions.php
 * Add your own functions at the bottom of this file.
 */


/****************************** THEME SETUP ******************************/

/**
 * Sets up theme for translation
 *
 * @since BuddyBoss Child 1.0.0
 */
function buddyboss_theme_child_languages()
{
  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss-theme', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'buddyboss_theme_child_languages' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function buddyboss_theme_child_scripts_styles()
{
  // Styles
  wp_enqueue_style( 'buddyboss-child-css', get_stylesheet_directory_uri().'/assets/css/custom.css' );

  // Javascript
  wp_enqueue_script( 'buddyboss-child-js', get_stylesheet_directory_uri().'/assets/js/custom.js' );
}
add_action( 'wp_enqueue_scripts', 'buddyboss_theme_child_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Admin-Bar aus (du hattest das schon)
add_filter('show_admin_bar', '__return_false', 9999);


/**
 * Helper: Check if we are on Robo-Finder page.
 * (Slug + Template; du kannst hier auch zusätzlich mit Page-ID absichern)
 */
function bb_child_is_robo_finder_page(): bool {
  return ( is_page('robo-finder') || is_page_template('page-robo-finder.php') );
}


/**
 * 1) RankMath auf Robo-Finder Seite möglichst komplett entfernen (Scripts/Styles)
 *    Früh genug, damit möglichst nichts mehr registriert wird.
 */
add_action('wp_enqueue_scripts', function () {

  if ( ! bb_child_is_robo_finder_page() ) {
    return;
  }

  // Rank Math Handles (können je nach Version/Setup variieren)
  $handles = [
    'rank-math',
    'rank-math-frontend',
    'rank-math-common',
    'rank-math-schema',
    'rank-math-admin-bar',
    'rank-math-analyzer',
    'rank-math-app',
    'rank-math-content-ai',

    // Alternative Handles, die manchmal verwendet werden
    'rankmath-frontend',
    'rank-math-jsonld',
    'rank-math-sidebar',
    'rank-math-seo-score',
  ];

  foreach ($handles as $h) {
    wp_dequeue_script($h);
    wp_deregister_script($h);
    wp_dequeue_style($h);
    wp_deregister_style($h);
  }

}, 20);


/**
 * 2) Notbremse: Falls RankMath (oder ein anderer Code) trotzdem
 *    den nervigen Request /wp-json/rankmath/v1/an/post/undefined abfeuert,
 *    fangen wir ihn auf dieser Seite ab, damit es keine Layout-Reflows/„Springen“ gibt.
 *
 *    Das ist absichtlich eng gefiltert NUR auf genau diese URL.
 */
add_action('wp_footer', function () {

  if ( ! bb_child_is_robo_finder_page() ) {
    return;
  }
  ?>
  <script>
  (function(){
    // Wenn fetch nicht existiert, nichts tun
    if (!window.fetch) return;

    const BLOCK_PART = '/wp-json/rankmath/v1/an/post/undefined';
    const origFetch = window.fetch;

    window.fetch = function(input, init){
      try {
        const url = (typeof input === 'string')
          ? input
          : (input && input.url ? input.url : '');

        // Nur exakt den undefined-Call blocken
        if (url && url.indexOf(BLOCK_PART) !== -1) {
          return Promise.resolve(
            new Response("{}", {
              status: 200,
              headers: { "Content-Type": "application/json" }
            })
          );
        }
      } catch (e) {}

      return origFetch.apply(this, arguments);
    };
  })();
  </script>
  <?php

}, 9999);



// Add your own custom functions here

?>
