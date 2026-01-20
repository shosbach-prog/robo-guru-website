<?php
/**
 * Slider_Markup Class file.
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
 * Slider_Markup Class.
 *
 * @since 0.0.1
 */
class Slider_Markup extends Base {
	/**
	 * Step value for the slider.
	 *
	 * @var int
	 * @since 0.0.1
	 */
	protected $step;

	/**
	 * Type of the slider.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $slider_type;

	/**
	 * Text slider values.
	 *
	 * @var array<array<string, string>>
	 * @since 0.0.1
	 */
	protected $options = [];

	/**
	 * Tooltip prefix.
	 *
	 * @var string
	 * @since 1.5.0
	 */
	protected $prefix_tooltip;

	/**
	 * Tooltip suffix.
	 *
	 * @var string
	 * @since 1.5.0
	 */
	protected $suffix_tooltip;

	/**
	 * Left label for the slider.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $left_label;

	/**
	 * Right label for the slider.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $right_label;

	/**
	 * Default value for the slider.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $default;

	/**
	 * Percentage based on default value.
	 *
	 * @var int
	 * @since 0.0.1
	 */
	protected $value_percentage;

	/**
	 * Tooltip value and aria-valuetext attribute value.
	 *
	 * @var string
	 * @since 1.2.1
	 */
	protected $tooltip_value;

	/**
	 * Initialize the properties based on block attributes.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 * @since 0.0.1
	 */
	public function __construct( $attributes ) {
		$this->set_properties( $attributes );
		$this->set_input_label( __( 'Slider', 'sureforms-pro' ) );
		$this->set_error_msg( 'srfm_slider_block_required_text', $attributes );
		$this->slug           = 'slider';
		$this->step           = $attributes['step'] ?? 1;
		$this->slider_type    = $attributes['type'] ?? '';
		$this->prefix_tooltip = ! empty( $attributes['prefixTooltip'] ) ? $attributes['prefixTooltip'] : '';
		$this->suffix_tooltip = ! empty( $attributes['suffixTooltip'] ) ? $attributes['suffixTooltip'] : '';
		$this->left_label     = ! empty( $attributes['leftLabel'] ) ? $attributes['leftLabel'] : $this->min;
		$this->right_label    = ! empty( $attributes['rightLabel'] ) ? $attributes['rightLabel'] : $this->max;
		$this->default        = ! empty( $attributes['numberDefaultValue'] ) ? $attributes['numberDefaultValue'] : '';
		$range                = (int) $this->max - (int) $this->min;
		if ( 0 !== $range ) {
			$this->value_percentage = ( ! empty( $this->default ) ? (int) $this->default : (int) $this->min - (int) $this->min ) / $range * 100;
		} else {
			$this->value_percentage = 0;
		}
		if ( 'text' === $this->slider_type ) {
			$this->options     = $attributes['options'] ?? [];
			$this->left_label  = $this->options[0]['label'];
			$this->right_label = $this->options[ count( $this->options ) - 1 ]['label'];
			$this->default     = ! empty( $attributes['textDefaultValue'] ) ? $attributes['textDefaultValue'] : '';
		}
		$this->tooltip_value = ! empty( $this->default ) ? $this->default : $this->left_label;
		$this->set_unique_slug();
		$this->set_field_name( $this->unique_slug );
		$this->set_markup_properties( $this->input_label );
		$this->set_aria_described_by();
		add_action( 'srfm_form_css_variables', [ $this, 'slider_css_variables' ], 10, 1 );
	}

