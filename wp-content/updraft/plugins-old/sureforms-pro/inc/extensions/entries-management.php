<?php
/**
 * Main class to load the entries management related functionalities.
 *
 * @since 1.3.0
 * @package sureforms-pro
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM\Inc\Database\Tables\Entries;
use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Helper as Pro_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Entries Management Class.
 *
 * @since 1.3.0
 */
class Entries_Management {
	use Get_Instance;

	/**
	 * Entries Management constructor.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		// Action hooks.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_script' ], 15 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_style' ], 5 );

		// AJAX hooks.
		add_action( 'wp_ajax_sureforms_pro_entry_delete_file', [ $this, 'entry_delete_file' ] );
	}

	/**
	 * Enqueue entries management script.
	 *
	 * @param string $hook_prefix The current admin page.
	 * @since 1.3.0
	 * @return void
	 */
	public function enqueue_script( $hook_prefix ) {
		if ( 'sureforms_page_sureforms_entries' !== $hook_prefix ) {
			return;
		}
		$dir_name  = 'dist';
		$file_name = 'entry';
		$js_uri    = SRFM_PRO_URL . $dir_name . '/' . $file_name . '.js';

		$script_asset_path = SRFM_PRO_DIR . 'dist/' . $file_name . '.asset.php';
		$script_info       = file_exists( $script_asset_path )
			? include $script_asset_path
			: [
				'dependencies' => [],
				'version'      => SRFM_PRO_VER,
			];
		wp_enqueue_script( SRFM_PRO_SLUG . '-entries', $js_uri, $script_info['dependencies'], $script_info['version'], true );

		wp_localize_script(
			SRFM_PRO_SLUG . '-entries',
			'srfm_pro_entries',
			[
				'nonce' => wp_create_nonce( '_srfm_entry_delete_file' ),
			]
		);

		Pro_Helper::register_script_translations( SRFM_PRO_SLUG . '-entries' );
	}

	/**
	 * Enqueue entries management style.
	 *
	 * @param string $hook_prefix The current admin page.
	 * @since 1.3.0
	 * @return void
	 */
	public function enqueue_style( $hook_prefix ) {
		if ( 'sureforms_page_sureforms_entries' !== $hook_prefix ) {
			return;
		}
		$dir_name  = 'dist/';
		$file_name = 'entry';
		$css_uri   = SRFM_PRO_URL . $dir_name . '/' . $file_name . '.css';

		wp_enqueue_style( SRFM_PRO_SLUG . '-entries', $css_uri, [], SRFM_PRO_VER );
	}

