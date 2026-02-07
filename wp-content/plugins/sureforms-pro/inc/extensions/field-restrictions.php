<?php
/**
 * Field Restrictions - Init Class
 *
 * @since 2.5.0
 * @package sureforms-pro
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Helper as Pro_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field Restrictions Class
 *
 * Provides field-based conditional restriction functionality for SureForms.
 * Allows blocking or allowing form submissions based on field values using conditional logic.
 *
 * @since 2.5.0
 * @package sureforms-pro
 */
class Field_Restrictions {
	use Get_Instance;

	/**
	 * Tracks the type of restriction that was triggered
	 *
	 * @since 2.5.0
	 * @var string|null 'field' or null if none
	 */
	private ?string $triggered_restriction_type = null;

	/**
	 * Tracks the form ID for which the restriction was triggered
	 *
	 * @since 2.5.0
	 * @var int|null WordPress form post ID or null if none
	 */
	private ?int $restricted_form_id = null;

	/**
	 * Constructor - Initialize field restriction functionality
	 *
	 * @since 2.5.0
	 */
	public function __construct() {
		add_action( 'srfm_register_additional_post_meta', [ $this, 'register_additional_form_restriction_meta' ] );
		add_filter( 'srfm_additional_restriction_check', [ $this, 'apply_field_based_restrictions' ], 10, 3 );
		add_filter( 'srfm_additional_restriction_message', [ $this, 'get_field_restriction_message' ], 10, 3 );
	}

	/**
	 * Register WordPress post meta for field restrictions
	 *
	 * Registers `_srfm_field_restriction` meta key to store field-based conditional
	 * restriction settings as JSON data with proper sanitization and authorization callbacks.
	 *
	 * @hooked srfm_register_additional_post_meta - WordPress action
	 * @since 2.5.0
	 * @return void
	 */
	public function register_additional_form_restriction_meta() {
		register_post_meta(
			SRFM_FORMS_POST_TYPE, // Post type to register meta for.
			'_srfm_field_restriction', // Meta key name.
			[
				'type'              => 'string', // Store as JSON string for complex data.
				'single'            => true, // Only one value per post.
				'show_in_rest'      => [ // Make available via REST API for Gutenberg editor.
					'schema' => [
						'type'    => 'string', // REST API data type.
						'context' => [ 'edit' ], // Available only in edit context.
					],
				],
				'sanitize_callback' => [ $this, 'sanitize_additional_form_restriction_meta' ], // Security: sanitize data before saving.
				'auth_callback'     => static function () {
					// Security: check if current user can edit SureForms.
					return Helper::current_user_can();
				},
			]
		);
	}

	/**
	 * Sanitize and validate field restriction meta data
	 *
	 * Ensures all field-based conditional restriction data is properly sanitized before saving to database.
	 * Validates data types, allowed values, and provides fallbacks for missing data.
	 *
	 * @param string $meta_value Raw meta value received from frontend (JSON string).
	 * @since 2.5.0
	 * @return string Sanitized and validated JSON string ready for database storage.
	 */
	public function sanitize_additional_form_restriction_meta( $meta_value ) {
		// Ensure we have a string to decode, handle null/empty gracefully.
		if ( ! is_string( $meta_value ) || empty( $meta_value ) ) {
			$result = wp_json_encode( [] );
			return false !== $result ? $result : '{}';
		}

		// Decode JSON string to PHP array for processing.
		$data = json_decode( $meta_value, true );

		// Return empty JSON if invalid data received.
		if ( ! is_array( $data ) ) {
			$result = wp_json_encode( [] );
			return false !== $result ? $result : '{}';
		}

		// Sanitize field-based conditional restrictions.
		if ( isset( $data['mode'] ) ) {
			$data['mode'] = in_array( $data['mode'], [ 'block', 'allow' ], true ) ? $data['mode'] : 'block';
		}
		if ( isset( $data['message'] ) && is_string( $data['message'] ) ) {
			$data['message'] = sanitize_text_field( $data['message'] );
		}

		// Sanitize conditional logic.
		if ( isset( $data['conditionalLogic'] ) && is_array( $data['conditionalLogic'] ) ) {
			$data['conditionalLogic']['status'] = isset( $data['conditionalLogic']['status'] ) ? (bool) $data['conditionalLogic']['status'] : false;
			$data['conditionalLogic']['logic']  = isset( $data['conditionalLogic']['logic'] ) && in_array( $data['conditionalLogic']['logic'], [ 'AND', 'OR', '_AND_', '_OR_' ], true ) ? $data['conditionalLogic']['logic'] : 'OR';

			// Sanitize rules array.
			if ( isset( $data['conditionalLogic']['rules'] ) && is_array( $data['conditionalLogic']['rules'] ) ) {
				$sanitized_rules = [];
				foreach ( $data['conditionalLogic']['rules'] as $rule ) {
					if ( is_array( $rule ) ) {
						$sanitized_rules[] = [
							'field'    => isset( $rule['field'] ) && is_string( $rule['field'] ) ? sanitize_text_field( $rule['field'] ) : '',
							'operator' => isset( $rule['operator'] ) && is_string( $rule['operator'] ) ? sanitize_text_field( $rule['operator'] ) : 'is',
							'value'    => isset( $rule['value'] ) && is_string( $rule['value'] ) ? sanitize_text_field( $rule['value'] ) : '',
						];
					}
				}
				$data['conditionalLogic']['rules'] = $sanitized_rules;
			}
		}

		// Return sanitized data as JSON string for database storage.
		$result = wp_json_encode( $data );
		return false !== $result ? $result : '{}';
	}

