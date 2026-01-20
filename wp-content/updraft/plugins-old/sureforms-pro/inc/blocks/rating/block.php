<?php
/**
 * PHP render form Rating Block.
 *
 * @package sureforms-pro.
 */

namespace SRFM_Pro\Inc\Blocks\Rating;

use SRFM_Pro\Inc\Blocks\Base;
use SRFM_Pro\Inc\Fields\Rating_Markup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Rating Block.
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
			$markup_class = new Rating_Markup( $attributes );
			ob_start();
			// phpcs:ignore
			echo $markup_class->markup();
		}
		return ob_get_clean();
	}
}
