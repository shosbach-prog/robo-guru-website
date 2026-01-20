<?php
/**
 * Date_Picker_Markup Class file.
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
 * Date_Picker_Markup Class.
 *
 * @since 0.0.1
 */
class Date_Picker_Markup extends Base {
	/**
	 * Type of input field (e.g., date, time, date-time).
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $input_type;

	/**
	 * Calendar date format value.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $date_format;

	/**
	 * Stores the days of the week enabled. if not set, it will be an empty array. and all days will be enabled.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $days_of_week_enabled;

	/**
	 * Stores whether past dates should be disabled.
	 *
	 * @var bool
	 * @since 2.3.0
	 */
	protected $disable_past_dates;

	/**
	 * Initialize the properties based on block attributes.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 * @since 0.0.1
	 */
	public function __construct( $attributes ) {
		$this->set_properties( $attributes );
		$this->set_error_msg( 'srfm_date_picker_block_required_text', $attributes );
		$this->set_input_label( __( 'Date', 'sureforms-pro' ) );
		$this->slug       = 'datepicker';
		$this->input_type = 'date';
		$this->set_unique_slug();
		$this->set_field_name( $this->unique_slug );
		$this->set_markup_properties( $this->input_label );
		$this->set_aria_described_by();

		$this->date_format          = ! empty( $attributes['dateFormat'] ) ? $attributes['dateFormat'] : 'dd/mm/yyyy';
		$this->days_of_week_enabled = ! empty( $attributes['daysOfWeekEnabled'] ) ? $attributes['daysOfWeekEnabled'] : [];
		$this->disable_past_dates   = ! empty( $attributes['disablePastDates'] ) ? (bool) $attributes['disablePastDates'] : false;
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
			'calendar',
			'srfm-' . esc_attr( $this->slug ) . '-icon srfm-input-icon',
			'for="' . esc_attr( $this->unique_slug ) . '" aria-hidden="true"',
			'label'
		);

		// Need to format the min-max to make the date format compatible with the JS library.
		$formatted_min_date   = Helper::get_integer_value( strtotime( Helper::get_string_value( $this->min ) ) );
		$formatted_min_date   = $formatted_min_date ? Helper::get_string_value( wp_date( 'mm/dd/yyyy' === $this->date_format ? 'm-d-Y' : 'd-m-Y', $formatted_min_date ) ) : '';
		$formatted_max_date   = Helper::get_integer_value( strtotime( Helper::get_string_value( $this->max ) ) );
		$formatted_max_date   = $formatted_max_date ? Helper::get_string_value( wp_date( 'mm/dd/yyyy' === $this->date_format ? 'm-d-Y' : 'd-m-Y', $formatted_max_date ) ) : '';
		$days_of_week_enabled = wp_json_encode( $this->days_of_week_enabled );
		$days_of_week_enabled = $days_of_week_enabled ? $days_of_week_enabled : '[]';
		$described_by         = trim( $this->aria_described_by . ' srfm-date-format-' . $this->block_id );

		ob_start(); ?>
			<div data-block-id="<?php echo esc_attr( $this->block_id ); ?>" class="srfm-block-single srfm-block srfm-<?php echo esc_attr( $this->slug ); ?>-block<?php echo esc_attr( $this->block_width ); ?><?php echo esc_attr( $this->classname ); ?> <?php echo esc_attr( $this->conditional_class ); ?>">
				<?php echo wp_kses_post( $this->label_markup ); ?>
				<?php echo wp_kses_post( $this->help_markup ); ?>
				<div class="srfm-block-wrap srfm-with-icon">
					<?php echo $pro_svg; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ignored to render svg ?>
					<input
						type="text"
						class="srfm-input-common srfm-input-<?php echo esc_attr( $this->slug ); ?>"
						name="<?php echo esc_attr( $this->field_name ); ?>"
						id="<?php echo esc_attr( $this->unique_slug ); ?>"
						aria-describedby="<?php echo esc_attr( $described_by ); ?>"
						aria-required="<?php echo esc_attr( $this->data_require_attr ); ?>" data-required="<?php echo esc_attr( $this->data_require_attr ); ?>" <?php echo wp_kses_post( $this->placeholder_attr ); ?> value="<?php echo esc_attr( $this->default ); ?>" min="<?php echo esc_attr( $formatted_min_date ); ?>" max="<?php echo esc_attr( $formatted_max_date ); ?>" data-date-format="<?php echo esc_attr( $this->date_format ); ?>" placeholder="<?php echo esc_attr( $this->date_format ); ?>" autocomplete="off"
						data-days-enabled="<?php echo esc_attr( $days_of_week_enabled ); ?>"
						data-disable-past="<?php echo esc_attr( $this->disable_past_dates ? 'true' : 'false' ); ?>"
					>
					<span class="screen-reader-text" id="srfm-date-format-<?php echo esc_attr( $this->block_id ); ?>"><?php echo esc_attr( $this->date_format ); ?></span>
				</div>
				<div class="srfm-error-wrap">
					<?php echo wp_kses_post( $this->error_msg_markup ); ?>
				</div>
			</div>
		<?php

		return ob_get_clean();
	}
}
