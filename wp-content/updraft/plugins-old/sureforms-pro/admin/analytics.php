<?php
/**
 * Analytics class helps to connect BSF Analytics.
 *
 * @package sureforms.
 */

namespace SRFM_Pro\Admin;

use SRFM_Pro\Inc\Pro\Database\Tables\Integrations;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Analytics class.
 *
 * @since 1.4.0
 */
class Analytics {
	use Get_Instance;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 1.4.0
	 */
	public function __construct() {
		add_filter( 'bsf_core_stats', [ $this, 'add_srfm_pro_analytics_data' ], 11 );
		add_filter( 'srfm_deactivation_survey_data', [ $this, 'add_pro_deactivation_data' ] );
	}

	/**
	 * Adds pro analytics data to existing stats data
	 *
	 * @param array $stats_data existing stats data.
	 * @since 1.4.0
	 * @return array
	 */
	public function add_srfm_pro_analytics_data( $stats_data ) {
		if ( ! empty( $stats_data['plugin_data']['sureforms'] ) && is_array( $stats_data['plugin_data']['sureforms'] ) ) {
			$stats_data['plugin_data']['sureforms'] = array_merge_recursive( $stats_data['plugin_data']['sureforms'], $this->pro_analytics_data() );
		}

		return $stats_data;
	}

	/**
	 * Returns pro analytics data
	 *
	 * @since 1.4.0
	 * @return array
	 */
	public function pro_analytics_data() {
		$pro_data                = [];
		$pro_data['pro_version'] = SRFM_PRO_VER;
		$pro_data['numeric_values']['conditional_logic_forms']     = $this->conditional_logic_forms();
		$pro_data['numeric_values']['multi_step_forms']            = $this->multi_step_forms();
		$pro_data['numeric_values']['custom_apps_enabled']         = $this->custom_apps_enabled();
		$pro_data['numeric_values']['conversational_forms']        = $this->conversational_forms();
		$pro_data['numeric_values']['calculator_forms']            = $this->get_calculator_forms();
		$pro_data['numeric_values']['signature_block']             = $this->get_forms_count_with_signature_block();
		$pro_data['numeric_values']['ai_generated_calculator']     = $this->ai_generated_forms_by_type( 'calculator' );
		$pro_data['numeric_values']['ai_generated_conversational'] = $this->ai_generated_forms_by_type( 'conversational' );
		$pro_data['numeric_values']['custom_styled_forms']         = $this->get_custom_styled_forms();
		$pro_data['numeric_values']['post_feeds']                  = $this->get_forms_count_with_post_feeds();
		$pro_data['numeric_values']['user_registration_forms']     = $this->get_user_registration_forms();
		$pro_data['numeric_values']['login_forms']                 = $this->get_login_forms();
		$pro_data['numeric_values']['pdf_generation_forms']        = $this->get_pdf_generation_forms();
		$pro_data['numeric_values']['conditional_emails']          = $this->get_conditional_emails();
		$pro_data['numeric_values']['save_resume_forms']           = $this->get_save_resume_forms();
		$pro_data['numeric_values']['keyword_restricted_forms']    = $this->get_keyword_restricted_forms();
		$pro_data['numeric_values']['ip_restricted_forms']         = $this->get_ip_restricted_forms();
		$pro_data['numeric_values']['country_restricted_forms']    = $this->get_country_restricted_forms();
		$pro_data['numeric_values']['login_restricted_forms']      = $this->get_login_restricted_forms();
		$pro_data['numeric_values']['password_restricted_forms']   = $this->get_password_restricted_forms();

		$integration_settings                          = get_option( 'srfm_pro_integration_settings', [] );
		$pro_data['boolean_values']['webhook_enabled'] = is_array( $integration_settings ) && ! empty( $integration_settings['webhooks_enabled'] );

		$zapier_data                                  = get_option( 'srfm_zap_auth_data', [] );
		$pro_data['boolean_values']['zapier_enabled'] = ! empty( $zapier_data ) && is_array( $zapier_data );

		if ( class_exists( 'SRFM_PRO\Admin\Licensing' ) ) {
			$pro_data['boolean_values']['license_active'] = Licensing::is_license_active();
		} else {
			$pro_data['boolean_values']['license_active'] = false;
		}

		// Add configured integrations data.
		$pro_data['integrations'] = $this->get_configured_integrations();

		return $pro_data;
	}

