<?php
/* maybe_unserialize all saved option for second section options */
$option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
$option1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );
$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );

/*
 * Sanitize, escape and validate values
 */
$sfsi_plus_actvite_theme                 = isset( $option3['sfsi_plus_actvite_theme'] ) ? sanitize_text_field( $option3['sfsi_plus_actvite_theme'] ) : '';
$sfsi_plus_mouseOver                     = isset( $option3['sfsi_plus_mouseOver'] ) ? sanitize_text_field( $option3['sfsi_plus_mouseOver'] ) : '';
$sfsi_plus_mouseOver_effect              = isset( $option3['sfsi_plus_mouseOver_effect'] ) ? sanitize_text_field( $option3['sfsi_plus_mouseOver_effect'] ) : '';
$sfsi_plus_shuffle_icons                 = isset( $option3['sfsi_plus_shuffle_icons'] ) ? sanitize_text_field( $option3['sfsi_plus_shuffle_icons'] ) : '';
$sfsi_plus_shuffle_Firstload             = isset( $option3['sfsi_plus_shuffle_Firstload'] ) ? sanitize_text_field( $option3['sfsi_plus_shuffle_Firstload'] ) : '';
$sfsi_plus_shuffle_interval              = isset( $option3['sfsi_plus_shuffle_interval'] ) ? sanitize_text_field( $option3['sfsi_plus_shuffle_interval'] ) : '';
$sfsi_plus_shuffle_intervalTime          = isset( $option3['sfsi_plus_shuffle_intervalTime'] ) ? intval( $option3['sfsi_plus_shuffle_intervalTime'] ) : '';
$sfsi_plus_mouseOver_effect_type         = isset( $option3['sfsi_plus_mouseOver_effect_type'] ) ? sanitize_text_field( $option3['sfsi_plus_mouseOver_effect_type'] ) : 'same_icons';
$sfsi_plus_mouseOver_other_icon_images   = isset( $option3['sfsi_plus_mouseOver_other_icon_images'] ) ? maybe_unserialize( $option3['sfsi_plus_mouseOver_other_icon_images'] ) : array();
$mouseover_other_icons_transition_effect = isset( $option3['sfsi_plus_mouseover_other_icons_transition_effect'] ) ? sanitize_text_field( $option3['sfsi_plus_mouseover_other_icons_transition_effect'] ) : 'noeffect';
?>
<!-- Section 3 "What design & animation do you want to give your icons?" main div Start -->
<div class="tab3">
    <!--Content of 4-->
    <div class="row <?php echo $sfsi_plus_mouseOver_effect_type; ?> mouse_txt sfsiplusmousetxt tab3">
        <p>
			<?php _e( 'A good & well-fitting design is not only nice to look at, but it increases the chances that people will subscribe and/or share your site with friends:', 'ultimate-social-media-plus' ); ?>
        </p>
        <ul class="tab_3_list">
            <li>
				<?php _e( 'It comes across as more professional and gives your site more “credit”', 'ultimate-social-media-plus' ); ?>
            </li>
            <li>
				<?php _e( 'A smart automatic animation can make your visitors aware of your icons in an unintrusive manner', 'ultimate-social-media-plus' ); ?>
            </li>
        </ul>

        <p style="padding:0px;">
			<?php _e( 'The icon have been compressed by Shortpixel.com for faster loading of your site. Thank you Shortpixel!', 'ultimate-social-media-plus' ); ?>
        </p>

        <div class="row">
            <h3><?php _e( 'Design options', 'ultimate-social-media-plus' ); ?></h3>

            <!--icon themes section start -->
            <ul class="sfsiplus_tab_3_icns sfsiplus_tab_3_icns_list">
                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'default' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="default" class="styled"/>
                    <label>
						<?php _e( 'Default', 'ultimate-social-media-plus' ); ?>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_1_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_1_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_1_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_1_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_1_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_1_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_1_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_1_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_1_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_1_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_1_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_1_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_1_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_1_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_1_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_1_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_1_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_1_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_1_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_1_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_1_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_1_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_1_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_1_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_1_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_1_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_1_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_1_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_1_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_1_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_1_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_1_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_1_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_1_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_1_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'flat' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="flat" class="styled"/>
                    <label>
						<?php _e( 'Flat', 'ultimate-social-media-plus' ); ?>
                    </label>
					<?php
					$sfsi_plus_rss_bgColor = $sfsi_plus_rss_bgColor_style = $sfsi_plus_email_bgColor = $sfsi_plus_email_bgColor_style = $sfsi_plus_facebook_bgColor = $sfsi_plus_facebook_bgColor_style = $sfsi_plus_twitter_bgColor = $sfsi_plus_twitter_bgColor_style = $sfsi_plus_share_bgColor = $sfsi_plus_share_bgColor_style = $sfsi_plus_youtube_bgColor = $sfsi_plus_youtube_bgColor_style = $sfsi_plus_pinterest_bgColor = $sfsi_plus_pinterest_bgColor_style = $sfsi_plus_linkedin_bgColor = $sfsi_plus_linkedin_bgColor_style = $sfsi_plus_instagram_bgColor = $sfsi_plus_instagram_bgColor_style = $sfsi_plus_ria_bgColor = $sfsi_plus_ria_bgColor_style = $sfsi_plus_inha_bgColor = $sfsi_plus_inha_bgColor_style = $sfsi_plus_houzz_bgColor = $sfsi_plus_houzz_bgColor_style = $sfsi_plus_snapchat_bgColor = $sfsi_plus_snapchat_bgColor_style = $sfsi_plus_whatsapp_bgColor = $sfsi_plus_whatsapp_bgColor_style = $sfsi_plus_skype_bgColor = $sfsi_plus_skype_bgColor_style = $sfsi_plus_phone_bgColor = $sfsi_plus_phone_bgColor_style = $sfsi_plus_vimeo_bgColor = $sfsi_plus_vimeo_bgColor_style = $sfsi_plus_soundcloud_bgColor = $sfsi_plus_soundcloud_bgColor_style = $sfsi_plus_yummly_bgColor = $sfsi_plus_yummly_bgColor_style = $sfsi_plus_flickr_bgColor = $sfsi_plus_flickr_bgColor_style = $sfsi_plus_reddit_bgColor = $sfsi_plus_reddit_bgColor_style = $sfsi_plus_tumblr_bgColor = $sfsi_plus_tumblr_bgColor_style = $sfsi_plus_fbmessenger_bgColor = $sfsi_plus_fbmessenger_bgColor_style = $sfsi_plus_gab_bgColor = $sfsi_plus_gab_bgColor_style = $sfsi_plus_mix_bgColor = $sfsi_plus_mix_bgColor_style = $sfsi_plus_ok_bgColor = $sfsi_plus_ok_bgColor_style = $sfsi_plus_telegram_bgColor = $sfsi_plus_telegram_bgColor_style = $sfsi_plus_vk_bgColor = $sfsi_plus_vk_bgColor_style = $sfsi_plus_wechat_bgColor = $sfsi_plus_wechat_bgColor_style = $sfsi_plus_weibo_bgColor = $sfsi_plus_weibo_bgColor_style = $sfsi_plus_xing_bgColor = $sfsi_plus_xing_bgColor_style = $sfsi_plus_copylink_bgColor = $sfsi_plus_copylink_bgColor_style = $sfsi_plus_mastodon_bgColor = $sfsi_plus_mastodon_bgColor_style = $sfsi_plus_threads_bgColor = $sfsi_plus_threads_bgColor_style = $sfsi_plus_bluesky_bgColor = $sfsi_plus_bluesky_bgColor_style = '';

					if ( isset( $option3['sfsi_plus_rss_bgColor'] ) && $option3['sfsi_plus_rss_bgColor'] != '' ) {
						$sfsi_plus_rss_bgColor       = $option3['sfsi_plus_rss_bgColor'];
						$sfsi_plus_rss_bgColor_style = 'background: ' . $sfsi_plus_rss_bgColor;
					} else {
						$sfsi_plus_rss_bgColor_style = 'background: #FF9845';
					}

					if ( isset( $option3['sfsi_plus_email_bgColor'] ) && $option3['sfsi_plus_email_bgColor'] != '' ) {
						$sfsi_plus_email_bgColor       = $option3['sfsi_plus_email_bgColor'];
						$sfsi_plus_email_bgColor_style = 'background: ' . $sfsi_plus_email_bgColor;
					} else {
						if ( $option2['sfsi_plus_rss_icons'] == "sfsi" ) {
							$sfsi_plus_email_bgColor_style = 'background: #05B04E';
						} elseif ( $option2['sfsi_plus_rss_icons'] == "email" ) {
							$sfsi_plus_email_bgColor_style = 'background: #343D44';
						} else {
							$sfsi_plus_email_bgColor_style = 'background: #16CB30';
						}
					}

					if ( isset( $option3['sfsi_plus_facebook_bgColor'] ) && $option3['sfsi_plus_facebook_bgColor'] != '' ) {
						$sfsi_plus_facebook_bgColor       = $option3['sfsi_plus_facebook_bgColor'];
						$sfsi_plus_facebook_bgColor_style = 'background: ' . $sfsi_plus_facebook_bgColor;
					} else {
						$sfsi_plus_facebook_bgColor_style = 'background: #336699';
					}

					if ( isset( $option3['sfsi_plus_threads_bgColor'] ) && $option3['sfsi_plus_threads_bgColor'] != '' ) {
						$sfsi_plus_threads_bgColor       = $option3['sfsi_plus_threads_bgColor'];
						$sfsi_plus_threads_bgColor_style = 'background: ' . $sfsi_plus_threads_bgColor;
					} else {
						$sfsi_plus_threads_bgColor_style = 'background: #252525';
					}

					if ( isset( $option3['sfsi_plus_bluesky_bgColor'] ) && $option3['sfsi_plus_bluesky_bgColor'] != '' ) {
						$sfsi_plus_bluesky_bgColor       = $option3['sfsi_plus_bluesky_bgColor'];
						$sfsi_plus_bluesky_bgColor_style = 'background: ' . $sfsi_plus_bluesky_bgColor;
					} else {
						$sfsi_plus_bluesky_bgColor_style = 'background: #1185fe';
					}

					if ( isset( $option3['sfsi_plus_twitter_bgColor'] ) && $option3['sfsi_plus_twitter_bgColor'] != '' ) {
						$sfsi_plus_twitter_bgColor       = $option3['sfsi_plus_twitter_bgColor'];
						$sfsi_plus_twitter_bgColor_style = 'background: ' . $sfsi_plus_twitter_bgColor;
					} else {
						$sfsi_plus_twitter_bgColor_style = 'background: #000000';
					}

					if ( isset( $option3['sfsi_plus_share_bgColor'] ) && $option3['sfsi_plus_share_bgColor'] != '' ) {
						$sfsi_plus_share_bgColor       = $option3['sfsi_plus_share_bgColor'];
						$sfsi_plus_share_bgColor_style = 'background: ' . $sfsi_plus_share_bgColor;
					} else {
						$sfsi_plus_share_bgColor_style = 'background: #26AD62';
					}

					if ( isset( $option3['sfsi_plus_youtube_bgColor'] ) && $option3['sfsi_plus_youtube_bgColor'] != '' ) {
						$sfsi_plus_youtube_bgColor       = $option3['sfsi_plus_youtube_bgColor'];
						$sfsi_plus_youtube_bgColor_style = 'background: ' . $sfsi_plus_youtube_bgColor;
					} else {
						$sfsi_plus_youtube_bgColor_style = 'background: linear-gradient(141.52deg, #E02F2F 14.26%, #E02F2F 48.98%, #C92A2A 49.12%, #C92A2A 85.18%);';
					}

					if ( isset( $option3['sfsi_plus_pinterest_bgColor'] ) && $option3['sfsi_plus_pinterest_bgColor'] != '' ) {
						$sfsi_plus_pinterest_bgColor       = $option3['sfsi_plus_pinterest_bgColor'];
						$sfsi_plus_pinterest_bgColor_style = 'background: ' . $sfsi_plus_pinterest_bgColor;
					} else {
						$sfsi_plus_pinterest_bgColor_style = 'background: #CC3333';
					}

					if ( isset( $option3['sfsi_plus_linkedin_bgColor'] ) && $option3['sfsi_plus_linkedin_bgColor'] != '' ) {
						$sfsi_plus_linkedin_bgColor       = $option3['sfsi_plus_linkedin_bgColor'];
						$sfsi_plus_linkedin_bgColor_style = 'background: ' . $sfsi_plus_linkedin_bgColor;
					} else {
						$sfsi_plus_linkedin_bgColor_style = 'background: #0877B5';
					}

					if ( isset( $option3['sfsi_plus_instagram_bgColor'] ) && $option3['sfsi_plus_instagram_bgColor'] != '' ) {
						$sfsi_plus_instagram_bgColor       = $option3['sfsi_plus_instagram_bgColor'];
						$sfsi_plus_instagram_bgColor_style = 'background: ' . $sfsi_plus_instagram_bgColor;
					} else {
						$sfsi_plus_instagram_bgColor_style = 'background: #336699';
					}

					if ( isset( $option3['sfsi_plus_ria_bgColor'] ) && $option3['sfsi_plus_ria_bgColor'] != '' ) {
						$sfsi_plus_ria_bgColor       = $option3['sfsi_plus_ria_bgColor'];
						$sfsi_plus_ria_bgColor_style = 'background: ' . $sfsi_plus_ria_bgColor;
					} else {
						$sfsi_plus_ria_bgColor_style = 'background: #10A9A0';
					}

					if ( isset( $option3['sfsi_plus_inha_bgColor'] ) && $option3['sfsi_plus_inha_bgColor'] != '' ) {
						$sfsi_plus_inha_bgColor       = $option3['sfsi_plus_inha_bgColor'];
						$sfsi_plus_inha_bgColor_style = 'background: ' . $sfsi_plus_inha_bgColor;
					} else {
						$sfsi_plus_inha_bgColor_style = 'background: #348CBC';
					}

					if ( isset( $option3['sfsi_plus_houzz_bgColor'] ) && $option3['sfsi_plus_houzz_bgColor'] != '' ) {
						$sfsi_plus_houzz_bgColor       = $option3['sfsi_plus_houzz_bgColor'];
						$sfsi_plus_houzz_bgColor_style = 'background: ' . $sfsi_plus_houzz_bgColor;
					} else {
						$sfsi_plus_houzz_bgColor_style = 'background: #7BC043';
					}

					if ( isset( $option3['sfsi_plus_snapchat_bgColor'] ) && $option3['sfsi_plus_snapchat_bgColor'] != '' ) {
						$sfsi_plus_snapchat_bgColor       = $option3['sfsi_plus_snapchat_bgColor'];
						$sfsi_plus_snapchat_bgColor_style = 'background: ' . $sfsi_plus_snapchat_bgColor;
					} else {
						$sfsi_plus_snapchat_bgColor_style = 'background: #EDEC1F';
					}

					if ( isset( $option3['sfsi_plus_whatsapp_bgColor'] ) && $option3['sfsi_plus_whatsapp_bgColor'] != '' ) {
						$sfsi_plus_whatsapp_bgColor       = $option3['sfsi_plus_whatsapp_bgColor'];
						$sfsi_plus_whatsapp_bgColor_style = 'background: ' . $sfsi_plus_whatsapp_bgColor;
					} else {
						$sfsi_plus_whatsapp_bgColor_style = 'background: #3ED946';
					}

					if ( isset( $option3['sfsi_plus_skype_bgColor'] ) && $option3['sfsi_plus_skype_bgColor'] != '' ) {
						$sfsi_plus_skype_bgColor       = $option3['sfsi_plus_skype_bgColor'];
						$sfsi_plus_skype_bgColor_style = 'background: ' . $sfsi_plus_skype_bgColor;
					} else {
						$sfsi_plus_skype_bgColor_style = 'background: #00A9F1';
					}

					if ( isset( $option3['sfsi_plus_phone_bgColor'] ) && $option3['sfsi_plus_phone_bgColor'] != '' ) {
						$sfsi_plus_phone_bgColor       = $option3['sfsi_plus_phone_bgColor'];
						$sfsi_plus_phone_bgColor_style = 'background: ' . $sfsi_plus_phone_bgColor;
					} else {
						$sfsi_plus_phone_bgColor_style = 'background: #51AD47';
					}

					if ( isset( $option3['sfsi_plus_vimeo_bgColor'] ) && $option3['sfsi_plus_vimeo_bgColor'] != '' ) {
						$sfsi_plus_vimeo_bgColor       = $option3['sfsi_plus_vimeo_bgColor'];
						$sfsi_plus_vimeo_bgColor_style = 'background: ' . $sfsi_plus_vimeo_bgColor;
					} else {
						$sfsi_plus_vimeo_bgColor_style = 'background: #1AB7EA';
					}

					if ( isset( $option3['sfsi_plus_soundcloud_bgColor'] ) && $option3['sfsi_plus_soundcloud_bgColor'] != '' ) {
						$sfsi_plus_soundcloud_bgColor       = $option3['sfsi_plus_soundcloud_bgColor'];
						$sfsi_plus_soundcloud_bgColor_style = 'background: ' . $sfsi_plus_soundcloud_bgColor;
					} else {
						$sfsi_plus_soundcloud_bgColor_style = 'background: #FF541C';
					}

					if ( isset( $option3['sfsi_plus_yummly_bgColor'] ) && $option3['sfsi_plus_yummly_bgColor'] != '' ) {
						$sfsi_plus_yummly_bgColor       = $option3['sfsi_plus_yummly_bgColor'];
						$sfsi_plus_yummly_bgColor_style = 'background: ' . $sfsi_plus_yummly_bgColor;
					} else {
						$sfsi_plus_yummly_bgColor_style = 'background: #E36308';
					}

					if ( isset( $option3['sfsi_plus_flickr_bgColor'] ) && $option3['sfsi_plus_flickr_bgColor'] != '' ) {
						$sfsi_plus_flickr_bgColor       = $option3['sfsi_plus_flickr_bgColor'];
						$sfsi_plus_flickr_bgColor_style = 'background: ' . $sfsi_plus_flickr_bgColor;
					} else {
						$sfsi_plus_flickr_bgColor_style = 'background: #FF0084';
					}

					if ( isset( $option3['sfsi_plus_reddit_bgColor'] ) && $option3['sfsi_plus_reddit_bgColor'] != '' ) {
						$sfsi_plus_reddit_bgColor       = $option3['sfsi_plus_reddit_bgColor'];
						$sfsi_plus_reddit_bgColor_style = 'background: ' . $sfsi_plus_reddit_bgColor;
					} else {
						$sfsi_plus_reddit_bgColor_style = 'background: #FF642C';
					}

					if ( isset( $option3['sfsi_plus_tumblr_bgColor'] ) && $option3['sfsi_plus_tumblr_bgColor'] != '' ) {
						$sfsi_plus_tumblr_bgColor       = $option3['sfsi_plus_tumblr_bgColor'];
						$sfsi_plus_tumblr_bgColor_style = 'background: ' . $sfsi_plus_tumblr_bgColor;
					} else {
						$sfsi_plus_tumblr_bgColor_style = 'background: #36465F';
					}

					if ( isset( $option3['sfsi_plus_fbmessenger_bgColor'] ) && $option3['sfsi_plus_fbmessenger_bgColor'] != '' ) {
						$sfsi_plus_fbmessenger_bgColor       = $option3['sfsi_plus_fbmessenger_bgColor'];
						$sfsi_plus_fbmessenger_bgColor_style = 'background: ' . $sfsi_plus_fbmessenger_bgColor;
					} else {
						$sfsi_plus_fbmessenger_bgColor_style = 'background: #447BBF';
					}

					if ( isset( $option3['sfsi_plus_gab_bgColor'] ) && $option3['sfsi_plus_gab_bgColor'] != '' ) {
						$sfsi_plus_gab_bgColor       = $option3['sfsi_plus_gab_bgColor'];
						$sfsi_plus_gab_bgColor_style = 'background: ' . $sfsi_plus_gab_bgColor;
					} else {
						$sfsi_plus_gab_bgColor_style = 'background: #25CC80';
					}

					if ( isset( $option3['sfsi_plus_mix_bgColor'] ) && $option3['sfsi_plus_mix_bgColor'] != '' ) {
						$sfsi_plus_mix_bgColor       = $option3['sfsi_plus_mix_bgColor'];
						$sfsi_plus_mix_bgColor_style = 'background: ' . $sfsi_plus_mix_bgColor;
					} else {
						$sfsi_plus_mix_bgColor_style = 'background: conic-gradient(from 180deg at 50% 50%, #DE201D 0deg, #DE201D 117.02deg, #FF8126 117.58deg, #FFA623 230.42deg, #FFD51F 231.6deg, #FFD51F 360deg)';
					}

					if ( isset( $option3['sfsi_plus_ok_bgColor'] ) && $option3['sfsi_plus_ok_bgColor'] != '' ) {
						$sfsi_plus_ok_bgColor       = $option3['sfsi_plus_ok_bgColor'];
						$sfsi_plus_ok_bgColor_style = 'background: ' . $sfsi_plus_ok_bgColor;
					} else {
						$sfsi_plus_ok_bgColor_style = 'background: #F58220';
					}

					if ( isset( $option3['sfsi_plus_telegram_bgColor'] ) && $option3['sfsi_plus_telegram_bgColor'] != '' ) {
						$sfsi_plus_telegram_bgColor       = $option3['sfsi_plus_telegram_bgColor'];
						$sfsi_plus_telegram_bgColor_style = 'background: ' . $sfsi_plus_telegram_bgColor;
					} else {
						$sfsi_plus_telegram_bgColor_style = 'background: #33A1D1';
					}

					if ( isset( $option3['sfsi_plus_vk_bgColor'] ) && $option3['sfsi_plus_vk_bgColor'] != '' ) {
						$sfsi_plus_vk_bgColor       = $option3['sfsi_plus_vk_bgColor'];
						$sfsi_plus_vk_bgColor_style = 'background: ' . $sfsi_plus_vk_bgColor;
					} else {
						$sfsi_plus_vk_bgColor_style = 'background: #4E77A2';
					}

					if ( isset( $option3['sfsi_plus_wechat_bgColor'] ) && $option3['sfsi_plus_wechat_bgColor'] != '' ) {
						$sfsi_plus_wechat_bgColor       = $option3['sfsi_plus_wechat_bgColor'];
						$sfsi_plus_wechat_bgColor_style = 'background: ' . $sfsi_plus_wechat_bgColor;
					} else {
						$sfsi_plus_wechat_bgColor_style = 'background: #4BAD33';
					}

					if ( isset( $option3['sfsi_plus_weibo_bgColor'] ) && $option3['sfsi_plus_weibo_bgColor'] != '' ) {
						$sfsi_plus_weibo_bgColor       = $option3['sfsi_plus_weibo_bgColor'];
						$sfsi_plus_weibo_bgColor_style = 'background: ' . $sfsi_plus_weibo_bgColor;
					} else {
						$sfsi_plus_weibo_bgColor_style = 'background: #4BAD33;';
					}

					if ( isset( $option3['sfsi_plus_xing_bgColor'] ) && $option3['sfsi_plus_xing_bgColor'] != '' ) {
						$sfsi_plus_xing_bgColor       = $option3['sfsi_plus_xing_bgColor'];
						$sfsi_plus_xing_bgColor_style = 'background: ' . $sfsi_plus_xing_bgColor;
					} else {
						$sfsi_plus_xing_bgColor_style = 'background: #005A60';
					}

					if ( isset( $option3['sfsi_plus_copylink_bgColor'] ) && $option3['sfsi_plus_copylink_bgColor'] != '' ) {
						$sfsi_plus_copylink_bgColor       = $option3['sfsi_plus_copylink_bgColor'];
						$sfsi_plus_copylink_bgColor_style = 'background: ' . $sfsi_plus_copylink_bgColor;
					} else {
						$sfsi_plus_copylink_bgColor_style = 'background: linear-gradient(180deg, #C295FF 0%, #4273F7 100%)';
					}

					if ( isset( $option3['sfsi_plus_mastodon_bgColor'] ) && $option3['sfsi_plus_mastodon_bgColor'] != '' ) {
						$sfsi_plus_mastodon_bgColor       = $option3['sfsi_plus_mastodon_bgColor'];
						$sfsi_plus_mastodon_bgColor_style = 'background: ' . $sfsi_plus_mastodon_bgColor;
					} else {
						$sfsi_plus_mastodon_bgColor_style = 'background: #583ED1';
					}
					?>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_2_1 sfsiplus_icon_bgcolor sfsiplus_rss_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_rss_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_rss.png" alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_rss_bgColor" data-default-color="#FF9845" id="sfsi_plus_rss_bgColor"
                                   class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_rss_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_2 sfsiplus_icon_bgcolor sfsiplus_email_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_email_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_email.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_email_bgColor" data-default-color="#343D44"
                                   id="sfsi_plus_email_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_email_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_3 sfsiplus_icon_bgcolor sfsiplus_facebook_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_facebook_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_fb.png" alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_facebook_bgColor" data-default-color="#336699"
                                   id="sfsi_plus_facebook_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_facebook_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_5 sfsiplus_icon_bgcolor sfsiplus_twitter_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_twitter_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_twitter.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_twitter_bgColor" data-default-color="#000000"
                                   id="sfsi_plus_twitter_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_twitter_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_6 sfsiplus_icon_bgcolor sfsiplus_share_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_share_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_share.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_share_bgColor" data-default-color="#26AD62"
                                   id="sfsi_plus_share_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_share_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_7 sfsiplus_icon_bgcolor sfsiplus_youtube_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_youtube_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_youtube.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_youtube_bgColor"
                                   data-default-color-custom="linear-gradient(141.52deg, #E02F2F 14.26%, #E02F2F 48.98%, #C92A2A 49.12%, #C92A2A 85.18%)"
                                   id="sfsi_plus_youtube_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_youtube_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_8 sfsiplus_icon_bgcolor sfsiplus_pinterest_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_pinterest_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_pinterest.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_pinterest_bgColor" data-default-color="#CC3333"
                                   id="sfsi_plus_pinterest_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_pinterest_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_9 sfsiplus_icon_bgcolor sfsiplus_linkedin_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_linkedin_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_linkedin.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_linkedin_bgColor" data-default-color="#0877B5"
                                   id="sfsi_plus_linkedin_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_linkedin_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_10 sfsiplus_icon_bgcolor sfsiplus_instagram_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_instagram_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_instagram.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_instagram_bgColor" data-default-color="#336699"
                                   id="sfsi_plus_instagram_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_instagram_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_35 sfsiplus_icon_bgcolor sfsiplus_threads_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_threads_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_threads.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_threads_bgColor" data-default-color="#252525"
                                   id="sfsi_plus_threads_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_threads_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_33 sfsiplus_icon_bgcolor sfsiplus_ria_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_ria_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_ria.png" alt=""
                                     style="width: 35px"/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_ria_bgColor" data-default-color="#10A9A0" id="sfsi_plus_ria_bgColor"
                                   class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_ria_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_34 sfsiplus_icon_bgcolor sfsiplus_inha_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_inha_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_inha.png" alt=""
                                     style="width: 35px"/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_inha_bgColor" data-default-color="#348CBC"
                                   id="sfsi_plus_inha_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_inha_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_11 sfsiplus_icon_bgcolor sfsiplus_houzz_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_houzz_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_houzz.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_houzz_bgColor" data-default-color="#7BC043"
                                   id="sfsi_plus_houzz_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_houzz_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_12 sfsiplus_icon_bgcolor sfsiplus_snapchat_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_snapchat_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_snapchat.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_snapchat_bgColor" data-default-color="#EDEC1F"
                                   id="sfsi_plus_snapchat_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_snapchat_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_13 sfsiplus_icon_bgcolor sfsiplus_whatsapp_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_whatsapp_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_whatsapp.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_whatsapp_bgColor" data-default-color="#3ED946"
                                   id="sfsi_plus_whatsapp_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_whatsapp_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_14 sfsiplus_icon_bgcolor sfsiplus_skype_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_skype_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_skype.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_skype_bgColor" data-default-color="#00A9F1"
                                   id="sfsi_plus_skype_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_skype_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_28 sfsiplus_icon_bgcolor sfsiplus_phone_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_phone_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_phone.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_phone_bgColor" data-default-color="#51AD47"
                                   id="sfsi_plus_phone_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_phone_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_15 sfsiplus_icon_bgcolor sfsiplus_vimeo_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_vimeo_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_vimeo.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_vimeo_bgColor" data-default-color="#1AB7EA"
                                   id="sfsi_plus_vimeo_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_vimeo_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_16 sfsiplus_icon_bgcolor sfsiplus_soundcloud_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_soundcloud_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_soundcloud.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_soundcloud_bgColor" data-default-color="#FF541C"
                                   id="sfsi_plus_soundcloud_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_soundcloud_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_17 sfsiplus_icon_bgcolor sfsiplus_yummly_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_yummly_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_yummly.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_yummly_bgColor" data-default-color="#E36308"
                                   id="sfsi_plus_yummly_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_yummly_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_18 sfsiplus_icon_bgcolor sfsiplus_flickr_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_flickr_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_flickr.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_flickr_bgColor" data-default-color="#FF0084"
                                   id="sfsi_plus_flickr_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_flickr_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_19 sfsiplus_icon_bgcolor sfsiplus_reddit_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_reddit_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_reddit.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_reddit_bgColor" data-default-color="#FF642C"
                                   id="sfsi_plus_reddit_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_reddit_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_20 sfsiplus_icon_bgcolor sfsiplus_tumblr_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_tumblr_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_tumblr.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_tumblr_bgColor" data-default-color="#36465F"
                                   id="sfsi_plus_tumblr_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_tumblr_bgColor ); ?>"/>
                            </span>
                        </span>

                        <span class="sfsiplus_row_2_21 sfsiplus_icon_bgcolor sfsiplus_fbmessenger_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_fbmessenger_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_fbmessenger.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_fbmessenger_bgColor" data-default-color="#447BBF"
                                   id="sfsi_plus_fbmessenger_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_fbmessenger_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_30 sfsiplus_icon_bgcolor sfsiplus_gab_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_gab_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_gab.png" alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_gab_bgColor" data-default-color="#25CC80" id="sfsi_plus_gab_bgColor"
                                   class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_gab_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_22 sfsiplus_icon_bgcolor sfsiplus_mix_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_mix_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_mix.png" alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_mix_bgColor"
                                   data-default-color-custom="conic-gradient(from 180deg at 50% 50%, #DE201D 0deg, #DE201D 117.02deg, #FF8126 117.58deg, #FFA623 230.42deg, #FFD51F 231.6deg, #FFD51F 360deg)"
                                   id="sfsi_plus_mix_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_mix_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_23 sfsiplus_icon_bgcolor sfsiplus_ok_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_ok_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_ok.png" alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_ok_bgColor" data-default-color="#F58220" id="sfsi_plus_ok_bgColor"
                                   class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_ok_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_24 sfsiplus_icon_bgcolor sfsiplus_telegram_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_telegram_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_telegram.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_telegram_bgColor" data-default-color="#33A1D1"
                                   id="sfsi_plus_telegram_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_telegram_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_25 sfsiplus_icon_bgcolor sfsiplus_vk_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_vk_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_vk.png" alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_vk_bgColor" data-default-color="#4E77A2" id="sfsi_plus_vk_bgColor"
                                   class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_vk_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_36 sfsiplus_icon_bgcolor sfsiplus_bluesky_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_bluesky_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_bluesky.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_bluesky_bgColor" data-default-color="#1185fe"
                                   id="sfsi_plus_bluesky_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_bluesky_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_29 sfsiplus_icon_bgcolor sfsiplus_wechat_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_wechat_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_wechat.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_wechat_bgColor" data-default-color="#4BAD33"
                                   id="sfsi_plus_wechat_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_wechat_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_26 sfsiplus_icon_bgcolor sfsiplus_weibo_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_weibo_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_weibo.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_weibo_bgColor" data-default-color="#E6162D"
                                   id="sfsi_plus_weibo_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_weibo_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_27 sfsiplus_icon_bgcolor sfsiplus_xing_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_xing_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_xing.png" alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_xing_bgColor" data-default-color="#005A60"
                                   id="sfsi_plus_xing_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_xing_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_31 sfsiplus_icon_bgcolor sfsiplus_copylink_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_copylink_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_copylink.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_copylink_bgColor"
                                   data-default-color-custom="linear-gradient(180deg, #C295FF 0%, #4273F7 100%)"
                                   id="sfsi_plus_copylink_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_copylink_bgColor ); ?>"/>
                            </span>
                        </span>
                        <span class="sfsiplus_row_2_32 sfsiplus_icon_bgcolor sfsiplus_mastodon_section">
                            <span class="sfsiplus_icon_img_wrapper"
                                  style="<?php echo esc_attr( $sfsi_plus_mastodon_bgColor_style ); ?>">
                                <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/icons_theme/flat/flat_mastodon.png"
                                     alt=""/>
                            </span>
                            <span class="sfsiplus_icon_color_picker">
                            <input name="sfsi_plus_mastodon_bgColor" data-default-color-custom="#583ED1"
                                   id="sfsi_plus_mastodon_bgColor" class="sfsi_plus_input_bgColor" type="text"
                                   value="<?php echo esc_attr( $sfsi_plus_mastodon_bgColor ); ?>"/>
                            </span>
                        </span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'thin' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="thin" class="styled"/>
                    <label>
						<?php _e( 'Thin', 'ultimate-social-media-plus' ); ?>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_3_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_3_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_3_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_3_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_3_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_3_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_3_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_3_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_3_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_3_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_3_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_3_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_3_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_3_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_3_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_3_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_3_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_3_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_3_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_3_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_3_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_3_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_3_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_3_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_3_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_3_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_3_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_3_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_3_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_3_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_3_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_3_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_3_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_3_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_3_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'cute' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="cute" class="styled"/>
                    <label>
						<?php _e( 'Cute', 'ultimate-social-media-plus' ); ?>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_4_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_4_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_4_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_4_5  sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_4_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_4_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_4_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_4_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_4_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_4_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_4_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_4_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_4_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_4_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_4_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_4_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_4_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_4_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_4_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_4_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_4_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_4_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_4_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_4_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_4_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_4_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_4_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_4_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_4_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_4_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_4_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_4_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_4_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_4_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_4_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>
                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'cubes' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="cubes" class="styled"/>
                    <label><?php _e( 'Cubes', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_5_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_5_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_5_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_5_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_5_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_5_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_5_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_5_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_5_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_5_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_5_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_5_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_5_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_5_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_5_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_5_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_5_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_5_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_5_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_5_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_5_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_5_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_5_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_5_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_5_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_5_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_5_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_5_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_5_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_5_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_5_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_5_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_5_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_5_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_5_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'chrome_blue' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="chrome_blue" class="styled"/>
                    <label><?php _e( 'Chrome Blue', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_6_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_6_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_6_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_6_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_6_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_6_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_6_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_6_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_6_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_6_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_6_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_6_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_6_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_6_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_6_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_6_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_6_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_6_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_6_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_6_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_6_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_6_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_6_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_6_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_6_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_6_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_6_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_6_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_6_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_6_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_6_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_6_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_6_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_6_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_6_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'chrome_grey' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="chrome_grey" class="styled"/>
                    <label><?php _e( 'Chrome Grey', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_7_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_7_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_7_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_7_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_7_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_7_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_7_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_7_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_7_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_7_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_7_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_7_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_7_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_7_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_7_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_7_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_7_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_7_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_7_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_7_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_7_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_7_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_7_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_7_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_7_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_7_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_7_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_7_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_7_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_7_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_7_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_7_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_7_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_7_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_7_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'splash' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="splash" class="styled"/>
                    <label><?php _e( 'Splash', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_8_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_8_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_8_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_8_5  sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_8_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_8_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_8_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_8_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_8_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_8_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_8_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_8_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_8_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_8_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_8_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_8_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_8_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_8_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_8_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_8_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_8_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_8_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_8_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_8_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_8_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_8_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_8_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_8_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_8_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_8_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_8_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_8_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_8_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_8_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_8_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'orange' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="orange" class="styled"/>
                    <label><?php _e( 'Orange', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_9_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_9_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_9_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_9_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_9_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_9_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_9_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_9_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_9_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_9_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_9_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_9_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_9_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_9_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_9_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_9_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_9_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_9_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_9_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_9_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_9_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_9_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_9_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_9_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_9_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_9_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_9_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_9_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_9_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_9_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_9_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_9_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_9_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_9_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_9_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'crystal' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="crystal" class="styled"/>
                    <label><?php _e( 'Crystal', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_10_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_10_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_10_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_10_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_10_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_10_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_10_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_10_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_10_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_10_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_10_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_10_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_10_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_10_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_10_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_10_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_10_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_10_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_10_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_10_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_10_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_10_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_10_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_10_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_10_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_10_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_10_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_10_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_10_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_10_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_10_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_10_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_10_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_10_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_10_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'glossy' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="glossy" class="styled"/>
                    <label><?php _e( 'Glossy', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_11_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_11_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_11_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_11_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_11_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_11_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_11_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_11_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_11_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_11_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_11_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_11_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_11_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_11_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_11_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_11_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_11_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_11_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_11_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_11_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_11_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_11_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_11_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_11_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_11_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_11_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_11_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_11_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_11_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_11_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_11_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_11_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_11_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_11_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_11_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'black' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="black" class="styled"/>
                    <label><?php _e( 'Black', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_12_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_12_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_12_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_12_5  sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_12_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_12_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_12_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_12_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_12_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_12_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_12_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_12_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_12_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_12_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_12_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_12_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_12_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_12_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_12_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_12_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_12_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_12_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_12_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_12_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_12_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_12_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_12_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_12_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_12_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_12_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_12_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_12_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_12_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_12_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_12_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'silver' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="silver" class="styled"/>
                    <label><?php _e( 'Silver', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_13_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_13_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_13_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_13_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_13_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_13_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_13_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_13_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_13_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_13_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_13_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_13_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_13_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_13_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_13_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_13_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_13_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_13_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_13_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_13_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_13_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_13_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_13_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_13_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_13_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_13_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_13_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_13_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_13_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_13_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_13_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_13_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_13_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_13_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_13_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'shaded_dark' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="shaded_dark" class="styled"/>
                    <label><?php _e( 'Shaded Dark', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_14_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_14_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_14_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_14_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_14_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_14_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_14_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_14_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_14_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_14_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_14_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_14_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_14_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_14_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_14_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_14_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_14_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_14_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_14_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_14_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_14_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_14_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_14_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_14_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_14_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_14_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_14_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_14_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_14_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_14_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_14_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_14_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_14_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_14_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_14_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'shaded_light' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="shaded_light" class="styled"/>
                    <label><?php _e( 'Shaded Light', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_15_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_15_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_15_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_15_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_15_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_15_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_15_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_15_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_15_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_15_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_15_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_15_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_15_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_15_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_15_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_15_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_15_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_15_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_15_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_15_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_15_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_15_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_15_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_15_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_15_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_15_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_15_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_15_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_15_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_15_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_15_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_15_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_15_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_15_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_15_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'cool' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="cool" class="styled"/>
                    <label><?php _e( 'Cool', 'ultimate-social-media-plus' ); ?></label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_23_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_23_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_23_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_23_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_23_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_23_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_23_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_23_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_23_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_23_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_23_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_23_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_23_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_23_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_23_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_23_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_23_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_23_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_23_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_23_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_23_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_23_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_23_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_23_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_23_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_23_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_23_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_23_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_23_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_23_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_23_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_23_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_23_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_23_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_23_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'transparent' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="transparent" class="styled"/>
                    <label style="line-height:20px !important;margin-top:15px;  ">
						<?php _e( 'Transparent', 'ultimate-social-media-plus' ); ?> <br/>
                        <span style="font-size: 9px;">(<?php _e( 'for dark backgrounds', 'ultimate-social-media-plus' ) ?>)</span>
                    </label>
                    <div class="sfsiplus_icns_tab_3 trans_bg">
                        <span class="sfsiplus_row_16_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_16_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_16_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_16_5  sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_16_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_16_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_16_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_16_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_16_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_16_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_16_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_16_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_16_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_16_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_16_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_16_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_16_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_16_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_16_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_16_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_16_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_16_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_16_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_16_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_16_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_16_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_16_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_16_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_16_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_16_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_16_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_16_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_16_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_16_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_16_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'yellow' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="yellow" class="styled"/>
                    <label style="line-height:20px !important;margin-top:15px;  ">
						<?php _e( 'Yellow', 'ultimate-social-media-plus' ); ?> <br/>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_18_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_18_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_18_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_18_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_18_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_18_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_18_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_18_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_18_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_18_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_18_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_18_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_18_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_18_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_18_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_18_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_18_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_18_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_18_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_18_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_18_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_18_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_18_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_18_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_18_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_18_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_18_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_18_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_18_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_18_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_18_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_18_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_18_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_18_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_18_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'glossyblack' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="glossyblack" class="styled"/>
                    <label style="line-height:20px !important;margin-top:15px;  ">
						<?php _e( 'Glossy Black', 'ultimate-social-media-plus' ); ?> <br/>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_19_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_19_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_19_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_19_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_19_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_19_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_19_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_19_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_19_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_19_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_19_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_19_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_19_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_19_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_19_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_19_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_19_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_19_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_19_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_19_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_19_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_19_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_19_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_19_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_19_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_19_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_19_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_19_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_19_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_19_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_19_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_19_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_19_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_19_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_19_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'blackgrunge' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="blackgrunge" class="styled"/>
                    <label style="line-height:20px !important;margin-top:15px;  ">
						<?php _e( 'Black Grunge', 'ultimate-social-media-plus' ); ?> <br/>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_20_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_20_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_20_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_20_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_20_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_20_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_20_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_20_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_20_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_20_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_20_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_20_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_20_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_20_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_20_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_20_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_20_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_20_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_20_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_20_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_20_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_20_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_20_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_20_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_20_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_20_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_20_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_20_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_20_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_20_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_20_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_20_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_20_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_20_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_20_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'waxedwood' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="waxedwood" class="styled"/>
                    <label style="line-height:20px !important;margin-top:15px;  ">
						<?php _e( 'Waxed Wood', 'ultimate-social-media-plus' ); ?> <br/>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_21_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_21_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_21_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_21_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_21_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_21_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_21_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_21_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_21_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_21_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_21_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_21_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_21_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_21_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_21_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_21_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_21_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_21_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_21_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_21_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_21_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_21_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_21_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_21_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_21_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_21_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_21_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_21_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_21_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_21_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_21_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_21_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_21_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_21_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_21_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <li>
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'black2' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="black2" class="styled"/>
                    <label style="line-height:20px !important;margin-top:15px;">
						<?php _e( 'Black 2', 'ultimate-social-media-plus' ); ?> <br/>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
                        <span class="sfsiplus_row_22_1 sfsiplus_rss_section"></span>
                        <span class="sfsiplus_row_22_2 sfsiplus_email_section"></span>
                        <span class="sfsiplus_row_22_3 sfsiplus_facebook_section"></span>
                        <span class="sfsiplus_row_22_5 sfsiplus_twitter_section"></span>
                        <span class="sfsiplus_row_22_6 sfsiplus_share_section"></span>
                        <span class="sfsiplus_row_22_7 sfsiplus_youtube_section"></span>
                        <span class="sfsiplus_row_22_8 sfsiplus_pinterest_section"></span>
                        <span class="sfsiplus_row_22_9 sfsiplus_linkedin_section"></span>
                        <span class="sfsiplus_row_22_10 sfsiplus_instagram_section"></span>
                        <span class="sfsiplus_row_22_35 sfsiplus_threads_section"></span>
                        <span class="sfsiplus_row_22_33 sfsiplus_ria_section"></span>
                        <span class="sfsiplus_row_22_34 sfsiplus_inha_section"></span>
                        <span class="sfsiplus_row_22_11 sfsiplus_houzz_section"></span>
                        <span class="sfsiplus_row_22_12 sfsiplus_snapchat_section"></span>
                        <span class="sfsiplus_row_22_13 sfsiplus_whatsapp_section"></span>
                        <span class="sfsiplus_row_22_14 sfsiplus_skype_section"></span>
                        <span class="sfsiplus_row_22_28 sfsiplus_phone_section"></span>
                        <span class="sfsiplus_row_22_15 sfsiplus_vimeo_section"></span>
                        <span class="sfsiplus_row_22_16 sfsiplus_soundcloud_section"></span>
                        <span class="sfsiplus_row_22_17 sfsiplus_yummly_section"></span>
                        <span class="sfsiplus_row_22_18 sfsiplus_flickr_section"></span>
                        <span class="sfsiplus_row_22_19 sfsiplus_reddit_section"></span>
                        <span class="sfsiplus_row_22_20 sfsiplus_tumblr_section"></span>
                        <span class="sfsiplus_row_22_21 sfsiplus_fbmessenger_section"></span>
                        <span class="sfsiplus_row_22_30 sfsiplus_gab_section"></span>
                        <span class="sfsiplus_row_22_22 sfsiplus_mix_section"></span>
                        <span class="sfsiplus_row_22_23 sfsiplus_ok_section"></span>
                        <span class="sfsiplus_row_22_24 sfsiplus_telegram_section"></span>
                        <span class="sfsiplus_row_22_25 sfsiplus_vk_section"></span>
                        <span class="sfsiplus_row_22_36 sfsiplus_bluesky_section"></span>
                        <span class="sfsiplus_row_22_29 sfsiplus_wechat_section"></span>
                        <span class="sfsiplus_row_22_26 sfsiplus_weibo_section"></span>
                        <span class="sfsiplus_row_22_27 sfsiplus_xing_section"></span>
                        <span class="sfsiplus_row_22_31 sfsiplus_copylink_section"></span>
                        <span class="sfsiplus_row_22_32 sfsiplus_mastodon_section"></span>
                    </div>
                </li>

                <!--Custom Icon Support {Monad}-->
                <li class="cstomskins_upload">
                    <input name="sfsi_plus_actvite_theme" <?php echo ( $sfsi_plus_actvite_theme == 'custom_support' ) ? 'checked="true"' : ''; ?>
                           type="radio" value="custom_support" class="styled"/>
                    <label style="line-height:20px !important;margin-top:15px;">
						<?php _e( 'Custom Icons', 'ultimate-social-media-plus' ); ?>
                        <br/>
                    </label>
                    <div class="sfsiplus_icns_tab_3">
						<?php
						$plus_rss_skin = get_option( "plus_rss_skin" );
						if ( $plus_rss_skin ) {
							echo '<span class="sfsiplus_row_17_1 sfsiplus_rss_section sfsi_plus-bgimage" style="background: url(' . $plus_rss_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_1 sfsiplus_rss_section" style="background-position:-3px 6px;"></span>';
						}

						$plus_email_skin = get_option( "plus_email_skin" );
						if ( $plus_email_skin ) {
							echo '<span class="sfsiplus_row_17_2 sfsiplus_email_section sfsi_plus-bgimage" style="background: url(' . $plus_email_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_2 sfsiplus_email_section" style="background-position:-51px 6px;"></span>';
						}

						$plus_facebook_skin = get_option( "plus_facebook_skin" );
						if ( $plus_facebook_skin ) {
							echo '<span class="sfsiplus_row_17_3 sfsiplus_facebook_section sfsi_plus-bgimage" style="background: url(' . $plus_facebook_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_3 sfsiplus_facebook_section" style="background-position:-98px 6px;"></span>';
						}

						$plus_twitter_skin = get_option( "plus_twitter_skin" );
						if ( $plus_twitter_skin ) {
							echo '<span class="sfsiplus_row_17_5 sfsiplus_twitter_section sfsi_plus-bgimage" style="background: url(' . $plus_twitter_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_5 sfsiplus_twitter_section" style="background-position:-192px 6px;"></span>';
						}

						$plus_share_skin = get_option( "plus_share_skin" );
						if ( $plus_share_skin ) {
							echo '<span class="sfsiplus_row_17_6 sfsiplus_share_section sfsi_plus-bgimage" style="background: url(' . $plus_share_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_6 sfsiplus_share_section" style="background-position:-238px 6px;"></span>';
						}

						$plus_youtube_skin = get_option( "plus_youtube_skin" );
						if ( $plus_youtube_skin ) {
							echo '<span class="sfsiplus_row_17_7 sfsiplus_youtube_section sfsi_plus-bgimage" style="background: url(' . $plus_youtube_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_7 sfsiplus_youtube_section" style="background-position:-285px 6px;"></span>';
						}

						$plus_pintrest_skin = get_option( "plus_pintrest_skin" );
						if ( $plus_pintrest_skin ) {
							echo '<span class="sfsiplus_row_17_8 sfsiplus_pinterest_section sfsi_plus-bgimage" style="background: url(' . $plus_pintrest_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_8 sfsiplus_pinterest_section" style="background-position:-332px 6px;"></span>';
						}

						$plus_linkedin_skin = get_option( "plus_linkedin_skin" );
						if ( $plus_linkedin_skin ) {
							echo '<span class="sfsiplus_row_17_9 sfsiplus_linkedin_section sfsi_plus-bgimage" style="background: url(' . $plus_linkedin_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_9 sfsiplus_linkedin_section" style="background-position:-379px 6px;"></span>';
						}

						$plus_instagram_skin = get_option( "plus_instagram_skin" );
						if ( $plus_instagram_skin ) {
							echo '<span class="sfsiplus_row_17_10 sfsiplus_instagram_section sfsi_plus-bgimage" style="background: url(' . $plus_instagram_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_10 sfsiplus_instagram_section" style="background-position:-426px 6px;"></span>';
						}

						$plus_threads_skin = get_option( "plus_threads_skin" );
						if ( $plus_threads_skin ) {
							echo '<span class="sfsiplus_row_17_35 sfsiplus_threads_section sfsi_plus-bgimage" style="background: url(' . $plus_threads_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_35 sfsiplus_threads_section"></span>';
						}

						$plus_ria_skin = get_option( "plus_ria_skin" );
						if ( $plus_ria_skin ) {
							echo '<span class="sfsiplus_row_17_33 sfsiplus_ria_section sfsi_plus-bgimage" style="background: url(' . $plus_ria_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_33 sfsiplus_ria_section" style="background-position:-1457px 6px;"></span>';
						}

						$plus_inha_skin = get_option( "plus_inha_skin" );
						if ( $plus_inha_skin ) {
							echo '<span class="sfsiplus_row_17_34 sfsiplus_inha_section sfsi_plus-bgimage" style="background: url(' . $plus_inha_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_34 sfsiplus_inha_section" style="background-position:-1503px 6px;"></span>';
						}

						$plus_houzz_skin = get_option( "plus_houzz_skin" );
						if ( $plus_houzz_skin ) {
							echo '<span class="sfsiplus_row_17_11 sfsiplus_houzz_section sfsi_plus-bgimage" style="background: url(' . $plus_houzz_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_11 sfsiplus_houzz_section" style="background-position:-566px 6px;"></span>';
						}

						$plus_snapchat_skin = get_option( "plus_snapchat_skin" );
						if ( $plus_snapchat_skin ) {
							echo '<span class="sfsiplus_row_17_12 sfsiplus_snapchat_section sfsi_plus-bgimage" style="background: url(' . $plus_snapchat_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_12 sfsiplus_snapchat_section" style="background-position:-613px 6px;"></span>';
						}

						$plus_whatsapp_skin = get_option( "plus_whatsapp_skin" );
						if ( $plus_whatsapp_skin ) {
							echo '<span class="sfsiplus_row_17_13 sfsiplus_whatsapp_section sfsi_plus-bgimage" style="background: url(' . $plus_whatsapp_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_13 sfsiplus_whatsapp_section" style="background-position:-660px 6px;"></span>';
						}

						$plus_skype_skin = get_option( "plus_skype_skin" );
						if ( $plus_skype_skin ) {
							echo '<span class="sfsiplus_row_17_14 sfsiplus_skype_section sfsi_plus-bgimage" style="background: url(' . $plus_skype_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_14 sfsiplus_skype_section" style="background-position:-706px 6px;"></span>';
						}

						$plus_phone_skin = get_option( "plus_phone_skin" );
						if ( $plus_phone_skin ) {
							echo '<span class="sfsiplus_row_17_28 sfsiplus_phone_section sfsi_plus-bgimage" style="background: url(' . $plus_phone_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_28 sfsiplus_phone_section" style="background-position:-660px 6px;"></span>';
						}

						$plus_vimeo_skin = get_option( "plus_vimeo_skin" );
						if ( $plus_vimeo_skin ) {
							echo '<span class="sfsiplus_row_17_15 sfsiplus_vimeo_section sfsi_plus-bgimage" style="background: url(' . $plus_vimeo_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_15 sfsiplus_vimeo_section" style="background-position:-752px 6px;"></span>';
						}

						$plus_soundcloud_skin = get_option( "plus_soundcloud_skin" );
						if ( $plus_soundcloud_skin ) {
							echo '<span class="sfsiplus_row_17_16 sfsiplus_soundcloud_section sfsi_plus-bgimage" style="background: url(' . $plus_soundcloud_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_16 sfsiplus_soundcloud_section" style="background-position:-799px 6px;"></span>';
						}

						$plus_yummly_skin = get_option( "plus_yummly_skin" );
						if ( $plus_yummly_skin ) {
							echo '<span class="sfsiplus_row_17_17 sfsiplus_yummly_section sfsi_plus-bgimage" style="background: url(' . $plus_yummly_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_17 sfsiplus_yummly_section" style="background-position:-845px 6px;"></span>';
						}

						$plus_flickr_skin = get_option( "plus_flickr_skin" );
						if ( $plus_flickr_skin ) {
							echo '<span class="sfsiplus_row_17_18 sfsiplus_flickr_section sfsi_plus-bgimage" style="background: url(' . $plus_flickr_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_18 sfsiplus_flickr_section" style="background-position:-892px 6px;"></span>';
						}

						$plus_reddit_skin = get_option( "plus_reddit_skin" );
						if ( $plus_reddit_skin ) {
							echo '<span class="sfsiplus_row_17_19 sfsiplus_reddit_section sfsi_plus-bgimage" style="background: url(' . $plus_reddit_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_19 sfsiplus_reddit_section" style="background-position:-940px 6px;"></span>';
						}

						$plus_tumblr_skin = get_option( "plus_tumblr_skin" );
						if ( $plus_tumblr_skin ) {
							echo '<span class="sfsiplus_row_17_20 sfsiplus_tumblr_section sfsi_plus-bgimage" style="background: url(' . $plus_tumblr_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_20 sfsiplus_tumblr_section" style="background-position:-986px 6px;"></span>';
						}

						$plus_fbmessenger_skin = get_option( "plus_fbmessenger_skin" );
						if ( $plus_fbmessenger_skin ) {
							echo '<span class="sfsiplus_row_17_21 sfsiplus_fbmessenger_section sfsi_plus-bgimage" style="background: url(' . $plus_fbmessenger_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_21 sfsiplus_fbmessenger_section" style="background-position:-1038px 6px;"></span>';
						}

						$plus_gab_skin = get_option( "plus_gab_skin" );
						if ( $plus_gab_skin ) {
							echo '<span class="sfsiplus_row_17_30 sfsiplus_gab_section sfsi_plus-bgimage" style="background: url(' . $plus_gab_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_30 sfsiplus_gab_section" style="background-position:-1362px 6px;"></span>';
						}

						$plus_mix_skin = get_option( "plus_mix_skin" );
						if ( $plus_mix_skin ) {
							echo '<span class="sfsiplus_row_17_22 sfsiplus_mix_section sfsi_plus-bgimage" style="background: url(' . $plus_mix_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_22 sfsiplus_mix_section"></span>';
						}

						$plus_ok_skin = get_option( "plus_ok_skin" );
						if ( $plus_ok_skin ) {
							echo '<span class="sfsiplus_row_17_23 sfsiplus_ok_section sfsi_plus-bgimage" style="background: url(' . $plus_ok_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_23 sfsiplus_ok_section"></span>';
						}

						$plus_telegram_skin = get_option( "plus_telegram_skin" );
						if ( $plus_telegram_skin ) {
							echo '<span class="sfsiplus_row_17_24 sfsiplus_telegram_section sfsi_plus-bgimage" style="background: url(' . $plus_telegram_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_24 sfsiplus_telegram_section"></span>';
						}

						$plus_vk_skin = get_option( "plus_vk_skin" );
						if ( $plus_vk_skin ) {
							echo '<span class="sfsiplus_row_17_25 sfsiplus_vk_section sfsi_plus-bgimage" style="background: url(' . $plus_vk_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_25 sfsiplus_vk_section"></span>';
						}

						$plus_bluesky_skin = get_option( "plus_bluesky_skin" );
						if ( $plus_bluesky_skin ) {
							echo '<span class="sfsiplus_row_17_36 sfsiplus_bluesky_section sfsi_plus-bgimage" style="background: url(' . $plus_bluesky_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_36 sfsiplus_bluesky_section"></span>';
						}

						$plus_wechat_skin = get_option( "plus_wechat_skin" );
						if ( $plus_wechat_skin ) {
							echo '<span class="sfsiplus_row_17_29 sfsiplus_wechat_section sfsi_plus-bgimage" style="background: url(' . $plus_wechat_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_29 sfsiplus_wechat_section"></span>';
						}

						$plus_weibo_skin = get_option( "plus_weibo_skin" );
						if ( $plus_weibo_skin ) {
							echo '<span class="sfsiplus_row_17_26 sfsiplus_weibo_section sfsi_plus-bgimage" style="background: url(' . $plus_weibo_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_26 sfsiplus_weibo_section"></span>';
						}

						$plus_xing_skin = get_option( "plus_xing_skin" );
						if ( $plus_xing_skin ) {
							echo '<span class="sfsiplus_row_17_27 sfsiplus_xing_section sfsi_plus-bgimage" style="background: url(' . $plus_xing_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_27 sfsiplus_xing_section"></span>';
						}

						$plus_copylink_skin = get_option( "plus_copylink_skin" );
						if ( $plus_copylink_skin ) {
							echo '<span class="sfsiplus_row_17_31 sfsiplus_copylink_section sfsi_plus-bgimage" style="background: url(' . $plus_copylink_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_31 sfsiplus_copylink_section"></span>';
						}

						$plus_mastodon_skin = get_option( "plus_mastodon_skin" );
						if ( $plus_mastodon_skin ) {
							echo '<span class="sfsiplus_row_17_32 sfsiplus_mastodon_section sfsi_plus-bgimage" style="background: url(' . $plus_mastodon_skin . ') no-repeat;"></span>';
						} else {
							echo '<span class="sfsiplus_row_17_32 sfsiplus_mastodon_section"></span>';
						}
						?>
                    </div>
                </li>
            </ul>
            <!--icon themes section start -->

            <!--icon Animation section   start -->
            <div class="sfsi_premium_themeSection" style="margin-left: 0px;">

                <p>
					<?php
					printf(
						__( 'If you are interested in %1$sthemed icons%2$s or %3$sanimated icons%4$s, please %5$scontact us%6$s and let us know which specific theme of icons you want. We will then send it to you.%7$s We didn\'t include all the themed icons here as it would blow up the size of the plugin.%8$s', 'ultimate-social-media-plus' ),
						'<a href="https://www.ultimatelysocial.com/themed-icons-search/" target="_blank" style="text-decoration: underline;">',
						'</a>',
						'<a href="https://www.ultimatelysocial.com/animated-social-media-icons/" target="_blank" style="text-decoration: underline;">',
						'</a>',
						'<a style="text-decoration: underline;" href="' . License_Manager::supportLink() . '" target="_blank" class="lit_txt">',
						'</a>',
						'<b>',
						'</b>'
					);
					?>
                </p>

                <!--<h3>
                    <?php //_e('Theme & topic icons', 'ultimate-social-media-plus' ); ?>
                </h3>

                <p><?php //_e('We also offer icons which match certain website themes', 'ultimate-social-media-plus' ); ?>, e.g.:</p>

                <div class="sfsi_premium_themedicon">
                    <img src="<?php //echo SFSI_PLUS_PLUGURL."/images/themed-icons.png"; ?>" />
                </div>

                <p><?php //_e('To see which icons are currently available and how to use them', 'ultimate-social-media-plus' ); ?>: <a href="https://www.ultimatelysocial.com/themed-icons/" target="_blank"><b><?php //_e('Click here', 'ultimate-social-media-plus' ); ?></b></a></p>-->

            </div>

            <!--icon Animation section   start -->
            <div class="sub_row stand sec_new" style="margin-left: 0px;">

                <h3>
					<?php _e( 'Animate them (your main icons)', 'ultimate-social-media-plus' ); ?>
                </h3>

                <div id="animationSection" class="radio_section tab_3_option">

                    <input name="sfsi_plus_mouseOver" <?php echo ( $sfsi_plus_mouseOver == 'yes' ) ? 'checked="true"' : ''; ?>
                           type="checkbox" value="yes" class="styled"/>
                    <label>
						<?php _e( 'Mouse-Over effects', 'ultimate-social-media-plus' ); ?>
                    </label>

                    <div class="col-md-12 rowmarginleft45 mouse-over-effects <?php echo ( $sfsi_plus_mouseOver == 'yes' ) ? 'show' : 'hide'; ?>">

                        <div class="row sfsi-flex-align-item-center">

                            <input value="same_icons"
                                   name="sfsi_plus_mouseOver_effect_type" <?php echo ( $sfsi_plus_mouseOver_effect_type == 'same_icons' ) ? 'checked=checked' : ''; ?>
                                   type="radio" class="styled"/>
                            <label><?php _e( 'Same-icon effects', 'ultimate-social-media-plus' ); ?></label>

                        </div>

                        <div class="row rowpadding10 rowmarginleft45 same_icons_effects <?php echo ( $sfsi_plus_mouseOver_effect_type == 'same_icons' ) ? 'show' : 'hide'; ?>">

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3">

                                        <input class="styled" type="radio" name="sfsi_plus_same_icons_mouseOver_effect"
                                               value="fade_in" <?php echo ( $sfsi_plus_mouseOver_effect == 'fade_in' ) ? 'checked="true"' : ''; ?>>

                                        <label>
                                            <span><?php _e( 'Fade In', 'ultimate-social-media-plus' ); ?></span>
                                            <span><?php _e( '(Icons turn from shadow to full color)', 'ultimate-social-media-plus' ); ?></span>
                                        </label>

                                    </div>

                                    <div class="col-md-3">

                                        <input class="styled" type="radio" name="sfsi_plus_same_icons_mouseOver_effect"
                                               value="fade_out" <?php echo ( $sfsi_plus_mouseOver_effect == 'fade_out' ) ? 'checked="true"' : ''; ?>>

                                        <label>
                                            <span><?php _e( 'Fade Out', 'ultimate-social-media-plus' ); ?></span>
                                            <span><?php _e( '(Icons turn from full color to shadow)', 'ultimate-social-media-plus' ); ?></span>
                                        </label>

                                    </div>

                                    <div class="col-md-3">

                                        <input class="styled" type="radio" name="sfsi_plus_same_icons_mouseOver_effect"
                                               value="scale" <?php echo ( $sfsi_plus_mouseOver_effect == 'scale' ) ? 'checked="true"' : ''; ?>>

                                        <label>
                                            <span> <?php _e( 'Scale', 'ultimate-social-media-plus' ); ?></span>
                                            <span><?php _e( '(Icons become bigger)', 'ultimate-social-media-plus' ); ?></span>
                                        </label>

                                    </div>
                                </div>

                            </div><!-- row closes -->

                            <div class="col-md-12 topmargin40">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input class="styled" type="radio" name="sfsi_plus_same_icons_mouseOver_effect"
                                               value="combo" <?php echo ( $sfsi_plus_mouseOver_effect == 'combo' ) ? 'checked="true"' : ''; ?>>
                                        <label>
                                            <span><?php _e( 'Combo (Fade In , Scale)', 'ultimate-social-media-plus' ); ?></span>
                                            <span><?php _e( '(Both fade in and scale effects)', 'ultimate-social-media-plus' ); ?></span>
                                        </label>
                                    </div>
                                    <div class="col-sm-3">
                                        <input class="styled" type="radio" name="sfsi_plus_same_icons_mouseOver_effect"
                                               value="combo-fade-out-scale" <?php echo ( $sfsi_plus_mouseOver_effect == 'combo-fade-out-scale' ) ? 'checked="true"' : ''; ?>>
                                        <label>
                                            <span><?php _e( 'Combo (Fade Out , Scale)', 'ultimate-social-media-plus' ); ?></span>
                                            <span><?php _e( '(Icons turn from shadow to full color)', 'ultimate-social-media-plus' ); ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div><!-- row closes -->
                        </div>
                        <div class="row zerobottompadding other_icons_effects">
                            <input value="other_icons"
                                   name="sfsi_plus_mouseOver_effect_type" <?php echo ( $sfsi_plus_mouseOver_effect_type == 'other_icons' ) ? 'checked=checked' : ''; ?>
                                   type="radio" class="styled"/>
                            <label><?php _e( 'Show other icons on mouse-over (Only applied for Desktop Icons)', 'ultimate-social-media-plus' ); ?></label>
                        </div>

                        <div class="row rowpadding10 rowmarginleft45 other_icons_effects_options <?php echo ( $sfsi_plus_mouseOver_effect_type == 'other_icons' ) ? 'show' : 'hide'; ?>">
                            <div class="sfsiMouseOverloader"></div>
                            <div class="col-md-12 other_icons_effects_options_container">

								<?php

								$arrDefaultIcons             = maybe_unserialize( SFSI_PLUS_ALLICONS );
								$arrMouseoverIconImages      = $sfsi_plus_mouseOver_other_icon_images;
								$arrActiveStdDesktopIcons    = sfsi_plus_get_displayed_std_desktop_icons( $option1 );
								$arrActiveCustomDesktopicons = sfsi_plus_get_displayed_custom_desktop_icons( $option1 );
								$arrAllCustomIcons           = sfsi_get_custom_icons_images( $option1 );

								foreach ( $arrDefaultIcons as $key => $iconName ):
									sfsi_generate_other_icon_effect_admin_html( $iconName, $arrMouseoverIconImages, $arrActiveStdDesktopIcons, $option3 );
								endforeach;

								if ( isset( $arrAllCustomIcons ) && ! empty( $arrAllCustomIcons ) && is_array( $arrAllCustomIcons ) ) {
									$i = 1;
									foreach ( $arrAllCustomIcons as $index => $imgUrl ) {
										if ( ! empty( $imgUrl ) ) {
											sfsi_generate_other_icon_effect_admin_html( "custom", $arrMouseoverIconImages, $arrActiveCustomDesktopicons, $option3, $index, $imgUrl, $i );
											$i ++;
										}
									}
								}
								?>
                            </div>
                            <div class="col-md-12 topmargin10">
                                <label><?php _e( 'Transition effect to those icons', 'ultimate-social-media-plus' ); ?></label>
                                <select name="mouseover_other_icons_transition_effect">
                                    <option <?php echo 'noeffect' == $mouseover_other_icons_transition_effect ? "selected=selected" : ""; ?>
                                            value="noeffect"><?php _e( 'No effect', 'ultimate-social-media-plus' ) ?></option>
                                    <option <?php echo 'flip' == $mouseover_other_icons_transition_effect ? "selected=selected" : ""; ?>
                                            value="flip"><?php _e( 'Flip', 'ultimate-social-media-plus' ); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
				$shuffleChecked = '';
				$shuffleClass   = 'hide';

				if ( $sfsi_plus_shuffle_icons == 'yes' ) {
					$shuffleChecked = 'checked="checked"';
					$shuffleClass   = 'show';
				}
				?>
                <div class="Shuffle_auto">
                    <p class="radio_section tab_3_option">
                        <input name="sfsi_plus_shuffle_icons" <?php echo $shuffleChecked; ?> type="checkbox" value="yes"
                               class="styled"/>
                        <label>
							<?php _e( 'Shuffle them automatically', 'ultimate-social-media-plus' ); ?>
                        </label>
                    </p>

                    <div class="sub_sub_box shuffle_sub <?php echo $shuffleClass; ?>">
                        <p class="radio_section tab_3_option">
                            <input name="sfsi_plus_shuffle_Firstload" <?php echo ( $sfsi_plus_shuffle_Firstload == 'yes' ) ? 'checked="true"' : ''; ?>
                                   type="checkbox" value="yes" class="styled"/>
                            <label>
								<?php _e( 'When the site is first loaded', 'ultimate-social-media-plus' ); ?>
                            </label>
                        </p>
                        <p class="radio_section tab_3_option">
                            <input name="sfsi_plus_shuffle_interval" <?php echo ( $sfsi_plus_shuffle_interval == 'yes' ) ? 'checked="true"' : ''; ?>
                                   type="checkbox" value="yes" class="styled"/>
                            <label>
								<?php _e( 'Every', 'ultimate-social-media-plus' ); ?>
                            </label>
                            <input class="smal_inpt" type="text" name="sfsi_plus_shuffle_intervalTime"
                                   value="<?php echo ( $sfsi_plus_shuffle_intervalTime != '' ) ? esc_attr( $sfsi_plus_shuffle_intervalTime ) : ''; ?>"
                                   style="padding:0 10px">
                            <label>
								<?php _e( 'seconds', 'ultimate-social-media-plus' ); ?>
                            </label>
                        </p>
                    </div>
                </div>

            </div>
            <!--END icon Animation section -->

        </div>
    </div>
    <!--Content of 4-->

    <!-- SAVE BUTTON SECTION   -->
    <div class="save_button tab_3_sav">
        <img src="<?php echo SFSI_PLUS_PLUGURL ?>images/ajax-loader.gif" alt="loader" class="loader-img"/>
		<?php $nonce = wp_create_nonce( "update_plus_step3" ); ?>
        <a href="javascript:;" id="sfsi_plus_save3" title="<?php _e( 'Save', 'ultimate-social-media-plus' ); ?>"
           data-nonce="<?php echo $nonce; ?>">
			<?php _e( 'Save', 'ultimate-social-media-plus' ); ?>
        </a>
    </div>   <!-- END SAVE BUTTON SECTION   -->

    <a class="sfsiColbtn closeSec" href="javascript:;">
		<?php _e( 'Collapse area', 'ultimate-social-media-plus' ); ?>
    </a>
    <label class="closeSec"></label>
    <!-- ERROR AND SUCCESS MESSAGE AREA-->
    <p class="red_txt errorMsg" style="display:none;"></p>
    <p class="green_txt sucMsg" style="display:none;"></p>
</div><!-- END Section 3 "What design & animation do you want to give your icons?" main div  -->
