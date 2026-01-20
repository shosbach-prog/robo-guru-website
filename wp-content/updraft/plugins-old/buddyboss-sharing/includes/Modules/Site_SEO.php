<?php
/**
 * Site SEO Module.
 *
 * @package BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Modules;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site_SEO class.
 *
 * @since 1.0.0
 */
class Site_SEO {

	/**
	 * The single instance of the class.
	 *
	 * @var Site_SEO
	 */
	protected static $instance = null;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Main Site_SEO Instance.
	 *
	 * @since 1.0.0
	 * @return Site_SEO
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->settings = get_option( 'buddyboss_seo_settings', array() );
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'wp_head', array( $this, 'output_seo_meta' ), 1 );
		add_filter( 'pre_get_document_title', array( $this, 'filter_document_title' ), 10 );
		add_action( 'wp_head', array( $this, 'output_open_graph_meta' ), 5 );
		add_filter( 'wp_robots', array( $this, 'filter_robots' ) );
	}

	/**
	 * Output SEO meta tags.
	 *
	 * @since 1.0.0
	 */
	public function output_seo_meta() {
		$description = $this->get_seo_description();

		if ( $description ) {
			echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
		}
	}

	/**
	 * Filter document title.
	 *
	 * @since 1.0.0
	 * @param string $title Document title.
	 * @return string
	 */
	public function filter_document_title( $title ) {
		if ( is_front_page() && ! empty( $this->settings['seo_title'] ) ) {
			return $this->settings['seo_title'];
		}
		return $title;
	}

	/**
	 * Output Open Graph meta tags.
	 *
	 * @since 1.0.0
	 */
	public function output_open_graph_meta() {
		if ( empty( $this->settings['enable_open_graph'] ) ) {
			return;
		}

		$og_title       = $this->get_og_title();
		$og_description = $this->get_og_description();
		$og_image       = $this->get_og_image();
		$og_url         = $this->get_current_url();

		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">' . "\n";

		if ( $og_description ) {
			echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '">' . "\n";
		}

		if ( $og_image ) {
			echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
		}

		echo '<meta property="og:url" content="' . esc_url( $og_url ) . '">' . "\n";
		echo '<meta property="og:type" content="website">' . "\n";

		// Twitter Card tags.
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $og_title ) . '">' . "\n";

		if ( $og_description ) {
			echo '<meta name="twitter:description" content="' . esc_attr( $og_description ) . '">' . "\n";
		}

		if ( $og_image ) {
			echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '">' . "\n";
		}
	}

	/**
	 * Filter robots meta tag.
	 *
	 * @since 1.0.0
	 * @param array $robots Robots directives.
	 * @return array
	 */
	public function filter_robots( $robots ) {
		// Check if current page type should be indexed.
		// Note: "Posts" refers to Activity Posts, not WordPress posts.
		if ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) {
			$index_posts = bp_get_option( 'buddyboss_seo_index_posts', 1 );
			if ( empty( $index_posts ) ) {
				$robots['noindex'] = true;
			}
		} elseif ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
			// Make sure we're not on a single activity page within a user profile.
			if ( ! ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) ) {
				$index_profiles = bp_get_option( 'buddyboss_seo_index_profiles', 1 );
				if ( empty( $index_profiles ) ) {
					$robots['noindex'] = true;
				}
			}
		} elseif ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
			$index_groups = bp_get_option( 'buddyboss_seo_index_groups', 1 );
			if ( empty( $index_groups ) ) {
				$robots['noindex'] = true;
			}
		}

		return $robots;
	}

	/**
	 * Get SEO description.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private function get_seo_description() {
		if ( is_front_page() && ! empty( $this->settings['seo_description'] ) ) {
			return $this->settings['seo_description'];
		}

		return get_bloginfo( 'description' );
	}

	/**
	 * Get Open Graph title.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private function get_og_title() {
		if ( ! empty( $this->settings['use_same_seo_title'] ) && ! empty( $this->settings['seo_title'] ) ) {
			return $this->settings['seo_title'];
		}

		if ( ! empty( $this->settings['og_title'] ) ) {
			return $this->settings['og_title'];
		}

		return get_bloginfo( 'name' );
	}

	/**
	 * Get Open Graph description.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private function get_og_description() {
		if ( ! empty( $this->settings['use_same_seo_desc'] ) && ! empty( $this->settings['seo_description'] ) ) {
			return $this->settings['seo_description'];
		}

		if ( ! empty( $this->settings['og_description'] ) ) {
			return $this->settings['og_description'];
		}

		return get_bloginfo( 'description' );
	}

	/**
	 * Get Open Graph image.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private function get_og_image() {
		if ( ! empty( $this->settings['og_image'] ) ) {
			return $this->settings['og_image'];
		}

		// Try to get site logo.
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( $custom_logo_id ) {
			$logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			if ( $logo ) {
				return $logo[0];
			}
		}

		return '';
	}

	/**
	 * Get current URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private function get_current_url() {
		global $wp;
		return home_url( add_query_arg( array(), $wp->request ) );
	}
}
