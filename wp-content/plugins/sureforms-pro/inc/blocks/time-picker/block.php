<?php
/**
 * PHP render form Time Picker Block.
 *
 * @package sureforms-pro.
 */

namespace SRFM_Pro\Inc\Blocks\Time_Picker;

use SRFM_Pro\Inc\Blocks\Base;
use SRFM_Pro\Inc\Fields\Time_Picker_Markup;

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
			$markup_class = new Time_Picker_Markup( $attributes );
			ob_start();
			// phpcs:ignore
			echo $markup_class->markup();
		}
			return ob_get_clean();
	}
}
