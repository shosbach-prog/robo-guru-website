<?php
if (!function_exists('array_column')) {
    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;
        }

        if (
            !is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (
            isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }

        return $resultArray;
    }
}


function sfsi_premium_array_column($input = null, $columnKey = null, $indexKey = null)
{
    // Using func_get_args() in order to check for proper number of
    // parameters and trigger errors exactly as the built-in array_column()
    // does in PHP 5.5.
    $argc = func_num_args();
    $params = func_get_args();

    if ($argc < 2) {
        trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
        return null;
    }

    if (!is_array($params[0])) {
        trigger_error(
            'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
            E_USER_WARNING
        );
        return null;
    }

    if (
        !is_int($params[1])
        && !is_float($params[1])
        && !is_string($params[1])
        && $params[1] !== null
        && !(is_object($params[1]) && method_exists($params[1], '__toString'))
    ) {
        trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
        return false;
    }

    if (
        isset($params[2])
        && !is_int($params[2])
        && !is_float($params[2])
        && !is_string($params[2])
        && !(is_object($params[2]) && method_exists($params[2], '__toString'))
    ) {
        trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
        return false;
    }

    $paramsInput = $params[0];
    $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

    $paramsIndexKey = null;
    if (isset($params[2])) {
        if (is_float($params[2]) || is_int($params[2])) {
            $paramsIndexKey = (int) $params[2];
        } else {
            $paramsIndexKey = (string) $params[2];
        }
    }

    $resultArray = array();

    foreach ($paramsInput as $row) {
        $key = $value = null;
        $keySet = $valueSet = false;

        if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
            $keySet = true;
            $key = (string) $row[$paramsIndexKey];
        }

        if ($paramsColumnKey === null) {
            $valueSet = true;
            $value = $row;
        } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
            $valueSet = true;
            $value = $row[$paramsColumnKey];
        }

        if ($valueSet) {
            if ($keySet) {
                $resultArray[$key] = $value;
            } else {
                $resultArray[] = $value;
            }
        }
    }

    return $resultArray;
}

if (!function_exists('sfsi_premium_version_compare')) {
    function sfsi_premium_version_compare($ver1, $ver2, $operator = null)
    {
        $p = '#(\.0+)+($|-)#';
        $ver1 = preg_replace($p, '', $ver1);
        $ver2 = preg_replace($p, '', $ver2);
        return isset($operator) ? version_compare($ver1, $ver2, $operator) : version_compare($ver1, $ver2);
    }
}

if (!function_exists('sfsi_premium_get_client_ip')) {

    function sfsi_premium_get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}

function sfsi_premium_is_ssl()
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
    return $scheme;
}
//getting line height for the icon
function sfsi_plus_getlinhght($lineheight)
{
    if ($lineheight < 16) {
        $lineheight = $lineheight * 2;
        return $lineheight;
    } elseif ($lineheight >= 16 && $lineheight < 20) {
        $lineheight = $lineheight + 10;
        return $lineheight;
    } elseif ($lineheight >= 20 && $lineheight < 28) {
        $lineheight = $lineheight + 3;
        return $lineheight;
    } elseif ($lineheight >= 28 && $lineheight < 40) {
        $lineheight = $lineheight + 4;
        return $lineheight;
    } elseif ($lineheight >= 40 && $lineheight < 50) {
        $lineheight = $lineheight + 5;
        return $lineheight;
    }
    $lineheight = $lineheight + 6;
    return $lineheight;
}

function sfsi_premium_is_blog_page()
{

    // Default home page, take settings from "On Blog pages"
    if (is_front_page() && is_home()) {
        return true;
        //echo "Default homepage";
    }
    // Default home page, take settings from "Also show icons at the end of pages?"
    elseif (is_front_page()) {
        return false;
        //echo "Static homepage";
    }
    // Posts page take settings from "On Blog pages"
    elseif (is_home()) {
        return true;
        //echo "Blog page";
    }
    // Default home page, take settings from "Also show icons at the end of pages?"
    else {
        return false;
        //echo "everything else";
    }
}

function sfsi_strpos_all($string, $searchStr, $replaceStr)
{

    $offset = 0;

    while (($pos = strpos($string, $searchStr, $offset)) !== FALSE) {
        $offset   = $pos + 1;
        $string = substr_replace($string, $replaceStr, $pos, strlen($searchStr));
    }
    return $string;
}

function sfsi_get_description($postid)
{
    $post    = get_post($postid);
    $excerpt = !empty($post->post_excerpt) ? get_the_excerpt($postid) : '';
    $desc    = trim($excerpt);

    if (strlen($desc) == 0) {
        $desc = isset($post) && !empty($post) && is_object($post) && isset($post->post_content) ? $post->post_content : '';
        $desc = str_replace(']]>', ']]&gt;', $desc);
        $desc = strip_shortcodes($desc);
    }

    $desc   = strip_tags($desc);
    $desc   = esc_attr($desc);
    $desc   = trim(preg_replace("/\s+/", " ", $desc));
    $desc   = sfsi_sub_string($desc, 400);
    return $desc;
}

function sfsi_filter_text($desc)
{
    if (strlen($desc) > 0) {
        $desc   = str_replace(']]>', ']]&gt;', $desc);
        $desc   = strip_shortcodes($desc);
        $desc   = strip_tags($desc);
        $desc   = esc_attr($desc);
        $desc   = trim(preg_replace("/\s+/", " ", $desc));
    }
    return $desc;
}

function sfsi_sub_string($text, $charlength = 200)
{
    $charlength++;
    $retext = "";
    if (mb_strlen($text) > $charlength) {
        $subex = mb_substr($text, 0, $charlength - 5);
        $exwords = explode(' ', $subex);
        $excut = -(mb_strlen($exwords[count($exwords) - 1]));
        if ($excut < 0) {
            $retext .= mb_substr($subex, 0, $excut);
        } else {
            $retext .= $subex;
        }
        $retext .= '[...]';
    } else {
        $retext .= $text;
    }

    return $retext;
}

/**
 * Get an attachment ID given a URL.
 * 
 * @param string $url
 *
 * @return int Attachment ID on success, 0 on failure
 */
function sfsi_get_attachment_id($url)
{
    $attachment_id = false;
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url));

    // var_dump($attachment, $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $url));

    $attachment_id = (isset($attachment[0]) && !empty($attachment[0])) ? $attachment[0] : $attachment_id;
    return $attachment_id;
}

//sanitizing values
function sfsi_plus_string_sanitize($s)
{
    $result = preg_replace("/[^a-zA-Z0-9]+/", " ", html_entity_decode($s, ENT_QUOTES));
    return $result;
}

function sfsi_plus_get_bloginfo($url)
{
    $web_url = get_bloginfo($url);

    //Block to use feedburner url
    if (preg_match("/(feedburner)/im", $web_url, $match)) {
        $web_url = site_url() . "/feed";
    }
    return $web_url;
}

function sfsi_premium_strip_tags_content($text, $tags = '', $invert = FALSE)
{

    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);

    if (is_array($tags) && count($tags) > 0) {
        if ($invert == FALSE) {
            return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
        } else {
            return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
        }
    } elseif ($invert == FALSE) {
        return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    }

    $text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $text);

    return $text;
}

