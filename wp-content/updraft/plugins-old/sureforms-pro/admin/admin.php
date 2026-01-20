<?php
/**
 * Admin Class.
 *
 * @package sureforms-pro.
 */

namespace SRFM_Pro\Admin;

use SRFM\Inc\Helper;
use SRFM_Pro\Inc\Helper as Pro_Helper;
use SRFM_Pro\Inc\Traits\Get_Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin handler class.
 *
 * @since 0.0.1
 */
class Admin {
	use Get_Instance;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function __construct() {
		add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets_styles' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_global_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_license_popup_assets' ] );

		// Add action links to the plugin page.
		add_filter( 'plugin_action_links_' . SRFM_PRO_BASENAME, [ $this, 'add_action_links' ] );
	}

	/**
	 * Show action on plugin page.
	 *
	 * @param  array $links links.
	 * @return array
	 * @since 1.4.1
	 */
	public function add_action_links( $links ) {

		if ( ! Licensing::is_license_active() ) {
			ob_start(); ?>
			<a class="srfm-pro-activate-license" href="<?php echo esc_url( admin_url( 'admin.php?page=sureforms_form_settings&tab=account-settings' ) ); ?>" rel="noreferrer">
				<?php echo esc_html__( 'Activate License', 'sureforms-pro' ); ?>
			</a>
			<?php
			$links[] = ob_get_clean();
		}

		return $links;
	}

	/**
	 * Block editor styles.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function enqueue_block_assets_styles() {
		$current_screen = get_current_screen();

		if ( is_null( $current_screen ) ) {
			return;
		}

		$file_prefix = defined( 'SRFM_DEBUG' ) && SRFM_DEBUG ? '' : '.min';
		$dir_name    = defined( 'SRFM_DEBUG' ) && SRFM_DEBUG ? 'unminified' : 'minified';
		$css_uri     = SRFM_PRO_URL . 'assets/css/' . $dir_name . '/';

		/* RTL */
		if ( is_rtl() ) {
			$file_prefix .= '-rtl';
		}

			// Enqueue editor styles for post and page.
		if ( SRFM_FORMS_POST_TYPE === $current_screen->post_type ) {
			wp_enqueue_style( SRFM_PRO_SLUG . '-backend-blocks', $css_uri . 'blocks/default/backend' . $file_prefix . '.css', [], SRFM_PRO_VER );
		}
	}

	/**
	 * Enqueue activate license popup Scripts.
	 *
	 * @Hooked - admin_enqueue_scripts
	 *
	 * @return void
	 * @since 1.8.0
	 */
	public function enqueue_license_popup_assets() {

		$current_screen = get_current_screen();

		if ( is_null( $current_screen ) ) {
			return;
		}

		$is_screen_sureforms_add_new_form = method_exists( '\SRFM\Inc\Helper', 'validate_request_context' ) ? Helper::validate_request_context( 'add-new-form', 'page' ) : 'sureforms_page_add-new-form' === $current_screen->id;

		if ( $is_screen_sureforms_add_new_form ) {
			$asset_handle = 'activateLicensePopup';

			$script_asset_path = SRFM_PRO_DIR . 'dist/' . $asset_handle . '.asset.php';
			$script_info       = file_exists( $script_asset_path )
			? include $script_asset_path
			: [
				'dependencies' => [],
				'version'      => SRFM_PRO_VER,
			];

			wp_enqueue_script(
				SRFM_PRO_SLUG . 'activate-license-popup',
				SRFM_PRO_URL . 'dist/' . $asset_handle . '.js',
				$script_info['dependencies'],
				$script_info['version'],
				true
			);

			Pro_Helper::register_script_translations( SRFM_PRO_SLUG . 'activate-license-popup' );
		}
	}

	/**
	 * Enqueue global Scripts and Styles.
	 *
	 * @Hooked - admin_enqueue_scripts
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function enqueue_global_assets() {

		$current_screen = get_current_screen();

		if ( is_null( $current_screen ) ) {
			return;
		}

		// common admin styles.

		$file_suffix = defined( 'SRFM_DEBUG' ) && SRFM_DEBUG ? '' : '.min';
		$dir_name    = defined( 'SRFM_DEBUG' ) && SRFM_DEBUG ? 'unminified' : 'minified';
		$css_uri     = SRFM_PRO_URL . 'assets/css/' . $dir_name . '/';

		/* RTL */
		if ( is_rtl() ) {
			$file_suffix .= '-rtl';
		}

		wp_enqueue_style(
			SRFM_PRO_SLUG . '-admin-styles',
			$css_uri . 'admin' . $file_suffix . '.css',
			[],
			SRFM_PRO_VER
		);

		$is_screen_sureforms_form_settings = method_exists( '\SRFM\Inc\Helper', 'validate_request_context' ) ? Helper::validate_request_context( 'sureforms_form_settings', 'page' ) : 'sureforms_page_sureforms_form_settings' === $current_screen->id;

		if ( $is_screen_sureforms_form_settings ) {
			$asset_handle = 'globalSettings';

			$script_asset_path = SRFM_PRO_DIR . 'dist/' . $asset_handle . '.asset.php';
			$script_info       = file_exists( $script_asset_path )
			? include $script_asset_path
			: [
				'dependencies' => [],
				'version'      => SRFM_PRO_VER,
			];

			wp_enqueue_script(
				SRFM_PRO_SLUG . 'global-settings',
				SRFM_PRO_URL . 'dist/' . $asset_handle . '.js',
				$script_info['dependencies'],
				$script_info['version'],
				true
			);

			// Check css file exists if yes then enqueue it.
			$css_file = SRFM_PRO_DIR . 'dist/' . $asset_handle . '.css';
			if ( file_exists( $css_file ) ) {
				wp_enqueue_style(
					SRFM_PRO_SLUG . 'global-settings',
					SRFM_PRO_URL . 'dist/' . $asset_handle . '.css',
					[],
					SRFM_PRO_VER
				);
			}

			Pro_Helper::register_script_translations( SRFM_PRO_SLUG . 'global-settings' );

			wp_localize_script(
				SRFM_PRO_SLUG . 'global-settings',
				'srfm_pro_global_settings',
				[
					'licensing_nonce' => wp_create_nonce( 'srfm_pro_licensing_nonce' ),
					'no_integrations' => SRFM_URL . 'images/no-integrations.svg',
					'zapier_icon'     => SRFM_URL . 'images/zapier.svg',
				]
			);
		}
	}

	/**
	 * Enqueue Admin Scripts.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function enqueue_block_editor_scripts() {

		$scripts = [
			[
				'unique_file'        => 'blocks',
				'unique_handle'      => 'block-editor',
				'extra_dependencies' => [ 'wp-blocks', 'wp-i18n' ],
			],
			[
				'unique_file'        => 'formSettings',
				'unique_handle'      => 'form-settings',
				'extra_dependencies' => [],
			],
		];

		foreach ( $scripts as $script ) {

			$script_dep_path = SRFM_PRO_DIR . 'dist/' . $script['unique_file'] . '.asset.php';
			$script_dep_data = file_exists( $script_dep_path )
				? include $script_dep_path
				: [
					'dependencies' => [],
					'version'      => SRFM_PRO_VER,
				];
			$script_dep      = array_merge( $script_dep_data['dependencies'], $script['extra_dependencies'] );

			// Scripts.
			wp_enqueue_script(
				SRFM_PRO_SLUG . '-' . $script['unique_handle'], // Handle.
				SRFM_PRO_URL . 'dist/' . $script['unique_file'] . '.js',
				$script_dep, // Dependencies, defined above.
				$script_dep_data['version'], // SRFM_VER.
				true // Enqueue the script in the footer.
			);

			// Check css file exists if yes then enqueue it.
			$css_file = SRFM_PRO_DIR . 'dist/' . $script['unique_file'] . '.css';
			if ( file_exists( $css_file ) ) {
				wp_enqueue_style(
					SRFM_PRO_SLUG . '-' . $script['unique_handle'],
					SRFM_PRO_URL . 'dist/' . $script['unique_file'] . '.css',
					[],
					SRFM_PRO_VER
				);
			}

			// Register script translations.
			Pro_Helper::register_script_translations( SRFM_PRO_SLUG . '-' . $script['unique_handle'] );
		}

		$integration_settings = get_option( 'srfm_pro_integration_settings', [] );
		$file_types           = Pro_Helper::get_wp_file_types( true );

		wp_localize_script(
			SRFM_PRO_SLUG . '-block-editor',
			'SRFM_Pro',
			[
				'upload_formats'       => $file_types['formats'],
				'upload_max_limit'     => $file_types['maxsize'],
				'integration_settings' => $integration_settings,
				'no_integrations'      => SRFM_URL . 'images/no-integrations.svg',
			]
		);

		// Localize the srfmHTMLData.nonce, ajaxurl, and srfmHTMLData.ajaxurl.
		wp_localize_script(
			SRFM_PRO_SLUG . '-block-editor',
			'srfmHTMLData',
			[
				'nonce'                 => wp_create_nonce( 'srfm_html_nonce' ),
				'ajaxUrl'               => admin_url( 'admin-ajax.php' ),
				'editor_default_styles' => Pro_Helper::editor_default_styles(),
			]
		);
	}
}
