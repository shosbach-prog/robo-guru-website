<?php
/* maybe_unserialize all saved option for  section 4 options */
$option2 =  maybe_unserialize(get_option('sfsi_premium_section2_options', false));
$option4 =  maybe_unserialize(get_option('sfsi_premium_section4_options', false));

$sfsi_premium_install_after_13 = get_option('sfsi_premium_install_after_13.3',false);
if (!isset($option4['sfsi_plus_facebook_mypageCounts'])) {
	$option4['sfsi_plus_facebook_mypageCounts'] = '';
}
/*
	 * Sanitize, escape and validate values
	 */
$option4['sfsi_plus_display_counts'] 			= (isset($option4['sfsi_plus_display_counts']))
	? sanitize_text_field($option4['sfsi_plus_display_counts'])
	: '';
$sfsi_plus_email_countsFrom 					= (isset($option4['sfsi_plus_email_countsFrom']))
	? sanitize_text_field($option4['sfsi_plus_email_countsFrom'])
	: '';
$sfsi_plus_email_manualCounts 					= (isset($option4['sfsi_plus_email_manualCounts']))
	? intval($option4['sfsi_plus_email_manualCounts'])
	: '';
$option4['sfsi_plus_rss_countsDisplay'] 		= (isset($option4['sfsi_plus_rss_countsDisplay']))
	? sanitize_text_field($option4['sfsi_plus_rss_countsDisplay'])
	: '';
$option4['sfsi_plus_rss_manualCounts'] 			= (isset($option4['sfsi_plus_rss_manualCounts']))
	? intval($option4['sfsi_plus_rss_manualCounts'])
	: '';
$option4['sfsi_plus_email_countsDisplay'] 		= (isset($option4['sfsi_plus_email_countsDisplay']))
	? sanitize_text_field($option4['sfsi_plus_email_countsDisplay'])
	: '';

$option4['sfsi_plus_facebook_countsDisplay']	= (isset($option4['sfsi_plus_facebook_countsDisplay']))
	? sanitize_text_field($option4['sfsi_plus_facebook_countsDisplay'])
	: '';
$option4['sfsi_plus_facebook_countsFrom'] 		= (isset($option4['sfsi_plus_facebook_countsFrom']))
	? sanitize_text_field($option4['sfsi_plus_facebook_countsFrom'])
	: '';