	/**
	 * Deletes a file based on the provided file URL via an AJAX request.
	 *
	 * This method handles the deletion of a file by verifying the AJAX nonce,
	 * checking if the file URL is provided, converting the file URL to a file path,
	 * and then attempting to delete the file from the server.
	 *
	 * @since 1.3.0
	 * @return void
	 */
	public function entry_delete_file() {
		if ( ! check_ajax_referer( '_srfm_entry_delete_file', 'security' ) ) {
			wp_send_json_error( [ 'message' => $this->get_error_msg( 'nonce' ) ] );
		}

		if ( empty( $_POST['file'] ) ) {
			wp_send_json_error( [ 'message' => $this->get_error_msg( 'invalid' ) ] );
		}

		$entry_id  = $this->validate_entry_request();
		$file_path = Helper::convert_fileurl_to_filepath( urldecode( esc_url_raw( wp_unslash( $_POST['file'] ) ) ) );

		if ( ! file_exists( $file_path ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'File not found.', 'sureforms-pro' ) ] );
		}

		$remove_file = Pro_Helper::delete_upload_file_from_subdir( $file_path, 'sureforms/' );

		if ( ! $remove_file ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Failed to delete file.', 'sureforms-pro' ) ] );
		}

		$instance = Entries::get_instance();
		/* translators: %1$s is the file basename and %2$s is the user display name. */
		$instance->add_log( sprintf( esc_html__( 'File "%1$s" deleted by %2$s', 'sureforms-pro' ), esc_html( basename( $file_path ) ), esc_html( wp_get_current_user()->display_name ) ) );
		$instance::update(
			$entry_id,
			[
				'logs' => $instance->get_logs(),
			]
		);
		wp_send_json_success();
	}

	/**
	 * Filters the email notifications to only include those that are enabled.
	 *
	 * @param array<int|string,array<string,bool|int|string>> $email_notifications The email notifications to filter.
	 * @since 1.7.1
	 * @return array<int|string,array<string,bool|int|string>>
	 */
	public static function get_enabled_email_notifications( $email_notifications ) {

		foreach ( $email_notifications as $key => $email_notification ) {
			if ( ! isset( $email_notification['status'] ) || true !== $email_notification['status'] ) {
				unset( $email_notifications[ $key ] );
			}
		}

		return $email_notifications;
	}

	/**
	 * Helper method to paginate the provided array data.
	 *
	 * @param array<mixed> $array Array item to paginate.
	 * @param int          $current_page Current page number.
	 * @param int          $items_per_page Total items to return per pagination.
	 * @since 1.3.0
	 * @return array<mixed>
	 */
	public static function paginate_array( $array, $current_page, $items_per_page = 3 ) {
		$total_items = count( $array );
		$total_pages = Helper::get_integer_value( ceil( $total_items / $items_per_page ) );

		// Ensure current page is within bounds.
		$current_page = max( 1, min( $total_pages, $current_page ) );

		// Calculate the offset for slicing.
		$offset = ( $current_page - 1 ) * $items_per_page;

		// Get the items for the current page.
		$items = array_slice( $array, $offset, $items_per_page, true );

		// Determine the next and previous page numbers.
		$next_page = $current_page < $total_pages ? $current_page + 1 : false;
		$prev_page = $current_page > 1 ? $current_page - 1 : false;

		return compact(
			'items',
			'offset',
			'next_page',
			'prev_page',
			'total_items',
			'total_pages',
			'current_page',
		);
	}

	/**
	 * Prepares the form blocks for entry editing mode.
	 *
	 * @param \WP_Post|null $post The post object.
	 * @param array<mixed>  $entry The entry data.
	 * @since 1.3.0
	 * @return array
	 */
	public static function prepare_editing_blocks( $post, $entry ) {
		if ( ! $post || ! is_object( $post ) ) {
			return [];
		}

		$parsed_blocks = parse_blocks( $post->post_content );

		if ( ! is_array( $parsed_blocks ) ) {
			return [];
		}

		if ( ! empty( $parsed_blocks ) && is_array( $parsed_blocks ) ) {
			foreach ( $parsed_blocks as &$parsed_block ) {
				self::prepare_editing_blocks_attrs( $parsed_block, $entry );
			}
		}

		return $parsed_blocks;
	}

	/**
	 * Returns the error message based on the error type.
	 *
	 * @param string $type The error type.
	 * @since 1.3.0
	 * @return string
	 */
	protected function get_error_msg( $type ) {
		$messages = [
			'permission' => __( 'You do not have permission to perform this action.', 'sureforms-pro' ),
			'invalid'    => __( 'Invalid request.', 'sureforms-pro' ),
			'nonce'      => __( 'Nonce verification failed.', 'sureforms-pro' ),
		];

		return $messages[ $type ] ?? '';
	}

	/**
	 * Helper method to validate the entry request.
	 *
	 * This method checks if the user has permission to manage options and
	 * if the entry ID is provided in the POST request. It then returns the
	 * sanitized entry ID.
	 *
	 * @since 1.3.0
	 * @return int
	 */
	protected function validate_entry_request() {
		if ( ! Helper::current_user_can() ) {
			wp_send_json_error( [ 'message' => $this->get_error_msg( 'permission' ) ] );
		}

		if ( empty( $_POST['entryID'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- We are checking nonce in the necessary methods.
			wp_send_json_error( [ 'message' => $this->get_error_msg( 'invalid' ) ] );
		}

		return absint( wp_unslash( $_POST['entryID'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- We are checking nonce in the necessary methods.
	}

	/**
	 * Prepares the form blocks for entry editing mode.
	 *
	 * @param array<array<mixed>> $block The block data.
	 * @param array<mixed>        $entry The entry data.
	 * @since 1.3.0
	 * @return void
	 */
	protected static function prepare_editing_blocks_attrs( &$block, $entry ) {
		if ( ! $block['blockName'] ) {
			return;
		}

		if ( empty( $block['attrs']['slug'] ) ) {
			// If we don't have slug then this is invalid block for editing purpose.
			$block['blockName'] = null;
			return;
		}

		$block['attrs']['entryID']   = absint( $entry['ID'] );
		$block['attrs']['isEditing'] = true;

		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as &$inner_block ) {
				self::prepare_editing_blocks_attrs( $inner_block, $entry );
			}
			return;
		}

		if ( ! empty( $entry['form_data'] ) && is_array( $entry['form_data'] ) ) {
			foreach ( $entry['form_data'] as $field_name => $value ) {
				if ( false !== strpos( $field_name, "-{$block['attrs']['block_id']}-" ) ) {
					$block['attrs']['fieldName']    = $field_name;
					$block['attrs']['defaultValue'] = $value;
					break;
				}
			}
		}
	}
}
