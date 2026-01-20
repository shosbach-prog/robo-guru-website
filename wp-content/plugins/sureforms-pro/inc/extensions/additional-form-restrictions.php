<?php
/**
 * Advanced Form Restriction - Init Class
 *
 * @since 2.2.0
 * @package sureforms-pro
 */

namespace SRFM_Pro\Inc\Extensions;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Additional Form Restrictions Class
 *
 * Provides advanced form restriction functionality including IP address,
 * country-based, and keyword-based restrictions for SureForms.
 *
 * @since 2.2.0
 * @package sureforms-pro
 */
class Additional_Form_Restrictions {
	use Get_Instance;

	/**
	 * Tracks the type of restriction that was triggered
	 *
	 * @since 2.2.0
	 * @var string|null 'ip', 'country', 'keyword', or null if none
	 */
	private ?string $triggered_restriction_type = null;

	/**
	 * Tracks the form ID for which the restriction was triggered
	 *
	 * @since 2.2.0
	 * @var int|null WordPress form post ID or null if none
	 */
	private ?int $restricted_form_id = null;

	/**
	 * Constructor - Initialize advanced form restriction functionality
	 *
	 * Hooks into WordPress action to register custom post meta for storing
	 * advanced form restriction data (IP, Country, Keyword restrictions)
	 *
	 * @since 2.2.0
	 */
	public function __construct() {
		add_action( 'srfm_register_additional_post_meta', [ $this, 'register_additional_form_restriction_meta' ] );
		add_filter( 'srfm_is_form_restricted', [ $this, 'apply_advanced_restrictions' ], 10, 2 );
		add_filter( 'srfm_form_restriction_message', [ $this, 'customize_restriction_message' ], 10, 3 );
		add_filter( 'srfm_additional_restriction_check', [ $this, 'apply_keyword_restriction' ], 10, 3 );
		add_filter( 'srfm_additional_restriction_message', [ $this, 'customize_restriction_message' ], 10, 3 );
		add_filter( 'protected_title_format', [ $this, 'customize_sureforms_protected_title' ], 10, 2 );
		add_filter( 'the_password_form', [ $this, 'customize_sureforms_password_form' ], 10, 2 );
	}

	/**
	 * Register WordPress post meta for advanced form restrictions
	 *
	 * Registers `_srfm_additional_form_restriction` meta key to store restriction settings
	 * as JSON data. Includes default structure for IP, Country, and Keyword restrictions
	 * with proper sanitization and authorization callbacks.
	 *
	 * @hooked srfm_register_additional_post_meta - WordPress action
	 * @since 2.2.0
	 * @return void
	 */
	public function register_additional_form_restriction_meta() {
		register_post_meta(
			SRFM_FORMS_POST_TYPE, // Post type to register meta for.
			'_srfm_additional_form_restriction', // Meta key name.
			[
				'type'              => 'string', // Store as JSON string for complex data.
				'single'            => true, // Only one value per post.
				'show_in_rest'      => [ // Make available via REST API for Gutenberg editor.
					'schema' => [
						'type'    => 'string', // REST API data type.
						'context' => [ 'edit' ], // Available only in edit context.
					],
				],
				'sanitize_callback' => [ $this, 'sanitize_additional_form_restriction_meta' ], // Security: sanitize data before saving.
				'auth_callback'     => static function () {
					// Security: check if current user can edit SureForms.
					return Helper::current_user_can();
				},
				'default'           => wp_json_encode(
					[
						'ip'       => [
							'status'  => false, // Whether IP restriction is active.
							'mode'    => 'block', // Block or allow mode.
							'ips'     => '', // Comma-separated IP addresses.
							'message' => __( "We're sorry, this form isn't accessible from your IP address.", 'sureforms-pro' ),
						],
						'country'  => [
							'status'    => false, // Whether country restriction is active.
							'mode'      => 'block', // Block or allow mode.
							'countries' => '', // Comma-separated country names.
							'message'   => __( "We're sorry, this form isn't available in your region right now.", 'sureforms-pro' ),
						],
						'keyword'  => [
							'status'   => false, // Whether keyword restriction is active.
							'keywords' => '', // Comma-separated keywords to check.
							'message'  => __( 'Your submission contains restricted keywords.', 'sureforms-pro' ),
						],
						'login'    => [
							'status'  => false, // Whether login restriction is active.
							'message' => __( 'This form is only available to logged-in users. Please sign in to proceed.', 'sureforms-pro' ),
						],
						'password' => [
							'status'      => false, // Whether password protection UI is active.
							'description' => __( 'This form is password-protected. To view it, please enter the password below.', 'sureforms-pro' ),
							'message'     => __( 'Incorrect password. Please try again!', 'sureforms-pro' ),
						],
					]
				),
			]
		);
	}

