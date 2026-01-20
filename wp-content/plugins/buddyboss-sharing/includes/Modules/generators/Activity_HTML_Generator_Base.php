<?php
/**
 * Activity HTML Generator - Base Class.
 *
 * This abstract class contains shared logic for generating HTML for shared activities.
 * Concrete implementations provide platform-specific CSS class prefixes.
 *
 * @package BuddyBoss\Sharing\Modules\Generators
 * @since 1.0.0
 */

namespace BuddyBoss\Sharing\Modules\Generators;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activity_HTML_Generator_Base abstract class.
 *
 * Base class containing shared HTML generation logic.
 * Subclasses must implement get_class_prefix() to provide platform-specific prefixes.
 *
 * @since 1.0.0
 */
abstract class Activity_HTML_Generator_Base {

	/**
	 * Get the CSS class prefix for this generator.
	 *
	 * @since 1.0.0
	 * @return string CSS class prefix (e.g., 'bb-' or 'bb-rl-').
	 */
	abstract protected function get_class_prefix();

	/**
	 * Get the CSS class prefix for specific elements.
	 *
	 * Used for elements that need a different prefix than the main class prefix.
	 *
	 * @since 1.0.0
	 * @return string CSS class prefix for elements (e.g., 'bb-rl-' for ReadyLaunch or '' for regular).
	 */
	abstract protected function get_element_class_prefix();

	/**
	 * Generate HTML for shared activity in messages (compact version).
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @return string Generated HTML.
	 */
	public function generate_shared_activity_html_for_message( $activity ) {
		$user = get_userdata( $activity->user_id );

		if ( ! $user ) {
			return '';
		}

		$html  = '<div class="shared-activity-wrapper shared-activity-message" data-activity-id="' . esc_attr( $activity->id ) . '">';
		$html .= '<div class="shared-activity-preview" data-activity-id="' . esc_attr( $activity->id ) . '">';

		// Generate media section (video > featured image > media > documents).
		$html .= $this->generate_media_section( $activity, $user, true );

		// Process embeds using BuddyBoss Platform's embed instance.
		if ( function_exists( 'buddypress' ) && isset( buddypress()->embed ) ) {
			$html = buddypress()->embed->autoembed( $html, $activity, false );
		}

		// Generate link preview if available.
		$link_preview_html = $this->generate_link_preview_html( $activity );
		if ( ! empty( $link_preview_html ) ) {
			$html .= $link_preview_html;
		}

		// Compact footer with user info (after media).
		$html .= $this->generate_compact_footer( $activity, $user );

		$html .= '</div>'; // .shared-activity-preview
		$html .= '</div>'; // .shared-activity-wrapper

		return $html;
	}

	public function keep_only_iframe($content) {
		// Extract only iframe tags
		preg_match_all('/<iframe\b[^>]*>.*?<\/iframe>/is', $content, $matches);

		// If any iframe found, return them joined together
		if (!empty($matches[0])) {
			return implode("\n", $matches[0]);
		}

		// Otherwise return empty string
		return '';
	}

	/**
	 * Generate HTML for shared activity (full version).
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param bool   $for_message Whether this is for a message context.
	 * @return string Generated HTML.
	 */
	public function generate_shared_activity_html( $activity, $for_message = false ) {
		// Use compact version for messages.
		if ( $for_message ) {
			return $this->generate_shared_activity_html_for_message( $activity );
		}

		$user = get_userdata( $activity->user_id );

		if ( ! $user ) {
			return '';
		}

		$html  = '<div class="shared-activity-wrapper" data-activity-id="' . esc_attr( $activity->id ) . '">';
		$html .= '<div class="shared-activity-preview" data-activity-id="' . esc_attr( $activity->id ) . '">';

		// Generate media section (returns array with HTML and document HTML).
		$media_result = $this->generate_media_section_with_documents( $activity, $user );
		$html .= $media_result['media_html'];

		// Generate link preview if available (should come before header so image is at top).
		$link_preview_html = $this->generate_link_preview_html( $activity );
		if ( ! empty( $link_preview_html ) ) {
			$html .= $link_preview_html;
		}

		// Generate header with user info and activity action.
		$html .= $this->generate_header_section( $activity, $user );

		// Generate content section.
		$html .= $this->generate_content_section( $activity, $media_result['has_media_displayed'] );

		// Process embeds using BuddyBoss Platform's embed instance.
		if ( function_exists( 'buddypress' ) && isset( buddypress()->embed ) ) {
			$html .= $this->keep_only_iframe( buddypress()->embed->autoembed( $activity->content, $activity, false ) );
		}

		// Add document HTML after content (if any).
		if ( ! empty( $media_result['document_html'] ) ) {
			$html .= $media_result['document_html'];
		}

		$html .= '</div>'; // .shared-activity-preview
		$html .= '</div>'; // .shared-activity-wrapper

		return $html;
	}

