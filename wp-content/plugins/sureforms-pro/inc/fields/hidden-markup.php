<?php
/**
 * Hidden_Markup Class file.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Fields;

use SRFM\Inc\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Hidden_Markup Class.
 *
 * @since 0.0.1
 */
class Hidden_Markup extends Base {
	/**
	 * Default value for the hidden input field.
	 *
	 * @var string $default value from the block attributes.
	 * @since 0.0.1
	 */
	protected $default;

	/**
	 * Initialize the properties based on block attributes.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 * @since 0.0.1
	 */
	public function __construct( $attributes ) {
		$this->set_properties( $attributes );
		$this->set_input_label( __( 'Hidden Field', 'sureforms-pro' ) );
		$this->slug    = 'hidden';
		$this->default = $attributes['defaultValue'] ?? '';
	}

	/**
	 * Render HTML markup of a block.
	 * This function renders the markup of the hidden field block.
	 *
	 * @since 0.0.1
	 * @return string|bool
	 */
	public function markup() {
		$container_classes = Helper::join_strings(
			[
				"srfm-{$this->slug}-block",
				"srfm-slug-{$this->block_slug}",
				$this->classname,
			]
		);
		$data_config       = $this->field_config;

		ob_start(); ?>
		<div data-block-id="<?php echo esc_attr( $this->block_id ); ?>" class="<?php echo esc_attr( $container_classes ); ?>" <?php echo $data_config ? "data-field-config='" . esc_attr( $data_config ) . "'" : ''; ?>>
			<input name="srfm-<?php echo esc_attr( $this->slug ); ?>-<?php echo esc_attr( $this->block_id ); ?><?php echo esc_attr( $this->field_name ); ?>" value="<?php echo esc_attr( strval( $this->default ) ); ?>" type="hidden" class="srfm-<?php echo esc_attr( $this->slug ); ?>-input">
		</div>
		<?php
		$markup = ob_get_clean();

		return apply_filters(
			'srfm_block_field_markup',
			$markup,
			[
				'slug'       => $this->slug,
				'is_editing' => $this->is_editing,
				'field_name' => $this->field_name,
				'attributes' => $this->attributes,
			]
		);
	}
}
