<?php
/**
 * Conditional Emails Class.
 *
 * This class handles the conditional emails functionality for SureForms Pro.
 *
 * @package sureforms-pro.
 * @since 1.10.1
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Helper as Pro_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Conditional_Emails Class.
 *
 * @since 1.10.1
 */
class Conditional_Emails {
	use Get_Instance;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 1.10.1
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		add_action( 'srfm_register_additional_post_meta', [ $this, 'register_starter_post_metas' ] );
		add_filter( 'srfm_should_send_email', [ $this, 'maybe_send_email' ], 10, 4 );
	}

	/**
	 * Enqueue Admin Scripts.
	 *
	 * @since 1.10.1
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		$script = [
			'unique_file'        => 'srfmConditionalEmails',
			'unique_handle'      => 'srfm-conditional-emails',
			'extra_dependencies' => [],
		];

		$script_dep_path = SRFM_PRO_DIR . 'dist/' . $script['unique_file'] . '.asset.php';
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
			SRFM_PRO_URL . 'dist/' . $script['unique_file'] . '.js',
			$script_dep, // Dependencies, defined above.
			$script_dep_data['version'], // SRFM_VER.
			true // Enqueue the script in the footer.
		);

		// Register script translations.
		Pro_Helper::register_script_translations( SRFM_PRO_SLUG . '-' . $script['unique_handle'] );
	}

	/**
	 * Registers the sureforms metas.
	 *
	 * @since 1.10.1
	 * @return void
	 */
	public function register_starter_post_metas() {
		register_post_meta(
			'sureforms_form',
			'_srfm_email_conditional_meta',
			[
				'type'              => 'string',  // Will store as JSON string.
				'single'            => true,    // Store as single value.
				'show_in_rest'      => [
					'schema' => [
						'type'    => 'string',
						'context' => [ 'edit' ],
					],
				], // Make available in REST API.
				'sanitize_callback' => [ $this, 'sanitize_conditional_email_data' ],
				'auth_callback'     => static function () {
					return Helper::current_user_can();
				},
			]
		);
	}

	/**
	 * Sanitizer for the Conditional Email data.
	 *
	 * @param string $data The data to sanitize.
	 * @since 1.10.1
	 * @return string
	 */
	public static function sanitize_conditional_email_data( $data ) {
		if ( empty( $data ) || ! is_string( $data ) ) {
			return '';
		}

		$data = json_decode( $data, true );

		if ( ! is_array( $data ) ) {
			return '';
		}

		$sanitized = [];

		foreach ( $data as $feed ) {

			$sanitized_feed = [
				'conditionalLogic' => self::sanitize_conditional_logic( $feed['conditionalLogic'] ?? [] ),
			];

			$sanitized[] = $sanitized_feed;
		}

		$return = wp_json_encode( $sanitized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		return $return ? $return : '';
	}

	/**
	 * Determines if an email should be sent based on conditional logic.
	 *
	 * @param bool  $should_send_email Whether the email should be sent.
	 * @param int   $notification_id   The ID of the notification.
	 * @param int   $id                The ID of the form submission.
	 * @param array $form_data        The form data submitted.
	 * @return bool
	 */
	public function maybe_send_email( $should_send_email, $notification_id, $id, $form_data ) {
		$raw_meta = get_post_meta( intval( $id ), '_srfm_email_conditional_meta', true );

		// Ensure $conditional_email_notification_data is a string before json_decode.
		if ( is_string( $raw_meta ) ) {
			$conditional_email_notification_data = json_decode( $raw_meta, true );
		} else {
			$conditional_email_notification_data = [];
		}

		$conditional_logic = null;

		if ( is_array( $conditional_email_notification_data ) ) {
			foreach ( $conditional_email_notification_data as $item ) {
				if (
				is_array( $item ) &&
				isset( $item['conditionalLogic'] ) &&
				is_array( $item['conditionalLogic'] ) &&
				isset( $item['conditionalLogic']['id'] ) &&
				$item['conditionalLogic']['id'] === $notification_id
				) {
					$conditional_logic = $item;
					break;
				}
			}
		}

		$submission_data = Helper::map_slug_to_submission_data( $form_data );

		// parse conditional email notification data and check whether to send the email or not.
		if ( is_array( $conditional_logic ) ) {
			$should_send_email = Pro_Helper::check_trigger_conditions( $conditional_logic, $submission_data );
		}

		return $should_send_email;
	}

	/**
	 * Sanitizes the conditional logic rules.
	 *
	 * @param array $logic The conditional logic to sanitize.
	 * @return array
	 */
	private static function sanitize_conditional_logic( $logic ) {
		$rules = [];

		if ( isset( $logic['rules'] ) && is_array( $logic['rules'] ) ) {
			foreach ( $logic['rules'] as $rule ) {
				$rules[] = [
					'field'    => sanitize_text_field( $rule['field'] ?? '' ),
					'operator' => sanitize_text_field( $rule['operator'] ?? '' ),
					'value'    => sanitize_text_field( $rule['value'] ?? '' ),
				];
			}
		}

		return [
			'id'     => filter_var( $logic['id'] ?? 1, FILTER_VALIDATE_INT ),
			'status' => isset( $logic['status'] ) ? filter_var( $logic['status'], FILTER_VALIDATE_BOOLEAN ) : false,
			'logic'  => sanitize_text_field( $logic['logic'] ?? 'AND' ),
			'rules'  => $rules,
		];
	}

}