	/**
	 * Generate media section HTML (for message context).
	 *
	 * Priority: Video > Featured Image > GIF > Media Images > Documents
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param object $user User object.
	 * @param bool   $compact Whether to generate compact version.
	 * @return string Media HTML.
	 */
	protected function generate_media_section( $activity, $user, $compact = false ) {
		$html = '';
		$has_media_displayed = false;

		// Get media IDs.
		$video_ids    = bp_activity_get_meta( $activity->id, 'bp_video_ids', true );
		$media_ids    = bp_activity_get_meta( $activity->id, 'bp_media_ids', true );
		$document_ids = bp_activity_get_meta( $activity->id, 'bp_document_ids', true );

		// Get featured image.
		$feature_image_data = null;
		if ( function_exists( 'bb_pro_activity_post_feature_image_instance' ) &&
			bb_pro_activity_post_feature_image_instance() &&
			method_exists( bb_pro_activity_post_feature_image_instance(), 'bb_get_feature_image_data' )
		) {
			$feature_image_data = bb_pro_activity_post_feature_image_instance()->bb_get_feature_image_data( $activity->id );
		}

		// Priority 1: Video.
		if ( ! $has_media_displayed && ! empty( $video_ids ) ) {
			$video_html = $this->generate_video_html( $activity, $user, $video_ids );
			if ( ! empty( $video_html ) ) {
				$html .= $video_html;
				$has_media_displayed = true;
			}
		}

		// Priority 2: Featured Image.
		if ( ! $has_media_displayed && ! empty( $feature_image_data ) ) {
			$html .= '<div class="shared-activity-media shared-activity-featured-image">';
			$html .= '<img src="' . esc_url( $feature_image_data['url'] ) . '" alt="' . esc_attr( $feature_image_data['title'] ) . '" class="activity-feature-image-media">';
			$html .= '</div>';
			$has_media_displayed = true;
		}

		// Priority 3: GIF.
		if ( ! $has_media_displayed ) {
			$gif_html = $this->generate_gif_html( $activity );
			if ( ! empty( $gif_html ) ) {
				$html .= $gif_html;
				$has_media_displayed = true;
			}
		}

		// Priority 4: Media Images.
		if ( ! $has_media_displayed && ! empty( $media_ids ) ) {
			$media_html = $this->generate_media_images_html( $activity, $user, $media_ids );
			if ( ! empty( $media_html ) ) {
				$html .= $media_html;
				$has_media_displayed = true;
			}
		}

		// Priority 5: Documents (only if no other media displayed).
		if ( ! $has_media_displayed && ! empty( $document_ids ) ) {
			$document_html = $this->generate_documents_html( $activity, $user, $document_ids );
			if ( ! empty( $document_html ) ) {
				$html .= $document_html;
				$has_media_displayed = true;
			}
		}

		return $html;
	}

	/**
	 * Generate media section with separate document HTML (for full context).
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param object $user User object.
	 * @return array Array with 'media_html', 'document_html', and 'has_media_displayed'.
	 */
	protected function generate_media_section_with_documents( $activity, $user ) {
		$media_html = '';
		$document_html = '';
		$has_media_displayed = false;

		// Get media IDs.
		$video_ids    = bp_activity_get_meta( $activity->id, 'bp_video_ids', true );
		$media_ids    = bp_activity_get_meta( $activity->id, 'bp_media_ids', true );
		$document_ids = bp_activity_get_meta( $activity->id, 'bp_document_ids', true );

		// Get featured image.
		$feature_image_data = null;
		if ( function_exists( 'bb_pro_activity_post_feature_image_instance' ) &&
			bb_pro_activity_post_feature_image_instance() &&
			method_exists( bb_pro_activity_post_feature_image_instance(), 'bb_get_feature_image_data' )
		) {
			$feature_image_data = bb_pro_activity_post_feature_image_instance()->bb_get_feature_image_data( $activity->id );
		}

		// Priority 1: Video.
		if ( ! $has_media_displayed && ! empty( $video_ids ) ) {
			$video_html = $this->generate_video_html( $activity, $user, $video_ids );
			if ( ! empty( $video_html ) ) {
				$media_html .= $video_html;
				$has_media_displayed = true;
			}
		}

		// Priority 2: Featured Image.
		if ( ! $has_media_displayed && ! empty( $feature_image_data ) ) {
			$media_html .= '<div class="shared-activity-media shared-activity-featured-image">';
			$media_html .= '<img src="' . esc_url( $feature_image_data['url'] ) . '" alt="' . esc_attr( $feature_image_data['title'] ) . '" class="activity-feature-image-media">';
			$media_html .= '</div>';
			$has_media_displayed = true;
		}

		// Priority 3: GIF.
		if ( ! $has_media_displayed ) {
			$gif_html = $this->generate_gif_html( $activity );
			if ( ! empty( $gif_html ) ) {
				$media_html .= $gif_html;
				$has_media_displayed = true;
			}
		}

		// Priority 4: Media Images.
		if ( ! $has_media_displayed && ! empty( $media_ids ) ) {
			$media_images_html = $this->generate_media_images_html( $activity, $user, $media_ids );
			if ( ! empty( $media_images_html ) ) {
				$media_html .= $media_images_html;
				$has_media_displayed = true;
			}
		}

		// Documents are prepared separately (added after content in full version).
		if ( ! empty( $document_ids ) ) {
			$document_html = $this->generate_documents_html( $activity, $user, $document_ids );
		}

		return array(
			'media_html'          => $media_html,
			'document_html'       => $document_html,
			'has_media_displayed' => $has_media_displayed,
		);
	}

	/**
	 * Generate GIF HTML.
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @return string GIF HTML.
	 */
	protected function generate_gif_html( $activity ) {
		// Use platform's function if available.
		if ( function_exists( 'bp_media_activity_embed_gif_content' ) ) {
			$gif_html = bp_media_activity_embed_gif_content( $activity->id );
			if ( ! empty( $gif_html ) ) {
				return $gif_html;
			}
		}

		return '';
	}

