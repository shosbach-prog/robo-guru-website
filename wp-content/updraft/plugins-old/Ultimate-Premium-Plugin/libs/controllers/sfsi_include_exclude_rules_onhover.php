<?php
function sfsi_plus_is_url_contain_keyword($keywords,$url=null)
{   
    global $wp;
    if(null===$url){
        $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ));
        $current_url = $current_url."/";
    }else{
        $current_url=$url;
    }
    // ($current_url,$keywords);
    if(false != isset($keywords) && is_array($keywords)){
    
        $count = count($keywords);

            for($i = 0; $i < $count; $i++)
            {
                if (strpos(strtolower($current_url), strtolower($keywords[$i]))) {
                    return true;
                }
            }           
    }
    return false;
}

function sfsi_plus_onhover_is_any_rules_settingActive($ruleType,$option8=false){

    $isSettingActive = false;

    $option8 = false != $option8 ? $option8: maybe_unserialize(get_option('sfsi_premium_section8_options',false));

    $keys = array('tag','category','date_archive','author_archive','post','page','home','search');

    foreach ($keys as $value) {
        
        $value = 'sfsi_plus_icon_hover_'.$ruleType.'_'.$value;
        // echo $value,($option8[$value]);
        if(isset($option8[$value]) && !empty($option8[$value]) && "yes" == strtolower($option8[$value])){
            $isSettingActive = true;
            break;
        }
    }

    $isSettingActive = $isSettingActive || (isset($option8['sfsi_plus_icon_hover_switch_'.$ruleType.'_taxonomies']) && !empty($option8['sfsi_plus_icon_hover_switch_'.$ruleType.'_taxonomies']) && "yes" == $option8['sfsi_plus_icon_hover_switch_'.$ruleType.'_taxonomies']) 
        || (isset($option8['sfsi_plus_icon_hover_switch_'.$ruleType.'_custom_post_types']) && !empty($option8['sfsi_plus_icon_hover_switch_'.$ruleType.'_custom_post_types']) && "yes" == $option8['sfsi_plus_icon_hover_switch_'.$ruleType.'_custom_post_types']);
    return $isSettingActive;
}

