<?php
class sfsi_plus_SocialHelper
{
  private $url, $timeout = 90;

  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region Twitter
  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  /* get twitter followers */
  function sfsi_get_tweets($username, $tw_settings)
  {
    require_once(SFSI_PLUS_DOCROOT . '/helpers/twitteroauth/twiiterCount.php');
    return sfsi_premium_twitter_followers();
  }


  /********   Twitter sharing title & link functions STARTS  **************/

  public function sfsi_get_custom_share_link($iconName = '', $option5 = null)
  {

    $url      = get_bloginfo('url');
    $post_id  = $this->sfsi_get_the_ID();

    $permalink_structure = get_option('permalink_structure', false);
    if ($option5 == null) {
      $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    }
    $isSharingShortUrl = $this->sfsi_is_url_shortning_on_for_icon($iconName, $option5);

    if ( ( !in_the_loop() && !is_front_page() && !is_home() ) || is_singular() ) {

      if (is_author()) {

        $url   = get_author_posts_url(get_the_author_meta('ID'));
        if (sfsi_premium_is_site_url($url) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
          $url = $option5['sfsi_premium_static_path'] . strrev(str_replace(strrev(site_url()), '', strrev($url)));
        }
      } else if (is_archive()) {

        try {

          $queryObj = get_queried_object();

          if (isset($queryObj) && !empty($queryObj) && is_object($queryObj) && isset($queryObj->term_id)) {
            $termId  = $queryObj->term_id;
            $url     = get_term_link($termId);
          }
          if (sfsi_premium_is_site_url($url) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
            $url = $option5['sfsi_premium_static_path'] . strrev(str_replace(strrev(site_url()), '', strrev($url)));
          }
        } catch (Exception $e) { }
      } else if (is_singular()) {
        $shortlink    = $isSharingShortUrl ? get_shortlink_from_db($post_id) : null;
        $longlink     = get_permalink($post_id);
        $currentUrl   = get_permalink($post_id);
        $longlink     = add_query_arg( $_SERVER['QUERY_STRING'], '', $longlink );
        $currentUrl     = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $longlink     = $currentUrl != $longlink ? $currentUrl : $longlink;
        if (sfsi_premium_is_site_url($url) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
          $longlink = $option5['sfsi_premium_static_path'] . strrev(str_replace(strrev(site_url()), '', strrev($longlink)));
        }
        $url          = ($isSharingShortUrl && $shortlink != null && !empty($shortlink)) ? $shortlink : ((strpos($longlink, '?') == false) ? trailingslashit($longlink) : $longlink );
      } else {
        $url = get_permalink();
      }
    } else if ($post_id) {

      // Not home page
      if (!is_front_page()) {

        $shortlink    = $isSharingShortUrl ? get_shortlink_from_db($post_id) : null;
        $longlink     = get_permalink($post_id);
        $longlink     = add_query_arg( $_SERVER['QUERY_STRING'], '', $longlink );
        $currentUrl   = urldecode(sfsi_plus_current_url());
        $currentUrl   = add_query_arg( $_SERVER['QUERY_STRING'], '', $currentUrl );
        $longlink     = $currentUrl != $longlink && !in_the_loop() ? $currentUrl : $longlink;
        if (sfsi_premium_is_site_url($url) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
          $longlink = $option5['sfsi_premium_static_path'] . strrev(str_replace(strrev(site_url()), '', strrev($longlink)));
        }
        $url          = ($isSharingShortUrl && $shortlink != null && !empty($shortlink)) ? $shortlink :((strpos($longlink, '?') == false) ? trailingslashit($longlink) : $longlink );
      } else if (is_home()) {

        $longlink     = get_permalink($post_id);
        $currentUrl   = urldecode(sfsi_plus_current_url());
        $currentUrl   = add_query_arg( $_SERVER['QUERY_STRING'], '', $currentUrl );
        $longlink     = $currentUrl != $longlink && !in_the_loop() ? $currentUrl : $longlink;
        if (sfsi_premium_is_site_url($url) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
          $longlink = $option5['sfsi_premium_static_path'] . strrev(str_replace(strrev(site_url()), '', strrev($longlink)));
        }
        $url          = ((strpos($longlink, '?') == false) ? trailingslashit($longlink) : $longlink );
      }
    } else if (is_front_page()) {
      $url = site_url();
      if (sfsi_premium_is_site_url($url) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
        $url = $option5['sfsi_premium_static_path'];
      }
      $url          = trailingslashit($url);
      $url          = add_query_arg( $_SERVER['QUERY_STRING'], '', $url );
    }

    if($post_id==0 && $url == site_url()){
      // not cached short_api but can't save as post_id is 0.
      $shortlink    = $isSharingShortUrl ? sfsi_url_short_api(urldecode(sfsi_plus_current_url())) : null;
          $longlink     = urldecode(sfsi_plus_current_url());
          $currentUrl   = urldecode(sfsi_plus_current_url());
          $longlink     = $currentUrl != $longlink ? $currentUrl : $longlink;
          if (sfsi_premium_is_site_url($url) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
            $longlink = $option5['sfsi_premium_static_path'] . strrev(str_replace(strrev(site_url()), '', strrev($longlink)));
          }
          $url          = ($isSharingShortUrl && $shortlink != null && !empty($shortlink)) ? $shortlink : ((strpos($longlink, '?') == false) ? trailingslashit($longlink) : $longlink );
    }

    $url  = (isset($permalink_structure) && !empty($permalink_structure)) || (strpos($url, '?') == false) ? $url : untrailingslashit($url);

    return $url;
  }

  public function sfsi_is_url_shortning_on_for_icon($iconName = '', $option5 = null)
  {

    $isUrlShortingOnForIcon = false;

    if (strlen($iconName) > 0 && is_string($iconName)) {
      if ($option5 == null) {
        $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
      }

      if (is_string($option5['sfsi_premium_url_shortner_icons_names_list'])) {
        $arrSelectedIcons   = (isset($option5['sfsi_premium_url_shortner_icons_names_list'])) ? maybe_unserialize($option5['sfsi_premium_url_shortner_icons_names_list']) : array();
      } else {
        $arrSelectedIcons = $option5['sfsi_premium_url_shortner_icons_names_list'];
      }

      if (!empty($arrSelectedIcons)) :

        if (in_array($iconName, $arrSelectedIcons)) :

          $isUrlShortingOnForIcon = true;

        endif;

      endif;
    }
    return $isUrlShortingOnForIcon;
  }

  public function sfsi_get_custom_tweet_title()
  {

    $title      = $this->sfsi_get_the_title();
    $post_id    = $this->sfsi_get_the_ID();

    if ($post_id) {
      $custom_title = get_post_meta($post_id, 'social-twitter-description', true);
      $title        = (isset($custom_title) && strlen($custom_title) > 0 && $custom_title != null) ? $custom_title : $title;
    }

    return $title;
  }

