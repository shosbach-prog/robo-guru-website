<?php
/**
 * Licensing Class
 *
 * This class handles all licensing related stuff.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Admin;

use SRFM_Pro\Inc\Traits\Get_Instance;
use SRFM\Inc\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Licensing handler class.
 *
 * @since 0.0.1
 */

class Licensing {

	use Get_Instance;

	/**
	 * Error messages.
	 *
	 * @var array
	 */
	public $error_messages = [];

	/**
	 * Class constructor
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public function __construct() {
		if ( ! class_exists( 'SureCart\Licensing\Client' ) ) {
			require_once SRFM_PRO_DIR . '/licensing/Client.php';
		}

		self::licensing_setup();

		$this->set_error_messages();

		add_action( 'wp_ajax_sureforms_activate_license', [ $this, 'activate_license' ] );
		add_action( 'wp_ajax_sureforms_deactivate_license', [ $this, 'deactivate_license' ] );

		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'add_sureforms_pro_logo']);
	}

	/**
	 * Licensing setup.
	 * Creates a client object for SureCart licensing.
	 *
	 * @since 0.0.1
	 * @return \SureCart\Licensing\Client
	 */
	public static function licensing_setup() {
		$client = new \SureCart\Licensing\Client(
			SRFM_PRO_PRODUCT, SRFM_PRO_PUBLIC_TOKEN, SRFM_PRO_FILE );

		return $client;
	}

	/**
	 * Set error messages.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	private function set_error_messages() {
		$this->error_messages = [
			'nonce' => __( 'Invalid nonce.', 'sureforms-pro' ),
			'permission' => __( 'You do not have permission to activate license.', 'sureforms-pro' ),
			'invalid_license' => __( 'Please enter a valid license key', 'sureforms-pro' ),
		];

	}

	/**
	 * Activate license
	 *
	 * @hooked wp_ajax_sureforms_activate_license
	 * @since 0.0.1
	 * @return void
	 */
	public function activate_license() {
		if( ! check_ajax_referer( 'srfm_pro_licensing_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => $this->error_messages['nonce'] ] );
		}

		if( ! Helper::current_user_can() ) {
			wp_send_json_error( [ 'message' => $this->error_messages['permission'] ] );
		}

		$license_key = ! empty( $_POST['license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';

		if ( empty( $license_key ) ) {
			wp_send_json_error( [ 'message' => $this->error_messages['invalid_license'] ] );
		}

		$client = self::licensing_setup();

		$get_license = $client->license()->retrieve( $license_key );

		if( !empty( $get_license->product ) && $get_license->product !== SRFM_PRO_PRODUCT_ID ) {
			wp_send_json_error( [ 'message' => __( 'Incorrect License key for this product.', 'sureforms-pro' ) ] );
		}

		$response = $client->license()->activate( $license_key );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( [ 'message' => $response->get_error_message() ] );
		}

		// Update the license status in the database after activating the license.
		update_option( 'srfm_pro_license_status', 'licensed' );
		wp_send_json_success( [ 'message' => __( 'License activated successfully.', 'sureforms-pro' ) ] );
	}

	/**
	 * Deactivate license.
	 *
	 * @hooked wp_ajax_sureforms_deactivate_license
	 * @since 0.0.1
	 * @return void
	 */
	public function deactivate_license() {
		if( ! check_ajax_referer( 'srfm_pro_licensing_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => $this->error_messages['nonce'] ] );
		}

		if( ! Helper::current_user_can() ) {
			wp_send_json_error( [ 'message' => $this->error_messages['permission'] ] );
		}

		$client = self::licensing_setup();

		$response = $client->license()->deactivate();

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( [ 'message' => $response->get_error_message() ] );
		}

		// Update the license status in the database after deactivating the license.
		update_option( 'srfm_pro_license_status', 'unlicensed' );
		wp_send_json_success( [ 'message' => __( 'License deactivated successfully.', 'sureforms-pro' ) ] );
	}

	/**
	 * Checks if license is active.
	 *
	 * @since 0.0.1
	 * @return boolean
	 */
	public static function is_license_active() {
		$client = self::licensing_setup();

		// getting license key from settings.
		// We want to determine if the saved license key is valid for this product.
		$license_key = $client->settings()->license_key;

		if( empty( $license_key ) ) {
			return false;
		}

		// retrieve the license from the server.
		$get_license = $client->license()->retrieve( $license_key );

		// if the license is not valid for this product, return false.
		if( ! empty( $get_license->product ) && $get_license->product !== SRFM_PRO_PRODUCT_ID ) {
			return false;
		}

		$activation = $client->settings()->get_activation();
		return ! empty( $activation->id );
	}

	/**
	 * Adds logo for SureForms Pro plugins on updater page
	 *
	 * @param object $transient
	 * @since 1.7.0
	 * @return object
	 */
	public function add_sureforms_pro_logo( $transient ) {
		$plugin_slug = 'sureforms-pro/sureforms-pro.php';

		if (isset($transient->response[$plugin_slug])) {
			$plugin_data = $transient->response[$plugin_slug];

			// Only update the icons
			$plugin_data->icons = [
				'1x' => SRFM_PRO_URL . 'assets/icons/icon-128x128.gif',
				'2x' => SRFM_PRO_URL . 'assets/icons/icon-256x256.gif',
			];

			$transient->response[$plugin_slug] = $plugin_data;
		}

		return $transient;
	}
}
