<?php
/**
 * Conditional Confirmations Class.
 *
 * This class handles the conditional confirmations functionality for SureForms Pro.
 *
 * @package sureforms-pro.
 * @since 2.4.0
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Helper as Pro_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Conditional_Confirmations Class.
 *
 * @since 2.4.0
 */
class Conditional_Confirmations {
	use Get_Instance;

	/**
	 * Holds the submit type.
	 *
	 * @var string
	 */
	private static ?string $submit_type = null;

	/**
	 * Holds the after submission action.
	 *
	 * @var string
	 */
	private static ?string $after_submission = null;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 2.4.0
	 */
	public function __construct() {
		add_action( 'srfm_register_additional_post_meta', [ $this, 'register_conditional_confirmation_meta' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_admin_scripts' ] );
		add_filter( 'srfm_form_confirmation_data', [ $this, 'override_form_confirmation' ], 10, 3 );
		add_filter( 'srfm_form_submission_response', [ $this, 'override_submission_settings' ] );
	}

	/**
	 * Registers the conditional confirmation meta.
	 *
	 * @hooked srfm_register_additional_post_meta
	 * @since 2.4.0
	 * @return void
	 */
	public function register_conditional_confirmation_meta() {
		register_post_meta(
			'sureforms_form',
			'_srfm_conditional_confirmation',
			[
				'type'              => 'string',  // Will store as JSON string.
				'single'            => true,    // Store as single value.
				'show_in_rest'      => [
					'schema' => [
						'type'    => 'string',
						'context' => [ 'edit' ],
					],
				], // Make available in REST API.
				'sanitize_callback' => [ $this, 'sanitize_conditional_confirmation_data' ],
				'auth_callback'     => static function () {
					return Helper::current_user_can();
				},
			]
		);
	}

	/**
	 * Sanitize conditional confirmation data.
	 *
	 * @since 2.4.0
	 * @param string $data The value to sanitize.
	 * @return string Sanitized JSON string.
	 */
	public function sanitize_conditional_confirmation_data( $data ) {
		// Return early if the value is empty.
		if ( empty( $data ) ) {
			return '';
		}

		// Decode JSON.
		$data = json_decode( $data, true );

		if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $data ) ) {
			return '';
		}

		// Sanitize each confirmation.
		$sanitized_data = [];
		foreach ( $data as $confirmation ) {
			if ( ! is_array( $confirmation ) ) {
				continue;
			}

			// Sanitize redirectUrl - handle both old string format and new object format.
			$redirect_url = [
				'label' => '',
				'value' => '',
			];
			if ( ! empty( $confirmation['redirectUrl'] ) ) {
				if ( is_array( $confirmation['redirectUrl'] ) ) {
					// New object format with label and value.
					$redirect_url = [
						'label' => sanitize_text_field( $confirmation['redirectUrl']['label'] ?? '' ),
						'value' => esc_url_raw( $confirmation['redirectUrl']['value'] ?? '' ),
					];
				}
			}

			$sanitized_confirmation = [
				'status'              => (bool) ( $confirmation['status'] ?? false ),
				'name'                => sanitize_text_field( $confirmation['name'] ?? '' ),
				'confirmationType'    => sanitize_text_field( $confirmation['confirmationType'] ?? 'success_message' ),
				'confirmationMessage' => Helper::strip_js_attributes( $confirmation['confirmationMessage'] ?? '' ),
				'afterSubmission'     => sanitize_text_field( $confirmation['afterSubmission'] ?? 'hide_form' ),
				'redirectTo'          => sanitize_text_field( $confirmation['redirectTo'] ?? 'page' ),
				'redirectUrl'         => $redirect_url,
				'enableQueryParams'   => ! empty( $confirmation['enableQueryParams'] ),
				'queryParams'         => [],
				'conditionalLogic'    => [
					'status' => ! empty( $confirmation['conditionalLogic']['status'] ),
					'logic'  => sanitize_text_field( $confirmation['conditionalLogic']['logic'] ?? '_AND_' ),
					'rules'  => [],
				],
			];

			// Sanitize query parameters.
			if ( ! empty( $confirmation['queryParams'] ) && is_array( $confirmation['queryParams'] ) ) {
				foreach ( $confirmation['queryParams'] as $param ) {
					if ( ! is_array( $param ) ) {
						continue;
					}
					foreach ( $param as $key => $value ) {
						$sanitized_confirmation['queryParams'][] = [
							sanitize_key( $key ) => sanitize_text_field( $value ),
						];
					}
				}
			}

			// Sanitize conditional logic rules.
			if ( ! empty( $confirmation['conditionalLogic']['rules'] ) && is_array( $confirmation['conditionalLogic']['rules'] ) ) {
				foreach ( $confirmation['conditionalLogic']['rules'] as $rule ) {
					if ( ! is_array( $rule ) ) {
						continue;
					}
					$sanitized_confirmation['conditionalLogic']['rules'][] = [
						'field'    => sanitize_text_field( $rule['field'] ?? '' ),
						'operator' => sanitize_text_field( $rule['operator'] ?? '_EQUAL_' ),
						'value'    => sanitize_text_field( $rule['value'] ?? '' ),
					];
				}
			}

			$sanitized_data[] = $sanitized_confirmation;
		}

		$encoded_data = wp_json_encode( $sanitized_data );

		return false !== $encoded_data ? $encoded_data : '';
	}