	/**
	 * Sanitize and validate advanced form restriction meta data
	 *
	 * Ensures all restriction data is properly sanitized before saving to database.
	 * Validates data types, allowed values, and provides fallbacks for missing data.
	 *
	 * @since 2.2.0
	 * @param string $meta_value Raw meta value received from frontend (JSON string).
	 * @return string Sanitized and validated JSON string ready for database storage.
	 */
	public function sanitize_additional_form_restriction_meta( $meta_value ) {
		// Ensure we have a string to decode, handle null/empty gracefully.
		if ( ! is_string( $meta_value ) || empty( $meta_value ) ) {
			$result = wp_json_encode( [] );
			return false !== $result ? $result : '{}';
		}

		// Decode JSON string to PHP array for processing.
		$data = json_decode( $meta_value, true );

		// Return empty JSON if invalid data received.
		if ( ! is_array( $data ) ) {
			$result = wp_json_encode( [] );
			return false !== $result ? $result : '{}';
		}

		// Sanitize IP restriction data with type validation and fallbacks.
		if ( isset( $data['ip'] ) && is_array( $data['ip'] ) ) {
			$data['ip']['status']  = isset( $data['ip']['status'] ) ? (bool) $data['ip']['status'] : false; // Ensure boolean.
			$data['ip']['mode']    = isset( $data['ip']['mode'] ) && in_array( $data['ip']['mode'], [ 'block', 'allow' ], true ) ? $data['ip']['mode'] : 'block'; // Validate allowed modes.
			$data['ip']['ips']     = isset( $data['ip']['ips'] ) && is_string( $data['ip']['ips'] ) ? sanitize_text_field( $data['ip']['ips'] ) : ''; // Clean IP list string.
			$data['ip']['message'] = isset( $data['ip']['message'] ) && is_string( $data['ip']['message'] ) ? sanitize_text_field( $data['ip']['message'] ) : ''; // Clean error message.
		}

		// Sanitize Country restriction data with type validation and fallbacks.
		if ( isset( $data['country'] ) && is_array( $data['country'] ) ) {
			$data['country']['status']    = isset( $data['country']['status'] ) ? (bool) $data['country']['status'] : false; // Ensure boolean.
			$data['country']['mode']      = isset( $data['country']['mode'] ) && in_array( $data['country']['mode'], [ 'block', 'allow' ], true ) ? $data['country']['mode'] : 'block'; // Validate allowed modes.
			$data['country']['countries'] = isset( $data['country']['countries'] ) && is_string( $data['country']['countries'] ) ? sanitize_text_field( $data['country']['countries'] ) : ''; // Clean country list string.
			$data['country']['message']   = isset( $data['country']['message'] ) && is_string( $data['country']['message'] ) ? sanitize_text_field( $data['country']['message'] ) : ''; // Clean error message.
		}

		// Sanitize Keyword restriction data with type validation and fallbacks.
		if ( isset( $data['keyword'] ) && is_array( $data['keyword'] ) ) {
			$data['keyword']['status']   = isset( $data['keyword']['status'] ) ? (bool) $data['keyword']['status'] : false; // Ensure boolean.
			$data['keyword']['keywords'] = isset( $data['keyword']['keywords'] ) && is_string( $data['keyword']['keywords'] ) ? sanitize_text_field( $data['keyword']['keywords'] ) : ''; // Clean keyword list string.
			$data['keyword']['message']  = isset( $data['keyword']['message'] ) && is_string( $data['keyword']['message'] ) ? sanitize_text_field( $data['keyword']['message'] ) : ''; // Clean error message.
		}

		// Sanitize Login restriction data with type validation and fallbacks.
		if ( isset( $data['login'] ) && is_array( $data['login'] ) ) {
			$data['login']['status']  = isset( $data['login']['status'] ) ? (bool) $data['login']['status'] : false; // Ensure boolean.
			$data['login']['message'] = isset( $data['login']['message'] ) && is_string( $data['login']['message'] ) ? sanitize_text_field( $data['login']['message'] ) : ''; // Clean error message.
		}

		// Sanitize Password restriction data with type validation and fallbacks.
		if ( isset( $data['password'] ) && is_array( $data['password'] ) ) {
			$data['password']['status']      = isset( $data['password']['status'] ) ? (bool) $data['password']['status'] : false; // Ensure boolean.
			$data['password']['description'] = isset( $data['password']['description'] ) && is_string( $data['password']['description'] ) ? sanitize_text_field( $data['password']['description'] ) : ''; // Clean description string.
			$data['password']['message']     = isset( $data['password']['message'] ) && is_string( $data['password']['message'] ) ? sanitize_text_field( $data['password']['message'] ) : ''; // Clean error message.
		}

		// Return sanitized data as JSON string for database storage.
		$result = wp_json_encode( $data );
		return false !== $result ? $result : '{}';
	}