//Check page excluded or not
function sfsi_plus_onhover_icon_exclude($url,$is_archive=null,$is_date=null,$is_author=null,$option8=null)
{ 
    if(is_null($option8)){
        $option8 = maybe_unserialize(get_option('sfsi_premium_section8_options',false));
    }
    $postid= url_to_postid($url);
    $home_url=get_home_url();
    if(substr($home_url, -1) !== '/') {
        $home_url .= '/';
    }
    
    if(is_null($is_date)){
        $is_date=is_date();
    }
    if(is_null($is_author)){
        $is_author=is_author();
    }
    $is_home=$url===$home_url;
    if(function_exists('wp_parse_url')){
        $parsed_url=wp_parse_url($url);
    }else{
        $parsed_url=parse_url($url);
    }
    // $page=get_page_by_path($parsed_url['path']);
    // if(!is_null($page)){
    //     $is_page=true;
    // }else{
    //     $is_page=false;
    // }
    
    $category_base= get_option('category_base');
    $tag_base= get_option('tag_base');
    if($category_base===""){
        $category_base="category";
    }
    if($tag_base===""){
        $tag_base="tag";
    }

    if(strpos($url,$home_url.$category_base)===0 || strpos($url,$home_url.'category')===0){
        $is_category=true;
    }else{
        $is_category=false;
    }
    if(strpos($url,$home_url.$tag_base)===0 || strpos($url,$home_url.'tag')===0){
        $is_tag=true;
    }else{
        $is_tag=false;
    }
    $query_vars=wp_parse_args(isset($parsed_url['query'])?$parsed_url['query']:'');
    if(isset($query_vars['s'])){
        $is_search=true;
    }else{
        $is_search=false;
    }
    if(is_null($is_archive)){
        $is_archive=is_archive()|| $is_category || $is_tag||$is_author||$is_date;
    }
    // ($category_base,$tag_base);

    $urlKeywords = isset($option8['sfsi_plus_icon_hover_exclude_urlKeywords']) && !empty($option8['sfsi_plus_icon_hover_exclude_urlKeywords']) ? $option8['sfsi_plus_icon_hover_exclude_urlKeywords'] : array();

    $switchExclude = isset($option8['sfsi_plus_icon_hover_exclude_url']) && !empty($option8['sfsi_plus_icon_hover_exclude_url']) ? $option8['sfsi_plus_icon_hover_exclude_url'] : "no";
    $post=null;
    if($postid>0){
        $post=get_post($postid);
    }
    // ($post);
    $anySettingSelected = sfsi_plus_onhover_is_any_rules_settingActive('exclude',$option8);

    // ($anySettingSelected);
    if($anySettingSelected){
        // ($postid,is_single($postid));

        if($is_archive){

            if ($is_tag){
                if($option8['sfsi_plus_icon_hover_exclude_tag'] == 'yes'){ return true; }
            }
            else if ($is_category){
                if($option8['sfsi_plus_icon_hover_exclude_category'] == 'yes'){ return true; }
            }
            else if ($is_date){
                if($option8['sfsi_plus_icon_hover_exclude_date_archive'] == 'yes'){ return true; }
            }
            else if ($is_author){
                if($option8['sfsi_plus_icon_hover_exclude_author_archive'] == 'yes'){ return true; }
            }
            else if(isset($option8['sfsi_plus_icon_hover_switch_exclude_taxonomies']) && $option8['sfsi_plus_icon_hover_switch_exclude_taxonomies']=="yes"){

                $arrSfsi_plus_list_exclude_taxonomies = (isset($option8['sfsi_plus_icon_hover_list_exclude_taxonomies']))  ?( is_array($option8['sfsi_plus_icon_hover_list_exclude_taxonomies'])? $option8['sfsi_plus_icon_hover_list_exclude_taxonomies'] : maybe_unserialize($option8['sfsi_plus_icon_hover_list_exclude_taxonomies']) ) : array();

                $arrExcludeTax = array_filter($arrSfsi_plus_list_exclude_taxonomies);

                if(!empty($arrExcludeTax)){
                    
                    $custom_taxonomies= get_terms(array(
                        'taxonomies'=>$arrExcludeTax,
                        'hide_empty'=>'false'
                    ));
                    foreach($custom_taxonomies as $custom_tax){
                        if(in_array($custom_tax->taxonomy,$arrExcludeTax)){
                            if(get_term_link($custom_tax->term_taxonomy_id)==$url){
                                return true;
                            }
                        }
                    }
                    if($switchInclude == 'yes')
                    {
                        return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                    }
                }
                else
                {
                    if($switchExclude == 'yes')
                    {
                        return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                    }
                }
            }                       
            else{
                    if($switchExclude == 'yes')
                    {
                        return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                    }           
            }
        } 
        else if ($postid>0 && null!==$post && isset($post->post_type) && 'post'===$post->post_type)
        {
            if($option8['sfsi_plus_icon_hover_exclude_post'] == 'yes'){ return true; }

            else if(isset($option8['sfsi_plus_icon_hover_switch_exclude_custom_post_types']) && $option8['sfsi_plus_icon_hover_switch_exclude_custom_post_types']=="yes")
            {

                $arrSfsi_plus_list_exclude_custom_post_types = (isset($option8['sfsi_plus_icon_hover_list_exclude_custom_post_types'])) ? maybe_unserialize($option8['sfsi_plus_icon_hover_list_exclude_custom_post_types']) : array();

                $arrExcludePostTypes = array_filter($arrSfsi_plus_list_exclude_custom_post_types);

                if(!empty($arrExcludePostTypes)){

                    $socialObj      = new sfsi_plus_SocialHelper();
                    $post_id        = $socialObj->sfsi_get_the_ID();
                    $curr_post_type = get_post_type($post_id);

                    if($post_id && in_array($curr_post_type, $arrExcludePostTypes)){ 
                        return true;            
                    }
                    else
                    {
                        if($switchExclude == 'yes')
                        {
                            return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                        }
                    }                                   
                }
                else
                {
                    if($switchExclude == 'yes')
                    {
                        return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                    }
                }       
            }       
            else
            {
                if($switchExclude == 'yes')
                {
                    return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                }
            }
        }
        else if ($option8['sfsi_plus_icon_hover_switch_exclude_custom_post_types']==='yes' && null!==$post && isset($post->post_type) && in_array($post->post_type,$option8['sfsi_plus_icon_hover_list_exclude_custom_post_types'])){
            return true;
        }
        else if ( $postid>0 && null!==$post && isset($post->post_type) && 'page'===$post->post_type && !is_front_page())
        {
            if($option8['sfsi_plus_icon_hover_exclude_page'] == 'yes'){ return true; }
            else
            {
                if($switchExclude == 'yes')
                {
                    return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                }
            }
        }
        else if ($is_home)
        {
            if($option8['sfsi_plus_icon_hover_exclude_home'] == 'yes'){ return true; }
            else
            {
                if($option8['sfsi_plus_icon_hover_exclude_url'] == 'yes')
                {
                    return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                }
            }
        }
        else if ($is_search)
        {
            if($option8['sfsi_plus_icon_hover_exclude_search'] == 'yes'){ return true; }
            else
            {
                if($option8['sfsi_plus_icon_hover_exclude_url'] == 'yes')
                {
                    return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
                }
            }
        }
        else
        {
            if($option8['sfsi_plus_icon_hover_exclude_url'] == 'yes')
            {
                return sfsi_plus_is_url_contain_keyword($option8['sfsi_plus_icon_hover_urlKeywords'],$url);
            }
        }


    }else if('yes' == $switchExclude){

        return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);

    }else{

        return false; // No setting is selected, returning false here because we are negating this value when in use

    }
}

