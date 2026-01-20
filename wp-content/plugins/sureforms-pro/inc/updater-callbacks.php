<?php
/**
 * SureForms Pro Updater Callbacks.
 * Provides static methods for the updater class.
 *
 * @package sureforms-pro.
 * @since 1.2.1
 */

namespace SRFM_Pro\Inc;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Extensions\Hooks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Updater class.
 *
 * @since 1.2.1
 */
class Updater_Callbacks {
	/**
	 * Update callback method to handle the default dynamic block options in the global settings.
	 *
	 * @since 1.2.1
	 * @return void
	 */
	public static function add_default_dynamic_options() {
		$previous_options = get_option( 'srfm_default_dynamic_block_option' );

		if ( ! empty( $previous_options ) && is_array( $previous_options ) ) {
			$required_message = Helper::get_common_err_msg();
			$current_options  = Translatable::dynamic_validation_messages();
			$current_options  = Hooks::add_default_dynamic_pro_block_values( $current_options, $required_message );

			// Iterate $current_options and update the options.
			foreach ( $current_options as $key => $value ) {
				if ( ! isset( $previous_options[ $key ] ) ) {
					$previous_options[ $key ] = $value;
				}
			}
			update_option( 'srfm_default_dynamic_block_option', $previous_options );
		}
	}
}