  public function sfsi_get_custom_tweet_text($sfsi_section5 = null)
  {

    $post_id    = $this->sfsi_get_the_ID();
    if ($sfsi_section5 == null) {
      $sfsi_section5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    }
    $sfsi_que6_tweet = (isset($sfsi_section5['sfsi_plus_twitter_aboutPageText'])) ? $sfsi_section5['sfsi_plus_twitter_aboutPageText'] : '${title} ${link}';
    if ($post_id) {

      $custom_title = get_post_meta($post_id, 'social-twitter-description', true);
      $link  = $this->sfsi_get_custom_share_link('twitter', $sfsi_section5);

      if (isset($custom_title) && strlen($custom_title) > 0 && $custom_title != null) {
        $twitter_text = $custom_title . ' ' . $link;
      } else if (isset($sfsi_que6_tweet) && strlen($sfsi_que6_tweet) > 0) {
        $twitter_text = $sfsi_que6_tweet;
        $twitter_text = stripslashes($twitter_text); //stripslashes(str_replace('"', "", str_replace("'", "", $twitter_text)));
        $twitter_text = str_replace('${title}', $this->sfsi_get_the_title(), $twitter_text);
        $twitter_text = str_replace('${link}', $link, $twitter_text);
      } else {
        $twitter_text = $this->sfsi_get_the_title() . ' ' . $link;
      }
    } else {
      $twitter_text = $sfsi_que6_tweet;
      $twitter_text = stripslashes($twitter_text); //stripslashes(str_replace('"', "", str_replace("'", "", $twitter_text)));
      $twitter_text = str_replace('${title}', $this->sfsi_get_the_title(), $twitter_text);
      $twitter_text = str_replace('${link}',  $this->sfsi_get_custom_share_link('twitter', $sfsi_section5), $twitter_text);
    }

    $twitter_text = html_entity_decode(strip_tags($twitter_text), ENT_QUOTES, 'UTF-8');
    return $twitter_text;
  }


  /* create on page twitter follow option */
  public function sfsi_twitterFollow($tw_username, $icons_language)
  {
    $twitter_html = '<a href="https://twitter.com/' . trim($tw_username) . '" class="twitter-follow-button"  data-show-count="false" data-lang="' . $icons_language . '" data-show-screen-name="false"></a>';
    return $twitter_html;
  }

  /* create on page twitter share icon */
  public function sfsi_twitterShare($permalink, $tweettext, $icons_language = '')
  {
    $option5      = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    $tweettext    = $this->sfsi_get_custom_tweet_text($option5);

    $link         = $this->sfsi_get_custom_share_link('twitter', $option5);

    preg_match_all('!https?://\S+!', $tweettext, $matches);
    $countUrl    = false;

    if (isset($matches[0]) && is_array($matches[0])) {

      $permalink_structure = get_option('permalink_structure', false);

      $link = isset($permalink_structure) && !empty($permalink_structure) ? trailingslashit($link) : untrailingslashit($link);

      if (in_array($link, $matches[0])) {

        $countUrl = true;
      }
    }

    $dataUrl      = ($countUrl) ? ' ' : $link;

    $forShortedUrl = 'no' === $option5['sfsi_plus_url_shorting_api_type_setting'] ? $dataUrl : ' ';

    $twitter_html = '<a data-url="' . $forShortedUrl . '" rel="nofollow" href="http://twitter.com/share" data-count="none" class="sr-twitter-button twitter-share-button" data-lang="' . $icons_language . '" data-text="' . $tweettext . '" ></a>';

    return $twitter_html;
  }

  /* create on page twitter share icon with count */
  public function sfsi_twitterSharewithcount($permalink, $tweettext, $show_count, $icons_language, $option5 = null)
  {
    //$tweettext    = $this->sfsi_get_custom_tweet_title();
    if ($option5 == null) {
      $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    }
    $link         = $this->sfsi_get_custom_share_link('twitter', $option5);
    $tweettext    = $this->sfsi_get_custom_tweet_text($option5);

    preg_match_all('!https?://\S+!', $tweettext, $matches);
    $countUrl    = false;

    if (isset($matches[0]) && is_array($matches[0])) {
      if (in_array(trailingslashit($link), $matches[0])) {
        $countUrl = true;
      }
    }
    $dataUrl      = ($countUrl) ? ' ' : $link;

    $forShortedUrl = 'no' === $option5['sfsi_plus_url_shorting_api_type_setting'] ? $dataUrl : ' ';

    if ($show_count) {
      $twitter_html = '<a href="https://twitter.com/share" class="sr-twitter-button twitter-share-button" data-lang="' . $icons_language . '" data-counturl="' . $link . '" data-url="' . $forShortedUrl . '" data-text="' . $tweettext . '" ></a>';
    } else {
      $twitter_html = '<a href="https://twitter.com/share" data-count="none" class="sr-twitter-button twitter-share-button" data-lang="' . $icons_language . '" data-url="' . $forShortedUrl . '" data-text="' . $tweettext . '" ></a>';
    }
    return $twitter_html;
  }


  /*************************************   Twitter sharing title & link functions CLOSES    ***********************************************/

  /*twitter like*/
  function sfsi_plus_twitterlike($permalink, $show_count)
  {
    $twitter_text = '';
    return $this->sfsi_twitterShare($permalink, $twitter_text);
  }
  /*twitter like*/

  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region LinkedIn
  #---------------------------------------------------------------------------------------------------------------------------------------------------------

  /* get linkedIn counts */
  function sfsi_get_linkedin( $url )
  {
    $json_string = $this->file_get_contents_curl(
      'https://www.linkedin.com/countserv/count/share?format=json&url=' . $url
    );
    $json = json_decode($json_string, true);
    return isset($json['count']) ? intval($json['count']) : 0;
  }

  /* get linkedIn follower */
  function sfsi_getlinkedin_follower($sfsi_plus_ln_company, $APIsettings)
  {
    require_once(SFSI_PLUS_DOCROOT . '/helpers/linkedin-api/linkedin-api.php');

    // $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
    // $url = $scheme.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

    $url = urldecode(sfsi_plus_current_url());

    $linkedin = new Plus_LinkedIn(
      $APIsettings['sfsi_plus_ln_api_key'],
      $APIsettings['sfsi_plus_ln_secret_key'],
      $APIsettings['sfsi_plus_ln_oAuth_user_token'],
      $url
    );
    $followers = $linkedin->getCompanyFollowersByName($sfsi_plus_ln_company);
    if (strpos($followers, '404') === false) {
      return  strip_tags($followers);
    } else {
      return  0;
    }
  }

  /* create linkedIn  follow button */
  public function sfsi_LinkedInFollow($company_id)
  {
    return  $ifollow = '<script type="IN/FollowCompany" data-id="' . $company_id . '"></script>';
  }

  /* create linkedIn  recommend button */
  public function sfsi_LinkedInRecommend($company_name, $product_id)
  {
    return  $ifollow = '<script type="IN/RecommendProduct" data-company="' . $company_name . '" data-product="' . $product_id . '"></script>';
  }