function sfsi_plus_onhover_icon_include($url,$is_archive=null,$is_date=null,$is_author=null,$option8=null)
{
    if(is_null($option8)){
        $option8 = maybe_unserialize(get_option('sfsi_premium_section8_options',false));
    }
    
    $postid= url_to_postid($url);
    
    $home_url=get_home_url();
    
    if(substr($home_url, -1) !== '/') {
        $home_url .= '/';
    }
    if(is_null($is_archive)){
        $is_archive=is_archive()|| $is_category || $is_tag||$is_author||$is_date;
    }
    if(is_null($is_date)){
        $is_date=is_date();
    }
    if(is_null($is_author)){
        $is_author=is_author();
    }
    $is_home=$url===$home_url;
    if(function_exists('wp_parse_url')){
        $parsed_url=wp_parse_url($url);
    }else{
        $parsed_url=parse_url($url);
    }
    //$page=get_page_by_path($parsed_url['path']);
    //if(!is_null($page)){
    //    $is_page=true;
    //}else{
    //    $is_page=false;
    //}

    $category_base= get_option('category_base');
    $tag_base= get_option('tag_base');
    if($category_base===""){
        $category_base="category";
    }
    if($tag_base===""){
        $tag_base="tag";
    }

    if(strpos($url,$home_url.$category_base)===0 || strpos($url,$home_url.'category')===0){
        $is_category=true;
    }else{
        $is_category=false;
    }
    if(strpos($url,$home_url.$tag_base)===0 || strpos($url,$home_url.'tag')===0){
        $is_tag=true;
    }else{
        $is_tag=false;
    }

    $query_vars=wp_parse_args(isset($parsed_url['query'])?$parsed_url['query']:'');
    if(isset($query_vars['s'])){
        $is_search=true;
    }else{
        $is_search=false;
    }

    $urlKeywords = isset($option8['sfsi_plus_icon_hover_include_urlKeywords']) && !empty($option8['sfsi_plus_icon_hover_include_urlKeywords']) ? $option8['sfsi_plus_icon_hover_include_urlKeywords'] : array();

    $switchInclude = isset($option8['sfsi_plus_icon_hover_include_url']) && !empty($option8['sfsi_plus_icon_hover_include_url']) ? $option8['sfsi_plus_icon_hover_include_url'] : "no";

    $post=null;
    if($postid>0){
        $post=get_post($postid);
    }
    //($postid,$post,$option8);
    $anySettingSelected = sfsi_plus_onhover_is_any_rules_settingActive('include',$option8);

    if($anySettingSelected){
        if($is_archive){

            if ($is_tag){
                if($option8['sfsi_plus_icon_hover_include_tag'] == 'yes'){ return true; }
            }
            else if ($is_category){
                if($option8['sfsi_plus_icon_hover_include_category'] == 'yes'){ return true; }
            }
            else if ($is_date){
                if($option8['sfsi_plus_icon_hover_include_date_archive'] == 'yes'){ return true; }
            }
            else if ($is_author){
                if($option8['sfsi_plus_icon_hover_include_author_archive'] == 'yes'){ return true; }
            }
            else if(isset($option8['sfsi_plus_icon_hover_switch_include_taxonomies']) && $option8['sfsi_plus_icon_hover_switch_include_taxonomies']=="yes"){

                $arrIncludeTx = array_filter($option8['sfsi_plus_icon_hover_list_include_taxonomies']);

                if(!empty($arrIncludeTx)){
                    
                    $custom_taxonomies= get_terms(array(
                        'taxonomies'=>$arrIncludeTx,
                        'hide_empty'=>'false'
                    ));
                    foreach($custom_taxonomies as $custom_tax){
                        if(in_array($custom_tax->taxonomy,$arrIncludeTx)){
                            if(get_term_link($custom_tax->term_taxonomy_id)==$url){
                                return true;
                            }
                        }
                    }
                    if($switchInclude == 'yes')
                    {
                        return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                    }
                }
                else
                {
                    if($switchInclude == 'yes')
                    {
                        return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                    }
                }
            }                       
            else{
                    if($switchInclude == 'yes' && !empty($urlKeywords))
                    {
                        return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                    }           
            }
        } 
        else if ($postid>0 && null!==$post && isset($post->post_type) && 'post'===$post->post_type)
        {
            if($option8['sfsi_plus_icon_hover_include_post'] == 'yes'){ return true; }

            else if(isset($option8['sfsi_plus_icon_hover_switch_include_custom_post_types']) && $option8['sfsi_plus_icon_hover_switch_include_custom_post_types']=="yes")
            {

                $arrSfsi_plus_list_include_custom_post_types = (isset($option8['sfsi_plus_icon_hover_list_include_custom_post_types'])) ? maybe_unserialize($option8['sfsi_plus_icon_hover_list_include_custom_post_types']) : array();

                $arrIncludePostTypes = array_filter($arrSfsi_plus_list_include_custom_post_types);

                if(!empty($arrIncludePostTypes)){

                    // $socialObj      = new sfsi_plus_SocialHelper();
                    // $post_id        = $socialObj->sfsi_get_the_ID();
                    $curr_post_type = get_post_type($postid);

                    if($postid && in_array($curr_post_type, $arrIncludePostTypes)){ 
                        return true;            
                    }
                    else
                    {
                        if($switchInclude == 'yes')
                        {
                            return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                        }
                    }                                   
                }
                else
                {
                    if($switchInclude == 'yes')
                    {
                        return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                    }
                }       
            }       
            else
            {
                if($switchInclude == 'yes')
                {
                    return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                }
            }
        }
        else if ($option8['sfsi_plus_icon_hover_switch_include_custom_post_types']==='yes' && null!==$post && isset($post->post_type) && in_array($post->post_type,$option8['sfsi_plus_icon_hover_list_include_custom_post_types'])){
            return true;
        }
        else if ($postid>0 && null!==$post && isset($post->post_type) && 'post'===$post->post_type && !is_front_page())
        {
            if($option8['sfsi_plus_icon_hover_include_page'] == 'yes'){ return true; }
            else
            {
                if($switchInclude == 'yes')
                {
                    return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                }
            }
        }
        else if ($is_home)
        {
            if($option8['sfsi_plus_icon_hover_include_home'] == 'yes'){ return true; }
            else
            {
                if($switchInclude == 'yes')
                {
                    return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                }
            }
        }
        else if ($is_search)
        {
            if($option8['sfsi_plus_icon_hover_include_search'] == 'yes'){ return true; }
            else
            {
                if($switchInclude == 'yes')
                {
                    return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
                }
            }
        }
        else
        {
            if($switchInclude == 'yes')
            {
                return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);
            }
        }

    }else if('yes' == $switchInclude){

        return sfsi_plus_is_url_contain_keyword($urlKeywords,$url);

    }else{

        return true;
    }
}

