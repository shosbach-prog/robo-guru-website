<?php
/**
 * Activity HTML Generator - Regular Version.
 *
 * This class generates HTML for shared activities using regular BuddyBoss classes.
 * Uses 'bb-' prefix for CSS classes.
 *
 * @package BuddyBoss\Sharing\Modules\Generators
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Modules\Generators;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activity_HTML_Generator class.
 *
 * Regular version - uses 'bb-' prefix for classes.
 *
 * @since 1.0.0
 */
class Activity_HTML_Generator extends Activity_HTML_Generator_Base {

	/**
	 * Get the CSS class prefix for this generator.
	 *
	 * Regular BuddyBoss uses 'bb-' prefix.
	 *
	 * @since 1.0.0
	 * @return string CSS class prefix.
	 */
	protected function get_class_prefix() {
		return 'bb-';
	}

	/**
	 * Get the CSS class prefix for specific elements.
	 *
	 * @since 1.0.0
	 * @return string CSS class prefix for elements.
	 */
	protected function get_element_class_prefix() {
		return '';
	}
}
