<?php
/**
 * Time_Picker_Markup Class file.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Fields;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Helper as Pro_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Time_Picker_Markup Class.
 *
 * @since 0.0.1
 */
class Time_Picker_Markup extends Base {
	/**
	 * Type of input field (e.g., date, time, date-time).
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $input_type;

	/**
	 * Time pickers time increments in minutes.
	 *
	 * @var int
	 * @since 0.0.1
	 */
	protected $increment;

	/**
	 * Twelve hour format.
	 *
	 * @var bool
	 * @since 0.0.1
	 */
	protected $twelve_hour_format = true;

	/**
	 * Initialize the properties based on block attributes.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 * @since 0.0.1
	 */
	public function __construct( $attributes ) {
		$this->set_properties( $attributes );
		$this->set_error_msg( 'srfm_time_picker_block_required_text', $attributes );
		$this->set_input_label( __( 'Time', 'sureforms-pro' ) );
		$this->slug       = 'time-picker';
		$this->input_type = 'time';
		$this->set_unique_slug();
		$this->set_field_name( $this->unique_slug );
		$this->set_markup_properties( $this->input_label );
		$this->set_aria_described_by();

		$this->increment          = ! empty( $attributes['increment'] ) ? Helper::get_integer_value( $attributes['increment'] ) : 30;
		$this->twelve_hour_format = isset( $attributes['showTwelveHourFormat'] ) && false === $attributes['showTwelveHourFormat'] ? false : true;
	}

	/**
	 * Render HTML markup of a block.
	 * This function renders the markup of the date time picker block.
	 *
	 * @since 0.0.1
	 * @return string|bool
	 */
	public function markup() {
		$pro_svg = Pro_Helper::fetch_pro_svg(
			'clock',
			'srfm-' . esc_attr( $this->slug ) . '-icon srfm-input-icon',
			'for="' . esc_attr( $this->unique_slug ) . '" aria-hidden="true"',
			'label'
		);

		ob_start();
		?>
			<div data-block-id="<?php echo esc_attr( $this->block_id ); ?>" class="srfm-block-single srfm-block srfm-<?php echo esc_attr( $this->slug ); ?>-block<?php echo esc_attr( $this->block_width ); ?><?php echo esc_attr( $this->classname ); ?> <?php echo esc_attr( $this->conditional_class ); ?>">
				<?php echo wp_kses_post( $this->label_markup ); ?>
				<?php echo wp_kses_post( $this->help_markup ); ?>
				<div class="srfm-block-wrap srfm-with-icon">
					<?php echo $pro_svg; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ignored to render svg ?>
					<input type="text" class="srfm-input-common srfm-input-<?php echo esc_attr( $this->slug ); ?>" name="<?php echo esc_attr( $this->field_name ); ?>" id="<?php echo esc_attr( $this->unique_slug ); ?>"
					<?php echo ! empty( $this->aria_described_by ) ? "aria-describedby='" . esc_attr( trim( $this->aria_described_by ) ) . "'" : ''; ?>
					aria-required="<?php echo esc_attr( $this->data_require_attr ); ?>" data-required="<?php echo esc_attr( $this->data_require_attr ); ?>" <?php echo wp_kses_post( $this->placeholder_attr ); ?> value="<?php echo esc_attr( $this->default ); ?>" min="<?php echo absint( $this->min ); ?>" max="<?php echo absint( $this->max ); ?>" data-twelve-hour-format="<?php echo esc_attr( Helper::get_string_value( $this->twelve_hour_format ) ); ?>" autocomplete="off" role="combobox" aria-expanded="true" aria-controls="srfm-time-picker-<?php echo esc_attr( $this->block_id ); ?>">
				</div>
				<?php $this->time_picker_element(); ?>
				<div class="srfm-error-wrap">
					<?php echo wp_kses_post( $this->error_msg_markup ); ?>
				</div>
			</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generates the Time Picker elements markup.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	protected function time_picker_element() {
		if ( $this->is_editing ) {
			return;
		}
		$increment       = max( $this->increment, 1 );
		$min             = $this->min ? Helper::get_integer_value( str_replace( ':', '', Helper::get_string_value( $this->min ) ) ) : 0;
		$max             = $this->max ? Helper::get_integer_value( str_replace( ':', '', Helper::get_string_value( $this->max ) ) ) : 2359;
		$time_item_count = 0;

		?>
		<div style="display: none;" class="srfm-time-picker hidden" data-block-id="<?php echo esc_attr( $this->block_id ); ?>" role="listbox" id="srfm-time-picker-<?php echo esc_attr( $this->block_id ); ?>">
			<?php
			for ( $hours = 0; $hours < 24; $hours++ ) {
				for ( $minutes = 0; $minutes < 60; $minutes += $increment ) {
					$time_as_number = $hours * 100 + $minutes;

					if ( ( $max > $min ) && ( $time_as_number < $min || $time_as_number > $max ) ) {
						continue;
					}

					$_hours   = str_pad( Helper::get_string_value( $hours ), 2, '0', STR_PAD_LEFT );
					$_minutes = str_pad( Helper::get_string_value( $minutes ), 2, '0', STR_PAD_LEFT );
					$time     = "{$_hours}:{$_minutes}";
					++$time_item_count;

					if ( $this->twelve_hour_format ) {
						$date_time = \DateTime::createFromFormat( 'H:i', $time );
						$time      = false !== $date_time ? $date_time->format( 'h:i A' ) : '';
					}

					?>
					<button
						type="button"
						class="srfm-time-picker-item"
						data-time="<?php echo esc_attr( "{$_hours}:{$_minutes}" ); // 24 hour format. ?>"
						data-time-view="<?php echo esc_attr( $time ); // Time used for display. ?>"
						data-time-number="<?php echo absint( $time_as_number ); // Time user for calculation. ?>"
						tabindex="-1"
						role="option"
						id="srfm-time-picker-<?php echo esc_attr( $this->block_id ); ?>-item-<?php echo esc_attr( Helper::get_string_value( $time_item_count ) ); ?>"
					>
						<?php echo esc_html( $time ); ?>
					</button>
					<?php
				}
			}
			?>
		</div>
		<?php
	}
}