function sfsi_plus_current_url()
{
    global $post, $wp;

    $permalink = get_option('permalink_structure');
    $url_from_server = get_option('sfsi_premium_set_url_fromserver', "no");
    if ($url_from_server == "yes") {
        return $_SERVER["SCRIPT_URI"];
    }
    if ("" == $permalink) {
        $query_string_array = $_GET;
    } else {
        $query_string_array = array();
    }
    if (!empty($wp)) {
        $path = add_query_arg($query_string_array, $wp->request);
        //currently adding for index.php should be extended for any thing. start
        if (strpos($permalink, 'index.php') !== false) {
            $path = 'index.php/' . $path;
        }
        //currently adding for index.php should be extended for any thing. stop

        $url = home_url($path);
    } elseif (!empty($post)) {
        $url = get_permalink($post->ID);
    } else {

        $protocol = false != sfsi_is_ssl() ? "https" : "http";

        $url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = urldecode($url);
        $url = sfsi_premium_strip_tags_content($url);
    }

    $url = urlencode($url);
    return $url;
}
add_shortcode("usm_premium_shared_current_url", "sfsi_plus_current_url");

function sfsi_premium_twitter_text(){
   
	$twitter_text = $socialObj->sfsi_get_custom_tweet_text();
    $twitter_text 	= urlencode($twitter_text);
    return  $twitter_text;
}
 
add_shortcode("usm_premium_twitter_text", "sfsi_premium_twitter_text");


function sfsi_premium_pinterest_media(){
    $socialObj = new sfsi_plus_SocialHelper(); 
    $media = $socialObj->sfsi_pinit_image();
    return  $media;
}
add_shortcode("usm_premium_pinterest_media", "sfsi_premium_pinterest_media");

function sfsi_premium_pinterest_description(){
    $socialObj = new sfsi_plus_SocialHelper(); 
				$description = $socialObj->sfsi_pinit_description();
				$description = str_replace("%22", '"', $description);
				$description = str_replace("%27", "'", $description);
                $encoded_description = wptexturize($description);
                return  $encoded_description;
}
add_shortcode("usm_premium_pinterest_description", "sfsi_premium_pinterest_description");

function sfsi_premium_mail_subject(){
	$option2 = maybe_unserialize(get_option('sfsi_premium_section2_options', false));
    $socialObj = new sfsi_plus_SocialHelper();
    $subject = stripslashes($option2['sfsi_plus_email_icons_subject_line']);
    $subject = str_replace('${title}', $socialObj->sfsi_get_the_title(), $subject);
    $subject = str_replace('"', '', str_replace("'", '', $subject));
    $subject = html_entity_decode(strip_tags($subject), ENT_QUOTES, 'UTF-8');
    $subject = str_replace("%26%238230%3B", "...", $subject);
    $subject = rawurlencode($subject);
    return $subject;
}
add_shortcode("usm_premium_mail_subject", "sfsi_premium_mail_subject");

function sfsi_premium_mail_body(){
    $option2 = maybe_unserialize(get_option('sfsi_premium_section2_options', false));
	$option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    $socialObj = new sfsi_plus_SocialHelper();
    $body = stripslashes($option2['sfsi_plus_email_icons_email_content']);
    $body = str_replace('${title}', $socialObj->sfsi_get_the_title(), $body);
    $body = str_replace('${link}',  trailingslashit($socialObj->sfsi_get_custom_share_link('email', $option5)), $body);
    $body = str_replace('"', '', str_replace("'", '', $body));
    $body = html_entity_decode(strip_tags($body), ENT_QUOTES, 'UTF-8');
    $body = str_replace("%26%238230%3B", "...", $body);
    $body = rawurlencode($body);
    return $body;
}
add_shortcode("usm_premium_mail_body", "sfsi_premium_mail_body");
/**
 * Filters a sanitized textarea field string.
 *
 * @param string $filtered The sanitized string.
 * @param string $str      The string prior to being sanitized.
 */

function sfsi_sanitize_textarea_field($str, $keep_newlines = false)
{

    $filtered = wp_check_invalid_utf8($str);

    if (strpos($filtered, '<') !== false) {

        $filtered = wp_pre_kses_less_than($filtered);

        // This will strip extra whitespace for us.
        $filtered = wp_strip_all_tags($filtered, false);

        // Use html entities in a special case to make sure no later
        // newline stripping stage could lead to a functional tag
        $filtered = str_replace("<\n", "&lt;\n", $filtered);
    }

    if (!$keep_newlines) {
        $filtered = preg_replace('/[\r\n\t ]+/', ' ', $filtered);
    }
    $filtered = trim($filtered);

    $found = false;
    while (preg_match('/%[a-f0-9]{2}/i', $filtered, $match)) {
        $filtered = str_replace($match[0], '', $filtered);
        $found = true;
    }

    if ($found) {
        // Strip out the whitespace that may now exist after removing the octets.
        $filtered = trim(preg_replace('/ +/', ' ', $filtered));
    }

    return $filtered;
}

function sfsi_is_ssl()
{

    $isssl = false;

    $server_opts = array(
        "HTTP_CLOUDFRONT_FORWARDED_PROTO"   => "https",
        "HTTP_CF_VISITOR"                   => "https",
        "HTTP_X_FORWARDED_PROTO"            => "https",
        "HTTP_X_FORWARDED_SSL"              => "on",
        "HTTP_X_FORWARDED_SSL"              => "1"
    );

    foreach ($server_opts as $option => $value) {

        if ((isset($_ENV["HTTPS"]) && ("on" == $_ENV["HTTPS"]))

            || (isset($_SERVER[$option]) && (strpos($_SERVER[$option], $value) !== false))

            || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)

        ) {

            $_SERVER["HTTPS"] = "on";
            $isssl = true;
            break;
        }
    }

    return $isssl;
}

/**
 * Sort a 2 dimensional array based on 1 or more indexes.
 * 
 * msort() can be used to sort a rowset like array on one or more
 * 'headers' (keys in the 2th array).
 * 
 * @param array        $array      The array to sort.
 * @param string|array $key        The index(es) to sort the array on.
 * @param int          $sort_flags The optional parameter to modify the sorting 
 *                                 behavior. This parameter does not work when 
 *                                 supplying an array in the $key parameter. 
 * 
 * @return array The sorted array.
 */
function sfsi_premium_msort($array, $key, $sort_flags = SORT_REGULAR)
{

    if (is_array($array) && count($array) > 0) {
        if (!empty($key)) {
            $mapping = array();
            foreach ($array as $k => $v) {
                $sort_key = '';
                if (!is_array($key)) {
                    $sort_key = $v[$key];
                } else {
                    // @TODO This should be fixed, now it will be sorted as string
                    foreach ($key as $key_key) {
                        $sort_key .= $v[$key_key];
                    }
                    $sort_flags = SORT_STRING;
                }
                $mapping[$k] = $sort_key;
            }
            asort($mapping, $sort_flags);
            $sorted = array();
            foreach ($mapping as $k => $v) {
                $sorted[] = $array[$k];
            }
            return $sorted;
        }
    }
    return $array;
}

/**
 * Converts an array to an object
 *
 * @params Array $array the array to be converted to an object
 * @params bool $recursive Whether or not to convert child arrays to child objects
 * @returns Object $object
 */
function sfsi_premium_arrayToObject($array, $recursive = true)
{

    $object = new stdClass;

    foreach ($array as $k => $v) {
        // var_dump($v);

        if ($recursive && is_array($v)) {
            $object->{$k} = sfsi_premium_arrayToObject($v, $recursive);
        } else {
            $object->{$k} = $v;
        }
    }
    return $object;
}

