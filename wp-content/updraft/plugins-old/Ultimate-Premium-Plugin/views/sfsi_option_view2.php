<?php
/* maybe_unserialize all saved option for second section options */
$option4 =  maybe_unserialize(get_option('sfsi_premium_section4_options', false));
$option2 =  maybe_unserialize(get_option('sfsi_premium_section2_options', false));
$option1 =  maybe_unserialize(get_option('sfsi_premium_section1_options', false));

/*
	 * Sanitize, escape and validate values
	 */
$option2['sfsi_plus_rss_url']                 =    (isset($option2['sfsi_plus_rss_url']))
    ? esc_url($option2['sfsi_plus_rss_url'])
    : '';

$option2['sfsi_plus_email_icons_functions']         =    (isset($option2['sfsi_plus_email_icons_functions']))
    ? sanitize_text_field($option2['sfsi_plus_email_icons_functions'])
    : '';
$option2['sfsi_plus_email_icons_contact']             =    (isset($option2['sfsi_plus_email_icons_contact']))
    ? sanitize_text_field($option2['sfsi_plus_email_icons_contact'])
    : '';
$option2['sfsi_plus_email_icons_pageurl']             =    (isset($option2['sfsi_plus_email_icons_pageurl']))
    ? sanitize_text_field($option2['sfsi_plus_email_icons_pageurl'])
    : '';
$option2['sfsi_plus_email_icons_mailchimp_apikey']     =    (isset($option2['sfsi_plus_email_icons_mailchimp_apikey']))
    ? sanitize_text_field($option2['sfsi_plus_email_icons_mailchimp_apikey'])
    : '';
$option2['sfsi_plus_email_icons_mailchimp_listid']     =    (isset($option2['sfsi_plus_email_icons_mailchimp_listid']))
    ? sanitize_text_field($option2['sfsi_plus_email_icons_mailchimp_listid'])
    : '';

$option2['sfsi_plus_email_icons_subject_line']         =    (isset($option2['sfsi_plus_email_icons_subject_line']))
    ? stripslashes(sanitize_text_field($option2['sfsi_plus_email_icons_subject_line']))
    : '${title}';
$option2['sfsi_plus_email_icons_email_content']     =    (isset($option2['sfsi_plus_email_icons_email_content']))
    ? stripslashes(sanitize_text_field($option2['sfsi_plus_email_icons_email_content']))
    : 'Check out this article «${title}»: ${link}';

$option2['sfsi_plus_rss_icons']             =    (isset($option2['sfsi_plus_rss_icons']))
    ? sanitize_text_field($option2['sfsi_plus_rss_icons'])
    : '';
$option2['sfsi_plus_email_url']                =    (isset($option2['sfsi_plus_email_url']))
    ? sanitize_text_field($option2['sfsi_plus_email_url'])
    : '';

$option2['sfsi_plus_facebookPage_option']    =    (isset($option2['sfsi_plus_facebookPage_option']))
    ? sanitize_text_field($option2['sfsi_plus_facebookPage_option'])
    : '';
$option2['sfsi_plus_facebookPage_url']         =    (isset($option2['sfsi_plus_facebookPage_url']))
    ? esc_url($option2['sfsi_plus_facebookPage_url'])
    : '';
$option2['sfsi_plus_facebookLike_option']    =    (isset($option2['sfsi_plus_facebookLike_option']))
    ? sanitize_text_field($option2['sfsi_plus_facebookLike_option'])
    : ' ';
