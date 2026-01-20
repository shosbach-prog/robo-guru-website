<?php
/**
 * Sureforms Global Settings.
 *
 * @package sureforms.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Global Settings.
 *
 * @since 0.0.1
 */
class Global_Settings {
	use Get_Instance;

	/**
	 * Namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'sureforms-pro/v1';

	/**
	 * Constructor
	 *
	 * @since  0.0.1
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_custom_endpoint' ] );
	}

	/**
	 * Add custom API Route
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function register_custom_endpoint() {
		$sureforms_helper = new Helper();
		register_rest_route(
			$this->namespace,
			'/global-settings',
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'save_global_settings' ],
				'permission_callback' => [ $sureforms_helper, 'get_items_permissions_check' ],
			]
		);
		register_rest_route(
			$this->namespace,
			'/global-settings',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_integration_settings' ],
				'permission_callback' => [ $sureforms_helper, 'get_items_permissions_check' ],
			]
		);
	}

	/**
	 * Save global settings options.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 *
	 * @since 0.0.1
	 */
	public static function save_global_settings( $request ) {

		$nonce = Helper::get_string_value( $request->get_header( 'X-WP-Nonce' ) );

		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				[
					'data' => __( 'Nonce verification failed.', 'sureforms-pro' ),
				]
			);
		}

		$tab      = $request->get_param( 'tab' );
		$settings = $request->get_param( 'settings' );

		switch ( $tab ) {
			case 'integration-settings':
				$is_option_saved = self::save_integration_settings( Helper::get_array_value( $settings ) );
				break;
			case 'user-registration-settings':
				$is_option_saved = apply_filters( 'srfm_save_user_registration_settings', false, Helper::get_array_value( $settings ) );
				break;
			default:
				$is_option_saved = false;
				break;
		}

		if ( ! $is_option_saved ) {
			return new WP_Error( __( 'Error Saving Settings!', 'sureforms-pro' ), __( 'Global Settings', 'sureforms-pro' ) );
		}
			return new WP_REST_Response(
				[
					'data' => __( 'Settings Saved Successfully.', 'sureforms-pro' ),
				]
			);
	}

	/**
	 * Save General Settings
	 *
	 * @param array<mixed> $settings Setting options.
	 * @return bool
	 * @since 0.0.1
	 */
	public static function save_integration_settings( $settings ) {

		$webhooks_enabled = $settings['webhooks_enabled'] ?? false;
		return update_option(
			'srfm_pro_integration_settings',
			[
				'webhooks_enabled' => $webhooks_enabled,
			]
		);
	}

	/**
	 * Get Settings Form Data
	 *
	 * @param \WP_REST_Request $request Request object or array containing form data.
	 * @return void
	 * @since 0.0.1
	 */
	public static function get_integration_settings( $request ) {

		$nonce = Helper::get_string_value( $request->get_header( 'X-WP-Nonce' ) );

		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				[
					'data' => __( 'Nonce verification failed.', 'sureforms-pro' ),
				]
			);
		}

		$options_to_get = $request->get_param( 'options_to_fetch' );

		$options_to_get = Helper::get_string_value( $options_to_get );

		$options_to_get = explode( ',', $options_to_get );

		$global_settings = get_options( $options_to_get );

		if ( empty( $global_settings['srfm_pro_integration_settings'] ) ) {
			$global_settings['srfm_pro_integration_settings'] = [
				'webhooks_enabled' => true,
			];
		}

		// Apply filters to modify global settings.
		$global_settings = apply_filters( 'srfm_pro_global_settings', $global_settings, $options_to_get );

		wp_send_json( $global_settings );
	}

}
