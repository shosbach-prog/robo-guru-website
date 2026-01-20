<?php
/**
 * Activity Sharing Modal Content template.
 *
 *  BuddyBoss_Sharing
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Variables passed from parent context.
if ( ! isset( $activity, $current_user, $share_type, $enable_custom_msg ) ) {
	return;
}

$user_display_name = bp_core_get_user_displayname( get_current_user_id() );
$user_avatar       = get_avatar( get_current_user_id(), 48 );
?>

<div class="share-form">
	<?php if ( $enable_custom_msg || 'feed' === $share_type ) : ?>
		<div class="share-post-form">
			<div class="share-user-header">
				<div class="share-user-avatar">
					<?php echo $user_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="share-user-info">
					<div class="user-name"><?php echo esc_html( $user_display_name ); ?></div>
					<div class="share-privacy-selector">
						<button type="button" class="privacy-dropdown-btn">
							<i class="bb-icon-l bb-icon-globe"></i>
							<span class="privacy-label"><?php esc_html_e( 'Public', 'buddyboss-sharing' ); ?></span>
							<i class="bb-icon-l bb-icon-angle-down"></i>
						</button>
					</div>
				</div>
			</div>
			<textarea
				class="share-custom-message"
				placeholder="<?php
				// Translators: %s is the user's display name.
				echo esc_attr( sprintf( __( 'Share what\'s on your mind, %s...', 'buddyboss-sharing' ), $user_display_name ) ); ?>"
			></textarea>
		</div>
	<?php endif; ?>

	<?php if ( 'group' === $share_type ) : ?>
		<div class="share-target-selector">
			<h4><?php esc_html_e( 'Share to a group', 'buddyboss-sharing' ); ?></h4>
			<input type="text" class="share-search-input" placeholder="<?php esc_attr_e( 'Search groups...', 'buddyboss-sharing' ); ?>" />
			<div class="share-target-list">
				<?php
				$groups = groups_get_groups( array(
					'user_id'  => get_current_user_id(),
					'per_page' => 20,
				) );

				if ( ! empty( $groups['groups'] ) ) :
					foreach ( $groups['groups'] as $group ) :
						?>
						<label class="share-target-item">
							<input type="radio" name="share_target" value="<?php echo esc_attr( $group->id ); ?>" />
							<span class="target-avatar">
								<?php echo bp_core_fetch_avatar( array(
									'item_id' => $group->id,
									'object'  => 'group',
									'type'    => 'thumb',
								) ); ?>
							</span>
							<span class="target-name"><?php echo esc_html( $group->name ); ?></span>
							<span class="target-meta"><?php echo esc_html( $group->status ); ?></span>
						</label>
						<?php
					endforeach;
				else :
					?>
					<p class="no-results"><?php esc_html_e( 'No groups found.', 'buddyboss-sharing' ); ?></p>
					<?php
				endif;
				?>
			</div>
		</div>
	<?php elseif ( 'profile' === $share_type ) : ?>
		<div class="share-target-selector">
			<h4><?php esc_html_e( "Share to friend's profile", 'buddyboss-sharing' ); ?></h4>
			<input type="text" class="share-search-input" placeholder="<?php esc_attr_e( 'Search friends...', 'buddyboss-sharing' ); ?>" />
			<div class="share-target-list">
				<?php
				// Get friends using BuddyPress/BuddyBoss function
				$friend_ids = array();
				if ( bp_is_active( 'friends' ) && function_exists( 'friends_get_friend_user_ids' ) ) {
					$friend_ids = friends_get_friend_user_ids( get_current_user_id() );
				}

				if ( ! empty( $friend_ids ) ) :
					// Limit to 20 friends
					$friend_ids = array_slice( $friend_ids, 0, 20 );
					foreach ( $friend_ids as $friend_id ) :
						?>
						<label class="share-target-item">
							<input type="radio" name="share_target" value="<?php echo esc_attr( $friend_id ); ?>" />
							<span class="target-avatar">
								<?php echo get_avatar( $friend_id, 40 ); ?>
							</span>
							<span class="target-name"><?php echo esc_html( bp_core_get_user_displayname( $friend_id ) ); ?></span>
						</label>
						<?php
					endforeach;
				else :
					?>
					<p class="no-results"><?php esc_html_e( 'No friends found.', 'buddyboss-sharing' ); ?></p>
					<?php
				endif;
				?>
			</div>
		</div>
	<?php elseif ( 'message' === $share_type ) : ?>
		<div class="share-message-selector">
			<h4><?php esc_html_e( 'Select recipients', 'buddyboss-sharing' ); ?></h4>
			<input type="text" class="share-search-input" placeholder="<?php esc_attr_e( 'Search members...', 'buddyboss-sharing' ); ?>" />
			<div class="share-target-list share-message-members">
				<?php
				// Get all site members (not just friends)
				$members = bp_core_get_users( array(
					'type'     => 'active',
					'per_page' => 50,
					'exclude'  => get_current_user_id(), // Exclude current user
				) );

				if ( ! empty( $members['users'] ) ) :
					foreach ( $members['users'] as $member ) :
						?>
						<label class="share-target-item share-message-member">
							<input type="checkbox" name="share_target[]" value="<?php echo esc_attr( $member->ID ); ?>" />
							<span class="target-avatar">
								<?php echo get_avatar( $member->ID, 40 ); ?>
							</span>
							<span class="target-name"><?php echo esc_html( bp_core_get_user_displayname( $member->ID ) ); ?></span>
						</label>
						<?php
					endforeach;
				else :
					?>
					<p class="no-results"><?php esc_html_e( 'No members found.', 'buddyboss-sharing' ); ?></p>
					<?php
				endif;
				?>
			</div>

			<!-- Message textarea -->
			<div class="share-message-text">
				<label for="share-message-content"><?php esc_html_e( 'Add a message (optional)', 'buddyboss-sharing' ); ?></label>
				<textarea
					id="share-message-content"
					class="share-custom-message share-message-textarea"
					placeholder="<?php esc_attr_e( 'Write your message here...', 'buddyboss-sharing' ); ?>"
					rows="4"
				></textarea>
			</div>
		</div>
	<?php elseif ( 'link' === $share_type ) : ?>
		<div class="share-link-platforms">
			<?php
			$link_platforms = bp_get_option( 'buddyboss_activity_sharing_link_platforms', array( 'messenger', 'whatsapp', 'facebook', 'twitter', 'linkedin' ) );

			$platform_icons = array(
				'messenger' => 'bb-icon-facebook-messenger',
				'whatsapp'  => 'bb-icon-whatsapp',
				'facebook'  => 'bb-icon-facebook',
				'twitter'   => 'bb-icon-twitter',
				'linkedin'  => 'bb-icon-linkedin',
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
					?>
					<button type="button" class="share-platform-btn" data-platform="<?php echo esc_attr( $platform ); ?>">
						<i class="bb-icon-l <?php echo esc_attr( $platform_icons[ $platform ] ); ?>"></i>
						<span><?php echo esc_html( $platform_labels[ $platform ] ); ?></span>
					</button>
					<?php
				endif;
			endforeach;
			?>
		</div>
	<?php endif; ?>

	<!-- Original Activity Preview -->
	<div class="shared-activity-preview">
		<div class="activity-header">
			<div class="activity-avatar">
				<?php echo get_avatar( $activity->user_id, 40 ); ?>
			</div>
			<div class="activity-meta">
				<div class="activity-author"><?php echo esc_html( bp_core_get_user_displayname( $activity->user_id ) ); ?></div>
				<div class="activity-time">
					<?php
					// Translators: %s is time ago.
					echo esc_html( sprintf( __( 'posted an update · %s', 'buddyboss-sharing' ), human_time_diff( strtotime( $activity->date_recorded ) ) ) );
					?>
				</div>
			</div>
		</div>
		<div class="activity-content">
			<?php
			// Display text content.
			if ( ! empty( $activity->content ) ) {
				echo wp_kses_post( $activity->content );
			}

			// Check for media (photos, videos, documents).
			if ( function_exists( 'bp_is_active' ) && bp_is_active( 'media' ) ) {
				$media_ids = bp_activity_get_meta( $activity->id, 'bp_media_ids', true );
				if ( ! empty( $media_ids ) && is_array( $media_ids ) ) {
					?>
					<div class="activity-media-wrap">
						<div class="activity-media-grid activity-media-grid-<?php echo count( $media_ids ) > 1 ? 'multiple' : 'single'; ?>">
							<?php
							foreach ( $media_ids as $media_id ) {
								if ( function_exists( 'bp_get_media' ) ) {
									$media = bp_get_media( $media_id );
									if ( $media ) {
										$attachment_url = wp_get_attachment_url( $media->attachment_id );
										$is_video       = strpos( $media->type, 'video' ) !== false;
										?>
										<div class="activity-media-item">
											<?php if ( $is_video ) : ?>
												<video controls>
													<source src="<?php echo esc_url( $attachment_url ); ?>" type="<?php echo esc_attr( $media->type ); ?>">
												</video>
											<?php else : ?>
												<img src="<?php echo esc_url( $attachment_url ); ?>" alt="<?php echo esc_attr( $media->title ); ?>" />
											<?php endif; ?>
										</div>
										<?php
									}
								}
							}
							?>
						</div>
					</div>
					<?php
				}
			}

			// Check for document attachments.
			if ( function_exists( 'bp_is_active' ) && bp_is_active( 'media' ) ) {
				$document_ids = bp_activity_get_meta( $activity->id, 'bp_document_ids', true );
				if ( ! empty( $document_ids ) && is_array( $document_ids ) ) {
					?>
					<div class="activity-document-wrap">
						<?php
						foreach ( $document_ids as $document_id ) {
							if ( function_exists( 'bp_get_document' ) ) {
								$document = bp_get_document( $document_id );
								if ( $document ) {
									?>
									<div class="activity-document-item">
										<i class="bb-icon-l bb-icon-file"></i>
										<span class="document-name"><?php echo esc_html( $document->title ); ?></span>
									</div>
									<?php
								}
							}
						}
						?>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>

	<?php if ( 'link' !== $share_type ) : ?>
		<div class="share-form-actions">
			<button type="button" class="button button-primary share-submit-btn">
				<i class="bb-icon-clock"></i>
				<?php esc_html_e( 'Post', 'buddyboss-sharing' ); ?>
			</button>
		</div>
	<?php endif; ?>
</div>