function sfsi_premium_key_match_array($key, $arr)
{

    $keyExists = isset($arr) && !empty($arr) && isset($arr[$key]) ? true : false;

    return $keyExists;
}

function sfsi_premium_js_str($s)
{
    return '"' . addcslashes($s, "\0..\37\"\\") . '"';
}

function sfsi_premium_json_array($array)
{
    $temp = array_map('sfsi_premium_js_str', $array);
    return '[' . implode(',', $temp) . ']';
}

/**
 * A method to parse and produce the alternate permalink.
 *
 * @since 1.0.0
 * @param int The post ID.
 * @param bool Whether to keep the post name.
 * @return string The modified URL of the post.
 *
 */
function sfsi_premium_get_alt_permalink($post = 0, $leavename = false)
{

    // global $swp_user_options;

    $swp_user_options = array(
        "recovery_format"       => "unchanged",
        "recovery_permalink"    => "/%postname%/%monthnum%/%year%/%day%/%hour%/%minute%/%second%/%post_id%/%category%/%author%/",
        "current_domain"        => "",
        "former_domain"         => "",
        "recovery_protocol"     => "",
        "recovery_prefix"       => "",
        "recovery_subdomain"    => ""
    );

    $rewritecode = array(
        '%year%',
        '%monthnum%',
        '%day%',
        '%hour%',
        '%minute%',
        '%second%',
        $leavename ? '' : '%postname%',
        '%post_id%',
        '%category%',
        '%author%',
        $leavename ? '' : '%pagename%',
    );

    if (is_object($post) && isset($post->filter) && 'sample' == $post->filter) {
        $sample = true;
    } else {
        $post = get_post($post);
        $sample = false;
    }

    if (empty($post->ID)) {
        return false;
    }

    // Build the structure
    $structure = $swp_user_options['recovery_format'];

    if ($structure == 'custom') :
        $permalink = $swp_user_options['recovery_permalink'];

    elseif ($structure == 'unchanged') :
        $permalink = get_option('permalink_structure');
    elseif ($structure == 'default') :
        $permalink = '';
    elseif ($structure == 'day_and_name') :
        $permalink = '/%year%/%monthnum%/%day%/%postname%/';
    elseif ($structure == 'month_and_name') :
        $permalink = '/%year%/%monthnum%/%postname%/';
    elseif ($structure == 'numeric') :
        $permalink = '/archives/%post_id%';
    elseif ($structure == 'post_name') :
        $permalink = '/%postname%/';
    else :
        $permalink = get_option('permalink_structure');
    endif;

    /**
     * Filter the permalink structure for a post before token replacement occurs.
     *
     * Only applies to posts with post_type of 'post'.
     *
     * @since 3.0.0
     *
     * @param string  $permalink The site's permalink structure.
     * @param WP_Post $post      The post in question.
     * @param bool    $leavename Whether to keep the post name.
     */
    $permalink = apply_filters('pre_post_link', $permalink, $post, $leavename);

    if ('' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft', 'future'))) {
        $unixtime = strtotime($post->post_date);

        $category = '';
        if (strpos($permalink, '%category%') !== false) {
            $cats = get_the_category($post->ID);
            if ($cats) {
                usort($cats, '_usort_terms_by_ID'); // order by ID

                /**
                 * Filter the category that gets used in the %category% permalink token.
                 *
                 * @since 3.5.0
                 *
                 * @param stdClass $cat  The category to use in the permalink.
                 * @param array    $cats Array of all categories associated with the post.
                 * @param WP_Post  $post The post in question.
                 */
                $category_object = apply_filters('post_link_category', $cats[0], $cats, $post);

                $category_object = get_term($category_object, 'category');
                $category = $category_object->slug;
                if ($parent = $category_object->parent) {
                    $category = get_category_parents($parent, false, '/', true) . $category;
                }
            }
            // show default category in permalinks, without
            // having to assign it explicitly
            if (empty($category)) {
                $default_category = get_term(get_option('default_category'), 'category');
                $category = is_wp_error($default_category) ? '' : $default_category->slug;
            }
        }

        $author = '';

        if (strpos($permalink, '%author%') !== false) {

            if (!function_exists('get_userdata')) {
                require_once(ABSPATH . 'wp-includes/pluggable.php');
            }

            $authordata = get_userdata($post->post_author);
            $author = $authordata->user_nicename;
        }

        $date = explode(' ', date('Y m d H i s', $unixtime));
        $rewritereplace =
            array(
                $date[0],
                $date[1],
                $date[2],
                $date[3],
                $date[4],
                $date[5],
                $post->post_name,
                $post->ID,
                $category,
                $author,
                $post->post_name,
            );
        $permalink = home_url(str_replace($rewritecode, $rewritereplace, $permalink));

        if ($structure != 'custom') :
            $permalink = user_trailingslashit($permalink, 'single');
        endif;
    } else { // if they're not using the fancy permalink option
        $permalink = home_url('?p=' . $post->ID);
    } // End if().

    /**
     * Filter the permalink for a post.
     *
     * Only applies to posts with post_type of 'post'.
     *
     * @since 1.5.0
     *
     * @param string  $permalink The post's permalink.
     * @param WP_Post $post      The post in question.
     * @param bool    $leavename Whether to keep the post name.
     */
    $url = apply_filters('post_link', $permalink, $post, $leavename);

    // Ignore all filters and just start with the site url on the home page
    if (is_front_page()) :
        $url = get_site_url();
    endif;

    return $url;
}

function sfsi_premium_get_all_site_postids()
{

    global $wp_rewrite;

    if (empty($wp_rewrite)) {
        $GLOBALS['wp_rewrite'] = new WP_Rewrite();
    }

    $args       = array('_builtin' => false, 'public'   => true);
    $post_types = get_post_types($args, 'names');

    array_push($post_types, "post", "page");

    $arrPosts = get_posts(array(
        'fields'          => 'ids', // Only get post IDs
        'posts_per_page'  => -1,
        'post_status'     => 'publish',
        'post_type'       => $post_types,
        'order'           => 'DESC',
        'orderby'         => 'ID'
    ));

    return isset($arrPosts) && !empty($arrPosts) ? $arrPosts : array();
}

function sfsi_premium_last_x_hr_postids($hour)
{

    global $wp_rewrite;

    if (empty($wp_rewrite)) {
        $GLOBALS['wp_rewrite'] = new WP_Rewrite();
    }

    $args       = array('_builtin' => false, 'public'   => true);
    $post_types = get_post_types($args, 'names');

    array_push($post_types, "post", "page");

    $arrPosts = get_posts(array(
        'fields'          => 'ids', // Only get post IDs
        'posts_per_page'  => -1,
        'post_status'     => 'publish',
        'post_type'       => $post_types,
        'order'           => 'DESC',
        'orderby'         => 'ID',
        'date_query'      => array(
            array(
                'after'     => $hour.' hour ago',  
                'inclusive' => true,
            ),
        ),
    ));

    return isset($arrPosts) && !empty($arrPosts) ? $arrPosts : array();
}

