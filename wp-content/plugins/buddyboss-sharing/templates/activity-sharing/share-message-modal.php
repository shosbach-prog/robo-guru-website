<?php
/**
 * Share to Message Modal Template
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Variables passed from parent context.
if ( ! isset( $activity ) ) {
	return;
}
?>

<div class="buddyboss-share-message-modal bb-share-modal">
	<div class="bb-share-modal-header">
		<h3><?php esc_html_e( 'Send to', 'buddyboss-sharing' ); ?></h3>
		<button type="button" class="share-message-close bb-share-modal-close">
			<i class="bb-icon-l bb-icon-times"></i>
		</button>
	</div>

	<div class="share-message-search bb-share-modal-search">
		<i class="bb-icon-l bb-icon-search"></i>
		<input
			type="text"
			class="share-message-search-input"
			placeholder="<?php esc_attr_e( 'Search for members', 'buddyboss-sharing' ); ?>"
			autocomplete="off"
		/>
	</div>

	<div class="share-message-content-wrapper bb-share-modal-content">
		<!-- Selected Recipients Display Area -->
		<div class="share-message-selected-container" style="display: none;">
			<!-- Selected recipients will appear here as chips -->
		</div>

		<div class="share-message-recent-label bb-share-modal-label">
			<?php esc_html_e( 'Recent', 'buddyboss-sharing' ); ?>
		</div>

		<div class="share-message-members-list">
			<div class="share-message-loading" style="display: none; text-align: center; padding: 24px; color: #8c8f94;">
				<span class="spinner is-active" style="float: none; margin: 0 auto;"></span>
				<p style="margin-top: 8px;"><?php esc_html_e( 'Loading...', 'buddyboss-sharing' ); ?></p>
			</div>
			<div class="share-message-results">
				<!-- Recent threads will be loaded via AJAX -->
			</div>
		</div>

		<?php if ( $enable_custom_msg ) : ?>
		<div class="share-message-textarea-wrapper">
			<textarea
				class="share-message-textarea"
				placeholder="<?php esc_attr_e( 'Add additional message...', 'buddyboss-sharing' ); ?>"
				rows="1"
			></textarea>
		</div>
		<?php endif; ?>
	</div>

	<div class="share-message-actions">
		<button type="button" class="button button-primary share-message-send-btn">
			<i class="bb-icon-f bb-icon-envelope"></i>
			<?php esc_html_e( 'Send', 'buddyboss-sharing' ); ?>
		</button>
	</div>

	<!-- Hidden field to store activity ID -->
	<input type="hidden" id="share-message-activity-id" value="<?php echo esc_attr( $activity->id ); ?>" />
</div>