	/**
	 * Apply advanced form restrictions (IP, Country, Keyword) to form submission check.
	 * Hooks into the core SureForms restriction filter to add our custom restrictions.
	 *
	 * @hooked srfm_is_form_restricted - WordPress filter
	 * @since 2.2.0
	 * @param bool $is_restricted Current restriction status from core checks.
	 * @param int  $form_id WordPress form post ID.
	 * @return bool True if form should be restricted, false if allowed
	 */
	public function apply_advanced_restrictions( $is_restricted, $form_id ) {
		// If already restricted by core checks, maintain that restriction.
		if ( $is_restricted ) {
			return true;
		}

		// Get our advanced restriction settings for this form.
		$advanced_restrictions = $this->get_advanced_restriction_settings( $form_id );

		// If no advanced restrictions configured, allow form submission.
		if ( empty( $advanced_restrictions ) ) {
			return false;
		}

		// Reset restriction tracking.
		$this->reset_restriction_tracking();
		$is_restricted = false;

		// Check each restriction type for both 'block' and 'allow' modes.

		// Check IP restriction.
		if ( ! empty( $advanced_restrictions['ip']['status'] ) ) {
			$ip_mode       = $advanced_restrictions['ip']['mode'] ?? 'block';
			$ip_restricted = $this->is_ip_restricted( $advanced_restrictions, $ip_mode );

			if ( $ip_restricted ) {
				$this->triggered_restriction_type = 'ip';
				$this->restricted_form_id         = $form_id;
				$is_restricted                    = true;
			}
		}

		// Check Country restriction.
		if ( ! $is_restricted && ! empty( $advanced_restrictions['country']['status'] ) ) {
			$country_mode       = $advanced_restrictions['country']['mode'] ?? 'block';
			$country_restricted = $this->is_country_restricted( $advanced_restrictions, $country_mode );

			if ( $country_restricted ) {
				$this->triggered_restriction_type = 'country';
				$this->restricted_form_id         = $form_id;
				$is_restricted                    = true;
			}
		}

		// Check Login restriction.
		if ( ! $is_restricted && ! empty( $advanced_restrictions['login']['status'] ) ) {
			$login_restricted = $this->is_login_restricted();

			if ( $login_restricted ) {
				$this->triggered_restriction_type = 'login';
				$this->restricted_form_id         = $form_id;
				$is_restricted                    = true;
			}
		}

		// if conversational form and is_restricted is true, then srfm-cf-progress-bar-ctn add inline style display none.
		$conversational_form            = get_post_meta( $form_id, '_srfm_conversational_form', true );
		$is_conversational_form_enabled = is_array( $conversational_form ) && isset( $conversational_form['is_cf_enabled'] ) ? $conversational_form['is_cf_enabled'] : false;
		if ( $is_restricted && $is_conversational_form_enabled ) {
			add_filter( 'srfm_show_conversational_form_footer', '__return_false' );
		}

		return $is_restricted;
	}

	/**
	 * Customize the form restriction message for advanced restrictions.
	 * Hooks into the core SureForms message filter to show appropriate messages.
	 * Considers both restriction type and mode (block/allow) for contextual messaging.
	 *
	 * @hooked srfm_form_restriction_message - WordPress filter
	 * @since 2.2.0
	 * @param string $message Default restriction message from core SureForms.
	 * @param int    $form_id WordPress form post ID.
	 * @param array  $form_restriction Core restriction settings - unused.
	 * @return string Customized message if advanced restriction triggered, otherwise original message
	 */
	public function customize_restriction_message( $message, $form_id, $form_restriction = [] ) {
		// Suppress unused parameter warning.
		unset( $form_restriction );

		// Only customize message if our advanced restriction was triggered for this form.
		if ( empty( $this->triggered_restriction_type ) || $this->restricted_form_id !== $form_id ) {
			return $message; // Return original message.
		}

		// Get our advanced restriction settings.
		$advanced_restrictions = $this->get_advanced_restriction_settings( $form_id );

		// If no advanced restrictions found, return original message.
		if ( empty( $advanced_restrictions ) ) {
			return $message;
		}

		// Get custom message based on restriction type and mode.
		return $this->get_contextual_restriction_message( $advanced_restrictions );
	}

