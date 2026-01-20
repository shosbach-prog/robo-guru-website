<?php
/**
 * HTML Block Markup Class file.
 *
 * @package sureforms-pro
 */

namespace SRFM_Pro\Inc\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * HTML Block Markup Class.
 */
class Html_Markup extends Base {
	/**
	 * Raw HTML content.
	 *
	 * @var string
	 */
	protected $html_content = '';

	/**
	 * Initialize class properties.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 */
	public function __construct( $attributes ) {
		$this->set_properties( $attributes );
		$this->slug         = 'html';
		$this->html_content = $attributes['htmlContent'] ?? '';
	}

	/**
	 * Render markup for HTML block.
	 *
	 * @return string|bool
	 */
	public function markup() {
		$wrapper_class = "srfm-block srfm-html-{$this->block_id}-block srfm-html-block{$this->block_width}{$this->classname} srfm-slug-{$this->block_slug} {$this->conditional_class}";

		// Process shortcodes in HTML content.
		$processed_content = wp_kses_post( do_shortcode( $this->html_content ) );

		$markup  = '<div data-block-id="' . esc_attr( $this->block_id ) . '" class="' . esc_attr( $wrapper_class ) . '">';
		$markup .= $processed_content; // Deliberately not escaped to allow raw HTML and processed shortcodes.
		$markup .= '</div>';

		return apply_filters(
			'srfm_block_field_markup',
			$markup,
			[
				'slug'       => $this->slug,
				'field_name' => '',
				'is_editing' => $this->is_editing,
				'attributes' => $this->attributes,
			]
		);
	}
}