function sfsi_premium_get_all_site_urls($arrPostIds = false, $option5 = null)
{

    $arrPosts = false != $arrPostIds && is_array($arrPostIds) ? $arrPostIds : sfsi_premium_get_all_site_postids();

    $arrUrl     = array();

    // Add home page link
    $homeHttpsUrl = trailingslashit(home_url());
    if (is_null($option5)) {
        $option5           = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
    }
    array_push($arrUrl, $homeHttpsUrl);
    if (isset($option5['sfsi_plus_counts_without_slash']) && $option5['sfsi_plus_counts_without_slash'] == "yes") {
        array_push($arrUrl, rtrim($homeHttpsUrl, '/'));
    }

    // Get all urls
    if (isset($arrPosts) && !empty($arrPosts)) {

        foreach ($arrPosts as $postId) :

            if (isset($postId) && !empty($postId)) :
                if (function_exists("get_the_permalink")) {
                    $url = trailingslashit(get_the_permalink($postId));
                } elseif (function_exists("get_permalink")) {
                    $url = trailingslashit(get_permalink($postId));
                } else {
                    $url = "";
                }
                array_push($arrUrl, $url);
                if (isset($option5['sfsi_plus_counts_without_slash']) && $option5['sfsi_plus_counts_without_slash'] == "yes") {
                    if (function_exists("get_the_permalink")) {
                        $url = rtrim(get_the_permalink($postId), "/");
                    } elseif (function_exists("get_permalink")) {
                        $url = rtrim(get_permalink($postId), "/");
                    } else {
                        $url = "";
                    }
                    array_push($arrUrl, $url);
                }

            endif;

        endforeach;
    }

    // Get taxonomies urls
    // $turls  = sfsi_premium_get_all_taxonomies_urls();
    // $arrUrl = false != $turls ? array_merge( $arrUrl, $turls ) : $arrUrl;

    return $arrUrl;
}

function sfsi_premium_get_permalinks($arrPostids)
{

    $arrUrls = array();

    if (isset($arrPostids) && !empty($arrPostids) && is_array($arrPostids)) {

        foreach ($arrPostids as $postId) :

            if (isset($postId) && !empty($postId)) :
                if (function_exists("get_the_permalink")) {
                    $url = trailingslashit(get_the_permalink($postId));
                } elseif (function_exists("get_permalink")) {
                    $url = trailingslashit(get_permalink($postId));
                } else {
                    $url = "";
                }

                array_push($arrUrls, $url);

            endif;

        endforeach;
    }

    return $arrUrls;
}

function sfsi_premium_get_urls_for_taxonomy($taxonomy)
{

    $urls = array();

    // get the terms of taxonomy
    $terms = get_terms(
        $args = array(
            'taxonomy' => $taxonomy
        )
    );

    if (!empty($terms) && !is_wp_error($terms)) {

        // loop through all terms
        foreach ($terms as $term) {

            if ($term->count > 0)

                // display link to term archive
                $url = trailingslashit(get_term_link($term->term_id));

            array_push($urls, $url);
        }
    }

    $urls = empty($urls) ? false : $urls;

    return $urls;
}

function sfsi_premium_get_all_taxonomies_urls()
{

    $allUrls = array();

    $allTaxonomies = get_taxonomies(array('public' => true, 'show_ui' => true), 'objects', 'and');

    if (isset($allTaxonomies) && !empty($allTaxonomies)) :

        foreach ($allTaxonomies as $taxonomy) :

            $urls = sfsi_premium_get_urls_for_taxonomy($taxonomy->name);

            if (false != $urls) {

                $allUrls = array_merge($allUrls, $urls);
            }

        endforeach;

    endif;

    $allUrls = empty($allUrls) ? false : $allUrls;

    return $allUrls;
}

function sfsi_premium_url_to_postid($url)
{

    global $wp_rewrite;
    global $wp;

    if (empty($wp_rewrite)) {
        $GLOBALS['wp_rewrite'] = new WP_Rewrite();
    }

    if (empty($wp)) {
        $GLOBALS['wp'] = new WP();
    }

    /**
     * Filters the URL to derive the post ID from.
     *
     * @since 2.2.0
     *
     * @param string $url The URL to derive the post ID from.
     */
    $url = apply_filters('url_to_postid', $url);

    $url_host      = str_replace('www.', '', parse_url($url, PHP_URL_HOST));
    $home_url_host = str_replace('www.', '', parse_url(home_url(), PHP_URL_HOST));

    // Bail early if the URL does not belong to this site.
    if ($url_host && $url_host !== $home_url_host) {
        return 0;
    }

    // First, check to see if there is a 'p=N' or 'page_id=N' to match against
    if (preg_match('#[?&](p|page_id|attachment_id)=(\d+)#', $url, $values)) {
        $id = absint($values[2]);
        if ($id)
            return $id;
    }

    // Get rid of the #anchor
    $url_split = explode('#', $url);
    $url = $url_split[0];

    // Get rid of URL ?query=string
    $url_split = explode('?', $url);
    $url = $url_split[0];

    // Set the correct URL scheme.
    $scheme = parse_url(home_url(), PHP_URL_SCHEME);
    $url = set_url_scheme($url, $scheme);

    // Add 'www.' if it is absent and should be there
    if (false !== strpos(home_url(), '://www.') && false === strpos($url, '://www.'))
        $url = str_replace('://', '://www.', $url);

    // Strip 'www.' if it is present and shouldn't be
    if (false === strpos(home_url(), '://www.'))
        $url = str_replace('://www.', '://', $url);

    if (trim($url, '/') === home_url() && 'page' == get_option('show_on_front')) {
        $page_on_front = get_option('page_on_front');

        if ($page_on_front && get_post($page_on_front) instanceof WP_Post) {
            return (int) $page_on_front;
        }
    }

    // Check to see if we are using rewrite rules
    $rewrite = $wp_rewrite->wp_rewrite_rules();

    // Not using rewrite rules, and 'p=N' and 'page_id=N' methods failed, so we're out of options
    if (empty($rewrite))
        return 0;

    // Strip 'index.php/' if we're not using path info permalinks
    if (!$wp_rewrite->using_index_permalinks())
        $url = str_replace($wp_rewrite->index . '/', '', $url);

    if (false !== strpos(trailingslashit($url), home_url('/'))) {
        // Chop off http://domain.com/[path]
        $url = str_replace(home_url(), '', $url);
    } else {
        // Chop off /path/to/blog
        $home_path = parse_url(home_url('/'));
        $home_path = isset($home_path['path']) ? $home_path['path'] : '';
        $url = preg_replace(sprintf('#^%s#', preg_quote($home_path)), '', trailingslashit($url));
    }

    // Trim leading and lagging slashes
    $url = trim($url, '/');

    $request = $url;
    $post_type_query_vars = array();

    foreach (get_post_types(array(), 'objects') as $post_type => $t) {
        if (!empty($t->query_var))
            $post_type_query_vars[$t->query_var] = $post_type;
    }

    // Look for matches.
    $request_match = $request;
    foreach ((array) $rewrite as $match => $query) {

        // If the requesting file is the anchor of the match, prepend it
        // to the path info.
        if (!empty($url) && ($url != $request) && (strpos($match, $url) === 0))
            $request_match = $url . '/' . $request;

        if (preg_match("#^$match#", $request_match, $matches)) {

            if ($wp_rewrite->use_verbose_page_rules && preg_match('/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch)) {
                // This is a verbose page match, let's check to be sure about it.
                $page = get_page_by_path($matches[$varmatch[1]]);
                if (!$page) {
                    continue;
                }

                $post_status_obj = get_post_status_object($page->post_status);
                if (
                    !$post_status_obj->public && !$post_status_obj->protected
                    && !$post_status_obj->private && $post_status_obj->exclude_from_search
                ) {
                    continue;
                }
            }

            // Got a match.
            // Trim the query of everything up to the '?'.
            $query = preg_replace("!^.+\?!", '', $query);

            // Substitute the substring matches into the query.
            $query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

            // Filter out non-public query vars
            parse_str($query, $query_vars);
            $query = array();
            foreach ((array) $query_vars as $key => $value) {
                if (in_array($key, $wp->public_query_vars)) {
                    $query[$key] = $value;
                    if (isset($post_type_query_vars[$key])) {
                        $query['post_type'] = $post_type_query_vars[$key];
                        $query['name'] = $value;
                    }
                }
            }

            // Resolve conflicts between posts with numeric slugs and date archive queries.
            $query = wp_resolve_numeric_slug_conflicts($query);

            // Do the query
            $query = new WP_Query($query);
            if (!empty($query->posts) && $query->is_singular)
                return $query->post->ID;
            else
                return 0;
        }
    }
    return 0;
}

function sfsi_premium_get_option($array, $textkey, $valueToReturnOnFailOrNotExist, $functionToUse = false)
{

    $valueToReturnOnFailOrNotExist = isset($valueToReturnOnFailOrNotExist) ? $valueToReturnOnFailOrNotExist : false;

    if (is_array($array) && isset($array) && !empty($array) && isset($textkey) && !empty($textkey)) {

        if (isset($array[$textkey])) {

            $value = $array[$textkey];

            if (false != $functionToUse && function_exists($functionToUse)) {
                return call_user_func_array($functionToUse, array($value));
            } else {
                return $array[$textkey];
            }
        }
    }

    return $valueToReturnOnFailOrNotExist;
}

function sfsi_premium_get_custom_post_types()
{
    $args               = array('_builtin' => false, 'public'   => true);
    $custom_post_types  = get_post_types($args, 'names');
    $custom_post_types  = array_values($custom_post_types);
    return $custom_post_types;
}

function sfsi_premium_get_all_taxonomies()
{
    $allListTaxonomies = get_taxonomies(array('_builtin' => false, 'public' => true, 'show_ui' => true), 'objects', 'and');
    return $allListTaxonomies;
}

function sfsi_premium_get_section_data($sectionNumber)
{

    $dbKey  = 'sfsi_premium_section' . $sectionNumber . '_options';
    $option = maybe_unserialize(get_option($dbKey, false));
    $option = isset($option) && !empty($option) ? $option : array();

    return $option;
}

if (!function_exists( 'sfsi_premium_nl2br' )) {

    function sfsi_premium_nl2br($string)
    {
        $string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string);
        return $string;
    }
}

