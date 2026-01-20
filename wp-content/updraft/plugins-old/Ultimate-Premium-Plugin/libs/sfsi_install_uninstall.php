<?php
//adding some meta tags for facebook news feed
function sfsi_plus_checkmetas()
{
	$is_seo_plugin_active = false;

	if (!function_exists('get_plugins')) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$all_plugins = get_plugins();

	foreach ($all_plugins as $key => $plugin) {
		if (is_plugin_active($key)) {
			if (preg_match("/(seo|search engine optimization|meta tag|open graph|opengraph|og tag|ogtag)/im", $plugin['Name']) || preg_match("/(seo|search engine optimization|meta tag|open graph|opengraph|og tag|ogtag)/im", $plugin['Description'])) {
				$is_seo_plugin_active = true;
				break;
			}
		}
	}
	return $is_seo_plugin_active;
}

function sfsi_plus_add_fields_for_desktop_icons_order_option5($option5 = false, $option1 = false)
{

	$option5 = false != $option5 && is_array($option5) ? $option5 : maybe_unserialize(get_option('sfsi_premium_section5_options', false));
	$option1 = false != $option1 && is_array($option1) ? $option1 : maybe_unserialize(get_option('sfsi_premium_section1_options', false));

	$arrIcons = array(
		1 => "rss", 2 => "email", 3 => "fb", 4 => "twitter", 5 => "share", 6 => "youtube", 7 => "pinterest", 8 => "linkedin", 9 => "instagram", 10 => "houzz", 11 => "snapchat", 12 => "whatsapp", 13 => "skype", 14 => "vimeo", 15 => "soundcloud", 16 => "yummly", 17 => "flickr", 18 => "reddit", 19 => "tumblr", 20 => "fbmessenger", 21 => "mix", 22 => "ok", 23 => "telegram", 24 => "vk", 25 => "weibo", 26 => "xing", 27 => 'mastodon',
	);

	$arrDefaultIconsOrder = array();

	foreach ($arrIcons as $defaultIndex => $iconName) {

		$data = array(

			"iconName" => $iconName,
			"index"    => sfsi_getOldDesktopIconOrder($iconName, $defaultIndex, $option5)
		);

		array_push($arrDefaultIconsOrder, $data);
	}

	if (isset($option5['sfsi_plus_CustomIcons_order']) && !empty($option5['sfsi_plus_CustomIcons_order']) && is_string($option5['sfsi_plus_CustomIcons_order'])) {

		$sfsi_plus_CustomIcons_order = maybe_unserialize($option5['sfsi_plus_CustomIcons_order']);

		if (isset($sfsi_plus_CustomIcons_order) && !empty($sfsi_plus_CustomIcons_order) && is_array($sfsi_plus_CustomIcons_order)) {

			foreach ($sfsi_plus_CustomIcons_order as $iconData) {

				$data = array(

					"iconName" 			 => 'custom',
					"index"    			 => $iconData['order'],
					"customElementIndex" => $iconData['ele']
				);

				array_push($arrDefaultIconsOrder, $data);
			}

			// Now remove old data for custom icon order
			unset($option5['sfsi_plus_CustomIcons_order']);
		}
	} else {

		$customIcons = array();

		if (isset($option1['sfsi_custom_files']) && !empty($option1['sfsi_custom_files'])) {

			$sfsi_custom_files = $option1['sfsi_custom_files'];

			if (is_string($sfsi_custom_files)) {
				$customIcons = maybe_unserialize($sfsi_custom_files);
			} else if (is_array($sfsi_custom_files)) {
				$customIcons = $sfsi_custom_files;
			}
		}

		if (!empty($customIcons)) {

			foreach ($customIcons as $key => $value) {

				$data = array();
				$data['iconName']           = 'custom';
				$data['index']              = count($arrDefaultIconsOrder) + 1;
				$data['customElementIndex'] = $key;

				array_push($arrDefaultIconsOrder, $data);
			}
		}
	}


	// Now remove old order data for standard icons
	foreach ($arrIcons as $key => $iconName) {

		$key = ("fb" == $iconName) ? 'sfsi_plus_facebookIcon_order' : 'sfsi_plus_' . $iconName . 'Icon_order';

		if (isset($option5[$key]) && !empty($option5[$key])) {
			unset($option5[$key]);
		}
	}

	update_option('sfsi_premium_section5_options', serialize($option5));

	return $arrDefaultIconsOrder;
}

function sfsi_plus_add_new_icons_in_saved_desktop_mobile_order($option5 = false, $option1 = false)
{

	$option5 = false != $option5 && is_array($option5) ? $option5 : maybe_unserialize(get_option('sfsi_premium_section5_options', false));

	$option1 = false != $option1 && is_array($option1) ? $option1 : maybe_unserialize(get_option('sfsi_premium_section1_options', false));

	$arrNewIcons = array(21 => "fbmessenger",
        22 => "mix",
        23 => "ok",
        24 => "telegram",
        25 => "vk",
        26 => "weibo",
        27 => "xing",
        28 => 'mastodon');

	$desktopIconOrder   = array();

	if (isset($option5['sfsi_order_icons_desktop'])  && !empty($option5['sfsi_order_icons_desktop'])) {

		$sfsi_order_icons_desktop = $option5['sfsi_order_icons_desktop'];

		if (is_string($sfsi_order_icons_desktop)) {
			$desktopIconOrder = maybe_unserialize($sfsi_order_icons_desktop);
		} else if (is_array($sfsi_order_icons_desktop)) {
			$desktopIconOrder = $sfsi_order_icons_desktop;
		}
	}

	if (!empty($desktopIconOrder)) {

		$arrIcons 	 = (SFSI_PHP_VERSION_7 ? sfsi_premium_array_column($desktopIconOrder, 'iconName') : array_column($desktopIconOrder, 'iconName'));

		foreach ($arrNewIcons as $key => $iconName) :

			$dbKey = 'sfsi_plus_' . $iconName . '_display';

			if (
				isset($option1[$dbKey]) && !empty($option1[$dbKey]) && "yes" == $option1[$dbKey]
				&& !in_array($iconName, $arrIcons)
			) :

				$arrData = array(

					"iconName" => $iconName,
					"index"    => $key
				);

				array_push($desktopIconOrder, $arrData);

			endif;

		endforeach;

		$option5['sfsi_order_icons_desktop'] = $desktopIconOrder;
	}

	$mobileIconOrder   = array();

	if (isset($option5['sfsi_order_icons_mobile'])  && !empty($option5['sfsi_order_icons_mobile'])) {

		$sfsi_order_icons_mobile = $option5['sfsi_order_icons_mobile'];

		if (is_string($sfsi_order_icons_mobile)) {
			$mobileIconOrder = maybe_unserialize($sfsi_order_icons_mobile);
		} else if (is_array($sfsi_order_icons_mobile)) {
			$mobileIconOrder = $sfsi_order_icons_mobile;
		}
	}

	if (!empty($mobileIconOrder)) {

		$arrIcons 	 = (SFSI_PHP_VERSION_7 ? sfsi_premium_array_column($mobileIconOrder, 'iconName') : array_column($mobileIconOrder, 'iconName'));

		foreach ($arrNewIcons as $key => $iconName) :

			$dbKey = isset($option1['sfsi_plus_icons_onmobile']) && !empty($option1['sfsi_plus_icons_onmobile'])
				&& "yes" == $option1['sfsi_plus_icons_onmobile'] ? 'sfsi_plus_' . $iconName . '_mobiledisplay' : 'sfsi_plus_' . $iconName . '_display';

			if (
				isset($option1[$dbKey]) && !empty($option1[$dbKey]) && "yes" == $option1[$dbKey]
				&& !in_array($iconName, $arrIcons)
			) :

				$arrData = array(
					"iconName" => $iconName,
					"index"    => $key
				);

				array_push($mobileIconOrder, $arrData);

			endif;

		endforeach;

		$option5['sfsi_order_icons_mobile'] = $mobileIconOrder;
	}

	if ((!empty($mobileIconOrder)) || (!empty($desktopIconOrder))) {
		update_option('sfsi_premium_section5_options', serialize($option5));
	}
}


// Migrating cached fb share count of new version >= 9.7 stored in 'postmeta' table to 'options' table

function sfsi_premium_migrate_fb_cached_count()
{

	if (PLUGIN_CURRENT_VERSION >= 9.7) {

		global $wpdb;

		$sql = "SELECT post_id,meta_key,meta_value FROM `" . $wpdb->prefix . "postmeta` WHERE (`meta_key` LIKE 'sfsi-premium-fb-cumulative-cached-count' OR `meta_key` LIKE 'sfsi-premium-fb-uncumulative-cached-count') AND `meta_value`> 0";

		$arrOldCachedFbCount = $wpdb->get_results($sql);

		$fbSocial   = new sfsiFacebookSocialHelper();

		if (isset($arrOldCachedFbCount) && !empty($arrOldCachedFbCount)) {

			$arrActiveCachedCumulativeCount   = $fbSocial->sfsi_get_cached_data_fbcount(true);
			$arrActiveCachedUnCumulativeCount = $fbSocial->sfsi_get_cached_data_fbcount(false);

			$arrActiveCachedCumulativePostIds 	= array();
			$arrActiveCachedUnCumulativePostIds = array();

			if (isset($arrActiveCachedCumulativeCount) && !empty($arrActiveCachedCumulativeCount)) {
				$arrActiveCachedCumulativePostIds    = (SFSI_PHP_VERSION_7 ? sfsi_premium_array_column($arrActiveCachedCumulativeCount, "i") : array_column($arrActiveCachedCumulativeCount, "i"));
			}

			if (isset($arrActiveCachedUnCumulativePostIds) && !empty($arrActiveCachedUnCumulativePostIds)) {
				$arrActiveCachedUnCumulativePostIds    = (SFSI_PHP_VERSION_7 ? sfsi_premium_array_column($arrActiveCachedUnCumulativeCount, "i") : array_column($arrActiveCachedUnCumulativeCount, "i"));
			}

			$arrOldCachedCumulativeCount   = array();
			$arrOldCachedUnCumulativeCount = array();

			foreach ($arrOldCachedFbCount as $arrCountData) {

				$postId   = $arrCountData->post_id;
				$meta_key = $arrCountData->meta_key;
				$count 	  = intval($arrCountData->meta_value);

				switch ($meta_key) {

					case 'sfsi-premium-fb-cumulative-cached-count':

						if (!in_array($postId, $arrActiveCachedCumulativePostIds)) {
							array_push($arrOldCachedCumulativeCount, array("i" => $postId, "c" => $count));
						}

						break;

					case 'sfsi-premium-fb-uncumulative-cached-count':

						if (!in_array($postId, $arrActiveCachedUnCumulativePostIds)) {
							array_push($arrOldCachedUnCumulativeCount, array("i" => $postId, "c" => $count));
						}

						break;
				}
			}

			$oldCumulativeHomePageCount   = get_option('sfsi-premium-homepage-fb-cumulative-cached-count', false);
			$oldUnCumulativeHomePageCount = get_option('sfsi-premium-homepage-fb-uncumulative-cached-count', false);

			if ($oldCumulativeHomePageCount > 0) {
				array_push($arrOldCachedCumulativeCount, array("i" => -1, "c" => $oldCumulativeHomePageCount));
			}

			if ($oldUnCumulativeHomePageCount > 0) {
				array_push($arrOldCachedUnCumulativeCount, array("i" => -1, "c" => $oldUnCumulativeHomePageCount));
			}

			if (!empty($arrOldCachedCumulativeCount)) {
				$arrActiveCachedCumulativeCount = array_merge($arrActiveCachedCumulativeCount, $arrOldCachedCumulativeCount);
				$fbSocial->sfsi_update_cached_data_fbcount($arrActiveCachedCumulativeCount, false, true);
			}

			if (!empty($arrOldCachedUnCumulativeCount)) {
				$arrActiveCachedUnCumulativeCount = array_merge($arrActiveCachedUnCumulativeCount, $arrOldCachedUnCumulativeCount);
				$fbSocial->sfsi_update_cached_data_fbcount($arrActiveCachedUnCumulativeCount, false, false);
			}
		}
	}
}

