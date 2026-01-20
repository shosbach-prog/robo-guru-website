<?php
/**
 * Share to Group Selection Modal Template
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

<div class="buddyboss-share-group-modal bb-share-modal">
	<div class="bb-share-modal-header">
		<h3><?php esc_html_e( 'Share to a group', 'buddyboss-sharing' ); ?></h3>
		<button type="button" class="share-group-close bb-share-modal-close">
			<i class="bb-icon-l bb-icon-times"></i>
		</button>
	</div>

	<div class="share-group-search bb-share-modal-search">
		<i class="bb-icon-l bb-icon-search"></i>
		<input
			type="text"
			class="share-group-search-input"
			placeholder="<?php esc_attr_e( 'Search for groups', 'buddyboss-sharing' ); ?>"
			autocomplete="off"
		/>
	</div>

	<div class="share-group-content bb-share-modal-content">
		<div class="share-group-label bb-share-modal-label">
			<?php esc_html_e( 'All Groups', 'buddyboss-sharing' ); ?>
		</div>

		<div class="share-group-list">
			<div class="share-group-loading" style="display: none;">
				<span class="spinner is-active"></span>
				<p><?php esc_html_e( 'Loading...', 'buddyboss-sharing' ); ?></p>
			</div>
			<div class="share-group-results">
				<!-- Groups will be loaded via AJAX -->
			</div>
		</div>
	</div>

	<!-- Hidden field to store activity ID -->
	<input type="hidden" id="share-group-activity-id" value="<?php echo esc_attr( $activity->id ); ?>" />
</div>