if (!function_exists( 'sfsiIsArrayOrObject' )) {

    function sfsiIsArrayOrObject($value)
    {

        $isArrayOrObject = false;

        if (is_array($value) || is_object($value)) {
            $isArrayOrObject = true;
        }

        return $isArrayOrObject;
    }
}

if (!function_exists('wp_scripts')) {
    function wp_scripts()
    {
        global $wp_scripts;
        if (!($wp_scripts instanceof WP_Scripts)) {
            $wp_scripts = new WP_Scripts();
        }
        return $wp_scripts;
    }
}


if (!function_exists( 'wp_add_inline_script' )) {

    function wp_add_inline_script($handle, $data, $position = 'after')
    {
        global $wp_scripts;
        if (!$data) {
            return false;
        }

        $script   = $wp_scripts->get_data($handle, 'data');
        $script  .= $data;
        return $wp_scripts->add_data($handle, 'data', $script);
    }
}

if (!function_exists( 'sfsi_premium_get_the_ID' )) {

    function sfsi_premium_get_the_ID()
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
}

if (!function_exists( 'sfsi_premium_get_active_url' )) {

    function sfsi_premium_get_active_url()
    {

        $url      = get_bloginfo('url');
        $post_id  = sfsi_premium_get_the_ID();

        if (!in_the_loop()) {

            if (is_author()) {

                $url   = get_author_posts_url(get_the_author_meta('ID'));
            } else if (is_archive()) {
                if (is_category() || is_tag() || is_tax()) {
                    $url   = get_term_link(get_queried_object()->term_id);
                }
            } else if (is_singular()) {
                $url  =  get_permalink($post_id);
                return $url;
            }
        } else if ($post_id) {
            $url  =  get_permalink($post_id);
        }

        if (is_string($url)) {
            return $url;
        } else {
            return get_bloginfo('url');
        }
    }
}

if (!function_exists( 'sfsi_premium_get_image_directory_path' )) {

    function sfsi_premium_get_image_directory_path($imgUrl)
    {

        $upload_dir = wp_upload_dir();

        $site_url   = parse_url($upload_dir['url']);
        $image_path = parse_url($imgUrl);

        //force the protocols to match if needed
        if (isset($image_path['scheme']) && ($image_path['scheme'] !== $site_url['scheme'])) {
            $imgUrl = str_replace($image_path['scheme'], $site_url['scheme'], $imgUrl);
        }

        $uploadedImgDirPath = false;

        if (0 === strpos($imgUrl, $upload_dir['baseurl'] . '/')) {
            $uploadedImgDirPath = substr($imgUrl, strlen($upload_dir['baseurl'] . '/'));
        }

        if (false != $uploadedImgDirPath) {
            $uploadedImgDirPath = trailingslashit($upload_dir['basedir']) . $uploadedImgDirPath;
        }

        return $uploadedImgDirPath;
    }
}

if (!function_exists('sfsi_premium_active_plugins')) {

    function sfsi_premium_active_plugins()
    {

        $activePlugins = get_option('active_plugins', false);

        $arrActivePlugins = array();

        foreach ($activePlugins as $path) :

            $arrData     = array();
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . "/" . $path);

            if (
                isset($plugin_data['Name']) && !empty($plugin_data['Name'])
                && isset($plugin_data['Version']) && !empty($plugin_data['Version'])
            ) :

                $arrData['Name']    = $plugin_data['Name'];
                $arrData['Version'] = $plugin_data['Version'];

                array_push($arrActivePlugins, $arrData);
                unset($arrData);

            endif;

        endforeach;

        return $arrActivePlugins;
    }
}

if (!function_exists('sfsi_plus_delete_image')) {

    function sfsi_plus_delete_image($url)
    {

        $imageDeleted = false;

        $path      = parse_url($url, PHP_URL_PATH);

        if (is_file($_SERVER['DOCUMENT_ROOT'] . $path) && file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
            $imageDeleted = unlink($_SERVER['DOCUMENT_ROOT'] . $path);
        }

        return $imageDeleted;
    }
}

if (!function_exists('sfsi_plus_delete_image_with_dir_path')) {

    function sfsi_plus_delete_image_with_dir_path($dirPath)
    {

        $imageDeleted = false;

        if (is_file($dirPath) && file_exists($dirPath)) {
            $imageDeleted = unlink($dirPath);
        }

        return $imageDeleted;
    }
}

