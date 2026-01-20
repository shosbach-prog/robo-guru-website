<?php
/**
 * Sureforms Webhooks.
 *
 * @package sureforms.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Integrations;

use SRFM\Inc\Database\Tables\Entries;
use SRFM\Inc\Helper;
use SRFM\Inc\Smart_Tags;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Sureforms Webhooks Class.
 *
 * @since 0.0.1
 */
class Webhooks {
	use Get_Instance;

	/**
	 * Submission Data.
	 *
	 * @var array<mixed>
	 * @since  0.0.1
	 */
	public $submission_data = [];

	/**
	 * Form Id.
	 *
	 * @var int
	 * @since  0.0.1
	 */
	protected $form_id = 0;

	/**
	 * Unprocessed form data.
	 *
	 * @var array<mixed>
	 * @since  0.0.1
	 */
	protected $form_data = [];

	/**
	 * Submission Id.
	 *
	 * @var int
	 * @since  0.0.1
	 */
	protected $submission_id = 0;
	/**
	 * Webhooks Settings.
	 *
	 * @var array<mixed>
	 * @since  0.0.1
	 */
	protected $webhook_settings = [];

	/**
	 * Constructor
	 *
	 * @since  0.0.1
	 */
	public function __construct() {
		add_action( 'srfm_register_additional_post_meta', [ $this, 'register_post_meta' ], 10 );
		add_action( 'srfm_after_submission_process', [ $this, 'after_submission_process' ] );
	}

	/**
	 * Post meta related to Webhooks Integration.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public static function register_post_meta() {

		register_post_meta(
			'sureforms_form',
			'_srfm_integrations_webhooks',
			[
				'single'        => true,
				'type'          => 'array',
				'auth_callback' => static function() {
					return Helper::current_user_can();
				},
				'show_in_rest'  => [
					'schema' => [
						'type'    => 'array',
						'context' => [ 'edit' ],
						'items'   => [
							'type'       => 'object',
							'properties' => [
								'id'                      => [
									'type' => 'integer',
								],
								'status'                  => [
									'type' => 'boolean',
								],
								'name'                    => [
									'type' => 'string',
								],
								'url'                     => [
									'type' => 'string',
								],
								'method'                  => [
									'type' => 'string',
								],
								'format'                  => [
									'type' => 'string',
								],
								'has_custom_headers'      => [
									'type' => 'boolean',
								],
								'custom_headers'          => [
									'type' => 'array',
								],
								'has_custom_data_filters' => [
									'type' => 'boolean',
								],
								'custom_data_filters'     => [
									'type' => 'array',
								],
								'has_trigger_conditions'  => [
									'type' => 'boolean',
								],
								'trigger_conditions'      => [
									'type'       => 'object',
									'properties' => [
										'timestamp'  => [
											'type' => 'string',
										],
										'type'       => [
											'type' => 'string',
										],
										'operator'   => [
											'type' => 'string',
										],
										'conditions' => [
											'type' => 'array',
										],
									],
								],
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Register 'After Submission' background process
	 *
	 * @param array<mixed> $form_data form data related to submission.
	 * @since 0.0.1
	 * @return void
	 */
	public function after_submission_process( $form_data ) {

		$integration_settings = Helper::get_array_value( get_option( 'srfm_pro_integration_settings', [] ) );

		if ( isset( $integration_settings['webhooks_enabled'] ) && empty( $integration_settings['webhooks_enabled'] ) ) {
			return;
		}

		$this->form_id = isset( $form_data['form_id'] )
		? Helper::get_integer_value( $form_data['form_id'] )
		: 0;

		$this->submission_id = isset( $form_data['submission_id'] )
		? Helper::get_integer_value( $form_data['submission_id'] )
		: 0;

		$this->form_data = array_filter(
			$form_data,
			static function ( $key ) {
				return 'form_id' !== Helper::get_string_value( $key ) && 'submission_id' !== Helper::get_string_value( $key );
			},
			ARRAY_FILTER_USE_KEY
		);

		$this->submission_data = Helper::map_slug_to_submission_data( $this->form_data );

		if ( ! ( 0 < $this->form_id ) ) {
			return;
		}

		$this->webhook_settings = Helper::get_array_value( get_post_meta( $this->form_id, '_srfm_integrations_webhooks', true ) );

		if ( ! is_array( $this->webhook_settings ) || empty( $this->webhook_settings ) ) {
			return;
		}

		$log     = $this->process_webhook_settings();
		$success = ! in_array(
			false,
			array_map(
				static function ( $log_item ) {
					return is_array( $log_item ) && isset( $log_item['status'] ) ? $log_item['status'] : false;
				},
				$log
			),
			true
		);

		if ( 0 < $this->submission_id ) {
			// Get existing extras data to preserve other data like PDF links.
			$entry_data      = Entries::get( $this->submission_id );
			$existing_extras = Helper::get_array_value( $entry_data['extras'] ) ?? [];

			// Merge webhook data with existing extras.
			$webhooks_data = [
				'webhooks' => [
					'log'    => $log,
					'status' => $success,
				],
			];

			$updated_extras = array_merge( $existing_extras, $webhooks_data );

			Entries::update(
				$this->submission_id,
				[
					'extras' => $updated_extras,
				]
			);
		}
	}

