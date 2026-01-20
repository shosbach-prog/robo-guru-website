<?php
/**
 * Entries Pro class to encapsulate the database operations for the entries pro features.
 *
 * @since 1.3.0
 * @package sureforms-pro
 */

namespace SRFM_Pro\Inc\Databases;

use SRFM\Inc\Database\Tables\Entries;
use SRFM\Inc\Helper;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Entries Pro class to encapsulate the database operations for the entries pro features.
 *
 * @since 1.3.0
 */
class Entries_Pro extends Entries {
	/**
	 * Add a note to an existing entry in the database.
	 *
	 * @param int    $entry_id The ID of the entry to which the note will be added. Must be a valid, non-empty integer.
	 * @param string $note The content of the note to be added. It will be trimmed before saving.
	 * @since 1.3.0
	 * @return bool True on success, false if the entry ID is invalid or the note is empty.
	 */
	public static function add_note( $entry_id, $note = '' ) {
		if ( empty( $entry_id ) ) {
			return false;
		}

		$note = trim( $note );

		if ( empty( $note ) ) {
			return false;
		}

		// Format notes structure.
		$_note = [
			'submitted_by' => get_current_user_id(),
			'timestamp'    => current_time( 'mysql' ), //phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp -- Using current_time() to match the WordPress timezone.
			'note'         => $note,
		];

		// Merge with old notes and save it to database and return boolean result.
		return false !== self::update(
			$entry_id,
			[
				'notes' => array_merge( [ $_note ], Helper::get_array_value( self::get( $entry_id )['notes'] ) ),
			]
		);
	}
}