	/**
	 * Enqueue Admin Scripts.
	 *
	 * @hooked enqueue_block_editor_assets
	 * @since 2.4.0
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		$script = [
			'unique_file'        => 'ConditionalConfirmationSettings',
			'unique_handle'      => 'conditional-confirmation-settings',
			'extra_dependencies' => [],
		];

		$script_dep_path = SRFM_PRO_DIR . 'dist/package/business/' . $script['unique_file'] . '.asset.php';
		$script_dep_data = file_exists( $script_dep_path )
			? include $script_dep_path
			: [
				'dependencies' => [],
				'version'      => SRFM_PRO_VER,
			];
		$script_dep      = array_merge( $script_dep_data['dependencies'], $script['extra_dependencies'] );

		// Scripts.
		wp_enqueue_script(
			SRFM_PRO_SLUG . '-' . $script['unique_handle'], // Handle.
			SRFM_PRO_URL . 'dist/package/business/' . $script['unique_file'] . '.js',
			$script_dep, // Dependencies, defined above.
			$script_dep_data['version'], // SRFM_VER.
			true // Enqueue the script in the footer.
		);

		// Register script translations.
		Pro_Helper::register_script_translations( SRFM_PRO_SLUG . '-' . $script['unique_handle'] );
	}

	/**
	 * Override form confirmation with conditional confirmation if conditions are met.
	 *
	 * @hooked srfm_form_confirmation_data
	 * @param array $form_confirmation The default form confirmation data.
	 * @param int   $form_id The form ID.
	 * @param array $submission_data The submission data (optional).
	 * @return array Modified form confirmation data.
	 * @since 2.4.0
	 */
	public function override_form_confirmation( $form_confirmation, $form_id, $submission_data = [] ) {
		// Only override if we have submission data (form was submitted).
		if ( empty( $submission_data ) || empty( $form_id ) ) {
			return $form_confirmation;
		}

		$confirmation_type = is_array( $form_confirmation )
			&& isset( $form_confirmation[0] )
			&& is_array( $form_confirmation[0] )
			&& isset( $form_confirmation[0][0] )
			&& is_array( $form_confirmation[0][0] )
			&& isset( $form_confirmation[0][0]['confirmation_type'] )
			? $form_confirmation[0][0]['confirmation_type']
			: '';
		// if the confirmation type is 'suretriggers_below_form', do not override.
		if ( 'suretriggers_below_form' === $confirmation_type ) {
			return $form_confirmation;
		}

		// Get conditional confirmations.
		$conditional_confirmations = get_post_meta( $form_id, '_srfm_conditional_confirmation', true );

		if ( empty( $conditional_confirmations ) ) {
			return $form_confirmation;
		}

		// Decode JSON if it's a string.
		if ( is_string( $conditional_confirmations ) ) {
			$conditional_confirmations = json_decode( $conditional_confirmations, true );
		}

		if ( ! is_array( $conditional_confirmations ) ) {
			return $form_confirmation;
		}

		// Find the first matching conditional confirmation.
		$matching_confirmation = $this->get_matching_conditional_confirmation( $conditional_confirmations, $submission_data );

		if ( ! $matching_confirmation ) {
			return $form_confirmation;
		}

		// Convert to form confirmation format and return.
		return $this->convert_to_form_confirmation_format( $matching_confirmation );
	}

