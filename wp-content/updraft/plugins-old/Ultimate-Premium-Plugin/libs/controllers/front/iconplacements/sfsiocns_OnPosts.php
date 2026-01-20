<?php
/* add fb like add this to end of every post */
function sfsi_plus_social_buttons_below( $content, $inline = true, $sfsi_section8 = null, $gutenberg = false ) {
	global $post;
	$socialObj     = new sfsi_plus_SocialHelper();
	$sfsi_section4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
	$sfsi_section5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );

	//new options that are added on the third questions
	//so in this function we are replacing all the past options
	//that were saved under option6 by new settings saved under option8
	if ( $sfsi_section8 == null ) {
		$sfsi_section8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );
	}
	$sfsi_plus_show_item_onposts = $sfsi_section8['sfsi_plus_show_item_onposts'];
	//new options that are added on the third questions

	//checking for standard icons
	if ( !isset( $sfsi_section8['sfsi_plus_rectsub'] ) ) {
		$sfsi_section8['sfsi_plus_rectsub'] = 'no';
	}
	if ( !isset( $sfsi_section8['sfsi_plus_rectfb'] ) ) {
		$sfsi_section8['sfsi_plus_rectfb'] = 'yes';
	}

	if ( !isset( $sfsi_section8['sfsi_plus_recttwtr'] ) ) {
		$sfsi_section8['sfsi_plus_recttwtr'] = 'no';
	}
	if ( !isset( $sfsi_section8['sfsi_plus_rectpinit'] ) ) {
		$sfsi_section8['sfsi_plus_rectpinit'] = 'no';
	}
	if ( !isset( $sfsi_section8['sfsi_plus_rectfbshare'] ) ) {
		$sfsi_section8['sfsi_plus_rectfbshare'] = 'no';
	}
	if ( !isset( $sfsi_section8['sfsi_plus_rectlinkedin'] ) ) {
		$sfsi_section8['sfsi_plus_rectlinkedin'] = 'no';
	}
	if ( !isset( $sfsi_section8['sfsi_plus_rectreddit'] ) ) {
		$sfsi_section8['sfsi_plus_rectreddit'] = 'no';
	}
	//checking for standard icons

	$permalink 	  = $socialObj->sfsi_get_custom_share_link( $post->ID, $sfsi_section5 );
	$title 		  = get_the_title();
	$sfsiLikeWith = "45px;";

	/* check for counter display */
	if ( $sfsi_section8['sfsi_plus_icons_DisplayCounts'] == "yes" ) {
		$show_count = 1;
		$sfsiLikeWith = "75px;";
	} else {
		$show_count = 0;
	}

	$txt 	= ( isset( $sfsi_section8['sfsi_plus_textBefor_icons'] ) ) ? $sfsi_section8['sfsi_plus_textBefor_icons'] : __( 'Please follow and like us:', 'ultimate-social-media-plus' );
	$fontSize = (isset($sfsi_section8['sfsi_plus_textBefor_icons_font_size']) && $sfsi_section8['sfsi_plus_textBefor_icons_font_size'] != 0) ? $sfsi_section8['sfsi_plus_textBefor_icons_font_size'] : "inherit";
	$fontFamily = (isset($sfsi_section8['sfsi_plus_textBefor_icons_font'])) ? $sfsi_section8['sfsi_plus_textBefor_icons_font'] : "inherit";
	$fontColor = (isset($sfsi_section8['sfsi_plus_textBefor_icons_fontcolor'])) ? $sfsi_section8['sfsi_plus_textBefor_icons_fontcolor'] : "#000000";
	$txt = "<span style='font-size:" . $fontSize . "px; font-family:" . $fontFamily . "; color:" . $fontColor . ";'>" . $txt . "</span>";
	
	if ($gutenberg) {
		$txt = "";
	}
	$float	= $sfsi_section8['sfsi_plus_icons_alignment'];
	if ($inline) {
		if ($float == "center") {
			$style_parent = 'text-align: center;';
			$style = 'float:none; display: inline-block;display:flex;align-items:flex-start;';
		} else {
			$style_parent = 'text-align:' . $float . ';';
			$style = 'float:' . $float . ';display:flex;align-items:flex-start;';
		}
	} else {
		if ($float == "center") {
			$style_parent = 'text-align: center;';
			$style = 'float:none; display: inline-block;display:flex;';
		} else {
			$style_parent = '';
			$style = 'float:' . $float . ';display:flex;';
		}
	}


	if (
		$sfsi_section8['sfsi_plus_rectsub'] 	== 'yes' ||
		$sfsi_section8['sfsi_plus_rectfb'] 		== 'yes' ||
		$sfsi_section8['sfsi_plus_recttwtr'] 	== 'yes' ||
		$sfsi_section8['sfsi_plus_rectpinit'] 	== 'yes' ||
		$sfsi_section8['sfsi_plus_rectlinkedin'] == 'yes' ||
		$sfsi_section8['sfsi_plus_rectreddit'] 	== 'yes' ||
		$sfsi_section8['sfsi_plus_rectfbshare'] == 'yes'
	) {
		if ($inline) {
            $mouse_hover_effect = sfsi_premium_mouseOver_effect_classlist();

			$icons = "<div class='sfsi_plus_Sicons_style_2 sfsi_plus_Sicons $mouse_hover_effect " . $float . "' style='" . $style . $style_parent . "'>
			<div style='display: " . ($inline ? "inline-flex" : 'block') . ";margin-bottom: 0; margin-left: 0; margin-right: 8px; vertical-align: middle;width: auto;'>
				<span>" . $txt . "</span>
			</div>";
		} else {
			$icons = "<div style='display: " . ($inline ? "inline-flex" : 'block') . ";margin-bottom: 0; margin-left: 0; margin-right: 8px; margin-top: 5px; vertical-align: middle;width: auto;'>
				<span>" . $txt . "</span>
			</div>
			<div class='sfsi_plus_Sicons_style_2 sfsi_plus_Sicons " . $float . "' style='" . $style . $style_parent . "'>";
		}
	}
	if ($sfsi_section8['sfsi_plus_rectsub'] == 'yes') {
		if ($show_count) {
			$sfsiLikeWithsub = "93px";
		} else {
			$sfsiLikeWithsub = "64px";
		}
		if (!isset($sfsiLikeWithsub)) {
			$sfsiLikeWithsub = $sfsiLikeWith;
		}

		$icons .= "<div class='sf_subscrbe' style='display: inline-flex;vertical-align: middle;width: auto;'>" . sfsi_plus_Subscribelike($permalink, $show_count) . "</div>";
	}
	if ( $sfsi_section8['sfsi_plus_rectfb'] == 'yes' ) {
		if ($show_count) { } else {
			$sfsiLikeWithfb = "48px";
		}
		if ( !isset( $sfsiLikeWithfb ) ) {
			$sfsiLikeWithfb = $sfsiLikeWith;
		}

		$icons .= "<div class='sf_fb sf_fb_like' style='display: inline-flex;vertical-align: sub;width: auto;'>";
		if ( $sfsi_section5['sfsi_plus_Facebook_linking'] == "facebookcustomurl" ) {
			$userDefineLink = $sfsi_section5['sfsi_plus_facebook_linkingcustom_url'];
			$icons .= $socialObj->sfsi_plus_FBlike( $userDefineLink, $show_count );
		} else {
			$icons .= $socialObj->sfsi_plus_FBlike( $permalink, $show_count );
		}
		$icons .= "</div>";
	}
	if ( $sfsi_section8['sfsi_plus_rectfbshare'] == 'yes' ) {
		$count_html = "";
		if ( $show_count ) {
			//if ($sfsi_section4['sfsi_plus_facebook_countsDisplay'] == "yes" && $sfsi_section4['sfsi_plus_display_counts'] == "yes") {
				if ( $sfsi_section4['sfsi_plus_facebook_countsFrom'] == "manual" ) {
					$counts = $sfsi_section4['sfsi_plus_facebook_manualCounts'];
				} else if ( $sfsi_section4['sfsi_plus_facebook_countsFrom'] == "likes" ) {
					$counts = $socialObj->sfsi_get_fb( $permalink );
				} else if ( $sfsi_section4['sfsi_plus_facebook_countsFrom'] == "followers" ) {
					$counts = $socialObj->sfsi_get_fb( $permalink );
				} else if ( $sfsi_section4['sfsi_plus_facebook_countsFrom'] == "mypage" ) {
					$current_url = $sfsi_section4['sfsi_plus_facebook_mypageCounts'];
					$counts      = $socialObj->sfsi_get_fb_pagelike( $current_url );
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

	/* Check icon language */
	$icons_language = $sfsi_section5['sfsi_plus_icons_language'];

	if ( $icons_language == 'ar' ) {
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

	if ( "automatic" == $icons_language ) {
		if ( function_exists( 'icl_object_id' ) && has_filter( 'wpml_current_language' ) ) {
			$icons_language = apply_filters( 'wpml_current_language', NULL );
			if ( !empty( $icons_language ) ) {
				$icons_language = sfsi_premium_wordpress_locale_from_locale_code( $icons_language );
			}
		} else {
			$icons_language = get_locale();
		}
	}

	if ( $sfsi_section8['sfsi_plus_recttwtr'] == 'yes' ) {
		if ( $show_count ) {
			$sfsiLikeWithtwtr = "77px";
		} else {
			$sfsiLikeWithtwtr = "56px";
		}
		if ( !isset( $sfsiLikeWithtwtr ) ) {
			$sfsiLikeWithtwtr = $sfsiLikeWith;
		}
		$count_html = "";
		if ( $show_count ) {
			/* get twitter counts */
			if ( $sfsi_section4['sfsi_plus_twitter_countsFrom'] == "source" ) {
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
		$icons .= "<div class='sf_twiter' style='display: inline-flex;vertical-align: middle;width: auto;'><a href='https://x.com/intent/post?text=" . urlencode($twitter_text) . "' " . sfsi_plus_checkNewWindow() . " style='display:inline-block' ><img nopin=nopin class='sfsi_premium_wicon' src='" . $tweet_icon . "' alt='".__( 'Tweet', 'ultimate-social-media-plus' )."' title='".__( 'Tweet', 'ultimate-social-media-plus' )."' ></a>" . $count_html . "</div>";
		// $icons .= "<div class='sf_twiter' style='display: inline-block;vertical-align: middle;width: auto;'>" . sfsi_plus_twitterlike($permalink, $show_count) . "</div>";
	}
	if ( $sfsi_section8['sfsi_plus_rectpinit'] == 'yes' ) {

		$sfsiLikeWithpinit = "auto";

		$count_html = "";
		if ( $show_count ) {
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
		$alt_text = sfsi_plus_get_icon_mouseover_text( "pinterest" );
		$media = $socialObj->sfsi_pinit_image();
		$pinterest_save = SFSI_PLUS_PLUGURL . 'images/share_icons/Pinterest_Save/' . $icons_language . '_save.svg';
		$description = $socialObj->sfsi_pinit_description();
		$description = str_replace("%22", '"', $description);
		$description = str_replace("%27", "'", $description);
		$encoded_description = wptexturize($description);

		/* Add missing description_escaped */
		$description_escaped = addslashes( $description );

		if ($sfsi_section5['sfsi_premium_pinterest_sharing_texts_and_pics'] === "yes") {
			$icons .= "<div class='sf_pinit2' style='display: inline-flex;text-align:left;vertical-align:top;'><a class ='sfsi_premium_pinterest_create' style='display:inline-block;vertical-align:bottom;'  onclick='sfsi_premium_pinterest_modal_images(\"" . $permalink . "\",\"" . $description_escaped . "\")'><img class='sfsi_premium_wicon' data-pin-nopin='true'  nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $pinterest_save . "'  /></a>" . $count_html . "</div>";
		} else {
			$icons .= "<div class='sf_pinit2' style='display: inline-flex;text-align:left;vertical-align:top;'><a  data-pin-custom='true' style='cursor:pointer;display:inline-block;vertical-align:bottom;' href='https://pinterest.com/pin/create/button/?url=" . urlencode($permalink) . "&media=" . urlencode($media) . "&description=" . ($encoded_description) . "'" . sfsi_plus_checkNewWindow() . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $pinterest_save . "'  /></a>" . $count_html . "</div>";
		}
		// $icons .= "<div class='sf_pinit' style='display: inline-flex;text-align:left;vertical-align: top;width: " . $sfsiLikeWithpinit . ";'>" . sfsi_plus_pinitpinterest($permalink, $show_count) . "</div>";
	}
	if ($sfsi_section8['sfsi_plus_rectlinkedin'] == 'yes') {

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
		if ($linkedIn_icons_lang == "ar") {
			$linkedIn_icons_lang = "ar_Ar";
		}*/
		$linkedin_share_icon = SFSI_PLUS_PLUGURL . "images/share_icons/Linkedin_Share/" . $icons_language . "_share.svg";
		$icons .= "<div class='sf_linkedin' style='display: inline-flex;vertical-align: top;text-align:left;width: " . $sfsiLikeWithlinkedin . "'>
			<a href='https://www.linkedin.com/shareArticle?url=" . urlencode($permalink) . "'" . sfsi_plus_checkNewWindow() .  ">
				<img class='sfsi_premium_wicon' nopin=nopin alt='".__( 'Share', 'ultimate-social-media-plus' )."' title='".__( 'Share', 'ultimate-social-media-plus' )."' src='" . $linkedin_share_icon . "'  />
			</a>" . $count_html . "</div>";
		// $icons .= "<div class='sf_linkedin' style='display: inline-flex;vertical-align: middle;text-align:left;width: " . $sfsiLikeWithlinkedin . "'>" . sfsi_LinkedInShare($permalink, $show_count) . "</div>";
	}
	if ($sfsi_section8['sfsi_plus_rectreddit'] == 'yes') {
		$icons .= "<div class='sf_reddit' style='display: inline-flex;vertical-align: top;text-align:left;margin-top:6px;width:auto;'>" . sfsi_redditShareButton($permalink) . "</div>";
	}

	$icons .= "</div>";

	if ( !is_feed() && !is_home() ) {
		$content = $content . $icons;
	}

	return $content;
}

function sfsi_premium_social_responsive_buttons($content, $option8, $server_side = false, $option5 = null) {
	global $post;

	if ((isset($option8["sfsi_plus_show_item_onposts"]) && $option8["sfsi_plus_show_item_onposts"] == "yes" && isset($option8["sfsi_plus_display_button_type"]) && $option8["sfsi_plus_display_button_type"] == "responsive_button") || $server_side) :
		$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );
		$option4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
		if ($option5 == null) {
			$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
		}

		$icons = "";
		$sfsi_premium_responsive_icons = (isset($option8["sfsi_plus_responsive_icons"]) ? $option8["sfsi_plus_responsive_icons"] : null);

		if (is_null($sfsi_premium_responsive_icons)) {
			return ""; // dont return anything if options not set;
		}
		$icon_width_type = $sfsi_premium_responsive_icons["settings"]["icon_width_type"];
		$margin_above = $sfsi_premium_responsive_icons["settings"]["margin_above"];
		$margin_below = $sfsi_premium_responsive_icons["settings"]["margin_below"];
		if ($option4['sfsi_plus_display_counts'] == 'yes' && isset($sfsi_premium_responsive_icons["settings"]['show_count']) && $sfsi_premium_responsive_icons["settings"]['show_count'] == "yes") :
			$counter_class = "sfsi_premium_responsive_with_counter_icons";
			$couter_display = "inline-block";
		else :
			$counter_class = "sfsi_premium_responsive_without_counter_icons";
			$couter_display = "none";
		endif;

		$icons .= "<div class='sfsi_premium_responsive_icons' style='display:inline-block; margin-top:" . $margin_above . "px; margin-bottom: " . $margin_below . "px;" . ($icon_width_type == "Fully Responsive" ? "width:100%;display:flex; " : 'width:100%') . "' data-icon-width-type='" . $icon_width_type . "' data-icon-width-size='" . $sfsi_premium_responsive_icons["settings"]['icon_width_size'] . "' data-edge-type='" . $sfsi_premium_responsive_icons["settings"]['edge_type'] . "' data-edge-radius='" . $sfsi_premium_responsive_icons["settings"]['edge_radius'] . "'  >";
		$sfsi_premium_anchor_div_style = "";
		if ($sfsi_premium_responsive_icons["settings"]["edge_type"] === "Round") {
			$sfsi_premium_anchor_div_style .= " border-radius:";
			if ($sfsi_premium_responsive_icons["settings"]["edge_radius"] !== "") {
				$sfsi_premium_anchor_div_style .= $sfsi_premium_responsive_icons["settings"]["edge_radius"] . 'px; ';
			} else {
				$sfsi_premium_anchor_div_style .= '0px; ';
			}
		}

		ob_start(); ?>
		<div class="sfsi_premium_responsive_icons_count sfsi_premium_<?php echo ($icon_width_type == "Fully responsive" ? 'responsive' : 'fixed'); ?>_count_container sfsi_premium_<?php echo strtolower($sfsi_premium_responsive_icons['settings']['icon_size']); ?>_button" style='display:<?php echo $couter_display; ?>;text-align:center; background-color:<?php echo $sfsi_premium_responsive_icons['settings']['counter_bg_color']; ?>;color:<?php echo $sfsi_premium_responsive_icons['settings']['counter_color']; ?>; <?php echo $sfsi_premium_anchor_div_style; ?>;'>
			<h3 style="color:<?php echo $sfsi_premium_responsive_icons['settings']['counter_color']; ?>; ">
				<?php echo sfsi_premium_total_count($sfsi_premium_responsive_icons['default_icons']); ?></h3>
			<h6 style="color:<?php echo $sfsi_premium_responsive_icons['settings']['counter_color']; ?>;">
				<?php echo $sfsi_premium_responsive_icons['settings']["share_count_text"]; ?></h6>
		</div>
		<?php
				$icons .= ob_get_contents();
				ob_end_clean();

                $mouse_hover_effect = sfsi_premium_mouseOver_effect_classlist();
				$icons .= "\t<div class='sfsi_premium_icons_container " . $counter_class . " sfsi_premium_" . strtolower($sfsi_premium_responsive_icons['settings']['icon_size']) . "_button_container sfsi_premium_icons_container_box_" . ($icon_width_type !== "Fixed icon width" ? "fully" : 'fixed') . "_container " .$mouse_hover_effect. "' style='" . ($icon_width_type !== "Fixed icon width" ? "width:100%;display:flex; " : 'width:auto;') . " text-align:center;' >";
				$socialObj = new sfsi_plus_SocialHelper();
				//styles
				$sfsi_premium_anchor_style = "";
				if ($sfsi_premium_responsive_icons["settings"]["text_align"] == "Centered") {
					$sfsi_premium_anchor_style .= 'text-align:center;';
				}
				if ($sfsi_premium_responsive_icons["settings"]["margin"] !== "") {
					$sfsi_premium_anchor_style .= 'margin-left:' . $sfsi_premium_responsive_icons["settings"]["margin"] . "px; ";
					// $sfsi_premium_anchor_style.='margin-bottom:'.$sfsi_premium_responsive_icons["settings"]["margin"]."px; ";
				}

				if ($sfsi_premium_responsive_icons['settings']['icon_width_type'] === "Fixed icon width") {
					$sfsi_premium_anchor_div_style .= 'width:' . $sfsi_premium_responsive_icons['settings']['icon_width_size'] . 'px;';
				} else {
					$sfsi_premium_anchor_style .= " flex-basis:100%;";
					$sfsi_premium_anchor_div_style .= " width:100%;";
				}
				$is_pinterest = false;
				// var_dump($sfsi_premium_anchor_style,$sfsi_premium_anchor_div_style);
				foreach ($sfsi_premium_responsive_icons['default_icons'] as $icon => $icon_config) {
					// var_dump($icon_config['url']);

					$current_url =  $socialObj->sfsi_get_custom_share_link(strtolower($icon), $option5);
					switch ($icon) {
						case "facebook":
							$share_url = "https://www.facebook.com/sharer/sharer.php?u=" . trailingslashit($current_url);
							break;
						case "Twitter":
							$twitter_text = urlencode($socialObj->sfsi_get_custom_tweet_text($option5));
							$share_url = "https://x.com/intent/post?text=" . $twitter_text . "&url=";
							break;
						case "Follow":
							if (isset($option2['sfsi_plus_email_icons_functions']) && $option2['sfsi_plus_email_icons_functions'] == 'sf') {
								$share_url = (isset($option2['sfsi_plus_email_url']))
									? $option2['sfsi_plus_email_url']
									: 'https://follow.it/now';
							} elseif (isset($option2['sfsi_plus_email_icons_functions']) && $option2['sfsi_plus_email_icons_functions'] == 'contact') {
								$share_url = (isset($option2['sfsi_plus_email_icons_contact']) && !empty($option2['sfsi_plus_email_icons_contact']))
									? "mailto:" . $option2['sfsi_plus_email_icons_contact']
									: 'javascript:';
							} elseif (isset($option2['sfsi_plus_email_icons_functions']) && $option2['sfsi_plus_email_icons_functions'] == 'page') {
								$share_url = (isset($option2['sfsi_plus_email_icons_pageurl']) && !empty($option2['sfsi_plus_email_icons_pageurl']))
									? $option2['sfsi_plus_email_icons_pageurl']
									: 'javascript:';
							} elseif (isset($option2['sfsi_plus_email_icons_functions']) && $option2['sfsi_plus_email_icons_functions'] == 'share_email') {
								$subject = stripslashes($option2['sfsi_plus_email_icons_subject_line']);
								$subject = str_replace('${title}', $socialObj->sfsi_get_the_title(), $subject);
								$subject = str_replace('"', '', str_replace("'", '', $subject));
								$subject = html_entity_decode(strip_tags($subject), ENT_QUOTES, 'UTF-8');
								$subject = str_replace("%26%238230%3B", "...", $subject);
								$subject = rawurlencode($subject);

								$body = stripslashes($option2['sfsi_plus_email_icons_email_content']);
								$body = str_replace('${title}', $socialObj->sfsi_get_the_title(), $body);
								$body = str_replace('${link}',  trailingslashit($socialObj->sfsi_get_custom_share_link('email', $option5)), $body);
								$body = str_replace('"', '', str_replace("'", '', $body));
								$body = html_entity_decode(strip_tags($body), ENT_QUOTES, 'UTF-8');
								$body = str_replace("%26%238230%3B", "...", $body);
								$body = rawurlencode($body);
								$share_url = "mailto:?subject=$subject&body=$body";
							} else {
								$share_url = (isset($option2['sfsi_plus_email_url']))
									? $option2['sfsi_plus_email_url']
									: 'https://follow.it/now';
							}
							break;

							// $pin_it_html    = '<a data-pin-do="buttonPin" data-pin-save="true" href="create/button/?url='.$url.'&media='.$pinterest_img.'&description='.$pinterest_desc.'"></a>';

							// $pinit_html = '<a href="https://www.pinterest.com/pin/create/button/?url='.$permalink.'&media='.$socialObj->sfsi_pinit_image().'&description='.$socialObj->sfsi_pinit_description().'" data-pin-do="buttonPin" data-pin-save="true"';
							// case "pinterest":$share_url = "http://pinterest.com/pin/create/link/?url=".trailingslashit($current_url);break;
						case "pinterest":
							$share_url = 'https://www.pinterest.com/pin/create/link/?url=' . urlencode($current_url) . '&media=' . $socialObj->sfsi_pinit_image($option5) . '&description=' . $socialObj->sfsi_pinit_description($option5);
							$is_pinterest = true;
							break;

						case "Linkedin":
							$share_url = "http://www.linkedin.com/shareArticle?mini=true&url=" . urlencode($current_url);
							break;
						case "Whatsapp":
							$share_url = wp_is_mobile() ? 'https://api.whatsapp.com/send?text=' . urlencode($current_url) : 'https://web.whatsapp.com/send?text=' . urlencode($current_url);
							break;
						case "vk":
							$share_url = "http://vk.com/share.php?url=" . trailingslashit(urlencode($current_url));
							break;
						case "Odnoklassniki":
							$share_url = "https://connect.ok.ru/offer?url=" . trailingslashit(urlencode($current_url));
							break;
						case "Telegram":
							$share_url = "https://telegram.me/share/url?url=" . trailingslashit(urlencode($current_url));
							break;
						case "Weibo":
							$share_url = "http://service.weibo.com/share/share.php?url=" . trailingslashit(urlencode($current_url));
							break;
						case "QQ2":
							$share_url = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=" . trailingslashit(urlencode($current_url));
							break;
						case "xing":
							$share_url = "https://www.xing.com/app/user?op=share&url=" . trailingslashit(urlencode($current_url));
							break;
					}
					if (false == $is_pinterest || (true == $is_pinterest && $icon_config['url'] !== "")) {
						if (isset($option5['sfsi_plus_url_shorting_api_type_setting']) && $option5['sfsi_plus_url_shorting_api_type_setting'] == "bitly") {
							$share_url = untrailingslashit(urlencode($share_url));
						}
						if ( $icon_config['active'] == 'yes' || is_admin() ) {
							$icons .= "\t\t" . "<a " . sfsi_plus_checkNewWindow() . " href='" . ($icon_config['url'] == "" ? $share_url : do_shortcode($icon_config['url'])) . "' style='" . ($icon_config['active'] == 'yes' ? ($sfsi_premium_responsive_icons['settings']['icon_width_type'] === "Fixed icon width" ? 'display:inline-flex' : 'display:flex') : 'display:none') . ";" . $sfsi_premium_anchor_style . "' class=" . ($sfsi_premium_responsive_icons['settings']['icon_width_type'] === "Fixed icon width" ? 'sfsi_premium_responsive_fixed_width' : 'sfsi_premium_responsive_fluid') . " >" . "\n";
							$icons .= "\t\t\t<div class='sfsi_premium_responsive_icon_item_container sfsi_premium_responsive_icon_" . strtolower($icon) . "_container sfsi_premium_" . strtolower($sfsi_premium_responsive_icons['settings']['icon_size']) . "_button " . ($sfsi_premium_responsive_icons['settings']['style'] == "Gradient" ? 'sfsi_premium_responsive_icon_gradient' : '') . (" sfsi_premium_" . (strtolower($sfsi_premium_responsive_icons['settings']['text_align']) == "centered" ? 'centered' : 'left-align') . "_icon") . "' style='" . $sfsi_premium_anchor_div_style . " ' >" . "\n";
							$icons .= "\t\t\t\t<img style='max-height: 25px;display:unset;margin:0' class='sfsi_premium_wicon' alt='' src='" . SFSI_PLUS_PLUGURL . "images/responsive-icon/" . $icon . ('Follow' === $icon ? '.png' : '.svg') . "'>" . "\n";
                            $temp = $icon_config["text"] == 'Tweet' ? 'Post on X' : $icon_config["text"];
							$icons .= "\t\t\t\t<span style='color:#fff' >" . $temp . "</span>" . "\n";
							$icons .= "\t\t\t</div>" . "\n";
							$icons .= "\t\t</a>" . "\n\n";
						}
					} else {
						// var_dump($icon_config);
						if ( $icon_config['active'] == 'yes' || is_admin() ) {
							$icons2 = "\t\t\t<div class='sfsi_premium_responsive_icon_item_container sfsi_premium_responsive_custom_icon sfsi_premium_responsive_icon_" . strtolower($icon) . "_container sfsi_premium_" . strtolower($sfsi_premium_responsive_icons['settings']['icon_size']) . "_button " . ("sfsi_premium_" . (strtolower($sfsi_premium_responsive_icons['settings']['text_align']) == "centered" ? 'centered' : 'left-align') . "_icon") . " " . ($sfsi_premium_responsive_icons['settings']['style'] == "Gradient" ? 'sfsi_premium_responsive_icon_gradient' : '') . "' style='" . $sfsi_premium_anchor_div_style . " background-color:" . (isset($icon_config['bg-color']) ? $icon_config['bg-color'] : '#73d17c') . "' >" . "\n";
							$icons2 .= "\t\t\t\t<img style='max-height: 25px;display:unset;margin:0' onclick='event.target.parentNode.click()' alt='' src='" . SFSI_PLUS_PLUGURL . "images/responsive-icon/" . $icon . '.svg' . "'>" . "\n";
							$icons2 .= "\t\t\t\t<span onclick='event.target.parentNode.click()' style='color:#fff' >" . ($icon_config["text"]) . "</span>" . "\n";
							$icons2 .= "\t\t\t</div>" . "\n";
							$icons .= $socialObj->sfsi_PinIt('', ($sfsi_premium_responsive_icons['settings']['icon_width_type'] === "Fixed icon width" ? 'sfsi_premium_responsive_fixed_width' : 'sfsi_premium_responsive_fluid'), $current_url, $icons2, 1, ($icon_config['active'] == 'yes' ? ($sfsi_premium_responsive_icons['settings']['icon_width_type'] === "Fixed icon width" ? 'display:inline-flex' : 'display:flex') : 'display:none') . ";" . $sfsi_premium_anchor_style);
						}
					}
					$is_pinterest = false;
				}
				$sfsi_premium_responsive_icons_custom_icons = array();
				if (!isset($sfsi_premium_responsive_icons['custom_icons']) || !empty($sfsi_premium_responsive_icons['custom_icons'])) {
					$sfsi_premium_responsive_icons_custom_icons = $sfsi_premium_responsive_icons['custom_icons'];
				} else {
					$count = 5;
					for ($i = 0; $i < $count; $i++) {
						array_push($sfsi_premium_responsive_icons_custom_icons, array(
							"added" => "no",
							"active" => "no",
							"text" => "Share",
							"bg-color" => "#729fcf",
							"url" => "",
							"icon" => ''
						));
					}
				}
				foreach ($sfsi_premium_responsive_icons_custom_icons as $icon => $icon_config) {
					// var_dump($icon_config);
					if ( $icon_config['active'] == 'yes' || is_admin() ) {
						$current_url =  $socialObj->sfsi_get_custom_share_link(strtolower($icon), $option5);
						$icons .= "\t\t" . "<a " . sfsi_plus_checkNewWindow() . " href='" . ($icon_config['url'] == "" ? "" : do_shortcode($icon_config['url'])) . "' style='" . ($icon_config['active'] == 'yes' ? 'display:inline-flex' : 'display:none') . ";" . $sfsi_premium_anchor_style . "' class=" . ($sfsi_premium_responsive_icons['settings']['icon_width_type'] === "Fixed icon width" ? 'sfsi_premium_responsive_fixed_width' : 'sfsi_premium_responsive_fluid') . "  >" . "\n";
						$icons .= "\t\t\t<div class='sfsi_premium_responsive_icon_item_container sfsi_premium_responsive_custom_icon sfsi_premium_responsive_icon_" . strtolower($icon) . "_container sfsi_premium_" . strtolower($sfsi_premium_responsive_icons['settings']['icon_size']) . "_button " . ("sfsi_premium_" . (strtolower($sfsi_premium_responsive_icons['settings']['text_align']) == "centered" ? 'centered' : 'left-align') . "_icon") . " " . ($sfsi_premium_responsive_icons['settings']['style'] == "Gradient" ? 'sfsi_premium_responsive_icon_gradient' : '') . "' style='" . $sfsi_premium_anchor_div_style . " background-color:" . (isset($icon_config['bg-color']) ? $icon_config['bg-color'] : '#73d17c') . "' >" . "\n";
						$icons .= "\t\t\t\t<img style='max-height: 25px;margin-bottom:0 !important' alt='' src='" . (isset($icon_config['icon']) ? $icon_config['icon'] : '#') . "'>" . "\n";
						$icons .= "\t\t\t\t<span style='color:#fff' >" . ($icon_config["text"]) . "</span>" . "\n";
						$icons .= "\t\t\t</div>" . "\n";
						$icons .= "\t\t</a>" . "\n\n";
					}
				}
				$icons .= "</div></div><!--end responsive_icons-->";
				return $icons;
			endif;
		}

		function sfsi_premium_total_count($icons)
		{
			// ini_set('max_execution_time', 300);
			$count = 0;
			$socialObj = new sfsi_plus_SocialHelper();
			$icon_counts =  sfsi_plus_getCounts(false);
			foreach ($icons as $icon_name => $icon) {
				if ($icon['active'] == "yes") {
					switch (strtolower($icon_name)) {
						case 'facebook':
							$count += (isset($icon_counts['fb_count']) ? $icon_counts['fb_count'] : 0);
							break;
						case 'twitter':
							$count += (isset($icon_counts['twitter_count']) ? $icon_counts['twitter_count'] : 0);
							break;
						case 'follow':
							$count += (isset($icon_counts['email_count']) ? $icon_counts['email_count'] : 0);
							break;
						case 'pinterest':
							$count += (isset($icon_counts['pin_count']) ? $icon_counts['pin_count'] : 0);
							break;
						case 'linkedin':
							$count += (isset($icon_counts['linkedIn_count']) ? $icon_counts['linkedIn_count'] : 0);
							break;
						case 'GooglePlus':
							$count += (isset($icon_counts['google_count']) ? $icon_counts['google_count'] : 0);
							break;
						case 'Whatsapp':
							$count += (isset($icon_counts['whatsapp_count']) ? $icon_counts['whatsapp_count'] : 0);
							break;
						case 'vk':
							$count += (isset($icon_counts['vk_count']) ? $icon_counts['vk_count'] : 0);
							break;
					}
				}
			}
			$count = get_option('sfsi_premium_icon_counts', 0);

			return $socialObj->format_num($count);
		}

		function sfsi_premium_sticky_total_count($icons)
		{
			// $option8 			= maybe_unserialize(get_option('sfsi_premium_section8_options', false));

			// $icons = $option8['sfsi_plus_sticky_bar'];
			$count = 0;
			$socialObj = new sfsi_plus_SocialHelper();
			$icon_counts =  sfsi_plus_getCounts(false);
			$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
			foreach ($icons as $icon_name => $icon) {
				if (strtolower($icon_name) == "follow") {
					$icon_name = "email";
				} elseif (strtolower($icon_name) == "linkedin") {
					$icon_name = "linkedIn";
				} elseif (strtolower($icon_name) == "odnoklassniki") {
					break;
				} elseif (strtolower($icon_name) == "qQ2") {
					break;
				}

				if ($icon['active'] == "yes" && $option4['sfsi_plus_' . lcfirst($icon_name) . '_countsDisplay'] == "yes") {
					switch (strtolower($icon_name)) {
						case 'facebook':
							$count += (isset($icon_counts['fb_count']) ? $icon_counts['fb_count'] : 0);
							break;
						case 'twitter':
							$count += (isset($icon_counts['twitter_count']) ? $icon_counts['twitter_count'] : 0);
							break;
						case 'email':
							$count += (isset($icon_counts['email_count']) ? $icon_counts['email_count'] : 0);
							break;
						case 'pinterest':
							$count += (isset($icon_counts['pin_count']) ? $icon_counts['pin_count'] : 0);
							break;
						case 'linkedIn':
							$count += (isset($icon_counts['linkedIn_count']) ? $icon_counts['linkedIn_count'] : 0);
							break;
						case 'whatsapp':
							$count += (isset($icon_counts['whatsapp_count']) ? $icon_counts['whatsapp_count'] : 0);
							break;
						case 'vk':
							$count += (isset($icon_counts['vk_count']) ? $icon_counts['vk_count'] : 0);
							break;
					}
				}
			}
			$count = get_option('sfsi_premium_sticky_icon_counts', 0);
			return $socialObj->format_num($count);
		}

		/*subscribe like*/
		function sfsi_plus_Subscribelike($permalink, $show_count, $sfsi_section5 = null)
		{
			global $socialObj;
			$socialObj = new sfsi_plus_SocialHelper();

			$sfsi_premium_section2_options = maybe_unserialize(get_option('sfsi_premium_section2_options', false));
			$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
			$sfsi_premium_section8_options = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
			if ($sfsi_section5 == null) {
				$option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
			} else {
				$option5 = $sfsi_section5;
			}

			$post_icons     = $option5['sfsi_plus_follow_icons_language'];
			$visit_icon1    = SFSI_PLUS_DOCROOT . '/images/visit_icons/Follow/icon_' . $post_icons . '.png';
			$visit_iconsUrl = SFSI_PLUS_PLUGURL . "images/";

			if (file_exists($visit_icon1)) {
				$visit_icon = $visit_iconsUrl . "visit_icons/Follow/icon_" . $post_icons . ".png";
			} else {
				$visit_icon = $visit_iconsUrl . "follow_subscribe.png";
			}

			$url = (isset($sfsi_premium_section2_options['sfsi_plus_email_url'])) ? $sfsi_premium_section2_options['sfsi_plus_email_url'] : 'https://follow.it/now';

			if ($option4['sfsi_plus_email_countsFrom'] == "manual") {
				$counts = $socialObj->format_num($option4['sfsi_plus_email_manualCounts']);
			} else {
				$counts = $socialObj->SFSI_getFeedSubscriber(sanitize_text_field(get_option('sfsi_premium_feed_id', false)));
			}

			$icon = '<a href="' . $url . '" target="_blank"><img nopin=nopin src="' . $visit_icon . '" alt="'.__( 'onpost_follow', 'ultimate-social-media-plus' ).'" /></a>';

			if ( $sfsi_premium_section8_options['sfsi_plus_icons_DisplayCounts'] == "yes" && $counts > 0 ) {
				$icon .= '<span class="bot_no">'.$counts.'</span>';
			}

			return $icon;
		}
		/*subscribe like*/

		/*twitter like*/
		function sfsi_plus_twitterlike($permalink, $show_count)
		{
			global $socialObj;
			$socialObj = new sfsi_plus_SocialHelper();
			$twitter_text = '';
			$sfsi_premium_section5_options = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
			$icons_language = $sfsi_premium_section5_options['sfsi_plus_icons_language'];
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
			$postid =  $socialObj->sfsi_get_the_ID();

			if (!empty($postid)) {
				$twitter_text = $socialObj->sfsi_get_custom_tweet_title();
			}
			return $socialObj->sfsi_twitterSharewithcount($permalink, $twitter_text, $show_count, $icons_language, $sfsi_premium_section5_options);
		}
		/*twitter like*/

		/* create fb like button */
		function sfsi_plus_FBlike($permalink, $show_count)
		{
			$send = 'false';
			$width = 180;
			$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
			$fb_like_html = '';

			if ($option8['sfsi_plus_rectfbshare'] == 'yes' && $option8['sfsi_plus_rectfb'] == 'yes') {
				$fb_like_html .= '<div class="fb-like" data-href="' . $permalink . '" data-action="like" data-share="true"';
			} else if ($option8['sfsi_plus_rectfb'] == 'no' && $option8['sfsi_plus_rectfbshare'] == 'yes') {
				$fb_like_html .= '<div class="fb-share-button" data-href="' . $permalink . '" ';
			} else {
				$fb_like_html .= '<div class="fb-like" data-href="' . $permalink . '" data-action="like" data-share="false"';
			}
			if ($show_count == 1) {
				$fb_like_html .= ' data-layout="button_count"';
			} else {
				$fb_like_html .= ' data-layout="button"';
			}
			$fb_like_html .= ' ></div>';
			return $fb_like_html;
		}

		function sfsi_plus_pinterest_Custom($permalink, $show_count = false)
		{
			$pinit_html = 'https://www.pinterest.com/pin/create/button/?url=&media=&description';

			$pinit_html = "<a href='" . $pinit_html . "' style='display:inline-block;'> <img class='sfsi_wicon' data-pin-nopin='true' alt='".__( 'Pin Share', 'ultimate-social-media-plus' )."' title='".__( 'Pin Share', 'ultimate-social-media-plus' )."' src='" . SFSI_PLUS_PLUGURL . "images/share_icons/Pinterest_Save/en_US_save.svg" . "' /></a>";
			return $pinit_html;
		}

		/* create pinit button */
		function sfsi_plus_pinitpinterest($permalink, $show_count)
		{
			//******************* Get custom meta data set in admin STARTS **************************************//
			$socialObj    = new sfsi_plus_SocialHelper();

			// $pin_it_html    = '<a data-pin-do="buttonPin" data-pin-save="true" href="https://www.pinterest.com/pin/create/button/?url='.$url.'&media='.$pinterest_img.'&description='.$pinterest_desc.'"></a>';

			$pinit_html = '<a href="https://www.pinterest.com/pin/create/button/?url=' . urlencode($permalink) . '&media=' . $socialObj->sfsi_pinit_image() . '&description=' . str_replace("+", "%20", str_replace('#', '%23', urlencode($socialObj->sfsi_pinit_description()))) . '" data-pin-do="buttonPin" data-pin-save="true"';
			if ($show_count) {
				$pinit_html .= ' data-pin-count="beside"';
			} else {
				$pinit_html .= ' data-pin-count="none"';
			}
			$pinit_html .= '></a>';

			return $pinit_html;
		}

		/* create add this  button */
		function sfsi_plus_Addthis($show_count, $permalink, $post_title)
		{
			$atiocn = ' <script type="text/javascript">
		var addthis_config = {
			url: "' . $permalink . '",
			title: "' . $post_title . '"
		}
	</script>';

			if ($show_count == 1) {
				$atiocn .= ' <div class="addthis_toolbox" addthis:url="' . $permalink . '" addthis:title="' . $post_title . '">
			<a class="addthis_counter addthis_pill_style share_showhide"></a>
		</div>';
				return $atiocn;
			} else {
				$atiocn .= '<div class="addthis_toolbox addthis_default_style addthis_20x20_style" addthis:url="' . $permalink . '" addthis:title="' . $post_title . '"><a class="addthis_button_compact " href="#">  <img nopin=nopin src="' . SFSI_PLUS_PLUGURL . 'images/sharebtn.png"  border="0" alt="'.__( 'Share', 'ultimate-social-media-plus' ).'" /></a></div>';
				return $atiocn;
			}
		}

		function sfsi_plus_Addthis_blogpost($show_count, $permalink, $post_title)
		{
			$atiocn = ' <script type="text/javascript">
		var addthis_config = {
			 url: "' . $permalink . '",
			 title: "' . $post_title . '"
		}
	</script>';

			if ($show_count == 1) {
				$atiocn .= ' <div class="addthis_toolbox" addthis:url="' . $permalink . '" addthis:title="' . $post_title . '">
              <a class="addthis_counter addthis_pill_style share_showhide"></a>
	   </div>';
				return $atiocn;
			} else {
				$atiocn .= '<div class="addthis_toolbox addthis_default_style addthis_20x20_style" addthis:url="' . $permalink . '" addthis:title="' . $post_title . '"><a class="addthis_button_compact " href="#">  <img nopin=nopin src="' . SFSI_PLUS_PLUGURL . 'images/sharebtn.png"  border="0" alt="'.__( 'Share', 'ultimate-social-media-plus' ).'" /></a></div>';
				return $atiocn;
			}
		}

		/* create linkedIn share button */
		function sfsi_LinkedInShare($url = '', $count = '')
		{
			$url = (isset($url)) ? $url : home_url();

			if ($count == 1) {
				return  $ifollow = '<script type="IN/Share" data-url="' . $url . '" data-counter="right"></script>';
			} else {
				return  $ifollow = '<script type="IN/Share" data-url="' . $url . '" ></script>';
			}
		}

		/* create reddit share button */
		function sfsi_redditShareButton( $url = '' ) {
			$url = ( isset( $url ) ) ? $url : home_url();
			$onclick = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=800');return false;";
			return $ifollow = '<a href="//www.reddit.com/submit?url=' . urlencode($url) . '" onclick="' . $onclick . '"><img nopin=nopin src="'.SFSI_PLUS_PLUGURL.'images/reddit-share.jpg" alt="'.__( 'submit to reddit', 'ultimate-social-media-plus' ).'" style="border:0" /></a>';
		}
