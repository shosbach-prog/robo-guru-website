<?php
/**
 * Template for Share to Activity Feed Modal
 *
 * @package BuddyBoss\SEO
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Check if custom message is enabled.
$enable_custom_message = get_option( 'buddyboss_activity_sharing_custom_message', 1 );
?>

<div id="buddyboss-share-activity-modal" class="buddyboss-share-modal" style="display: none;">
	<div class="buddyboss-share-modal-overlay"></div>
	<div class="buddyboss-share-modal-container">
		<div class="buddyboss-share-modal-content">
			<!-- Modal Header -->
			<div class="buddyboss-share-modal-header">
				<h3><?php esc_html_e( 'Create a post', 'buddyboss-sharing' ); ?></h3>
				<button type="button" class="buddyboss-share-modal-close">
					<i class="bb-icon-l bb-icon-times"></i>
				</button>
			</div>

			<!-- Modal Body -->
			<div class="buddyboss-share-modal-body">
				<!-- User Info & Privacy -->
				<div class="share-activity-header">
					<div class="share-activity-avatar">
						<?php echo get_avatar( bp_loggedin_user_id(), 50 ); ?>
					</div>
					<div class="share-activity-user-info">
						<div class="share-activity-user-name">
							<?php echo esc_html( bp_get_loggedin_user_fullname() ); ?>
						</div>
						<div class="share-activity-privacy">
							<select id="share-activity-privacy" class="share-activity-privacy-select">
								<option value="public"><?php esc_html_e( 'Public', 'buddyboss-sharing' ); ?></option>
								<option value="loggedin"><?php esc_html_e( 'All Members', 'buddyboss-sharing' ); ?></option>
								<option value="friends"><?php esc_html_e( 'My Connections', 'buddyboss-sharing' ); ?></option>
								<option value="onlyme"><?php esc_html_e( 'Only Me', 'buddyboss-sharing' ); ?></option>
							</select>
						</div>
					</div>
				</div>

				<?php if ( $enable_custom_message ) : ?>
					<!-- Custom Message Textarea -->
					<div class="share-activity-message" id="share-activity-message-wrapper">
						<textarea
							id="share-activity-message"
							class="share-activity-textarea"
							placeholder="<?php esc_attr_e( 'Write something about this...', 'buddyboss-sharing' ); ?>"
							rows="3"
						></textarea>
					</div>
				<?php endif; ?>

				<!-- Original Activity Preview -->
				<div class="share-activity-preview" id="share-activity-preview">
					<!-- Original activity content will be loaded here via JavaScript -->
				</div>
			</div>

			<!-- Modal Footer -->
			<div class="buddyboss-share-modal-footer">
				<button type="button" class="button share-activity-cancel">
					<?php esc_html_e( 'Cancel', 'buddyboss-sharing' ); ?>
				</button>
				<button type="button" class="button share-activity-submit" id="share-activity-submit">
					<?php esc_html_e( 'Post', 'buddyboss-sharing' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
<?php
