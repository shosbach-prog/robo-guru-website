<?php
/**
 * Hooks.
 *
 * All hooks which are required in frontend and backend are managed in this class.
 *
 * @package sureforms-pro.
 * @since 0.0.1
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM\Inc\Database\Tables\Entries;
use SRFM\Inc\Form_Submit;
use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Helper as Pro_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;
use SRFM_Pro\Inc\Translatable;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gutenberg_Hooks Class.
 *
 * @since 0.0.1
 */
class Hooks {
	use Get_Instance;

	/**
	 * An associative array used to store custom styling related values.
	 *
	 * @var array<string,mixed>
	 * @since 1.6.3
	 */
	protected $data = [];

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function __construct() {
		add_filter( 'srfm_register_additional_blocks', [ $this, 'register_pro_blocks' ] );
		add_filter( 'srfm_blocks', [ $this, 'add_pro_blocks' ] );
		add_filter( 'srfm_css_vars_sizes', [ $this, 'merge_pro_block_sizes' ] );
		add_filter( 'srfm_default_dynamic_block_option', [ $this, 'add_default_dynamic_pro_block_values' ], 10, 2 );
		add_filter( 'srfm_general_dynamic_options_to_save', [ $this, 'add_pro_options_to_save' ], 10, 2 );
		add_filter( 'srfm_add_button_classes', [ $this, 'add_pro_btn_classes' ], 10, 2 );
		add_filter( 'srfm_add_background_classes', [ $this, 'add_custom_styling_class' ], 10, 2 );
		add_action( 'srfm_register_additional_post_meta', [ $this, 'register_starter_post_metas' ] );
		add_action( 'srfm_form_css_variables', [ $this, 'add_premium_form_styling_variables' ] );
		add_filter( 'srfm_rest_api_endpoints', [ $this, 'register_entries_endpoints' ] );
		add_filter( 'srfm_extract_form_fields_field_name', [ $this, 'change_field_name' ], 10, 4 );

		/**
		 * Add upload files to form data.
		 */
		add_filter( 'srfm_form_submit_data', [ $this, 'add_upload_files_to_form_data' ], 10, 1 );

		/**
		 * Delete entry files before deleting the entry.
		 */
		add_action( 'srfm_pro_before_deleting_entry', [ $this, 'delete_entry_files' ], 10, 1 );

		/**
		 * Register hooks for translatable class.
		 */
		Translatable::hooks();

		/**
		 * Register hooks for field validation.
		 */
		Field_Validation::get_instance();

		add_action( 'srfm_before_delete_entry', [ $this, 'before_delete_entry' ] );
	}

	/**
	 * Before deleting the entry.
	 *
	 * @param int $entry_id The ID of the entry to delete.
	 *
	 * @since 1.8.0
	 * @return void
	 */
	public function before_delete_entry( $entry_id ) {
		$entry = Entries::get_entry_data( $entry_id );
		if ( empty( $entry ) ) {
			return;
		}

		// Do action to delete the entry files.
		do_action( 'srfm_pro_before_deleting_entry', $entry );
	}

	/**
	 * Register REST API endpoints for entries management.
	 *
	 * @param array<string, mixed> $endpoints Array of endpoints.
	 * @since 2.0.0
	 * @return array<string, mixed>
	 */
	public function register_entries_endpoints( $endpoints ) {
		$endpoints['entry/(?P<id>\d+)/logs/(?P<log_id>\d+)'] = [
			'methods'             => 'DELETE',
			'callback'            => [ $this, 'delete_entry_log' ],
			'permission_callback' => [ Helper::class, 'get_items_permissions_check' ],
			'args'                => [
				'id'     => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
				'log_id' => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
			],
		];

		// Fetch, Add, and Delete Entry Notes.
		$endpoints['entry/(?P<id>\d+)/notes'] = [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_entry_notes' ],
				'permission_callback' => [ Helper::class, 'get_items_permissions_check' ],
				'args'                => [
					'id'       => [
						'required'          => true,
						'sanitize_callback' => 'absint',
					],
					'per_page' => [
						'sanitize_callback' => 'absint',
						'default'           => 10,
					],
					'page'     => [
						'sanitize_callback' => 'absint',
						'default'           => 1,
					],
				],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'add_entry_note' ],
				'permission_callback' => [ Helper::class, 'get_items_permissions_check' ],
				'args'                => [
					'id'   => [
						'required'          => true,
						'sanitize_callback' => 'absint',
					],
					'note' => [
						'required'          => true,
						'sanitize_callback' => 'sanitize_textarea_field',
					],
				],
			],
			[
				'methods'             => 'DELETE',
				'callback'            => [ $this, 'delete_entry_note' ],
				'permission_callback' => [ Helper::class, 'get_items_permissions_check' ],
				'args'                => [
					'id'      => [
						'required'          => true,
						'sanitize_callback' => 'absint',
					],
					'note_id' => [
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			],
		];

		// Resend Entry Notification (single or multiple entries).
		$endpoints['entries/resend-notification'] = [
			'methods'             => 'POST',
			'callback'            => [ $this, 'resend_notification' ],
			'permission_callback' => [ Helper::class, 'get_items_permissions_check' ],
			'args'                => [
				'entry_ids'          => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				],
				'form_id'            => [
					'sanitize_callback' => 'absint',
					'default'           => 0,
				],
				'email_notification' => [
					'sanitize_callback' => 'absint',
					'default'           => 0,
				],
				'send_to'            => [
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'default',
				],
				'recipient'          => [
					'sanitize_callback' => 'sanitize_email',
					'default'           => '',
				],
			],
		];

		// Get enabled email notifications for a form.
		$endpoints['entries/(?P<form_id>\d+)/enabled-email-notifications'] = [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_enabled_email_notifications' ],
			'permission_callback' => [ Helper::class, 'get_items_permissions_check' ],
			'args'                => [
				'form_id' => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
			],
		];
		// Update Entry Data.
		$endpoints['entry/edit'] = [
			'methods'             => 'PUT',
			'callback'            => [ $this, 'update_entry_data' ],
			'permission_callback' => [ Helper::class, 'get_items_permissions_check' ],
			'args'                => [
				'id'        => [
					'required'          => true,
					'sanitize_callback' => 'absint',
				],
				'form_data' => [
					'required'          => true,
					'validate_callback' => [ $this, 'validate_entry_form_data' ],
				],
			],
		];