	/**
	 * Apply keyword restrictions to form submission.
	 *
	 * @since 2.2.0
	 * @param bool  $is_restricted Current restriction status.
	 * @param int   $form_id WordPress form post ID.
	 * @param array $form_data Form submission data.
	 * @return bool True if form should be restricted, false if allowed.
	 */
	public function apply_keyword_restriction( bool $is_restricted, int $form_id, array $form_data = [] ): bool {
		// If already restricted by core checks, maintain that restriction.
		if ( $is_restricted ) {
			return true;
		}

		// Get our advanced restriction settings for this form.
		$advanced_restrictions = $this->get_advanced_restriction_settings( $form_id );

		// If no advanced restrictions configured, allow form submission.
		if ( empty( $advanced_restrictions ) ) {
			return false;
		}

		// Reset restriction tracking.
		$this->reset_restriction_tracking();
		$is_restricted = false;

		// Check Keyword restriction (always uses 'block' mode).
		if ( ! empty( $advanced_restrictions['keyword']['status'] ) ) {
			$keyword_restricted = $this->is_keyword_restricted( $advanced_restrictions, $form_data );

			if ( $keyword_restricted ) {
				$this->triggered_restriction_type = 'keyword';
				$this->restricted_form_id         = $form_id;
				$is_restricted                    = true;
			}
		}

		return $is_restricted;
	}

	/**
	 * Customize the protected title format for SureForms forms only.
	 * Removes the default "Protected: " prefix from the title.
	 *
	 * @hooked protected_title_format - WordPress filter
	 * @since 2.3.0
	 * @param string   $title The protected title format.
	 * @param \WP_Post $post The post object.
	 * @return string. Modified title format without "Protected: " prefix for SureForms forms.
	 */
	public function customize_sureforms_protected_title( $title, $post ) {
		if ( SRFM_FORMS_POST_TYPE === $post->post_type ) {
			return '%s';
		}
		return $title;
	}

	/**
	 * Customize WordPress password form for SureForms forms only.
	 * Applies SureForms styling to match the form design system.
	 *
	 * @hooked the_password_form - WordPress filter
	 * @since 2.3.0
	 * @param string   $output Default password form HTML.
	 * @param \WP_Post $post The post object.
	 * @return string  Custom password form HTML with SureForms styling
	 */
	public function customize_sureforms_password_form( $output, $post ) {
		// Only customize for SureForms forms.
		if ( SRFM_FORMS_POST_TYPE !== $post->post_type ) {
			return $output;
		}

		// Get form ID.
		$form_id = $post->ID;

		// if conversational form and is_restricted is true, then srfm-cf-progress-bar-ctn add inline style display none.
		$conversational_form            = get_post_meta( $form_id, '_srfm_conversational_form', true );
		$is_conversational_form_enabled = is_array( $conversational_form ) && isset( $conversational_form['is_cf_enabled'] ) ? $conversational_form['is_cf_enabled'] : false;
		if ( $is_conversational_form_enabled ) {
			add_filter( 'srfm_show_conversational_form_footer', '__return_false' );
		}

		// Generate unique instance ID to prevent duplicate IDs when form appears multiple times.
		$unique_id = 'pwbox-' . ( empty( $post->ID ) ? wp_rand() : $post->ID );

		// Get custom password error message from form meta.
		$advanced_restrictions  = $this->get_advanced_restriction_settings( $form_id );
		$password_description   = ! empty( $advanced_restrictions['password']['description'] ) ? $advanced_restrictions['password']['description'] : '';
		$password_error_message = ! empty( $advanced_restrictions['password']['message'] )
		? $advanced_restrictions['password']['message']
		: __( 'Incorrect password. Please try again.', 'sureforms-pro' );

		// get required form settings for button styling.
		$post_type    = get_post_type( $form_id );
		$form_styling = get_post_meta( $form_id, '_srfm_forms_styling', true );
		$form_styling = ! empty( $form_styling ) && is_array( $form_styling ) ? $form_styling : [];
		// Submit button.
		$submit_button_alignment = is_array( $form_styling ) && ! empty( $form_styling['submit_button_alignment'] ) ? $form_styling['submit_button_alignment'] : 'left';

		if ( is_rtl() && ( 'left' === $submit_button_alignment || 'right' === $submit_button_alignment ) ) {
			$submit_button_alignment = 'right' === $submit_button_alignment ? 'left' : 'right';
		}

		$btn_from_theme = Helper::get_meta_value( $form_id, '_srfm_inherit_theme_button' );
		$full           = 'justify' === $submit_button_alignment ? true : false;

		$srfm_button_classes = apply_filters( 'srfm_add_button_classes', [ '1' === $btn_from_theme ? 'wp-block-button__link' : 'srfm-btn-frontend srfm-button srfm-submit-button' ] );

		// Build the custom password form with SureForms styling.
		ob_start();
		?>
		<form action="<?php echo esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ); ?>" method="post" id="srfm-form-<?php echo esc_attr( $unique_id ); ?>" class="srfm-password-protected-form <?php echo esc_attr( SRFM_FORMS_POST_TYPE === $post_type ? 'srfm-single-form ' : '' ); ?>" form-id="<?php echo esc_attr( Helper::get_string_value( $form_id ) ); ?>">
			<?php
			// If the referrer is the same as the current request, the user has entered an invalid password.
			// current post id to make it work with embedded forms.
			$current_post_id = Helper::get_integer_value( get_the_ID() );
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- COOKIEHASH is a WordPress core constant
			if ( ! empty( $post->ID ) && wp_get_raw_referer() === get_permalink( $current_post_id ) && defined( 'COOKIEHASH' ) && isset( $_COOKIE[ 'wp-postpass_' . COOKIEHASH ] ) ) {
				$icon             = Helper::fetch_svg( 'info_circle', '', 'aria-hidden="true"' );
				$allowed_svg_tags = \SRFM\Inc\Helper::$allowed_tags_svg;
				$classes          = 'srfm-common-error-message srfm-error-message srfm-head-error';
				?>
						<p id="srfm-error-message" class="<?php echo esc_attr( $classes ); ?>"><?php echo wp_kses( $icon, $allowed_svg_tags ); ?><span class="srfm-error-content"><?php echo esc_html( $password_error_message ); ?></span></p>
					<?php
			}
			?>

			<?php if ( ! empty( $password_description ) ) { ?>
				<p style="margin: 0;"><?php echo esc_html( $password_description ); ?></p>
			<?php } ?>

			<div class="srfm-block srfm-block-single srfm-block-width-100">
				<label for="<?php echo esc_attr( $unique_id ); ?>" class="srfm-block-label">
					<?php esc_html_e( 'Password', 'sureforms-pro' ); ?>
					<span class="srfm-required" aria-hidden="true"> *</span>
				</label>
				<div class="srfm-block-wrap srfm-with-icon">
					<input class="srfm-input-common" type="password" name="post_password" id="<?php echo esc_attr( $unique_id ); ?>" placeholder="<?php esc_attr_e( 'Enter password', 'sureforms-pro' ); ?>" required aria-required="true" />
				</div>
			</div>

			<div class="srfm-submit-container">
				<div style="width: <?php echo esc_attr( $full ? '100%' : 'auto' ); ?>; text-align: <?php echo esc_attr( $submit_button_alignment ); ?>" class="wp-block-button">
					<button style="<?php echo esc_attr( $full ? 'width: 100%;' : 'width: auto;' ); ?>" id="srfm-submit-btn-<?php echo esc_attr( $unique_id ); ?>" class="<?php echo esc_attr( implode( ' ', array_filter( $srfm_button_classes ) ) ); ?>"
					>
						<div class="srfm-submit-wrap">
							<?php echo esc_html__( 'Enter', 'sureforms-pro' ); ?>
						<div class="srfm-loader"></div>
						</div>
					</button>
				</div>
			</div>

			<input type="hidden" name="post_ID" value="<?php echo esc_attr( (string) $form_id ); ?>" />
		</form>
		<?php
		$output = ob_get_clean();
		return false !== $output ? $output : '';
	}