	/**
	 * Override form submit type based on conditional confirmation.
	 * Only when a conditional confirmation was matched.
	 *
	 * @hooked srfm_form_submission_response
	 * @param array $response The form submission response data.
	 * @return array Modified form submission response data.
	 * @since 2.4.0
	 */
	public function override_submission_settings( $response ) {

		// Map submit types to submission modes.
		if ( ! empty( self::$submit_type ) ) {
			$submission_modes = [
				'redirect'        => 'different page',
				'success_message' => 'same page',
			];

			if ( isset( $submission_modes[ self::$submit_type ] ) ) {
				$response['data']['submission_settings']['submission_mode']
					= $submission_modes[ self::$submit_type ];
			}
		}

		// Handle after submission behavior.
		if ( ! empty( self::$after_submission ) ) {
			$response['data']['submission_settings']['after_submission']
				= 'hide_form' === self::$after_submission
					? 'hide form'
					: 'reset form';
		}

		return $response;
	}

	/**
	 * Get the first matching conditional confirmation.
	 *
	 * If multiple confirmations have matching conditions, the first one is used.
	 *
	 * @param array $conditional_confirmations Array of conditional confirmations.
	 * @param array $submission_data The submission data.
	 * @return array|null The first matching confirmation or null.
	 * @since 2.4.0
	 */
	private function get_matching_conditional_confirmation( $conditional_confirmations, $submission_data ) {
		$submission_data = Helper::map_slug_to_submission_data( $submission_data );

		foreach ( $conditional_confirmations as $confirmation ) {
			// Skip if disabled.
			if ( empty( $confirmation['status'] ) ) {
				continue;
			}

			// Check if conditions match using the existing helper function.
			// This uses Pro_Helper::check_trigger_conditions() which handles all conditional logic.
			if ( Pro_Helper::check_trigger_conditions( $confirmation, $submission_data ) ) {

				// Store submit type and after submission for later use.
				self::$submit_type      = $confirmation['confirmationType'] ?? '';
				self::$after_submission = $confirmation['afterSubmission'] ?? '';

				return $confirmation;

			}
		}

		return null;
	}

	/**
	 * Convert conditional confirmation format to standard form confirmation format.
	 * That is currently used by SureForms Core version.
	 *
	 * @param array $conditional_confirmation The conditional confirmation data.
	 * @return array The form confirmation formatted data.
	 * @since 2.4.0
	 */
	private function convert_to_form_confirmation_format( $conditional_confirmation ) {
		$confirmation_type = 'same page'; // Default.
		$submission_action = $conditional_confirmation['afterSubmission'] ?? 'hide form';
		$page_url          = '';
		$custom_url        = '';

		// Map confirmation type.
		if ( 'redirect' === $conditional_confirmation['confirmationType'] ) {
			if ( 'page' === $conditional_confirmation['redirectTo'] ) {
				$confirmation_type = 'different page';
				$page_url          = rtrim( $conditional_confirmation['redirectUrl']['value'] ?? '', '/' );
			} elseif ( 'url' === $conditional_confirmation['redirectTo'] ) {
				$confirmation_type = 'custom url';
				$custom_url        = rtrim( $conditional_confirmation['redirectUrl']['value'] ?? '', '/' );
			}
		}

		// Build query parameters.
		$query_params = [];
		if ( ! empty( $conditional_confirmation['enableQueryParams'] ) && ! empty( $conditional_confirmation['queryParams'] ) ) {
			$query_params = $conditional_confirmation['queryParams'];
		}

		// Return in the same format as standard form confirmation.
		return [
			[
				[
					'message'             => $conditional_confirmation['confirmationMessage'] ?? '',
					'confirmation_type'   => $confirmation_type,
					'submission_action'   => $submission_action,
					'page_url'            => $page_url,
					'custom_url'          => $custom_url,
					'enable_query_params' => ! empty( $conditional_confirmation['enableQueryParams'] ),
					'query_params'        => $query_params,
				],
			],
		];
	}

}
