<?php
/*
Plugin Name: USM Premium
Plugin URI: https://www.ultimatelysocial.com
Description: The best social media plugin on the market. Allows you to add social media & share icons to your blog (esp. Facebook, Twitter, Email, RSS, Pinterest, Instagram, LinkedIn, Share-button). It offers a wide range of design options and other features.
Author: UltimatelySocial
Text Domain: usm-premium-icons
Domain Path: /languages
Author URI: https://www.ultimatelysocial.com
Version: 17.3
*/

if (!function_exists('sfsi_is_inside_builder')) {
	function sfsi_is_inside_builder() {

    if (class_exists('\Elementor\Plugin') && isset(\Elementor\Plugin::$instance->editor) && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
			return true;
		}

		if (isset($_GET['action']) && $_GET['action'] == 'elementor') {
			return true;
		}

		if (isset($_GET['elementor-preview'])) {
			return true;
		}

		if (isset($_GET['fl_builder'])) {
			return true;
		}

		if (isset($_GET['page']) && $_GET['page'] == 'seedprod_lite_builder') {
			return true;
		}

		return false;
	}
}

if (sfsi_is_inside_builder()) {
	return;
}

//***************************** Setting error reporting STARTS ***************************************//
if ( !function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if (!is_plugin_active('ultimate-social-media-plus/ultimate_social_media_icons.php')) {
	global $wpdb;
	function sfsi_plus_error_reporting()
	{
		global $wpdb;
		$option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));

		if (
			isset($option5['sfsi_plus_icons_suppress_errors'])

			&& !empty($option5['sfsi_plus_icons_suppress_errors'])

			&& "yes" == $option5['sfsi_plus_icons_suppress_errors']
		) {

			error_reporting(0);
			@ini_set('display_errors', 0);
			$wpdb->suppress_errors(true);
		}
	}
	//************************** Setting error reporting CLOSES ****************************************//

	sfsi_plus_error_reporting();

	define( 'SFSI_PLUS_PLUGINFILE', plugin_basename(__FILE__) );
	define( 'SFSI_PLUS_PLUGIN_PATH', plugin_dir_path(__FILE__) );
	include( SFSI_PLUS_PLUGIN_PATH . 'constants.php' );
	include( SFSI_PLUS_PLUGIN_PATH . 'includes.php');
	include( SFSI_PLUS_PLUGIN_PATH . 'init.php');

	/* plugin install and uninstall hooks */
	register_activation_hook(__FILE__, 'sfsi_premium_activate_plugin');
	register_deactivation_hook(__FILE__, 'sfsi_plus_deactivate_plugin');
	register_uninstall_hook(__FILE__, 'sfsi_plus_Unistall_plugin');
  
  $license_api_name = (false === get_option('sfsi_active_license_api_name')) ? SELLCODES_LICENSING : get_option('sfsi_active_license_api_name');
  $license = trim(get_option($license_api_name . '_license_key'));
  $status  = trim(get_option($license_api_name . '_license_status'));

  if (!empty($license) && "valid" == strtolower($status)) {
  
    if ((isset($option5['sfsi_plus_hook_priority_value'])  && !empty($option5['sfsi_plus_hook_priority_value'])) && ($option5['sfsi_plus_hook_priority_value'] > 20)) {
      $hook_priority_value = 20;
      $the_excerpt = 10;
      $priority_value = ($option5['sfsi_plus_hook_priority_value'] - $hook_priority_value);
      if ($priority_value <= $the_excerpt) {
        $priority_value =  $the_excerpt - $priority_value;
      } elseif ($priority_value > $the_excerpt) {
        $priority_value =  $the_excerpt;
      }
    } else {
      $priority_value = 10;
    }
    
    add_filter('the_content', 'sfsi_plus_beforaftereposts', isset($option5['sfsi_plus_hook_priority_value']) && !empty($option5['sfsi_plus_hook_priority_value']) ? $option5['sfsi_plus_hook_priority_value'] : 20);
    
    // showing before and after blog posts
    add_filter('get_the_excerpt', 'sfsi_plus_excerpt_filter', $priority_value);
    add_filter('the_excerpt', 'sfsi_plus_beforeafterblogposts', isset($option5['sfsi_plus_hook_priority_value'])  && !empty($option5['sfsi_plus_hook_priority_value']) ? $option5['sfsi_plus_hook_priority_value'] : 20);
    add_filter('the_content', 'sfsi_plus_content_beforeafterblogposts', isset($option5['sfsi_plus_hook_priority_value']) && !empty($option5['sfsi_plus_hook_priority_value']) ? $option5['sfsi_plus_hook_priority_value'] : 20);

    // showing icons after blog pagespost
    add_filter('the_excerpt', 'sfsi_plus_afterepages', 20);
    add_filter('the_content', 'sfsi_plus_afterepages', 20);
    
    // plugin action link
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sfsi_plus_action_links', -10);
    add_filter('upload_mimes', 'sfsi_premium_return_extra_mime');
    add_filter('wp_handle_upload_prefilter', 'sfsi_premium_js_upload');
    add_filter('body_class', 'sfsi_premium_body_class');
    
    // actions
    add_action('woocommerce_single_product_summary', 'sfsi_premium_woocomerce_before_icons', 12);
    add_action('woocommerce_share', 'sfsi_premium_woocomerce_after_icons');
    add_action('init', 'sfsi_plus_load_domain', 1);
    
    // redirect setting page hook
    add_action('admin_init', 'sfsi_plus_plugin_redirect');
    
    // and make sure it's called whenever WordPress loads
    add_action('wp', 'sfsi_plus_cronstarter_activation');
    
  }

	function sfsi_get_before_posts_icons($sfsi_section8 = null) {

		$icons_before  = '';
		$sfsi_plus_round_icon_before_after_post = sfsi_plus_shall_show_icons('round_icon_before_after_post');
		$sfsi_plus_rect_icon_before_after_post  = sfsi_plus_shall_show_icons('rect_icon_before_after_post');
		$sfsi_plus_responsive_icon_before_after_post  = sfsi_plus_shall_show_icons('responsive_icon_before_after_post');
		// var_dump($sfsi_plus_responsive_icon_before_after_post);die();

		$socialObj    = new sfsi_plus_SocialHelper();
		$postid       = $socialObj->sfsi_get_the_ID();

		if ($postid) {
			if ($sfsi_section8 == null) {
				$sfsi_section8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
			}
			$sfsi_section5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));

			$lineheight = $sfsi_section8['sfsi_plus_post_icons_size'];
			$lineheight = sfsi_plus_getlinhght($lineheight);

			$sfsi_plus_display_button_type = $sfsi_section8['sfsi_plus_display_button_type'];
			$sfsi_plus_show_item_onposts   = $sfsi_section8['sfsi_plus_show_item_onposts'];

			$post         = get_post($postid);
			$permalink    = get_permalink($postid);
			$post_title   = $post->post_title;
			$sfsiLikeWith = "45px;";

			if ($sfsi_section8['sfsi_plus_icons_DisplayCounts'] == "yes") {
				$show_count = 1;
				$sfsiLikeWith = "75px;";
			} else {
				$show_count = 0;
			}

			//checking for standard icons
			if (!isset($sfsi_section8['sfsi_plus_rectsub'])) {
				$sfsi_section8['sfsi_plus_rectsub'] = 'no';
			}
			if (!isset($sfsi_section8['sfsi_plus_rectfb'])) {
				$sfsi_section8['sfsi_plus_rectfb'] = 'yes';
			}
			if (!isset($sfsi_section8['sfsi_plus_recttwtr'])) {
				$sfsi_section8['sfsi_plus_recttwtr'] = 'no';
			}
			if (!isset($sfsi_section8['sfsi_plus_rectpinit'])) {
				$sfsi_section8['sfsi_plus_rectpinit'] = 'no';
			}
			if (!isset($sfsi_section8['sfsi_plus_rectfbshare'])) {
				$sfsi_section8['sfsi_plus_rectfbshare'] = 'no';
			}

			//checking for standard icons
			$txt = (isset($sfsi_section8['sfsi_plus_textBefor_icons'])) ? $sfsi_section8['sfsi_plus_textBefor_icons'] : "Please follow and like us:";

			$float = $sfsi_section8['sfsi_plus_icons_alignment'];
			if ($float == "center") {
				$style_parent = 'text-align: center;';
				$style = 'float:none; display: inline-block;';
			} else {
				$style_parent = 'text-align:' . $float . ';';
				$style = 'float:' . $float . ";";
			}
			if ($sfsi_plus_display_button_type == 'responsive_button') {
				$style .= " width:100%; ";
			}

			//icon selection
			$icons_before .= "<div class='sfsibeforpstwpr' style='" . $style_parent . "'>";

			$icon_style_class = '';
			if ( $sfsi_plus_display_button_type == 'standard_buttons' && $sfsi_plus_rect_icon_before_after_post ) {
				$icon_style_class .= ' sfsi_plus_Sicons_style_2';
			}

			$icons_before .= "<div class='sfsi_plus_Sicons " . $float . $icon_style_class."' style='" . $style . "'>";

			if ($sfsi_plus_display_button_type == 'standard_buttons') {

				if ($sfsi_plus_rect_icon_before_after_post) {

					if (
						$sfsi_section8['sfsi_plus_rectsub']		== 'yes' ||
						$sfsi_section8['sfsi_plus_rectfb']		== 'yes' ||
						$sfsi_section8['sfsi_plus_recttwtr'] 	== 'yes' ||
						$sfsi_section8['sfsi_plus_rectpinit'] 	== 'yes' ||
						$sfsi_section8['sfsi_plus_rectlinkedin'] == 'yes' ||
						$sfsi_section8['sfsi_plus_rectreddit'] 	== 'yes' ||
						$sfsi_section8['sfsi_plus_rectfbshare'] == 'yes'
					) {
						$icons_before .= "<div style='display: inline-block;margin-bottom: 0; margin-left: 0; margin-right: 8px; margin-top: 0; vertical-align: middle;width: auto;'><span>" . $txt . "</span></div>";
					}
					$icons_before .= DISPLAY_PREMIUM_RECTANGLE_ICONS(true);
					// if ($sfsi_section8['sfsi_plus_rectsub'] == 'yes') {
					// 	if ($show_count) {
					// 		$sfsiLikeWithsub = "93px";
					// 	} else {
					// 		$sfsiLikeWithsub = "64px";
					// 	}
					// 	if (!isset($sfsiLikeWithsub)) {
					// 		$sfsiLikeWithsub = $sfsiLikeWith;
					// 	}
					// 	$icons_before .= "<div class='sf_subscrbe' style='display: inline-block;vertical-align: middle;width: auto;'>" . sfsi_plus_Subscribelike($permalink, $show_count, $sfsi_section5) . "</div>";
					// }

					// if ($sfsi_section8['sfsi_plus_rectfb'] == 'yes') {
					// 	if ($show_count) { } else {
					// 		$sfsiLikeWithfb = "48px";
					// 	}
					// 	if (!isset($sfsiLikeWithfb)) {
					// 		$sfsiLikeWithfb = $sfsiLikeWith;
					// 	}

					// 	if ($sfsi_section5['sfsi_plus_Facebook_linking'] == "facebookcustomurl") {
					// 		$userDefineLink = ($sfsi_section5['sfsi_plus_facebook_linkingcustom_url']);
					// 		$icons_before .= "<div class='sf_fb' style='display: inline-block;vertical-align: middle;width: auto;'>" . $socialObj->sfsi_plus_FBlike($userDefineLink, $show_count) . "</div>";
					// 	} else {
					// 		$icons_before .= "<div class='sf_fb' style='display: inline-block;vertical-align: middle;width: auto;'>" . $socialObj->sfsi_plus_FBlike($permalink, $show_count) . "</div>";
					// 	}
					// }

					// if ($sfsi_section8['sfsi_plus_rectfbshare'] == 'yes') {
					// 	if ($show_count) { } else {
					// 		$sfsiLikeWithfb = "48px";
					// 	}
					// 	$permalink = $socialObj->sfsi_get_custom_share_link('facebook', $sfsi_section5);
					// 	$icons_before .= "<div class='sf_fb' style='display: inline-block;vertical-align: middle;width: auto;'>" . $socialObj->sfsiFB_Share($permalink, $show_count) . "</div>";
					// }

					// if ($sfsi_section8['sfsi_plus_recttwtr'] == 'yes') {
					// 	if ($show_count) {
					// 		$sfsiLikeWithtwtr = "77px";
					// 	} else {
					// 		$sfsiLikeWithtwtr = "56px";
					// 	}
					// 	if (!isset($sfsiLikeWithtwtr)) {
					// 		$sfsiLikeWithtwtr = $sfsiLikeWith;
					// 	}

					// 	$permalink = $socialObj->sfsi_get_custom_share_link('twitter', $sfsi_section5);
					// 	$icons_before .= "<div class='sf_twiter' style='display: inline-block;vertical-align: middle;width: auto;'>" . $socialObj->sfsi_plus_twitterlike($permalink, $show_count) . "</div>";
					// }
					// if ($sfsi_section8['sfsi_plus_rectpinit'] == 'yes') {
					// 	if ($show_count) {
					// 		$sfsiLikeWithpinit = "100px";
					// 	} else {
					// 		$sfsiLikeWithpinit = "auto";
					// 	}
					// 	$icons_before .= "<div class='sf_pinit' style='display: inline-block;vertical-align: top;text-align:left;width: " . $sfsiLikeWithpinit . "'>" . sfsi_plus_pinitpinterest($permalink, $show_count) . "</div>";
					// }
					// if ($sfsi_section8['sfsi_plus_rectlinkedin'] == 'yes') {
					// 	if ($show_count) {
					// 		$sfsiLikeWithlinkedin = "100px";
					// 	} else {
					// 		$sfsiLikeWithlinkedin = "auto";
					// 	}
					// 	$icons_before .= "<div class='sf_linkedin' style='display: inline-block;vertical-align: middle;text-align:left;width: " . $sfsiLikeWithlinkedin . "'>" . sfsi_LinkedInShare($permalink, $show_count) . "</div>";
					// }
					// if ($sfsi_section8['sfsi_plus_rectreddit'] == 'yes') {
					// 	if ($show_count) {
					// 		$sfsiLikeWithreddit = "auto";
					// 	} else {
					// 		$sfsiLikeWithreddit = "auto";
					// 	}
					// 	$icons_before .= "<div class='sf_reddit' style='display: inline-block;vertical-align: middle;text-align:left;width: " . $sfsiLikeWithreddit . "'>" . sfsi_redditShareButton($permalink) . "</div>";
					// }
				}
			} elseif ($sfsi_plus_display_button_type == 'responsive_button') {
				if (isset($sfsi_section8['sfsi_plus_responsive_icons_before_post_on_taxonomy']) && 'yes' === $sfsi_section8['sfsi_plus_responsive_icons_before_post_on_taxonomy'] && $sfsi_plus_responsive_icon_before_after_post) {
					$icons_before .= sfsi_premium_social_responsive_buttons(null, $sfsi_section8, '', $sfsi_section5);
				}
			} else if (sfsi_premium_is_any_standard_icon_selected() && $sfsi_plus_round_icon_before_after_post) {
				$icons_before .= "<div style='float:left;margin:0 0px; line-height:" . $lineheight . "px !important'><span>" . $txt . "</span></div>";
				$icons_before .= sfsi_plus_check_posts_visiblity(0, "yes", $sfsi_section5, $sfsi_section8);
			}

			$icons_before .= "</div><div style='clear:both'></div>";

			$icons_before .= "</div>";
		}
		return $icons_before;
	}

	function sfsi_get_after_posts_icons($sfsi_section8 = null)
	{

		$icons_after   = '';
		$sfsi_plus_round_icon_before_after_post = sfsi_plus_shall_show_icons('round_icon_before_after_post');
		$sfsi_plus_rect_icon_before_after_post = sfsi_plus_shall_show_icons('rect_icon_before_after_post');
		$sfsi_plus_responsive_icon_before_after_post = sfsi_plus_shall_show_icons('responsive_icon_before_after_post');
		$socialObj    = new sfsi_plus_SocialHelper();
		$postid       = $socialObj->sfsi_get_the_ID();

		if ($postid) {
			if ($sfsi_section8 == null) {
				$sfsi_section8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
			}
			$sfsi_section5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));

			$lineheight = $sfsi_section8['sfsi_plus_post_icons_size'];
			$lineheight = sfsi_plus_getlinhght($lineheight);

			$sfsi_plus_display_button_type = $sfsi_section8['sfsi_plus_display_button_type'];
			$sfsi_plus_show_item_onposts   = $sfsi_section8['sfsi_plus_show_item_onposts'];

			$post         = get_post($postid);
			$permalink    = get_permalink($postid);
			$post_title   = $post->post_title;
			$sfsiLikeWith = "45px;";

			if ($sfsi_section8['sfsi_plus_icons_DisplayCounts'] == "yes") {
				$show_count = 1;
				$sfsiLikeWith = "75px;";
			} else {
				$show_count = 0;
			}

			//checking for standard icons
			if (!isset($sfsi_section8['sfsi_plus_rectsub'])) {
				$sfsi_section8['sfsi_plus_rectsub'] = 'no';
			}
			if (!isset($sfsi_section8['sfsi_plus_rectfb'])) {
				$sfsi_section8['sfsi_plus_rectfb'] = 'yes';
			}
			if (!isset($sfsi_section8['sfsi_plus_recttwtr'])) {
				$sfsi_section8['sfsi_plus_recttwtr'] = 'no';
			}
			if (!isset($sfsi_section8['sfsi_plus_rectpinit'])) {
				$sfsi_section8['sfsi_plus_rectpinit'] = 'no';
			}
			if (!isset($sfsi_section8['sfsi_plus_rectfbshare'])) {
				$sfsi_section8['sfsi_plus_rectfbshare'] = 'no';
			}

			//checking for standard icons
			$txt = (isset($sfsi_section8['sfsi_plus_textBefor_icons'])) ? $sfsi_section8['sfsi_plus_textBefor_icons'] : __( 'Please follow and like us:', 'ultimate-social-media-plus' );

			$float = $sfsi_section8['sfsi_plus_icons_alignment'];
			if ($float == "center") {
				$style_parent = 'text-align: center;';
				$style = 'float:none; display: inline-block;';
			} else {
				$style_parent = 'text-align:' . $float . ';';
				$style = 'float:' . $float . ';';
			}


			if ($sfsi_plus_display_button_type == 'responsive_button') {
				$style .= "; width:100%; ";
			}
			//icon selection
			$icons_after .= "<div class='sfsiaftrpstwpr' style='" . $style_parent . "'>";

			$icon_style_class = '';
			if ( $sfsi_plus_display_button_type == 'standard_buttons' && $sfsi_plus_rect_icon_before_after_post ) {
                $mouse_hover_effect = sfsi_premium_mouseOver_effect_classlist();
				$icon_style_class .= ' sfsi_plus_Sicons_style_2 ' . $mouse_hover_effect;
			}

			$icons_after .= "<div class='sfsi_plus_Sicons " . $float . $icon_style_class."' style='" . $style . "'>";

			if ($sfsi_plus_display_button_type == 'standard_buttons') {
				if ($sfsi_plus_rect_icon_before_after_post) {
					if (
						$sfsi_section8['sfsi_plus_rectsub'] 	== 'yes' ||
						$sfsi_section8['sfsi_plus_rectfb'] 		== 'yes' ||
						$sfsi_section8['sfsi_plus_recttwtr'] 	== 'yes' ||
						$sfsi_section8['sfsi_plus_rectpinit'] 	== 'yes' ||
						$sfsi_section8['sfsi_plus_rectlinkedin'] == 'yes' ||
						$sfsi_section8['sfsi_plus_rectreddit'] 	== 'yes' ||
						$sfsi_section8['sfsi_plus_rectfbshare'] == 'yes'
					) {
						$icons_after .= "<div style='display: inline-block;margin-bottom: 0; margin-left: 0; margin-right: 8px; margin-top: 0; vertical-align: middle;width: auto;'><span>" . $txt . "</span></div>";
					}
					$icons_after .= DISPLAY_PREMIUM_RECTANGLE_ICONS(true);
				}
			} elseif ($sfsi_section8['sfsi_plus_display_button_type'] == 'responsive_button') {

				if (isset($sfsi_section8['sfsi_plus_responsive_icons_after_post_on_taxonomy']) && $sfsi_section8['sfsi_plus_responsive_icons_after_post_on_taxonomy'] == "yes" && $sfsi_plus_rect_icon_before_after_post) {
					$icons_after .= sfsi_premium_social_responsive_buttons(null, $sfsi_section8, '', $sfsi_section5);
				}
			} else if (sfsi_premium_is_any_standard_icon_selected() && $sfsi_plus_round_icon_before_after_post) {
				$icons_after .= "<div style='float:left;margin:0 0px; line-height:" . $lineheight . "px !important'><span>" . $txt . "</span></div>";
				$icons_after .= sfsi_plus_check_posts_visiblity(0, "yes", $sfsi_section5, $sfsi_section8);
			}
			$icons_after .= "</div>";
			$icons_after .= "</div>";
		}
		return $icons_after;
	}

	if (isset($option5) && !empty($option5)) {
		$option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
	}
  
	//functionality for before and after single posts
	function sfsi_plus_beforaftereposts($content)
	{
		$sfsi_plus_round_icon_before_after_post = sfsi_plus_shall_show_icons('round_icon_before_after_post');
		$sfsi_plus_rect_icon_before_after_post = sfsi_plus_shall_show_icons('rect_icon_before_after_post');
		$sfsi_plus_responsive_icon_before_after_post = sfsi_plus_shall_show_icons('responsive_icon_before_after_post');
		if ($sfsi_plus_rect_icon_before_after_post || $sfsi_plus_round_icon_before_after_post || $sfsi_plus_responsive_icon_before_after_post) {

			$org_content  = $content;
			$icons_before = '';
			$icons_after  = '';

			global $post;
			$current_post_type = isset($post->post_type) && !empty($post->post_type) ? $post->post_type : false;
			$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
			$sfsi_plus_display_button_type = $option8['sfsi_plus_display_button_type'];
			if ($sfsi_plus_display_button_type == 'responsive_button') {
				$select  = (isset($option8['sfsi_plus_choose_post_types_responsive'])) ? maybe_unserialize($option8['sfsi_plus_choose_post_types_responsive']) : array();
			}else{
				$select  = (isset($option8['sfsi_plus_choose_post_types'])) ? maybe_unserialize($option8['sfsi_plus_choose_post_types']) : array();
			}

			$select  = (is_array($select)) ? $select : array($select);

			if (!in_array("post", $select)) {
				array_push($select, "post");
			}

			if (!empty($select)) {
				$cond = is_single() && in_array($current_post_type, $select);
				if (function_exists('is_product')) {
					// var_dump(function_exists('is_product'));die();

					$cond = $cond && !is_product();
				}
			}
			// var_dump($cond ,'cond ',$current_post_type,$select,$option8['sfsi_plus_choose_post_types']);
			if ($cond) {
				$option8	= maybe_unserialize(get_option('sfsi_premium_section8_options', false));
				$lineheight = $option8['sfsi_plus_post_icons_size'];
				$lineheight = sfsi_plus_getlinhght($lineheight);
				$sfsi_plus_display_button_type = $option8['sfsi_plus_display_button_type'];
				$txt 		= (isset($option8['sfsi_plus_textBefor_icons'])) ? $option8['sfsi_plus_textBefor_icons'] : "Please follow and like us:";
				$float 		= $option8['sfsi_plus_icons_alignment'];

				if ($float == "center") {
					$style_parent = 'display:flex;justify-content:center;';
					$style = 'float:none; display: inline-block;';
				} else if ($float == "left") {
					$style_parent = 'display:flex;justify-content:flex-start;';
					$style 		  = 'float:' . $float . ';';
				} else if ($float == "right") {
					$style_parent = 'display:flex;justify-content:flex-end;';
					$style 		  = 'float:' . $float . ';';
				}

				if (($option8['sfsi_plus_display_before_posts'] == "yes" || (isset($option8['sfsi_plus_responsive_icons_before_post']) && $option8['sfsi_plus_responsive_icons_before_post'])) && $option8['sfsi_plus_show_item_onposts'] == "yes") {
					$icons_before .= '<div class="sfsibeforpstwpr" style="' . $style_parent . ' clear:both;" >';
					// var_dump($sfsi_plus_display_button_type);die();
					if ($sfsi_plus_display_button_type == 'standard_buttons' && $option8['sfsi_plus_display_before_posts'] == "yes") {
						if ($sfsi_plus_rect_icon_before_after_post) {
							$icons_before .= sfsi_plus_social_buttons_below($content = null);
						}
					} elseif ($sfsi_plus_display_button_type == 'responsive_button') {
						// var_dump($option8['sfsi_plus_responsive_icons_before_post']);
						if ($option8['sfsi_plus_responsive_icons_before_post'] == "yes" && $sfsi_plus_responsive_icon_before_after_post) {
							$icons_before .= sfsi_premium_social_responsive_buttons(null, $option8);
						}
					} else if (sfsi_premium_is_any_standard_icon_selected() && $sfsi_plus_round_icon_before_after_post && $option8['sfsi_plus_display_before_posts'] == "yes") {
						$icons_before .= "<div class='sfsi_plus_Sicons' style='" . $style . "'>";
						$icons_before .= "<div style='float:left;margin:0 0px; line-height:" . $lineheight . "px !important'><span>" . $txt . "</span></div>";
						$icons_before .= sfsi_plus_check_posts_visiblity(0, "yes", '', $option8);
						$icons_before .= "</div>";
						$icons_before .= "<div style='clear:both'></div>";
					}

					$icons_before .= '</div>';
				}

				if (($option8['sfsi_plus_display_after_posts'] == "yes" || (isset($option8['sfsi_plus_responsive_icons_after_post']) && $option8['sfsi_plus_responsive_icons_after_post'])) && $option8['sfsi_plus_show_item_onposts'] == "yes") {
					$icons_after .= '<div class="sfsiaftrpstwpr"  style="' . $style_parent . '">';

					if ($sfsi_plus_display_button_type == 'standard_buttons' && $option8['sfsi_plus_display_after_posts'] == "yes") {
						if ($sfsi_plus_rect_icon_before_after_post) {
							$icons_after .= sfsi_plus_social_buttons_below($content = null);
						}
					} elseif ($sfsi_plus_display_button_type == 'responsive_button') {
						if ($option8['sfsi_plus_responsive_icons_after_post'] == "yes" && $sfsi_plus_rect_icon_before_after_post) {
							$icons_after .= sfsi_premium_social_responsive_buttons(null, $option8);
						}
					} else if (sfsi_premium_is_any_standard_icon_selected()  && $sfsi_plus_round_icon_before_after_post && $option8['sfsi_plus_display_after_posts'] == "yes") {
						$icons_after .= "<div class='sfsi_plus_Sicons' style='" . $style . "'>";
						$icons_after .= "<div style='float:left;margin:0 0px; line-height:" . $lineheight . "px !important'><span>" . $txt . "</span></div>";
						$icons_after .= sfsi_plus_check_posts_visiblity(0, "yes", '', $option8);
						$icons_after .= "</div>";
					}
					$icons_after .= '</div>';
				}

				if (wp_is_mobile()) {
					if ($sfsi_plus_display_button_type == 'responsive_button') {
						if (isset($option8['sfsi_plus_responsive_icons_show_on_mobile']) && 'yes' == $option8['sfsi_plus_responsive_icons_show_on_mobile']) {
							$content = $icons_before . $org_content . $icons_after;
						} else {
							$content = $org_content;
						}
					} else {
						if (isset($option8['sfsi_plus_beforeafterposts_show_on_mobile']) && $option8['sfsi_plus_beforeafterposts_show_on_mobile'] == 'yes') {
							$content = $icons_before . $org_content . $icons_after;
						} else {
							$content = $org_content;
						}
					}
				} else {
					if ($sfsi_plus_display_button_type == 'responsive_button') {
						if (isset($option8['sfsi_plus_responsive_icons_show_on_desktop']) && 'yes' == $option8['sfsi_plus_responsive_icons_show_on_desktop']) {
							$content = $icons_before . $org_content . $icons_after;
						} else {
							$content = $org_content;
						}
					} else {
						if (isset($option8['sfsi_plus_beforeafterposts_show_on_desktop']) && $option8['sfsi_plus_beforeafterposts_show_on_desktop'] == 'yes') {
							$content = $icons_before . $org_content . $icons_after;
						} else {
							$content = $org_content;
						}
					}
				}
			}
		}
		return $content;
	}

	function sfsi_plus_excerpt_filter($excerpt)
	{
		if (!defined('USED_EXCERPT')) {
			define('USED_EXCERPT', "true");
		}
		return $excerpt;
	}

	function sfsi_plus_content_beforeafterblogposts($content)
	{

		if (!defined('USED_EXCERPT')) {
			return sfsi_plus_beforeafterblogposts($content);
		}
		return $content;
	}

	function sfsi_plus_beforeafterblogposts($content)
	{
		$sfsi_plus_round_icon_before_after_post = sfsi_plus_shall_show_icons('round_icon_before_after_post');
		$sfsi_plus_rect_icon_before_after_post = sfsi_plus_shall_show_icons('rect_icon_before_after_post');
		$sfsi_plus_responsive_icon_before_after_post = sfsi_plus_shall_show_icons('responsive_icon_before_after_post');
		$sfsi_section8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
		if ($sfsi_plus_rect_icon_before_after_post || $sfsi_plus_round_icon_before_after_post || $sfsi_plus_responsive_icon_before_after_post) {

			$org_content   = $content;
			$icons_before  = '';
			$icons_after   = '';

			$select  	   = (isset($sfsi_section8['sfsi_plus_choose_post_types'])) ? maybe_unserialize($sfsi_section8['sfsi_plus_choose_post_types']) : array();
			$select  	   = (is_array($select)) ? $select : array($select);

			if (!in_array("post", $select)) {
				array_push($select, "post");
			}
			// Check if it is Category page for selected taxonomies
			if (is_archive()) {

				if (isset($sfsi_section8['sfsi_plus_show_item_onposts']) && $sfsi_section8['sfsi_plus_show_item_onposts'] == "yes") {

					$arrSfsi_plus_taxonomies_for_icons = (isset($sfsi_section8['sfsi_plus_taxonomies_for_icons'])) ? maybe_unserialize($sfsi_section8['sfsi_plus_taxonomies_for_icons']) : array();


					$arrTax = is_array($arrSfsi_plus_taxonomies_for_icons) ? array_filter($arrSfsi_plus_taxonomies_for_icons) : array();

					if (!empty($arrTax)) {

						$termData = get_queried_object();

						if (isset($termData->taxonomy) && in_array($termData->taxonomy, $arrTax)) {

							if ($sfsi_section8['sfsi_plus_display_before_blogposts'] == "yes") {
								$icons_before  = sfsi_get_before_posts_icons($sfsi_section8);
							}
							if ($sfsi_section8['sfsi_plus_display_after_blogposts'] == "yes") {
								$icons_after   = sfsi_get_after_posts_icons($sfsi_section8);
							}
						}
					}
				} elseif ($sfsi_section8['sfsi_plus_show_item_onposts'] == "yes" && 'responsive_button' == $sfsi_section8['sfsi_plus_display_button_type'] && $sfsi_plus_responsive_icon_before_after_post) {

					if ($sfsi_section8['sfsi_plus_responsive_icons_before_post_on_taxonomy'] == "yes") {
						$icons_before  = sfsi_get_before_posts_icons($sfsi_section8);
					}
					if ($sfsi_section8['sfsi_plus_responsive_icons_after_post_on_taxonomy'] == "yes") {
						$icons_after   = sfsi_get_after_posts_icons($sfsi_section8);
					}
				}
			}
			// Check if it is default index page or posts page or custom loop of any post type in page
			else if (false != sfsi_premium_is_blog_page() || (false === is_single() && in_array(get_post_type(), $select))) {
				// var_dump(isset($sfsi_section8['sfsi_plus_show_item_onposts']) && $sfsi_section8['sfsi_plus_show_item_onposts'] == "yes" );
				if (isset($sfsi_section8['sfsi_plus_show_item_onposts']) && $sfsi_section8['sfsi_plus_show_item_onposts'] == "yes") {

					if ($sfsi_section8['sfsi_plus_display_before_blogposts'] == "yes") {
						$icons_before  = sfsi_get_before_posts_icons($sfsi_section8);
					}

					if ($sfsi_section8['sfsi_plus_display_after_blogposts'] == "yes") {
						$icons_after  = sfsi_get_after_posts_icons($sfsi_section8);
					}
				}
				if ($sfsi_section8['sfsi_plus_show_item_onposts'] == "yes" && 'responsive_button' == $sfsi_section8['sfsi_plus_display_button_type'] && $sfsi_plus_responsive_icon_before_after_post) {

					if ($sfsi_section8['sfsi_plus_responsive_icons_before_post_on_taxonomy'] == "yes") {
						$icons_before  = sfsi_premium_social_responsive_buttons(null, $sfsi_section8);
					}
					if ($sfsi_section8['sfsi_plus_responsive_icons_after_post_on_taxonomy'] == "yes") {
						$icons_after   = sfsi_premium_social_responsive_buttons(null, $sfsi_section8);
					}
				}
			}

			if (wp_is_mobile()) {
				if ($sfsi_section8['sfsi_plus_display_button_type'] == 'responsive_button') {
					if (isset($sfsi_section8['sfsi_plus_responsive_icons_show_on_mobile']) && 'yes' == $sfsi_section8['sfsi_plus_responsive_icons_show_on_mobile']  && $sfsi_plus_responsive_icon_before_after_post) {
						$content = $icons_before . $org_content . $icons_after;
					} else {
						$content = $org_content;
					}
				} else {
					if (isset($sfsi_section8['sfsi_plus_beforeafterposts_show_on_mobile']) && $sfsi_section8['sfsi_plus_beforeafterposts_show_on_mobile'] == 'yes') {
						$content = $icons_before . $org_content . $icons_after;
					} else {
						$content = $org_content;
					}
				}
			} else {
				if ($sfsi_section8['sfsi_plus_display_button_type'] == 'responsive_button') {

					if (isset($sfsi_section8['sfsi_plus_responsive_icons_show_on_desktop']) && 'yes' == $sfsi_section8['sfsi_plus_responsive_icons_show_on_desktop'] && $sfsi_plus_responsive_icon_before_after_post) {
						$content = $icons_before . $org_content . $icons_after;
					} else {
						$content = $org_content;
					}
				} else {
					if (isset($sfsi_section8['sfsi_plus_beforeafterposts_show_on_desktop']) && $sfsi_section8['sfsi_plus_beforeafterposts_show_on_desktop'] == 'yes') {
						$content = $icons_before . $org_content . $icons_after;
					} else {
						$content = $org_content;
					}
				}
			}
		}
		return $content;
	}
  
	function sfsi_plus_afterepages($content)
	{
		$sfsi_plus_round_icon_before_after_post = sfsi_plus_shall_show_icons('round_icon_before_after_post');
		$sfsi_plus_rect_icon_before_after_post = sfsi_plus_shall_show_icons('rect_icon_before_after_post');
		$sfsi_plus_responsive_icon_before_after_post = sfsi_plus_shall_show_icons('responsive_icon_before_after_post');
		$option8    = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
		if ($sfsi_plus_rect_icon_before_after_post || $sfsi_plus_round_icon_before_after_post || $sfsi_plus_responsive_icon_before_after_post) {

			$org_content = $content;
			$icons_before = '';
			$icons_after = '';

			if ("page" === get_post_type()) {
				$lineheight = $option8['sfsi_plus_post_icons_size'];
				$lineheight = sfsi_plus_getlinhght($lineheight);
				$sfsi_plus_display_button_type = $option8['sfsi_plus_display_button_type'];
				$txt 		= (isset($option8['sfsi_plus_textBefor_icons'])) ? $option8['sfsi_plus_textBefor_icons'] : "Please follow and like us:";
				$float 		= $option8['sfsi_plus_icons_alignment'];

				if ($float == "center") {
					$style_parent = 'display:flex;justify-content:center;';
					$style = 'float:none; display: inline-block;';
				} else if ($float == "left") {
					$style_parent = 'display:flex;justify-content:flex-start;';
					$style 		  = 'float:' . $float . ';';
				} else if ($float == "right") {
					$style_parent = 'display:flex;justify-content:flex-end;';
					$style 		  = 'float:' . $float . ';';
				}

				if (((($sfsi_plus_round_icon_before_after_post || $sfsi_plus_rect_icon_before_after_post) && $option8['sfsi_plus_display_after_pageposts'] == "yes" || (($sfsi_plus_responsive_icon_before_after_post && isset($option8['sfsi_plus_responsive_icons_after_pages']) && $option8['sfsi_plus_responsive_icons_after_pages'] == "yes")))) && $option8['sfsi_plus_show_item_onposts'] == "yes") {
					/*$icons_after .= '</br>';*/
					$icons_after .= '<div class="sfsiaftrpstwpr"  style="' . $style_parent . '">';
					if ($sfsi_plus_display_button_type == 'standard_buttons' && $option8['sfsi_plus_display_after_pageposts'] == "yes") {
						if ($sfsi_plus_rect_icon_before_after_post) {
							$icons_after .= sfsi_plus_social_buttons_below($content = null, '', $option8);
						}
					} elseif ($sfsi_plus_display_button_type == 'responsive_button') {
						if ($option8["sfsi_plus_responsive_icons_after_pages"] == "yes" && $sfsi_plus_responsive_icon_before_after_post) {
							$icons_after .= sfsi_premium_social_responsive_buttons(null, $option8);
						}
					} else if (sfsi_premium_is_any_standard_icon_selected() && $sfsi_plus_round_icon_before_after_post && $option8['sfsi_plus_display_after_pageposts'] == "yes") {
						$icons_after .= "<div class='sfsi_plus_Sicons' style='" . $style . "'>";
						$icons_after .= "<div style='float:left;margin:0 0px; line-height:" . $lineheight . "px !important'><span>" . $txt . "</span></div>";
						$icons_after .= sfsi_plus_check_posts_visiblity(0, "yes", '', $option8);
						$icons_after .= "</div>";
					}
					$icons_after .= '</div>';
				}

				if (((($sfsi_plus_round_icon_before_after_post || $sfsi_plus_rect_icon_before_after_post) && $option8['sfsi_plus_display_before_pageposts'] == "yes" || (($sfsi_plus_responsive_icon_before_after_post && isset($option8['sfsi_plus_responsive_icons_before_pages']) && $option8['sfsi_plus_responsive_icons_before_pages'] == "yes")))) && $option8['sfsi_plus_show_item_onposts'] == "yes") {
					/*$icons_after .= '</br>';*/
					$icons_before .= '<div class="sfsibeforpstwpr"  style="' . $style_parent . '">';
					if ($sfsi_plus_display_button_type == 'standard_buttons' && $option8['sfsi_plus_display_before_pageposts'] == "yes") {
						if ($sfsi_plus_rect_icon_before_after_post) {
							$icons_before .= sfsi_plus_social_buttons_below($content = null, '', $option8);
						}
					} elseif ($sfsi_plus_display_button_type == 'responsive_button') {
						if ($option8["sfsi_plus_responsive_icons_before_pages"] == "yes" && $sfsi_plus_responsive_icon_before_after_post) {
							$icons_before .= sfsi_premium_social_responsive_buttons(null, $option8);
						}
					} else if (sfsi_premium_is_any_standard_icon_selected() && $sfsi_plus_round_icon_before_after_post && $option8['sfsi_plus_display_before_pageposts'] == "yes") {
						$icons_before .= "<div class='sfsi_plus_Sicons' style='" . $style . "'>";
						$icons_before .= "<div style='float:left;margin:0 0px; line-height:" . $lineheight . "px !important'><span>" . $txt . "</span></div>";
						$icons_before .= sfsi_plus_check_posts_visiblity(0, "yes", '', $option8);
						$icons_before .= "</div>";
						$icons_before .= "<div style='clear:both'></div>";
					}
					$icons_before .= '</div>';
				}
			}

			if (wp_is_mobile()) {
				if ($option8['sfsi_plus_display_button_type'] == 'responsive_button') {
					if (isset($option8['sfsi_plus_responsive_icons_show_on_mobile']) && 'yes' == $option8['sfsi_plus_responsive_icons_show_on_mobile']) {
						$content = $icons_before . $org_content . $icons_after;
					} else {
						$content = $org_content;
					}
				} else {
					if (isset($option8['sfsi_plus_beforeafterposts_show_on_mobile']) && $option8['sfsi_plus_beforeafterposts_show_on_mobile'] == 'yes') {
						$content = $icons_before . $org_content . $icons_after;
					} else {
						$content = $org_content;
					}
				}
			} else {
				if ($option8['sfsi_plus_display_button_type'] == 'responsive_button') {
					if (isset($option8['sfsi_plus_responsive_icons_show_on_desktop']) && 'yes' == $option8['sfsi_plus_responsive_icons_show_on_desktop'] && $sfsi_plus_responsive_icon_before_after_post) {
						$content = $icons_before . $org_content . $icons_after;
					} else {
						$content = $org_content;
					}
				} else {
					if (isset($option8['sfsi_plus_beforeafterposts_show_on_desktop']) && $option8['sfsi_plus_beforeafterposts_show_on_desktop'] == 'yes') {
						$content = $icons_before . $org_content . $icons_after;
					} else {
						$content = $org_content;
					}
				}
			}
		}
		return $content;
	}
	
	function sfsi_premium_woocomerce_before_icons()
	{
		$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
		if (isset($option8['sfsi_plus_show_item_onposts']) && "yes" === $option8['sfsi_plus_show_item_onposts'] && ((isset($option8['sfsi_plus_display_before_woocomerce_desc']) && $option8['sfsi_plus_display_before_woocomerce_desc'] == 'yes'))) {
			return sfsi_woocomerce_icon_render($option8);
		}
	}
	function sfsi_premium_woocomerce_after_icons()
	{
		$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
		if (isset($option8['sfsi_plus_show_item_onposts']) && "yes" === $option8['sfsi_plus_show_item_onposts'] && ((isset($option8['sfsi_plus_display_after_woocomerce_desc']) && $option8['sfsi_plus_display_after_woocomerce_desc'] == 'yes'))) {
			return sfsi_woocomerce_icon_render($option8);
		}
	}
  
	function sfsi_plus_load_domain() {
		$plugin_dir = basename( dirname( __FILE__ ) ) . '/languages';
		load_plugin_textdomain( 'ultimate-social-media-plus', false, $plugin_dir );
	}

	
	function sfsi_plus_action_links( $mylinks ) {
		$mylinks[] = '<a href="' . admin_url("/admin.php?page=sfsi-plus-options") . '">'.__( 'Settings', 'ultimate-social-media-plus' ).'</a>';
		return $mylinks;
	}

	function sfsi_plus_plugin_redirect()
	{
		if (get_option('sfsi_premium_plugin_do_activation_redirect', false)) {
			delete_option('sfsi_premium_plugin_do_activation_redirect');
			wp_redirect(admin_url('admin.php?page=sfsi-plus-options'));
		}
	}

	function sfsi_premium_social_image_issues_support_link() { ?>

		<div style="float:left;margin-top:20px; clear: both;" class="imgTxt">
			<span><?php
				printf(
				    __( '%1$sSomething not working?%2$s Please read %3$sour guide.%4$s', 'ultimate-social-media-plus' ),
					'<b>',
					'</b>',
					'<a style="text-decoration:underline;" target="_blank" href="https://www.ultimatelysocial.com/making-the-right-image-show-up-when-sharing-on-social-media/">',
					'</a>'
				);
			?></span>
		</div>

	<?php }

	// Removing job queues which started before 24 hrs & not finished yet
	call_user_func(function () {

		// don't run on ajax calls
		if (defined('DOING_AJAX') && DOING_AJAX) {
			return;
		}

		// only run on front-end
		if (is_admin()) {
			return;
		}

		$sfsi_job_queue = sfsiJobQueue::getInstance();

		$jobQueueInstalled = get_option('sfsi_premium_job_queue_installed', false);

		if (false != $jobQueueInstalled) {
			$sfsi_job_queue->remove_unfinished_jobs(129600);
		}
	});

	function sfsi_premium_wp_loaded_fbcount_api_call()
	{
		// don't run on ajax calls
		if (defined('DOING_AJAX') && DOING_AJAX) {
			return true;
		}
		// only run on front-end
		if (is_admin()) {
			return true;
		}
		$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));

		if (is_string($option4)) {
			sfsi_premium_deserialize_options();
			$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
		}
		if (empty($GLOBALS['socialhelper']) && class_exists('sfsi_plus_SocialHelper')) {

			$GLOBALS['socialhelper'] = new sfsi_plus_SocialHelper();
		}
		if ($option4["sfsi_plus_display_counts"] === "yes" && $option4["sfsi_plus_facebook_countsDisplay"] == "yes" && $option4["sfsi_plus_fb_count_caching_active"] == "yes" && $option4["sfsi_plus_facebook_appid"] != "" && $option4["sfsi_plus_facebook_appsecret"] != "") {
			global $socialhelper;

			$fbSocialHelper = new sfsiFacebookSocialHelper();
			$fbSocialHelper->sfsi_fbcount_inbatch_api();
		}
	}
	
  function sfsi_premium_return_extra_mime($mimes)
	{
		return array_merge(
			$mimes,
			array(
				'js' => 'text/javascript',
			)
		);
	}

	function sfsi_premium_js_upload($file)
	{
		if ($file['type'] == "text/javascript" || $file['type'] == "application/javascript") {
			define('ALLOW_UNFILTERED_UPLOADS', true);
		}
		return $file;
	}

	function sfsi_plus_sf_instagram_count_fetcher()
	{
		$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
		if ($option4["sfsi_plus_display_counts"] === "yes") {
			$sfsi_plus_SocialHelper = new sfsi_plus_SocialHelper();
			$return_data = true;
			if ($option4["sfsi_plus_email_countsDisplay"] == "yes") {
				$feed_id		= sanitize_text_field(get_option('sfsi_plus_feed_id', false));
				$return_data = $return_data && $sfsi_plus_SocialHelper->SFSI_getFeedSubscriberFetch($feed_id);
			}
			if ($option4["sfsi_plus_instagram_countsDisplay"] == "yes" && $option4["sfsi_plus_instagram_countsFrom"] == "followers") {
				$return_data = $return_data && $sfsi_plus_SocialHelper->sfsi_get_instagramFollowersFetch();
			}
			return $return_data;
		} else {
			return true;
		}
	}
	$caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options', "a:0:{}"));
	if (isset($caching_debug_option["on"]) && $caching_debug_option["on"] === "yes" && isset($caching_debug_option["for"]) && $caching_debug_option["for"] == sfsi_premium_get_client_ip()) {
		sfsi_premium_wp_loaded_fbcount_api_call();
	}

	function sfsi_plus_youtube_count_fetcher()
	{
		$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
		if ($option4["sfsi_plus_display_counts"] === "yes") {
			$sfsi_plus_SocialHelper = new sfsi_plus_SocialHelper();
			$return_data = true;

			if ($option4["sfsi_plus_youtube_countsDisplay"] == "yes" && $option4["sfsi_plus_youtube_countsFrom"] == "subscriber") {
				$return_data =  $sfsi_plus_SocialHelper->sfsi_get_youtube_subs();
			}
			return $return_data;
		} else {
			return true;
		}
	}

	function sfsi_plus_cronstarter_activation()
	{
		// sfsi_plus_write_log(wp_next_scheduled( 'sfsi_plus_sf_instagram_count_fetcher' ));
		if (!wp_next_scheduled('sfsi_plus_sf_instagram_count_fetcher')) {
			wp_schedule_event(time(), 'hourly', 'sfsi_plus_sf_instagram_count_fetcher');
		}

		$sfsi_premium_cron = maybe_unserialize(get_option('sfsi_premium_cron'));

		if (((intval($sfsi_premium_cron['hourly']) + 3600) <  time() || $sfsi_premium_cron['hourly'] == "")) {
			sfsi_premium_wp_loaded_fbcount_api_call();
			$sfsi_premium_cron['hourly'] = time();
			update_option('sfsi_premium_cron', serialize($sfsi_premium_cron));
		}
		if (((intval($sfsi_premium_cron['daily']) + 86400) <  time() || $sfsi_premium_cron['daily'] == "")) {
			sfsi_plus_youtube_count_fetcher();
			$sfsi_premium_cron['daily'] = time();
			update_option('sfsi_premium_cron', serialize($sfsi_premium_cron));
		}
	}
	
  // create a scheduled event (if it does not exist already)
	function sfsi_premium_save_calculated_count()
	{
		$sfsi_plus_SocialHelper = new sfsi_plus_SocialHelper();
		$icon_counts = sfsi_plus_getCounts(false);
		$count = 0;
		$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
		$sfsi_premium_responsive_icons = (isset($option8["sfsi_plus_responsive_icons"]) ? $option8["sfsi_plus_responsive_icons"] : null);
		if ($option8["sfsi_plus_show_item_onposts"] == "yes" && $option8["sfsi_plus_display_button_type"] == "responsive_button") {
			foreach ($sfsi_premium_responsive_icons['default_icons'] as $icon_name => $icon) {
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
							// case 'GooglePlus': $count+=(isset($icon_counts['google_count'])?$icon_counts['google_count']:0);break;
						case 'Whatsapp':
							$count += (isset($icon_counts['whatsapp_count']) ? $icon_counts['whatsapp_count'] : 0);
							break;
						case 'vk':
							$count += (isset($icon_counts['vk_count']) ? $icon_counts['vk_count'] : 0);
							break;
					}
				}
			}
			update_option('sfsi_premium_icon_counts', $count);
		}
		return true;
	}

	function sfsi_premium_deserialize_options()
	{

		$arr_options = array('sfsi_premium_section1_options', 'sfsi_premium_section2_options', 'sfsi_premium_section3_options', 'sfsi_premium_section5_options', 'sfsi_premium_section6_options', 'sfsi_premium_section7_options', 'sfsi_premium_section8_options', 'sfsi_premium_section9_options', 'sfsi_premium_section4_options');
		$serialized_count = 0;
		foreach ($arr_options as $option) {
			$unserialize_option = maybe_unserialize(get_option($option, false));
			while (is_string($unserialize_option)) {
				$unserialize_option = maybe_unserialize($unserialize_option);
				$serialized_count++;
			}
			if (is_array($unserialize_option)) {
				update_option($option, serialize($unserialize_option));
			}
		}
		if ($serialized_count > 0) {
			$sfsi_premium_serialize_options_list = maybe_unserialize(get_option('sfsi_premium_serialize_options', false));

			$sfsi_premium_serialize_options_date = date("Y-m-d h:i");
			if ($sfsi_premium_serialize_options_list == false) {
				$sfsi_premium_serialize_options_list = array(array("sfsi_premium_serialize_options_date" => $sfsi_premium_serialize_options_date, "sfsi_premium_serialize_options_counts" => $serialized_count));
			} else {
				$sfsi_premium_serialize_options = array(
					'sfsi_premium_serialize_options_date'      => $sfsi_premium_serialize_options_date,
					'$sfsi_premium_serialize_options_counts' => $serialized_count
				);
				array_push($sfsi_premium_serialize_options_list, $sfsi_premium_serialize_options);
				if (count($sfsi_premium_serialize_options_list) > 10) {
					array_shift($sfsi_premium_serialize_options_list);
				}
			}
			update_option('sfsi_premium_serialize_options', serialize($sfsi_premium_serialize_options_list));
		}
	}

	function sfsi_premium_body_class( $classes ) {
		$sfsi_premium_pluginVersion = get_option( 'sfsi_premium_pluginVersion' );

		$classes[] = 'usm-premium-' . $sfsi_premium_pluginVersion . '-updated-' . get_option( 'sfsi_premium_plugin_update' );

		/* Add plugin current version class in body */
		$classes[] = 'sfsi_plus_' . $sfsi_premium_pluginVersion;

		/* Add class when count is enabled */
		$option4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
		if ( isset( $option4['sfsi_plus_display_counts'] ) && $option4['sfsi_plus_display_counts'] == "yes" ) {
			$classes[] = 'sfsi_plus_count_enabled';
		} else {
			$classes[] = 'sfsi_plus_count_disabled';
		}

		/* Add class for theme style */
		$option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
		if ( isset( $option3['sfsi_plus_actvite_theme'] ) ) {
			$classes[] = 'sfsi_plus_actvite_theme_'.$option3['sfsi_plus_actvite_theme'];
		}

		return $classes;
	}

} else {
	function sfsi_premium_conflict_admin_notice()
		{ ?>
		<div class="error">
			<p><?php _e( 'Cannot install both Ultimate Social Media PLUS and USM Premium.', 'ultimate-social-media-plus' ); ?></p>
		</div><?php
		@trigger_error( __( 'Cannot install both Ultimate Social Media PLUS and USM Premium.', 'ultimate-social-media-plus' ), E_USER_ERROR);
	}
	add_action('admin_notices', 'sfsi_premium_conflict_admin_notice');
	register_activation_hook(__FILE__, 'sfsi_premium_conflict_admin_notice');
}

add_action('plugins_loaded', function () {
	// Include footer banner
	include_once trailingslashit(__DIR__) . '/banner/misc.php';

	// Global Banner
	$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
	if ( isset( $option5['sfsi_plus_disable_promotions'] ) && "yes" !== $option5['sfsi_plus_disable_promotions'] ) {
		if (!class_exists('Inisev\Subs\InisevPlugPromo')) require_once trailingslashit(__DIR__) . 'promotion/misc.php';
		if (!defined('insPP_initialized')) new Inisev\Subs\InisevPlugPromo(__FILE__, 'usm-premium', __( 'USM Premium', 'ultimate-social-media-plus' ), 'sfsi-plus-options');
	}
});