	/**
	 * Get advanced restriction settings for a specific form.
	 * Retrieves and decodes the JSON data from post meta.
	 *
	 * @since 2.2.0
	 * @param int $form_id WordPress form post ID.
	 * @return array Decoded restriction settings or empty array if not found
	 */
	private function get_advanced_restriction_settings( $form_id ) {
		// Validate form ID.
		if ( empty( $form_id ) || ! is_int( $form_id ) ) {
			return [];
		}

		// Get raw meta data from WordPress.
		$meta_value = get_post_meta( $form_id, '_srfm_additional_form_restriction', true );

		// Return empty if no data found.
		if ( empty( $meta_value ) || ! is_string( $meta_value ) ) {
			return [];
		}

		// Decode JSON to PHP array.
		$decoded = json_decode( $meta_value, true );

		// Ensure we have valid array data.
		return is_array( $decoded ) ? $decoded : [];
	}

	/**
	 * Reset restriction tracking variables.
	 *
	 * @since 2.2.0
	 * @return void
	 */
	private function reset_restriction_tracking() {
		$this->triggered_restriction_type = null;
		$this->restricted_form_id         = null;
	}

	/**
	 * Check if current user's IP address should be restricted based on the mode.
	 * Handles both 'block' and 'allow' modes with different logic.
	 *
	 * @since 2.2.0
	 * @param array  $restrictions Advanced restriction settings array.
	 * @param string $mode Restriction mode: 'block' or 'allow'.
	 * @return bool True if IP should be restricted (blocked), false if allowed
	 */
	private function is_ip_restricted( $restrictions, $mode = 'block' ) {
		// Get IP list from configuration.
		$ip_list = trim( $restrictions['ip']['ips'] ?? '' );

		// If no IPs configured, handle based on mode.
		if ( empty( $ip_list ) ) {
			return $this->handle_empty_config( $mode );
		}

		// Get user's current IP address.
		$user_ip = $this->get_user_ip();

		// If can't determine user IP, handle based on mode (fail safe).
		if ( empty( $user_ip ) ) {
			// Block mode: can't identify IP, so allow access (fail open).
			// Allow mode: can't identify IP, so block access (fail secure).
			return 'allow' === $mode;
		}

		// Parse comma-separated IP list.
		$configured_ips = array_map( 'trim', explode( ',', $ip_list ) );
		$configured_ips = array_filter( $configured_ips ); // Remove empty values.

		// Check if user IP is in the configured list.
		$ip_found = in_array( $user_ip, $configured_ips, true );

		// Apply restriction logic based on mode.
		if ( 'allow' === $mode ) {
			// Allow mode: restrict if IP is NOT in the allow list (whitelist).
			return ! $ip_found;
		}
		// Block mode: restrict if IP is in the block list (blacklist).
		return $ip_found;
	}