	/**
	 * Generate video HTML.
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param object $user User object.
	 * @param string $video_ids Comma-separated video IDs.
	 * @return string Video HTML.
	 */
	protected function generate_video_html( $activity, $user, $video_ids ) {
		if ( ! function_exists( 'bp_video_get_specific' ) ) {
			return '';
		}

		$video_ids_array = array_filter( array_map( 'intval', explode( ',', $video_ids ) ) );

		if ( empty( $video_ids_array ) ) {
			return '';
		}

		$video = bp_video_get_specific(
			array(
				'video_ids' => $video_ids_array,
				'order_by'  => 'menu_order',
				'sort'      => 'ASC',
			)
		);

		if ( empty( $video['videos'] ) ) {
			return '';
		}

		$video_items   = $video['videos'];
		$video_count   = count( $video_items );
		$max_length    = function_exists( 'bb_video_get_activity_max_thumb_length' ) ? bb_video_get_activity_max_thumb_length() : 3;
		$more_video    = $video_count > $max_length;
		$display_items = $more_video ? array_slice( $video_items, 0, $max_length ) : $video_items;
		$prefix        = $this->get_class_prefix();

		// Use platform's markup structure for styling.
		$html  = '<div class="bb-activity-video-wrap bb-video-length-' . esc_attr( $video_count );
		if ( $video_count > 5 ) {
			$html .= ' bb-video-length-more';
		}
		$html .= '">';

		foreach ( $display_items as $index => $video_item ) {
			$video_url       = '';
			$video_poster    = '';
			$video_type      = '';
			$attachment_full = '';
			$attachment_id   = $video_item->attachment_id;
			$width           = isset( $video_item->attachment_data->thumb_meta['width'] ) ? (int) $video_item->attachment_data->thumb_meta['width'] : 0;
			$height          = isset( $video_item->attachment_data->thumb_meta['height'] ) ? (int) $video_item->attachment_data->thumb_meta['height'] : 0;
			$is_last_visible = $more_video && ( $max_length - 1 ) === $index;
			$is_single_or_first = 1 === $video_count || ( $video_count > 1 && 0 === $index );

			// Use video_link from video object (includes access control).
			if ( ! empty( $video_item->video_link ) ) {
				$video_url = $video_item->video_link;
			}

			// Get video type/mime type.
			if ( ! empty( $video_item->attachment_data->meta->mime_type ) ) {
				$video_type = $video_item->attachment_data->meta->mime_type;
			} else {
				$video_type = 'video/mp4'; // Default fallback.
			}

			// Get poster/thumbnail URL with access control.
			if ( ! empty( $video_item->attachment_data->video_popup_thumb ) ) {
				$attachment_full = $video_item->attachment_data->video_popup_thumb;
				$video_poster    = $attachment_full;
			} elseif ( ! empty( $video_item->attachment_data->video_activity_thumb ) ) {
				$video_poster = $video_item->attachment_data->video_activity_thumb;
			} elseif ( function_exists( 'bb_video_get_thumb_url' ) ) {
				$attachment_full = bb_video_get_thumb_url(
					$video_item->id,
					$attachment_id,
					'bb-video-poster-popup-image'
				);
				$video_poster = bb_video_get_thumb_url(
					$video_item->id,
					$attachment_id,
					'bb-video-activity-image'
				);
			}

			if ( $video_url ) {
				// Build classes similar to platform's video activity-entry.php.
				$elem_classes = $prefix . 'activity-video-elem ' . esc_attr( $video_item->id ) . ' ';
				if ( $is_single_or_first ) {
					$elem_classes .= 'act-grid-1-1 ';
				}
				if ( $video_count > 1 && $index > 0 ) {
					$elem_classes .= 'act-grid-1-2 ';
				}
				if ( $width > $height ) {
					$elem_classes .= 'bb-horizontal-layout ';
				}
				if ( $height > $width || $width === $height ) {
					$elem_classes .= 'bb-vertical-layout ';
				}
				if ( $is_last_visible ) {
					$elem_classes .= ' no_more_option';
				}
				$elem_classes = trim( $elem_classes );

				// For single video, show video player; for multiple, show thumbnail.
				if ( 1 === $video_count ) {
					// Single video - show video player with Video.js setup.
					$html .= '<div class="' . esc_attr( $elem_classes ) . '" data-id="' . esc_attr( $video_item->id ) . '">';
					
					// Add video-action-wrap to match platform structure.
					$element_prefix = $this->get_element_class_prefix();
					if ( 'bb-rl-' === $element_prefix ) {
						// ReadyLaunch version uses bb-rl-more_dropdown-wrap.
						$html .= '<div class="bb-rl-more_dropdown-wrap"></div>';
					} else {
						// Regular version uses video-action-wrap item-action-wrap.
						$html .= '<div class="video-action-wrap item-action-wrap"></div>';
					}
					
					$html .= '<video';
					$html .= ' playsinline';
					$html .= ' id="video-' . esc_attr( $video_item->id ) . '"';
					$html .= ' class="video-js single-activity-video"';
					$html .= ' data-id="' . esc_attr( $video_item->id ) . '"';
					if ( $attachment_full ) {
						$html .= ' data-attachment-full="' . esc_url( $attachment_full ) . '"';
					}
					$html .= ' data-activity-id="' . esc_attr( $video_item->activity_id ) . '"';
					$html .= ' data-privacy="' . esc_attr( $video_item->privacy ) . '"';
					$html .= ' data-parent-activity-id="' . esc_attr( $video_item->activity_id ) . '"';
					$html .= ' data-album-id="' . esc_attr( $video_item->album_id ) . '"';
					$html .= ' data-group-id="' . esc_attr( $video_item->group_id ) . '"';
					$html .= ' data-attachment-id="' . esc_attr( $attachment_id ) . '"';
					$html .= ' controls';
					if ( $video_poster ) {
						$html .= ' poster="' . esc_url( $video_poster ) . '"';
					}
					$html .= ' data-setup=\'{"aspectRatio": "16:9", "fluid": true,"playbackRates": [0.5, 1, 1.5, 2], "fullscreenToggle" : false }\'';
					$html .= '>';
					$html .= '<source src="' . esc_url( $video_url ) . '" type="' . esc_attr( $video_type ) . '">';
					$html .= esc_html__( 'Your browser does not support the video tag.', 'buddyboss-sharing' );
					$html .= '</video>';
					$html .= '<p class="bb-video-loader"></p>';
					if ( ! empty( $video_item->length ) ) {
						$html .= '<p class="bb-video-duration">' . esc_html( $video_item->length ) . '</p>';
					}
					$html .= '</div>';
				} else {
					// Multiple videos - show thumbnail.
					$html .= '<div class="' . esc_attr( $elem_classes ) . '" data-id="' . esc_attr( $video_item->id ) . '">';
					$html .= '<a';
					// For ReadyLaunch, add both bb-rl-open-video-theatre and bb-open-video-theatre classes
					// platform handler listens for .bb-open-video-theatre
					$video_theatre_class = $prefix . 'open-video-theatre';
					if ( 'bb-rl-' === $prefix ) {
						$video_theatre_class .= ' bb-open-video-theatre';
					}
					$html .= ' class="' . $video_theatre_class . ' ' . $prefix . 'video-cover-wrap ' . $prefix . 'item-cover-wrap"';
					$html .= ' data-id="' . esc_attr( $video_item->id ) . '"';
					if ( $attachment_full ) {
						$html .= ' data-attachment-full="' . esc_url( $attachment_full ) . '"';
					}
					$html .= ' data-activity-id="' . esc_attr( $video_item->activity_id ) . '"';
					$html .= ' data-privacy="' . esc_attr( $video_item->privacy ) . '"';
					$html .= ' data-parent-activity-id="' . esc_attr( $video_item->activity_id ) . '"';
					$html .= ' data-album-id="' . esc_attr( $video_item->album_id ) . '"';
					$html .= ' data-group-id="' . esc_attr( $video_item->group_id ) . '"';
					$html .= ' data-attachment-id="' . esc_attr( $attachment_id ) . '"';
					$html .= ' href="#">';
					// Use lazy loading pattern like platform.
					$placeholder_url = function_exists( 'buddypress' ) ? buddypress()->plugin_url . 'bp-templates/bp-nouveau/images/video-placeholder.jpg' : '';
					if ( $placeholder_url ) {
						$html .= '<img src="' . esc_url( $placeholder_url ) . '" data-src="' . esc_url( $video_poster ) . '" alt="' . esc_attr( $user->display_name ) . '" class="lazy">';
					} else {
						$html .= '<img src="' . esc_url( $video_poster ) . '" alt="' . esc_attr( $user->display_name ) . '">';
					}

					// Show "+X More Videos" if this is the last visible item and there are more.
					if ( $is_last_visible ) {
						$remaining_count = $video_count - $max_length;
						$length_class = $prefix . 'videos-length';
						if ( 1 === $remaining_count ) {
							$html .= '<span class="' . esc_attr( $length_class ) . '"><span><strong>+' . esc_html( $remaining_count ) . '</strong> <span>' . esc_html__( 'More Video', 'buddyboss-sharing' ) . '</span></span></span>';
						} else {
							$html .= '<span class="' . esc_attr( $length_class ) . '"><span><strong>+' . esc_html( $remaining_count ) . '</strong> <span>' . esc_html__( 'More Videos', 'buddyboss-sharing' ) . '</span></span></span>';
						}
					}

					if ( ! empty( $video_item->length ) ) {
						$html .= '<p class="bb-video-duration">' . esc_html( $video_item->length ) . '</p>';
					}

					$html .= '</a>';
					$html .= '</div>';
				}
			}
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Generate media images HTML.
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param object $user User object.
	 * @param string $media_ids Comma-separated media IDs.
	 * @return string Media images HTML.
	 */
	protected function generate_media_images_html( $activity, $user, $media_ids ) {
		if ( ! function_exists( 'bp_media_get_specific' ) ) {
			return '';
		}

		$media_ids_array = array_filter( array_map( 'intval', explode( ',', $media_ids ) ) );

		if ( empty( $media_ids_array ) ) {
			return '';
		}

		$media = bp_media_get_specific(
			array(
				'media_ids' => $media_ids_array,
				'order_by'  => 'menu_order',
				'sort'      => 'ASC',
			)
		);

		if ( empty( $media['medias'] ) ) {
			return '';
		}

		$media_items   = $media['medias'];
		$media_count   = count( $media_items );
		$max_length    = function_exists( 'bb_media_get_activity_max_thumb_length' ) ? bb_media_get_activity_max_thumb_length() : 5;
		$more_media    = $media_count > $max_length;
		$display_items = $more_media ? array_slice( $media_items, 0, $max_length ) : $media_items;
		$prefix        = $this->get_class_prefix();

		// Use platform's markup structure for styling.
		$html  = '<div class="bb-activity-media-wrap bb-media-length-' . esc_attr( $media_count );
		if ( $media_count > 5 ) {
			$html .= ' bb-media-length-more';
		}
		$html .= '">';

		foreach ( $display_items as $index => $media_item ) {
			$media_thumb        = '';
			$media_popup        = '';
			$attachment_id      = $media_item->attachment_id;
			$width              = isset( $media_item->attachment_data->meta['width'] ) ? (int) $media_item->attachment_data->meta['width'] : 0;
			$height             = isset( $media_item->attachment_data->meta['height'] ) ? (int) $media_item->attachment_data->meta['height'] : 0;
			$is_last_visible    = $more_media && ( $max_length - 1 ) === $index;
			$is_single_or_first = 1 === $media_count || ( $media_count > 1 && 0 === $index );

			// Get thumbnail URL (single image uses activity-image, multiple use album-directory-image-medium).
			$size = 1 === $media_count ? 'bb-media-activity-image' : 'bb-media-photos-album-directory-image-medium';

			if ( ! empty( $media_item->attachment_data ) ) {
				// Use attachment_data if available.
				if ( 1 === $media_count && ! empty( $media_item->attachment_data->activity_thumb ) ) {
					$media_thumb = $media_item->attachment_data->activity_thumb;
				} elseif ( $media_count > 1 && ! empty( $media_item->attachment_data->media_photos_directory_page ) ) {
					$media_thumb = $media_item->attachment_data->media_photos_directory_page;
				}

				// Get popup URL for theatre.
				if ( ! empty( $media_item->attachment_data->media_theatre_popup ) ) {
					$media_popup = $media_item->attachment_data->media_theatre_popup;
				}
			}

			// Fallback - get preview URL directly with access control.
			if ( empty( $media_thumb ) && function_exists( 'bp_media_get_preview_image_url' ) ) {
				$media_thumb = bp_media_get_preview_image_url(
					$media_item->id,
					$attachment_id,
					$size
				);
			}

			if ( empty( $media_popup ) && function_exists( 'bp_media_get_preview_image_url' ) ) {
				$media_popup = bp_media_get_preview_image_url(
					$media_item->id,
					$attachment_id,
					'bb-media-photos-popup-image'
				);
			}

			if ( $media_thumb ) {
				// Build classes similar to platform's activity-entry.php.
				$elem_classes = $prefix . 'activity-media-elem media-activity ' . esc_attr( $media_item->id ) . ' ';
				if ( $is_single_or_first ) {
					$elem_classes .= 'act-grid-1-1 ';
				}
				if ( $media_count > 1 && $index > 0 ) {
					$elem_classes .= 'act-grid-1-2 ';
				}
				if ( $width > $height ) {
					$elem_classes .= 'bb-horizontal-layout';
				} elseif ( $height >= $width ) {
					$elem_classes .= 'bb-vertical-layout';
				}
				if ( $is_last_visible ) {
					$elem_classes .= ' no_more_option';
				}
				$elem_classes = trim( $elem_classes );

				$html .= '<div class="' . esc_attr( $elem_classes ) . '" data-id="' . esc_attr( $media_item->id ) . '">';

				$element_prefix = $this->get_element_class_prefix();
				$link_class = $prefix . 'open-media-theatre ' . $element_prefix . 'entry-img';
				$html .= '<a href="#" class="' . esc_attr( $link_class ) . '"';
				$html .= ' data-id="' . esc_attr( $media_item->id ) . '"';
				$html .= ' data-attachment-id="' . esc_attr( $attachment_id ) . '"';
				$html .= ' data-attachment-full="' . esc_attr( $media_popup ) . '"';
				$html .= ' data-activity-id="' . esc_attr( $media_item->activity_id ) . '"';
				$html .= ' data-privacy="' . esc_attr( $media_item->privacy ) . '"';
				$html .= ' data-parent-activity-id="' . esc_attr( $media_item->activity_id ) . '"';
				$html .= ' data-group-id="' . esc_attr( $media_item->group_id ) . '"';
				$html .= '>';

				// Use actual image URL (no lazy loading needed in preview context).
				$html .= '<img src="' . esc_url( $media_thumb ) . '" class="no-round photo" alt="' . esc_attr( $user->display_name ) . '">';

				// Show "+X More Photos" if this is the last visible item and there are more.
				if ( $is_last_visible ) {
					$remaining_count = $media_count - $max_length;
					$length_class = $prefix . 'photos-length';
					$html .= '<span class="' . esc_attr( $length_class ) . '"><span><strong>+' . esc_html( $remaining_count ) . '</strong> <span>' . esc_html__( 'More Photos', 'buddyboss-sharing' ) . '</span></span></span>';
				}

				$html .= '</a>';
				$html .= '</div>';
			}
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Generate documents HTML.
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param object $user User object.
	 * @param string $document_ids Comma-separated document IDs.
	 * @return string Documents HTML.
	 */
	protected function generate_documents_html( $activity, $user, $document_ids ) {
		if ( ! function_exists( 'bp_document_get_specific' ) ) {
			return '';
		}

		$document_ids_array = array_filter( array_map( 'intval', explode( ',', $document_ids ) ) );

		if ( empty( $document_ids_array ) ) {
			return '';
		}

		$document = bp_document_get_specific(
			array(
				'document_ids' => $document_ids_array,
				'order_by'     => 'menu_order',
				'sort'         => 'ASC',
			)
		);

		if ( empty( $document['documents'] ) ) {
			return '';
		}

		$document_items = $document['documents'];
		$document_count = count( $document_items );
		$prefix         = $this->get_class_prefix();

		// Use platform's markup structure for styling.
		$html = '<div class="bb-activity-media-wrap bb-media-length-' . esc_attr( $document_count ) . '">';

		foreach ( $document_items as $document_item ) {
			$attachment_id = $document_item->attachment_id;
			$document_id   = $document_item->id;

			$element_prefix = $this->get_element_class_prefix();

			// Get document properties.
			$extension = '';
			if ( function_exists( 'bp_document_extension' ) ) {
				$extension = bp_document_extension( $attachment_id );
			} else {
				$filename  = basename( get_attached_file( $attachment_id ) );
				$extension = pathinfo( $filename, PATHINFO_EXTENSION );
			}

			// Get download URL with access control.
			$download_url = '';
			if ( function_exists( 'bp_document_download_link' ) ) {
				$download_url = bp_document_download_link( $attachment_id, $document_id );
			}

			// Get SVG icon.
			$svg_icon = '';
			if ( function_exists( 'bp_document_svg_icon' ) ) {
				$svg_icon = bp_document_svg_icon( $extension, $attachment_id );
			} else {
				$svg_icon = 'bb-icon-f bb-icon-file';
			}

			// Get filename.
			$filename = basename( get_attached_file( $attachment_id ) );

			// Get file size.
			$size      = '';
			$file_path = get_attached_file( $attachment_id );
			if ( is_file( $file_path ) && function_exists( 'bp_document_size_format' ) ) {
				$size = bp_document_size_format( filesize( $file_path ) );
			}

			// Get extension description.
			$extension_description = '';
			if ( ! empty( $extension ) && function_exists( 'bp_document_get_extension_description' ) ) {
				$extension_description = bp_document_get_extension_description( $extension );
			}

			// Get attachment URL for preview.
			$attachment_url = '';
			if ( function_exists( 'bp_document_get_preview_url' ) ) {
				$attachment_url = bp_document_get_preview_url( $document_id, $attachment_id, 'bb-document-image-preview-activity-image' );
			} else {
				$attachment_url = wp_get_attachment_url( $attachment_id );
			}

			// Get image preview URL for document preview.
			$image_preview_url = '';
			if ( function_exists( 'bp_document_get_preview_url' ) ) {
				$image_preview_url = bp_document_get_preview_url( $document_id, $attachment_id );
			}

			// Build document element similar to platform's activity-entry.php.
			$elem_class = $prefix . 'activity-media-elem ' . $element_prefix . 'document-activity ' . esc_attr( $document_id );
			$html .= '<div class="' . esc_attr( $elem_class ) . '" data-id="' . esc_attr( $document_id ) . '" data-parent-id="' . esc_attr( $document_item->folder_id ) . '">';

			// Document preview (for images/PDFs/audio that can be previewed).
			$html .= '<div class="bb-code-extension-files-preview">';

			// Check if this is an audio file and generate audio preview.
			$music_extensions = function_exists( 'bp_get_document_preview_music_extensions' ) ? bp_get_document_preview_music_extensions() : array( 'mp3', 'wav', 'ogg' );
			$code_extensions = function_exists( 'bp_get_document_preview_code_extensions' ) ? bp_get_document_preview_code_extensions() : array( 'css', 'txt', 'html', 'htm', 'js', 'csv' );
			$bp_document_music_preview = apply_filters( 'bp_document_music_preview', true );

			if ( ! empty( $extension ) && in_array( $extension, $music_extensions, true ) && $bp_document_music_preview ) {
				// Audio preview.
				$audio_url = '';
				if ( function_exists( 'bp_document_get_preview_audio_url' ) ) {
					$audio_url = bp_document_get_preview_audio_url( $document_id, $attachment_id, $extension );
				}
				if ( $audio_url ) {
					$html .= '<div class="document-audio-wrap">';
					$html .= '<audio controls controlsList="nodownload">';
					$html .= '<source src="' . esc_url( $audio_url ) . '" type="audio/mpeg">';
					$html .= esc_html__( 'Your browser does not support the audio element.', 'buddyboss-sharing' );
					$html .= '</audio>';
					$html .= '</div>';
				}
			} elseif ( ! empty( $extension ) && ! in_array( $extension, $code_extensions, true ) && ! in_array( $extension, $music_extensions, true ) ) {
				// Image/PDF preview (exclude code and audio extensions).
				$bp_document_image_preview = apply_filters( 'bp_document_image_preview', true );
				if ( $image_preview_url && $bp_document_image_preview ) {
					$html .= '<div class="document-preview-wrap">';
					$html .= '<img src="' . esc_url( $image_preview_url ) . '" alt="" />';
					$html .= '</div>';
				}
			}

			$html .= '</div>';

			// Document description wrap.
			$html .= '<div class="' . $element_prefix . 'document-description-wrap">';

			// Document icon link.
			$class_theatre = apply_filters( 'bp_document_activity_theater_class', 'bb-open-document-theatre' );
			$link_class = $element_prefix . 'entry-img ' . $class_theatre;
			$html .= '<a href="' . esc_url( $download_url ) . '" class="' . esc_attr( $link_class ) . '"';
			$html .= ' data-id="' . esc_attr( $document_id ) . '"';
			$html .= ' data-attachment-full=""';
			$html .= ' data-attachment-id="' . esc_attr( $attachment_id ) . '"';
			$html .= ' data-privacy="' . esc_attr( $document_item->privacy ) . '"';
			if ( ! empty( $extension ) ) {
				$html .= ' data-extension="' . esc_attr( $extension ) . '"';
			}
			$html .= ' data-parent-activity-id="' . esc_attr( $document_item->activity_id ) . '"';
			$html .= ' data-activity-id="' . esc_attr( $document_item->activity_id ) . '"';
			$html .= ' data-author="' . esc_attr( $document_item->user_id ) . '"';
			$html .= ' data-preview="' . esc_attr( $attachment_url ) . '"';
			$html .= ' data-full-preview="' . esc_attr( $attachment_url ) . '"';
			$html .= ' data-text-preview="' . esc_attr( $attachment_url ) . '"';
			$html .= ' data-video-preview=""';
			$html .= ' data-mp3-preview="' . esc_attr( $attachment_url ) . '"';
			$html .= ' data-album-id="' . esc_attr( $document_item->folder_id ) . '"';
			$html .= ' data-group-id="' . esc_attr( $document_item->group_id ) . '"';
			$html .= ' data-document-title="' . esc_attr( $filename ) . '"';
			$html .= ' data-icon-class="' . esc_attr( $svg_icon ) . '">';
			$html .= '<i class="' . esc_attr( $svg_icon ) . '"></i>';
			$html .= '</a>';

			// Document details link.
			$class_popup = apply_filters( 'bp_document_activity_theater_description_class', $element_prefix . 'document-detail-wrap-description-popup' );
			$click_text  = apply_filters( 'bp_document_activity_click_to_view_text', __( ' view', 'buddyboss' ) );
			$html .= '<a href="' . esc_url( $download_url ) . '" class="' . $element_prefix . 'document-detail-wrap ' . esc_attr( $class_popup ) . '"';
			$html .= ' data-id="' . esc_attr( $document_id ) . '"';
			$html .= ' data-attachment-id="' . esc_attr( $attachment_id ) . '"';
			$html .= ' data-attachment-full=""';
			$html .= ' data-privacy="' . esc_attr( $document_item->privacy ) . '"';
			if ( ! empty( $extension ) ) {
				$html .= ' data-extension="' . esc_attr( $extension ) . '"';
			}
			$html .= ' data-parent-activity-id="' . esc_attr( $document_item->activity_id ) . '"';
			$html .= ' data-activity-id="' . esc_attr( $document_item->activity_id ) . '"';
			$html .= ' data-author="' . esc_attr( $document_item->user_id ) . '"';
			$html .= ' data-preview="' . esc_attr( $attachment_url ) . '"';
			$html .= ' data-full-preview="' . esc_attr( $attachment_url ) . '"';
			$html .= ' data-text-preview="' . esc_attr( $attachment_url ) . '"';
			$html .= ' data-mp3-preview="' . esc_attr( $attachment_url ) . '"';
			$html .= ' data-video-preview=""';
			$html .= ' data-album-id="' . esc_attr( $document_item->folder_id ) . '"';
			$html .= ' data-group-id="' . esc_attr( $document_item->group_id ) . '"';
			$html .= ' data-document-title="' . esc_attr( $filename ) . '"';
			$html .= ' data-icon-class="' . esc_attr( $svg_icon ) . '">';
			$html .= '<span class="' . $element_prefix . 'document-title">' . esc_html( $filename ) . '</span>';
			if ( $size ) {
				$html .= '<span class="' . $element_prefix . 'document-description">' . esc_html( $size ) . '</span>';
			}
			if ( ! empty( $extension_description ) ) {
				$html .= '<span class="' . $element_prefix . 'document-extension-description">' . esc_html( $extension_description ) . '</span>';
			}
			$html .= '<span class="' . $element_prefix . 'document-helper-text"><span> - </span><span class="' . $element_prefix . 'document-helper-text-click">' . esc_html__( 'Click to', 'buddyboss-sharing' ) . '</span><span class="' . $element_prefix . 'document-helper-text-inner">' . esc_html( $click_text ) . '</span></span>';
			$html .= '</a>';

			$html .= '</div>'; // .document-description-wrap
			$html .= '</div>'; // .bb-activity-media-elem
		}

		$html .= '</div>'; // .bb-activity-media-wrap

		return $html;
	}

	/**
	 * Generate compact footer HTML (for message context).
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param object $user User object.
	 * @return string Footer HTML.
	 */
	protected function generate_compact_footer( $activity, $user ) {
		$html  = '<div class="shared-activity-footer">';
		$html .= '<div class="shared-activity-author-info">';
		$html .= '<div class="shared-activity-avatar">';
		$html .= get_avatar( $user->ID, 32 );
		$html .= '</div>';
		$html .= '<div class="shared-activity-author-name">' . esc_html( $user->display_name ) . '</div>';
		$html .= '</div>';

		// Content (strip ALL media/video/document/div tags from content for clean text display).
		$content = $activity->content;
		
		// Fix: Unescape quotes incorrectly escaped (e.g., \" should be ")
		// Handles cases where content is stored with escaped quotes
		$content = str_replace( array( '\\"', "\\'" ), array( '"', "'" ), $content );

		// Remove ALL HTML elements from content - we want clean text only.
		// Strip images (including embedded screenshots).
		$content = preg_replace( '/<img[^>]+>/i', '', $content );
		// Strip videos.
		$content = preg_replace( '/<video[^>]*>.*?<\/video>/is', '', $content );
		// Strip documents.
		$content = preg_replace( '/<div[^>]*class="[^"]*document[^"]*"[^>]*>.*?<\/div>/is', '', $content );
		// Strip any remaining divs (like screenshot containers).
		$content = preg_replace( '/<div[^>]*>.*?<\/div>/is', '', $content );
		// Strip any other HTML tags.
		$content = wp_strip_all_tags( $content );

		// Store original content to check if truncation occurred.
		$original_content = $content;
		$original_word_count = str_word_count( $original_content );

		// Truncate content if too long (for preview).
		$truncated_content = wp_trim_words( $content, 30, '...' );

		// Check if content was actually truncated.
		// wp_trim_words() only adds '...' if content was truncated, so we check:
		// 1. If truncated content ends with '...'
		// 2. If original word count exceeds the limit (30 words)
		$was_truncated = ( substr( $truncated_content, -3 ) === '...' ) && ( $original_word_count > 30 );

		if ( ! empty( $truncated_content ) ) {
			$html .= '<div class="shared-activity-content">' . esc_html( $truncated_content );
			
			// Add "read more" link if content was truncated.
			if ( $was_truncated && function_exists( 'bp_activity_get_permalink' ) ) {
				$activity_permalink = bp_activity_get_permalink( $activity->id, $activity );
				if ( ! empty( $activity_permalink ) ) {
					$read_more_text = esc_html__( 'Read more', 'buddyboss-sharing' );
					$html .= ' <a href="' . esc_url( $activity_permalink ) . '" class="shared-activity-read-more">' . $read_more_text . '</a>';
				}
			}
			
			$html .= '</div>';
		}

		$html .= '</div>'; // .shared-activity-footer

		return $html;
	}

	/**
	 * Generate header section HTML (for full context).
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param object $user User object.
	 * @return string Header HTML.
	 */
	protected function generate_header_section( $activity, $user ) {
		// Get activity action string.
		$activity_action = '';
		if ( ! empty( $activity->action ) ) {
			// Use existing action if available.
			$activity_action = $activity->action;
		} elseif ( function_exists( 'bp_activity_generate_action_string' ) ) {
			// Generate action string if not set.
			$generated_action = bp_activity_generate_action_string( $activity );
			if ( false !== $generated_action ) {
				$activity_action = $generated_action;
			}
		}

		// Fallback to display name if action is empty.
		if ( empty( $activity_action ) ) {
			$activity_action = esc_html( $user->display_name );
		}

		// Add secondary avatars for friendship connections and group activities.
		if ( ! empty( $activity_action ) && in_array( $activity->component, array( 'friends', 'groups' ), true ) && function_exists( 'bp_nouveau_activity_secondary_avatars' ) ) {
			global $activities_template;

			// Save the current global state.
			$original_activities_template = $activities_template;

			// Temporarily set up $activities_template with our activity object
			// To allows bp_get_activity_secondary_avatar() to work properly.
			$activities_template = (object) array(
				'activity' => $activity,
			);

			// Call the function directly.
			$activity_action = bp_nouveau_activity_secondary_avatars( $activity_action, $activity );

			// Restore the original global state.
			$activities_template = $original_activities_template;
		}

		// Header with user info.
		$html  = '<div class="shared-activity-header">';
		$html .= '<div class="shared-activity-avatar">';
		$html .= get_avatar( $user->ID, 40 );
		$html .= '</div>';
		$html .= '<div class="shared-activity-meta">';
		$html .= '<div class="shared-activity-author">' . wp_kses_post( $activity_action ) . '</div>';
		$html .= '<div class="shared-activity-time">' . esc_html( human_time_diff( strtotime( $activity->date_recorded ) ) ) . ' ' . esc_html__( 'ago', 'buddyboss-sharing' ) . '</div>';

		// Add activity topic if topics are enabled (same as BuddyBoss Platform activity/entry.php)
		if (
			(
				'groups' === $activity->component &&
				function_exists( 'bb_is_enabled_group_activity_topics' ) &&
				bb_is_enabled_group_activity_topics()
			) ||
			(
				'groups' !== $activity->component &&
				function_exists( 'bb_is_enabled_activity_topics' ) &&
				bb_is_enabled_activity_topics()
			)
		) {
			if (
				function_exists( 'bb_activity_topics_manager_instance' ) &&
				method_exists( bb_activity_topics_manager_instance(), 'bb_get_activity_topic_url' )
			) {
				$topic_html = bb_activity_topics_manager_instance()->bb_get_activity_topic_url(
					array(
						'activity_id' => $activity->id,
						'html'        => true,
					)
				);

				if ( ! empty( $topic_html ) ) {
					$html .= '<p class="activity-topic">' . wp_kses_post( $topic_html ) . '</p>';
				}
			}
		}

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Generate link preview HTML.
	 *
	 * Builds the link preview HTML manually to ensure correct structure.
	 * Based on bp_activity_link_preview but without the content part.
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @return string Link preview HTML or empty string.
	 */
	protected function generate_link_preview_html( $activity ) {
		$meta = bb_activity_get_metadata( $activity->id );
		if ( empty( $meta ) || empty( $meta['_link_preview_data'][0] ) ) {
			return '';
		}

		$preview_data = maybe_unserialize( $meta['_link_preview_data'][0] );
		if ( empty( $preview_data['url'] ) ) {
			return '';
		}

		$preview_data = bp_parse_args(
			$preview_data,
			array(
				'title'       => '',
				'description' => '',
			)
		);

		$parse_url   = wp_parse_url( $preview_data['url'] );
		$domain_name = '';
		if ( ! empty( $parse_url['host'] ) ) {
			$domain_name = str_replace( 'www.', '', $parse_url['host'] );
		}

		$description = $preview_data['description'];
		$read_more   = ' &hellip; <a class="activity-link-preview-more" href="' . esc_url( $preview_data['url'] ) . '" target="_blank" rel="nofollow">' . __( 'Continue reading', 'buddyboss' ) . '</a>';
		$description = wp_trim_words( $description, 40, $read_more );

		$html = '<div class="activity-link-preview-container">';

		// Image section (if available).
		if ( ! empty( $preview_data['attachment_id'] ) ) {
			$image_url = wp_get_attachment_image_url( $preview_data['attachment_id'], 'full' );
			$html .= '<div class="activity-link-preview-image">';
			$html .= '<div class="activity-link-preview-image-cover">';
			$html .= '<a href="' . esc_url( $preview_data['url'] ) . '" target="_blank"><img src="' . esc_url( $image_url ) . '" /></a>';
			$html .= '</div>';
			$html .= '</div>';
		} elseif ( ! empty( $preview_data['image_url'] ) ) {
			$html .= '<div class="activity-link-preview-image">';
			$html .= '<div class="activity-link-preview-image-cover">';
			$html .= '<a href="' . esc_url( $preview_data['url'] ) . '" target="_blank"><img src="' . esc_url( $preview_data['image_url'] ) . '" /></a>';
			$html .= '</div>';
			$html .= '</div>';
		}

		// Info section (always present).
		$html .= '<div class="activity-link-preview-info">';
		$html .= '<p class="activity-link-preview-link-name">' . esc_html( $domain_name ) . '</p>';
		$html .= '<p class="activity-link-preview-title"><a href="' . esc_url( $preview_data['url'] ) . '" target="_blank" rel="nofollow">' . esc_html( $preview_data['title'] ) . '</a></p>';
		$html .= '<div class="activity-link-preview-excerpt"><p>' . $description . '</p></div>';
		$html .= '</div>'; // .activity-link-preview-info
		$html .= '</div>'; // .activity-link-preview-container

		return $html;
	}

	/**
	 * Generate content section HTML (for full context).
	 *
	 * @since 1.0.0
	 * @param object $activity Activity object.
	 * @param bool   $has_media_displayed Whether media was already displayed.
	 * @return string Content HTML.
	 */
	protected function generate_content_section( $activity, $has_media_displayed ) {
		// Content (strip media/video/document tags from content if already shown above).
		$content = $activity->content;
		
		// Fix: Unescape quotes incorrectly escaped (e.g., \" should be ")
		// Handles cases where content is stored with escaped quotes
		$content = str_replace( array( '\\"', "\\'" ), array( '"', "'" ), $content );

		// Remove media elements from content if they're already displayed.
		if ( $has_media_displayed ) {
			$content = preg_replace( '/<img[^>]+>/i', '', $content );
			$content = preg_replace( '/<video[^>]*>.*?<\/video>/is', '', $content );
			$content = preg_replace( '/<div[^>]*class="[^"]*document[^"]*"[^>]*>.*?<\/div>/is', '', $content );
		}

		// Truncate content if too long (for preview).
		$content = wp_trim_words( wp_strip_all_tags( $content ), 50, '...' );

		if ( ! empty( $content ) ) {
			return '<div class="shared-activity-content">' . wp_kses_post( wpautop( $content ) ) . '</div>';
		}

		return '';
	}
}
