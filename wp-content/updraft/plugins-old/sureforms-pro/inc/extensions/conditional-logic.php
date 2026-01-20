<?php
/**
 * Conditional_Extension Class file.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Conditional logic Class.
 *
 * @since 0.0.1
 */
class Conditional_Logic {
	use Get_Instance;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function __construct() {
		add_action( 'srfm_register_conditional_logic_post_meta', [ $this, 'register_post_meta' ], 10 );
		add_action( 'srfm_localize_conditional_logic_data', [ $this, 'localize_condition_logic_data' ], 10 );
		add_filter( 'srfm_conditional_logic_classes', [ $this, 'add_conditional_classes' ], 10, 2 );
	}

	/**
	 * Conditional logic
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public static function register_post_meta() {
		register_post_meta(
			'sureforms_form',
			'_srfm_conditional_logic',
			[
				'type'          => 'array',
				'single'        => true,
				'auth_callback' => static function() {
					return Helper::current_user_can();
				},
				'show_in_rest'  => [
					'schema' => [
						'type'    => 'array',
						'context' => [ 'edit' ],
						'items'   => [
							'type'                 => 'object',
							'additionalProperties' => true,
							'properties'           => [
								'logic'  => [
									'type'  => 'array',
									'items' => [
										'type'       => 'object',
										'properties' => [
											'field'    => [ 'type' => 'string' ],
											'operator' => [ 'type' => 'string' ],
											'value'    => [ 'type' => 'string' ],
											'type'     => [ 'type' => 'string' ],
										],
									],
								],
								'action' => [ 'type' => 'string' ],
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Conditional logic data
	 *
	 * @param int $id form id.
	 * @return void
	 * @since 0.0.1
	 */
	public static function localize_condition_logic_data( $id ) {
		$conditional_data = get_post_meta( $id, '_srfm_conditional_logic' );
		$file_suffix      = defined( 'SRFM_DEBUG' ) && SRFM_DEBUG ? '' : '.min';
		$dir_name         = defined( 'SRFM_DEBUG' ) && SRFM_DEBUG ? 'unminified' : 'minified';
		wp_enqueue_script( SRFM_PRO_SLUG . '-conditional-logic', SRFM_PRO_URL . 'assets/js/' . $dir_name . '/conditional-logic' . $file_suffix . '.js', [], SRFM_PRO_VER, true );
		wp_localize_script(
			SRFM_PRO_SLUG . '-conditional-logic',
			'srfm_conditional_logic_data_' . $id,
			[
				'data-' . $id => $conditional_data,
			]
		);
	}

	/**
	 * Conditional logic data
	 *
	 * @param int $id form id.
	 * @param int $block_id block id.
	 * @return array<int, string>|string
	 * @since 0.0.1
	 */
	public static function add_conditional_classes( $id, $block_id ) {
		$data = get_post_meta( $id, '_srfm_conditional_logic' );
		if ( ! is_array( $data ) ) {
			return '';
		}
		$result         = [];
		$processed_keys = [];

		foreach ( $data as $level1 ) {
			if ( is_array( $level1 ) ) {
				foreach ( $level1 as $level2 ) {
					if ( is_array( $level2 ) ) {
						foreach ( $level2 as $key => $value ) {
							if ( is_array( $value ) && isset( $value['logic'] ) ) {
								$logic = $value['logic'];

								if ( ! isset( $result[ $key ] ) ) {
									$result[ $key ] = [];
								}

								if ( isset( $value['action'] ) ) {
									$action_class     = 'show' === $value['action'] ? 'srfm-show' : 'srfm-hide';
									$result[ $key ][] = 'conditional-logic ' . $action_class;
								}

								if ( is_array( $logic ) ) {
									foreach ( $logic as $condition ) {
										if ( is_array( $condition ) ) {
											foreach ( $condition as $sub_condition ) {
												if ( is_array( $sub_condition ) && isset( $sub_condition['field'] ) ) {
													$field = $sub_condition['field'];
													if ( ! isset( $processed_keys[ $field ] ) ) {
														if ( ! isset( $result[ $field ] ) ) {
															$result[ $field ] = [];
														}
														$result[ $field ][]       = 'conditional-trigger';
														$processed_keys[ $field ] = true;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		foreach ( $result as &$values ) {
			$values = implode( ' ', $values );
		}

		return $result[ $block_id ] ?? '';
	}

}