	/**
	 * Check if current user's country should be restricted based on the mode.
	 * Handles both 'block' and 'allow' modes with different logic.
	 *
	 * @since 2.2.0
	 * @param array  $restrictions Advanced restriction settings array.
	 * @param string $mode Restriction mode: 'block' or 'allow'.
	 * @return bool True if country should be restricted (blocked), false if allowed
	 */
	private function is_country_restricted( $restrictions, $mode = 'block' ) {
		// Get configured countries from restrictions.
		$configured_countries = $restrictions['country']['countries'] ?? '';

		// If no countries configured, handle based on mode.
		if ( empty( $configured_countries ) ) {
			return $this->handle_empty_config( $mode );
		}

		// Get country from external API directly (skip headers).
		$user_ip      = $this->get_user_ip();
		$user_country = null;

		if ( ! empty( $user_ip ) ) {
			$user_country_code = $this->get_country_from_api( $user_ip );
			if ( $user_country_code ) {
				$user_country = [
					'code' => $user_country_code,
					'name' => $this->get_country_name_from_code( $user_country_code ),
				];
			}
		}

		// If we can't determine the country, handle based on mode (fail safe).
		if ( empty( $user_country ) ) {
			// Block mode: can't identify country, so allow access (fail open).
			// Allow mode: can't identify country, so block access (fail secure).
			return 'allow' === $mode;
		}

		// Parse configured countries from comma-separated string.
		$country_list = array_map( 'trim', explode( ',', $configured_countries ) );
		$country_list = array_filter( $country_list ); // Remove empty values.

		// Check if user's country is in the configured list.
		// This checks both country names and country codes for flexibility.
		$country_found = false;
		foreach ( $country_list as $restricted_country ) {
			if ( strcasecmp( $user_country['name'], $restricted_country ) === 0 ||
				strcasecmp( $user_country['code'], $restricted_country ) === 0 ) {
				$country_found = true;
				break;
			}
		}

		// Apply restriction logic based on mode.
		if ( 'allow' === $mode ) {
			// Allow mode: restrict if country is NOT in the allow list (whitelist).
			return ! $country_found;
		}
		// Block mode: restrict if country is in the block list (blacklist).
		return $country_found;
	}

	/**
	 * Check if current user's login status should be restricted.
	 * Login restrictions require users to be logged in to submit the form.
	 *
	 * @since 2.2.0
	 * @return bool True if login should be restricted (user not logged in), false if allowed.
	 */
	private function is_login_restricted() {
		// Return true if user is not logged in (restrict access).
		return ! is_user_logged_in();
	}

	/**
	 * Check if empty configuration should be restricted based on mode.
	 *
	 * @since 2.2.0
	 * @param string $mode Restriction mode: 'block' or 'allow'.
	 * @return bool True if should be restricted
	 */
	private function handle_empty_config( $mode ) {
		// Block mode: no items to block, so allow access.
		// Allow mode: no items to allow, so block access (whitelist is empty).
		return 'allow' === $mode;
	}

