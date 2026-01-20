<?php
/**
 * PHP render form HTML Block.
 *
 * @package sureforms-pro
 */

namespace SRFM_Pro\Inc\Blocks\Html;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Blocks\Base;
use SRFM_Pro\Inc\Fields\Html_Markup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * HTML Block.
 */
class Block extends Base {
	/**
	 * Render the block
	 *
	 * @param array<mixed> $attributes Block attributes.
	 *
	 * @return string|bool
	 */
	public function render( $attributes ) {
		if ( empty( $attributes ) ) {
			return '';
		}

		// Check if form tags exist in HTML content.
		if ( isset( $attributes['htmlContent'] ) && preg_match( '/<\s*\/?\s*form[^>]*>/i', Helper::get_string_value( $attributes['htmlContent'] ) ) ) {
			return esc_html( Helper::get_string_value( $attributes['htmlContent'] ) );
		}
		$markup_class = new Html_Markup( $attributes );
		ob_start();
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output raw HTML
		echo $markup_class->markup();

		return ob_get_clean();
	}
}
