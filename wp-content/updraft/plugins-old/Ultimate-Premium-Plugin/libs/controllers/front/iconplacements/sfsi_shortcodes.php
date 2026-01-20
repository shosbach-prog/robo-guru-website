<?php
	function DISPLAY_PREMIUM_RECTANGLE_ICONS( $inline = false ) {

		if (sfsi_plus_shall_show_icons('rect_icon_shortcode')) {

			$socialObj = new sfsi_plus_SocialHelper();
			$postid    = $socialObj->sfsi_get_the_ID();

			$icons = '';

			if ( $postid ) {

				$sfsi_section5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
				$sfsi_section8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );
				$sfsi_section4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );

				$permalink = $socialObj->sfsi_get_custom_share_link( $postid, $sfsi_section5 );
				$title     = get_the_title( $postid );

				////////// ----------- Get all settings for rectangle icons STARTS -------------------------////

				$sfsiLikeWith = "45px;";

				/* check for counter display */
				if ( $sfsi_section8['sfsi_plus_icons_DisplayCounts'] == "yes" ) {
					$show_count   = true;
					$sfsiLikeWith = "75px;";
				} else {
					$show_count  = false;
				}
				// setting float and text-align property from rectangular icon on post.
				$sfsi_plus_icons_alignment = $sfsi_section8['sfsi_plus_icons_alignment'];
				$float		 =  'center' === $sfsi_plus_icons_alignment ? 'none' : $sfsi_plus_icons_alignment;
				// $show_count  =  0;
				$style_parent = 'text-align:' . $sfsi_plus_icons_alignment . ';';
				$style 		 = 'float:' . $float . ';';
				if (false == $inline) {
					$icons = "<div class='sfsi_plus_rectangle_icons_shortcode_container sfsi_plus_Sicons_style_2 sfsi_plus_Sicons " . $sfsi_plus_icons_alignment . "' style='" . $style . $style_parent . "'>";
				}


				////////// ---------Get all settings for rectangle icons CLOSES -------------------////

				$txt 	  = (isset($sfsi_section8['sfsi_plus_textBefor_icons'])) ? $sfsi_section8['sfsi_plus_textBefor_icons'] : __( 'Please follow and like us:', 'ultimate-social-media-plus' );
				$fontSize = (isset($sfsi_section8['sfsi_plus_textBefor_icons_font_size']) && $sfsi_section8['sfsi_plus_textBefor_icons_font_size'] != 0) ? $sfsi_section8['sfsi_plus_textBefor_icons_font_size'] : "inherit";
				$fontFamily = (isset($sfsi_section8['sfsi_plus_textBefor_icons_font'])) ? $sfsi_section8['sfsi_plus_textBefor_icons_font'] : "inherit";
				$fontColor = (isset($sfsi_section8['sfsi_plus_textBefor_icons_fontcolor'])) ? $sfsi_section8['sfsi_plus_textBefor_icons_fontcolor'] : "#000000";

				$txt = "<span style='font-size: " . $fontSize . "px; font-family: " . $fontFamily . "; color: " . $fontColor . ";'>" . $txt . "</span>";

				if (false == $inline ) {
					$icons .= "<div style='display: " . ($inline ? "inline-flex" : 'block') . ";margin-bottom: 0; margin-left: 0; margin-right: 8px; vertical-align: middle;width: auto;'>
						<span>" . $txt . "</span>
					</div>";
				}

				if ($show_count) {
					$sfsiLikeWithsub = "93px";
				} else {
					$sfsiLikeWithsub = "64px";
				}
				if (!isset($sfsiLikeWithsub)) {
					$sfsiLikeWithsub = $sfsiLikeWith;
				}
				if (isset($sfsi_section8['sfsi_plus_rectsub']) && ('yes' === $sfsi_section8['sfsi_plus_rectsub'])) {
					$icons .= "<div class='sf_subscrbe' style='display: inline-flex;vertical-align: middle;width: auto;'>" . sfsi_plus_Subscribelike($permalink, $show_count) . "</div>";
				}
				if ($show_count) { } else {
					$sfsiLikeWithfb = "48px";
				}
				if (!isset($sfsiLikeWithfb)) {
					$sfsiLikeWithfb = $sfsiLikeWith;
				}
				if ($sfsi_section8['sfsi_plus_rectfb'] == 'yes') {
					if ($sfsi_section5['sfsi_plus_Facebook_linking'] == "facebookcustomurl") {
						$userDefineLink = ($sfsi_section5['sfsi_plus_facebook_linkingcustom_url']);
						$icons .= "<div class='sf_fb sf_fb_like' style='display: inline-flex;vertical-align: middle;width: auto;'>" . $socialObj->sfsi_plus_FBlike($userDefineLink, $show_count) . "</div>";
					} else {
						$icons.="<div class='sf_fb sf_fb_like' style='display: inline-flex;vertical-align: middle;width: auto;'>".$socialObj->sfsi_plus_FBlike($permalink,$show_count)."</div>";
					}
				}
				if ($sfsi_section8['sfsi_plus_rectfbshare'] == 'yes') {
					$count_html = "";
					if ($show_count) {
						//if ($sfsi_section4['sfsi_plus_facebook_countsDisplay'] == "yes" && $sfsi_section4['sfsi_plus_display_counts'] == "yes") {
							if ($sfsi_section4['sfsi_plus_facebook_countsFrom'] == "manual") {
								$counts = $sfsi_section4['sfsi_plus_facebook_manualCounts'];
							} else if ($sfsi_section4['sfsi_plus_facebook_countsFrom'] == "likes") {
								$counts = $socialObj->sfsi_get_fb($permalink);
							} else if ($sfsi_section4['sfsi_plus_facebook_countsFrom'] == "followers") {
								$counts = $socialObj->sfsi_get_fb($permalink);
							} else if ($sfsi_section4['sfsi_plus_facebook_countsFrom'] == "mypage") {
								$current_url = $sfsi_section4['sfsi_plus_facebook_mypageCounts'];
								$counts      = $socialObj->sfsi_get_fb_pagelike($current_url);
							}
                        if (is_array($counts)){
                            if (isset($counts['c'])){
                                if( $counts['c'] > 0 ) {
                                    $count_html = '<span class="bot_no">' . $counts['share_count'] . '</span>';
                                }
                            }
                        }else{
                            $count_html = '<span class="bot_no">' . $counts . '</span>';
                        }
						//}
					}
					$permalink = $socialObj->sfsi_get_custom_share_link('facebook', $sfsi_section5);
					$icons .= "<div class='sf_fb sf_fb_share' style='display: inline-flex;vertical-align: middle;'>" . $socialObj->sfsiFB_Share_Custom($permalink) . $count_html . "</div>";
					// $icons .= "<div class='sf_fb' style='display: inline-flex;vertical-align: middle;width: auto;'>" . $socialObj->sfsiFB_Share($permalink, $show_count) . "</div>";
				}
				if ($show_count) {
					$sfsiLikeWithtwtr = "77px";
				} else {
					$sfsiLikeWithtwtr = "56px";
				}
				if (!isset($sfsiLikeWithtwtr)) {
					$sfsiLikeWithtwtr = $sfsiLikeWith;
				}

				/* Check icon language */
				$icons_language = $sfsi_section5['sfsi_plus_icons_language'];

				if ($icons_language == 'ar') {
					$icons_language = 'ar_AR';
				}
				if ( $icons_language == "ja" ) {
					$icons_language = "ja_JP";
				}
				if ( $icons_language == "el" ) {
					$icons_language = "el_GR";
				}
				if ( $icons_language == "fi" ) {
					$icons_language = "fi_FI";
				}
				if ( $icons_language == "th" ) {
					$icons_language = "th_TH";
				}
				if ( $icons_language == "vi" ) {
					$icons_language = "vi_VN";
				}

				if ("automatic" == $icons_language) {
					if (function_exists('icl_object_id') && has_filter('wpml_current_language')) {
						$icons_language = apply_filters('wpml_current_language', NULL);
						if (!empty($icons_language)) {
							$icons_language = sfsi_premium_wordpress_locale_from_locale_code($icons_language);
						}
					} else {
						$icons_language = get_locale();
					}
				}

				if (isset($sfsi_section8['sfsi_plus_recttwtr']) && ('yes' === $sfsi_section8['sfsi_plus_recttwtr'])) {
					if ($show_count) {
						$sfsiLikeWithtwtr = "77px";
					} else {
						$sfsiLikeWithtwtr = "56px";
					}
					if (!isset($sfsiLikeWithtwtr)) {
						$sfsiLikeWithtwtr = $sfsiLikeWith;
					}
					$count_html = "";
					if ($show_count) {
						/* get twitter counts */
						if ($sfsi_section4['sfsi_plus_twitter_countsFrom'] == "source") {
							$twitter_user = $sfsi_premium_section2_options['sfsi_plus_twitter_followUserName'];
							$tw_settings = array(
								'sfsiplus_tw_consumer_key' => $sfsi_section4['sfsiplus_tw_consumer_key'],
								'sfsiplus_tw_consumer_secret' => $sfsi_section4['sfsiplus_tw_consumer_secret'],
								'sfsiplus_tw_oauth_access_token' => $sfsi_section4['sfsiplus_tw_oauth_access_token'],
								'sfsiplus_tw_oauth_access_token_secret' => $sfsi_section4['sfsiplus_tw_oauth_access_token_secret']
							);
							$followers = $socialObj->sfsi_get_tweets($twitter_user, $tw_settings);
							$counts = $socialObj->format_num($followers);
						} else {
							$counts = $socialObj->format_num($sfsi_section4['sfsi_plus_twitter_manualCounts']);
						}
						if ($counts > 0) {
							$count_html = '<span class="bot_no">' . $counts . '</span>';
						}
					}

					$twitter_text = $socialObj->sfsi_get_custom_tweet_text();
					$permalink = $socialObj->sfsi_get_custom_share_link('twitter', $sfsi_section5);
					$tweet_icon = SFSI_PLUS_PLUGURL . 'images/share_icons/Twitter_Tweet/' . $icons_language . '_Tweet.svg';
					$icons .= "<div class='sf_twiter' style='display: inline-flex;vertical-align: middle;width: auto;'><a href='https://x.com/intent/post?text=" . urlencode($twitter_text) . "' " . sfsi_plus_checkNewWindow() . " style='display:inline-block' ><img nopin=nopin class='sfsi_premium_wicon' src='" . $tweet_icon . "' alt='Tweet' title='Tweet' ></a>" . $count_html . "</div>";
				}
				if ($show_count) {
					$sfsiLikeWithpinit = "100px";
				} else {
					$sfsiLikeWithpinit = "auto";
				}

				if (isset($sfsi_section8['sfsi_plus_rectpinit']) && ('yes' === $sfsi_section8['sfsi_plus_rectpinit'])) {

					$sfsiLikeWithpinit = "auto";

					$count_html = "";
					if ($show_count) {
						/* get Pinterest counts */
						if ($sfsi_section4['sfsi_plus_pinterest_countsFrom'] == "pins") {
							$url = home_url();
							$pins = $socialObj->sfsi_get_pinterest($url);
							$counts = $socialObj->format_num($pins);
						} else {
							$counts = $sfsi_section4['sfsi_plus_pinterest_manualCounts'];
						}
						if ($counts > 0) {
							$count_html = '<span class="bot_no">' . $counts . '</span>';
						}
					}
					$alt_text = sfsi_plus_get_icon_mouseover_text("pinterest");
					$media = $socialObj->sfsi_pinit_image();
					$pinterest_save = SFSI_PLUS_PLUGURL . 'images/share_icons/Pinterest_Save/' . $icons_language . '_save.svg';
					$description = $socialObj->sfsi_pinit_description();
					$description = str_replace("%22", '"', $description);
					$description = str_replace("%27", "'", $description);
					$encoded_description = wptexturize($description);

					/* Add missing description_escaped */
					$description_escaped = addslashes( $description );

					if ($sfsi_section5['sfsi_premium_pinterest_sharing_texts_and_pics'] === "yes") {
						$icons .= "<div class='sf_pinit2' style='display: inline-flex;text-align:left;vertical-align:middle;'><a class ='sfsi_premium_pinterest_create' style='display:inline-block;vertical-align:middle;'  onclick='sfsi_premium_pinterest_modal_images(\"" . urlencode($permalink) . "\",\"" . $description_escaped . "\")'><img class='sfsi_premium_wicon' data-pin-nopin='true'  nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $pinterest_save . "'  /></a>" . $count_html . "</div>";
					} else {
						$icons .= "<div class='sf_pinit2' style='display: inline-flex;text-align:left;vertical-align:middle;'><a  data-pin-custom='true' style='cursor:pointer;display:inline-block;vertical-align:middle;' href='https://pinterest.com/pin/create/button/?url=" . urlencode($permalink) . "&media=" . urlencode($media) . "&description=" . ($encoded_description) . "' " . sfsi_plus_checkNewWindow() . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $pinterest_save . "'  /></a>" . $count_html . "</div>";
					}
				}
				if ($show_count) {
					$sfsiLikeWithlinkedin = "100px";
				} else {
					$sfsiLikeWithlinkedin = "auto";
				}
				if (isset($sfsi_section8['sfsi_plus_rectlinkedin']) && ('yes' === $sfsi_section8['sfsi_plus_rectlinkedin'])) {

					$sfsiLikeWithlinkedin = "auto";

					$count_html = "";
					if ($show_count) {
						/* get linkedIn counts */
						if ($sfsi_section4['sfsi_plus_linkedIn_countsFrom'] == "follower") {
							$linkedIn_compay = $sfsi_premium_section2_options['sfsi_plus_linkedin_followCompany'];
							$linkedIn_compay = $sfsi_section4['sfsi_plus_ln_company'];
							$ln_settings = array(
								'sfsi_plus_ln_api_key'          => $sfsi_section4['sfsi_plus_ln_api_key'],
								'sfsi_plus_ln_secret_key'       => $sfsi_section4['sfsi_plus_ln_secret_key'],
								'sfsi_plus_ln_oAuth_user_token' => $sfsi_section4['sfsi_plus_ln_oAuth_user_token']
							);
							$followers = $socialObj->sfsi_getlinkedin_follower($linkedIn_compay, $ln_settings);
							$counts = $socialObj->format_num($followers);
						} else {
							$counts = $socialObj->format_num($sfsi_section4['sfsi_plus_linkedIn_manualCounts']);
						}
						if ($counts > 0) {
							$count_html = '<span class="bot_no">' . $counts . '</span>';
						}
					}
					/*$linkedIn_icons_lang = isset($option5['sfsi_plus_linkedin_icons_language']) ? $option5['sfsi_plus_linkedin_icons_language'] : 'en_US';
					if($linkedIn_icons_lang == "ar"){
						$linkedIn_icons_lang = "ar_Ar";
					}*/
					$linkedin_share_icon = SFSI_PLUS_PLUGURL . "images/share_icons/Linkedin_Share/" . $icons_language . "_share.svg";
					$icons .= "<div class='sf_linkedin' style='display: inline-flex;vertical-align: top;text-align:left;width: " . $sfsiLikeWithlinkedin . "'>
						<a href='https://www.linkedin.com/shareArticle?url=" . $permalink . "'" . sfsi_plus_checkNewWindow() . ">
							<img class='sfsi_premium_wicon' nopin=nopin alt='".__( 'Share', 'ultimate-social-media-plus' )."' title='".__( 'Share', 'ultimate-social-media-plus' )."' src='" . $linkedin_share_icon . "'  />
						</a>" . $count_html . "</div>";
				}

				$sfsiLikeWithreddit = "auto";

				if (isset($sfsi_section8['sfsi_plus_rectreddit']) && ('yes' === $sfsi_section8['sfsi_plus_rectreddit'])) {
					$icons .= "<div class='sf_reddit' style='display: inline-flex;vertical-align: middle;text-align:left;width: " . $sfsiLikeWithreddit . "'>" . sfsi_redditShareButton($permalink) . "</div>";
				}

				$sfsiLikeWithgp = "auto";

				if ($show_count) {
					$sfsiLikeWithpingogl = "63px";
				} else {
					$sfsiLikeWithpingogl = "auto";
				}
				if ( false == $inline ) {
					$icons .= "</div><div style='clear:both'></div>";
				}
			}

			// if( !isset($sfsi_section8['sfsi_plus_place_rect_shortcode']) ||
			//  	(
			//  		( isset($sfsi_section8['sfsi_plus_place_rect_shortcode'])   ) &&
			//  		( "yes" == $sfsi_section8['sfsi_plus_place_rect_shortcode'] )
			//  	)
			// ){
			/*if (wp_is_mobile()) {
				if (isset($sfsi_section8['sfsi_plus_rectangle_icons_shortcode_show_on_mobile']) && $sfsi_section8['sfsi_plus_rectangle_icons_shortcode_show_on_mobile'] == 'yes') {
					// Migrating setting for new version to allow rectangle icons shortcode
					// if( !isset($sfsi_section8['sfsi_plus_place_rect_shortcode']) ){
					// 	$sfsi_section8['sfsi_plus_place_rect_shortcode'] = "yes";
					// 	update_option('sfsi_premium_section8_options',serialize($sfsi_section8));
					// }

					return $icons;
				}
			} else {
				if (isset($sfsi_section8['sfsi_plus_rectangle_icons_shortcode_show_on_desktop']) && $sfsi_section8['sfsi_plus_rectangle_icons_shortcode_show_on_desktop'] == 'yes') {
					// Migrating setting for new version to allow rectangle icons shortcode
					// if(!isset($sfsi_section8['sfsi_plus_place_rect_shortcode'])){
					// 	$sfsi_section8['sfsi_plus_place_rect_shortcode'] = "yes";
					// 	update_option('sfsi_premium_section8_options',serialize($sfsi_section8));
					// }

					return $icons;
				}
			}*/

			// }

			return $icons;
		}
	}
	add_shortcode( 'DISPLAY_PREMIUM_RECTANGLE_ICONS', 'DISPLAY_PREMIUM_RECTANGLE_ICONS' );


	function DISPLAY_ULTIMATE_PLUS( $args = null, $content = null ) {
		if ( sfsi_plus_shall_show_icons( 'round_icon_shortcode' ) ) {
			$instance = array( "showf" => 1, "title" => '' );

			$sfsi_premium_section8_options = maybe_unserialize( get_option( "sfsi_premium_section8_options" ) );
			$sfsi_plus_place_item_manually = isset( $sfsi_premium_section8_options['sfsi_plus_place_item_manually'] ) ? $sfsi_premium_section8_options['sfsi_plus_place_item_manually'] : "no";

			if ( $sfsi_plus_place_item_manually == "yes" ) {
				$return = '';
				if (!isset($before_widget)) : $before_widget = '';
				endif;
				if (!isset($after_widget)) : $after_widget = '';
				endif;

				/* Our variables from the widget settings. */
				$title = apply_filters('widget_title', $instance['title']);
				$show_info = isset( $instance['show_info'] ) ? $instance['show_info'] : false;
				global $is_floter;
				$return .= $before_widget;

				/* Display the widget title */
				if ( $title ) {
					if ( isset( $before_title ) ) {
						$return .= $before_title;
					}

					$return .= $title;

					if ( isset( $after_title ) ) {
						$return .= $after_title;
					}
				}

				$return .= '<div class="sfsi_plus_widget sfsi_plus_shortcode_container">';
				$return .= '<div id="sfsi_plus_wDiv"></div>';

				/* Link the main icons function */
				if ( wp_is_mobile() ) {
					if (isset($sfsi_premium_section8_options['sfsi_plus_shortcode_show_on_mobile']) && $sfsi_premium_section8_options['sfsi_plus_shortcode_show_on_mobile'] == 'yes') {
						$return .= sfsi_plus_check_mobile_visiblity( 0, $sfsi_premium_section8_options );
					}
				} else {
					if (isset($sfsi_premium_section8_options['sfsi_plus_shortcode_show_on_desktop']) && $sfsi_premium_section8_options['sfsi_plus_shortcode_show_on_desktop'] == 'yes') {
						$return .= sfsi_plus_check_visiblity( 0, $sfsi_premium_section8_options );
					}
				}

				$return .= '<div style="clear: both;"></div>';
				$return .= '</div>';
				$return .= '<div style="clear: both;"></div>';
				$return .= $after_widget;
				return $return;
			} else {
				return __( 'Kindly go to setting page and check the option "Place them manually"', 'ultimate-social-media-plus' );
			}
		}
	}
	add_shortcode( "DISPLAY_ULTIMATE_PLUS", "DISPLAY_ULTIMATE_PLUS" );

	function DISPLAY_RESPONSIVE_ICONS( $args = null, $content = null ) {
		if ( sfsi_plus_shall_show_icons( 'responsive_icon_shortcode' ) ) {
			$instance = array( "showf" => 1, "title" => '' );

			$sfsi_premium_section8_options = maybe_unserialize( get_option( "sfsi_premium_section8_options" ) );

			// $sfsi_plus_place_item_manually = (isset($sfsi_premium_section8_options['sfsi_plus_place_item_manually'])) ? $sfsi_premium_section8_options['sfsi_plus_place_item_manually']: "no";

			// if($sfsi_plus_place_item_manually == "yes")
			// {
			$return = '';
			if (!isset($before_widget)) : $before_widget = '';
			endif;
			if (!isset($after_widget)) : $after_widget = '';
			endif;

			/*Our variables from the widget settings. */
			$title = apply_filters( 'widget_title', $instance['title'] );
			$show_info = isset($instance['show_info']) ? $instance['show_info'] : false;
			global $is_floter;
			$return .= $before_widget;

			/* Display the widget title */
			if ( $title ) {
				if ( isset( $before_title ) ) {
					$return .= $before_title;
				}

				$return .= $title;

				if ( isset( $after_title ) ) {
					$return .= $after_title;
				}
			}

			$return .= '<div class="sfsi_plus_widget sfsi_plus_shortcode_container" style="width:100%">';
			$return .= '<div id="sfsi_plus_wDiv"></div>';

			/* Link the main icons function */
			if ( wp_is_mobile() ) {
				if ( isset( $sfsi_premium_section8_options['sfsi_plus_responsive_icons_show_on_mobile'] ) && $sfsi_premium_section8_options['sfsi_plus_responsive_icons_show_on_mobile'] == 'yes' ) {
					$return .= sfsi_premium_social_responsive_buttons( null, $sfsi_premium_section8_options );
				}
			} else {
				if ( isset( $sfsi_premium_section8_options['sfsi_plus_responsive_icons_show_on_desktop'] ) && $sfsi_premium_section8_options['sfsi_plus_responsive_icons_show_on_desktop'] == 'yes' ) {
					$return .= sfsi_premium_social_responsive_buttons( null, $sfsi_premium_section8_options, true );
				}
			}

			$return .= '<div style="clear: both;"></div>';
			$return .= '</div>';
			$return .= $after_widget;
			return $return;
			// }
			// else
			// {
			// 	return __('Kindly go to setting page and check the option "Place them manually"', 'ultimate-social-media-plus');
			// }
		}
	}
	add_shortcode( "DISPLAY_RESPONSIVE_ICONS", "DISPLAY_RESPONSIVE_ICONS" );
