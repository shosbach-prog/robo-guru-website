<?php
/**
 * PHP render form Date & Time Picker Block.
 *
 * @package sureforms-pro.
 */

namespace SRFM_Pro\Inc\Blocks\Date_Picker;

use SRFM_Pro\Inc\Blocks\Base;
use SRFM_Pro\Inc\Fields\Date_Picker_Markup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Date & Time Picker Picker Block.
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
			$markup_class = new Date_Picker_Markup( $attributes );
			ob_start();
			// phpcs:ignore
			echo $markup_class->markup();
		}
			return ob_get_clean();
	}
}
