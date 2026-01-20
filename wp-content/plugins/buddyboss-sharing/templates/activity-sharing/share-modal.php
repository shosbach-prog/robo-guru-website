<?php
/**
 * Activity Sharing Modal template.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get individual settings.
$enable_custom_msg = bp_get_option( 'buddyboss_activity_sharing_custom_message', 1 );
$share_to_groups   = bp_get_option( 'buddyboss_activity_sharing_to_groups', 1 );
$share_to_friends  = bp_get_option( 'buddyboss_activity_sharing_to_friends', 1 );
$share_to_message  = bp_get_option( 'buddyboss_activity_sharing_to_message', 1 );
$share_as_link     = bp_get_option( 'buddyboss_activity_sharing_as_link', 1 );
$link_platforms    = bp_get_option( 'buddyboss_activity_sharing_link_platforms', array( 'messenger', 'whatsapp', 'facebook', 'twitter', 'linkedin' ) );
?>

<!-- Share Options Dropdown (shows on share button click) -->
<div id="buddyboss-share-dropdown" class="buddyboss-share-dropdown" style="display: none;">
	<div class="share-dropdown-menu">
		<button type="button" class="share-dropdown-item" data-share-type="feed">
			<i class="bb-icon-l bb-icon-pencil"></i>
			<span><?php esc_html_e( 'Share to Activity Feed', 'buddyboss-sharing' ); ?></span>
		</button>

		<?php if ( $share_to_groups && bp_is_active( 'groups' ) ) : ?>
			<button type="button" class="share-dropdown-item" data-share-type="group">
				<i class="bb-icon-l bb-icon-users"></i>
				<span><?php esc_html_e( 'Share to Group', 'buddyboss-sharing' ); ?></span>
			</button>
		<?php endif; ?>

		<?php if ( $share_to_friends && bp_is_active( 'friends' ) ) : ?>
			<button type="button" class="share-dropdown-item" data-share-type="profile">
				<i class="bb-icon-l bb-icon-user"></i>
				<span><?php esc_html_e( "Share to Friend's Profile", 'buddyboss-sharing' ); ?></span>
			</button>
		<?php endif; ?>

		<?php if ( $share_to_message && bp_is_active( 'messages' ) ) : ?>
			<button type="button" class="share-dropdown-item" data-share-type="message">
				<i class="bb-icon-l bb-icon-envelope"></i>
				<span><?php esc_html_e( 'Share to Message', 'buddyboss-sharing' ); ?></span>
			</button>
		<?php endif; ?>

		<?php if ( $share_as_link ) : ?>
			<button type="button" class="share-dropdown-item" data-share-type="link">
				<i class="bb-icon-l bb-icon-link"></i>
				<span><?php esc_html_e( 'Share as Link', 'buddyboss-sharing' ); ?></span>
			</button>
		<?php endif; ?>
	</div>
</div>

<!-- Share Modal (for selecting group/friend and composing post) -->
<div id="buddyboss-share-modal" class="buddyboss-modal" style="display: none;">
	<div class="buddyboss-modal-overlay"></div>
	<div class="buddyboss-modal-container bb-share-modal">
		<div class="buddyboss-modal-header bb-share-modal-header">
			<h3 class="buddyboss-modal-title"><?php esc_html_e( 'Create a post', 'buddyboss-sharing' ); ?></h3>
			<button type="button" class="buddyboss-modal-close bb-share-modal-close">
				<i class="bb-icon-l bb-icon-times"></i>
			</button>
		</div>

		<div class="buddyboss-modal-body">
			<div class="share-content-area">
				<!-- Dynamic content loaded here -->
			</div>
		</div>
	</div>
</div>

<!-- Share as Link Modal -->
<div id="buddyboss-share-link-modal" class="buddyboss-modal buddyboss-share-link-modal" style="display: none;">
	<div class="buddyboss-modal-overlay"></div>
	<div class="buddyboss-modal-container bb-share-modal">
		<div class="buddyboss-modal-header bb-share-modal-header">
			<h3><?php esc_html_e( 'Share link', 'buddyboss-sharing' ); ?></h3>
			<button type="button" class="buddyboss-modal-close bb-share-modal-close">
				<i class="bb-icon-l bb-icon-times"></i>
			</button>
		</div>

		<div class="buddyboss-modal-body">
			<div class="share-link-url-container">
				<input type="text" class="share-link-url-input" readonly value="" />
				<button type="button" class="share-link-copy-btn">
					<i class="bb-icon-l bb-icon-copy"></i>
					<span><?php esc_html_e( 'Copy', 'buddyboss-sharing' ); ?></span>
				</button>
			</div>

			<div class="share-link-platforms">
				<?php
				$platform_icons = array(
					'messenger' => 'messenger.svg',
					'whatsapp'  => 'whatsapp.svg',
					'facebook'  => 'facebook.svg',
					'twitter'   => 'twitter.svg',
					'linkedin'  => 'linkedin.svg',
				);

				$platform_labels = array(
					'messenger' => esc_html__( 'Messenger', 'buddyboss-sharing' ),
					'whatsapp'  => esc_html__( 'WhatsApp', 'buddyboss-sharing' ),
					'facebook'  => esc_html__( 'Facebook', 'buddyboss-sharing' ),
					'twitter'   => esc_html__( 'X', 'buddyboss-sharing' ),
					'linkedin'  => esc_html__( 'LinkedIn', 'buddyboss-sharing' ),
				);

				foreach ( $link_platforms as $platform ) :
					if ( isset( $platform_icons[ $platform ] ) ) :
						$icon_url = BUDDYBOSS_SHARING_PLUGIN_URL . 'assets/images/social-icons/' . $platform_icons[ $platform ];
						?>
						<button type="button" class="share-platform-btn" data-platform="<?php echo esc_attr( $platform ); ?>" title="<?php echo esc_attr( $platform_labels[ $platform ] ); ?>">
							<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $platform_labels[ $platform ] ); ?>" class="platform-icon" />
							<span class="platform-label"><?php echo esc_html( $platform_labels[ $platform ] ); ?></span>
						</button>
						<?php
					endif;
				endforeach;
				?>
			</div>
		</div>
	</div>
</div>
