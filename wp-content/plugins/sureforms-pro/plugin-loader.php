<?php
/**
 * Plugin Loader.
 *
 * @package sureforms-pro
 * @since 0.0.1
 */

namespace SRFM_Pro;

use SRFM_Pro\Admin\Admin;
use SRFM_Pro\Admin\Analytics;
use SRFM_Pro\Admin\Licensing;
use SRFM_Pro\Inc\Block_Assets;

use SRFM_Pro\Inc\Extensions\Conditional_Logic;
use SRFM_Pro\Inc\Extensions\Additional_Form_Restrictions;
use SRFM_Pro\Inc\Extensions\Entries_Management;
use SRFM_Pro\Inc\Extensions\Gutenberg_Hooks;
use SRFM_Pro\Inc\Extensions\Hooks;
use SRFM_Pro\Inc\Extensions\Page_Break;
use SRFM_Pro\Inc\Extensions\Sanitize_Callbacks;
use SRFM_Pro\Inc\Extensions\Conditional_Emails;
use SRFM_Pro\Inc\Extensions\Conditional_Confirmations;
use SRFM_Pro\Inc\Global_Settings;
use SRFM_Pro\Inc\Integrations\Webhooks;

use SRFM_Pro\Inc\Updater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin Loader.
 *
 * @package sureforms-pro
 * @since 0.0.1
 */
