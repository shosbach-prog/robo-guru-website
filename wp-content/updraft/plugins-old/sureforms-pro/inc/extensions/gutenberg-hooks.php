<?php
/**
 * Gutenberg_Hooks Class file.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gutenberg_Hooks Class.
 *
 * @since 0.0.1
 */
class Gutenberg_Hooks {
	use Get_Instance;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function __construct() {
		add_filter( 'srfm_allowed_block_types', [ $this, 'allowed_blocks' ], 10, 1 );
		add_filter( 'srfm_block_preview_images', [ $this, 'block_preview_images' ], 10, 1 );
	}

	/**
	 * Allowed pro blocks
	 *
	 * @param array<mixed> $blocks Free block list.
	 * @since 0.0.1
	 * @return array<mixed>
	 */
	public static function allowed_blocks( $blocks ) {
		$allowed_blocks = [
			'srfm/page-break',
			'srfm/slider',
			'srfm/date-picker',
			'srfm/time-picker',
			'srfm/rating',
			'srfm/hidden',
			'srfm/upload',
			'srfm/html',
		];
		return array_merge( $blocks, $allowed_blocks );
	}

	/**
	 * Allowed pro blocks
	 *
	 * @param array<mixed> $preview_blocks preview block list.
	 * @since 0.0.1
	 * @return array<mixed>
	 */
	public static function block_preview_images( $preview_blocks ) {

		$block_preview_image_list = [
			'rating_preview'      => SRFM_PRO_URL . 'images/field-previews/rating.svg',
			'upload_preview'      => SRFM_PRO_URL . 'images/field-previews/upload.svg',
			'password_preview'    => SRFM_PRO_URL . 'images/field-previews/password.svg',
			'date_picker_preview' => SRFM_PRO_URL . 'images/field-previews/date-picker.svg',
			'time_picker_preview' => SRFM_PRO_URL . 'images/field-previews/time-picker.svg',
			'pagebreak_preview'   => SRFM_PRO_URL . 'images/field-previews/page-break.svg',
			'slider_preview'      => SRFM_PRO_URL . 'images/field-previews/slider.svg',
			'html_preview'        => SRFM_PRO_URL . 'images/field-previews/html.svg',
		];
		return array_merge( $preview_blocks, $block_preview_image_list );
	}

}
