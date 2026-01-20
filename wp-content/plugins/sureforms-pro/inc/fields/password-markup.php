<?php
/**
 * Password Markup Class file.
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
 * Password_Markup Class.
 *
 * @since 0.0.1
 */
class Password_Markup extends Base {
	/**
	 * Flag indicating whether confirmation password is required.
	 *
	 * @var bool
	 * @since 0.0.1
	 */
	protected $is_confirm_password;

	/**
	 * Label for the input field of the confirmation password.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $input_confirm_label;

	/**
	 * Unique slug for the confirmation password field.
	 *
	 * @var string
	 * @since 0.0.1
	 */
	protected $unique_confirm_slug;

	/**
	 * Initialize the properties based on block attributes.
	 *
	 * @param array<mixed> $attributes Block attributes.
	 * @since 0.0.1
	 */
	public function __construct( $attributes ) {
		$this->set_properties( $attributes );
		$this->set_error_msg( 'srfm_password_block_required_text', $attributes );
		$this->set_input_label( __( 'Password', 'sureforms-pro' ) );
		$this->slug                = 'password';
		$this->is_confirm_password = $attributes['isConfirmPassword'] ?? false;
		$this->placeholder_attr    = $this->placeholder ? ' placeholder="' . esc_attr( $this->placeholder ) . '" ' : '';

		// translators: Placeholder %s represents the label being confirmed.
		$this->input_confirm_label = '-lbl-' . Helper::encrypt( sprintf( __( 'Confirm %s', 'sureforms-pro' ), $this->input_label_fallback ) );
		$this->unique_confirm_slug = 'srfm-' . $this->slug . '-confirm-' . $this->block_id . $this->input_confirm_label;
		$this->set_unique_slug();
		$this->set_field_name( $this->unique_slug );
		$this->set_markup_properties( $this->input_label, true );
		$this->set_aria_described_by();
	}

	/**
	 * Render HTML markup of a block.
	 * This function renders the markup of the password input block.
	 *
	 * @since 0.0.1
	 * @return string|bool
	 */
	public function markup() {
		$confirm_help_markup  = ! $this->is_confirm_password ? $this->help_markup : '';
		$confirm_label_markup = Helper::generate_common_form_markup( $this->form_id, 'label', 'Confirm ' . $this->label, $this->slug . '-confirm', $this->block_id . $this->input_confirm_label, boolval( $this->required ) );

		ob_start(); ?>
		<div data-block-id="<?php echo esc_attr( $this->block_id ); ?>" class="srfm-block-single srfm-<?php echo esc_attr( $this->slug ); ?>-block-wrap<?php echo esc_attr( $this->block_width ); ?><?php echo esc_attr( $this->classname ); ?> <?php echo esc_attr( $this->conditional_class ); ?>">
		<div class="srfm-block srfm-<?php echo esc_attr( $this->slug ); ?>-block srfm-<?php echo esc_attr( $this->slug ); ?>-<?php echo esc_attr( $this->block_id ); ?>-block">
			<?php echo wp_kses_post( $this->label_markup ); ?>
			<div class="srfm-block-wrap">
				<input class="srfm-input-common srfm-input-<?php echo esc_attr( $this->slug ); ?>" type="password" name="<?php echo esc_attr( $this->field_name ); ?>" id="<?php echo esc_attr( $this->unique_slug ); ?>"
				<?php echo ! empty( $this->aria_described_by ) ? "aria-describedby='" . esc_attr( trim( $this->aria_described_by ) ) . "'" : ''; ?>
				aria-required="<?php echo esc_attr( $this->data_require_attr ); ?>" data-required="<?php echo esc_attr( $this->data_require_attr ); ?>" <?php echo wp_kses_post( $this->placeholder_attr ); ?> autocomplete="on"/>
			</div>
			<?php echo wp_kses_post( $confirm_help_markup ); ?>
			<?php echo wp_kses_post( $this->error_msg_markup ); ?>
		</div>
		<?php if ( true === $this->is_confirm_password ) { ?>
			<div class="srfm-block srfm-<?php echo esc_attr( $this->slug ); ?>-confirm-block srfm-<?php echo esc_attr( $this->slug ); ?>-<?php echo esc_attr( $this->block_id ); ?>-confirm-block">
				<?php echo wp_kses_post( $confirm_label_markup ); ?>
				<div class="srfm-block-wrap">
					<input class="srfm-input-common srfm-input-<?php echo esc_attr( $this->slug ); ?>-confirm" type="password" name="<?php echo esc_attr( $this->unique_confirm_slug ); ?>" id="<?php echo esc_attr( $this->unique_confirm_slug ); ?>"
					<?php echo ! empty( $this->aria_described_by ) ? "aria-describedby='" . esc_attr( trim( $this->aria_described_by ) ) . "'" : ''; ?>
					aria-required="<?php echo esc_attr( $this->data_require_attr ); ?>" data-required="<?php echo esc_attr( $this->data_require_attr ); ?>" <?php echo wp_kses_post( $this->placeholder_attr ); ?> autocomplete="on"/>
				</div>
				<?php echo wp_kses_post( $this->help_markup ); ?>
				<?php echo wp_kses_post( $this->error_msg_markup ); ?>
			</div>
		<?php } ?>
		</div>
		<?php

		return ob_get_clean();
	}
}