	/**
	 * Get the user's IP address from various possible sources.
	 * Handles proxy headers and server variables safely.
	 *
	 * @since 2.2.0
	 * @return string User's IP address or empty string if not found
	 */
	private function get_user_ip() {
		// List of possible IP header sources (in order of preference).
		$ip_headers = [
			'HTTP_CF_CONNECTING_IP', // Cloudflare.
			'HTTP_X_REAL_IP', // Nginx proxy.
			'HTTP_X_FORWARDED_FOR', // Standard proxy header.
			'HTTP_CLIENT_IP', // Proxy header.
			'REMOTE_ADDR', // Direct connection.
		];

		// Check each possible source.
		foreach ( $ip_headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );

				// Handle comma-separated IPs (take first one).
				if ( strpos( $ip, ',' ) !== false ) {
					$ip = trim( explode( ',', $ip )[0] );
				}

				// Validate IP format.
				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
					return $ip;
				}
			}
		}

		// Fallback to direct connection IP (may include local IPs).
		if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}

		// No valid IP found.
		return '';
	}

	/**
	 * Convert country code to country name using the complete countries.json data.
	 * Loads data from SureForms countries.json file for comprehensive coverage.
	 *
	 * @since 2.2.0
	 * @param string $country_code Two-letter country code (ISO 3166-1 alpha-2).
	 * @return string Country name or the original code if not found
	 */
	private function get_country_name_from_code( $country_code ) {
		if ( empty( $country_code ) || strlen( $country_code ) !== 2 ) {
			return $country_code;
		}

		$country_code   = strtoupper( $country_code );
		$countries_data = $this->get_countries_data();

		return $countries_data[ $country_code ] ?? $country_code;
	}

	/**
	 * Load and cache countries data from the JSON file.
	 * Uses caching to avoid repeated file reads for performance.
	 *
	 * @since 2.2.0
	 * @return array Array of country data with code => name mappings
	 */
	private function get_countries_data() {
		// Check if data is already cached in memory.
		static $countries_cache = null;

		if ( null !== $countries_cache ) {
			return $countries_cache;
		}

		// Check WordPress transient cache (24 hours).
		$cache_key        = 'srfm_countries_data';
		$cached_countries = get_transient( $cache_key );

		if ( false !== $cached_countries && is_array( $cached_countries ) ) {
			$countries_cache = $cached_countries;
			return $countries_cache;
		}

		// Load from JSON file.
		$countries_file = defined( 'SRFM_DIR' ) ? SRFM_DIR . 'inc/fields/countries.json' : '';

		if ( empty( $countries_file ) || ! file_exists( $countries_file ) ) {
			// Fallback to empty array if file doesn't exist.
			$countries_cache = [];
			return $countries_cache;
		}

		// Read and decode JSON file.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$json_content = file_get_contents( $countries_file );
		if ( false === $json_content ) {
			$countries_cache = [];
			return $countries_cache;
		}

		$countries_array = json_decode( $json_content, true );

		if ( ! is_array( $countries_array ) ) {
			// Fallback to empty array if JSON is invalid.
			$countries_cache = [];
			return $countries_cache;
		}

		// Convert array to code => name mapping for faster lookups.
		$countries_mapping = [];
		foreach ( $countries_array as $country ) {
			if ( isset( $country['code'], $country['name'] ) ) {
				$countries_mapping[ $country['code'] ] = $country['name'];
			}
		}

		// Cache the result.
		$countries_cache = $countries_mapping;
		set_transient( $cache_key, $countries_mapping, DAY_IN_SECONDS );

		return $countries_cache;
	}

	/**
	 * Get contextual restriction message based on type and mode.
	 * Provides different messages for 'block' vs 'allow' modes to give users clear context.
	 *
	 * @since 2.2.0
	 * @param array $advanced_restrictions Advanced restriction settings array.
	 * @return string Custom message or empty string if none found
	 */
	private function get_contextual_restriction_message( $advanced_restrictions ) {
		if ( empty( $this->triggered_restriction_type ) ) {
			return '';
		}

		// Get the restriction configuration for the triggered type.
		$restriction_config = $advanced_restrictions[ $this->triggered_restriction_type ] ?? [];

		if ( empty( $restriction_config ) ) {
			return '';
		}

		// Get custom message if configured.
		$custom_message = $restriction_config['message'] ?? '';

		// If custom message exists, use it (it should already account for the mode context).
		return sanitize_text_field( $custom_message );
	}

	/**
	 * Check if form submission contains restricted keywords.
	 * Keywords always use 'block' mode - if keywords are found, submission is blocked.
	 *
	 * @since 2.2.0
	 * @param array $restrictions Advanced restriction settings array.
	 * @param array $form_data Form submission data.
	 * @return bool True if keywords should be restricted (blocked), false if allowed
	 */
	private function is_keyword_restricted( $restrictions, $form_data = [] ) {
		// Get keyword list from configuration.
		$keyword_list = trim( $restrictions['keyword']['keywords'] ?? '' );

		// If no keywords configured, allow access.
		if ( empty( $keyword_list ) ) {
			return false;
		}

		// If no form data provided, cannot check keywords - allow access.
		if ( empty( $form_data ) || ! is_array( $form_data ) ) {
			return false;
		}

		// Parse comma-separated keyword list and clean it.
		$keywords = array_map( 'trim', explode( ',', $keyword_list ) );
		$keywords = array_filter(
			$keywords,
			static function( $keyword ) {
				return ! empty( $keyword );
			}
		);

		// If no valid keywords after cleaning, allow access.
		if ( empty( $keywords ) ) {
			return false;
		}

		// Convert form data to searchable text content.
		$form_text_content = $this->extract_text_from_form_data( $form_data );

		// If no text content found, allow access (fail safe).
		if ( empty( $form_text_content ) ) {
			return false;
		}

		// Check for exact keyword matches in form content (case-insensitive).
		// Block mode only: restrict if keywords are found (blacklist).
		return $this->contains_restricted_keywords( $form_text_content, $keywords );
	}

	/**
	 * Check if text contains any restricted keywords.
	 * Uses precise word boundary matching to avoid blocking partial words.
	 * E.g., blocking "bat" should NOT block "batman" or "combat".
	 *
	 * @since 2.2.0
	 * @param string $value The text content to check.
	 * @param array  $provided_keywords Array of keywords to search for.
	 * @return bool True if restricted keywords found, false otherwise
	 */
	private function contains_restricted_keywords( $value, $provided_keywords ) {
		foreach ( $provided_keywords as $keyword ) {
			$keyword = trim( $keyword );
			if ( empty( $keyword ) ) {
				continue;
			}

			// Escape the keyword for regex safety.
			$escaped_keyword = preg_quote( $keyword, '/' );

			// Use negative lookbehind and lookahead to ensure the keyword is not part of a larger word.
			// (?<!\w) = not preceded by word character (letter/number/underscore).
			// (?!\w) = not followed by word character.
			// This ensures "bat" matches "bat." but NOT "batman" or "combat".
			$pattern = '/(?<!\w)' . $escaped_keyword . '(?!\w)/ui';

			// Check if the keyword appears as a standalone word (case-insensitive, Unicode-aware).
			if ( preg_match( $pattern, $value ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Extract text content from form data for keyword checking.
	 * Recursively searches through form data array to extract all text values.
	 *
	 * @since 2.2.0
	 * @param array $form_data The form submission data.
	 * @return string Combined text content from all form fields
	 */
	private function extract_text_from_form_data( $form_data ) {
		if ( ! is_array( $form_data ) ) {
			return '';
		}

		$text_content = [];

		// Recursively extract text from nested arrays.
		foreach ( $form_data as $key => $value ) {
			// Skip certain meta keys that don't contain user content.
			if ( is_string( $key ) && (
				strpos( $key, '_' ) === 0 || // Skip private/meta fields.
				in_array( $key, [ 'form_id', 'action', 'nonce', 'submit' ], true )
			) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				// Recursively process arrays (for multi-select, checkboxes, etc.).
				$nested_text = $this->extract_text_from_form_data( $value );
				if ( ! empty( $nested_text ) ) {
					$text_content[] = $nested_text;
				}
			} elseif ( is_string( $value ) ) {
				// Add string values directly.
				$cleaned_value = trim( $value );
				if ( ! empty( $cleaned_value ) ) {
					$text_content[] = $cleaned_value;
				}
			} elseif ( is_numeric( $value ) ) {
				// Convert numeric values to strings.
				$text_content[] = (string) $value;
			}
		}

		// Join all text content with spaces for keyword searching.
		return implode( ' ', $text_content );
	}

	/**
	 * Get country code from external geolocation API.
	 * Uses a reliable third-party geolocation service for accurate country detection.
	 *
	 * @since 2.2.0
	 * @param string $ip User's IP address.
	 * @return string|null Two-letter country code or null if detection fails
	 */
	private function get_country_from_api( $ip ) {
		// Validate IP address.
		if ( empty( $ip ) || ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
			return null;
		}

		// Check cache first to avoid repeated API calls.
		$cache_key      = 'srfm_country_' . md5( $ip );
		$cached_country = get_transient( $cache_key );
		if ( false !== $cached_country ) {
			return is_string( $cached_country ) && ! empty( $cached_country ) ? $cached_country : null;
		}

		// Use external geolocation API for reliable country detection.
		$request = wp_remote_get(
			"https://apip.cc/api-json/{$ip}",
			[
				'timeout' => 5,
				'headers' => [
					'User-Agent' => 'SureForms/1.0 (WordPress/' . get_bloginfo( 'version' ) . ')',
				],
			]
		);

		$country_code = null;

		if ( ! is_wp_error( $request ) && 200 === wp_remote_retrieve_response_code( $request ) ) {
			$body = wp_remote_retrieve_body( $request );
			if ( ! empty( $body ) ) {
				$data = json_decode( $body, true );
				// Check for success status and CountryCode field.
				if ( is_array( $data ) &&
					isset( $data['status'] ) && 'success' === $data['status'] &&
					isset( $data['CountryCode'] ) ) {
					$country_code = strtoupper( trim( $data['CountryCode'] ) );
					// Validate country code format.
					if ( strlen( $country_code ) === 2 && ctype_alpha( $country_code ) && 'XX' !== $country_code ) {
						// Cache successful result for 1 hour.
						set_transient( $cache_key, $country_code, HOUR_IN_SECONDS );
						return $country_code;
					}
				}
			}
		}

		// Cache failed result for 10 minutes to avoid repeated API calls.
		set_transient( $cache_key, '', 10 * MINUTE_IN_SECONDS );
		return null;
	}

}