if (!function_exists('sfsi_get_other_icon_image')) {

    function sfsi_get_other_icon_image($iconName, $arrImg, $customIconIndex = null)
    {

        $iconImgVal = false;

        if (isset($arrImg[$iconName]) && !empty($arrImg[$iconName])) {

            if ("custom" == $iconName && null !== $customIconIndex && is_array($arrImg[$iconName])) {

                if (isset($arrImg[$iconName][$customIconIndex]) && !empty($arrImg[$iconName][$customIconIndex])) {

                    $iconImgVal = $arrImg[$iconName][$customIconIndex];
                }
            } else {
                $iconImgVal = $arrImg[$iconName];
            }
        }

        return $iconImgVal;
    }
}

if (!function_exists('sfsi_get_custom_icons_images')) {

    function sfsi_get_custom_icons_images($option1 = false)
    {

        $option1 = false != $option1 && is_array($option1) ? $option1 : maybe_unserialize(get_option('sfsi_premium_section1_options', false));

        $custom_icons = array();

        if (
            !empty($option1) && is_array($option1)
            && isset($option1['sfsi_custom_files']) && !empty($option1['sfsi_custom_files'])
        ) {

            $custom_icons   = maybe_unserialize($option1['sfsi_custom_files']);
            $custom_icons   = is_array($custom_icons) ? $custom_icons : array();
        }

        return $custom_icons;
    }
}

if (!function_exists('sfsi_plus_get_displayed_std_desktop_icons')) {

    function sfsi_plus_get_displayed_std_desktop_icons($option1 = false)
    {

        $option1 =  false !== $option1 && is_array($option1) ? $option1 : maybe_unserialize(get_option('sfsi_premium_section1_options', false));

        $arrDisplay = array();

        if (false !== $option1 && is_array($option1)) {

            foreach ($option1 as $key => $value) {

                if (strpos($key, '_display') !== false) {

                    $arrDisplay[$key] = isset($option1[$key]) ? sanitize_text_field($option1[$key]) : '';
                }
            }
        }

        return $arrDisplay;
    }
}

if (!function_exists('sfsi_plus_get_displayed_custom_desktop_icons')) {

    function sfsi_plus_get_displayed_custom_desktop_icons($option1 = false)
    {

        $option1 = false != $option1 && is_array($option1) ? $option1 : maybe_unserialize(get_option('sfsi_premium_section1_options', false));

        $arrDisplay = array();

        if (
            !empty($option1) && is_array($option1) && isset($option1['sfsi_custom_desktop_icons'])
            && !empty($option1['sfsi_custom_desktop_icons'])
        ) :

            $arrdbDisplay = maybe_unserialize($option1['sfsi_custom_desktop_icons']);

            if (is_array($arrdbDisplay)) :

                $arrDisplay = $arrdbDisplay;

            endif;

        endif;

        return $arrDisplay;
    }
}

if (!function_exists('sfsi_generate_other_icon_effect_admin_html')) {

    function sfsi_generate_other_icon_effect_admin_html( $iconName, $arrImg, $arrActiveDesktopIcons, $option3, $customIconIndex = -1, $customIconImgUrl = null, $customIconSrNo = null ) {

        $iconImgVal         = false;
        $activeIconImgUrl   = false;

        $classForRevertLink = 'hide';
        $defaultIconImgUrl  = false;

        $displayIconClass   = "hide";

        $arruploadDir   = wp_upload_dir();

        $sfsi_plus_flat_icon_color = '';
        $sfsi_plus_flat_theme_flag = false;
        if ( is_null( $option3 ) ) {
            $option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
        }

        if (isset($iconName) && !empty($iconName)) {

            if ("custom" == $iconName && $customIconIndex > -1) {

                if (null !== $customIconImgUrl) {

                    $activeIconImgUrl  = $customIconImgUrl;
                    $defaultIconImgUrl = $customIconImgUrl;

                    // Check if icon is selected under Question 1
                    if (in_array($customIconImgUrl, $arrActiveDesktopIcons)) {
                        $displayIconClass = "show";
                    }

                    // Check if other icon set for custom icons in Question 4 -> Mouse-Over effects -> Show other icons on mouse-over (Only applied for Desktop Icons)

                    if (
                        isset($arrImg['custom'][$customIconIndex])
                        && !empty($arrImg['custom'][$customIconIndex]) && is_array($arrImg['custom'])
                    ) {

                        $iconImgVal         = SFSI_PLUS_UPLOAD_DIR_BASEURL . $arrImg['custom'][$customIconIndex];
                        $activeIconImgUrl   = $iconImgVal;
                        $classForRevertLink = "show";
                    }

                    $iconNameStr = $iconName . $customIconSrNo;
                }
            } else {
                if ("google" == $iconName) {
                    return false;
                }
                $dbKey = "sfsi_plus_" . $iconName . "_display";

                if ( in_array( $dbKey, $arrActiveDesktopIcons ) && "yes" == $arrActiveDesktopIcons[$dbKey] ) {
                    $displayIconClass = "show";
                }

                $iconImgVal         = sfsi_get_other_icon_image($iconName, $arrImg);
                $defaultIconImgUrl  = "facebook" == $iconName ? sfsi_plus_get_icon_image("fb") : sfsi_plus_get_icon_image($iconName);
                //$activeIconImgUrl   = ( false != $iconImgVal && !filter_var( $iconImgVal, FILTER_VALIDATE_URL ) ) ? SFSI_PLUS_UPLOAD_DIR_BASEURL . $iconImgVal : $defaultIconImgUrl;
                if ( false != $iconImgVal ) { 
                    $iconImgVal = filter_var($iconImgVal, FILTER_SANITIZE_URL);
                    if (filter_var($iconImgVal, FILTER_VALIDATE_URL) !== false) {
                        $activeIconImgUrl = $iconImgVal;
                    } else {
                        $activeIconImgUrl = SFSI_PLUS_UPLOAD_DIR_BASEURL . $iconImgVal;
                    }
                } else {
                    $activeIconImgUrl = $defaultIconImgUrl;
                }

                $classForRevertLink = false != $iconImgVal ? 'show' : 'hide';

                $iconNameStr = $iconName;

                /* Flat icon */
                $active_theme = ( isset( $option3['sfsi_plus_actvite_theme'] ) && !empty( $option3['sfsi_plus_actvite_theme'] ) ) ? $option3['sfsi_plus_actvite_theme'] : '' ;
                if( $active_theme == 'flat' ) {
                    $sfsi_plus_flat_theme_flag = true;
                    $sfsi_plus_flat_icon_color = sfsi_plus_flat_icon_color( $iconName, $option3 );
                }
            }

            if ( false != $iconImgVal && !filter_var( $iconImgVal, FILTER_VALIDATE_URL ) ) {
                $iconImgVal = SFSI_PLUS_UPLOAD_DIR_BASEURL . $iconImgVal;
            }

            $attrCustomIconSrNo  = null !== $customIconSrNo ? 'data-customiconsrno="' . $customIconSrNo . '"' : null;
            $attrCustomIconIndex = -1 != $customIconIndex ? 'data-customiconindex="' . $customIconIndex . '"' : null;

            $attrIconName = 'data-iconname="' . $iconName . '"';

            ?>
            <div <?php echo $attrCustomIconIndex; ?> <?php echo $attrIconName; ?> class="col-md-3 bottommargin20 <?php echo $displayIconClass; ?>">
                <?php
                $temp = $iconNameStr == 'twitter' ? 'x (Twitter)' : $iconNameStr;
                ?>

                <label <?php echo $attrCustomIconSrNo; ?> class="mouseover_other_icon_label"><?php echo ucfirst( $temp ); ?></label>

                <?php if ( $sfsi_plus_flat_theme_flag ) { ?>
                    <span class="sfsiplus_icon_img_wrapper mouseover_sfsi_plus_<?php echo esc_attr( $iconName ); ?>_bgColor" <?php echo esc_html( $sfsi_plus_flat_icon_color ); ?>>
                        <img data-defaultImg="<?php echo $defaultIconImgUrl; ?>" class="mouseover_other_icon_img" src="<?php echo $activeIconImgUrl; ?>">
                    </span>
                <?php } else { ?>
                    <img data-defaultImg="<?php echo $defaultIconImgUrl; ?>" class="mouseover_other_icon_img" src="<?php echo $activeIconImgUrl; ?>">
                <?php } ?>

                <input <?php echo $attrCustomIconIndex; ?> <?php echo $attrIconName; ?> type="hidden" value="<?php echo $iconImgVal; ?>" name="mouseover_other_icon_<?php echo $iconName; ?>">

                <a <?php echo $attrCustomIconIndex; ?> <?php echo $attrIconName; ?> id="btn_mouseover_other_icon_<?php echo $iconName; ?>" class="mouseover_other_icon_change_link js_mouseover_upload" href="javascript:void(0)" class="mouseover_other_icon" data-nonce="<?php echo wp_create_nonce('plus_MouseOverIcons') ?>"><?php _e('Change', 'ultimate-social-media-plus'); ?></a>

                <a <?php echo $attrCustomIconIndex; ?> <?php echo $attrIconName; ?>class="<?php echo $classForRevertLink; ?> mouseover_other_icon_revert_link js_mouseover_revert" href="javascript:void(0)" class="mouseover_other_icon" data-nonce="<?php echo wp_create_nonce('sfsi_premium_deleteIcons') ?>"><?php _e('Revert', 'ultimate-social-media-plus'); ?></a>

            </div>

<?php

        }
    }
}