	/**
	 * Apply field-based conditional restrictions to form submission.
	 *
	 * @param bool  $is_restricted Current restriction status.
	 * @param int   $form_id WordPress form post ID.
	 * @param array $form_data Form submission data.
	 * @since 2.5.0
	 * @return bool True if form should be restricted, false if allowed.
	 */
	public function apply_field_based_restrictions( $is_restricted, $form_id, $form_data = [] ) {
		// If already restricted by other checks, maintain that restriction.
		if ( $is_restricted ) {
			return true;
		}

		// Get field restriction settings for this form.
		$field_restrictions = $this->get_advanced_restriction_settings( $form_id );

		// If no field restrictions configured or not enabled, allow form submission.
		if ( empty( $field_restrictions ) || isset( $field_restrictions['conditionalLogic']['status'] ) && ! $field_restrictions['conditionalLogic']['status'] ) {
			return false;
		}

		if ( empty( $form_id ) || ! is_numeric( $form_id ) ) {
			return false;
		}

		// Reset restriction tracking.
		$this->reset_restriction_tracking();

		// Map form data to submission format (slug-based).
		$submission_data = Helper::map_slug_to_submission_data( $form_data );

		// Use existing conditional logic evaluation function.
		$rules_matched = Pro_Helper::check_trigger_conditions(
			$field_restrictions,
			$submission_data
		);

		// Determine if form should be restricted based on mode.
		$mode          = $field_restrictions['mode'] ?? 'block';
		$is_restricted = false;

		if ( 'block' === $mode ) {
			// Block mode: restrict if rules matched.
			$is_restricted = $rules_matched;
		} elseif ( 'allow' === $mode ) {
			// Allow mode: restrict if rules did NOT match.
			$is_restricted = ! $rules_matched;
		}

		// Track restriction if triggered.
		if ( $is_restricted ) {
			$this->triggered_restriction_type = 'field';
			$this->restricted_form_id         = $form_id;
		}

		return $is_restricted;
	}

	/**
	 * Get the custom restriction message for field-based restrictions.
	 *
	 * @param string $message Default restriction message.
	 * @param int    $form_id WordPress form post ID.
	 * @param array  $form_data Form submission data.
	 * @since 2.5.0
	 * @return string Custom message or default message.
	 */
	public function get_field_restriction_message( string $message, int $form_id, array $form_data = [] ): string {
		// Suppress unused parameter warning.
		unset( $form_data );

		// Only customize message if our field restriction was triggered for this form.
		if ( 'field' !== $this->triggered_restriction_type || $this->restricted_form_id !== $form_id ) {
			return $message;
		}

		// Get field restriction settings.
		$field_restrictions = $this->get_advanced_restriction_settings( $form_id );

		// Get custom message if configured.
		$custom_message = $field_restrictions['message'] ?? __( 'You are restricted from submitting this form.', 'sureforms-pro' );

		if ( ! empty( $custom_message ) ) {
			return sanitize_text_field( $custom_message );
		}

		// Return default message if no custom message configured.
		return $message;
	}

	/**
	 * Get field restriction settings for a specific form.
	 * Retrieves and decodes the JSON data from post meta.
	 *
	 * @param int $form_id WordPress form post ID.
	 * @since 2.5.0
	 * @return array Decoded restriction settings or empty array if not found
	 */
	private function get_advanced_restriction_settings( $form_id ) {
		// Validate form ID.
		if ( empty( $form_id ) || ! is_int( $form_id ) ) {
			return [];
		}

		// Get raw meta data from WordPress.
		$meta_value = get_post_meta( $form_id, '_srfm_field_restriction', true );

		// Return empty if no data found.
		if ( empty( $meta_value ) || ! is_string( $meta_value ) ) {
			return [];
		}

		// Decode JSON to PHP array.
		$decoded = json_decode( $meta_value, true );

		// Ensure we have valid array data.
		return is_array( $decoded ) ? $decoded : [];
	}

	/**
	 * Reset restriction tracking variables.
	 *
	 * @since 2.5.0
	 * @return void
	 */
	private function reset_restriction_tracking() {
		$this->triggered_restriction_type = null;
		$this->restricted_form_id         = null;
	}

}