class Plugin_Loader {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since 0.0.1
	 */
	private static $instance;

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		add_filter( 'srfm_blocks', [ $this, 'add_pro_blocks' ] );
		add_action( 'init', [ $this, 'on_plugin_init' ] );
		register_activation_hook(
			SRFM_PRO_FILE,
			[ $this, 'activation_reset' ]
		);
		add_action( 'admin_init', [ $this, 'activation_redirect' ] );
		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
		Analytics::get_instance();
	}

	/**
	 * Initiator
	 *
	 * @since 0.0.1
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Autoload classes.
	 *
	 * @param string $class class name.
	 * @return void
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		$filename = preg_replace(
			[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
			[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
			$class_to_load
		);

		if ( is_string( $filename ) ) {
			$filename = strtolower( $filename );

			$file = SRFM_PRO_DIR . $filename . '.php';

			// if the file redable, include it.
			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}
	}

	/**
	 * Set Redirect flag on activation.
	 *
	 * @Hooked - register_activation_hook
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function activation_reset() {
		update_option( '__srfm_pro_do_redirect', true );
	}

	/**
	 * Handle the activation redirect to the Dashboard.
	 *
	 * @Hooked - admin_init
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function activation_redirect() {

		// Avoid redirection in case of WP_CLI calls.
		if ( defined( 'WP_CLI' ) && \WP_CLI ) {
			return;
		}

		// Avoid redirection in case of ajax calls.
		if ( wp_doing_ajax() ) {
			return;
		}

		$do_redirect = apply_filters( 'srfm_pro_enable_redirect_activation', get_option( '__srfm_pro_do_redirect' ) );

		if ( $do_redirect && is_plugin_active( 'sureforms/sureforms.php' ) ) {

			update_option( '__srfm_pro_do_redirect', false );

			if ( ! is_multisite() ) {
				wp_safe_redirect(
					add_query_arg(
						[
							'page'                         => 'sureforms_form_settings',
							'tab'                          => 'account-settings',
							'srfm-pro-activation-redirect' => true,
						],
						admin_url( 'admin.php' )
					)
				);
				exit;
			}
		}
	}

	/**
	 * After Finish loading SureForms Free, then loaded pro core functionality
	 *
	 * Hooked - srfm_core_loaded
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function on_plugin_init() {

		if ( ! defined( 'SRFM_VER' ) ) {
			add_action( 'admin_notices', [ $this, 'fail_load' ] );
			return;
		}

		if ( ! did_action( 'srfm_core_loaded' ) || ! version_compare( SRFM_VER, SRFM_PRO_CORE_RQD_VER, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'fail_load_out_of_date' ] );
			return;
		}

		Page_Break::get_instance();
		Webhooks::get_instance();
		Hooks::get_instance();
		Updater::get_instance();

		if ( is_admin() ) {
			Admin::get_instance();
			Gutenberg_Hooks::get_instance();
			Entries_Management::get_instance();
		} else {
			Global_Settings::get_instance();
			Block_Assets::get_instance();
		}
		Licensing::get_instance();
		Conditional_Logic::get_instance();
		Additional_Form_Restrictions::get_instance();
		Sanitize_Callbacks::get_instance();
		Conditional_Emails::get_instance();
		Conditional_Confirmations::get_instance();
		
		
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
	 * Check sureforms core is installed or not.
	 *
	 * @return bool
	 * @since 0.0.1
	 */
	public function is_core_installed() {
		$path    = 'sureforms/sureforms.php';
		$plugins = get_plugins();

		return isset( $plugins[ $path ] );
	}

	/**
	 * Admin Notice Callback if failed to load core.
	 *
	 * Hooked - admin_notices
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function fail_load() {
		$screen = get_current_screen();

		if ( empty( $screen ) ) {
			return;
		}

		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		if ( 'sureforms_page_add-new-form' === $screen->id ) {
			return;
		}

		$plugin          = 'sureforms/sureforms.php';
		$pro_plugin_name = defined( 'SRFM_PRO_PRODUCT' ) ? SRFM_PRO_PRODUCT : 'SureForms Pro';

		if ( $this->is_core_installed() ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

			$message = '<h3>' . esc_html__( 'Activate the SureForms Plugin', 'sureforms-pro' ) . '</h3>';
			// translators: %s: SureForms Pro Product Name.
			$message .= '<p>' . sprintf( esc_html__( 'Before you can use all the features of %s, you need to activate the SureForms plugin first.', 'sureforms-pro' ), esc_html( $pro_plugin_name ) ) . '</p>';
			$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', esc_url( $activation_url ), esc_html__( 'Activate Now', 'sureforms-pro' ) ) . '</p>';
		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}

			$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=sureforms' ), 'install-plugin_sureforms' );

			$message = '<h3>' . esc_html__( 'Install and Activate the SureForms Plugin', 'sureforms-pro' ) . '</h3>';
			// translators: %s: SureForms Pro Product Name.
			$message .= '<p>' . sprintf( esc_html__( 'Before you can use all the features of %s, you need to install and activate the SureForms plugin first.', 'sureforms-pro' ), esc_html( $pro_plugin_name ) ) . '</p>';
			$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', esc_url( $install_url ), esc_html__( 'Install SureForms', 'sureforms-pro' ) ) . '</p>';
		}//end if

		// Phpcs ignore comment is required as $message variable is already escaped.
		echo '<div class="error">' . wp_kses_post( $message ) . '</div>';
	}

	/**
	 * Admin Notice Callback if failed to load updated core.
	 *
	 * Hooked - admin_notices
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function fail_load_out_of_date() {
		$screen = get_current_screen();

		if ( empty( $screen ) ) {
			return;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$file_path = 'sureforms/sureforms.php';

		$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
		$message      = '<p>' . esc_html__( 'SureForms Pro is not working because you are using an old version of SureForms.', 'sureforms-pro' ) . '</p>';
		$message     .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', esc_url( $upgrade_link ), esc_html__( 'Update SureForms Now', 'sureforms-pro' ) ) . '</p>';

		// Phpcs ignore comment is required as $message variable is already escaped.
		echo '<div class="error">' . wp_kses_post( $message ) . '</div>';
	}

	/**
	 * Load Plugin Text Domain.
	 * This will load the translation textdomain depending on the file priorities.
	 *      1. Global Languages /wp-content/languages/sureforms-pro/ folder
	 *      2. Local directory /wp-content/plugins/sureforms-pro/languages/ folder
	 *
	 * @since 1.2.2
	 * @return void
	 */
	public function load_textdomain() {
		/**
		 * Filters the languages directory path to use for plugin.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'srfm_languages_directory', SRFM_PRO_DIR . 'languages/' );

		// Traditional WordPress plugin locale filter.
		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		/**
		 * Language Locale for plugin
		 *
		 * Uses get_user_locale()` in WordPress 4.7 or greater,
		 * otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'sureforms-pro' );//phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- wordpress hook
		$mofile = sprintf( '%1$s-%2$s.mo', 'sureforms-pro', $locale );

		// Setup paths to current locale file.
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;
		$mofile_local  = $lang_dir . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/sureforms-pro/ folder.
			load_textdomain( 'sureforms-pro', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/sureforms-pro/languages/ folder.
			load_textdomain( 'sureforms-pro', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'sureforms-pro', false, $lang_dir );
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Plugin_Loader::get_instance();
