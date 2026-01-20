<?php
/**
 * Template for displaying "no access" message when user cannot view activity
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="buddyboss-activity-no-access">
	<div class="no-access-container">
		<div class="no-access-icon">
			<?php
			$icon_class = ( function_exists( 'bb_is_readylaunch_enabled' ) && bb_is_readylaunch_enabled() ) 
				? 'bb-icons-rl-eye-slash' 
				: 'bb-icon-l bb-icon-eye-slash';
			?>
			<i class="<?php echo esc_attr( $icon_class ); ?>"></i>
		</div>
		<div class="no-access-content">
			<h5 class="no-access-title">
				<?php esc_html_e( "This content isn't available right now", 'buddyboss-sharing' ); ?>
			</h5>
			<p class="no-access-description">
				<?php esc_html_e( "When this happens, it's usually because the owner changed who can see it or it's been deleted.", 'buddyboss-sharing' ); ?>
			</p>
		</div>
	</div>
</div>
