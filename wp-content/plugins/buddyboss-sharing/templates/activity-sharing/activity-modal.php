<?php
/**
 * Template for displaying shared activity in modal
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="bb-shared-activity-modal" class="bb-shared-activity-modal" style="display: none;">
	<div class="bb-shared-activity-modal-overlay"></div>
	<div class="bb-shared-activity-modal-container">
		<div class="bb-shared-activity-modal-header">
			<h2 class="bb-shared-activity-modal-title"><?php esc_html_e( 'Post', 'buddyboss-sharing' ); ?></h2>
			<button type="button" class="bb-shared-activity-modal-close" aria-label="<?php esc_attr_e( 'Close', 'buddyboss-sharing' ); ?>">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</button>
		</div>
		<div class="bb-shared-activity-modal-content">
			<div class="bb-shared-activity-modal-loading">
				<div class="bb-loading-spinner"></div>
				<p><?php esc_html_e( 'Loading...', 'buddyboss-sharing' ); ?></p>
			</div>
			<div class="bb-shared-activity-modal-body"></div>
		</div>
	</div>
</div>
