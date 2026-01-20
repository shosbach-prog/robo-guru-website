<?php
/**
 * Plugin Name: Robo-Guru Hotfix Pack
 * Description: Hotfixes for mobile video embeds and Robo Finder Pro forum linkage + JS error guard.
 * Version: 1.0.0
 * Author: Robo-Guru
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

final class RG_Hotfix_Pack {

  const VERSION = '1.0.0';

  /** Robo Finder Pro meta keys */
  const META_ENABLED = '_rf_forum_enabled';
  const META_TOPIC_URL = '_rf_forum_topic_url';

  /**
   * Other common keys we try to import from (ACF/custom fields/legacy).
   * Feel free to extend this list.
   */
  private $import_url_keys = array(
    'forum_topic_url',
    'forum_url',
    'bbp_topic_url',
    'topic_url',
    '_forum_topic_url',
    '_rg_forum_topic_url',
    'rg_forum_topic_url',
  );

  public function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ), 30 );

    // Keep forum meta in sync when a robot is saved.
    add_action( 'save_post', array( $this, 'sync_forum_meta_on_save' ), 20, 2 );

    // One-time migration (safe, idempotent) on admin init.
    add_action( 'admin_init', array( $this, 'maybe_run_migration' ) );

    // Re-enable forum box append if Robo Finder Pro is present.
    add_action( 'init', array( $this, 'maybe_reenable_robo_finder_forum_box' ), 25 );
  }

  /**
   * Frontend assets:
   * - JS: swallow known null.setAttribute promise errors coming from bundled/minified assets (mobile-only issues)
   * - CSS: keep embed wrappers responsive and visible on mobile
   */
  public function enqueue_frontend() {
    $should_load = is_singular() || is_page();
    if ( ! $should_load ) return;

    $base = plugin_dir_url( __FILE__ );

    wp_register_style(
      'rg-hotfix-pack',
      $base . 'assets/css/rg-hotfix.css',
      array(),
      self::VERSION
    );

    wp_register_script(
      'rg-hotfix-pack',
      $base . 'assets/js/rg-hotfix.js',
      array(),
      self::VERSION,
      true
    );

    wp_enqueue_style( 'rg-hotfix-pack' );
    wp_enqueue_script( 'rg-hotfix-pack' );

    // Small config passed to the JS.
    wp_add_inline_script(
      'rg-hotfix-pack',
      'window.RG_HOTFIX = window.RG_HOTFIX || {}; window.RG_HOTFIX.isMobile = ' . ( wp_is_mobile() ? 'true' : 'false' ) . ';',
      'before'
    );
  }

  /**
   * Synchronize forum linkage meta for Robo Robot CPT.
   * This fixes cases where a forum link exists in a different meta key but Robo Finder Pro reads _rf_*.
   */
  public function sync_forum_meta_on_save( $post_id, $post ) {
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;
    if ( ! $post || ! is_object( $post ) ) return;

    // Adjust if your CPT differs.
    if ( $post->post_type !== 'robo_robot' ) return;

    $this->sync_forum_meta( $post_id );
  }

  private function sync_forum_meta( $post_id ) {
    $current_url = get_post_meta( $post_id, self::META_TOPIC_URL, true );

    // If Robo Finder Pro already has the URL, just ensure enabled flag matches.
    if ( $current_url ) {
      $enabled = get_post_meta( $post_id, self::META_ENABLED, true );
      if ( $enabled !== '1' ) {
        update_post_meta( $post_id, self::META_ENABLED, '1' );
      }
      return;
    }

    // Try import from other keys.
    $imported = '';
    foreach ( $this->import_url_keys as $k ) {
      $v = get_post_meta( $post_id, $k, true );
      if ( is_string( $v ) && $v ) {
        $v = esc_url_raw( $v );
        if ( $v ) {
          $imported = $v;
          break;
        }
      }
    }

    if ( $imported ) {
      update_post_meta( $post_id, self::META_TOPIC_URL, $imported );
      update_post_meta( $post_id, self::META_ENABLED, '1' );
    }
  }

  /**
   * One-time migration that attempts to sync all existing robo_robot posts.
   * Runs once per site (flag stored in options).
   */
  public function maybe_run_migration() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $flag = get_option( 'rg_hotfix_pack_migrated_v1', '' );
    if ( $flag === '1' ) return;

    // If CPT doesn't exist yet (early load), don't set the flag.
    if ( ! post_type_exists( 'robo_robot' ) ) return;

    $q = new WP_Query( array(
      'post_type'      => 'robo_robot',
      'posts_per_page' => 200,
      'post_status'    => array( 'publish', 'private', 'draft', 'pending' ),
      'fields'         => 'ids',
      'no_found_rows'  => true,
    ) );

    if ( ! empty( $q->posts ) ) {
      foreach ( $q->posts as $pid ) {
        $this->sync_forum_meta( (int) $pid );
      }
    }

    update_option( 'rg_hotfix_pack_migrated_v1', '1', false );
  }

  /**
   * Robo Finder Pro has a forum box append method that might be disabled in some builds.
   * We try to re-enable it safely if the class/method exists.
   */
  public function maybe_reenable_robo_finder_forum_box() {
    if ( ! function_exists( 'is_singular' ) ) return;

    // Only add filter on frontend.
    if ( is_admin() ) return;

    // Robo Finder Pro main class is typically instantiated and stored in global scope.
    // We search for an object with method append_forum_box_to_robot.
    $candidate = null;

    foreach ( array( 'Robo_Finder_Pro', 'RoboFinderPro', 'RF_Pro', 'Robo_Finder_Pro_Plugin' ) as $class ) {
      if ( class_exists( $class ) ) {
        // Try global instance patterns.
        if ( isset( $GLOBALS[ $class ] ) && is_object( $GLOBALS[ $class ] ) ) {
          $candidate = $GLOBALS[ $class ];
        }
      }
    }

    // If not found via globals, try common global instance variable.
    if ( ! $candidate && isset( $GLOBALS['robo_finder_pro'] ) && is_object( $GLOBALS['robo_finder_pro'] ) ) {
      $candidate = $GLOBALS['robo_finder_pro'];
    }

    if ( $candidate && method_exists( $candidate, 'append_forum_box_to_robot' ) ) {
      // Add once.
      if ( ! has_filter( 'the_content', array( $candidate, 'append_forum_box_to_robot' ) ) ) {
        add_filter( 'the_content', array( $candidate, 'append_forum_box_to_robot' ), 22 );
      }
    } else {
      // Fallback: we add our own minimal forum box append for robo_robot.
      add_filter( 'the_content', array( $this, 'fallback_append_forum_box' ), 23 );
    }
  }

  /**
   * Minimal fallback forum box (only when meta is present).
   */
  public function fallback_append_forum_box( $content ) {
    if ( ! is_singular( 'robo_robot' ) ) return $content;

    $post_id = get_the_ID();
    if ( ! $post_id ) return $content;

    $enabled = get_post_meta( $post_id, self::META_ENABLED, true );
    $url     = get_post_meta( $post_id, self::META_TOPIC_URL, true );

    if ( $enabled !== '1' || ! $url ) return $content;

    $url = esc_url( $url );
    if ( ! $url ) return $content;

    $box = '<div class="rg-forum-box" style="margin:26px 0;padding:16px 18px;border:1px solid rgba(0,0,0,.12);border-radius:16px;background:#fff;box-shadow:0 10px 28px rgba(0,0,0,.06);">'
         . '<h3 style="margin:0 0 10px 0;font-weight:800;">Forum</h3>'
         . '<p style="margin:0 0 12px 0;opacity:.85;">Zum passenden Topic im Robo-Guru Forum:</p>'
         . '<p style="margin:0;"><a href="' . $url . '" class="button" style="display:inline-block;padding:10px 14px;border-radius:12px;text-decoration:none;">Zum Forum-Topic</a></p>'
         . '</div>';

    return $content . $box;
  }
}

new RG_Hotfix_Pack();
