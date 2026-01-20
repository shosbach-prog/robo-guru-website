<?php
/**
 * Translatable Class file for Sureforms Pro.
 *
 * @package Sureforms Pro
 * @since 1.0.5
 */

namespace SRFM_Pro\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Sureforms Pro Translatable Class
 *
 * A helper class providing an interface for handling translation of text elements, specifically
 * frontend validation messages, used in the Sureforms Pro plugin. This class enables dynamic and
 * reusable translated strings to enhance user experience across different languages.
 *
 * @since 1.0.5
 */
class Translatable {
	/**
	 * Register hooks.
	 *
	 * @since 1.2.1
	 * @return void
	 */
	public static function hooks() {
		add_filter( 'srfm_frontend_validation_messages', [ self::class, 'get_frontend_validation_messages' ] );
		add_filter( 'srfm_dynamic_validation_messages', [ self::class, 'dynamic_validation_messages' ] );
	}

	/**
	 * Retrieve default frontend validation messages.
	 *
	 * Returns an array of validation messages, each identified by a unique key. Messages are
	 * translated for frontend display, with placeholders included for dynamically populated values.
	 *
	 * @param array<string, string> $default_value Associative array of translated validation messages for frontend use.
	 * @since @since 1.0.5
	 * @return array<string, string> Associative array of translated validation messages for frontend use.
	 */
	public static function get_frontend_validation_messages( $default_value ) {
		$translatable_array = [
			'timepicker_am'                      => __( 'AM', 'sureforms-pro' ),
			'timepicker_pm'                      => __( 'PM', 'sureforms-pro' ),

			/* translators: %s represents the file size in MB */
			'size_mb'                            => __( '%s MB', 'sureforms-pro' ),

			/* translators: %s represents the item to be deleted */
			'delete_string'                      => __( 'Delete %s', 'sureforms-pro' ),

			/* translators: %s represents the name of the deleted file */
			'delete_file'                        => __( 'Deleted %s file', 'sureforms-pro' ),

			/* translators: %1$s represents the current page number, %2$s represents the total page number */
			'page_break_page_number'             => __( 'Page %1$s of %2$s', 'sureforms-pro' ),

			/* translators: %1$s represents the selected rating value in format value (for star icon shape) + label */
			'rating_selected_value'              => __( 'You have selected %s.', 'sureforms-pro' ),

			/* translators: %s represents the selected option */
			'text_slider_selected_value'         => __( 'You have selected %s', 'sureforms-pro' ),

			'date_picker_past_dates_not_allowed' => __( 'Past dates are not allowed.', 'sureforms-pro' ),
		];

		return array_merge( $default_value, $translatable_array );
	}

	/**
	 * Retrieve default dynamic validation messages.
	 *
	 * @param array<string, string> $default_value Associative array of translated validation messages for frontend use.
	 * @since 1.2.1
	 * @return array Associative array of translated validation messages for frontend use.
	 */
	public static function dynamic_validation_messages( $default_value = [] ) {
		$translatable_array = [
			// Note: These password strength messages are prepared for the password block.
			// As of now, the password block is not registered in SureForms. Once registered, these messages should be used.
			// phpcs:ignore
			/*
			'srfm_password_strength_weak'        => __( 'Your password strength is weak.', 'sureforms-pro' ),
			'srfm_password_strength_medium'      => __( 'Your password strength is moderate.', 'sureforms-pro' ),
			'srfm_password_strength_strong'      => __( 'Your password strength is strong.', 'sureforms-pro' ),
			'srfm_password_strength_very_strong' => __( 'Your password strength is very strong.', 'sureforms-pro' ),
			*/
			// phpcs:enable
			/* translators: %s represents the maximum file size allowed in MB */
			'srfm_file_size_exceed'           => __( 'File size should not exceed %s MB.', 'sureforms-pro' ),

			'srfm_file_type_not_allowed'      => __( 'File type not allowed.', 'sureforms-pro' ),

			/* translators: %s represents the maximum number of files allowed */
			'srfm_file_upload_limit'          => __( 'You can only upload up to %s files.', 'sureforms-pro' ),

			// Save & Resume email messages.
			'srfm_save_resume_enter_email'    => __( 'Please enter your email address.', 'sureforms-pro' ),
			'srfm_save_resume_valid_email'    => __( 'Please enter a valid email address.', 'sureforms-pro' ),
			'srfm_save_resume_sending'        => __( 'Sending…', 'sureforms-pro' ),
			'srfm_save_resume_email_sent'     => __( 'Email sent successfully!', 'sureforms-pro' ),
			'srfm_save_resume_email_failed'   => __( 'Failed to send email. Please try again!', 'sureforms-pro' ),
			'srfm_save_resume_error_occurred' => __( 'An error occurred. Please try again!', 'sureforms-pro' ),
		];

		return ! empty( $default_value ) && is_array( $default_value ) ? array_merge( $default_value, $translatable_array ) : $translatable_array;
	}
}
