<?php
/**
 * Admin functionality.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin class.
 *
 * @since 1.0.0
 */
class Admin {

	/**
	 * The single instance of the class.
	 *
	 * @var Admin
	 */
	protected static $instance = null;

	/**
	 * Main Admin Instance.
	 *
	 * @since 1.0.0
	 * @return Admin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		// Hook into BuddyBoss General settings page to add Site SEO section.
		add_action( 'bp_admin_setting_general_register_fields', array( $this, 'register_general_seo_settings' ), 10, 1 );

		// Enqueue scripts on BuddyBoss settings pages.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add license status notice if invalid.
		add_action( 'admin_notices', array( $this, 'show_license_notice' ) );
	}

	/**
	 * Register Site SEO settings in BuddyBoss General settings.
	 *
	 * @since 1.0.0
	 * @param object $settings_tab The settings tab object.
	 */
	public function register_general_seo_settings( $settings_tab ) {
		// Add Site SEO section.
		$settings_tab->add_section(
			'buddyboss_site_seo',
			esc_html__( 'Site SEO', 'buddyboss-sharing' ),
			'__return_null',
			array( $this, 'seo_tutorial_callback' )
		);

		// Search Result Preview (read-only display).
		$settings_tab->add_field(
			'buddyboss_seo_search_preview',
			esc_html__( 'Search Result Preview', 'buddyboss-sharing' ),
			array( $this, 'render_search_preview' ),
			'',
			array(),
			'buddyboss_site_seo'
		);

		// SEO Title.
		$settings_tab->add_field(
			'buddyboss_seo_title',
			esc_html__( 'SEO Title', 'buddyboss-sharing' ),
			array( $this, 'render_seo_title_field' ),
			'sanitize_text_field',
			array(),
			'buddyboss_site_seo'
		);

		// SEO Description.
		$settings_tab->add_field(
			'buddyboss_seo_description',
			esc_html__( 'SEO Description', 'buddyboss-sharing' ),
			array( $this, 'render_seo_description_field' ),
			'sanitize_textarea_field',
			array(),
			'buddyboss_site_seo'
		);

		// Social Section Label.
		$settings_tab->add_field(
			'buddyboss_seo_social_label',
			esc_html__( 'Social', 'buddyboss-sharing' ),
			array( $this, 'render_social_label' ),
			'',
			array(),
			'buddyboss_site_seo'
		);

		// Enable Open Graph.
		$settings_tab->add_field(
			'buddyboss_enable_open_graph',
			'',
			array( $this, 'render_enable_open_graph_field' ),
			'intval',
			array( 'class' => 'child-no-padding-first' ),
			'buddyboss_site_seo'
		);

		// Social Media Preview (read-only display).
		$settings_tab->add_field(
			'buddyboss_seo_social_preview',
			'',
			array( $this, 'render_social_preview' ),
			'',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// OG Title.
		$settings_tab->add_field(
			'buddyboss_og_title',
			esc_html__( 'Open Graph Title', 'buddyboss-sharing' ),
			array( $this, 'render_og_title_field' ),
			'sanitize_text_field',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// Use same as SEO title checkbox.
		$settings_tab->add_field(
			'buddyboss_og_use_same_title',
			'',
			array( $this, 'render_og_use_same_title' ),
			'intval',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// OG Description.
		$settings_tab->add_field(
			'buddyboss_og_description',
			esc_html__( 'Open Graph Description', 'buddyboss-sharing' ),
			array( $this, 'render_og_description_field' ),
			'sanitize_textarea_field',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// Use same as SEO description checkbox.
		$settings_tab->add_field(
			'buddyboss_og_use_same_desc',
			'',
			array( $this, 'render_og_use_same_desc' ),
			'intval',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// OG Image.
		$settings_tab->add_field(
			'buddyboss_og_image',
			esc_html__( 'Open Graph Image', 'buddyboss-sharing' ),
			array( $this, 'render_og_image_field' ),
			'esc_url_raw',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// Activity Open Graph (Public Networks) Section Label.
		$settings_tab->add_field(
			'buddyboss_activity_og_public_label',
			esc_html__( 'Activity Open Graph (Public Networks)', 'buddyboss-sharing' ),
			array( $this, 'render_activity_og_public_label' ),
			'',
			array(),
			'buddyboss_site_seo'
		);

		// Activity OG Title Template.
		$settings_tab->add_field(
			'buddyboss_activity_og_title_template',
			esc_html__( 'Activity Title Template', 'buddyboss-sharing' ),
			array( $this, 'render_activity_og_title_template_field' ),
			'sanitize_text_field',
			array( 'class' => 'child-no-padding-first' ),
			'buddyboss_site_seo'
		);

		// Activity OG Description Template.
		$settings_tab->add_field(
			'buddyboss_activity_og_description_template',
			esc_html__( 'Activity Description Template', 'buddyboss-sharing' ),
			array( $this, 'render_activity_og_description_template_field' ),
			'sanitize_text_field',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// Group Open Graph (Public Networks) Section Label.
		$settings_tab->add_field(
			'buddyboss_group_og_public_label',
			esc_html__( 'Group Open Graph (Public Networks)', 'buddyboss-sharing' ),
			array( $this, 'render_group_og_public_label' ),
			'',
			array(),
			'buddyboss_site_seo'
		);

		// Group OG Title Template.
		$settings_tab->add_field(
			'buddyboss_group_og_title_template',
			esc_html__( 'Group Title Template', 'buddyboss-sharing' ),
			array( $this, 'render_group_og_title_template_field' ),
			'sanitize_text_field',
			array( 'class' => 'child-no-padding-first' ),
			'buddyboss_site_seo'
		);

		// Group OG Description Template.
		$settings_tab->add_field(
			'buddyboss_group_og_description_template',
			esc_html__( 'Group Description Template', 'buddyboss-sharing' ),
			array( $this, 'render_group_og_description_template_field' ),
			'sanitize_text_field',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// Member Open Graph (Public Networks) Section Label.
		$settings_tab->add_field(
			'buddyboss_member_og_public_label',
			esc_html__( 'Member Open Graph (Public Networks)', 'buddyboss-sharing' ),
			array( $this, 'render_member_og_public_label' ),
			'',
			array(),
			'buddyboss_site_seo'
		);

		// Member OG Title Template.
		$settings_tab->add_field(
			'buddyboss_member_og_title_template',
			esc_html__( 'Member Title Template', 'buddyboss-sharing' ),
			array( $this, 'render_member_og_title_template_field' ),
			'sanitize_text_field',
			array( 'class' => 'child-no-padding-first' ),
			'buddyboss_site_seo'
		);

		// Member OG Description Template.
		$settings_tab->add_field(
			'buddyboss_member_og_description_template',
			esc_html__( 'Member Description Template', 'buddyboss-sharing' ),
			array( $this, 'render_member_og_description_template_field' ),
			'sanitize_text_field',
			array( 'class' => 'child-no-padding' ),
			'buddyboss_site_seo'
		);

		// Indexing Section Label.
		$settings_tab->add_field(
			'buddyboss_seo_indexing_label',
			esc_html__( 'Indexing', 'buddyboss-sharing' ),
			array( $this, 'render_indexing_label' ),
			'',
			array(),
			'buddyboss_site_seo'
		);

		// Search Engine Indexing.
		$settings_tab->add_field(
			'buddyboss_seo_index_posts',
			'',
			array( $this, 'render_search_indexing_field' ),
			'intval',
			array( 'class' => 'child-no-padding-first' ),
			'buddyboss_site_seo'
		);
	}

	/**
	 * SEO tutorial callback.
	 *
	 * @since 1.0.0
	 */
	public function seo_tutorial_callback() {
		?>
		<p>
			<a class="button" href="https://www.buddyboss.com/docs/how-to-set-up-site-seo-in-buddyboss/" target="_blank">
				<?php esc_html_e( 'View Tutorial', 'buddyboss-sharing' ); ?>
			</a>
		</p>
		<?php
	}

	/**
	 * Render Search Result Preview.
	 *
	 * @since 1.0.0
	 */
	public function render_search_preview() {
		// Get saved values or defaults (site title and tagline)
		$default_title = get_bloginfo( 'name' );
		$default_desc  = get_bloginfo( 'description' );

		$seo_title = bp_get_option( 'buddyboss_seo_title', '' );
		$seo_desc  = bp_get_option( 'buddyboss_seo_description', '' );

		// Use defaults if empty
		$display_title = ! empty( $seo_title ) ? $seo_title : $default_title;
		$display_desc  = ! empty( $seo_desc ) ? $seo_desc : $default_desc;

		// Get site icon (favicon)
		$site_icon_url = get_site_icon_url( 32 );
		$site_url      = home_url();
		$site_url_display = str_replace( array( 'http://', 'https://' ), '', $site_url );
		?>
		<div class="buddyboss-seo-preview-box" style="background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; padding: 20px; margin-bottom: 20px;">
			<div class="buddyboss-seo-preview-brand" style="display: flex; align-items: center; margin-bottom: 8px;">
				<span style="background: #2271b1; color: white; padding: 2px 8px; border-radius: 3px; font-size: 12px; font-weight: 600;">BuddyBoss</span>
			</div>
			<div class="buddyboss-seo-preview-url" style="color: #5f6368; font-size: 14px; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
				<?php if ( $site_icon_url ) : ?>
					<img src="<?php echo esc_url( $site_icon_url ); ?>" alt="Site Icon" style="width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;" />
				<?php else : ?>
					<span style="width: 26px; height: 26px; background: #e0e0e0; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">🌐</span>
				<?php endif; ?>
				<span><?php echo esc_html( $site_url_display ); ?></span>
			</div>
			<div class="buddyboss-seo-preview-title" style="color: #1a0dab; font-size: 20px; font-weight: 400; line-height: 1.3; margin-bottom: 4px;" id="seo-preview-title">
				<?php echo esc_html( $display_title ); ?>
			</div>
			<div class="buddyboss-seo-preview-description" style="color: #4d5156; font-size: 14px; line-height: 1.5;" id="seo-preview-desc">
				<?php echo esc_html( $display_desc ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render SEO Title field.
	 *
	 * @since 1.0.0
	 */
	public function render_seo_title_field() {
		$default_title = get_bloginfo( 'name' );
		$value         = bp_get_option( 'buddyboss_seo_title', '' );
		$display_value = ! empty( $value ) ? $value : $default_title;
		?>
		<input type="text" name="buddyboss_seo_title" id="buddyboss_seo_title" value="<?php echo esc_attr( $display_value ); ?>" class="regular-text buddyboss-seo-title-input" placeholder="<?php echo esc_attr( $default_title ); ?>" />
		<p class="description">
			<?php esc_html_e( 'Define the main title of your website that Google will index. Optimal title length is approximately 55 characters.', 'buddyboss-sharing' ); ?>
		</p>
		<?php
	}

	/**
	 * Render SEO Description field.
	 *
	 * @since 1.0.0
	 */
	public function render_seo_description_field() {
		$default_desc  = get_bloginfo( 'description' );
		$value         = bp_get_option( 'buddyboss_seo_description', '' );
		$display_value = ! empty( $value ) ? $value : $default_desc;
		?>
		<textarea name="buddyboss_seo_description" id="buddyboss_seo_description" rows="4" class="large-text buddyboss-seo-desc-input" placeholder="<?php echo esc_attr( $default_desc ); ?>"><?php echo esc_textarea( $display_value ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Set the default description that will accompany your SEO title in search engine results. Optimal description length is 155 to 300 characters.', 'buddyboss-sharing' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Social label.
	 *
	 * @since 1.0.0
	 */
	public function render_social_label() {
		?>
		<h3 style="margin-top: 30px; margin-bottom: 10px; font-size: 16px;"><?php esc_html_e( 'Social', 'buddyboss-sharing' ); ?></h3>
		<?php
	}

	/**
	 * Render Enable Open Graph field.
	 *
	 * @since 1.0.0
	 */
	public function render_enable_open_graph_field() {
		$checked = bp_get_option( 'buddyboss_enable_open_graph', 1 );
		?>
		<input type="checkbox" name="buddyboss_enable_open_graph" id="buddyboss_enable_open_graph" value="1" <?php checked( $checked, 1 ); ?> />
		<label for="buddyboss_enable_open_graph">
			<strong><?php esc_html_e( 'Enable Open Graph', 'buddyboss-sharing' ); ?></strong>
		</label>
		<p class="description">
			<?php esc_html_e( 'Open Graph support improves how your content looks when shared on social media like Facebook, ensuring a more engaging and visually appealing experience.', 'buddyboss-sharing' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Social Media Preview.
	 *
	 * @since 1.0.0
	 */
	public function render_social_preview() {
		// Get defaults from site settings
		$default_title = get_bloginfo( 'name' );
		$default_desc  = get_bloginfo( 'description' );

		// Get SEO values
		$seo_title = bp_get_option( 'buddyboss_seo_title', '' );
		$seo_desc  = bp_get_option( 'buddyboss_seo_description', '' );

		// Get OG values
		$og_title = bp_get_option( 'buddyboss_og_title', '' );
		$og_desc  = bp_get_option( 'buddyboss_og_description', '' );

		// Determine display values (OG > SEO > Default)
		if ( ! empty( $og_title ) ) {
			$display_title = $og_title;
		} elseif ( ! empty( $seo_title ) ) {
			$display_title = $seo_title;
		} else {
			$display_title = $default_title;
		}

		if ( ! empty( $og_desc ) ) {
			$display_desc = $og_desc;
		} elseif ( ! empty( $seo_desc ) ) {
			$display_desc = $seo_desc;
		} else {
			$display_desc = $default_desc;
		}

		$og_image       = bp_get_option( 'buddyboss_og_image', '' );
		$site_url       = home_url();
		$site_url_clean = str_replace( array( 'http://', 'https://' ), '', $site_url );
		?>
		<div class="buddyboss-seo-social-preview" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; max-width: 500px; margin: 20px 0;">
			<?php if ( $og_image ) : ?>
				<div class="social-preview-image" style="width: 100%; height: 260px; overflow: hidden; background: #f0f2f5;">
					<img src="<?php echo esc_url( $og_image ); ?>" style="width: 100%; height: 100%; object-fit: cover;" id="social-preview-img" />
				</div>
			<?php else : ?>
				<div class="social-preview-image" style="width: 100%; height: 260px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
					<span style="color: white; font-size: 48px;">📱</span>
				</div>
			<?php endif; ?>
			<div style="padding: 12px; background: #f0f2f5;">
				<div style="color: #606770; font-size: 12px; text-transform: uppercase; margin-bottom: 4px;">
					<?php echo esc_html( $site_url_clean ); ?>
				</div>
				<div style="color: #1c1e21; font-size: 16px; font-weight: 600; line-height: 20px; margin-bottom: 4px;" id="social-preview-title">
					<?php echo esc_html( $display_title ); ?>
				</div>
				<div style="color: #606770; font-size: 14px; line-height: 20px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" id="social-preview-desc">
					<?php echo esc_html( $display_desc ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render OG Title field.
	 *
	 * @since 1.0.0
	 */
	public function render_og_title_field() {
		$use_same = bp_get_option( 'buddyboss_og_use_same_title', 1 );

		// Get values for fallback
		$default_title = get_bloginfo( 'name' );
		$seo_title     = bp_get_option( 'buddyboss_seo_title', '' );
		$og_title      = bp_get_option( 'buddyboss_og_title', '' );

		// Determine display value
		if ( $use_same ) {
			// If synced, use SEO title or default
			$display_value = ! empty( $seo_title ) ? $seo_title : $default_title;
		} else {
			// If not synced, use OG title > SEO title > default
			if ( ! empty( $og_title ) ) {
				$display_value = $og_title;
			} elseif ( ! empty( $seo_title ) ) {
				$display_value = $seo_title;
			} else {
				$display_value = $default_title;
			}
		}

		$disabled = $use_same ? 'readonly' : '';
		?>
		<input type="text" name="buddyboss_og_title" id="buddyboss_og_title" value="<?php echo esc_attr( $display_value ); ?>" class="regular-text buddyboss-og-title-input" <?php echo esc_attr( $disabled ); ?> placeholder="<?php echo esc_attr( ! empty( $seo_title ) ? $seo_title : $default_title ); ?>" />
		<?php
	}

	/**
	 * Render 'Use same as SEO title' checkbox.
	 *
	 * @since 1.0.0
	 */
	public function render_og_use_same_title() {
		$checked = bp_get_option( 'buddyboss_og_use_same_title', 1 );
		?>
		<label style="display: inline-flex; align-items: center;">
			<input type="checkbox" name="buddyboss_og_use_same_title" id="buddyboss_og_use_same_title" value="1" <?php checked( $checked, 1 ); ?> class="buddyboss-og-sync-title" />
			<span style="margin-left: 8px;"><?php esc_html_e( 'Use same as SEO title', 'buddyboss-sharing' ); ?></span>
		</label>
		<?php
	}

	/**
	 * Render OG Description field.
	 *
	 * @since 1.0.0
	 */
	public function render_og_description_field() {
		$use_same = bp_get_option( 'buddyboss_og_use_same_desc', 1 );

		// Get values for fallback
		$default_desc = get_bloginfo( 'description' );
		$seo_desc     = bp_get_option( 'buddyboss_seo_description', '' );
		$og_desc      = bp_get_option( 'buddyboss_og_description', '' );

		// Determine display value
		if ( $use_same ) {
			// If synced, use SEO description or default
			$display_value = ! empty( $seo_desc ) ? $seo_desc : $default_desc;
		} else {
			// If not synced, use OG description > SEO description > default
			if ( ! empty( $og_desc ) ) {
				$display_value = $og_desc;
			} elseif ( ! empty( $seo_desc ) ) {
				$display_value = $seo_desc;
			} else {
				$display_value = $default_desc;
			}
		}

		$disabled = $use_same ? 'readonly' : '';
		?>
		<textarea name="buddyboss_og_description" id="buddyboss_og_description" rows="4" class="large-text buddyboss-og-desc-input" <?php echo esc_attr( $disabled ); ?> placeholder="<?php echo esc_attr( ! empty( $seo_desc ) ? $seo_desc : $default_desc ); ?>"><?php echo esc_textarea( $display_value ); ?></textarea>
		<?php
	}

	/**
	 * Render 'Use same as SEO description' checkbox.
	 *
	 * @since 1.0.0
	 */
	public function render_og_use_same_desc() {
		$checked = bp_get_option( 'buddyboss_og_use_same_desc', 1 );
		?>
		<label style="display: inline-flex; align-items: center;">
			<input type="checkbox" name="buddyboss_og_use_same_desc" id="buddyboss_og_use_same_desc" value="1" <?php checked( $checked, 1 ); ?> class="buddyboss-og-sync-desc" />
			<span style="margin-left: 8px;"><?php esc_html_e( 'Use same as SEO description', 'buddyboss-sharing' ); ?></span>
		</label>
		<?php
	}

	/**
	 * Render OG Image field.
	 *
	 * @since 1.0.0
	 */
	public function render_og_image_field() {
		$image_url = bp_get_option( 'buddyboss_og_image', '' );
		?>
		<div class="buddyboss-sharing-image-upload">
			<input type="hidden" name="buddyboss_og_image" id="buddyboss_og_image" value="<?php echo esc_url( $image_url ); ?>" />

			<?php if ( $image_url ) : ?>
				<div class="buddyboss-sharing-image-preview" style="margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; max-width: 500px;">
					<img src="<?php echo esc_url( $image_url ); ?>" id="og-image-preview-img" style="width: 100%; height: auto; display: block;" />
				</div>
			<?php endif; ?>

			<p>
				<button type="button" class="button buddyboss-sharing-upload-image"><?php esc_html_e( 'Upload', 'buddyboss-sharing' ); ?></button>
				<?php if ( $image_url ) : ?>
					<button type="button" class="button buddyboss-sharing-remove-image"><?php esc_html_e( 'Remove', 'buddyboss-sharing' ); ?></button>
				<?php endif; ?>
			</p>
			<p class="description">
				<?php esc_html_e( "Use an image that's at least 1200px by 630px minimum (1.91:1 ratio). Maximum file size: 8 MB. Recommended: 1-2 MB for optimal performance.", 'buddyboss-sharing' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render Indexing label.
	 *
	 * @since 1.0.0
	 */
	public function render_indexing_label() {
		?>
		<h3 style="margin-top: 30px; margin-bottom: 10px; font-size: 16px;"><?php esc_html_e( 'Indexing', 'buddyboss-sharing' ); ?></h3>
		<p class="description" style="margin-top: 0;">
			<?php esc_html_e( 'Choose if you want search engines to index this page.', 'buddyboss-sharing' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Search Indexing field.
	 *
	 * @since 1.0.0
	 */
	public function render_search_indexing_field() {
		$index_posts    = bp_get_option( 'buddyboss_seo_index_posts', 1 );
		$index_profiles = bp_get_option( 'buddyboss_seo_index_profiles', 1 );
		$index_groups   = bp_get_option( 'buddyboss_seo_index_groups', 1 );
		?>
		<fieldset>
			<label style="display: block; margin-bottom: 10px;">
				<input type="checkbox" name="buddyboss_seo_index_posts" value="1" <?php checked( $index_posts, 1 ); ?> />
				<?php esc_html_e( 'Posts', 'buddyboss-sharing' ); ?>
			</label>
			<label style="display: block; margin-bottom: 10px;">
				<input type="checkbox" name="buddyboss_seo_index_profiles" value="1" <?php checked( $index_profiles, 1 ); ?> />
				<?php esc_html_e( 'Profiles', 'buddyboss-sharing' ); ?>
			</label>
			<label style="display: block; margin-bottom: 10px;">
				<input type="checkbox" name="buddyboss_seo_index_groups" value="1" <?php checked( $index_groups, 1 ); ?> />
				<?php esc_html_e( 'Groups', 'buddyboss-sharing' ); ?>
			</label>
			<p class="description" style="margin-top: 10px;">
				<?php esc_html_e( 'Disabling indexing will hide this content from search engines.', 'buddyboss-sharing' ); ?>
			</p>
		</fieldset>
		<?php
	}

	/**
	 * Render Activity OG Public Network label.
	 *
	 * @since 1.0.0
	 */
	public function render_activity_og_public_label() {
		?>
		<h3 style="margin-top: 30px; margin-bottom: 10px; font-size: 16px;"><?php esc_html_e( 'Activity Open Graph (Public Networks)', 'buddyboss-sharing' ); ?></h3>
		<p class="description" style="margin-top: 0;">
			<?php esc_html_e( 'Customize how single activity posts appear when shared on social media. These settings only apply when your network is public.', 'buddyboss-sharing' ); ?>
		</p>
		<p class="description" style="margin-top: 10px; padding: 10px; background: #f0f6fc; border-left: 4px solid #2271b1; border-radius: 3px;">
			<strong><?php esc_html_e( 'Note:', 'buddyboss-sharing' ); ?></strong>
			<?php
			/* translators: %s: BuddyBoss Platform Settings menu link */
			printf(
				esc_html__( 'For private networks, the general Open Graph settings above will be used. You can configure your network privacy in %s.', 'buddyboss-sharing' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=bp-settings' ) ) . '">' . esc_html__( 'BuddyBoss &gt; Settings', 'buddyboss-sharing' ) . '</a>'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render Activity OG Title Template field.
	 *
	 * @since 1.0.0
	 */
	public function render_activity_og_title_template_field() {
		$default_template = '{activity_action} | {site_title}';
		$value            = bp_get_option( 'buddyboss_activity_og_title_template', '' );
		$display_value    = ! empty( $value ) ? $value : $default_template;
		?>
		<input type="text" name="buddyboss_activity_og_title_template" id="buddyboss_activity_og_title_template" value="<?php echo esc_attr( $display_value ); ?>" class="large-text" placeholder="<?php echo esc_attr( $default_template ); ?>" />
		<p class="description">
			<?php esc_html_e( 'Template for activity Open Graph titles. Use the tags below to dynamically insert activity data.', 'buddyboss-sharing' ); ?>
		</p>
		<div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px;">
			<p style="margin: 0 0 10px 0; font-weight: 600;"><?php esc_html_e( 'Available Tags:', 'buddyboss-sharing' ); ?></p>
			<ul style="margin: 0; padding-left: 20px; list-style: disc;">
				<li><code>{activity_title}</code> - <?php esc_html_e( 'Activity post title (falls back to activity_action if empty)', 'buddyboss-sharing' ); ?></li>
				<li><code>{activity_action}</code> - <?php esc_html_e( 'Activity action text (e.g., "John posted an update")', 'buddyboss-sharing' ); ?></li>
				<li><code>{activity_content}</code> - <?php esc_html_e( 'Activity content (limited to 60 characters)', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_name}</code> - <?php esc_html_e( 'Activity author display name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_first_name}</code> - <?php esc_html_e( 'Activity author first name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_last_name}</code> - <?php esc_html_e( 'Activity author last name', 'buddyboss-sharing' ); ?></li>
				<li><code>{site_title}</code> - <?php esc_html_e( 'Your site name', 'buddyboss-sharing' ); ?></li>
				<li><code>{separator}</code> - <?php esc_html_e( 'Separator (displays as |)', 'buddyboss-sharing' ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render Activity OG Description Template field.
	 *
	 * @since 1.0.0
	 */
	public function render_activity_og_description_template_field() {
		$default_template = '{activity_content}';
		$value            = bp_get_option( 'buddyboss_activity_og_description_template', '' );
		$display_value    = ! empty( $value ) ? $value : $default_template;
		?>
		<textarea name="buddyboss_activity_og_description_template" id="buddyboss_activity_og_description_template" rows="3" class="large-text" placeholder="<?php echo esc_attr( $default_template ); ?>"><?php echo esc_textarea( $display_value ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Template for activity Open Graph descriptions. Use the tags below to dynamically insert activity data. Optimal length is 155-300 characters.', 'buddyboss-sharing' ); ?>
		</p>
		<div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px;">
			<p style="margin: 0 0 10px 0; font-weight: 600;"><?php esc_html_e( 'Available Tags:', 'buddyboss-sharing' ); ?></p>
			<ul style="margin: 0; padding-left: 20px; list-style: disc;">
				<li><code>{activity_title}</code> - <?php esc_html_e( 'Activity post title (falls back to activity_action if empty)', 'buddyboss-sharing' ); ?></li>
				<li><code>{activity_action}</code> - <?php esc_html_e( 'Activity action text (e.g., "John posted an update")', 'buddyboss-sharing' ); ?></li>
				<li><code>{activity_content}</code> - <?php esc_html_e( 'Activity content (limited to 300 characters)', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_name}</code> - <?php esc_html_e( 'Activity author display name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_first_name}</code> - <?php esc_html_e( 'Activity author first name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_last_name}</code> - <?php esc_html_e( 'Activity author last name', 'buddyboss-sharing' ); ?></li>
				<li><code>{site_title}</code> - <?php esc_html_e( 'Your site name', 'buddyboss-sharing' ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render Group OG Public Network label.
	 *
	 * @since 1.0.0
	 */
	public function render_group_og_public_label() {
		?>
		<h3 style="margin-top: 30px; margin-bottom: 10px; font-size: 16px;"><?php esc_html_e( 'Group Open Graph (Public Networks)', 'buddyboss-sharing' ); ?></h3>
		<p class="description" style="margin-top: 0;">
			<?php esc_html_e( 'Customize how group pages appear when shared on social media. These settings only apply when your network is public.', 'buddyboss-sharing' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Group OG Title Template field.
	 *
	 * @since 1.0.0
	 */
	public function render_group_og_title_template_field() {
		$default_template = '{group_name} | {site_title}';
		$value            = bp_get_option( 'buddyboss_group_og_title_template', '' );
		$display_value    = ! empty( $value ) ? $value : $default_template;
		?>
		<input type="text" name="buddyboss_group_og_title_template" id="buddyboss_group_og_title_template" value="<?php echo esc_attr( $display_value ); ?>" class="large-text" placeholder="<?php echo esc_attr( $default_template ); ?>" />
		<p class="description">
			<?php esc_html_e( 'Template for group Open Graph titles. Use the tags below to dynamically insert group data.', 'buddyboss-sharing' ); ?>
		</p>
		<div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px;">
			<p style="margin: 0 0 10px 0; font-weight: 600;"><?php esc_html_e( 'Available Tags:', 'buddyboss-sharing' ); ?></p>
			<ul style="margin: 0; padding-left: 20px; list-style: disc;">
				<li><code>{group_name}</code> - <?php esc_html_e( 'Group name', 'buddyboss-sharing' ); ?></li>
				<li><code>{group_description}</code> - <?php esc_html_e( 'Group description (limited to 60 characters)', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_name}</code> - <?php esc_html_e( 'Group creator display name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_first_name}</code> - <?php esc_html_e( 'Group creator first name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_last_name}</code> - <?php esc_html_e( 'Group creator last name', 'buddyboss-sharing' ); ?></li>
				<li><code>{site_title}</code> - <?php esc_html_e( 'Your site name', 'buddyboss-sharing' ); ?></li>
				<li><code>{separator}</code> - <?php esc_html_e( 'Separator (displays as |)', 'buddyboss-sharing' ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render Group OG Description Template field.
	 *
	 * @since 1.0.0
	 */
	public function render_group_og_description_template_field() {
		$default_template = '{group_description}';
		$value            = bp_get_option( 'buddyboss_group_og_description_template', '' );
		$display_value    = ! empty( $value ) ? $value : $default_template;
		?>
		<textarea name="buddyboss_group_og_description_template" id="buddyboss_group_og_description_template" rows="3" class="large-text" placeholder="<?php echo esc_attr( $default_template ); ?>"><?php echo esc_textarea( $display_value ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Template for group Open Graph descriptions. Use the tags below to dynamically insert group data. Optimal length is 155-300 characters.', 'buddyboss-sharing' ); ?>
		</p>
		<div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px;">
			<p style="margin: 0 0 10px 0; font-weight: 600;"><?php esc_html_e( 'Available Tags:', 'buddyboss-sharing' ); ?></p>
			<ul style="margin: 0; padding-left: 20px; list-style: disc;">
				<li><code>{group_name}</code> - <?php esc_html_e( 'Group name', 'buddyboss-sharing' ); ?></li>
				<li><code>{group_description}</code> - <?php esc_html_e( 'Group description (limited to 300 characters)', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_name}</code> - <?php esc_html_e( 'Group creator display name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_first_name}</code> - <?php esc_html_e( 'Group creator first name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_last_name}</code> - <?php esc_html_e( 'Group creator last name', 'buddyboss-sharing' ); ?></li>
				<li><code>{site_title}</code> - <?php esc_html_e( 'Your site name', 'buddyboss-sharing' ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render Member OG Public Network label.
	 *
	 * @since 1.0.0
	 */
	public function render_member_og_public_label() {
		?>
		<h3 style="margin-top: 30px; margin-bottom: 10px; font-size: 16px;"><?php esc_html_e( 'Member Open Graph (Public Networks)', 'buddyboss-sharing' ); ?></h3>
		<p class="description" style="margin-top: 0;">
			<?php esc_html_e( 'Customize how member profile pages appear when shared on social media. These settings only apply when your network is public.', 'buddyboss-sharing' ); ?>
		</p>
		<?php
	}

	/**
	 * Render Member OG Title Template field.
	 *
	 * @since 1.0.0
	 */
	public function render_member_og_title_template_field() {
		$default_template = '{author_name} | {site_title}';
		$value            = bp_get_option( 'buddyboss_member_og_title_template', '' );
		$display_value    = ! empty( $value ) ? $value : $default_template;
		?>
		<input type="text" name="buddyboss_member_og_title_template" id="buddyboss_member_og_title_template" value="<?php echo esc_attr( $display_value ); ?>" class="large-text" placeholder="<?php echo esc_attr( $default_template ); ?>" />
		<p class="description">
			<?php esc_html_e( 'Template for member profile Open Graph titles. Use the tags below to dynamically insert member data.', 'buddyboss-sharing' ); ?>
		</p>
		<div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px;">
			<p style="margin: 0 0 10px 0; font-weight: 600;"><?php esc_html_e( 'Available Tags:', 'buddyboss-sharing' ); ?></p>
			<ul style="margin: 0; padding-left: 20px; list-style: disc;">
				<li><code>{author_name}</code> - <?php esc_html_e( 'Member display name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_first_name}</code> - <?php esc_html_e( 'Member first name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_last_name}</code> - <?php esc_html_e( 'Member last name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_bio}</code> - <?php esc_html_e( 'Member biography (limited to 60 characters)', 'buddyboss-sharing' ); ?></li>
				<li><code>{site_title}</code> - <?php esc_html_e( 'Your site name', 'buddyboss-sharing' ); ?></li>
				<li><code>{separator}</code> - <?php esc_html_e( 'Separator (displays as |)', 'buddyboss-sharing' ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render Member OG Description Template field.
	 *
	 * @since 1.0.0
	 */
	public function render_member_og_description_template_field() {
		$default_template = '{author_bio}';
		$value            = bp_get_option( 'buddyboss_member_og_description_template', '' );
		$display_value    = ! empty( $value ) ? $value : $default_template;
		?>
		<textarea name="buddyboss_member_og_description_template" id="buddyboss_member_og_description_template" rows="3" class="large-text" placeholder="<?php echo esc_attr( $default_template ); ?>"><?php echo esc_textarea( $display_value ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Template for member profile Open Graph descriptions. Use the tags below to dynamically insert member data. Optimal length is 155-300 characters.', 'buddyboss-sharing' ); ?>
		</p>
		<div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 4px;">
			<p style="margin: 0 0 10px 0; font-weight: 600;"><?php esc_html_e( 'Available Tags:', 'buddyboss-sharing' ); ?></p>
			<ul style="margin: 0; padding-left: 20px; list-style: disc;">
				<li><code>{author_name}</code> - <?php esc_html_e( 'Member display name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_first_name}</code> - <?php esc_html_e( 'Member first name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_last_name}</code> - <?php esc_html_e( 'Member last name', 'buddyboss-sharing' ); ?></li>
				<li><code>{author_bio}</code> - <?php esc_html_e( 'Member biography (limited to 300 characters)', 'buddyboss-sharing' ); ?></li>
				<li><code>{site_title}</code> - <?php esc_html_e( 'Your site name', 'buddyboss-sharing' ); ?></li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since 1.0.0
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_scripts( $hook ) {
		// Only load on BuddyBoss settings pages.
		if ( strpos( $hook, 'bp-' ) === false && strpos( $hook, 'buddyboss' ) === false ) {
			return;
		}

		// Enqueue styles.
		$css_file = is_rtl() ? 'admin-rtl.min.css' : 'admin.min.css';
		wp_enqueue_style(
			'buddyboss-sharing-admin',
			BUDDYBOSS_SHARING_PLUGIN_URL . 'assets/css/' . $css_file,
			array(),
			BUDDYBOSS_SHARING_VERSION
		);

		// Enqueue scripts.
		wp_enqueue_media();

        $admin_js_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'assets/js/admin.js' : 'assets/js/admin.min.js';
		wp_enqueue_script(
			'buddyboss-sharing-admin',
			BUDDYBOSS_SHARING_PLUGIN_URL . $admin_js_file,
			array( 'jquery', 'wp-i18n' ),
			BUDDYBOSS_SHARING_VERSION,
			true
		);

		// Localize script.
		wp_localize_script(
			'buddyboss-sharing-admin',
			'buddybossSeoAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'buddyboss_seo_admin' ),
				'i18n'    => array(
					'uploadImage'   => esc_html__( 'Upload Image', 'buddyboss-sharing' ),
					'selectImage'   => esc_html__( 'Select Image', 'buddyboss-sharing' ),
					'remove'        => esc_html__( 'Remove', 'buddyboss-sharing' ),
					'settingsSaved' => esc_html__( 'Settings saved successfully!', 'buddyboss-sharing' ),
					'error'         => esc_html__( 'An error occurred. Please try again.', 'buddyboss-sharing' ),
				),
			)
		);

	}

	/**
	 * Show license notice in admin.
	 *
	 * @since 1.0.0
	 */
	public function show_license_notice() {
		// Only show on BuddyBoss settings pages.
		$screen = get_current_screen();
		if ( ! $screen || ( strpos( $screen->id, 'bp-' ) === false && strpos( $screen->id, 'buddyboss' ) === false ) ) {
			return;
		}

		$license_manager = \BuddyBoss\Sharing\Core\License_Manager::instance();

		// Don't show on staging servers.
		if ( function_exists( 'bb_pro_check_staging_server' ) && bb_pro_check_staging_server() ) {
			return;
		}

		// Show notice if license is invalid.
		if ( ! $license_manager->is_license_valid() ) {
			$message = $license_manager->get_license_status_message();
			$upgrade_url = $license_manager->get_upgrade_url();
			?>
			<div class="notice notice-error is-dismissible buddyboss-sharing-license-notice">
				<p>
					<strong><?php esc_html_e( 'BuddyBoss Sharing:', 'buddyboss-sharing' ); ?></strong>
					<?php echo esc_html( $message ); ?>
				</p>
				<p>
					<a href="<?php echo esc_url( $upgrade_url ); ?>" class="button button-primary" target="_blank">
						<?php esc_html_e( 'Get BuddyBoss Pro', 'buddyboss-sharing' ); ?>
					</a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=buddyboss-license' ) ); ?>" class="button">
						<?php esc_html_e( 'Activate License', 'buddyboss-sharing' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
	}

}

