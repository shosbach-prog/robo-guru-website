<?php
/**
 * PHP render form Text Block.
 *
 * @package sureforms-pro.
 */

namespace SRFM_Pro\Inc\Blocks\Page_Break;

use SRFM_Pro\Inc\Blocks\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Page break Block.
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
			ob_start(); ?>
			<div class="srfm-page-break">
			</div>
			<?php
		}
			return ob_get_clean();
	}
}