	/**
	 * Render HTML markup of a block.
	 * This function renders the markup of the slider block.
	 *
	 * @since 0.0.1
	 * @return string|bool
	 */
	public function markup() {
		$container_classes = Helper::join_strings(
			[
				'srfm-block-single',
				'srfm-block',
				"srfm-{$this->slug}-block",
				"srfm-{$this->slug}-{$this->block_id}-block",
				$this->block_width,
				$this->classname,
				"srfm-slug-{$this->block_slug}",
				$this->conditional_class,
			]
		);

		ob_start(); ?>
		<div data-block-id="<?php echo esc_attr( $this->block_id ); ?>" class="<?php echo esc_attr( $container_classes ); ?>" data-required="<?php echo esc_attr( $this->data_require_attr ); ?>" data-default="<?php echo ! empty( $this->default ) ? 'true' : 'false'; ?>" data-type="<?php echo esc_attr( $this->slider_type ); ?>">
			<?php echo wp_kses_post( $this->label_markup ); ?>
			<?php echo wp_kses_post( $this->help_markup ); ?>
			<div class="srfm-block-wrap">
				<?php if ( 'number' === $this->slider_type ) { ?>
				<input class="srfm-input-<?php echo esc_attr( $this->slug ); ?>" name="<?php echo esc_attr( $this->field_name ); ?>" id="<?php echo esc_attr( $this->unique_slug ); ?>" type="range" tabindex="0" value="<?php echo esc_attr( $this->default ? Helper::get_string_value( $this->default ) : Helper::get_string_value( $this->min ) ); ?>" max="<?php echo esc_attr( Helper::get_string_value( $this->max ) ); ?>" min="<?php echo esc_attr( Helper::get_string_value( $this->min ) ); ?>" step="<?php echo esc_attr( Helper::get_string_value( $this->step ) ); ?>"
					aria-required="<?php echo esc_attr( $this->data_require_attr ); ?>"
					<?php echo ! empty( $this->aria_described_by ) ? "aria-describedby='" . esc_attr( trim( $this->aria_described_by ) ) . "'" : ''; ?>>
				<?php } else { ?>
					<div class="srfm-text-slider">
				<?php } ?>
				<div class="srfm-<?php echo esc_attr( $this->slug ); ?>-wrap"
					<?php if ( 'number' === $this->slider_type ) { ?>
						style="--min:<?php echo esc_attr( Helper::get_string_value( $this->min ) ); ?>%; --max:<?php echo esc_attr( Helper::get_string_value( $this->max ) ); ?>%; --value:<?php echo esc_attr( Helper::get_string_value( $this->value_percentage ) ); ?>%;"
					<?php } ?>
				>
					<div class="srfm-<?php echo esc_attr( $this->slug ); ?>"></div>
					<span
						class="srfm-<?php echo esc_attr( $this->slug ); ?>-thumb"
						<?php
						if ( 'text' === $this->slider_type ) {
							echo 'tabindex="0" role="slider"';
							echo 'aria-required="' . esc_attr( $this->data_require_attr ) . '"';
							echo 'aria-labelledby="srfm-label-' . esc_attr( $this->block_id ) . esc_attr( $this->input_label ) . '"';
							echo 'aria-valuetext="' . esc_attr( Helper::get_string_value( $this->tooltip_value ) ) . '"';
							if ( ! empty( $this->aria_described_by ) ) {
								echo "aria-describedby='" . esc_attr( trim( $this->aria_described_by ) ) . "'";
							}
						}
						?>
					></span>
					<div class="srfm-<?php echo esc_attr( $this->slug ); ?>-tooltip">
						<span data-suffix="<?php echo esc_attr( $this->suffix_tooltip ); ?>" data-prefix="<?php echo esc_attr( $this->prefix_tooltip ); ?>"><?php echo esc_html( Helper::get_string_value( $this->tooltip_value ) ); ?></span>
					</div>
				</div>
				<?php if ( 'text' === $this->slider_type ) { ?>
					<div class="srfm-text-slider-options">
						<?php foreach ( $this->options as $index => $option ) { ?>
							<label class="srfm-text-slider-option" style="--index:<?php echo esc_attr( $index ); ?>;">
								<input type="radio" name="<?php echo esc_attr( $this->field_name ); ?>" value="<?php echo esc_attr( $option['label'] ); ?>" <?php echo $option['label'] === $this->default ? 'checked' : ''; ?>>
								<span class="screen-reader-text"><?php echo esc_html( $option['label'] ); ?></span>
							</label>
						<?php } ?>
					</div>
					<span aria-live="assertive" aria-atomic="true" id="srfm-slider-a11y-text" class="screen-reader-text"></span>
				</div>
				<?php } ?>
			</div>
			<div class="slider-range">
				<span class="slider-min"><?php echo esc_html( Helper::get_string_value( $this->left_label ) ); ?></span>
				<span class="slider-max"><?php echo esc_html( Helper::get_string_value( $this->right_label ) ); ?></span>
			</div>
			<div class="srfm-error-wrap">
				<?php echo wp_kses_post( $this->error_msg_markup ); ?>
			</div>
		</div>
		<?php
		$markup = ob_get_clean();

		return apply_filters(
			'srfm_block_field_markup',
			$markup,
			[
				'slug'        => $this->slug,
				'field_name'  => $this->field_name,
				'unique_slug' => $this->unique_slug,
				'is_editing'  => $this->is_editing,
				'attributes'  => $this->attributes,
			]
		);
	}

	/**
	 * Add CSS variables for the slider block.
	 *
	 * @param array<string,string> $params array of values sent by action 'srfm_form_css_variables'.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function slider_css_variables( $params ) {
		$help_color_var = $params['help_color'];
		?>
		--srfm-slider-thumb-shadow-color: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.10 );
		<?php
	}
}
