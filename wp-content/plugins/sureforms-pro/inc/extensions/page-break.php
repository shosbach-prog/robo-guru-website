<?php
/**
 * PageBreak_Extension Class file.
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
 * Page_Break Class.
 *
 * @since 0.0.1
 */
class Page_Break {
	use Get_Instance;

	/**
	 * Page break settings.
	 *
	 * @var array
	 * @since 0.0.1
	 */
	private static $page_break_settings = [];

	/**
	 * Welcome screen settings.
	 *
	 * @var array
	 * @since 1.4.0
	 */
	private static $welcome_screen_settings = [];

	/**
	 * Is welcome screen enabled.
	 *
	 * @var bool
	 * @since 1.4.0
	 */
	private static $is_welcome_screen = false;

	/**
	 * Live mode data.
	 *
	 * @var array
	 * @since 1.4.0
	 */
	private static $srfm_live_mode_data = [];

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 0.0.1
	 */
	public function __construct() {
		add_action( 'srfm_page_break_header', [ $this, 'render_break_header_container' ], 10, 1 );
		add_action( 'srfm_page_break_pagination', [ $this, 'render_form_pagination' ], 10, 2 );
		add_action( 'srfm_page_break_btn', [ $this, 'render_next_previous_btn' ], 10, 1 );
		add_action( 'srfm_form_css_variables', [ $this, 'render_css_variables' ], 10, 1 );
		add_action( 'srfm_register_additional_post_meta', [ $this, 'register_page_break_post_meta' ], 10 );
	}

