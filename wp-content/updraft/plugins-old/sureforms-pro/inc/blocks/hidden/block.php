<?php
/**
 * PHP render form Hidden Block.
 *
 * @package sureforms-pro.
 */

namespace SRFM_Pro\Inc\Blocks\Hidden;

use SRFM_Pro\Inc\Blocks\Base;
use SRFM_Pro\Inc\Fields\Hidden_Markup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Hidden Block.
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
		if ( ! empty( $attributes ) ) {
			$markup_class = new Hidden_Markup( $attributes );
			ob_start();
			// phpcs:ignore
			echo $markup_class->markup();
		}
		return ob_get_clean();
	}
}