// add_action( 'template_redirect', 'sfsi_plus_icon_include');
// add_action( 'template_redirect', 'sfsi_plus_icon_exclude');

function sfsi_plus_onhover_icon_get_active_rule_type($option8=null){

    $activeRule = 0;
    if(is_null($option8)){
        $option8    = maybe_unserialize(get_option('sfsi_premium_section8_options',false));
    }
    // foreach($option8 as $index=>$item){

    //     if(strstr($index, 'sfsi_plus_icon_hover')===false){
    //         unset($option8[$index]);
    //     }
    // }
    // ($option8);
    // (array_filter($option8,function($item){return (strripos($item, 'sfsi_plus_icon_hover')>-1);}));
    if(isset($option8['sfsi_plus_icon_hover_on_all_pages'])){
        if('include'===$option8['sfsi_plus_icon_hover_on_all_pages']){
            $activeRule=1;
        }elseif('exclude'===$option8['sfsi_plus_icon_hover_on_all_pages']){
            $activeRule=2;
        }
    }
    // $activeRule = isset($option8['sfsi_plus_icon_hover_icons_rules']) ? intval($option8['sfsi_plus_icon_hover_icons_rules']) : 0;

    return $activeRule;
}


function sfsi_plus_onhover_shall_show_icons($location,$is_archive=null,$is_date=null,$is_author=null,$option8=null){
    $shallDisplayIcons = true;
// //  checking if we need to apply the include exclude rule if applies check rules to show else show.
//     if(false===sfsi_plus_include_exclude_apply_to($location)){
//         return true;
//     }

    $activeRule = sfsi_plus_onhover_icon_get_active_rule_type($option8);

    // ($activeRule);die();

    switch($activeRule) {

        case 1:
        
            $shallDisplayIcons = sfsi_plus_onhover_icon_include($location,$is_archive,$is_date,$is_author,$option8);
        
        break;

        case 2:

            // Exclusion rules setting active for current page, so don't display icons
            if(false != sfsi_plus_onhover_icon_exclude($location,$is_archive,$is_date,$is_author,$option8) ){
                $shallDisplayIcons =  false;
            }
            else{
                $shallDisplayIcons =  true;
            }

        break;  
    }

    // $shallDisplayIcons = $shallDisplayIcons && sfsi_is_icons_showing_on_front() && false!= License_Manager::validate_license();
    // (sfsi_plus_onhover_icon_exclude($location));
    return $shallDisplayIcons;
}