$option4['sfsi_plus_facebook_mypageCounts'] 	= (isset($option4['sfsi_plus_facebook_mypageCounts']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_facebook_mypageCounts'])
	: '';
$option4['sfsi_plus_facebook_appid'] 			= (isset($option4['sfsi_plus_facebook_appid']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_facebook_appid'])
	: '';
$option4['sfsi_plus_facebook_appsecret'] 		= (isset($option4['sfsi_plus_facebook_appsecret']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_facebook_appsecret'])
	: '';
$option4['sfsi_plus_facebook_manualCounts'] 	= (isset($option4['sfsi_plus_facebook_manualCounts']))
	? intval($option4['sfsi_plus_facebook_manualCounts'])
	: '';
$option4['sfsi_plus_facebook_countsFrom_blog'] 	= (isset($option4['sfsi_plus_facebook_countsFrom_blog']))
	? ($option4['sfsi_plus_facebook_countsFrom_blog'])
	: '';

$option4['sfsi_plus_twitter_countsDisplay'] 	= (isset($option4['sfsi_plus_twitter_countsDisplay']))
	? sanitize_text_field($option4['sfsi_plus_twitter_countsDisplay'])
	: '';
$option4['sfsi_plus_twitter_countsFrom'] 		= (isset($option4['sfsi_plus_twitter_countsFrom']))
	? sanitize_text_field($option4['sfsi_plus_twitter_countsFrom'])
	: '';
$option4['sfsi_plus_twitter_manualCounts'] 		= (isset($option4['sfsi_plus_twitter_manualCounts']))
	? intval($option4['sfsi_plus_twitter_manualCounts'])
	: '';
$option4['sfsi_plus_threads_manualCounts'] 		= (isset($option4['sfsi_plus_threads_manualCounts']))
	? intval($option4['sfsi_plus_threads_manualCounts'])
	: '';
$option4['sfsi_plus_bluesky_manualCounts'] 		= (isset($option4['sfsi_plus_bluesky_manualCounts']))
	? intval($option4['sfsi_plus_bluesky_manualCounts'])
	: '';
$option4['sfsiplus_tw_consumer_key'] 			= (isset($option4['sfsiplus_tw_consumer_key']))
	? sfsi_plus_sanitize_field($option4['sfsiplus_tw_consumer_key'])
	: '';
$option4['sfsiplus_tw_consumer_secret'] 		= (isset($option4['sfsiplus_tw_consumer_secret']))
	? sfsi_plus_sanitize_field($option4['sfsiplus_tw_consumer_secret'])
	: '';
$option4['sfsiplus_tw_oauth_access_token'] 		= (isset($option4['sfsiplus_tw_oauth_access_token']))
	? sfsi_plus_sanitize_field($option4['sfsiplus_tw_oauth_access_token'])
	: '';
$option4['sfsiplus_tw_oauth_access_token_secret'] = (isset($option4['sfsiplus_tw_oauth_access_token_secret']))
	? sfsi_plus_sanitize_field($option4['sfsiplus_tw_oauth_access_token_secret'])
	: '';

$option4['sfsi_plus_youtube_countsDisplay'] 	= (isset($option4['sfsi_plus_youtube_countsDisplay']))
	? sanitize_text_field($option4['sfsi_plus_youtube_countsDisplay'])
	: '';
$option4['sfsi_plus_youtube_countsFrom'] 		= (isset($option4['sfsi_plus_youtube_countsFrom']))
	? sanitize_text_field($option4['sfsi_plus_youtube_countsFrom'])
	: '';
$option4['sfsi_plus_youtubeusernameorid'] 		= (isset($option4['sfsi_plus_youtubeusernameorid']))
	? sanitize_text_field($option4['sfsi_plus_youtubeusernameorid'])
	: '';
$option4['sfsi_plus_youtube_manualCounts'] 		= (isset($option4['sfsi_plus_youtube_manualCounts']))
	? intval($option4['sfsi_plus_youtube_manualCounts'])
	: '';
$option4['sfsi_plus_youtube_user'] 				= (isset($option4['sfsi_plus_youtube_user']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_youtube_user'])
	: '';
$option4['sfsi_plus_youtube_channelId'] 		= (isset($option4['sfsi_plus_youtube_channelId']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_youtube_channelId'])
	: '';


$option4['sfsi_plus_instagram_manualCounts'] 	= (isset($option4['sfsi_plus_instagram_manualCounts']))
	? intval($option4['sfsi_plus_instagram_manualCounts'])
	: '';
$option4['sfsi_plus_instagram_User'] 			= (isset($option4['sfsi_plus_instagram_User']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_instagram_User'])
	: '';
$option4['sfsi_plus_instagram_clientid'] 		= (isset($option4['sfsi_plus_instagram_clientid']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_instagram_clientid'])
	: '';
$option4['sfsi_plus_instagram_appurl'] 			= (isset($option4['sfsi_plus_instagram_appurl']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_instagram_appurl'])
	: '';
$option4['sfsi_plus_instagram_token'] 			= (isset($option4['sfsi_plus_instagram_token']))
	? sfsi_plus_sanitize_field($option4['sfsi_plus_instagram_token'])
	: '';
$option4['sfsi_plus_instagram_countsFrom'] 		= (isset($option4['sfsi_plus_instagram_countsFrom']))
	? sanitize_text_field($option4['sfsi_plus_instagram_countsFrom'])
	: '';
$option4['sfsi_plus_instagram_countsDisplay']	= (isset($option4['sfsi_plus_instagram_countsDisplay']))
	? sanitize_text_field($option4['sfsi_plus_instagram_countsDisplay'])
	: '';

$option4['sfsi_plus_linkedIn_manualCounts'] 	= (isset($option4['sfsi_plus_linkedIn_manualCounts']))
	? intval($option4['sfsi_plus_linkedIn_manualCounts'])
	: '';
$option4['sfsi_plus_houzz_manualCounts'] 		= (isset($option4['sfsi_plus_houzz_manualCounts']))
	? intval($option4['sfsi_plus_houzz_manualCounts'])
	: '';
$option4['sfsi_plus_pinterest_manualCounts'] 	= (isset($option4['sfsi_plus_pinterest_manualCounts']))
	? intval($option4['sfsi_plus_pinterest_manualCounts'])
	: '';
$option4['sfsi_plus_shares_manualCounts'] 		= (isset($option4['sfsi_plus_shares_manualCounts']))
	? intval($option4['sfsi_plus_shares_manualCounts'])
	: '';

$option4['sfsi_plus_snapchat_manualCounts'] 	= (isset($option4['sfsi_plus_snapchat_manualCounts']))
	? intval($option4['sfsi_plus_snapchat_manualCounts'])
	: '';
$option4['sfsi_plus_whatsapp_manualCounts'] 	= (isset($option4['sfsi_plus_whatsapp_manualCounts']))
	? intval($option4['sfsi_plus_whatsapp_manualCounts'])
	: '';
$option4['sfsi_plus_skype_countsDisplay'] 		= (isset($option4['sfsi_plus_skype_countsDisplay']))
	? intval($option4['sfsi_plus_skype_countsDisplay'])
	: '';
$option4['sfsi_plus_phone_manualCounts'] 		= (isset($option4['sfsi_plus_phone_manualCounts']))
	? intval($option4['sfsi_plus_phone_manualCounts'])
	: '';
$option4['sfsi_plus_phone_manualCounts'] 		= (isset($option4['sfsi_plus_phone_manualCounts']))
	? intval($option4['sfsi_plus_phone_manualCounts'])
	: '';
$option4['sfsi_plus_vimeo_manualCounts'] 		= (isset($option4['sfsi_plus_vimeo_manualCounts']))
	? intval($option4['sfsi_plus_vimeo_manualCounts'])
	: '';
$option4['sfsi_plus_soundcloud_manualCounts'] 	= (isset($option4['sfsi_plus_soundcloud_manualCounts']))
	? intval($option4['sfsi_plus_soundcloud_manualCounts'])
	: '';
$option4['sfsi_plus_yummly_manualCounts'] 		= (isset($option4['sfsi_plus_yummly_manualCounts']))
	? intval($option4['sfsi_plus_yummly_manualCounts'])
	: '';
$option4['sfsi_plus_yummly_countsFrom'] 		= (isset($option4['sfsi_plus_yummly_countsFrom']))
	? sanitize_text_field($option4['sfsi_plus_yummly_countsFrom'])
	: '';
$option4['sfsi_plus_flickr_manualCounts'] 		= (isset($option4['sfsi_plus_flickr_manualCounts']))
	? intval($option4['sfsi_plus_flickr_manualCounts'])
	: '';
$option4['sfsi_plus_reddit_manualCounts'] 		= (isset($option4['sfsi_plus_reddit_manualCounts']))
	? intval($option4['sfsi_plus_reddit_manualCounts'])
	: '';
$option4['sfsi_plus_tumblr_manualCounts'] 		= (isset($option4['sfsi_plus_tumblr_manualCounts']))
	? intval($option4['sfsi_plus_tumblr_manualCounts'])
	: '';

$sfsi_plus_fb_count_caching_active 				= (isset($option4['sfsi_plus_fb_count_caching_active']))
	? $option4['sfsi_plus_fb_count_caching_active']
	: 'no';
$sfsi_plus_tw_count_caching_active 				= (isset($option4['sfsi_plus_tw_count_caching_active']))
	? $option4['sfsi_plus_tw_count_caching_active']
	: 'no';


$sfsi_plus_fb_caching_interval      = (isset($option4['sfsi_plus_fb_caching_interval'])) ? intval($option4['sfsi_plus_fb_caching_interval']) : 1;

$min_count 							= sfsi_premium_get_option($option4, 'sfsi_plus_min_display_counts', 1, 'intval');

$counts = sfsi_plus_getCounts();
/* fetch counts for admin sections */

/* check for email icon display */
$email_image = "email.png";
if (isset($option2['sfsi_plus_rss_icons']) && $option2['sfsi_plus_rss_icons'] == "sfsi") {
	$email_image = "sf_arow_icn.png";
} elseif (isset($option2['sfsi_plus_rss_icons']) && $option2['sfsi_plus_rss_icons'] == "email") {
	$email_image = "email.png";
} else {
	$email_image = "subscribe.png";
}

$hide = "display:none;";
?>
<!-- Section 4 "Do you want to display "counts" next to your icons?" main div Start -->
<div class="tab4">

	<div class="sfsi_plus_curlNotification">
	</div>

	<p>
		<?php _e('It’s a psychological fact that people like to follow other people, so when they see that your site has already a good number of Facebook likes, it’s more likely that they will subscribe/like/share your site than if it had 0.', 'ultimate-social-media-plus'); ?>
	</p>
	<p>
		<?php _e('Therefore, you can select to display the count next to your icons which will look like this:', 'ultimate-social-media-plus'); ?>
	</p>

	<!-- sample icons -->
	<ul class="like_icon">
		<li class="sfsiplus_rss_section">
			<a href="#" title="RSS">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/rss.png" alt="<?php _e( 'RSS', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_email_section">
			<a href="#" title="Email">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/<?php echo $email_image; ?>" alt="<?php _e( 'Email', 'ultimate-social-media-plus' ); ?>" class="icon_img" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_facebook_section">
			<a href="#" title="<?php _e( 'Facebook', 'ultimate-social-media-plus' ); ?>">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/fb.png" alt="<?php _e( 'Facebook', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_twitter_section">
			<a href="#" title="X/Twitter">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/twitter.png" alt="<?php _e( 'Twitter', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_share_section">
			<a href="#" title="Share">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/share.png" alt="<?php _e( 'Share', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_youtube_section">
			<a href="#" title="YouTube">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/youtube.png" alt="<?php _e( 'YouTube', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_pinterest_section">
			<a href="#" title="Pinterest">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/pinterest.png" alt="<?php _e( 'Pinterest', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_linkedin_section">
			<a href="#" title="Linked In">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/linked_in.png" alt="<?php _e( 'Linked In', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_instagram_section">
			<a href="#" title="Instagram">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/instagram.png" alt="<?php _e( 'instagram', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
        <li class="sfsiplus_ria_section">
            <a href="#" title="RateItAll">
                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/ria.png" alt="<?php _e( 'RateItAll', 'ultimate-social-media-plus' ); ?>" />
            </a><span>12k</span>
        </li>
        <li class="sfsiplus_inha_section">
            <a href="#" title="IncreasingHappiness">
                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/inha.png" alt="<?php _e( 'IncreasingHappiness', 'ultimate-social-media-plus' ); ?>" />
            </a><span>12k</span>
        </li>
		<li class="sfsiplus_houzz_section">
			<a href="#" title="Houzz">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/houzz.png" alt="<?php _e( 'Houzz', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_snapchat_section">
			<a href="#" title="Snapchat">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/snapchat.png" alt="<?php _e( 'snapchat', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_whatsapp_section">
			<a href="#" title="WhatsApp">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/whatsapp.png" alt="<?php _e( 'WhatsApp', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_skype_section">
			<a href="#" title="Skype">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/skype.png" alt="<?php _e( 'Skype', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_phone_section">
			<a href="#" title="Phone">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_phone.png" style="width:47px!important;height:47px!important;padding-bottom: 3px;"  alt="<?php _e( 'Phone', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_vimeo_section">
			<a href="#" title="Vimeo">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/vimeo.png" alt="<?php _e( 'Vimeo', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_soundcloud_section">
			<a href="#" title="Soundcloud">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/soundcloud.png" alt="<?php _e( 'Soundcloud', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_yummly_section">
			<a href="#" title="Yummly">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/yummly.png" alt="<?php _e( 'Yummly', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_flickr_section">
			<a href="#" title="Flickr">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/flickr.png" alt="<?php _e( 'Flickr', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_reddit_section">
			<a href="#" title="Reddit">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/reddit.png" alt="<?php _e( 'Reddit', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_tumblr_section">
			<a href="#" title="Tumblr">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/tumblr.png" alt="<?php _e( 'Tumblr', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_fbmessenger_section">
			<a href="#" title="fbmessenger">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_fbmessenger.png" alt="<?php _e( 'fbmessenger', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_gab_section">
			<a href="#" title="gab">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_gab.png" alt="<?php _e( 'gab', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_mix_section">
			<a href="#" title="Mix">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_mix.png" alt="<?php _e( 'Mix', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_ok_section">
			<a href="#" title="ok">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_ok.png" alt="<?php _e( 'ok', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_telegram_section">
			<a href="#" title="Telegram">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_telegram.png" alt="<?php _e( 'Telegram', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_vk_section">
			<a href="#" title="VK">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_vk.png" alt="<?php _e( 'VK', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_weibo_section">
			<a href="#" title="Weibo">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_weibo.png" alt="<?php _e( 'Weibo', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_wechat_section">
			<a href="#" title="Wechat">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_wechat.png" alt="<?php _e( 'Wechat', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsiplus_xing_section">
			<a href="#" title="Xing">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_xing.png" alt="<?php _e( 'Xing', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

		<li class="sfsi_mastodon_section">
			<a href="#" title="Mastodon">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_mastodon.png" alt="<?php _e( 'Mastodon', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>
		<li class="sfsiplus_copylink_section">
			<a href="#" title="Copylink">
				<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_copylink.png" alt="<?php _e( 'Copylink', 'ultimate-social-media-plus' ); ?>" />
			</a><span>12k</span>
		</li>

	</ul>
	<!-- END sample icons -->

	<p>
		<?php _e( 'Of course, if you start at 0, you shoot yourself in the foot with that. So we suggest that you only turn this feature on once you have a good number of followers/likes/shares (min. of 20 – no worries if it’s not too many, it should just not be 0).', 'ultimate-social-media-plus' ); ?>
	</p>
	<h4>
		<?php _e('Enough waffling. So do you want to display counts?', 'ultimate-social-media-plus'); ?>
	</h4>
	<!-- show/hide counts for icons section  START -->
	<ul class="enough_waffling">
		<li>
			<input name="sfsi_plus_display_counts" <?php echo ($option4['sfsi_plus_display_counts'] == 'yes') ? 'checked="true"' : ''; ?> type="radio" value="yes" class="styled" />
			<label>
				<?php _e('Yes', 'ultimate-social-media-plus'); ?>
			</label>
		</li>
		<li>
			<input name="sfsi_plus_display_counts" <?php echo ($option4['sfsi_plus_display_counts'] == 'no') ? 'checked="true"' : ''; ?> type="radio" value="no" class="styled" />
			<label>
				<?php _e('No', 'ultimate-social-media-plus'); ?>
			</label>
		</li>
	</ul>
	<!-- END  show/hide counts for icons section -->
	<!-- show/hide counts for all icons section  START -->
	<div class="sfsiplus_count_sections" style="display:none">
		<h4>
			<?php _e('Please specify which counts should be shown:', 'ultimate-social-media-plus'); ?>
		</h4>

		<!-- RSS ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_rss_section">
			<div class="radio_section">
				<input name="sfsi_plus_rss_countsDisplay" <?php echo ($option4['sfsi_plus_rss_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="RSS">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/rss.png" alt="RSS" />
							<span><?php echo $counts['rss_count']; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<?php
						_e('We cannot track this. So enter the figure here:', 'ultimate-social-media-plus'); ?>
						<input name="sfsi_plus_rss_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_rss_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_rss_manualCounts']) : ''; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END RSS ICON COUNT SECTION-->

		<!-- EMAIL ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_email_section">
			<div class="radio_section">
				<input name="sfsi_plus_email_countsDisplay" <?php echo ($option4['sfsi_plus_email_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="Email">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/<?php echo $email_image; ?>" alt="Email" />
							<span><?php echo $counts['email_count']; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_email_countsFrom" <?php echo ($sfsi_plus_email_countsFrom == 'source') ? 'checked="true"' : ''; ?> type="radio" value="source" class="styled" />
						<?php _e('Retrieve the number of subscribers automatically', 'ultimate-social-media-plus'); ?>
					</li>
					<li>
						<input name="sfsi_plus_email_countsFrom" <?php echo ($sfsi_plus_email_countsFrom == 'manual') ? 'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />

						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>

						<input name="sfsi_plus_email_manualCounts" type="text" class="input" value="<?php echo ($sfsi_plus_email_manualCounts != '') ? esc_attr($sfsi_plus_email_manualCounts) : ''; ?>" style="<?php echo ($sfsi_plus_email_countsFrom == 'source') ? 'display:none;' : ''; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!--END  EMAIL  ICON COUNT SECTION-->

		<!-- FACEBOOK ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_facebook_section">
			<div class="radio_section">
				<input name="sfsi_plus_facebook_countsDisplay" <?php echo ($option4['sfsi_plus_facebook_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="<?php _e( 'Facebook', 'ultimate-social-media-plus' ); ?>">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/fb.png" alt="<?php _e( 'Facebook', 'ultimate-social-media-plus' ); ?>" />
							<span><?php echo $counts['fb_count']; ?></span>
						</a>
					</li>
				</ul>
			</div>

			<?php $fbAppSectionDisplay = ($option4['sfsi_plus_facebook_countsFrom'] == 'followers' || $option4['sfsi_plus_facebook_countsFrom'] == 'manual') ? 'display:none;' : '';
			$accesstokenDisplay  = (isset($option4['sfsi_plus_facebook_countsFrom_blog']) && !empty($option4['sfsi_plus_facebook_countsFrom_blog'])) ? '' : 'display:none;';
			?>

			<div id="fbappinstructionContainer" style="<?php echo $fbAppSectionDisplay; ?>">

				<div class="fbappidandsecret">

					<div class="sfsiplus_fbpgiddesc">

						<div class="sfsiplus_fbpgidlbl"><?php _e('Facebook App ID:', 'ultimate-social-media-plus'); ?></div>
						<input name="sfsi_plus_facebook_appid" type="text" class="input mypginpt" value="<?php echo ($option4['sfsi_plus_facebook_appid'] != '') ?  esc_attr($option4['sfsi_plus_facebook_appid']) : ''; ?>" />
					</div>

					<div class="sfsiplus_fbpgiddesc">
						<div class="sfsiplus_fbpgidlbl"><?php _e('Facebook App Secret:', 'ultimate-social-media-plus'); ?></div>
						<input name="sfsi_plus_facebook_appsecret" type="text" class="input mypginpt" value="<?php echo ($option4['sfsi_plus_facebook_appsecret'] != '') ?  esc_attr($option4['sfsi_plus_facebook_appsecret']) : ''; ?>" />
					</div>

					<div class="sfsi_premium_fbInstructions">

						<p>
							<?php _e("How to create a Facebook app, and get the required information above, please see: <a target='_blank' style='text-decoration: underline;' href='https://www.ultimatelysocial.com/how-to-create-a-facebook-app/'>https://www.ultimatelysocial.com/how-to-create-a-facebook-app/</a>", 'ultimate-social-media-plus'); ?>
						</p>

					</div>

					<div class="sfsi_premium_fbInstructions_fbtoken" style="<?php echo $accesstokenDisplay; ?>">
						<p><?php _e('To get your access token please follow these steps:', 'ultimate-social-media-plus'); ?></p>
						<ul>
							<li><?php _e('1. Go to ', 'ultimate-social-media-plus'); ?><a href="https://developers.facebook.com" target="_blank">https://developers.facebook.com</a></li>
							<li><?php _e('2. Login, click on “My Apps” and then in the drop-down on “Add a New App”', 'ultimate-social-media-plus'); ?></li>
							<li><?php _e('3. Give your App a name (e.g. “USM app”)', 'ultimate-social-media-plus'); ?></li>
							<li><?php _e('4. Click on “Create App ID”', 'ultimate-social-media-plus'); ?></li>
							<li><?php _e('5. (Enter the captcha if it shows one)', 'ultimate-social-media-plus'); ?></li>
							<li><?php _e('6. Then enter url in browser: ', 'ultimate-social-media-plus'); ?><a href="https://developers.facebook.com/tools/accesstoken/" target="_blank">https://developers.facebook.com/tools/accesstoken/</a></li>
							<li><?php _e('7. Search your app name in page & click on “Debug“ button in front of “User Token“', 'ultimate-social-media-plus'); ?></li>
							<li><?php _e('8. Click on “Extend Access Token”', 'ultimate-social-media-plus'); ?></li>
							<li><?php _e('9. There you’ll see your access token. Copy it and paste it on the plugin page and click on “Verify access token”. Then save it', 'ultimate-social-media-plus'); ?>.</li>
						</ul>
					</div>

				</div>

			</div>

			<div class="listing">
				<ul>
					<li>
						<div class="sfsi_premium_fbField">

							<input name="sfsi_plus_facebook_countsFrom" <?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'likes') ? 'checked="true"' : ''; ?> type="radio" value="likes" class="styled" />

							<?php _e('Retrieve the number of likes of your blog?', 'ultimate-social-media-plus'); ?>

							<div class="sfsi_plus_caching_section sfsi_plus_fb_caching_section" style="<?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'likes') ? 'display: block;' : 'display: none;'; ?>">

								<h4>
									<?php _e('Do you want to activate caching for facebook like count?', 'ultimate-social-media-plus'); ?>
								</h4>

								<!--<div class="sfsi_caching_note">
						        	<h4 style="margin-top:0px !important;"><?php //_e(' ( * Counts are only updated every 24 hrs)');
																			?></h4>
						        </div>-->

								<ul class="sfsi_plus_caching_selection">

									<li class="commonliradio">

										<?php
										$check = ($sfsi_plus_fb_count_caching_active == 'no') ? 'checked="checked"' : '';
										?>

										<input type="radio" name="sfsi_plus_fb_count_caching_active" class="styled" value="no" <?php echo $check; ?> />

										<label class="commonliradioLabel">

											<?php _e("No", 'ultimate-social-media-plus'); ?>

										</label>

									</li>

									<li class="commonliradio">

										<?php
										$check = ($sfsi_plus_fb_count_caching_active == 'yes') ? 'checked="checked"' : '';
										?>

										<input type="radio" name="sfsi_plus_fb_count_caching_active" class="styled" value="yes" <?php echo $check; ?> />

										<label class="commonliradioLabel">

											<?php _e("Yes", 'ultimate-social-media-plus'); ?>

										</label>

									</li>

								</ul>

								<?php
								$disInterval = ($sfsi_plus_fb_count_caching_active == 'no') ? 'display:none' : 'display:block';
								?>

								<div class="field" style="<?php echo $disInterval; ?>">

									<h4>
										<?php _e('Please specify the time interval (hrs) for caching', 'ultimate-social-media-plus'); ?>

									</h4>

									<select name="sfsi_plus_fb_caching_interval" id="sfsi_plus_fb_caching_interval">

										<?php $intervals = range(1, 48);

										foreach ($intervals as $value) :

											$selected =  $value == $sfsi_plus_fb_caching_interval ? "selected=selected" : '';

											?>

											<option <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $value; ?></option>

										<?php endforeach;

										?>
									</select>

									<div class="sfsi_caching_note">
										<h4 style="margin-top:0px !important;"><?php _e(' ( * Maximum URLs for which single facebook api will fetch count in specified interval is 4950. If "Aggregation Count" is active then it is: 2475)', 'ultimate-social-media-plus' ); ?></h4>
									</div>

								</div>

							</div>

							<div class="sfsi_plus_facebookfbtocen" style="<?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'likes' && isset($option4['sfsi_plus_facebook_countsFrom_blog']) && $option4['sfsi_plus_facebook_countsFrom_blog'] != '') ? '' : 'display:none;'; ?>">
								<div class="sfsi_plus_facebook_button">
									<div class="sfsi_plus_fldtitle">
										<?php _e('Facebook access token:', 'ultimate-social-media-plus'); ?>
									</div>
									<input name="sfsi_plus_facebook_countsFrom_blog" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_facebook_countsFrom_blog']) && $option4['sfsi_plus_facebook_countsFrom_blog'] != '') ? esc_attr($option4['sfsi_plus_facebook_countsFrom_blog']) : ''; ?>" />
									<a href="javascript:" id="sfsi_plus_facebook_fbtoken">
										<?php _e('Verify access token', 'ultimate-social-media-plus'); ?>
									</a>
								</div>
								<span id="sfsi_plus_facebook_token_notice"></span>
								<span id="sfsi_plus_facebook_notice"></span>
							</div>
						</div>

					</li>
					<li>
						<div class="sfsi_premium_fbField">

							<input name="sfsi_plus_facebook_countsFrom" <?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'mypage') ? 'checked="true"' : ''; ?> type="radio" value="mypage" class="styled" />
							<?php _e('Retrieve the number of likes of your facebook page', 'ultimate-social-media-plus'); ?>

							<div class="sfsiplus_fbpgiddesc">

								<div class="sfsiplus_fbpgidwpr fbpagidlbl" style="<?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'likes' || $option4['sfsi_plus_facebook_countsFrom'] == 'followers' || $option4['sfsi_plus_facebook_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
									<?php _e('Facebook page ID:', 'ultimate-social-media-plus'); ?>
								</div>
								<input name="sfsi_plus_facebook_mypageCounts" type="text" class="input mypginpt" value="<?php echo ($option4['sfsi_plus_facebook_mypageCounts'] != '') ?  esc_attr($option4['sfsi_plus_facebook_mypageCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'likes' || $option4['sfsi_plus_facebook_countsFrom'] == 'followers' || $option4['sfsi_plus_facebook_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>" />
							</div>

							<div class="sfsiplus_fbpgidwpr sfsiplus_fbpgiddesc" style="<?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'likes' || $option4['sfsi_plus_facebook_countsFrom'] == 'followers' || $option4['sfsi_plus_facebook_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								(<?php _e("You'll find it at the bottom of the << About >> -tab on your facebook page", 'ultimate-social-media-plus'); ?>)
							</div>

						</div>

					</li>
					<li>
						<input name="sfsi_plus_facebook_countsFrom" <?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'manual') ? 'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_facebook_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_facebook_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_facebook_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_facebook_countsFrom'] == 'likes' || $option4['sfsi_plus_facebook_countsFrom'] == 'followers' || $option4['sfsi_plus_facebook_countsFrom'] == 'mypage') ? 'display:none;' : ''; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END FACEBOOK ICON COUNT SECTION-->

		<!-- TWITTER ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_twitter_section">
			<div class="radio_section">
				<input name="sfsi_plus_twitter_countsDisplay" <?php echo ($option4['sfsi_plus_twitter_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="X/Twitter">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/twitter.png" alt="Twitter" />
							<span><?php echo $counts['twitter_count']; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_twitter_countsFrom" <?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'source') ? 'checked="true"' : ''; ?> type="radio" value="source" class="styled" />
						<?php _e('Retrieve the number of X (Twitter) followers', 'ultimate-social-media-plus'); ?>
					</li>
					<li class="SFSI_tglli">
						<ul class="SFSI_lsngfrm">
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<label>
									<?php _e('Enter Consumer Key', 'ultimate-social-media-plus'); ?>
								</label>
								<input name="sfsiplus_tw_consumer_key" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsiplus_tw_consumer_key']) && $option4['sfsiplus_tw_consumer_key'] != '') ? esc_attr($option4['sfsiplus_tw_consumer_key']) : ''; ?>" />
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<label>
									<?php _e('Enter Consumer Secret', 'ultimate-social-media-plus'); ?>
								</label>
								<input name="sfsiplus_tw_consumer_secret" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsiplus_tw_consumer_secret']) && $option4['sfsiplus_tw_consumer_secret'] != '') ? esc_attr($option4['sfsiplus_tw_consumer_secret']) : ''; ?>" />
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<label>
									<?php _e('Enter Access Token', 'ultimate-social-media-plus'); ?>
								</label>
								<input name="sfsiplus_tw_oauth_access_token" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsiplus_tw_oauth_access_token']) && $option4['sfsiplus_tw_oauth_access_token'] != '') ? esc_attr($option4['sfsiplus_tw_oauth_access_token']) : ''; ?>" />
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<label>
									<?php _e('Enter Access Token Secret', 'ultimate-social-media-plus'); ?>
								</label>
								<input name="sfsiplus_tw_oauth_access_token_secret" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsiplus_tw_oauth_access_token_secret']) && $option4['sfsiplus_tw_oauth_access_token_secret'] != '') ? esc_attr($option4['sfsiplus_tw_oauth_access_token_secret']) : ''; ?>" />
							</li>

							<li style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'source') ? 'display: block' : 'display: none'; ?>">

								<div class="sfsi_plus_caching_section sfsi_plus_tw_caching_section">
									<h4>
										<?php _e('Do you want to activate caching for X (Twitter) followers count?', 'ultimate-social-media-plus'); ?>
									</h4>

									<ul class="sfsi_plus_caching_selection">

										<li class="commonliradio">

											<?php
											$check = ($sfsi_plus_tw_count_caching_active == 'no') ? 'checked="checked"' : '';
											?>

											<input type="radio" name="sfsi_plus_tw_count_caching_active" class="styled" value="no" <?php echo $check; ?> />

											<label class="commonliradioLabel">

												<?php _e("No", 'ultimate-social-media-plus'); ?>

											</label>

										</li>

										<li class="commonliradio">

											<?php
											$check = ($sfsi_plus_tw_count_caching_active == 'yes') ? 'checked="checked"' : '';
											?>

											<input type="radio" name="sfsi_plus_tw_count_caching_active" class="styled" value="yes" <?php echo $check; ?> />

											<label class="commonliradioLabel">

												<?php _e("Yes", 'ultimate-social-media-plus'); ?>

											</label>

										</li>

									</ul>
								</div>
							</li>

						</ul>

						<ul class="SFSI_instructions">
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<?php _e('Please make sure you have entered the Username for "Follow me on X (Twitter):" in X (Twitter) settings under question number 2.', 'ultimate-social-media-plus'); ?>
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<h3>
									<?php _e('To get this information:', 'ultimate-social-media-plus'); ?>
								</h3>
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<?php _e('1: Go to', 'ultimate-social-media-plus'); ?>
								<a href="http://apps.twitter.com" target="_blank">
									apps.twitter.com
								</a>
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<?php _e('2: Click on "Create new app"', 'ultimate-social-media-plus'); ?>
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<?php _e('3: Enter a random Name, Description and Website URL (including the "http://", e.g. http://dummysitename.com)', 'ultimate-social-media-plus'); ?>
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<?php _e('4: Go to "Keys and Access Tokens" tab and click on "Generate Token" in the "Token actions" section at the bottom', 'ultimate-social-media-plus'); ?>
							</li>
							<li class="tw_follow_options" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
								<?php _e('5: Then click on "Test OAuth" at the top right and you will see the 4 token key', 'ultimate-social-media-plus'); ?>
							</li>
						</ul>

					</li>
					<li>
						<input name="sfsi_plus_twitter_countsFrom" <?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'manual') ? 'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_twitter_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_twitter_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_twitter_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_twitter_countsFrom'] == 'source') ? 'display:none;' : ''; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!--END TWITTER ICON COUNT SECTION-->

        <!-- THREADS ICON COUNT SECTION-->
        <div class="sfsiplus_specify_counts sfsiplus_threads_section">
            <div class="radio_section">
                <input name="sfsi_plus_threads_countsDisplay" <?php echo (isset($option4['sfsi_plus_threads_countsDisplay']) && $option4['sfsi_plus_threads_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="Threads">
                            <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/threads.png" alt="Threads" />
                            <span><?php echo $counts['threads_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>
<!--                        <input name="sfsi_plus_threads_countsFrom" --><?php //echo ($option4['sfsi_plus_threads_countsFrom'] == 'manual') ? 'checked="true"' : ''; ?><!-- type="radio" value="manual" class="styled" />-->
                        <input name="sfsi_plus_threads_countsFrom" checked="checked" type="radio" value="manual" class="styled" />
                        <label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
                        </label>
                        <input name="sfsi_plus_threads_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_threads_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_threads_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_threads_countsFrom'] == 'source') ? 'display:none;' : ''; ?>" />
                    </li>
                </ul>
            </div>
        </div>
        <!--END THREADS ICON COUNT SECTION-->

        <!-- BLUESKY ICON COUNT SECTION-->
        <div class="sfsiplus_specify_counts sfsiplus_bluesky_section">
            <div class="radio_section">
                <input name="sfsi_plus_bluesky_countsDisplay" <?php echo (isset($option4['sfsi_plus_bluesky_countsDisplay']) && $option4['sfsi_plus_bluesky_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="Bluesky">
                            <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/bluesky.png" alt="Bluesky" />
                            <span><?php echo $counts['bluesky_count']; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>
<!--                        <input name="sfsi_plus_bluesky_countsFrom" --><?php //echo ($option4['sfsi_plus_bluesky_countsFrom'] == 'manual') ? 'checked="true"' : ''; ?><!-- type="radio" value="manual" class="styled" />-->
                        <input name="sfsi_plus_bluesky_countsFrom" checked="checked" type="radio" value="manual" class="styled" />
                        <label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
                        </label>
                        <input name="sfsi_plus_bluesky_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_bluesky_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_bluesky_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_bluesky_countsFrom'] == 'source') ? 'display:none;' : ''; ?>" />
                    </li>
                </ul>
            </div>
        </div>
        <!--END BLUESKY ICON COUNT SECTION-->

		<!-- LINKEDIN ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_linkedin_section">
			<div class="radio_section">
				<input name="sfsi_plus_linkedIn_countsDisplay" <?php echo ($option4['sfsi_plus_linkedIn_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="Linked in">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/linked_in.png" alt="Linked in" />
							<span><?php echo $counts['linkedIn_count']; ?></span>
						</a>
					</li>
				</ul>
			</div>

			<div class="listing">
				<ul>
					<?php /*?><li><input name="sfsi_plus_linkedIn_countsFrom" <?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='follower') ? 'checked="true"' : '' ;?>  type="radio" value="follower" class="styled"  />Retrieve the number of Linkedin followers</li>
						<li class="SFSI_tglli">
							<ul class="SFSI_lsngfrm">
								<li class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>"><label>Enter Company Name </label><input name="sfsi_plus_ln_company" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_ln_company']) && $option4['sfsi_plus_ln_company']!='') ?  $option4['sfsi_plus_ln_company'] : '' ;?>"  /> </li>
						<li  class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>"><label>Enter API Key </label><input name="sfsi_plus_ln_api_key" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_ln_api_key']) && $option4['sfsi_plus_ln_api_key']!='') ?  $option4['sfsi_plus_ln_api_key'] : '' ;?>"  /> </li>
						<li  class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>"><label>Enter Secret Key </label><input name="sfsi_plus_ln_secret_key" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_ln_secret_key']) && $option4['sfsi_plus_ln_secret_key']!='') ?  $option4['sfsi_plus_ln_secret_key'] : '' ;?>"  /> </li>
						<li  class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>" ><label>Enter OAuth User Token</label> <input name="sfsi_plus_ln_oAuth_user_token" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_ln_oAuth_user_token']) && $option4['sfsi_plus_ln_oAuth_user_token']!='') ?  $option4['sfsi_plus_ln_oAuth_user_token'] : '' ;?>"  /> </li>
					</ul>
					<ul class="SFSI_instructions">
						<li class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>"><h3>To get the API key for LinkedIn:</h3></li>
						<li class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>">1: Go to <a href="https://developer.linkedin.com/" target="_blank">www.developer.linkedin.com</a>, mouse over “Support” and select “API keys”</li>
						<li class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>">2: Then login with your Linkedin account and create a new application</li>
						<li class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>">3: Fill the required boxes in the form with random data, accept the Terms and add the application</li>
						<li class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>">4: In the next step you will see the required API information</li>
						<li class="linkedIn_options" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom']=='manual') ? 'display:none;' : '' ;?>">When you enter this key into the plugin for the first time, it may take some time until the correct follower count is displayed on your website.</li>
					</ul>
					</li><?php */ ?>
					<li>
						<input name="sfsi_plus_linkedIn_countsFrom" <?php echo ($option4['sfsi_plus_linkedIn_countsFrom'] == 'manual' || $option4['sfsi_plus_linkedIn_countsFrom'] == 'follower') ? 'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_linkedIn_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_linkedIn_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_linkedIn_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_linkedIn_countsFrom'] == 'follower') ? 'display:none;' : ''; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END LINKEDIN ICON COUNT SECTION-->

		<!-- YOUTUBE ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_youtube_section">
			<div class="radio_section">
				<input name="sfsi_plus_youtube_countsDisplay" <?php echo ($option4['sfsi_plus_youtube_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="YouTube">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/youtube.png" alt="YouTube" />
							<span><?php echo $counts['youtube_count']; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_youtube_countsFrom" type="radio" value="subscriber" <?php echo ($option4['sfsi_plus_youtube_countsFrom'] == 'subscriber') ? 'checked="true"' : ''; ?> class="styled" />
						<?php _e('Retrieve the number of Subscribers', 'ultimate-social-media-plus'); ?>
					</li>

					<li class="youtube_options" style="<?php echo ($option4['sfsi_plus_youtube_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
						<div>
							<label>
								<?php _e('Enter Youtube Channel id', 'ultimate-social-media-plus'); ?>
							</label>
							<input name="sfsi_plus_youtube_channelId" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_youtube_channelId']) && $option4['sfsi_plus_youtube_channelId'] != '') ? esc_attr($option4['sfsi_plus_youtube_channelId']) : ''; ?>" />
						</div>
					</li>

					<li>
						<input name="sfsi_plus_youtube_countsFrom" type="radio" value="manual" <?php echo ($option4['sfsi_plus_youtube_countsFrom'] == 'manual') ? 'checked="true"' : ''; ?> class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_youtube_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_youtube_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_youtube_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_youtube_countsFrom'] == 'subscriber') ? 'display:none;' : ''; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END YOUTUBE ICON COUNT SECTION-->

		<!-- PINIT ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_pinterest_section">
			<div class="radio_section">
				<input name="sfsi_plus_pinterest_countsDisplay" <?php echo ($option4['sfsi_plus_pinterest_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="Pinterest">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/pinterest.png" alt="Pinterest" />
							<span><?php echo $counts['pin_count']; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_pinterest_countsFrom" <?php echo ($option4['sfsi_plus_pinterest_countsFrom'] == 'pins') ? 'checked="true"' : ''; ?> type="radio" value="pins" class="styled" />
						<?php _e('Retrieve the number of Pinterest (+1) (on your blog)', 'ultimate-social-media-plus'); ?>
					</li>

					<li>
						<input name="sfsi_plus_pinterest_countsFrom" <?php echo ($option4['sfsi_plus_pinterest_countsFrom'] == 'accountpins') ? 'checked="true"' : ''; ?> type="radio" value="accountpins" class="styled" />
						<?php _e('Retrieve the number of pins from your pinterest account', 'ultimate-social-media-plus'); ?>
					</li>

					<li>
						<input name="sfsi_plus_pinterest_countsFrom" <?php echo ($option4['sfsi_plus_pinterest_countsFrom'] == 'followers') ? 'checked="true"' : ''; ?> type="radio" value="followers" class="styled" />
						<?php _e('Retrieve the number of pinterest followers', 'ultimate-social-media-plus'); ?>
					</li>

					<li>
						<input name="sfsi_plus_pinterest_countsFrom" <?php echo ($option4['sfsi_plus_pinterest_countsFrom'] == 'board') ? 'checked="true"' : ''; ?> type="radio" value="board" class="styled" />
						<?php _e('Retrieve the number of pins of board from your pinterest account', 'ultimate-social-media-plus'); ?>
					</li>

					<li class="sfsi_input_section_pinterest_username_board_li">
						<div>
							<label>
								<?php _e('Enter Pinterest User name', 'ultimate-social-media-plus'); ?>
							</label>
							<input name="sfsi_plus_pinterest_user" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_pinterest_user']) && $option4['sfsi_plus_pinterest_user'] != '') ? esc_attr($option4['sfsi_plus_pinterest_user']) : ''; ?>" />
						</div>

						<div class="sfsi_plus_pinterest_board_name_cl">
							<label>
								<?php _e('Enter Board Name', 'ultimate-social-media-plus'); ?>
							</label>
							<input name="sfsi_plus_pinterest_board_name" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_pinterest_board_name']) && $option4['sfsi_plus_pinterest_board_name'] != '') ? esc_attr($option4['sfsi_plus_pinterest_board_name']) : ''; ?>" />
						</div>
					</li>

					<li>
						<input name="sfsi_plus_pinterest_countsFrom" <?php echo ( isset( $option4['sfsi_plus_pinterest_countsFrom'] ) && ( $option4['sfsi_plus_pinterest_countsFrom'] == '' || $option4['sfsi_plus_pinterest_countsFrom'] == 'manual' ) ) ? 'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_pinterest_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_pinterest_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_pinterest_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_pinterest_countsFrom'] == 'pins') ? 'display:none;' : ''; ?>" />
					</li>
				</ul>

				<?php

				$style = "";
				if ( $option4['sfsi_plus_pinterest_countsFrom'] == "accountpins" || $option4['sfsi_plus_pinterest_countsFrom'] == "followers" || $option4['sfsi_plus_pinterest_countsFrom'] == "board" ) {
					$style = 'display:block';
				}
				?>

				<ul class="sfsi_plus_pinterest_access_token_ul" style="<?php echo esc_attr($style); ?>">

					<li class="sfsi_plus_pinterest_options">

						<div class="sfsi_plus_pinterest_accesstoken_container">

							<div class="sfsi_premium_pinterestFields">

								<div>
									<label>
										<?php _e('Enter Pinterest App ID', 'ultimate-social-media-plus'); ?>
									</label>
									<input name="sfsi_plus_pinterest_appid" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_pinterest_appid'])) ?
																															esc_attr($option4['sfsi_plus_pinterest_appid']) : ''; ?>" />
								</div>
								<div>
									<label>
										<?php _e('Enter Pinterest App secret', 'ultimate-social-media-plus'); ?>
									</label>
									<input name="sfsi_plus_pinterest_appsecret" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_pinterest_appsecret'])) ?
																																esc_attr($option4['sfsi_plus_pinterest_appsecret']) : ''; ?>" />
								</div>
								<div>
									<label>
										<?php _e('Enter Pinterest Redirect Url', 'ultimate-social-media-plus'); ?>
									</label>
									<input name="sfsi_plus_pinterest_appurl" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_pinterest_appurl'])) ?
																															esc_attr($option4['sfsi_plus_pinterest_appurl']) : ''; ?>" />
								</div>

								<div class="sfsi_plus_codeGenerateButton">
									<p><?php _e( 'After entering App ID, App secret & App Redirect url click on Generate Code button.', 'ultimate-social-media-plus'); ?></p>
									<a href="javascript:">
										<?php _e("Generate Code", 'ultimate-social-media-plus'); ?>
									</a>
								</div>

								<div class="sfsi_plus_paccessTokenGenerateButton">

									<p><?php _e('After getting code, enter the code & click on Generate Token button.', 'ultimate-social-media-plus'); ?></p>
									<label><?php _e('Enter Code', 'ultimate-social-media-plus'); ?></label>

									<input name="sfsi_plus_pinterest_code" class="input_facebook" type="text" />
									<a href="javascript:">
										<?php _e("Generate Token", 'ultimate-social-media-plus'); ?>
									</a>
								</div>

							</div>

							<div class="sfsi_plus_pinterest_button">

								<label><?php _e('Enter Pinterest access token:', 'ultimate-social-media-plus'); ?></label>

								<input name="sfsi_plus_pinterest_access_token" class="input_pinterest_acnt_pins_accesstoken" type="text" value="<?php echo (isset($option4['sfsi_plus_pinterest_access_token']) && $option4['sfsi_plus_pinterest_access_token'] != '') ? esc_attr($option4['sfsi_plus_pinterest_access_token']) : ''; ?>" />

								<!--<a href="javascript:" id="sfsi_plus_verfify_pinterest_accesstoken">
										<?php _e('Verify access token', 'ultimate-social-media-plus'); ?>
									</a>-->
							</div>
							<!--<span id="sfsi_plus_pinterest_token_notice"></span>
								<span id="sfsi_plus_pinterest_notice"></span>-->
						</div>

						<div class="sfsi_premium_pinterest_instructions_accesstoken">
							<p><?php _e('To get your access token please follow these steps', 'ultimate-social-media-plus'); ?></p>
							<!--(a video showing the steps can be found at <a href="https://youtu.be/ZSVPj1KhTPY" target="_blank">https://youtu.be/ZSVPj1KhTPY): </a>-->
							<ul style="width: 100% !important;">
								<li><?php _e('1. Go to', 'ultimate-social-media-plus'); ?> <a href="https://developers.pinterest.com" target="_blank">https://developers.pinterest.com</a></li>
								<li><?php _e('2. Login, click on “Apps” and then click on “Create App”', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('3. Give your App a name (e.g. “USM Pinterest app”)', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('4. Give your App Description', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('5. Click on “Create”', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('6. Once in the App main menu scroll down to “Platforms”', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('7. Under “Web” enter your website URL under “Redirect URIs” (For example: https://www.example.com), press “enter” and click “Save”', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('8. Copy the “App id”, “App secret” and “App Redirect url” you entered in app & paste into plugin. After that click on the “Generate Code” button.', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('9. Authorize your app to access your account info by clicking the “OK” button.', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('10. Now you will be redirected to the redirect url (which you entered during app creation) and find your code at the end of this url (For example: http://your-website.com/#?code=< your code >)', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('11. Copy the code, paste it into the plugin. After that click on the “Generate Token” button', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('12. Then you’ll see your access token automatically entered below "Enter Pinterest access token:"', 'ultimate-social-media-plus'); ?></li>
							</ul>
						</div>

					</li>

				</ul>

			</div>
		</div>
		<!-- END PINIT ICON COUNT SECTION-->

		<!-- INSTAGRAM ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_instagram_section">
			<div class="radio_section">
				<input name="sfsi_plus_instagram_countsDisplay" <?php echo ($option4['sfsi_plus_instagram_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="Instagram">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/instagram.png" alt="instagram" />
							<span><?php echo $counts['instagram_count']; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_instagram_countsFrom" <?php echo ($option4['sfsi_plus_instagram_countsFrom'] == 'followers') ? 'checked="true"' : ''; ?> type="radio" value="followers" class="styled" />
						<?php _e('Retrieve the number of Instagram followers', 'ultimate-social-media-plus'); ?>
					</li>
					<li class="instagram_userLi" style="<?php echo ($option4['sfsi_plus_instagram_countsFrom'] == 'manual') ? 'display:none;' : ''; ?>">
						<div class="sfsi_premium_instagramFields">
							<div>
								<label>
									<?php _e('Enter Instagram User name', 'ultimate-social-media-plus'); ?>
								</label>
								<input name="sfsi_plus_instagram_User" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_instagram_User'])) ?
																														esc_attr($option4['sfsi_plus_instagram_User']) : ''; ?>" />
							</div>
							<div style="<?php echo $sfsi_premium_install_after_13?'display:none;':''; ?>" >
								<label>
									<?php _e('Enter Instagram Client Id', 'ultimate-social-media-plus'); ?>
								</label>
								<input name="sfsi_plus_instagram_clientid" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_instagram_clientid'])) ?
																															esc_attr($option4['sfsi_plus_instagram_clientid']) : ''; ?>" />
							</div>
							<div style="<?php echo $sfsi_premium_install_after_13?'display:none;':''; ?>" >
								<label>
									<?php _e('Enter Instagram Redirect Url', 'ultimate-social-media-plus'); ?>
								</label>
								<input name="sfsi_plus_instagram_appurl" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_instagram_appurl'])) ?
																														esc_attr($option4['sfsi_plus_instagram_appurl']) : ''; ?>" />
							</div>

							<div class="sfsi_plus_tokenGenerateButton" style="<?php echo $sfsi_premium_install_after_13?'display:none;':''; ?>" >
								<p><?php _e('For generate your app token you need to enter your "Client Id" and "Redirect Url".', 'ultimate-social-media-plus'); ?></p>
								<a href="javascript:">
									<?php _e("Generate Token", 'ultimate-social-media-plus'); ?>
								</a>
							</div>

							<div style="<?php echo $sfsi_premium_install_after_13?'display:none;':''; ?>" >
								<label>
									<?php _e('Enter Instagram Token', 'ultimate-social-media-plus'); ?>
								</label>
								<input name="sfsi_plus_instagram_token" class="input_facebook" type="text" value="<?php echo (isset($option4['sfsi_plus_instagram_token'])) ?
																														esc_attr($option4['sfsi_plus_instagram_token']) : ''; ?>" />
							</div>
						</div>
						<div style="<?php echo $sfsi_premium_install_after_13?'display:none;':''; ?>"  class="sfsi_premium_instagramInstruction">
							<p><?php _e('To see how this process works please follow these steps:', 'ultimate-social-media-plus'); ?></p>
							<ul>
								<li><?php _e('Go to ', 'ultimate-social-media-plus'); ?>&nbsp;<a href="https://developers.facebook.com/docs/instagram-platform/instagram-api-with-instagram-login/create-a-meta-app-with-instagram" target="_blank">https://developers.facebook.com/</a></li>
								<li><?php _e('Login and then click on “Register Your Application” to get to the “Manage Clients” section. On there click on the “Register a new client” button', 'ultimate-social-media-plus'); ?>.</li>
								<li><?php _e('Fill out the form and make sure that the “Redirect url” is valid and uncheck the “Disable Implicit oAuth” under the security tab', 'ultimate-social-media-plus'); ?>.</li>
								<li><?php _e('Then click on “Register” button', 'ultimate-social-media-plus'); ?>.</li>
								<li><?php _e('Copy the “Client id” and “Redirect url” you entered into the plugin. Also enter your Instagram User name. After that click on the “Generate token” button', 'ultimate-social-media-plus'); ?>.</li>
								<li><?php _e('Authorize your app to access your account info by clicking the “Authorize” button', 'ultimate-social-media-plus'); ?>.</li>
								<li><?php _e('Now you will be redirected to the redirect url (which you entered during app creation) and find your access token at the end of this url (For example: http://your-website.com/#access_token=< your access token>)', 'ultimate-social-media-plus'); ?></li>
								<li><?php _e('Copy the access token, paste it into the plugin and click on “Save”', 'ultimate-social-media-plus'); ?>.</li>
							</ul>
						</div>
					</li>
					<li>
						<input name="sfsi_plus_instagram_countsFrom" <?php echo ($option4['sfsi_plus_instagram_countsFrom'] == 'manual') ? 'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_instagram_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_instagram_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_instagram_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_instagram_countsFrom'] == 'followers') ? 'display:none;' : ''; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END INSTAGRAM ICON COUNT SECTION-->

		<!-- ADDTHIS ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_share_section">
			<div class="radio_section">
				<input name="sfsi_plus_shares_countsDisplay" <?php echo ($option4['sfsi_plus_shares_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="Share">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/share.png" alt="Share" />
							<span><?php echo isset($counts['share_count']) ? $counts['share_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_shares_countsFrom" <?php echo ($option4['sfsi_plus_shares_countsFrom'] == 'shares') ? 'checked="true"' : ''; ?> type="radio" value="shares" class="styled" />
						<?php _e('Retrieve the number of shares', 'ultimate-social-media-plus'); ?>
					</li>
					<li>
						<input name="sfsi_plus_shares_countsFrom" <?php echo ($option4['sfsi_plus_shares_countsFrom'] == 'manual') ? 'checked="true"' : ''; ?> type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_shares_manualCounts" type="text" class="input" value="<?php echo ($option4['sfsi_plus_shares_manualCounts'] != '') ?  esc_attr($option4['sfsi_plus_shares_manualCounts']) : ''; ?>" style="<?php echo ($option4['sfsi_plus_shares_countsFrom'] == 'shares') ? 'display:none;' : ''; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END ADDTHIS ICON COUNT SECTION-->

		<!-- HOUZZ ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_houzz_section">
			<div class="radio_section">
				<input name="sfsi_plus_houzz_countsDisplay" <?php echo (isset($option4['sfsi_plus_houzz_countsDisplay']) && $option4['sfsi_plus_houzz_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="Houzz">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/houzz.png" alt="Houzz" />
							<span><?php echo isset($counts['houzz_count']) ? $counts['houzz_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_houzz_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_houzz_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_houzz_manualCounts']) && $option4['sfsi_plus_houzz_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_houzz_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END HOUZZ ICON COUNT SECTION-->

		<!-- snapchat ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_snapchat_section">
			<div class="radio_section">
				<input name="sfsi_plus_snapchat_countsDisplay" <?php echo (isset($option4['sfsi_plus_snapchat_countsDisplay']) && $option4['sfsi_plus_snapchat_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="snapchat">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/snapchat.png" alt="snapchat" />
							<span><?php echo isset($counts['snapchat_count']) ? $counts['snapchat_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_snapchat_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_snapchat_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_snapchat_manualCounts']) && $option4['sfsi_plus_snapchat_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_snapchat_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END snapchat ICON COUNT SECTION-->

        <!-- ria ICON COUNT SECTION-->
        <div class="sfsiplus_specify_counts sfsiplus_ria_section">
            <div class="radio_section">
                <input name="sfsi_plus_ria_countsDisplay" <?php echo (isset($option4['sfsi_plus_ria_countsDisplay']) && $option4['sfsi_plus_ria_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="RateItAll">
                            <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/ria.png" alt="RateItAll" />
                            <span><?php echo isset($counts['ria_count']) ? $counts['ria_count'] : 0; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>
                        <input name="sfsi_plus_ria_countsFrom" checked="true" type="radio" value="manual" class="styled" />
                        <label class="high_prb">
                            <?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
                        </label>
                        <input name="sfsi_plus_ria_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_ria_manualCounts']) && $option4['sfsi_plus_ria_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_ria_manualCounts']) : '20'; ?>" />
                    </li>
                </ul>
            </div>
        </div>
        <!-- END ria ICON COUNT SECTION-->

        <!-- inha ICON COUNT SECTION-->
        <div class="sfsiplus_specify_counts sfsiplus_inha_section">
            <div class="radio_section">
                <input name="sfsi_plus_inha_countsDisplay" <?php echo (isset($option4['sfsi_plus_inha_countsDisplay']) && $option4['sfsi_plus_inha_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
            </div>
            <div class="social_icon_like">
                <ul class="like_icon">
                    <li>
                        <a title="IncreasingHappiness">
                            <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/inha.png" alt="IncreasingHappiness" />
                            <span><?php echo isset($counts['inha_count']) ? $counts['inha_count'] : 0; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="listing">
                <ul>
                    <li>
                        <input name="sfsi_plus_inha_countsFrom" checked="true" type="radio" value="manual" class="styled" />
                        <label class="high_prb">
                            <?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
                        </label>
                        <input name="sfsi_plus_inha_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_inha_manualCounts']) && $option4['sfsi_plus_inha_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_inha_manualCounts']) : '20'; ?>" />
                    </li>
                </ul>
            </div>
        </div>
        <!-- END inha ICON COUNT SECTION-->

        <!-- whatsapp ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_whatsapp_section">
			<div class="radio_section">
				<input name="sfsi_plus_whatsapp_countsDisplay" <?php echo (isset($option4['sfsi_plus_whatsapp_countsDisplay']) && $option4['sfsi_plus_whatsapp_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="whatsapp">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/whatsapp.png" alt="whatsapp" />
							<span><?php echo isset($counts['whatsapp_count']) ? $counts['whatsapp_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_whatsapp_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_whatsapp_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_whatsapp_manualCounts']) && $option4['sfsi_plus_whatsapp_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_whatsapp_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END whatsapp ICON COUNT SECTION-->

		<!-- skype ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_skype_section">
			<div class="radio_section">
				<input name="sfsi_plus_skype_countsDisplay" <?php echo (isset($option4['sfsi_plus_skype_countsDisplay']) && $option4['sfsi_plus_skype_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="skype">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/skype.png" alt="skype" />
							<span><?php echo isset($counts['skype_count']) ? $counts['skype_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_skype_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_skype_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_skype_manualCounts']) && $option4['sfsi_plus_skype_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_skype_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END skype ICON COUNT SECTION-->
		<!-- Phone ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_phone_section">
			<div class="radio_section">
				<input name="sfsi_plus_phone_countsDisplay" <?php echo (isset($option4['sfsi_plus_phone_countsDisplay']) && $option4['sfsi_plus_phone_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="phone">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/phone.png" alt="phone" />
							<span><?php echo isset($counts['phone_count']) ? $counts['phone_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_phone_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_phone_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_phone_manualCounts']) && $option4['sfsi_plus_phone_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_phone_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END phone ICON COUNT SECTION-->

		<!-- vimeo ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_vimeo_section">
			<div class="radio_section">
				<input name="sfsi_plus_vimeo_countsDisplay" <?php echo (isset($option4['sfsi_plus_vimeo_countsDisplay']) && $option4['sfsi_plus_vimeo_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="vimeo">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/vimeo.png" alt="vimeo" />
							<span><?php echo isset($counts['vimeo_count']) ? $counts['vimeo_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_vimeo_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_vimeo_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_vimeo_manualCounts']) && $option4['sfsi_plus_vimeo_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_vimeo_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END vimeo ICON COUNT SECTION-->

		<!-- soundcloud ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_soundcloud_section">
			<div class="radio_section">
				<input name="sfsi_plus_soundcloud_countsDisplay" <?php echo (isset($option4['sfsi_plus_soundcloud_countsDisplay']) && $option4['sfsi_plus_soundcloud_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="soundcloud">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/soundcloud.png" alt="soundcloud" />
							<span><?php echo isset($counts['soundcloud_count']) ? $counts['soundcloud_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_soundcloud_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_soundcloud_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_soundcloud_manualCounts']) && $option4['sfsi_plus_soundcloud_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_soundcloud_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END soundcloud ICON COUNT SECTION-->

		<!-- yummly ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_yummly_section">
			<div class="radio_section">
				<input name="sfsi_plus_yummly_countsDisplay" <?php echo (isset($option4['sfsi_plus_yummly_countsDisplay']) && $option4['sfsi_plus_yummly_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="yummly">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/yummly.png" alt="yummly" />
							<span><?php echo isset($counts['yummly_count']) ? $counts['yummly_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>

			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_yummly_countsFrom" checked="true" <?php echo ($option4['sfsi_plus_yummly_countsFrom'] == 'share') ? 'checked="true"' : ''; ?> type="radio" value="share" class="styled" />
							<?php _e('Retrieve the number of yummly counts', 'ultimate-social-media-plus'); ?>
					</li>
					<li>
						<input name="sfsi_plus_yummly_countsFrom" checked="true" type="radio" value="manual" class="styled" <?php echo ($option4['sfsi_plus_yummly_countsFrom'] == 'manual') ? 'checked="true"' : ''; ?>  />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_yummly_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_yummly_manualCounts']) && $option4['sfsi_plus_yummly_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_yummly_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END yummly ICON COUNT SECTION-->

		<!-- flickr ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_flickr_section">
			<div class="radio_section">
				<input name="sfsi_plus_flickr_countsDisplay" <?php echo (isset($option4['sfsi_plus_flickr_countsDisplay']) && $option4['sfsi_plus_flickr_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="flickr">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/flickr.png" alt="flickr" />
							<span><?php echo isset($counts['flickr_count']) ? $counts['flickr_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_flickr_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_flickr_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_flickr_manualCounts']) && $option4['sfsi_plus_flickr_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_flickr_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END flickr ICON COUNT SECTION-->

		<!-- reddit ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_reddit_section">
			<div class="radio_section">
				<input name="sfsi_plus_reddit_countsDisplay" <?php echo (isset($option4['sfsi_plus_reddit_countsDisplay']) && $option4['sfsi_plus_reddit_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="reddit">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/reddit.png" alt="reddit" />
							<span><?php echo isset($counts['reddit_count']) ? $counts['reddit_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_reddit_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_reddit_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_reddit_manualCounts']) && $option4['sfsi_plus_reddit_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_reddit_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END reddit ICON COUNT SECTION-->

		<!-- tumblr ICON COUNT SECTION-->
		<div class="sfsiplus_specify_counts sfsiplus_tumblr_section">
			<div class="radio_section">
				<input name="sfsi_plus_tumblr_countsDisplay" <?php echo (isset($option4['sfsi_plus_tumblr_countsDisplay']) && $option4['sfsi_plus_tumblr_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="tumblr">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/tumblr.png" alt="tumblr" />
							<span><?php echo isset($counts['tumblr_count']) ? $counts['tumblr_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_tumblr_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_tumblr_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_tumblr_manualCounts']) && $option4['sfsi_plus_tumblr_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_tumblr_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<!-- END tumblr ICON COUNT SECTION-->

		<div class="sfsiplus_specify_counts sfsiplus_fbmessenger_section">

			<div class="radio_section">
				<input name="sfsi_plus_fbmessenger_countsDisplay" <?php echo (isset($option4['sfsi_plus_fbmessenger_countsDisplay']) && $option4['sfsi_plus_fbmessenger_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>

			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="tumblr">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_fbmessenger.png" alt="fbmessenger" />
							<span><?php echo isset($counts['fbmessenger_count']) ? $counts['fbmessenger_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_fbmessenger_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_fbmessenger_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_fbmessenger_manualCounts']) && $option4['sfsi_plus_fbmessenger_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_fbmessenger_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<div class="sfsiplus_specify_counts sfsiplus_gab_section">

			<div class="radio_section">
				<input name="sfsi_plus_gab_countsDisplay" <?php echo (isset($option4['sfsi_plus_gab_countsDisplay']) && $option4['sfsi_plus_gab_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>

			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="gab">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_gab.png" alt="gab" />
							<span><?php echo isset($counts['gab_count']) ? $counts['gab_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_gab_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>

						<input name="sfsi_plus_gab_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_gab_manualCounts']) && $option4['sfsi_plus_gab_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_gab_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>
		<div class="sfsiplus_specify_counts sfsiplus_mix_section">
			<div class="radio_section">
				<input name="sfsi_plus_mix_countsDisplay" <?php echo (isset($option4['sfsi_plus_mix_countsDisplay']) && $option4['sfsi_plus_mix_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="mix">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_mix.png" alt="mix" />
							<span><?php echo isset($counts['mix_count']) ? $counts['mix_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_mix_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_mix_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_mix_manualCounts']) && $option4['sfsi_plus_mix_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_mix_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="sfsiplus_specify_counts sfsiplus_ok_section">
			<div class="radio_section">
				<input name="sfsi_plus_ok_countsDisplay" <?php echo (isset($option4['sfsi_plus_ok_countsDisplay']) && $option4['sfsi_plus_ok_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="ok">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_ok.png" alt="ok" />
							<span><?php echo isset($counts['ok_count']) ? $counts['ok_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_ok_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_ok_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_ok_manualCounts']) && $option4['sfsi_plus_ok_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_ok_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="sfsiplus_specify_counts sfsiplus_vk_section">
			<div class="radio_section">
				<input name="sfsi_plus_vk_countsDisplay" <?php echo (isset($option4['sfsi_plus_vk_countsDisplay']) && $option4['sfsi_plus_vk_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="vk">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_vk.png" alt="vk" />
							<span><?php echo isset($counts['vk_count']) ? $counts['vk_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_vk_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_vk_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_vk_manualCounts']) && $option4['sfsi_plus_vk_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_vk_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="sfsiplus_specify_counts sfsiplus_telegram_section">
			<div class="radio_section">
				<input name="sfsi_plus_telegram_countsDisplay" <?php echo (isset($option4['sfsi_plus_telegram_countsDisplay']) && $option4['sfsi_plus_telegram_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="telegram">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_telegram.png" alt="telegram" />
							<span><?php echo isset($counts['telegram_count']) ? $counts['telegram_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_telegram_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_telegram_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_telegram_manualCounts']) && $option4['sfsi_plus_telegram_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_telegram_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="sfsiplus_specify_counts sfsiplus_weibo_section">
			<div class="radio_section">
				<input name="sfsi_plus_weibo_countsDisplay" <?php echo (isset($option4['sfsi_plus_weibo_countsDisplay']) && $option4['sfsi_plus_weibo_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="weibo">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_weibo.png" alt="weibo" />
							<span><?php echo isset($counts['weibo_count']) ? $counts['weibo_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_weibo_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_weibo_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_weibo_manualCounts']) && $option4['sfsi_plus_weibo_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_weibo_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="sfsiplus_specify_counts sfsiplus_wechat_section">
			<div class="radio_section">
				<input name="sfsi_plus_wechat_countsDisplay" <?php echo (isset($option4['sfsi_plus_wechat_countsDisplay']) && $option4['sfsi_plus_wechat_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="wechat">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_wechat.png" alt="wechat" />
							<span><?php echo isset($counts['wechat_count']) ? $counts['wechat_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_wechat_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_wechat_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_wechat_manualCounts']) && $option4['sfsi_plus_wechat_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_wechat_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="sfsiplus_specify_counts sfsiplus_xing_section">
			<div class="radio_section">
				<input name="sfsi_plus_xing_countsDisplay" <?php echo (isset($option4['sfsi_plus_xing_countsDisplay']) && $option4['sfsi_plus_xing_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="xing">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_xing.png" alt="xing" />
							<span><?php echo isset($counts['xing_count']) ? $counts['xing_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_xing_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_xing_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_xing_manualCounts']) && $option4['sfsi_plus_xing_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_xing_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="sfsiplus_specify_counts sfsiplus_copylink_section">
			<div class="radio_section">
				<input name="sfsi_plus_copylink_countsDisplay" <?php echo (isset($option4['sfsi_plus_copylink_countsDisplay']) && $option4['sfsi_plus_copylink_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="copylink">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_copylink.png" alt="copylink" />
							<span><?php echo isset($counts['copylink_count']) ? $counts['copylink_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_copylink_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_copylink_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_copylink_manualCounts']) && $option4['sfsi_plus_copylink_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_copylink_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="sfsiplus_specify_counts sfsiplus_mastodon_section">
			<div class="radio_section">
				<input name="sfsi_plus_mastodon_countsDisplay" <?php echo (isset($option4['sfsi_plus_mastodon_countsDisplay']) && $option4['sfsi_plus_mastodon_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="social_icon_like">
				<ul class="like_icon">
					<li>
						<a title="mastodon">
							<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/default/default_mastodon.png" alt="mastodon" />
							<span><?php echo isset($counts['mastodon_count']) ? $counts['mastodon_count'] : 0; ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="listing">
				<ul>
					<li>
						<input name="sfsi_plus_mastodon_countsFrom" checked="true" type="radio" value="manual" class="styled" />
						<label class="high_prb">
							<?php _e('Enter the figure manually', 'ultimate-social-media-plus'); ?>
						</label>
						<input name="sfsi_plus_mastodon_manualCounts" type="text" class="input" value="<?php echo (isset($option4['sfsi_plus_mastodon_manualCounts']) && $option4['sfsi_plus_mastodon_manualCounts'] != '') ? esc_attr($option4['sfsi_plus_mastodon_manualCounts']) : '20'; ?>" />
					</li>
				</ul>
			</div>
		</div>

		<div class="row sfsi_min_count_section sfsiplus_specify_counts">
			<h4>
				<?php _e("State the minimum number of counts so that count bubble get displayed:", 'ultimate-social-media-plus'); ?>
			</h4>

			<input class="small" min="0" name="sfsi_plus_min_display_counts" type="number" value="<?php echo $min_count; ?>" />
		</div>
		<!-- <div class="row sfsi_premiumm_count_location sfsiplus_specify_counts">
			<h4>
				<?php _e("For which icons do you want count to be displayed:", 'ultimate-social-media-plus'); ?>
			</h4>
		</div>
		<div class="row" style="float:left;clear:both">
			<div class="radio_section" style="display:inline-block;">
				<input name="sfsi_plus_ok_countsDisplay" <?php echo (isset($option4['sfsi_plus_ok_countsDisplay']) && $option4['sfsi_plus_ok_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="sfsi_premium_checkbox_center_align">
				Round Icons
			</div>
		</div>
		<div class="row" style="float:left;clear:both" >
			<div class="radio_section" style="display:inline-block;">
				<input name="sfsi_plus_ok_countsDisplay" <?php echo (isset($option4['sfsi_plus_ok_countsDisplay']) && $option4['sfsi_plus_ok_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="sfsi_premium_checkbox_center_align">
				Rectangular Icons
			</div>
		</div>
		<div class="row" style="float:left;clear:both" >
			<div class="radio_section" style="display:inline-block;">
				<input name="sfsi_plus_ok_countsDisplay" <?php echo (isset($option4['sfsi_plus_ok_countsDisplay']) && $option4['sfsi_plus_ok_countsDisplay'] == 'yes') ? 'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
			</div>
			<div class="sfsi_premium_checkbox_center_align">
				Responsive Icons
			</div>
		</div> -->

	</div>
	<!-- END show/hide counts for all icons section -->

	<!-- SAVE BUTTON SECTION   -->
	<div class="save_button">
		<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/ajax-loader.gif" alt="loader" class="loader-img" />
		<?php $nonce = wp_create_nonce("update_plus_step4"); ?>
		<a href="javascript:;" id="sfsi_plus_save4" title="Save" data-nonce="<?php echo $nonce; ?>">
			<?php _e('Save', 'ultimate-social-media-plus'); ?>
		</a>
	</div>
	<!-- END SAVE BUTTON SECTION   -->
	<a class="sfsiColbtn closeSec" href="javascript:;">
		<?php _e('Collapse area', 'ultimate-social-media-plus'); ?>
	</a>
	<label class="closeSec"></label>
	<!-- ERROR AND SUCCESS MESSAGE AREA-->
	<p class="red_txt errorMsg" style="display:none;"> </p>
    <p class="green_txt sucMsg" style="display:none;"> </p>
	<div class="clear"></div>
</div>
<!-- END Section 4 "Do you want to display "counts" next to your icons?"  -->