		return $endpoints;
	}

	/**
	 * Delete a specific entry log.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @since 2.0.0
	 * @return \WP_REST_Response
	 */
	public function delete_entry_log( $request ) {
		$nonce = Helper::get_string_value( $request->get_header( 'X-WP-Nonce' ) );

		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Nonce verification failed.', 'sureforms-pro' ) ],
				403
			);
		}

		$entry_id = absint( $request->get_param( 'id' ) );
		$log_id   = absint( $request->get_param( 'log_id' ) );

		if ( empty( $entry_id ) || ! isset( $log_id ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry ID and Log ID are required.', 'sureforms-pro' ) ],
				400
			);
		}

		$entry = Entries::get( $entry_id );

		if ( ! $entry ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry not found.', 'sureforms-pro' ) ],
				404
			);
		}

		$logs = isset( $entry['logs'] ) && is_array( $entry['logs'] ) ? $entry['logs'] : [];

		if ( ! isset( $logs[ $log_id ] ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Log not found.', 'sureforms-pro' ) ],
				404
			);
		}

		// Remove the log at the specified index.
		unset( $logs[ $log_id ] );
		$logs = array_values( $logs );

		// Update the entry with modified logs.
		$update_result = Entries::get_instance()->use_update(
			[ 'logs' => $logs ],
			[ 'ID' => absint( $entry_id ) ]
		);

		if ( false === $update_result ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Failed to delete log.', 'sureforms-pro' ) ],
				500
			);
		}

		return new \WP_REST_Response( [ 'success' => true ], 200 );
	}

	/**
	 * Delete a specific entry note.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @since 2.0.0
	 * @return \WP_REST_Response
	 */
	public function delete_entry_note( $request ) {
		$nonce = Helper::get_string_value( $request->get_header( 'X-WP-Nonce' ) );

		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Nonce verification failed.', 'sureforms-pro' ) ],
				403
			);
		}

		$entry_id = absint( $request->get_param( 'id' ) );
		$note_id  = sanitize_text_field( Helper::get_string_value( $request->get_param( 'note_id' ) ) );

		if ( empty( $entry_id ) || empty( $note_id ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry ID and Note ID are required.', 'sureforms-pro' ) ],
				400
			);
		}

		$entry = Entries::get( $entry_id );

		if ( ! $entry ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry not found.', 'sureforms-pro' ) ],
				404
			);
		}

		$notes      = isset( $entry['notes'] ) && is_array( $entry['notes'] ) ? $entry['notes'] : [];
		$note_found = false;

		// Find and remove the note with matching ID.
		foreach ( $notes as $index => $note ) {
			if ( isset( $note['id'] ) && Helper::get_string_value( $note['id'] ) === Helper::get_string_value( $note_id ) ) {
				array_splice( $notes, $index, 1 );
				$note_found = true;
				break;
			}
		}

		if ( ! $note_found ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Note not found.', 'sureforms-pro' ) ],
				404
			);
		}

		// Update the entry with modified notes.
		$update_result = Entries::get_instance()->update(
			$entry_id,
			[ 'notes' => $notes ]
		);

		if ( false === $update_result ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Failed to delete note.', 'sureforms-pro' ) ],
				500
			);
		}

		return new \WP_REST_Response( [ 'success' => true ], 200 );
	}

	/**
	 * Get entry notes with pagination support.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @since 2.0.0
	 * @return \WP_REST_Response
	 */
	public function get_entry_notes( $request ) {
		$nonce = Helper::get_string_value( $request->get_header( 'X-WP-Nonce' ) );

		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Nonce verification failed.', 'sureforms-pro' ) ],
				403
			);
		}

		$entry_id = absint( $request->get_param( 'id' ) );
		$per_page = absint( $request->get_param( 'per_page' ) );
		$per_page = $per_page ? $per_page : 3;
		$page     = absint( $request->get_param( 'page' ) );
		$page     = $page ? $page : 1;

		if ( empty( $entry_id ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry ID is required.', 'sureforms-pro' ) ],
				400
			);
		}

		$entry = Entries::get( $entry_id );

		if ( ! $entry ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry not found.', 'sureforms-pro' ) ],
				404
			);
		}

		$notes = isset( $entry['notes'] ) && is_array( $entry['notes'] ) ? $entry['notes'] : [];

		// Sort notes by timestamp in descending order (newest first).
		usort(
			$notes,
			static function( $a, $b ) {
				return ( $b['timestamp'] ?? 0 ) - ( $a['timestamp'] ?? 0 );
			}
		);

		$total_notes = count( $notes );
		$total_pages = ceil( $total_notes / $per_page );
		$offset      = ( $page - 1 ) * $per_page;

		// Paginate notes.
		$paginated_notes = array_slice( Helper::get_array_value( $notes ), $offset, $per_page );

		// Format notes with unique IDs for operations.
		$formatted_notes = [];
		foreach ( $paginated_notes as $note ) {
			if ( ! is_array( $note ) ) {
				continue;
			}
			$formatted_notes[] = [
				'id'          => $note['id'] ?? time(), // Use stored ID or generate one.
				'note'        => $note['note'] ?? '',
				'timestamp'   => $note['timestamp'] ?? time(),
				'author'      => $note['author'] ?? get_current_user_id(),
				'author_name' => $note['author_name'] ?? wp_get_current_user()->display_name,
			];
		}

		$response_data = [
			'notes'        => $formatted_notes,
			'current_page' => $page,
			'per_page'     => $per_page,
			'total'        => $total_notes,
			'total_pages'  => $total_pages,
		];

		return new \WP_REST_Response( $response_data, 200 );
	}

	/**
	 * Add a new note to an entry.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @since 2.0.0
	 * @return \WP_REST_Response
	 */
	public function add_entry_note( $request ) {
		$nonce = Helper::get_string_value( $request->get_header( 'X-WP-Nonce' ) );

		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Nonce verification failed.', 'sureforms-pro' ) ],
				403
			);
		}

		$entry_id = absint( $request->get_param( 'id' ) );
		$content  = sanitize_textarea_field( Helper::get_string_value( $request->get_param( 'note' ) ) );

		if ( empty( $entry_id ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry ID is required.', 'sureforms-pro' ) ],
				400
			);
		}

		if ( empty( $content ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Note content is required.', 'sureforms-pro' ) ],
				400
			);
		}

		$entry = Entries::get( $entry_id );

		if ( ! $entry ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry not found.', 'sureforms-pro' ) ],
				404
			);
		}

		$notes        = isset( $entry['notes'] ) && is_array( $entry['notes'] ) ? $entry['notes'] : [];
		$current_user = wp_get_current_user();
		$timestamp    = time();

		// Create new note.
		$new_note = [
			'id'          => $timestamp, // Unique ID with timestamp.
			'note'        => $content,
			'timestamp'   => $timestamp,
			'author'      => $current_user->ID,
			'author_name' => $current_user->display_name,
		];

		// Add note to the beginning of the array (newest first).
		array_unshift( $notes, $new_note );

		// Update the entry with new notes.
		$update_result = Entries::get_instance()->update(
			$entry_id,
			[ 'notes' => $notes ]
		);

		if ( false === $update_result ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Failed to add note.', 'sureforms-pro' ) ],
				500
			);
		}

		return new \WP_REST_Response(
			[
				'success' => true,
				'note'    => $new_note,
			],
			201
		);
	}

	/**
	 * Resend notification for single or multiple entries.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @since 2.0.0
	 * @return \WP_REST_Response
	 */
	public function resend_notification( $request ) {
		$nonce = Helper::get_string_value( $request->get_header( 'X-WP-Nonce' ) );

		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Nonce verification failed.', 'sureforms-pro' ) ],
				403
			);
		}

		if ( ! Helper::current_user_can() ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Permission denied.', 'sureforms-pro' ) ],
				403
			);
		}

		// Get entry IDs from request body (supports single or multiple).
		$entry_ids_param       = $request->get_param( 'entry_ids' );
		$form_id               = absint( $request->get_param( 'form_id' ) );
		$email_notification_id = absint( $request->get_param( 'email_notification' ) );
		$send_to               = sanitize_text_field( Helper::get_string_value( $request->get_param( 'send_to' ) ) );
		$send_to               = $send_to ? $send_to : 'default';
		$recipient             = '';

		// Parse entry IDs (string, array, or single number).
		$entry_ids = [];
		if ( is_string( $entry_ids_param ) ) {
			// Handle comma-separated string or single ID.
			$entry_ids = array_map( 'absint', explode( ',', $entry_ids_param ) );
		} elseif ( is_array( $entry_ids_param ) ) {
			// Handle array of IDs.
			$entry_ids = array_map( 'absint', $entry_ids_param );
		} elseif ( is_numeric( $entry_ids_param ) ) {
			// Handle single numeric ID.
			$entry_ids = [ absint( $entry_ids_param ) ];
		}

		if ( empty( $entry_ids ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Entry ID(s) are required.', 'sureforms-pro' ) ],
				400
			);
		}

		if ( 'other' === $send_to ) {
			$recipient = sanitize_email( Helper::get_string_value( $request->get_param( 'recipient' ) ) );
			if ( ! is_email( $recipient ) ) {
				return new \WP_REST_Response(
					[ 'error' => __( 'You must provide a valid email for the recipient.', 'sureforms-pro' ) ],
					400
				);
			}
		}

		// Get form_id from first entry if not provided.
		if ( ! $form_id ) {
			$first_entry = Entries::get( $entry_ids[0] );
			if ( ! $first_entry ) {
				return new \WP_REST_Response(
					[ 'error' => __( 'Entry not found.', 'sureforms-pro' ) ],
					404
				);
			}
			$form_id = $first_entry['form_id'];
		}

		$email_notifications = Helper::get_array_value( get_post_meta( Helper::get_integer_value( $form_id ), '_srfm_email_notification', true ) );

		if ( empty( $email_notifications ) || ! is_array( $email_notifications ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'No email notifications found for this form.', 'sureforms-pro' ) ],
				404
			);
		}

		$notification_found = false;
		$display_name       = wp_get_current_user()->display_name;

		foreach ( $email_notifications as $notification ) {
			// If email_notification_id is provided, only process that specific notification.
			if ( $email_notification_id && absint( $notification['id'] ) !== $email_notification_id ) {
				continue;
			}

			if ( true !== $notification['status'] ) {
				// Email notification is toggled off, skip it.
				continue;
			}

			$notification_found = true;

			// Process each entry ID.
			foreach ( $entry_ids as $entry_id ) {
				// We don't want the same instance here, instead we need to init new object for each entry id here.
				$entries_db = new Entries();
				$log_key    = $entries_db->add_log(
					sprintf(
						/* translators: Here %1$s is email notification label and %2$s is the user display name. */
						__( 'Resend email notification "%1$s" initiated by %2$s.', 'sureforms-pro' ),
						esc_html( $notification['name'] ),
						esc_html( $display_name )
					)
				);

				$form_data = Helper::get_array_value( $entries_db::get( $entry_id )['form_data'] );
				$parsed    = Form_Submit::parse_email_notification_template( $form_data, $notification );

				// If user has provided recipient then reroute email to user provided recipient.
				$email_to = $recipient ? $recipient : $parsed['to'];
				$sent     = wp_mail( $email_to, $parsed['subject'], $parsed['message'], $parsed['headers'] );

				// Log the result.
				/* translators: Here, %s is email address. */
				$log_message = $sent ? sprintf( __( 'Email notification recipient: %s', 'sureforms-pro' ), esc_html( $email_to ) ) : sprintf( __( 'Failed sending email notification to %s', 'sureforms-pro' ), esc_html( $email_to ) );

				if ( is_int( $log_key ) ) {
					$entries_db->update_log( $log_key, null, [ $log_message ] );
				}

				// Update the entry with modified logs.
				$entries_db::update(
					$entry_id,
					[
						'logs' => $entries_db->get_logs(),
					]
				);
			}
		}

		if ( ! $notification_found ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'No active email notifications found.', 'sureforms-pro' ) ],
				404
			);
		}

		return new \WP_REST_Response(
			[
				'success' => true,
				'message' => __( 'Email notification sent successfully.', 'sureforms-pro' ),
			],
			200
		);
	}

	/**
	 * Get enabled email notifications for a form.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @since 2.0.0
	 * @return \WP_REST_Response
	 */
	public function get_enabled_email_notifications( $request ) {
		$nonce = Helper::get_string_value( $request->get_header( 'X-WP-Nonce' ) );

		if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), 'wp_rest' ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Nonce verification failed.', 'sureforms-pro' ) ],
				403
			);
		}

		$form_id = absint( $request->get_param( 'form_id' ) );

		if ( empty( $form_id ) ) {
			return new \WP_REST_Response(
				[ 'error' => __( 'Form ID is required.', 'sureforms-pro' ) ],
				400
			);
		}

		/**
		 * Retrieve email notification options from form meta.
		 *
		 * @var array<int|string, array<string, bool|int|string>> $email_notifications Email notification option items.
		 */
		$email_notifications = Helper::get_array_value( get_post_meta( $form_id, '_srfm_email_notification', true ) );

		if ( empty( $email_notifications ) ) {
			return new \WP_REST_Response(
				[ 'enabled_email_notifications' => [] ],
				200
			);
		}

		$enabled_email_notifications = Entries_Management::get_enabled_email_notifications( $email_notifications );

		return new \WP_REST_Response(
			[ 'enabled_email_notifications' => $enabled_email_notifications ],
			200
		);
	}

	/**
	 * Register starter blocks.
	 *
	 * @param array $blocks Blocks.
	 * @return array
	 */
	public function register_pro_blocks( $blocks ) {
		$blocks[] = [
			'dir'       => SRFM_PRO_DIR . 'inc/blocks/**/*.php',
			'namespace' => 'SRFM_PRO\\Inc\\Blocks',
		];
		return $blocks;
	}

	/**
	 * Registers the sureforms metas.
	 *
	 * @return void
	 * @since 1.4.0
	 */
	public function register_starter_post_metas() {
		// Registers metas that are common for the variations of premium plugin.
		register_post_meta(
			SRFM_FORMS_POST_TYPE,
			'_srfm_premium_common',
			[
				'single'        => true,
				'type'          => 'object',
				'auth_callback' => static function() {
					return Helper::current_user_can();
				},
				'show_in_rest'  => [
					'schema' => [
						'type'       => 'object',
						'context'    => [ 'edit' ],
						'properties' => [
							'is_welcome_screen'        => [
								'type' => 'boolean',
							],
							'welcome_screen_heading'   => [
								'type' => 'string',
							],
							'welcome_screen_message'   => [
								'type' => 'string',
							],
							'welcome_screen_image'     => [
								'type' => 'string',
							],
							'start_btn_text'           => [
								'type' => 'string',
							],
							// Welcome Screen Heading Size.
							'heading_text_size'        => [
								'type' => 'number',
							],
							'heading_text_size_unit'   => [
								'type' => 'string',
							],
							'heading_line_height'      => [
								'type' => 'number',
							],
							'heading_line_height_unit' => [
								'type' => 'string',
							],
							// Welcome Screen Message Size.
							'message_text_size'        => [
								'type' => 'number',
							],
							'message_text_size_unit'   => [
								'type' => 'string',
							],
							'message_line_height'      => [
								'type' => 'number',
							],
							'message_line_height_unit' => [
								'type' => 'string',
							],
						],
					],
				],
				'default'       => [
					'is_welcome_screen'        => false,
					'welcome_screen_heading'   => __( 'Help Us Gather Valuable Insights!', 'sureforms-pro' ),
					'welcome_screen_message'   => __( 'Answer a few quick questions to share your thoughts and make your voice heard.', 'sureforms-pro' ),
					'welcome_screen_image'     => '',
					'start_btn_text'           => __( 'Get Started', 'sureforms-pro' ),
					// Welcome Screen Heading Size.
					'heading_text_size'        => 30,
					'heading_text_size_unit'   => 'px',
					'heading_line_height'      => 1.4,
					'heading_line_height_unit' => 'em',
					// Welcome Screen Message Size.
					'message_text_size'        => 18,
					'message_text_size_unit'   => 'px',
					'message_line_height'      => 1.4,
					'message_line_height_unit' => 'em',
				],
			]
		);

		register_post_meta(
			SRFM_FORMS_POST_TYPE,
			'_srfm_forms_styling_starter',
			[
				'single'        => true,
				'type'          => 'object',
				'auth_callback' => static function() {
					return Helper::current_user_can();
				},
				'show_in_rest'  => [
					'schema' => [
						'type'       => 'object',
						'context'    => [ 'edit' ],
						'properties' => [
							'form_theme'                   => [
								'type' => 'string',
							],
							'error_color'                  => [
								'type' => 'string',
							],
							'form_row_gap'                 => [
								'type' => 'integer',
							],
							'form_row_gap_type'            => [
								'type' => 'string',
							],
							'form_column_gap'              => [
								'type' => 'integer',
							],
							'form_column_gap_type'         => [
								'type' => 'string',
							],
							// Button Properties.
							'button_padding_top'           => [
								'type' => 'number',
							],
							'button_padding_right'         => [
								'type' => 'number',
							],
							'button_padding_bottom'        => [
								'type' => 'number',
							],
							'button_padding_left'          => [
								'type' => 'number',
							],
							'button_padding_unit'          => [
								'type' => 'string',
							],
							'button_padding_link'          => [
								'type' => 'boolean',
							],
							// Border Width.
							'button_border_style'          => [
								'type' => 'string',
							],
							'button_border_width_top'      => [
								'type' => 'number',
							],
							'button_border_width_right'    => [
								'type' => 'number',
							],
							'button_border_width_bottom'   => [
								'type' => 'number',
							],
							'button_border_width_left'     => [
								'type' => 'number',
							],
							'button_border_width_link'     => [
								'type' => 'boolean',
							],
							// Border Radius.
							'button_border_radius_top'     => [
								'type' => 'number',
							],
							'button_border_radius_right'   => [
								'type' => 'number',
							],
							'button_border_radius_bottom'  => [
								'type' => 'number',
							],
							'button_border_radius_left'    => [
								'type' => 'number',
							],
							'button_border_radius_unit'    => [
								'type' => 'string',
							],
							'button_border_radius_link'    => [
								'type' => 'boolean',
							],
							// Border Color.
							'button_border_color_normal'   => [
								'type' => 'string',
							],
							'button_border_color_hover'    => [
								'type' => 'string',
							],
							// Background Normal.
							'button_text_color_normal'     => [
								'type' => 'string',
							],
							'button_background_type_normal' => [
								'type' => 'string',
							],
							'button_background_color_normal' => [
								'type' => 'string',
							],
							'button_gradient_type_normal'  => [
								'type' => 'string',
							],
							'button_background_gradient_type_normal' => [
								'type' => 'string',
							],
							'button_background_gradient_color_1_normal' => [
								'type' => 'string',
							],
							'button_background_gradient_color_2_normal' => [
								'type' => 'string',
							],
							'button_background_gradient_location_1_normal' => [
								'type' => 'integer',
							],
							'button_background_gradient_location_2_normal' => [
								'type' => 'integer',
							],
							'button_background_gradient_angle_normal' => [
								'type' => 'integer',
							],
							'button_background_gradient_normal' => [
								'type' => 'string',
							],
							// Background Hover.
							'button_text_color_hover'      => [
								'type' => 'string',
							],
							'button_background_type_hover' => [
								'type' => 'string',
							],
							'button_background_color_hover' => [
								'type' => 'string',
							],
							'button_gradient_type_hover'   => [
								'type' => 'string',
							],
							'button_background_gradient_type_hover' => [
								'type' => 'string',
							],
							'button_background_gradient_color_1_hover' => [
								'type' => 'string',
							],
							'button_background_gradient_color_2_hover' => [
								'type' => 'string',
							],
							'button_background_gradient_location_1_hover' => [
								'type' => 'integer',
							],
							'button_background_gradient_location_2_hover' => [
								'type' => 'integer',
							],
							'button_background_gradient_angle_hover' => [
								'type' => 'integer',
							],
							'button_background_gradient_hover' => [
								'type' => 'string',
							],
							// Typography.
							'button_text_size'             => [
								'type' => 'number',
							],
							'button_text_size_unit'        => [
								'type' => 'string',
							],
							'button_line_height'           => [
								'type' => 'number',
							],
							'button_line_height_unit'      => [
								'type' => 'string',
							],
							// Field Properties.
							'field_bg_color'               => [
								'type' => 'string',
							],
							'field_label_color'            => [
								'type' => 'string',
							],
							'field_help_text_color'        => [
								'type' => 'string',
							],
							'field_label_size'             => [
								'type' => 'number',
							],
							'field_label_size_unit'        => [
								'type' => 'string',
							],
							'field_label_line_height'      => [
								'type' => 'number',
							],
							'field_label_line_height_unit' => [
								'type' => 'string',
							],
							'field_help_text_size'         => [
								'type' => 'number',
							],
							'field_help_text_size_unit'    => [
								'type' => 'string',
							],
							'field_help_text_line_height'  => [
								'type' => 'number',
							],
							'field_help_text_line_height_unit' => [
								'type' => 'string',
							],
							'field_border_color'           => [
								'type' => 'string',
							],
							'field_label_gap'              => [
								'type' => 'number',
							],
							'field_label_gap_unit'         => [
								'type' => 'string',
							],
							'field_border_width_top'       => [
								'type' => 'number',
							],
							'field_border_width_right'     => [
								'type' => 'number',
							],
							'field_border_width_bottom'    => [
								'type' => 'number',
							],
							'field_border_width_left'      => [
								'type' => 'number',
							],
							'field_border_width_unit'      => [
								'type' => 'string',
							],
							'field_border_width_link'      => [
								'type' => 'boolean',
							],
							'field_border_radius_top'      => [
								'type' => 'number',
							],
							'field_border_radius_right'    => [
								'type' => 'number',
							],
							'field_border_radius_bottom'   => [
								'type' => 'number',
							],
							'field_border_radius_left'     => [
								'type' => 'number',
							],
							'field_border_radius_unit'     => [
								'type' => 'string',
							],
							'field_border_radius_link'     => [
								'type' => 'boolean',
							],
							'field_box_shadow_color_normal' => [
								'type' => 'string',
							],
							'field_box_shadow_horizontal_offset_normal' => [
								'type' => 'number',
							],
							'field_box_shadow_vertical_offset_normal' => [
								'type' => 'number',
							],
							'field_box_shadow_blur_normal' => [
								'type' => 'number',
							],
							'field_box_shadow_spread_normal' => [
								'type' => 'number',
							],
							'field_box_shadow_position_normal' => [
								'type' => 'string',
							],
							'field_box_shadow_color_focus' => [
								'type' => 'string',
							],
							'field_box_shadow_horizontal_offset_focus' => [
								'type' => 'number',
							],
							'field_box_shadow_vertical_offset_focus' => [
								'type' => 'number',
							],
							'field_box_shadow_blur_focus'  => [
								'type' => 'number',
							],
							'field_box_shadow_spread_focus' => [
								'type' => 'number',
							],
							'field_box_shadow_position_focus' => [
								'type' => 'string',
							],
							// Page Break Buttons.
							'page_break_next_button_text_color_normal' => [
								'type' => 'string',
							],
							'page_break_next_button_background_color_normal' => [
								'type' => 'string',
							],
							'page_break_next_button_border_color_normal' => [
								'type' => 'string',
							],
							'page_break_back_button_text_color_normal' => [
								'type' => 'string',
							],
							'page_break_back_button_background_color_normal' => [
								'type' => 'string',
							],
							'page_break_back_button_border_color_normal' => [
								'type' => 'string',
							],
							'page_break_next_button_text_color_hover' => [
								'type' => 'string',
							],
							'page_break_next_button_background_color_hover' => [
								'type' => 'string',
							],
							'page_break_next_button_border_color_hover' => [
								'type' => 'string',
							],
							'page_break_back_button_text_color_hover' => [
								'type' => 'string',
							],
							'page_break_back_button_background_color_hover' => [
								'type' => 'string',
							],
							'page_break_back_button_border_color_hover' => [
								'type' => 'string',
							],

						],
					],
				],
				'default'       => [
					'form_theme'                       => 'default',
					'error_color'                      => '#dc2626',
					'form_row_gap'                     => 18,
					'form_row_gap_type'                => 'px',
					'form_column_gap'                  => 16,
					'form_column_gap_type'             => 'px',
					// Button Properties.
					'button_padding_top'               => 10,
					'button_padding_right'             => 14,
					'button_padding_bottom'            => 10,
					'button_padding_left'              => 14,
					'button_padding_unit'              => 'px',
					'button_padding_link'              => false,
					// Border Width.
					'button_border_style'              => 'solid',
					'button_border_width_top'          => 1,
					'button_border_width_right'        => 1,
					'button_border_width_bottom'       => 1,
					'button_border_width_left'         => 1,
					'button_border_width_link'         => true,
					// Border Radius.
					'button_border_radius_top'         => 6,
					'button_border_radius_right'       => 6,
					'button_border_radius_bottom'      => 6,
					'button_border_radius_left'        => 6,
					'button_border_radius_unit'        => 'px',
					'button_border_radius_link'        => true,
					// Border Color.
					'button_border_color_normal'       => '',
					'button_border_color_hover'        => '',
					// Background Normal.
					'button_text_color_normal'         => '',
					'button_background_color_normal'   => '',
					'button_gradient_type_normal'      => 'basic',
					'button_background_gradient_type_normal' => 'linear',
					'button_background_gradient_angle_normal' => 90,
					'button_background_gradient_color_1_normal' => '#FFC9B2',
					'button_background_gradient_color_2_normal' => '#C7CBFF',
					'button_background_gradient_location_1_normal' => 0,
					'button_background_gradient_location_2_normal' => 100,
					// Background Hover.
					'button_text_color_hover'          => '',
					'button_background_color_hover'    => '',
					'button_gradient_type_hover'       => 'basic',
					'button_background_gradient_type_hover' => 'linear',
					'button_background_gradient_angle_hover' => 90,
					'button_background_gradient_color_1_hover' => '#FFC9B2',
					'button_background_gradient_color_2_hover' => '#C7CBFF',
					'button_background_gradient_location_1_hover' => 0,
					'button_background_gradient_location_2_hover' => 100,
					// Typography.
					'button_text_size'                 => 16,
					'button_text_size_unit'            => 'px',
					'button_line_height'               => 1.5,
					'button_line_height_unit'          => 'em',
					// Field Properties.
					'field_bg_color'                   => '#FBFBFB',
					'field_label_color'                => '#1E1E1E',
					'field_help_text_color'            => '#1E1E1EA6',
					'field_label_size'                 => 16,
					'field_label_size_unit'            => 'px',
					'field_label_line_height'          => 1.5,
					'field_label_line_height_unit'     => 'em',
					'field_help_text_size'             => 14,
					'field_help_text_size_unit'        => 'px',
					'field_help_text_line_height'      => 1.4,
					'field_help_text_line_height_unit' => 'em',
					'field_border_color'               => '#C4C4C4',
					'field_label_gap'                  => 6,
					'field_label_gap_unit'             => 'px',
					'field_border_width_top'           => 1,
					'field_border_width_right'         => 1,
					'field_border_width_bottom'        => 1,
					'field_border_width_left'          => 1,
					'field_border_width_unit'          => 'px',
					'field_border_width_link'          => true,
					'field_border_radius_top'          => 6,
					'field_border_radius_right'        => 6,
					'field_border_radius_bottom'       => 6,
					'field_border_radius_left'         => 6,
					'field_border_radius_unit'         => 'px',
					'field_border_radius_link'         => true,
					// Field Box Shadow Properties.
					'field_box_shadow_color_normal'    => '',
					'field_box_shadow_horizontal_offset_normal' => 0,
					'field_box_shadow_vertical_offset_normal' => 0,
					'field_box_shadow_blur_normal'     => 0,
					'field_box_shadow_spread_normal'   => 0,
					'field_box_shadow_position_normal' => 'outset',
					'field_box_shadow_color_focus'     => '',
					'field_box_shadow_horizontal_offset_focus' => 0,
					'field_box_shadow_vertical_offset_focus' => 0,
					'field_box_shadow_blur_focus'      => 0,
					'field_box_shadow_spread_focus'    => 3,
					'field_box_shadow_position_focus'  => 'outset',
					// Page Break Button Properties.
					'page_break_next_button_text_color_normal' => '#FFFFFF',
					'page_break_next_button_background_color_normal' => '#434343',
					'page_break_next_button_border_color_normal' => '#434343',
					'page_break_back_button_text_color_normal' => '#FFFFFF',
					'page_break_back_button_background_color_normal' => '#434343',
					'page_break_back_button_border_color_normal' => '#434343',
					'page_break_next_button_text_color_hover' => '#FFFFFFCC',
					'page_break_next_button_background_color_hover' => '#434343CC',
					'page_break_next_button_border_color_hover' => '#434343CC',
					'page_break_back_button_text_color_hover' => '#FFFFFFCC',
					'page_break_back_button_background_color_hover' => '#434343CC',
					'page_break_back_button_border_color_hover' => '#434343CC',
				],
			]
		);
	}

	/**
	 * Adds the pro blocks in the list free SureForms blocks.
	 *
	 * @param array<string> $blocks SureForms block list.
	 * @since 0.0.1
	 * @return array<string>
	 */
	public function add_pro_blocks( $blocks ) {
		$pro_blocks = [
			// pro blocks.
			'srfm/date-picker',
			'srfm/time-picker',
			'srfm/hidden',
			'srfm/slider',
			'srfm/rating',
			'srfm/upload',
			'srfm/html',
		];

		return array_merge( $blocks, $pro_blocks );
	}

	/**
	 * Adds css variables for pro blocks.
	 *
	 * @param array<string|mixed> $sizes array of css variables coming from hook 'srfm_css_vars_sizes'.
	 * @since 0.0.1
	 * @return array<string|mixed>
	 */
	public function merge_pro_block_sizes( $sizes ) {
		if ( ! is_array( $sizes ) || empty( $sizes ) ) {
			return $sizes;
		}

		$pro_block_sizes = [
			'small'  => [
				// Upload block variables.
				'--srfm-upload-vertical-padding'       => '24px',
				'--srfm-upload-inner-gap'              => '12px',
				'--srfm-upload-text-line-height'       => '20px',
				'--srfm-upload-file-margin-top'        => '12px',
				'--srfm-upload-preview-size'           => '40px',
				// Slider block variables.
				'--srfm-slider-label-font-size'        => '12px',
				'--srfm-slider-label-line-height'      => '16px',
				'--srfm-slider-label-top-padding'      => '6px',
				'--srfm-slider-error-gap'              => '4px',
				// Page break block variables.
				'--srfm-page-break-row-gap'            => '24px',
				'--srfm-rating-icon-size'              => '24px',
				'--srfm-rating-icon-gap'               => '4px',

				// Datepicker block variables.
				'--srfm-datepicker-dropdown-input-gap' => '4px',
			],
			'medium' => [
				// Upload block variables.
				'--srfm-upload-vertical-padding'       => '28px',
				'--srfm-upload-text-line-height'       => '24px',
				'--srfm-upload-file-margin-top'        => '16px',
				'--srfm-upload-preview-size'           => '42px',
				// Slider block variables.
				'--srfm-slider-label-top-padding'      => '8px',
				'--srfm-slider-error-gap'              => '6px',
				// Page break block variables.
				'--srfm-page-break-row-gap'            => '28px',
				'--srfm-rating-icon-size'              => '28px',
				'--srfm-rating-icon-gap'               => '6px',

				// Datepicker block variables.
				'--srfm-datepicker-dropdown-input-gap' => '4px',
			],
			'large'  => [
				// Upload block variables.
				'--srfm-upload-vertical-padding'       => '32px',
				'--srfm-upload-inner-gap'              => '14px',
				'--srfm-upload-text-line-height'       => '28px',
				'--srfm-upload-file-margin-top'        => '16px',
				'--srfm-upload-preview-size'           => '48px',
				// Slider block variables.
				'--srfm-slider-label-font-size'        => '14px',
				'--srfm-slider-label-line-height'      => '20px',
				'--srfm-slider-label-top-padding'      => '10px',
				'--srfm-slider-error-gap'              => '8px',
				// Page break block variables.
				'--srfm-page-break-row-gap'            => '32px',
				'--srfm-rating-icon-size'              => '36px',
				'--srfm-rating-icon-gap'               => '8px',

				// Datepicker block variables.
				'--srfm-datepicker-dropdown-input-gap' => '6px',
			],
		];

		return array_replace_recursive( $sizes, $pro_block_sizes );
	}
	/**
	 * Add pro block's default error message values.
	 *
	 * @Hooked - srfm_default_dynamic_block_option
	 *
	 * @since 0.0.1
	 * @param array<mixed> $default_values the default values.
	 * @param array<mixed> $common_err_msg the common error message.
	 * @return array<mixed>
	 */
	public static function add_default_dynamic_pro_block_values( $default_values, $common_err_msg ) {

		$default_pro_values = [
			// Note: These password strength messages are prepared for the password block.
			// As of now, the password block is not registered in SureForms. Once registered, these messages should be used.
			// phpcs:ignore
			// 'srfm_password_block_required_text'    => $common_err_msg['required'],
			// phpcs:enable
			'srfm_rating_block_required_text'      => $common_err_msg['required'],
			'srfm_date_picker_block_required_text' => $common_err_msg['required'],
			'srfm_time_picker_block_required_text' => $common_err_msg['required'],
			'srfm_upload_block_required_text'      => $common_err_msg['required'],
			'srfm_slider_block_required_text'      => $common_err_msg['required'],
		];

		return array_merge( $default_values, $default_pro_values );
	}

	/**
	 * Add pro options in the default options to save.
	 *
	 * @Hooked - srfm_general_dynamic_options_to_save
	 *
	 * @since 0.0.1
	 * @param array<mixed> $default_options the default free options.
	 * @param array<mixed> $setting_options the setting options.
	 * @return array<mixed>
	 */
	public static function add_pro_options_to_save( $default_options, $setting_options ) {
		$pro_options_keys = [
			'srfm_rating_block_required_text',
			'srfm_date_picker_block_required_text',
			'srfm_time_picker_block_required_text',
			'srfm_upload_block_required_text',
			'srfm_slider_block_required_text',
			// Note: These password strength messages are prepared for the password block.
			// As of now, the password block is not registered in SureForms. Once registered, these messages should be used.
			// phpcs:ignore
			/*
			'srfm_password_block_required_text',
			'srfm_password_strength_weak',
			'srfm_password_strength_medium',
			'srfm_password_strength_strong',
			'srfm_password_strength_very_strong',
			*/
			// phpcs:enable
			'srfm_file_size_exceed',
			'srfm_file_type_not_allowed',
			'srfm_file_upload_limit',
		];

		$pro_options_to_save = [];

		foreach ( $pro_options_keys as $key ) {
			if ( isset( $setting_options[ $key ] ) ) {
				$pro_options_to_save[ $key ] = $setting_options[ $key ];
			}
		}

		return array_merge( $default_options, $pro_options_to_save );
	}

	/**
	 * Add custom styling css variables.
	 *
	 * @param array<string,string> $params array of values sent by action 'srfm_form_css_variables'.
	 * @since 1.6.3
	 * @return void
	 */
	public function add_premium_form_styling_variables( $params ) {
		if ( empty( $params['id'] ) ) {
			return;
		}

		$form_styling_starter     = Helper::get_array_value( get_post_meta( Helper::get_integer_value( $params['id'] ), '_srfm_forms_styling_starter', true ) );
		$srfm_premium_common      = Helper::get_array_value( get_post_meta( Helper::get_integer_value( $params['id'] ), '_srfm_premium_common', true ) );
		$is_welcome_screen        = $srfm_premium_common['is_welcome_screen'] ?? false;
		$form_theme               = Helper::get_string_value( $form_styling_starter['form_theme'] ) ?? 'default';
		$this->data['form_theme'] = $form_theme;

		$welcome_screen_vars = [
			// The welcome screen properties are stored in the _srfm_premium_common post meta.
			// Welcome Screen Properties.
			'--srfm-common-heading-font-size'   => $is_welcome_screen && isset( $srfm_premium_common['heading_text_size'] ) ? sanitize_text_field( "{$srfm_premium_common['heading_text_size']}{$srfm_premium_common['heading_text_size_unit']}" ) : '',
			'--srfm-common-heading-line-height' => $is_welcome_screen && isset( $srfm_premium_common['heading_line_height'] ) ? sanitize_text_field( "{$srfm_premium_common['heading_line_height']}{$srfm_premium_common['heading_line_height_unit']}" ) : '',
			'--srfm-common-message-font-size'   => $is_welcome_screen && isset( $srfm_premium_common['message_text_size'] ) ? sanitize_text_field( "{$srfm_premium_common['message_text_size']}{$srfm_premium_common['message_text_size_unit']}" ) : '',
			'--srfm-common-message-line-height' => $is_welcome_screen && isset( $srfm_premium_common['message_line_height'] ) ? sanitize_text_field( "{$srfm_premium_common['message_line_height']}{$srfm_premium_common['message_line_height_unit']}" ) : '',
		];
		// Return if the form theme is not set to custom.
		if ( 'custom' !== $form_theme ) {
			// We need to add the welcome screen variables even if the form theme is not custom and the welcome screen is enabled.
			if ( $is_welcome_screen ) {
				Pro_Helper::add_css_variables( $welcome_screen_vars );
			}
			return;
		}

		$error_color = Helper::get_string_value( $form_styling_starter['error_color'] ) ?? '#dc2626';
		$form        = [
			'row_gap'         => Helper::get_integer_value( $form_styling_starter['form_row_gap'] ) ?? 18,
			'row_gap_type'    => Helper::get_string_value( $form_styling_starter['form_row_gap_type'] ) ?? 'px',
			'column_gap'      => Helper::get_integer_value( $form_styling_starter['form_column_gap'] ) ?? 16,
			'column_gap_type' => Helper::get_string_value( $form_styling_starter['form_column_gap_type'] ) ?? 'px',
		];

		// Button Properties.
		$button            = [
			// Padding.
			'padding_top'          => isset( $form_styling_starter['button_padding_top'] ) && is_scalar( $form_styling_starter['button_padding_top'] ) ? floatval( $form_styling_starter['button_padding_top'] ) : 10,
			'padding_right'        => isset( $form_styling_starter['button_padding_right'] ) && is_scalar( $form_styling_starter['button_padding_right'] ) ? floatval( $form_styling_starter['button_padding_right'] ) : 14,
			'padding_bottom'       => isset( $form_styling_starter['button_padding_bottom'] ) && is_scalar( $form_styling_starter['button_padding_bottom'] ) ? floatval( $form_styling_starter['button_padding_bottom'] ) : 10,
			'padding_left'         => isset( $form_styling_starter['button_padding_left'] ) && is_scalar( $form_styling_starter['button_padding_left'] ) ? floatval( $form_styling_starter['button_padding_left'] ) : 14,
			'padding_unit'         => Helper::get_string_value( $form_styling_starter['button_padding_unit'] ) ?? 'px',
			// Border Style.
			'border_style'         => Helper::get_string_value( $form_styling_starter['button_border_style'] ) ?? 'solid',
			// Border Width.
			'border_width_top'     => isset( $form_styling_starter['button_border_width_top'] ) && is_scalar( $form_styling_starter['button_border_width_top'] ) ? floatval( $form_styling_starter['button_border_width_top'] ) : 1,
			'border_width_right'   => isset( $form_styling_starter['button_border_width_right'] ) && is_scalar( $form_styling_starter['button_border_width_right'] ) ? floatval( $form_styling_starter['button_border_width_right'] ) : 1,
			'border_width_bottom'  => isset( $form_styling_starter['button_border_width_bottom'] ) && is_scalar( $form_styling_starter['button_border_width_bottom'] ) ? floatval( $form_styling_starter['button_border_width_bottom'] ) : 1,
			'border_width_left'    => isset( $form_styling_starter['button_border_width_left'] ) && is_scalar( $form_styling_starter['button_border_width_left'] ) ? floatval( $form_styling_starter['button_border_width_left'] ) : 1,
			// Border Radius.
			'border_radius_top'    => isset( $form_styling_starter['button_border_radius_top'] ) && is_scalar( $form_styling_starter['button_border_radius_top'] ) ? floatval( $form_styling_starter['button_border_radius_top'] ) : 6,
			'border_radius_right'  => isset( $form_styling_starter['button_border_radius_right'] ) && is_scalar( $form_styling_starter['button_border_radius_right'] ) ? floatval( $form_styling_starter['button_border_radius_right'] ) : 6,
			'border_radius_bottom' => isset( $form_styling_starter['button_border_radius_bottom'] ) && is_scalar( $form_styling_starter['button_border_radius_bottom'] ) ? floatval( $form_styling_starter['button_border_radius_bottom'] ) : 6,
			'border_radius_left'   => isset( $form_styling_starter['button_border_radius_left'] ) && is_scalar( $form_styling_starter['button_border_radius_left'] ) ? floatval( $form_styling_starter['button_border_radius_left'] ) : 6,
			'border_radius_unit'   => Helper::get_string_value( $form_styling_starter['button_border_radius_unit'] ) ?? 'px',
			// Border Color.
			'border_color'         => Helper::get_string_value( $form_styling_starter['button_border_color_normal'] ) ?? '',
			'border_hover_color'   => Helper::get_string_value( $form_styling_starter['button_border_color_hover'] ) ?? '',
		];
		$border_properties = [];

		// Background Stylings.
		$is_advanced_gradient_normal = isset( $form_styling_starter['button_gradient_type_normal'] ) && 'advanced' === Helper::get_string_value( $form_styling_starter['button_gradient_type_normal'] );
		$btn_bg_normal               = [
			// Background Normal.
			'text_color'                     => isset( $form_styling_starter['button_text_color_normal'] ) ? Helper::get_string_value( $form_styling_starter['button_text_color_normal'] ) : '',
			'background_type'                => isset( $form_styling_starter['button_background_type_normal'] ) ? Helper::get_string_value( $form_styling_starter['button_background_type_normal'] ) : '',
			'background_color'               => isset( $form_styling_starter['button_background_color_normal'] ) ? Helper::get_string_value( $form_styling_starter['button_background_color_normal'] ) : '',
			'background_gradient'            => isset( $form_styling_starter['button_background_gradient_normal'] ) ? Helper::get_string_value( $form_styling_starter['button_background_gradient_normal'] ) : 'linear-gradient(90deg, #FFC9B2 0%, #C7CBFF 100%)',
			'background_gradient_type'       => $is_advanced_gradient_normal && isset( $form_styling_starter['button_background_gradient_type_normal'] ) ? Helper::get_string_value( $form_styling_starter['button_background_gradient_type_normal'] ) : '',
			'background_gradient_color_1'    => $is_advanced_gradient_normal && isset( $form_styling_starter['button_background_gradient_color_1_normal'] ) ? Helper::get_string_value( $form_styling_starter['button_background_gradient_color_1_normal'] ) : '',
			'background_gradient_color_2'    => $is_advanced_gradient_normal && isset( $form_styling_starter['button_background_gradient_color_2_normal'] ) ? Helper::get_string_value( $form_styling_starter['button_background_gradient_color_2_normal'] ) : '',
			'background_gradient_location_1' => $is_advanced_gradient_normal && isset( $form_styling_starter['button_background_gradient_location_1_normal'] ) ? Helper::get_integer_value( $form_styling_starter['button_background_gradient_location_1_normal'] ) : 0,
			'background_gradient_location_2' => $is_advanced_gradient_normal && isset( $form_styling_starter['button_background_gradient_location_2_normal'] ) ? Helper::get_integer_value( $form_styling_starter['button_background_gradient_location_2_normal'] ) : 100,
			'background_gradient_angle'      => $is_advanced_gradient_normal && isset( $form_styling_starter['button_background_gradient_angle_normal'] ) ? Helper::get_integer_value( $form_styling_starter['button_background_gradient_angle_normal'] ) : 90,
		];
		$bg_advanced_gradient_normal = $is_advanced_gradient_normal ? Helper::get_gradient_css( $btn_bg_normal['background_gradient_type'], $btn_bg_normal['background_gradient_color_1'], $btn_bg_normal['background_gradient_color_2'], $btn_bg_normal['background_gradient_location_1'], $btn_bg_normal['background_gradient_location_2'], $btn_bg_normal['background_gradient_angle'] ) : '';

		$is_advanced_gradient_hover = isset( $form_styling_starter['button_gradient_type_hover'] ) && 'advanced' === Helper::get_string_value( $form_styling_starter['button_gradient_type_hover'] );
		$btn_bg_hover               = [
			// Background Hover.
			'text_color'                     => isset( $form_styling_starter['button_text_color_hover'] ) ? Helper::get_string_value( $form_styling_starter['button_text_color_hover'] ) : '',
			'background_type'                => isset( $form_styling_starter['button_background_type_hover'] ) ? Helper::get_string_value( $form_styling_starter['button_background_type_hover'] ) : '',
			'background_color'               => isset( $form_styling_starter['button_background_color_hover'] ) ? Helper::get_string_value( $form_styling_starter['button_background_color_hover'] ) : '',
			'background_gradient'            => isset( $form_styling_starter['button_background_gradient_hover'] ) ? Helper::get_string_value( $form_styling_starter['button_background_gradient_hover'] ) : 'linear-gradient(90deg, #FFC9B2 0%, #C7CBFF 100%)',
			'background_gradient_type'       => $is_advanced_gradient_hover && isset( $form_styling_starter['button_background_gradient_type_hover'] ) ? Helper::get_string_value( $form_styling_starter['button_background_gradient_type_hover'] ) : '',
			'background_gradient_color_1'    => $is_advanced_gradient_hover && isset( $form_styling_starter['button_background_gradient_color_1_hover'] ) ? Helper::get_string_value( $form_styling_starter['button_background_gradient_color_1_hover'] ) : '',
			'background_gradient_color_2'    => $is_advanced_gradient_hover && isset( $form_styling_starter['button_background_gradient_color_2_hover'] ) ? Helper::get_string_value( $form_styling_starter['button_background_gradient_color_2_hover'] ) : '',
			'background_gradient_location_1' => $is_advanced_gradient_hover && isset( $form_styling_starter['button_background_gradient_location_1_hover'] ) ? Helper::get_integer_value( $form_styling_starter['button_background_gradient_location_1_hover'] ) : 0,
			'background_gradient_location_2' => $is_advanced_gradient_hover && isset( $form_styling_starter['button_background_gradient_location_2_hover'] ) ? Helper::get_integer_value( $form_styling_starter['button_background_gradient_location_2_hover'] ) : 100,
			'background_gradient_angle'      => $is_advanced_gradient_hover && isset( $form_styling_starter['button_background_gradient_angle_hover'] ) ? Helper::get_integer_value( $form_styling_starter['button_background_gradient_angle_hover'] ) : 90,
		];
		$bg_advanced_gradient_hover = $is_advanced_gradient_hover ? Helper::get_gradient_css( $btn_bg_hover['background_gradient_type'], $btn_bg_hover['background_gradient_color_1'], $btn_bg_hover['background_gradient_color_2'], $btn_bg_hover['background_gradient_location_1'], $btn_bg_hover['background_gradient_location_2'], $btn_bg_hover['background_gradient_angle'] ) : '';
		$btn_typography             = [
			'font_size'        => $form_styling_starter['button_text_size'] ?? 16,
			'line_height'      => $form_styling_starter['button_line_height'] ?? 1.5,
			'font_size_unit'   => isset( $form_styling_starter['button_text_size_unit'] ) ? Helper::get_string_value( $form_styling_starter['button_text_size_unit'] ) : 'px',
			'line_height_unit' => isset( $form_styling_starter['button_line_height_unit'] ) ? Helper::get_string_value( $form_styling_starter['button_line_height_unit'] ) : 'em',
		];

		$variables = [
			'--srfm-error-color'                       => sanitize_text_field( $error_color ),
			'--srfm-error-color-border'                => Pro_Helper::get_hsl_notation_from_hex( $error_color, 0.65 ),
			'--srfm-error-color-border-glow'           => Pro_Helper::get_hsl_notation_from_hex( $error_color, 0.15 ),
			'--srfm-row-gap-between-blocks'            => sanitize_text_field( "{$form['row_gap']}{$form['row_gap_type']}" ),
			'--srfm-column-gap-between-blocks'         => sanitize_text_field( "{$form['column_gap']}{$form['column_gap_type']}" ),
			// Button Properties.
			// Padding.
			'--srfm-button-padding-top'                => sanitize_text_field( "{$button['padding_top']}{$button['padding_unit']}" ),
			'--srfm-button-padding-right'              => sanitize_text_field( "{$button['padding_right']}{$button['padding_unit']}" ),
			'--srfm-button-padding-bottom'             => sanitize_text_field( "{$button['padding_bottom']}{$button['padding_unit']}" ),
			'--srfm-button-padding-left'               => sanitize_text_field( "{$button['padding_left']}{$button['padding_unit']}" ),
			// Border Style.
			'--srfm-button-border-style'               => sanitize_text_field( $button['border_style'] ),
			// Background Normal.
			'--srfm-button-text-color-normal'          => sanitize_text_field( $btn_bg_normal['text_color'] ),
			'--srfm-button-background-type-normal'     => sanitize_text_field( $btn_bg_normal['background_type'] ),
			'--srfm-button-background-color-normal'    => sanitize_text_field( $btn_bg_normal['background_color'] ),
			'--srfm-button-background-gradient-normal' => $is_advanced_gradient_normal ? $bg_advanced_gradient_normal : sanitize_text_field( $btn_bg_normal['background_gradient'] ),
			'--srfm-button-text-color-hover'           => sanitize_text_field( $btn_bg_hover['text_color'] ),
			'--srfm-button-background-type-hover'      => sanitize_text_field( $btn_bg_hover['background_type'] ),
			'--srfm-button-background-color-hover'     => sanitize_text_field( $btn_bg_hover['background_color'] ),
			'--srfm-button-background-gradient-hover'  => $is_advanced_gradient_hover ? $bg_advanced_gradient_hover : sanitize_text_field( $btn_bg_hover['background_gradient'] ),
			// Typography.
			'--srfm-btn-font-size'                     => sanitize_text_field( "{$btn_typography['font_size']}{$btn_typography['font_size_unit']}" ),
			'--srfm-btn-line-height'                   => sanitize_text_field( "{$btn_typography['line_height']}{$btn_typography['line_height_unit']}" ),
			// Label Size.
			'--srfm-label-font-size'                   => sanitize_text_field( "{$form_styling_starter['field_label_size']}{$form_styling_starter['field_label_size_unit']}" ),
			'--srfm-label-line-height'                 => sanitize_text_field( "{$form_styling_starter['field_label_line_height']}{$form_styling_starter['field_label_line_height_unit']}" ),
			// Help Text Size.
			'--srfm-description-font-size'             => sanitize_text_field( "{$form_styling_starter['field_help_text_size']}{$form_styling_starter['field_help_text_size_unit']}" ),
			'--srfm-description-line-height'           => sanitize_text_field( "{$form_styling_starter['field_help_text_line_height']}{$form_styling_starter['field_help_text_line_height_unit']}" ),
			// Label Color.
			'--srfm-color-input-label'                 => sanitize_text_field( $form_styling_starter['field_label_color'] ),
			// Help Text Color.
			'--srfm-color-input-description'           => sanitize_text_field( $form_styling_starter['field_help_text_color'] ),
			// Label Gap.
			'--srfm-input-label-gap'                   => sanitize_text_field( "{$form_styling_starter['field_label_gap']}{$form_styling_starter['field_label_gap_unit']}" ),
			// Field Background Color.
			'--srfm-color-input-background'            => sanitize_text_field( $form_styling_starter['field_bg_color'] ),
			'--srfm-color-input-background-hover'      => 'hsl( from ' . sanitize_text_field( $form_styling_starter['field_bg_color'] ) . ' h s l / 0.05 )',
			'--srfm-color-input-background-disabled'   => 'hsl( from ' . sanitize_text_field( $form_styling_starter['field_bg_color'] ) . ' h s l / 0.05 )',
			// Field Border Color.
			'--srfm-color-input-border'                => sanitize_text_field( $form_styling_starter['field_border_color'] ),
			'--srfm-color-input-border-disabled'       => 'hsl( from ' . sanitize_text_field( $form_styling_starter['field_border_color'] ) . ' h s l / 0.15 )',
			// Field Border Radius.
			'--srfm-field-border-radius-top'           => sanitize_text_field( "{$form_styling_starter['field_border_radius_top']}{$form_styling_starter['field_border_radius_unit']}" ),
			'--srfm-field-border-radius-right'         => sanitize_text_field( "{$form_styling_starter['field_border_radius_right']}{$form_styling_starter['field_border_radius_unit']}" ),
			'--srfm-field-border-radius-bottom'        => sanitize_text_field( "{$form_styling_starter['field_border_radius_bottom']}{$form_styling_starter['field_border_radius_unit']}" ),
			'--srfm-field-border-radius-left'          => sanitize_text_field( "{$form_styling_starter['field_border_radius_left']}{$form_styling_starter['field_border_radius_unit']}" ),
			// Field Border Width.
			'--srfm-field-border-width-top'            => sanitize_text_field( "{$form_styling_starter['field_border_width_top']}{$form_styling_starter['field_border_width_unit']}" ),
			'--srfm-field-border-width-right'          => sanitize_text_field( "{$form_styling_starter['field_border_width_right']}{$form_styling_starter['field_border_width_unit']}" ),
			'--srfm-field-border-width-bottom'         => sanitize_text_field( "{$form_styling_starter['field_border_width_bottom']}{$form_styling_starter['field_border_width_unit']}" ),
			'--srfm-field-border-width-left'           => sanitize_text_field( "{$form_styling_starter['field_border_width_left']}{$form_styling_starter['field_border_width_unit']}" ),
			// Field Box Shadow.
			'--srfm-field-box-shadow-normal'           => sanitize_text_field(
				trim(
					sprintf(
						'%s%s %s%s %s%s %s%s %s %s',
						$form_styling_starter['field_box_shadow_horizontal_offset_normal'] ?? 0,
						'px',
						$form_styling_starter['field_box_shadow_vertical_offset_normal'] ?? 0,
						'px',
						$form_styling_starter['field_box_shadow_blur_normal'] ?? 0,
						'px',
						$form_styling_starter['field_box_shadow_spread_normal'] ?? 0,
						'px',
						! empty( $form_styling_starter['field_box_shadow_color_normal'] ) ? Helper::get_string_value( $form_styling_starter['field_box_shadow_color_normal'] ) : 'var(--srfm-color-input-border-focus-glow)',
						isset( $form_styling_starter['field_box_shadow_position_normal'] ) && 'inset' === $form_styling_starter['field_box_shadow_position_normal'] ? 'inset' : ''
					)
				)
			),
			'--srfm-field-box-shadow-focus'            => sanitize_text_field(
				trim(
					sprintf(
						'%s%s %s%s %s%s %s%s %s %s',
						$form_styling_starter['field_box_shadow_horizontal_offset_focus'] ?? 0,
						'px',
						$form_styling_starter['field_box_shadow_vertical_offset_focus'] ?? 0,
						'px',
						$form_styling_starter['field_box_shadow_blur_focus'] ?? 0,
						'px',
						$form_styling_starter['field_box_shadow_spread_focus'] ?? 3,
						'px',
						! empty( $form_styling_starter['field_box_shadow_color_focus'] ) ? Helper::get_string_value( $form_styling_starter['field_box_shadow_color_focus'] ) : 'var(--srfm-color-input-border-focus-glow)',
						isset( $form_styling_starter['field_box_shadow_position_focus'] ) && 'inset' === $form_styling_starter['field_box_shadow_position_focus'] ? 'inset' : ''
					)
				)
			),
			// Page Break Button Properties.
			'--srfm-page-break-next-btn-color-normal'  => isset( $form_styling_starter['page_break_next_button_text_color_normal'] ) ? sanitize_text_field( $form_styling_starter['page_break_next_button_text_color_normal'] ) : '',
			'--srfm-page-break-next-btn-bg-normal'     => isset( $form_styling_starter['page_break_next_button_background_color_normal'] ) ? sanitize_text_field( $form_styling_starter['page_break_next_button_background_color_normal'] ) : '',
			'--srfm-page-break-next-btn-border-color-normal' => isset( $form_styling_starter['page_break_next_button_border_color_normal'] ) ? sanitize_text_field( $form_styling_starter['page_break_next_button_border_color_normal'] ) : '',
			'--srfm-page-break-back-btn-color-normal'  => isset( $form_styling_starter['page_break_back_button_text_color_normal'] ) ? sanitize_text_field( $form_styling_starter['page_break_back_button_text_color_normal'] ) : '',
			'--srfm-page-break-back-btn-bg-normal'     => isset( $form_styling_starter['page_break_back_button_background_color_normal'] ) ? sanitize_text_field( $form_styling_starter['page_break_back_button_background_color_normal'] ) : '',
			'--srfm-page-break-back-btn-border-color-normal' => isset( $form_styling_starter['page_break_back_button_border_color_normal'] ) ? sanitize_text_field( $form_styling_starter['page_break_back_button_border_color_normal'] ) : '',
			'--srfm-page-break-next-btn-color-hover'   => isset( $form_styling_starter['page_break_next_button_text_color_hover'] ) ? sanitize_text_field( $form_styling_starter['page_break_next_button_text_color_hover'] ) : '',
			'--srfm-page-break-next-btn-bg-hover'      => isset( $form_styling_starter['page_break_next_button_background_color_hover'] ) ? sanitize_text_field( $form_styling_starter['page_break_next_button_background_color_hover'] ) : '',
			'--srfm-page-break-next-btn-border-color-hover' => isset( $form_styling_starter['page_break_next_button_border_color_hover'] ) ? sanitize_text_field( $form_styling_starter['page_break_next_button_border_color_hover'] ) : '',
			'--srfm-page-break-back-btn-color-hover'   => isset( $form_styling_starter['page_break_back_button_text_color_hover'] ) ? sanitize_text_field( $form_styling_starter['page_break_back_button_text_color_hover'] ) : '',
			'--srfm-page-break-back-btn-bg-hover'      => isset( $form_styling_starter['page_break_back_button_background_color_hover'] ) ? sanitize_text_field( $form_styling_starter['page_break_back_button_background_color_hover'] ) : '',
			'--srfm-page-break-back-btn-border-color-hover' => isset( $form_styling_starter['page_break_back_button_border_color_hover'] ) ? sanitize_text_field( $form_styling_starter['page_break_back_button_border_color_hover'] ) : '',
		];

		if ( 'none' === $button['border_style'] ) {
			$border_properties = [
				// Border Radius.
				'--srfm-button-border-radius-top'    => sanitize_text_field( "{$button['border_radius_top']}{$button['border_radius_unit']}" ),
				'--srfm-button-border-radius-right'  => sanitize_text_field( "{$button['border_radius_right']}{$button['border_radius_unit']}" ),
				'--srfm-button-border-radius-bottom' => sanitize_text_field( "{$button['border_radius_bottom']}{$button['border_radius_unit']}" ),
				'--srfm-button-border-radius-left'   => sanitize_text_field( "{$button['border_radius_left']}{$button['border_radius_unit']}" ),
			];
		} elseif ( 'default' !== $button['border_style'] ) {
			$border_properties = [
				// Border Width.
				'--srfm-button-border-width-top'     => sanitize_text_field( "{$button['border_width_top']}px" ),
				'--srfm-button-border-width-right'   => sanitize_text_field( "{$button['border_width_right']}px" ),
				'--srfm-button-border-width-bottom'  => sanitize_text_field( "{$button['border_width_bottom']}px" ),
				'--srfm-button-border-width-left'    => sanitize_text_field( "{$button['border_width_left']}px" ),
				// Border Radius.
				'--srfm-button-border-radius-top'    => sanitize_text_field( "{$button['border_radius_top']}{$button['border_radius_unit']}" ),
				'--srfm-button-border-radius-right'  => sanitize_text_field( "{$button['border_radius_right']}{$button['border_radius_unit']}" ),
				'--srfm-button-border-radius-bottom' => sanitize_text_field( "{$button['border_radius_bottom']}{$button['border_radius_unit']}" ),
				'--srfm-button-border-radius-left'   => sanitize_text_field( "{$button['border_radius_left']}{$button['border_radius_unit']}" ),
				// Border Color.
				'--srfm-button-border-color'         => sanitize_text_field( $button['border_color'] ),
				'--srfm-button-border-hover-color'   => sanitize_text_field( $button['border_hover_color'] ),
			];
		}

		// Add the button normal and hover background type to the class variable.
		$this->data['normal'] = $btn_bg_normal['background_type'];
		$this->data['hover']  = $btn_bg_hover['background_type'];

		$variables = array_merge( $variables, $border_properties, $welcome_screen_vars );

		// Add the form styling variables to the form.
		Pro_Helper::add_css_variables( $variables );
	}

	/**
	 * Add classes to the button based on custom styling.
	 *
	 * @param array<string> $classes Array of classes.
	 * @param int           $form_id The form ID.
	 * @since 1.6.3
	 * @return array<string> Final array of classes.
	 */
	public function add_pro_btn_classes( $classes, $form_id = 0 ) {
		$form_styling_starter = [];
		if ( empty( $this->data ) ) {
			$form_styling_starter = Helper::get_array_value( get_post_meta( Helper::get_integer_value( $form_id ), '_srfm_forms_styling_starter', true ) );
		}
		$background_type_normal = ! empty( $this->data['normal'] ) ? Helper::get_string_value( $this->data['normal'] ) : ( isset( $form_styling_starter['button_background_type_normal'] ) ? Helper::get_string_value( $form_styling_starter['button_background_type_normal'] ) : '' );
		$background_type_hover  = ! empty( $this->data['hover'] ) ? Helper::get_string_value( $this->data['hover'] ) : ( isset( $form_styling_starter['button_background_type_hover'] ) ? Helper::get_string_value( $form_styling_starter['button_background_type_hover'] ) : '' );
		$pro_btn_classes        = [
			Pro_Helper::get_button_background_classes( $background_type_normal, $background_type_hover ),
		];

		return array_merge( $classes, $pro_btn_classes );
	}

	/**
	 * Add custom styling class to the form container.
	 *
	 * @param string $classes Existing classes from generate-form-markup.
	 * @param int    $form_id The form ID.
	 * @since 1.6.3
	 * @return string Final string of classes joined.
	 */
	public function add_custom_styling_class( $classes, $form_id = 0 ) {
		if ( empty( $form_id ) || ( isset( $this->data['form_theme'] ) && 'custom' !== $this->data['form_theme'] ) ) {
			return $classes;
		}
		$form_styling_starter = [];
		$form_theme           = '';
		if ( ! empty( $this->data ) ) {
			$form_theme = Helper::get_string_value( $this->data['form_theme'] ?? '' );
		} else {
			$form_styling_starter = Helper::get_array_value( get_post_meta( Helper::get_integer_value( $form_id ), '_srfm_forms_styling_starter', true ) );
			$form_theme           = Helper::get_string_value( $form_styling_starter['form_theme'] ) ?? 'default';
		}

		// Return if the form theme is not set to custom.
		if ( 'custom' !== $form_theme ) {
			return $classes;
		}

		$custom_styling_class = 'srfm-custom-stylings';

		return Helper::join_strings( [ $classes, $custom_styling_class ] );
	}

	/**
	 * Add upload files to form data.
	 *
	 * @param array<mixed> $form_data Form data.
	 * @since 1.8.0
	 * @return array<mixed>
	 */
	public function add_upload_files_to_form_data( $form_data ) {
		if ( ! isset( $form_data['form-id'] ) ) {
			return [ 'error' => __( 'Form ID is missing', 'sureforms-pro' ) ];
		}

		// Check if the form data is empty or not an array.
		$is_error = false;

		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && ! empty( $_FILES ) ) {
			$allowed_file_types = Pro_Helper::get_normalized_file_types();

			// Flatten for MIME validation.
			$all_mimes = ! empty( $allowed_file_types ) ? array_merge( ...array_values( $allowed_file_types ) ) : [];

			if ( empty( $all_mimes ) ) {
				return [ 'error' => __( 'File types are not allowed', 'sureforms-pro' ) ];
			}

			// Get the allowed file size.
			add_filter( 'upload_dir', [ $this, 'change_upload_dir' ] );

			foreach ( $_FILES as $field => $file ) {
				// Ensure that the field type key is valid for the expected key structure.
				// This prevents uploading invalid upload data.
				// Example: $field = 'srfm-upload-abc123-lbl-upload' will return 'upload' as field type.
				$field_type = Pro_Helper::get_field_type_from_key_with_srfm( $field );
				if ( 'srfm-upload' !== $field_type ) {
					continue;
				}

				if ( is_array( $file['name'] ) ) {
					foreach ( $file['name'] as $key => $filename ) {
						// Check the file name is empty or not.
						if ( empty( $filename ) || ! is_string( $filename ) ) {
							continue;
						}

						// Get the file name.
						$temp_path  = $file['tmp_name'][ $key ];
						$file_size  = $file['size'][ $key ];
						$file_type  = $file['type'][ $key ];
						$file_error = $file['error'][ $key ];

						// Check if the file type is allowed by WP.
						if ( ! isset( $file_type ) || ! is_string( $file_type ) || ! in_array( $file_type, $all_mimes, true ) ) {
							$is_error = true;
							continue;
						}

						if ( ! $temp_path && ! $file_size && ! $file_type ) {
							$form_data[ $field ][] = '';
							continue;
						}

						$uploaded_file = [
							'name'     => sanitize_file_name( $filename ),
							'type'     => $file_type,
							'tmp_name' => $temp_path,
							'error'    => $file_error,
							'size'     => $file_size,
						];

						$upload_overrides = [
							'test_form' => false,
						];

						// wp handled upload internally handle the file type checking and file content checking.
						$move_file = wp_handle_upload( $uploaded_file, $upload_overrides );

						remove_filter( 'upload_dir', [ $this, 'change_upload_dir' ] );

						if ( $move_file && ! isset( $move_file['error'] ) ) {
							$form_data[ $field ][] = $move_file['url'];
						} else {
							$is_error = true;
						}
					}
				} else {
					$form_data[ $field ][] = '';
				}
			}
		}

		return $is_error ? [ 'error' => __( 'File is not uploaded', 'sureforms-pro' ) ] : $form_data;
	}

	/**
	 * Change the upload directory
	 *
	 * @param array<mixed> $dirs upload directory.
	 * @since 1.8.0
	 * @return array<mixed>
	 */
	public function change_upload_dir( $dirs ) {
		$dirs['subdir'] = '/sureforms';
		$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
		$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
		return $dirs;
	}

	/**
	 * Delete entry files before deleting the entry.
	 *
	 * @param array<mixed> $entry Entry data.
	 * @since 1.8.0
	 * @return void
	 */
	public function delete_entry_files( $entry ) {
		if ( empty( $entry ) ) {
			return;
		}

		// Check if the form data is empty or not an array.
		if ( empty( $entry['form_data'] ) || ! is_array( $entry['form_data'] ) ) {
			return;
		}

		foreach ( $entry['form_data'] as $field_name => $value ) {
			if ( false === strpos( $field_name, 'srfm-upload' ) && ! is_array( $value ) ) {
				continue;
			}

			foreach ( $value as $file_url ) {
				if ( empty( $file_url ) ) {
					continue;
				}

				// Delete the file from the uploads directory.
				Pro_Helper::delete_upload_file_from_subdir( $file_url, 'sureforms/' );
			}
		}
	}

	/**
	 * Validate form data for entry update.
	 *
	 * @param mixed $value   The form data to validate.
	 * @since 2.0.0
	 * @return bool|\WP_Error
	 */
	public function validate_entry_form_data( $value ) {
		if ( ! is_array( $value ) ) {
			return new \WP_Error( 'invalid_form_data', __( 'Form data must be an array.', 'sureforms-pro' ), [ 'status' => 400 ] );
		}

		return true;
	}

	/**
	 * Update entry data via REST API.
	 *
	 * @param \WP_REST_Request $request Full details about the request.
	 * @since 2.0.0
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function update_entry_data( $request ) {
		$nonce = $request->get_header( 'X-WP-Nonce' );

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error( 'nonce_verification_failed', __( 'Nonce verification failed.', 'sureforms-pro' ), [ 'status' => 403 ] );
		}

		$entry_id  = Helper::get_integer_value( $request->get_param( 'id' ) );
		$form_data = Helper::get_array_value( $request->get_param( 'form_data' ) );

		// Verify entry exists.
		$entry = Entries::get( $entry_id );
		if ( empty( $entry ) ) {
			return new \WP_Error( 'entry_not_found', __( 'Entry not found.', 'sureforms-pro' ), [ 'status' => 404 ] );
		}

		// Sanitize the form data using the same method as the original function.
		$data = Helper::sanitize_by_field_type( $form_data );

		$instance = Entries::get_instance();

		/* translators: Here %s means the users display name. */
		$instance->add_log( sprintf( __( 'Entry edited by %s', 'sureforms-pro' ), wp_get_current_user()->display_name ) );

		$changed        = 0;
		$edited         = [];
		$change_details = [];

		if ( ! empty( $data ) && is_array( $data ) ) {
			$saved_form_data = Helper::get_array_value( $entry['form_data'] );

			// Get the form data and merge it with the submitted data.
			$_data = array_merge( $saved_form_data, $data );

			foreach ( $_data as $field_name => $v ) {
				if ( ! array_key_exists( $field_name, $saved_form_data ) && ( false === strpos( $field_name, '-lbl-' ) ) ) {
					continue;
				}

				/**
				 * Action fired just before updating individual entry field data.
				 *
				 * This action hook allows developers to perform custom operations before a form field
				 * value is updated in the entry logs. Common use cases include:
				 * - Updating entry logs with field-specific change tracking
				 * - Processing complex field types like repeaters or file uploads
				 * - Adding custom validation or data transformation
				 * - Integrating with external systems
				 *
				 * @param array $args {
				 *     Arguments passed to the action hook.
				 *     @type string     $field_name      The name/key of the field being updated
				 *     @type mixed      $field_value     The new value being saved for this field
				 *     @type array      $saved_form_data The existing form data before update
				 *     @type array      $current_data    The complete new form data being saved
				 *     @type object     $entry_instance  Instance of the Entries class
				 *     @type int        $changed         Reference to counter tracking number of changes
				 * }
				 * @since 1.11.0 - Migrated this from the older code added in version 1.11.0 from the entries-management.php file.
				 */
				do_action(
					'srfm_pro_before_update_entry_data',
					[
						'field_name'      => $field_name,
						'field_value'     => $v,
						'saved_form_data' => $saved_form_data,
						'current_data'    => $_data,
						'entry_instance'  => $instance,
						'changed'         => &$changed,
					]
				);

				// If the field is an array, encode the values. This is to add support for multi-upload field.
				if ( is_array( $v ) ) {
					if ( false !== strpos( $field_name, 'srfm-upload' ) ) {
						$edited[ $field_name ] = array_map(
							static function ( $val ) {
								return rawurlencode( esc_url_raw( $val ) );
							},
							$v
						);
						// Skip the rest of the loop if we are handling uploads field.
						continue;
					}

					if ( false !== strpos( $field_name, 'srfm-repeater' ) ) {
						// If the field is a repeater field, then we need to process the repeater field.
						$edited[ $field_name ] = $v;
						continue;
					}

					// Compare submitted array and previously saved array.
					// Retrieve the previously saved value for the field from the database.
					$previous_saved_value = Helper::get_string_value( $saved_form_data[ $field_name ] );

					// Determine the separator used in the saved value for backward compatibility.
					// If the string contains " | ", split it using " | "; otherwise, split it using ",".
					$prev_value = strpos( $previous_saved_value, '|' ) !== false
						? explode( ' | ', $previous_saved_value )
						: explode( ',', $previous_saved_value );

					$current_value = $v;

					// Sort both arrays to compare them.
					sort( $prev_value );
					sort( $current_value );

					// Convert both arrays to string to compare them.
					$prev_value    = implode( ' | ', $prev_value );
					$current_value = implode( ' | ', $current_value );

					if ( md5( $current_value ) === md5( $prev_value ) ) {
						// If both arrays are same then skip the rest of the loop.
						$edited[ $field_name ] = $prev_value;
						continue;
					}
					$edited[ $field_name ] = implode( ' | ', $v );

				} else {
					$edited[ $field_name ] = htmlspecialchars( $v );
				}

				if ( false !== strpos( $field_name, 'srfm-checkbox' ) && empty( $v ) && ! isset( $saved_form_data[ $field_name ] ) ) {
					unset( $edited[ $field_name ] );
					continue;
				}

				$log = is_array( $v ) ? implode( ' | ', $v ) : $v;

				if ( ! isset( $saved_form_data[ $field_name ] ) ) {
					// &#8594; is html entity for arrow -> sign.
					$instance->update_log( $instance->get_last_log_key(), null, [ '<strong>' . Helper::get_field_label_from_key( $field_name ) . ': </strong> "" &#8594; ' . $log ] );
					$changed++;
					$change_details[] = [
						'field'     => $field_name,
						'label'     => Helper::get_field_label_from_key( $field_name ),
						'old_value' => '',
						'new_value' => $log,
						'type'      => 'added',
					];
					continue;
				}

				if ( $saved_form_data[ $field_name ] === $edited[ $field_name ] ) {
					continue;
				}

				$form_data_log = Helper::get_string_value( $saved_form_data[ $field_name ] );

				// &#8594; is html entity for arrow -> sign. Use HTML template instead of inline HTML string.
				ob_start(); ?>
				<strong><?php echo esc_html( Helper::get_field_label_from_key( $field_name ) . ': ' ); ?></strong> <del><?php echo esc_html( $form_data_log ); ?></del> &#8594; <?php echo esc_html( $log ); ?>
				<?php
				$srfm_formatted_log = ob_get_clean();
				$srfm_formatted_log = is_string( $srfm_formatted_log ) ? $srfm_formatted_log : '';
				$instance->update_log( $instance->get_last_log_key(), null, [ $srfm_formatted_log ] );
				$changed++;
				$change_details[] = [
					'field'     => $field_name,
					'label'     => Helper::get_field_label_from_key( $field_name ),
					'old_value' => $form_data_log,
					'new_value' => $log,
					'type'      => 'modified',
				];
			}
		}

		if ( ! $changed ) {
			// Reset logs to zero if no valid changes are made.
			$instance->reset_logs();
		}

		$update_result = $instance::update(
			$entry_id,
			[
				'form_data' => $edited,
				'logs'      => $instance->get_logs(),
			]
		);

		if ( 0 === $update_result ) {
			return new \WP_REST_Response(
				[
					'success' => true,
					'message' => __( 'No changes detected - entry remains the same.', 'sureforms-pro' ),
				],
				200
			);
		}

		if ( ! $update_result ) {
			return new \WP_Error( 'update_failed', __( 'Failed to update entry data.', 'sureforms-pro' ), [ 'status' => 500 ] );
		}

		// Return success response with details.
		return new \WP_REST_Response(
			[
				'success' => true,
				'message' => __( 'Entry updated successfully.', 'sureforms-pro' ),
				'data'    => [
					'entry_id'      => $entry_id,
					'changes_count' => $changed,
					'changes'       => $change_details,
					'updated_at'    => current_time( 'mysql' ),
					'updated_by'    => wp_get_current_user()->display_name,
				],
			],
			200
		);
	}

	/**
	 * Change the field name for the date picker field.
	 *
	 * @param string $field_name Field name.
	 * @param string $base_field_name Base field name.
	 * @param string $block_type Block type.
	 * @param string $block_id Block ID.
	 *
	 * @since 2.0.0
	 * @return string Field name.
	 */
	public function change_field_name( $field_name, $base_field_name, $block_type, $block_id ) {
		if ( 'date-picker' === $block_type ) {
			$field_name = 'srfm-datepicker-' . $block_id . $base_field_name;
		}

		return $field_name;
	}
}