	/**
	 * Process webhook_settings.
	 *
	 * @since  0.0.1
	 * @return array<mixed>
	 */
	public function process_webhook_settings() {
		$log = [];
		foreach ( $this->webhook_settings as $webhook ) {
			if ( is_array( $webhook ) ) {
				[ $status, $response ] = $this->trigger_webhook( $webhook );
				$log[]                 = array_merge(
					$webhook,
					[
						'request_status' => $status,
						'response'       => $response,
					]
				);

				$this->add_webhook_status_in_entry_logs( $webhook, $response );
			}
		}
		return $log;
	}

	/**
	 * Add webhook status in entry logs.
	 *
	 * @param array<mixed> $webhook webhook setting.
	 * @param mixed        $response response from webhook.
	 * @since  1.6.1
	 * @return void
	 */
	public function add_webhook_status_in_entry_logs( $webhook, $response ) {
		$entry_id = $this->submission_id;

		if ( empty( $entry_id ) || ! is_array( $webhook ) || 'disabled' === $response ) {
			return;
		}

		$webhook_name = isset( $webhook['name'] ) ?
			sanitize_text_field( Helper::get_string_value( $webhook['name'] ) )
			: __( '(no title)', 'sureforms-pro' );

		$log_message = $this->generate_webhook_log_message( $webhook_name, $response );

		$this->update_entry_logs( $entry_id, $log_message );
	}

	/**
	 * Generate webhook log message.
	 *
	 * @param string $webhook_name webhook name.
	 * @param mixed  $response     webhook response.
	 * @since  1.6.1
	 * @return string
	 */
	public function generate_webhook_log_message( $webhook_name, $response ) {
		if ( 'trigger conditions not met' === $response ) {
			return sprintf(
				/* translators: Here %1$s is webhook name */
				__( 'Webhook "%1$s" was not triggered - conditions not met.', 'sureforms-pro' ),
				$webhook_name
			);
		}

		$response_code    = '0';
		$response_message = __( 'No message', 'sureforms-pro' );

		if ( is_array( $response ) && isset( $response['response'] ) && is_array( $response['response'] ) ) {
			if ( isset( $response['response']['code'] ) ) {
				$response_code = sanitize_text_field( Helper::get_string_value( $response['response']['code'] ) );
			}

			if ( isset( $response['response']['message'] ) ) {
				$response_message = sanitize_text_field( Helper::get_string_value( $response['response']['message'] ) );
			}
		}

		if ( '200' === $response_code ) {
			return sprintf(
				/* translators: Here %1$s is webhook name */
				__( 'Webhook "%1$s" triggered successfully.', 'sureforms-pro' ),
				$webhook_name
			);
		}

		return sprintf(
			/* translators: Here %1$s is webhook name, %2$s is webhook response code and %3$s is webhook response message. */
			__( 'Webhook "%1$s" failed to trigger - Response code: %2$s, Message: "%3$s".', 'sureforms-pro' ),
			$webhook_name,
			$response_code,
			$response_message
		);
	}

