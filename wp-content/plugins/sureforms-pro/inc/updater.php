<?php
/**
 * SureForms Pro Updater.
 * Manages important update related to the plugin.
 *
 * @package sureforms-pro.
 * @since 1.2.1
 */

namespace SRFM_Pro\Inc;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Updater class.
 *
 * @since 1.2.1
 */
class Updater {
	use Get_Instance;

	/**
	 * Current DB saved version of SureForms.
	 *
	 * @var string
	 * @since 1.2.1
	 */
	private $old_version;

	/**
	 * Constructor.
	 *
	 * @since 1.2.1
	 * @return void
	 */
	public function __construct() {
		// Get auto saved version number.
		$this->old_version = Helper::get_string_value( get_option( 'srfm-pro-version', '' ) );

		/**
		 * Adds the `init` action with a priority of 15 for the Pro plugin.
		 *
		 * The base plugin uses a priority of 10 for its `init` action. Setting the priority
		 * to 15 ensures that the Pro plugin's initialization occurs after the base plugin.
		 * This priority order maintains proper dependencies and ensures that the base plugin
		 * setup is completed before the Pro plugin extends its functionality.
		 */
		add_action( 'init', [ $this, 'init' ], 15 );
	}

	/**
	 * Whether or not to call the DB update methods.
	 *
	 * @since 1.2.1
	 * @return bool
	 */
	public function needs_db_update() {
		$updater_callbacks = $this->get_updater_callbacks();

		if ( empty( $updater_callbacks ) ) {
			return false;
		}

		$versions = array_keys( $updater_callbacks );
		$latest   = $versions[ count( $versions ) - 1 ];

		return version_compare( $this->old_version, $latest, '<' );
	}

	/**
	 * This function will help us to determine the plugin version and update it.
	 * Any major change in the option can be handed here on the basis of last plugin version found in the database.
	 *
	 * @since 1.2.1
	 * @return void
	 */
	public function init() {
		if ( version_compare( SRFM_PRO_VER, $this->old_version, '=' ) ) {
			// Bail early because saved version is already updated and no change detected.
			return;
		}

		if ( $this->needs_db_update() ) {
			foreach ( $this->get_updater_callbacks() as $updater_version => $updater_callback_functions ) {
				if ( ! is_array( $updater_callback_functions ) ) {
					continue;
				}

				if ( $this->old_version && ! version_compare( $this->old_version, $updater_version, '<' ) ) {
					// Skip as SRFM saved version is not less than updaters version so db upgrade is not needed here.
					continue;
				}

				foreach ( $updater_callback_functions as $updater_callback_function ) {
					call_user_func( $updater_callback_function, $this->old_version );
				}
			}
		}

		// Finally update cache and DB with current version.
		$this->old_version = SRFM_PRO_VER;
		update_option( 'srfm-pro-version', $this->old_version );
	}

	/**
	 * Returns an array of DB updater callback functions.
	 *
	 * @since 1.2.1
	 * @return array<string,array<callable>>> Array of DB updater callback functions
	 */
	public function get_updater_callbacks() {
		return apply_filters(
			'srfm_pro_updater_callbacks',
			[
				'1.2.1' => [
					'SRFM_Pro\Inc\Updater_Callbacks::add_default_dynamic_options',
				],
			]
		);
	}
}