$option2['sfsi_plus_facebookShare_option']     =    (isset($option2['sfsi_plus_facebookShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_facebookShare_option'])
    : '';
$option2['sfsi_plus_facebookFollow_option'] =    (isset($option2['sfsi_plus_facebookFollow_option']))
    ? sanitize_text_field($option2['sfsi_plus_facebookFollow_option'])
    : 'no';
$option2['sfsi_plus_facebookProfile_url']    =    (isset($option2['sfsi_plus_facebookProfile_url']))
    ? esc_url($option2['sfsi_plus_facebookProfile_url'])
    : '';

$option2['sfsi_plus_threadsShare_option']     =    (isset($option2['sfsi_plus_threadsShare_option']))
	? sanitize_text_field($option2['sfsi_plus_threadsShare_option'])
	: '';
$option2['sfsi_plus_blueskyShare_option']     =    (isset($option2['sfsi_plus_blueskyShare_option']))
	? sanitize_text_field($option2['sfsi_plus_blueskyShare_option'])
	: '';

$option2['sfsi_plus_twitter_followme']         =    (isset($option2['sfsi_plus_twitter_followme']))
    ? sanitize_text_field($option2['sfsi_plus_twitter_followme'])
    : '';
$option2['sfsi_plus_twitter_followUserName'] =    (isset($option2['sfsi_plus_twitter_followUserName']))
    ? sanitize_text_field($option2['sfsi_plus_twitter_followUserName'])
    : '';
$option2['sfsi_plus_twitter_aboutPage']     =    (isset($option2['sfsi_plus_twitter_aboutPage']))
    ? sanitize_text_field($option2['sfsi_plus_twitter_aboutPage'])
    : 'no';

$option2['sfsi_plus_twitter_page']             =    (isset($option2['sfsi_plus_twitter_page']))
    ? sanitize_text_field($option2['sfsi_plus_twitter_page'])
    : '';
$option2['sfsi_plus_twitter_pageURL']         =    (isset($option2['sfsi_plus_twitter_pageURL']))
    ? esc_url($option2['sfsi_plus_twitter_pageURL'])
    : '';


$option2['sfsi_plus_youtube_pageUrl']         =    (isset($option2['sfsi_plus_youtube_pageUrl']))
    ? esc_url($option2['sfsi_plus_youtube_pageUrl'])
    : '';
$option2['sfsi_plus_youtube_page']             =    (isset($option2['sfsi_plus_youtube_page']))
    ? sanitize_text_field($option2['sfsi_plus_youtube_page'])
    : '';
$option2['sfsi_plus_youtube_follow']         =    (isset($option2['sfsi_plus_youtube_follow']))
    ? sanitize_text_field($option2['sfsi_plus_youtube_follow'])
    : '';
$option2['sfsi_plus_ytube_user']             =    (isset($option2['sfsi_plus_ytube_user']))
    ? sanitize_text_field($option2['sfsi_plus_ytube_user'])
    : '';

$option2['sfsi_plus_pinterest_page']         =    (isset($option2['sfsi_plus_pinterest_page']))
    ? sanitize_text_field($option2['sfsi_plus_pinterest_page'])
    : '';
$option2['sfsi_plus_pinterest_pageUrl']        =    (isset($option2['sfsi_plus_pinterest_pageUrl']))
    ? esc_url($option2['sfsi_plus_pinterest_pageUrl'])
    : '';
$option2['sfsi_plus_pinterest_pingBlog']     =    (isset($option2['sfsi_plus_pinterest_pingBlog']))
    ? sanitize_text_field($option2['sfsi_plus_pinterest_pingBlog'])
    : '';

$option2['sfsi_plus_instagram_pageUrl']        =    (isset($option2['sfsi_plus_instagram_pageUrl']))
    ? esc_url($option2['sfsi_plus_instagram_pageUrl'])
    : '';

$option2['sfsi_plus_linkedin_page']         =    (isset($option2['sfsi_plus_linkedin_page']))
    ? sanitize_text_field($option2['sfsi_plus_linkedin_page'])
    : '';
$option2['sfsi_plus_linkedin_pageURL']         =    (isset($option2['sfsi_plus_linkedin_pageURL']))
    ? esc_url($option2['sfsi_plus_linkedin_pageURL'])
    : '';
$option2['sfsi_plus_linkedin_follow']         =     (isset($option2['sfsi_plus_linkedin_follow']))
    ? sanitize_text_field($option2['sfsi_plus_linkedin_follow'])
    : '';
$option2['sfsi_plus_linkedin_followCompany'] =    (isset($option2['sfsi_plus_linkedin_followCompany']))
    ? intval($option2['sfsi_plus_linkedin_followCompany'])
    : '';
$option2['sfsi_plus_linkedin_SharePage']     =     (isset($option2['sfsi_plus_linkedin_SharePage']))
    ? sanitize_text_field($option2['sfsi_plus_linkedin_SharePage'])
    : '';
$option2['sfsi_plus_linkedin_recommendBusines']        =     (isset($option2['sfsi_plus_linkedin_recommendBusines']))
    ? sanitize_text_field($option2['sfsi_plus_linkedin_recommendBusines'])
    : '';
$option2['sfsi_plus_linkedin_recommendCompany']     =     (isset($option2['sfsi_plus_linkedin_recommendCompany']))
    ? sanitize_text_field($option2['sfsi_plus_linkedin_recommendCompany'])
    : '';
$option2['sfsi_plus_linkedin_recommendProductId']     =     (isset($option2['sfsi_plus_linkedin_recommendProductId']))
    ? intval($option2['sfsi_plus_linkedin_recommendProductId'])
    : '';
$option2['sfsi_plus_linkedin_recommendCompany']     =     (isset($option2['sfsi_plus_linkedin_recommendCompany']))
    ? sanitize_text_field($option2['sfsi_plus_linkedin_recommendCompany'])
    : '';

// $option2['sfsi_plus_houzzVisit_option'] 		= 	(isset($option2['sfsi_plus_houzzVisit_option']))
// 													? sanitize_text_field($option2['sfsi_plus_houzzVisit_option'])
// 													: 'no';

$option2['sfsi_plus_houzz_pageUrl']         =     (isset($option2['sfsi_plus_houzz_pageUrl']))
    ? esc_url($option2['sfsi_plus_houzz_pageUrl'])
    : '';

$option2['sfsi_plus_houzzShare_option']     =     (isset($option2['sfsi_plus_houzzShare_option'])) ? sanitize_text_field($option2['sfsi_plus_houzzShare_option']) : '';

$option2['sfsi_plus_houzz_websiteId']     =     (isset($option2['sfsi_plus_houzz_websiteId'])) ? sanitize_text_field($option2['sfsi_plus_houzz_websiteId']) : '';

$option2['sfsi_plus_linkedin_recommendCompany']     =     (isset($option2['sfsi_plus_linkedin_recommendCompany']))
    ? sanitize_text_field($option2['sfsi_plus_linkedin_recommendCompany'])
    : '';

$option4['sfsi_plus_youtubeusernameorid']     =     (isset($option4['sfsi_plus_youtubeusernameorid']))
    ? sanitize_text_field($option4['sfsi_plus_youtubeusernameorid'])
    : '';
$option4['sfsi_plus_ytube_chnlid']             =     (isset($option4['sfsi_plus_ytube_chnlid']))
    ? strip_tags(trim($option4['sfsi_plus_ytube_chnlid']))
    : '';

$option2['sfsi_plus_snapchat_pageUrl']         =     (isset($option2['sfsi_plus_snapchat_pageUrl']))
    ? esc_url($option2['sfsi_plus_snapchat_pageUrl'])
    : '';
$option2['sfsi_plus_whatsapp_message']         =     (isset($option2['sfsi_plus_whatsapp_message']))
    ? stripslashes(sanitize_text_field($option2['sfsi_plus_whatsapp_message']))
    : '';
$option2['sfsi_plus_my_whatsapp_number']         =     (isset($option2['sfsi_plus_my_whatsapp_number']))
    ? ($option2['sfsi_plus_my_whatsapp_number'])
    : '';
$option2['sfsi_plus_whatsapp_number']         =     (isset($option2['sfsi_plus_whatsapp_number']))
    ? ($option2['sfsi_plus_whatsapp_number'])
    : '';
$option2['sfsi_plus_whatsapp_share_page']     =    (isset($option2['sfsi_plus_whatsapp_share_page']))
    ? stripslashes(sanitize_text_field($option2['sfsi_plus_whatsapp_share_page']))
    : '${title} ${link}';

$option2['sfsi_plus_skype_options']         =     (isset($option2['sfsi_plus_skype_options'])) ? sanitize_text_field($option2['sfsi_plus_skype_options']) : 'call';


$option2['sfsi_plus_skype_pageUrl']         =     (isset($option2['sfsi_plus_skype_pageUrl']))
    ? sanitize_text_field($option2['sfsi_plus_skype_pageUrl'])
    : '';
$option2['sfsi_plus_vimeo_pageUrl']         =     (isset($option2['sfsi_plus_vimeo_pageUrl']))
    ? esc_url($option2['sfsi_plus_vimeo_pageUrl'])
    : '';
$option2['sfsi_plus_soundcloud_pageUrl']     =     (isset($option2['sfsi_plus_soundcloud_pageUrl']))
    ? esc_url($option2['sfsi_plus_soundcloud_pageUrl'])
    : '';

$option2['sfsi_plus_yummlyVisit_option']     =    (isset($option2['sfsi_plus_yummlyVisit_option']))
    ? sanitize_text_field($option2['sfsi_plus_yummlyVisit_option'])
    : 'no';

$option2['sfsi_plus_yummly_pageUrl']         =     (isset($option2['sfsi_plus_yummly_pageUrl']))
    ? esc_url($option2['sfsi_plus_yummly_pageUrl'])
    : '';

$option2['sfsi_plus_yummlyShare_option']     =    (isset($option2['sfsi_plus_yummlyShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_yummlyShare_option'])
    : 'no';

$option2['sfsi_plus_flickr_pageUrl']         =     (isset($option2['sfsi_plus_flickr_pageUrl']))
    ? esc_url($option2['sfsi_plus_flickr_pageUrl'])
    : '';
$option2['sfsi_plus_reddit_pageUrl']         =     (isset($option2['sfsi_plus_reddit_pageUrl']))
    ? esc_url($option2['sfsi_plus_reddit_pageUrl'])
    : '';
$option2['sfsi_plus_tumblr_pageUrl']         =     (isset($option2['sfsi_plus_tumblr_pageUrl']))
    ? esc_url($option2['sfsi_plus_tumblr_pageUrl'])
    : '';

$option2['sfsi_plus_fbmessengerContact_option']     =    (isset($option2['sfsi_plus_fbmessengerContact_option'])) ? sanitize_text_field($option2['sfsi_plus_fbmessengerContact_option'])
    : 'no';

$option2['sfsi_plus_fbmessengerShare_option']     =    (isset($option2['sfsi_plus_fbmessengerShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_fbmessengerShare_option'])
    : 'no';

$option2['sfsi_plus_fbmessengerContact_url']     =    (isset($option2['sfsi_plus_fbmessengerContact_url']))
    ? sanitize_text_field($option2['sfsi_plus_fbmessengerContact_url'])
    : '';

$option2['sfsi_plus_mixShare_option']     =    (isset($option2['sfsi_plus_mixShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_mixShare_option'])
    : 'no';
$option2['sfsi_plus_mixVisit_option']     =    (isset($option2['sfsi_plus_mixVisit_option']))
    ? sanitize_text_field($option2['sfsi_plus_mixVisit_option'])
    : 'no';

$option2['sfsi_plus_mixVisit_url']     =    (isset($option2['sfsi_plus_mixVisit_url']))
    ? sanitize_text_field($option2['sfsi_plus_mixVisit_url'])
    : '';

$option2['sfsi_plus_okLike_option']     =    (isset($option2['sfsi_plus_okLike_option']))
    ? sanitize_text_field($option2['sfsi_plus_okLike_option'])
    : 'no';
$option2['sfsi_plus_okVisit_option']     =    (isset($option2['sfsi_plus_okVisit_option']))
    ? sanitize_text_field($option2['sfsi_plus_okVisit_option'])
    : 'no';

$option2['sfsi_plus_okVisit_url']     =    (isset($option2['sfsi_plus_okVisit_url']))
    ? sanitize_text_field($option2['sfsi_plus_okVisit_url'])
    : '';

$option2['sfsi_plus_okSubscribe_option']     =    (isset($option2['sfsi_plus_okSubscribe_option']))
    ? sanitize_text_field($option2['sfsi_plus_okSubscribe_option'])
    : 'no';

$option2['sfsi_plus_okSubscribe_userid']     =    (isset($option2['sfsi_plus_okSubscribe_userid']))
    ? sanitize_text_field($option2['sfsi_plus_okSubscribe_userid'])
    : '';

$option2['sfsi_plus_telegramShare_option']     =    (isset($option2['sfsi_plus_telegramShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_telegramShare_option'])
    : 'no';
$option2['sfsi_plus_telegramMessage_option']     =    (isset($option2['sfsi_plus_telegramMessage_option']))
    ? sanitize_text_field($option2['sfsi_plus_telegramMessage_option'])
    : 'no';

$option2['sfsi_plus_telegram_username']     =    (isset($option2['sfsi_plus_telegram_username']))
    ? sanitize_text_field($option2['sfsi_plus_telegram_username'])
    : '';

$option2['sfsi_plus_telegram_message']     =    (isset($option2['sfsi_plus_telegram_message']))
    ? sanitize_text_field($option2['sfsi_plus_telegram_message'])
    : '';

$option2['sfsi_plus_vkShare_option']     =    (isset($option2['sfsi_plus_vkShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_vkShare_option'])
    : 'no';
$option2['sfsi_plus_vkVisit_option']     =    (isset($option2['sfsi_plus_vkVisit_option']))
    ? sanitize_text_field($option2['sfsi_plus_vkVisit_option'])
    : 'no';

$option2['sfsi_plus_vkLike_option']     =    (isset($option2['sfsi_plus_vkLike_option']))
    ? sanitize_text_field($option2['sfsi_plus_vkLike_option'])
    : 'no';
$option2['sfsi_plus_vkFollow_option']     =    (isset($option2['sfsi_plus_vkFollow_option']))
    ? sanitize_text_field($option2['sfsi_plus_vkFollow_option'])
    : 'no';

$option2['sfsi_plus_vkFollow_url']     =    (isset($option2['sfsi_plus_vkFollow_url']))
    ? sanitize_text_field($option2['sfsi_plus_vkFollow_url'])
    : '';

$option2['sfsi_plus_vkVisit_url']     =    (isset($option2['sfsi_plus_vkVisit_url']))
    ? sanitize_text_field($option2['sfsi_plus_vkVisit_url'])
    : '';

$option2['sfsi_plus_weiboVisit_option']     =    (isset($option2['sfsi_plus_weiboVisit_option']))
    ? sanitize_text_field($option2['sfsi_plus_weiboVisit_option'])
    : 'no';
$option2['sfsi_plus_weiboVisit_url']     =    (isset($option2['sfsi_plus_weiboVisit_url']))
    ? sanitize_text_field($option2['sfsi_plus_weiboVisit_url'])
    : '';
$option2['sfsi_plus_weiboShare_option']     =    (isset($option2['sfsi_plus_weiboShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_weiboShare_option'])
    : 'no';

$option2['sfsi_plus_weiboLike_option']     =    (isset($option2['sfsi_plus_weiboLike_option']))
    ? sanitize_text_field($option2['sfsi_plus_weiboLike_option'])
    : 'no';

$option2['sfsi_plus_xingVisit_option']     =    (isset($option2['sfsi_plus_xingVisit_option']))
    ? sanitize_text_field($option2['sfsi_plus_xingVisit_option'])
    : 'no';
$option2['sfsi_plus_xingVisit_url']     =    (isset($option2['sfsi_plus_xingVisit_url']))
    ? esc_url($option2['sfsi_plus_xingVisit_url'])
    : '';
$option2['sfsi_plus_xingFollow_option']     =    (isset($option2['sfsi_plus_xingFollow_option']))
    ? sanitize_text_field($option2['sfsi_plus_xingFollow_option'])
    : 'no';
$option2['sfsi_plus_xingFollow_url']     =    (isset($option2['sfsi_plus_xingFollow_url']))
    ? sanitize_text_field($option2['sfsi_plus_xingFollow_url'])
    : '';
$option2['sfsi_plus_xingShare_option']     =    (isset($option2['sfsi_plus_xingShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_xingShare_option'])
    : 'no';

$option2['sfsi_plus_mastodonVisit_option']     =    (isset($option2['sfsi_plus_mastodonVisit_option']))
    ? sanitize_text_field($option2['sfsi_plus_mastodonVisit_option'])
    : 'no';
$option2['sfsi_plus_mastodonVisit_url']     =    (isset($option2['sfsi_plus_mastodonVisit_url']))
    ? esc_url($option2['sfsi_plus_mastodonVisit_url'])
    : '';
$option2['sfsi_plus_mastodonShare_option']     =    (isset($option2['sfsi_plus_mastodonShare_option']))
    ? sanitize_text_field($option2['sfsi_plus_mastodonShare_option'])
    : 'no';

// As in new setting Tweet about my page was set in Question 5, moving back to Question 2, so getting setting from question 5
$tweetAboutPage = 'no';

if (isset($option2['sfsi_plus_twitter_aboutPage'])) {
    $tweetAboutPage = sanitize_text_field($option2['sfsi_plus_twitter_aboutPage']);
}


$classToAddSfsi_navigate_to_question6 = ($tweetAboutPage == 'yes') ? 'addCss' : 'removeCss';
$checked = ($tweetAboutPage == 'yes') ?  'checked="true"' : '';
?>

<!-- Section 2 "What do you want the icon to do?" main div Start -->
<div class="tab2">
    <!-- RSS ICON -->
    <div class="row bdr_top sfsiplus_rss_section">
        <h2 class="sfsicls_rs_s">
            <?php _e('RSS', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('When clicked on, users can subscribe via RSS', 'ultimate-social-media-plus'); ?>
            </p>
            <div class="rss_url_row d-flex" style="align-items: center;">
                <h4 style="font-size: 17px;padding-top: 0 !important">
                    <?php _e('RSS URL:', 'ultimate-social-media-plus'); ?>
                </h4>
                <input name="sfsi_plus_rss_url" id="sfsi_plus_rss_url" class="add" type="url" value="<?php echo ($option2['sfsi_plus_rss_url'] != '') ?  $option2['sfsi_plus_rss_url'] : ''; ?>" placeholder="E.g http://www.yoursite.com/feed" />
                <span class="sfrsTxt" style="font-size: 17px;display:flex;line-height: 39px;">
                    <?php _e('For most blogs it’s http://blogname.com/feed', 'ultimate-social-media-plus'); ?>
                </span>
            </div>
        </div>
    </div>
    <!-- END RSS ICON -->

    <!-- EMAIL ICON -->
    <?php
    $feedId         = sanitize_text_field(get_option('sfsi_premium_feed_id', false));
    $connectToFeed     = "https://api.follow.it/?" . base64_encode("userprofile=wordpress&feed_id=" . $feedId);
    ?>
    <div class="row sfsiplus_email_section">
        <h2 class="sfsicls_email">
            <?php _e('Email', 'ultimate-social-media-plus'); ?>
        </h2>

        <div class="inr_cont">
            <p>
                <?php _e('Which action should the email icon perform?', 'ultimate-social-media-plus'); ?>
            </p>

            <ul class="sfsi_plus_email_functions_container">
                <li>
                    <div class="sfsiplusicnsdvwrp">
                        <input name="sfsi_plus_email_icons_functions" <?php echo ($option2['sfsi_plus_email_icons_functions'] == 'sf') ?  'checked="true"' : ''; ?> type="radio" value="sf" class="styled" />
                    </div>
                    <div style="line-height: 35px">
                        <b><?php _e('Automatic updates for subscribers: ', 'ultimate-social-media-plus'); ?></b>
                        <?php
                        _e('If people subscribe they will receive your new posts automatically by email. The service is FREE and you get full access to your subscriber’s emails and interesting statistics: ', 'ultimate-social-media-plus');
                        ?>

                        <a class="pop-up" href="javascript:" data-id="feedClaiming-overlay">
                            <?php _e('Get full access now.', 'ultimate-social-media-plus'); ?>
                        </a>

                        <?php _e('It also makes sense if you already offer an email newsletter. ', 'ultimate-social-media-plus'); ?>

                        <a href="https://follow.it/docs/publishers/introduction/what-is-follow-it-and-what-shall-i-use-it-for" target="_new">
                            <?php _e('Learn more.', 'ultimate-social-media-plus'); ?>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="sfsiplusicnsdvwrp">
                        <input name="sfsi_plus_email_icons_functions" <?php echo ($option2['sfsi_plus_email_icons_functions'] == 'contact') ?  'checked="true"' : ''; ?> type="radio" value="contact" class="styled" />
                    </div>
                    <div style="margin-top: 10px; line-height: 35px">
                        <b><?php _e('Contact me: ', 'ultimate-social-media-plus'); ?></b>
                        <?php
                        _e('If people click on the icon an email will open with your email address already entered. This makes sense to use if you don’t offer a contact option anywhere else.', 'ultimate-social-media-plus');
                        ?>
                    </div>
                    <div class="sfsi_plus_field">
                        <b><?php _e('Your Email:', 'ultimate-social-media-plus'); ?></b>
                        <input type="text" name="sfsi_plus_email_icons_contact" value="<?php echo (isset($option2['sfsi_plus_email_icons_contact'])) ? esc_attr($option2['sfsi_plus_email_icons_contact']) : ''; ?>">
                    </div>
                </li>
                <li>
                    <div class="sfsiplusicnsdvwrp">
                        <input name="sfsi_plus_email_icons_functions" <?php echo ($option2['sfsi_plus_email_icons_functions'] == 'page') ?  'checked="true"' : ''; ?> type="radio" value="page" class="styled" />
                    </div>
                    <div style="margin-top: 10px;">
                        <b><?php _e('Link it to a certain page ', 'ultimate-social-media-plus'); ?></b>
                        <?php
                        _e('(e.g. contact us or subscription page)', 'ultimate-social-media-plus');
                        ?>
                    </div>
                    <div class="sfsi_plus_field">
                        <b><?php _e('URL:', 'ultimate-social-media-plus'); ?></b>
                        <input type="text" name="sfsi_plus_email_icons_pageurl" placeholder="http://" value="<?php echo (isset($option2['sfsi_plus_email_icons_pageurl'])) ? esc_attr($option2['sfsi_plus_email_icons_pageurl']) : ''; ?>">
                    </div>
                </li>
                <li>
                    <div class="sfsiplusicnsdvwrp">
                        <input name="sfsi_plus_email_icons_functions" <?php echo ($option2['sfsi_plus_email_icons_functions'] == 'share_email') ?  'checked="true"' : ''; ?> type="radio" value="share_email" class="styled" />
                    </div>
                    <div style="margin-top: 10px;">
                        <b><?php _e('<b>Share by email</b>', 'ultimate-social-media-plus'); ?></b>
                    </div>
                    <div class="sfsi_plus_field">
                        <b><?php _e('Subject line:', 'ultimate-social-media-plus'); ?></b>
                        <input type="text" name="sfsi_plus_email_icons_subject_line" placeholder="http://" value="<?php echo (isset($option2['sfsi_plus_email_icons_subject_line'])) ? esc_attr($option2['sfsi_plus_email_icons_subject_line']) : '${title}'; ?>" />
                    </div>
                    <div class="sfsi_plus_field">
                        <b><?php _e('Email content:', 'ultimate-social-media-plus'); ?></b>
                        <div class="sfsi_plus_email_icons_content">
                            <textarea name="sfsi_plus_email_icons_email_content" id="sfsi_plus_email_icons_email_content" class="add_txt"><?php echo (isset($option2['sfsi_plus_email_icons_email_content']) && $option2['sfsi_plus_email_icons_email_content'] != '') ?  stripslashes($option2['sfsi_plus_email_icons_email_content']) : 'Check out this article «${title}»: ${link}'; ?></textarea>
                            <p>
                                <?php
                                _e('In the fields above, insert ${title} where you want the title of the story to get displayed, and ${link} where you want the link to the article to appear.', 'ultimate-social-media-plus');
                                ?>
                            </p>
                            <p>
                                <?php _e('Any other text you enter will always be used as you entered it.', 'ultimate-social-media-plus'); ?>
</p>
                        </div>
                    </div>
                </li>
            </ul>

            <p><?php _e('Pick which icon you want to use:', 'ultimate-social-media-plus'); ?></p>

            <ul class="tab_2_email_sec">
                <li>
                    <div class="sfsiplusicnsdvwrp">
                        <input name="sfsi_plus_rss_icons" <?php echo ($option2['sfsi_plus_rss_icons'] == 'email') ?  'checked="true"' : ''; ?> type="radio" value="email" class="styled" /><span class="email_icn"></span>
                    </div>
                    <label>
                        <?php _e('Email icon', 'ultimate-social-media-plus'); ?>
                    </label>
                </li>
                <li>
                    <div class="sfsiplusicnsdvwrp">
                        <input name="sfsi_plus_rss_icons" <?php echo ($option2['sfsi_plus_rss_icons'] == 'subscribe') ?  'checked="true"' : ''; ?> type="radio" value="subscribe" class="styled" /><span class="subscribe_icn"></span>
                    </div>
                    <label>
                        <?php _e('Email + Follow text', 'ultimate-social-media-plus'); ?>
                    </label>
                </li>
                <li>
                    <div class="sfsiplusicnsdvwrp">
                        <input name="sfsi_plus_rss_icons" <?php echo ($option2['sfsi_plus_rss_icons'] == 'sfsi') ?  'checked="true"' : ''; ?> type="radio" value="sfsi" class="styled" /><span class="sf_arow"></span>
                    </div>
                    <label>
                        <?php _e('follow.it icon', 'ultimate-social-media-plus'); ?>
                        <span class="sfplsdesc">
                            (<?php _e('provider of the service', 'ultimate-social-media-plus'); ?>)
                        </span>
                    </label>
                </li>
            </ul>
            <p><?php _e('The service offers many (more) advantages:', 'ultimate-social-media-plus'); ?></p>
            <div class='sfsi_plus_service_row'>
                <div class='sfsi_plus_service_column'>
                    <ul>
                        <li><?php
                            printf(
                                __('%1$sMore people come back%2$s to your site', 'ultimate-social-media-plus'),
                                '<span>',
                                '</span>'
                            );
                            ?></li>
                        <li><?php
                            printf(
                                __('See your %1$ssubscribers’ emails%2$s & %1$sinteresting statistics%2$s', 'ultimate-social-media-plus'),
                                '<span>',
                                '</span>'
                            );
                            ?></li>
                        <li><?php
                            printf(
                                __('Automatically post on %1$sFacebook & X (Twitter)%2$s', 'ultimate-social-media-plus'),
                                '<span>',
                                '</span>'
                            );
                            ?></li>
                    </ul>
                </div>
                <div class='sfsi_plus_service_column'>
                    <ul>
                        <li><?php
                            printf(
                                __('%1$sGet more traffic%2$s by being listed in the SF directory', 'ultimate-social-media-plus'),
                                '<span>',
                                '</span>'
                            );
                            ?></li>
                        <li><?php
                            printf(
                                __('%1$sGet alerts%2$s when people subscribe or unsubscribe', 'ultimate-social-media-plus'),
                                '<span>',
                                '</span>'
                            );
                            ?></li>
                        <li><?php
                            printf(
                                __('%1$sTailor the sender name & subject line%2$s of the emails', 'ultimate-social-media-plus'),
                                '<span>',
                                '</span>'
                            );
                            ?></li>
                    </ul>
                </div>
            </div>

            <form id="calimingOptimizationForm" method="get" action="https://api.follow.it/wpclaimfeeds/getFullAccess" target="_blank">
                <div class="sfsi_plus_inputbtn">
                    <input type="hidden" name="feed_id" value="<?php echo sanitize_text_field(get_option('sfsi_premium_feed_id', false)); ?>" />
                    <input type="email" name="email" value="<?php echo bloginfo('admin_email'); ?>" style="text-align: center!important;" />
                </div>
                <div class='sfsi_plus_more_services_link'>
                    <a class="pop-up" href="javascript:" id="getMeFullAccess" class="sfsi_premium_getMeFullAccess_class" data-nonce-fetch-feed-id="<?php echo wp_create_nonce('sfsi_premium_get_feed_id'); ?>" title="Give me access">
                        <?php _e('Click here to benefit from all advantages >', 'ultimate-social-media-plus'); ?>
                    </a>
                </div>
            </form>
            <p class='sfsi_plus_email_last_paragraph'>
                <?php _e('This will create your FREE account on follow.it, using the above email.', 'ultimate-social-media-plus'); ?><br>
                <?php _e('All data will be treated highly confidentially, see the', 'ultimate-social-media-plus'); ?>
                <a href="https://follow.it/info/privacy" target="_new">
                    <?php _e('Privacy Policy.', 'ultimate-social-media-plus'); ?>
                </a>
            </p>

        </div>
    </div>
    <!-- END EMAIL ICON -->

    <!-- FACEBOOK ICON -->
    <div class="row sfsiplus_facebook_section">
        <h2 class="sfsicls_facebook">
            <?php _e('Facebook', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('The Facebook icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do', 'ultimate-social-media-plus'); ?>
                <a class="rit_link pop-up" href="javascript:;" data-id="fbex-s2">
                    (<?php _e('see an example', 'ultimate-social-media-plus'); ?>).
                </a>
            </p>
            <p>
                <?php _e('The Facebook icon should allow users to...', 'ultimate-social-media-plus'); ?>
            </p>

            <p class="radio_section fb_url">
                <input name="sfsi_plus_facebookPage_option" <?php echo ($option2['sfsi_plus_facebookPage_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label>
                    <?php _e('Visit my Facebook page at:', 'ultimate-social-media-plus'); ?>
                </label>

                <input class="add" name="sfsi_plus_facebookPage_url" type="url" value="<?php echo ($option2['sfsi_plus_facebookPage_url'] != '') ?  $option2['sfsi_plus_facebookPage_url'] : ''; ?>" placeholder="E.g https://www.facebook.com/your_page_name" />
            </p>

            <p class="radio_section fb_url extra_sp">
                <input name="sfsi_plus_facebookLike_option" <?php echo ($option2['sfsi_plus_facebookLike_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label>
                    <?php _e('Like my blog on Facebook (+1)', 'ultimate-social-media-plus'); ?>
                </label>
            </p>

            <p class="radio_section fb_url extra_sp" style="margin-top: 11px;">
                <input name="sfsi_plus_facebookShare_option" <?php echo ($option2['sfsi_plus_facebookShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label>
                    <?php _e('Share my blog with friends (on Facebook)', 'ultimate-social-media-plus'); ?>
                </label>
            </p>

            <!--<p class="radio_section fb_url extra_sp sfsi_plus_profile_check_section">
            	<input name="sfsi_plus_facebookFollow_option" <?php echo ($option2['sfsi_plus_facebookFollow_option'] == 'yes') ?  'checked="true"' : ''; ?>  type="checkbox" value="yes" class="styled"/>

                <label>
            		<?php _e('Follow via facebook (allow user to follow you with one click, while still on your page)', 'ultimate-social-media-plus'); ?>
            	</label>
            </p>

            <p class="radio_section fb_url sfsi_plus_profile_url_section"
				style="<?php echo ($option2['sfsi_plus_facebookFollow_option'] == 'yes') ? 'display:block' : 'display:none'; ?>">

            	<label><?php _e('Your facebook profile URL:', 'ultimate-social-media-plus'); ?></label>
                <input class="add" name="sfsi_plus_facebookProfile_url" type="url" value="<?php echo ($option2['sfsi_plus_facebookProfile_url'] != '') ?  $option2['sfsi_plus_facebookProfile_url'] : ''; ?>" placeholder="E.g https://www.facebook.com/your_profile" />
            </p>-->

        </div>
    </div>
    <!-- END FACEBOOK ICON -->

    <!-- TWITTER ICON -->
    <div class="row sfsiplus_twitter_section">
        <h2 class="sfsicls_twt">
            <?php _e('X (Twitter)', 'ultimate-social-media-plus'); ?>
        </h2>

        <div class="inr_cont twt_tab_2">
            <p>
                <?php
                _e('The X (Twitter) icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do', 'ultimate-social-media-plus');
                ?>
                <a class="rit_link pop-up" href="javascript:;" data-id="twex-s2">
                    (<?php _e('see an example', 'ultimate-social-media-plus'); ?>).
                </a>
            </p>
            <p>
                <?php _e('The X (Twitter) icon should allow users to...', 'ultimate-social-media-plus'); ?>
            </p>
            <p class="radio_section fb_url twt_fld">
                <input name="sfsi_plus_twitter_page" <?php echo ($option2['sfsi_plus_twitter_page'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label>
                    <?php _e('Visit me on X (Twitter):', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_twitter_pageURL" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_plus_twitter_pageURL'] != '') ?  $option2['sfsi_plus_twitter_pageURL'] : ''; ?>" class="add" />
            </p>

            <div class="radio_section fb_url twt_fld">
                <input name="sfsi_plus_twitter_followme" <?php echo ($option2['sfsi_plus_twitter_followme'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label>
                    <?php _e('Follow me on X (Twitter):', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_twitter_followUserName" type="text" value="<?php echo ($option2['sfsi_plus_twitter_followUserName'] != '') ? esc_attr($option2['sfsi_plus_twitter_followUserName']) : ''; ?>" placeholder="my_x_twitter_name" class="add" />
            </div>

            <div class="radio_section fb_url twt_fld">
                <input name="sfsi_plus_twitter_aboutPage" type="checkbox" value="yes" <?php echo $checked; ?> class="styled" />
                <label>
                    <?php _e('X Post about my page:', 'ultimate-social-media-plus'); ?>
                </label>

                <a class="sfsi_navigate_to_question6 <?php echo $classToAddSfsi_navigate_to_question6; ?>" id="navigate_to_question6" href="#custom_social_data_setting" style="float: left;margin-left: 10px;margin-top:23px;color:#1a1d20;"><?php _e("To define what will be posted on X, please make the selections under question 6.", 'ultimate-social-media-plus'); ?></a>
            </div>

        </div>
    </div>
    <!-- END TWITTER ICON -->



    <!-- YOUTUBE ICON -->
    <div class="row sfsiplus_youtube_section">
        <h2 class="sfsicls_utube">
            <?php _e('Youtube', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont utube_inn">
            <p>
                <?php _e('The Youtube icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do', 'ultimate-social-media-plus'); ?>
                <a class="rit_link pop-up" href="javascript:;" data-id="ytex-s2">
                    (<?php _e('see an example', 'ultimate-social-media-plus'); ?>).
                </a>
            </p>
            <p>
                <?php _e('The Youtube icon should allow users to...', 'ultimate-social-media-plus'); ?>
            </p>
            <p class="radio_section fb_url"><input name="sfsi_plus_youtube_page" <?php echo ($option2['sfsi_plus_youtube_page'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label>
                    <?php _e('Visit my Youtube page at:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_youtube_pageUrl" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_plus_youtube_pageUrl'] != '') ?  $option2['sfsi_plus_youtube_pageUrl'] : ''; ?>" class="add" />
            </p>
            <p class="radio_section fb_url"><input name="sfsi_plus_youtube_follow" <?php echo ($option2['sfsi_plus_youtube_follow'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label style="margin-top: 10px; line-height: 26px;">
                    <?php _e('Subscribe to me on Youtube', 'ultimate-social-media-plus'); ?>
                    <span>
                        <?php _e('(allows people to subscribe to you directly, without leaving your blog)', 'ultimate-social-media-plus'); ?>
                    </span>
                </label>
            </p>
            <!--Adding Code for Channel Id and Channel Name-->
            <div class="cstmutbewpr" style="margin-left:47px">
                <div class="cstmutbtxtwpr">
                    <div class="cstmutbchnlidwpr" style="display: block">
                        <p class="extra_pp">
                            <label>
                                <?php _e('Channel Id:', 'ultimate-social-media-plus'); ?>
                            </label>
                            <input name="sfsi_plus_ytube_chnlid" type="text" value="<?php echo (isset($option2['sfsi_plus_ytube_chnlid']) && $option2['sfsi_plus_ytube_chnlid'] != '') ? esc_attr($option2['sfsi_plus_ytube_chnlid']) : ''; ?>" placeholder="youtube channel id" class="add" style="margin-left: 112px;" />
                        </p>
                        <div class="utbe_instruction">
                            <?php _e('To find your Channel ID, login to your YouTube account, click the user icon at the top right corner and select "Settings" , then click "Advanced settings". Your Channel ID will be displayed there.', 'ultimate-social-media-plus'); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- END YOUTUBE ICON -->

    <!-- PINTEREST ICON -->
    <div class="row sfsiplus_pinterest_section">
        <h2 class="sfsicls_pinterest"><?php _e('Pinterest', 'ultimate-social-media-plus'); ?></h2>
        <div class="inr_cont">
            <p>
                <?php _e('The Pinterest icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do', 'ultimate-social-media-plus'); ?>
                <a class="rit_link pop-up" href="javascript:;" data-id="pinex-s2">
                    (<?php _e('see an example', 'ultimate-social-media-plus'); ?>).
                </a>
            </p>
            <p>
                <?php _e('The Pinterest icon should allow users to...', 'ultimate-social-media-plus'); ?>
            </p>
            <p class="radio_section fb_url">
                <input name="sfsi_plus_pinterest_page" <?php echo ($option2['sfsi_plus_pinterest_page'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label>
                    <?php _e('Visit my Pinterest page at:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_pinterest_pageUrl" type="url" placeholder="http://" value="<?php echo ($option2['sfsi_plus_pinterest_pageUrl'] != '') ?  $option2['sfsi_plus_pinterest_pageUrl'] : ''; ?>" class="add" />
            </p>
            <div class="pint_url">
                <p class="radio_section fb_url">
                    <input name="sfsi_plus_pinterest_pingBlog" <?php echo ($option2['sfsi_plus_pinterest_pingBlog'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                    <label>
                        <?php _e('Pin my blog on Pinterest (+1)', 'ultimate-social-media-plus'); ?>
                    </label>
                </p>
            </div>
        </div>
    </div>
    <!-- END PINTEREST ICON -->

    <!-- INSTAGRAM ICON -->
    <div class="row sfsiplus_instagram_section">
        <h2 class="sfsicls_instagram">
            <?php _e('Instagram', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('When clicked on, users will get directed to your Instagram page', 'ultimate-social-media-plus'); ?>.
            </p>
            <p class="radio_section fb_url  cus_link instagram_space">
                <label class="sfsi_premium_label_width" style="margin-left:0">
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_instagram_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_plus_instagram_pageUrl']) && $option2['sfsi_plus_instagram_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_instagram_pageUrl']) : ''; ?>" placeholder="http://" class="add" />
            </p>
        </div>
    </div>
    <!-- END INSTAGRAM ICON -->

    <!-- THREADS ICON -->
    <div class="row sfsiplus_threads_section">
        <h2 class="sfsicls_threads">
            <?php _e('Threads', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('When clicked on, users can share your content on their Threads', 'ultimate-social-media-plus'); ?>.
            </p>
            <p class="radio_section fb_url extra_sp" style="margin-top: 11px;">
                <input name="sfsi_plus_threadsShare_option" <?php echo ($option2['sfsi_plus_threadsShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label>
			        <?php _e('Share my blog with friends (on Threads)', 'ultimate-social-media-plus'); ?>
                </label>
            </p>

        </div>
    </div>
    <!-- END THREADS ICON -->

    <!-- RIA ICON -->
    <div class="row sfsiplus_ria_section">

        <h2 class="sfsicls_ria"><?php _e('RateItAll', 'ultimate-social-media-plus'); ?></h2>
        <div class="inr_cont">

            <p><?php _e('When clicked on it, users will get directed to your page on ', 'ultimate-social-media-plus');
                echo '<a href="https://rateitall.com/">RateItAll.</a>' ?>
            </p>
            <p class="radio_section fb_url  cus_link instagram_space">
                <label class="sfsi_premium_label_width" style="margin-left:0">
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_ria_pageUrl" type="text"
                       value="<?php echo (isset($option2['sfsi_plus_ria_pageUrl']) && $option2['sfsi_plus_ria_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_ria_pageUrl']) : ''; ?>"
                       placeholder="http://" class="add"/>
            </p>
        </div>
    </div>
    <!-- RIA ICON -->

    <!-- INHA ICON -->
    <div class="row sfsiplus_inha_section">

        <h2 class="sfsicls_inha"><?php _e('IncreasingHappiness', 'ultimate-social-media-plus'); ?></h2>
        <div class="inr_cont">

            <p><?php _e('When clicked on it, users get taken to your profile page on ', 'ultimate-social-media-plus');
                echo '<a href="https://increasinghappiness.org/">IncreasingHappiness.org.</a>' ?>
            </p>
            <p class="radio_section fb_url  cus_link instagram_space">
                <label class="sfsi_premium_label_width" style="margin-left:0">
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_inha_pageUrl" type="text"
                       value="<?php echo (isset($option2['sfsi_plus_inha_pageUrl']) && $option2['sfsi_plus_inha_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_inha_pageUrl']) : ''; ?>"
                       placeholder="http://" class="add"/>
            </p>
        </div>
    </div>
    <!-- INHA ICON -->

    <!-- LINKEDIN ICON -->
    <div class="row sfsiplus_linkedin_section">
        <h2 class="sfsicls_linkdin">
            <?php _e('LinkedIn', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont linked_tab_2 link_in">
            <p>
                <?php _e('The LinkedIn icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do', 'ultimate-social-media-plus'); ?>
                <a class="rit_link pop-up" href="javascript:;" data-id="linkex-s2">
                    (<?php _e('see an example', 'ultimate-social-media-plus'); ?>).
                </a>
            </p>
            <p>
                <?php _e('You find your ID in the link of your profile page, e.g. https://www.linkedin.com/profile/view?id=<b>8539887</b>&trk=nav_responsive_tab_profile_pic', 'ultimate-social-media-plus'); ?>
            </p>
            <p>
                <?php _e('The LinkedIn icon should allow users to...', 'ultimate-social-media-plus'); ?>
            </p>
            <div class="radio_section fb_url link_1">
                <input name="sfsi_plus_linkedin_page" <?php echo ($option2['sfsi_plus_linkedin_page'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label>
                    <?php _e('Visit my Linkedin page at:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_linkedin_pageURL" type="text" placeholder="http://" value="<?php echo ($option2['sfsi_plus_linkedin_pageURL'] != '') ? esc_attr($option2['sfsi_plus_linkedin_pageURL']) : ''; ?>" class="add" />
            </div>

            <div class="radio_section fb_url link_2">
                <input name="sfsi_plus_linkedin_follow" <?php echo ($option2['sfsi_plus_linkedin_follow'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label>
                    <?php _e('Follow me on Linkedin:', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_linkedin_followCompany" type="text" value="<?php echo ($option2['sfsi_plus_linkedin_followCompany'] != '' && $option2['sfsi_plus_linkedin_followCompany'] > 0) ? esc_attr($option2['sfsi_plus_linkedin_followCompany']) : ''; ?>" class="add" placeholder="<?php _e('Enter company ID, e.g. 123456', 'ultimate-social-media-plus'); ?>" />
            </div>

            <div class="radio_section fb_url link_3">
                <input name="sfsi_plus_linkedin_SharePage" <?php echo ($option2['sfsi_plus_linkedin_SharePage'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label>
                    <?php _e('Share my page on LinkedIn', 'ultimate-social-media-plus'); ?>
                </label>
            </div>

            <div class="radio_section fb_url link_4">
                <input name="sfsi_plus_linkedin_recommendBusines" <?php echo ($option2['sfsi_plus_linkedin_recommendBusines'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label class="anthr_labl">
                    <?php _e('Recommend my business or product on Linkedin', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_linkedin_recommendProductId" type="text" value="<?php echo ($option2['sfsi_plus_linkedin_recommendProductId'] != '' && $option2['sfsi_plus_linkedin_recommendProductId'] > 0) ? esc_attr($option2['sfsi_plus_linkedin_recommendProductId']) : ''; ?>" class="add link_dbl" placeholder="Enter Product ID, e.g. 1441" /> <input name="sfsi_plus_linkedin_recommendCompany" type="text" value="<?php echo ($option2['sfsi_plus_linkedin_recommendCompany'] != '') ? esc_attr($option2['sfsi_plus_linkedin_recommendCompany']) : ''; ?>" class="add" placeholder="<?php _e('Enter company name, e.g. Google', 'ultimate-social-media-plus'); ?>" />
            </div>
            <div class="lnkdin_instruction">
                <?php _e('To find your Product or Company ID, you can use their ID lookup tool at', 'ultimate-social-media-plus'); ?>
                <a target="_blank" href="https://developer.linkedin.com/apply-getting-started#company-lookup">
                    https://developer.linkedin.com/apply-getting-started#company-lookup
                </a>
                . <?php _e('You need to be logged in to Linkedin to be able to use it.', 'ultimate-social-media-plus'); ?>
            </div>
        </div>
    </div>
    <!-- END LINKEDIN ICON -->

    <!-- SNAPCHAT ICON -->
    <div class="row sfsiplus_snapchat_section">
        <h2 class="sfsicls_snapchat">
            <?php _e('Snapchat', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('When clicked on, users will get directed to your Snapchat page.', 'ultimate-social-media-plus'); ?>
            </p>
            <p class="radio_section fb_url  cus_link instagram_space sfsi_align">
                <label style="margin-left:0px" class="sfsi_premium_label_width">
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_snapchat_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_plus_snapchat_pageUrl']) && $option2['sfsi_plus_snapchat_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_snapchat_pageUrl']) : ''; ?>" placeholder="http://" class="add" />
            </p>
        </div>
    </div>
    <!-- SNAPCHAT ICON -->

    <!-- Whatapp ICON -->
    <div class="row sfsiplus_whatsapp_section">
        <h2 class="sfsicls_whatsapp">
            <?php _e('WhatsApp', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('Here you have two options:', 'ultimate-social-media-plus'); ?>
            </p>
            <div class="radio_section fb_url  cus_link instagram_space" style="margin-left: -1px!important;">
                <ul>
                    <li>
                        <p class="sfsi_plus_pageurl_type">
                            <input class="styled" type="radio" value="message" name="sfsi_plus_whatsapp_url_type" <?php echo ($option2['sfsi_plus_whatsapp_url_type'] == 'message') ?  'checked="true"' : ''; ?>>
                            <label><?php _e("Allow users to send me a WhatsApp-message", 'ultimate-social-media-plus'); ?></label>
                        </p>
                        <div class="tab2">
                            <label><?php _e('Pre-filled message:', 'ultimate-social-media-plus'); ?></label>
                            <input name="sfsi_plus_whatsapp_message" type="text" value="<?php echo (isset($option2['sfsi_plus_whatsapp_message']) && $option2['sfsi_plus_whatsapp_message'] != '') ?  esc_attr($option2['sfsi_plus_whatsapp_message']) : ''; ?>" placeholder="Hey, i like your website." class="add" />
                        </div>
                        <div class="tab2">
                            <label><?php _e('My Whatsapp number', 'ultimate-social-media-plus'); ?></label>
                            <input name="sfsi_plus_my_whatsapp_number" type="text" value="<?php echo (isset($option2['sfsi_plus_my_whatsapp_number']) && $option2['sfsi_plus_my_whatsapp_number'] != '') ?  esc_attr($option2['sfsi_plus_my_whatsapp_number']) : ''; ?>" placeholder="" class="add" />
                        </div>
                        <div class="inr_cont_div">
                            <label><?php _e(" Start with country code, e.g. 1-541-754-3010 (without + in the country code)", 'ultimate-social-media-plus'); ?></label>
                        </div>
                    </li>

                    <li>
                        <p class="sfsi_plus_pageurl_type">
                            <input class="styled" type="radio" value="share_page" name="sfsi_plus_whatsapp_url_type" <?php echo ($option2['sfsi_plus_whatsapp_url_type'] == 'share_page') ?  'checked="true"' : ''; ?>>
                            <label><?php _e("Allow users to share the page via WhatsApp ", 'ultimate-social-media-plus'); ?></label>
                        </p>
                        <div class="sfsi_plus_whatsapp_share_page_container">
                            <textarea name="sfsi_plus_whatsapp_share_page" id="sfsi_plus_whatsapp_share_page" class="add_txt" placeholder=""><?php echo (isset($option2['sfsi_plus_whatsapp_share_page']) && $option2['sfsi_plus_whatsapp_share_page'] != '') ?  stripslashes($option2['sfsi_plus_whatsapp_share_page']) : '${title} ${link}'; ?></textarea>
                            <label>
                                <p>
                                    <?php _e('In the field above, insert ${title} where you want the title of the story to get displayed, and ${link} where you want the link to the article to appear.', 'ultimate-social-media-plus'); ?>
                                </p>
                                <p>
                                    <?php _e("Any other text you enter will always be used as you entered it.",  'ultimate-social-media-plus'); ?>
                                </p>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Whatapp ICON -->

    <!-- Skype ICON -->
    <div class="row sfsiplus_skype_section">

        <h2 class="sfsicls_skype"><?php _e('Skype', 'ultimate-social-media-plus'); ?></h2>

        <div class="container">

            <div class="row noborder">
                <p><?php _e('What shall happen if visitors click on this icon', 'ultimate-social-media-plus'); ?></p>
            </div>

            <div class="row noborder">

                <div class="col-sm-6 col-md-3 col-lg-2" style="padding-left: 0px; margin-left: -2px;">
                    <label class="radio-inline"><?php _e('Call me', 'ultimate-social-media-plus'); ?></label>
                    <input name="sfsi_plus_skype_options" type="radio" value="call" <?php if ($option2['sfsi_plus_skype_options'] == "call") {
                                                                                        echo 'checked="true"';
                                                                                    } ?> class="styled">
                </div>

                <div class="col-sm-6 col-md-3 col-lg-2" style="padding-left: 0px; margin-left: -2px;">
                    <label class="radio-inline"><?php _e('Open skype chat', 'ultimate-social-media-plus'); ?></label>
                    <input name="sfsi_plus_skype_options" type="radio" value="chat" <?php if ($option2['sfsi_plus_skype_options'] == "chat") {
                                                                                        echo 'checked="true"';
                                                                                    } ?> class="styled">
                </div>

            </div>

            <div class="row noborder">

                <div class="tab-content" style="margin-left:0">

                    <div id="sfsi_plus_skype_call" class="tab-pane active">
                        <p class="radio_section fb_url  cus_link instagram_space">
                            <label>
                                <?php _e('Your Skype username:', 'ultimate-social-media-plus'); ?>
                            </label>
                            <input name="sfsi_plus_skype_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_plus_skype_pageUrl']) && $option2['sfsi_plus_skype_pageUrl'] != '') ?  esc_attr($option2['sfsi_plus_skype_pageUrl']) : ''; ?>" placeholder="live:" class="add" />
                        </p>
                    </div>
                    <div id="sfsi_plus_skype_chat" class="tab-pane active">

                    </div>

                </div>
            </div>

        </div>

    </div>
    <!-- Skype ICON -->

    <!-- Phone ICON -->
    <div class="row sfsiplus_phone_section" style="<?php echo ("no" == $option1["sfsi_plus_phone_display"]) && ("no" == $option1["sfsi_plus_phone_mobiledisplay"]) ? 'display:none' : '' ?>">
        <h2 class="sfsicls_phone">
            <?php _e('Phone', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <div class="radio_section fb_url  cus_link instagram_space">
                <ul>
                    <li>
                        <p class="sfsi_plus_pageurl_type">
                            <label style="display:inline;margin-left:0"><?php _e("When clicked on, users will call you via their mobile or standard desktop application.", 'ultimate-social-media-plus'); ?></label>
                        </p>
                        <div class="tab2" style="padding-left: 0!important">
                            <label style="margin-left: 0!important"><?php _e('My phone number:', 'ultimate-social-media-plus'); ?></label>
                            <div class="sfsi_plus_whatsapp_number_container" style="max-width:50%">
                                <input name="sfsi_plus_whatsapp_number" type="text" value="<?php echo (isset($option2['sfsi_plus_whatsapp_number']) && $option2['sfsi_plus_whatsapp_number'] != '') ?  esc_attr($option2['sfsi_plus_whatsapp_number']) : ''; ?>" placeholder="" class="add" />
                                <label><?php _e("Start with + and then country code, e.g. +1 for US", 'ultimate-social-media-plus'); ?></label>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Phone ICON -->

    <!-- vimeo ICON -->
    <div class="row sfsiplus_vimeo_section">
        <h2 class="sfsicls_vimeo">
            <?php _e('Vimeo', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('When clicked on, users will get directed to your Vimeo page.', 'ultimate-social-media-plus'); ?>
            </p>
            <p class="radio_section fb_url  cus_link instagram_space">
                <label style="margin-left:0!important" class="sfsi_premium_label_width">
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_vimeo_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_plus_vimeo_pageUrl']) && $option2['sfsi_plus_vimeo_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_vimeo_pageUrl']) : ''; ?>" placeholder="http://" class="add" />
            </p>
        </div>
    </div>
    <!-- vimeo ICON -->

    <!-- Soundcloud ICON -->
    <div class="row sfsiplus_soundcloud_section">
        <h2 class="sfsicls_soundcloud">
            <?php _e('Soundcloud', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('When clicked on, users will get directed to your Soundcloud page.', 'ultimate-social-media-plus'); ?>
            </p>
            <p class="radio_section fb_url  cus_link instagram_space">
                <label style="margin-left:0!important" class="sfsi_premium_label_width">
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_soundcloud_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_plus_soundcloud_pageUrl']) && $option2['sfsi_plus_soundcloud_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_soundcloud_pageUrl']) : ''; ?>" placeholder="http://" class="add" />
            </p>
        </div>
    </div>
    <!-- Soundcloud ICON -->

    <!-- Yummly ICON -->
    <div class="row sfsiplus_yummly_section">

        <h2 class="sfsicls_yummly"><?php _e('Yummly', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The Yummly icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder inr_cont sfsi_align">

                <input name="sfsi_plus_yummlyVisit_option" <?php echo ($option2['sfsi_plus_yummlyVisit_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Visit my Yummly account at:', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_yummly_pageUrl" type="url" placeholder="" value="<?php echo $option2['sfsi_plus_yummly_pageUrl']; ?>" class="add" />

            </div>

            <div class="row notopborder">

                <input name="sfsi_plus_yummlyShare_option" <?php echo ($option2['sfsi_plus_yummlyShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Save your recipe to their Yums/recipe box', 'ultimate-social-media-plus'); ?>
                </label>

            </div>

        </div>

    </div>
    <!-- Yummly ICON -->

    <!-- Flickr ICON -->
    <div class="row sfsiplus_flickr_section">
        <h2 class="sfsicls_flickr">
            <?php _e('Flickr', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('When clicked on, users will get directed to your Flickr page.', 'ultimate-social-media-plus'); ?>
            </p>
            <p class="radio_section fb_url  cus_link instagram_space">
                <label style="margin-left:0!important" class="sfsi_premium_label_width">
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_flickr_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_plus_flickr_pageUrl']) && $option2['sfsi_plus_flickr_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_flickr_pageUrl']) : ''; ?>" placeholder="http://" class="add" />
            </p>
        </div>
    </div>
    <!-- Flickr ICON -->

    <!-- Reddit ICON -->
    <div class="row sfsiplus_reddit_section">
        <h2 class="sfsicls_reddit">
            <?php _e('Reddit', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('Here you have two options:', 'ultimate-social-media-plus'); ?>
            </p>
            <div class="radio_section fb_url  cus_link instagram_space">
                <ul>
                    <li>
                        <p class="sfsi_plus_pageurl_type" style="margin-left:-1px!important">
                            <input class="styled" type="radio" value="share" name="sfsi_plus_reddit_url_type" <?php echo ($option2['sfsi_plus_reddit_url_type'] == 'share') ?  'checked="true"' : ''; ?>>
                            <label><?php _e("Allow users to share the page on Reddit", 'ultimate-social-media-plus'); ?></label>
                        </p>
                    </li>
                    <li>
                        <p class="sfsi_plus_pageurl_type" style="margin-left:-1px!important">
                            <input class="styled" type="radio" value="url" name="sfsi_plus_reddit_url_type" <?php echo ($option2['sfsi_plus_reddit_url_type'] == 'url') ?  'checked="true"' : ''; ?>>
                            <label><?php _e("Allow users to visit my Redit page", 'ultimate-social-media-plus'); ?></label>
                        </p>
                        <label style="margin-left:0px!important" class="sfsi_premium_label_width">URL:</label>
                        <input name="sfsi_plus_reddit_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_plus_reddit_pageUrl']) && $option2['sfsi_plus_reddit_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_reddit_pageUrl']) : ''; ?>" placeholder="http://" class="add" />
                    </li>
                </ul>

            </div>
        </div>
    </div>
    <!-- Reddit ICON -->

    <!-- Tumblr ICON -->
    <div class="row sfsiplus_tumblr_section">
        <h2 class="sfsicls_tumblr"><?php _e('Tumblr', 'ultimate-social-media-plus'); ?></h2>
        <div class="inr_cont">
            <p>
                <?php _e('When clicked on, users will get directed to your Tumblr page.', 'ultimate-social-media-plus'); ?>
            </p>
            <p class="radio_section fb_url  cus_link instagram_space">
                <label style="margin-left:0px!important" class="sfsi_premium_label_width">
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_tumblr_pageUrl" type="text" value="<?php echo (isset($option2['sfsi_plus_tumblr_pageUrl']) && $option2['sfsi_plus_tumblr_pageUrl'] != '') ? esc_attr($option2['sfsi_plus_tumblr_pageUrl']) : ''; ?>" placeholder="http://" class="add" />
            </p>
        </div>
    </div>
    <!-- Tumblr ICON -->

    <!-- share button -->
    <div class="row sfsiplus_share_section">
        <h2 class="sfsicls_share">
            <?php _e('Share', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
                <?php _e('Nothing needs to be done here – your visitors will be able to share your site via «all the other» social media sites.', 'ultimate-social-media-plus'); ?>
                <a class="rit_link pop-up" href="javascript:;" data-id="share-s2">
                    (<?php _e('(see an example)', 'ultimate-social-media-plus'); ?>).
                </a>
            </p>
        </div>
    </div>
    <!-- share end -->

    <!-- HOUZZ ICON -->
    <div class="row sfsiplus_houzz_section">

        <h2 class="sfsicls_houzz"><?php _e('Houzz', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel">
            <?php //_e( 'The Houzz icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus' ); 
            ?>
            <?php _e('When clicked on, users will get directed to your Houzz page', 'ultimate-social-media-plus'); ?>
        </label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder inr_cont">

                <!-- <input name="sfsi_plus_houzzVisit_option" <?php //echo ($option2['sfsi_plus_houzzVisit_option']=='yes') ?  'checked="true"' : '' ;
                                                                ?>  type="checkbox" value="yes" class="styled"/> -->

                <label class="sfsiLabel pageUrlLabel sfsi_premium_label_width" style="margin-left:0!important;padding: 0 33px 0 0;">
                    <?php  //_e( 'Visit my Houzz profile page at:', 'ultimate-social-media-plus' );
                    ?>
                    <?php _e('URL:', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_houzz_pageUrl" type="url" placeholder="" value="<?php echo $option2['sfsi_plus_houzz_pageUrl']; ?>" class="add" />

            </div>

            <!-- <div class="row notopborder">

	        	<input name="sfsi_plus_houzzShare_option" <?php //echo ($option2['sfsi_plus_houzzShare_option']=='yes') ?  'checked="true"' : '' ;
                                                            ?>  type="checkbox" value="yes" class="styled"/>

	            <label class="sfsiLabel">
	        		<?php  //_e( 'Save photos to Ideabooks*', 'ultimate-social-media-plus');
                    ?>
	        	</label>

	        	<?php //$shareSectionClass = 'yes' == $option2['sfsi_plus_houzzShare_option'] ? 'show' : 'hide'; 
                ?>

	        	<div class="row notopborder info <?php //echo $shareSectionClass; 
                                                    ?> houzzideabooks">
	        		<p>
	        			* <?php //_e(' A Houzz ideabook is a place where you can store ideas and build the house of your dreams'); 
                            ?>
	        		</p>
	        	</div>

	        	<div class="row notopborder inr_cont detailinfo <?php //echo $shareSectionClass; 
                                                                ?> houzzideabooks">

		            <label class="sfsiLabel">
		        		<?php  //_e( 'Your Houzz Website ID', 'ultimate-social-media-plus');
                        ?>
		        	</label>

		            <input name="sfsi_plus_houzz_websiteId" type="url" placeholder="e.g. 52966" value="<?php //echo $option2['sfsi_plus_houzz_websiteId'];
                                                                                                        ?>" class="add" />
	        	</div>

	        	<div class="row notopborder inr_cont detailinfo wdetailinfo <?php //echo $shareSectionClass; 
                                                                            ?> houzzideabooks">
	        		<p>
	        			<?php  //_e( 'The Website ID is located at the bottom of the “Advanced Settings” section in your Houzz profile page.', 'ultimate-social-media-plus');
                        ?>
	        		</p>
	        	</div>

        	</div> -->

        </div>

    </div>
    <!-- HOUZZ ICON -->

    <!-- Facebook Messenger ICON -->
    <div class="row sfsiplus_fbmessenger_section">

        <h2 class="sfsicls_fbmessenger"><?php _e('Facebook Messenger', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The Facebook Messenger icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 inr_cont" style="padding:0;width:calc( 100% - 100px )">

            <div class="row notopborder">

                <input name="sfsi_plus_fbmessengerShare_option" <?php echo ($option2['sfsi_plus_fbmessengerShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Share my site with friends (on FB Messenger)', 'ultimate-social-media-plus'); ?>
                </label>
            </div>
            <div class="row notopborder sfsi_premium_only_fbmessangershare" style="margin-left:50px!important;width: calc( 100% - 50px );display:<?php echo ($option2['sfsi_plus_fbmessengerShare_option'] == 'yes') ?  'block' : 'none'; ?> ">
                <p class="radio_section">
                    <label>
                        <?php _e('Facebook App ID: ', 'ultimate-social-media-plus'); ?>
                    </label>
                    <input name="sfsi_plus_fbmessenger_app_id" type="text" value="<?php echo (isset($option2['sfsi_plus_fbmessengerShare_app_id']) && $option2['sfsi_plus_fbmessengerShare_app_id'] != '') ?  esc_attr($option2['sfsi_plus_fbmessengerShare_app_id']) : ''; ?>" placeholder="app_id" class="add" style="float:none" />
                <p>
                    <?php _e('Note: Sharing will still work without an App ID if the user (who wants to share) is logged in Facebook. however, if he\'s not logged in, he will se an error. You can avoid this error if you create a Facebook App, validate the domain and enter the App ID above. See here how to create the Facebook App:', 'ultimate-social-media-plus'); ?>
                    <a style="font-family: inherit!important;text-decoration: underline" href="https://www.ultimatelysocial.com/how-to-create-a-facebook-app/" target="_blank">https://www.ultimatelysocial.com/how-to-create-a-facebook-app/</a>
                </p>
                </p>

            </div>

            <div class="row notopborder inr_cont sfsi_align">

                <input name="sfsi_plus_fbmessengerContact_option" <?php echo ($option2['sfsi_plus_fbmessengerContact_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Contact us (on FB Messenger)', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_fbmessengerContact_url" type="url" placeholder="https://m.me/your_facebook_page_username" value="<?php echo $option2['sfsi_plus_fbmessengerContact_url']; ?>" class="add" />

            </div>

        </div>

    </div>
    <!-- Facebook Messenger ICON -->

    <!-- GAB ICON -->
    <div class="row sfsiplus_gab_section">

        <h2 class="sfsicls_gab"><?php _e('Gab', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('When clicked on, your website/blog will be shared on Gab.', 'ultimate-social-media-plus'); ?></label>

    </div>
    <!-- GAB ICON -->

    <!-- Mix ICON -->
    <div class="row sfsiplus_mix_section">

        <h2 class="sfsicls_mix"><?php _e('Mix.com (formerly StumbleUpon)', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The Mix Icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder">

                <input name="sfsi_plus_mixShare_option" <?php echo ($option2['sfsi_plus_mixShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Share my blog on Mix.com', 'ultimate-social-media-plus'); ?>
                </label>

            </div>
            <div class="row notopborder inr_cont">

                <input name="sfsi_plus_mixVisit_option" <?php echo ($option2['sfsi_plus_mixVisit_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Visit my Mix.com Page:', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_mixVisit_url" type="url" placeholder="https://mix.com/[username]" value="<?php echo ($option2['sfsi_plus_mixVisit_url'] != '') ?  $option2['sfsi_plus_mixVisit_url'] : ''; ?>" class="add" />

            </div>

        </div>

    </div>
    <!-- Mix ICON -->

    <!-- Ok ICON -->
    <div class="row sfsiplus_ok_section">

        <h2 class="sfsicls_ok"><?php _e('OdnoKlassniki', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The OK icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder inr_cont">

                <input name="sfsi_plus_okVisit_option" <?php echo ($option2['sfsi_plus_okVisit_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Visit my OK page at:', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_okVisit_url" type="url" placeholder="" value="<?php echo $option2['sfsi_plus_okVisit_url']; ?>" class="add" />

            </div>

            <div class="row notopborder" style="margin-bottom: 7px;">

                <input name="sfsi_plus_okLike_option" <?php echo ($option2['sfsi_plus_okLike_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Like my site (on OK)', 'ultimate-social-media-plus'); ?>
                </label>

            </div>

            <div class="row notopborder inr_cont">

                <input name="sfsi_plus_okSubscribe_option" <?php echo ($option2['sfsi_plus_okSubscribe_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Subscribe to me (on OK):', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_okSubscribe_userid" type="url" placeholder="Enter your Ok profile userid" value="<?php echo $option2['sfsi_plus_okSubscribe_userid']; ?>" class="add" />

            </div>

        </div>

    </div>
    <!-- Ok ICON -->

    <!-- Telegram ICON -->
    <div class="row sfsiplus_telegram_section">

        <h2 class="sfsicls_telegram"><?php _e('Telegram', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The Telegram icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder">

                <input name="sfsi_plus_telegramShare_option" <?php echo ($option2['sfsi_plus_telegramShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Share my blog on Telegram', 'ultimate-social-media-plus'); ?>
                </label>

            </div>

            <div class="row notopborder inr_cont">

                <input name="sfsi_plus_telegramMessage_option" <?php echo ($option2['sfsi_plus_telegramMessage_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Message me on Telegram:', 'ultimate-social-media-plus'); ?>
                </label>

                <!-- <div class="row notopborder topmargin10 col-md-offset-03">

                	<p class="sfsi_plus_pageurl_type">

                		<input class="styled" type="radio" value="message" name="sfsi_plus_telegram_url_type" <?php //echo ($option2['sfsi_plus_telegram_url_type']=='message') ?  'checked="true"' : '' ;
                                                                                                                ?>>
						<label><?php //_e("Allow users to send me a Telegram-message (Only works on mobile phones)", 'ultimate-social-media-plus'); 
                                ?></label>
                    </p>

                </div> -->

                <div class="row topmargin10 notopborder col-md-offset-03 sfsi_premium_d_flex_center">
                    <label class="sfsiLabel"><?php _e("Pre-filled message:", 'ultimate-social-media-plus'); ?></label>
                    <input name="sfsi_plus_telegram_message" type="text" value="<?php echo (isset($option2['sfsi_plus_telegram_message']) && $option2['sfsi_plus_telegram_message'] != '') ? esc_attr($option2['sfsi_plus_telegram_message']) : ''; ?>" placeholder="<?php _e('Hey, I like your website.', 'ultimate-social-media-plus'); ?>" class="add" />
                </div>

                <div class="row notopborder col-md-offset-03 sfsi_premium_d_flex_center">
                    <label class="sfsiLabel"><?php _e("My Telegram username:", 'ultimate-social-media-plus'); ?></label>
                    <input name="sfsi_plus_telegram_username" type="text" value="<?php echo (isset($option2['sfsi_plus_telegram_username']) && $option2['sfsi_plus_telegram_username'] != '') ? esc_attr($option2['sfsi_plus_telegram_username']) : ''; ?>" placeholder="" class="add" />
                </div>

                <!--<div class="row notopborder col-md-offset-03">
					<div class="col-md-3"></div>
					<div class="col-md-9">
					<label class="clear sfsiLabel noteLabel"><a target="_blank" href="https://telegram.org/blog/usernames-and-secret-chats-v2"><?php //_e("*Don't have username. Click here to know 'How to setup a telegram username'" , 'ultimate-social-media-plus');
                                                                                                                                                ?></a></label>
					</div>
				</div>-->

                <!--
            	<div class="row notopborder topmargin10 col-md-offset-03">

                	<p class="sfsi_plus_pageurl_type">

                		<input class="styled" type="radio" value="share" name="sfsi_plus_telegram_url_type" <?php //echo ($option2['sfsi_plus_telegram_url_type']=='share') ?  'checked="true"' : '' ;
                                                                                                            ?>>
						<label><?php //_e("Allow users to share the page via Telegram", 'ultimate-social-media-plus'); 
                                ?></label>
                    </p>

                </div>

                <div class="row notopborder col-md-offset-065">
					<textarea name="sfsi_plus_telegram_share_page" id="sfsi_plus_telegram_share_page" type="text" class="add_txt" placeholder="" /><?php //echo (isset($option2['sfsi_plus_telegram_share_page']) && $option2['sfsi_plus_telegram_share_page']!='') ?  stripslashes($option2['sfsi_plus_telegram_share_page']) : '${title} ${link}' ;
                                                                                                                                                    ?></textarea>
               </div> -->

            </div>

        </div>

    </div>
    <!-- Telegram ICON -->

    <!-- VK ICON -->
    <div class="row sfsiplus_vk_section">

        <h2 class="sfsicls_vk"><?php _e('VK', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The VK icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder inr_cont sfsi_align">

                <input name="sfsi_plus_vkVisit_option" <?php echo ($option2['sfsi_plus_vkVisit_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Visit my VK page at:', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_vkVisit_url" type="url" placeholder="" value="<?php echo $option2['sfsi_plus_vkVisit_url']; ?>" class="add" />

            </div>

            <div class="row notopborder">

                <input name="sfsi_plus_vkShare_option" <?php echo ($option2['sfsi_plus_vkShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Share my blog with friends (on VK)', 'ultimate-social-media-plus'); ?>
                </label>

            </div>

            <!--<div class="row notopborder inr_cont">

				<input name="sfsi_plus_vkLike_option" <?php //echo ($option2['sfsi_plus_vkLike_option']=='yes') ?  'checked="true"' : '' ;
                                                        ?>  type="checkbox" value="yes" class="styled"/>

	            <label class="sfsiLabel">
	        		<?php  //_e( 'Like my site (on VK)', 'ultimate-social-media-plus' );
                    ?>
	        	</label>

        	</div>

			<div class="row notopborder inr_cont">

				<input name="sfsi_plus_vkFollow_option" <?php echo ($option2['sfsi_plus_vkFollow_option'] == 'yes') ?  'checked="true"' : ''; ?>  type="checkbox" value="yes" class="styled"/>

	            <label class="sfsiLabel">
	        		<?php _e('Follow me (on VK)', 'ultimate-social-media-plus'); ?>
	        	</label>

	            <input name="sfsi_plus_vkFollow_url" type="url" placeholder="https://vk.com/[profile or page name]" value="<?php echo $option2['sfsi_plus_vkFollow_url']; ?>" class="add" />

        	</div>-->

        </div>

    </div>
    <!-- VK ICON -->

    <!-- BLUESKY ICON -->
    <div class="row sfsiplus_bluesky_section">
        <h2 class="sfsicls_bluesky">
			<?php _e('Bluesky', 'ultimate-social-media-plus'); ?>
        </h2>
        <div class="inr_cont">
            <p>
				<?php _e('When clicked on, users can share your content on their Bluesky', 'ultimate-social-media-plus'); ?>.
            </p>
            <p class="radio_section fb_url extra_sp" style="margin-top: 11px;">
                <input name="sfsi_plus_blueskyShare_option" <?php echo ($option2['sfsi_plus_blueskyShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label>
					<?php _e('Share my blog with friends (on Bluesky)', 'ultimate-social-media-plus'); ?>
                </label>
            </p>

        </div>
    </div>
    <!-- END BLUESKY ICON -->

    <!-- Weibo ICON -->
    <div class="row sfsiplus_weibo_section">

        <h2 class="sfsicls_weibo"><?php _e('Sina Weibo', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The Weibo icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder inr_cont sfsi_align">

                <input name="sfsi_plus_weiboVisit_option" <?php echo ($option2['sfsi_plus_weiboVisit_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Visit my Sina Weibo page at:', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_weiboVisit_url" type="url" placeholder="" value="<?php echo $option2['sfsi_plus_weiboVisit_url']; ?>" class="add" />

            </div>

            <div class="row notopborder" style="margin-bottom:7px">

                <input name="sfsi_plus_weiboShare_option" <?php echo ($option2['sfsi_plus_weiboShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Share my blog on Sina Weibo', 'ultimate-social-media-plus'); ?>
                </label>

            </div>

            <div class="row notopborder inr_cont">

                <input name="sfsi_plus_weiboLike_option" <?php echo ($option2['sfsi_plus_weiboLike_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Like my page with Sina Weibo', 'ultimate-social-media-plus'); ?>
                </label>

            </div>

        </div>

    </div>
    <!-- Weibo ICON -->

    <!-- WeChat ICON -->
    <div class="row sfsiplus_wechat_section" style="display: <?php echo isset($option1["sfsi_plus_wechat_display"]) && $option1["sfsi_plus_wechat_display"] == "yes" ? 'block' : 'none';  ?>">

        <h2 class="sfsicls_wechat"><?php _e('WeChat', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The WeChat icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do.', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder inr_cont">

                <input name="sfsi_plus_wechatFollow_option" <?php echo (isset($option2['sfsi_plus_wechatFollow_option']) && $option2['sfsi_plus_wechatFollow_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel" style="float:none">
                    <?php _e('Follow me on WeChat', 'ultimate-social-media-plus'); ?>
                </label>
                <div class="sfsi_premium_wechat_follow_desc" style="display:<?php echo isset($option2['sfsi_plus_wechatFollow_option']) && $option2['sfsi_plus_wechatFollow_option'] == 'yes' ?  'block"' : 'none'  ?>">
                    <p>
                        <?php _e('When clicking the Follow us/me icon, a pop-up box containing the QR code associated with your WeChat account will open so that your visitors can easily scan it with their mobile phones and follow/chat with you.', 'ultimate-social-media-plus'); ?>
                    </p>
                    <div class="sfsi_plus_wechat_instruction  <?php echo isset($option2['sfsi_premium_wechat_scan_image']) && $option2['sfsi_premium_wechat_scan_image'] !== "" ?  'hide' : 'show'  ?>">
                        <p><?php _e('Upload the WeChat QR code image below (1. Click on button below, 2. Select or upload the icon into the media gallery, 3. Click on “Use this media”). To find the image, click on “Me” on the WeChat app menu and then tap the little QR code icon in the top left. ', 'ultimate-social-media-plus'); ?>
                        </p>
                        <br>
                        <div style="text-align:center">
                            <a href="" onclick="upload_image_wechat_scan(event)" class="upload_butt"><?php _e('Upload QR code image ', 'ultimate-social-media-plus'); ?></a>
                        </div>
                    </div>
                    <div class="sfsi_plus_wechat_display  <?php echo isset($option2['sfsi_premium_wechat_scan_image']) && $option2['sfsi_premium_wechat_scan_image'] !== "" ?  'show' : 'hide'  ?>" style="text-align:center">
                        <img class="sfsi_plus_wechat_folow_image" style="width:300px;height:250px;" src="<?php echo isset($option2['sfsi_premium_wechat_scan_image']) && $option2['sfsi_premium_wechat_scan_image'] !== "" ?  $option2['sfsi_premium_wechat_scan_image'] : ''  ?>" alt="<?php _e('WeChat Follow Scan Image', 'ultimate-social-media-plus'); ?>"><br><br>
                        <input type="hidden" name="sfsi_premium_wechat_scan_image" value="<?php echo isset($option2['sfsi_premium_wechat_scan_image']) && $option2['sfsi_premium_wechat_scan_image'] !== "" ?  $option2['sfsi_premium_wechat_scan_image'] : ''  ?>" />
                        <input type="hidden" name="sfsi_plus_deleteWebChatFollow_nonce" value="<?php echo wp_create_nonce('sfsi_plus_deleteWebChatFollow'); ?>" />
                        <a href="" onclick="sfsi_premium_delete_wechat_scan_upload(event,'<?php echo isset($option2['sfsi_premium_wechat_scan_image']) && $option2['sfsi_premium_wechat_scan_image'] !== "" ?  "from-server" : 'local'  ?>')" class="upload_butt"><?php _e('Delete QR code image ', 'ultimate-social-media-plus'); ?></a>
                    </div>

                </div>

            </div>

            <div class="row notopborder inr_cont">

                <input name="sfsi_plus_wechatShare_option" <?php echo (isset($option2['sfsi_plus_wechatShare_option']) && $option2['sfsi_plus_wechatShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Share my page on WeChat', 'ultimate-social-media-plus'); ?>
                </label>

            </div>

        </div>

    </div>
    <!-- Weibo ICON -->

    <!-- Xing ICON -->
    <div class="row sfsiplus_xing_section">

        <h2 class="sfsicls_xing"><?php _e('Xing', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The Xing icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">

            <div class="row notopborder inr_cont sfsi_premium_d_flex_center">

                <input name="sfsi_plus_xingVisit_option" <?php echo ($option2['sfsi_plus_xingVisit_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Visit my Xing page at:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_xingVisit_url" type="url" placeholder="" value="<?php echo $option2['sfsi_plus_xingVisit_url']; ?>" class="add" />

            </div>

            <div class="row notopborder inr_cont sfsi_premium_d_flex_center">

                <input name="sfsi_plus_xingFollow_option" <?php echo ($option2['sfsi_plus_xingFollow_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />
                <label class="sfsiLabel">
                    <?php _e('Follow me (on Xing)', 'ultimate-social-media-plus'); ?>
                </label>

                <input name="sfsi_plus_xingFollow_url" type="url" placeholder="Ex. https://www.xing.com/news/pages/marketing-werbung-44" value="<?php echo $option2['sfsi_plus_xingFollow_url']; ?>" class="add" />
            </div>

            <div class="row notopborder">

                <input name="sfsi_plus_xingShare_option" <?php echo ($option2['sfsi_plus_xingShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Share my blog on Xing', 'ultimate-social-media-plus'); ?>
                </label>

            </div>

        </div>

    </div>
    <!-- Xing ICON -->

    <!-- Mastodon ICON -->
    <div class="row sfsiplus_mastodon_section">

        <h2 class="sfsicls_mastodon"><?php _e('Mastodon', 'ultimate-social-media-plus'); ?></h2>

        <label class="sfsiLabel infoLabel"><?php _e('The Mastodon icon can perform several actions. Pick below which ones it should perform. If you select several options, then users can select what they want to do:', 'ultimate-social-media-plus'); ?></label>

        <div class="col-md-12 sfsi_premium_tab2_checkbox_margin">
            <div class="row notopborder inr_cont sfsi_premium_d_flex_center">
                <input name="sfsi_plus_mastodonVisit_option" <?php echo ($option2['sfsi_plus_mastodonVisit_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Visit my Mastodon profile page at:', 'ultimate-social-media-plus'); ?>
                </label>
                <input name="sfsi_plus_mastodonVisit_url" type="url" placeholder="https://mastodon.social/@username" value="<?php echo $option2['sfsi_plus_mastodonVisit_url']; ?>" class="add" />
            </div>

            <div class="row notopborder">
                <input name="sfsi_plus_mastodonShare_option" <?php echo ($option2['sfsi_plus_mastodonShare_option'] == 'yes') ?  'checked="true"' : ''; ?> type="checkbox" value="yes" class="styled" />

                <label class="sfsiLabel">
                    <?php _e('Share my blog on Mastodon', 'ultimate-social-media-plus'); ?>
                </label>
            </div>
        </div>
    </div>
    <!-- Mastodon ICON -->

    <!-- COPY LINK ICON -->
    <div class="row sfsiplus_copylink_section">
        <h2 class="sfsicls_copylink"><?php _e('Copy link', 'ultimate-social-media-plus'); ?></h2>
        <label class="sfsiLabel infoLabel"><?php _e('Copies the link of the webpage to clipboard.', 'ultimate-social-media-plus'); ?></label>
    </div>
    <!-- COPY LINK ICON -->

    <!-- Custom icon section start here -->
    <div class="plus_custom-links sfsiplus_custom_section">
        <?php
        $costom_links = isset($option2['sfsi_plus_CustomIcon_links']) ? maybe_unserialize($option2['sfsi_plus_CustomIcon_links']) : '';
        $count = 1;
        for ($i = $first_key; $i <= $endkey; $i++) :
            if (!empty($icons[$i])) :
        ?>
                <div class="row  sfsiICON_<?php echo $i; ?> cm_lnk">
                    <h2 class="custom">
                        <span class="customstep2-img">
                            <img src="<?php echo (!empty($icons[$i])) ?  esc_url($icons[$i]) : SFSI_PLUS_PLUGURL . 'images/custom.png'; ?>" alt="custom" id="CImg_<?php echo $new_element; ?>" />
                        </span>
                        <span class="sfsiCtxt">
                            <?php _e('Custom', 'ultimate-social-media-plus'); ?>
                            <?php echo $count; ?>
                        </span>
                    </h2>
                    <div class="inr_cont ">
                        <p>
                            <?php _e('Where do you want this icon to link to?', 'ultimate-social-media-plus'); ?>
                        </p>

                        <p class="radio_section fb_url sfsiplus_custom_section cus_link ">

                            <label>
                                <?php _e('Link:', 'ultimate-social-media-plus'); ?>
                            </label>
                            <input name="sfsi_plus_CustomIcon_links[]" type="text" value="<?php echo (isset($costom_links[$i]) && $costom_links[$i] != '') ? sanitize_text_field($costom_links[$i]) : ''; ?>" placeholder="http://" class="add" file-id="<?php echo $i; ?>" />
                        </p>

                        <p class="customIconNote">
                            <?php _e('* Note: you can also give the icon a «call me» functionality be entering «tel:» and then the phone number, and give it a + before the country code, e.g. in the case of US (country code = 1) it could be «tel:+145054654654» (without quotes). ', 'ultimate-social-media-plus'); ?>
                        </p>

                        <p class="customIconNote">
                            <?php _e(' Or, give it a «Send me an SMS» functionality by entering «sms://» and then the mobile phone number as mentioned above, e.g. «sms://+145054654654» (without quotes).', 'ultimate-social-media-plus'); ?>
                        </p>

                    </div>
                </div>
        <?php
                $count++;
            endif;
        endfor;
        ?>
    </div>

    <!-- END Custom icon section here -->

    <!-- SAVE BUTTON SECTION -->
    <div class="save_button tab_2_sav">
        <img src="<?php echo SFSI_PLUS_PLUGURL; ?>images/ajax-loader.gif" alt="custom" class="loader-img" />

        <?php $nonce = wp_create_nonce("update_plus_step2"); ?>

        <a href="javascript:;" id="sfsi_plus_save2" title="Save" data-nonce="<?php echo $nonce; ?>">
            <?php _e('Save', 'ultimate-social-media-plus'); ?>
        </a>
    </div>
    <!-- END SAVE BUTTON SECTION   -->
    <a class="sfsiColbtn closeSec section2sfsiColbtn" href="javascript:;">
        <?php _e('Collapse area', 'ultimate-social-media-plus'); ?>
    </a>

    <label class="closeSec"></label>

    <!-- ERROR AND SUCCESS MESSAGE AREA-->
    <p class="red_txt errorMsg" style="display:none;"> </p>
    <p class="green_txt sucMsg" style="display:none;"> </p>

</div>
<!-- END Section 2 "What do you want the icon to do?" main div -->