	/**
	 * Update entry logs.
	 *
	 * @param int    $entry_id entry id.
	 * @param string $log_message log message.
	 * @since 1.6.1
	 * @return void
	 */
	public function update_entry_logs( $entry_id, $log_message ) {
		$entries_db = new Entries();

		$log_key = $entries_db->add_log( __( 'Webhook status notification', 'sureforms-pro' ) );

		if ( is_int( $log_key ) ) {
			$entries_db->update_log( $log_key, null, [ $log_message ] );
		}

		$entries_db::update(
			$entry_id,
			[
				'logs' => $entries_db->get_logs(),
			]
		);
	}

	/**
	 * Trigger Webhook
	 *
	 * @param array<mixed> $webhook webhook setting.
	 * @return array{ bool, mixed }
	 */
	public function trigger_webhook( $webhook ) {
		if ( ! $webhook['status'] ) {
			return [ false, 'disabled' ];
		}

		if ( ! $this->check_trigger_conditions( $webhook ) ) {
			return [ false, 'trigger conditions not met' ];
		}

		$url = $webhook['url'];

		$headers = [];

		$method = $webhook['method'];

		$body = '';

		$data = $this->add_data_filters( $webhook );

		switch ( $webhook['method'] ) {
			case 'GET':
				$url = add_query_arg( $data, $url );
				break;
			default:
				$body = wp_json_encode( $data );
				break;
		}

		if ( isset( $webhook['format'] ) && 'FORM' === $webhook['format'] ) {
			$headers['Content-type'] = 'multipart/form-data';
		} else {
			$headers['Content-type'] = 'application/json';
		}

		$headers = $this->add_custom_headers( $headers, $webhook );

		$response = wp_remote_request(
			$url,
			[
				'method'  => Helper::get_string_value( $method ),
				'headers' => $headers,
				'body'    => Helper::get_string_value( $body ),
			]
		);

		if ( is_wp_error( $response ) ) {
			return [ false, $response ];
		}

		return [ false, $response ];
	}

	/**
	 * Add custom headers
	 *
	 * @param array<mixed> $headers request header.
	 * @param array<mixed> $webhook webhook setting.
	 * @since  0.0.1
	 * @return array<mixed>
	 */
	public function add_custom_headers( $headers, $webhook ) {

		$has_custom_headers = $webhook['has_custom_headers'] ?? false;

		if ( $has_custom_headers &&
			is_array( $webhook['custom_headers'] ) &&
			! empty( $webhook['custom_headers'] )
		) {
			foreach ( $webhook['custom_headers'] as $custom_header ) {
				$custom_header_key   = Helper::get_string_value( array_keys( $custom_header )[0] );
				$custom_header_value = Helper::get_string_value( array_values( $custom_header )[0] );
				if ( ! empty( $custom_header_key ) ) {
					$headers[ $custom_header_key ] = $custom_header_value;
				}
			}
		}
		return $headers;
	}

	/**
	 * Filter submission data
	 *
	 * @param array<mixed> $webhook webhook setting.
	 * @since  0.0.1
	 * @return array<mixed>
	 */
	public function add_data_filters( $webhook ) {
		$filtered_data = [];
		$smart_tags    = new Smart_Tags();

		$has_custom_data_filters = $webhook['has_custom_data_filters'] ?? false;

		if ( $has_custom_data_filters &&
			is_array( $webhook['custom_data_filters'] ) &&
			! empty( $webhook['custom_data_filters'] )
		) {
			// Add a flag to identify that this data is being processed for webhooks.
			// This helps determine the data format during smart tags processing.
			$this->form_data['_is_webhook_processing'] = true;
			foreach ( $webhook['custom_data_filters'] as $custom_data_filter ) {
				$custom_data_filter_key   = Helper::get_string_value( array_keys( $custom_data_filter )[0] );
				$custom_data_filter_value = Helper::get_string_value( array_values( $custom_data_filter )[0] );
				if ( ! empty( $custom_data_filter_key ) ) {
					$filtered_data[ $custom_data_filter_key ] = $smart_tags->process_smart_tags( $custom_data_filter_value, $this->form_data );
				}
			}
		}
		return empty( $filtered_data ) ? $this->submission_data : $filtered_data;
	}