function sfsi_plus_update_plugin()
{
	if ($feed_id = sanitize_text_field(get_option('sfsi_premium_feed_id'))) {
		if (is_numeric($feed_id)) {
			$sfsiId = SFSI_PLUS_updateFeedUrl();
			update_option('sfsi_premium_feed_id', sanitize_text_field($sfsiId->feed_id));
			update_option('sfsi_premium_redirect_url', sanitize_text_field($sfsiId->redirect_url));
		}
		if ("" == $feed_id) {
			$sfsiId = SFSI_PLUS_getFeedUrl();
			update_option('sfsi_premium_feed_id', sanitize_text_field($sfsiId->feed_id));
			update_option('sfsi_premium_redirect_url', sanitize_text_field($sfsiId->redirect_url));
		}
	}

	update_option("sfsi_premium_plugin_update", date("Y-m-d"));

	//Update version
	update_option("sfsi_premium_pluginVersion", PLUGIN_CURRENT_VERSION);

	if (get_option('sfsi_premium_curlErrorMessage')) {
		delete_option('sfsi_premium_curlErrorMessage');
	}

	if (!get_option('sfsi_premium_serverphpVersionnotification')) {
		add_option("sfsi_premium_serverphpVersionnotification", "yes");
	}
	if (!get_option('sfsi_plus_banner_popups')) {
		add_option("sfsi_plus_banner_popups", "yes");
	}
	/** reset the curl message on update **/


	/** end reset the curl message on update **/
	$option9 = maybe_unserialize(get_option('sfsi_premium_section9_options', false));

	if (isset($option9) && !empty($option9)) {

		if (!isset($option9['sfsi_plus_form_privacynotice_text'])) {
			$option9['sfsi_plus_form_privacynotice_text'] = 'We will treat your data confidentially';
		}

		if (!isset($option9['sfsi_plus_form_privacynotice_font'])) {
			$option9['sfsi_plus_form_privacynotice_font'] = 'Helvetica,Arial,sans-serif';
		}
		if (!isset($option9['sfsi_plus_form_privacynotice_fontcolor'])) {
			$option9['sfsi_plus_form_privacynotice_fontcolor'] = '#000000';
		}

		if (!isset($option9['sfsi_plus_form_privacynotice_fontsize'])) {
			$option9['sfsi_plus_form_privacynotice_fontsize'] = 20;
		}

		if (!isset($option9['sfsi_plus_form_privacynotice_fontalign'])) {
			$option9['sfsi_plus_form_privacynotice_fontalign'] = 'center';
		}
	} else {
		/* subscription form */
		$option9 = array(
			'sfsi_plus_form_adjustment'			=> 'yes',
			'sfsi_plus_form_height'				=> '180',
			'sfsi_plus_form_width' 				=> '230',
			'sfsi_plus_form_border'				=> 'yes',
			'sfsi_plus_form_border_thickness'	=> '1',
			'sfsi_plus_form_border_color'		=> '#b5b5b5',
			'sfsi_plus_form_background'			=> '#ffffff',

			'sfsi_plus_form_heading_text'		=> __( 'Get new posts by email:', 'ultimate-social-media-plus' ),
			'sfsi_plus_form_heading_font'		=> 'Helvetica,Arial,sans-serif',
			'sfsi_plus_form_heading_fontstyle'	=> 'bold',
			'sfsi_plus_form_heading_fontcolor'	=> '#000000',
			'sfsi_plus_form_heading_fontsize'	=> '16',
			'sfsi_plus_form_heading_fontalign'	=> 'center',

			'sfsi_plus_form_field_text'			=> __( 'Enter your email', 'ultimate-social-media-plus' ),
			'sfsi_plus_form_field_font'			=> 'Helvetica,Arial,sans-serif',
			'sfsi_plus_form_field_fontstyle'	=> 'normal',
			'sfsi_plus_form_field_fontcolor'	=> '#000000',
			'sfsi_plus_form_field_fontsize'		=> '14',
			'sfsi_plus_form_field_fontalign'	=> 'center',

			'sfsi_plus_form_button_text'		=> __( 'Subscribe', 'ultimate-social-media-plus' ),
			'sfsi_plus_form_button_font'		=> 'Helvetica,Arial,sans-serif',
			'sfsi_plus_form_button_fontstyle'	=> 'bold',
			'sfsi_plus_form_button_fontcolor'	=> '#000000',
			'sfsi_plus_form_button_fontsize'	=> '16',
			'sfsi_plus_form_button_fontalign'	=> 'center',
			'sfsi_plus_form_button_background'	=> '#dedede',

			'sfsi_plus_form_privacynotice_text'		 => __( 'We will treat your data confidentially', 'ultimate-social-media-plus' ),
			'sfsi_plus_form_privacynotice_font'		 => 'Helvetica,Arial,sans-serif',
			'sfsi_plus_form_privacynotice_fontcolor' => '#000000',
			'sfsi_plus_form_privacynotice_fontsize'	 => '16',
			'sfsi_plus_form_privacynotice_fontalign' => 'center'
		);
	}

	update_option('sfsi_premium_section9_options',  serialize($option9));

	$sfsi_premium_instagram_sf_count = maybe_unserialize(get_option('sfsi_premium_instagram_sf_count', false));
	/*Extra important options*/
	if ($sfsi_premium_instagram_sf_count) {
		$sfsi_premium_instagram_sf_count = array(
			"date_sf" => strtotime(date("Y-m-d")),
			"date_instagram" => strtotime(date("Y-m-d")),
			"sfsi_plus_sf_count" => "",
			"sfsi_plus_instagram_count" => ""
		);
		add_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
	} else {
		$sfsi_premium_instagram_sf_count["data_sf"] = $sfsi_premium_instagram_sf_count["data"];
		$sfsi_premium_instagram_sf_count["date_instagram"] = $sfsi_premium_instagram_sf_count["data"];
		update_option('sfsi_premium_instagram_sf_count', serialize($sfsi_premium_instagram_sf_count));
	}

	$sfsi_premium_youtube_count = maybe_unserialize(get_option('sfsi_premium_youtube_count', false));

	/*Extra important options*/
	if (false === $sfsi_premium_youtube_count) {
		$sfsi_plus_SocialHelper = new sfsi_plus_SocialHelper();
		$sfsi_premium_youtube_count["sfsi_plus_count"] = $sfsi_plus_SocialHelper->sfsi_get_youtube_subs();
		update_option('sfsi_premium_youtube_count', serialize($sfsi_premium_youtube_count));
	}
	$sfsi_premium_cron = maybe_unserialize(get_option('sfsi_premium_cron', false));
	if (isset($sfsi_premium_cron) && !empty($sfsi_premium_cron)) {
		$sfsi_premium_cron = array(
			"daily" => $sfsi_premium_cron['daily'],
			"hourly" => $sfsi_premium_cron['hourly'],
		);
		update_option('sfsi_premium_cron',  serialize($sfsi_premium_cron));
	}else{
		$sfsi_premium_cron = array(
			"daily" => (time()-86400),
			"hourly" => (time()-3600),
		);
		add_option('sfsi_premium_cron',  serialize($sfsi_premium_cron));
	}

	/*Float Icon setting*/
	$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
	$sfsi_plus_responsive_icons_default = array(
		"default_icons" => array(
			"facebook" => array("active" => "yes", "text" => __( "Share on Facebook", "usm-premium-icons" ), "url" => ""),
			"Twitter" => array("active" => "yes", "text" => __( "Tweet", "usm-premium-icons" ), "url" => ""),
			"Follow" => array("active" => "yes", "text" => __( "Follow us", "usm-premium-icons" ), "url" => ""),
			"pinterest" => array("active" => "no", "text" => __( "Save", "usm-premium-icons" ), "url" => ""),
			"Linkedin" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"Whatsapp" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"vk" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"Odnoklassniki" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"Telegram" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"Weibo" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"QQ2" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"xing" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
		),
		"custom_icons" => array(),
		"settings" => array(
			"icon_size" => "Medium",
			"icon_width_type" => "Fully responsive",
			"icon_width_size" => 240,
			"edge_type" => "Round",
			"edge_radius" => 5,
			"style" => "Gradient",
			"margin" => 10,
			"text_align" => "Centered",
			"show_count" => "no",
			"responsive_mobile_icons" => "yes",
			"counter_color" => "#aaaaaa",
			"counter_bg_color" => "#fff",
			"share_count_text" => __( "SHARES", "usm-premium-icons" ),
			"margin_above" => 10,
			"margin_below" => 10
		)
	);
	if (isset($option8) && !empty($option8)) {
		if (!isset($option8['sfsi_plus_icons_floatMargin_top'])) {
			$option8['sfsi_plus_icons_floatMargin_top']    = '';
			$option8['sfsi_plus_icons_floatMargin_bottom'] = '';
			$option8['sfsi_plus_icons_floatMargin_left']   = '';
			$option8['sfsi_plus_icons_floatMargin_right']  = '';
		}
		if (!isset($option8['sfsi_plus_rectpinit'])) {
			$option8['sfsi_plus_rectpinit'] = 'no';
		}
		if (!isset($option8['sfsi_plus_rectfbshare'])) {
			$option8['sfsi_plus_rectfbshare'] = 'no';
		}

		if (!isset($option8['sfsi_plus_exclude_page'])) {
			$option8['sfsi_plus_exclude_page'] 	= 'no';
		}

		// *** New exclusion rule added for Custom Post types & Taxnomies in VERISON 4.0  STARTS **** //
		if (!isset($option8['sfsi_plus_switch_exclude_custom_post_types'])) {
			$option8['sfsi_plus_switch_exclude_custom_post_types'] = 'no';
		}
		if (!isset($option8['sfsi_plus_list_exclude_custom_post_types'])) {
			$option8['sfsi_plus_list_exclude_custom_post_types'] = serialize(array());
		}

		if (!isset($option8['sfsi_plus_switch_exclude_taxonomies'])) {
			$option8['sfsi_plus_switch_exclude_taxonomies'] = 'no';
		}
		if (!isset($option8['sfsi_plus_switch_exclude_taxonomies'])) {
			$option8['sfsi_plus_list_exclude_taxonomies'] = serialize(array());
		}
		// *** New exclusion rule added for Custom Post types & Taxnomies in VERISON 4.0  CLOSES **** //


		// *** New inclusion rule added for in VERISON 8.4  STARTS **** //

		if (!isset($option8['sfsi_plus_icons_rules'])) {
			$option8['sfsi_plus_icons_rules'] = 2;
		}

		if (!isset($option8['sfsi_plus_include_home'])) {
			$option8['sfsi_plus_include_home'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_page'])) {
			$option8['sfsi_plus_include_page'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_post'])) {
			$option8['sfsi_plus_include_post'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_tag'])) {
			$option8['sfsi_plus_include_tag'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_category'])) {
			$option8['sfsi_plus_include_category'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_date_archive'])) {
			$option8['sfsi_plus_include_date_archive'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_author_archive'])) {
			$option8['sfsi_plus_include_author_archive'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_search'])) {
			$option8['sfsi_plus_include_search'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_url'])) {
			$option8['sfsi_plus_include_url'] = 'no';
		}
		if (!isset($option8['sfsi_plus_include_urlKeywords'])) {
			$option8['sfsi_plus_include_urlKeywords'] = array();
		}
		if (!isset($option8['sfsi_plus_switch_include_custom_post_types'])) {
			$option8['sfsi_plus_switch_include_custom_post_types'] = 'no';
		}
		if (!isset($option8['sfsi_plus_list_include_custom_post_types'])) {
			$option8['sfsi_plus_list_include_custom_post_types'] = serialize(array());
		}
		if (!isset($option8['sfsi_plus_switch_include_taxonomies'])) {
			$option8['sfsi_plus_switch_include_taxonomies'] = 'no';
		}
		if (!isset($option8['sfsi_plus_list_include_taxonomies'])) {
			$option8['sfsi_plus_list_include_taxonomies'] = serialize(array());
		}
		// *** New inclusion rule added for Custom Post types & Taxnomies in VERISON 4.0  CLOSES **** //

		if (!isset($option8['sfsi_plus_textBefor_icons_font'])) {
			$option8['sfsi_plus_textBefor_icons_font'] = 'inherit';
		}
		if (!isset($option8['sfsi_plus_textBefor_icons_fontcolor'])) {
			$option8['sfsi_plus_textBefor_icons_fontcolor'] = '#000000';
		}

		if (!isset($option8['sfsi_plus_shortcode_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_shortcode_horizontal_verical_Alignment'] = 'Horizontal';
		}

		if (!isset($option8['sfsi_plus_display_after_pageposts'])) {
			$option8['sfsi_plus_display_after_pageposts'] = 'no';
		}
		if (!isset($option8['sfsi_plus_display_before_pageposts'])) {
			$option8['sfsi_plus_display_before_pageposts'] = 'no';
		}

		if (!isset($option8['sfsi_plus_taxonomies_for_icons'])) {
			$option8['sfsi_plus_taxonomies_for_icons'] = serialize(array());
		}

		if (!isset($option8['sfsi_plus_post_icons_vertical_spacing'])) {
			$option8['sfsi_plus_post_icons_vertical_spacing'] = 5;
		}

		// include/exclude rule applies to  section . defaults to round icon-widget, round icon-define-localtion, round-icon-shortcode. start
		if (!isset($option8['sfsi_plus_display_on_all_icons'])) {
			$option8['sfsi_plus_display_on_all_icons'] = 'no';
		}
		if (!isset($option8['sfsi_plus_display_rule_round_icon_widget'])) {
			$option8['sfsi_plus_display_rule_round_icon_widget'] = 'yes';
		}
		if (!isset($option8['sfsi_plus_display_rule_round_icon_define_location'])) {
			$option8['sfsi_plus_display_rule_round_icon_define_location'] = 'yes';
		}
		if (!isset($option8['sfsi_plus_display_rule_round_icon_shortcode'])) {
			$option8['sfsi_plus_display_rule_round_icon_shortcode'] = 'yes';
		}
		if (!isset($option8['sfsi_plus_display_rule_round_icon_before_after_post'])) {
			$option8['sfsi_plus_display_rule_round_icon_before_after_post'] = 'no';
		}
		if (!isset($option8['sfsi_plus_display_rule_rect_icon_before_after_post'])) {
			$option8['sfsi_plus_display_rule_rect_icon_before_after_post'] = 'no';
		}
		if (!isset($option8['sfsi_plus_display_rule_rect_icon_shortcode'])) {
			$option8['sfsi_plus_display_rule_rect_icon_shortcode'] = 'no';
		}
		if (!isset($option8['sfsi_plus_display_before_woocomerce_desc'])) {
			$option8['sfsi_plus_display_before_woocomerce_desc'] = 'no';
		}
		if (!isset($option8['sfsi_plus_display_after_woocomerce_desc'])) {
			$option8['sfsi_plus_display_after_woocomerce_desc'] = 'no';
		}
		if (!isset($option8['sfsi_plus_responsive_icons_after_pages'])) {
			$option8['sfsi_plus_responsive_icons_after_pages'] = 'no';
		}
		if (!isset($option8['sfsi_plus_responsive_icons_before_pages'])) {
			$option8['sfsi_plus_responsive_icons_before_pages'] = 'no';
		}
		if (!isset($option8['sfsi_plus_responsive_icons_before_post'])) {
			$option8['sfsi_plus_responsive_icons_before_post'] = 'no';
		}
		if (!isset($option8['sfsi_plus_responsive_icons_after_post'])) {
			$option8['sfsi_plus_responsive_icons_after_post'] = 'no';
		}
		if (!isset($option8['sfsi_plus_responsive_icons_after_post_on_taxonomy'])) {
			$option8['sfsi_plus_responsive_icons_after_post_on_taxonomy'] = 'no';
		}
		if (!isset($option8['sfsi_plus_responsive_icons_before_post_on_taxonomy'])) {
			$option8['sfsi_plus_responsive_icons_before_post_on_taxonomy'] = 'no';
		}
		if (!isset($option8['sfsi_plus_responsive_icons_show_on_desktop'])) {
			$option8['sfsi_plus_responsive_icons_show_on_desktop'] = 'yes';
		}
		if (!isset($option8['sfsi_plus_responsive_icons_show_on_mobile'])) {
			$option8['sfsi_plus_responsive_icons_show_on_mobile'] = 'yes';
		}


		if (!isset($option8['sfsi_plus_post_mobile_icons_size'])) {
			$option8['sfsi_plus_post_mobile_icons_size'] = 40;
		}
		if (!isset($option8['sfsi_plus_post_mobile_icons_spacing'])) {
			$option8['sfsi_plus_post_mobile_icons_spacing'] = 5;
		}
		if (!isset($option8['sfsi_plus_post_mobile_icons_vertical_spacing'])) {
			$option8['sfsi_plus_post_mobile_icons_vertical_spacing'] = 5;
		}


		if (!isset($option8['sfsi_plus_sticky_bar'])) {
			$option8['sfsi_plus_sticky_bar'] = 'no';
		}
		$sfsi_plus_sticky_icons_default = array(
			"default_icons" => array(
				"facebook" => array("active" => "yes", "url" => ""),
				"Twitter" => array("active" => "yes", "url" => ""),
				"Follow" => array("active" => "yes",  "url" => ""),
				"pinterest" => array("active" => "no", "url" => ""),
				"Linkedin" => array("active" => "no", "url" => ""),
				"Whatsapp" => array("active" => "no", "url" => ""),
				"vk" => array("active" => "no", "url" => ""),
				"Odnoklassniki" => array("active" => "no", "url" => ""),
				"Telegram" => array("active" => "no", "url" => ""),
				"Weibo" => array("active" => "no", "url" => ""),
				"QQ2" => array("active" => "no", "url" => ""),
				"xing" => array("active" => "no", "url" => ""),
			),
			"custom_icons" => array(),
			"settings" => array(
				"desktop" => "no",
				"desktop_width" => 782,
				"desktop_placement" => "left",
				"display_position" => 0,
				"desktop_placement_direction" => "up",
				"mobile" => "no",
				"mobile_width" => 784,
				"mobile_placement" => "left",
				"counts" => 0,
				"bg_color" => "#000000",
				"color" => "#ffffff",
				"share_count_text" => __( "SHARE", "usm-premium-icons" ),
			)
		);
		if (isset($option8['sfsi_plus_sticky_icons'])) {
			if (isset($option8['sfsi_plus_sticky_icons']['default_icons'])) {
				foreach ($sfsi_plus_sticky_icons_default['default_icons'] as $index => $data) {
					if (!isset($option8['sfsi_plus_sticky_icons']['default_icons'][$index])) {
						$option8['sfsi_plus_sticky_icons']['default_icons'][$index] = $data;
					}
				}
				foreach ($sfsi_plus_sticky_icons_default['settings'] as $index => $data) {
					if (!isset($option8['sfsi_plus_sticky_icons']['settings'][$index])) {
						$option8['sfsi_plus_sticky_icons']['settings'][$index] = $data;
					}
				}
			} else {
				$option8['sfsi_plus_sticky_icons']['default_icons'] = $sfsi_plus_sticky_icons_default['default_icons'];
			}
		} else {
			$option8['sfsi_plus_sticky_icons'] = $sfsi_plus_sticky_icons_default;
		}

		if (isset($option8['sfsi_plus_responsive_icons'])) {
			if (isset($option8['sfsi_plus_responsive_icons']['default_icons'])) {
				foreach ($sfsi_plus_responsive_icons_default['default_icons'] as $index => $data) {
					if (!isset($option8['sfsi_plus_responsive_icons']['default_icons'][$index])) {
						$option8['sfsi_plus_responsive_icons']['default_icons'][$index] = $data;
					}
				}
				foreach ($sfsi_plus_responsive_icons_default['settings'] as $index => $data) {
					if (!isset($option8['sfsi_plus_responsive_icons']['settings'][$index])) {
						$option8['sfsi_plus_responsive_icons']['settings'][$index] = $data;
					}
				}
			} else {
				$option8['sfsi_plus_responsive_icons']['default_icons'] = $sfsi_plus_responsive_icons_default['default_icons'];
			}
		} else {
			$option8['sfsi_plus_responsive_icons'] = $sfsi_plus_responsive_icons_default;
		}
		if (!isset($option8['sfsi_plus_mobile_size_space_beforeafterposts'])) {
			$option8['sfsi_plus_mobile_size_space_beforeafterposts'] = 'no';
		}

		if (isset($option8["sfsi_plus_icon_hover_custom_icon_url"])) {
			$option8["sfsi_plus_icon_hover_custom_icon_url"] = "";
		}

		update_option('sfsi_premium_section8_options', serialize($option8));
	}

	// Add key for choosing  custom icons on mobile in Question 1-> Want to show different icons for mobile?
	$option2 = maybe_unserialize(get_option('sfsi_premium_section2_options', false));
	$option1 = maybe_unserialize(get_option('sfsi_premium_section1_options', false));

	if (isset($option1) && !empty($option1)) {

		if (!isset($option1['sfsi_custom_mobile_icons'])) {
			$option1['sfsi_custom_mobile_icons'] = '';
		}

		if (!isset($option1['sfsi_custom_desktop_icons'])) {
			$option1['sfsi_custom_desktop_icons'] = $option1['sfsi_custom_files'];
		}

		// ********** New icons added in version 10.2 ***********//
		if (!isset($option1['sfsi_plus_fbmessenger_display'])) {
			$option1['sfsi_plus_fbmessenger_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_gab_display'])) {
			$option1['sfsi_plus_gab_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_mix_display'])) {
			$option1['sfsi_plus_mix_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_ok_display'])) {
			$option1['sfsi_plus_ok_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_telegram_display'])) {
			$option1['sfsi_plus_telegram_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_vk_display'])) {
			$option1['sfsi_plus_vk_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_weibo_display'])) {
			$option1['sfsi_plus_weibo_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_xing_display'])) {
			$option1['sfsi_plus_xing_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_copylink_display'])) {
			$option1['sfsi_plus_copylink_display'] = 'no';
		}
		if (!isset($option1['sfsi_plus_mastodon_display'])) {
			$option1['sfsi_plus_mastodon_display'] = 'no';
		}

		if (!isset($option1['sfsi_plus_fbmessenger_mobiledisplay'])) {
			$option1['sfsi_plus_fbmessenger_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_gab_mobiledisplay'])) {
			$option1['sfsi_plus_gab_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_mix_mobiledisplay'])) {
			$option1['sfsi_plus_mix_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_ok_mobiledisplay'])) {
			$option1['sfsi_plus_ok_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_telegram_mobiledisplay'])) {
			$option1['sfsi_plus_telegram_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_vk_mobiledisplay'])) {
			$option1['sfsi_plus_vk_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_weibo_mobiledisplay'])) {
			$option1['sfsi_plus_weibo_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_xing_mobiledisplay'])) {
			$option1['sfsi_plus_xing_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_copylink_mobiledisplay'])) {
			$option1['sfsi_plus_copylink_mobiledisplay'] = 'no';
		}
		if (!isset($option1['sfsi_plus_mastodon_mobiledisplay'])) {
			$option1['sfsi_plus_mastodon_mobiledisplay'] = 'no';
		}
		// ********** New icons added in version 10.2 ***********//
		if (!isset($option1['sfsi_plus_phone_mobiledisplay'])) {
			if ("yes" == $option1["sfsi_plus_whatsapp_mobiledisplay"] && $option2['sfsi_plus_whatsapp_url_type'] == 'call') {
				$option1['sfsi_plus_phone_mobiledisplay'] = 'yes';
				$option1['sfsi_plus_whatsapp_mobiledisplay'] = 'no';
			} else {
				$option1['sfsi_plus_phone_mobiledisplay'] = 'no';
			}
		}
		if (!isset($option1['sfsi_plus_phone_display'])) {
			if ("yes" == $option1["sfsi_plus_whatsapp_display"] && $option2['sfsi_plus_whatsapp_url_type'] == 'call') {
				$option1['sfsi_plus_phone_display'] = 'yes';
				$option1['sfsi_plus_whatsapp_display'] = 'no';
			} else {
				$option1['sfsi_plus_phone_display'] = 'no';
			}
		}

		// ****** Separate Phone from Whatsapp *******//

		update_option('sfsi_premium_section1_options', serialize($option1));
	}

	//******** Set default selection of socila icons in Content selection option added in Question 7 in VERSION 3.6 STARTS *****//	
	$option7 = maybe_unserialize(get_option('sfsi_premium_section7_options', false));

	if (isset($option7) && !empty($option7)) {
		if (!isset($option7['sfsi_plus_popup_type_iconsOrForm'])) {
			$option7['sfsi_plus_popup_type_iconsOrForm'] = 'icons';
		}
	}
	update_option('sfsi_premium_section7_options', serialize($option7));

	//******** Set default selection of socila icons in Content selection option added in Question 7 in VERSION 3.6 CLOSES *****//

	$option5 =  maybe_unserialize(get_option('sfsi_premium_section5_options', false));

	if (isset($option5) && !empty($option5)) {
		if (!isset($option5['sfsi_plus_follow_icons_language'])) {
			$option5['sfsi_plus_follow_icons_language']  = 'Follow_en_US';
		}

		if (!isset($option5['sfsi_plus_facebook_icons_language'])) {
			$option5['sfsi_plus_facebook_icons_language'] = 'Visit_us_en_US';
		}

		if (!isset($option5['sfsi_plus_twitter_icons_language'])) {
			$option5['sfsi_plus_twitter_icons_language']  = 'Visit_us_en_US';
		}

		if (!isset($option5['sfsi_plus_linkedin_icons_language'])) {
			$option5['sfsi_plus_linkedin_icons_language']   = 'en_US';
		}

		if (!isset($option5['sfsi_plus_icons_language'])) {
			$option5['sfsi_plus_icons_language'] 		  = 'en_US';
		}

		if (!isset($option5['sfsi_plus_social_sharing_options'])) {
			$option5['sfsi_plus_social_sharing_options'] = 'posttype';
		}
		if (!isset($option5['sfsiSocialMediaImage'])) {
			$option5['sfsiSocialMediaImage'] 			 = '';
		}
		if (!isset($option5['sfsiSocialtTitleTxt'])) {
			$option5['sfsiSocialtTitleTxt'] 			 = '';
		}
		if (!isset($option5['sfsiSocialDescription'])) {
			$option5['sfsiSocialDescription'] 			 = '';
		}
		if (!isset($option5['sfsiSocialPinterestImage'])) {
			$option5['sfsiSocialPinterestImage'] 		 = '';
		}
		if (!isset($option5['sfsiSocialPinterestDesc'])) {
			$option5['sfsiSocialPinterestDesc'] 		 = '';
		}
		if (!isset($option5['sfsiSocialTwitterDesc'])) {
			$option5['sfsiSocialMediaImage'] 			 = '';
		}
		if (!isset($option5['sfsi_plus_loadjquery'])) {
			$option5['sfsi_plus_loadjquery'] 			 = 'yes';
		}
		if (!isset($option5['sfsi_plus_loadjscript'])) {
			$option5['sfsi_plus_loadjscript'] 			 = 'yes';
		}
		if (!isset($option5['sfsi_plus_nofollow_links'])) {
			$option5['sfsi_plus_nofollow_links'] 		 = 'no';
		}
		if (!isset($option5['sfsi_plus_hook_priority_value'])) {
			$option5['sfsi_plus_hook_priority_value'] 		 = 20;
		}

		if (!isset($option5['sfsi_plus_icons_suppress_errors'])) {

			$sup_errors = "no";
			$sup_errors_banner_dismissed = true;

			if (defined('WP_DEBUG') && false != WP_DEBUG) {
				$sup_errors = 'yes';
				$sup_errors_banner_dismissed = false;
			}

			$option5['sfsi_plus_icons_suppress_errors'] = $sup_errors;
			update_option('sfsi_plus_error_reporting_notice_dismissed', $sup_errors_banner_dismissed);
		}
		if (!isset($option5["sfsi_premium_featured_image_as_og_image"])) {
			$option5['sfsi_premium_featured_image_as_og_image'] = "no";
		}
		if (!isset($option5['sfsi_plus_change_number_format'])) {
			$option5['sfsi_plus_change_number_format'] 			 = 'no';
		}
		if (isset($option5['sfsi_plus_icons_language'])) {
			if ($option5['sfsi_plus_icons_language'] == 'el_GR') {
				$option5['sfsi_plus_icons_language'] == 'el';
			}

			if ($option5['sfsi_plus_icons_language'] == 'fi_FI') {
				$option5['sfsi_plus_icons_language'] == 'fi';
			}

			if ($option5['sfsi_plus_icons_language'] == 'ja_JP') {
				$option5['sfsi_plus_icons_language'] == 'ja';
			}
			if ($option5['sfsi_plus_icons_language'] == 'pt_BR') {
				$option5['sfsi_plus_icons_language'] == 'pt_PT';
			}
			if ($option5['sfsi_plus_icons_language'] == 'th_TH') {
				$option5['sfsi_plus_icons_language'] == 'th';
			}
			if ($option5['sfsi_plus_icons_language'] == 'vi_VN') {
				$option5['sfsi_plus_icons_language'] == 'vi';
			}
			if (in_array($option5['sfsi_plus_icons_language'], array('az_AZ', 'af_ZA', 'ms_MY', 'bn_IN', 'bs_BA', 'ca_ES', 'cy_GB', 'eo_EO', 'et_EE', 'eu_ES', 'gl_ES', 'he_IL', 'hi_IN', 'hr_HR', 'hy_AM', 'is_IS', 'lt_LT', 'my_MM', 'nn_NO', 'ps_AF', 'sl_SI', 'sq_AL', 'sr_RS', 'tl_PH', 'ug_CN', 'uk_UA', 'ur_PK'))) {
				$option5['sfsi_plus_icons_language'] == 'en_US';
			}
		}
	}

	if (isset($option5) && !empty($option5) && !isset($option5['sfsi_plus_youtube_icons_language'])) {
		$option5['sfsi_plus_youtube_icons_language'] = 'Visit_us_en_US';
		update_option('sfsi_premium_section5_options', serialize($option5));
	}

	/*Youtube Channelid settings*/
	$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
	if (isset($option4) && !empty($option4) && !isset($option4['sfsi_plus_youtube_icons_language'])) {
		$option4['sfsi_plus_youtube_channelId'] = '';
		update_option('sfsi_premium_section4_options', serialize($option4));
	}

	$option3 = maybe_unserialize(get_option('sfsi_premium_section3_options', false));

	if (isset($option3) && !empty($option3)) {
		if (!isset($option3['sfsi_plus_mouseOver_effect_type'])) {
			$option3['sfsi_plus_mouseOver_effect_type'] = 'same_icons';
		}

		if (!isset($option3['sfsi_plus_mouseOver_other_icon_images'])) {
			$option3['sfsi_plus_mouseOver_other_icon_images'] = serialize(array());
		}

		if (!isset($option3['sfsi_plus_mouseover_other_icons_transition_effect'])) {
			$option3['sfsi_plus_mouseover_other_icons_transition_effect'] = 'noeffect';
		}

		update_option('sfsi_premium_section3_options', serialize($option3));
	}

	/*add whasapp page share and email page share*/
	$option2 = maybe_unserialize(get_option('sfsi_premium_section2_options', false));
	if (isset($option2) && !empty($option2)) {
		if (!isset($option2['sfsi_plus_whatsapp_share_page'])) {
			$option2['sfsi_plus_whatsapp_share_page'] 		= '${title} ${link}';
		}
		if (!isset($option2['sfsi_plus_email_icons_subject_line'])) {
			$option2['sfsi_plus_email_icons_subject_line'] 	= '${title}';
		}
		if (!isset($option2['sfsi_plus_email_icons_email_content'])) {
			$option2['sfsi_plus_email_icons_email_content'] = 'Check out this article «${title}»: ${link}';
		}

		if (!isset($option2['sfsi_plus_fbmessengerContact_option'])) {
			$option2['sfsi_plus_fbmessengerContact_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_fbmessengerContact_url'])) {
			$option2['sfsi_plus_fbmessengerContact_url'] 	= '';
		}
		if (!isset($option2['sfsi_plus_fbmessengerShare_option'])) {
			$option2['sfsi_plus_fbmessengerShare_option'] 	= 'no';
		}

		if (!isset($option2['sfsi_plus_mixVisit_option'])) {
			$option2['sfsi_plus_mixVisit_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_vkVisit_option'])) {
			$option2['sfsi_plus_vkVisit_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_weiboVisit_option'])) {
			$option2['sfsi_plus_weiboVisit_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_xingShare_option'])) {
			$option2['sfsi_plus_xingShare_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_xingVisit_option'])) {
			$option2['sfsi_plus_xingVisit_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_xingFollow_option'])) {
			$option2['sfsi_plus_xingFollow_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_mixVisit_url'])) {
			$option2['sfsi_plus_mixVisit_url'] 	= '';
		}
		if (!isset($option2['sfsi_plus_mixShare_option'])) {
			$option2['sfsi_plus_mixShare_option'] 	= 'no';
		}

		if (!isset($option2['sfsi_plus_okVisit_option'])) {
			$option2['sfsi_plus_okVisit_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_okVisit_url'])) {
			$option2['sfsi_plus_okVisit_url'] 	= '';
		}
		if (!isset($option2['sfsi_plus_okSubscribe_option'])) {
			$option2['sfsi_plus_okSubscribe_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_okSubscribe_userid'])) {
			$option2['sfsi_plus_okSubscribe_userid'] 	= '';
		}
		if (!isset($option2['sfsi_plus_okLike_option'])) {
			$option2['sfsi_plus_okLike_option'] 	= 'no';
		}

		if (!isset($option2['sfsi_plus_telegramShare_option'])) {
			$option2['sfsi_plus_telegramShare_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_telegramMessage_option'])) {
			$option2['sfsi_plus_telegramMessage_option'] 	= 'no';
		}
		if (!isset($option2['sfsi_plus_telegram_message'])) {
			$option2['sfsi_plus_telegram_message'] 	= '';
		}
		if (!isset($option2['sfsi_plus_telegram_username'])) {
			$option2['sfsi_plus_telegram_username'] 	= '';
		}

		if (!isset($option2['sfsi_plus_yummlyVisit_option'])) {
			$option2['sfsi_plus_yummlyVisit_option'] = isset($option2['sfsi_plus_yummly_pageUrl']) && !empty($option2['sfsi_plus_yummly_pageUrl']) ? 'yes' : 'no';
		}

		if (!isset($option2['sfsi_plus_yummlyShare_option'])) {
			$option2['sfsi_plus_yummlyShare_option'] 	= 'no';
		}

		// if( !isset($option2['sfsi_plus_houzzVisit_option']) )
		// {
		// 	$option2['sfsi_plus_houzzVisit_option'] = isset($option2['sfsi_plus_houzz_pageUrl']) && !empty($option2['sfsi_plus_houzz_pageUrl']) ? 'yes': 'no';
		// }

		// if( !isset($option2['sfsi_plus_houzzShare_option']) )
		// {
		// 	$option2['sfsi_plus_houzzShare_option'] 	= 'no';
		// }

		// if( !isset($option2['sfsi_plus_houzz_websiteId']) )
		// {
		// 	$option2['sfsi_plus_houzz_websiteId'] 	= '';
		// }

		update_option('sfsi_premium_section2_options', serialize($option2));
	}

	$option2 = maybe_unserialize(get_option('sfsi_premium_section2_options', false));

	if (isset($option2) && !empty($option2)) {
		if (!isset($option2['sfsi_plus_skype_options'])) {
			$option2['sfsi_plus_skype_options'] 		= 'call';
		}
		if (!isset($option2['sfsi_plus_my_whatsapp_number'])) {
			$option2['sfsi_plus_my_whatsapp_number'] 	= '';
		}
		update_option('sfsi_premium_section2_options', serialize($option2));
	}

	$option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));

	if (isset($option5) && !empty($option5)) {
		if (false  == isset($option5['sfsi_plus_mobile_icon_alignment_setting'])) {
			$option5['sfsi_plus_mobile_icon_alignment_setting'] 		= 'no';
		}

		if (false  == isset($option5['sfsi_plus_mobile_horizontal_verical_Alignment'])) {
			$option5['sfsi_plus_mobile_horizontal_verical_Alignment'] 		= 'Horizontal';
		}

		if (!isset($option5['sfsi_plus_mobile_icons_Alignment_via_widget'])) {
			$option5['sfsi_plus_mobile_icons_Alignment_via_widget'] 	= 'left';
		}

		if (!isset($option5['sfsi_plus_mobile_icons_Alignment_via_shortcode'])) {
			$option5['sfsi_plus_mobile_icons_Alignment_via_shortcode'] 	= 'left';
		}

		if (!isset($option5['sfsi_plus_mobile_icons_Alignment'])) {
			$option5['sfsi_plus_mobile_icons_Alignment'] 				= 'left';
		}

		if (!isset($option5['sfsi_plus_mobile_icons_perRow'])) {
			$option5['sfsi_plus_mobile_icons_perRow'] 					= '5';
		}

		if (!isset($option5['sfsi_plus_horizontal_verical_Alignment'])) {
			$option5['sfsi_plus_horizontal_verical_Alignment'] 	= 'Horizontal';
			$option5['sfsi_plus_icons_Alignment_via_shortcode'] = 'left';
			$option5['sfsi_plus_icons_Alignment_via_widget'] 	= 'left';
		}
		if (!isset($option5['sfsi_plus_twitter_summery'])) {
			$option5['sfsi_plus_tooltip_Color'] 		= '#FFF';
			$option5['sfsi_plus_tooltip_border_Color'] 	= '#e7e7e7';
			$option5['sfsi_plus_tooltip_alighn'] 		= 'Automatic';
		}

		if (!isset($option5['sfsi_plus_Facebook_linking'])) {
			$option5['sfsi_plus_Facebook_linking'] 		= 'facebookurl';
			$option5['sfsi_plus_facebook_linkingcustom_url'] 	= '';
		}

		if (!isset($option5['sfsi_plus_mobile_icons_order_setting'])) {
			$option5['sfsi_plus_mobile_icons_order_setting'] = 'no';
		}

		if (!isset($option5['sfsi_plus_jscript_fileName'])) {
			$option5['sfsi_plus_jscript_fileName'] = array();
		}
		if (!isset($option5['sfsi_plus_more_jscript_fileName'])) {
			$option5['sfsi_plus_more_jscript_fileName'] = 'no';
		}

		if (!isset($option5['sfsi_order_icons_desktop'])) {
			$orderArrDesktopIcons = sfsi_plus_add_fields_for_desktop_icons_order_option5($option5);
			$option5['sfsi_order_icons_desktop'] = serialize($orderArrDesktopIcons);
		}

		if (!isset($option5['sfsi_order_icons_mobile'])) {

			$option1 = 	maybe_unserialize(get_option('sfsi_premium_section1_options', false));

			if (
				"no" == $option1['sfsi_plus_icons_onmobile'] && "no" == $option5['sfsi_plus_mobile_icons_order_setting']
				&& isset($option5['sfsi_order_icons_desktop']) && !empty($option5['sfsi_order_icons_desktop'])
			) {

				$option5['sfsi_order_icons_mobile']  = maybe_unserialize($option5['sfsi_order_icons_desktop']);
			} else {

				$option5['sfsi_order_icons_mobile']  = serialize(array());
			}
		}
		if (!isset($option5['sfsi_premium_static_path'])) {
			$option5['sfsi_premium_static_path']		= '';
		}




		update_option('sfsi_premium_section5_options', serialize($option5));
	}

	$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));

	if (isset($option4) && !empty($option4)) {
		if (!isset($option4['sfsi_plus_facebook_countsFrom_blog'])) {
			$option4['sfsi_plus_facebook_countsFrom_blog'] 		= '';
		}
		if (!isset($option4['sfsi_plus_pinterest_appid'])) {
			$option4['sfsi_plus_pinterest_appid'] = "";
		}
		if (!isset($option4['sfsi_plus_pinterest_appsecret'])) {
			$option4['sfsi_plus_pinterest_appsecret'] = "";
		}
		if (!isset($option4['sfsi_plus_pinterest_appurl'])) {
			$option4['sfsi_plus_pinterest_appurl'] = "";
		}

		update_option('sfsi_premium_section4_options', serialize($option4));
	}

	/** Url shortner data table **/
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_s_name    = $wpdb->prefix . "sfsi_shorten_links";

	if ($wpdb->get_var("SHOW TABLES LIKE '$table_s_name'") != $table_s_name) {
		$sql = "CREATE TABLE $table_s_name (
		  id bigint(9) NOT NULL AUTO_INCREMENT,
		  post_id bigint(9) NOT NULL,
		  shorteningMethod varchar(30) NOT NULL,
		  longUrl text NOT NULL,
		  shortenUrl varchar(100) DEFAULT '' NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	//********* UPDATE DB data code for VERSION 2.7  STARTS  **************************************//

	// Get values from Question 2
	$option2 = maybe_unserialize(get_option('sfsi_premium_section2_options'));
	$option5 = maybe_unserialize(get_option('sfsi_premium_section5_options'));

	// If true then current version is less than 2.6. If False then current version is 2.6
	if (isset($option2['sfsi_plus_twitter_aboutPage'])) {

		if (isset($option2['sfsi_plus_twitter_aboutPageText'])) {
			$option5['sfsi_plus_twitter_aboutPageText'] = $option2['sfsi_plus_twitter_aboutPageText'];
			unset($option2['sfsi_plus_twitter_aboutPageText']);
		}

		// Check if Add Twitter Card set in Question 2, Move to Question 5 
		if (isset($option2['sfsi_plus_twitter_twtAddCard'])) {
			$option5['sfsi_plus_twitter_twtAddCard'] = $option2['sfsi_plus_twitter_twtAddCard'];
			unset($option2['sfsi_plus_twitter_twtAddCard']);
		}

		// Check if Add Twitter Card Type in Question 2, Move to Question 5
		if (isset($option2['sfsi_plus_twitter_twtCardType'])) {
			$option5['sfsi_plus_twitter_twtCardType'] = $option2['sfsi_plus_twitter_twtCardType'];
			unset($option2['sfsi_plus_twitter_twtCardType']);
		}

		// Check if Add Twitter Card Type in Question 2, Move to Question 5
		if (isset($option2['sfsi_plus_twitter_card_twitter_handle'])) {
			$option5['sfsi_plus_twitter_card_twitter_handle'] = $option2['sfsi_plus_twitter_card_twitter_handle'];
			unset($option2['sfsi_plus_twitter_card_twitter_handle']);
		}
	}

	// Current version is 2.6
	else {
		// Not get value for "Tweet about my page" from Question 2,Checking value in Question 6 
		if (isset($option5['sfsi_plus_twitter_aboutPage'])) {
			$option2['sfsi_plus_twitter_aboutPage'] = $option5['sfsi_plus_twitter_aboutPage']; // set value in Question 2
			unset($option5['sfsi_plus_twitter_aboutPage']); // Remove value from Question 6
		} else { // Value not get for "Tweet about my page" from Question 2 & Question 5, Setting default keys & data 

			$option2['sfsi_plus_twitter_aboutPage'] = "yes"; // Set "Tweet about my page" on for new users

			// Set default values for twitter users on for new users
			if (!isset($option5['sfsi_plus_twitter_aboutPageText']) && !isset($option5['sfsi_plus_twitter_twtAddCard']) && !isset($option5['sfsi_plus_twitter_twtCardType']) && !isset($option5['sfsi_plus_twitter_card_twitter_handle'])) {
				$option5['sfsi_plus_twitter_aboutPageText'] 	  = '${title} ${link}';
				$option5['sfsi_plus_twitter_twtAddCard'] 		  = "yes";
				$option5['sfsi_plus_twitter_twtCardType'] 		  = "summary";
				$option5['sfsi_plus_twitter_card_twitter_handle'] = '';
			}
		}
	}


	// *********** Updating setting for post type selection in section 6. Now setting Page, Post selected default STARTS ****************/

	if (isset($option5['sfsi_custom_social_data_post_types_data'])) {

		$sfsi_custom_social_data_post_types_data = maybe_unserialize($option5['sfsi_custom_social_data_post_types_data']);

		if (count($sfsi_custom_social_data_post_types_data) > 0) {

			// CODE TO REMOVE FOR VERSION 2.10 STARTS //

			if (isset($sfsi_custom_social_data_post_types_data[0]) && is_array($sfsi_custom_social_data_post_types_data[0])) {
				$sfsi_custom_social_data_post_types_data = $sfsi_custom_social_data_post_types_data[0];
			}

			// CODE TO REMOVE FOR VERSION 2.10 CLOSES //					

			if (!in_array('page', $sfsi_custom_social_data_post_types_data)) {
				$add_custom_social_data_post_types_data = array('page');
				$sfsi_custom_social_data_post_types_data = array_merge($add_custom_social_data_post_types_data, $sfsi_custom_social_data_post_types_data);
			} else if (!in_array('post', $sfsi_custom_social_data_post_types_data)) {
				$add_custom_social_data_post_types_data = array('post');
				$sfsi_custom_social_data_post_types_data = array_merge($add_custom_social_data_post_types_data, $sfsi_custom_social_data_post_types_data);
			}
			$option5['sfsi_custom_social_data_post_types_data']	= serialize($sfsi_custom_social_data_post_types_data);
		} else {
			$option5['sfsi_custom_social_data_post_types_data']	= serialize(array('page', 'post'));
		}
	} else {
		$option5['sfsi_custom_social_data_post_types_data']	= serialize(array('page', 'post'));
	}

	// **** Updating setting for post type selection in section 6. Now setting Page, Post selected default CLOSES ********************/	



	// ***** Update setting to allow USM to add open graph meta tags in Question 6 STARTS ********************************************/

	if (get_option('adding_plustags')) {
		delete_option('adding_plustags');
	}
	// ** If get setting found fron db -> ($option5['sfsi_plus_disable_usm_og_meta_tags']) from Question 6, else check other SEO plugins are activated, then set "Disable Ultimate Social Media Plugin to set the meta tags" setting only once ** //
	$option5 =  maybe_unserialize(get_option('sfsi_premium_section5_options', false));

	if (!isset($option5['sfsi_plus_disable_usm_og_meta_tags'])) {
		$option5['sfsi_plus_disable_usm_og_meta_tags'] = (sfsi_plus_checkmetas()) ? "yes" : "no";
	}

	// **********Update setting to allow USM to add open graph meta tags in Question 6 CLOSES **********************************/

	if (!isset($option5['sfsi_premium_url_shortner_icons_names_list'])) {
		$option5['sfsi_premium_url_shortner_icons_names_list'] = serialize(array('twitter', 'facebook', 'email'));
	}

	if (!isset($option5['sfsi_plus_url_shorting_api_type_setting'])) {
		$option5['sfsi_plus_url_shorting_api_type_setting'] = 'no';
	}

	if (!isset($option5['sfsi_plus_url_shortner_bitly_key'])) {
		$option5['sfsi_plus_url_shortner_bitly_key'] = '';
	}

	if (!isset($option5['sfsi_plus_url_shortner_google_key'])) {
		$option5['sfsi_plus_url_shortner_google_key'] = '';
	}

	if (!isset($option5['sfsi_plus_custom_css'])) {
		$option5['sfsi_plus_custom_css'] = serialize('');
	}

	if (!isset($option5['sfsi_plus_custom_admin_css'])) {
		$option5['sfsi_plus_custom_admin_css'] = serialize('');
	}
	if (!isset($option5['sfsi_premium_pinterest_sharing_texts_and_pics'])) {
		$option5['sfsi_premium_pinterest_sharing_texts_and_pics'] = 'no';
	}
	if (!isset($option5['sfsi_premium_pinterest_placements'])) {
		$option5['sfsi_premium_pinterest_placements'] = 'no';
	}

	if (empty($option5['sfsi_plus_icons_ClickPageOpen'])) {
		$option5['sfsi_plus_icons_ClickPageOpen'] = 'no';
	} elseif ($option5['sfsi_plus_icons_ClickPageOpen'] == 'yes') {
		$option5['sfsi_plus_icons_ClickPageOpen'] = 'tab';
	} else {
		$iconLinkOpen = $option5['sfsi_plus_icons_ClickPageOpen'];
	}

	if (!isset($option5['sfsi_plus_bitly_v4'])) {
		$option5['sfsi_plus_bitly_v4'] = 'no';
	}

	$option5['sfsi_plus_icons_ClickPageOpen'] = $iconLinkOpen;

	$option5['sfsi_plus_mobile_open_type_setting'] = 'no';
	$option5['sfsi_plus_icons_mobile_ClickPageOpen'] = $option5['sfsi_plus_icons_ClickPageOpen'];

	// Now updating Values in Question 2 & Question 6
	update_option('sfsi_premium_section5_options', serialize($option5));
	update_option('sfsi_premium_section2_options', serialize($option2));

	//*********** UPDATE DB data code for VERSION 2.7  CLOSES  **************************************//


	//******** UPDATE Desktop, Mobile show/hide settings in Question 3 in each section from VERSION 4.5 STARTS**********//

	// In Question 3 -> B) Show on Desktop Show on Mobile section removed IN VERSION 4.5

	$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
	$option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));

	if (isset($option8) && !empty($option8)) {

		$desktopValue = (isset($option8['sfsi_plus_show_on_desktop'])) ? $option8['sfsi_plus_show_on_desktop'] : "yes";
		$mobileValue  = (isset($option8['sfsi_plus_show_on_mobile']))  ? $option8['sfsi_plus_show_on_mobile']  : "yes";

		if (false == isset($option8['sfsi_plus_widget_show_on_desktop'])) {
			$option8['sfsi_plus_widget_show_on_desktop'] = $desktopValue;
		}

		if (false == isset($option8['sfsi_plus_float_show_on_desktop'])) {
			$option8['sfsi_plus_float_show_on_desktop'] = $desktopValue;
		}

		if (false == isset($option8['sfsi_plus_shortcode_show_on_desktop'])) {
			$option8['sfsi_plus_shortcode_show_on_desktop'] = $desktopValue;
		}

		if (false == isset($option8['sfsi_plus_beforeafterposts_show_on_desktop'])) {
			$option8['sfsi_plus_beforeafterposts_show_on_desktop'] = $desktopValue;
		}

		if (false == isset($option8['sfsi_plus_rectangle_icons_shortcode_show_on_desktop'])) {
			$option8['sfsi_plus_rectangle_icons_shortcode_show_on_desktop'] = "no";
		}


		if (false == isset($option8['sfsi_plus_widget_show_on_mobile'])) {
			$option8['sfsi_plus_widget_show_on_mobile'] = $mobileValue;
		}

		if (false == isset($option8['sfsi_plus_float_show_on_mobile'])) {
			$option8['sfsi_plus_float_show_on_mobile'] = $mobileValue;
		}

		if (false == isset($option8['sfsi_plus_shortcode_show_on_mobile'])) {
			$option8['sfsi_plus_shortcode_show_on_mobile'] = $mobileValue;
		}

		if (false == isset($option8['sfsi_plus_beforeafterposts_show_on_mobile'])) {
			$option8['sfsi_plus_beforeafterposts_show_on_mobile'] = $mobileValue;
		}

		if (false == isset($option8['sfsi_plus_rectangle_icons_shortcode_show_on_desktop'])) {
			$option8['sfsi_plus_rectangle_icons_shortcode_show_on_desktop'] = "yes";
		}

		if (false == isset($option8['sfsi_plus_rectangle_icons_shortcode_show_on_mobile'])) {
			$option8['sfsi_plus_rectangle_icons_shortcode_show_on_mobile'] = "yes";
		}

		// Copy the setting if "Question 6-> Need different selections for mobile?"
		if (!isset($option8['sfsi_plus_mobile_widget'])) {
			$option8['sfsi_plus_mobile_widget'] = (isset($option5['sfsi_plus_mobile_icon_alignment_setting'])) ? $option5['sfsi_plus_mobile_icon_alignment_setting'] : "no";
		}

		if (!isset($option8['sfsi_plus_mobile_float'])) {
			$option8['sfsi_plus_mobile_float'] = (isset($option5['sfsi_plus_mobile_icon_alignment_setting'])) ? $option5['sfsi_plus_mobile_icon_alignment_setting'] : "no";
		}

		if (!isset($option8['sfsi_plus_mobile_shortcode'])) {
			$option8['sfsi_plus_mobile_shortcode'] = (isset($option5['sfsi_plus_mobile_icon_alignment_setting'])) ? $option5['sfsi_plus_mobile_icon_alignment_setting'] : "no";
		}

		if (!isset($option8['sfsi_plus_mobile_beforeafterposts'])) {
			$option8['sfsi_plus_mobile_beforeafterposts'] = (isset($option5['sfsi_plus_mobile_icon_alignment_setting'])) ? $option5['sfsi_plus_mobile_icon_alignment_setting'] : "no";
		}

		// Get Alignments settings from Question 6-> Desktop & Mobile
		$option5['sfsi_plus_horizontal_verical_Alignment'] = (isset($option5['sfsi_plus_horizontal_verical_Alignment'])) ? $option5['sfsi_plus_horizontal_verical_Alignment'] : "Horizontal";

		$option5['sfsi_plus_mobile_horizontal_verical_Alignment'] = (isset($option5['sfsi_plus_mobile_horizontal_verical_Alignment'])) ? $option5['sfsi_plus_mobile_horizontal_verical_Alignment'] : "Horizontal";


		// Set the alignments setting of icons for Desktop in Question 3 for Widget, floating icons, shortcode & before after posts
		if (false == isset($option8['sfsi_plus_widget_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_widget_horizontal_verical_Alignment'] 			= $option5['sfsi_plus_horizontal_verical_Alignment'];
		}
		if (false == isset($option8['sfsi_plus_float_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_float_horizontal_verical_Alignment'] 			= $option5['sfsi_plus_horizontal_verical_Alignment'];
		}
		if (false == isset($option8['sfsi_plus_shortcode_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_shortcode_horizontal_verical_Alignment'] 		= $option5['sfsi_plus_horizontal_verical_Alignment'];
		}
		if (false == isset($option8['sfsi_plus_beforeafterposts_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_beforeafterposts_horizontal_verical_Alignment'] = $option5['sfsi_plus_horizontal_verical_Alignment'];
		}

		// Set the alignments setting of icons for Mobile in Question 3 for Widget, floating icons, shortcode & before after posts
		if (false == isset($option8['sfsi_plus_widget_mobile_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_widget_mobile_horizontal_verical_Alignment'] = ($option8['sfsi_plus_mobile_widget'] == "yes") ? $option5['sfsi_plus_mobile_horizontal_verical_Alignment'] : $option5['sfsi_plus_horizontal_verical_Alignment'];
		}
		if (false == isset($option8['sfsi_plus_float_mobile_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_float_mobile_horizontal_verical_Alignment'] = ($option8['sfsi_plus_mobile_float'] == "yes") ? $option5['sfsi_plus_mobile_horizontal_verical_Alignment'] : $option5['sfsi_plus_horizontal_verical_Alignment'];
		}
		if (false == isset($option8['sfsi_plus_shortcode_mobile_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_shortcode_mobile_horizontal_verical_Alignment'] = ($option8['sfsi_plus_mobile_shortcode'] == "yes") ? $option5['sfsi_plus_mobile_horizontal_verical_Alignment'] : $option5['sfsi_plus_horizontal_verical_Alignment'];
		}
		if (false == isset($option8['sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment'])) {
			$option8['sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment'] = ($option8['sfsi_plus_mobile_beforeafterposts'] == "yes") ? $option5['sfsi_plus_mobile_horizontal_verical_Alignment'] : $option5['sfsi_plus_horizontal_verical_Alignment'];
		}

		update_option('sfsi_premium_section8_options', serialize($option8));
	}

	//***** UPDATE Desktop, Mobile show/hide settings in Question 3 in each section from VERSION 4.5 CLOSES  *********


	//*** UPDATE Desktop, Mobile show/hide settings in Question 7 in each section from VERSION 4.5 STARTS***

	// In Question 3 B) Show on Desktop Show on Mobile section removed IN VERSION 4.5

	$option7 = maybe_unserialize(get_option('sfsi_premium_section7_options', false));
	$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));

	if (isset($option7) && !empty($option7)) {

		$desktopValue = (isset($option8['sfsi_plus_show_on_desktop']) && !empty($option8['sfsi_plus_show_on_desktop'])) ? $option8['sfsi_plus_show_on_desktop'] : "yes";
		$mobileValue  = (isset($option8['sfsi_plus_show_on_mobile'])  && !empty($option8['sfsi_plus_show_on_mobile']))  ? $option8['sfsi_plus_show_on_mobile']  : "yes";

		$option7['sfsi_plus_popup_show_on_desktop'] = (isset($option7['sfsi_plus_popup_show_on_desktop']) && !empty($option7['sfsi_plus_show_on_desktop'])) ? $option7['sfsi_plus_popup_show_on_desktop'] : $desktopValue;
		$option7['sfsi_plus_popup_show_on_mobile']  = (isset($option7['sfsi_plus_popup_show_on_mobile'])  && !empty($option7['sfsi_plus_popup_show_on_mobile']))  ? $option7['sfsi_plus_popup_show_on_mobile']  : $mobileValue;

		if (isset($option8['sfsi_plus_show_on_desktop'])) {
			unset($option8['sfsi_plus_show_on_desktop']);
		}
		if (isset($option8['sfsi_plus_show_on_mobile'])) {
			unset($option8['sfsi_plus_show_on_mobile']);
		}

		if (!isset($option7['sfsi_plus_Show_popupOn_somepages_blogpage'])) {
			$option7['sfsi_plus_Show_popupOn_somepages_blogpage'] = '';
		}
		if (!isset($option7['sfsi_plus_Show_popupOn_somepages_selectedpage'])) {
			$option7['sfsi_plus_Show_popupOn_somepages_selectedpage'] = '';
		}

		if (!isset($option7['sfsi_plus_Hide_popupOnScroll'])) {
			$option7['sfsi_plus_Hide_popupOnScroll'] = 'yes';
		}
		if (!isset($option7['sfsi_plus_Hide_popupOn_OutsideClick'])) {
			$option7['sfsi_plus_Hide_popupOn_OutsideClick'] = 'no';
		}

		update_option('sfsi_premium_section7_options', serialize($option7));
		update_option('sfsi_premium_section8_options', serialize($option8));
	}

	//**** UPDATE Desktop, Mobile show/hide settings in Question 7 in each section from VERSION 4.5 CLOSES ******

	$option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));

	if (isset($option4) && !empty($option4)) {

		if (!isset($option4['sfsi_plus_pinterest_appid'])) {
			$option4['sfsi_plus_pinterest_appid'] = '';
		}
		if (!isset($option4['sfsi_plus_pinterest_appsecret'])) {
			$option4['sfsi_plus_pinterest_appsecret'] = '';
		}
		if (!isset($option4['sfsi_plus_pinterest_appurl'])) {
			$option4['sfsi_plus_pinterest_appurl'] = '';
		}

		if (!isset($option4['sfsi_plus_pinterest_board_name'])) {
			$option4['sfsi_plus_pinterest_board_name'] = '';
		}
		if (!isset($option4['sfsi_plus_pinterest_access_token'])) {
			$option4['sfsi_plus_pinterest_access_token'] = '';
		}
		if (!isset($option4['sfsi_plus_pinterest_user'])) {
			$option4['sfsi_plus_pinterest_user'] = '';
		}

		if (!isset($option4['sfsi_plus_fb_count_caching_active'])) {
			$option4['sfsi_plus_fb_count_caching_active'] = 'no';
		}

		if (!isset($option4['sfsi_plus_fb_caching_interval'])) {
			$option4['sfsi_plus_fb_caching_interval'] = 1;
		}

		if (!isset($option4['sfsi_plus_tw_count_caching_active'])) {
			$option4['sfsi_plus_tw_count_caching_active'] = 'no';
		}
		if (!isset($option4['sfsi_plus_min_display_counts'])) {
			$option4['sfsi_plus_min_display_counts'] = 1;
		}

		if (!isset($option4['sfsi_plus_fbmessenger_countsDisplay'])) {
			$option4['sfsi_plus_fbmessenger_countsDisplay'] = 'no';
		}
		if (!isset($option4['sfsi_plus_mix_countsDisplay'])) {
			$option4['sfsi_plus_mix_countsDisplay'] = 'no';
		}
		if (!isset($option4['sfsi_plus_ok_countsDisplay'])) {
			$option4['sfsi_plus_ok_countsDisplay'] = 'no';
		}
		if (!isset($option4['sfsi_plus_vk_countsDisplay'])) {
			$option4['sfsi_plus_vk_countsDisplay'] = 'no';
		}
		if (!isset($option4['sfsi_plus_telegram_countsDisplay'])) {
			$option4['sfsi_plus_telegram_countsDisplay'] = 'no';
		}
		if (!isset($option4['sfsi_plus_weibo_countsDisplay'])) {
			$option4['sfsi_plus_weibo_countsDisplay'] = 'no';
		}
		if (!isset($option4['sfsi_plus_xing_countsDisplay'])) {
			$option4['sfsi_plus_xing_countsDisplay'] = 'no';
		}
		if (!isset($option4['sfsi_plus_phone_countsDisplay'])) {
			$option4['sfsi_plus_phone_countsDisplay'] = 'no';
		}
		if (!isset($option4['sfsi_plus_mastodon_countsDisplay'])) {
			$option4['sfsi_plus_mastodon_countsDisplay'] = 'no';
		}
        if (!isset($option4['sfsi_plus_wechat_countsDisplay'])) {
            $option4['sfsi_plus_wechat_countsDisplay'] = 'no';
        }
        if (!isset($option4['sfsi_plus_copylink_countsDisplay'])) {
            $option4['sfsi_plus_copylink_countsDisplay'] = 'no';
        }

		if (!isset($option4['sfsi_plus_fbmessenger_manualCounts'])) {
			$option4['sfsi_plus_fbmessenger_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_mix_manualCounts'])) {
			$option4['sfsi_plus_mix_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_ok_manualCounts'])) {
			$option4['sfsi_plus_ok_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_vk_manualCounts'])) {
			$option4['sfsi_plus_vk_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_telegram_manualCounts'])) {
			$option4['sfsi_plus_telegram_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_weibo_manualCounts'])) {
			$option4['sfsi_plus_weibo_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_wechat_manualCounts'])) {
			$option4['sfsi_plus_wechat_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_copylink_manualCounts'])) {
			$option4['sfsi_plus_copylink_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_xing_manualCounts'])) {
			$option4['sfsi_plus_xing_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_phone_manualCounts'])) {
			$option4['sfsi_plus_phone_manualCounts'] = '20';
		}
		if (!isset($option4['sfsi_plus_mastodon_manualCounts'])) {
			$option4['sfsi_plus_mastodon_manualCounts'] = '20';
		}
	}

	update_option('sfsi_premium_section4_options', serialize($option4));

	// Adding option to save current active licensing api added in version 6.6
	if (false === get_option('sfsi_active_license_api_name')) {

		$ultimate_license_key  = get_option(ULTIMATELYSOCIAL_LICENSING . '_license_key');
		$sellcodes_license_key = get_option(SELLCODES_LICENSING . '_license_key');

		if (false !== $ultimate_license_key) {
			update_option('sfsi_active_license_api_name', ULTIMATELYSOCIAL_LICENSING);
		}

		if (false !== $sellcodes_license_key) {
			update_option('sfsi_active_license_api_name', SELLCODES_LICENSING);
		}
	}

	//update options for hover image version 9.7 STARTS
	$option5 =  maybe_unserialize(get_option('sfsi_premium_section5_options', false));
	$option8 =  maybe_unserialize(get_option('sfsi_premium_section8_options', false));
	if (!isset($option8['sfsi_plus_display_rule_responsive_icon_before_after_post'])) {
		$option8['sfsi_plus_display_rule_responsive_icon_before_after_post'] = "no";
	}
	if (!isset($option8['sfsi_plus_display_rule_responsive_icon_shortcode'])) {
		$option8['sfsi_plus_display_rule_responsive_icon_shortcode'] = "no";
	}
	if (!isset($option8['sfsi_plus_icon_hover_show_pinterest'])) {
		if (!isset($option5['sfsi_plus_icon_hover_show_pinterest'])) {
			$option8['sfsi_plus_icon_hover_show_pinterest'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_show_pinterest'] = $option5['sfsi_plus_icon_hover_show_pinterest'];
			unset($option5['sfsi_plus_icon_hover_show_pinterest']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_type'])) {
		if (!isset($option5['sfsi_plus_icon_hover_type'])) {
			$option8['sfsi_plus_icon_hover_type'] = "square";
		} else {
			$option8['sfsi_plus_icon_hover_type'] = $option5['sfsi_plus_icon_hover_type'];
			unset($option5['sfsi_plus_icon_hover_type']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_language'])) {
		if (!isset($option5['sfsi_plus_icon_hover_language'])) {
			$option8['sfsi_plus_icon_hover_language'] = "en_US";
		} else {
			$option8['sfsi_plus_icon_hover_language'] = $option5['sfsi_plus_icon_hover_language'];
			unset($option5['sfsi_plus_icon_hover_language']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_placement'])) {
		if (!isset($option5['sfsi_plus_icon_hover_placement'])) {
			$option8['sfsi_plus_icon_hover_placement'] = "top-left";
		} else {
			$option8['sfsi_plus_icon_hover_placement'] = $option5['sfsi_plus_icon_hover_placement'];
			unset($option5['sfsi_plus_icon_hover_placement']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_width'])) {
		if (!isset($option5['sfsi_plus_icon_hover_width'])) {
			$option8['sfsi_plus_icon_hover_width'] = "20";
		} else {
			$option8['sfsi_plus_icon_hover_width'] = $option5['sfsi_plus_icon_hover_width'];
			unset($option5['sfsi_plus_icon_hover_width']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_height'])) {
		if (!isset($option5['sfsi_plus_icon_hover_height'])) {
			$option8['sfsi_plus_icon_hover_height'] = "20";
		} else {
			$option8['sfsi_plus_icon_hover_height'] = $option5['sfsi_plus_icon_hover_height'];
			unset($option5['sfsi_plus_icon_hover_height']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_desktop'])) {
		if (!isset($option5['sfsi_plus_icon_hover_desktop'])) {
			$option8['sfsi_plus_icon_hover_desktop'] = "yes";
		} else {
			$option8['sfsi_plus_icon_hover_desktop'] = $option5['sfsi_plus_icon_hover_desktop'];
			unset($option5['sfsi_plus_icon_hover_desktop']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_mobile'])) {
		if (!isset($option5['sfsi_plus_icon_hover_mobile'])) {
			$option8['sfsi_plus_icon_hover_mobile'] = "yes";
		} else {
			$option8['sfsi_plus_icon_hover_mobile'] = $option5['sfsi_plus_icon_hover_mobile'];
			unset($option5['sfsi_plus_icon_hover_mobile']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_mobile'])) {
		if (!isset($option5['sfsi_plus_icon_hover_mobile'])) {
			$option8['sfsi_plus_icon_hover_mobile'] = "yes";
		} else {
			$option8['sfsi_plus_icon_hover_mobile'] = $option5['sfsi_plus_icon_hover_mobile'];
			unset($option5['sfsi_plus_icon_hover_mobile']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_on_all_pages'])) {
		if (!isset($option5['sfsi_plus_icon_hover_on_all_pages'])) {
			$option8['sfsi_plus_icon_hover_on_all_pages'] = "yes";
		} else {
			$option8['sfsi_plus_icon_hover_on_all_pages'] = $option5['sfsi_plus_icon_hover_on_all_pages'];
			unset($option5['sfsi_plus_icon_hover_on_all_pages']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_home'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_home'])) {
			$option8['sfsi_plus_icon_hover_exclude_home'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_home'] = $option5['sfsi_plus_icon_hover_exclude_home'];
			unset($option5['sfsi_plus_icon_hover_exclude_home']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_page'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_page'])) {
			$option8['sfsi_plus_icon_hover_exclude_page'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_page'] = $option5['sfsi_plus_icon_hover_exclude_page'];
			unset($option5['sfsi_plus_icon_hover_exclude_page']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_post'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_post'])) {
			$option8['sfsi_plus_icon_hover_exclude_post'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_post'] = $option5['sfsi_plus_icon_hover_exclude_post'];
			unset($option5['sfsi_plus_icon_hover_exclude_post']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_tag'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_tag'])) {
			$option8['sfsi_plus_icon_hover_exclude_tag'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_tag'] = $option5['sfsi_plus_icon_hover_exclude_tag'];
			unset($option5['sfsi_plus_icon_hover_exclude_tag']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_category'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_category'])) {
			$option8['sfsi_plus_icon_hover_exclude_category'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_category'] = $option5['sfsi_plus_icon_hover_exclude_category'];
			unset($option5['sfsi_plus_icon_hover_exclude_category']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_date_archive'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_date_archive'])) {
			$option8['sfsi_plus_icon_hover_exclude_date_archive'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_date_archive'] = $option5['sfsi_plus_icon_hover_exclude_date_archive'];
			unset($option5['sfsi_plus_icon_hover_exclude_date_archive']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_author_archive'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_author_archive'])) {
			$option8['sfsi_plus_icon_hover_exclude_author_archive'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_author_archive'] = $option5['sfsi_plus_icon_hover_exclude_author_archive'];
			unset($option5['sfsi_plus_icon_hover_exclude_author_archive']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_search'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_search'])) {
			$option8['sfsi_plus_icon_hover_exclude_search'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_search'] = $option5['sfsi_plus_icon_hover_exclude_search'];
			unset($option5['sfsi_plus_icon_hover_exclude_search']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_url'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_url'])) {
			$option8['sfsi_plus_icon_hover_exclude_url'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_exclude_url'] = $option5['sfsi_plus_icon_hover_exclude_url'];
			unset($option5['sfsi_plus_icon_hover_exclude_url']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_mobile'])) {
		if (!isset($option5['sfsi_plus_icon_hover_mobile'])) {
			$option8['sfsi_plus_icon_hover_mobile'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_mobile'] = $option5['sfsi_plus_icon_hover_mobile'];
			unset($option5['sfsi_plus_icon_hover_mobile']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_urlKeywords'])) {
		if (!isset($option5['sfsi_plus_icon_hover_urlKeywords'])) {
			$option8['sfsi_plus_icon_hover_urlKeywords'] = array();
		} else {
			$option8['sfsi_plus_icon_hover_urlKeywords'] = $option5['sfsi_plus_icon_hover_urlKeywords'];
			unset($option5['sfsi_plus_icon_hover_urlKeywords']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_urlKeywords'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_urlKeywords'])) {
			$option8['sfsi_plus_icon_hover_include_urlKeywords'] = array();
		} else {
			$option8['sfsi_plus_icon_hover_include_urlKeywords'] = $option5['sfsi_plus_icon_hover_include_urlKeywords'];
			unset($option5['sfsi_plus_icon_hover_include_urlKeywords']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_exclude_urlKeywords'])) {
		if (!isset($option5['sfsi_plus_icon_hover_exclude_urlKeywords'])) {
			$option8['sfsi_plus_icon_hover_exclude_urlKeywords'] = array();
		} else {
			$option8['sfsi_plus_icon_hover_exclude_urlKeywords'] = $option5['sfsi_plus_icon_hover_exclude_urlKeywords'];
			unset($option5['sfsi_plus_icon_hover_exclude_urlKeywords']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_switch_exclude_custom_post_types'])) {
		if (!isset($option5['sfsi_plus_icon_hover_switch_exclude_custom_post_types'])) {
			$option8['sfsi_plus_icon_hover_switch_exclude_custom_post_types'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_switch_exclude_custom_post_types'] = $option5['sfsi_plus_icon_hover_switch_exclude_custom_post_types'];
			unset($option5['sfsi_plus_icon_hover_switch_exclude_custom_post_types']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_list_exclude_custom_post_types'])) {
		if (!isset($option5['sfsi_plus_icon_hover_list_exclude_custom_post_types'])) {
			$option8['sfsi_plus_icon_hover_list_exclude_custom_post_types'] = serialize(array());
		} else {
			$option8['sfsi_plus_icon_hover_list_exclude_custom_post_types'] = $option5['sfsi_plus_icon_hover_list_exclude_custom_post_types'];
			unset($option5['sfsi_plus_icon_hover_list_exclude_custom_post_types']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_switch_exclude_taxonomies'])) {
		if (!isset($option5['sfsi_plus_icon_hover_switch_exclude_taxonomies'])) {
			$option8['sfsi_plus_icon_hover_switch_exclude_taxonomies'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_switch_exclude_taxonomies'] = $option5['sfsi_plus_icon_hover_switch_exclude_taxonomies'];
			unset($option5['sfsi_plus_icon_hover_switch_exclude_taxonomies']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_list_exclude_taxonomies'])) {
		if (!isset($option5['sfsi_plus_icon_hover_list_exclude_taxonomies'])) {
			$option8['sfsi_plus_icon_hover_list_exclude_taxonomies'] = serialize(array());
		} else {
			$option8['sfsi_plus_icon_hover_list_exclude_taxonomies'] = $option5['sfsi_plus_icon_hover_list_exclude_taxonomies'];
			unset($option5['sfsi_plus_icon_hover_list_exclude_taxonomies']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_home'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_home'])) {
			$option8['sfsi_plus_icon_hover_include_home'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_home'] = $option5['sfsi_plus_icon_hover_include_home'];
			unset($option5['sfsi_plus_icon_hover_include_home']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_page'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_page'])) {
			$option8['sfsi_plus_icon_hover_include_page'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_page'] = $option5['sfsi_plus_icon_hover_include_page'];
			unset($option5['sfsi_plus_icon_hover_include_page']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_post'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_post'])) {
			$option8['sfsi_plus_icon_hover_include_post'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_post'] = $option5['sfsi_plus_icon_hover_include_post'];
			unset($option5['sfsi_plus_icon_hover_include_post']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_tag'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_tag'])) {
			$option8['sfsi_plus_icon_hover_include_tag'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_tag'] = $option5['sfsi_plus_icon_hover_include_tag'];
			unset($option5['sfsi_plus_icon_hover_include_tag']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_category'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_category'])) {
			$option8['sfsi_plus_icon_hover_include_category'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_category'] = $option5['sfsi_plus_icon_hover_include_category'];
			unset($option5['sfsi_plus_icon_hover_include_category']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_date_archive'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_date_archive'])) {
			$option8['sfsi_plus_icon_hover_include_date_archive'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_date_archive'] = $option5['sfsi_plus_icon_hover_include_date_archive'];
			unset($option5['sfsi_plus_icon_hover_include_date_archive']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_author_archive'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_author_archive'])) {
			$option8['sfsi_plus_icon_hover_include_author_archive'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_author_archive'] = $option5['sfsi_plus_icon_hover_include_author_archive'];
			unset($option5['sfsi_plus_icon_hover_include_author_archive']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_search'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_search'])) {
			$option8['sfsi_plus_icon_hover_include_search'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_search'] = $option5['sfsi_plus_icon_hover_include_search'];
			unset($option5['sfsi_plus_icon_hover_include_search']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_include_url'])) {
		if (!isset($option5['sfsi_plus_icon_hover_include_url'])) {
			$option8['sfsi_plus_icon_hover_include_url'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_include_url'] = $option5['sfsi_plus_icon_hover_include_url'];
			unset($option5['sfsi_plus_icon_hover_include_url']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_mobile'])) {
		if (!isset($option5['sfsi_plus_icon_hover_mobile'])) {
			$option8['sfsi_plus_icon_hover_mobile'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_mobile'] = $option5['sfsi_plus_icon_hover_mobile'];
			unset($option5['sfsi_plus_icon_hover_mobile']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_urlKeywords'])) {
		if (!isset($option5['sfsi_plus_icon_hover_urlKeywords'])) {
			$option8['sfsi_plus_icon_hover_urlKeywords'] = array();
		} else {
			$option8['sfsi_plus_icon_hover_urlKeywords'] = $option5['sfsi_plus_icon_hover_urlKeywords'];
			unset($option5['sfsi_plus_icon_hover_urlKeywords']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_switch_include_custom_post_types'])) {
		if (!isset($option5['sfsi_plus_icon_hover_switch_include_custom_post_types'])) {
			$option8['sfsi_plus_icon_hover_switch_include_custom_post_types'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_switch_include_custom_post_types'] = $option5['sfsi_plus_icon_hover_switch_include_custom_post_types'];
			unset($option5['sfsi_plus_icon_hover_switch_include_custom_post_types']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_list_include_custom_post_types'])) {
		if (!isset($option5['sfsi_plus_icon_hover_list_include_custom_post_types'])) {
			$option8['sfsi_plus_icon_hover_list_include_custom_post_types'] = serialize(array());
		} else {
			$option8['sfsi_plus_icon_hover_list_include_custom_post_types'] = $option5['sfsi_plus_icon_hover_list_include_custom_post_types'];
			unset($option5['sfsi_plus_icon_hover_list_include_custom_post_types']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_switch_include_taxonomies'])) {
		if (!isset($option5['sfsi_plus_icon_hover_switch_include_taxonomies'])) {
			$option8['sfsi_plus_icon_hover_switch_include_taxonomies'] = "no";
		} else {
			$option8['sfsi_plus_icon_hover_switch_include_taxonomies'] = $option5['sfsi_plus_icon_hover_switch_include_taxonomies'];
			unset($option5['sfsi_plus_icon_hover_switch_include_taxonomies']);
		}
	}
	if (!isset($option8['sfsi_plus_icon_hover_list_include_taxonomies'])) {
		if (!isset($option5['sfsi_plus_icon_hover_list_include_taxonomies'])) {
			$option8['sfsi_plus_icon_hover_list_include_taxonomies'] = serialize(array());
		} else {
			$option8['sfsi_plus_icon_hover_list_include_taxonomies'] = $option5['sfsi_plus_icon_hover_list_include_taxonomies'];
			unset($option5['sfsi_plus_icon_hover_list_include_taxonomies']);
		}
	}

	update_option('sfsi_premium_section8_options', serialize($option8));

	update_option('sfsi_premium_section5_options', serialize($option5));
	//update options for hover image version 9.7 ENDS

	// Remove job queue data with old code
	delete_option('sfsi-premium-fb-cumulative-api-call-queue');
	delete_option('sfsi-premium-fb-uncumulative-api-call-queue');

	// Create job queue table for handling facebook count caching
	$sfsi_job_queue = sfsiJobQueue::getInstance();

	$jobQueueInstalled = get_option('sfsi_premium_job_queue_installed', false);

	if (false == $jobQueueInstalled) {
		$sfsi_job_queue->install_job_queue();
	}

	sfsi_plus_add_new_icons_in_saved_desktop_mobile_order($option5);

	sfsi_premium_migrate_fb_cached_count();
	$sfsi_plus_all_icon_count = get_option('sfsi_premium_icon_counts', false);
	if (false == $sfsi_plus_all_icon_count) {
		update_option('sfsi_premium_icon_counts', 0);
	}
	$sfsi_plus_all_sticky_icon_count = get_option('sfsi_premium_sticky_icon_counts', false);
	if (false == $sfsi_plus_all_sticky_icon_count) {
		update_option('sfsi_premium_sticky_icon_counts', 0);
	}
	sfsi_plus_remove_google();
}

function sfsi_plus_remove_google()
{
	$option1 = maybe_unserialize(get_option('sfsi_premium_section1_options', false));
	if (isset($option1['sfsi_plus_google_display'])) {
		unset($option1['sfsi_plus_google_display']);
	}
	if (isset($option1['sfsi_plus_google_mobiledisplay'])) {
		unset($option1['sfsi_plus_google_mobiledisplay']);
	}
	if (isset($option1['sfsi_plus_google_display'])) {
		unset($option1['sfsi_plus_google_display']);
	}
	update_option('sfsi_premium_section1_options', serialize($option1));

	$option2 = maybe_unserialize(get_option('sfsi_premium_section2_options', false));
	if (isset($option2['sfsi_plus_google_page'])) {
		unset($option2['sfsi_plus_google_page']);
	}
	if (isset($option2['sfsi_plus_google_pageURL'])) {
		unset($option2['sfsi_plus_google_pageURL']);
	}
	if (isset($option2['sfsi_plus_googleLike_option'])) {
		unset($option2['sfsi_plus_googleLike_option']);
	}
	if (isset($option2['sfsi_plus_googleShare_option'])) {
		unset($option2['sfsi_plus_googleShare_option']);
	}
	if (isset($option2['sfsi_plus_googleFollow_option'])) {
		unset($option2['sfsi_plus_googleFollow_option']);
	}
	update_option('sfsi_premium_section2_options', serialize($option2));

	$option4 =  maybe_unserialize(get_option('sfsi_premium_section4_options', false));
	if (isset($option4['sfsi_plus_google_api_key'])) {
		unset($option4['sfsi_plus_google_api_key']);
	}
	if (isset($option4['sfsi_plus_google_countsDisplay'])) {
		unset($option4['sfsi_plus_google_countsDisplay']);
	}
	if (isset($option4['sfsi_plus_google_countsFrom'])) {
		unset($option4['sfsi_plus_google_countsFrom']);
	}
	if (isset($option4['sfsi_plus_google_manualCounts'])) {
		unset($option4['sfsi_plus_google_manualCounts']);
	}
	update_option('sfsi_premium_section4_options', serialize($option4));

	$option5 =  maybe_unserialize(get_option('sfsi_premium_section5_options', false));
	if (isset($option5['sfsi_plus_google_icons_language'])) {
		unset($option5['sfsi_plus_google_icons_language']);
	}
	if (isset($option5['sfsi_plus_google_MouseOverText'])) {
		unset($option5['sfsi_plus_google_MouseOverText']);
	}
	if (isset($option5['sfsi_order_icons_desktop']) && is_array($option5['sfsi_order_icons_desktop'])) {
		$sfsi_premium_icons_order = $option5['sfsi_order_icons_desktop'];
		foreach ($sfsi_premium_icons_order as $index => $icon_order) {
			if ($icon_order["iconName"] == "google") {
				unset($sfsi_premium_icons_order[$index]);
			}
		}
		$option5['sfsi_order_icons_desktop'] = $sfsi_premium_icons_order;
	}
	if (isset($option5['sfsi_order_icons_mobile']) && is_array($option5['sfsi_order_icons_mobile'])) {
		$sfsi_premium_icons_order = $option5['sfsi_order_icons_mobile'];
		foreach ($sfsi_premium_icons_order as $index => $icon_order) {
			if ($icon_order["iconName"] == "google") {
				unset($sfsi_premium_icons_order[$index]);
			}
		}
		$option5['sfsi_order_icons_mobile'] = $sfsi_premium_icons_order;
	}
	update_option('sfsi_premium_section5_options', serialize($option5));

	$option8 =  maybe_unserialize(get_option('sfsi_premium_section8_options', false));
	if (isset($option8['sfsi_plus_rectgp'])) {
		unset($option8['sfsi_plus_rectgp']);
	}
	if (isset($option5['sfsi_plus_responsive_icons']) && is_array($option5['sfsi_plus_responsive_icons'])) {
		$sfsi_plus_responsive_icons = $option5['sfsi_plus_responsive_icons'];
		if (isset($sfsi_plus_responsive_icons["default_icons"]) && is_array($sfsi_plus_responsive_icons["default_icons"])) {
			if (isset($sfsi_plus_responsive_icons["default_icons"]["google"])) {
				unset($sfsi_plus_responsive_icons["default_icons"]["google"]);
			}
		}
		$option5['sfsi_plus_responsive_icons'] = $sfsi_plus_responsive_icons;
	}
	update_option('sfsi_premiun_section8_options', serialize($option8));
}

function sfsi_premium_activate_plugin()
{

	add_option('sfsi_premium_plugin_do_activation_redirect', true);

	// Adding option to save current active licensing api added in version 6.6
	if (false === get_option('sfsi_active_license_api_name')) {

		$ultimate_license_key  = get_option(ULTIMATELYSOCIAL_LICENSING . '_license_key');
		$sellcodes_license_key = get_option(SELLCODES_LICENSING . '_license_key');

		if (false !== $ultimate_license_key) {
			update_option('sfsi_active_license_api_name', ULTIMATELYSOCIAL_LICENSING);
		}

		if (false !== $sellcodes_license_key) {
			update_option('sfsi_active_license_api_name', SELLCODES_LICENSING);
		}
	}

	/* check for CURL enable at server */
	// sfsi_plus_curl_enable_notice();	

	$options1 = array(
		'sfsi_plus_rss_display'				=> 'yes',
		'sfsi_plus_email_display'			=> 'yes',
		'sfsi_plus_facebook_display'		=> 'yes',
		'sfsi_plus_twitter_display'			=> 'yes',
		'sfsi_plus_share_display'			=> 'no',
		'sfsi_plus_pinterest_display'		=> 'no',
		'sfsi_plus_instagram_display'		=> 'no',
		'sfsi_plus_linkedin_display'		=> 'no',
		'sfsi_plus_youtube_display'			=> 'no',
		'sfsi_plus_houzz_display'			=> 'no',
		'sfsi_plus_snapchat_display'		=> 'no',
		'sfsi_plus_whatsapp_display'		=> 'no',
		'sfsi_plus_phone_display'			=> 'no',
		'sfsi_plus_skype_display'			=> 'no',
		'sfsi_plus_vimeo_display'			=> 'no',
		'sfsi_plus_soundcloud_display'		=> 'no',
		'sfsi_plus_yummly_display'			=> 'no',
		'sfsi_plus_flickr_display'			=> 'no',
		'sfsi_plus_reddit_display'			=> 'no',
		'sfsi_plus_tumblr_display'			=> 'no',
		'sfsi_plus_fbmessenger_display'		=> 'no',
		'sfsi_plus_gab_display'				=> 'no',
		'sfsi_plus_mix_display'				=> 'no',
		'sfsi_plus_ok_display'				=> 'no',
		'sfsi_plus_telegram_display'		=> 'no',
		'sfsi_plus_vk_display'				=> 'no',
		'sfsi_plus_weibo_display'			=> 'no',
		'sfsi_plus_wechat_display'			=> 'no',
		'sfsi_plus_xing_display'			=> 'no',
		'sfsi_plus_copylink_display'		=> 'no',
		'sfsi_plus_mastodon_display'		=> 'no',

		'sfsi_custom_display'				=> '',

		'sfsi_custom_mobile_icons'			=> '',
		'sfsi_custom_desktop_icons' 		=> '',
		'sfsi_custom_files'					=> '',

		'sfsi_plus_icons_onmobile'			=> 'no',
		'sfsi_plus_rss_mobiledisplay'		=> 'no',
		'sfsi_plus_email_mobiledisplay'		=> 'no',
		'sfsi_plus_facebook_mobiledisplay'	=> 'no',
		'sfsi_plus_twitter_mobiledisplay'	=> 'no',
		'sfsi_plus_share_mobiledisplay'		=> 'no',
		'sfsi_plus_pinterest_mobiledisplay'	=> 'no',
		'sfsi_plus_instagram_mobiledisplay'	=> 'no',
		'sfsi_plus_linkedin_mobiledisplay'	=> 'no',
		'sfsi_plus_youtube_mobiledisplay'	=> 'no',
		'sfsi_plus_houzz_mobiledisplay'		=> 'no',
		'sfsi_plus_snapchat_mobiledisplay'	=> 'no',
		'sfsi_plus_whatsapp_mobiledisplay'	=> 'no',
		'sfsi_plus_phone_mobiledisplay'		=> 'no',
		'sfsi_plus_skype_mobiledisplay'		=> 'no',
		'sfsi_plus_vimeo_mobiledisplay'		=> 'no',
		'sfsi_plus_soundcloud_mobiledisplay' => 'no',
		'sfsi_plus_yummly_mobiledisplay'	=> 'no',
		'sfsi_plus_flickr_mobiledisplay'	=> 'no',
		'sfsi_plus_reddit_mobiledisplay'	=> 'no',
		'sfsi_plus_tumblr_mobiledisplay'	=> 'no',
		'sfsi_plus_fbmessenger_mobiledisplay'	=> 'no',
		'sfsi_plus_gab_mobiledisplay'			=> 'no',
		'sfsi_plus_mix_mobiledisplay'			=> 'no',
		'sfsi_plus_ok_mobiledisplay'			=> 'no',
		'sfsi_plus_telegram_mobiledisplay'		=> 'no',
		'sfsi_plus_vk_mobiledisplay'			=> 'no',
		'sfsi_plus_weibo_mobiledisplay'			=> 'no',
		'sfsi_plus_wechat_mobiledisplay'		=> 'no',
		'sfsi_plus_xing_mobiledisplay'			=> 'no',
		'sfsi_plus_copylink_mobiledisplay'		=> 'no',
		'sfsi_plus_mastodon_mobiledisplay'		=> 'no',
	);
	add_option('sfsi_premium_section1_options',  serialize($options1));

	if (get_option('sfsi_premium_feed_id') && get_option('sfsi_premium_redirect_url')) {
		$sffeeds["feed_id"] 		= sanitize_text_field(get_option('sfsi_premium_feed_id'));
		$sffeeds["redirect_url"] 	= sanitize_text_field(get_option('sfsi_premium_redirect_url'));
		$sffeeds 					= (object) $sffeeds;
	} else {
		$sffeeds = SFSI_PLUS_getFeedUrl();
	}
	// var_dump($sffeeds);

	/* Links and icons  options */
	$options2 = array(
		'sfsi_plus_rss_url'						=> sfsi_plus_get_bloginfo('rss2_url'),
		'sfsi_plus_rss_icons'					=> 'subscribe',
		'sfsi_plus_email_url'					=> is_object($sffeeds) && isset($sffeeds->redirect_url) ? $sffeeds->redirect_url : '',
		'sfsi_plus_email_icons_functions'		=> 'sf',
		'sfsi_plus_email_icons_contact'			=> '',
		'sfsi_plus_email_icons_pageurl'			=> '',
		'sfsi_plus_email_icons_mailchimp_apikey' => '',
		'sfsi_plus_email_icons_mailchimp_listid' => '',
		'sfsi_plus_email_icons_subject_line'    => '${title}',
		'sfsi_plus_email_icons_email_content'	=> 'Check out this article «${title}»: ${link}',

		'sfsi_plus_facebookPage_option'		=> 'no',
		'sfsi_plus_facebookPage_url'		=> '',
		'sfsi_plus_facebookProfile_url'		=> '',
		'sfsi_plus_facebookLike_option'		=> 'yes',
		'sfsi_plus_facebookShare_option'	=> 'yes',
		'sfsi_plus_facebookFollow_option'	=> 'no',

		'sfsi_plus_twitter_followme'		=> 'no',
		'sfsi_plus_twitter_followUserName'	=> '',
		'sfsi_plus_twitter_aboutPage'		=> 'yes',
		'sfsi_plus_twitter_page'			=> 'no',
		'sfsi_plus_twitter_pageURL'			=> '',

		'sfsi_plus_youtube_pageUrl'			=> '',
		'sfsi_plus_youtube_page'			=> 'no',
		'sfsi_plus_youtube_follow'			=> 'no',
		'sfsi_plus_youtubeusernameorid'		=> 'name',
		'sfsi_plus_ytube_chnlid'			=> '',
		'sfsi_plus_ytube_user'				=> '',
		'sfsi_plus_pinterest_page'			=> 'no',
		'sfsi_plus_pinterest_pageUrl'		=> '',
		'sfsi_plus_pinterest_pingBlog'		=> '',
		'sfsi_plus_instagram_page'			=> 'no',
		'sfsi_plus_instagram_pageUrl'		=> '',

		//'sfsi_plus_houzzVisit_option' 		=> 'no',
		'sfsi_plus_houzz_pageUrl'			=> '',
		// 'sfsi_plus_houzzShare_option' 		=> 'no',
		// 'sfsi_plus_houzz_websiteId'			=> '',

		'sfsi_plus_snapchat_pageUrl'		=> '',
		'sfsi_plus_whatsapp_message'		=> '',
		'sfsi_plus_my_whatsapp_number'      => '',
		'sfsi_plus_whatsapp_number'			=> '',
		'sfsi_plus_whatsapp_share_page'     => '${title} ${link}',

		'sfsi_plus_skype_options'			=> 'call',
		'sfsi_plus_skype_pageUrl'			=> '',
		'sfsi_plus_vimeo_pageUrl'			=> '',
		'sfsi_plus_soundcloud_pageUrl'		=> '',

		'sfsi_plus_yummlyVisit_option'		=> 'no',
		'sfsi_plus_yummly_pageUrl'			=> '',
		'sfsi_plus_yummlyShare_option' 		=> 'no',

		'sfsi_plus_flickr_pageUrl'			=> '',
		'sfsi_plus_reddit_pageUrl'			=> '',
		'sfsi_plus_tumblr_pageUrl'			=> '',
		'sfsi_plus_whatsapp_url_type'		=> '',
		'sfsi_plus_reddit_url_type'			=> '',

		'sfsi_plus_linkedin_page'			=> 'no',
		'sfsi_plus_linkedin_pageURL'		=> '',
		'sfsi_plus_linkedin_follow'			=> 'no',
		'sfsi_plus_linkedin_followCompany'	=> '',
		'sfsi_plus_linkedin_SharePage'		=> 'yes',
		'sfsi_plus_linkedin_recommendBusines' => 'no',
		'sfsi_plus_linkedin_recommendCompany' => '',
		'sfsi_plus_linkedin_recommendProductId' => '',

		'sfsi_plus_fbmessengerContact_option' => 'no',
		'sfsi_plus_fbmessengerContact_url'	  => '',
		'sfsi_plus_fbmessengerShare_option'	  => 'no',

		'sfsi_plus_mixVisit_option'			=> 'no',
		'sfsi_plus_vkVisit_option'			=> 'no',
		'sfsi_plus_weiboVisit_option'		=> 'no',
		'sfsi_plus_xingShare_option'		=> 'no',
		'sfsi_plus_xingVisit_option'		=> 'no',
		'sfsi_plus_xingFollow_option'		=> 'no',
		'sfsi_plus_mixVisit_url'			=> '',
		'sfsi_plus_mixShare_option'			=> 'no',

		'sfsi_plus_okVisit_option'			=> 'no',
		'sfsi_plus_okVisit_url'				=> '',
		'sfsi_plus_okSubscribe_option'		=> 'no',
		'sfsi_plus_okSubscribe_userid'		=> '',
		'sfsi_plus_okLike_option'			=> 'no',
		'sfsi_plus_wechatFollow_option'		=> 'no',
		'sfsi_plus_wechatShare_option'		=> 'no',

		'sfsi_plus_telegramShare_option'    => 'no',
		'sfsi_plus_telegramMessage_option'  => 'no',
		'sfsi_plus_telegram_message'   		=> '',
		'sfsi_plus_telegram_username'    => '',
		'sfsi_plus_CustomIcon_links'		=> ''
	);
	add_option('sfsi_premium_section2_options',  serialize($options2));

	/* Design and animation option  */
	$options3 = array(
		'sfsi_plus_mouseOver'	=> 'no',
		'sfsi_plus_mouseOver_effect'		=> 'fade_in',
		'sfsi_plus_shuffle_icons'			=> 'no',
		'sfsi_plus_shuffle_Firstload'		=> 'no',
		'sfsi_plus_shuffle_interval'		=> 'no',
		'sfsi_plus_shuffle_intervalTime'	=> '',
		'sfsi_plus_actvite_theme'			=> 'default',

		'sfsi_plus_mouseOver_effect_type'   => 'same_icons',
		'sfsi_plus_mouseOver_other_icon_images'   => serialize(array()),
		'sfsi_plus_mouseover_other_icons_transition_effect'   => 'noeffect',
		'sfsi_plus_rss_bgColor' => '',
		'sfsi_plus_email_bgColor' => '',
		'sfsi_plus_facebook_bgColor' => '',
		'sfsi_plus_twitter_bgColor' => '',
		'sfsi_plus_share_bgColor' => '',
		'sfsi_plus_youtube_bgColor' => '',
		'sfsi_plus_pinterest_bgColor' => '',
		'sfsi_plus_linkedin_bgColor' => '',
		'sfsi_plus_instagram_bgColor' => '',
		'sfsi_plus_ria_bgColor' => '',
		'sfsi_plus_inha_bgColor' => '',
		'sfsi_plus_houzz_bgColor' => '',
		'sfsi_plus_snapchat_bgColor' => '',
		'sfsi_plus_whatsapp_bgColor' => '',
		'sfsi_plus_skype_bgColor' => '',
		'sfsi_plus_phone_bgColor' => '',
		'sfsi_plus_vimeo_bgColor' => '',
		'sfsi_plus_soundcloud_bgColor' => '',
		'sfsi_plus_yummly_bgColor' => '',
		'sfsi_plus_flickr_bgColor' => '',
		'sfsi_plus_reddit_bgColor' => '',
		'sfsi_plus_tumblr_bgColor' => '',
		'sfsi_plus_fbmessenger_bgColor' => '',
		'sfsi_plus_gab_bgColor' => '',
		'sfsi_plus_mix_bgColor' => '',
		'sfsi_plus_ok_bgColor' => '',
		'sfsi_plus_telegram_bgColor' => '',
		'sfsi_plus_vk_bgColor' => '',
		'sfsi_plus_weibo_bgColor' => '',
		'sfsi_plus_xing_bgColor' => '',
		'sfsi_plus_copylink_bgColor' => '',
		'sfsi_plus_mastodon_bgColor' => '',
	);
	add_option('sfsi_premium_section3_options',  serialize($options3));

	/* display counts options */
	$options4 = array(
		'sfsi_plus_display_counts'			 => 'no',
		'sfsi_plus_email_countsDisplay'		 => 'no',
		'sfsi_plus_email_countsFrom'		 => 'source',
		'sfsi_plus_email_manualCounts'		 => '20',
		'sfsi_plus_rss_countsDisplay'		 => 'no',
		'sfsi_plus_rss_manualCounts'		 => '20',
		'sfsi_plus_facebook_PageLink'		 => '',
		'sfsi_plus_facebook_countsDisplay'	 => 'no',
		'sfsi_plus_facebook_countsFrom'		 => 'manual',
		'sfsi_plus_facebook_manualCounts'	 => '20',
		'sfsi_plus_facebook_countsFrom_blog' => '',
		'sfsi_plus_facebook_appid'			 => '',
		'sfsi_plus_facebook_appsecret'		 => '',
		'sfsi_plus_fb_count_caching_active'  => 'no',
		'sfsi_plus_fb_caching_interval'		 => 1,

		'sfsi_plus_twitter_countsDisplay'	=> 'no',
		'sfsi_plus_twitter_countsFrom'		=> 'manual',
		'sfsi_plus_bluesky_countsFrom'		=> 'manual',
		'sfsi_plus_threads_countsFrom'		=> 'manual',
		'sfsi_plus_tw_count_caching_active' => 'no',

		'sfsi_plus_twitter_manualCounts'	=> '20',
		'sfsi_plus_threads_manualCounts'	=> '20',
		'sfsi_plus_bluesky_manualCounts'	=> '20',

		'sfsi_plus_linkedIn_countsDisplay'	=> 'no',
		'sfsi_plus_linkedIn_countsFrom'		=> 'manual',
		'sfsi_plus_linkedIn_manualCounts'	=> '20',
		'sfsi_plus_ln_api_key'				=> '',
		'sfsi_plus_ln_secret_key'			=> '',
		'sfsi_plus_ln_oAuth_user_token'		=> '',
		'sfsi_plus_ln_company'				=> '',
		'sfsi_plus_youtube_user'			=> '',
		'sfsi_plus_youtube_channelId'		=> '',
		'sfsi_plus_youtube_countsDisplay'	=> 'no',
		'sfsi_plus_youtube_countsFrom'		=> 'manual',
		'sfsi_plus_youtube_manualCounts'	=> '20',
		'sfsi_plus_pinterest_countsDisplay'	=> 'no',
		'sfsi_plus_pinterest_countsFrom'	=> 'manual',
		'sfsi_plus_pinterest_manualCounts'	=> '20',

		'sfsi_plus_pinterest_appid'			=> '',
		'sfsi_plus_pinterest_appsecret'		=> '',
		'sfsi_plus_pinterest_appurl'  		=> '',

		'sfsi_plus_pinterest_user'			=> '',
		'sfsi_plus_pinterest_board_name'	=> '',
		'sfsi_plus_pinterest_access_token'  => '',

		'sfsi_plus_instagram_countsFrom'	=> 'manual',
		'sfsi_plus_instagram_countsDisplay'	=> 'no',
		'sfsi_plus_instagram_manualCounts'	=> '20',
		'sfsi_plus_instagram_User'			=> '',
		'sfsi_plus_instagram_clientid'		=> '',
		'sfsi_plus_instagram_appurl'		=> '',
		'sfsi_plus_instagram_token'			=> '',
		'sfsi_plus_shares_countsDisplay'	=> 'no',
		'sfsi_plus_shares_countsFrom'		=> 'manual',
		'sfsi_plus_shares_manualCounts'		=> '20',
		'sfsi_plus_houzz_countsDisplay'		=> 'no',
		'sfsi_plus_houzz_countsFrom'		=> 'manual',
		'sfsi_plus_houzz_manualCounts'		=> '20',

		'sfsi_plus_snapchat_countsDisplay'	=> 'no',
		'sfsi_plus_snapchat_countsFrom'		=> 'manual',
		'sfsi_plus_snapchat_manualCounts'	=> '20',

        'sfsi_plus_ria_countsDisplay'	=> 'no',
		'sfsi_plus_ria_countsFrom'		=> 'manual',
		'sfsi_plus_ria_manualCounts'	=> '20',

        'sfsi_plus_inha_countsDisplay'	=> 'no',
        'sfsi_plus_inha_countsFrom'		=> 'manual',
        'sfsi_plus_inha_manualCounts'	=> '20',

		'sfsi_plus_whatsapp_countsDisplay'	=> 'no',
		'sfsi_plus_whatsapp_countsFrom'		=> 'manual',
		'sfsi_plus_whatsapp_manualCounts'	=> '20',

		'sfsi_plus_skype_countsDisplay'		=> 'no',
		'sfsi_plus_skype_countsFrom'		=> 'manual',
		'sfsi_plus_skype_manualCounts'		=> '20',

		'sfsi_plus_vimeo_countsDisplay'		=> 'no',
		'sfsi_plus_vimeo_countsFrom'		=> 'manual',
		'sfsi_plus_vimeo_manualCounts'		=> '20',

		'sfsi_plus_soundcloud_countsDisplay' => 'no',
		'sfsi_plus_soundcloud_countsFrom'	=> 'manual',
		'sfsi_plus_soundcloud_manualCounts'	=> '20',

		'sfsi_plus_yummly_countsDisplay'	=> 'no',
		'sfsi_plus_yummly_countsFrom'		=> 'manual',
		'sfsi_plus_yummly_manualCounts'		=> '20',

		'sfsi_plus_flickr_countsDisplay'	=> 'no',
		'sfsi_plus_flickr_countsFrom'		=> 'manual',
		'sfsi_plus_flickr_manualCounts'		=> '20',
		'sfsi_plus_reddit_countsDisplay'	=> 'no',
		'sfsi_plus_reddit_countsFrom'		=> 'manual',
		'sfsi_plus_reddit_manualCounts'		=> '20',

		'sfsi_plus_tumblr_countsDisplay'	=> 'no',
		'sfsi_plus_tumblr_countsFrom'		=> 'manual',
		'sfsi_plus_tumblr_manualCounts'		=> '20',
		'sfsi_plus_min_display_counts'		=> 1,

		'sfsi_plus_fbmessenger_countsFrom' => 'manual',
		'sfsi_plus_mix_countsFrom'		  => 'manual',
		'sfsi_plus_ok_countsFrom' 		  => 'manual',
		'sfsi_plus_vk_countsFrom' 		  => 'manual',
		'sfsi_plus_telegram_countsFrom' 	  => 'manual',
		'sfsi_plus_weibo_countsFrom'		  => 'manual',
		'sfsi_plus_xing_countsFrom' 		  => 'manual',
		'sfsi_plus_mastodon_countsFrom' 	  => 'manual',

		'sfsi_plus_fbmessenger_countsDisplay' => 'no',
		'sfsi_plus_mix_countsDisplay'		  => 'no',
		'sfsi_plus_ok_countsDisplay' 		  => 'no',
		'sfsi_plus_vk_countsDisplay' 		  => 'no',
		'sfsi_plus_telegram_countsDisplay' 	  => 'no',
		'sfsi_plus_weibo_countsDisplay'		  => 'no',
		'sfsi_plus_xing_countsDisplay' 		  => 'no',
		'sfsi_plus_mastodon_countsDisplay' 	  => 'no',

		'sfsi_plus_fbmessenger_manualCounts' => '20',
		'sfsi_plus_mix_manualCounts' 		 => '20',
		'sfsi_plus_ok_manualCounts' 		 => '20',
		'sfsi_plus_vk_manualCounts' 		 => '20',
		'sfsi_plus_telegram_manualCounts' 	 => '20',
		'sfsi_plus_weibo_manualCounts' 		 => '20',
        'sfsi_plus_wechat_manualCounts'      => '20',
        'sfsi_plus_copylink_manualCounts'      => '20',
		'sfsi_plus_xing_manualCounts' 		 => '20',
		'sfsi_plus_mastodon_manualCounts' 	 => '20',
	);

	add_option('sfsi_premium_section4_options',  serialize($options4));

	// Setting to allow USM to add open graph meta tags //
	$is_other_seo_plugins_active 		= sfsi_plus_checkmetas();
	$sfsi_plus_disable_usm_og_meta_tags = ($is_other_seo_plugins_active) ? "yes" : "no";

	$options5 = array(
		'sfsi_plus_icons_size'				=> '40',
		'sfsi_plus_icons_spacing'			=> '5',
		'sfsi_plus_icons_verical_spacing'	=> '5',

		'sfsi_plus_mobile_icon_alignment_setting'			=> 'no',
		'sfsi_plus_mobile_horizontal_verical_Alignment'		=> 'Horizontal',
		'sfsi_plus_mobile_icons_Alignment_via_widget'		=> 'left',
		'sfsi_plus_mobile_icons_Alignment_via_shortcode'	=> 'left',
		'sfsi_plus_mobile_icons_Alignment'					=> 'left',
		'sfsi_plus_mobile_icons_perRow'					=> '5',

		'sfsi_plus_mobile_icon_setting'		=> 'no',
		'sfsi_plus_icons_mobilesize'		=> '40',
		'sfsi_plus_icons_mobilespacing'		=> '5',
		'sfsi_plus_icons_verical_mobilespacing'	=> '5',

		'sfsi_plus_horizontal_verical_Alignment'  => 'Horizontal',
		'sfsi_plus_icons_Alignment_via_shortcode' => 'left',
		'sfsi_plus_icons_Alignment_via_widget'    => 'left',

		'sfsi_plus_icons_Alignment'			=> 'left',
		'sfsi_plus_icons_perRow'			=> '5',
		'sfsi_plus_follow_icons_language'	=> 'Follow_en_US',
		'sfsi_plus_facebook_icons_language'	=> 'Visit_us_en_US',
		'sfsi_plus_youtube_icons_language'  => 'Visit_us_en_US',
		'sfsi_plus_twitter_icons_language'	=> 'Visit_us_en_US',
		'sfsi_plus_linkedin_icons_language'	=> 'en_US',
		'sfsi_plus_icons_language'			=> 'en_US',
		'sfsi_plus_icons_ClickPageOpen'		=> 'no',
		'sfsi_plus_icons_AddNoopener' 		=> 'yes',
		'sfsi_plus_icons_float'				=> 'no',
		'sfsi_plus_disable_floaticons'		=> 'no',
		'sfsi_plus_disable_viewport'		=> 'no',
		'sfsi_plus_icons_floatPosition'		=> 'center-right',
		'sfsi_plus_icons_stick'				=> 'no',

		'sfsi_order_icons_desktop' 			=> serialize(array()),
		'sfsi_order_icons_mobile' 			=> serialize(array()), // Added in version 9.2 to support Order of mobile icons

		'sfsi_plus_rss_MouseOverText'		=> __( 'RSS', 'ultimate-social-media-plus' ),
		'sfsi_plus_email_MouseOverText'		=> __( 'Follow by Email', 'ultimate-social-media-plus' ),
		'sfsi_plus_twitter_MouseOverText'	=> __( 'X (Twitter)', 'ultimate-social-media-plus' ),
		'sfsi_plus_facebook_MouseOverText'	=> __( 'Facebook', 'ultimate-social-media-plus' ),
		'sfsi_plus_linkedIn_MouseOverText'	=> __( 'LinkedIn', 'ultimate-social-media-plus' ),
		'sfsi_plus_pinterest_MouseOverText'	=> __( 'Pinterest', 'ultimate-social-media-plus' ),
		'sfsi_plus_instagram_MouseOverText'	=> __( 'Instagram', 'ultimate-social-media-plus' ),
		'sfsi_plus_houzz_MouseOverText'		=> __( 'Houzz', 'ultimate-social-media-plus' ),
		'sfsi_plus_youtube_MouseOverText'	=> __( 'Youtube', 'ultimate-social-media-plus' ),
		'sfsi_plus_share_MouseOverText'		=> __( 'Share', 'ultimate-social-media-plus' ),
		'sfsi_plus_snapchat_MouseOverText'	=> __( 'Snapchat', 'ultimate-social-media-plus' ),
		'sfsi_plus_whatsapp_MouseOverText'	=> __( 'Whatsapp', 'ultimate-social-media-plus' ),
		'sfsi_plus_skype_MouseOverText'		=> __( 'Skype', 'ultimate-social-media-plus' ),
		'sfsi_plus_vimeo_MouseOverText'		=> __( 'Vimeo', 'ultimate-social-media-plus' ),
		'sfsi_plus_soundcloud_MouseOverText' => __( 'Soundcloud', 'ultimate-social-media-plus' ),
		'sfsi_plus_yummly_MouseOverText'	=> __( 'Yummly', 'ultimate-social-media-plus' ),
		'sfsi_plus_flickr_MouseOverText'	=> __( 'Flickr', 'ultimate-social-media-plus' ),
		'sfsi_plus_reddit_MouseOverText'	=> __( 'Reddit', 'ultimate-social-media-plus' ),
		'sfsi_plus_tumblr_MouseOverText'	=> __( 'Tumblr', 'ultimate-social-media-plus' ),

		'sfsi_plus_fbmessenger_MouseOverText' => __( 'Fb messenger', 'ultimate-social-media-plus' ),
		'sfsi_plus_gab_MouseOverText' 		  => __( 'GAB', 'ultimate-social-media-plus' ),
		'sfsi_plus_mix_MouseOverText'		  => __( 'Mix', 'ultimate-social-media-plus' ),
		'sfsi_plus_ok_MouseOverText' 		  => __( 'Ok', 'ultimate-social-media-plus' ),
		'sfsi_plus_telegram_MouseOverText'	  => __( 'Telegram', 'ultimate-social-media-plus' ),
		'sfsi_plus_vk_MouseOverText'		  => __( 'Vk', 'ultimate-social-media-plus' ),
		'sfsi_plus_weibo_MouseOverText'		  => __( 'Weibo', 'ultimate-social-media-plus' ),
		'sfsi_plus_wechat_MouseOverText'	  => __( 'Wechat', 'ultimate-social-media-plus' ),
		'sfsi_plus_xing_MouseOverText'		  => __( 'Xing', 'ultimate-social-media-plus' ),
		'sfsi_plus_copylink_MouseOverText'	  => __( 'Copy link', 'ultimate-social-media-plus' ),
		'sfsi_plus_mastodon_MouseOverText'	  => __( 'Mastodon', 'ultimate-social-media-plus' ),
		'sfsi_plus_ria_MouseOverText'	  => __( 'RateItAll', 'ultimate-social-media-plus' ),
		'sfsi_plus_inha_MouseOverText'	  => __( 'IncreasingHappiness', 'ultimate-social-media-plus' ),

		'sfsi_plus_custom_MouseOverTexts'	=> '',

		'sfsi_plus_Facebook_linking'		=> "facebookurl",
		'sfsi_plus_facebook_linkingcustom_url' => "",
		'sfsi_plus_tooltip_Color'           => '#FFF',
		'sfsi_plus_tooltip_border_Color'    => '#e7e7e7',
		'sfsi_plus_tooltip_alighn'          => 'Automatic',

		'sfsi_plus_twitter_aboutPageText'   => '${title} ${link}',
		'sfsi_plus_twitter_twtAddCard'		=> 'yes',
		'sfsi_plus_twitter_twtCardType'		=> 'summary',
		'sfsi_plus_twitter_card_twitter_handle'   => '',

		'sfsi_plus_social_sharing_options' => 'posttype',

		'sfsiSocialMediaImage' 				=> '',
		'sfsiSocialtTitleTxt' 				=> '',
		'sfsiSocialDescription' 			=> '',
		'sfsiSocialPinterestImage' 			=> '',
		'sfsiSocialPinterestDesc' 			=> '',
		'sfsiSocialTwitterDesc' 			=> '',

		'sfsi_custom_social_data_post_types_data' => serialize(array('page', 'post')),
		'sfsi_plus_disable_usm_og_meta_tags' => $sfsi_plus_disable_usm_og_meta_tags,

		'sfsi_premium_url_shortner_icons_names_list' => serialize(array('twitter', 'facebook', 'email')),
		'sfsi_plus_url_shorting_api_type_setting' => 'no',
		'sfsi_plus_url_shortner_bitly_key'  => '',
		'sfsi_plus_url_shortner_google_key' => '',
		'sfsi_plus_custom_css'				=> serialize(''),
		'sfsi_plus_custom_admin_css'		=> serialize(''),
		'sfsi_plus_loadjquery'				=> 'yes',
		'sfsi_plus_loadjscript'				=> 'yes',
		'sfsi_plus_icons_suppress_errors'	=> 'no',
		'sfsi_plus_nofollow_links'			=> 'no',
		'sfsi_plus_jscript_fileName'        =>  array(),
		'sfsi_plus_more_jscript_fileName'   => '',
		'sfsi_premium_static_path'			=>	'',
		'sfsi_premium_featured_image_as_og_image' => 'no',
		'sfsi_premium_pinterest_sharing_texts_and_pics' => 'no',
		'sfsi_premium_pinterest_placements'	=> 'no',
		'sfsi_plus_mobile_open_type_setting' => 'no',
		'sfsi_plus_icons_mobile_ClickPageOpen' => 'no',
		'sfsi_plus_counts_without_slash' => 'no',
		'sfsi_plus_hook_priority_value ' => 20,
		'sfsi_plus_loadjquery'				=> 'no',
		'sfsi_plus_bitly_v4'  => 'no',
	);
	add_option('sfsi_premium_section5_options',  serialize($options5));

	/* post options */
	$options6 = array(
		'sfsi_plus_show_Onposts'		=> 'no',
		'sfsi_plus_show_Onbottom'		=> 'no',
		'sfsi_plus_icons_postPositon'	=> 'source',
		'sfsi_plus_icons_alignment'		=> 'center-right',
		'sfsi_plus_rss_countsDisplay'	=> 'no',
		'sfsi_plus_textBefor_icons'		=> __( 'Please follow and like us:', 'ultimate-social-media-plus' ),
		'sfsi_plus_icons_DisplayCounts'	=> 'no'
	);
	add_option('sfsi_premium_section6_options',  serialize($options6));

	/* icons pop options */

	$option7 = maybe_unserialize(get_option('sfsi_premium_section7_options', false));

	if (isset($option7) && !empty($option7)) {

		if (!isset($option7['sfsi_plus_Show_popupOn_somepages_blogpage'])) {
			$option7['sfsi_plus_Show_popupOn_somepages_blogpage'] = '';
		}
		if (!isset($option7['sfsi_plus_Show_popupOn_somepages_selectedpage'])) {
			$option7['sfsi_plus_Show_popupOn_somepages_selectedpage'] = '';
		}

		if (!isset($option7['sfsi_plus_Hide_popupOnScroll'])) {
			$option7['sfsi_plus_Hide_popupOnScroll'] = 'yes';
		}
		if (!isset($option7['sfsi_plus_Hide_popupOn_OutsideClick'])) {
			$option7['sfsi_plus_Hide_popupOn_OutsideClick'] = 'no';
		}

		if (!isset($option7['sfsi_plus_popup_fontStyle'])) {
			$option7['sfsi_plus_popup_fontStyle'] = 'normal';
		}
	} else {

		$options7 = array(
			'sfsi_plus_show_popup'				=> 'no',
			'sfsi_plus_popup_text'				=> __( 'Enjoy this blog? Please spread the word :)', 'ultimate-social-media-plus' ),
			'sfsi_plus_popup_background_color'	=> '#eff7f7',
			'sfsi_plus_popup_border_color'		=> '#f3faf2',
			'sfsi_plus_popup_border_thickness'	=> '1',
			'sfsi_plus_popup_border_shadow'		=> 'yes',
			'sfsi_plus_popup_font'				=> 'Helvetica,Arial,sans-serif',
			'sfsi_plus_popup_fontSize'			=> '30',
			'sfsi_plus_popup_fontStyle'			=> 'normal',
			'sfsi_plus_popup_fontColor'			=> '#000000',
			'sfsi_plus_Show_popupOn'			=> 'none',
			'sfsi_plus_Show_popupOn_PageIDs'	=> '',

			'sfsi_plus_Show_popupOn_somepages_blogpage' => '',
			'sfsi_plus_Show_popupOn_somepages_selectedpage' => '',

			'sfsi_plus_Hide_popupOnScroll'		  => 'yes',
			'sfsi_plus_Hide_popupOn_OutsideClick' => 'no',

			'sfsi_plus_Shown_pop'				=> array('ETscroll'),
			'sfsi_plus_Shown_popupOnceTime'		=> '',
			'sfsi_plus_Shown_popuplimitPerUserTime' => '',
			'sfsi_plus_popup_limit'				=> 'no',
			'sfsi_plus_popup_limit_count'		=> '',
			'sfsi_plus_popup_limit_type'		=> '',
			'sfsi_plus_popup_type_iconsOrForm'  => 'icons'
		);
		add_option('sfsi_premium_section7_options',  serialize($options7));
	}

	/*options that are added in the third question*/
	if (get_option('sfsi_premium_section4_options', false))
		$option4 =  maybe_unserialize(get_option('sfsi_premium_section4_options', false));
	if (get_option('sfsi_premium_section5_options', false))
		$option5 =  maybe_unserialize(get_option('sfsi_premium_section5_options', false));
	if (get_option('sfsi_premium_section6_options', false))
		$option6 =  maybe_unserialize(get_option('sfsi_premium_section6_options', false));
	$sfsi_plus_responsive_icons_default = array(
		"default_icons" => array(
			"facebook" => array("active" => "yes", "text" => __( "Share on Facebook", "usm-premium-icons" ), "url" => ""),
			"Twitter" => array("active" => "yes", "text" => __( "Tweet", "usm-premium-icons" ), "url" => ""),
			"Follow" => array("active" => "yes", "text" => __( "Follow us", "usm-premium-icons" ), "url" => ""),
			"pinterest" => array("active" => "no", "text" => __( "Save", "usm-premium-icons" ), "url" => ""),
			"Linkedin" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"Whatsapp" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"vk" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"Odnoklassniki" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"Telegram" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"Weibo" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"QQ2" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
			"xing" => array("active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => ""),
		),
		"custom_icons" => array(),
		"settings" => array(
			"icon_size" => "Medium",
			"icon_width_type" => "Fully responsive",
			"icon_width_size" => 240,
			"edge_type" => "Round",
			"edge_radius" => 5,
			"style" => "Gradient",
			"margin" => 10,
			"text_align" => "Centered",
			"show_count" => "no",
			"responsive_mobile_icons" => "yes",
			"counter_color" => "#aaaaaa",
			"counter_bg_color" => "#fff",
			"share_count_text" => "SHARES",
			"margin_above" => 10,
			"margin_below" => 10
		)
	);

	$options8 = array(
		'sfsi_plus_show_via_widget'				=> 'no',
		'sfsi_plus_float_on_page'				=> isset($option5['sfsi_plus_icons_float']) ? $option5['sfsi_plus_icons_float'] : 'no',
		'sfsi_plus_float_page_position'			=> isset($option5['sfsi_plus_icons_floatPosition']) ? $option5['sfsi_plus_icons_floatPosition'] : 'center-right',
		'sfsi_plus_make_icon'					=> 'float',
		'sfsi_plus_icons_floatMargin_top'		=> '',
		'sfsi_plus_icons_floatMargin_bottom'	=> '',
		'sfsi_plus_icons_floatMargin_left'		=> '',
		'sfsi_plus_icons_floatMargin_right'		=> '',

		'sfsi_plus_mobile_widget'				=> 'no',
		'sfsi_plus_mobile_float'				=> 'no',
		'sfsi_plus_mobile_shortcode'			=> 'no',
		'sfsi_plus_mobile_beforeafterposts'		=> 'no',

		'sfsi_plus_widget_horizontal_verical_Alignment'			 => 'Horizontal',
		'sfsi_plus_float_horizontal_verical_Alignment'			 => 'Horizontal',
		'sfsi_plus_shortcode_horizontal_verical_Alignment'		 => 'Horizontal',
		'sfsi_plus_beforeafterposts_horizontal_verical_Alignment' => 'Horizontal',

		'sfsi_plus_widget_mobile_horizontal_verical_Alignment'			 => 'Horizontal',
		'sfsi_plus_float_mobile_horizontal_verical_Alignment'			 => 'Horizontal',
		'sfsi_plus_shortcode_mobile_horizontal_verical_Alignment'		 => 'Horizontal',
		'sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment' => 'Horizontal',

		'sfsi_plus_float_page_mobileposition'		=> isset($option5['sfsi_plus_icons_floatPosition']) ? $option5['sfsi_plus_icons_floatPosition'] : 'center-right',
		'sfsi_plus_make_mobileicon'					=> '',
		'sfsi_plus_icons_floatMargin_mobiletop'		=> '',
		'sfsi_plus_icons_floatMargin_mobilebottom'	=> '',
		'sfsi_plus_icons_floatMargin_mobileleft'	=> '',
		'sfsi_plus_icons_floatMargin_mobileright'	=> '',

		'sfsi_plus_post_icons_size'				=> isset($option5['sfsi_plus_post_icons_size']) ? $option5['sfsi_plus_post_icons_size'] : '40',
		'sfsi_plus_post_icons_spacing'			=> isset($option5['sfsi_plus_post_icons_spacing']) ? $option5['sfsi_plus_post_icons_spacing'] : '5',
		'sfsi_plus_post_icons_vertical_spacing'	=> 5,

		'sfsi_plus_show_Onposts'				=> $option6['sfsi_plus_show_Onposts'],
		'sfsi_plus_textBefor_icons'				=> $option6['sfsi_plus_textBefor_icons'],
		'sfsi_plus_textBefor_icons_font_size'	=> '20',
		'sfsi_plus_textBefor_icons_fontcolor'	=> '#000000',
		'sfsi_plus_textBefor_icons_font_type'	=> 'normal',
		'sfsi_plus_textBefor_icons_font'		=> 'inherit',

		'sfsi_plus_icons_alignment'				=> $option6['sfsi_plus_icons_alignment'],
		'sfsi_plus_icons_DisplayCounts'			=> $option6['sfsi_plus_icons_DisplayCounts'],

		'sfsi_plus_place_item_manually'			=> 'no',
		'sfsi_plus_shortcode_horizontal_verical_Alignment' => 'Horizontal',

		'sfsi_plus_place_rectangle_icons_item_manually'	=> 'no',

		'sfsi_plus_show_item_onposts'			=> $option6['sfsi_plus_show_Onposts'],
		'sfsi_plus_display_button_type'			=> 'standard_buttons',
		'sfsi_plus_display_before_posts'		=> 'no',
		'sfsi_plus_display_after_posts'			=> $option6['sfsi_plus_show_Onposts'],
		'sfsi_plus_display_on_postspage'		=> 'no',
		'sfsi_plus_display_on_homepage'			=> 'no',
		'sfsi_plus_display_before_blogposts'	=> 'no',
		'sfsi_plus_display_after_blogposts'		=> 'no',
		'sfsi_plus_display_before_pageposts'	=> 'no',
		'sfsi_plus_display_after_pageposts'		=> 'no',
		'sfsi_plus_rectsub'						=> 'yes',
		'sfsi_plus_rectfb'						=> 'yes',
		'sfsi_plus_recttwtr'					=> 'yes',
		'sfsi_plus_rectpinit'					=> 'yes',
		'sfsi_plus_rectfbshare'					=> 'yes',
		'sfsi_plus_rectlinkedin'				=> 'yes',
		'sfsi_plus_rectreddit'					=> 'yes',

		'sfsi_plus_widget_show_on_desktop'		=> 'yes',
		'sfsi_plus_widget_show_on_mobile'		=> 'yes',

		'sfsi_plus_float_show_on_desktop'		=> 'yes',
		'sfsi_plus_float_show_on_mobile'		=> 'yes',

		'sfsi_plus_shortcode_show_on_desktop'	=> 'yes',
		'sfsi_plus_shortcode_show_on_mobile'	=> 'yes',

		'sfsi_plus_beforeafterposts_show_on_desktop' => 'yes',
		'sfsi_plus_beforeafterposts_show_on_mobile'	 => 'yes',

		'sfsi_plus_rectangle_icons_shortcode_show_on_desktop' => 'yes',
		'sfsi_plus_rectangle_icons_shortcode_show_on_mobile'  => 'yes',

		'sfsi_plus_responsive_icons_after_pages'	 => 'no',
		'sfsi_plus_responsive_icons_before_pages'	 => 'no',
		'sfsi_plus_responsive_icons_before_post_on_taxonomy'	 => 'no',
		'sfsi_plus_responsive_icons_before_post_on_taxonomy'	 => 'no',
		'sfsi_plus_responsive_icons_after_post'	 => 'no',
		'sfsi_plus_responsive_icons_before_post'	 => 'no',
		'sfsi_plus_responsive_icons_show_on_desktop' => 'yes',
		'sfsi_plus_responsive_icons_show_on_mobile'  => 'yes',

		'sfsi_plus_choose_post_types_responsive'=> serialize(array()),
		'sfsi_plus_choose_post_types'			=> serialize(array()),
		'sfsi_plus_taxonomies_for_icons'        => serialize(array()), // Taxonomy selection field added in Que3 in VERSION 3.1

		'sfsi_plus_icons_rules' 				=> 0,

		'sfsi_plus_exclude_home'				=> 'no',
		'sfsi_plus_exclude_page'				=> 'no',
		'sfsi_plus_exclude_post'				=> 'no',
		'sfsi_plus_exclude_tag'					=> 'no',
		'sfsi_plus_exclude_category'			=> 'no',
		'sfsi_plus_exclude_date_archive'		=> 'no',
		'sfsi_plus_exclude_author_archive'		=> 'no',
		'sfsi_plus_exclude_search'				=> 'no',
		'sfsi_plus_exclude_url'					=> 'no',
		'sfsi_plus_urlKeywords'					=> array(),
		'sfsi_plus_switch_exclude_custom_post_types' => 'no',
		'sfsi_plus_list_exclude_custom_post_types' => serialize(array()),
		'sfsi_plus_switch_exclude_taxonomies'	=> 'no',
		'sfsi_plus_list_exclude_taxonomies'		=> serialize(array()),

		'sfsi_plus_include_home'				=> 'no',
		'sfsi_plus_include_page'				=> 'no',
		'sfsi_plus_include_post'				=> 'no',
		'sfsi_plus_include_tag'					=> 'no',
		'sfsi_plus_include_category'			=> 'no',
		'sfsi_plus_include_date_archive'		=> 'no',
		'sfsi_plus_include_author_archive'		=> 'no',
		'sfsi_plus_include_search'				=> 'no',
		'sfsi_plus_include_url'					=> 'no',
		'sfsi_plus_include_urlKeywords'			=> array(),
		'sfsi_plus_switch_include_custom_post_types' => 'no',
		'sfsi_plus_list_include_custom_post_types' => serialize(array()),
		'sfsi_plus_switch_include_taxonomies'	=> 'no',
		'sfsi_plus_list_include_taxonomies'		=> serialize(array()),

		'sfsi_plus_marginAbove_postIcon'		=> '',
		'sfsi_plus_marginBelow_postIcon'		=> '',
		'sfsi_plus_display_on_all_icons'		=>	"no",
		'sfsi_plus_display_rule_round_icon_widget'	=>	"yes",
		'sfsi_plus_display_rule_round_icon_define_location'	=>	"yes",
		'sfsi_plus_display_rule_round_icon_shortcode'	=>	"yes",
		'sfsi_plus_display_rule_round_icon_before_after_post'	=>	"no",
		'sfsi_plus_display_rule_rect_icon_before_after_post'	=>	"no",
		'sfsi_plus_display_rule_rect_icon_shortcode'	=>	"no",
		'sfsi_plus_display_before_woocomerce_desc' => "no",
		'sfsi_plus_display_after_woocomerce_desc' => "no",
		'sfsi_plus_display_rule_responsive_icon_before_after_post'	=>	"no",
		'sfsi_plus_display_rule_responsive_icon_shortcode'	=>	"no",

		// pintrest hover icon

		'sfsi_plus_icon_hover_show_pinterest' => "no",
		'sfsi_plus_icon_hover_type'			=> "square",
		'sfsi_plus_icon_hover_language'		=> "en_US",
		'sfsi_plus_icon_hover_placement'	=> "top-left",
		'sfsi_plus_icon_hover_width'		=> "20",
		'sfsi_plus_icon_hover_height'		=> "20",
		'sfsi_plus_icon_hover_desktop'		=> "yes",
		'sfsi_plus_icon_hover_mobile'		=> "yes",
		'sfsi_plus_icon_hover_mobile'		=> "yes",
		'sfsi_plus_icon_hover_on_all_pages'	=> "yes",

		'sfsi_plus_icon_hover_exclude_home'	=> 'no',
		'sfsi_plus_icon_hover_exclude_page'	=> 'no',
		'sfsi_plus_icon_hover_exclude_post'	=> 'no',
		'sfsi_plus_icon_hover_exclude_tag'	=> 'no',
		'sfsi_plus_icon_hover_exclude_category'	=> 'no',
		'sfsi_plus_icon_hover_exclude_date_archive'	=> 'no',
		'sfsi_plus_icon_hover_exclude_author_archive'	=> 'no',
		'sfsi_plus_icon_hover_exclude_search' => 'no',
		'sfsi_plus_icon_hover_exclude_url'	=> 'no',
		'sfsi_plus_icon_hover_urlKeywords'	=> array(),
		'sfsi_plus_icon_hover_include_urlKeywords'	=> array(),
		'sfsi_plus_icon_hover_exclude_urlKeywords'	=> array(),
		'sfsi_plus_icon_hover_switch_exclude_custom_post_types' => 'no',
		'sfsi_plus_icon_hover_list_exclude_custom_post_types' => serialize(array()),
		'sfsi_plus_icon_hover_switch_exclude_taxonomies' => 'no',
		'sfsi_plus_icon_hover_list_exclude_taxonomies'	=> serialize(array()),

		'sfsi_plus_icon_hover_include_home'	=> 'no',
		'sfsi_plus_icon_hover_include_page'	=> 'no',
		'sfsi_plus_icon_hover_include_post'	=> 'no',
		'sfsi_plus_icon_hover_include_tag'	=> 'no',
		'sfsi_plus_icon_hover_include_category'	=> 'no',
		'sfsi_plus_icon_hover_include_date_archive'	=> 'no',
		'sfsi_plus_icon_hover_include_author_archive'	=> 'no',
		'sfsi_plus_icon_hover_include_search' => 'no',
		'sfsi_plus_icon_hover_include_url'	=> 'no',
		'sfsi_plus_icon_hover_include_urlKeywords'	=> array(),
		'sfsi_plus_icon_hover_switch_include_custom_post_types' => 'no',
		'sfsi_plus_icon_hover_list_include_custom_post_types' => serialize(array()),
		'sfsi_plus_icon_hover_switch_include_taxonomies' => 'no',
		'sfsi_plus_icon_hover_list_include_taxonomies'	=> serialize(array()),
		'sfsi_premium_woocomerce_before_icons' 			=> 'no',
		'sfsi_premium_woocomerce_before_icons'			=> 'no',
		'sfsi_plus_responsive_icons'					=> $sfsi_plus_responsive_icons_default,
		'sfsi_plus_post_mobile_icons_size'				=> isset($option5['sfsi_plus_post_mobile_icons_size']) ? $option5['sfsi_plus_post_mobile_icons_size'] : '40',
		'sfsi_plus_post_mobile_icons_spacing'			=> isset($option5['sfsi_plus_post_mobile_icons_spacing']) ? $option5['sfsi_plus_post_mobile_icons_spacing'] : '5',
		'sfsi_plus_post_mobile_icons_vertical_spacing'	=> isset($option5['sfsi_plus_post_mobile_icons_vertical_spacing']) ? $option5['sfsi_plus_post_mobile_icons_vertical_spacing'] : '5',
		'sfsi_plus_mobile_size_space_beforeafterposts'	=> 'no',
		'sfsi_plus_icon_hover_custom_icon_url'			=> '',
		'sfsi_plus_sticky_bar'				=> 'no',
		'sfsi_plus_sticky_bar_desktop_width'	=> 782,
		'sfsi_plus_sticky_bar_desktop_placement' => 'left',
		'sfsi_plus_sticky_bar_display_position'	=> 0,
		'sfsi_plus_sticky_bar_desktop_placement_direction' => 'up',
		'sfsi_plus_sticky_bar_mobile_width'	=> 782,
		'sfsi_plus_sticky_bar_mobile_placement' => 'up',
		'sfsi_plus_sticky_bar_mobile'			=> 'no',
		'sfsi_plus_sticky_bar_counts'			=> 'no',
		'sfsi_plus_sticky_bar_bg_color'			=> '#000000',
		'sfsi_plus_sticky_bar_color'			=> '#aaaaaa',
		'sfsi_plus_sticky_bar_share_count_text'	=> 'SHARES',
	    'sfsi_plus_place_item_gutenberg'=>'no'
	);
	add_option('sfsi_premium_section8_options',  serialize($options8));

	$options9 = array(
		'sfsi_plus_form_adjustment'			=> 'yes',
		'sfsi_plus_form_height'				=> '180',
		'sfsi_plus_form_width' 				=> '230',
		'sfsi_plus_form_border'				=> 'yes',
		'sfsi_plus_form_border_thickness'	=> '1',
		'sfsi_plus_form_border_color'		=> '#b5b5b5',
		'sfsi_plus_form_background'			=> '#ffffff',

		'sfsi_plus_form_heading_text'		=> __( 'Get new posts by email:', 'ultimate-social-media-plus' ),
		'sfsi_plus_form_heading_font'		=> 'Helvetica,Arial,sans-serif',
		'sfsi_plus_form_heading_fontstyle'	=> 'bold',
		'sfsi_plus_form_heading_fontcolor'	=> '#000000',
		'sfsi_plus_form_heading_fontsize'	=> '16',
		'sfsi_plus_form_heading_fontalign'	=> 'center',

		'sfsi_plus_form_field_text'			=> __( 'Enter your email', 'ultimate-social-media-plus' ),
		'sfsi_plus_form_field_font'			=> 'Helvetica,Arial,sans-serif',
		'sfsi_plus_form_field_fontstyle'	=> 'normal',
		'sfsi_plus_form_field_fontcolor'	=> '#000000',
		'sfsi_plus_form_field_fontsize'		=> '14',
		'sfsi_plus_form_field_fontalign'	=> 'center',

		'sfsi_plus_form_button_text'		=> __( 'Subscribe', 'ultimate-social-media-plus' ),
		'sfsi_plus_form_button_font'		=> 'Helvetica,Arial,sans-serif',
		'sfsi_plus_form_button_fontstyle'	=> 'bold',
		'sfsi_plus_form_button_fontcolor'	=> '#000000',
		'sfsi_plus_form_button_fontsize'	=> '16',
		'sfsi_plus_form_button_fontalign'	=> 'center',
		'sfsi_plus_form_button_background'	=> '#dedede',

		'sfsi_plus_form_privacynotice_text'		 => __( 'We will treat your data confidentially', 'ultimate-social-media-plus' ),
		'sfsi_plus_form_privacynotice_font'		 => 'Helvetica,Arial,sans-serif',
		'sfsi_plus_form_privacynotice_fontcolor' => '#000000',
		'sfsi_plus_form_privacynotice_fontsize'	 => '16',
		'sfsi_plus_form_privacynotice_fontalign' => 'center'
	);

	add_option('sfsi_premium_section9_options',  serialize($options9));

	/*Some additional option added*/
	if (is_object($sffeeds) && isset($sffeeds->feed_id) && isset($sffeeds->redirect_url)) {
		update_option('sfsi_premium_feed_id', sanitize_text_field($sffeeds->feed_id));
		update_option('sfsi_premium_redirect_url', sanitize_text_field($sffeeds->redirect_url));
	}

	add_option('sfsi_premium_installDate', date('Y-m-d h:i:s'));
	add_option('sfsi_premium_RatingDiv', 'no');
	add_option('sfsi_premium_footer_sec', 'no');
	add_option('sfsi_premium_bitly_options', '');
	add_option('sfsi_plus_bitly_v4', 'no');


	update_option('sfsi_premium_activate', 1);

	/*Changes in option 2*/
	if ( is_object( $sffeeds ) && isset( $sffeeds->redirect_url ) ) {
		$get_option2 = maybe_unserialize(get_option('sfsi_premium_section2_options', false));
		$get_option2['sfsi_plus_email_url'] = $sffeeds->redirect_url;
		update_option('sfsi_premium_section2_options', serialize($get_option2));
	}

	/*Activation Setup for (specificfeed)*/
	if ( is_object( $sffeeds ) && isset( $sffeeds->feed_id ) ) {
		sfsi_plus_setUpfeeds( $sffeeds->feed_id );
		sfsi_plus_updateFeedPing( 'N', $sffeeds->feed_id );
	}

	/*Extra important options*/
	$sfsi_premium_instagram_sf_count = array(
		"date_sf" => strtotime(date("Y-m-d")),
		"date_instagram" => strtotime(date("Y-m-d")),
		"sfsi_plus_sf_count" => "",
		"sfsi_plus_instagram_count" => ""
	);
	add_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));

	$sfsi_premium_youtube_count = array(
		"date" => strtotime(date("Y-m-d")),
		"sfsi_plus_count" => "",
	);
	add_option('sfsi_premium_youtube_count',  serialize($sfsi_premium_youtube_count));

	$sfsi_premium_cron = array(
		"daily" => (time()-86400),
		"hourly" => (time()-3600)
	);
	add_option('sfsi_premium_cron',  serialize($sfsi_premium_cron));

	/** Url shortner data table **/
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . "sfsi_shorten_links";

	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
		  id bigint(9) NOT NULL AUTO_INCREMENT,
		  post_id bigint(9) NOT NULL,
		  shorteningMethod varchar(30) NOT NULL,
		  longUrl text NOT NULL,
		  shortenUrl varchar(100) DEFAULT '' NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	//update_option("sfsi_premium_install_after_13.3", true);
	// Create job queue table for handling facebook count caching
	$sfsi_job_queue = sfsiJobQueue::getInstance();

	$jobQueueInstalled = get_option('sfsi_premium_job_queue_installed', false);

	if (false == $jobQueueInstalled) {
		$sfsi_job_queue->install_job_queue();
	}

	// New
	update_option("sfsi_premium_was_installed_before", PLUGIN_CURRENT_VERSION);
}
/* end function  */
/* deactivate plugin */
function sfsi_plus_deactivate_plugin()
{
	global $wpdb;
	sfsi_plus_updateFeedPing('Y', sanitize_text_field(get_option('sfsi_premium_feed_id')));
}
/* end function  */
function sfsi_plus_updateFeedPing($status, $feed_id)
{
	// $curl = curl_init();  

	$curl = wp_remote_post('https://api.follow.it/wordpress/pingfeed', array(
		'blocking' => true,
		'timeout'     => 30,
		'sslverify' => true,
		// 'CURLOPT_URL' => 'https://api.follow.it/wordpress/pingfeed',
		'user-agent' => 'sf rss request',
		'body' => array(
			'feed_id' => $feed_id,
			'status' => $status
		)
	));
	// Send the request & save response to $resp
	// $resp = curl_exec($curl);
	// $resp=json_decode($resp);
	// curl_close($curl);
}

/* unistall plugin function */
function sfsi_plus_Unistall_plugin()
{
	global $wpdb;
	/* Delete option for which icons to display */
	delete_option('sfsi_premium_section1_options');
	delete_option('sfsi_premium_section2_options');
	delete_option('sfsi_premium_section3_options');
	delete_option('sfsi_premium_section4_options');
	delete_option('sfsi_premium_section5_options');
	delete_option('sfsi_premium_section6_options');
	delete_option('sfsi_premium_section7_options');
	delete_option('sfsi_premium_section8_options');
	delete_option('sfsi_premium_section9_options');

	delete_option('sfsi_premium_feed_id');
	delete_option('sfsi_premium_redirect_url');
	delete_option('sfsi_premium_footer_sec');
	delete_option('sfsi_premium_activate');
	delete_option('sfsi_premium_bitly_options');
	delete_option('sfsi_plus_bitly_v4');
	delete_option('sfsi_premium_pluginVersion');
	delete_option('sfsi_premium_verificatiom_code');
	delete_option('sfsi_premium_curlErrorNotices');
	delete_option('sfsi_premium_curlErrorMessage');

	delete_option('sfsi_active_license_api_name');
	delete_option(ULTIMATELYSOCIAL_LICENSING . '_license_key');
	delete_option(ULTIMATELYSOCIAL_LICENSING . '_license_status');
	delete_option(ULTIMATELYSOCIAL_LICENSING . '_license_activated');
	delete_option(ULTIMATELYSOCIAL_LICENSING . '_license_expiry');

	delete_option(SELLCODES_LICENSING . '_license_key');
	delete_option(SELLCODES_LICENSING . '_license_status');
	delete_option(SELLCODES_LICENSING . '_license_activated');
	delete_option(SELLCODES_LICENSING . '_license_expiry');

	delete_option('adding_plustags');
	delete_option('sfsi_premium_RatingDiv');
	delete_option('sfsi_premium_instagram_sf_count');
	delete_option('sfsi_premium_installDate');
	delete_option('sfsi_premium_serverphpVersionnotification');

	delete_option('widget_sfsi-plus-widget');
	delete_option('widget_sfsiplus_subscriber_widget');

	/* Remove all images data from db saved for custom images for icons in Questions 4. We are not deleting actual files */
	delete_option('plus_rss_skin');
	delete_option('plus_email_skin');
	delete_option('plus_facebook_skin');
	delete_option('plus_google_skin');
	delete_option('plus_twitter_skin');
	delete_option('plus_share_skin');
	delete_option('plus_youtube_skin');
	delete_option('plus_pintrest_skin');
	delete_option('plus_linkedin_skin');
	delete_option('plus_instagram_skin');
	delete_option('plus_houzz_skin');
	delete_option('plus_snapchat_skin');
	delete_option('plus_whatsapp_skin');
	delete_option('plus_skype_skin');
	delete_option('plus_vimeo_skin');
	delete_option('plus_soundcloud_skin');
	delete_option('plus_yummly_skin');
	delete_option('plus_flickr_skin');
	delete_option('plus_reddit_skin');
	delete_option('plus_tumblr_skin');

	delete_option('plus_fbmessenger_skin');
	delete_option('plus_gab_skin');
	delete_option('plus_mix_skin');
	delete_option('plus_ok_skin');
	delete_option('plus_telegram_skin');
	delete_option('plus_vk_skin');
	delete_option('plus_wechat_skin');
	delete_option('plus_weibo_skin');
	delete_option('plus_xing_skin');
	delete_option('plus_copylink_skin');

	// Removing data saved for facebook count caching
	delete_option('sfsi_premium_fb_batch_api_last_call_log');

	// Remove data saved for twitter followers caching 
	delete_option('sfsi_premium_tw_api_last_call_log');
	delete_option('sfsi_premium_twitter_followers_count');

	delete_option('sfsi_premium_icon_counts');
	delete_option('sfsi_premium_sticky_icon_counts');


	delete_option('sfsi_premium_cache_debug_options');
	delete_option('sfsi_premium_youtube_count');
	delete_option('sfsi_premium_cron');
	delete_option('sfsi_plus_banner_popups');


	

	/***** Remove table created for url shortner ******/
	$table_name = $wpdb->prefix . 'sfsi_shorten_links';
	$wpdb->query("DROP TABLE IF EXISTS $table_name");

	$sfsiJobQueue = sfsiJobQueue::getInstance();
	$sfsiJobQueue->uninstall_job_queue();
}
/* end function */
/* check CUrl */
function sfsi_plus_curl_enable_notice()
{

	if (!function_exists('curl_init')) {
		echo '<div class="error"><p> ' . __('Error: It seems that CURL is disabled on your server. Please contact your server administrator to install / enable CURL.', 'ultimate-social-media-plus') . '</p></div>';
		die;
	}
}

/* add admin menus */
function sfsi_plus_admin_menu()
{

	$license_api_name = (false === get_option('sfsi_active_license_api_name')) ? SELLCODES_LICENSING : get_option('sfsi_active_license_api_name');
	$license = trim(get_option($license_api_name . '_license_key'));
	$status  = trim(get_option($license_api_name . '_license_status'));

	if (!empty($license) && "valid" == strtolower($status)) {
		add_menu_page(
			__( 'USM Premium', 'ultimate-social-media-plus' ),
			__( 'USM Premium', 'ultimate-social-media-plus' ),
			'manage_options',
			'sfsi-plus-options',
			'sfsi_plus_options_page',
			plugins_url('images/premium-logo-small.png', dirname(__FILE__))
		);

		//add_submenu_page( 'sfsi-plus-options', "Import Setting", "Import Setting", 'administrator', "sfsi-import-setting", 'sfsi_plus_import_setting');
	} else {
		add_menu_page(
			__( 'USM Premium', 'ultimate-social-media-plus' ),
			__( 'USM Premium', 'ultimate-social-media-plus' ),
			'manage_options',
			'sfsi-plus-options',
			'sfsi_plus_about_page',
			plugins_url('images/premium-logo-small.png', dirname(__FILE__))
		);
	}
}
function sfsi_plus_options_page()
{
	include SFSI_PLUS_DOCROOT . '/views/sfsi_options_view.php';
} /* end function  */

function sfsi_plus_about_page()
{
	include SFSI_PLUS_DOCROOT . '/views/sfsi_aboutus.php';
} /* end function  */

if (is_admin()) {
	add_action('admin_menu', 'sfsi_plus_admin_menu');
}

/* fetch rss url from follow.it */
function SFSI_PLUS_getFeedUrl()
{
	// $curl = curl_init();  

	$curl = wp_remote_post('https://api.follow.it/wordpress/plugin_setup', array(
		'blocking' => true,
		'timeout' => 30,
		// 'CURLOPT_URL' => 'https://api.follow.it/wordpress/plugin_setup',
		'user-agent' => 'sf rss request',
		'body' => array(
			'web_url'	=> get_bloginfo('url'),
			'feed_url'	=> sfsi_plus_get_bloginfo('rss2_url'),
			'email'		=> '',
			'subscriber_type' => 'PWP'
		)
	));
	// Send the request & save response to $resp
	// $resp = curl_exec($curl);
	if (is_wp_error($curl)) { } else {
		$resp = json_decode($curl['body']);
		//$feed_url = stripslashes_deep($resp->redirect_url);
		return $resp;
		exit;
	}
}
/* fetch rss url from follow.it on */
function SFSI_PLUS_updateFeedUrl()
{
	// $curl = curl_init();
	$curl = wp_remote_post('https://api.follow.it/wordpress/updateFeedPlugin', array(
		'blocking' => true,
		'timeout' => 30,
		// 'CURLOPT_URL' => 'https://api.follow.it/wordpress/updateFeedPlugin',
		'user-agent' => 'sf rss request',
		'body' => array(
			'feed_id' 	=> sanitize_text_field(get_option('sfsi_premium_feed_id')),
			'web_url' 	=> get_bloginfo('url'),
			'feed_url' 	=> sfsi_plus_get_bloginfo('rss2_url'),
			'email'		=> ''
		)
	));
	// Send the request & save response to $resp
	// $resp = curl_exec($curl);
	// $resp = json_decode($resp);
	// curl_close($curl);
	if (is_wp_error($curl)) { } else {
		$resp = json_decode($curl['body']);
		$feed_url = stripslashes_deep($resp->redirect_url);
		return $resp;
		exit;
	}
}
/* add sf tags */
function sfsi_plus_setUpfeeds($feed_id)
{
	// $curl = curl_init();  
	$curl = wp_remote_get('https://api.follow.it/rssegtcrons/download_rssmorefeed_data_single/' . $feed_id . "/Y", array(
		'blocking' => true,
		'timeout' => 30,
		// 'CURLOPT_URL' => 'https://api.follow.it/rssegtcrons/download_rssmorefeed_data_single/'.$feed_id."/Y",
		'user-agent' => 'sf rss request',
	));
	// $resp = curl_exec($curl);
	// curl_close($curl);	

}
/* admin notice if wp_head is missing in active theme */
function sfsi_plus_check_wp_head()
{

	$template_directory = get_template_directory();
	$header = $template_directory . '/header.php';

	if (is_file($header)) {

		$search_header = "wp_head";
		$file_lines = @file($header);
		$foind_header = 0;
		foreach ($file_lines as $line) {
			$searchCount = substr_count($line, $search_header);
			if ($searchCount > 0) {
				return true;
			}
		}
		$path = pathinfo($_SERVER['REQUEST_URI']);

		if ($path['basename'] == "themes.php" || $path['basename'] == "theme-editor.php" || $path['basename'] == "admin.php?page=sfsi-plus-options") {
			$currentTheme = wp_get_theme();

			if (!is_child_theme() && isset($currentTheme) && !empty($currentTheme) && $currentTheme->get('Name') != "Customizr") {

				echo "<div class='error'><p>" . __('Error : Please fix your theme to make plugins work correctly. Go to the Theme Editor and insert the following string:', 'ultimate-social-media-plus') . " &lt;?php wp_head(); ?&gt; " . __('Please enter it just before the following line of your header.php file:', 'ultimate-social-media-plus') . " &lt;/head&gt; " . __('Go to your theme editor: ', 'ultimate-social-media-plus') . "<a href='theme-editor.php'>" . __('click here', 'ultimate-social-media-plus') . "</a>.</p></div>";
			}
		}
	}
}
/* admin notice if wp_footer is missing in active theme */
function sfsi_plus_check_wp_footer()
{
	$template_directory = get_template_directory();
	$footer = $template_directory . '/footer.php';

	if (is_file($footer)) {
		$search_string = "wp_footer";
		$file_lines = @file($footer);

		foreach ($file_lines as $line) {
			$searchCount = substr_count($line, $search_string);
			if ($searchCount > 0) {
				return true;
			}
		}

		$path = pathinfo( $_SERVER['REQUEST_URI'] );

		if ($path['basename'] == "themes.php" || $path['basename'] == "theme-editor.php" || $path['basename'] == "admin.php?page=sfsi-plus-options") {
			if (!is_child_theme()) {
				echo "<div class='error'><p>" .	__("Error: Please fix your theme to make plugins work correctly. Go to the Theme Editor and insert the following string as the first line of your theme's footer.php file: ", 'ultimate-social-media-plus') . " &lt;?php wp_footer(); ?&gt; " . __("Go to your theme editor: ", 'ultimate-social-media-plus') . "<a href='theme-editor.php'>" . __('click here', 'ultimate-social-media-plus') . "</a>.</p></div>";
			}
		}
	}
}
/* admin notice for first time installation */
function sfsi_plus_activation_msg() {

	if ( isset( $_GET['page'] ) && $_GET['page'] !== "sfsi-plus-options" && get_option( 'sfsi_premium_activate', false ) == 1 ) {
		echo "<div class='updated'><p>" . __("Thank you for installing the Ultimate Social Media Premium plugin. Please go to the plugin's settings page to configure it: ", 'ultimate-social-media-plus') . "<b><a href='admin.php?page=sfsi-plus-options'>" . __("Click here", 'ultimate-social-media-plus') . "</a></b></p></div>";
		update_option( 'sfsi_premium_activate', 0 );
	}
}
/* admin notice for first time installation */
function sfsi_plus_rating_msg()
{
	global $wp_version;
	$install_date = get_option('sfsi_premium_installDate');
	$display_date = date('Y-m-d h:i:s');
	$datetime1 = new DateTime($install_date);
	$datetime2 = new DateTime($display_date);
	$diff_inrval = round(($datetime2->format('U') - $datetime1->format('U')) / (60 * 60 * 24));

	if ($diff_inrval >= 30 && get_option('sfsi_premium_RatingDiv') == "no") {
		$nonce = wp_create_nonce('plushideRating');
		$notification = '
			<div class="sfwp_fivestar updated">
				<p>' . __('We noticed you\'ve been using the Ultimate Social Media Premium Plugin for more than 30 days. If you\'re happy with it, could you please do us a BIG favor and give it a 5-star rating on Wordpress?', 'ultimate-social-media-plus') . '</p>
				<ul class="sfwp_fivestar_ul">
					<li><a href="https://wordpress.org/support/view/plugin-reviews/ultimate-social-media-plus" target="_new" title="' . __( 'Ok, you deserved it', 'ultimate-social-media-plus' ) . '">' . __( 'Ok, you deserved it', 'ultimate-social-media-plus' ) . '</a></li>
					<li><a href="javascript:void(0);" class="sfsiHideRating" title="'.__( 'I already did', 'ultimate-social-media-plus' ).'">' . __( 'I already did', 'ultimate-social-media-plus' ) . '</a></li>
					<li><a href="javascript:void(0);" class="sfsiHideRating" title="' . __('No, not good enough', 'ultimate-social-media-plus') . '">' . __('No, not good enough', 'ultimate-social-media-plus') . '</a></li>
				</ul>
			</div>
			<script>
			jQuery( document ).ready(function( $ ) {
				jQuery(\'.sfsiHideRating\').click(function(){
					var data={\'action\':\'plushideRating\',\'nonce\':\'' . $nonce . '\'}
					jQuery.ajax({
						url: "' . admin_url('admin-ajax.php') . '",
						type: "post",
						data: data,
						dataType: "json",
						async: !0,
						success: function(e) {
							if (e=="success") {
							   jQuery(\'.sfwp_fivestar\').slideUp(\'slow\');
							}
						}
					});
				})
			});
			</script>';
	}
}
add_action('wp_ajax_plushideRating', 'sfsi_plusHideRatingDiv');
function sfsi_plusHideRatingDiv()
{
	if (!wp_verify_nonce($_POST['nonce'], "plushideRating")) {
		echo json_encode(array('res' => 'wrong_nonce'));
		exit;
	}
	if (!current_user_can('manage_options')) {
		echo json_encode(array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ));
		exit;
	}
	update_option('sfsi_premium_RatingDiv', 'yes');
	echo  json_encode(array("success"));
	exit;
}
/* add all admin message */

add_action('admin_notices', 'sfsi_plus_activation_msg');
add_action('admin_notices', 'sfsi_plus_rating_msg');
add_action('admin_notices', 'sfsi_plus_check_wp_head');
add_action('admin_notices', 'sfsi_plus_check_wp_footer');
function sfsi_plus_pingVendor($post_id)
{
	global $wp, $wpdb;
	// If this is just a revision, don't send the email.
	if (wp_is_post_revision($post_id))
		return;

	$post_data = get_post($post_id, ARRAY_A);
	if ($post_data['post_status'] == 'publish' && $post_data['post_type'] == 'post') :
		$feed_id = sanitize_text_field(get_option('sfsi_premium_feed_id'));
		return sfsi_plus_setUpfeeds($feed_id);
	// $categories = wp_get_post_categories($post_data['ID']);
	// $cats='';
	// $total=count($categories);
	// $count=1;
	// foreach($categories as $c)
	// {	
	// 	$cat_data = get_category( $c );
	// 	if($count==$total)
	// 	{
	// 		$cats.= $cat_data->name;
	// 	}
	// 	else
	// 	{
	// 		$cats.= $cat_data->name.',';	
	// 	}
	// 	$count++;	
	// }
	// $postto_array = array(
	// 	'feed_id'	=> sanitize_text_field(get_option('sfsi_premium_feed_id')),
	// 	'title'		=> $post_data['post_title'],
	// 	'description' => $post_data['post_content'],
	// 	'link'		=> $post_data['guid'],
	// 	'author'	=> get_the_author_meta('user_login', $post_data['post_author']),
	// 	'category' 	=> $cats,
	// 	'pubDate'	=> $post_data['post_modified'],
	// 	'rssurl'	=> sfsi_plus_get_bloginfo('rss2_url')
	// );

	// // $curl = curl_init();  
	// $curl = wp_remote_post('https://api.follow.it/wordpress/addpostdata', array(
	// 	'blocking' => true,
	//       	'timeout'=> 30,
	// 	// CURLOPT_URL => 'https://api.follow.it/wordpress/addpostdata ',
	// 	'user-agent' => 'sf rss request',
	// 	'body' => $postto_array
	// ));
	// // Send the request & save response to $resp
	// // $resp = curl_exec($curl);
	// // $resp=json_decode($resp);
	// // curl_close($curl);
	// return true;
	endif;
}

add_action('save_post', 'sfsi_plus_pingVendor');