if (!function_exists('sfsi_premium_get_email_icon')) {

    function sfsi_premium_get_email_icon()
    {

        $email_image = "subscribe.png";

        $option2 =  maybe_unserialize(get_option('sfsi_premium_section2_options', false));

        if ("sfsi" == $option2['sfsi_plus_rss_icons']) {
            $email_image = "sf_arow_icn.png";
        } elseif ("email" == $option2['sfsi_plus_rss_icons']) {
            $email_image = "email.png";
        }

        return $email_image;
    }
}
if (!function_exists('sfsi_premium_is_site_url')) {

    function sfsi_premium_is_site_url()
    {
        global $wp_filter;
        if (!isset($wp_filter['registered_taxonomy'])) {
            return false;
        }

        return count($wp_filter['registered_taxonomy']->callbacks) > 0;
    }
}
if (!function_exists('get_the_permalink')) {
    function get_the_permalink($postId)
    {
        return trailingslashit(get_permalink($postId));
    }
}

if (!function_exists('sfsi_premium_wordpress_locale_from_locale_code_global')) {
    function sfsi_premium_wordpress_locale_from_locale_code($locale_code)
    {
        $sfsi_premium_wordpress_locale_from_locale_code = array(
            "ar_AR",
            "az_AZ",
            "af_ZA",
            "bg_BG",
            "ms_MY",
            "bn_IN",
            "bs_BA",
            "ca_ES",
            "cy_GB",
            "da_DK",
            "de_DE",
            "en_US",
            "el_GR",
            "eo_EO",
            "es_ES",
            "et_EE",
            "eu_ES",
            "fa_IR",
            "fi_FI",
            "fr_FR",
            "gl_ES",
            "he_IL",
            "hi_IN",
            "hr_HR",
            "hu_HU",
            "hy_AM",
            "id_ID",
            "is_IS",
            "it_IT",
            "ja_JP",
            "ko_KR",
            "lt_LT",
            "my_MM",
            "nl_NL",
            "nn_NO",
            "pl_PL",
            "ps_AF",
            "pt_BR",
            "ro_RO",
            "ru_RU",
            "sk_SK",
            "sl_SI",
            "sq_AL",
            "sr_RS",
            "sv_SE",
            "th_TH",
            "tl_PH",
            "tr_TR",
            "ug_CN",
            "uk_UA",
            "vi_VN",
            "zh_CN",
            "cs_CZ",
            "ur_PK"
        );
        if (!in_array($locale_code, $sfsi_premium_wordpress_locale_from_locale_code)) {
            foreach ($sfsi_premium_wordpress_locale_from_locale_code as $key => $value) {
                $split_key = explode('_', $value);
                if ($split_key[0] == $locale_code) {
                    $locale_code = $value;
                }
            }
        }


        // if (array_key_exists($locale_code, $sfsi_premium_wordpress_locale_from_locale_code)) { }

        return $locale_code;
    }
}


if (!function_exists('sfsi_premium_youtube_options_values')) {
    function sfsi_premium_map_language_values($locale_code, $visit_icon_name)
    {
        $sfsi_premium_map_language_values = array(
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
        );
        if (!in_array($locale_code, $sfsi_premium_map_language_values)) {
            foreach ($sfsi_premium_map_language_values as $key => $value) {
                $value_us = strstr($value, '_us_');
                $value_me = strstr($value, '_me_');

                if ($visit_icon_name == "automatic_visit_us" && !empty($value_us)) {
                    $split_key = explode('_us_', $value);
                } else if ($visit_icon_name == "automatic_visit_me" && !empty($value_me)) {
                    $split_key = explode('_me_', $value);
                }
                if (!empty($split_key) && array_key_exists(1, $split_key)) {
                    if ($split_key[1] == $locale_code) {
                        $locale_code = $value;
                    }
                }
            }
        };

        return $locale_code;
    }
}

function sfsi_plus_amp_FBlike($permalink, $show_count = '')
{
    $send = 'false';
    $width = 180;
    $show_count = 0;

    $permalink = trailingslashit($permalink);
    $fb_like_html = "<amp-facebook-like data-href='" . $permalink . "' ";


    if ($show_count == 1) {
        $fb_like_html .= " data-layout='button_count' ";
    } else {
        $fb_like_html .= " data-layout='button'";
    }
    $fb_like_html .= " data-action='like'  data-share='false'  ></amp-facebook-like>";
  
    return $fb_like_html;
}