	/**
	 * Return total number of ai generated forms by form type.
	 *
	 * @param string $form_type Form type to check.
	 *
	 * @since 1.6.1
	 * @return int
	 */
	public function ai_generated_forms_by_type( $form_type = '' ) {

		// Check if form type is valid.
		if ( empty( $form_type ) || ! is_string( $form_type ) ) {
			return 0;
		}

		$valid_types = [ 'conversational', 'calculator' ];
		if ( ! in_array( $form_type, $valid_types, true ) ) {
			return 0;
		}

		// Meta query to get all AI generated forms.
		$meta_query = [
			[
				'key'     => '_srfm_is_ai_generated',
				'value'   => '',
				'compare' => '!=', // Checks if the value is NOT empty.
			],
		];

		$search = '';

		// Generate meta query or search string based on form type.
		switch ( $form_type ) {
			case 'conversational':
				$meta_query[] = [
					'key'     => '_srfm_conversational_form',
					'value'   => '"is_cf_enabled";b:1;',
					'compare' => 'LIKE',
				];
				break;
			case 'calculator':
				$search = '"enableCalculation":true';
				break;
			default:
				return 0;
		}

		return $this->custom_wp_query_total_posts( $meta_query, $search );
	}

	/**
	 * Return forms using custom apps
	 *
	 * @since 1.4.0
	 * @return int
	 */
	public function custom_apps_enabled() {
		$meta_query = [
			'relation' => 'OR',
			[
				'key'     => '_srfm_form_confirmation',
				'value'   => '"confirmation_type";s:23:"suretriggers_below_form"',
				'compare' => 'LIKE',
			],
			[
				'key'     => '_srfm_form_confirmation',
				'value'   => '"confirmation_type";s:30:"suretriggers_confirmation_page"',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Return the number of forms using conditional logic
	 *
	 * @since 1.4.0
	 * @return int
	 */
	public function conditional_logic_forms() {
		$meta_query = [
			'relation' => 'AND',
			[
				'key'     => '_srfm_conditional_logic',
				'value'   => '',
				'compare' => '!=', // Exclude empty string.
			],
			[
				'key'     => '_srfm_conditional_logic',
				'value'   => 'a:0:{}',
				'compare' => '!=', // Exclude empty serialized array.
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get count of multi steps forms
	 *
	 * @since 1.4.0
	 * @return int
	 */
	public function multi_step_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_page_break_settings',
				'value'   => '',
				'compare' => '!=', // Checks if the value is NOT empty.
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Add pro data required for plugin deactivation survey.
	 *
	 * @param array $deactivation_data array of free deactivation data.
	 * @since 1.4.0
	 * @return array
	 */
	public function add_pro_deactivation_data( $deactivation_data ) {
		$deactivation_data[] = [
			'id'                => 'deactivation-survey-sureforms-starter',
			'popup_logo'        => SRFM_URL . 'admin/assets/sureforms-logo.png',
			'plugin_slug'       => 'sureforms-starter',
			'popup_title'       => 'Quick Feedback',
			'support_url'       => 'https://sureforms.com/contact/',
			// Translators: Message asking users for deactivation feedback. %1s is the product name.
			'popup_description' => sprintf( 'If you have a moment, please share why you are deactivating %1s:', SRFM_PRO_PRODUCT ),
			'show_on_screens'   => [ 'plugins' ],
			'plugin_version'    => SRFM_PRO_VER,
		];

		return $deactivation_data;
	}

	/**
	 * Get count of forms where conversational forms are active.
	 *
	 * @since 1.4.2
	 * @return int
	 */
	public function conversational_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_conversational_form',
				'value'   => '"is_cf_enabled";b:1;',
				'compare' => 'LIKE', // Checks if the value is NOT empty.
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get all published SureForms forms which have signature block.
	 *
	 * @since 1.6.0
	 * @return int
	 */
	public function get_forms_count_with_signature_block() {
		return $this->custom_wp_query_total_posts( [], 'wp:srfm/signature' );
	}

	/**
	 * Get all published SureForms forms where post_content contains '"enableCalculation":true'
	 *
	 * @since 1.5.0
	 * @return int
	 */
	public function get_calculator_forms() {
		return $this->custom_wp_query_total_posts( [], '"enableCalculation":true' );
	}

	/**
	 * Get the count of forms where users are using custom styling options.
	 * All the published forms where the '_srfm_forms_styling_starter' meta contains '"form_theme";s:6:"custom"',
	 * are counted as forms using custom styling options.
	 *
	 * @since 1.6.3
	 * @return int
	 */
	public function get_custom_styled_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_forms_styling_starter',
				'value'   => '"form_theme";s:6:"custom"',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get all published SureForms forms which have post-feeds.
	 *
	 * @since 1.10.0
	 * @return int
	 */
	public function get_forms_count_with_post_feeds() {
		return $this->custom_wp_query_total_posts(
			[
				'key'     => '_srfm_raw_cpt_meta',
				'compare' => 'EXISTS', // Ensure the meta key exists.
			]
		);
	}

	/**
	 * Get count of forms with conditional email notifications enabled.
	 *
	 * @since 1.10.1
	 * @return int
	 */
	public function get_conditional_emails() {
		$meta_query = [
			[
				'key'     => '_srfm_email_conditional_meta',
				'value'   => '"status":true',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get count of forms with user registration configuration enabled
	 *
	 * Checks for forms where the _srfm_user_registration_settings meta contains
	 * at least one configuration with "status":true
	 *
	 * @since 1.8.0
	 * @return int
	 */
	public function get_user_registration_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_user_registration_settings',
				'value'   => '"status":true',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get count of forms containing login blocks
	 *
	 * @since 1.8.0
	 * @return int
	 */
	public function get_login_forms() {
		return $this->custom_wp_query_total_posts( [], 'wp:srfm/login' );
	}

	/**
	 * Get count of forms with PDF generation functionality enabled
	 *
	 * Checks for forms where the _srfm_pdf_meta contains PDF configuration settings.
	 * This indicates that PDF generation has been set up for the form.
	 *
	 * @since 1.9.0
	 * @return int
	 */
	public function get_pdf_generation_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_pdf_meta',
				'value'   => '"status":true',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get count of forms with IP restriction enabled
	 *
	 * @since 2.2.0
	 * @return int
	 */
	public function get_ip_restricted_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_additional_form_restriction',
				'value'   => '"ip":{"status":true,"',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get count of forms with country restriction enabled
	 *
	 * @since 2.2.0
	 * @return int
	 */
	public function get_country_restricted_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_additional_form_restriction',
				'value'   => '"country":{"status":true,"',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get count of forms with keyword restriction enabled
	 *
	 * @since 2.2.0
	 * @return int
	 */
	public function get_keyword_restricted_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_additional_form_restriction',
				'value'   => '"keyword":{"status":true,"',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get count of forms with login restriction enabled
	 *
	 * @since 2.2.0
	 * @return int
	 */
	public function get_login_restricted_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_additional_form_restriction',
				'value'   => '"login":{"status":true,"',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get count of forms with password restriction enabled
	 *
	 * @since 2.3.0
	 * @return int
	 */
	public function get_password_restricted_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_additional_form_restriction',
				'value'   => '"password":{"status":true,"',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Get list of configured integrations
	 *
	 * Returns an array of integration names that are configured (enabled or disabled).
	 * Only sends the integration slug/type for privacy, not any configuration data.
	 *
	 * @since 1.13.0
	 * @return array Array of integration names (strings).
	 */
	public function get_configured_integrations() {
		$configured_integrations = [];

		// Check if the method exists before calling it.
		if ( ! method_exists( 'SRFM_Pro\Inc\Pro\Database\Tables\Integrations', 'get_enabled' ) ) {
			return $configured_integrations;
		}

		try {
			// Get all enabled integrations from database.
			$integrations = Integrations::get_enabled( false );

			// Extract only the integration name.
			foreach ( $integrations as $integration ) {
				if ( is_array( $integration ) && ! empty( $integration['name'] ) ) {
					$configured_integrations[] = $integration['name'];
				}
			}
		} catch ( \Exception $e ) {
			// Return empty array if there's any error accessing the database.
			$configured_integrations = [];
		}

		return $configured_integrations;
	}

	/**
	 * Get count of forms with Save & Resume functionality enabled
	 *
	 * Checks for forms where the _srfm_save_resume meta contains
	 * configuration with status set to true.
	 *
	 * @since 2.2.0
	 * @return int
	 */
	public function get_save_resume_forms() {
		$meta_query = [
			[
				'key'     => '_srfm_save_resume',
				'value'   => '"status":true',
				'compare' => 'LIKE',
			],
		];

		return $this->custom_wp_query_total_posts( $meta_query );
	}

	/**
	 * Runs custom WP_Query to fetch data as per requirement
	 *
	 * @param array  $meta_query meta query array for WP_Query.
	 * @param string $search search string.
	 * @since 1.4.0
	 * @return int
	 */
	private function custom_wp_query_total_posts( $meta_query = [], $search = '' ) {

		$args = [
			'post_type'      => SRFM_FORMS_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		];

		if ( ! empty( $meta_query ) && is_array( $meta_query ) ) {
			$args['meta_query'] = $meta_query; //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Meta query required as we need to fetch count of nested data.
		}

		// if search string is provided, add it to the query.
		if ( ! empty( $search ) ) {
			$args['s'] = sanitize_text_field( $search );
		}

		$query       = new \WP_Query( $args );
		$posts_count = $query->found_posts;

		wp_reset_postdata();

		return $posts_count;
	}
}
