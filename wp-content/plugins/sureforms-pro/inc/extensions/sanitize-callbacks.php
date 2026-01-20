<?php
/**
 * Sanitize callback methods for the form's pro fields.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Sanitize_Callbacks Class.
 *
 * @since 0.0.1
 */
class Sanitize_Callbacks {
	use Get_Instance;

	/**
	 * Initialize our class.
	 */
	public function __construct() {
		add_filter( 'srfm_field_type_sanitize_functions', [ $this, 'extend_sanitize_callbacks' ] );
	}

	/**
	 * Extend the filter hook for the sanitize callbacks.
	 *
	 * @param array<mixed> $sanitize_callbacks Default sanitize callbacks.
	 * @since 0.0.1
	 * @return array<mixed> Extended sanitize callbacks.
	 */
	public function extend_sanitize_callbacks( $sanitize_callbacks ) {

		$sanitize_callbacks['rating']        = [ 'SRFM\Inc\Helper', 'sanitize_number' ];
		$sanitize_callbacks['number-rating'] = [ 'SRFM\Inc\Helper', 'sanitize_number' ];

		return $sanitize_callbacks;
	}
}