  /* create linkedIn  share button */
  public function sfsi_LinkedInShare($url = '')
  {
    $url = (isset($url)) ? $url : home_url();
    return  $ifollow = '<script type="IN/Share" data-url="' . $url . '"></script>';
  }


  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region Facebook
  #---------------------------------------------------------------------------------------------------------------------------------------------------------


  function sfsi_get_fb($url)
  {

    $fbSocialHelper = new sfsiFacebookSocialHelper();

    $count  = [
        'reaction_count' => 0,
        'share_count' => 0,
        'comment_plugin_count' => 0,
        'comment_count' =>0
    ];

    if ($fbSocialHelper->sfsi_isFbCachingActive()) {

      $postId = $this->sfsi_get_the_ID();
      if (isset($postId) && is_numeric($postId) && $postId > 0) :

        $count  = $fbSocialHelper->sfsi_get_cached_fbcount_for_postId($postId);

      else :

        $url          = trailingslashit($url);
        $homeHttpsUrl = trailingslashit(home_url());
        if ($url == $homeHttpsUrl) :

          $count  = $fbSocialHelper->sfsi_get_cached_fbcount_for_postId(-1);

        endif;

      endif;
    } else {

      $count = $fbSocialHelper->sfsi_get_uncachedfbcount($url);
    }

    return $count;
  }


  /* get facebook page likes */
  function sfsi_get_fb_pagelike($url)
  {
    $option4  = maybe_unserialize(get_option('sfsi_premium_section4_options', false));

    $appid    = (isset($option4['sfsi_plus_facebook_appid']) && !empty($option4['sfsi_plus_facebook_appid']))
      ? $option4['sfsi_plus_facebook_appid']
      : '713531893926550';

    $appsecret  = (isset($option4['sfsi_plus_facebook_appsecret']) && !empty($option4['sfsi_plus_facebook_appsecret']))
      ? $option4['sfsi_plus_facebook_appsecret']
      : '4ae60ab4041394d6670bff6e5f8ff0ae';

    $json_url   = 'https://graph.facebook.com/' . $url . '?fields=fan_count&access_token=' . $appid . '|' . $appsecret;
    $json_string = $this->file_get_contents_curl($json_url);
    
    $json     = json_decode($json_string, true);
    return isset($json['fan_count']) ? $json['fan_count'] : 0;
  }


  /* create on page facebook links option */
  public function sfsi_plus_FBlike($permalink, $show_count = '')
  {
    $send = 'false';
    $width = 180;

    $permalink = trailingslashit($permalink);

    $permalink = rawurlencode(esc_url(rawurldecode($permalink))); /* XSS Vulnerability */

    $fb_like_html = '<div class="fb-like" data-href="' . $permalink . '"';

    if ($show_count == 1) {
      $fb_like_html .= ' data-layout="button_count"';
    } else {
      $fb_like_html .= ' data-layout="button"';
    }
    $fb_like_html .= ' data-action="like" data-share="false" ></div>';
    return $fb_like_html;
  }



  /* create on page facebook share option */
  public function sfsiFB_Share($permalink, $show_count = false)
  {
    $fb_share_html = '<div class="fb-share-button" data-href="' . $permalink . '" data-share="true"';

    if ($show_count == 1) {
      $fb_share_html .= ' data-layout="button_count"';
    } else {
      $fb_share_html .= ' data-layout="button"';
    }
    if (strpos($permalink, 'bit.ly')) {
      $permalink = rtrim($permalink, "/");
    } else {
      $permalink = trailingslashit($permalink);
    }
    $fb_share_html .= '><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . $permalink . '&src=sdkpreparse"></a></div>';
    return $fb_share_html;
  }

