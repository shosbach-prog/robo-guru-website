<?php
/**
 * Block_Assets Class.
 *
 * @package sureforms-pro.
 */

namespace SRFM_Pro\Inc;

use SRFM_Pro\Inc\Helper as Pro_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Block_Assets handler class.
 *
 * @since 0.0.1
 */
class Block_Assets {
	use Get_Instance;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'render_block', [ $this, 'generate_render_script' ], 10, 2 );
	}

	/**
	 * Enqueue Script.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function enqueue_scripts() {
		// Page Break compatibility for Elementor and Bricks preview.
		 // phpcs:ignore -- wordpress.Security.NonceVerification
		if ( ! empty( $_GET['bricks'] ) && 'run' === $_GET['bricks'] || ! empty( $_GET['elementor-preview'] ) ) {
		// phpcs:ignoreEnd
			wp_enqueue_script( SRFM_PRO_SLUG . '-page-break-deps', SRFM_PRO_URL . 'dist/pageBreak.js', [ 'srfm-form-submit' ], SRFM_PRO_VER, true );

			wp_localize_script(
				SRFM_PRO_SLUG . '-page-break-deps',
				'srfm_page_break_data',
				[
					'is_bricks_preview' => ! empty( $_GET['bricks'] ) && 'run' === $_GET['bricks'], // phpcs:ignore -- wordpress.Security.NonceVerification
					'is_elementor_preview' => ! empty( $_GET['elementor-preview'] ), // phpcs:ignore -- wordpress.Security.NonceVerification
				]
			);

			// Register script translations.
			Pro_Helper::register_script_translations( SRFM_PRO_SLUG . '-page-break-deps' );
		}
	}

	/**
	 * Render function.
	 *
	 * @param string $block_content Entire Block Content.
	 * @param array  $block Block Properties As An Array.
	 * @return string
	 * @phpstan-ignore-next-line
	 */
	public function generate_render_script( $block_content, $block ) {

		if ( isset( $block['blockName'] ) ) {
			self::enqueue_script( $block['blockName'] );
		}
		return $block_content;
	}

	/**
	 * Enqueue block scripts
	 *
	 * @param string $block_type block name.
	 * @since 0.0.1
	 * @return void
	 */
	public function enqueue_script( $block_type ) {
		$block_name        = str_replace( 'srfm/', '', $block_type );
		$script_dep_blocks = [ 'rating', 'upload', 'date-picker', 'time-picker', 'slider', 'password', 'page-break', 'html' ];

		$file_prefix = defined( 'SRFM_DEBUG' ) && SRFM_DEBUG ? '' : '.min';
		$dir_name    = defined( 'SRFM_DEBUG' ) && SRFM_DEBUG ? 'unminified' : 'minified';
		$css_uri     = SRFM_PRO_URL . 'assets/css/' . $dir_name . '/';
		$rtl         = is_rtl() ? '-rtl' : '';

		wp_enqueue_style( SRFM_PRO_SLUG . '-custom-styles', $css_uri . 'custom-styles' . $file_prefix . $rtl . '.css', [], SRFM_PRO_VER );

		if ( in_array( $block_name, $script_dep_blocks, true ) ) {
			$js_uri         = SRFM_PRO_URL . 'assets/js/' . $dir_name . '/blocks/';
			$js_vendor_uri  = SRFM_PRO_URL . 'assets/js/minified/deps/';
			$css_vendor_uri = SRFM_PRO_URL . 'assets/css/minified/deps/';

			wp_enqueue_style( SRFM_PRO_SLUG . '-frontend-default', $css_uri . 'blocks/default/frontend' . $file_prefix . $rtl . '.css', [], SRFM_PRO_VER );

			// Load common dependencies by wp_enqueue_script.
			wp_enqueue_script( 'wp-a11y' );

			switch ( $block_name ) {
				case 'date-picker':
					/**
					 * We require the input mask library as well for the date-picker block.
					 * The same is being enqueued from the free frontend assets file along with the input block.
					 */
					wp_enqueue_style( SRFM_PRO_SLUG . '-vanillajs-datepicker', $css_vendor_uri . 'vanillajs-datepicker.min.css', [], SRFM_PRO_VER );
					wp_enqueue_script( SRFM_PRO_SLUG . "-{$block_name}-vanillajs-datepicker-deps", $js_vendor_uri . 'vanillajs-datepicker.min.js', [], SRFM_PRO_VER, true );
					wp_enqueue_script( SRFM_PRO_SLUG . "-{$block_name}-deps", SRFM_PRO_URL . 'dist/datePicker.js', [ 'srfm-form-submit' ], SRFM_PRO_VER, true );
					break;

				case 'time-picker':
					wp_enqueue_script( SRFM_PRO_SLUG . "-{$block_name}", $js_uri . $block_name . $file_prefix . '.js', [], SRFM_PRO_VER, true );
					break;

				case 'page-break':
					wp_enqueue_script( SRFM_PRO_SLUG . "-{$block_name}-deps", SRFM_PRO_URL . 'dist/pageBreak.js', [ 'srfm-form-submit' ], SRFM_PRO_VER, true );
					break;

				case 'html':
					// HTML block only needs CSS, no JavaScript required.
					break;

				default:
					wp_enqueue_script( SRFM_PRO_SLUG . "-{$block_name}", $js_uri . $block_name . $file_prefix . '.js', [], SRFM_PRO_VER, true );
					break;
			}

			// if block type is upload then localize allowed mime types.
			if ( 'upload' === $block_name ) {
				wp_localize_script(
					SRFM_PRO_SLUG . "-{$block_name}",
					'srfmProBlocksData',
					[
						'allowed_mime_types' => Pro_Helper::get_wp_file_types( true )['formats'],
					]
				);
			}
		}
	}
}
