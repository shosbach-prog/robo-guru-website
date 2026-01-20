<?php
/**
 * Plugin Compatibility Module
 *
 * Prevents conflicts with other SEO plugins on BuddyBoss pages.
 * Disables OG tags from other plugins when we're handling BuddyBoss content.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Modules;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin_Compatibility class.
 *
 * @since 1.0.0
 */
class Plugin_Compatibility {

	/**
	 * The single instance of the class.
	 *
	 * @var Plugin_Compatibility
	 */
	protected static $instance = null;

	/**
	 * Main Plugin_Compatibility Instance.
	 *
	 * @since 1.0.0
	 * @return Plugin_Compatibility
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
		// Run early to disable other plugins before they output tags
		add_action( 'wp', array( $this, 'disable_conflicting_plugins' ), 1 );
	}

	/**
	 * Check if we're on a BuddyBoss page that we're handling.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	private function is_buddyboss_page() {
		// Check for single activity page
		if ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) {
			return true;
		}

		// Check for group page
		if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
			return true;
		}

		// Check for member/user page (but not activity)
		if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
			// Exclude if it's a single activity page within user profile
			if ( ! ( function_exists( 'bp_is_single_activity' ) && bp_is_single_activity() ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Disable conflicting SEO plugins on BuddyBoss pages.
	 *
	 * @since 1.0.0
	 */
	public function disable_conflicting_plugins() {
		// Only disable on pages we're handling
		if ( ! $this->is_buddyboss_page() ) {
			return;
		}

		// Disable Yoast SEO
		$this->disable_yoast_seo();

		// Disable Rank Math
		$this->disable_rank_math();

		// Disable All in One SEO (AIOSEO)
		$this->disable_aioseo();

		// Disable The SEO Framework
		$this->disable_seo_framework();

		// Disable SEOPress
		$this->disable_seopress();
	}

	/**
	 * Disable Yoast SEO on BuddyBoss pages.
	 *
	 * @since 1.0.0
	 */
	private function disable_yoast_seo() {
		if ( ! defined( 'WPSEO_VERSION' ) ) {
			return;
		}

		// Remove OpenGraph output
		add_filter( 'wpseo_opengraph_title', '__return_false' );
		add_filter( 'wpseo_opengraph_desc', '__return_false' );
		add_filter( 'wpseo_opengraph_url', '__return_false' );
		add_filter( 'wpseo_opengraph_type', '__return_false' );
		add_filter( 'wpseo_opengraph_site_name', '__return_false' );
		add_filter( 'wpseo_opengraph_image', '__return_false' );

		// Disable OpenGraph completely
		add_filter( 'wpseo_og_enabled', '__return_false' );
		add_filter( 'wpseo_enable_opengraph', '__return_false' );

		// Remove frontend output
		$frontend = \WPSEO_Frontend::get_instance();
		if ( $frontend && method_exists( $frontend, 'opengraph' ) ) {
			remove_action( 'wpseo_head', array( $frontend, 'opengraph' ), 30 );
		}

		// Remove Twitter cards as well
		add_filter( 'wpseo_twitter_enabled', '__return_false' );
	}

	/**
	 * Disable Rank Math on BuddyBoss pages.
	 *
	 * @since 1.0.0
	 */
	private function disable_rank_math() {
		if ( ! class_exists( 'RankMath' ) ) {
			return;
		}

		// Disable OpenGraph
		add_filter( 'rank_math/opengraph/enable', '__return_false' );
		add_filter( 'rank_math/opengraph/facebook/enable', '__return_false' );
		add_filter( 'rank_math/opengraph/twitter/enable', '__return_false' );

		// Remove specific OG tags
		add_filter( 'rank_math/opengraph/title', '__return_false' );
		add_filter( 'rank_math/opengraph/description', '__return_false' );
		add_filter( 'rank_math/opengraph/url', '__return_false' );
		add_filter( 'rank_math/opengraph/image', '__return_false' );
		add_filter( 'rank_math/opengraph/type', '__return_false' );

		// Remove Twitter cards
		add_filter( 'rank_math/twitter/title', '__return_false' );
		add_filter( 'rank_math/twitter/description', '__return_false' );
		add_filter( 'rank_math/twitter/image', '__return_false' );
	}

	/**
	 * Disable All in One SEO (AIOSEO) on BuddyBoss pages.
	 *
	 * @since 1.0.0
	 */
	private function disable_aioseo() {
		if ( ! function_exists( 'aioseo' ) ) {
			return;
		}

		// Disable OpenGraph
		add_filter( 'aioseo_opengraph_enabled', '__return_false' );
		add_filter( 'aioseo_facebook_enabled', '__return_false' );
		add_filter( 'aioseo_twitter_enabled', '__return_false' );

		// Disable specific OG tags
		add_filter( 'aioseo_facebook_tags', '__return_empty_array' );
		add_filter( 'aioseo_twitter_tags', '__return_empty_array' );

		// Remove the main OpenGraph output action
		if ( class_exists( '\AIOSEO\Plugin\Common\Social\Output' ) ) {
			remove_action( 'wp_head', array( aioseo()->social->output, 'output' ), 1 );
		}

		// Alternative: Use the disable filter
		add_filter( 'aioseo_disable', '__return_true' );
	}

	/**
	 * Disable The SEO Framework on BuddyBoss pages.
	 *
	 * @since 1.0.0
	 */
	private function disable_seo_framework() {
		if ( ! defined( 'THE_SEO_FRAMEWORK_VERSION' ) ) {
			return;
		}

		// Disable Open Graph
		add_filter( 'the_seo_framework_ogtype_output', '__return_false' );
		add_filter( 'the_seo_framework_ogtitle_output', '__return_false' );
		add_filter( 'the_seo_framework_ogdescription_output', '__return_false' );
		add_filter( 'the_seo_framework_ogurl_output', '__return_false' );
		add_filter( 'the_seo_framework_ogimage_output', '__return_false' );
		add_filter( 'the_seo_framework_ogsitename_output', '__return_false' );

		// Disable Twitter cards
		add_filter( 'the_seo_framework_twittercard_output', '__return_false' );
		add_filter( 'the_seo_framework_twittertitle_output', '__return_false' );
		add_filter( 'the_seo_framework_twitterdescription_output', '__return_false' );
		add_filter( 'the_seo_framework_twitterimage_output', '__return_false' );
	}

	/**
	 * Disable SEOPress on BuddyBoss pages.
	 *
	 * @since 1.0.0
	 */
	private function disable_seopress() {
		if ( ! defined( 'SEOPRESS_VERSION' ) ) {
			return;
		}

		// Disable social meta
		add_filter( 'seopress_social_og_title', '__return_false' );
		add_filter( 'seopress_social_og_desc', '__return_false' );
		add_filter( 'seopress_social_og_url', '__return_false' );
		add_filter( 'seopress_social_og_img', '__return_false' );
		add_filter( 'seopress_social_og_type', '__return_false' );

		// Disable Facebook OG
		add_filter( 'seopress_social_facebook_og_enable', '__return_false' );

		// Disable Twitter cards
		add_filter( 'seopress_social_twitter_card_enable', '__return_false' );
	}
}