	/**
	 * Register page break post meta.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public static function register_page_break_post_meta() {
		register_post_meta(
			SRFM_FORMS_POST_TYPE,
			'_srfm_page_break_settings',
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
							'is_page_break'           => [
								'type' => 'boolean',
							],
							'first_page_label'        => [
								'type' => 'string',
							],
							'progress_indicator_type' => [
								'type' => 'string',
							],
							'toggle_label'            => [
								'type' => 'boolean',
							],
							'next_button_text'        => [
								'type' => 'string',
							],
							'back_button_text'        => [
								'type' => 'string',
							],
						],
					],
				],
				'default'       => [
					'is_page_break'           => false,
					'first_page_label'        => __( 'Page Break Label', 'sureforms-pro' ),
					'progress_indicator_type' => 'connector',
					'toggle_label'            => false,
					'next_button_text'        => __( 'Next', 'sureforms-pro' ),
					'back_button_text'        => __( 'Back', 'sureforms-pro' ),
				],
			]
		);
	}

	/**
	 * Form pagination
	 *
	 * @param \WP_Post $post The current WP_Post object.
	 * @param string   $page_break_first_label label of first page break.
	 * @since 0.0.1
	 * @return void
	 */
	public static function form_pagination( $post, $page_break_first_label ) {
		$content = $post->post_content;
		preg_match_all( '/wp:srfm\/page-break {"block_id":"[^"]*","label":"([^"]*)"} \/-->/', $content, $matches );
		$labels  = $matches[1];
		$content = preg_replace( '/<!--\s*wp:srfm\/page-break\s*{[^}]+?}\s*\/-->/i', '<!-- wp:srfm/page-break /-->', $content );
		if ( ! $content ) {
			return;
		}
		$pages       = explode( '<!-- wp:srfm/page-break /-->', $content );
		$new_content = '';
		$i           = 0;

		// Add the welcome screen if it is enabled.
		if ( self::$is_welcome_screen ) {
			self::render_welcome_screen( $post->ID );
		}

		foreach ( $pages as $page ) {
			$style = 0 === $i && ! self::$is_welcome_screen ? 'display: flex;' : 'display:none';
			if ( 0 === $i ) {
				$label = $page_break_first_label;
			} else {
				$label = $labels[ $i - 1 ] ?? __( 'Page Break Label', 'sureforms-pro' );
			}
			ob_start();
			?>
			<div class="srfm-page-break" style="<?php echo esc_attr( $style ); ?>" data="<?php echo esc_attr( wp_strip_all_tags( Helper::decode_block_attribute( $label ) ) ); ?>">
				<?php
				echo do_blocks( $page ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need it to parse page break content.
				?>
			</div>
			<?php
			$new_content .= ob_get_clean();
			$i++;
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need it to render page break content.
		echo $new_content;
	}

	/**
	 * Render page break header
	 *
	 * @param int $id id.
	 * @since 0.0.1
	 * @return void
	 */
	public static function render_break_header_container( $id ) {
		$page_break_settings      = self::get_page_break_settings( $id );
		$page_break_progress_type = $page_break_settings['progress_indicator_type'];
		$page_break_toggle_label  = $page_break_settings['toggle_label'];
		?>
		<div class="<?php echo method_exists( '\SRFM\Inc\Helper', 'join_strings' ) ? esc_attr( Helper::join_strings( [ 'srfm-page-break-header-container', self::$is_welcome_screen ? 'srfm-welcome-screen-active' : '' ] ) ) : 'srfm-page-break-header-container'; ?>" type="<?php echo esc_attr( $page_break_progress_type ); ?>" toggle-label="<?php echo esc_attr( $page_break_toggle_label ); ?>">
			<?php self::render_page_break_progress_container( $page_break_progress_type ); ?>
		</div>
		<?php
	}

	/**
	 * Render page break progress section
	 *
	 * @param string $page_break_progress_type type of progress type.
	 * @since 0.0.1
	 * @return void
	 */
	public static function render_page_break_progress_container( $page_break_progress_type ) {

		switch ( $page_break_progress_type ) {
			case 'steps':
				?>
					<div class="srfm-page-break-progress-container">
						<ul class="srfm-progress-connector"></ul>
					</div>
				<?php
				break;

			case 'connector':
				?>
					<div class="srfm-page-break-steps">
						<div class="srfm-steps-content">
							<div class="srfm-steps-label">
								<span class="srfm-steps-count-wrap">
									<span class="srfm-step-count"></span>
								</span>
								<span class="srfm-steps-page-title"></span>
							</div>
						</div>
						<div class="srfm-steps-container"></div>
					</div>
				<?php
				break;

			// progress bar is default selected.
			default:
				?>
					<div class="srfm-page-break-progress">
						<progress class="srfm-page-break-indicator" max="100" value="0"></progress>
						<div class="srfm-page-break-steps">
							<div class="srfm-steps-content">
								<span class="srfm-steps-page-title"></span>
							</div>
							<div class="srfm-steps-content">
								<span class="srfm-steps-count-wrap">
									<span class="srfm-step-count"></span>
								</span>
							</div>
						</div>
					</div>
				<?php
				break;
		}
	}

	/**
	 * Render Next and Previous
	 *
	 * @param int $id id.
	 * @since 0.0.1
	 * @return void
	 */
	public static function render_next_previous_btn( $id ) {
		$page_break_settings = self::get_page_break_settings( $id );
		$previous_btn_text   = $page_break_settings['back_button_text'];
		$next_btn_text       = $page_break_settings['next_button_text'];

		ob_start();
		?>
		<div class="<?php echo method_exists( '\SRFM\Inc\Helper', 'join_strings' ) ? esc_attr( Helper::join_strings( [ 'srfm-page-break-buttons', 'wp-block-button', self::$is_welcome_screen ? 'srfm-welcome-screen-active' : '' ] ) ) : 'srfm-page-break-buttons wp-block-button'; ?>">
			<button aria-label="<?php echo esc_attr__( 'Go to Previous Page', 'sureforms-pro' ); ?>" class="srfm-pre-btn">
				<?php echo esc_attr( $previous_btn_text ); ?>
			</button>
			<button aria-label="<?php echo esc_attr__( 'Go to Next Page', 'sureforms-pro' ); ?>" class="srfm-nxt-btn">
				<?php echo esc_attr( $next_btn_text ); ?>
			</button>
		</div>
		<?php
		$html_output = ob_get_clean();
		$html        = trim( false !== $html_output ? $html_output : '' );

		echo wp_kses_post(
			apply_filters(
				'srfm_page_break_buttons_html',
				$html,
				[
					'id'                => $id,
					'previous_btn_text' => $previous_btn_text,
					'next_btn_text'     => $next_btn_text,
					'is_welcome_screen' => self::$is_welcome_screen,
				]
			)
		);
	}

	/**
	 * Render Form Pagination.
	 *
	 * @param \WP_Post $post The current WP_Post object.
	 * @param int      $id id.
	 * @since 0.0.1
	 * @return void
	 */
	public static function render_form_pagination( $post, $id ) {
		$page_break_settings    = self::get_page_break_settings( $id );
		$page_break_first_label = $page_break_settings['first_page_label'];
		?>
		<div id="srfm-anchor" tabindex="-1"></div>
		<?php
		// Render the anchor for managing the focus after page navigation.
		self::form_pagination( $post, $page_break_first_label );
	}

	/**
	 * Get the CSS variables and add them in the form markup.
	 *
	 * @param array<string,string> $params array of values sent by action 'srfm_form_css_variables'.
	 *
	 * @since 0.0.1
	 * @return void
	 */
	public static function render_css_variables( $params ) {
		// CSS variables can be added here.
		$primary_color  = $params['primary_color'];
		$help_color_var = $params['help_color'];
		?>
		--srfm-page-break-back-btn-text-color: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.80 );
		--srfm-page-break-back-btn-background: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.05 );
		--srfm-page-break-unfilled-progress: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.15 );
		--srfm-page-break-indicator-text-color: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.50 );
		--srfm-page-break-connector-checked: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.15 );
		--srfm-page-break-connector-pending: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.25 );
		--srfm-page-break-connector-active: hsl( from <?php echo esc_html( $primary_color ); ?> h s l / 0.50 );
		--srfm-page-break-steps-unfilled: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.50 );
		--srfm-page-break-steps-filled: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.25 );
		--srfm-page-break-steps-progress: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.15 );
		--srfm-page-break-steps-pending-text-color: hsl( from <?php echo esc_html( $help_color_var ); ?> h s l / 0.80 );
		<?php
	}

	/**
	 * Render the welcome screen.
	 *
	 * @param int $id id.
	 * @since 1.4.0
	 * @return void
	 */
	public static function render_welcome_screen( $id ) {
		$welcome_screen         = self::get_welcome_screen_settings( $id );
		$welcome_screen_heading = is_array( $welcome_screen ) && ! empty( $welcome_screen['welcome_screen_heading'] ) ? Helper::get_string_value( $welcome_screen['welcome_screen_heading'] ) : '';
		$welcome_screen_message = is_array( $welcome_screen ) && ! empty( $welcome_screen['welcome_screen_message'] ) ? Helper::get_string_value( $welcome_screen['welcome_screen_message'] ) : '';
		$welcome_screen_image   = is_array( $welcome_screen ) && ! empty( $welcome_screen['welcome_screen_image'] ) ? Helper::get_string_value( $welcome_screen['welcome_screen_image'] ) : '';
		$start_btn_text         = is_array( $welcome_screen ) && ! empty( $welcome_screen['start_btn_text'] ) ? Helper::get_string_value( $welcome_screen['start_btn_text'] ) : '';
		?>
			<div class="srfm-welcome-screen">
				<?php if ( $welcome_screen_image ) { ?>
					<div class="srfm-welcome-image-ctn">
						<img src="<?php echo esc_url( $welcome_screen_image ); ?>" alt="<?php esc_attr_e( 'Welcome Screen Image', 'sureforms-pro' ); ?>" class="srfm-welcome-image">
					</div>
				<?php } ?>
				<?php if ( $welcome_screen_heading ) { ?>
					<h2 class="srfm-welcome-heading"><?php echo esc_html( wp_strip_all_tags( $welcome_screen_heading ) ); ?></h2>
				<?php } ?>
				<?php if ( $welcome_screen_message ) { ?>
					<p class="srfm-welcome-message"><?php echo esc_html( wp_strip_all_tags( $welcome_screen_message ) ); ?></p>
				<?php } ?>
				<button class="srfm-button srfm-start-btn"><?php echo esc_html( wp_strip_all_tags( $start_btn_text ) ); ?></button>
			</div>
		<?php
	}

	/**
	 * Get page break settings.
	 *
	 * @param int $id id.
	 * @since 0.0.1
	 * @return array
	 */
	private static function get_page_break_settings( $id ) {
		if ( ! isset( self::$page_break_settings[ $id ] ) ) {
			self::$page_break_settings[ $id ] = get_post_meta( $id, '_srfm_page_break_settings', true );
		}
		self::get_welcome_screen_settings( $id );

		return self::$page_break_settings[ $id ];
	}

	/**
	 * Get welcome screen settings.
	 *
	 * @param int $id id.
	 * @since 1.4.0
	 * @return array
	 */
	private static function get_welcome_screen_settings( $id ) {
		// Get the live mode data and set the welcome screen status to handle the instant form preview.
		self::$srfm_live_mode_data = empty( self::$srfm_live_mode_data ) ? Helper::get_instant_form_live_data() : self::$srfm_live_mode_data;
		if ( ! isset( self::$welcome_screen_settings[ $id ] ) ) {
			self::$welcome_screen_settings[ $id ] = ! empty( self::$srfm_live_mode_data ) ? self::$srfm_live_mode_data : Helper::get_array_value( get_post_meta( $id, '_srfm_premium_common', true ) );
		}
		if ( ! empty( self::$srfm_live_mode_data['live_mode'] ) ) {
			self::$is_welcome_screen = self::$srfm_live_mode_data['is_welcome_screen'] ?? false;
		} else {
			self::$is_welcome_screen = is_array( self::$welcome_screen_settings[ $id ] ) && ! empty( self::$welcome_screen_settings[ $id ]['is_welcome_screen'] ) ? (bool) Helper::get_string_value( self::$welcome_screen_settings[ $id ]['is_welcome_screen'] ) : false;
		}
		return self::$welcome_screen_settings[ $id ];
	}
}
