<?php
/* save all option to options table in database using ajax */
/* save settings for section 1 */
add_action( 'wp_ajax_plus_updateSrcn1', 'sfsi_plus_options_updater1' );
function sfsi_plus_options_updater1() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step1" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$option1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );

	$sfsi_plus_rss_display       = isset( $_POST["sfsi_plus_rss_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_rss_display"] ) : 'no';
	$sfsi_plus_email_display     = isset( $_POST["sfsi_plus_email_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_display"] ) : 'no';
	$sfsi_plus_facebook_display  = isset( $_POST["sfsi_plus_facebook_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_facebook_display"] ) : 'no';
	$sfsi_plus_twitter_display   = isset( $_POST["sfsi_plus_twitter_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_display"] ) : 'no';
	$sfsi_plus_share_display     = isset( $_POST["sfsi_plus_share_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_share_display"] ) : 'no';
	$sfsi_plus_youtube_display   = isset( $_POST["sfsi_plus_youtube_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_youtube_display"] ) : 'no';
	$sfsi_plus_pinterest_display = isset( $_POST["sfsi_plus_pinterest_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_pinterest_display"] ) : 'no';
	$sfsi_plus_linkedin_display  = isset( $_POST["sfsi_plus_linkedin_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_linkedin_display"] ) : 'no';
	$sfsi_plus_instagram_display = isset( $_POST["sfsi_plus_instagram_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_display"] ) : 'no';
	$sfsi_plus_threads_display   = isset( $_POST["sfsi_plus_threads_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_threads_display"] ) : 'no';
	$sfsi_plus_bluesky_display   = isset( $_POST["sfsi_plus_bluesky_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_bluesky_display"] ) : 'no';
	$sfsi_plus_houzz_display     = isset( $_POST["sfsi_plus_houzz_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_houzz_display"] ) : 'no';

	$sfsi_plus_snapchat_display   = isset( $_POST["sfsi_plus_snapchat_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_snapchat_display"] ) : 'no';
	$sfsi_plus_whatsapp_display   = isset( $_POST["sfsi_plus_whatsapp_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_whatsapp_display"] ) : 'no';
	$sfsi_plus_phone_display      = isset( $_POST["sfsi_plus_phone_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_phone_display"] ) : 'no';
	$sfsi_plus_skype_display      = isset( $_POST["sfsi_plus_skype_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_skype_display"] ) : 'no';
	$sfsi_plus_vimeo_display      = isset( $_POST["sfsi_plus_vimeo_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_vimeo_display"] ) : 'no';
	$sfsi_plus_soundcloud_display = isset( $_POST["sfsi_plus_soundcloud_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_soundcloud_display"] ) : 'no';
	$sfsi_plus_yummly_display     = isset( $_POST["sfsi_plus_yummly_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_yummly_display"] ) : 'no';
	$sfsi_plus_flickr_display     = isset( $_POST["sfsi_plus_flickr_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_flickr_display"] ) : 'no';
	$sfsi_plus_reddit_display     = isset( $_POST["sfsi_plus_reddit_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_reddit_display"] ) : 'no';
	$sfsi_plus_tumblr_display     = isset( $_POST["sfsi_plus_tumblr_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_tumblr_display"] ) : 'no';

	$sfsi_plus_fbmessenger_display = isset( $_POST["sfsi_plus_fbmessenger_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_fbmessenger_display"] ) : 'no';
	$sfsi_plus_gab_display         = isset( $_POST["sfsi_plus_gab_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_gab_display"] ) : 'no';
	$sfsi_plus_mix_display         = isset( $_POST["sfsi_plus_mix_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_mix_display"] ) : 'no';
	$sfsi_plus_ok_display          = isset( $_POST["sfsi_plus_ok_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_ok_display"] ) : 'no';
	$sfsi_plus_telegram_display    = isset( $_POST["sfsi_plus_telegram_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegram_display"] ) : 'no';
	$sfsi_plus_vk_display          = isset( $_POST["sfsi_plus_vk_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_vk_display"] ) : 'no';
	$sfsi_plus_weibo_display       = isset( $_POST["sfsi_plus_weibo_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_weibo_display"] ) : 'no';
	$sfsi_plus_wechat_display      = isset( $_POST["sfsi_plus_wechat_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_wechat_display"] ) : 'no';
	$sfsi_plus_xing_display        = isset( $_POST["sfsi_plus_xing_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_xing_display"] ) : 'no';
	$sfsi_plus_copylink_display    = isset( $_POST["sfsi_plus_copylink_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_copylink_display"] ) : 'no';
	$sfsi_plus_mastodon_display    = isset( $_POST["sfsi_plus_mastodon_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_mastodon_display"] ) : 'no';
	$sfsi_plus_ria_display         = isset( $_POST["sfsi_plus_ria_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_ria_display"] ) : 'no';
	$sfsi_plus_inha_display        = isset( $_POST["sfsi_plus_inha_display"] ) ? sanitize_text_field( $_POST["sfsi_plus_inha_display"] ) : 'no';

	$sfsi_plus_icons_onmobile = isset( $_POST["sfsi_plus_icons_onmobile"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_onmobile"] ) : 'no';

	$sfsi_plus_rss_mobiledisplay       = isset( $_POST["sfsi_plus_rss_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_rss_mobiledisplay"] ) : 'no';
	$sfsi_plus_email_mobiledisplay     = isset( $_POST["sfsi_plus_email_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_mobiledisplay"] ) : 'no';
	$sfsi_plus_facebook_mobiledisplay  = isset( $_POST["sfsi_plus_facebook_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_facebook_mobiledisplay"] ) : 'no';
	$sfsi_plus_twitter_mobiledisplay   = isset( $_POST["sfsi_plus_twitter_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_mobiledisplay"] ) : 'no';
	$sfsi_plus_share_mobiledisplay     = isset( $_POST["sfsi_plus_share_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_share_mobiledisplay"] ) : 'no';
	$sfsi_plus_youtube_mobiledisplay   = isset( $_POST["sfsi_plus_youtube_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_youtube_mobiledisplay"] ) : 'no';
	$sfsi_plus_pinterest_mobiledisplay = isset( $_POST["sfsi_plus_pinterest_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_pinterest_mobiledisplay"] ) : 'no';
	$sfsi_plus_instagram_mobiledisplay = isset( $_POST["sfsi_plus_instagram_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_mobiledisplay"] ) : 'no';
	$sfsi_plus_threads_mobiledisplay   = isset( $_POST["sfsi_plus_threads_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_threads_mobiledisplay"] ) : 'no';
	$sfsi_plus_bluesky_mobiledisplay   = isset( $_POST["sfsi_plus_bluesky_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_bluesky_mobiledisplay"] ) : 'no';
	$sfsi_plus_houzz_mobiledisplay     = isset( $_POST["sfsi_plus_houzz_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_houzz_mobiledisplay"] ) : 'no';

	$sfsi_plus_snapchat_mobiledisplay   = isset( $_POST["sfsi_plus_snapchat_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_snapchat_mobiledisplay"] ) : 'no';
	$sfsi_plus_whatsapp_mobiledisplay   = isset( $_POST["sfsi_plus_whatsapp_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_whatsapp_mobiledisplay"] ) : 'no';
	$sfsi_plus_phone_mobiledisplay      = isset( $_POST["sfsi_plus_phone_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_phone_mobiledisplay"] ) : 'no';
	$sfsi_plus_skype_mobiledisplay      = isset( $_POST["sfsi_plus_skype_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_skype_mobiledisplay"] ) : 'no';
	$sfsi_plus_vimeo_mobiledisplay      = isset( $_POST["sfsi_plus_vimeo_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_vimeo_mobiledisplay"] ) : 'no';
	$sfsi_plus_soundcloud_mobiledisplay = isset( $_POST["sfsi_plus_soundcloud_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_soundcloud_mobiledisplay"] ) : 'no';
	$sfsi_plus_yummly_mobiledisplay     = isset( $_POST["sfsi_plus_yummly_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_yummly_mobiledisplay"] ) : 'no';
	$sfsi_plus_flickr_mobiledisplay     = isset( $_POST["sfsi_plus_flickr_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_flickr_mobiledisplay"] ) : 'no';
	$sfsi_plus_reddit_mobiledisplay     = isset( $_POST["sfsi_plus_reddit_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_reddit_mobiledisplay"] ) : 'no';
	$sfsi_plus_tumblr_mobiledisplay     = isset( $_POST["sfsi_plus_tumblr_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_tumblr_mobiledisplay"] ) : 'no';

	$sfsi_plus_fbmessenger_mobiledisplay = isset( $_POST["sfsi_plus_fbmessenger_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_fbmessenger_mobiledisplay"] ) : 'no';
	$sfsi_plus_gab_mobiledisplay         = isset( $_POST["sfsi_plus_gab_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_gab_mobiledisplay"] ) : 'no';
	$sfsi_plus_mix_mobiledisplay         = isset( $_POST["sfsi_plus_mix_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_tumblr_mobiledisplay"] ) : 'no';
	$sfsi_plus_ok_mobiledisplay          = isset( $_POST["sfsi_plus_ok_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_ok_mobiledisplay"] ) : 'no';
	$sfsi_plus_telegram_mobiledisplay    = isset( $_POST["sfsi_plus_telegram_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegram_mobiledisplay"] ) : 'no';
	$sfsi_plus_vk_mobiledisplay          = isset( $_POST["sfsi_plus_vk_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_vk_mobiledisplay"] ) : 'no';
	$sfsi_plus_weibo_mobiledisplay       = isset( $_POST["sfsi_plus_weibo_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_weibo_mobiledisplay"] ) : 'no';
	$sfsi_plus_wechat_mobiledisplay      = isset( $_POST["sfsi_plus_wechat_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_wechat_mobiledisplay"] ) : 'no';
	$sfsi_plus_xing_mobiledisplay        = isset( $_POST["sfsi_plus_xing_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_xing_mobiledisplay"] ) : 'no';
	$sfsi_plus_copylink_mobiledisplay    = isset( $_POST["sfsi_plus_copylink_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_copylink_mobiledisplay"] ) : 'no';
	$sfsi_plus_mastodon_mobiledisplay    = isset( $_POST["sfsi_plus_mastodon_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_mastodon_mobiledisplay"] ) : 'no';
	$sfsi_plus_ria_mobiledisplay         = isset( $_POST["sfsi_plus_ria_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_ria_mobiledisplay"] ) : 'no';
	$sfsi_plus_inha_mobiledisplay        = isset( $_POST["sfsi_plus_inha_mobiledisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_inha_mobiledisplay"] ) : 'no';

	$sfsi_plus_linkedin_mobiledisplay = isset( $_POST["sfsi_plus_linkedin_mobiledisplay"] ) ? $_POST["sfsi_plus_linkedin_mobiledisplay"] : 'no';

	$sfsi_custom_icons = isset( $option1['sfsi_custom_files'] ) ? $option1['sfsi_custom_files'] : '';

	$sfsi_custom_desktop_icons = isset( $_POST['sfsi_custom_desktop_icons'] ) ? serialize( is_array( $_POST['sfsi_custom_desktop_icons'] ) ? array_map( function ( $url ) {
		return strpos( $url, '?' ) > 0 ? substr( $url, 0, strpos( $url, '?' ) ) : $url;
	}, $_POST['sfsi_custom_desktop_icons'] ) : $_POST['sfsi_custom_desktop_icons'] ) : '';

	$sfsi_custom_mobile_icons = isset( $_POST['sfsi_custom_mobile_icons'] ) ? serialize( is_array( $_POST['sfsi_custom_mobile_icons'] ) ? array_map( function ( $url ) {
		return strpos( $url, '?' ) > 0 ? substr( $url, 0, strpos( $url, '?' ) ) : $url;
	}, $_POST['sfsi_custom_mobile_icons'] ) : $_POST['sfsi_custom_mobile_icons'] ) : '';

	$up_option1 = array(
		'sfsi_plus_rss_display'       => sanitize_text_field( $sfsi_plus_rss_display ),
		'sfsi_plus_email_display'     => sanitize_text_field( $sfsi_plus_email_display ),
		'sfsi_plus_facebook_display'  => sanitize_text_field( $sfsi_plus_facebook_display ),
		'sfsi_plus_twitter_display'   => sanitize_text_field( $sfsi_plus_twitter_display ),
		'sfsi_plus_share_display'     => sanitize_text_field( $sfsi_plus_share_display ),
		'sfsi_plus_youtube_display'   => sanitize_text_field( $sfsi_plus_youtube_display ),
		'sfsi_plus_pinterest_display' => sanitize_text_field( $sfsi_plus_pinterest_display ),
		'sfsi_plus_linkedin_display'  => sanitize_text_field( $sfsi_plus_linkedin_display ),
		'sfsi_plus_instagram_display' => sanitize_text_field( $sfsi_plus_instagram_display ),
		'sfsi_plus_threads_display'   => sanitize_text_field( $sfsi_plus_threads_display ),
		'sfsi_plus_bluesky_display'   => sanitize_text_field( $sfsi_plus_bluesky_display ),
		'sfsi_plus_houzz_display'     => sanitize_text_field( $sfsi_plus_houzz_display ),

		'sfsi_plus_snapchat_display'   => sanitize_text_field( $sfsi_plus_snapchat_display ),
		'sfsi_plus_whatsapp_display'   => sanitize_text_field( $sfsi_plus_whatsapp_display ),
		'sfsi_plus_phone_display'      => sanitize_text_field( $sfsi_plus_phone_display ),
		'sfsi_plus_skype_display'      => sanitize_text_field( $sfsi_plus_skype_display ),
		'sfsi_plus_vimeo_display'      => sanitize_text_field( $sfsi_plus_vimeo_display ),
		'sfsi_plus_soundcloud_display' => sanitize_text_field( $sfsi_plus_soundcloud_display ),
		'sfsi_plus_yummly_display'     => sanitize_text_field( $sfsi_plus_yummly_display ),
		'sfsi_plus_flickr_display'     => sanitize_text_field( $sfsi_plus_flickr_display ),
		'sfsi_plus_reddit_display'     => sanitize_text_field( $sfsi_plus_reddit_display ),
		'sfsi_plus_tumblr_display'     => sanitize_text_field( $sfsi_plus_tumblr_display ),

		'sfsi_plus_fbmessenger_display' => sanitize_text_field( $sfsi_plus_fbmessenger_display ),
		'sfsi_plus_gab_display'         => sanitize_text_field( $sfsi_plus_gab_display ),
		'sfsi_plus_mix_display'         => sanitize_text_field( $sfsi_plus_mix_display ),
		'sfsi_plus_ok_display'          => sanitize_text_field( $sfsi_plus_ok_display ),
		'sfsi_plus_telegram_display'    => sanitize_text_field( $sfsi_plus_telegram_display ),
		'sfsi_plus_vk_display'          => sanitize_text_field( $sfsi_plus_vk_display ),
		'sfsi_plus_weibo_display'       => sanitize_text_field( $sfsi_plus_weibo_display ),
		'sfsi_plus_wechat_display'      => sanitize_text_field( $sfsi_plus_wechat_display ),
		'sfsi_plus_xing_display'        => sanitize_text_field( $sfsi_plus_xing_display ),
		'sfsi_plus_copylink_display'    => sanitize_text_field( $sfsi_plus_copylink_display ),
		'sfsi_plus_mastodon_display'    => sanitize_text_field( $sfsi_plus_mastodon_display ),
		'sfsi_plus_ria_display'         => sanitize_text_field( $sfsi_plus_ria_display ),
		'sfsi_plus_inha_display'        => sanitize_text_field( $sfsi_plus_inha_display ),

		'sfsi_plus_icons_onmobile'          => sanitize_text_field( $sfsi_plus_icons_onmobile ),
		'sfsi_plus_rss_mobiledisplay'       => sanitize_text_field( $sfsi_plus_rss_mobiledisplay ),
		'sfsi_plus_email_mobiledisplay'     => sanitize_text_field( $sfsi_plus_email_mobiledisplay ),
		'sfsi_plus_facebook_mobiledisplay'  => sanitize_text_field( $sfsi_plus_facebook_mobiledisplay ),
		'sfsi_plus_twitter_mobiledisplay'   => sanitize_text_field( $sfsi_plus_twitter_mobiledisplay ),
		'sfsi_plus_share_mobiledisplay'     => sanitize_text_field( $sfsi_plus_share_mobiledisplay ),
		'sfsi_plus_youtube_mobiledisplay'   => sanitize_text_field( $sfsi_plus_youtube_mobiledisplay ),
		'sfsi_plus_pinterest_mobiledisplay' => sanitize_text_field( $sfsi_plus_pinterest_mobiledisplay ),
		'sfsi_plus_linkedin_mobiledisplay'  => sanitize_text_field( $sfsi_plus_linkedin_mobiledisplay ),
		'sfsi_plus_instagram_mobiledisplay' => sanitize_text_field( $sfsi_plus_instagram_mobiledisplay ),
		'sfsi_plus_threads_mobiledisplay'   => sanitize_text_field( $sfsi_plus_threads_mobiledisplay ),
		'sfsi_plus_bluesky_mobiledisplay'   => sanitize_text_field( $sfsi_plus_bluesky_mobiledisplay ),
		'sfsi_plus_houzz_mobiledisplay'     => sanitize_text_field( $sfsi_plus_houzz_mobiledisplay ),

		'sfsi_plus_snapchat_mobiledisplay'   => sanitize_text_field( $sfsi_plus_snapchat_mobiledisplay ),
		'sfsi_plus_whatsapp_mobiledisplay'   => sanitize_text_field( $sfsi_plus_whatsapp_mobiledisplay ),
		'sfsi_plus_phone_mobiledisplay'      => sanitize_text_field( $sfsi_plus_phone_mobiledisplay ),
		'sfsi_plus_skype_mobiledisplay'      => sanitize_text_field( $sfsi_plus_skype_mobiledisplay ),
		'sfsi_plus_vimeo_mobiledisplay'      => sanitize_text_field( $sfsi_plus_vimeo_mobiledisplay ),
		'sfsi_plus_soundcloud_mobiledisplay' => sanitize_text_field( $sfsi_plus_soundcloud_mobiledisplay ),
		'sfsi_plus_yummly_mobiledisplay'     => sanitize_text_field( $sfsi_plus_yummly_mobiledisplay ),
		'sfsi_plus_flickr_mobiledisplay'     => sanitize_text_field( $sfsi_plus_flickr_mobiledisplay ),
		'sfsi_plus_reddit_mobiledisplay'     => sanitize_text_field( $sfsi_plus_reddit_mobiledisplay ),
		'sfsi_plus_tumblr_mobiledisplay'     => sanitize_text_field( $sfsi_plus_tumblr_mobiledisplay ),

		'sfsi_plus_fbmessenger_mobiledisplay' => sanitize_text_field( $sfsi_plus_fbmessenger_mobiledisplay ),
		'sfsi_plus_gab_mobiledisplay'         => sanitize_text_field( $sfsi_plus_gab_mobiledisplay ),
		'sfsi_plus_mix_mobiledisplay'         => sanitize_text_field( $sfsi_plus_mix_mobiledisplay ),
		'sfsi_plus_ok_mobiledisplay'          => sanitize_text_field( $sfsi_plus_ok_mobiledisplay ),
		'sfsi_plus_telegram_mobiledisplay'    => sanitize_text_field( $sfsi_plus_telegram_mobiledisplay ),
		'sfsi_plus_vk_mobiledisplay'          => sanitize_text_field( $sfsi_plus_vk_mobiledisplay ),
		'sfsi_plus_weibo_mobiledisplay'       => sanitize_text_field( $sfsi_plus_weibo_mobiledisplay ),
		'sfsi_plus_wechat_mobiledisplay'      => sanitize_text_field( $sfsi_plus_wechat_mobiledisplay ),
		'sfsi_plus_xing_mobiledisplay'        => sanitize_text_field( $sfsi_plus_xing_mobiledisplay ),
		'sfsi_plus_copylink_mobiledisplay'    => sanitize_text_field( $sfsi_plus_copylink_mobiledisplay ),
		'sfsi_plus_mastodon_mobiledisplay'    => sanitize_text_field( $sfsi_plus_mastodon_mobiledisplay ),
		'sfsi_plus_ria_mobiledisplay'         => sanitize_text_field( $sfsi_plus_ria_mobiledisplay ),
		'sfsi_plus_inha_mobiledisplay'        => sanitize_text_field( $sfsi_plus_inha_mobiledisplay ),

		'sfsi_custom_files'         => $sfsi_custom_icons,
		'sfsi_custom_desktop_icons' => sanitize_text_field( $sfsi_custom_desktop_icons ),
		'sfsi_custom_mobile_icons'  => sanitize_text_field( $sfsi_custom_mobile_icons ),
	);

	update_option( 'sfsi_premium_section1_options', serialize( $up_option1 ) );

	header( 'Content-Type: application/json' );
	echo json_encode( array( "success" ) );
	exit;
}

/* save settings for section 2 */
add_action( 'wp_ajax_plus_updateSrcn2', 'sfsi_plus_options_updater2' );
function sfsi_plus_options_updater2() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step2" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$sfsi_plus_rss_url   = isset( $_POST["sfsi_plus_rss_url"] ) ? esc_url( trim( $_POST["sfsi_plus_rss_url"] ) ) : '';
	$sfsi_plus_rss_icons = isset( $_POST["sfsi_plus_rss_icons"] ) ? sanitize_text_field( $_POST["sfsi_plus_rss_icons"] ) : 'email';

	$sfsi_plus_email_icons_functions        = isset( $_POST["sfsi_plus_email_icons_functions"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_icons_functions"] ) : 'sf';
	$sfsi_plus_email_icons_contact          = isset( $_POST["sfsi_plus_email_icons_contact"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_icons_contact"] ) : '';
	$sfsi_plus_email_icons_pageurl          = isset( $_POST["sfsi_plus_email_icons_pageurl"] ) ? esc_url( trim( $_POST["sfsi_plus_email_icons_pageurl"] ) ) : '';
	$sfsi_plus_email_icons_mailchimp_apikey = isset( $_POST["sfsi_plus_email_icons_mailchimp_apikey"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_icons_mailchimp_apikey"] ) : '';
	$sfsi_plus_email_icons_mailchimp_listid = isset( $_POST["sfsi_plus_email_icons_mailchimp_listid"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_icons_mailchimp_listid"] ) : '';

	$sfsi_plus_email_icons_subject_line  = isset( $_POST["sfsi_plus_email_icons_subject_line"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_icons_subject_line"] ) : '${title}';
	$sfsi_plus_email_icons_email_content = isset( $_POST["sfsi_plus_email_icons_email_content"] ) ? $_POST["sfsi_plus_email_icons_email_content"] : 'Check out this article «${title}»: ${link}';

	$sfsi_plus_email_icons_email_content = htmlentities( $sfsi_plus_email_icons_email_content, ENT_QUOTES, 'UTF-8' );

	$sfsi_plus_facebookPage_option  = isset( $_POST["sfsi_plus_facebookPage_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_facebookPage_option"] ) : 'no';
	$sfsi_plus_facebookPage_url     = isset( $_POST["sfsi_plus_facebookPage_url"] ) ? esc_url( trim( $_POST["sfsi_plus_facebookPage_url"] ) ) : '';
	$sfsi_plus_facebookLike_option  = isset( $_POST["sfsi_plus_facebookLike_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_facebookLike_option"] ) : 'no';
	$sfsi_plus_facebookShare_option = isset( $_POST["sfsi_plus_facebookShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_facebookShare_option"] ) : 'no';
	//$sfsi_plus_facebookFollow_option= isset($_POST["sfsi_plus_facebookFollow_option"]) ? $_POST["sfsi_plus_facebookFollow_option"] : 'no';
	//$sfsi_plus_facebookProfile_url    = isset($_POST["sfsi_plus_facebookProfile_url"]) ? $_POST["sfsi_plus_facebookProfile_url"] : '';

	$sfsi_plus_blueskyShare_option = isset( $_POST["sfsi_plus_blueskyShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_blueskyShare_option"] ) : 'no';
	$sfsi_plus_threadsShare_option = isset( $_POST["sfsi_plus_threadsShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_threadsShare_option"] ) : 'no';

	$sfsi_plus_twitter_followme            = isset( $_POST["sfsi_plus_twitter_followme"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_followme"] ) : 'no';
	$sfsi_plus_twitter_followUserName      = isset( $_POST["sfsi_plus_twitter_followUserName"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_twitter_followUserName"] ) ) : '';
	$sfsi_plus_twitter_aboutPage           = isset( $_POST["sfsi_plus_twitter_aboutPage"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_aboutPage"] ) : 'no';
	$sfsi_plus_twitter_page                = isset( $_POST["sfsi_plus_twitter_page"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_page"] ) : 'no';
	$sfsi_plus_twitter_pageURL             = isset( $_POST["sfsi_plus_twitter_pageURL"] ) ? esc_url( trim( $_POST["sfsi_plus_twitter_pageURL"] ) ) : '';
	$sfsi_plus_twitter_aboutPageText       = isset( $_POST["sfsi_plus_twitter_aboutPageText"] )
		? sanitize_text_field( $_POST["sfsi_plus_twitter_aboutPageText"] )
		: 'Hey check out this cool site I found';
	$sfsi_plus_twitter_twtAddCard          = isset( $_POST["sfsi_plus_twitter_twtAddCard"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_twtAddCard"] ) : 'no';
	$sfsi_plus_twitter_twtCardType         = isset( $_POST["sfsi_plus_twitter_twtCardType"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_twtCardType"] ) : 'summary';
	$sfsi_plus_twitter_card_twitter_handle = isset( $_POST["sfsi_plus_twitter_card_twitter_handle"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_card_twitter_handle"] ) : $sfsi_plus_twitter_followUserName;

	$sfsi_plus_youtube_pageUrl = isset( $_POST["sfsi_plus_youtube_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_youtube_pageUrl"] ) ) : '';
	$sfsi_plus_youtube_page    = isset( $_POST["sfsi_plus_youtube_page"] ) ? sanitize_text_field( $_POST["sfsi_plus_youtube_page"] ) : 'no';
	$sfsi_plus_youtube_follow  = isset( $_POST["sfsi_plus_youtube_follow"] ) ? sanitize_text_field( $_POST["sfsi_plus_youtube_follow"] ) : 'no';

	$sfsi_plus_pinterest_page     = isset( $_POST["sfsi_plus_pinterest_page"] ) ? sanitize_text_field( $_POST["sfsi_plus_pinterest_page"] ) : 'no';
	$sfsi_plus_pinterest_pageUrl  = isset( $_POST["sfsi_plus_pinterest_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_pinterest_pageUrl"] ) ) : '';
	$sfsi_plus_pinterest_pingBlog = isset( $_POST["sfsi_plus_pinterest_pingBlog"] ) ? sanitize_text_field( $_POST["sfsi_plus_pinterest_pingBlog"] ) : 'no';

	$sfsi_plus_instagram_pageUrl = isset( $_POST["sfsi_plus_instagram_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_instagram_pageUrl"] ) ) : '';
	$sfsi_plus_ria_pageUrl       = isset( $_POST["sfsi_plus_ria_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_ria_pageUrl"] ) ) : '';
	$sfsi_plus_inha_pageUrl      = isset( $_POST["sfsi_plus_inha_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_inha_pageUrl"] ) ) : '';

	$sfsi_plus_linkedin_page      = isset( $_POST["sfsi_plus_linkedin_page"] ) ? sanitize_text_field( $_POST["sfsi_plus_linkedin_page"] ) : 'no';
	$sfsi_plus_linkedin_pageURL   = isset( $_POST["sfsi_plus_linkedin_pageURL"] ) ? esc_url( trim( $_POST["sfsi_plus_linkedin_pageURL"] ) ) : '';
	$sfsi_plus_linkedin_follow    = isset( $_POST["sfsi_plus_linkedin_follow"] ) ? sanitize_text_field( $_POST["sfsi_plus_linkedin_follow"] ) : 'no';
	$sfsi_plus_linkedin_SharePage = isset( $_POST["sfsi_plus_linkedin_SharePage"] ) ? sanitize_text_field( $_POST["sfsi_plus_linkedin_SharePage"] ) : 'no';

	$sfsi_plus_linkedin_followCompany      = isset( $_POST["sfsi_plus_linkedin_followCompany"] )
		? intval( trim( $_POST["sfsi_plus_linkedin_followCompany"] ) )
		: '';
	$sfsi_plus_linkedin_recommendBusines   = isset( $_POST["sfsi_plus_linkedin_recommendBusines"] )
		? sanitize_text_field( $_POST["sfsi_plus_linkedin_recommendBusines"] )
		: 'no';
	$sfsi_plus_linkedin_recommendCompany   = isset( $_POST["sfsi_plus_linkedin_recommendCompany"] )
		? sanitize_text_field( trim( $_POST["sfsi_plus_linkedin_recommendCompany"] ) )
		: '';
	$sfsi_plus_linkedin_recommendProductId = isset( $_POST["sfsi_plus_linkedin_recommendProductId"] )
		? intval( trim( $_POST["sfsi_plus_linkedin_recommendProductId"] ) )
		: '';

	$sfsi_plus_youtubeusernameorid = isset( $_POST["sfsi_plus_youtubeusernameorid"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_youtubeusernameorid"] ) ) : '';
	$sfsi_plus_ytube_user          = isset( $_POST["sfsi_plus_ytube_user"] ) ? sanitize_text_field( $_POST["sfsi_plus_ytube_user"] ) : '';
	$sfsi_plus_ytube_chnlid        = isset( $_POST["sfsi_plus_ytube_chnlid"] ) ? sanitize_text_field( $_POST["sfsi_plus_ytube_chnlid"] ) : '';

	/*
     * Escape custom icons url
     */
	$esacpedUrls = array();
	if (
		isset( $_POST["sfsi_plus_custom_links"] ) &&
		! empty( $_POST["sfsi_plus_custom_links"] )
	) {

		$sfsi_plus_CustomIcon_links = $_POST["sfsi_plus_custom_links"];
		foreach ( $sfsi_plus_CustomIcon_links as $key => $sfsi_pluscustomIconUrl ) {
			$esacpedUrls[ $key ] = $sfsi_pluscustomIconUrl;
		}
	}

	$sfsi_plus_CustomIcon_links = isset( $_POST["sfsi_plus_custom_links"] ) ? serialize( $esacpedUrls ) : '';

	// $sfsi_plus_houzzVisit_option    = isset($_POST["sfsi_plus_houzzVisit_option"]) ? trim($_POST["sfsi_plus_houzzVisit_option"]) : 'no';

	$sfsi_plus_houzz_pageUrl = isset( $_POST["sfsi_plus_houzz_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_houzz_pageUrl"] ) ) : '';

	// $sfsi_plus_houzzShare_option    = isset($_POST["sfsi_plus_houzzShare_option"]) ? trim($_POST["sfsi_plus_houzzShare_option"]) : 'no';

	// $sfsi_plus_houzz_websiteId        = isset($_POST["sfsi_plus_houzz_websiteId"]) ? trim($_POST["sfsi_plus_houzz_websiteId"]) : '';

	$sfsi_plus_snapchat_pageUrl    = isset( $_POST["sfsi_plus_snapchat_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_snapchat_pageUrl"] ) ) : '';
	$sfsi_plus_whatsapp_message    = isset( $_POST["sfsi_plus_whatsapp_message"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_whatsapp_message"] ) ) : '';
	$sfsi_plus_my_whatsapp_number  = isset( $_POST["sfsi_plus_my_whatsapp_number"] ) ? trim( $_POST["sfsi_plus_my_whatsapp_number"] ) : '';
	$sfsi_plus_whatsapp_number     = isset( $_POST["sfsi_plus_whatsapp_number"] ) ? trim( $_POST["sfsi_plus_whatsapp_number"] ) : '';
	$sfsi_plus_whatsapp_share_page = isset( $_POST["sfsi_plus_whatsapp_share_page"] ) ? trim( $_POST["sfsi_plus_whatsapp_share_page"] ) : '${title} ${link}';

	$sfsi_plus_skype_options = isset( $_POST["sfsi_plus_skype_options"] ) ? sanitize_text_field( $_POST["sfsi_plus_skype_options"] ) : 'call';
	$sfsi_plus_skype_pageUrl = isset( $_POST["sfsi_plus_skype_pageUrl"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_skype_pageUrl"] ) ) : '';

	$sfsi_plus_vimeo_pageUrl      = isset( $_POST["sfsi_plus_vimeo_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_vimeo_pageUrl"] ) ) : '';
	$sfsi_plus_soundcloud_pageUrl = isset( $_POST["sfsi_plus_soundcloud_pageUrl"] ) ? trim( $_POST["sfsi_plus_soundcloud_pageUrl"] ) : '';

	$sfsi_plus_yummlyVisit_option = isset( $_POST["sfsi_plus_yummlyVisit_option"] ) ? trim( $_POST["sfsi_plus_yummlyVisit_option"] ) : 'no';

	$sfsi_plus_yummly_pageUrl     = isset( $_POST["sfsi_plus_yummly_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_yummly_pageUrl"] ) ) : '';
	$sfsi_plus_yummlyShare_option = isset( $_POST["sfsi_plus_yummlyShare_option"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_yummlyShare_option"] ) ) : 'no';


	$sfsi_plus_flickr_pageUrl    = isset( $_POST["sfsi_plus_flickr_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_flickr_pageUrl"] ) ) : '';
	$sfsi_plus_reddit_pageUrl    = isset( $_POST["sfsi_plus_reddit_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_reddit_pageUrl"] ) ) : '';
	$sfsi_plus_tumblr_pageUrl    = isset( $_POST["sfsi_plus_tumblr_pageUrl"] ) ? esc_url( trim( $_POST["sfsi_plus_tumblr_pageUrl"] ) ) : '';
	$sfsi_plus_whatsapp_url_type = isset( $_POST["sfsi_plus_whatsapp_url_type"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_whatsapp_url_type"] ) ) : '';
	$sfsi_plus_reddit_url_type   = isset( $_POST["sfsi_plus_reddit_url_type"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_reddit_url_type"] ) ) : '';

	$sfsi_plus_fbmessengerContact_option = isset( $_POST["sfsi_plus_fbmessengerContact_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_fbmessengerContact_option"] ) : 'no';
	$sfsi_plus_fbmessengerContact_url    = isset( $_POST["sfsi_plus_fbmessengerContact_url"] ) ? esc_url( trim( $_POST["sfsi_plus_fbmessengerContact_url"] ) ) : '';
	$sfsi_plus_fbmessengerShare_option   = isset( $_POST["sfsi_plus_fbmessengerShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_fbmessengerShare_option"] ) : 'no';
	$sfsi_plus_fbmessengerShare_app_id   = isset( $_POST["sfsi_plus_fbmessengerShare_app_id"] ) ? sanitize_text_field( $_POST["sfsi_plus_fbmessengerShare_app_id"] ) : '';

	$sfsi_plus_mixVisit_option = isset( $_POST["sfsi_plus_mixVisit_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_mixVisit_option"] ) : 'no';
	$sfsi_plus_mixVisit_url    = isset( $_POST["sfsi_plus_mixVisit_url"] ) ? esc_url( trim( $_POST["sfsi_plus_mixVisit_url"] ) ) : '';
	$sfsi_plus_mixShare_option = isset( $_POST["sfsi_plus_mixShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_mixShare_option"] ) : 'no';

	$sfsi_plus_okVisit_option = isset( $_POST["sfsi_plus_okVisit_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_okVisit_option"] ) : 'no';
	$sfsi_plus_okVisit_url    = isset( $_POST["sfsi_plus_okVisit_url"] ) ? esc_url( trim( $_POST["sfsi_plus_okVisit_url"] ) ) : '';

	$sfsi_plus_okSubscribe_option = isset( $_POST["sfsi_plus_okSubscribe_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_okSubscribe_option"] ) : 'no';
	$sfsi_plus_okSubscribe_userid = isset( $_POST["sfsi_plus_okSubscribe_userid"] ) ? sanitize_text_field( $_POST["sfsi_plus_okSubscribe_userid"] ) : '';

	$sfsi_plus_okLike_option = isset( $_POST["sfsi_plus_okLike_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_okLike_option"] ) : 'no';

	$sfsi_plus_telegramShare_option   = isset( $_POST["sfsi_plus_telegramShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegramShare_option"] ) : 'no';
	$sfsi_plus_telegramMessage_option = isset( $_POST["sfsi_plus_telegramMessage_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegramMessage_option"] ) : 'no';
	$sfsi_plus_telegram_message       = isset( $_POST["sfsi_plus_telegram_message"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegram_message"] ) : '';
	$sfsi_plus_telegram_username      = isset( $_POST["sfsi_plus_telegram_username"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegram_username"] ) : '';

	$sfsi_plus_vkVisit_option = isset( $_POST["sfsi_plus_vkVisit_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_vkVisit_option"] ) : 'no';
	$sfsi_plus_vkShare_option = isset( $_POST["sfsi_plus_vkShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_vkShare_option"] ) : 'no';
	$sfsi_plus_vkLike_option  = isset( $_POST["sfsi_plus_vkLike_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_vkLike_option"] ) : 'no';
	$sfsi_plus_vkVisit_url    = isset( $_POST["sfsi_plus_vkVisit_url"] ) ? esc_url( trim( $_POST["sfsi_plus_vkVisit_url"] ) ) : '';


	/*$sfsi_plus_vkFollow_option     = isset($_POST["sfsi_plus_vkFollow_option"]) ? $_POST["sfsi_plus_vkFollow_option"] : 'no';
    $sfsi_plus_vkFollow_url  = isset($_POST["sfsi_plus_vkFollow_url"]) ? $_POST["sfsi_plus_vkFollow_url"] : '';*/


	$sfsi_plus_weiboVisit_option    = isset( $_POST["sfsi_plus_weiboVisit_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_weiboVisit_option"] ) : 'no';
	$sfsi_plus_weiboShare_option    = isset( $_POST["sfsi_plus_weiboShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_weiboShare_option"] ) : 'no';
	$sfsi_plus_weiboLike_option     = isset( $_POST["sfsi_plus_weiboLike_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_weiboLike_option"] ) : 'no';
	$sfsi_plus_weiboVisit_url       = isset( $_POST["sfsi_plus_weiboVisit_url"] ) ? esc_url( trim( $_POST["sfsi_plus_weiboVisit_url"] ) ) : '';
	$sfsi_plus_wechatFollow_option  = isset( $_POST["sfsi_plus_wechatFollow_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_wechatFollow_option"] ) : 'no';
	$sfsi_plus_wechatShare_option   = isset( $_POST["sfsi_plus_wechatShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_wechatShare_option"] ) : 'no';
	$sfsi_premium_wechat_scan_image = isset( $_POST["sfsi_premium_wechat_scan_image"] ) ? sanitize_text_field( $_POST["sfsi_premium_wechat_scan_image"] ) : '';

	$sfsi_plus_mastodonVisit_option = isset( $_POST["sfsi_plus_mastodonVisit_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_mastodonVisit_option"] ) : 'no';
	$sfsi_plus_mastodonShare_option = isset( $_POST["sfsi_plus_mastodonShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_mastodonShare_option"] ) : 'no';
	$sfsi_plus_mastodonVisit_url    = isset( $_POST["sfsi_plus_mastodonVisit_url"] ) ? esc_url( trim( $_POST["sfsi_plus_mastodonVisit_url"] ) ) : '';

	$sfsi_plus_xingVisit_option  = isset( $_POST["sfsi_plus_xingVisit_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_xingVisit_option"] ) : 'no';
	$sfsi_plus_xingShare_option  = isset( $_POST["sfsi_plus_xingShare_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_xingShare_option"] ) : 'no';
	$sfsi_plus_xingFollow_option = isset( $_POST["sfsi_plus_xingFollow_option"] ) ? sanitize_text_field( $_POST["sfsi_plus_xingFollow_option"] ) : 'no';

	$sfsi_plus_xingVisit_url  = isset( $_POST["sfsi_plus_xingVisit_url"] ) ? esc_url( trim( $_POST["sfsi_plus_xingVisit_url"] ) ) : '';
	$sfsi_plus_xingFollow_url = isset( $_POST["sfsi_plus_xingFollow_url"] ) ? esc_url( trim( $_POST["sfsi_plus_xingFollow_url"] ) ) : '';

	$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );

	$up_option2 = array(
		'sfsi_plus_rss_url'   => esc_url( $sfsi_plus_rss_url ),
		'sfsi_rss_blogName'   => '',
		'sfsi_rss_blogEmail'  => '',
		'sfsi_plus_rss_icons' => sanitize_text_field( $sfsi_plus_rss_icons ),
		'sfsi_plus_email_url' => esc_url( $option2['sfsi_plus_email_url'] ),

		'sfsi_plus_email_icons_functions'        => sanitize_text_field( $sfsi_plus_email_icons_functions ),
		'sfsi_plus_email_icons_contact'          => sanitize_text_field( $sfsi_plus_email_icons_contact ),
		'sfsi_plus_email_icons_pageurl'          => esc_url( $sfsi_plus_email_icons_pageurl ),
		'sfsi_plus_email_icons_mailchimp_apikey' => sanitize_text_field( $sfsi_plus_email_icons_mailchimp_apikey ),
		'sfsi_plus_email_icons_mailchimp_listid' => sanitize_text_field( $sfsi_plus_email_icons_mailchimp_listid ),

		'sfsi_plus_email_icons_subject_line'    => sanitize_text_field( $sfsi_plus_email_icons_subject_line ),
		'sfsi_plus_email_icons_email_content'   => $sfsi_plus_email_icons_email_content,

		/* facebook buttons options */
		'sfsi_plus_facebookPage_option'         => sanitize_text_field( $sfsi_plus_facebookPage_option ),
		'sfsi_plus_facebookPage_url'            => esc_url( $sfsi_plus_facebookPage_url ),
		'sfsi_plus_facebookLike_option'         => sanitize_text_field( $sfsi_plus_facebookLike_option ),
		'sfsi_plus_facebookShare_option'        => sanitize_text_field( $sfsi_plus_facebookShare_option ),
		//'sfsi_plus_facebookFollow_option' => sanitize_text_field($sfsi_plus_facebookFollow_option),
		//'sfsi_plus_facebookProfile_url'       => esc_url($sfsi_plus_facebookProfile_url),
		'sfsi_plus_threadsShare_option'         => sanitize_text_field( $sfsi_plus_threadsShare_option ),
		'sfsi_plus_blueskyShare_option'         => sanitize_text_field( $sfsi_plus_blueskyShare_option ),

		/* Twitter buttons options */
		'sfsi_plus_twitter_followme'            => sanitize_text_field( $sfsi_plus_twitter_followme ),
		'sfsi_plus_twitter_followUserName'      => sanitize_text_field( $sfsi_plus_twitter_followUserName ),
		'sfsi_plus_twitter_aboutPage'           => sanitize_text_field( $sfsi_plus_twitter_aboutPage ),
		'sfsi_plus_twitter_page'                => sanitize_text_field( $sfsi_plus_twitter_page ),
		'sfsi_plus_twitter_pageURL'             => esc_url( $sfsi_plus_twitter_pageURL ),
		'sfsi_plus_twitter_aboutPageText'       => sanitize_text_field( $sfsi_plus_twitter_aboutPageText ),
		'sfsi_plus_twitter_twtAddCard'          => sanitize_text_field( $sfsi_plus_twitter_twtAddCard ),
		'sfsi_plus_twitter_twtCardType'         => sanitize_text_field( $sfsi_plus_twitter_twtCardType ),
		'sfsi_plus_twitter_card_twitter_handle' => sanitize_text_field( $sfsi_plus_twitter_card_twitter_handle ),


		/* youtube options */
		'sfsi_plus_youtube_pageUrl'             => esc_url( $sfsi_plus_youtube_pageUrl ),
		'sfsi_plus_youtube_page'                => sanitize_text_field( $sfsi_plus_youtube_page ),
		'sfsi_plus_youtube_follow'              => sanitize_text_field( $sfsi_plus_youtube_follow ),
		'sfsi_plus_ytube_user'                  => sanitize_text_field( $sfsi_plus_ytube_user ),
		'sfsi_plus_youtubeusernameorid'         => sanitize_text_field( $sfsi_plus_youtubeusernameorid ),
		'sfsi_plus_ytube_chnlid'                => sanitize_text_field( $sfsi_plus_ytube_chnlid ),

		/* pinterest options */
		'sfsi_plus_pinterest_page'              => sanitize_text_field( $sfsi_plus_pinterest_page ),
		'sfsi_plus_pinterest_pageUrl'           => esc_url( $sfsi_plus_pinterest_pageUrl ),
		'sfsi_plus_pinterest_pingBlog'          => sanitize_text_field( $sfsi_plus_pinterest_pingBlog ),

		/* instagram and houzz options */
		'sfsi_plus_instagram_pageUrl'           => esc_url( $sfsi_plus_instagram_pageUrl ),
		'sfsi_plus_ria_pageUrl'                 => esc_url( $sfsi_plus_ria_pageUrl ),
		'sfsi_plus_inha_pageUrl'                => esc_url( $sfsi_plus_inha_pageUrl ),

		//'sfsi_plus_houzzVisit_option'       => sanitize_text_field($sfsi_plus_houzzVisit_option),
		'sfsi_plus_houzz_pageUrl'               => esc_url( $sfsi_plus_houzz_pageUrl ),
		// 'sfsi_plus_houzzShare_option'       => sanitize_text_field($sfsi_plus_houzzShare_option),
		// 'sfsi_plus_houzz_websiteId'         => sanitize_text_field($sfsi_plus_houzz_websiteId),

		'sfsi_plus_snapchat_pageUrl'    => esc_url( $sfsi_plus_snapchat_pageUrl ),
		'sfsi_plus_whatsapp_message'    => sanitize_text_field( $sfsi_plus_whatsapp_message ),
		'sfsi_plus_my_whatsapp_number'  => $sfsi_plus_my_whatsapp_number,
		'sfsi_plus_whatsapp_number'     => $sfsi_plus_whatsapp_number,
		'sfsi_plus_whatsapp_share_page' => $sfsi_plus_whatsapp_share_page,

		'sfsi_plus_skype_options'      => $sfsi_plus_skype_options,
		'sfsi_plus_skype_pageUrl'      => sanitize_text_field( $sfsi_plus_skype_pageUrl ),
		'sfsi_plus_vimeo_pageUrl'      => esc_url( $sfsi_plus_vimeo_pageUrl ),
		'sfsi_plus_soundcloud_pageUrl' => esc_url( $sfsi_plus_soundcloud_pageUrl ),

		'sfsi_plus_yummlyVisit_option' => sanitize_text_field( $sfsi_plus_yummlyVisit_option ),
		'sfsi_plus_yummly_pageUrl'     => esc_url( $sfsi_plus_yummly_pageUrl ),
		'sfsi_plus_yummlyShare_option' => sanitize_text_field( $sfsi_plus_yummlyShare_option ),

		'sfsi_plus_flickr_pageUrl'              => esc_url( $sfsi_plus_flickr_pageUrl ),
		'sfsi_plus_reddit_pageUrl'              => esc_url( $sfsi_plus_reddit_pageUrl ),
		'sfsi_plus_tumblr_pageUrl'              => esc_url( $sfsi_plus_tumblr_pageUrl ),
		'sfsi_plus_whatsapp_url_type'           => sanitize_text_field( $sfsi_plus_whatsapp_url_type ),
		'sfsi_plus_reddit_url_type'             => sanitize_text_field( $sfsi_plus_reddit_url_type ),

		/* linkedIn options */
		'sfsi_plus_linkedin_page'               => sanitize_text_field( $sfsi_plus_linkedin_page ),
		'sfsi_plus_linkedin_pageURL'            => esc_url( $sfsi_plus_linkedin_pageURL ),
		'sfsi_plus_linkedin_follow'             => sanitize_text_field( $sfsi_plus_linkedin_follow ),
		'sfsi_plus_linkedin_followCompany'      => intval( $sfsi_plus_linkedin_followCompany ),
		'sfsi_plus_linkedin_SharePage'          => sanitize_text_field( $sfsi_plus_linkedin_SharePage ),
		'sfsi_plus_linkedin_recommendBusines'   => sanitize_text_field( $sfsi_plus_linkedin_recommendBusines ),
		'sfsi_plus_linkedin_recommendCompany'   => sanitize_text_field( $sfsi_plus_linkedin_recommendCompany ),
		'sfsi_plus_linkedin_recommendProductId' => intval( $sfsi_plus_linkedin_recommendProductId ),

		'sfsi_plus_fbmessengerContact_option' => sanitize_text_field( $sfsi_plus_fbmessengerContact_option ),
		'sfsi_plus_fbmessengerShare_option'   => sanitize_text_field( $sfsi_plus_fbmessengerShare_option ),
		'sfsi_plus_fbmessengerContact_url'    => sanitize_text_field( $sfsi_plus_fbmessengerContact_url ),
		'sfsi_plus_fbmessengerShare_app_id'   => sanitize_text_field( $sfsi_plus_fbmessengerShare_app_id ),
		'sfsi_plus_mixVisit_option'           => sanitize_text_field( $sfsi_plus_mixVisit_option ),
		'sfsi_plus_mixVisit_url'              => sanitize_text_field( $sfsi_plus_mixVisit_url ),
		'sfsi_plus_mixShare_option'           => sanitize_text_field( $sfsi_plus_mixShare_option ),

		'sfsi_plus_okVisit_option'     => sanitize_text_field( $sfsi_plus_okVisit_option ),
		'sfsi_plus_okVisit_url'        => sanitize_text_field( $sfsi_plus_okVisit_url ),
		'sfsi_plus_okSubscribe_option' => sanitize_text_field( $sfsi_plus_okSubscribe_option ),
		'sfsi_plus_okSubscribe_userid' => sanitize_text_field( $sfsi_plus_okSubscribe_userid ),
		'sfsi_plus_okLike_option'      => sanitize_text_field( $sfsi_plus_okLike_option ),

		'sfsi_plus_telegramShare_option'   => sanitize_text_field( $sfsi_plus_telegramShare_option ),
		'sfsi_plus_telegramMessage_option' => sanitize_text_field( $sfsi_plus_telegramMessage_option ),
		'sfsi_plus_telegram_message'       => sanitize_text_field( $sfsi_plus_telegram_message ),
		'sfsi_plus_telegram_username'      => sanitize_text_field( $sfsi_plus_telegram_username ),

		'sfsi_plus_vkVisit_option' => sanitize_text_field( $sfsi_plus_vkVisit_option ),
		'sfsi_plus_vkShare_option' => sanitize_text_field( $sfsi_plus_vkShare_option ),
		'sfsi_plus_vkLike_option'  => sanitize_text_field( $sfsi_plus_vkLike_option ),
		//'sfsi_plus_vkFollow_option'     => sanitize_text_field($sfsi_plus_vkFollow_option),
		'sfsi_plus_vkVisit_url'    => sanitize_text_field( $sfsi_plus_vkVisit_url ),
		//'sfsi_plus_vkFollow_url'        => sanitize_text_field($sfsi_plus_vkFollow_url),

		'sfsi_plus_weiboVisit_option' => sanitize_text_field( $sfsi_plus_weiboVisit_option ),
		'sfsi_plus_weiboShare_option' => sanitize_text_field( $sfsi_plus_weiboShare_option ),
		'sfsi_plus_weiboLike_option'  => sanitize_text_field( $sfsi_plus_weiboLike_option ),
		'sfsi_plus_weiboVisit_url'    => sanitize_text_field( $sfsi_plus_weiboVisit_url ),

		'sfsi_plus_mastodonVisit_option' => sanitize_text_field( $sfsi_plus_mastodonVisit_option ),
		'sfsi_plus_mastodonShare_option' => sanitize_text_field( $sfsi_plus_mastodonShare_option ),
		'sfsi_plus_mastodonVisit_url'    => sanitize_text_field( $sfsi_plus_mastodonVisit_url ),

		'sfsi_plus_wechatFollow_option'  => sanitize_text_field( $sfsi_plus_wechatFollow_option ),
		'sfsi_plus_wechatShare_option'   => sanitize_text_field( $sfsi_plus_wechatShare_option ),
		'sfsi_premium_wechat_scan_image' => sanitize_text_field( $sfsi_premium_wechat_scan_image ),

		'sfsi_plus_xingVisit_option'  => sanitize_text_field( $sfsi_plus_xingVisit_option ),
		'sfsi_plus_xingShare_option'  => sanitize_text_field( $sfsi_plus_xingShare_option ),
		'sfsi_plus_xingFollow_option' => $sfsi_plus_xingFollow_option,
		'sfsi_plus_xingVisit_url'     => $sfsi_plus_xingVisit_url,
		'sfsi_plus_xingFollow_url'    => sanitize_text_field( $sfsi_plus_xingFollow_url ),

		'sfsi_plus_CustomIcon_links' => $sfsi_plus_CustomIcon_links
	);
	update_option( 'sfsi_premium_section2_options', serialize( $up_option2 ) );
	$option4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );

	$option4['sfsi_plus_youtubeusernameorid'] = sanitize_text_field( $sfsi_plus_youtubeusernameorid );
	$option4['sfsi_plus_ytube_chnlid']        = sfsi_plus_sanitize_field( $sfsi_plus_ytube_chnlid );
	update_option( 'sfsi_premium_section4_options', serialize( $option4 ) );

	header( 'Content-Type: application/json' );
	echo json_encode( array( "success" ) );
	exit;
}

/* save settings for section 3 */
add_action( 'wp_ajax_plus_updateSrcn3', 'sfsi_plus_options_updater3' );
function sfsi_plus_options_updater3() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step3" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$sfsi_plus_actvite_theme = isset( $_POST["sfsi_plus_actvite_theme"] ) ? sanitize_text_field( $_POST["sfsi_plus_actvite_theme"] ) : 'no';
	$sfsi_plus_mouseOver     = isset( $_POST["sfsi_plus_mouseOver"] ) ? sanitize_text_field( $_POST["sfsi_plus_mouseOver"] ) : 'no';

	// Effect for same icons
	$sfsi_plus_mouseOver_effect = isset( $_POST["sfsi_plus_mouseOver_effect"] ) ? sanitize_text_field( $_POST["sfsi_plus_mouseOver_effect"] ) : 'fade_in';

	$sfsi_plus_mouseOver_effect_type = isset( $_POST["sfsi_plus_mouseOver_effect_type"] ) ? sanitize_text_field( $_POST["sfsi_plus_mouseOver_effect_type"] ) : 'same_icons';

	$mouseover_other_icons_transition_effect = isset( $_POST["mouseover_other_icons_transition_effect"] ) ? sanitize_text_field( $_POST["mouseover_other_icons_transition_effect"] ) : 'noeffect';

	$sfsi_plus_mouseOver_other_icon_images = isset( $_POST["sfsi_plus_mouseOver_other_icon_images"] ) ? serialize( $_POST["sfsi_plus_mouseOver_other_icon_images"] ) : serialize( array() );

	$sfsi_plus_shuffle_icons         = isset( $_POST["sfsi_plus_shuffle_icons"] ) ? sanitize_text_field( $_POST["sfsi_plus_shuffle_icons"] ) : 'no';
	$sfsi_plus_shuffle_Firstload     = isset( $_POST["sfsi_plus_shuffle_Firstload"] ) ? sanitize_text_field( $_POST["sfsi_plus_shuffle_Firstload"] ) : 'no';
	$sfsi_plus_shuffle_interval      = isset( $_POST["sfsi_plus_shuffle_interval"] ) ? sanitize_text_field( $_POST["sfsi_plus_shuffle_interval"] ) : 'no';
	$sfsi_plus_shuffle_intervalTime  = isset( $_POST["sfsi_plus_shuffle_intervalTime"] ) ? sanitize_text_field( $_POST["sfsi_plus_shuffle_intervalTime"] ) : '';
	$sfsi_plus_specialIcon_animation = isset( $_POST["sfsi_plus_specialIcon_animation"] ) ? sanitize_text_field( $_POST["sfsi_plus_specialIcon_animation"] ) : '';
	$sfsi_plus_specialIcon_MouseOver = isset( $_POST["sfsi_plus_specialIcon_MouseOver"] ) ? sanitize_text_field( $_POST["sfsi_plus_specialIcon_MouseOver"] ) : 'no';
	$sfsi_plus_specialIcon_Firstload = isset( $_POST["sfsi_plus_specialIcon_Firstload"] ) ? sanitize_text_field( $_POST["sfsi_plus_specialIcon_Firstload"] ) : 'no';

	$sfsi_plus_specialIcon_Firstload_Icons = isset( $_POST["sfsi_plus_specialIcon_Firstload_Icons"] )
		? sanitize_text_field( $_POST["sfsi_plus_specialIcon_Firstload_Icons"] )
		: 'all';
	$sfsi_plus_specialIcon_interval        = isset( $_POST["sfsi_plus_specialIcon_interval"] )
		? sanitize_text_field( $_POST["sfsi_plus_specialIcon_interval"] )
		: 'no';
	$sfsi_plus_specialIcon_intervalTime    = isset( $_POST["sfsi_plus_specialIcon_intervalTime"] )
		? sanitize_text_field( $_POST["sfsi_plus_specialIcon_intervalTime"] )
		: '';
	$sfsi_plus_specialIcon_intervalIcons   = isset( $_POST["sfsi_plus_specialIcon_intervalIcons"] )
		? sanitize_text_field( $_POST["sfsi_plus_specialIcon_intervalIcons"] )
		: 'all';

	$sfsi_plus_specialIcon_intervalIcons = isset( $_POST["sfsi_plus_specialIcon_intervalIcons"] )
		? sanitize_text_field( $_POST["sfsi_plus_specialIcon_intervalIcons"] )
		: '';

	/* Flat color settings */
	$sfsi_plus_rss_bgColor         = isset( $_POST["sfsi_plus_rss_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_rss_bgColor"] )
		: '';
	$sfsi_plus_email_bgColor       = isset( $_POST["sfsi_plus_email_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_email_bgColor"] )
		: '';
	$sfsi_plus_facebook_bgColor    = isset( $_POST["sfsi_plus_facebook_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_facebook_bgColor"] )
		: '';
	$sfsi_plus_twitter_bgColor     = isset( $_POST["sfsi_plus_twitter_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_twitter_bgColor"] )
		: '';
	$sfsi_plus_threads_bgColor     = isset( $_POST["sfsi_plus_threads_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_threads_bgColor"] )
		: '';
	$sfsi_plus_bluesky_bgColor     = isset( $_POST["sfsi_plus_bluesky_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_bluesky_bgColor"] )
		: '';
	$sfsi_plus_share_bgColor       = isset( $_POST["sfsi_plus_share_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_share_bgColor"] )
		: '';
	$sfsi_plus_youtube_bgColor     = isset( $_POST["sfsi_plus_youtube_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_youtube_bgColor"] )
		: '';
	$sfsi_plus_pinterest_bgColor   = isset( $_POST["sfsi_plus_pinterest_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_pinterest_bgColor"] )
		: '';
	$sfsi_plus_linkedin_bgColor    = isset( $_POST["sfsi_plus_linkedin_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_linkedin_bgColor"] )
		: '';
	$sfsi_plus_instagram_bgColor   = isset( $_POST["sfsi_plus_instagram_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_instagram_bgColor"] )
		: '';
	$sfsi_plus_ria_bgColor         = isset( $_POST["sfsi_plus_ria_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_ria_bgColor"] )
		: '';
	$sfsi_plus_inha_bgColor        = isset( $_POST["sfsi_plus_inha_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_inha_bgColor"] )
		: '';
	$sfsi_plus_houzz_bgColor       = isset( $_POST["sfsi_plus_houzz_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_houzz_bgColor"] )
		: '';
	$sfsi_plus_snapchat_bgColor    = isset( $_POST["sfsi_plus_snapchat_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_snapchat_bgColor"] )
		: '';
	$sfsi_plus_whatsapp_bgColor    = isset( $_POST["sfsi_plus_whatsapp_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_whatsapp_bgColor"] )
		: '';
	$sfsi_plus_skype_bgColor       = isset( $_POST["sfsi_plus_skype_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_skype_bgColor"] )
		: '';
	$sfsi_plus_phone_bgColor       = isset( $_POST["sfsi_plus_phone_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_phone_bgColor"] )
		: '';
	$sfsi_plus_vimeo_bgColor       = isset( $_POST["sfsi_plus_vimeo_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_vimeo_bgColor"] )
		: '';
	$sfsi_plus_soundcloud_bgColor  = isset( $_POST["sfsi_plus_soundcloud_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_soundcloud_bgColor"] )
		: '';
	$sfsi_plus_yummly_bgColor      = isset( $_POST["sfsi_plus_yummly_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_yummly_bgColor"] )
		: '';
	$sfsi_plus_flickr_bgColor      = isset( $_POST["sfsi_plus_flickr_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_flickr_bgColor"] )
		: '';
	$sfsi_plus_reddit_bgColor      = isset( $_POST["sfsi_plus_reddit_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_reddit_bgColor"] )
		: '';
	$sfsi_plus_tumblr_bgColor      = isset( $_POST["sfsi_plus_tumblr_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_tumblr_bgColor"] )
		: '';
	$sfsi_plus_fbmessenger_bgColor = isset( $_POST["sfsi_plus_fbmessenger_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_fbmessenger_bgColor"] )
		: '';
	$sfsi_plus_gab_bgColor         = isset( $_POST["sfsi_plus_gab_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_gab_bgColor"] )
		: '';
	$sfsi_plus_mix_bgColor         = isset( $_POST["sfsi_plus_mix_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_mix_bgColor"] )
		: '';
	$sfsi_plus_ok_bgColor          = isset( $_POST["sfsi_plus_ok_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_ok_bgColor"] )
		: '';
	$sfsi_plus_telegram_bgColor    = isset( $_POST["sfsi_plus_telegram_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_telegram_bgColor"] )
		: '';
	$sfsi_plus_vk_bgColor          = isset( $_POST["sfsi_plus_vk_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_vk_bgColor"] )
		: '';
	$sfsi_plus_wechat_bgColor      = isset( $_POST["sfsi_plus_wechat_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_wechat_bgColor"] )
		: '';
	$sfsi_plus_weibo_bgColor       = isset( $_POST["sfsi_plus_weibo_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_weibo_bgColor"] )
		: '';
	$sfsi_plus_xing_bgColor        = isset( $_POST["sfsi_plus_xing_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_xing_bgColor"] )
		: '';
	$sfsi_plus_copylink_bgColor    = isset( $_POST["sfsi_plus_copylink_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_copylink_bgColor"] )
		: '';

	$sfsi_plus_mastodon_bgColor = isset( $_POST["sfsi_plus_mastodon_bgColor"] )
		? sanitize_text_field( $_POST["sfsi_plus_mastodon_bgColor"] )
		: '';

	/* Design and animation option  */
	$up_option3 = array(
		'sfsi_plus_actvite_theme'                           => sanitize_text_field( $sfsi_plus_actvite_theme ),
		/* animations options */
		'sfsi_plus_mouseOver'                               => sanitize_text_field( $sfsi_plus_mouseOver ),
		'sfsi_plus_mouseOver_effect'                        => sanitize_text_field( $sfsi_plus_mouseOver_effect ),
		'sfsi_plus_mouseOver_effect_type'                   => sanitize_text_field( $sfsi_plus_mouseOver_effect_type ),
		'sfsi_plus_mouseOver_other_icon_images'             => sanitize_text_field( $sfsi_plus_mouseOver_other_icon_images ),
		'sfsi_plus_mouseover_other_icons_transition_effect' => $mouseover_other_icons_transition_effect,
		'sfsi_plus_shuffle_icons'                           => sanitize_text_field( $sfsi_plus_shuffle_icons ),
		'sfsi_plus_shuffle_Firstload'                       => sanitize_text_field( $sfsi_plus_shuffle_Firstload ),
		'sfsi_plus_shuffle_interval'                        => sanitize_text_field( $sfsi_plus_shuffle_interval ),
		'sfsi_plus_shuffle_intervalTime'                    => intval( $sfsi_plus_shuffle_intervalTime ),
		'sfsi_plus_specialIcon_animation'                   => sanitize_text_field( $sfsi_plus_specialIcon_animation ),
		'sfsi_plus_specialIcon_MouseOver'                   => sanitize_text_field( $sfsi_plus_specialIcon_MouseOver ),
		'sfsi_plus_specialIcon_Firstload'                   => sanitize_text_field( $sfsi_plus_specialIcon_Firstload ),
		'sfsi_plus_specialIcon_Firstload_Icons'             => sanitize_text_field( $sfsi_plus_specialIcon_Firstload_Icons ),
		'sfsi_plus_specialIcon_interval'                    => sanitize_text_field( $sfsi_plus_specialIcon_interval ),
		'sfsi_plus_specialIcon_intervalTime'                => sanitize_text_field( $sfsi_plus_specialIcon_intervalTime ),
		'sfsi_plus_specialIcon_intervalIcons'               => sanitize_text_field( $sfsi_plus_specialIcon_intervalIcons ),

		'sfsi_plus_rss_bgColor'         => sanitize_text_field( $sfsi_plus_rss_bgColor ),
		'sfsi_plus_email_bgColor'       => sanitize_text_field( $sfsi_plus_email_bgColor ),
		'sfsi_plus_facebook_bgColor'    => sanitize_text_field( $sfsi_plus_facebook_bgColor ),
		'sfsi_plus_twitter_bgColor'     => sanitize_text_field( $sfsi_plus_twitter_bgColor ),
		'sfsi_plus_threads_bgColor'     => sanitize_text_field( $sfsi_plus_threads_bgColor ),
		'sfsi_plus_bluesky_bgColor'     => sanitize_text_field( $sfsi_plus_bluesky_bgColor ),
		'sfsi_plus_share_bgColor'       => sanitize_text_field( $sfsi_plus_share_bgColor ),
		'sfsi_plus_youtube_bgColor'     => sanitize_text_field( $sfsi_plus_youtube_bgColor ),
		'sfsi_plus_pinterest_bgColor'   => sanitize_text_field( $sfsi_plus_pinterest_bgColor ),
		'sfsi_plus_linkedin_bgColor'    => sanitize_text_field( $sfsi_plus_linkedin_bgColor ),
		'sfsi_plus_instagram_bgColor'   => sanitize_text_field( $sfsi_plus_instagram_bgColor ),
		'sfsi_plus_ria_bgColor'         => sanitize_text_field( $sfsi_plus_ria_bgColor ),
		'sfsi_plus_inha_bgColor'        => sanitize_text_field( $sfsi_plus_inha_bgColor ),
		'sfsi_plus_houzz_bgColor'       => sanitize_text_field( $sfsi_plus_houzz_bgColor ),
		'sfsi_plus_snapchat_bgColor'    => sanitize_text_field( $sfsi_plus_snapchat_bgColor ),
		'sfsi_plus_whatsapp_bgColor'    => sanitize_text_field( $sfsi_plus_whatsapp_bgColor ),
		'sfsi_plus_skype_bgColor'       => sanitize_text_field( $sfsi_plus_skype_bgColor ),
		'sfsi_plus_phone_bgColor'       => sanitize_text_field( $sfsi_plus_phone_bgColor ),
		'sfsi_plus_vimeo_bgColor'       => sanitize_text_field( $sfsi_plus_vimeo_bgColor ),
		'sfsi_plus_soundcloud_bgColor'  => sanitize_text_field( $sfsi_plus_soundcloud_bgColor ),
		'sfsi_plus_yummly_bgColor'      => sanitize_text_field( $sfsi_plus_yummly_bgColor ),
		'sfsi_plus_flickr_bgColor'      => sanitize_text_field( $sfsi_plus_flickr_bgColor ),
		'sfsi_plus_reddit_bgColor'      => sanitize_text_field( $sfsi_plus_reddit_bgColor ),
		'sfsi_plus_tumblr_bgColor'      => sanitize_text_field( $sfsi_plus_tumblr_bgColor ),
		'sfsi_plus_fbmessenger_bgColor' => sanitize_text_field( $sfsi_plus_fbmessenger_bgColor ),
		'sfsi_plus_gab_bgColor'         => sanitize_text_field( $sfsi_plus_gab_bgColor ),
		'sfsi_plus_mix_bgColor'         => sanitize_text_field( $sfsi_plus_mix_bgColor ),
		'sfsi_plus_ok_bgColor'          => sanitize_text_field( $sfsi_plus_ok_bgColor ),
		'sfsi_plus_telegram_bgColor'    => sanitize_text_field( $sfsi_plus_telegram_bgColor ),
		'sfsi_plus_vk_bgColor'          => sanitize_text_field( $sfsi_plus_vk_bgColor ),
		'sfsi_plus_wechat_bgColor'      => sanitize_text_field( $sfsi_plus_wechat_bgColor ),
		'sfsi_plus_weibo_bgColor'       => sanitize_text_field( $sfsi_plus_weibo_bgColor ),
		'sfsi_plus_xing_bgColor'        => sanitize_text_field( $sfsi_plus_xing_bgColor ),
		'sfsi_plus_copylink_bgColor'    => sanitize_text_field( $sfsi_plus_copylink_bgColor ),
		'sfsi_plus_mastodon_bgColor'    => sanitize_text_field( $sfsi_plus_mastodon_bgColor ),
	);
	update_option( 'sfsi_premium_section3_options', serialize( $up_option3 ) );
	header( 'Content-Type: application/json' );
	echo json_encode( array( "success" ) );
	exit;
}

/* save settings for section 4 */
add_action( 'wp_ajax_plus_updateSrcn4', 'sfsi_plus_options_updater4' );
function sfsi_plus_options_updater4() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step4" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$sfsi_plus_display_counts = isset( $_POST["sfsi_plus_display_counts"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_counts"] ) : 'no';

	$sfsi_plus_email_countsDisplay = isset( $_POST["sfsi_plus_email_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_countsDisplay"] ) : 'no';
	$sfsi_plus_email_countsFrom    = isset( $_POST["sfsi_plus_email_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_countsFrom"] ) : 'manual';
	$sfsi_plus_email_manualCounts  = isset( $_POST["sfsi_plus_email_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_email_manualCounts"] ) ) : '';

	$sfsi_plus_rss_countsDisplay = isset( $_POST["sfsi_plus_rss_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_rss_countsDisplay"] ) : 'no';
	$sfsi_plus_rss_manualCounts  = isset( $_POST["sfsi_plus_rss_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_rss_manualCounts"] ) ) : '';

	$sfsi_plus_facebook_countsDisplay = isset( $_POST["sfsi_plus_facebook_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_facebook_countsDisplay"] ) : 'no';

	$sfsi_plus_facebook_countsFrom = isset( $_POST["sfsi_plus_facebook_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_facebook_countsFrom"] ) : 'manual';

	$sfsi_plus_facebook_cachingActive = isset( $_POST["sfsi_plus_fb_count_caching_active"] ) ? sanitize_text_field( $_POST["sfsi_plus_fb_count_caching_active"] ) : 'no';

	$sfsi_plus_facebook_countsFrom_blog = isset( $_POST["sfsi_plus_facebook_countsFrom_blog"] ) ? sfsi_plus_sanitize_field( trim( $_POST["sfsi_plus_facebook_countsFrom_blog"] ) ) : '';

	$sfsi_plus_facebook_mypageCounts = isset( $_POST["sfsi_plus_facebook_mypageCounts"] ) ? sfsi_plus_sanitize_field( trim( $_POST["sfsi_plus_facebook_mypageCounts"] ) ) : '';
	$sfsi_plus_facebook_appid        = isset( $_POST["sfsi_plus_facebook_appid"] ) ? sfsi_plus_sanitize_field( trim( $_POST["sfsi_plus_facebook_appid"] ) ) : '';
	$sfsi_plus_facebook_appsecret    = isset( $_POST["sfsi_plus_facebook_appsecret"] ) ? sfsi_plus_sanitize_field( trim( $_POST["sfsi_plus_facebook_appsecret"] ) ) : '';
	$sfsi_plus_facebook_manualCounts = isset( $_POST["sfsi_plus_facebook_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_facebook_manualCounts"] ) ) : '';
	$sfsi_plus_facebook_PageLink     = isset( $_POST["sfsi_plus_facebook_PageLink"] ) ? trim( $_POST["sfsi_plus_facebook_PageLink"] ) : '';

	$sfsi_plus_twitter_countsDisplay = isset( $_POST["sfsi_plus_twitter_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_countsDisplay"] ) : 'no';
	$sfsi_plus_threads_countsDisplay = isset( $_POST["sfsi_plus_threads_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_threads_countsDisplay"] ) : 'no';
	$sfsi_plus_bluesky_countsDisplay = isset( $_POST["sfsi_plus_bluesky_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_bluesky_countsDisplay"] ) : 'no';
	$sfsi_plus_twitter_countsFrom    = isset( $_POST["sfsi_plus_twitter_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_countsFrom"] ) : 'manual';
	$sfsi_plus_threads_countsFrom    = isset( $_POST["sfsi_plus_threads_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_threads_countsFrom"] ) : 'manual';
	$sfsi_plus_bluesky_countsFrom    = isset( $_POST["sfsi_plus_bluesky_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_bluesky_countsFrom"] ) : 'manual';
	$sfsi_plus_twitter_manualCounts  = isset( $_POST["sfsi_plus_twitter_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_twitter_manualCounts"] ) ) : '';
	$sfsi_plus_threads_manualCounts  = isset( $_POST["sfsi_plus_threads_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_threads_manualCounts"] ) ) : '';
	$sfsi_plus_bluesky_manualCounts  = isset( $_POST["sfsi_plus_bluesky_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_bluesky_manualCounts"] ) ) : '';
	$sfsiplus_tw_consumer_key        = isset( $_POST["sfsiplus_tw_consumer_key"] ) ? sanitize_text_field( trim( $_POST["sfsiplus_tw_consumer_key"] ) ) : '';
	$sfsiplus_tw_consumer_secret     = isset( $_POST["sfsiplus_tw_consumer_secret"] ) ? sanitize_text_field( trim( $_POST["sfsiplus_tw_consumer_secret"] ) ) : '';
	$sfsiplus_tw_oauth_access_token  = isset( $_POST["sfsiplus_tw_oauth_access_token"] ) ? sanitize_text_field( trim( $_POST["sfsiplus_tw_oauth_access_token"] ) ) : '';

	$sfsiplus_tw_oauth_access_token_secret = isset( $_POST["sfsiplus_tw_oauth_access_token_secret"] )
		? sanitize_text_field( trim( $_POST["sfsiplus_tw_oauth_access_token_secret"] ) )
		: '';

	$sfsi_plus_tw_cachingActive = isset( $_POST["sfsi_plus_tw_count_caching_active"] ) ? sanitize_text_field( $_POST["sfsi_plus_tw_count_caching_active"] ) : 'no';


	$sfsi_plus_linkedIn_countsDisplay = isset( $_POST["sfsi_plus_linkedIn_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_linkedIn_countsDisplay"] ) : 'no';
	$sfsi_plus_linkedIn_countsFrom    = isset( $_POST["sfsi_plus_linkedIn_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_linkedIn_countsFrom"] ) : 'manual';
	$sfsi_plus_linkedIn_manualCounts  = isset( $_POST["sfsi_plus_linkedIn_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_linkedIn_manualCounts"] ) ) : '';
	$sfsi_plus_ln_company             = isset( $_POST["sfsi_plus_ln_company"] ) ? trim( $_POST["sfsi_plus_ln_company"] ) : '';
	$sfsi_plus_ln_api_key             = isset( $_POST["sfsi_plus_ln_api_key"] ) ? trim( $_POST["sfsi_plus_ln_api_key"] ) : '';
	$sfsi_plus_ln_secret_key          = isset( $_POST["sfsi_plus_ln_secret_key"] ) ? trim( $_POST["sfsi_plus_ln_secret_key"] ) : '';
	$sfsi_plus_ln_oAuth_user_token    = isset( $_POST["sfsi_plus_ln_oAuth_user_token"] ) ? trim( $_POST["sfsi_plus_ln_oAuth_user_token"] ) : '';

	$sfsi_plus_youtube_countsDisplay = isset( $_POST["sfsi_plus_youtube_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_youtube_countsDisplay"] ) : 'no';
	$sfsi_plus_youtube_countsFrom    = isset( $_POST["sfsi_plus_youtube_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_youtube_countsFrom"] ) : 'manual';
	$sfsi_plus_youtube_manualCounts  = isset( $_POST["sfsi_plus_youtube_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_youtube_manualCounts"] ) ) : '';
	$sfsi_plus_youtube_user          = isset( $_POST["sfsi_plus_youtube_user"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_youtube_user"] ) ) : '';
	$sfsi_plus_youtube_channelId     = isset( $_POST["sfsi_plus_youtube_channelId"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_youtube_channelId"] ) ) : '';

	$sfsi_plus_pinterest_countsDisplay = isset( $_POST["sfsi_plus_pinterest_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_pinterest_countsDisplay"] ) : 'no';
	$sfsi_plus_pinterest_countsFrom    = isset( $_POST["sfsi_plus_pinterest_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_pinterest_countsFrom"] ) : 'manual';
	$sfsi_plus_pinterest_manualCounts  = isset( $_POST["sfsi_plus_pinterest_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_pinterest_manualCounts"] ) ) : '';

	$sfsi_plus_pinterest_appid     = isset( $_POST["sfsi_plus_pinterest_appid"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_pinterest_appid"] ) ) : '';
	$sfsi_plus_pinterest_appsecret = isset( $_POST["sfsi_plus_pinterest_appsecret"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_pinterest_appsecret"] ) ) : '';
	$sfsi_plus_pinterest_appurl    = isset( $_POST["sfsi_plus_pinterest_appurl"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_pinterest_appurl"] ) ) : '';

	$sfsi_plus_pinterest_access_token = isset( $_POST["sfsi_plus_pinterest_access_token"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_pinterest_access_token"] ) ) : '';

	$sfsi_plus_pinterest_user  = isset( $_POST["sfsi_plus_pinterest_user"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_pinterest_user"] ) ) : '';
	$sfsi_plus_pinterest_board = isset( $_POST["sfsi_plus_pinterest_board_name"] ) ? sanitize_text_field( trim( $_POST["sfsi_plus_pinterest_board_name"] ) ) : '';

	$sfsi_plus_instagram_countsDisplay = isset( $_POST["sfsi_plus_instagram_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_countsDisplay"] ) : 'no';
	$sfsi_plus_instagram_countsFrom    = isset( $_POST["sfsi_plus_instagram_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_countsFrom"] ) : 'manual';
	$sfsi_plus_instagram_manualCounts  = isset( $_POST["sfsi_plus_instagram_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_instagram_manualCounts"] ) ) : '';
	$sfsi_plus_instagram_User          = isset( $_POST["sfsi_plus_instagram_User"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_User"] ) : '';
	$sfsi_plus_instagram_clientid      = isset( $_POST["sfsi_plus_instagram_clientid"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_clientid"] ) : '';
	$sfsi_plus_instagram_appurl        = isset( $_POST["sfsi_plus_instagram_appurl"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_appurl"] ) : '';
	$sfsi_plus_instagram_token         = isset( $_POST["sfsi_plus_instagram_token"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_token"] ) : '';

	$sfsi_plus_shares_countsDisplay = isset( $_POST["sfsi_plus_shares_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_shares_countsDisplay"] ) : 'no';
	$sfsi_plus_shares_countsFrom    = isset( $_POST["sfsi_plus_shares_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_shares_countsFrom"] ) : 'manual';
	$sfsi_plus_shares_manualCounts  = isset( $_POST["sfsi_plus_shares_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_shares_manualCounts"] ) ) : '';

	$sfsi_plus_houzz_countsDisplay = isset( $_POST["sfsi_plus_houzz_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_houzz_countsDisplay"] ) : 'no';
	$sfsi_plus_houzz_countsFrom    = isset( $_POST["sfsi_plus_houzz_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_houzz_countsFrom"] ) : 'manual';
	$sfsi_plus_houzz_manualCounts  = isset( $_POST["sfsi_plus_houzz_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_houzz_manualCounts"] ) ) : '';

	$sfsi_plus_snapchat_countsDisplay = isset( $_POST["sfsi_plus_snapchat_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_snapchat_countsDisplay"] ) : 'no';
	$sfsi_plus_snapchat_countsFrom    = isset( $_POST["sfsi_plus_snapchat_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_snapchat_countsFrom"] ) : 'manual';
	$sfsi_plus_snapchat_manualCounts  = isset( $_POST["sfsi_plus_snapchat_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_snapchat_manualCounts"] ) ) : '';

	$sfsi_plus_ria_countsDisplay = isset( $_POST["sfsi_plus_ria_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_ria_countsDisplay"] ) : 'no';
	$sfsi_plus_ria_countsFrom    = isset( $_POST["sfsi_plus_ria_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_ria_countsFrom"] ) : 'manual';
	$sfsi_plus_ria_manualCounts  = isset( $_POST["sfsi_plus_ria_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_ria_manualCounts"] ) ) : '';

	$sfsi_plus_inha_countsDisplay = isset( $_POST["sfsi_plus_inha_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_inha_countsDisplay"] ) : 'no';
	$sfsi_plus_inha_countsFrom    = isset( $_POST["sfsi_plus_inha_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_inha_countsFrom"] ) : 'manual';
	$sfsi_plus_inha_manualCounts  = isset( $_POST["sfsi_plus_inha_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_inha_manualCounts"] ) ) : '';

	$sfsi_plus_whatsapp_countsDisplay = isset( $_POST["sfsi_plus_whatsapp_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_whatsapp_countsDisplay"] ) : 'no';
	$sfsi_plus_whatsapp_countsFrom    = isset( $_POST["sfsi_plus_whatsapp_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_whatsapp_countsFrom"] ) : 'manual';
	$sfsi_plus_whatsapp_manualCounts  = isset( $_POST["sfsi_plus_whatsapp_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_whatsapp_manualCounts"] ) ) : '';

	$sfsi_plus_phone_countsDisplay = isset( $_POST["sfsi_plus_phone_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_phone_countsDisplay"] ) : 'no';
	$sfsi_plus_phone_manualCounts  = isset( $_POST["sfsi_plus_phone_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_phone_manualCounts"] ) ) : '';

	$sfsi_plus_skype_countsDisplay = isset( $_POST["sfsi_plus_skype_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_skype_countsDisplay"] ) : 'no';
	$sfsi_plus_skype_countsFrom    = isset( $_POST["sfsi_plus_skype_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_skype_countsFrom"] ) : 'manual';
	$sfsi_plus_skype_manualCounts  = isset( $_POST["sfsi_plus_skype_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_skype_manualCounts"] ) ) : '';

	$sfsi_plus_vimeo_countsDisplay = isset( $_POST["sfsi_plus_vimeo_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_vimeo_countsDisplay"] ) : 'no';
	$sfsi_plus_vimeo_countsFrom    = isset( $_POST["sfsi_plus_vimeo_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_vimeo_countsFrom"] ) : 'manual';
	$sfsi_plus_vimeo_manualCounts  = isset( $_POST["sfsi_plus_vimeo_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_vimeo_manualCounts"] ) ) : '';

	$sfsi_plus_soundcloud_countsDisplay = isset( $_POST["sfsi_plus_soundcloud_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_soundcloud_countsDisplay"] ) : 'no';
	$sfsi_plus_soundcloud_countsFrom    = isset( $_POST["sfsi_plus_soundcloud_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_soundcloud_countsFrom"] ) : 'manual';
	$sfsi_plus_soundcloud_manualCounts  = isset( $_POST["sfsi_plus_soundcloud_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_soundcloud_manualCounts"] ) ) : '';

	$sfsi_plus_yummly_countsDisplay = isset( $_POST["sfsi_plus_yummly_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_yummly_countsDisplay"] ) : 'no';
	$sfsi_plus_yummly_countsFrom    = isset( $_POST["sfsi_plus_yummly_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_yummly_countsFrom"] ) : 'manual';
	$sfsi_plus_yummly_manualCounts  = isset( $_POST["sfsi_plus_yummly_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_yummly_manualCounts"] ) ) : '';

	$sfsi_plus_flickr_countsDisplay = isset( $_POST["sfsi_plus_flickr_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_flickr_countsDisplay"] ) : 'no';
	$sfsi_plus_flickr_countsFrom    = isset( $_POST["sfsi_plus_flickr_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_flickr_countsFrom"] ) : 'manual';
	$sfsi_plus_flickr_manualCounts  = isset( $_POST["sfsi_plus_flickr_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_flickr_manualCounts"] ) ) : '';

	$sfsi_plus_reddit_countsDisplay = isset( $_POST["sfsi_plus_reddit_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_reddit_countsDisplay"] ) : 'no';
	$sfsi_plus_reddit_countsFrom    = isset( $_POST["sfsi_plus_reddit_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_reddit_countsFrom"] ) : 'manual';
	$sfsi_plus_reddit_manualCounts  = isset( $_POST["sfsi_plus_reddit_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_reddit_manualCounts"] ) ) : '';

	$sfsi_plus_tumblr_countsDisplay = isset( $_POST["sfsi_plus_tumblr_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_tumblr_countsDisplay"] ) : 'no';
	$sfsi_plus_tumblr_countsFrom    = isset( $_POST["sfsi_plus_tumblr_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_tumblr_countsFrom"] ) : 'manual';
	$sfsi_plus_tumblr_manualCounts  = isset( $_POST["sfsi_plus_tumblr_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_tumblr_manualCounts"] ) ) : '';

	$sfsi_plus_fbmessenger_countsDisplay = isset( $_POST["sfsi_plus_fbmessenger_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_fbmessenger_countsDisplay"] ) : 'no';
	$sfsi_plus_fbmessenger_countsFrom    = isset( $_POST["sfsi_plus_fbmessenger_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_fbmessenger_countsFrom"] ) : 'manual';
	$sfsi_plus_fbmessenger_manualCounts  = isset( $_POST["sfsi_plus_fbmessenger_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_fbmessenger_manualCounts"] ) ) : '';

	$sfsi_plus_gab_countsDisplay = isset( $_POST["sfsi_plus_gab_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_gab_countsDisplay"] ) : 'no';
	$sfsi_plus_gab_countsFrom    = isset( $_POST["sfsi_plus_gab_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_gab_countsFrom"] ) : 'manual';
	$sfsi_plus_gab_manualCounts  = isset( $_POST["sfsi_plus_gab_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_gab_manualCounts"] ) ) : '';

	$sfsi_plus_mix_countsDisplay = isset( $_POST["sfsi_plus_mix_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_mix_countsDisplay"] ) : 'no';
	$sfsi_plus_mix_countsFrom    = isset( $_POST["sfsi_plus_mix_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_mix_countsFrom"] ) : 'manual';
	$sfsi_plus_mix_manualCounts  = isset( $_POST["sfsi_plus_mix_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_mix_manualCounts"] ) ) : '';

	$sfsi_plus_ok_countsDisplay = isset( $_POST["sfsi_plus_ok_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_ok_countsDisplay"] ) : 'no';
	$sfsi_plus_ok_countsFrom    = isset( $_POST["sfsi_plus_ok_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_ok_countsFrom"] ) : 'manual';
	$sfsi_plus_ok_manualCounts  = isset( $_POST["sfsi_plus_ok_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_ok_manualCounts"] ) ) : '';

	$sfsi_plus_vk_countsDisplay = isset( $_POST["sfsi_plus_vk_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_vk_countsDisplay"] ) : 'no';
	$sfsi_plus_vk_countsFrom    = isset( $_POST["sfsi_plus_vk_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_vk_countsFrom"] ) : 'manual';
	$sfsi_plus_vk_manualCounts  = isset( $_POST["sfsi_plus_vk_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_vk_manualCounts"] ) ) : '';

	$sfsi_plus_telegram_countsDisplay = isset( $_POST["sfsi_plus_telegram_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegram_countsDisplay"] ) : 'no';
	$sfsi_plus_telegram_countsFrom    = isset( $_POST["sfsi_plus_telegram_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegram_countsFrom"] ) : 'manual';
	$sfsi_plus_telegram_manualCounts  = isset( $_POST["sfsi_plus_telegram_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_telegram_manualCounts"] ) ) : '';

	$sfsi_plus_weibo_countsDisplay = isset( $_POST["sfsi_plus_weibo_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_weibo_countsDisplay"] ) : 'no';
	$sfsi_plus_weibo_countsFrom    = isset( $_POST["sfsi_plus_weibo_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_weibo_countsFrom"] ) : 'manual';
	$sfsi_plus_weibo_manualCounts  = isset( $_POST["sfsi_plus_weibo_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_weibo_manualCounts"] ) ) : '';

	$sfsi_plus_xing_countsDisplay = isset( $_POST["sfsi_plus_xing_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_xing_countsDisplay"] ) : 'no';
	$sfsi_plus_xing_countsFrom    = isset( $_POST["sfsi_plus_xing_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_xing_countsFrom"] ) : 'manual';
	$sfsi_plus_xing_manualCounts  = isset( $_POST["sfsi_plus_xing_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_xing_manualCounts"] ) ) : '';

	$sfsi_plus_wechat_countsDisplay = isset( $_POST["sfsi_plus_wechat_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_wechat_countsDisplay"] ) : 'no';
	$sfsi_plus_wechat_countsFrom    = isset( $_POST["sfsi_plus_wechat_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_wechat_countsFrom"] ) : 'manual';
	$sfsi_plus_wechat_manualCounts  = isset( $_POST["sfsi_plus_wechat_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_wechat_manualCounts"] ) ) : '';

	$sfsi_plus_copylink_countsDisplay = isset( $_POST["sfsi_plus_copylink_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_copylink_countsDisplay"] ) : 'no';
	$sfsi_plus_copylink_countsFrom    = isset( $_POST["sfsi_plus_copylink_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_copylink_countsFrom"] ) : 'manual';
	$sfsi_plus_copylink_manualCounts  = isset( $_POST["sfsi_plus_copylink_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_copylink_manualCounts"] ) ) : '';

	$sfsi_plus_mastodon_countsDisplay = isset( $_POST["sfsi_plus_mastodon_countsDisplay"] ) ? sanitize_text_field( $_POST["sfsi_plus_mastodon_countsDisplay"] ) : 'no';
	$sfsi_plus_mastodon_countsFrom    = isset( $_POST["sfsi_plus_mastodon_countsFrom"] ) ? sanitize_text_field( $_POST["sfsi_plus_mastodon_countsFrom"] ) : 'manual';
	$sfsi_plus_mastodon_manualCounts  = isset( $_POST["sfsi_plus_mastodon_manualCounts"] ) ? intval( trim( $_POST["sfsi_plus_mastodon_manualCounts"] ) ) : '';

	$sfsi_plus_facebookPage_url = isset( $_POST["sfsi_plus_facebookPage_url"] ) ? trim( $_POST["sfsi_plus_facebookPage_url"] ) : '';

	$sfsi_plus_min_display_counts = isset( $_POST["sfsi_plus_min_display_counts"] ) ? intval( trim( $_POST["sfsi_plus_min_display_counts"] ) ) : 1;

	$sfsi_plus_fb_caching_interval = isset( $_POST["sfsi_plus_fb_caching_interval"] ) ? intval( trim( $_POST["sfsi_plus_fb_caching_interval"] ) ) : 1;

	$up_option4 = array(

		'sfsi_plus_display_counts' => sanitize_text_field( $sfsi_plus_display_counts ),

		'sfsi_plus_email_countsDisplay' => sanitize_text_field( $sfsi_plus_email_countsDisplay ),
		'sfsi_plus_email_countsFrom'    => sanitize_text_field( $sfsi_plus_email_countsFrom ),
		'sfsi_plus_email_manualCounts'  => intval( $sfsi_plus_email_manualCounts ),

		'sfsi_plus_rss_countsDisplay' => sanitize_text_field( $sfsi_plus_rss_countsDisplay ),
		'sfsi_plus_rss_manualCounts'  => intval( $sfsi_plus_rss_manualCounts ),

		'sfsi_plus_facebook_countsDisplay'  => sanitize_text_field( $sfsi_plus_facebook_countsDisplay ),
		'sfsi_plus_facebook_countsFrom'     => sanitize_text_field( $sfsi_plus_facebook_countsFrom ),
		'sfsi_plus_fb_count_caching_active' => sanitize_text_field( $sfsi_plus_facebook_cachingActive ),

		'sfsi_plus_facebook_countsFrom_blog' => sfsi_plus_sanitize_field( $sfsi_plus_facebook_countsFrom_blog ),
		'sfsi_plus_facebook_mypageCounts'    => sfsi_plus_sanitize_field( $sfsi_plus_facebook_mypageCounts ),
		'sfsi_plus_facebook_appid'           => sfsi_plus_sanitize_field( $sfsi_plus_facebook_appid ),
		'sfsi_plus_facebook_appsecret'       => sfsi_plus_sanitize_field( $sfsi_plus_facebook_appsecret ),
		'sfsi_plus_facebook_manualCounts'    => intval( $sfsi_plus_facebook_manualCounts ),
		//'sfsi_plus_facebook_PageLink'      => $sfsi_plus_facebook_PageLink,

		'sfsi_plus_twitter_countsDisplay'   => sanitize_text_field( $sfsi_plus_twitter_countsDisplay ),
		'sfsi_plus_bluesky_countsDisplay'   => sanitize_text_field( $sfsi_plus_bluesky_countsDisplay ),
		'sfsi_plus_threads_countsDisplay'   => sanitize_text_field( $sfsi_plus_threads_countsDisplay ),
		'sfsi_plus_twitter_countsFrom'      => sanitize_text_field( $sfsi_plus_twitter_countsFrom ),
		'sfsi_plus_threads_countsFrom'      => sanitize_text_field( $sfsi_plus_threads_countsFrom ),
		'sfsi_plus_bluesky_countsFrom'      => sanitize_text_field( $sfsi_plus_bluesky_countsFrom ),
		'sfsi_plus_tw_count_caching_active' => sanitize_text_field( $sfsi_plus_tw_cachingActive ),

		'sfsi_plus_twitter_manualCounts'        => intval( $sfsi_plus_twitter_manualCounts ),
		'sfsi_plus_threads_manualCounts'        => intval( $sfsi_plus_threads_manualCounts ),
		'sfsi_plus_bluesky_manualCounts'        => intval( $sfsi_plus_bluesky_manualCounts ),
		'sfsiplus_tw_consumer_key'              => sfsi_plus_sanitize_field( $sfsiplus_tw_consumer_key ),
		'sfsiplus_tw_consumer_secret'           => sfsi_plus_sanitize_field( $sfsiplus_tw_consumer_secret ),
		'sfsiplus_tw_oauth_access_token'        => sfsi_plus_sanitize_field( $sfsiplus_tw_oauth_access_token ),
		'sfsiplus_tw_oauth_access_token_secret' => sfsi_plus_sanitize_field( $sfsiplus_tw_oauth_access_token_secret ),

		/*'sfsi_plus_ln_company'             => $sfsi_plus_ln_company,
       'sfsi_plus_ln_api_key'               => $sfsi_plus_ln_api_key,
       'sfsi_plus_ln_secret_key'            => $sfsi_plus_ln_secret_key,
       'sfsi_plus_ln_oAuth_user_token'      => $sfsi_plus_ln_oAuth_user_token,*/
		'sfsi_plus_linkedIn_countsDisplay'      => sanitize_text_field( $sfsi_plus_linkedIn_countsDisplay ),
		'sfsi_plus_linkedIn_countsFrom'         => sanitize_text_field( $sfsi_plus_linkedIn_countsFrom ),
		'sfsi_plus_linkedIn_manualCounts'       => intval( $sfsi_plus_linkedIn_manualCounts ),

		'sfsi_plus_youtube_countsDisplay' => sanitize_text_field( $sfsi_plus_youtube_countsDisplay ),
		'sfsi_plus_youtube_countsFrom'    => sanitize_text_field( $sfsi_plus_youtube_countsFrom ),
		'sfsi_plus_youtube_manualCounts'  => intval( $sfsi_plus_youtube_manualCounts ),
		'sfsi_plus_youtube_user'          => sfsi_plus_sanitize_field( $sfsi_plus_youtube_user ),
		'sfsi_plus_youtube_channelId'     => sfsi_plus_sanitize_field( $sfsi_plus_youtube_channelId ),

		'sfsi_plus_pinterest_countsDisplay' => sanitize_text_field( $sfsi_plus_pinterest_countsDisplay ),
		'sfsi_plus_pinterest_countsFrom'    => sanitize_text_field( $sfsi_plus_pinterest_countsFrom ),
		'sfsi_plus_pinterest_manualCounts'  => intval( $sfsi_plus_pinterest_manualCounts ),

		'sfsi_plus_pinterest_appid'     => sanitize_text_field( $sfsi_plus_pinterest_appid ),
		'sfsi_plus_pinterest_appsecret' => sanitize_text_field( $sfsi_plus_pinterest_appsecret ),
		'sfsi_plus_pinterest_appurl'    => sanitize_text_field( $sfsi_plus_pinterest_appurl ),

		'sfsi_plus_pinterest_access_token' => sanitize_text_field( $sfsi_plus_pinterest_access_token ),
		'sfsi_plus_pinterest_user'         => sanitize_text_field( $sfsi_plus_pinterest_user ),
		'sfsi_plus_pinterest_board_name'   => sanitize_text_field( $sfsi_plus_pinterest_board ),

		'sfsi_plus_instagram_countsFrom'    => sanitize_text_field( $sfsi_plus_instagram_countsFrom ),
		'sfsi_plus_instagram_countsDisplay' => sanitize_text_field( $sfsi_plus_instagram_countsDisplay ),
		'sfsi_plus_instagram_manualCounts'  => intval( $sfsi_plus_instagram_manualCounts ),
		'sfsi_plus_instagram_User'          => sanitize_text_field( $sfsi_plus_instagram_User ),
		'sfsi_plus_instagram_clientid'      => sanitize_text_field( $sfsi_plus_instagram_clientid ),
		'sfsi_plus_instagram_appurl'        => sanitize_text_field( $sfsi_plus_instagram_appurl ),
		'sfsi_plus_instagram_token'         => sanitize_text_field( $sfsi_plus_instagram_token ),

		'sfsi_plus_shares_countsDisplay' => sanitize_text_field( $sfsi_plus_shares_countsDisplay ),
		'sfsi_plus_shares_countsFrom'    => sanitize_text_field( $sfsi_plus_shares_countsFrom ),
		'sfsi_plus_shares_manualCounts'  => intval( $sfsi_plus_shares_manualCounts ),

		'sfsi_plus_houzz_countsDisplay' => sanitize_text_field( $sfsi_plus_houzz_countsDisplay ),
		'sfsi_plus_houzz_countsFrom'    => sanitize_text_field( $sfsi_plus_houzz_countsFrom ),
		'sfsi_plus_houzz_manualCounts'  => intval( $sfsi_plus_houzz_manualCounts ),

		'sfsi_plus_snapchat_countsDisplay' => sanitize_text_field( $sfsi_plus_snapchat_countsDisplay ),
		'sfsi_plus_snapchat_countsFrom'    => sanitize_text_field( $sfsi_plus_snapchat_countsFrom ),
		'sfsi_plus_snapchat_manualCounts'  => intval( $sfsi_plus_snapchat_manualCounts ),

		'sfsi_plus_ria_countsDisplay' => sanitize_text_field( $sfsi_plus_ria_countsDisplay ),
		'sfsi_plus_ria_countsFrom'    => sanitize_text_field( $sfsi_plus_ria_countsFrom ),
		'sfsi_plus_ria_manualCounts'  => intval( $sfsi_plus_ria_manualCounts ),

		'sfsi_plus_inha_countsDisplay' => sanitize_text_field( $sfsi_plus_inha_countsDisplay ),
		'sfsi_plus_inha_countsFrom'    => sanitize_text_field( $sfsi_plus_inha_countsFrom ),
		'sfsi_plus_inha_manualCounts'  => intval( $sfsi_plus_inha_manualCounts ),

		'sfsi_plus_whatsapp_countsDisplay' => sanitize_text_field( $sfsi_plus_whatsapp_countsDisplay ),
		'sfsi_plus_whatsapp_countsFrom'    => sanitize_text_field( $sfsi_plus_whatsapp_countsFrom ),
		'sfsi_plus_whatsapp_manualCounts'  => intval( $sfsi_plus_whatsapp_manualCounts ),

		'sfsi_plus_phone_countsDisplay' => sanitize_text_field( $sfsi_plus_phone_countsDisplay ),
		'sfsi_plus_phone_manualCounts'  => intval( $sfsi_plus_phone_manualCounts ),

		'sfsi_plus_skype_countsDisplay' => sanitize_text_field( $sfsi_plus_skype_countsDisplay ),
		'sfsi_plus_skype_countsFrom'    => sanitize_text_field( $sfsi_plus_skype_countsFrom ),
		'sfsi_plus_skype_manualCounts'  => intval( $sfsi_plus_skype_manualCounts ),

		'sfsi_plus_vimeo_countsDisplay' => sanitize_text_field( $sfsi_plus_vimeo_countsDisplay ),
		'sfsi_plus_vimeo_countsFrom'    => sanitize_text_field( $sfsi_plus_vimeo_countsFrom ),
		'sfsi_plus_vimeo_manualCounts'  => intval( $sfsi_plus_vimeo_manualCounts ),

		'sfsi_plus_soundcloud_countsDisplay' => sanitize_text_field( $sfsi_plus_soundcloud_countsDisplay ),
		'sfsi_plus_soundcloud_countsFrom'    => sanitize_text_field( $sfsi_plus_soundcloud_countsFrom ),
		'sfsi_plus_soundcloud_manualCounts'  => intval( $sfsi_plus_soundcloud_manualCounts ),

		'sfsi_plus_yummly_countsDisplay' => sanitize_text_field( $sfsi_plus_yummly_countsDisplay ),
		'sfsi_plus_yummly_countsFrom'    => sanitize_text_field( $sfsi_plus_yummly_countsFrom ),
		'sfsi_plus_yummly_manualCounts'  => intval( $sfsi_plus_yummly_manualCounts ),

		'sfsi_plus_flickr_countsDisplay' => sanitize_text_field( $sfsi_plus_flickr_countsDisplay ),
		'sfsi_plus_flickr_countsFrom'    => sanitize_text_field( $sfsi_plus_flickr_countsFrom ),
		'sfsi_plus_flickr_manualCounts'  => intval( $sfsi_plus_flickr_manualCounts ),

		'sfsi_plus_reddit_countsDisplay' => sanitize_text_field( $sfsi_plus_reddit_countsDisplay ),
		'sfsi_plus_reddit_countsFrom'    => sanitize_text_field( $sfsi_plus_reddit_countsFrom ),
		'sfsi_plus_reddit_manualCounts'  => intval( $sfsi_plus_reddit_manualCounts ),

		'sfsi_plus_tumblr_countsDisplay' => sanitize_text_field( $sfsi_plus_tumblr_countsDisplay ),
		'sfsi_plus_tumblr_countsFrom'    => sanitize_text_field( $sfsi_plus_tumblr_countsFrom ),
		'sfsi_plus_tumblr_manualCounts'  => intval( $sfsi_plus_tumblr_manualCounts ),

		'sfsi_plus_fbmessenger_countsDisplay' => sanitize_text_field( $sfsi_plus_fbmessenger_countsDisplay ),
		'sfsi_plus_fbmessenger_countsFrom'    => sanitize_text_field( $sfsi_plus_fbmessenger_countsFrom ),
		'sfsi_plus_fbmessenger_manualCounts'  => intval( $sfsi_plus_fbmessenger_manualCounts ),

		'sfsi_plus_gab_countsDisplay' => sanitize_text_field( $sfsi_plus_gab_countsDisplay ),
		'sfsi_plus_gab_countsFrom'    => sanitize_text_field( $sfsi_plus_gab_countsFrom ),
		'sfsi_plus_gab_manualCounts'  => intval( $sfsi_plus_gab_manualCounts ),

		'sfsi_plus_mix_countsDisplay' => sanitize_text_field( $sfsi_plus_mix_countsDisplay ),
		'sfsi_plus_mix_countsFrom'    => sanitize_text_field( $sfsi_plus_mix_countsFrom ),
		'sfsi_plus_mix_manualCounts'  => intval( $sfsi_plus_mix_manualCounts ),

		'sfsi_plus_ok_countsDisplay' => sanitize_text_field( $sfsi_plus_ok_countsDisplay ),
		'sfsi_plus_ok_countsFrom'    => sanitize_text_field( $sfsi_plus_ok_countsFrom ),
		'sfsi_plus_ok_manualCounts'  => intval( $sfsi_plus_ok_manualCounts ),

		'sfsi_plus_vk_countsDisplay' => sanitize_text_field( $sfsi_plus_vk_countsDisplay ),
		'sfsi_plus_vk_countsFrom'    => sanitize_text_field( $sfsi_plus_vk_countsFrom ),
		'sfsi_plus_vk_manualCounts'  => intval( $sfsi_plus_vk_manualCounts ),

		'sfsi_plus_telegram_countsDisplay' => sanitize_text_field( $sfsi_plus_telegram_countsDisplay ),
		'sfsi_plus_telegram_countsFrom'    => sanitize_text_field( $sfsi_plus_telegram_countsFrom ),
		'sfsi_plus_telegram_manualCounts'  => intval( $sfsi_plus_telegram_manualCounts ),

		'sfsi_plus_weibo_countsDisplay' => sanitize_text_field( $sfsi_plus_weibo_countsDisplay ),
		'sfsi_plus_weibo_countsFrom'    => sanitize_text_field( $sfsi_plus_weibo_countsFrom ),
		'sfsi_plus_weibo_manualCounts'  => intval( $sfsi_plus_weibo_manualCounts ),

		'sfsi_plus_wechat_countsDisplay' => sanitize_text_field( $sfsi_plus_wechat_countsDisplay ),
		'sfsi_plus_wechat_countsFrom'    => sanitize_text_field( $sfsi_plus_wechat_countsFrom ),
		'sfsi_plus_wechat_manualCounts'  => intval( $sfsi_plus_wechat_manualCounts ),

		'sfsi_plus_copylink_countsDisplay' => sanitize_text_field( $sfsi_plus_copylink_countsDisplay ),
		'sfsi_plus_copylink_countsFrom'    => sanitize_text_field( $sfsi_plus_copylink_countsFrom ),
		'sfsi_plus_copylink_manualCounts'  => intval( $sfsi_plus_copylink_manualCounts ),

		'sfsi_plus_xing_countsDisplay' => sanitize_text_field( $sfsi_plus_xing_countsDisplay ),
		'sfsi_plus_xing_countsFrom'    => sanitize_text_field( $sfsi_plus_xing_countsFrom ),
		'sfsi_plus_xing_manualCounts'  => intval( $sfsi_plus_xing_manualCounts ),

		'sfsi_plus_mastodon_countsDisplay' => sanitize_text_field( $sfsi_plus_mastodon_countsDisplay ),
		'sfsi_plus_mastodon_countsFrom'    => sanitize_text_field( $sfsi_plus_mastodon_countsFrom ),
		'sfsi_plus_mastodon_manualCounts'  => intval( $sfsi_plus_mastodon_manualCounts ),

		'sfsi_plus_min_display_counts'  => intval( $sfsi_plus_min_display_counts ),
		'sfsi_plus_fb_caching_interval' => intval( $sfsi_plus_fb_caching_interval )
	);

	$arrNewIcons = array( "fbmessenger", "mix", "ok", "telegram", "vk", "weibo", "xing" );

	foreach ( $arrNewIcons as $key => $iconName ) {

		$keyDisplay = 'sfsi_plus_' . $iconName . '_countsDisplay';
		$keyMCount  = 'sfsi_plus_' . $iconName . '_manualCounts';

		$up_option4[ $keyDisplay ] = isset( $_POST[ $keyDisplay ] ) ? $_POST[ $keyDisplay ] : 'no';
		$up_option4[ $keyMCount ]  = isset( $_POST[ $keyMCount ] ) ? $_POST[ $keyMCount ] : '20';
	}

	update_option( 'sfsi_premium_section4_options', serialize( $up_option4 ) );

	if ( "yes" == $sfsi_plus_display_counts && "yes" == $sfsi_plus_youtube_countsDisplay && "subscriber" == $sfsi_plus_youtube_countsFrom && ( $sfsi_plus_youtube_user !== "" || ( $$sfsi_plus_youtube_channelId !== "" ) ) ) {
		$sfsi_plus_SocialHelper = new sfsi_plus_SocialHelper();
		$return_data            = $sfsi_plus_SocialHelper->sfsi_get_youtube_subs( '' );
	}

	$new_counts = sfsi_plus_getCounts( true, true );


	$option8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );

	$icons        = $option8['sfsi_plus_sticky_icons'];
	$sticky_count = 0;
	$socialObj    = new sfsi_plus_SocialHelper();
	$icon_counts  = sfsi_plus_getCounts( false );
	$option4      = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
	foreach ( $icons['default_icons'] as $icon_name => $icon ) {
		if ( strtolower( $icon_name ) == "follow" ) {
			$icon_name = "email";
		} elseif ( strtolower( $icon_name ) == "linkedin" ) {
			$icon_name = "linkedIn";
		} elseif ( strtolower( $icon_name ) == "odnoklassniki" ) {
			break;
		} elseif ( strtolower( $icon_name ) == "qQ2" ) {
			break;
		}

		if ( $icon['active'] == "yes" && $option4[ 'sfsi_plus_' . lcfirst( $icon_name ) . '_countsDisplay' ] == "yes" ) {
			switch ( strtolower( $icon_name ) ) {
				case 'facebook':
					$sticky_count += ( isset( $icon_counts['fb_count'] ) ? $icon_counts['fb_count'] : 0 );
					break;
				case 'twitter':
					$sticky_count += ( isset( $icon_counts['twitter_count'] ) ? $icon_counts['twitter_count'] : 0 );
					break;
				case 'email':
					$sticky_count += ( isset( $icon_counts['email_count'] ) ? $icon_counts['email_count'] : 0 );
					break;
				case 'pinterest':
					$sticky_count += ( isset( $icon_counts['pin_count'] ) ? $icon_counts['pin_count'] : 0 );
					break;
				case 'linkedIn':
					$sticky_count += ( isset( $icon_counts['linkedIn_count'] ) ? $icon_counts['linkedIn_count'] : 0 );
					break;
				case 'GooglePlus':
					$sticky_count += ( isset( $icon_counts['google_count'] ) ? $icon_counts['google_count'] : 0 );
					break;
				case 'whatsapp':
					$sticky_count += ( isset( $icon_counts['whatsapp_count'] ) ? $icon_counts['whatsapp_count'] : 0 );
					break;
				case 'vk':
					$sticky_count += ( isset( $icon_counts['vk_count'] ) ? $icon_counts['vk_count'] : 0 );
					break;
			}
		}
	}
	update_option( 'sfsi_premium_sticky_icon_counts', $sticky_count );


	// function sfsi_premium_total_count($icons)
	// {
	// ini_set('max_execution_time', 300);
	// $icons =


	// return $socialObj->format_num($count);
	// }

	header( 'Content-Type: application/json' );
	echo json_encode( array( "res" => "success", 'counts' => $new_counts, 'update' => $up_option4 ) );
	exit;
}

/* save settings for section 5 */
add_action( 'wp_ajax_plus_updateSrcn5', 'sfsi_plus_options_updater5' );
function sfsi_plus_options_updater5() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step5" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$sfsi_plus_icons_size            = isset( $_POST["sfsi_plus_icons_size"] ) ? intval( $_POST["sfsi_plus_icons_size"] ) : '51';
	$sfsi_plus_icons_spacing         = isset( $_POST["sfsi_plus_icons_spacing"] ) ? intval( $_POST["sfsi_plus_icons_spacing"] ) : '2';
	$sfsi_plus_icons_verical_spacing = isset( $_POST["sfsi_plus_icons_verical_spacing"] ) ? intval( $_POST["sfsi_plus_icons_verical_spacing"] ) : '5';

	$sfsi_plus_mobile_icon_setting                  = isset( $_POST["sfsi_plus_mobile_icon_setting"] ) ? sanitize_text_field( $_POST["sfsi_plus_mobile_icon_setting"] ) : 'no';
	$sfsi_plus_mobile_horizontal_verical_Alignment  = isset( $_POST["sfsi_plus_mobile_horizontal_verical_Alignment"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_horizontal_verical_Alignment"] )
		: 'Horizontal';
	$sfsi_plus_mobile_icons_Alignment_via_widget    = isset( $_POST["sfsi_plus_mobile_icons_Alignment_via_widget"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_icons_Alignment_via_widget"] )
		: 'left';
	$sfsi_plus_mobile_icons_Alignment_via_shortcode = isset( $_POST["sfsi_plus_mobile_icons_Alignment_via_shortcode"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_icons_Alignment_via_shortcode"] )
		: 'left';
	$sfsi_plus_mobile_icons_Alignment               = isset( $_POST["sfsi_plus_mobile_icons_Alignment"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_icons_Alignment"] )
		: 'left';
	$sfsi_plus_mobile_icons_perRow                  = ( isset( $_POST["sfsi_plus_mobile_icons_perRow"] ) && ! empty( $_POST["sfsi_plus_mobile_icons_perRow"] ) )
		? intval( $_POST["sfsi_plus_mobile_icons_perRow"] )
		: '5';
	$sfsi_plus_mobile_icon_alignment_setting        = isset( $_POST["sfsi_plus_mobile_icon_alignment_setting"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_icon_alignment_setting"] )
		: 'no';

	$sfsi_plus_icons_mobilesize              = isset( $_POST["sfsi_plus_icons_mobilesize"] ) ? intval( $_POST["sfsi_plus_icons_mobilesize"] ) : '50';
	$sfsi_plus_icons_mobilespacing           = isset( $_POST["sfsi_plus_icons_mobilespacing"] ) ? intval( $_POST["sfsi_plus_icons_mobilespacing"] ) : '2';
	$sfsi_plus_icons_verical_mobilespacing   = isset( $_POST["sfsi_plus_icons_verical_mobilespacing"] )
		? intval( $_POST["sfsi_plus_icons_verical_mobilespacing"] )
		: '5';
	$sfsi_plus_horizontal_verical_Alignment  = isset( $_POST["sfsi_plus_horizontal_verical_Alignment"] )
		? sanitize_text_field( $_POST["sfsi_plus_horizontal_verical_Alignment"] )
		: 'Horizontal';
	$sfsi_plus_icons_Alignment_via_shortcode = isset( $_POST["sfsi_plus_icons_Alignment_via_shortcode"] )
		? sanitize_text_field( $_POST["sfsi_plus_icons_Alignment_via_shortcode"] )
		: 'left';
	$sfsi_plus_icons_Alignment_via_widget    = isset( $_POST["sfsi_plus_icons_Alignment_via_widget"] )
		? sanitize_text_field( $_POST["sfsi_plus_icons_Alignment_via_widget"] )
		: 'left';

	$sfsi_plus_icons_Alignment               = isset( $_POST["sfsi_plus_icons_Alignment"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_Alignment"] ) : 'center';
	$sfsi_plus_icons_perRow                  = ( isset( $_POST["sfsi_plus_icons_perRow"] ) && ! empty( $_POST["sfsi_plus_icons_perRow"] ) )
		? intval( $_POST["sfsi_plus_icons_perRow"] )
		: '5';
	$sfsi_plus_icons_language                = isset( $_POST["sfsi_plus_icons_language"] ) && sfsi_verify_language_values( $_POST["sfsi_plus_icons_language"] ) ? esc_js( sanitize_text_field( $_POST["sfsi_plus_icons_language"] ) ) : 'en_US';
	$sfsi_plus_icons_ClickPageOpen           = isset( $_POST["sfsi_plus_icons_ClickPageOpen"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_ClickPageOpen"] ) : 'no';
	$sfsi_plus_icons_AddNoopener             = isset( $_POST["sfsi_plus_icons_AddNoopener"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_AddNoopener"] ) : 'no';
	$sfsi_plus_icons_float                   = isset( $_POST["sfsi_plus_icons_float"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_float"] ) : 'no';
	$sfsi_plus_disable_floaticons            = isset( $_POST["sfsi_plus_disable_floaticons"] ) ? sanitize_text_field( $_POST["sfsi_plus_disable_floaticons"] ) : 'no';
	$sfsi_plus_disable_viewport              = isset( $_POST["sfsi_plus_disable_viewport"] ) ? sanitize_text_field( $_POST["sfsi_plus_disable_viewport"] ) : 'no';
	$sfsi_plus_icons_floatPosition           = isset( $_POST["sfsi_plus_icons_floatPosition"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_floatPosition"] ) : 'center-right';
	$sfsi_plus_icons_stick                   = isset( $_POST["sfsi_plus_icons_stick"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_stick"] ) : 'no';
	$sfsi_plus_rss_MouseOverText             = isset( $_POST["sfsi_plus_rss_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_rss_MouseOverText"] ) : '';
	$sfsi_plus_email_MouseOverText           = isset( $_POST["sfsi_plus_email_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_email_MouseOverText"] ) : '';
	$sfsi_plus_twitter_MouseOverText         = isset( $_POST["sfsi_plus_twitter_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_MouseOverText"] ) : '';
	$sfsi_plus_facebook_MouseOverText        = isset( $_POST["sfsi_plus_facebook_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_facebook_MouseOverText"] ) : '';
	$sfsi_plus_linkedIn_MouseOverText        = isset( $_POST["sfsi_plus_linkedIn_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_linkedIn_MouseOverText"] ) : '';
	$sfsi_plus_pinterest_MouseOverText       = isset( $_POST["sfsi_plus_pinterest_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_pinterest_MouseOverText"] ) : '';
	$sfsi_plus_instagram_MouseOverText       = isset( $_POST["sfsi_plus_instagram_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_instagram_MouseOverText"] ) : '';
	$sfsi_plus_threads_MouseOverText         = isset( $_POST["sfsi_plus_threads_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_threads_MouseOverText"] ) : '';
	$sfsi_plus_bluesky_MouseOverText         = isset( $_POST["sfsi_plus_bluesky_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_bluesky_MouseOverText"] ) : '';
	$sfsi_plus_ria_MouseOverText             = isset( $_POST["sfsi_plus_ria_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_ria_MouseOverText"] ) : '';
	$sfsi_plus_inha_MouseOverText            = isset( $_POST["sfsi_plus_inha_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_inha_MouseOverText"] ) : '';
	$sfsi_plus_houzz_MouseOverText           = isset( $_POST["sfsi_plus_houzz_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_houzz_MouseOverText"] ) : '';
	$sfsi_plus_youtube_MouseOverText         = isset( $_POST["sfsi_plus_youtube_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_youtube_MouseOverText"] ) : '';
	$sfsi_plus_share_MouseOverText           = isset( $_POST["sfsi_plus_share_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_share_MouseOverText"] ) : '';
	$sfsi_premium_featured_image_as_og_image = isset( $_POST["sfsi_premium_featured_image_as_og_image"] ) ? sanitize_text_field( $_POST["sfsi_premium_featured_image_as_og_image"] ) : "no";
	$sfsi_plus_mobile_open_type_setting      = isset( $_POST["sfsi_plus_mobile_open_type_setting"] ) ? sanitize_text_field( $_POST["sfsi_plus_mobile_open_type_setting"] ) : "no";
	$sfsi_plus_icons_mobile_ClickPageOpen    = isset( $_POST["sfsi_plus_icons_mobile_ClickPageOpen"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_mobile_ClickPageOpen"] ) : "no";

	$option1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );

	///////////////////////////// save desktop icons order //////////////////////////////

	$sfsi_order_icons_desktop = isset( $_POST["sfsi_order_icons_desktop"]["data"] ) ? $_POST["sfsi_order_icons_desktop"]["data"] : array();

	$sfsi_order_icons_desktop = serialize( $sfsi_order_icons_desktop );

	///////////////////////////// save desktop icons order //////////////////////////////


	///////////////////////////// save mobile icons order //////////////////////////////

	$sfsi_order_icons_mobile = isset( $_POST["sfsi_order_icons_mobile"]["data"] ) ? $_POST["sfsi_order_icons_mobile"]["data"] : array();
	$sfsi_order_icons_mobile = serialize( $sfsi_order_icons_mobile );


	///////////////////////////// save mobile icons order //////////////////////////////


	$sfsi_plus_mobile_icons_order_setting = isset( $_POST["sfsi_plus_mobile_icons_order_setting"] ) ? sanitize_text_field( $_POST["sfsi_plus_mobile_icons_order_setting"] ) : 'no';


	$sfsi_plus_custom_MouseOverTexts = isset( $_POST["sfsi_plus_custom_MouseOverTexts"] ) ? serialize( $_POST["sfsi_plus_custom_MouseOverTexts"] ) : '';

	$sfsi_plus_follow_icons_language = isset( $_POST["sfsi_plus_follow_icons_language"] ) && sfsi_verify_language_values( $_POST["sfsi_plus_follow_icons_language"] )
		? esc_js( sanitize_text_field( $_POST["sfsi_plus_follow_icons_language"] ) )
		: 'Follow_en_US';

	$sfsi_plus_facebook_icons_language = isset( $_POST["sfsi_plus_facebook_icons_language"] ) && sfsi_verify_language_values( $_POST["sfsi_plus_facebook_icons_language"] )
		? esc_js( sanitize_text_field( $_POST["sfsi_plus_facebook_icons_language"] ) )
		: 'Visit_us_en_US';

	$sfsi_plus_youtube_icons_language = isset( $_POST["sfsi_plus_youtube_icons_language"] ) && sfsi_verify_language_values( $_POST["sfsi_plus_youtube_icons_language"] )
		? esc_js( sanitize_text_field( $_POST["sfsi_plus_youtube_icons_language"] ) )
		: 'Visit_us_en_US';

	$sfsi_plus_twitter_icons_language  = isset( $_POST["sfsi_plus_twitter_icons_language"] ) && sfsi_verify_language_values( $_POST["sfsi_plus_twitter_icons_language"] )
		? esc_js( sanitize_text_field( $_POST["sfsi_plus_twitter_icons_language"] ) )
		: 'Visit_us_en_US';
	$sfsi_plus_linkedin_icons_language = isset( $_POST["sfsi_plus_linkedin_icons_language"] ) && sfsi_verify_language_values( $_POST["sfsi_plus_linkedin_icons_language"] )
		? esc_js( sanitize_text_field( $_POST["sfsi_plus_linkedin_icons_language"] ) )
		: 'en_US';

	$sfsi_plus_snapchat_MouseOverText   = isset( $_POST["sfsi_plus_snapchat_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_snapchat_MouseOverText"] ) : '';
	$sfsi_plus_whatsapp_MouseOverText   = isset( $_POST["sfsi_plus_whatsapp_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_whatsapp_MouseOverText"] ) : '';
	$sfsi_plus_skype_MouseOverText      = isset( $_POST["sfsi_plus_skype_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_skype_MouseOverText"] ) : '';
	$sfsi_plus_vimeo_MouseOverText      = isset( $_POST["sfsi_plus_vimeo_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_vimeo_MouseOverText"] ) : '';
	$sfsi_plus_soundcloud_MouseOverText = isset( $_POST["sfsi_plus_soundcloud_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_soundcloud_MouseOverText"] ) : '';
	$sfsi_plus_yummly_MouseOverText     = isset( $_POST["sfsi_plus_yummly_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_yummly_MouseOverText"] ) : '';
	$sfsi_plus_flickr_MouseOverText     = isset( $_POST["sfsi_plus_flickr_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_flickr_MouseOverText"] ) : '';
	$sfsi_plus_reddit_MouseOverText     = isset( $_POST["sfsi_plus_reddit_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_reddit_MouseOverText"] ) : '';
	$sfsi_plus_tumblr_MouseOverText     = isset( $_POST["sfsi_plus_tumblr_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_tumblr_MouseOverText"] ) : '';

	$sfsi_plus_fbmessenger_MouseOverText = isset( $_POST["sfsi_plus_fbmessenger_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_fbmessenger_MouseOverText"] ) : '';
	$sfsi_plus_gab_MouseOverText         = isset( $_POST["sfsi_plus_gab_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_gab_MouseOverText"] ) : '';
	$sfsi_plus_mix_MouseOverText         = isset( $_POST["sfsi_plus_mix_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_mix_MouseOverText"] ) : '';
	$sfsi_plus_ok_MouseOverText          = isset( $_POST["sfsi_plus_ok_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_ok_MouseOverText"] ) : '';
	$sfsi_plus_telegram_MouseOverText    = isset( $_POST["sfsi_plus_telegram_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_telegram_MouseOverText"] ) : '';
	$sfsi_plus_vk_MouseOverText          = isset( $_POST["sfsi_plus_vk_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_vk_MouseOverText"] ) : '';
	$sfsi_plus_weibo_MouseOverText       = isset( $_POST["sfsi_plus_weibo_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_weibo_MouseOverText"] ) : '';
	$sfsi_plus_xing_MouseOverText        = isset( $_POST["sfsi_plus_xing_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_xing_MouseOverText"] ) : '';
	$sfsi_plus_copylink_MouseOverText    = isset( $_POST["sfsi_plus_copylink_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_copylink_MouseOverText"] ) : '';
	$sfsi_plus_mastodon_MouseOverText    = isset( $_POST["sfsi_plus_mastodon_MouseOverText"] ) ? sanitize_text_field( $_POST["sfsi_plus_mastodon_MouseOverText"] ) : '';

	$sfsi_plus_Facebook_linking           = isset( $_POST["sfsi_plus_Facebook_linking"] ) ? sanitize_text_field( $_POST["sfsi_plus_Facebook_linking"] ) : 'facebookurl';
	$sfsi_plus_facebook_linkingcustom_url = isset( $_POST["sfsi_plus_facebook_linkingcustom_url"] ) ? esc_url( $_POST["sfsi_plus_facebook_linkingcustom_url"] ) : '';

	$sfsi_plus_tooltip_alighn       = isset( $_POST["sfsi_plus_tooltip_alighn"] ) ? $_POST["sfsi_plus_tooltip_alighn"] : 'Automatic';
	$sfsi_plus_tooltip_Color        = isset( $_POST["sfsi_plus_tooltip_Color"] ) ? sfsi_plus_sanitize_hex_color( $_POST["sfsi_plus_tooltip_Color"] ) : '#FFF';
	$sfsi_plus_tooltip_border_Color = isset( $_POST["sfsi_plus_tooltip_border_Color"] ) ? sfsi_plus_sanitize_hex_color( $_POST["sfsi_plus_tooltip_border_Color"] ) : '#e7e7e7';

	$option2                          = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );
	$sfsi_plus_twitter_followUserName = ( isset( $option2['sfsi_plus_twitter_followUserName'] ) && strlen( $option2['sfsi_plus_twitter_followUserName'] ) ) ? $option2['sfsi_plus_twitter_followUserName'] : '';

	parse_str( urldecode( $_POST['sfsi_custom_social_data_post_types_data'] ), $output );
	$sfsi_custom_social_data_post_types_data = isset( $output['sfsi_custom_social_data_post_types'] ) && is_array( $output['sfsi_custom_social_data_post_types'] ) ? serialize( $output['sfsi_custom_social_data_post_types'] ) : serialize( array() );

	// *************************** Sharing texts & pictures STARTS **********************************//

	$sfsi_plus_social_sharing_options = isset( $_POST["sfsi_plus_social_sharing_options"] ) ? sanitize_text_field( $_POST["sfsi_plus_social_sharing_options"] ) : 'posttype';

	$sfsiSocialMediaImage     = isset( $_POST["sfsiSocialMediaImage"] ) ? sanitize_text_field( $_POST["sfsiSocialMediaImage"] ) : '';
	$sfsiSocialtTitleTxt      = isset( $_POST["sfsiSocialtTitleTxt"] ) ? sanitize_text_field( $_POST["sfsiSocialtTitleTxt"] ) : '';
	$sfsiSocialDescription    = isset( $_POST["sfsiSocialDescription"] ) ? sanitize_text_field( $_POST["sfsiSocialDescription"] ) : '';
	$sfsiSocialPinterestImage = isset( $_POST["sfsiSocialPinterestImage"] ) ? sanitize_text_field( $_POST["sfsiSocialPinterestImage"] ) : '';
	$sfsiSocialPinterestDesc  = isset( $_POST["sfsiSocialPinterestDesc"] ) ? sanitize_text_field( $_POST["sfsiSocialPinterestDesc"] ) : '';
	$sfsiSocialTwitterDesc    = isset( $_POST["sfsiSocialTwitterDesc"] ) ? sanitize_text_field( $_POST["sfsiSocialTwitterDesc"] ) : '';

	// *************************** Sharing texts & pictures CLOSES ************************************//

	$sfsi_plus_twitter_aboutPageText = isset( $_POST["sfsi_plus_twitter_aboutPageText"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_aboutPageText"] ) : '${title} ${link}';
	$sfsi_plus_twitter_twtAddCard    = isset( $_POST["sfsi_plus_twitter_twtAddCard"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_twtAddCard"] ) : 'no';
	$sfsi_plus_twitter_twtCardType   = isset( $_POST["sfsi_plus_twitter_twtCardType"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_twtCardType"] ) : 'summary';

	$sfsi_plus_twitter_card_twitter_handle = isset( $_POST["sfsi_plus_twitter_card_twitter_handle"] ) ? sanitize_text_field( $_POST["sfsi_plus_twitter_card_twitter_handle"] ) : $sfsi_plus_twitter_followUserName;


	parse_str( urldecode( $_POST['sfsi_premium_url_shortner_icons_names_list'] ), $IconsListurlShortner );
	$sfsi_premium_url_shortner_icons_names_list = serialize( $IconsListurlShortner['sfsi_premium_url_shortner_icons_names_list'] );

	$sfsi_plus_url_shorting_api_type_setting = isset( $_POST["sfsi_plus_url_shorting_api_type_setting"] ) ? sanitize_text_field( $_POST["sfsi_plus_url_shorting_api_type_setting"] ) : 'no';

	$sfsi_plus_url_shortner_bitly_key  = isset( $_POST["sfsi_plus_url_shortner_bitly_key"] ) ? sanitize_text_field( $_POST["sfsi_plus_url_shortner_bitly_key"] ) : '';
	$sfsi_plus_url_shortner_google_key = isset( $_POST["sfsi_plus_url_shortner_google_key"] ) ? sanitize_text_field( $_POST["sfsi_plus_url_shortner_google_key"] ) : '';

	$sfsi_plus_disable_usm_og_meta_tags = isset( $_POST["sfsi_plus_disable_usm_og_meta_tags"] ) ? $_POST["sfsi_plus_disable_usm_og_meta_tags"] : "no";

	if ( $sfsi_plus_horizontal_verical_Alignment == 'Vertical' ) {
		$sfsi_plus_icons_perRow = 1;
	}

	if ( $sfsi_plus_mobile_horizontal_verical_Alignment == 'Vertical' ) {
		$sfsi_plus_mobile_icons_perRow = 1;
	}

	$sfsi_plus_custom_css = isset( $_POST["sfsi_plus_custom_css"] ) ? preg_replace( '#(<.*?>)(.*?)(</.*?>)#', '', $_POST["sfsi_plus_custom_css"] ) : "";
	$sfsi_plus_custom_css = str_replace( "\\", "", sfsi_sanitize_textarea_field( $sfsi_plus_custom_css ) );
	$sfsi_plus_custom_css = str_replace( "'", "", str_replace( '"', '', $sfsi_plus_custom_css ) );

	$sfsi_plus_custom_admin_css = isset( $_POST["sfsi_plus_custom_admin_css"] ) ? preg_replace( '#(<.*?>)(.*?)(</.*?>)#', '', $_POST["sfsi_plus_custom_admin_css"] ) : "";
	$sfsi_plus_custom_admin_css = str_replace( "\\", "", sfsi_sanitize_textarea_field( $sfsi_plus_custom_admin_css ) );
	$sfsi_plus_custom_admin_css = str_replace( "'", "", str_replace( '"', '', $sfsi_plus_custom_admin_css ) );

	$sfsi_plus_cumulative_count_active               = isset( $_POST["sfsi_plus_cumulative_count_active"] ) ? $_POST["sfsi_plus_cumulative_count_active"] : "no";
	$sfsi_plus_http_cumulative_count_active          = isset( $_POST["sfsi_plus_http_cumulative_count_active"] ) ? $_POST["sfsi_plus_http_cumulative_count_active"] : "no";
	$sfsi_plus_http_cumulative_count_new_domain      = isset( $_POST["sfsi_plus_http_cumulative_count_new_domain"] ) ? $_POST["sfsi_plus_http_cumulative_count_new_domain"] : "";
	$sfsi_plus_http_cumulative_count_previous_domain = isset( $_POST["sfsi_plus_http_cumulative_count_previous_domain"] ) ? $_POST["sfsi_plus_http_cumulative_count_previous_domain"] : "";
	$sfsi_plus_facebook_cumulative_count_active      = isset( $_POST["sfsi_plus_facebook_cumulative_count_active"] ) ? $_POST["sfsi_plus_facebook_cumulative_count_active"] : "no";
	$sfsi_plus_pinterest_cumulative_count_active     = isset( $_POST["sfsi_plus_pinterest_cumulative_count_active"] ) ? $_POST["sfsi_plus_pinterest_cumulative_count_active"] : "no";

	$sfsi_plus_loadjquery  = isset( $_POST["sfsi_plus_loadjquery"] ) ? sanitize_text_field( $_POST["sfsi_plus_loadjquery"] ) : "yes";
	$sfsi_plus_loadjscript = isset( $_POST["sfsi_plus_loadjscript"] ) ? sanitize_text_field( $_POST["sfsi_plus_loadjscript"] ) : "yes";


	$sfsi_plus_more_jscript_fileName = isset( $_POST["sfsi_plus_more_jscript_fileName"] ) ? sanitize_text_field( $_POST["sfsi_plus_more_jscript_fileName"] ) : "";
	$sfsi_plus_icons_suppress_errors = isset( $_POST["sfsi_plus_icons_suppress_errors"] ) ? $_POST["sfsi_plus_icons_suppress_errors"] : 'no';

	$sfsi_plus_nofollow_links = isset( $_POST["sfsi_plus_nofollow_links"] ) ? sanitize_text_field( $_POST["sfsi_plus_nofollow_links"] ) : 'no';

	$sfsi_premium_static_path = isset( $_POST["sfsi_premium_static_path"] ) ? $_POST["sfsi_premium_static_path"] : '';

	$sfsi_plus_jscript_fileName = array();
	if ( isset( $_POST["sfsi_premium_jscriptFileName"] ) ) {
		foreach ( $_POST["sfsi_premium_jscriptFileName"] as $sfsi_plus_jscript_file ) {
			$sfsi_plus_jscript_fileName_url = esc_url( $sfsi_plus_jscript_file );
			if ( '' !== $sfsi_plus_jscript_fileName_url ) {
				$sfsi_plus_jscript_fileName[] = $sfsi_plus_jscript_fileName_url;
			}
		}
	}
	$sfsi_premium_pinterest_sharing_texts_and_pics = isset( $_POST["sfsi_premium_pinterest_sharing_texts_and_pics"] ) ? sanitize_text_field( $_POST["sfsi_premium_pinterest_sharing_texts_and_pics"] ) : 'no';
	$sfsi_premium_pinterest_placements             = isset( $_POST["sfsi_premium_pinterest_placements"] ) ? sanitize_text_field( $_POST["sfsi_premium_pinterest_placements"] ) : 'no';
	$sfsi_plus_counts_without_slash                = isset( $_POST["sfsi_plus_counts_without_slash"] ) ? $_POST["sfsi_plus_counts_without_slash"] : "no";
	$sfsi_plus_hook_priority_value                 = isset( $_POST["sfsi_plus_hook_priority_value"] )
		? intval( trim( $_POST["sfsi_plus_hook_priority_value"] ) )
		: '';
	$sfsi_plus_change_number_format                = isset( $_POST["sfsi_plus_change_number_format"] ) ? sanitize_text_field( $_POST["sfsi_plus_change_number_format"] ) : "yes";
	$sfsi_plus_disable_promotions                  = isset( $_POST["sfsi_plus_disable_promotions"] ) ? sanitize_text_field( $_POST["sfsi_plus_disable_promotions"] ) : "no";

	// var_dump($sfsi_plus_icon_hover_switch_include_taxonomies);die();
	/* size and spacing of icons */
	$up_option5 = array(
		'sfsi_plus_icons_size'            => intval( $sfsi_plus_icons_size ),
		'sfsi_plus_icons_spacing'         => intval( $sfsi_plus_icons_spacing ),
		'sfsi_plus_icons_verical_spacing' => intval( $sfsi_plus_icons_verical_spacing ),

		'sfsi_plus_mobile_icon_alignment_setting'        => sanitize_text_field( $sfsi_plus_mobile_icon_alignment_setting ),
		'sfsi_plus_mobile_horizontal_verical_Alignment'  => sanitize_text_field( $sfsi_plus_mobile_horizontal_verical_Alignment ),
		'sfsi_plus_mobile_icons_Alignment_via_widget'    => sanitize_text_field( $sfsi_plus_mobile_icons_Alignment_via_widget ),
		'sfsi_plus_mobile_icons_Alignment_via_shortcode' => sanitize_text_field( $sfsi_plus_mobile_icons_Alignment_via_shortcode ),
		'sfsi_plus_mobile_icons_Alignment'               => sanitize_text_field( $sfsi_plus_mobile_icons_Alignment ),
		'sfsi_plus_mobile_icons_perRow'                  => intval( $sfsi_plus_mobile_icons_perRow ),
		'sfsi_plus_mobile_icon_setting'                  => sanitize_text_field( $sfsi_plus_mobile_icon_setting ),
		'sfsi_plus_icons_mobilesize'                     => intval( $sfsi_plus_icons_mobilesize ),
		'sfsi_plus_icons_mobilespacing'                  => intval( $sfsi_plus_icons_mobilespacing ),
		'sfsi_plus_icons_verical_mobilespacing'          => intval( $sfsi_plus_icons_verical_mobilespacing ),

		'sfsi_plus_horizontal_verical_Alignment'  => sanitize_text_field( $sfsi_plus_horizontal_verical_Alignment ),
		'sfsi_plus_icons_Alignment_via_shortcode' => sanitize_text_field( $sfsi_plus_icons_Alignment_via_shortcode ),
		'sfsi_plus_icons_Alignment_via_widget'    => sanitize_text_field( $sfsi_plus_icons_Alignment_via_widget ),

		'sfsi_plus_icons_Alignment'          => sanitize_text_field( $sfsi_plus_icons_Alignment ),
		'sfsi_plus_icons_perRow'             => intval( $sfsi_plus_icons_perRow ),
		'sfsi_plus_follow_icons_language'    => sanitize_text_field( $sfsi_plus_follow_icons_language ),
		'sfsi_plus_facebook_icons_language'  => sanitize_text_field( $sfsi_plus_facebook_icons_language ),
		'sfsi_plus_youtube_icons_language'   => sanitize_text_field( $sfsi_plus_youtube_icons_language ),
		'sfsi_plus_twitter_icons_language'   => sanitize_text_field( $sfsi_plus_twitter_icons_language ),
		'sfsi_plus_linkedin_icons_language'  => sanitize_text_field( $sfsi_plus_linkedin_icons_language ),
		'sfsi_plus_icons_language'           => sanitize_text_field( $sfsi_plus_icons_language ),
		'sfsi_plus_icons_ClickPageOpen'      => sanitize_text_field( $sfsi_plus_icons_ClickPageOpen ),
		'sfsi_plus_icons_AddNoopener'        => sanitize_text_field( $sfsi_plus_icons_AddNoopener ),
		'sfsi_plus_icons_float'              => sanitize_text_field( $sfsi_plus_icons_float ),
		'sfsi_plus_disable_floaticons'       => sanitize_text_field( $sfsi_plus_disable_floaticons ),
		'sfsi_plus_disable_viewport'         => sanitize_text_field( $sfsi_plus_disable_viewport ),
		'sfsi_plus_icons_floatPosition'      => sanitize_text_field( $sfsi_plus_icons_floatPosition ),
		'sfsi_plus_icons_stick'              => sanitize_text_field( $sfsi_plus_icons_stick ),
		/* mouse over texts */
		'sfsi_plus_rss_MouseOverText'        => sanitize_text_field( $sfsi_plus_rss_MouseOverText ),
		'sfsi_plus_email_MouseOverText'      => sanitize_text_field( $sfsi_plus_email_MouseOverText ),
		'sfsi_plus_twitter_MouseOverText'    => sanitize_text_field( $sfsi_plus_twitter_MouseOverText ),
		'sfsi_plus_facebook_MouseOverText'   => sanitize_text_field( $sfsi_plus_facebook_MouseOverText ),
		'sfsi_plus_linkedIn_MouseOverText'   => sanitize_text_field( $sfsi_plus_linkedIn_MouseOverText ),
		'sfsi_plus_pinterest_MouseOverText'  => sanitize_text_field( $sfsi_plus_pinterest_MouseOverText ),
		'sfsi_plus_youtube_MouseOverText'    => sanitize_text_field( $sfsi_plus_youtube_MouseOverText ),
		'sfsi_plus_share_MouseOverText'      => sanitize_text_field( $sfsi_plus_share_MouseOverText ),
		'sfsi_plus_instagram_MouseOverText'  => sanitize_text_field( $sfsi_plus_instagram_MouseOverText ),
		'sfsi_plus_threads_MouseOverText'    => sanitize_text_field( $sfsi_plus_threads_MouseOverText ),
		'sfsi_plus_bluesky_MouseOverText'    => sanitize_text_field( $sfsi_plus_bluesky_MouseOverText ),
		'sfsi_plus_ria_MouseOverText'        => sanitize_text_field( $sfsi_plus_ria_MouseOverText ),
		'sfsi_plus_inha_MouseOverText'       => sanitize_text_field( $sfsi_plus_inha_MouseOverText ),
		'sfsi_plus_houzz_MouseOverText'      => sanitize_text_field( $sfsi_plus_houzz_MouseOverText ),
		'sfsi_plus_snapchat_MouseOverText'   => sanitize_text_field( $sfsi_plus_snapchat_MouseOverText ),
		'sfsi_plus_whatsapp_MouseOverText'   => sanitize_text_field( $sfsi_plus_whatsapp_MouseOverText ),
		'sfsi_plus_skype_MouseOverText'      => sanitize_text_field( $sfsi_plus_skype_MouseOverText ),
		'sfsi_plus_vimeo_MouseOverText'      => sanitize_text_field( $sfsi_plus_vimeo_MouseOverText ),
		'sfsi_plus_soundcloud_MouseOverText' => sanitize_text_field( $sfsi_plus_soundcloud_MouseOverText ),
		'sfsi_plus_yummly_MouseOverText'     => sanitize_text_field( $sfsi_plus_yummly_MouseOverText ),
		'sfsi_plus_flickr_MouseOverText'     => sanitize_text_field( $sfsi_plus_flickr_MouseOverText ),
		'sfsi_plus_reddit_MouseOverText'     => sanitize_text_field( $sfsi_plus_reddit_MouseOverText ),
		'sfsi_plus_tumblr_MouseOverText'     => sanitize_text_field( $sfsi_plus_tumblr_MouseOverText ),

		'sfsi_plus_fbmessenger_MouseOverText' => sanitize_text_field( $sfsi_plus_fbmessenger_MouseOverText ),
		'sfsi_plus_gab_MouseOverText'         => sanitize_text_field( $sfsi_plus_gab_MouseOverText ),
		'sfsi_plus_mix_MouseOverText'         => sanitize_text_field( $sfsi_plus_mix_MouseOverText ),
		'sfsi_plus_ok_MouseOverText'          => sanitize_text_field( $sfsi_plus_ok_MouseOverText ),
		'sfsi_plus_telegram_MouseOverText'    => sanitize_text_field( $sfsi_plus_telegram_MouseOverText ),
		'sfsi_plus_vk_MouseOverText'          => sanitize_text_field( $sfsi_plus_vk_MouseOverText ),
		'sfsi_plus_weibo_MouseOverText'       => sanitize_text_field( $sfsi_plus_weibo_MouseOverText ),
		'sfsi_plus_xing_MouseOverText'        => sanitize_text_field( $sfsi_plus_xing_MouseOverText ),
		'sfsi_plus_copylink_MouseOverText'    => sanitize_text_field( $sfsi_plus_copylink_MouseOverText ),
		'sfsi_plus_mastodon_MouseOverText'    => sanitize_text_field( $sfsi_plus_mastodon_MouseOverText ),

		'sfsi_plus_custom_MouseOverTexts' => $sfsi_plus_custom_MouseOverTexts,

		'sfsi_order_icons_desktop' => $sfsi_order_icons_desktop,
		'sfsi_order_icons_mobile'  => $sfsi_order_icons_mobile,

		'sfsi_plus_mobile_icons_order_setting' => $sfsi_plus_mobile_icons_order_setting,

		'sfsi_plus_Facebook_linking'           => sanitize_text_field( $sfsi_plus_Facebook_linking ),
		'sfsi_plus_facebook_linkingcustom_url' => esc_url( $sfsi_plus_facebook_linkingcustom_url ),
		'sfsi_plus_tooltip_alighn'             => $sfsi_plus_tooltip_alighn,
		'sfsi_plus_tooltip_border_Color'       => sfsi_plus_sanitize_hex_color( $sfsi_plus_tooltip_border_Color ),
		'sfsi_plus_tooltip_Color'              => sfsi_plus_sanitize_hex_color( $sfsi_plus_tooltip_Color ),

		'sfsi_custom_social_data_post_types_data' => $sfsi_custom_social_data_post_types_data,

		'sfsi_plus_social_sharing_options' => $sfsi_plus_social_sharing_options,
		'sfsiSocialMediaImage'             => $sfsiSocialMediaImage,
		'sfsiSocialtTitleTxt'              => $sfsiSocialtTitleTxt,
		'sfsiSocialDescription'            => $sfsiSocialDescription,
		'sfsiSocialPinterestImage'         => $sfsiSocialPinterestImage,
		'sfsiSocialPinterestDesc'          => $sfsiSocialPinterestDesc,
		'sfsiSocialTwitterDesc'            => $sfsiSocialTwitterDesc,

		'sfsi_plus_twitter_aboutPageText'       => sanitize_text_field( $sfsi_plus_twitter_aboutPageText ),
		'sfsi_plus_twitter_twtAddCard'          => sanitize_text_field( $sfsi_plus_twitter_twtAddCard ),
		'sfsi_plus_twitter_twtCardType'         => sanitize_text_field( $sfsi_plus_twitter_twtCardType ),
		'sfsi_plus_twitter_card_twitter_handle' => sanitize_text_field( $sfsi_plus_twitter_card_twitter_handle ),

		'sfsi_premium_url_shortner_icons_names_list' => $sfsi_premium_url_shortner_icons_names_list,
		'sfsi_plus_url_shorting_api_type_setting'    => sanitize_text_field( $sfsi_plus_url_shorting_api_type_setting ),
		'sfsi_plus_url_shortner_bitly_key'           => sanitize_text_field( $sfsi_plus_url_shortner_bitly_key ),
		'sfsi_plus_url_shortner_google_key'          => sanitize_text_field( $sfsi_plus_url_shortner_google_key ),
		'sfsi_plus_disable_usm_og_meta_tags'         => $sfsi_plus_disable_usm_og_meta_tags,
		'sfsi_plus_custom_css'                       => serialize( $sfsi_plus_custom_css ),
		'sfsi_plus_custom_admin_css'                 => serialize( $sfsi_plus_custom_admin_css ),

		'sfsi_plus_cumulative_count_active'               => $sfsi_plus_cumulative_count_active,
		'sfsi_plus_http_cumulative_count_active'          => $sfsi_plus_http_cumulative_count_active,
		'sfsi_plus_http_cumulative_count_new_domain'      => $sfsi_plus_http_cumulative_count_new_domain,
		'sfsi_plus_http_cumulative_count_previous_domain' => $sfsi_plus_http_cumulative_count_previous_domain,
		'sfsi_plus_facebook_cumulative_count_active'      => $sfsi_plus_facebook_cumulative_count_active,
		'sfsi_plus_pinterest_cumulative_count_active'     => $sfsi_plus_pinterest_cumulative_count_active,

		'sfsi_plus_loadjquery'            => $sfsi_plus_loadjquery,
		'sfsi_plus_icons_suppress_errors' => sanitize_text_field( $sfsi_plus_icons_suppress_errors ),
		'sfsi_plus_nofollow_links'        => sanitize_text_field( $sfsi_plus_nofollow_links ),

		'sfsi_premium_static_path'                      => sanitize_text_field( $sfsi_premium_static_path ),
		'sfsi_plus_loadjscript'                         => $sfsi_plus_loadjscript,
		'sfsi_plus_jscript_fileName'                    => sanitize_text_field( $sfsi_plus_jscript_fileName ),
		'sfsi_plus_more_jscript_fileName'               => sanitize_text_field( $sfsi_plus_more_jscript_fileName ),
		'sfsi_plus_jscript_fileName'                    => $sfsi_plus_jscript_fileName,
		'sfsi_premium_featured_image_as_og_image'       => $sfsi_premium_featured_image_as_og_image,
		'sfsi_premium_pinterest_sharing_texts_and_pics' => sanitize_text_field( $sfsi_premium_pinterest_sharing_texts_and_pics ),
		'sfsi_premium_pinterest_placements'             => sanitize_text_field( $sfsi_premium_pinterest_placements ),
		'sfsi_plus_mobile_open_type_setting'            => sanitize_text_field( $sfsi_plus_mobile_open_type_setting ),
		'sfsi_plus_icons_mobile_ClickPageOpen'          => sanitize_text_field( $sfsi_plus_icons_mobile_ClickPageOpen ),
		'sfsi_plus_counts_without_slash'                => $sfsi_plus_counts_without_slash,
		'sfsi_plus_hook_priority_value'                 => intval( $sfsi_plus_hook_priority_value ),
		'sfsi_plus_change_number_format'                => $sfsi_plus_change_number_format,
		'sfsi_plus_disable_promotions'                  => $sfsi_plus_disable_promotions,
	);
	update_option( 'sfsi_premium_section5_options', serialize( $up_option5 ) );
	header( 'Content-Type: application/json' );
	echo json_encode( array( "success" ) );
	exit;
	// echo json_encode($up_option5); exit;
}

/* save settings for section 6 */
add_action( 'wp_ajax_plus_updateSrcn6', 'sfsi_plus_options_updater6' );
function sfsi_plus_options_updater6() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step6" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$sfsi_plus_show_Onposts        = isset( $_POST["sfsi_plus_show_Onposts"] ) ? sanitize_text_field( $_POST["sfsi_plus_show_Onposts"] ) : 'no';
	$sfsi_plus_icons_postPositon   = isset( $_POST["sfsi_plus_icons_postPositon"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_postPositon"] ) : '';
	$sfsi_plus_icons_alignment     = isset( $_POST["sfsi_plus_icons_alignment"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_alignment"] ) : 'center-right';
	$sfsi_plus_textBefor_icons     = isset( $_POST["sfsi_plus_textBefor_icons"] ) ? sanitize_text_field( $_POST["sfsi_plus_textBefor_icons"] ) : '';
	$sfsi_plus_icons_DisplayCounts = isset( $_POST["sfsi_plus_icons_DisplayCounts"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_DisplayCounts"] ) : 'no';
	/* post options */
	$up_option6 = array(
		'sfsi_plus_show_Onposts'        => sanitize_text_field( $sfsi_plus_show_Onposts ),
		'sfsi_plus_icons_postPositon'   => sanitize_text_field( $sfsi_plus_icons_postPositon ),
		'sfsi_plus_icons_alignment'     => sanitize_text_field( $sfsi_plus_icons_alignment ),
		'sfsi_plus_textBefor_icons'     => sanitize_text_field( stripslashes( $sfsi_plus_textBefor_icons ) ),
		'sfsi_plus_icons_DisplayCounts' => sanitize_text_field( $sfsi_plus_icons_DisplayCounts ),
	);
	update_option( 'sfsi_premium_section6_options', serialize( $up_option6 ) );
	header( 'Content-Type: application/json' );
	echo json_encode( array( "success" ) );
	exit;
}

/* save settings for section 7 */
add_action( 'wp_ajax_plus_updateSrcn7', 'sfsi_plus_options_updater7' );
function sfsi_plus_options_updater7() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step7" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$sfsi_plus_popup_text = isset( $_POST["sfsi_plus_popup_text"] ) ? stripslashes( sfsi_sanitize_textarea_field( $_POST["sfsi_plus_popup_text"], true ) ) : "";

	$sfsi_plus_popup_background_color = isset( $_POST["sfsi_plus_popup_background_color"] )
		? $_POST["sfsi_plus_popup_background_color"]
		: '#fffff';
	$sfsi_plus_popup_border_color     = isset( $_POST["sfsi_plus_popup_border_color"] )
		? $_POST["sfsi_plus_popup_border_color"]
		: 'center-right';
	$sfsi_plus_popup_border_thickness = isset( $_POST["sfsi_plus_popup_border_thickness"] ) ? intval( $_POST["sfsi_plus_popup_border_thickness"] ) : '';
	$sfsi_plus_popup_border_shadow    = isset( $_POST["sfsi_plus_popup_border_shadow"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_border_shadow"] ) : 'no';
	$sfsi_plus_popup_font             = isset( $_POST["sfsi_plus_popup_font"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_font"] ) : '';
	$sfsi_plus_popup_fontSize         = isset( $_POST["sfsi_plus_popup_fontSize"] ) ? intval( $_POST["sfsi_plus_popup_fontSize"] ) : 'no';
	$sfsi_plus_popup_fontStyle        = isset( $_POST["sfsi_plus_popup_fontStyle"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_fontStyle"] ) : '';
	$sfsi_plus_popup_fontColor        = isset( $_POST["sfsi_plus_popup_fontColor"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_fontColor"] ) : 'no';
	$sfsi_plus_Show_popupOn           = isset( $_POST["sfsi_plus_Show_popupOn"] ) ? sanitize_text_field( $_POST["sfsi_plus_Show_popupOn"] ) : '';

	$sfsi_plus_Show_popupOn_PageIDs                = isset( $_POST["sfsi_plus_Show_popupOn_PageIDs"] ) ? serialize( $_POST["sfsi_plus_Show_popupOn_PageIDs"] ) : '';
	$sfsi_plus_Show_popupOn_somepages_blogpage     = isset( $_POST["sfsi_plus_Show_popupOn_somepages_blogpage"] ) ? $_POST["sfsi_plus_Show_popupOn_somepages_blogpage"] : '';
	$sfsi_plus_Show_popupOn_somepages_selectedpage = isset( $_POST["sfsi_plus_Show_popupOn_somepages_selectedpage"] ) ? $_POST["sfsi_plus_Show_popupOn_somepages_selectedpage"] : '';

	$sfsi_plus_Shown_pop                   = isset( $_POST["sfsi_plus_Shown_pop"] ) ? $_POST["sfsi_plus_Shown_pop"] : array( '' );
	$sfsi_plus_Shown_popupOnceTime         = isset( $_POST["sfsi_plus_Shown_popupOnceTime"] ) ? intval( $_POST["sfsi_plus_Shown_popupOnceTime"] ) : 'no';
	$sfsi_plus_Shown_popuplimitPerUserTime = isset( $_POST["sfsi_plus_Shown_popuplimitPerUserTime"] )
		? $_POST["sfsi_plus_Shown_popuplimitPerUserTime"]
		: '';

	$sfsi_plus_popup_limit       = isset( $_POST["sfsi_plus_popup_limit"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_limit"] ) : 'no';
	$sfsi_plus_popup_limit_count = isset( $_POST["sfsi_plus_popup_limit_count"] ) ? $_POST["sfsi_plus_popup_limit_count"] : '';
	$sfsi_plus_popup_limit_type  = isset( $_POST["sfsi_plus_popup_limit_type"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_limit_type"] ) : '';

	$sfsi_plus_popup_type_iconsOrForm = isset( $_POST["sfsi_plus_popup_type_iconsOrForm"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_type_iconsOrForm"] ) : 'icons';

	$sfsi_plus_popup_show_on_desktop = isset( $_POST["sfsi_plus_popup_show_on_desktop"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_show_on_desktop"] ) : 'no';
	$sfsi_plus_popup_show_on_mobile  = isset( $_POST["sfsi_plus_popup_show_on_mobile"] ) ? sanitize_text_field( $_POST["sfsi_plus_popup_show_on_mobile"] ) : 'no';

	$sfsi_plus_Hide_popupOnScroll        = isset( $_POST["sfsi_plus_Hide_popupOnScroll"] ) ? sanitize_text_field( $_POST["sfsi_plus_Hide_popupOnScroll"] ) : 'no';
	$sfsi_plus_Hide_popupOn_OutsideClick = isset( $_POST["sfsi_plus_Hide_popupOn_OutsideClick"] ) ? sanitize_text_field( $_POST["sfsi_plus_Hide_popupOn_OutsideClick"] ) : 'no';


	/* icons pop options */
	$up_option7 = array(
		'sfsi_plus_popup_text'             => $sfsi_plus_popup_text,
		'sfsi_plus_popup_font'             => sanitize_text_field( $sfsi_plus_popup_font ),
		'sfsi_plus_popup_fontColor'        => sfsi_plus_sanitize_hex_color( $sfsi_plus_popup_fontColor ),
		'sfsi_plus_popup_fontSize'         => intval( $sfsi_plus_popup_fontSize ),
		'sfsi_plus_popup_fontStyle'        => sanitize_text_field( $sfsi_plus_popup_fontStyle ),
		'sfsi_plus_popup_background_color' => sfsi_plus_sanitize_hex_color( $sfsi_plus_popup_background_color ),
		'sfsi_plus_popup_border_color'     => sfsi_plus_sanitize_hex_color( $sfsi_plus_popup_border_color ),
		'sfsi_plus_popup_border_thickness' => intval( $sfsi_plus_popup_border_thickness ),
		'sfsi_plus_popup_border_shadow'    => sanitize_text_field( $sfsi_plus_popup_border_shadow ),
		'sfsi_plus_Show_popupOn'           => sanitize_text_field( $sfsi_plus_Show_popupOn ),
		'sfsi_plus_Show_popupOn_PageIDs'   => $sfsi_plus_Show_popupOn_PageIDs,

		'sfsi_plus_Show_popupOn_somepages_blogpage'     => $sfsi_plus_Show_popupOn_somepages_blogpage,
		'sfsi_plus_Show_popupOn_somepages_selectedpage' => $sfsi_plus_Show_popupOn_somepages_selectedpage,

		'sfsi_plus_Shown_pop'           => $sfsi_plus_Shown_pop,
		'sfsi_plus_Shown_popupOnceTime' => intval( $sfsi_plus_Shown_popupOnceTime ),

		'sfsi_plus_Hide_popupOnScroll'        => sanitize_text_field( $sfsi_plus_Hide_popupOnScroll ),
		'sfsi_plus_Hide_popupOn_OutsideClick' => sanitize_text_field( $sfsi_plus_Hide_popupOn_OutsideClick ),

		"sfsi_plus_popup_limit"            => sanitize_text_field( $sfsi_plus_popup_limit ),
		"sfsi_plus_popup_limit_count"      => intval( $sfsi_plus_popup_limit_count ),
		"sfsi_plus_popup_limit_type"       => sanitize_text_field( $sfsi_plus_popup_limit_type ),
		'sfsi_plus_popup_type_iconsOrForm' => sanitize_text_field( $sfsi_plus_popup_type_iconsOrForm ),

		'sfsi_plus_popup_show_on_desktop' => $sfsi_plus_popup_show_on_desktop,
		'sfsi_plus_popup_show_on_mobile'  => $sfsi_plus_popup_show_on_mobile
		//'sfsi_plus_Shown_popuplimitPerUserTime'   => $sfsi_plus_Shown_popuplimitPerUserTime,
	);
	update_option( 'sfsi_premium_section7_options', serialize( $up_option7 ) );
	header( 'Content-Type: application/json' );
	echo json_encode( array( 'success' ) );
	exit;
}

/* save settings for section 3 */
add_action( 'wp_ajax_plus_updateSrcn8', 'sfsi_plus_options_updater8' );
function sfsi_plus_options_updater8() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step8" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$sfsi_plus_show_via_widget     = isset( $_POST["sfsi_plus_show_via_widget"] ) ? sanitize_text_field( $_POST["sfsi_plus_show_via_widget"] ) : 'no';
	$sfsi_plus_float_on_page       = isset( $_POST["sfsi_plus_float_on_page"] ) ? sanitize_text_field( $_POST["sfsi_plus_float_on_page"] ) : 'no';
	$sfsi_plus_float_page_position = isset( $_POST["sfsi_plus_float_page_position"] ) ? sanitize_text_field( $_POST["sfsi_plus_float_page_position"] ) : 'no';
	$sfsi_plus_make_icon           = isset( $_POST["sfsi_plus_make_icon"] ) ? sanitize_text_field( $_POST["sfsi_plus_make_icon"] ) : 'float';

	$sfsi_plus_icons_floatMargin_top    = isset( $_POST["sfsi_plus_icons_floatMargin_top"] ) ? intval( $_POST["sfsi_plus_icons_floatMargin_top"] ) : '';
	$sfsi_plus_icons_floatMargin_bottom = isset( $_POST["sfsi_plus_icons_floatMargin_bottom"] ) ? intval( $_POST["sfsi_plus_icons_floatMargin_bottom"] ) : '';
	$sfsi_plus_icons_floatMargin_left   = isset( $_POST["sfsi_plus_icons_floatMargin_left"] ) ? intval( $_POST["sfsi_plus_icons_floatMargin_left"] ) : '';
	$sfsi_plus_icons_floatMargin_right  = isset( $_POST["sfsi_plus_icons_floatMargin_right"] ) ? intval( $_POST["sfsi_plus_icons_floatMargin_right"] ) : '';

	$sfsi_plus_place_item_manually = isset( $_POST["sfsi_plus_place_item_manually"] ) ? sanitize_text_field( $_POST["sfsi_plus_place_item_manually"] ) : 'no';

	// $sfsi_plus_place_rect_shortcode       = isset($_POST["sfsi_plus_place_rect_shortcode"]) ? $_POST["sfsi_plus_place_rect_shortcode"] : 'no';

	$sfsi_plus_shortcode_horizontal_verical_Alignment        = isset( $_POST['sfsi_plus_shortcode_horizontal_verical_Alignment'] ) ? sanitize_text_field( $_POST['sfsi_plus_shortcode_horizontal_verical_Alignment'] ) : "Horizontal";
	$sfsi_plus_shortcode_mobile_horizontal_verical_Alignment = isset( $_POST['sfsi_plus_shortcode_mobile_horizontal_verical_Alignment'] ) ? sanitize_text_field( $_POST['sfsi_plus_shortcode_mobile_horizontal_verical_Alignment'] ) : "Horizontal";

	$sfsi_plus_widget_horizontal_verical_Alignment        = isset( $_POST['sfsi_plus_widget_horizontal_verical_Alignment'] ) ? sanitize_text_field( $_POST['sfsi_plus_widget_horizontal_verical_Alignment'] ) : "Horizontal";
	$sfsi_plus_widget_mobile_horizontal_verical_Alignment = isset( $_POST['sfsi_plus_widget_mobile_horizontal_verical_Alignment'] ) ? sanitize_text_field( $_POST['sfsi_plus_widget_mobile_horizontal_verical_Alignment'] ) : "Horizontal";

	$sfsi_plus_float_horizontal_verical_Alignment        = isset( $_POST['sfsi_plus_float_horizontal_verical_Alignment'] ) ? sanitize_text_field( $_POST['sfsi_plus_float_horizontal_verical_Alignment'] ) : "Horizontal";
	$sfsi_plus_float_mobile_horizontal_verical_Alignment = isset( $_POST['sfsi_plus_float_mobile_horizontal_verical_Alignment'] ) ? sanitize_text_field( $_POST['sfsi_plus_float_mobile_horizontal_verical_Alignment'] ) : "Horizontal";

	$sfsi_plus_beforeafterposts_horizontal_verical_Alignment        = isset( $_POST['sfsi_plus_beforeafterposts_horizontal_verical_Alignment'] ) ? sanitize_text_field( $_POST['sfsi_plus_beforeafterposts_horizontal_verical_Alignment'] ) : "Horizontal";
	$sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment = isset( $_POST['sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment'] ) ? sanitize_text_field( $_POST['sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment'] ) : "Horizontal";

	//$sfsi_plus_place_rectangle_icons_item_manually  = isset($_POST["sfsi_plus_place_rectangle_icons_item_manually"]) ? $_POST["sfsi_plus_place_rectangle_icons_item_manually"] : 'no';

	$sfsi_plus_show_item_onposts   = isset( $_POST["sfsi_plus_show_item_onposts"] ) ? sanitize_text_field( $_POST["sfsi_plus_show_item_onposts"] ) : 'no';
	$sfsi_plus_display_button_type = isset( $_POST["sfsi_plus_display_button_type"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_button_type"] ) : 'no';

	$sfsi_plus_post_icons_size             = isset( $_POST["sfsi_plus_post_icons_size"] ) ? intval( $_POST["sfsi_plus_post_icons_size"] ) : 40;
	$sfsi_plus_post_icons_spacing          = isset( $_POST["sfsi_plus_post_icons_spacing"] ) ? intval( $_POST["sfsi_plus_post_icons_spacing"] ) : 5;
	$sfsi_plus_post_icons_vertical_spacing = isset( $_POST["sfsi_plus_post_icons_vertical_spacing"] ) ? intval( $_POST["sfsi_plus_post_icons_vertical_spacing"] ) : 5;
	$sfsi_plus_show_Onposts                = isset( $_POST["sfsi_plus_show_Onposts"] ) ? sanitize_text_field( $_POST["sfsi_plus_show_Onposts"] ) : 'no';
	$sfsi_plus_textBefor_icons             = isset( $_POST["sfsi_plus_textBefor_icons"] ) ? sanitize_text_field( $_POST["sfsi_plus_textBefor_icons"] ) : 'Please follow and like us:';

	$sfsi_plus_icons_alignment      = isset( $_POST["sfsi_plus_icons_alignment"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_alignment"] ) : 'center-right';
	$sfsi_plus_icons_DisplayCounts  = isset( $_POST["sfsi_plus_icons_DisplayCounts"] ) ? sanitize_text_field( $_POST["sfsi_plus_icons_DisplayCounts"] ) : 'no';
	$sfsi_plus_display_before_posts = isset( $_POST["sfsi_plus_display_before_posts"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_before_posts"] ) : 'no';
	$sfsi_plus_display_after_posts  = isset( $_POST["sfsi_plus_display_after_posts"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_after_posts"] ) : 'no';

	$sfsi_plus_display_before_blogposts = isset( $_POST["sfsi_plus_display_before_blogposts"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_before_blogposts"] ) : 'no';
	$sfsi_plus_display_after_blogposts  = isset( $_POST["sfsi_plus_display_after_blogposts"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_after_blogposts"] ) : 'no';
	$sfsi_plus_rectsub                  = isset( $_POST["sfsi_plus_rectsub"] ) ? sanitize_text_field( $_POST["sfsi_plus_rectsub"] ) : 'no';
	$sfsi_plus_rectfb                   = isset( $_POST["sfsi_plus_rectfb"] ) ? sanitize_text_field( $_POST["sfsi_plus_rectfb"] ) : 'no';
	$sfsi_plus_recttwtr                 = isset( $_POST["sfsi_plus_recttwtr"] ) ? sanitize_text_field( $_POST["sfsi_plus_recttwtr"] ) : 'no';
	$sfsi_plus_rectpinit                = isset( $_POST["sfsi_plus_rectpinit"] ) ? sanitize_text_field( $_POST["sfsi_plus_rectpinit"] ) : 'no';
	$sfsi_plus_rectlinkedin             = isset( $_POST["sfsi_plus_rectlinkedin"] ) ? sanitize_text_field( $_POST["sfsi_plus_rectlinkedin"] ) : 'no';
	$sfsi_plus_rectreddit               = isset( $_POST["sfsi_plus_rectreddit"] ) ? sanitize_text_field( $_POST["sfsi_plus_rectreddit"] ) : 'no';
	$sfsi_plus_rectfbshare              = isset( $_POST["sfsi_plus_rectfbshare"] ) ? sanitize_text_field( $_POST["sfsi_plus_rectfbshare"] ) : 'no';

	$sfsi_plus_marginAbove_postIcon     = isset( $_POST["sfsi_plus_marginAbove_postIcon"] ) ? intval( $_POST["sfsi_plus_marginAbove_postIcon"] ) : '';
	$sfsi_plus_marginBelow_postIcon     = isset( $_POST["sfsi_plus_marginBelow_postIcon"] ) ? intval( $_POST["sfsi_plus_marginBelow_postIcon"] ) : '';
	$sfsi_plus_display_after_pageposts  = isset( $_POST["sfsi_plus_display_after_pageposts"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_after_pageposts"] ) : 'no';
	$sfsi_plus_display_before_pageposts = isset( $_POST["sfsi_plus_display_before_pageposts"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_before_pageposts"] ) : 'no';

	$sfsi_plus_choose_post_types = isset( $_POST["sfsi_plus_choose_post_types"] ) ? $_POST["sfsi_plus_choose_post_types"] : array();
	$sfsi_plus_choose_post_types = is_string( $sfsi_plus_choose_post_types ) ? (array) $sfsi_plus_choose_post_types : $sfsi_plus_choose_post_types;
	$sfsi_plus_choose_post_types = serialize( $sfsi_plus_choose_post_types );

	$sfsi_plus_choose_post_types_responsive = isset( $_POST["sfsi_plus_choose_post_types_responsive"] ) ? $_POST["sfsi_plus_choose_post_types_responsive"] : array();
	$sfsi_plus_choose_post_types_responsive = is_string( $sfsi_plus_choose_post_types_responsive ) ? (array) $sfsi_plus_choose_post_types_responsive : $sfsi_plus_choose_post_types_responsive;
	$sfsi_plus_choose_post_types_responsive = serialize( $sfsi_plus_choose_post_types_responsive );

	$sfsi_plus_taxonomies_for_icons = isset( $_POST["sfsi_plus_taxonomies_for_icons"] ) ? $_POST["sfsi_plus_taxonomies_for_icons"] : array();
	$sfsi_plus_taxonomies_for_icons = is_string( $sfsi_plus_taxonomies_for_icons ) ? (array) $sfsi_plus_taxonomies_for_icons : $sfsi_plus_taxonomies_for_icons;
	$sfsi_plus_taxonomies_for_icons = serialize( $sfsi_plus_taxonomies_for_icons );

	$sfsi_plus_widget_show_on_desktop = isset( $_POST["sfsi_plus_widget_show_on_desktop"] ) ? sanitize_text_field( $_POST["sfsi_plus_widget_show_on_desktop"] ) : 'no';
	$sfsi_plus_widget_show_on_mobile  = isset( $_POST["sfsi_plus_widget_show_on_mobile"] ) ? sanitize_text_field( $_POST["sfsi_plus_widget_show_on_mobile"] ) : 'no';

	$sfsi_plus_float_show_on_desktop = isset( $_POST["sfsi_plus_float_show_on_desktop"] ) ? sanitize_text_field( $_POST["sfsi_plus_float_show_on_desktop"] ) : 'no';
	$sfsi_plus_float_show_on_mobile  = isset( $_POST["sfsi_plus_float_show_on_mobile"] ) ? sanitize_text_field( $_POST["sfsi_plus_float_show_on_mobile"] ) : 'no';

	$sfsi_plus_shortcode_show_on_desktop = isset( $_POST["sfsi_plus_shortcode_show_on_desktop"] ) ? sanitize_text_field( $_POST["sfsi_plus_shortcode_show_on_desktop"] ) : 'no';
	$sfsi_plus_shortcode_show_on_mobile  = isset( $_POST["sfsi_plus_shortcode_show_on_mobile"] ) ? sanitize_text_field( $_POST["sfsi_plus_shortcode_show_on_mobile"] ) : 'no';

	$sfsi_plus_beforeafterposts_show_on_desktop = isset( $_POST["sfsi_plus_beforeafterposts_show_on_desktop"] ) ? sanitize_text_field( $_POST["sfsi_plus_beforeafterposts_show_on_desktop"] ) : 'no';
	$sfsi_plus_beforeafterposts_show_on_mobile  = isset( $_POST["sfsi_plus_beforeafterposts_show_on_mobile"] ) ? sanitize_text_field( $_POST["sfsi_plus_beforeafterposts_show_on_mobile"] ) : 'no';

	$sfsi_plus_rectangle_icons_shortcode_show_on_desktop = isset( $_POST["sfsi_plus_rectangle_icons_shortcode_show_on_desktop"] ) ? sanitize_text_field( $_POST["sfsi_plus_rectangle_icons_shortcode_show_on_desktop"] ) : 'no';
	$sfsi_plus_rectangle_icons_shortcode_show_on_mobile  = isset( $_POST["sfsi_plus_rectangle_icons_shortcode_show_on_mobile"] ) ? sanitize_text_field( $_POST["sfsi_plus_rectangle_icons_shortcode_show_on_mobile"] ) : 'no';


	$sfsi_plus_icons_rules          = isset( $_POST["sfsi_plus_icons_rules"] ) ? $_POST["sfsi_plus_icons_rules"] : 0;
	$sfsi_plus_place_item_gutenberg = isset( $_POST["sfsi_plus_place_item_gutenberg"] ) ? sanitize_text_field( $_POST["sfsi_plus_place_item_gutenberg"] ) : 'no';

	// **************************** Exclude rules STARTS ******************************* //

	$sfsi_plus_exclude_home           = isset( $_POST["sfsi_plus_exclude_home"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_home"] ) : 'no';
	$sfsi_plus_exclude_page           = isset( $_POST["sfsi_plus_exclude_page"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_page"] ) : 'no';
	$sfsi_plus_exclude_post           = isset( $_POST["sfsi_plus_exclude_post"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_post"] ) : 'no';
	$sfsi_plus_exclude_tag            = isset( $_POST["sfsi_plus_exclude_tag"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_tag"] ) : 'no';
	$sfsi_plus_exclude_category       = isset( $_POST["sfsi_plus_exclude_category"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_category"] ) : 'no';
	$sfsi_plus_exclude_date_archive   = isset( $_POST["sfsi_plus_exclude_date_archive"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_date_archive"] ) : 'no';
	$sfsi_plus_exclude_author_archive = isset( $_POST["sfsi_plus_exclude_author_archive"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_author_archive"] ) : 'no';
	$sfsi_plus_exclude_search         = isset( $_POST["sfsi_plus_exclude_search"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_search"] ) : 'no';
	$sfsi_plus_exclude_url            = isset( $_POST["sfsi_plus_exclude_url"] ) ? sanitize_text_field( $_POST["sfsi_plus_exclude_url"] ) : 'no';
	$sfsi_plus_urlKeywords            = isset( $_POST["sfsi_plus_urlKeywords"] ) ? $_POST["sfsi_plus_urlKeywords"] : array();

	// Exclude list for custom post types //
	$sfsi_plus_switch_exclude_custom_post_types = isset( $_POST["sfsi_plus_switch_exclude_custom_post_types"] ) ? sanitize_text_field( $_POST["sfsi_plus_switch_exclude_custom_post_types"] ) : "no";

	parse_str( urldecode( $_POST['sfsi_plus_list_exclude_custom_post_types'] ), $outputExPT );
	$sfsi_plus_list_exclude_custom_post_types = isset( $outputExPT['sfsi_plus_list_exclude_custom_post_types'] ) ? serialize( $outputExPT['sfsi_plus_list_exclude_custom_post_types'] ) : serialize( array() );

	//  Exclude list for custom post taxonomies //

	$sfsi_plus_switch_exclude_taxonomies = isset( $_POST["sfsi_plus_switch_exclude_taxonomies"] ) ? sanitize_text_field( $_POST["sfsi_plus_switch_exclude_taxonomies"] ) : "no";

	parse_str( urldecode( $_POST['sfsi_plus_list_exclude_taxonomies'] ), $outputExTax );
	$sfsi_plus_list_exclude_taxonomies = isset( $outputExTax['sfsi_plus_list_exclude_taxonomies'] ) ? serialize( $outputExTax['sfsi_plus_list_exclude_taxonomies'] ) : serialize( array() );

	// **************************** Exclude rules CLOSES ******************************* //

	// **************************** Include rules STARTS ******************************* //

	$sfsi_plus_include_home           = isset( $_POST["sfsi_plus_include_home"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_home"] ) : 'no';
	$sfsi_plus_include_page           = isset( $_POST["sfsi_plus_include_page"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_page"] ) : 'no';
	$sfsi_plus_include_post           = isset( $_POST["sfsi_plus_include_post"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_post"] ) : 'no';
	$sfsi_plus_include_tag            = isset( $_POST["sfsi_plus_include_tag"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_tag"] ) : 'no';
	$sfsi_plus_include_category       = isset( $_POST["sfsi_plus_include_category"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_category"] ) : 'no';
	$sfsi_plus_include_date_archive   = isset( $_POST["sfsi_plus_include_date_archive"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_date_archive"] ) : 'no';
	$sfsi_plus_include_author_archive = isset( $_POST["sfsi_plus_include_author_archive"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_author_archive"] ) : 'no';
	$sfsi_plus_include_search         = isset( $_POST["sfsi_plus_include_search"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_search"] ) : 'no';
	$sfsi_plus_include_url            = isset( $_POST["sfsi_plus_include_url"] ) ? sanitize_text_field( $_POST["sfsi_plus_include_url"] ) : 'no';
	$sfsi_plus_include_urlKeywords    = isset( $_POST["sfsi_plus_include_urlKeywords"] ) ? $_POST["sfsi_plus_include_urlKeywords"] : array();

	// include list for custom post types //

	$sfsi_plus_switch_include_custom_post_types = isset( $_POST["sfsi_plus_switch_include_custom_post_types"] ) ? sanitize_text_field( $_POST["sfsi_plus_switch_include_custom_post_types"] ) : "no";

	parse_str( urldecode( $_POST['sfsi_plus_list_include_custom_post_types'] ), $outputInPT );
	$sfsi_plus_list_include_custom_post_types = isset( $outputInPT['sfsi_plus_list_include_custom_post_types'] ) ? serialize( $outputInPT['sfsi_plus_list_include_custom_post_types'] ) : serialize( array() );

	//  include list for custom post taxonomies //

	$sfsi_plus_switch_include_taxonomies = isset( $_POST["sfsi_plus_switch_include_taxonomies"] ) ? $_POST["sfsi_plus_switch_include_taxonomies"] : "no";

	parse_str( urldecode( $_POST['sfsi_plus_list_include_taxonomies'] ), $outputInTax );
	$sfsi_plus_list_include_taxonomies = isset( $outputInTax['sfsi_plus_list_include_taxonomies'] ) ? serialize( $outputInTax['sfsi_plus_list_include_taxonomies'] ) : serialize( array() );

	// **************************** Include rules CLOSES ******************************* //

	$sfsi_plus_mobile_float            = isset( $_POST["sfsi_plus_mobile_float"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_float"] )
		: 'no';
	$sfsi_plus_mobile_widget           = isset( $_POST["sfsi_plus_mobile_widget"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_widget"] )
		: 'no';
	$sfsi_plus_mobile_shortcode        = isset( $_POST["sfsi_plus_mobile_shortcode"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_shortcode"] )
		: 'no';
	$sfsi_plus_mobile_beforeafterposts = isset( $_POST["sfsi_plus_mobile_beforeafterposts"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_beforeafterposts"] )
		: 'no';

	$sfsi_plus_float_page_mobileposition      = isset( $_POST["sfsi_plus_float_page_mobileposition"] )
		? sanitize_text_field( $_POST["sfsi_plus_float_page_mobileposition"] )
		: '';
	$sfsi_plus_make_mobileicon                = isset( $_POST["sfsi_plus_make_mobileicon"] )
		? sanitize_text_field( $_POST["sfsi_plus_make_mobileicon"] )
		: 'float';
	$sfsi_plus_icons_floatMargin_mobiletop    = isset( $_POST["sfsi_plus_icons_floatMargin_mobiletop"] )
		? intval( $_POST["sfsi_plus_icons_floatMargin_mobiletop"] )
		: '';
	$sfsi_plus_icons_floatMargin_mobilebottom = isset( $_POST["sfsi_plus_icons_floatMargin_mobilebottom"] )
		? intval( $_POST["sfsi_plus_icons_floatMargin_mobilebottom"] )
		: '';
	$sfsi_plus_icons_floatMargin_mobileleft   = isset( $_POST["sfsi_plus_icons_floatMargin_mobileleft"] )
		? intval( $_POST["sfsi_plus_icons_floatMargin_mobileleft"] )
		: '';
	$sfsi_plus_icons_floatMargin_mobileright  = isset( $_POST["sfsi_plus_icons_floatMargin_mobileright"] )
		? intval( $_POST["sfsi_plus_icons_floatMargin_mobileright"] )
		: '';

	$sfsi_plus_textBefor_icons_font_size = isset( $_POST["sfsi_plus_textBefor_icons_font_size"] )
		? intval( $_POST["sfsi_plus_textBefor_icons_font_size"] )
		: '20';

	$sfsi_plus_textBefor_icons_font = isset( $_POST["sfsi_plus_textBefor_icons_font"] )
		? sanitize_text_field( $_POST["sfsi_plus_textBefor_icons_font"] )
		: 'inherit';

	$sfsi_plus_textBefor_icons_fontcolor = isset( $_POST["sfsi_plus_textBefor_icons_fontcolor"] )
		? sanitize_text_field( $_POST["sfsi_plus_textBefor_icons_fontcolor"] )
		: '#000000';

	$sfsi_plus_textBefor_icons_font_type = isset( $_POST["sfsi_plus_textBefor_icons_font_type"] )
		? sanitize_text_field( $_POST["sfsi_plus_textBefor_icons_font_type"] )
		: 'normal';

	$sfsi_plus_display_on_all_icons                           = isset( $_POST["sfsi_plus_display_on_all_icons"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_on_all_icons"] ) : 'no';
	$sfsi_plus_display_rule_round_icon_widget                 = isset( $_POST["sfsi_plus_display_rule_round_icon_widget"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_round_icon_widget"] ) : 'yes';
	$sfsi_plus_display_rule_round_icon_define_location        = isset( $_POST["sfsi_plus_display_rule_round_icon_define_location"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_round_icon_define_location"] ) : 'yes';
	$sfsi_plus_display_rule_round_icon_shortcode              = isset( $_POST["sfsi_plus_display_rule_round_icon_shortcode"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_round_icon_shortcode"] ) : 'yes';
	$sfsi_plus_display_rule_round_icon_before_after_post      = isset( $_POST["sfsi_plus_display_rule_round_icon_before_after_post"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_round_icon_before_after_post"] ) : 'no';
	$sfsi_plus_display_rule_rect_icon_before_after_post       = isset( $_POST["sfsi_plus_display_rule_rect_icon_before_after_post"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_rect_icon_before_after_post"] ) : 'no';
	$sfsi_plus_display_rule_responsive_icon_before_after_post = isset( $_POST["sfsi_plus_display_rule_responsive_icon_before_after_post"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_responsive_icon_before_after_post"] ) : 'no';
	$sfsi_plus_display_rule_responsive_icon_shortcode         = isset( $_POST["sfsi_plus_display_rule_responsive_icon_shortcode"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_responsive_icon_shortcode"] ) : 'no';
	$sfsi_plus_display_rule_icon_sticky_bar                   = isset( $_POST["sfsi_plus_display_rule_icon_sticky_bar"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_icon_sticky_bar"] ) : 'no';
	$sfsi_plus_display_rule_rect_icon_shortcode               = isset( $_POST["sfsi_plus_display_rule_rect_icon_shortcode"] ) ? sanitize_text_field( $_POST["sfsi_plus_display_rule_rect_icon_shortcode"] ) : 'no';


	$sfsi_plus_icon_hover_show_pinterest  = isset( $_POST['sfsi_plus_icon_hover_show_pinterest'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_show_pinterest'] ) : 'no';
	$sfsi_plus_icon_hover_type            = isset( $_POST['sfsi_plus_icon_hover_type'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_type'] ) : 'square';
	$sfsi_plus_icon_hover_custom_icon_url = isset( $_POST['sfsi_plus_icon_hover_custom_icon_url'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_custom_icon_url'] ) : '';
	$sfsi_plus_icon_hover_language        = isset( $_POST['sfsi_plus_icon_hover_language'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_language'] ) : 'en_US';
	$sfsi_plus_icon_hover_placement       = isset( $_POST['sfsi_plus_icon_hover_placement'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_placement'] ) : 'top-left';
	$sfsi_plus_icon_hover_desktop         = isset( $_POST['sfsi_plus_icon_hover_desktop'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_desktop'] ) : 'yes';
	$sfsi_plus_icon_hover_mobile          = isset( $_POST['sfsi_plus_icon_hover_mobile'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_mobile'] ) : 'yes';
	$sfsi_plus_icon_hover_on_all_pages    = isset( $_POST['sfsi_plus_icon_hover_on_all_pages'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_on_all_pages'] ) : 'yes';
	$sfsi_plus_icon_hover_width           = isset( $_POST['sfsi_plus_icon_hover_width'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_width'] ) : '20';
	$sfsi_plus_icon_hover_height          = isset( $_POST['sfsi_plus_icon_hover_height'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_width'] ) : '20';

	$sfsi_plus_icon_hover_exclude_home           = isset( $_POST['sfsi_plus_icon_hover_exclude_home'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_home'] ) : 'no';
	$sfsi_plus_icon_hover_exclude_page           = isset( $_POST['sfsi_plus_icon_hover_exclude_page'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_page'] ) : 'no';
	$sfsi_plus_icon_hover_exclude_post           = isset( $_POST['sfsi_plus_icon_hover_exclude_post'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_post'] ) : 'no';
	$sfsi_plus_icon_hover_exclude_tag            = isset( $_POST['sfsi_plus_icon_hover_exclude_tag'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_tag'] ) : 'no';
	$sfsi_plus_icon_hover_exclude_category       = isset( $_POST['sfsi_plus_icon_hover_exclude_category'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_category'] ) : 'no';
	$sfsi_plus_icon_hover_exclude_date_archive   = isset( $_POST['sfsi_plus_icon_hover_exclude_date_archive'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_date_archive'] ) : 'no';
	$sfsi_plus_icon_hover_exclude_author_archive = isset( $_POST['sfsi_plus_icon_hover_exclude_author_archive'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_author_archive'] ) : 'no';
	$sfsi_plus_icon_hover_exclude_search         = isset( $_POST['sfsi_plus_icon_hover_exclude_search'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_search'] ) : 'no';
	$sfsi_plus_icon_hover_exclude_url            = isset( $_POST['sfsi_plus_icon_hover_exclude_url'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_exclude_url'] ) : 'no';

	$sfsi_plus_icon_hover_exclude_urlKeywords              = isset( $_POST['sfsi_plus_icon_hover_exclude_urlKeywords'] ) ? $_POST['sfsi_plus_icon_hover_exclude_urlKeywords'] : 'no';
	$sfsi_plus_icon_hover_switch_exclude_custom_post_types = isset( $_POST['sfsi_plus_icon_hover_switch_exclude_custom_post_types'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_switch_exclude_custom_post_types'] ) : 'no';
	$sfsi_plus_icon_hover_list_exclude_custom_post_types   = isset( $_POST['sfsi_plus_icon_hover_list_exclude_custom_post_types'] ) ? $_POST['sfsi_plus_icon_hover_list_exclude_custom_post_types'] : serialize( array() );
	$sfsi_plus_icon_hover_switch_exclude_taxonomies        = isset( $_POST['sfsi_plus_icon_hover_switch_exclude_taxonomies'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_switch_exclude_taxonomies'] ) : 'no';
	$sfsi_plus_icon_hover_list_exclude_taxonomies          = isset( $_POST['sfsi_plus_icon_hover_list_exclude_taxonomies'] ) ? $_POST['sfsi_plus_icon_hover_list_exclude_taxonomies'] : serialize( array() );

	$sfsi_plus_icon_hover_include_home                     = isset( $_POST['sfsi_plus_icon_hover_include_home'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_home'] ) : 'no';
	$sfsi_plus_icon_hover_include_page                     = isset( $_POST['sfsi_plus_icon_hover_include_page'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_page'] ) : 'no';
	$sfsi_plus_icon_hover_include_post                     = isset( $_POST['sfsi_plus_icon_hover_include_post'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_post'] ) : 'no';
	$sfsi_plus_icon_hover_include_tag                      = isset( $_POST['sfsi_plus_icon_hover_include_tag'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_tag'] ) : 'no';
	$sfsi_plus_icon_hover_include_category                 = isset( $_POST['sfsi_plus_icon_hover_include_category'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_category'] ) : 'no';
	$sfsi_plus_icon_hover_include_date_archive             = isset( $_POST['sfsi_plus_icon_hover_include_date_archive'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_date_archive'] ) : 'no';
	$sfsi_plus_icon_hover_include_author_archive           = isset( $_POST['sfsi_plus_icon_hover_include_author_archive'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_author_archive'] ) : 'no';
	$sfsi_plus_icon_hover_include_search                   = isset( $_POST['sfsi_plus_icon_hover_include_search'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_search'] ) : 'no';
	$sfsi_plus_icon_hover_include_url                      = isset( $_POST['sfsi_plus_icon_hover_include_url'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_include_url'] ) : 'no';
	$sfsi_plus_icon_hover_include_urlKeywords              = isset( $_POST['sfsi_plus_icon_hover_include_urlKeywords'] ) ? $_POST['sfsi_plus_icon_hover_include_urlKeywords'] : 'no';
	$sfsi_plus_icon_hover_switch_include_custom_post_types = isset( $_POST['sfsi_plus_icon_hover_switch_include_custom_post_types'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_switch_include_custom_post_types'] ) : 'no';
	$sfsi_plus_icon_hover_list_include_custom_post_types   = isset( $_POST['sfsi_plus_icon_hover_list_include_custom_post_types'] ) ? $_POST['sfsi_plus_icon_hover_list_include_custom_post_types'] : serialize( array() );
	$sfsi_plus_icon_hover_switch_include_taxonomies        = isset( $_POST['sfsi_plus_icon_hover_switch_include_taxonomies'] ) ? sanitize_text_field( $_POST['sfsi_plus_icon_hover_switch_include_taxonomies'] ) : 'no';
	$sfsi_plus_icon_hover_list_include_taxonomies          = isset( $_POST['sfsi_plus_icon_hover_list_include_taxonomies'] ) ? $_POST['sfsi_plus_icon_hover_list_include_taxonomies'] : serialize( array() );
	$sfsi_plus_responsive_icons_show_on_desktop            = isset( $_POST["sfsi_plus_responsive_icons_show_on_desktop"] ) ? sanitize_text_field( $_POST["sfsi_plus_responsive_icons_show_on_desktop"] ) : 'no';
	$sfsi_plus_responsive_icons_show_on_mobile             = isset( $_POST["sfsi_plus_responsive_icons_show_on_mobile"] ) ? sanitize_text_field( $_POST["sfsi_plus_responsive_icons_show_on_mobile"] ) : 'no';
	$sfsi_plus_responsive_icons_before_post                = isset( $_POST["sfsi_plus_responsive_icons_before_post"] ) ? sanitize_text_field( $_POST["sfsi_plus_responsive_icons_before_post"] ) : 'no';
	$sfsi_plus_responsive_icons_after_post                 = isset( $_POST["sfsi_plus_responsive_icons_after_post"] ) ? sanitize_text_field( $_POST["sfsi_plus_responsive_icons_after_post"] ) : 'no';
	$sfsi_plus_responsive_icons_before_post_on_taxonomy    = isset( $_POST["sfsi_plus_responsive_icons_before_post_on_taxonomy"] ) ? sanitize_text_field( $_POST["sfsi_plus_responsive_icons_before_post_on_taxonomy"] ) : 'no';
	$sfsi_plus_responsive_icons_after_post_on_taxonomy     = isset( $_POST["sfsi_plus_responsive_icons_after_post_on_taxonomy"] ) ? sanitize_text_field( $_POST["sfsi_plus_responsive_icons_after_post_on_taxonomy"] ) : 'no';
	$sfsi_plus_responsive_icons_before_pages               = isset( $_POST["sfsi_plus_responsive_icons_before_pages"] ) ? sanitize_text_field( $_POST["sfsi_plus_responsive_icons_before_pages"] ) : 'no';
	$sfsi_plus_responsive_icons_after_pages                = isset( $_POST["sfsi_plus_responsive_icons_after_pages"] ) ? sanitize_text_field( $_POST["sfsi_plus_responsive_icons_after_pages"] ) : 'no';

	$sfsi_plus_display_after_woocomerce_desc  = isset( $_POST['sfsi_plus_display_after_woocomerce_desc'] ) ? sanitize_text_field( $_POST['sfsi_plus_display_after_woocomerce_desc'] ) : 'no';
	$sfsi_plus_display_before_woocomerce_desc = isset( $_POST['sfsi_plus_display_before_woocomerce_desc'] ) ? sanitize_text_field( $_POST['sfsi_plus_display_before_woocomerce_desc'] ) : 'no';


	$sfsi_plus_responsive_icons_default = array(
		"default_icons" => array(
			"facebook"      => array(
				"active" => "yes",
				"text"   => __( "Share on Facebook", "usm-premium-icons" ),
				"url"    => ""
			),
			"Twitter"       => array( "active" => "yes", "text" => __( "Tweet", "usm-premium-icons" ), "url" => "" ),
			"Follow"        => array(
				"active" => "yes",
				"text"   => __( "Follow us", "usm-premium-icons" ),
				"url"    => ""
			),
			"pinterest"     => array( "active" => "no", "text" => __( "Save", "usm-premium-icons" ), "url" => "" ),
			"Linkedin"      => array( "active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => "" ),
			"Whatsapp"      => array( "active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => "" ),
			"vk"            => array( "active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => "" ),
			"Odnoklassniki" => array( "active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => "" ),
			"Telegram"      => array( "active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => "" ),
			"Weibo"         => array( "active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => "" ),
			"QQ2"           => array( "active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => "" ),
			"xing"          => array( "active" => "no", "text" => __( "Share", "usm-premium-icons" ), "url" => "" ),
		),
		"custom_icons"  => array(),
		"settings"      => array(
			"icon_size"               => "Medium",
			"icon_width_type"         => "Fully responsive",
			"icon_width_size"         => 240,
			"edge_type"               => "Round",
			"edge_radius"             => 5,
			"style"                   => "Gradient",
			"margin"                  => 10,
			"text_align"              => "Centered",
			"show_count"              => "no",
			"responsive_mobile_icons" => "yes",
			"counter_color"           => "#aaaaaa",
			"counter_bg_color"        => "#fff",
			"share_count_text"        => __( "SHARES", "usm-premium-icons" ),
			"margin_above"            => 10,
			"margin_below"            => 10
		)
	);
	$sfsi_plus_responsive_icons         = array();
	// var_dump($_POST['sfsi_plus_responsive_icons']);
	if ( isset( $_POST['sfsi_plus_responsive_icons'] ) && is_array( $_POST['sfsi_plus_responsive_icons'] ) ) {
		foreach ( $_POST['sfsi_plus_responsive_icons'] as $key => $value ) {
			if ( ! is_array( $value ) ) {
				$sfsi_plus_responsive_icons[ $key ] = sanitize_text_field( $value );
			} else {
				$sfsi_plus_responsive_icons[ $key ] = array();
				foreach ( $value as $key2 => $value2 ) {
					if ( ! is_array( $value2 ) ) {
						$sfsi_plus_responsive_icons[ $key ][ $key2 ] = sanitize_text_field( $value2 );
					} else {
						$sfsi_plus_responsive_icons[ $key ][ $key2 ] = array();
						foreach ( $value2 as $key3 => $value3 ) {
							if ( ! is_array( $value3 ) ) {
								$sfsi_plus_responsive_icons[ $key ][ $key2 ][ $key3 ] = sanitize_text_field( $value3 );
							}
						}
					}
				}
			}
		}
	}
	if ( empty( $sfsi_plus_responsive_icons ) ) {
		$sfsi_plus_responsive_icons = $sfsi_plus_responsive_icons_default;
	} else {
		if ( ! isset( $sfsi_plus_responsive_icons['default_icons'] ) ) {
			$sfsi_plus_responsive_icons["default_icons"] = $sfsi_plus_responsive_icons_default["default_icons"];
		}
		if ( ! isset( $sfsi_plus_responsive_icons['custom_icons'] ) ) {
			$sfsi_plus_responsive_icons["custom_icons"] = array();
		}
		if ( ! isset( $sfsi_plus_responsive_icons['settings'] ) ) {
			$sfsi_plus_responsive_icons["settings"] = $sfsi_plus_responsive_icons_default["settings"];
		}
		foreach ( $sfsi_plus_responsive_icons['default_icons'] as $key => $value ) {
			foreach ( array_keys( $sfsi_plus_responsive_icons_default['default_icons']['facebook'] ) as $default_icon_key ) {
				if ( ! isset( $value[ $default_icon_key ] ) ) {
					$sfsi_plus_responsive_icons["default_icons"][ $key ][ $default_icon_key ] = $sfsi_plus_responsive_icons_default['default_icons'][ $key ][ $default_icon_key ];
				} else {
					$sfsi_plus_responsive_icons["default_icons"][ $key ][ $default_icon_key ] = sanitize_text_field( $sfsi_plus_responsive_icons["default_icons"][ $key ][ $default_icon_key ] );
				}
			}
		}
		foreach ( $sfsi_plus_responsive_icons['custom_icons'] as $key => $value ) {
			if ( ! isset( $value['active'] ) ) {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["active"] = "no";
			} else {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["active"] = sanitize_text_field( $sfsi_plus_responsive_icons["custom_icons"][ $key ]["active"] );
			}
			if ( ! isset( $value['url'] ) ) {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["url"] = "#";
			} else {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["url"] = sanitize_text_field( $sfsi_plus_responsive_icons["custom_icons"][ $key ]["url"] );
			}
			if ( ! isset( $value['text'] ) ) {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["text"] = "Share";
			} else {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["text"] = sanitize_text_field( $sfsi_plus_responsive_icons["custom_icons"][ $key ]["text"] );
			}
			if ( ! isset( $value['icon'] ) ) {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["icon"] = "";
			} else {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["icon"] = sanitize_text_field( $sfsi_plus_responsive_icons["custom_icons"][ $key ]["icon"] );
			}
			if ( ! isset( $value['bg-color'] ) ) {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["bg-color"] = "#fff";
			} else {
				$sfsi_plus_responsive_icons["custom_icons"][ $key ]["bg-color"] = sanitize_text_field( $sfsi_plus_responsive_icons["custom_icons"][ $key ]["bg-color"] );
			}
		}
		foreach ( array_keys( $sfsi_plus_responsive_icons_default['settings'] ) as $setting_key ) {
			if ( ! isset( $sfsi_plus_responsive_icons["settings"][ $setting_key ] ) || is_null( $sfsi_plus_responsive_icons["settings"][ $setting_key ] ) || $sfsi_plus_responsive_icons["settings"][ $setting_key ] === "" ) {
				$sfsi_plus_responsive_icons["settings"][ $setting_key ] = $sfsi_plus_responsive_icons_default['settings'][ $setting_key ];
			} else {
				$sfsi_plus_responsive_icons["settings"][ $setting_key ] = sanitize_text_field( $sfsi_plus_responsive_icons["settings"][ $setting_key ] );
			}
		}
	}
	$sfsi_plus_post_mobile_icons_size             = isset( $_POST["sfsi_plus_post_mobile_icons_size"] ) ? intval( $_POST["sfsi_plus_post_mobile_icons_size"] ) : 40;
	$sfsi_plus_post_mobile_icons_spacing          = isset( $_POST["sfsi_plus_post_mobile_icons_spacing"] ) ? intval( $_POST["sfsi_plus_post_mobile_icons_spacing"] ) : 5;
	$sfsi_plus_post_mobile_icons_vertical_spacing = isset( $_POST["sfsi_plus_post_mobile_icons_vertical_spacing"] ) ? intval( $_POST["sfsi_plus_post_mobile_icons_vertical_spacing"] ) : 5;

	$sfsi_plus_mobile_size_space_beforeafterposts = isset( $_POST["sfsi_plus_mobile_size_space_beforeafterposts"] )
		? sanitize_text_field( $_POST["sfsi_plus_mobile_size_space_beforeafterposts"] )
		: 'no';

	$sfsi_plus_sticky_bar           = isset( $_POST["sfsi_plus_sticky_bar"] ) ? sanitize_text_field( $_POST["sfsi_plus_sticky_bar"] ) : 'no';
	$sfsi_plus_sticky_icons_default = array(
		"default_icons" => array(
			"facebook"      => array( "active" => "yes", "url" => "" ),
			"Twitter"       => array( "active" => "yes", "url" => "" ),
			"Follow"        => array( "active" => "yes", "url" => "" ),
			"pinterest"     => array( "active" => "no", "url" => "" ),
			"Linkedin"      => array( "active" => "no", "url" => "" ),
			"Whatsapp"      => array( "active" => "no", "url" => "" ),
			"vk"            => array( "active" => "no", "url" => "" ),
			"Odnoklassniki" => array( "active" => "no", "url" => "" ),
			"Telegram"      => array( "active" => "no", "url" => "" ),
			"Weibo"         => array( "active" => "no", "url" => "" ),
			"QQ2"           => array( "active" => "no", "url" => "" ),
			"xing"          => array( "active" => "no", "url" => "" ),
		),
		"custom_icons"  => array(),
		"settings"      => array(
			"desktop"                     => "yes",
			"desktop_width"               => 782,
			"desktop_placement"           => "left",
			"display_position"            => 0,
			"desktop_placement_direction" => "up",
			"mobile"                      => "no",
			"mobile_width"                => 784,
			"mobile_placement"            => "left",
			"counts"                      => 0,
			"bg_color"                    => "#000000",
			"color"                       => "#ffffff",
			"share_count_text"            => __( "SHARE", "usm-premium-icons" ),
		)
	);
	$sfsi_plus_sticky_icons         = array();
	// var_dump($_POST['sfsi_plus_sticky_icons']);
	if ( isset( $_POST['sfsi_plus_sticky_icons'] ) && is_array( $_POST['sfsi_plus_sticky_icons'] ) ) {
		foreach ( $_POST['sfsi_plus_sticky_icons'] as $key => $value ) {
			if ( ! is_array( $value ) ) {
				$sfsi_plus_sticky_icons[ $key ] = sanitize_text_field( $value );
			} else {
				$sfsi_plus_sticky_icons[ $key ] = array();
				foreach ( $value as $key2 => $value2 ) {
					if ( ! is_array( $value2 ) ) {
						$sfsi_plus_sticky_icons[ $key ][ $key2 ] = sanitize_text_field( $value2 );
					} else {
						$sfsi_plus_sticky_icons[ $key ][ $key2 ] = array();
						foreach ( $value2 as $key3 => $value3 ) {
							if ( ! is_array( $value3 ) ) {
								$sfsi_plus_sticky_icons[ $key ][ $key2 ][ $key3 ] = sanitize_text_field( $value3 );
							}
						}
					}
				}
			}
		}
	}
	if ( empty( $sfsi_plus_sticky_icons ) ) {
		$sfsi_plus_sticky_icons = $sfsi_plus_sticky_icons_default;
	} else {
		if ( ! isset( $sfsi_plus_sticky_icons['default_icons'] ) ) {
			$sfsi_plus_sticky_icons["default_icons"] = $sfsi_plus_sticky_icons_default["default_icons"];
		}
		if ( ! isset( $sfsi_plus_sticky_icons['custom_icons'] ) ) {
			$sfsi_plus_sticky_icons["custom_icons"] = array();
		}
		if ( ! isset( $sfsi_plus_sticky_icons['settings'] ) ) {
			$sfsi_plus_sticky_icons["settings"] = $sfsi_plus_sticky_icons_default["settings"];
		}
		foreach ( $sfsi_plus_sticky_icons['default_icons'] as $key => $value ) {
			foreach ( array_keys( $sfsi_plus_sticky_icons_default['default_icons']['facebook'] ) as $default_icon_key ) {
				if ( ! isset( $value[ $default_icon_key ] ) ) {
					$sfsi_plus_sticky_icons["default_icons"][ $key ][ $default_icon_key ] = $sfsi_plus_sticky_icons_default['default_icons'][ $key ][ $default_icon_key ];
				} else {
					$sfsi_plus_sticky_icons["default_icons"][ $key ][ $default_icon_key ] = sanitize_text_field( $sfsi_plus_sticky_icons["default_icons"][ $key ][ $default_icon_key ] );
				}
			}
		}
		foreach ( $sfsi_plus_sticky_icons['custom_icons'] as $key => $value ) {
			if ( ! isset( $value['active'] ) ) {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["active"] = "no";
			} else {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["active"] = sanitize_text_field( $sfsi_plus_sticky_icons["custom_icons"][ $key ]["active"] );
			}
			if ( ! isset( $value['url'] ) ) {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["url"] = "#";
			} else {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["url"] = sanitize_text_field( $sfsi_plus_sticky_icons["custom_icons"][ $key ]["url"] );
			}
			if ( ! isset( $value['text'] ) ) {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["text"] = "Share";
			} else {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["text"] = sanitize_text_field( $sfsi_plus_sticky_icons["custom_icons"][ $key ]["text"] );
			}
			if ( ! isset( $value['icon'] ) ) {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["icon"] = "";
			} else {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["icon"] = sanitize_text_field( $sfsi_plus_sticky_icons["custom_icons"][ $key ]["icon"] );
			}
			if ( ! isset( $value['bg-color'] ) ) {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["bg-color"] = "#fff";
			} else {
				$sfsi_plus_sticky_icons["custom_icons"][ $key ]["bg-color"] = sanitize_text_field( $sfsi_plus_sticky_icons["custom_icons"][ $key ]["bg-color"] );
			}
		}
		foreach ( array_keys( $sfsi_plus_sticky_icons_default['settings'] ) as $setting_key ) {
			if ( ! isset( $sfsi_plus_sticky_icons["settings"][ $setting_key ] ) || is_null( $sfsi_plus_sticky_icons["settings"][ $setting_key ] ) || $sfsi_plus_sticky_icons["settings"][ $setting_key ] === "" ) {
				$sfsi_plus_sticky_icons["settings"][ $setting_key ] = $sfsi_plus_sticky_icons_default['settings'][ $setting_key ];
			} else {
				$sfsi_plus_sticky_icons["settings"][ $setting_key ] = sanitize_text_field( $sfsi_plus_sticky_icons["settings"][ $setting_key ] );
			}
		}
	}
	$up_option8 = array(

		'sfsi_plus_show_via_widget'     => sanitize_text_field( $sfsi_plus_show_via_widget ),
		'sfsi_plus_float_on_page'       => sanitize_text_field( $sfsi_plus_float_on_page ),
		'sfsi_plus_place_item_manually' => sanitize_text_field( $sfsi_plus_place_item_manually ),
		'sfsi_plus_show_item_onposts'   => sanitize_text_field( $sfsi_plus_show_item_onposts ),
		// 'sfsi_plus_place_rect_shortcode'   => sanitize_text_field($sfsi_plus_place_rect_shortcode),

		'sfsi_plus_float_page_position'      => sanitize_text_field( $sfsi_plus_float_page_position ),
		'sfsi_plus_make_icon'                => sanitize_text_field( $sfsi_plus_make_icon ),
		'sfsi_plus_icons_floatMargin_top'    => intval( $sfsi_plus_icons_floatMargin_top ),
		'sfsi_plus_icons_floatMargin_bottom' => intval( $sfsi_plus_icons_floatMargin_bottom ),
		'sfsi_plus_icons_floatMargin_left'   => intval( $sfsi_plus_icons_floatMargin_left ),
		'sfsi_plus_icons_floatMargin_right'  => intval( $sfsi_plus_icons_floatMargin_right ),

		'sfsi_plus_mobile_float'            => sanitize_text_field( $sfsi_plus_mobile_float ),
		'sfsi_plus_mobile_widget'           => sanitize_text_field( $sfsi_plus_mobile_widget ),
		'sfsi_plus_mobile_shortcode'        => sanitize_text_field( $sfsi_plus_mobile_shortcode ),
		'sfsi_plus_mobile_beforeafterposts' => sanitize_text_field( $sfsi_plus_mobile_beforeafterposts ),

		'sfsi_plus_shortcode_horizontal_verical_Alignment'        => sanitize_text_field( $sfsi_plus_shortcode_horizontal_verical_Alignment ),
		'sfsi_plus_shortcode_mobile_horizontal_verical_Alignment' => sanitize_text_field( $sfsi_plus_shortcode_mobile_horizontal_verical_Alignment ),

		'sfsi_plus_widget_horizontal_verical_Alignment'        => sanitize_text_field( $sfsi_plus_widget_horizontal_verical_Alignment ),
		'sfsi_plus_widget_mobile_horizontal_verical_Alignment' => sanitize_text_field( $sfsi_plus_widget_mobile_horizontal_verical_Alignment ),

		'sfsi_plus_float_horizontal_verical_Alignment'        => sanitize_text_field( $sfsi_plus_float_horizontal_verical_Alignment ),
		'sfsi_plus_float_mobile_horizontal_verical_Alignment' => sanitize_text_field( $sfsi_plus_float_mobile_horizontal_verical_Alignment ),

		'sfsi_plus_beforeafterposts_horizontal_verical_Alignment'        => sanitize_text_field( $sfsi_plus_beforeafterposts_horizontal_verical_Alignment ),
		'sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment' => sanitize_text_field( $sfsi_plus_beforeafterposts_mobile_horizontal_verical_Alignment ),

		'sfsi_plus_display_button_type' => sanitize_text_field( $sfsi_plus_display_button_type ),
		'sfsi_plus_post_icons_size'     => intval( $sfsi_plus_post_icons_size ),
		'sfsi_plus_post_icons_spacing'  => intval( $sfsi_plus_post_icons_spacing ),

		'sfsi_plus_post_icons_vertical_spacing' => intval( $sfsi_plus_post_icons_vertical_spacing ),
		'sfsi_plus_show_Onposts'                => sanitize_text_field( $sfsi_plus_show_Onposts ),
		'sfsi_plus_textBefor_icons'             => sanitize_text_field( stripslashes( $sfsi_plus_textBefor_icons ) ),
		'sfsi_plus_icon_hover_custom_icon_url'  => sanitize_text_field( stripslashes( $sfsi_plus_icon_hover_custom_icon_url ) ),
		'sfsi_plus_textBefor_icons_font_size'   => intval( $sfsi_plus_textBefor_icons_font_size ),
		'sfsi_plus_textBefor_icons_fontcolor'   => sfsi_plus_sanitize_hex_color( $sfsi_plus_textBefor_icons_fontcolor ),
		'sfsi_plus_textBefor_icons_font'        => sanitize_text_field( $sfsi_plus_textBefor_icons_font ),
		'sfsi_plus_textBefor_icons_font_type'   => sanitize_text_field( $sfsi_plus_textBefor_icons_font_type ),

		'sfsi_plus_icons_alignment'              => sanitize_text_field( $sfsi_plus_icons_alignment ),
		'sfsi_plus_icons_DisplayCounts'          => sanitize_text_field( $sfsi_plus_icons_DisplayCounts ),
		'sfsi_plus_display_before_posts'         => sanitize_text_field( $sfsi_plus_display_before_posts ),
		'sfsi_plus_display_after_posts'          => sanitize_text_field( $sfsi_plus_display_after_posts ),
		'sfsi_plus_choose_post_types'            => $sfsi_plus_choose_post_types,
		'sfsi_plus_choose_post_types_responsive' => $sfsi_plus_choose_post_types_responsive,
		'sfsi_plus_taxonomies_for_icons'         => $sfsi_plus_taxonomies_for_icons,

		'sfsi_plus_float_page_mobileposition'      => sanitize_text_field( $sfsi_plus_float_page_mobileposition ),
		'sfsi_plus_make_mobileicon'                => sanitize_text_field( $sfsi_plus_make_mobileicon ),
		'sfsi_plus_icons_floatMargin_mobiletop'    => intval( $sfsi_plus_icons_floatMargin_mobiletop ),
		'sfsi_plus_icons_floatMargin_mobilebottom' => intval( $sfsi_plus_icons_floatMargin_mobilebottom ),
		'sfsi_plus_icons_floatMargin_mobileleft'   => intval( $sfsi_plus_icons_floatMargin_mobileleft ),
		'sfsi_plus_icons_floatMargin_mobileright'  => intval( $sfsi_plus_icons_floatMargin_mobileright ),

		'sfsi_plus_display_before_blogposts' => sanitize_text_field( $sfsi_plus_display_before_blogposts ),
		'sfsi_plus_display_after_blogposts'  => sanitize_text_field( $sfsi_plus_display_after_blogposts ),
		'sfsi_plus_rectsub'                  => sanitize_text_field( $sfsi_plus_rectsub ),
		'sfsi_plus_rectfb'                   => sanitize_text_field( $sfsi_plus_rectfb ),
		'sfsi_plus_recttwtr'                 => sanitize_text_field( $sfsi_plus_recttwtr ),
		'sfsi_plus_rectpinit'                => sanitize_text_field( $sfsi_plus_rectpinit ),
		'sfsi_plus_rectlinkedin'             => sanitize_text_field( $sfsi_plus_rectlinkedin ),
		'sfsi_plus_rectreddit'               => sanitize_text_field( $sfsi_plus_rectreddit ),
		'sfsi_plus_rectfbshare'              => sanitize_text_field( $sfsi_plus_rectfbshare ),

		'sfsi_plus_marginAbove_postIcon'     => intval( $sfsi_plus_marginAbove_postIcon ),
		'sfsi_plus_marginBelow_postIcon'     => intval( $sfsi_plus_marginBelow_postIcon ),
		'sfsi_plus_display_after_pageposts'  => sanitize_text_field( $sfsi_plus_display_after_pageposts ),
		'sfsi_plus_display_before_pageposts' => sanitize_text_field( $sfsi_plus_display_before_pageposts ),

		'sfsi_plus_widget_show_on_desktop' => sanitize_text_field( $sfsi_plus_widget_show_on_desktop ),
		'sfsi_plus_widget_show_on_mobile'  => sanitize_text_field( $sfsi_plus_widget_show_on_mobile ),

		'sfsi_plus_float_show_on_desktop' => sanitize_text_field( $sfsi_plus_float_show_on_desktop ),
		'sfsi_plus_float_show_on_mobile'  => sanitize_text_field( $sfsi_plus_float_show_on_mobile ),

		'sfsi_plus_shortcode_show_on_desktop' => sanitize_text_field( $sfsi_plus_shortcode_show_on_desktop ),
		'sfsi_plus_shortcode_show_on_mobile'  => sanitize_text_field( $sfsi_plus_shortcode_show_on_mobile ),

		'sfsi_plus_rectangle_icons_shortcode_show_on_desktop' => sanitize_text_field( $sfsi_plus_rectangle_icons_shortcode_show_on_desktop ),
		'sfsi_plus_rectangle_icons_shortcode_show_on_mobile'  => sanitize_text_field( $sfsi_plus_rectangle_icons_shortcode_show_on_mobile ),

		'sfsi_plus_beforeafterposts_show_on_desktop' => sanitize_text_field( $sfsi_plus_beforeafterposts_show_on_desktop ),
		'sfsi_plus_beforeafterposts_show_on_mobile'  => sanitize_text_field( $sfsi_plus_beforeafterposts_show_on_mobile ),

		'sfsi_plus_icons_rules'            => $sfsi_plus_icons_rules,
		'sfsi_plus_exclude_home'           => sanitize_text_field( $sfsi_plus_exclude_home ),
		'sfsi_plus_exclude_page'           => sanitize_text_field( $sfsi_plus_exclude_page ),
		'sfsi_plus_exclude_post'           => sanitize_text_field( $sfsi_plus_exclude_post ),
		'sfsi_plus_exclude_tag'            => sanitize_text_field( $sfsi_plus_exclude_tag ),
		'sfsi_plus_exclude_category'       => sanitize_text_field( $sfsi_plus_exclude_category ),
		'sfsi_plus_exclude_date_archive'   => sanitize_text_field( $sfsi_plus_exclude_date_archive ),
		'sfsi_plus_exclude_author_archive' => sanitize_text_field( $sfsi_plus_exclude_author_archive ),
		'sfsi_plus_exclude_search'         => sanitize_text_field( $sfsi_plus_exclude_search ),
		'sfsi_plus_exclude_url'            => sanitize_text_field( $sfsi_plus_exclude_url ),
		'sfsi_plus_urlKeywords'            => array_values( array_filter( $sfsi_plus_urlKeywords ) ),

		'sfsi_plus_switch_exclude_custom_post_types' => $sfsi_plus_switch_exclude_custom_post_types,
		'sfsi_plus_list_exclude_custom_post_types'   => $sfsi_plus_list_exclude_custom_post_types,

		'sfsi_plus_switch_exclude_taxonomies' => $sfsi_plus_switch_exclude_taxonomies,
		'sfsi_plus_list_exclude_taxonomies'   => $sfsi_plus_list_exclude_taxonomies,

		'sfsi_plus_include_home'           => sanitize_text_field( $sfsi_plus_include_home ),
		'sfsi_plus_include_page'           => sanitize_text_field( $sfsi_plus_include_page ),
		'sfsi_plus_include_post'           => sanitize_text_field( $sfsi_plus_include_post ),
		'sfsi_plus_include_tag'            => sanitize_text_field( $sfsi_plus_include_tag ),
		'sfsi_plus_include_category'       => sanitize_text_field( $sfsi_plus_include_category ),
		'sfsi_plus_include_date_archive'   => sanitize_text_field( $sfsi_plus_include_date_archive ),
		'sfsi_plus_include_author_archive' => sanitize_text_field( $sfsi_plus_include_author_archive ),
		'sfsi_plus_include_search'         => sanitize_text_field( $sfsi_plus_include_search ),
		'sfsi_plus_include_url'            => sanitize_text_field( $sfsi_plus_include_url ),
		'sfsi_plus_include_urlKeywords'    => array_values( array_filter( $sfsi_plus_include_urlKeywords ) ),

		'sfsi_plus_switch_include_custom_post_types' => $sfsi_plus_switch_include_custom_post_types,
		'sfsi_plus_list_include_custom_post_types'   => $sfsi_plus_list_include_custom_post_types,

		'sfsi_plus_display_on_all_icons'                           => sanitize_text_field( $sfsi_plus_display_on_all_icons ),
		'sfsi_plus_display_rule_round_icon_widget'                 => sanitize_text_field( $sfsi_plus_display_rule_round_icon_widget ),
		'sfsi_plus_display_rule_round_icon_define_location'        => sanitize_text_field( $sfsi_plus_display_rule_round_icon_define_location ),
		'sfsi_plus_display_rule_round_icon_shortcode'              => sanitize_text_field( $sfsi_plus_display_rule_round_icon_shortcode ),
		'sfsi_plus_display_rule_round_icon_before_after_post'      => sanitize_text_field( $sfsi_plus_display_rule_round_icon_before_after_post ),
		'sfsi_plus_display_rule_rect_icon_before_after_post'       => sanitize_text_field( $sfsi_plus_display_rule_rect_icon_before_after_post ),
		'sfsi_plus_display_rule_responsive_icon_before_after_post' => sanitize_text_field( $sfsi_plus_display_rule_responsive_icon_before_after_post ),
		'sfsi_plus_display_rule_responsive_icon_shortcode'         => sanitize_text_field( $sfsi_plus_display_rule_responsive_icon_shortcode ),
		'sfsi_plus_display_rule_icon_sticky_bar'                   => sanitize_text_field( $sfsi_plus_display_rule_icon_sticky_bar ),
		'sfsi_plus_display_rule_rect_icon_shortcode'               => sanitize_text_field( $sfsi_plus_display_rule_rect_icon_shortcode ),

		'sfsi_plus_icon_hover_show_pinterest' => sanitize_text_field( $sfsi_plus_icon_hover_show_pinterest ),
		'sfsi_plus_icon_hover_type'           => sanitize_text_field( $sfsi_plus_icon_hover_type ),
		'sfsi_plus_icon_hover_language'       => sanitize_text_field( $sfsi_plus_icon_hover_language ),
		'sfsi_plus_icon_hover_placement'      => sanitize_text_field( $sfsi_plus_icon_hover_placement ),
		'sfsi_plus_icon_hover_desktop'        => sanitize_text_field( $sfsi_plus_icon_hover_desktop ),
		'sfsi_plus_icon_hover_mobile'         => sanitize_text_field( $sfsi_plus_icon_hover_mobile ),
		'sfsi_plus_icon_hover_on_all_pages'   => sanitize_text_field( $sfsi_plus_icon_hover_on_all_pages ),
		'sfsi_plus_icon_hover_width'          => sanitize_text_field( $sfsi_plus_icon_hover_width ),
		'sfsi_plus_icon_hover_height'         => sanitize_text_field( $sfsi_plus_icon_hover_height ),

		'sfsi_plus_icon_hover_exclude_home'                     => sanitize_text_field( $sfsi_plus_icon_hover_exclude_home ),
		'sfsi_plus_icon_hover_exclude_page'                     => sanitize_text_field( $sfsi_plus_icon_hover_exclude_page ),
		'sfsi_plus_icon_hover_exclude_post'                     => sanitize_text_field( $sfsi_plus_icon_hover_exclude_post ),
		'sfsi_plus_icon_hover_exclude_tag'                      => sanitize_text_field( $sfsi_plus_icon_hover_exclude_tag ),
		'sfsi_plus_icon_hover_exclude_category'                 => sanitize_text_field( $sfsi_plus_icon_hover_exclude_category ),
		'sfsi_plus_icon_hover_exclude_date_archive'             => sanitize_text_field( $sfsi_plus_icon_hover_exclude_date_archive ),
		'sfsi_plus_icon_hover_exclude_author_archive'           => sanitize_text_field( $sfsi_plus_icon_hover_exclude_author_archive ),
		'sfsi_plus_icon_hover_exclude_search'                   => sanitize_text_field( $sfsi_plus_icon_hover_exclude_search ),
		'sfsi_plus_icon_hover_exclude_url'                      => sanitize_text_field( $sfsi_plus_icon_hover_exclude_url ),
		'sfsi_plus_icon_hover_exclude_urlKeywords'              => $sfsi_plus_icon_hover_exclude_urlKeywords,
		'sfsi_plus_icon_hover_switch_exclude_custom_post_types' => sanitize_text_field( $sfsi_plus_icon_hover_switch_exclude_custom_post_types ),
		'sfsi_plus_icon_hover_list_exclude_custom_post_types'   => $sfsi_plus_icon_hover_list_exclude_custom_post_types,
		'sfsi_plus_icon_hover_switch_exclude_taxonomies'        => sanitize_text_field( $sfsi_plus_icon_hover_switch_exclude_taxonomies ),
		'sfsi_plus_icon_hover_list_exclude_taxonomies'          => $sfsi_plus_icon_hover_list_exclude_taxonomies,

		'sfsi_plus_icon_hover_include_home'                     => sanitize_text_field( $sfsi_plus_icon_hover_include_home ),
		'sfsi_plus_icon_hover_include_page'                     => sanitize_text_field( $sfsi_plus_icon_hover_include_page ),
		'sfsi_plus_icon_hover_include_post'                     => sanitize_text_field( $sfsi_plus_icon_hover_include_post ),
		'sfsi_plus_icon_hover_include_tag'                      => sanitize_text_field( $sfsi_plus_icon_hover_include_tag ),
		'sfsi_plus_icon_hover_include_category'                 => sanitize_text_field( $sfsi_plus_icon_hover_include_category ),
		'sfsi_plus_icon_hover_include_date_archive'             => sanitize_text_field( $sfsi_plus_icon_hover_include_date_archive ),
		'sfsi_plus_icon_hover_include_author_archive'           => sanitize_text_field( $sfsi_plus_icon_hover_include_author_archive ),
		'sfsi_plus_icon_hover_include_search'                   => sanitize_text_field( $sfsi_plus_icon_hover_include_search ),
		'sfsi_plus_icon_hover_include_url'                      => sanitize_text_field( $sfsi_plus_icon_hover_include_url ),
		'sfsi_plus_icon_hover_include_urlKeywords'              => $sfsi_plus_icon_hover_include_urlKeywords,
		'sfsi_plus_icon_hover_switch_include_custom_post_types' => sanitize_text_field( $sfsi_plus_icon_hover_switch_include_custom_post_types ),
		'sfsi_plus_icon_hover_list_include_custom_post_types'   => $sfsi_plus_icon_hover_list_include_custom_post_types,
		'sfsi_plus_icon_hover_switch_include_taxonomies'        => sanitize_text_field( $sfsi_plus_icon_hover_switch_include_taxonomies ),
		'sfsi_plus_icon_hover_list_include_taxonomies'          => $sfsi_plus_icon_hover_list_include_taxonomies,
		'sfsi_plus_display_before_woocomerce_desc'              => $sfsi_plus_display_before_woocomerce_desc,
		'sfsi_plus_display_after_woocomerce_desc'               => $sfsi_plus_display_after_woocomerce_desc,

		'sfsi_plus_responsive_icons'                         => $sfsi_plus_responsive_icons,
		'sfsi_plus_responsive_icons_show_on_desktop'         => $sfsi_plus_responsive_icons_show_on_desktop,
		'sfsi_plus_responsive_icons_show_on_mobile'          => $sfsi_plus_responsive_icons_show_on_mobile,
		'sfsi_plus_responsive_icons_before_post'             => $sfsi_plus_responsive_icons_before_post,
		'sfsi_plus_responsive_icons_after_post'              => $sfsi_plus_responsive_icons_after_post,
		'sfsi_plus_responsive_icons_before_post_on_taxonomy' => $sfsi_plus_responsive_icons_before_post_on_taxonomy,
		'sfsi_plus_responsive_icons_after_post_on_taxonomy'  => $sfsi_plus_responsive_icons_after_post_on_taxonomy,
		'sfsi_plus_responsive_icons_before_pages'            => $sfsi_plus_responsive_icons_before_pages,
		'sfsi_plus_responsive_icons_after_pages'             => $sfsi_plus_responsive_icons_after_pages,
		'sfsi_plus_post_mobile_icons_size'                   => intval( $sfsi_plus_post_mobile_icons_size ),
		'sfsi_plus_post_mobile_icons_spacing'                => intval( $sfsi_plus_post_mobile_icons_spacing ),
		'sfsi_plus_post_mobile_icons_vertical_spacing'       => intval( $sfsi_plus_post_mobile_icons_vertical_spacing ),
		'sfsi_plus_mobile_size_space_beforeafterposts'       => sanitize_text_field( $sfsi_plus_mobile_size_space_beforeafterposts ),
		'sfsi_plus_sticky_bar'                               => sanitize_text_field( $sfsi_plus_sticky_bar ),
		'sfsi_plus_sticky_icons'                             => $sfsi_plus_sticky_icons,
		'sfsi_plus_place_item_gutenberg'                     => sanitize_text_field( $sfsi_plus_place_item_gutenberg ),

	);
	$success    = update_option( 'sfsi_premium_section8_options', serialize( $up_option8 ) );
	$success    = false != $success ? "success" : "failure";

	$icon_counts = sfsi_plus_getCounts( false );
	$count       = 0;
	foreach ( $sfsi_plus_responsive_icons['default_icons'] as $icon_name => $icon ) {
		if ( $icon['active'] == "yes" ) {
			switch ( strtolower( $icon_name ) ) {
				case 'facebook':
					$count += ( isset( $icon_counts['fb_count'] ) ? $icon_counts['fb_count'] : 0 );
					break;
				case 'twitter':
					$count += ( isset( $icon_counts['twitter_count'] ) ? $icon_counts['twitter_count'] : 0 );
					break;
				case 'follow':
					$count += ( isset( $icon_counts['email_count'] ) ? $icon_counts['email_count'] : 0 );
					break;
				case 'follow':
					$count += ( isset( $icon_counts['yummly_count'] ) ? $icon_counts['yummly_count'] : 0 );
					break;
				case 'pinterest':
					$count += ( isset( $icon_counts['pin_count'] ) ? $icon_counts['pin_count'] : 0 );
					break;
				case 'linkedin':
					$count += ( isset( $icon_counts['linkedIn_count'] ) ? $icon_counts['linkedIn_count'] : 0 );
					break;
				case 'Whatsapp':
					$count += ( isset( $icon_counts['whatsapp_count'] ) ? $icon_counts['whatsapp_count'] : 0 );
					break;
				case 'vk':
					$count += ( isset( $icon_counts['vk_count'] ) ? $icon_counts['vk_count'] : 0 );
					break;
			}
		}
	}


	update_option( 'sfsi_premium_icon_counts', $count );
	$option8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );

	$icons        = $option8['sfsi_plus_sticky_icons'];
	$sticky_count = 0;
	$socialObj    = new sfsi_plus_SocialHelper();
	$icon_counts  = sfsi_plus_getCounts( false );
	$option4      = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
	foreach ( $icons['default_icons'] as $icon_name => $icon ) {
		if ( strtolower( $icon_name ) == "follow" ) {
			$icon_name = "email";
		} elseif ( strtolower( $icon_name ) == "linkedin" ) {
			$icon_name = "linkedIn";
		}

		if ( $icon['active'] == "yes" && $option4[ 'sfsi_plus_' . lcfirst( $icon_name ) . '_countsDisplay' ] == "yes" ) {

			switch ( strtolower( $icon_name ) ) {
				case 'facebook':
					$sticky_count += ( isset( $icon_counts['fb_count'] ) ? $icon_counts['fb_count'] : 0 );
					break;
				case 'twitter':
					$sticky_count += ( isset( $icon_counts['twitter_count'] ) ? $icon_counts['twitter_count'] : 0 );
					break;
				case 'email':
					$sticky_count += ( isset( $icon_counts['email_count'] ) ? $icon_counts['email_count'] : 0 );
					break;
				case 'pinterest':
					$sticky_count += ( isset( $icon_counts['pin_count'] ) ? $icon_counts['pin_count'] : 0 );
					break;
				case 'linkedIn':
					$sticky_count += ( isset( $icon_counts['linkedIn_count'] ) ? $icon_counts['linkedIn_count'] : 0 );
					break;
				case 'GooglePlus':
					$sticky_count += ( isset( $icon_counts['google_count'] ) ? $icon_counts['google_count'] : 0 );
					break;
				case 'whatsapp':
					$sticky_count += ( isset( $icon_counts['whatsapp_count'] ) ? $icon_counts['whatsapp_count'] : 0 );
					break;
				case 'vk':
					$sticky_count += ( isset( $icon_counts['vk_count'] ) ? $icon_counts['vk_count'] : 0 );
					break;
			}
		}
	}

	update_option( 'sfsi_premium_sticky_icon_counts', $sticky_count );
	header( 'Content-Type: application/json' );
	echo json_encode( array( "success" ) );
	exit;
}

/* save settings for section 8 */
add_action( 'wp_ajax_plus_updateSrcn9', 'sfsi_plus_options_updater9' );
function sfsi_plus_options_updater9() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "update_plus_step9" ) ) {
		echo json_encode( array( "wrong_nonce" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$sfsi_plus_form_adjustment       = isset( $_POST["sfsi_plus_form_adjustment"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_adjustment"] ) : 'yes';
	$sfsi_plus_form_height           = isset( $_POST["sfsi_plus_form_height"] ) ? intval( $_POST["sfsi_plus_form_height"] ) : '180';
	$sfsi_plus_form_width            = isset( $_POST["sfsi_plus_form_width"] ) ? intval( $_POST["sfsi_plus_form_width"] ) : '230';
	$sfsi_plus_form_border           = isset( $_POST["sfsi_plus_form_border"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_border"] ) : 'no';
	$sfsi_plus_form_border_thickness = isset( $_POST["sfsi_plus_form_border_thickness"] ) ? $_POST["sfsi_plus_form_border_thickness"] : '1';
	$sfsi_plus_form_border_color     = isset( $_POST["sfsi_plus_form_border_color"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_border_color"] ) : '#f3faf2';
	$sfsi_plus_form_background       = isset( $_POST["sfsi_plus_form_background"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_background"] ) : '#eff7f7';

	$sfsi_plus_form_heading_text      = isset( $_POST["sfsi_plus_form_heading_text"] ) ? sanitize_text_field( stripslashes( $_POST["sfsi_plus_form_heading_text"] ) ) : '';
	$sfsi_plus_form_heading_font      = isset( $_POST["sfsi_plus_form_heading_font"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_heading_font"] ) : '';
	$sfsi_plus_form_heading_fontstyle = isset( $_POST["sfsi_plus_form_heading_fontstyle"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_heading_fontstyle"] ) : '';
	$sfsi_plus_form_heading_fontcolor = isset( $_POST["sfsi_plus_form_heading_fontcolor"] ) ? sfsi_plus_sanitize_hex_color( $_POST["sfsi_plus_form_heading_fontcolor"] ) : '';
	$sfsi_plus_form_heading_fontsize  = isset( $_POST["sfsi_plus_form_heading_fontsize"] ) ? intval( $_POST["sfsi_plus_form_heading_fontsize"] ) : '22';
	$sfsi_plus_form_heading_fontalign = isset( $_POST["sfsi_plus_form_heading_fontalign"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_heading_fontalign"] ) : 'center';

	$sfsi_plus_form_field_text      = isset( $_POST["sfsi_plus_form_field_text"] ) ? sanitize_text_field( stripslashes( $_POST["sfsi_plus_form_field_text"] ) ) : '';
	$sfsi_plus_form_field_font      = isset( $_POST["sfsi_plus_form_field_font"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_field_font"] ) : '';
	$sfsi_plus_form_field_fontstyle = isset( $_POST["sfsi_plus_form_field_fontstyle"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_field_fontstyle"] ) : '';
	$sfsi_plus_form_field_fontcolor = isset( $_POST["sfsi_plus_form_field_fontcolor"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_field_fontcolor"] ) : '';
	$sfsi_plus_form_field_fontsize  = isset( $_POST["sfsi_plus_form_field_fontsize"] ) ? intval( $_POST["sfsi_plus_form_field_fontsize"] ) : '22';
	$sfsi_plus_form_field_fontalign = isset( $_POST["sfsi_plus_form_field_fontalign"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_field_fontalign"] ) : 'center';

	$sfsi_plus_form_button_text       = isset( $_POST["sfsi_plus_form_button_text"] ) ? sanitize_text_field( stripslashes( $_POST["sfsi_plus_form_button_text"] ) ) : 'Subscribe';
	$sfsi_plus_form_button_font       = isset( $_POST["sfsi_plus_form_button_font"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_button_font"] ) : '';
	$sfsi_plus_form_button_fontstyle  = isset( $_POST["sfsi_plus_form_button_fontstyle"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_button_fontstyle"] ) : '';
	$sfsi_plus_form_button_fontcolor  = isset( $_POST["sfsi_plus_form_button_fontcolor"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_button_fontcolor"] ) : '';
	$sfsi_plus_form_button_fontsize   = isset( $_POST["sfsi_plus_form_button_fontsize"] ) ? intval( $_POST["sfsi_plus_form_button_fontsize"] ) : '22';
	$sfsi_plus_form_button_fontalign  = isset( $_POST["sfsi_plus_form_button_fontalign"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_button_fontalign"] ) : 'center';
	$sfsi_plus_form_button_background = isset( $_POST["sfsi_plus_form_button_background"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_button_background"] ) : '#5a6570';

	$sfsi_plus_shall_display_privacy_notice = isset( $_POST["sfsi_plus_shall_display_privacy_notice"] ) ? sanitize_text_field( $_POST["sfsi_plus_shall_display_privacy_notice"] ) : 'no';

	$sfsi_plus_form_privacynotice_text = isset( $_POST["sfsi_plus_form_privacynotice_text"] ) ? sanitize_text_field( stripslashes( $_POST["sfsi_plus_form_privacynotice_text"] ) ) : 'We will treat your data confidentially';

	$sfsi_plus_form_privacynotice_font = isset( $_POST["sfsi_plus_form_privacynotice_font"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_privacynotice_font"] ) : 'Helvetica,Arial,sans-serif';

	$sfsi_plus_form_privacynotice_fontstyle = isset( $_POST["sfsi_plus_form_privacynotice_fontstyle"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_privacynotice_fontstyle"] ) : 'center';

	$sfsi_plus_form_privacynotice_fontcolor = isset( $_POST["sfsi_plus_form_privacynotice_fontcolor"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_privacynotice_fontcolor"] ) : '#000000';
	$sfsi_plus_form_privacynotice_fontsize  = isset( $_POST["sfsi_plus_form_privacynotice_fontsize"] ) ? intval( $_POST["sfsi_plus_form_privacynotice_fontsize"] ) : '16';
	$sfsi_plus_form_privacynotice_fontalign = isset( $_POST["sfsi_plus_form_privacynotice_fontalign"] ) ? sanitize_text_field( $_POST["sfsi_plus_form_privacynotice_fontalign"] ) : 'center';

	/* icons pop options */
	$up_option9 = array(
		'sfsi_plus_form_adjustment'       => sanitize_text_field( $sfsi_plus_form_adjustment ),
		'sfsi_plus_form_height'           => intval( $sfsi_plus_form_height ),
		'sfsi_plus_form_width'            => intval( $sfsi_plus_form_width ),
		'sfsi_plus_form_border'           => sanitize_text_field( $sfsi_plus_form_border ),
		'sfsi_plus_form_border_thickness' => intval( $sfsi_plus_form_border_thickness ),
		'sfsi_plus_form_border_color'     => sfsi_plus_sanitize_hex_color( $sfsi_plus_form_border_color ),
		'sfsi_plus_form_background'       => sfsi_plus_sanitize_hex_color( $sfsi_plus_form_background ),

		'sfsi_plus_form_heading_text'      => sanitize_text_field( stripslashes( $sfsi_plus_form_heading_text ) ),
		'sfsi_plus_form_heading_font'      => sanitize_text_field( $sfsi_plus_form_heading_font ),
		'sfsi_plus_form_heading_fontstyle' => sanitize_text_field( $sfsi_plus_form_heading_fontstyle ),
		'sfsi_plus_form_heading_fontcolor' => sfsi_plus_sanitize_hex_color( $sfsi_plus_form_heading_fontcolor ),
		'sfsi_plus_form_heading_fontsize'  => intval( $sfsi_plus_form_heading_fontsize ),
		'sfsi_plus_form_heading_fontalign' => sanitize_text_field( $sfsi_plus_form_heading_fontalign ),

		'sfsi_plus_form_field_text'      => sanitize_text_field( stripslashes( $sfsi_plus_form_field_text ) ),
		'sfsi_plus_form_field_font'      => sanitize_text_field( $sfsi_plus_form_field_font ),
		'sfsi_plus_form_field_fontstyle' => sanitize_text_field( $sfsi_plus_form_field_fontstyle ),
		'sfsi_plus_form_field_fontcolor' => sfsi_plus_sanitize_hex_color( $sfsi_plus_form_field_fontcolor ),
		'sfsi_plus_form_field_fontsize'  => intval( $sfsi_plus_form_field_fontsize ),
		'sfsi_plus_form_field_fontalign' => sanitize_text_field( $sfsi_plus_form_field_fontalign ),

		'sfsi_plus_form_button_text'       => sanitize_text_field( stripslashes( $sfsi_plus_form_button_text ) ),
		'sfsi_plus_form_button_font'       => sanitize_text_field( $sfsi_plus_form_button_font ),
		'sfsi_plus_form_button_fontstyle'  => sanitize_text_field( $sfsi_plus_form_button_fontstyle ),
		'sfsi_plus_form_button_fontcolor'  => sfsi_plus_sanitize_hex_color( $sfsi_plus_form_button_fontcolor ),
		'sfsi_plus_form_button_fontsize'   => intval( $sfsi_plus_form_button_fontsize ),
		'sfsi_plus_form_button_fontalign'  => sanitize_text_field( $sfsi_plus_form_button_fontalign ),
		'sfsi_plus_form_button_background' => sfsi_plus_sanitize_hex_color( $sfsi_plus_form_button_background ),

		'sfsi_plus_shall_display_privacy_notice' => sanitize_text_field( $sfsi_plus_shall_display_privacy_notice ),
		'sfsi_plus_form_privacynotice_text'      => trim( sfsi_premium_strip_tags_content( stripslashes( $sfsi_plus_form_privacynotice_text ) ) ),
		'sfsi_plus_form_privacynotice_font'      => sanitize_text_field( $sfsi_plus_form_privacynotice_font ),
		'sfsi_plus_form_privacynotice_fontstyle' => sanitize_text_field( $sfsi_plus_form_privacynotice_fontstyle ),
		'sfsi_plus_form_privacynotice_fontcolor' => sfsi_plus_sanitize_hex_color( $sfsi_plus_form_privacynotice_fontcolor ),
		'sfsi_plus_form_privacynotice_fontsize'  => intval( $sfsi_plus_form_privacynotice_fontsize ),
		'sfsi_plus_form_privacynotice_fontalign' => sanitize_text_field( $sfsi_plus_form_privacynotice_fontalign )
	);

	update_option( 'sfsi_premium_section9_options', serialize( $up_option9 ) );
	header( 'Content-Type: application/json' );
	echo json_encode( array( "success" ) );
	exit;
}

/* upload custom icons images */
/* get counts for admin section */
function sfsi_plus_getCounts( $formated = true, $instafetch = false ) {
	$socialObj                     = new sfsi_plus_SocialHelper();
	$option4                       = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
	$sfsi_premium_section2_options = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );
	$scounts                       = array(
		'rss_count'      => '',
		'email_count'    => '',
		'fb_count'       => '',
		'twitter_count'  => '',
		'threads_count'  => '',
		'bluesky_count'  => '',
		'linkedIn_count' => '',
		'youtube_count'  => '',
		'pin_count'      => '',
		'share_count'    => '',
		'phone_count'    => '',
	);

	$option4['sfsi_plus_email_countsFrom']    = ( isset( $option4['sfsi_plus_email_countsFrom'] ) )
		? sanitize_text_field( $option4['sfsi_plus_email_countsFrom'] )
		: '';
	$option4['sfsi_plus_email_manualCounts']  = ( isset( $option4['sfsi_plus_email_manualCounts'] ) )
		? intval( $option4['sfsi_plus_email_manualCounts'] )
		: '';
	$option4['sfsi_plus_rss_countsDisplay']   = ( isset( $option4['sfsi_plus_rss_countsDisplay'] ) )
		? sanitize_text_field( $option4['sfsi_plus_rss_countsDisplay'] )
		: '';
	$option4['sfsi_plus_rss_manualCounts']    = ( isset( $option4['sfsi_plus_rss_manualCounts'] ) )
		? intval( $option4['sfsi_plus_rss_manualCounts'] )
		: '';
	$option4['sfsi_plus_email_countsDisplay'] = ( isset( $option4['sfsi_plus_email_countsDisplay'] ) )
		? sanitize_text_field( $option4['sfsi_plus_email_countsDisplay'] )
		: '';

	$option4['sfsi_plus_facebook_countsDisplay']   = ( isset( $option4['sfsi_plus_facebook_countsDisplay'] ) )
		? sanitize_text_field( $option4['sfsi_plus_facebook_countsDisplay'] )
		: '';
	$option4['sfsi_plus_facebook_countsFrom']      = ( isset( $option4['sfsi_plus_facebook_countsFrom'] ) )
		? sanitize_text_field( $option4['sfsi_plus_facebook_countsFrom'] )
		: '';
	$option4['sfsi_plus_facebook_mypageCounts']    = ( isset( $option4['sfsi_plus_facebook_mypageCounts'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsi_plus_facebook_mypageCounts'] )
		: '';
	$option4['sfsi_plus_facebook_appid']           = ( isset( $option4['sfsi_plus_facebook_appid'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsi_plus_facebook_appid'] )
		: '';
	$option4['sfsi_plus_facebook_appsecret']       = ( isset( $option4['sfsi_plus_facebook_appsecret'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsi_plus_facebook_appsecret'] )
		: '';
	$option4['sfsi_plus_facebook_manualCounts']    = ( isset( $option4['sfsi_plus_facebook_manualCounts'] ) )
		? intval( $option4['sfsi_plus_facebook_manualCounts'] )
		: '';
	$option4['sfsi_plus_facebook_countsFrom_blog'] = ( isset( $option4['sfsi_plus_facebook_countsFrom_blog'] ) )
		? ( $option4['sfsi_plus_facebook_countsFrom_blog'] )
		: '';

	$option4['sfsi_plus_twitter_countsDisplay']       = ( isset( $option4['sfsi_plus_twitter_countsDisplay'] ) )
		? sanitize_text_field( $option4['sfsi_plus_twitter_countsDisplay'] )
		: '';
	$option4['sfsi_plus_threads_countsDisplay']       = ( isset( $option4['sfsi_plus_threads_countsDisplay'] ) )
		? sanitize_text_field( $option4['sfsi_plus_threads_countsDisplay'] )
		: '';
	$option4['sfsi_plus_bluesky_countsDisplay']       = ( isset( $option4['sfsi_plus_bluesky_countsDisplay'] ) )
		? sanitize_text_field( $option4['sfsi_plus_bluesky_countsDisplay'] )
		: '';
	$option4['sfsi_plus_twitter_countsFrom']          = ( isset( $option4['sfsi_plus_twitter_countsFrom'] ) )
		? sanitize_text_field( $option4['sfsi_plus_twitter_countsFrom'] )
		: '';
	$option4['sfsi_plus_threads_countsFrom']          = ( isset( $option4['sfsi_plus_threads_countsFrom'] ) )
		? sanitize_text_field( $option4['sfsi_plus_threads_countsFrom'] )
		: '';
	$option4['sfsi_plus_bluesky_countsFrom']          = ( isset( $option4['sfsi_plus_bluesky_countsFrom'] ) )
		? sanitize_text_field( $option4['sfsi_plus_bluesky_countsFrom'] )
		: '';
	$option4['sfsi_plus_twitter_manualCounts']        = ( isset( $option4['sfsi_plus_twitter_manualCounts'] ) )
		? intval( $option4['sfsi_plus_twitter_manualCounts'] )
		: '';
	$option4['sfsi_plus_threads_manualCounts']        = ( isset( $option4['sfsi_plus_threads_manualCounts'] ) )
		? intval( $option4['sfsi_plus_threads_manualCounts'] )
		: '';
	$option4['sfsi_plus_bluesky_manualCounts']        = ( isset( $option4['sfsi_plus_bluesky_manualCounts'] ) )
		? intval( $option4['sfsi_plus_bluesky_manualCounts'] )
		: '';
	$option4['sfsiplus_tw_consumer_key']              = ( isset( $option4['sfsiplus_tw_consumer_key'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsiplus_tw_consumer_key'] )
		: '';
	$option4['sfsiplus_tw_consumer_secret']           = ( isset( $option4['sfsiplus_tw_consumer_secret'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsiplus_tw_consumer_secret'] )
		: '';
	$option4['sfsiplus_tw_oauth_access_token']        = ( isset( $option4['sfsiplus_tw_oauth_access_token'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsiplus_tw_oauth_access_token'] )
		: '';
	$option4['sfsiplus_tw_oauth_access_token_secret'] = ( isset( $option4['sfsiplus_tw_oauth_access_token_secret'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsiplus_tw_oauth_access_token_secret'] )
		: '';
	$option4['sfsi_plus_youtube_countsDisplay']       = ( isset( $option4['sfsi_plus_youtube_countsDisplay'] ) )
		? sanitize_text_field( $option4['sfsi_plus_youtube_countsDisplay'] )
		: '';
	$option4['sfsi_plus_youtube_countsFrom']          = ( isset( $option4['sfsi_plus_youtube_countsFrom'] ) )
		? sanitize_text_field( $option4['sfsi_plus_youtube_countsFrom'] )
		: '';
	$option4['sfsi_plus_youtubeusernameorid']         = ( isset( $option4['sfsi_plus_youtubeusernameorid'] ) )
		? sanitize_text_field( $option4['sfsi_plus_youtubeusernameorid'] )
		: '';
	$option4['sfsi_plus_youtube_manualCounts']        = ( isset( $option4['sfsi_plus_youtube_manualCounts'] ) )
		? intval( $option4['sfsi_plus_youtube_manualCounts'] )
		: '';
	$option4['sfsi_plus_youtube_channelId']           = ( isset( $option4['sfsi_plus_youtube_channelId'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsi_plus_youtube_channelId'] )
		: '';
	$option4['sfsi_plus_instagram_manualCounts']      = ( isset( $option4['sfsi_plus_instagram_manualCounts'] ) )
		? intval( $option4['sfsi_plus_instagram_manualCounts'] )
		: '';
	$option4['sfsi_plus_instagram_User']              = ( isset( $option4['sfsi_plus_instagram_User'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsi_plus_instagram_User'] )
		: '';
	$option4['sfsi_plus_instagram_clientid']          = ( isset( $option4['sfsi_plus_instagram_clientid'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsi_plus_instagram_clientid'] )
		: '';
	$option4['sfsi_plus_instagram_appurl']            = ( isset( $option4['sfsi_plus_instagram_appurl'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsi_plus_instagram_appurl'] )
		: '';
	$option4['sfsi_plus_instagram_token']             = ( isset( $option4['sfsi_plus_instagram_token'] ) )
		? sfsi_plus_sanitize_field( $option4['sfsi_plus_instagram_token'] )
		: '';
	$option4['sfsi_plus_instagram_countsFrom']        = ( isset( $option4['sfsi_plus_instagram_countsFrom'] ) )
		? sanitize_text_field( $option4['sfsi_plus_instagram_countsFrom'] )
		: '';
	$option4['sfsi_plus_instagram_countsDisplay']     = ( isset( $option4['sfsi_plus_instagram_countsDisplay'] ) )
		? sanitize_text_field( $option4['sfsi_plus_instagram_countsDisplay'] )
		: '';
	$option4['sfsi_plus_linkedIn_countsFrom']         = ( isset( $option4['sfsi_plus_linkedIn_countsFrom'] ) )
		? sanitize_text_field( $option4['sfsi_plus_linkedIn_countsFrom'] )
		: '';
	$option4['sfsi_plus_linkedIn_manualCounts']       = ( isset( $option4['sfsi_plus_linkedIn_manualCounts'] ) )
		? intval( $option4['sfsi_plus_linkedIn_manualCounts'] )
		: '';
	$option4['sfsi_plus_houzz_manualCounts']          = ( isset( $option4['sfsi_plus_houzz_manualCounts'] ) )
		? intval( $option4['sfsi_plus_houzz_manualCounts'] )
		: '';
	$option4['sfsi_plus_pinterest_countsFrom']        = ( isset( $option4['sfsi_plus_pinterest_countsFrom'] ) )
		? intval( $option4['sfsi_plus_pinterest_countsFrom'] )
		: '';
	$option4['sfsi_plus_pinterest_manualCounts']      = ( isset( $option4['sfsi_plus_pinterest_manualCounts'] ) )
		? intval( $option4['sfsi_plus_pinterest_manualCounts'] )
		: '';
	$option4['sfsi_plus_shares_manualCounts']         = ( isset( $option4['sfsi_plus_shares_manualCounts'] ) )
		? intval( $option4['sfsi_plus_shares_manualCounts'] )
		: '';
	$option4['sfsi_plus_snapchat_manualCounts']       = ( isset( $option4['sfsi_plus_snapchat_manualCounts'] ) )
		? intval( $option4['sfsi_plus_snapchat_manualCounts'] )
		: '';

	$option4['sfsi_plus_ria_manualCounts'] = ( isset( $option4['sfsi_plus_ria_manualCounts'] ) )
		? intval( $option4['sfsi_plus_ria_manualCounts'] )
		: '';

	$option4['sfsi_plus_inha_manualCounts'] = ( isset( $option4['sfsi_plus_inha_manualCounts'] ) )
		? intval( $option4['sfsi_plus_inha_manualCounts'] )
		: '';

	$option4['sfsi_plus_whatsapp_manualCounts']    = ( isset( $option4['sfsi_plus_whatsapp_manualCounts'] ) )
		? intval( $option4['sfsi_plus_whatsapp_manualCounts'] )
		: '';
	$option4['sfsi_plus_skype_manualCounts']       = ( isset( $option4['sfsi_plus_skype_manualCounts'] ) )
		? intval( $option4['sfsi_plus_skype_manualCounts'] )
		: '';
	$option4['sfsi_plus_vimeo_manualCounts']       = ( isset( $option4['sfsi_plus_vimeo_manualCounts'] ) )
		? intval( $option4['sfsi_plus_vimeo_manualCounts'] )
		: '';
	$option4['sfsi_plus_soundcloud_manualCounts']  = ( isset( $option4['sfsi_plus_soundcloud_manualCounts'] ) )
		? intval( $option4['sfsi_plus_soundcloud_manualCounts'] )
		: '';
	$option4['sfsi_plus_yummly_manualCounts']      = ( isset( $option4['sfsi_plus_yummly_manualCounts'] ) )
		? intval( $option4['sfsi_plus_yummly_manualCounts'] )
		: '';
	$option4['sfsi_plus_flickr_manualCounts']      = ( isset( $option4['sfsi_plus_flickr_manualCounts'] ) )
		? intval( $option4['sfsi_plus_flickr_manualCounts'] )
		: '';
	$option4['sfsi_plus_reddit_manualCounts']      = ( isset( $option4['sfsi_plus_reddit_manualCounts'] ) )
		? intval( $option4['sfsi_plus_reddit_manualCounts'] )
		: '';
	$option4['sfsi_plus_tumblr_manualCounts']      = ( isset( $option4['sfsi_plus_tumblr_manualCounts'] ) )
		? intval( $option4['sfsi_plus_tumblr_manualCounts'] )
		: '';
	$option4['sfsi_plus_fbmessenger_manualCounts'] = ( isset( $option4['sfsi_plus_fbmessenger_manualCounts'] ) )
		? intval( $option4['sfsi_plus_fbmessenger_manualCounts'] )
		: '';
	$option4['sfsi_plus_gab_manualCounts']         = ( isset( $option4['sfsi_plus_gab_manualCounts'] ) )
		? intval( $option4['sfsi_plus_gab_manualCounts'] )
		: '';
	$option4['sfsi_plus_mix_manualCounts']         = ( isset( $option4['sfsi_plus_mix_manualCounts'] ) )
		? intval( $option4['sfsi_plus_mix_manualCounts'] )
		: '';
	$option4['sfsi_plus_ok_manualCounts']          = ( isset( $option4['sfsi_plus_ok_manualCounts'] ) )
		? intval( $option4['sfsi_plus_ok_manualCounts'] )
		: '';
	$option4['sfsi_plus_vk_manualCounts']          = ( isset( $option4['sfsi_plus_vk_manualCounts'] ) )
		? intval( $option4['sfsi_plus_vk_manualCounts'] )
		: '';
	$option4['sfsi_plus_telegram_manualCounts']    = ( isset( $option4['sfsi_plus_telegram_manualCounts'] ) )
		? intval( $option4['sfsi_plus_telegram_manualCounts'] )
		: '';
	$option4['sfsi_plus_weibo_manualCounts']       = ( isset( $option4['sfsi_plus_weibo_manualCounts'] ) )
		? intval( $option4['sfsi_plus_weibo_manualCounts'] )
		: '';
	$option4['sfsi_plus_xing_manualCounts']        = ( isset( $option4['sfsi_plus_xing_manualCounts'] ) )
		? intval( $option4['sfsi_plus_xing_manualCounts'] )
		: '';
	$option4['sfsi_plus_mastodon_manualCounts']    = ( isset( $option4['sfsi_plus_mastodon_manualCounts'] ) )
		? intval( $option4['sfsi_plus_mastodon_manualCounts'] )
		: '';
	$option4['sfsi_plus_wechat_manualCounts']      = ( isset( $option4['sfsi_plus_wechat_manualCounts'] ) )
		? intval( $option4['sfsi_plus_wechat_manualCounts'] )
		: '';
	$option4['sfsi_plus_copylink_manualCounts']      = ( isset( $option4['sfsi_plus_copylink_manualCounts'] ) )
		? intval( $option4['sfsi_plus_copylink_manualCounts'] )
		: '';
	$option4['sfsi_plus_phone_manualCounts']       = ( isset( $option4['sfsi_plus_phone_manualCounts'] ) )
		? intval( $option4['sfsi_plus_phone_manualCounts'] )
		: '';
	/* get rss count */
	if ( ! empty( $option4['sfsi_plus_rss_manualCounts'] ) ) {
		$scounts['rss_count'] = $option4['sfsi_plus_rss_manualCounts'];
	}
	/* get email count */
	if ( $option4['sfsi_plus_email_countsFrom'] == "source" ) {
		$feed_id   = sanitize_text_field( get_option( 'sfsi_premium_feed_id', false ) );
		$feed_data = $socialObj->SFSI_getFeedSubscriber( $feed_id );
		if ( $formated ) {
			$scounts['email_count'] = $socialObj->format_num( $feed_data );
		} else {
			$scounts['email_count'] = $feed_data;
		}
		if ( empty( $scounts['email_count'] ) ) {
			$scounts['email_count'] = (string) "0";
		}
	} else {
		$scounts['email_count'] = $option4['sfsi_plus_email_manualCounts'];
	}

	/* get fb count */
	if ( $option4['sfsi_plus_facebook_countsFrom'] == "likes" ) {
		$url = home_url();

		$fb_data = $socialObj->sfsi_get_fb( $url );

		$fb_count = isset( $fb_data['share_count'] ) && ! empty( $fb_data['share_count'] ) ? (int) $fb_data['share_count'] : 0;
		if ( $formated ) {
			$scounts['fb_count'] = $socialObj->format_num( $fb_count );
		} else {
			$scounts['fb_count'] = $fb_count;
		}
	} else if ( $option4['sfsi_plus_facebook_countsFrom'] == "followers" ) {
		$url     = home_url();
		$fb_data = $socialObj->sfsi_get_fb( $url );
		if ( $formated ) {
			$scounts['fb_count'] = $socialObj->format_num( $fb_data['share_count'] );
		} else {
			$scounts['fb_count'] = $fb_data['share_count'];
		};
		if ( empty( $scounts['fb_count'] ) ) {
			$scounts['fb_count'] = (string) "0";
		}
	} else if ( $option4['sfsi_plus_facebook_countsFrom'] == "mypage" ) {
		$url      = $option4['sfsi_plus_facebook_mypageCounts'];
		$fb_data  = $socialObj->sfsi_get_fb_pagelike( $url );
		$fb_count = isset( $fb_data['share_count'] ) && ! empty( $fb_data['share_count'] ) ? (int) $fb_data['share_count'] : 0;

		if ( $formated ) {
			$scounts['fb_count'] = $socialObj->format_num( $fb_count );
		} else {
			$scounts['fb_count'] = $fb_count;
		}
	} else {
		if ( $formated ) {
			$scounts['fb_count'] = $socialObj->format_num( $option4['sfsi_plus_facebook_manualCounts'] );
		} else {
			$scounts['fb_count'] = $option4['sfsi_plus_facebook_manualCounts'];
		}
	}
	/* get twitter counts */
	if ( $option4['sfsi_plus_twitter_countsFrom'] == "source" ) {
		$twitter_user = $sfsi_premium_section2_options['sfsi_plus_twitter_followUserName'];
		$tw_settings  = array(
			'sfsiplus_tw_consumer_key'              => $option4['sfsiplus_tw_consumer_key'],
			'sfsiplus_tw_consumer_secret'           => $option4['sfsiplus_tw_consumer_secret'],
			'sfsiplus_tw_oauth_access_token'        => $option4['sfsiplus_tw_oauth_access_token'],
			'sfsiplus_tw_oauth_access_token_secret' => $option4['sfsiplus_tw_oauth_access_token_secret']
		);

		$followers = $socialObj->sfsi_get_tweets( $twitter_user, $tw_settings );
		if ( $formated ) {
			$scounts['twitter_count'] = $socialObj->format_num( $followers );
		} else {
			$scounts['twitter_count'] = $followers;
		}
	} else {
		if ( $formated ) {
			$scounts['twitter_count'] = $socialObj->format_num( $option4['sfsi_plus_twitter_manualCounts'] );
		} else {
			$scounts['twitter_count'] = $option4['sfsi_plus_twitter_manualCounts'];
		}
	}


	/* get linkedIn counts */
	if ( $option4['sfsi_plus_linkedIn_countsFrom'] == "follower" ) {
		$linkedIn_compay = $sfsi_premium_section2_options['sfsi_plus_linkedin_followCompany'];
		$linkedIn_compay = $option4['sfsi_plus_ln_company'];
		$ln_settings     = array(
			'sfsi_plus_ln_api_key'          => $option4['sfsi_plus_ln_api_key'],
			'sfsi_plus_ln_secret_key'       => $option4['sfsi_plus_ln_secret_key'],
			'sfsi_plus_ln_oAuth_user_token' => $option4['sfsi_plus_ln_oAuth_user_token']
		);
		$followers       = $socialObj->sfsi_getlinkedin_follower( $linkedIn_compay, $ln_settings );
		if ( $formated ) {
			$scounts['linkedIn_count'] = $socialObj->format_num( $followers );
		} else {
			$scounts['linkedIn_count'] = $followers;
		}
	} else {
		if ( $formated ) {
			$scounts['linkedIn_count'] = $socialObj->format_num( $option4['sfsi_plus_linkedIn_manualCounts'] );
		} else {
			$scounts['linkedIn_count'] = $option4['sfsi_plus_linkedIn_manualCounts'];
		}
	}
	/* get youtube counts */
	if ( $option4['sfsi_plus_youtube_countsFrom'] == "subscriber" ) {
		if (
			isset( $option4[''] )
		) {
			$youtube_user = $option4[''];

			$youtube_user = ( isset( $option4[''] ) &&
			                  ! empty( $option4[''] ) ) ? $option4[''] : 'follow.it';

			$followers = $socialObj->sfsi_get_youtube( $youtube_user );
			if ( $formated ) {
				$scounts['youtube_count'] = $socialObj->format_num( $followers );
			} else {
				$scounts['youtube_count'] = $followers;
			}
		} else {
			if ( $formated ) {
				$scounts['youtube_count'] = 01;
			} else {
				$scounts['youtube_count'] = 01;
			}
		}
	} else {
		if ( $formated ) {
			$scounts['youtube_count'] = $option4['sfsi_plus_youtube_manualCounts'];
		} else {
			$scounts['youtube_count'] = $option4['sfsi_plus_youtube_manualCounts'];
		}
	}
	/* get Pinterest counts */
	if ( $option4['sfsi_plus_pinterest_countsFrom'] == "pins" ) {
		$url  = home_url();
		$pins = $socialObj->sfsi_get_pinterest( $url );
		if ( $formated ) {
			$scounts['pin_count'] = $socialObj->format_num( $pins );
		} else {
			$scounts['pin_count'] = $pins;
		}
	} else {
		if ( $formated ) {
			$scounts['pin_count'] = $option4['sfsi_plus_pinterest_manualCounts'];
		} else {
			$scounts['pin_count'] = $option4['sfsi_plus_pinterest_manualCounts'];
		}
	}
	/* get addthis share counts */
	if ( isset( $option4['sfsi_plus_shares_countsFrom'] ) && $option4['sfsi_plus_shares_countsFrom'] == "shares" && isset( $option4['sfsi_share_countsDisplay'] ) && $option4['sfsi_share_countsDisplay'] == "yes" ) {
		$shares = $socialObj->sfsi_get_atthis();
		if ( $formated ) {
			$scounts['share_count'] = $socialObj->format_num( $shares );
		} else {
			$scounts['share_count'] = $shares;
		}
	} else {
		if ( $formated ) {
			$scounts['share_count'] = $option4['sfsi_plus_shares_manualCounts'];
		} else {
			$scounts['share_count'] = $option4['sfsi_plus_shares_manualCounts'];
		}
	}
	/* get instagram count */
	if ( $option4['sfsi_plus_instagram_countsFrom'] == "followers" ) {
		$iuser_name = $option4['sfsi_plus_instagram_User'];
		if ( $instafetch ) {
			$counts = $socialObj->sfsi_get_instagramFollowersFetch( $iuser_name );
		} else {
			$counts = $socialObj->sfsi_get_instagramFollowers( $iuser_name );
		}
		if ( empty( $counts ) ) {
			if ( $formated ) {
				$scounts['instagram_count'] = (string) "0";
			} else {
				$scounts['instagram_count'] = (string) "0";
			}
		} else {
			if ( $formated ) {
				$scounts['instagram_count'] = $counts;
			} else {
				$scounts['instagram_count'] = $counts;
			}
		}
	} else {
		if ( $formated ) {
			$scounts['instagram_count'] = $option4['sfsi_plus_instagram_manualCounts'];
		} else {
			$scounts['instagram_count'] = $option4['sfsi_plus_instagram_manualCounts'];
		}
	}

	/* get houzz count */
	if (
		isset( $option4['sfsi_plus_houzz_countsFrom'] ) &&
		$option4['sfsi_plus_houzz_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_houzz_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['houzz_count'] = $option4['sfsi_plus_houzz_manualCounts'];
			} else {
				$scounts['houzz_count'] = $option4['sfsi_plus_houzz_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['houzz_count'] = '20';
			} else {
				$scounts['houzz_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_houzz_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['houzz_count'] = '20';
		} else {
			$scounts['houzz_count'] = '20';
		}
	}

	/* get snapchat count */
	if (
		isset( $option4['sfsi_plus_snapchat_countsFrom'] ) &&
		$option4['sfsi_plus_snapchat_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_snapchat_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['snapchat_count'] = $option4['sfsi_plus_snapchat_manualCounts'];
			} else {
				$scounts['snapchat_count'] = $option4['sfsi_plus_snapchat_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['snapchat_count'] = '20';
			} else {
				$scounts['snapchat_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_snapchat_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['snapchat_count'] = '20';
		} else {
			$scounts['snapchat_count'] = '20';
		}
	}

	/* get ria count */
	if (
		isset( $option4['sfsi_plus_ria_countsFrom'] ) &&
		$option4['sfsi_plus_ria_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_ria_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['ria_count'] = $option4['sfsi_plus_ria_manualCounts'];
			} else {
				$scounts['ria_count'] = $option4['sfsi_plus_ria_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['ria_count'] = '20';
			} else {
				$scounts['ria_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_ria_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['ria_count'] = '20';
		} else {
			$scounts['ria_count'] = '20';
		}
	}

	/* get inha count */
	if (
		isset( $option4['sfsi_plus_inha_countsFrom'] ) &&
		$option4['sfsi_plus_inha_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_inha_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['inha_count'] = $option4['sfsi_plus_inha_manualCounts'];
			} else {
				$scounts['inha_count'] = $option4['sfsi_plus_inha_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['inha_count'] = '20';
			} else {
				$scounts['inha_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_inha_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['inha_count'] = '20';
		} else {
			$scounts['inha_count'] = '20';
		}
	}

	/* get whatsapp count */
	if (
		isset( $option4['sfsi_plus_whatsapp_countsFrom'] ) &&
		$option4['sfsi_plus_whatsapp_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_whatsapp_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['whatsapp_count'] = $option4['sfsi_plus_whatsapp_manualCounts'];
			} else {
				$scounts['whatsapp_count'] = $option4['sfsi_plus_whatsapp_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['whatsapp_count'] = '20';
			} else {
				$scounts['whatsapp_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_whatsapp_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['whatsapp_count'] = '20';
		} else {
			$scounts['whatsapp_count'] = '20';
		}
	}

	/* get skype count */
	if (
		isset( $option4['sfsi_plus_skype_countsFrom'] ) &&
		$option4['sfsi_plus_skype_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_skype_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['skype_count'] = $option4['sfsi_plus_skype_manualCounts'];
			} else {
				$scounts['skype_count'] = $option4['sfsi_plus_skype_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['skype_count'] = '20';
			} else {
				$scounts['skype_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_skype_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['skype_count'] = '20';
		} else {
			$scounts['skype_count'] = '20';
		}
	}

	/* get vimeo count */
	if (
		isset( $option4['sfsi_plus_vimeo_countsFrom'] ) &&
		$option4['sfsi_plus_vimeo_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_vimeo_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['vimeo_count'] = $option4['sfsi_plus_vimeo_manualCounts'];
			} else {
				$scounts['vimeo_count'] = $option4['sfsi_plus_vimeo_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['vimeo_count'] = '20';
			} else {
				$scounts['vimeo_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_vimeo_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['vimeo_count'] = '20';
		} else {
			$scounts['vimeo_count'] = '20';
		}
	}

	/* get soundcloud count */
	if (
		isset( $option4['sfsi_plus_soundcloud_countsFrom'] ) &&
		$option4['sfsi_plus_soundcloud_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_soundcloud_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['soundcloud_count'] = $option4['sfsi_plus_soundcloud_manualCounts'];
			} else {
				$scounts['soundcloud_count'] = $option4['sfsi_plus_soundcloud_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['soundcloud_count'] = '20';
			} else {
				$scounts['soundcloud_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_soundcloud_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['soundcloud_count'] = '20';
		} else {
			$scounts['soundcloud_count'] = '20';
		}
	}

	/* get yummly count */
	if (
		isset( $option4['sfsi_plus_yummly_countsFrom'] ) &&
		$option4['sfsi_plus_yummly_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_yummly_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['yummly_count'] = $option4['sfsi_plus_yummly_manualCounts'];
			} else {
				$scounts['yummly_count'] = $option4['sfsi_plus_yummly_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['yummly_count'] = '20';
			} else {
				$scounts['yummly_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_yummly_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['yummly_count'] = '20';
		} else {
			$scounts['yummly_count'] = '20';
		}
	} elseif (
		isset( $option4['sfsi_plus_yummly_countsFrom'] ) &&
		$option4['sfsi_plus_yummly_countsFrom'] == "share"
	) {
		$feed_id   = sanitize_text_field( get_option( 'sfsi_premium_feed_id', false ) );
		$feed_data = $socialObj->sfsi_yummly_share_count( $feed_id );
		if ( $formated ) {
			$scounts['yummly_count'] = $socialObj->format_num( $feed_data );
		} else {
			$scounts['yummly_count'] = $feed_data;
		}
		if ( empty( $scounts['yummly_count'] ) ) {
			$scounts['yummly_count'] = (string) "0";
		}
	}

	/* get flickr count */
	if (
		isset( $option4['sfsi_plus_flickr_countsFrom'] ) &&
		$option4['sfsi_plus_flickr_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_flickr_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['flickr_count'] = $option4['sfsi_plus_flickr_manualCounts'];
			} else {
				$scounts['flickr_count'] = $option4['sfsi_plus_flickr_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['flickr_count'] = '20';
			} else {
				$scounts['flickr_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_flickr_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['flickr_count'] = '20';
		} else {
			$scounts['flickr_count'] = '20';
		}
	}

	/* get reddit count */
	if (
		isset( $option4['sfsi_plus_reddit_countsFrom'] ) &&
		$option4['sfsi_plus_reddit_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_reddit_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['reddit_count'] = $option4['sfsi_plus_reddit_manualCounts'];
			} else {
				$scounts['reddit_count'] = $option4['sfsi_plus_reddit_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['reddit_count'] = '20';
			} else {
				$scounts['reddit_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_reddit_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['reddit_count'] = '20';
		} else {
			$scounts['reddit_count'] = '20';
		}
	}

    /* get threads count */
	if ( isset( $option4['sfsi_plus_threads_countsFrom'] ) && $option4['sfsi_plus_threads_countsFrom'] == "manual" ) {
		if ( isset( $option4['sfsi_plus_threads_manualCounts'] ) ) {
			$scounts['threads_count'] = $option4['sfsi_plus_threads_manualCounts'];

		} else {
			$scounts['threads_count'] = '20';
		}
	} elseif ( ! isset( $option4['sfsi_plus_threads_countsFrom'] ) ) {
		$scounts['threads_count'] = '20';
	}
    /* get bluesky count */
	if ( isset( $option4['sfsi_plus_bluesky_countsFrom'] ) && $option4['sfsi_plus_bluesky_countsFrom'] == "manual" ) {
		if ( isset( $option4['sfsi_plus_bluesky_manualCounts'] ) ) {
			$scounts['bluesky_count'] = $option4['sfsi_plus_bluesky_manualCounts'];
		} else {
			$scounts['bluesky_count'] = '20';
		}
	} elseif ( ! isset( $option4['sfsi_plus_bluesky_countsFrom'] ) ) {
		$scounts['bluesky_count'] = '20';
	}

	/* get tumblr count */
	if (
		isset( $option4['sfsi_plus_tumblr_countsFrom'] ) &&
		$option4['sfsi_plus_tumblr_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_tumblr_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['tumblr_count'] = $option4['sfsi_plus_tumblr_manualCounts'];
			} else {
				$scounts['tumblr_count'] = $option4['sfsi_plus_tumblr_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['tumblr_count'] = '20';
			} else {
				$scounts['tumblr_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_tumblr_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['tumblr_count'] = '20';
		} else {
			$scounts['tumblr_count'] = '20';
		}
	}
	/* get fbmessenger count */
	if (
		isset( $option4['sfsi_plus_fbmessenger_countsFrom'] ) &&
		$option4['sfsi_plus_fbmessenger_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_fbmessenger_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['fbmessenger_count'] = $option4['sfsi_plus_fbmessenger_manualCounts'];
			} else {
				$scounts['fbmessenger_count'] = $option4['sfsi_plus_fbmessenger_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['fbmessenger_count'] = '20';
			} else {
				$scounts['fbmessenger_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_fbmessenger_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['fbmessenger_count'] = '20';
		} else {
			$scounts['fbmessenger_count'] = '20';
		}
	}

	/* get gab count */
	if (
		isset( $option4['sfsi_plus_gab_countsFrom'] ) &&
		$option4['sfsi_plus_gab_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_gab_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['gab_count'] = $option4['sfsi_plus_gab_manualCounts'];
			} else {
				$scounts['gab_count'] = $option4['sfsi_plus_gab_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['gab_count'] = '20';
			} else {
				$scounts['gab_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_gab_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['gab_count'] = '20';
		} else {
			$scounts['gab_count'] = '20';
		}
	}

	/* get mix count */
	if (
		isset( $option4['sfsi_plus_mix_countsFrom'] ) &&
		$option4['sfsi_plus_mix_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_mix_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['mix_count'] = $option4['sfsi_plus_mix_manualCounts'];
			} else {
				$scounts['mix_count'] = $option4['sfsi_plus_mix_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['mix_count'] = '20';
			} else {
				$scounts['mix_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_mix_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['mix_count'] = '20';
		} else {
			$scounts['mix_count'] = '20';
		}
	}
	/* get ok count */
	if (
		isset( $option4['sfsi_plus_ok_countsFrom'] ) &&
		$option4['sfsi_plus_ok_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_ok_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['ok_count'] = $option4['sfsi_plus_ok_manualCounts'];
			} else {
				$scounts['ok_count'] = $option4['sfsi_plus_ok_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['ok_count'] = '20';
			} else {
				$scounts['ok_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_ok_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['ok_count'] = '20';
		} else {
			$scounts['ok_count'] = '20';
		}
	}

	/* get vk count */
	if (
		isset( $option4['sfsi_plus_vk_countsFrom'] ) &&
		$option4['sfsi_plus_vk_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_vk_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['vk_count'] = $option4['sfsi_plus_vk_manualCounts'];
			} else {
				$scounts['vk_count'] = $option4['sfsi_plus_vk_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['vk_count'] = '20';
			} else {
				$scounts['vk_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_vk_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['vk_count'] = '20';
		} else {
			$scounts['vk_count'] = '20';
		}
	}

	/* get telegram count */
	if (
		isset( $option4['sfsi_plus_telegram_countsFrom'] ) &&
		$option4['sfsi_plus_telegram_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_telegram_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['telegram_count'] = $option4['sfsi_plus_telegram_manualCounts'];
			} else {
				$scounts['telegram_count'] = $option4['sfsi_plus_telegram_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['telegram_count'] = '20';
			} else {
				$scounts['telegram_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_telegram_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['telegram_count'] = '20';
		} else {
			$scounts['telegram_count'] = '20';
		}
	}

	/* get weibo count */
	if (
		isset( $option4['sfsi_plus_weibo_countsFrom'] ) &&
		$option4['sfsi_plus_weibo_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_weibo_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['weibo_count'] = $option4['sfsi_plus_weibo_manualCounts'];
			} else {
				$scounts['weibo'] = $option4['sfsi_plus_weibo_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['weibo_count'] = '20';
			} else {
				$scounts['weibo_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_weibo_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['weibo_count'] = '20';
		} else {
			$scounts['weibo_count'] = '20';
		}
	}

	/* get xing count */
	if (
		isset( $option4['sfsi_plus_xing_countsFrom'] ) &&
		$option4['sfsi_plus_xing_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_xing_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['xing_count'] = $option4['sfsi_plus_xing_manualCounts'];
			} else {
				$scounts['xing_count'] = $option4['sfsi_plus_xing_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['xing_count'] = '20';
			} else {
				$scounts['xing_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_xing_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['xing_count'] = '20';
		} else {
			$scounts['xing_count'] = '20';
		}
	}

	/* get wechat count */
	if (
		isset( $option4['sfsi_plus_wechat_countsFrom'] ) &&
		$option4['sfsi_plus_wechat_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_wechat_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['wechat_count'] = $option4['sfsi_plus_wechat_manualCounts'];
			} else {
				$scounts['wechat_count'] = $option4['sfsi_plus_wechat_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['wechat_count'] = '20';
			} else {
				$scounts['wechat_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_wechat_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['wechat_count'] = '20';
		} else {
			$scounts['wechat_count'] = '20';
		}
	}
	/* get copylink count */
	if (
		isset( $option4['sfsi_plus_copylink_countsFrom'] ) &&
		$option4['sfsi_plus_copylink_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_copylink_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['copylink_count'] = $option4['sfsi_plus_copylink_manualCounts'];
			} else {
				$scounts['copylink_count'] = $option4['sfsi_plus_copylink_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['copylink_count'] = '20';
			} else {
				$scounts['copylink_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_copylink_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['copylink_count'] = '20';
		} else {
			$scounts['copylink_count'] = '20';
		}
	}

	/* get mastodon count */
	if (
		isset( $option4['sfsi_plus_mastodon_countsFrom'] ) &&
		$option4['sfsi_plus_mastodon_countsFrom'] == "manual"
	) {
		if (
			isset( $option4['sfsi_plus_mastodon_manualCounts'] )
		) {
			if ( $formated ) {
				$scounts['mastodon_count'] = $option4['sfsi_plus_mastodon_manualCounts'];
			} else {
				$scounts['mastodon_count'] = $option4['sfsi_plus_mastodon_manualCounts'];
			}
		} else {
			if ( $formated ) {
				$scounts['mastodon_count'] = '20';
			} else {
				$scounts['mastodon_count'] = '20';
			}
		}
	} elseif ( ! isset( $option4['sfsi_plus_mastodon_countsFrom'] ) ) {
		if ( $formated ) {
			$scounts['mastodon_count'] = '20';
		} else {
			$scounts['mastodon_count'] = '20';
		}
	}

	if (
		isset( $option4['sfsi_plus_phone_manualCounts'] )
	) {
		$scounts['phone_count'] = $option4['sfsi_plus_phone_manualCounts'];
	} else {
		$scounts['phone_count'] = '20';
	}

	return $scounts;
	exit;
}

/* activate and remove footer credit link */
add_action( 'wp_ajax_plus_activateFooter', 'sfsiplusActivateFooter' );
function sfsiplusActivateFooter() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "active_plusfooter" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	update_option( 'sfsi_premium_footer_sec', 'yes' );
	echo json_encode( array( 'res' => 'success' ) );
	exit;
}

add_action( 'wp_ajax_plus_removeFooter', 'sfsiplusremoveFooter' );
function sfsiplusremoveFooter() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "remove_plusfooter" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	update_option( 'sfsi_premium_footer_sec', 'no' );
	echo json_encode( array( 'res' => 'success' ) );
	exit;
}

add_action( 'wp_ajax_getIconPreview', 'sfsiPlusGetIconPreview' );
function sfsiPlusGetIconPreview() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "getIconPreview" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$iconname  = esc_url( $_POST['iconname'] );
	$iconValue = sanitize_text_field( $_POST['iconValue'] );
	echo '<img src="' . $iconname . "/icon_" . $iconValue . '.png" alt="' . $iconname . '" >';
	die;
}

function sfsiPlusGetForm() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "getForm" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$heading     = isset( $_POST['heading'] ) ? sanitize_text_field( $_POST['heading'] ) : '';
	$placeholder = isset( $_POST['placeholder'] ) ? sanitize_text_field( $_POST['placeholder'] ) : '';
	$button      = isset( $_POST['button'] ) ? sanitize_text_field( $_POST['button'] ) : '';
	// extract($_POST);
	?>
    <xmp>
        <div class="sfsi_subscribe_Popinner">
            <form method="post">
                <h5><?php echo $heading; ?></h5>
                <div class="sfsi_subscription_form_field">
                    <input type="email" name="subscribe_email" placeholder="<?php echo $placeholder; ?>" value=""/>
                </div>
                <div class="sfsi_subscription_form_field">
                    <input type="submit" name="subscribe" value="<?php echo $button; ?>"/>
                </div>
            </form>
        </div>
    </xmp>
	<?php
	die;
}

function sfsi_plus_sanitize_field( $value ) {
	return strip_tags( trim( $value ) );
}

//Sanitize color code
if ( ! function_exists( "sfsi_plus_sanitize_hex_color" ) ) {
	function sfsi_plus_sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';
		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;
		}
	}
}

add_action( 'wp_ajax_plus_update_disable_usm_ogtags_updater5', 'sfsi_plus_update_disable_usm_ogtags_updater5' );
function sfsi_plus_update_disable_usm_ogtags_updater5() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "plus_update_disable_usm_ogtags_updater5" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	if ( isset( $_POST['sfsi_plus_disable_usm_og_meta_tags'] ) ) {
		$option5                                       = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
		$option5['sfsi_plus_disable_usm_og_meta_tags'] = $_POST['sfsi_plus_disable_usm_og_meta_tags'];
		update_option( 'sfsi_premium_section5_options', serialize( $option5 ) );
		echo _e( 'Settings updated', 'ultimate-social-media-plus' );
	} else {
		echo _e( 'Failed to update settings', 'ultimate-social-media-plus' );
	}
	wp_die();
}

//registering api route for 4.7+ 4.4+ with wp-api plugin
function sfsi_premium_register_hover_icon_settings_route() {
	register_rest_route( 'usm-premium-icons/v1', 'hover_icon_setting', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'sfsi_premium_hover_icon_settings',
		'permission_callback' => '__return_true',
		'args'                => array(
			"share_url" => array(
				"type"              => 'string',
				"sanitize_callback" => 'sanitize_text_field'
			)
		)
	) );
}

add_action( 'rest_api_init', 'sfsi_premium_register_hover_icon_settings_route' );

//registering ajax call for fallback
add_action( 'wp_ajax_nopriv_premium_hover_icon_settings', 'sfsi_premium_hover_settings_echoed' );
add_action( 'wp_ajax_premium_hover_icon_settings', 'sfsi_premium_hover_settings_echoed' );
function sfsi_plus_get_hover_icon_image( $icon ) {
	switch ( $icon ) {
		case 'pinterest':
			return SFSI_PLUS_PLUGURL . "images/pinterest-on-hover.png";
			break;
		default:
			return SFSI_PLUS_PLUGURL . "images/pinterest-on-hover.png";
			break;
	}
}

add_action( 'wp_ajax_nopriv_premium_responsive_icon_settings', 'sfsi_premium_responsive_settings_echoed' );
add_action( 'wp_ajax_premium_responsive_icon_settings', 'sfsi_premium_responsive_settings_echoed' );
function sfsi_premium_responsive_settings_echoed() {
	$option8               = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );
	$return_data           = array();
	$return_data["device"] = array(
		"desktop" => $option8["sfsi_plus_responsive_icons_show_on_desktop"],
		"mobile"  => $option8["sfsi_plus_responsive_icons_show_on_mobile"]
	);

	return rest_ensure_response( $return_data );
}

function sfsi_premium_hover_icon_settings() {
	if ( isset( $_REQUEST['url'] ) ) {
		$url = $_REQUEST['url'];
	} else {
		$url = home_url();
	}
	$is_archive = null;
	if ( isset( $_REQUEST['is_archive'] ) ) {
		if ( 'yes' === $_REQUEST['is_archive'] ) {
			$is_archive = true;
		} else {
			$is_archive = false;
		}
	}
	$is_date = null;
	if ( isset( $_REQUEST['is_date'] ) ) {
		if ( 'yes' === $_REQUEST['is_date'] ) {
			$is_date = true;
		} else {
			$is_date = false;
		}
	}
	$is_author = null;
	if ( isset( $_REQUEST['is_author'] ) ) {
		if ( 'yes' === $_REQUEST['is_author'] ) {
			$is_author = true;
		} else {
			$is_author = false;
		}
	}
	$option8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );
	$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );

	$save_btn_txt = array(
		'af'    => 'red',
		'ar'    => 'حفظ',
		'az'    => 'yadda saxla',
		'be'    => 'эканоміць',
		'bg'    => 'спасяване',
		'bn'    => 'রক্ষা',
		'bs'    => 'štedi',
		'ca'    => 'guardar',
		'ceb'   => 'pagluwas',
		'cs'    => 'Uložit',
		'cy'    => 'arbed',
		'da'    => 'Gemme',
		'de'    => 'sparen',
		'el'    => 'αποθηκεύσετε',
		'en'    => 'save',
		'eo'    => 'save',
		'es'    => 'salvar',
		'et'    => 'salvestage',
		'eu'    => 'gorde',
		'fa'    => 'صرفه جویی',
		'fi'    => 'Tallentaa',
		'fr'    => 'enregistrer',
		'ga'    => 'sábháil',
		'gl'    => 'gardar',
		'gu'    => 'સાચવો',
		'ha'    => 'ajiye',
		'hi'    => 'सेव करें',
		'hmn'   => 'save',
		'hr'    => 'uštedjeti',
		'ht'    => 'sove',
		'hu'    => 'mentés',
		'hy'    => 'փրկեք',
		'id'    => 'menyimpan',
		'ig'    => 'zọpụta',
		'is'    => 'vista',
		'it'    => 'salvare',
		'iw'    => 'לשמור',
		'ja'    => '保存する',
		'jw'    => 'simpen',
		'ka'    => 'შენახვა',
		'kk'    => 'сақтау',
		'km'    => 'រក្សាទុក',
		'kn'    => 'ಉಳಿಸು',
		'ko'    => '구하다',
		'la'    => 'save',
		'lo'    => 'save',
		'lt'    => 'sutaupyti',
		'lv'    => 'ietaupīt',
		'mg'    => 'afa-tsy',
		'mi'    => 'tiaki',
		'mk'    => 'спаси',
		'ml'    => 'രക്ഷിക്കും',
		'mn'    => 'аврах',
		'mr'    => 'जतन करा',
		'ms'    => 'simpan',
		'mt'    => 'ħlief',
		'my'    => 'ကယ်ဆယ်',
		'ne'    => 'बचत गर्नुहोस्',
		'nl'    => 'opslaan',
		'no'    => 'lagre',
		'ny'    => 'sungani',
		'pa'    => 'ਬਚਾਓ',
		'pl'    => 'zapisać',
		'pt'    => 'Salve ',
		'ro'    => 'Salvați',
		'ru'    => 'спасти',
		'si'    => 'ඉතිරිකර ගන්න',
		'sk'    => 'uložiť',
		'sl'    => 'shranite',
		'so'    => 'badbaadi',
		'sq'    => 'ruaj',
		'sr'    => 'сачувати',
		'st'    => 'pholosa',
		'su'    => 'nyalametkeun',
		'sv'    => 'spara',
		'sw'    => 'salama',
		'ta'    => 'காப்பாற்ற',
		'te'    => 'సేవ్',
		'tg'    => 'захира кунед',
		'th'    => 'ประหยัด',
		'tl'    => 'i-save',
		'tr'    => 'kayıt etmek',
		'uk'    => 'зберегти',
		'ur'    => 'محفوظ کریں',
		'uz'    => 'saqlash',
		'vi'    => 'tiết kiệm',
		'yi'    => 'ראַטעווען',
		'yo'    => 'fi',
		'zh'    => '保存',
		'zh-CN' => '保存',
		'zh-TW' => '保存',
		'zu'    => 'londoloza',
	);

	$return_data = array();
	if ( ! isset( $option8['sfsi_plus_icon_hover_show_pinterest'] ) || ( is_null( $option8['sfsi_plus_icon_hover_show_pinterest'] ) ) || 'no' === $option8['sfsi_plus_icon_hover_show_pinterest'] || ! sfsi_plus_onhover_shall_show_icons( $url, $is_archive, $is_date, $is_author, $option8 ) ) {
		$return_data['icons'] = array();
	} else {
		$return_data['show_on'] = array();
		if ( isset( $option8['sfsi_plus_icon_hover_desktop'] ) && $option8['sfsi_plus_icon_hover_desktop'] === 'yes' ) {
			array_push( $return_data['show_on'], 'desktop' );
		}
		if ( isset( $option8['sfsi_plus_icon_hover_mobile'] ) && $option8['sfsi_plus_icon_hover_mobile'] === 'yes' ) {
			array_push( $return_data['show_on'], 'mobile' );
		}
		if ( isset( $option8['sfsi_plus_icon_hover_type'] ) ) {
			$return_data['icon_type'] = $option8['sfsi_plus_icon_hover_type'];
		}
		if ( isset( $option8['sfsi_plus_icon_hover_width'] ) ) {
			$return_data['width'] = ( trim( $option8['sfsi_plus_icon_hover_width'] === '' ) ? 0 : $option8['sfsi_plus_icon_hover_width'] ) . 'px';
		}
		if ( isset( $option8['sfsi_plus_icon_hover_height'] ) ) {
			$return_data['height'] = ( trim( $option8['sfsi_plus_icon_hover_height'] ) === "" ? 0 : $option8['sfsi_plus_icon_hover_height'] ) . 'px';
		}
		if ( isset( $option8['sfsi_plus_icon_hover_placement'] ) ) {
			$return_data['placement'] = $option8['sfsi_plus_icon_hover_placement'];
		}
		if ( isset( $option5['sfsi_premium_pinterest_placements'] ) && $option5['sfsi_premium_pinterest_placements'] === 'yes' ) {
			$return_data['type'] = 'absolute';
		} else {
			$return_data['type'] = 'regular';
		}
		if ( isset( $option5['sfsi_plus_icons_ClickPageOpen'] ) ) {
			$return_data['page'] = $option5['sfsi_plus_icons_ClickPageOpen'];
		}
		$return_data['icon']        = array();
		$sfsi_socialhandler         = new sfsi_plus_SocialHelper();
		$description                = $sfsi_socialhandler->sfsi_pinit_description( null, $url );
		$return_data['description'] = $description;
		$pinterest_icon             = array(
			'name'               => __( 'Pinterest', 'ultimate-social-media-plus' ),
			'icon_title'         => __( 'PINTEREST', 'ultimate-social-media-plus' ),
			'share_url_template' => 'https://www.pinterest.com/pin/create/button/?url=',
		);

		if ( $option8['sfsi_plus_icon_hover_type'] === "square" ) {
			if ( isset( $option5['sfsi_premium_pinterest_placements'] ) && $option5['sfsi_premium_pinterest_placements'] === 'yes' ) {
				$icon = '<img data-pin-nopin="true" src="' . sfsi_plus_get_hover_icon_image( 'pinterest' ) . '" title="' . $pinterest_icon['icon_title'] . '" alt="' . $pinterest_icon['icon_title'] . '" style="width:40px;height:40px"/>';
			} else {
				$icon = '<img data-pin-nopin="true" src="' . sfsi_plus_get_hover_icon_image( 'pinterest' ) . '" title="' . $pinterest_icon['icon_title'] . '" alt="' . $pinterest_icon['icon_title'] . '" class="sfsi_premium_hover_pinterest_icon" style="width:40px;height:40px" />';
			}
		} else if ( $option8['sfsi_plus_icon_hover_type'] === "custom" && $option8["sfsi_plus_icon_hover_custom_icon_url"] !== "" ) {
			$icon = '<img data-pin-nopin="true" src="' . $option8["sfsi_plus_icon_hover_custom_icon_url"] . '" title="' . $pinterest_icon['icon_title'] . '" alt="' . $pinterest_icon['icon_title'] . '" class="sfsi_premium_hover_pinterest_icon" style="width:40px;height:40px" />';
		} else {
			$large = $option8['sfsi_plus_icon_hover_type'] === "large-rectangle";
			$lang  = $save_btn_txt['en'];
			if ( isset( $option8['sfsi_plus_icon_hover_language'] ) ) {
				$lang_name_1     = $option8['sfsi_plus_icon_hover_language'];
				$lang_name_2_arr = explode( '_', $lang_name_1 );
				if ( isset( $lang_name_2_arr ) && count( $lang_name_2_arr ) ) {
					$lang_name_2 = $lang_name_2_arr[0];
				} else {
					$lang_name_2 = "";
				}
				if ( isset( $save_btn_txt[ $lang_name_1 ] ) ) {
					$lang = $save_btn_txt[ $lang_name_1 ];
				} elseif ( isset( $save_btn_txt[ $lang_name_2 ] ) ) {
					$lang = $save_btn_txt[ $lang_name_2 ];
				}
			}
			// $lang='en';
			$icon = '<span style="border-radius:2px;text-indent:' . ( $large ? 29 : 20 ) . 'px;width:auto;padding:0 6px 0 0; text-align:center;text-decoration:none;font:' . ( $large ? '18px/28px' : '11px/20px' ) . ' \'Helvetica Neue\', Helvetica , sans-serif;font-weight:bold;color:#fff!important;background:#bd081c url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iMzBweCIgd2lkdGg9IjMwcHgiIHZpZXdCb3g9Ii0xIC0xIDMxIDMxIj48Zz48cGF0aCBkPSJNMjkuNDQ5LDE0LjY2MiBDMjkuNDQ5LDIyLjcyMiAyMi44NjgsMjkuMjU2IDE0Ljc1LDI5LjI1NiBDNi42MzIsMjkuMjU2IDAuMDUxLDIyLjcyMiAwLjA1MSwxNC42NjIgQzAuMDUxLDYuNjAxIDYuNjMyLDAuMDY3IDE0Ljc1LDAuMDY3IEMyMi44NjgsMC4wNjcgMjkuNDQ5LDYuNjAxIDI5LjQ0OSwxNC42NjIiIGZpbGw9IiNmZmYiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLXdpZHRoPSIxIj48L3BhdGg+PHBhdGggZD0iTTE0LjczMywxLjY4NiBDNy41MTYsMS42ODYgMS42NjUsNy40OTUgMS42NjUsMTQuNjYyIEMxLjY2NSwyMC4xNTkgNS4xMDksMjQuODU0IDkuOTcsMjYuNzQ0IEM5Ljg1NiwyNS43MTggOS43NTMsMjQuMTQzIDEwLjAxNiwyMy4wMjIgQzEwLjI1MywyMi4wMSAxMS41NDgsMTYuNTcyIDExLjU0OCwxNi41NzIgQzExLjU0OCwxNi41NzIgMTEuMTU3LDE1Ljc5NSAxMS4xNTcsMTQuNjQ2IEMxMS4xNTcsMTIuODQyIDEyLjIxMSwxMS40OTUgMTMuNTIyLDExLjQ5NSBDMTQuNjM3LDExLjQ5NSAxNS4xNzUsMTIuMzI2IDE1LjE3NSwxMy4zMjMgQzE1LjE3NSwxNC40MzYgMTQuNDYyLDE2LjEgMTQuMDkzLDE3LjY0MyBDMTMuNzg1LDE4LjkzNSAxNC43NDUsMTkuOTg4IDE2LjAyOCwxOS45ODggQzE4LjM1MSwxOS45ODggMjAuMTM2LDE3LjU1NiAyMC4xMzYsMTQuMDQ2IEMyMC4xMzYsMTAuOTM5IDE3Ljg4OCw4Ljc2NyAxNC42NzgsOC43NjcgQzEwLjk1OSw4Ljc2NyA4Ljc3NywxMS41MzYgOC43NzcsMTQuMzk4IEM4Ljc3NywxNS41MTMgOS4yMSwxNi43MDkgOS43NDksMTcuMzU5IEM5Ljg1NiwxNy40ODggOS44NzIsMTcuNiA5Ljg0LDE3LjczMSBDOS43NDEsMTguMTQxIDkuNTIsMTkuMDIzIDkuNDc3LDE5LjIwMyBDOS40MiwxOS40NCA5LjI4OCwxOS40OTEgOS4wNCwxOS4zNzYgQzcuNDA4LDE4LjYyMiA2LjM4NywxNi4yNTIgNi4zODcsMTQuMzQ5IEM2LjM4NywxMC4yNTYgOS4zODMsNi40OTcgMTUuMDIyLDYuNDk3IEMxOS41NTUsNi40OTcgMjMuMDc4LDkuNzA1IDIzLjA3OCwxMy45OTEgQzIzLjA3OCwxOC40NjMgMjAuMjM5LDIyLjA2MiAxNi4yOTcsMjIuMDYyIEMxNC45NzMsMjIuMDYyIDEzLjcyOCwyMS4zNzkgMTMuMzAyLDIwLjU3MiBDMTMuMzAyLDIwLjU3MiAxMi42NDcsMjMuMDUgMTIuNDg4LDIzLjY1NyBDMTIuMTkzLDI0Ljc4NCAxMS4zOTYsMjYuMTk2IDEwLjg2MywyNy4wNTggQzEyLjA4NiwyNy40MzQgMTMuMzg2LDI3LjYzNyAxNC43MzMsMjcuNjM3IEMyMS45NSwyNy42MzcgMjcuODAxLDIxLjgyOCAyNy44MDEsMTQuNjYyIEMyNy44MDEsNy40OTUgMjEuOTUsMS42ODYgMTQuNzMzLDEuNjg2IiBmaWxsPSIjYmQwODFjIj48L3BhdGg+PC9nPjwvc3ZnPg==) 3px 50% no-repeat;background-size:' . ( $large ? '18px 18px' : '14px 14px' ) . '; cursor:pointer;display:inline-block;box-sizing:border-box;height:' . ( $large ? 28 : 20 ) . 'px;" >' . $lang . '</span>';
		}
		$pinterest_icon['icon'] = $icon;

		array_push( $return_data['icon'], $pinterest_icon );
	}

	return rest_ensure_response( $return_data );
}

function sfsi_premium_hover_settings_echoed() {
	echo json_encode( json_decode( json_encode( sfsi_premium_hover_icon_settings() ) )->data );
	wp_die();
}

add_action( 'wp_ajax_sfsi_premium_get_feed_id', 'sfsi_premium_get_feed_id' );
function sfsi_premium_get_feed_id() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "sfsi_premium_get_feed_id" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array(
			'res'     => 'Failed',
			'message' => __( 'You should be admin to take this action', 'ultimate-social-media-plus' )
		) );
		exit;
	}
	$feed_id = sanitize_text_field( get_option( 'sfsi_premium_feed_id' ) );
	if ( "" == $feed_id ) {
		$sfsiId = SFSI_PLUS_getFeedUrl();
		update_option( 'sfsi_premium_feed_id', sanitize_text_field( $sfsiId->feed_id ) );
		update_option( 'sfsi_premium_redirect_url', sanitize_text_field( $sfsiId->redirect_url ) );
		echo json_encode( array( "res" => "success", 'feed_id' => $sfsiId->feed_id ) );
		sfsi_plus_getverification_code();
	} else {
		echo json_encode( array( "res" => "success", "feed_id" => $feed_id ) );
		exit;
	}
	wp_die();
}

/* save settings for save export */
add_action( 'wp_ajax_save_export', 'sfsi_premium_save_export' );
function sfsi_premium_save_export() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "sfsi_premium_save_export" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array(
			'res'     => 'Failed',
			'message' => __( 'You should be admin to take this action', 'ultimate-social-media-plus' )
		) );
		exit;
	}
	$option1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );
	$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );
	$option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
	foreach ( $option3 as $key => $value ) {
		if ( $key == "sfsi_plus_mouseOver_other_icon_images" ) {
			if ( is_string( $value ) ) {
				$mouseOver_other_icon_images = maybe_unserialize( $value );
			} elseif ( is_array( $value ) ) {
				$mouseOver_other_icon_images = $value;
			}
			$option3[ $key ] = $mouseOver_other_icon_images;
		}
	}
	$option4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
	$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );

	foreach ( $option5 as $key => $value ) {
		if ( $key == "sfsi_order_icons_desktop" ) {
			if ( is_string( $value ) ) {
				$desktop_icons = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$desktop_icons = $value;
			}
			$option5[ $key ] = $desktop_icons;
		}

		if ( $key == "sfsi_order_icons_mobile" ) {
			if ( is_string( $value ) ) {
				$mobile_icons = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$mobile_icons = $value;
			}
			$option5[ $key ] = $mobile_icons;
		}

		if ( $key == "sfsi_custom_social_data_post_types_data" ) {
			if ( is_string( $value ) ) {
				$custom_social_data_post_types_data = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$custom_social_data_post_types_data = $value;
			}
			$option5[ $key ] = $custom_social_data_post_types_data;
		}

		if ( $key == "sfsi_premium_url_shortner_icons_names_list" ) {
			if ( is_string( $value ) ) {
				$premium_url_shortner_icons_names_list = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$premium_url_shortner_icons_names_list = $value;
			}
			$option5[ $key ] = $premium_url_shortner_icons_names_list;
		}

		if ( $key == "sfsi_plus_custom_css" ) {
			if ( is_string( $value ) ) {
				$custom_css = ( $value );
			} else if ( is_array( $value ) ) {
				$custom_css = $value;
			}
			$option5[ $key ] = $custom_css;
		}

		if ( $key == "sfsi_plus_custom_admin_css" ) {
			if ( is_string( $value ) ) {
				$custom_admin_css = ( $value );
			} else if ( is_array( $value ) ) {
				$custom_admin_css = $value;
			}
			$option5[ $key ] = $custom_admin_css;
		}
	}
	$option6 = maybe_unserialize( get_option( 'sfsi_premium_section6_options', false ) );
	$option7 = maybe_unserialize( get_option( 'sfsi_premium_section7_options', false ) );
	$option8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );
	foreach ( $option8 as $key => $value ) {
		if ( $key == "sfsi_plus_choose_post_types" ) {
			if ( is_string( $value ) ) {
				$choose_post_types = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$choose_post_types = $value;
			}
			$option8[ $key ] = $choose_post_types;
		}

		if ( $key == "sfsi_plus_choose_post_types_responsive" ) {
			if ( is_string( $value ) ) {
				$choose_post_types_responsive = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$choose_post_types_responsive = $value;
			}
			$option8[ $key ] = $choose_post_types_responsive;
		}

		if ( $key == "sfsi_plus_taxonomies_for_icons" ) {
			if ( is_string( $value ) ) {
				$taxonomies_for_icons = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$taxonomies_for_icons = $value;
			}
			$option8[ $key ] = $taxonomies_for_icons;
		}

		if ( $key == "sfsi_plus_list_exclude_custom_post_types" ) {
			if ( is_string( $value ) ) {
				$exclude_custom_post_types = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$exclude_custom_post_types = $value;
			}
			$option8[ $key ] = $exclude_custom_post_types;
		}

		if ( $key == "sfsi_plus_list_exclude_taxonomies" ) {
			if ( is_string( $value ) ) {
				$exclude_taxonomies = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$exclude_taxonomies = $value;
			}
			$option8[ $key ] = $exclude_taxonomies;
		}


		if ( $key == "sfsi_plus_list_include_custom_post_types" ) {
			if ( is_string( $value ) ) {
				$include_custom_post_types = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$include_custom_post_types = $value;
			}
			$option8[ $key ] = $include_custom_post_types;
		}

		if ( $key == "sfsi_plus_list_include_custom_post_types" ) {
			if ( is_string( $value ) ) {
				$include_custom_post_types = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$include_custom_post_types = $value;
			}
			$option8[ $key ] = $include_custom_post_types;
		}

		if ( $key == "sfsi_plus_icon_hover_list_exclude_custom_post_types" ) {
			if ( is_string( $value ) ) {
				$hover_list_exclude_custom_post_types = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$hover_list_exclude_custom_post_types = $value;
			}
			$option8[ $key ] = $hover_list_exclude_custom_post_types;
		}

		if ( $key == "sfsi_plus_icon_hover_list_exclude_taxonomies" ) {
			if ( is_string( $value ) ) {
				$hover_list_exclude_taxonomies = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$hover_list_exclude_taxonomies = $value;
			}
			$option8[ $key ] = $hover_list_exclude_taxonomies;
		}

		if ( $key == "sfsi_plus_icon_hover_list_include_custom_post_types" ) {
			if ( is_string( $value ) ) {
				$hover_list_include_custom_post_types = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$hover_list_include_custom_post_types = $value;
			}
			$option8[ $key ] = $hover_list_include_custom_post_types;
		}

		if ( $key == "sfsi_plus_icon_hover_list_include_taxonomies" ) {
			if ( is_string( $value ) ) {
				$hover_list_include_taxonomies = maybe_unserialize( $value );
			} else if ( is_array( $value ) ) {
				$hover_list_include_taxonomies = $value;
			}
			$option8[ $key ] = $hover_list_include_taxonomies;
		}
	}
	$option9 = maybe_unserialize( get_option( 'sfsi_premium_section9_options', false ) );

	foreach ( $option9 as $kay => $value ) {
		if ( $key == "sfsi_plus_icon_hover_list_include_custom_post_types" ) {
			if ( is_string( $value ) ) {
				$hover_list_include_custom_post_types = maybe_unserialize( $value );
			} elseif ( is_array( $value ) ) {
				$hover_list_include_custom_post_types = $value;
			}
			$option9[ $key ] = $hover_list_include_custom_post_types;
		}

		if ( $key == "sfsi_plus_icon_hover_list_include_taxonomies" ) {
			if ( is_string( $value ) ) {
				$hover_list_include_taxonomies = maybe_unserialize( $value );
			} elseif ( is_array( $value ) ) {
				$hover_list_include_taxonomies = $value;
			}
			$option9[ $key ] = $hover_list_include_taxonomies;
		}

		if ( $key == "sfsi_plus_responsive_icons" ) {
			if ( is_string( $value ) ) {
				$responsive_icons = maybe_unserialize( $value );
			} elseif ( is_array( $value ) ) {
				$responsive_icons = $value;
			}
			$option9[ $key ] = $responsive_icons;
		}
	}
	$sfsi_premium_serverphpVersionnotification = get_option( "sfsi_premium_serverphpVersionnotification" );
	$sfsi_premium_installDate                  = get_option( "sfsi_premium_installDate" ) ? get_option( "sfsi_premium_installDate" ) : date( 'Y-m-d h:i:s' );
	$sfsi_premium_RatingDiv                    = get_option( "sfsi_premium_RatingDiv" ) ? get_option( "sfsi_premium_RatingDiv" ) : 'no';
	$sfsi_premium_footer_sec                   = get_option( "sfsi_premium_footer_sec" ) ? get_option( "sfsi_premium_footer_sec" ) : 'no';

	/* size and spacing of icons */
	$save_export_options = array(
		'option1'                                   => $option1,
		'option2'                                   => $option2,
		'option3'                                   => $option3,
		'option4'                                   => $option4,
		'option5'                                   => $option5,
		'option6'                                   => $option6,
		'option7'                                   => $option7,
		'option8'                                   => $option8,
		'option9'                                   => $option9,
		'sfsi_premium_serverphpVersionnotification' => $sfsi_premium_serverphpVersionnotification,
		'sfsi_premium_installDate'                  => $sfsi_premium_installDate,
		'sfsi_premium_RatingDiv'                    => $sfsi_premium_RatingDiv,
		'sfsi_premium_footer_sec'                   => $sfsi_premium_footer_sec,
	);
	$json                = json_encode( $save_export_options );
	header( 'Content-disposition: attachment; filename=file.json' );
	header( 'Content-type: application/json' );
	echo $json;
	exit;
}

add_action( 'wp_ajax_recheck_license', 'sfsi_premium_recheck_license' );
function sfsi_premium_recheck_license() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "sfsi_premium_recheck_license" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array(
			'res'     => 'Failed',
			'message' => __( 'You should be admin to take this action', 'ultimate-social-media-plus' )
		) );
		exit;
	}
	$license_api_name = get_sfsi_active_license_api_name();

	if ( $license_api_name == 'ultimate' ) {
		// var_dump($license_api_name);
		$license = get_option( $license_api_name . '_license_key' );

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( ULTIMATELYSOCIAL_PRODUCT ), // the name of our product in EDD
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( ULTIMATELYSOCIAL_API_URL, array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params
		) );
		if ( ! is_wp_error( $response ) || 200 === wp_remote_retrieve_response_code( $response ) ) {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( false !== $license_data->success ) {
				update_option( 'ultimate_license_expiry', $license_data->expires );
				echo json_encode( array( "status" => true ) );
				die();
			}
		}
		echo json_encode( array( "status" => false ) );
		die();
	} elseif ( $license_api_name == 'sellcodes_usm' ) {
		$license_key = get_option( $license_api_name . '_license_key' );
		$api_params  = array(
			'product_id'  => SELLCODES_PRODUCT,
			'license_key' => $license_key,
			'baseurl'     => SITEURL
		);
		$response    = wp_remote_post( SELLCODES_API_URL . "/check_license", array(
			'timeout'   => 30,
			'sslverify' => false,
			'body'      => $api_params
		) );
		if ( ! is_wp_error( $response ) || 200 === wp_remote_retrieve_response_code( $response ) ) {
			$license_data = json_decode( str_replace( "\xEF\xBB\xBF", '', wp_remote_retrieve_body( $response ) ) );
			if ( isset( $license_data ) && isset( $license_data->expires ) && isset( $license_data->success ) ) {
				if ( false !== $license_data->success ) {
					update_option( $license_api_name . '_license_expiry', $license_data->expires );
					update_option( $license_api_name . '_license_status', 'valid' );
					echo json_encode( array( "status" => true ) );
					die();
				} else {
					echo json_encode( array( "status" => false ) );
					die();
				}
			} else {
				echo json_encode( array( "status" => false ) );
				die();
			}
		} else {
			echo json_encode( array( "status" => false ) );
			die();
		}
	}
}

function set_premium_key( $option ) {
	$key    = array_keys( $option );
	$key    = str_replace( 'sfsi_', 'sfsi_plus_', $key );
	$option = array_combine( $key, $option );

	return $option;
}

add_action( 'wp_ajax_file_input', 'sfsi_premium_file_input' );
function sfsi_premium_file_input() {
	if ( isset( $_POST['import_type'] ) ) {
		$current_option1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options' ) );
		$current_option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options' ) );
		$current_option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options' ) );
		$current_option4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options' ) );
		$current_option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options' ) );
		$current_option6 = maybe_unserialize( get_option( 'sfsi_premium_section6_options' ) );
		$current_option7 = maybe_unserialize( get_option( 'sfsi_premium_section7_options' ) );
		$current_option8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options' ) );
		$current_option9 = maybe_unserialize( get_option( 'sfsi_premium_section9_options' ) );

		$imported_option1 = $imported_option2 = $imported_option3 = $imported_option4 = $imported_option5 = $imported_option6 = $imported_option7 = $imported_option8 = $imported_option9 = array();

		if ( $_POST['imports']['option1'] ) {
			$imported_option1 = $_POST['imports']['option1'];
		}

		if ( $_POST['imports']['option2'] ) {
			$imported_option2 = $_POST['imports']['option2'];
		}

		if ( $_POST['imports']['option3'] ) {
			$imported_option3 = $_POST['imports']['option3'];
		}

		if ( $_POST['imports']['option4'] ) {
			$imported_option4 = $_POST['imports']['option4'];
		}

		if ( $_POST['imports']['option5'] ) {
			$imported_option5 = $_POST['imports']['option5'];
		}

		if ( $_POST['imports']['option6'] ) {
			$imported_option6 = $_POST['imports']['option6'];
		}

		if ( $_POST['imports']['option7'] ) {
			$imported_option7 = $_POST['imports']['option7'];
		}

		if ( $_POST['imports']['option8'] ) {
			$imported_option8 = $_POST['imports']['option8'];
		}

		if ( $_POST['imports']['option9'] ) {
			$imported_option9 = $_POST['imports']['option9'];
		}

		if ( $_POST['import_type'] == "plus" ) {
			if ( $imported_option1["sfsi_plus_telegram_display"] == "yes" ) {
				$imported_option2["sfsi_plus_telegramMessage_option"] = "yes";
			}

			update_option( 'sfsi_premium_section1_options', serialize( array_merge( $current_option1, $imported_option1 ) ) );
			update_option( 'sfsi_premium_section2_options', serialize( array_merge( $current_option2, $imported_option2 ) ) );
			update_option( 'sfsi_premium_section3_options', serialize( array_merge( $current_option3, $imported_option3 ) ) );
			update_option( 'sfsi_premium_section4_options', serialize( array_merge( $current_option4, $imported_option4 ) ) );

			$imported_option5['sfsi_plus_icons_suppress_errors'] = $imported_option5['sfsi_pplus_icons_suppress_errors'];
			unset( $imported_option5['sfsi_pplus_icons_suppress_errors'] );
			$icon_order = array();
			foreach ( $imported_option5 as $key => $val ) {
				if ( $key == 'sfsi_plus_custom_css' || $key == "sfsi_plus_custom_admin_css" || $key == "sfsi_plus_custom_MouseOverTexts" ) {
					$imported_option5[ $key ] = stripslashes( $val );
				}
				if ( $imported_option5["sfsi_plus_icons_ClickPageOpen"] == "yes" ) {
					$imported_option5["sfsi_plus_icons_ClickPageOpen"] = "window";
				}
				$icon_matched = array();
				if ( preg_match( '/sfsi_plus_(\w+)Icon_order/', $key, $icon_matched ) ) {
					if ( count( $icon_matched ) > 1 ) {
						$iconName = $icon_matched[1];
						if ( $iconName == "facebook" ) {
							$iconName = "fb";
						}
						array_push( $icon_order, array( "iconName" => $iconName, "index" => $val ) );
						unset( $imported_option5[ $key ] );
					}
				}
			}
			$imported_option5["sfsi_order_icons_mobile"]  = $icon_order;
			$imported_option5["sfsi_order_icons_desktop"] = $icon_order;
			update_option( 'sfsi_premium_section5_options', serialize( array_merge( $current_option5, $imported_option5 ) ) );
			update_option( 'sfsi_premium_section6_options', serialize( array_merge( $current_option6, $imported_option6 ) ) );

			foreach ( $imported_option7 as $key => $val ) {
				if ( $key == 'sfsi_plus_Shown_pop' ) {
					$imported_option7['sfsi_plus_Shown_pop'] = explode( " ", $imported_option7['sfsi_plus_Shown_pop'] );
				}
			}
			update_option( 'sfsi_premium_section7_options', serialize( array_merge( $current_option7, $imported_option7 ) ) );
			$sfsi_plus_responsive_icons = $imported_option8["sfsi_plus_responsive_icons"];
			if ( ! isset( $imported_option8["sfsi_plus_responsive_icons"]["custom_icons"] ) ) {
				$imported_option8["sfsi_plus_responsive_icons"]["custom_icons"] = array();
			}
			$imported_option8["sfsi_plus_responsive_icons_after_post"]             = $imported_option8["sfsi_plus_responsive_icons_end_post"];
			$imported_option8["sfsi_plus_responsive_icons_after_post_on_taxonomy"] = $imported_option8["sfsi_plus_responsive_icons_end_post"];

			unset( $imported_option8["sfsi_plus_responsive_icons_end_post"] );

			update_option( 'sfsi_premium_section8_options', serialize( array_merge( $current_option8, $imported_option8 ) ) );
			update_option( 'sfsi_premium_section9_options', serialize( array_merge( $current_option9, $imported_option9 ) ) );
			echo json_encode( "success" );
			die();
		} elseif ( $_POST['import_type'] == "old" ) {

			$imported_option1                      = set_premium_key( $imported_option1 );
			$imported_option1['sfsi_custom_files'] = $imported_option1['sfsi_plus_custom_files'];
			unset( $imported_option1['sfsi_plus_custom_files'] );
			update_option( 'sfsi_premium_section1_options', serialize( array_merge( $current_option1, $imported_option1 ) ) );

			$imported_option2                                     = set_premium_key( $imported_option2 );
			$imported_option2['sfsi_plus_weiboVisit_url']         = $imported_option2['sfsi_plus_weibo_pageURL'];
			$imported_option2['sfsi_plus_vkVisit_url']            = $imported_option2['sfsi_plus_vk_pageURL'];
			$imported_option2['sfsi_plus_okVisit_url']            = $imported_option2['sfsi_plus_vk_pageURL'];
			$imported_option2['sfsi_plus_telegramMessage_option'] = 'yes';
			$imported_option2['sfsi_plus_wechatShare_option']     = 'yes';
			$imported_option2['sfsi_plus_weiboVisit_option']      = 'yes';
			$imported_option2['sfsi_plus_okVisit_option']         = 'yes';
			$imported_option2['sfsi_plus_vkVisit_option']         = 'yes';
			unset( $imported_option2['sfsi_plus_rss_blogName'],
				$imported_option2['sfsi_plus_rss_blogEmail'],
				$imported_option2['sfsi_plus_rss_blogEmail'],
				$imported_option2['sfsi_plus_telegram_page'],
				$imported_option2['sfsi_plus_telegram_pageURL'],
				$imported_option2['sfsi_plus_weibo_page'],
				$imported_option2['sfsi_plus_vk_page'],
				$imported_option2['sfsi_plus_ok_page'],
				$imported_option2['sfsi_plus_weibo_pageURL'],
				$imported_option2['sfsi_plus_vk_pageURL'],
				$imported_option2['sfsi_plus_vk_pageURL'] );
			update_option( 'sfsi_premium_section2_options', serialize( array_merge( $current_option2, $imported_option2 ) ) );

			$imported_option3                                                      = set_premium_key( $imported_option3 );
			$imported_option3['sfsi_plus_mouseover_other_icons_transition_effect'] = isset( $imported_option3['mouseover_other_icons_transition_effect'] ) ? $imported_option3['mouseover_other_icons_transition_effect'] : '';
			if ( isset( $imported_option3['mouseover_other_icons_transition_effect'] ) ) {
				unset( $imported_option3['mouseover_other_icons_transition_effect'] );
			}
			update_option( 'sfsi_premium_section3_options', serialize( array_merge( $current_option3, $imported_option3 ) ) );

			$imported_option4                                          = set_premium_key( $imported_option4 );
			$imported_option4['sfsiplus_tw_consumer_key']              = $imported_option4['tw_consumer_key'];
			$imported_option4['sfsiplus_tw_consumer_secret']           = $imported_option4['tw_consumer_secret'];
			$imported_option4['sfsiplus_tw_oauth_access_token']        = $imported_option4['tw_oauth_access_token'];
			$imported_option4['sfsiplus_tw_oauth_access_token_secret'] = $imported_option4['tw_oauth_access_token_secret'];
			unset( $imported_option4['tw_consumer_key'],
				$imported_option4['tw_consumer_secret'],
				$imported_option4['tw_oauth_access_token'],
				$imported_option4['tw_oauth_access_token_secret'],
				$imported_option4['sfsi_plus_wechat_countsDisplay'],
				$imported_option4['sfsi_plus_wechat_manualCounts'] );
			update_option( 'sfsi_premium_section4_options', serialize( array_merge( $current_option4, $imported_option4 ) ) );

			$imported_option5                                    = set_premium_key( $imported_option5 );
			$imported_option5['sfsi_plus_twitter_aboutPageText'] = $imported_option2['sfsi_plus_twitter_aboutPageText'];
			foreach ( $imported_option5 as $key => $val ) {
				if ( $key == 'sfsi_plus_custom_css' || $key == "sfsi_plus_custom_admin_css" || $key == "sfsi_plus_custom_MouseOverTexts" ) {
					$imported_option5[ $key ] = stripslashes( $val );
				}
				if ( $imported_option5["sfsi_plus_icons_ClickPageOpen"] == "yes" ) {
					$imported_option5["sfsi_plus_icons_ClickPageOpen"] = "window";
				}
				$icon_matched = array();
				$icon_order   = array();
				if ( preg_match( '/sfsi_plus_(\w+)Icon_order/', $key, $icon_matched ) ) {
					if ( count( $icon_matched ) > 1 ) {
						$iconName = $icon_matched[1];
						if ( $iconName == "facebook" ) {
							$iconName = "fb";
						}
						array_push( $icon_order, array( "iconName" => $iconName, "index" => $val ) );
						unset( $imported_option5[ $key ] );
					}
				}
			}
			update_option( 'sfsi_premium_section5_options', serialize( array_merge( $current_option5, $imported_option5 ) ) );

			$imported_option6 = set_premium_key( $imported_option6 );
			foreach ( $imported_option6 as $key => $val ) {
				if (
					$key !== 'sfsi_plus_show_Onposts' ||
					$key !== 'sfsi_plus_icons_postPositon' ||
					$key !== 'sfsi_plus_icons_alignment' ||
					$key !== 'sfsi_plus_textBefor_icons' ||
					$key !== 'sfsi_plus_icons_DisplayCounts' ||
					$key !== 'sfsi_plus_display_button_type'
				) {
					unset( $imported_option6[ $key ] );
				}
			}
			update_option( 'sfsi_premium_section6_options', serialize( array_merge( $current_option6, $imported_option6 ) ) );

			$imported_option7 = set_premium_key( $imported_option7 );
			foreach ( $imported_option7 as $key => $val ) {
				if ( $key == 'sfsi_plus_Shown_pop' ) {
					$imported_option7['sfsi_plus_Shown_pop'] = explode( " ", $imported_option7['sfsi_plus_Shown_pop'] );
				}
			}
			update_option( 'sfsi_premium_section7_options', serialize( array_merge( $current_option7, $imported_option7 ) ) );

			$imported_option9                                          = set_premium_key( $imported_option9 );
			$imported_option9['sfsi_plus_show_item_onposts']           = $imported_option9['sfsi_plus_show_via_afterposts'];
			$imported_option9['sfsi_plus_place_item_manually']         = $imported_option9['sfsi_plus_show_via_shortcode'];
			$imported_option9['sfsi_plus_float_show_on_mobile']        = isset( $imported_option9['sfsi_disable_floaticons'] ) ? $imported_option9['sfsi_disable_floaticons'] : '';
			$imported_option9['sfsi_plus_responsive_icons_after_post'] = isset( $imported_option9['sfsi_responsive_icons_end_post'] ) ? $imported_option9['sfsi_responsive_icons_end_post'] : '';
			$imported_option9['sfsi_plus_float_on_page']               = $imported_option9['sfsi_plus_icons_float'];
			$imported_option9['sfsi_plus_float_page_position']         = $imported_option9['sfsi_plus_icons_floatPosition'];
			unset( $imported_option9['sfsi_plus_show_via_afterposts'],
				$imported_option9['sfsi_plus_show_via_shortcode'],
				$imported_option9['sfsi_responsive_icons_end_post'],
				$imported_option9['sfsi_plus_icons_float'],
				$imported_option9['sfsi_plus_icons_floatPosition'] );

			if ( isset( $imported_option9['sfsi_disable_floaticons'] ) ) {
				unset( $imported_option9['sfsi_disable_floaticons'] );
			}
			if ( isset( $imported_option9['sfsi_responsive_icons_end_post'] ) ) {
				unset( $imported_option9['sfsi_responsive_icons_end_post'] );
			}
			$imported_option6_all                                                = set_premium_key( $_POST['imports']['option6'] );
			$imported_option6_all['sfsi_plus_responsive_icons']['default_icons'] = ( array_merge( $current_option8['sfsi_plus_responsive_icons']['default_icons'], $imported_option6_all['sfsi_plus_responsive_icons']['default_icons'] ) );
			$imported_option6_all['sfsi_plus_responsive_icons']['settings']      = ( array_merge( $current_option8['sfsi_plus_responsive_icons']['settings'], $imported_option6_all['sfsi_plus_responsive_icons']['settings'] ) );
			if ( ! isset( $imported_option6_all["sfsi_plus_responsive_icons"]["custom_icons"] ) ) {
				$imported_option6_all["sfsi_plus_responsive_icons"]["custom_icons"] = array();
			}
			$imported_option6_all['sfsi_plus_responsive_icons']['custom_icons'] = ( array_merge( $current_option8['sfsi_plus_responsive_icons']['custom_icons'], $imported_option6_all['sfsi_plus_responsive_icons']['custom_icons'] ) );
			foreach ( $imported_option6_all as $key => $val ) {
				if (
					$key == 'sfsi_plus_show_Onposts' ||
					$key == 'sfsi_plus_icons_postPositon' ||
					$key == 'sfsi_plus_icons_alignment' ||
					$key == 'sfsi_plus_textBefor_icons' ||
					$key == 'sfsi_plus_icons_DisplayCounts'
				) {
					unset( $imported_option6_all[ $key ] );
				}
			}
			$imported_option9 = array_merge( $imported_option9, $imported_option6_all );
			update_option( 'sfsi_premium_section8_options', serialize( array_merge( $current_option8, $imported_option9 ) ) );

			$imported_option8 = set_premium_key( $imported_option8 );
			update_option( 'sfsi_premium_section9_options', serialize( array_merge( $current_option9, $imported_option8 ) ) );
			echo json_encode( "success" );
			die();
		} elseif ( $_POST['import_type'] !== "premium" ) {
			echo "cannot determine the exported plugin";
			echo json_encode( "success" );
			die();
		} else {
			$imported_option1["sfsi_custom_mobile_icons"]   = stripslashes( $imported_option1["sfsi_custom_mobile_icons"] );
			$imported_option1["sfsi_custom_desktop_icons"]  = stripslashes( $imported_option1["sfsi_custom_desktop_icons"] );
			$imported_option1["sfsi_custom_files"]          = stripslashes( $imported_option1["sfsi_custom_files"] );
			$imported_option2["sfsi_plus_CustomIcon_links"] = stripslashes( $imported_option2["sfsi_plus_CustomIcon_links"] );
			update_option( 'sfsi_premium_section1_options', serialize( array_merge( $current_option1, $imported_option1 ) ) );
			update_option( 'sfsi_premium_section2_options', serialize( array_merge( $current_option2, $imported_option2 ) ) );
			update_option( 'sfsi_premium_section3_options', serialize( array_merge( $current_option3, $imported_option3 ) ) );
			update_option( 'sfsi_premium_section4_options', serialize( array_merge( $current_option4, $imported_option4 ) ) );
			foreach ( $imported_option5 as $key => $val ) {
				if ( $key == 'sfsi_plus_custom_css' || $key == "sfsi_plus_custom_admin_css" || $key == "sfsi_plus_custom_MouseOverTexts" ) {
					$imported_option5[ $key ] = stripslashes( $val );
				}
			}
			update_option( 'sfsi_premium_section5_options', serialize( array_merge( $current_option5, $imported_option5 ) ) );
			update_option( 'sfsi_premium_section6_options', serialize( array_merge( $current_option6, $imported_option6 ) ) );
			update_option( 'sfsi_premium_section7_options', serialize( array_merge( $current_option7, $imported_option7 ) ) );
			$sfsi_plus_responsive_icons = $imported_option8["sfsi_plus_responsive_icons"];
			if ( ! isset( $imported_option8["sfsi_plus_responsive_icons"]["custom_icons"] ) ) {
				$imported_option8["sfsi_plus_responsive_icons"]["custom_icons"] = array();
			}
			update_option( 'sfsi_premium_section8_options', serialize( array_merge( $current_option8, $imported_option8 ) ) );
			update_option( 'sfsi_premium_section9_options', serialize( array_merge( $current_option9, $imported_option9 ) ) );
			echo json_encode( "success" );
			die();
		}
	}
}

add_action( 'wp_ajax_nopriv_authorize_bitly', 'sfsi_premium_authorize_bitly' );
add_action( 'wp_ajax_authorize_bitly', 'sfsi_premium_authorize_bitly' );
function sfsi_premium_authorize_bitly() {
	// var_dump($_GET,$_POST);die();return;
	if ( ! wp_verify_nonce( $_GET['nonce'], "sfsi_premium_authorize_bitly" ) ) {
		echo __( 'Could\'t Authenticate', 'ultimate-social-media-plus' );
		exit;
	}
	$code = $_GET['code'];
	$code = sanitize_text_field( $_GET['code'] );

	$access_token = sfsi_premium_get_bitly_access_token( $code );
	// $sfsi_premium_bitly_options = sanitize_text_field(get_option('sfsi_premium_bitly_options', false));
	// $sfsi_plus_bitly_v4 = sanitize_text_field(get_option('sfsi_plus_bitly_v4', false));
	$sfsi_premium_bitly_options = $access_token;
	$sfsi_plus_bitly_v4         = "yes";
	// var_dump($sfsi_premium_bitly_options, $code);
	update_option( 'sfsi_premium_bitly_options', ( $sfsi_premium_bitly_options ) );
	update_option( 'sfsi_plus_bitly_v4', ( $sfsi_plus_bitly_v4 ) );

	echo __( 'Successfully Authenticated', 'ultimate-social-media-plus' );
	echo "<script>setTimeout('self.close()', 3000 )</script>";
	// var_dump($access_token, $code);
	die();
}

function sfsi_premium_get_bitly_access_token( $code ) {
	$response = wp_remote_post( 'https://api-ssl.bitly.com/oauth/access_token', array(
		'blocking'   => true,
		'user-agent' => 'sf rss request',
		'timeout'    => 30,
		'sslverify'  => true,
		'headers'    => array(
			"Accept" => "application/json"
		),
		'body'       => array(
			'client_id'     => BITLY_CLIENT_ID,
			'client_secret' => BITLY_CLIENT_SECRET,
			'code'          => $code,
			'redirect_uri'  => BITLY_REDIRECT_URL,
		),

	) );
	if ( ! is_wp_error( $response ) || 200 === wp_remote_retrieve_response_code( $response ) ) {

		if ( ! empty( $response ) ) {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );
			if ( ! empty( $data ) && isset( $data['access_token'] ) ) {
				return $data['access_token'];
			} elseif ( ! empty( $data ) && isset( $data['status_code'] ) && ( 401 == $data['status_code'] ) ) {
				return array( __( 'Timed out . please try again after some time', 'ultimate-social-media-plus' ) );
			} else {

				return array( __( 'Data not received in correct format:', 'ultimate-social-media-plus' ) . $body );
			}
		} else {
			return array( __( 'Data not received in correct format, Please try again.', 'ultimate-social-media-plus' ) );
		}
	} else {
		return array( $response->get_error_message() );
	}

	return false;
}

if ( ! function_exists( 'sfsi_verify_language_values' ) ) {
	function sfsi_verify_language_values( $locale_code ) {
		$sfsi_map_language_values = array(
			"Follow_ar",
			"Subscribe_ar",
			"Follow_bg_BG",
			"Subscribe_bg_BG",
			"Follow_zh_CN",
			"Subscribe_zh_CN",
			"Follow_cs_CZ",
			"Subscribe_cs_CZ",
			"Follow_da_DK",
			"Subscribe_da_DK",
			"Follow_nl_NL",
			"Subscribe_nl_NL",
			"Follow_fi",
			"Subscribe_fi",
			"Follow_fr_FR",
			"Subscribe_fr_FR",
			"Follow_de_DE",
			"Subscribe_de_DE",
			"Follow_en_US",
			"Subscribe_en_US",
			"Follow_el",
			"Subscribe_el",
			"Follow_hu_HU",
			"Subscribe_hu_HU",
			"Follow_id_ID",
			"Subscribe_id_ID",
			"Follow_it_IT",
			"Subscribe_it_IT",
			"Follow_ja",
			"Subscribe_ja",
			"Follow_ko_KR",
			"Subscribe_ko_KR",
			"Follow_nb_NO",
			"Subscribe_nb_NO",
			"Follow_fa_IR",
			"Subscribe_fa_IR",
			"Follow_pl_PL",
			"Subscribe_pl_PL",
			"Follow_pt_PT",
			"Subscribe_pt_PT",
			"Follow_ro_RO",
			"Subscribe_ro_RO",
			"Follow_ru_RU",
			"Subscribe_ru_RU",
			"Follow_sk_SK",
			"Subscribe_sk_SK",
			"Follow_es_ES",
			"Subscribe_es_ES",
			"Follow_sv_SE",
			"Subscribe_sv_SE",
			"Follow_th",
			"Subscribe_th",
			"Follow_tr_TR",
			"Subscribe_tr_TR",
			"Follow_vi",
			"Subscribe_vi",

			"Visit_us_ar",
			"Visit_me_ar",
			"Visit_us_bg_BG",
			"Visit_me_bg_BG",
			"Visit_us_zh_CN",
			"Visit_me_zh_CN",
			"Visit_us_cs_CZ",
			"Visit_me_cs_CZ",
			"Visit_us_da_DK",
			"Visit_me_da_DK",
			"Visit_us_nl_NL",
			"Visit_me_nl_NL",
			"Visit_us_fi",
			"Visit_me_fi",
			"Visit_us_fr_FR",
			"Visit_me_fr_FR",
			"Visit_us_de_DE",
			"Visit_me_de_DE",
			"Visit_us_en_US",
			"Visit_me_en_US",
			"Visit_us_el",
			"Visit_me_el",
			"Visit_us_hu_HU",
			"Visit_me_hu_HU",
			"Visit_us_id_ID",
			"Visit_me_id_ID",
			"Visit_us_it_IT",
			"Visit_me_it_IT",
			"Visit_us_ja",
			"Visit_me_ja",
			"Visit_us_ko_KR",
			"Visit_me_ko_KR",
			"Visit_us_nb_NO",
			"Visit_me_nb_NO",
			"Visit_us_fa_IR",
			"Visit_me_fa_IR",
			"Visit_us_pl_PL",
			"Visit_me_pl_PL",
			"Visit_us_pt_PT",
			"Visit_me_pt_PT",
			"Visit_us_ro_RO",
			"Visit_me_ro_RO",
			"Visit_us_ru_RU",
			"Visit_me_ru_RU",
			"Visit_us_sk_SK",
			"Visit_me_sk_SK",
			"Visit_us_es_ES",
			"Visit_me_es_ES",
			"Visit_us_sv_SE",
			"Visit_me_sv_SE",
			"Visit_us_th",
			"Visit_me_th",
			"Visit_us_tr_TR",
			"Visit_me_tr_TR",
			"Visit_us_vi",
			"Visit_me_vi",

			"automatic_visit_us",
			"automatic_visit_me",
			"Visit_us_ar",
			"Visit_me_ar",
			"Visit_us_zh_CN",
			"Visit_me_zh_CN",
			"cs_CZ",
			"Visit_us_fr_FR",
			"Visit_me_fr_FR",
			"Visit_us_en_US",
			"Visit_me_en_US",
			"Visit_us_hu_HU",
			"Visit_me_hu_HU",
			"Visit_us_hi_IN",
			"Visit_me_hi_IN",
			"Visit_us_it_IT",
			"Visit_me_it_IT",
			"Visit_me_ja",
			"Visit_us_ja",
			"fa_IR",
			"visit_us_pl_PL",
			"visit_me_pl_PL",
			"visit_us_pt_PT",
			"visit_me_pt_PT",
			"visit_us_pt_BR",
			"visit_me_pt_BR",
			"visit_us_ru_RU",
			"visit_me_ru_RU",
			"visit_us_es_ES",
			"visit_me_es_ES",
			"visit_us_tr_TR",
			"visit_me_tr_TR",
			"visit_us_vi",
			"visit_me_vi",
			"visit_us_se",
			"visit_me_se",
			"automatic_visit_us",
			"automatic_visit_me",
			"ar",
			"Visit_us_ar",
			"zh_CN",
			"Visit_us_zh_CN",
			"cs_CZ",
			"fr_FR",
			"Visit_us_fr_FR",
			"en_US",
			"Visit_us_en_US",
			"hu_HU",
			"Visit_us_hu_HU",
			"hi_IN",
			"Visit_u_hi_IN",
			"it_IT",
			"Visit_us_it_IT",
			"ja",
			"Visit_us_ja",
			"fa_IR",
			"pl_PL",
			"Visit_u_pl_PL",
			"pt_PT",
			"Visit_us_pt_PT",
			"pt_BR",
			"Visit_pt_BR",
			"ru_RU",
			"Visit_us_ru_RU",
			"es_ES",
			"Visit_us_es_ES",
			"tr_TR",
			"Visit_us_tr_TR",
			"vi",
			"Visit_us_vi",
			"me_se_SE",
			"us_se_SE",

			"automatic",
			"ar",
			"bg_BG",
			"da_DK",
			"de_DE",
			"en_US",
			"el",
			"es_ES",
			"fa_IR",
			"fi_FI",
			"fr_FR",
			"he_IL",
			"hi_IN",
			"hu_HU",
			"id_ID",
			"it_IT",
			"ja",
			"ko_KR",
			"nl_NL",
			"pl_PL",
			"pt_PT",
			"ru_RU",
			"sv_SE",
			"th",
			"tr_TR",
			"vi",
			"zh_CN"
		);

		return in_array( $locale_code, $sfsi_map_language_values );
	}
}