function sfsi_plus_flat_icon_color( $iconName, $option3 ) {

    $sfsi_plus_icon_bgColor = $sfsi_plus_icon_bgColor_style = '';
    if ( $iconName ) {

        switch ( $iconName ) {
            case "rss":
                if ( isset( $option3['sfsi_plus_rss_bgColor'] ) && $option3['sfsi_plus_rss_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_rss_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#FF9845';
                }
            break;

            case "email":
                if ( isset( $option3['sfsi_plus_email_bgColor'] ) && $option3['sfsi_plus_email_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_email_bgColor'];
                } else {
                    $option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );
                    if ($option2['sfsi_plus_rss_icons'] == "sfsi") {
                        $sfsi_plus_icon_bgColor = '#05B04E';
                    } elseif ($option2['sfsi_plus_rss_icons'] == "email") {
                        $sfsi_plus_icon_bgColor = '#343D44';
                    } else {
                        $sfsi_plus_icon_bgColor = '#16CB30';
                    }
                }
            break;

            case "facebook":
                if ( isset( $option3['sfsi_plus_facebook_bgColor'] ) && $option3['sfsi_plus_facebook_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_facebook_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#336699';
                }
            break;

            case "twitter":
                if ( isset( $option3['sfsi_plus_twitter_bgColor'] ) && $option3['sfsi_plus_twitter_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_twitter_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#000000';
                }
            break;

            case "share":
                if ( isset( $option3['sfsi_plus_share_bgColor'] ) && $option3['sfsi_plus_share_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_share_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#26AD62';
                }
            break;

            case "youtube":
                if ( isset( $option3['sfsi_plus_youtube_bgColor'] ) && $option3['sfsi_plus_youtube_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_youtube_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = 'linear-gradient(141.52deg, #E02F2F 14.26%, #E02F2F 48.98%, #C92A2A 49.12%, #C92A2A 85.18%)';
                }
            break;

            case "linkedin":
                if ( isset( $option3['sfsi_plus_linkedin_bgColor'] ) && $option3['sfsi_plus_linkedin_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_linkedin_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#0877B5';
                }
            break;

            case "pinterest":
                if ( isset( $option3['sfsi_plus_pinterest_bgColor'] ) && $option3['sfsi_plus_pinterest_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_pinterest_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#CC3333';
                }
            break;

            case "instagram":
                if ( isset( $option3['sfsi_plus_instagram_bgColor'] ) && $option3['sfsi_plus_instagram_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_instagram_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#336699';
                }
            break;

            case "ria":
                if (isset($option3['sfsi_plus_ria_bgColor']) && $option3['sfsi_plus_ria_bgColor'] != '') {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_ria_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#10A9A0';
                }
                break;

            case "houzz":
                if ( isset( $option3['sfsi_plus_houzz_bgColor'] ) && $option3['sfsi_plus_houzz_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_houzz_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#7BC043';
                }
            break;

            case "snapchat":
                if ( isset( $option3['sfsi_plus_snapchat_bgColor'] ) && $option3['sfsi_plus_snapchat_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_snapchat_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#EDEC1F';
                }
            break;

            case "whatsapp":
                if ( isset( $option3['sfsi_plus_whatsapp_bgColor'] ) && $option3['sfsi_plus_whatsapp_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_whatsapp_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#3ED946';
                }
            break;

            case "skype":
                if ( isset( $option3['sfsi_plus_skype_bgColor'] ) && $option3['sfsi_plus_skype_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_skype_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#00A9F1';
                }
            break;

            case "phone":
                if ( isset( $option3['sfsi_plus_phone_bgColor'] ) && $option3['sfsi_plus_phone_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_phone_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#51AD47';
                }
            break;

            case "vimeo":
                if ( isset( $option3['sfsi_plus_vimeo_bgColor'] ) && $option3['sfsi_plus_vimeo_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_vimeo_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#1AB7EA';
                }
            break;

            case "soundcloud":
                if ( isset( $option3['sfsi_plus_soundcloud_bgColor'] ) && $option3['sfsi_plus_soundcloud_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_soundcloud_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#FF541C';
                }
            break;

            case "yummly":
                if ( isset( $option3['sfsi_plus_yummly_bgColor'] ) && $option3['sfsi_plus_yummly_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_yummly_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#E36308';
                }
            break;

            case "flickr":
                if ( isset( $option3['sfsi_plus_flickr_bgColor'] ) && $option3['sfsi_plus_flickr_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_flickr_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#FF0084';
                }
            break;

            case "reddit":
                if ( isset( $option3['sfsi_plus_reddit_bgColor'] ) && $option3['sfsi_plus_reddit_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_reddit_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#FF642C';
                }
            break;

            case "tumblr":
                if ( isset( $option3['sfsi_plus_tumblr_bgColor'] ) && $option3['sfsi_plus_tumblr_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_tumblr_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#36465F';
                }
            break;

            case "fbmessenger":
                if ( isset( $option3['sfsi_plus_fbmessenger_bgColor'] ) && $option3['sfsi_plus_fbmessenger_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_fbmessenger_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#447BBF';
                }
            break;

            case "gab":
                if ( isset( $option3['sfsi_plus_gab_bgColor'] ) && $option3['sfsi_plus_gab_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_gab_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#25CC80';
                }
            break;

            case "mix":
                if ( isset( $option3['sfsi_plus_mix_bgColor'] ) && $option3['sfsi_plus_mix_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_mix_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = 'conic-gradient(from 180deg at 50% 50%, #DE201D 0deg, #DE201D 117.02deg, #FF8126 117.58deg, #FFA623 230.42deg, #FFD51F 231.6deg, #FFD51F 360deg)';
                }
            break;

            case "ok":
                if ( isset( $option3['sfsi_plus_ok_bgColor'] ) && $option3['sfsi_plus_ok_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_ok_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#F58220';
                }
            break;

            case "telegram":
                if ( isset( $option3['sfsi_plus_telegram_bgColor'] ) && $option3['sfsi_plus_telegram_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_telegram_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#33A1D1';
                }
            break;

            case "vk":
                if ( isset( $option3['sfsi_plus_vk_bgColor'] ) && $option3['sfsi_plus_vk_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_vk_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#4E77A2';
                }
            break;

            case "weibo":
                if ( isset( $option3['sfsi_plus_weibo_bgColor'] ) && $option3['sfsi_plus_weibo_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_weibo_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#E6162D';
                }
            break;

            case "wechat":
                if ( isset( $option3['sfsi_plus_wechat_bgColor'] ) && $option3['sfsi_plus_wechat_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_wechat_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#4BAD33';
                }
            break;

            case "xing":
                if ( isset( $option3['sfsi_plus_xing_bgColor'] ) && $option3['sfsi_plus_xing_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_xing_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#005A60';
                }
            break;

            case "copylink":
                if ( isset( $option3['sfsi_plus_copylink_bgColor'] ) && $option3['sfsi_plus_copylink_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_copylink_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = 'linear-gradient(180deg, #C295FF 0%, #4273F7 100%)';
                }
            break;

            case "mastodon":
                if ( isset( $option3['sfsi_plus_mastodon_bgColor'] ) && $option3['sfsi_plus_mastodon_bgColor'] != '' ) {
                    $sfsi_plus_icon_bgColor = $option3['sfsi_plus_mastodon_bgColor'];
                } else {
                    $sfsi_plus_icon_bgColor = '#583ED1';
                }
            break;
        }

        if ( $sfsi_plus_icon_bgColor ) {
            $sfsi_plus_icon_bgColor_style = "style=background:" . $sfsi_plus_icon_bgColor . ";";
        }
    }

    return $sfsi_plus_icon_bgColor_style;
}

function sfsi_premium_mouseOver_effect_classlist() {

    $sfsi_section3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
    $mouse_hover_effect = '';
    if ( isset( $sfsi_section3['sfsi_plus_mouseOver'] ) && 'yes' === $sfsi_section3['sfsi_plus_mouseOver'] && !is_admin() ) {
        $mouse_hover_effect .= ' sfsi-plus-mouseOver-effect sfsi-plus-mouseOver-effect-';
        $mouse_hover_effect .= isset( $sfsi_section3["sfsi_plus_mouseOver_effect"] ) ? $sfsi_section3["sfsi_plus_mouseOver_effect"] : 'fade_in';
    }
    return $mouse_hover_effect;
}