  public function sfsiFB_Share_Custom($permalink, $show_count = false)
  {
    $shareurl = "https://www.facebook.com/sharer/sharer.php?u=";
    // var_dump(strpos($permalink,'bit.ly'),$permalink);die();
    if (false !== strpos($permalink, "bit.ly") || false !== strpos($permalink, "?")) {
      $permalink = rtrim($permalink, "/");
    } else {
      $permalink = trailingslashit($permalink);
    }
    // var_dump($permalink);die();
    $shareurl = $shareurl . urlencode(urldecode($permalink));
    $new_window = '';

    $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));

    $language = $option5["sfsi_plus_icons_language"];

    if ( $language == "ar" ) {
      $language = "ar_AR";
    }
    if ( $language == "ja" ) {
      $language = "ja_JP";
    }
    if ( $language == "el" ) {
      $language = "el_GR";
    }
    if ( $language == "fi" ) {
      $language = "fi_FI";
    }
    if ( $language == "th" ) {
      $language = "th_TH";
    }
    if ( $language == "vi" ) {
      $language = "vi_VN";
    }

    if ("automatic" == $language) {
      if (function_exists('icl_object_id') && has_filter('wpml_current_language')) {
        $language = apply_filters('wpml_current_language', NULL);
        if (!empty($language)) {
          $language = sfsi_premium_wordpress_locale_from_locale_code($language);
        }
      } else {
        $language = get_locale();
      }
    }

    if (!wp_is_mobile()) {
      $new_window = sfsi_plus_checkNewWindow();
    }
    $fb_share_html = "<a href='" . $shareurl . "' " . $new_window . " style='display:inline-block;' > <img class='sfsi_premium_wicon' data-pin-nopin='true' alt='".__( 'fb-share-icon', 'ultimate-social-media-plus' )."' title='".__( 'Facebook Share', 'ultimate-social-media-plus' )."' src='" . SFSI_PLUS_PLUGURL . "images/share_icons/fb_icons/" . $language . ".svg" . "'  /></a>";
    return $fb_share_html;
  }
  /* create on page facebook follow option */
  public function sfsiFB_Follow($permalink)
  {
    $fb_share_html = '<div class="fb-follow" data-href="' . trailingslashit($permalink) . '" data-layout="button" data-size="small" data-show-faces="true"></div>';
    return $fb_share_html;
  }



  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region Youtube
  #---------------------------------------------------------------------------------------------------------------------------------------------------------

  /* get youtube subscribers  */
  public function  sfsi_get_youtube_subs($user = "")
  {

    $sfsi_premium_youtube_count = maybe_unserialize(get_option('sfsi_premium_youtube_count', false));
    $sfsi_premium_youtube_count["date"] = strtotime(date("Y-m-d"));
    $counts = $this->sfsi_get_youtube_subs_fetch();
    if (!isset($sfsi_premium_youtube_count["sfsi_plus_count"]) || $counts > $sfsi_premium_youtube_count["sfsi_plus_count"]) {
      $sfsi_premium_youtube_count["sfsi_plus_count"] = $counts;
      update_option('sfsi_premium_youtube_count',  serialize($sfsi_premium_youtube_count));
    } else {
      $counts =  $sfsi_premium_youtube_count["sfsi_plus_count"];
    }

    if (empty($counts) || $counts == "O") {
      $counts = 0;
    }

    return $counts;
  }

  function sfsi_get_youtube($user)
  {
    $sfsi_premium_youtube_count = maybe_unserialize(get_option('sfsi_premium_youtube_count', false));
    if (isset($sfsi_premium_youtube_count["sfsi_plus_count"]) && $sfsi_premium_youtube_count["sfsi_plus_count"] > 0) {
      $counts = $sfsi_premium_youtube_count["sfsi_plus_count"];
      $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
      if (isset($option5["sfsi_plus_change_number_format"]) && $option5["sfsi_plus_change_number_format"] == "no") {
        $counts = $this->format_num($counts);
      }
    } else {
      $counts = 0;
    }

    return $counts;
  }

  function sfsi_get_youtube_subs_fetch() {

    $option4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );

    if ( isset( $option4['sfsi_plus_youtube_channelId'] ) && !empty( $option4['sfsi_plus_youtube_channelId'] ) ) {
      $channelId = $option4['sfsi_plus_youtube_channelId'];
      $xmlData   = $this->file_get_contents_curl( 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $channelId . '&key=AIzaSyAvxb71_DKTFYLM-URqjADuD-sa-bmG7CE' );
    } else {
      $youtube_user   = (isset($option4['sfsi_plus_youtube_user']) && !empty($option4['sfsi_plus_youtube_user'])) ? $option4['sfsi_plus_youtube_user'] : '';
      $xmlData = $this->file_get_contents_curl('https://www.googleapis.com/youtube/v3/channels?part=statistics&forUsername=' . $youtube_user . '&key=AIzaSyAvxb71_DKTFYLM-URqjADuD-sa-bmG7CE');
    }

    if ($xmlData) {
      $xmlData = json_decode($xmlData);
      if (
        isset($xmlData->items) &&
        !empty($xmlData->items)
      ) {
        $subs = $xmlData->items[0]->statistics->subscriberCount;
      } else {
        $subs = 0;
      }
    } else {
      $subs = 0;
    }
    return $subs;
  }


  /* create on page youtube subscribe icon */
  public function sfsi_YouTubeSub($yuser)
  {
    $option2 = maybe_unserialize(get_option('sfsi_premium_section2_options', false));
    $sfsi_plus_ytube_chnlid = empty($option2['sfsi_plus_ytube_chnlid']) ? '' : $option2['sfsi_plus_ytube_chnlid'];
    $sfsi_plus_ytube_user = empty($option2['sfsi_plus_ytube_user']) ? '' : $option2['sfsi_plus_ytube_user'];

    if(empty($sfsi_plus_ytube_user) && empty($sfsi_plus_ytube_chnlid)){
        return '<div>Set Youtube Channel ID</div>';
    }

    if(!empty($sfsi_plus_ytube_chnlid)){
      return '<div class="g-ytsubscribe" data-channelid="' . $sfsi_plus_ytube_chnlid . '" data-layout="default" data-count="hidden"></div>';
    }

    return '<div class="g-ytsubscribe" data-channel="' . $sfsi_plus_ytube_user . '" data-layout="default" data-count="hidden"></div>';
  }
  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region AddThis (Share)
  #---------------------------------------------------------------------------------------------------------------------------------------------------------

  /* get addthis counts  */
  function sfsi_get_atthis()
  {
    // $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" :"http";
    // $url=$scheme.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $url = $this->sfsi_get_custom_share_link();
    $url = trailingslashit($url);

    $json_string = $this->file_get_contents_curl('http://api-public.addthis.com/url/shares.json?url=' . urlencode($url));
    $json = json_decode($json_string, true);
    return isset($json['shares']) ? $this->format_num((int) $json['shares']) : 0;
  }


  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region Pinterest
  #---------------------------------------------------------------------------------------------------------------------------------------------------------

  /* get pinit counts  */
  function sfsi_get_pinterest($url)
  {
    $pURL = parse_url($url, PHP_URL_PATH);
    $url = ($pURL != null && "html" == substr($pURL, -4)) ? $url : trailingslashit($url);
    $count = 0;
    $option5  = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    $option4  = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
    if (!isset($option5['sfsi_plus_pinterest_cumulative_count_active'])) {
      $isPinterestCumulationActive = (isset($option5['sfsi_plus_pinterest_cumulative_count_active'])) ? $option5['sfsi_plus_pinterest_cumulative_count_active'] : "no";
    } else {
      $isPinterestCumulationActive = ('yes' == $option5['sfsi_plus_pinterest_cumulative_count_active'] && (isset($option5['sfsi_plus_pinterest_cumulative_count_active']))) ? $option5['sfsi_plus_pinterest_cumulative_count_active'] : "no";
    }
    if ($isPinterestCumulationActive == "yes") {

      if ("no" == $option5['sfsi_plus_http_cumulative_count_active']) {
        $httpUrl   = str_replace(strtolower($option5['sfsi_plus_http_cumulative_count_new_domain']), strtolower($option5['sfsi_plus_http_cumulative_count_previous_domain']), strtolower($url));
      } else {
        if (is_ssl()) {
          $httpUrl = preg_replace("/^https:/i", "http:",  $url);
        } else {
          $httpUrl = preg_replace("/^http:/i", "https:",  $url);
        }
      }
      $httpsUrl  = $url;

      $cumuObj   = new sfsiCumulativeCount($httpUrl, $httpsUrl, "");
      $count     = $cumuObj->sfsi_pinterest_get_count();
      if (isset($option5["sfsi_plus_counts_without_slash"]) && $option5["sfsi_plus_counts_without_slash"] == "yes") {
        $httpUrl = rtrim($httpUrl, '/');
        $httpsUrl = rtrim($httpsUrl, '/');
        $cumuObj   = new sfsiCumulativeCount($httpUrl, $httpsUrl, "");
        $count     = $count + $cumuObj->sfsi_pinterest_get_count();
      }
    } else {
      $count = $this->get_pinit_counts($url, $option4);
    }
    return $count;
  }

  function sfsi_get_yummly($url)
  {
    $url = ("html" == substr(parse_url($url, PHP_URL_PATH), -4)) ? $url : trailingslashit($url);
    $count = 0;
    $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    $option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
    if (!isset($option5['sfsi_plus_pinterest_cumulative_count_active'])) {
      $isPinterestCumulationActive = (isset($option5['sfsi_plus_pinterest_cumulative_count_active'])) ? $option5['sfsi_plus_pinterest_cumulative_count_active'] : "no";
    } else {
      $isPinterestCumulationActive = ('yes' == $option5['sfsi_plus_pinterest_cumulative_count_active'] && (isset($option5['sfsi_plus_pinterest_cumulative_count_active']))) ? $option5['sfsi_plus_pinterest_cumulative_count_active'] : "no";
    }
    if ($isPinterestCumulationActive == "yes") {

      if ("no" == $option5['sfsi_plus_http_cumulative_count_active']) {
        $httpUrl   = str_replace(strtolower($option5['sfsi_plus_http_cumulative_count_new_domain']), strtolower($option5['sfsi_plus_http_cumulative_count_previous_domain']), strtolower($url));
      } else {
        if (is_ssl()) {
          $httpUrl = preg_replace("/^https:/i", "http:",  $url);
        } else {
          $httpUrl = preg_replace("/^http:/i", "https:",  $url);
        }
      }
      $httpsUrl  = $url;
      $cumuObj   = new sfsiCumulativeCount($httpUrl, $httpsUrl, "");
      $count     = $cumuObj->sfsi_pinterest_get_count();
    } else {
      $count = $this->get_pinit_counts($url, $option4);
    }
    return $count;
  }


  private function sfsi_get_board_pins($option4 = null)
  {

    $bcount    = 0;
    if ($option4 == null) {
      $option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
    }

    $user_name = (isset($option4['sfsi_plus_pinterest_user']) && !empty($option4['sfsi_plus_pinterest_user'])) ? $option4['sfsi_plus_pinterest_user'] : false;
    $board     = (isset($option4['sfsi_plus_pinterest_board_name']) && !empty($option4['sfsi_plus_pinterest_board_name'])) ? $option4['sfsi_plus_pinterest_board_name'] : false;

    if ($user_name && $board) {

      $boardSlug = sanitize_title_with_dashes($board);
      $query     = $user_name . "/" . $boardSlug;

      $burl       = 'https://pinterest.com/' . $query . '/';
      $board_respon = $this->sfsi_get_http_response_code($burl);

      if ($board_respon != 404) {
        $metas = get_meta_tags($burl);
        $bcount = (isset($metas['pinterestapp:pins'])) ? $metas['pinterestapp:pins'] : 0;
      }
    }
    return $bcount;
  }

  private function get_pinit_counts($pageurl, $option4 = null)
  {

    $pcount         = 0;
    $arrRespJson    = array();
    $url            = '';
    if ($option4 == null) {
      $option4 = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
    }
    $getCountOf     =  trim($option4['sfsi_plus_pinterest_countsFrom']);

    if (isset($getCountOf) && !empty($getCountOf)) {

      if ($getCountOf == "manual") {
        $pcount   = $option4['sfsi_plus_pinterest_manualCounts'];
      }

      // Retrieve the number of Pinterest (+1) (on your blog)
      else if ($getCountOf == "pins") {

        $return_data = $this->file_get_contents_curl('https://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=' . $pageurl, true);
        $json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
        $json        = json_decode($json_string, true);
        $pcount      = isset($json['count']) ? intval($json['count']) : 0;
      } else if ($getCountOf == "board") {
        $pcount = $this->sfsi_get_board_pins($option4);
      } else {

        $access_token = $option4['sfsi_plus_pinterest_access_token'];

        // Check if access token is set
        if (isset($access_token) && !empty($access_token)) {

          // Get User data using acces token
          $urlUser  = "https://api.pinterest.com/v1/me/?access_token=" . $access_token . "&fields=counts,username";
          $url_respon = $this->sfsi_get_http_response_code($urlUser);

          if ($url_respon != 404) {
            $responseJson = $this->file_get_contents_curl($urlUser, true);
            $objUserData  = json_decode($responseJson);

            if (is_object($objUserData) && isset($objUserData->data)) {

              $userData = $objUserData->data;

              if (isset($userData->counts)) {

                $pcounts = $userData->counts;

                // Retrieve the number of pins from your pinterest account
                if ($getCountOf == "accountpins") {
                  $pcount = (isset($pcounts->pins)) ? $pcounts->pins : 0;
                }
                // Retrieve the number of pinterest followers
                else if ($getCountOf == "followers") {
                  $pcount = (isset($pcounts->followers)) ? $pcounts->followers : 0;
                }
              }
            }
          }
        }
      }
    }

    $pcount = (strlen((string) $pcount) > 5) ? $this->format_num($pcount) : $pcount;
    return $pcount;
  }


  public function sfsi_pinit_image($option5 = null)
  {

    $post_id = $this->sfsi_get_the_ID();
    $pinterest_img = '';
    if ($option5 == null) {
      $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    }
    $isGlobalSharingOn   = (isset($option5['sfsi_plus_social_sharing_options']) && !empty($option5['sfsi_plus_social_sharing_options']) && strtolower($option5['sfsi_plus_social_sharing_options']) == "global") ? true : false;
    if ($isGlobalSharingOn || $post_id == 0) {
      if (isset($option5['sfsiSocialPinterestImage']) && "" !== $option5['sfsiSocialPinterestImage']) {
        $pinterest_img = $option5['sfsiSocialPinterestImage'];
        if (sfsi_premium_is_site_url($pinterest_img) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
          $pinterest_img = $option5['sfsi_premium_static_path'] . strrev(str_replace(strrev(site_url()), '', strrev($pinterest_img)));
        }
      }
    } else {

      $custom_pinit_img  = get_post_meta($post_id, 'sfsi-pinterest-media-image', true);
      if (isset($custom_pinit_img) && !empty($custom_pinit_img)) {
        $pinterest_img = $custom_pinit_img;
      } else {
        if (isset($option5['sfsi_premium_featured_image_as_og_image']) && 'yes' ==  $option5['sfsi_premium_featured_image_as_og_image']) {
          $post_thumbnail_id  = get_post_thumbnail_id($post_id);
          if ( $post_thumbnail_id ) {
            $pinterest_img_obj = wp_get_attachment_image_src($post_thumbnail_id, 'original');
            $pinterest_img = isset( $pinterest_img_obj[0] ) ? $pinterest_img_obj[0] : '';
          }
        }
      }
      if (sfsi_premium_is_site_url($pinterest_img) && isset($option5['sfsi_premium_static_path']) && $option5['sfsi_premium_static_path'] !== "") {
        if (strpos($pinterest_img, site_url()) !== false) {
          $pinterest_img = $pinterest_img;
        } else {
          $pinterest_img = $option5['sfsi_premium_static_path'] . strrev(str_replace(strrev(site_url()), '', strrev($pinterest_img)));
        }
      }
    }

    return $pinterest_img;
  }

  public function sfsi_pinit_description($option5 = null, $post = null, $post_type = 'url')
  {
    // var_dump(wp_doing_ajax());die();
    // if(wp_doing_ajax()){
    if (!is_null($post)) {
      if ($post_type == "id") {
        $post_id = $post;
      } elseif ('url' == $post_type) {
        $post_id = url_to_postid($post);
      } elseif ('object' == $post_type && isset($post->id)) {
        $post_id = $post->id;
      } else {
        $post_id = 0;
      }
    } else {
      $post_id = $this->sfsi_get_the_ID();
    }
    // var_dump($post_id);die();
    $pinterest_desc = '';
    if ($option5 == null) {
      $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    }
    $isGlobalSharingOn   = (isset($option5['sfsi_plus_social_sharing_options']) && !empty($option5['sfsi_plus_social_sharing_options']) && strtolower($option5['sfsi_plus_social_sharing_options']) == "global") ? true : false;

    //var_dump($isGlobalSharingOn,$post_id);die();
    if ($isGlobalSharingOn || $post_id == 0) {

      if (isset($option5['sfsiSocialPinterestDesc']) && !empty($option5['sfsiSocialPinterestDesc'])) {
        $pinterest_desc = trim($option5['sfsiSocialPinterestDesc']);
      }
    } else {

      $custom_pinit_desc  = get_post_meta($post_id, 'social-pinterest-description', true);
      // var_dump($custom_pinit_desc);die();

      if (isset($custom_pinit_desc) && !empty($custom_pinit_desc)) {
        $pinterest_desc = $custom_pinit_desc;
      }
      if ("" == $pinterest_desc) {
        $pinterest_desc = get_the_title($post_id);
      }
      return $pinterest_desc;
    }
    return $pinterest_desc;
  }

  /* create on page pinit button icon */
  public function sfsi_PinIt( $mouse_hover_effect = '', $class = '', $url = '', $iconImg = '', $icon_opacity = 1, $style = '', $icons_size = '' ) {
    $option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );

    $sfsi_premium_section2_options = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );

    $addCustomImgAttr = '';
    $alt_text = sfsi_plus_get_icon_mouseover_text( "pinterest", $option5 );

    // Add custo
    if ( false != isset( $iconImg ) && !empty( $iconImg ) ) {
      $addCustomImgAttr = 'data-pin-custom="true"';
    }

    $media = $this->sfsi_pinit_image( $option5 );

    $description = $this->sfsi_pinit_description( $option5 );

    /* Check icon height and width */
    $iconhw = '';
    if ( $icons_size ) {
      $iconhw = "height:".$icons_size."px;width:".$icons_size."px;";
    }

    if ( $option5['sfsi_premium_pinterest_sharing_texts_and_pics'] === "yes" ) {

      /* Check target value for popup */
      $popup_target_state = '';
      if ( wp_is_mobile() && isset( $option5["sfsi_plus_mobile_open_type_setting"] ) && "yes" == $option5["sfsi_plus_mobile_open_type_setting"] ) {
        if ( $option5['sfsi_plus_icons_mobile_ClickPageOpen'] == "window" ) {
          $popup_target_state = 'pinterest_new_window ';
        } elseif ( $option5['sfsi_plus_icons_mobile_ClickPageOpen'] == "tab" ) {
          $popup_target_state = 'pinterest_new_tab ';
        }

      } else {
        if ( $option5['sfsi_plus_icons_ClickPageOpen'] == "window" ) {
          $popup_target_state = 'pinterest_new_window ';
        } elseif ( $option5['sfsi_plus_icons_ClickPageOpen'] == "tab" ) {
          $popup_target_state = 'pinterest_new_tab ';
        }
      }

      $noopener = '';
      if(empty($popup_target_state)) {
        $noopener = " rel='noopener'";
      }

      $pin_it_html    = "<a class='sfsi_premium_pinterest_create {$popup_target_state}{$class}' style='cursor:pointer;{$iconhw}{$style}' onclick='sfsi_premium_pinterest_modal_images(\"" . $url . "\",\"" . urlencode(($description)) . "\")' data-description=" . urlencode(wptexturize($description)) . " data-effect='{$mouse_hover_effect}' {$noopener}>{$iconImg}</a>";
    } else {
      $encoded_description = urlencode( $description );
      $description = str_replace( "#", "%26", $encoded_description );
      $description = str_replace( "+", "%20", $description );
      $pin_it_html = "<a data-pin-custom='true' style='cursor:pointer;".$iconhw . $style . "' href='https://www.pinterest.com/pin/create/button/?url=" . urlencode($url) . "&media=" . urlencode($media) . "&description=" . ($description) . "' " . sfsi_plus_checkNewWindow($url) . " class='" . $class . "' data-effect='" . $mouse_hover_effect . "'>".$iconImg."</a>";
    }

    return $pin_it_html;
  }


  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region Instagram
  #---------------------------------------------------------------------------------------------------------------------------------------------------------

  /* get instragram followers */
  public function  sfsi_get_instagramFollowers($feedid)
  {
    $sfsi_premium_instagram_sf_count = maybe_unserialize(get_option('sfsi_premium_instagram_sf_count', false));

    /*if date is empty (for decrease request count)*/
    if (empty($sfsi_premium_instagram_sf_count["date_instagram"])) {
      // $sfsi_premium_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
      // $counts = $this->sfsi_plus_getFeedSubscriberCount($feedid);
      // $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"] = $counts;
      // update_option('sfsi_pcountsremium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
      $count = 0;
    } else {
      // $phpVersion = phpVersion();
      // if($phpVersion >= '5.3')
      // {
      //   $diff = date_diff(
      //     date_create(
      //       date("Y-m-d", $sfsi_premium_instagram_sf_count["date_sf"])
      //     ),
      //     date_create(
      //       date("Y-m-d")
      //   ));
      // }
      // if((isset($diff) && $diff->format("%a") >= 1) ||(!isset($sfsi_instagram_sf_count["sfsi_plus_sf_count"]) || ($sfsi_instagram_sf_count["sfsi_plus_sf_count"]=="") ))
      // {
      //   $sfsi_premium_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
      //   $counts = $this->sfsi_plus_getFeedSubscriberCount($feedid);
      //   $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"] = $counts;
      //   update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
      // }
      // else
      // {
      $counts = $sfsi_premium_instagram_sf_count["sfsi_plus_instagram_count"];
      // }
    }

    if (empty($counts) || $counts == "O") {
      $counts = 0;
    }

    return $counts;
  }

  /* get no of subscribers from follow.it for current blog */
  public function  sfsi_get_instagramFollowersFetch($user_name = null)
  {
    $sfsi_premium_instagram_sf_count = maybe_unserialize(get_option('sfsi_premium_instagram_sf_count', false));

    /*if date is empty (for decrease request count)*/
    // if(empty($sfsi_premium_instagram_sf_count["date_sf"]))
    // {
    $sfsi_premium_instagram_sf_count["date_instagram"] = strtotime(date("Y-m-d"));
    $counts = $this->sfsi_plus_get_instagramFollowersCount();
    $sfsi_premium_instagram_sf_count["sfsi_plus_instagram_count"] = $counts;
    update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
    // }
    // else
    // {
    //   $phpVersion = phpVersion();
    //   if($phpVersion >= '5.3')
    //   {
    //     $diff = date_diff(
    //       date_create(
    //         date("Y-m-d", $sfsi_premium_instagram_sf_count["date_sf"])
    //       ),
    //       date_create(
    //         date("Y-m-d")
    //     ));
    //   }
    //   if((isset($diff) && $diff->format("%a") >= 1) ||(!isset($sfsi_instagram_sf_count["sfsi_plus_sf_count"]) || ($sfsi_instagram_sf_count["sfsi_plus_sf_count"]=="") ))
    //   {
    //     $sfsi_premium_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
    //     $counts = $this->sfsi_plus_getFeedSubscriberCount($feedid);
    //     $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"] = $counts;
    //     update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
    //   }
    //   else
    //   {
    //     $counts = $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"];
    //   }
    // }

    if (empty($counts) || $counts == "O") {
      $counts = 0;
    }

    return $counts;
  }
  // public function sfsi_get_instagramFollowers($user_name)
  // {
  //   $sfsi_premium_instagram_sf_count = maybe_unserialize(get_option('sfsi_premium_instagram_sf_count', false));

  //   /*if date is empty (for decrease request count)*/
  //   if (empty($sfsi_premium_instagram_sf_count["date_instagram"])) {
  //     $sfsi_premium_instagram_sf_count["date_instagram"] = strtotime(date("Y-m-d"));
  //     $counts = $this->sfsi_plus_get_instagramFollowersCount($user_name);
  //     $sfsi_premium_instagram_sf_count["sfsi_plus_instagram_count"] = $counts;
  //     update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
  //   } else {
  //     $phpVersion = phpVersion();
  //     if ($phpVersion >= '5.3') {
  //       $diff = date_diff(
  //         date_create(
  //           date("Y-m-d", $sfsi_premium_instagram_sf_count["date_instagram"])
  //         ),
  //         date_create(
  //           date("Y-m-d")
  //         )
  //       );
  //     }
  //     if ((isset($diff) && $diff->format("%a") < 1) || 1 == 1) {
  //       $sfsi_premium_instagram_sf_count["date_instagram"] = strtotime(date("Y-m-d"));
  //       $counts = $this->sfsi_plus_get_instagramFollowersCount($user_name);
  //       $sfsi_premium_instagram_sf_count["sfsi_plus_instagram_count"] = $counts;
  //       update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
  //     } else {
  //       $counts = $sfsi_premium_instagram_sf_count["sfsi_plus_instagram_count"];
  //     }
  //   }
  //   return $counts;
  // }

  /* get instragram followers count*/
  public function sfsi_plus_get_instagramFollowersCount($user_name = null)
  {
    $option4  = maybe_unserialize(get_option('sfsi_premium_section4_options', false));
    $token    = $option4['sfsi_plus_instagram_token'];
    if (is_null($user_name)) {
      $username = $option4['sfsi_plus_instagram_User'];
    } else {
      $username = $user_name;
    }
    $count    = 0;

    if (isset($token) && !empty($token)) {

      $return_data = $this->get_content_curl('https://api.instagram.com/v1/users/self/?access_token=' . $token);
      $objData   = json_decode($return_data);

      if (isset($objData) && isset($objData->data) && isset($objData->data->counts) && isset($objData->data->counts->followed_by)) {
        $count   = $objData->data->counts->followed_by;
      }
    } elseif (isset($username) && !empty($username)) {
      $return_data = $this->get_content_curl('https://www.instagram.com/' . $username . '/?__a=1');

      $objData   = json_decode($return_data, true);

      if (isset($objData) && isset($objData['graphql']) && isset($objData['graphql']['user']) && isset($objData['graphql']['user']['edge_followed_by']) && isset($objData['graphql']['user']['edge_followed_by']['count'])) {
        $count   = $objData['graphql']['user']['edge_followed_by']['count'];
        $count   = $this->format_num_back($count);
      }
    }
    return $this->format_num($count, 0);
  }


  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region Houzz
  #---------------------------------------------------------------------------------------------------------------------------------------------------------

  public function get_houzz_save_button($url, $houzzWebsiteId, $categoryKeywords = '')
  {

    $postId = $this->sfsi_get_the_ID();

    $title  = $this->sfsi_get_the_title();
    $desc   = sfsi_get_description($postId);
    $imgUrl = get_the_post_thumbnail_url($postId);

    $houzz_button = '<a class="houzz-share-button" data-url="' . $url . '" data-hzid="' . $houzzWebsiteId . '" data-title="' . $url . '" data-img="' . $imgUrl . '" data-desc="' . $desc . '"
       data-category="' . $categoryKeywords . '" data-showcount="1" href="https://www.houzz.com"></a>';

    return $houzz_button;
  }

  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region Email
  #---------------------------------------------------------------------------------------------------------------------------------------------------------

  /* get no of subscribers from follow.it for current blog */
  public function  SFSI_getFeedSubscriber($feedid)
  {
    $sfsi_premium_instagram_sf_count = maybe_unserialize(get_option('sfsi_premium_instagram_sf_count', false));

    /*if date is empty (for decrease request count)*/
    if (empty($sfsi_premium_instagram_sf_count["date_sf"])) {
      // $sfsi_premium_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
      // $counts = $this->sfsi_plus_getFeedSubscriberCount($feedid);
      // $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"] = $counts;
      // update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
      $count = 0;
    } else {
      // $phpVersion = phpVersion();
      // if($phpVersion >= '5.3')
      // {
      //   $diff = date_diff(
      //     date_create(
      //       date("Y-m-d", $sfsi_premium_instagram_sf_count["date_sf"])
      //     ),
      //     date_create(
      //       date("Y-m-d")
      //   ));
      // }
      // if((isset($diff) && $diff->format("%a") >= 1) ||(!isset($sfsi_instagram_sf_count["sfsi_plus_sf_count"]) || ($sfsi_instagram_sf_count["sfsi_plus_sf_count"]=="") ))
      // {
      //   $sfsi_premium_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
      //   $counts = $this->sfsi_plus_getFeedSubscriberCount($feedid);
      //   $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"] = $counts;
      //   update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
      // }
      // else
      // {
      $counts = $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"];
      // }
    }

    if (empty($counts) || $counts == "O") {
      $counts = 0;
    }

    return $counts;
  }

  /* get no of subscribers from follow.it for current blog */
  public function  SFSI_getFeedSubscriberFetch($feedid)
  {
    $sfsi_premium_instagram_sf_count = maybe_unserialize(get_option('sfsi_premium_instagram_sf_count', false));

    /*if date is empty (for decrease request count)*/
    // if(empty($sfsi_premium_instagram_sf_count["date_sf"]))
    // {
    $sfsi_premium_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
    $counts = $this->sfsi_plus_getFeedSubscriberCount($feedid);
    $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"] = $counts;
    update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
    // }
    // else
    // {
    //   $phpVersion = phpVersion();
    //   if($phpVersion >= '5.3')
    //   {
    //     $diff = date_diff(
    //       date_create(
    //         date("Y-m-d", $sfsi_premium_instagram_sf_count["date_sf"])
    //       ),
    //       date_create(
    //         date("Y-m-d")
    //     ));
    //   }
    //   if((isset($diff) && $diff->format("%a") >= 1) ||(!isset($sfsi_instagram_sf_count["sfsi_plus_sf_count"]) || ($sfsi_instagram_sf_count["sfsi_plus_sf_count"]=="") ))
    //   {
    //     $sfsi_premium_instagram_sf_count["date_sf"] = strtotime(date("Y-m-d"));
    //     $counts = $this->sfsi_plus_getFeedSubscriberCount($feedid);
    //     $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"] = $counts;
    //     update_option('sfsi_premium_instagram_sf_count',  serialize($sfsi_premium_instagram_sf_count));
    //   }
    //   else
    //   {
    //     $counts = $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"];
    //   }
    // }

    if (empty($counts) || $counts == "O") {
      $counts = 0;
    }

    return $counts;
  }

  /* get no of subscribers from follow.it for current blog count*/
  public function  sfsi_plus_getFeedSubscriberCount($feedid)
  {
    $curl = wp_remote_post('https://api.follow.it/wordpress/wpCountSubscriber', array(
      'blocking' => true,
      'user-agent' => 'sf rss request',
      'timeout'     => 30,
      'sslverify' => true,
      'body' =>   array(
        'feed_id' => $feedid,
        'v' => 'newplugincount'
      ),

    ));

    /* Send the request & save response to $resp */
    $resp = $curl;


    if (!is_wp_error($resp)) {
      if (!empty($resp)) {
        $resp = json_decode($resp['body']);
        $feeddata = stripslashes_deep($resp->subscriber_count);
      } else {
        $sfsi_premium_instagram_sf_count = maybe_unserialize(get_option('sfsi_premium_instagram_sf_count', false));
        $feeddata = $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"];
      }
    } else {
      $sfsi_premium_instagram_sf_count = maybe_unserialize(get_option('sfsi_premium_instagram_sf_count', false));
      $feeddata = $sfsi_premium_instagram_sf_count["sfsi_plus_sf_count"];
    }
    return $this->format_num($feeddata);
    exit;
  }


  #---------------------------------------------------------------------------------------------------------------------------------------------------------
  #region HELPER FUNCTIONS
  #---------------------------------------------------------------------------------------------------------------------------------------------------------

  /* send curl request   */
  private function file_get_contents_curl($url, $followlocation = false)
  {
    
    $params = [];
    $parsed = parse_url($url);
    $query = $parsed['query'];
    parse_str($query, $params);
    if (isset($params['doing_wp_cron']) || isset($params['fbclid'])) {
      if (isset($params['doing_wp_cron'])) unset($params['doing_wp_cron']);
      if (isset($params['fbclid'])) unset($params['fbclid']);
      $url = $parsed['scheme'] . '://' . $parsed['host'] . $parsed['path'] . '?' . http_build_query($params);
    }
    
    $transientName = 'usm-url-' . sha1($url);
    if ($body = get_transient($transientName)) {
      return $body;
    }
    
    $curl = wp_remote_get($url, array(
      'user-agent'    =>    $_SERVER['HTTP_USER_AGENT'],
      'timeout'       =>    $this->timeout,
      'sslverify'     =>  false,
      'blocking' => true,
    ));
  
    if (is_wp_error($curl)) {
      
      return  $curl->get_error_message();
      
    } else {
      
      $status = wp_remote_retrieve_response_code($curl);
      $body = wp_remote_retrieve_body($curl);
      
      if (intval($status) == 200) {
        set_transient($transientName, $body, intval(60*60*1.5));
      }
      
      return $body;
      
    }
    
  }

  private function get_content_curl($url)
  {
    $curl = wp_remote_get($url, array(
      'timeout'    =>  $this->timeout,
      'blocking' => true,
      'sslverify' => false,
    ));
    
    $status = wp_remote_retrieve_response_code($curl);
    error_log($status . ' –2–> ' . $url);

    if (is_wp_error($curl)) {
      $curl->get_error_message();
    }
    return wp_remote_retrieve_body($curl);
  }

  /* convert no. to 2K,3M format   */
  function format_num($n, $precision = 1)
  {
      if(is_array($n)){
          //is facebook
          $n = $n['c'];
      }
    if ($n < 900) {
      // 0 - 900
      if (is_numeric($n)) {
        $n_format = number_format($n, $precision);
      } else {
        $n_format = $n;
      }
      $suffix = '';
    } else if ($n < 900000) {
      // 0.9k-850k
      $n_format = number_format($n / 1000, $precision);
      $suffix = 'k';
    } else if ($n < 900000000) {
      // 0.9m-850m
      $n_format = number_format($n / 1000000, $precision);
      $suffix = 'm';
    } else if ($n < 900000000000) {
      // 0.9b-850b
      $n_format = number_format($n / 1000000000, $precision);
      $suffix = 'b';
    } else {
      // 0.9t+
      $n_format = number_format($n / 1000000000000, $precision);
      $suffix = 't';
    }
    // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
    // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ($precision > 0) {
      $dotzero = '.' . str_repeat('0', $precision);
      $n_format = str_replace($dotzero, '', $n_format);
    }
    return $n_format . $suffix;
  }

  /* convert no. to 2K,3M format */
  function format_num_back( $n ) {
    if ( intval( $n ) != $n ) {
      $check = preg_match_all( "/(\d+\.?\d+)\s*(\w)/", $n, $matches );
      if ( $check ) {
        $n = $matches[1][0];
        $suffix = strtolower( $matches[2][0] );
        switch ( $suffix ) {
          case "k":
            $n = $n * 1000;
            break;
          case "m":
            $n = $n * 1000000;
            break;
          case "b":
            $n = $n * 1000000000;
            break;
          case "t":
            $n = $n * 1000000000000;
            break;
        }
      } else {
        $n = intval( $n );
      }
    }
    return $n;
  }

  /*
      This function returns 0 if post id not found
    */
  public function sfsi_get_the_ID()
  {

    $post_id = false;

    try {
      if (in_the_loop()) {
        $post_id = (get_the_ID()) ? get_the_ID() : sfsi_premium_url_to_postid(urldecode(sfsi_plus_current_url()));
      } else {
        /** @var $wp_query wp_query */
        global $wp_query;

        if (isset($wp_query) && !empty($wp_query) && is_object($wp_query)) {

          $post_id = $wp_query->get_queried_object_id();
        }
      }
    }

    //catch exception
    catch (Exception $e) {
      return false;
    }
    return $post_id;
  }

  public function sfsi_get_the_title()
  {

    $title    = get_bloginfo('name');
    $title    = (isset($title) && strlen($title) > 0) ? $title : get_bloginfo('url');
    $post_id  = $this->sfsi_get_the_ID();

    if ($post_id) {
      $post_title = (is_archive()) ? get_queried_object()->name : get_the_title($post_id);
      $title      = (isset($post_title) && strlen(trim($post_title)) > 0) ? $post_title : $title;
    }

    return wp_kses_post($title);
  }

  /* check response from a url */
  private function sfsi_get_http_response_code($url)
  {
    $headers = get_headers($url);
    return substr(@$headers[0], 9, 3);
  }

  public function sfsi_yummly_share_count($url)
  {
    $result = wp_safe_remote_get('http://www.yummly.com/services/yum-count?url=' . $url, array(
      'timeout' => 30,
    ));

    if (is_wp_error($result)) {
      return 0;
    }

    $array = json_decode($result['body'], true);
    $count = isset($array['count']) ? $array['count'] : '0';

    return $count;
  }
}