	/**
	 * Check conditional logic for trigger
	 *
	 * @param array<mixed> $webhook webhook setting.
	 * @since  1.2.2
	 * @return bool
	 */
	public function check_trigger_conditions( $webhook ) {
		$trigger = true;

		$has_trigger_conditions = $webhook['has_trigger_conditions'] ?? false;

		if ( $has_trigger_conditions &&
			is_array( $webhook['trigger_conditions'] ) &&
			! empty( $webhook['trigger_conditions'] ) && ! empty( $webhook['trigger_conditions']['operator'] ) &&
			! empty( $webhook['trigger_conditions']['conditions'] )
		) {
			return $this->process_logic( $webhook['trigger_conditions'] );
		}
		return $trigger;
	}

	/**
	 * Process conditional logic.
	 *
	 * @param array{operator: string, conditions: array<array{field: string, operator: string, value: string}>} $rules Schema of conditional logic.
	 * @since  1.2.2
	 * @return bool
	 */
	public function process_logic( $rules ) {
		// By default trigger is true.
		$trigger = true;

		// Check conditions in the rules. if conditions are not empty then process the conditions.
		switch ( $rules['operator'] ) {
			case '_AND_':
				foreach ( $rules['conditions'] as $rule ) {
					if ( ! $this->process_condition( $rule ) ) {
						$trigger = false;
						break;
					}
				}
				break;
			case '_OR_':
				foreach ( $rules['conditions'] as $rule ) {
					if ( true === $this->process_condition( $rule ) ) {
						$trigger = true;
						break;
					}
					if ( false === $this->process_condition( $rule ) ) {
						$trigger = false;
					}
				}
				break;
		}

		return $trigger;
	}

	/**
	 * Process conditional logic.
	 *
	 * @param array<string> $rule logical conditions.
	 * @since  1.2.2
	 * @return bool|string
	 */
	public function process_condition( $rule ) {
		if ( ! is_array( $rule ) || ! isset( $rule['field'] ) ) {
			return 'field_not_found';
		}

		// Check if field has ":" then split and get the field and value. this is in case of form:field present. in that time we need to get only field.
		$rule['field'] = is_string( $rule['field'] ) && strpos( $rule['field'], ':' ) ? explode( ':', $rule['field'] )[1] : $rule['field'];

		if ( ! isset( $this->submission_data[ $rule['field'] ] ) ) {
			return 'field_not_found';
		}

		$value = Helper::get_string_value( $rule['value'] );
		$field = Helper::get_string_value( $this->submission_data[ $rule['field'] ] );

		switch ( $rule['operator'] ) {
			case '_EQUAL_':
				$trigger = (
					$value === $field
				);
				break;

			case '_NOT_EQUAL_':
				$trigger = (
					$value !== $field
				);
				break;

			case '_GREATER_':
				$trigger = (
					$value < $field
				);
				break;

			case '_GREATER_OR_EQUAL_':
				$trigger = (
					$value <= $field
				);
				break;

			case '_LESSER_':
				$trigger = (
					$value > $field
				);
				break;

			case '_LESSER_OR_EQUAL_':
				$trigger = (
					$value >= $field
				);
				break;

			case '_STARTS_WITH_':
				$trigger = 0 === substr_compare(
					$field,
					$value,
					0,
					strlen( $value )
				);
				break;

			case '_ENDS_WITH_':
				$trigger = (
					0 === substr_compare(
						$field,
						$value,
						-strlen( $value )
					)
				);
				break;

			case '_CONTAINS_':
				$trigger = false !== strpos(
					$field,
					$value
				);
				break;
			case '_NOT_CONTAINS_':
				$trigger = false === strpos(
					$field,
					$value
				);
				break;

			case '_REGEX_':
				$trigger = 1 === preg_match(
					$value,
					$field
				);
				break;
			default:
				$trigger = true;
				break;
		}

		return $trigger;
	}
}
