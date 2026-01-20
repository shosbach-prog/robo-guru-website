<?php

class sfsiFacebookSocialHelper{

    private $url,$timeout=90;

    public function __construct(){

    }

    ////////////////////////////// HELPERS :Fb cached count  functions STARTS ///////////////

    private function sfsi_parse_fb_api_response($apiType,$apiresponseObj){

        $responseObj = new stdClass;

        $responseObj->url     = isset($apiresponseObj->id) && !empty($apiresponseObj->id) ? $apiresponseObj->id : '';
        $responseObj->c       = 0; // $responseObj->c represent count
        //$responseObj->og_object = isset($apiresponseObj->og_object) && !empty($apiresponseObj->og_object) ? (is_object($apiresponseObj->og_object) ? $apiresponseObj->og_object->id: $apiresponseObj->og_object['id']) : '';
        $responseObj->share_count = 0;
        $responseObj->reaction_count = 0;
        $responseObj->comment_count = 0;
        $responseObj->comment_plugin_count = 0;

        if ( isset($apiresponseObj->engagement ) ) {

//            $apiresponseObj->og_object = is_object($apiresponseObj->og_object) ? $apiresponseObj->og_object: (object) $apiresponseObj->og_object;
            $responseObj->share_count = $apiresponseObj->engagement->share_count;
            $responseObj->reaction_count = $apiresponseObj->engagement->reaction_count;
            $responseObj->comment_count = $apiresponseObj->engagement->comment_count;
            $responseObj->comment_plugin_count = $apiresponseObj->engagement->comment_plugin_count;

            $responseObj->c = $apiresponseObj->engagement->reaction_count + $apiresponseObj->engagement->comment_count +
                $apiresponseObj->engagement->share_count +
                $apiresponseObj->engagement->comment_plugin_count;
        }

        return $responseObj;
    }

    public function sfsi_get_all_siteurls($arrPostids=false){
        $arrUrl = array_unique(sfsi_premium_get_all_site_urls($arrPostids));
        $option5 = maybe_unserialize(get_option('sfsi_premium_section5_options',false));
        $arrUrl=array_map(function($url){
            if(1===preg_match("/\w+\/[a-zA-Z-_.]+\.\w+\/$/",$url)){
                $url=rtrim($url,'/');
            }
            return $url;

        },$arrUrl);
        if($this->sfsi_isfbCumulationCountOn() && !empty($arrUrl) ){

            $arrCumulativeUrls = array();

            foreach ($arrUrl as $key => $url):
                if(1===preg_match("/\.\w+\\$/",$url)){
                    $url=rtrim($url,'/');
                }
                if("no"==$option5['sfsi_plus_http_cumulative_count_active']){
                    $httpUrl   = str_replace(strtolower($option5['sfsi_plus_http_cumulative_count_new_domain']), strtolower($option5['sfsi_plus_http_cumulative_count_previous_domain']) , strtolower($url) );
                    $httpsUrl = $url;

                }else{
                    if("https" == parse_url($url, PHP_URL_SCHEME)){
                        $httpsUrl = $url;
                        $httpUrl  = preg_replace("/^https:/i", "http:", $url);

                    }else{
                        $httpUrl = $url;
                        $httpsUrl  = preg_replace("/^http:/i", "https:", $url);

                    }
                }

                array_push($arrCumulativeUrls,$httpUrl,$httpsUrl);

                // $httpUrl   = $url;
                // $httpsUrl  = preg_replace("/^http:/i", "https:", $url);
                //array_push($arrCumulativeUrls,$httpUrl,$httpsUrl);

            endforeach;

            $arrCumulativeUrls = empty($arrCumulativeUrls) ? $arrUrl : $arrCumulativeUrls;

            return $arrCumulativeUrls;

        }

        return $arrUrl;
    }

    ////////////////////////////// HELPERS:Fb cached count  functions CLOSES ///////////////

    ////////////////////////////// MODELS : Fb cached count  functions STARTS ///////////////

    public function sfsi_isFbCachingActive($option4=false){

        $isFbCachingActive  = false;

        $option4      =  (false != $option4 && is_array($option4)) ? $option4 : maybe_unserialize(get_option('sfsi_premium_section4_options',false));

        $option1      =  maybe_unserialize(get_option('sfsi_premium_section1_options',false));

        if(isset($option1['sfsi_plus_facebook_display']) && !empty($option1['sfsi_plus_facebook_display'])
            && isset($option4['sfsi_plus_display_counts']) && !empty($option4['sfsi_plus_display_counts'])
            && isset($option4['sfsi_plus_facebook_countsDisplay']) && !empty($option4['sfsi_plus_facebook_countsDisplay'])
            && "yes" == $option1['sfsi_plus_facebook_display'] && "yes" == $option4['sfsi_plus_display_counts'] && "yes" == $option4['sfsi_plus_facebook_countsDisplay']){

            $isFbCachingActive  = (isset($option4['sfsi_plus_fb_count_caching_active']) && !empty($option4['sfsi_plus_fb_count_caching_active']))? $option4['sfsi_plus_fb_count_caching_active']: 'no';

            $isFbCachingActive =  "yes" == strtolower($isFbCachingActive) ? true : false;

        }

        return $isFbCachingActive;
    }

    public function sfsi_get_fb_access_token($option4=false){

        $access_token = '';

        $option4      =  (false != $option4 && is_array($option4)) ? $option4 : maybe_unserialize(get_option('sfsi_premium_section4_options',false)) ;

        $appid        = (isset($option4['sfsi_plus_facebook_appid']) && !empty($option4['sfsi_plus_facebook_appid']))? $option4['sfsi_plus_facebook_appid']: '714218973853876';

        $appsecret    = (isset($option4['sfsi_plus_facebook_appsecret']) && !empty($option4['sfsi_plus_facebook_appsecret']))? $option4['sfsi_plus_facebook_appsecret']: '51f244f4fac8ed48d660dd68aa43acd2';

        $access_token= $appid.'|'.$appsecret;

        return $access_token;
    }

    public function sfsi_isfbCumulationCountOn(){

        $isfbCumulationCountOn = false;

        $option5    = maybe_unserialize(get_option('sfsi_premium_section5_options',false));
        if(isset($option5['sfsi_plus_cumulative_count_active']) && "yes"==$option5['sfsi_plus_cumulative_count_active'] && isset($option5['sfsi_plus_facebook_cumulative_count_active'])

            && !empty($option5['sfsi_plus_facebook_cumulative_count_active'])

            && $option5['sfsi_plus_facebook_cumulative_count_active']=="yes"){

            $isfbCumulationCountOn = true;


            //return $isfbCumulationCountOn;
            if(isset($option5['sfsi_plus_http_cumulative_count_active'])&&'yes'==$option5['sfsi_plus_http_cumulative_count_active']){
                return $isfbCumulationCountOn && is_ssl();
            }else{
                return $isfbCumulationCountOn;
            }
        }
        return false;
    }

    public function sfsi_get_fb_caching_interval($option4=false){

        $caching_interval = 1;

        $option4      =  (false != $option4 && is_array($option4)) ? $option4 : maybe_unserialize(get_option('sfsi_premium_section4_options',false)) ;

        if($this->sfsi_isFbCachingActive($option4) && isset($option4['sfsi_plus_fb_caching_interval']) && !empty($option4['sfsi_plus_fb_caching_interval'])){

            $caching_interval  = $option4['sfsi_plus_fb_caching_interval'];

        }

        return $caching_interval;
    }

    public function sfsi_get_fb_api_last_call_log(){

        $data           = get_option('sfsi_premium_fb_batch_api_last_call_log',false);

        $arrApiCallData = isset($data) && !empty($data) && is_string($data) ? (object) maybe_unserialize($data) : false;

        return $arrApiCallData;
    }

    private function sfsi_update_fb_api_call_log($count){

        $arrApiCallData = $this->sfsi_get_fb_api_last_call_log();

        $fbApiCounter   = isset($count)?$count:99;

        if(isset($arrApiCallData) && !empty($arrApiCallData) && isset($arrApiCallData->apicount) && !empty($arrApiCallData->apicount)){

            $fbApiCounter = $arrApiCallData->apicount + $fbApiCounter;
        }

        $apidata = array(
            "apicount"    => $fbApiCounter,
            "lastapicall" => time(),
            "last_95plus_time"=>(isset($arrApiCallData->last_95plus_time)?$arrApiCallData->last_95plus_time:null)
        );
        update_option('sfsi_premium_fb_batch_api_last_call_log',serialize($apidata));
    }

    public function sfsi_get_cached_data_fbcount($isfbCumulationCountOn=null){

        $arrResult = array();

        if(null === $isfbCumulationCountOn){
            $isfbCumulationCountOn = $this->sfsi_isfbCumulationCountOn();
        }

        $key  = false === $isfbCumulationCountOn ?
            'sfsi_premium_fb_uncumulative_cached_count_'.home_url():
            'sfsi_premium_fb_cumulative_cached_count_'.home_url();

        $jsonData = get_option($key,false);
        if(false != $jsonData):

            $arrFbCount = json_decode($jsonData,true);
            if(function_exists('json_last_error')):
                if (JSON_ERROR_NONE === json_last_error()):
                    $arrResult = $arrFbCount;
                else:
                    $this->sfsi_add_api_error_log(json_last_error());
                endif;

            else:
                if(is_array($arrFbCount)){
                    $arrResult = $arrFbCount;
                }
            endif;

        endif;
        return $arrResult;
    }

    public function sfsi_update_cached_data_fbcount($arrData,$dbKey=false,$isfbCumulationCountOn=null){

        if(false == $dbKey){

            if(null === $isfbCumulationCountOn){
                $isfbCumulationCountOn = $this->sfsi_isfbCumulationCountOn();
            }

            $dbKey  = false === $isfbCumulationCountOn ?
                'sfsi_premium_fb_uncumulative_cached_count_'.home_url():
                'sfsi_premium_fb_cumulative_cached_count_'.home_url();
        }
        if(isset($arrData) && !empty($arrData) && is_array($arrData)){
            update_option($dbKey,utf8_encode(json_encode($arrData)));
        }
    }

    private function sfsi_save_multiple_url_facebook_count_for_caching($apiType,$arrJsonResponse){

        if(isset($arrJsonResponse) && !empty($arrJsonResponse) && is_array($arrJsonResponse)){

            $arrFinalResponse = array();

            foreach($arrJsonResponse as $json_response):

                if(isset($json_response) && !empty($json_response)){
                    if(!is_string($json_response)){
                        $json_response = json_encode($json_response);
                    }
                    $responseArr      = json_decode($json_response,true);
                    $arrFinalResponse = array_merge($arrFinalResponse,$responseArr);

                }

            endforeach;

            $this->sfsi_process_facebook_count_for_caching($apiType,$arrFinalResponse,true);
        }
    }

    public function sfsi_process_fbcount_data_to_add_in_final_arr($url=null,$count=null,$arrDbFbCachedCount=null,$postId=null){
        if(is_null($postId)){
            $postId  = sfsi_premium_url_to_postid($url);
            $postId  = isset($postId) && is_numeric($postId) && $postId>0 ? $postId: -1;
        }
        $arrDbPostIds = isset($arrDbFbCachedCount) && !empty($arrDbFbCachedCount) && is_array($arrDbFbCachedCount) ? (SFSI_PHP_VERSION_7 ? sfsi_premium_array_column($arrDbFbCachedCount,"i") :array_column($arrDbFbCachedCount,"i")) : array();
        $dbIndex = null;

        if(isset($arrDbPostIds) && !empty($arrDbPostIds)){

            $dbIndex = array_search($postId,$arrDbPostIds);

            $dbIndex = false === $dbIndex ? null : $dbIndex;
            if($dbIndex > 0 || $dbIndex === 0){
                $oldcount = $arrDbFbCachedCount[$dbIndex]['c'];
                $count->c = max($count->c, $oldcount);
                $arrDbFbCachedCount[$dbIndex]['i'] = $postId;
                $arrDbFbCachedCount[$dbIndex]['c'] = $count->c;
                $arrDbFbCachedCount[$dbIndex]['share_count'] = $count->share_count;
                $arrDbFbCachedCount[$dbIndex]['comment_count'] = $count->comment_count;
                $arrDbFbCachedCount[$dbIndex]['comment_plugin_count'] = $count->comment_plugin_count;
            }
        }
        // var_dump($arrDbPostIds,$postId,$arrDbFbCachedCount,)
        if(is_null($dbIndex)){
            $arrCountData      = array();
            $arrCountData['i'] = $postId;
            $arrCountData['c'] = $count->c;
            $arrDbFbCachedCount[$dbIndex]['share_count'] = $count->share_count;
            $arrDbFbCachedCount[$dbIndex]['comment_count'] = $count->comment_count;
            $arrDbFbCachedCount[$dbIndex]['comment_plugin_count'] = $count->comment_plugin_count;
            array_push($arrDbFbCachedCount, $arrCountData);
        }

        return $arrDbFbCachedCount;
    }

    public function sfsi_process_facebook_count_for_caching($apiType,$json_response,$isResponseArr=false)
    {
        $arrDbFbCachedCount = false;

        if($isResponseArr) {
            $responseArr  = $json_response;
        } else {
            $responseArr  = isset($json_response) && !empty($json_response) ? json_decode($json_response,true) :  array();
        }
        if(isset($responseArr) && !empty($responseArr)):

            $isfbCumulationCountOn = $this->sfsi_isfbCumulationCountOn();

            $arrDbFbCachedCount    = $this->sfsi_get_cached_data_fbcount();

            $option5    = maybe_unserialize(get_option('sfsi_premium_section5_options',false));

            foreach ($responseArr as $url => $singleRespArr):
                $singleRespObj = sfsi_premium_arrayToObject($singleRespArr);
                // var_dump($singleRespObj);
                if(!isset($singleRespObj->error)):
                    // var_dump("isfbCumulationCountOn",$isfbCumulationCountOn);
                    if(false != $isfbCumulationCountOn){
                        if(isset($option5["sfsi_plus_http_cumulative_count_active"])&& ("yes"==$option5["sfsi_plus_http_cumulative_count_active"])){
                            if("http" == parse_url($url, PHP_URL_SCHEME)){
                                $httpsUrl = preg_replace("/^http:/i", "https:", $url);
                            }else{
                                $httpsUrl = preg_replace("/^https:/i", "http:", $url);
                            }
                            if(isset($option5["sfsi_plus_counts_without_slash"])&& ("yes"==$option5["sfsi_plus_counts_without_slash"])){
                                if(rtrim( $httpsUrl,'/')==$httpsUrl){
                                    $httpsUrlWSlash = trailingslashit( $httpsUrl);
                                    $httpUrlWSlash = trailingslashit( $url);
                                }else{
                                    $httpsUrlWSlash = rtrim( $httpsUrl , '/');
                                    $httpUrlWSlash = rtrim( $url , '/');
                                }
                            }
                        }elseif(isset($option5["sfsi_plus_http_cumulative_count_active"])&& ("no"==$option5["sfsi_plus_http_cumulative_count_active"])){
                            $httpsUrl   = str_replace(strtolower($option5['sfsi_plus_http_cumulative_count_new_domain']), strtolower($option5['sfsi_plus_http_cumulative_count_previous_domain']) , strtolower($url) );
                        }else{
                            continue;
                        }
                        // Count for http url
                        $httpUrlCountDataObj  = sfsi_premium_arrayToObject($responseArr[$url]);
                        $data = array($httpUrlCountDataObj);
                        if(isset($responseArr[$httpsUrl]))
                        {
                            $httpsUrlCountDataObj = sfsi_premium_arrayToObject($responseArr[$httpsUrl]);
                            $data                 = array($httpUrlCountDataObj,$httpsUrlCountDataObj);
                        }

                        // Count for https url without slash
                        if(isset($responseArr[$httpsUrlWSlash]))
                        {
                            $httpsUrlWSlashCountDataObj = sfsi_premium_arrayToObject($responseArr[$httpsUrlWSlash]);
                            array_push($data,$httpsUrlWSlashCountDataObj);
                        }
                        // Count for http url without slash
                        if(isset($responseArr[$httpUrlWSlash]))
                        {
                            $httpsUrlWSlashCountDataObj = sfsi_premium_arrayToObject($responseArr[$httpUrlWSlash]);
                            array_push($data,$httpsUrlWSlashCountDataObj);
                        }
                        // var_dump("$data2",$data);die();
                        $arrResp = array(
                            "api" => $apiType,
                            "data"=> $data
                        );
                        // var_dump($data);
                        $cumulativeObj        = new sfsiCumulativeCount($url,$httpsUrl);
                        $count                = $cumulativeObj->sfsi_count_cumulative($arrResp);
                        // var_dump($count);

                        if(0 != $count){

                            $arrDbFbCachedCount = $this->sfsi_process_fbcount_data_to_add_in_final_arr($httpsUrl,$count,$arrDbFbCachedCount);

                        }


                    }else{

                        $objUnCumulative     = $this->sfsi_parse_fb_api_response($apiType,$singleRespObj);
                        // var_dump(array('objUnCumulative',$objUnCumulative) );
                        // $post_id = sfsi_premium_url_to_postid($objUnCumulative->url);
                        // echo "<pre>";
                        // var_dump(array($post_id,$objUnCumulative,false != $objUnCumulative ,is_object($objUnCumulative) , 0 != $objUnCumulative->c),$arrDbFbCachedCount);
                        // $stored_count = null;
                        // foreach($arrDbFbCachedCount as $index=>$stored){
                        //   if($stored["i"]==$post_id){
                        //     $stored_count = (object)$stored;
                        //     break;
                        //   }
                        // }
                        // var_dump($stored_count,);
                        // echo "</pre>";

                        if(false != $objUnCumulative && is_object($objUnCumulative) && 0 != $objUnCumulative->c ){
                            $arrDbFbCachedCount = $this->sfsi_process_fbcount_data_to_add_in_final_arr($objUnCumulative->url,$objUnCumulative,$arrDbFbCachedCount);
                        }
                    }

                endif;

            endforeach;
        endif;
        // var_dump($arrDbFbCachedCount);
        if(isset($arrDbFbCachedCount) && !empty($arrDbFbCachedCount) && is_array($arrDbFbCachedCount) ){

            $this->sfsi_update_cached_data_fbcount($arrDbFbCachedCount);
        }
    }

    /*
      Parameters: (3) -> (int) $postId Post ID.Required: Yes
      Returns:    -> (int) On success will return cached fb count Default: 0
    */

    public function sfsi_get_cached_fbcount_for_postId($postId){

        $count = [];
        if(isset($postId)){

            $arrFbCachedCount = $this->sfsi_get_cached_data_fbcount();
            if(isset($arrFbCachedCount) && !empty($arrFbCachedCount))
            {
                $arrFbCachedPostIds = (SFSI_PHP_VERSION_7?sfsi_premium_array_column($arrFbCachedCount,"i"):array_column($arrFbCachedCount,"i"));
                $key   = array_search($postId,$arrFbCachedPostIds);

                if(false !== $key){
                    //get all items
                    $count['c'] = isset($arrFbCachedCount[$key]['c']) ? $arrFbCachedCount[$key]['c'] : 0;
                    $count['share_count'] = isset($arrFbCachedCount[$key]['share_count']) ?  $arrFbCachedCount[$key]['share_count'] : 0;
                    $count['reaction_count'] = isset($arrFbCachedCount[$key]['reaction_count']) ? $arrFbCachedCount[$key]['reaction_count'] : 0;
                    $count['comment_count'] = isset($arrFbCachedCount[$key]['comment_count']) ?  $arrFbCachedCount[$key]['comment_count'] : 0;
                    $count['comment_plugin_count'] =isset($arrFbCachedCount[$key]['comment_plugin_count']) ?  $arrFbCachedCount[$key]['comment_plugin_count'] : 0;
                }
            }
        }

        return $count;
    }

    private function sfsi_add_api_error_log($message){

        $jsonApiIssues = get_option('sfsi_premium_fb_batch_api_issue',false);
        $arrApiIssues  = array();

        if(false != $jsonApiIssues){
            $arrDbApiIssues = json_decode($jsonApiIssues,true);

            $c = count($arrDbApiIssues);
            $arrApiIssues=array_slice($arrDbApiIssues, -9, 9, true);
        }

        $arrErrData = array(
            "time"    => time(),
            "message" => $message
        );

        array_push($arrApiIssues,$arrErrData);
        update_option('sfsi_premium_fb_batch_api_issue',json_encode($arrApiIssues));
    }
    /////////////////////////////////////// CONTROLLERS :Fb cached count model functions CLOSES /////////////////////////////

    public function sfsi_shall_call_fbcount_batch_api(){
        $shallCallFbCountApi = false;
        if(false != $this->sfsi_isFbCachingActive()):
            $arrApiCallData = $this->sfsi_get_fb_api_last_call_log();
            $lastapicallTimestamp = isset($arrApiCallData->lastapicall) && !empty($arrApiCallData->lastapicall) ? $arrApiCallData->lastapicall : false;
            $last_95plus_time = isset($arrApiCallData->last_95plus_time) && !empty($arrApiCallData->last_95plus_time) ? $arrApiCallData->last_95plus_time : false;
            if(false == $lastapicallTimestamp){
                $shallCallFbCountApi = true;
            }
            else{
                $setInterval = $this->sfsi_get_fb_caching_interval();
                $setInterval = isset($setInterval) && !empty($setInterval) ? $setInterval: 1;

                $caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options',"a:0:{}"));
                if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                    if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                        $setInterval = 0;
                    }
                }
                $diff   = (time() - $lastapicallTimestamp)/ 3600;   // 1 hr
                $shallCallFbCountApi = ($diff >= $setInterval) ? true :false;
                if(false!==$last_95plus_time){
                    $shallCallFbCountApi = $shallCallFbCountApi && (time() - $last_95plus_time > 3600);
                }
            }
        endif;
        return $shallCallFbCountApi;
    }

    public function sfsi_fbcount_inbatch_api(){
        try {
            $caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options',"a:0:{}"));
            if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                    var_dump("shall_call_fbcount",$this->sfsi_shall_call_fbcount_batch_api());
                }
            }
            if(false != $this->sfsi_shall_call_fbcount_batch_api()):
                $arrAllPostIds  = sfsi_premium_get_all_site_postids();

                $access_token   = $this->sfsi_get_fb_access_token();
                $sfsi_job_queue = sfsiJobQueue::getInstance();
                // Call for remaining urls for pending api calls
                $arrPendingJobs = $sfsi_job_queue->get_pending_jobs();
                $caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options',"a:0:{}"));
                if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                    if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                        var_dump($arrPendingJobs,isset($arrPendingJobs) && !empty($arrPendingJobs));
                    }
                }
                if(isset($arrPendingJobs) && !empty($arrPendingJobs)){
                    $getTopJob = $arrPendingJobs[0];
                    if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                        if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                            var_dump("picking from pending job",$getTopJob);
                        }
                    }
                    // var_dump(isset($getTopJob) , !empty($getTopJob) , false == $getTopJob->status);die();
                    if(isset($getTopJob) && !empty($getTopJob) && false == $getTopJob->status):
                        $jobInterval = $this->sfsi_get_fb_caching_interval();
                        $jobInterval = isset($jobInterval) && !empty($jobInterval) ? $jobInterval: 1;
                        $postsId_between_interval = sfsi_premium_last_x_hr_postids(2*$jobInterval);
                        $arrPostids = json_decode($getTopJob->urls,true);

                        if( function_exists('json_last_error')){

                            if(JSON_ERROR_NONE === json_last_error()):
                                if(filter_var($arrPostids[0], FILTER_VALIDATE_URL)){

                                    $arrUrls =   array_merge($this->sfsi_get_all_siteurls($postsId_between_interval),$arrPostids);
                                }else{
                                    $arrUrls = $this->sfsi_get_all_siteurls(array_merge($postsId_between_interval,$arrPostids));
                                }
                                // For backward compatibility as urls were saved in job data instead of postids
                                $jobId   = $getTopJob->id;
                                $this->sfsi_fbcount_multiple_batch_api($jobId,$arrUrls,$access_token);
                            else:
                                $this->sfsi_add_api_error_log(json_last_error());
                            endif;
                        }else{
                            // For backward compatibility as urls were saved in job data instead of postids
                            $arrUrls = filter_var($arrPostids[0], FILTER_VALIDATE_URL) ? $arrPostids: $this->sfsi_get_all_siteurls($arrPostids);
                            $jobId   = $getTopJob->id;
                            $this->sfsi_fbcount_multiple_batch_api($jobId,$arrUrls,$access_token);
                        }
                    endif;
                }else if(isset($arrAllPostIds) && !empty($arrAllPostIds)):
                    // $chunkByCounter = $this->sfsi_isfbCumulationCountOn() ? 2475 : 4950;
                    $chunkByCounter = 200;
                    $postCount      = count($arrAllPostIds);
                    $arrAllUrls  = $this->sfsi_get_all_siteurls($arrAllPostIds);
                    if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                        if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                            var_dump("$postCount :".$postCount);
                        }
                    }
                    if( count($arrAllUrls) > 50 ) {

                        if($postCount > $chunkByCounter):
                            //call api for first 4950 urls & put others in queue to be called in next hour
                            $arrChunked     =  array_chunk($arrAllPostIds, $chunkByCounter);
                            // Add remmaining job with not started status
                            $arrJobIds = $sfsi_job_queue->add_multiple_jobs(1,$arrChunked);
                            $arrPendingJobs = $sfsi_job_queue->get_pending_jobs();
                            if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                                if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                                    var_dump("created ".count($arrJobIds)." the jobs and picking first one:",$arrJobIds);
                                }
                            }
                            if(!empty($arrJobIds)):
                                $jobId        = $arrJobIds[0];
                                $arrFjPostids = $arrChunked[0];
                                $arrFJUrls = $this->sfsi_get_all_siteurls($arrFjPostids);
                                $sfsi_job_queue->job_start($jobId);
                                $this->sfsi_fbcount_multiple_batch_api($jobId,$arrFJUrls,$access_token);
                            endif;
                        else:
                            // Create job
                            $arrAllUrls  = $this->sfsi_get_all_siteurls($arrAllPostIds);
                            $jsonPostIds = json_encode($arrAllPostIds);
                            if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                                if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                                    var_dump("created single job the urls :");
                                }
                            }
                            if( function_exists('json_last_error')){
                                if (JSON_ERROR_NONE === json_last_error()){
                                    $jobId = $sfsi_job_queue->add_single_job(1,$jsonPostIds);
                                    if(isset($jobId) && !empty($jobId)):
                                        $sfsi_job_queue->job_start($jobId);
                                        $this->sfsi_fbcount_multiple_batch_api($jobId,$arrAllUrls,$access_token);

                                    endif;
                                }else{
                                    $this->sfsi_add_api_error_log(json_last_error());
                                }
                            }else{
                                $jobId = $sfsi_job_queue->add_single_job(1,$jsonPostIds);
                                if(isset($jobId) && !empty($jobId)){
                                    $sfsi_job_queue->job_start($jobId);
                                    $this->sfsi_fbcount_multiple_batch_api($jobId,$arrAllUrls,$access_token);
                                }
                            }
                        endif;
                    }
                    else{
                        $arrAllUrls  = $this->sfsi_get_all_siteurls($arrAllPostIds);
                        if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                            if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                                var_dump("total post less than 50 so going for single batch",$arrAllUrls);
                            }
                        }
                        $this->sfsi_fbcount_single_batch_api($arrAllUrls,$access_token);
                    }
                endif;
            endif;
        }catch(Exception $e) {
            $this->sfsi_add_api_error_log($e->getMessage());
        }

    }

    public function sfsi_get_api_url_array_multiple_batch_api($arrUrl,$access_token){

        $arrUrl = array_chunk($arrUrl, 50);

        $arrApiUrl =  array();

        foreach($arrUrl as $arrData):

            $arrJsonn    = json_encode($arrData);
            $apiUrl      = 'https://graph.facebook.com/v18.0/?ids='.$arrJsonn.'&fields=engagement&access_token='.$access_token;

            array_push($arrApiUrl,$apiUrl);

        endforeach;

        return $arrApiUrl;
    }

    public function sfsi_fbcount_multiple_batch_api($jobId,$arrUrl,$access_token){
        $arrApiUrl = $this->sfsi_get_api_url_array_multiple_batch_api($arrUrl,$access_token);
        if(!empty($arrApiUrl)){
            // Calling api
            $caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options',"a:0:{}"));

            $sfsiCumulativeCount = new sfsiCumulativeCount();
            $resp = $sfsiCumulativeCount->sfsi_get_multi_curl($arrApiUrl,array(),true);
            if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                    // var_dump("multiple response", $resp);
                }
            }
            if(isset($resp) && !empty($resp)){
                $sfsi_job_queue = sfsiJobQueue::getInstance();
                // Update call log, last call time & increase counter
                $url_count = isset($arrUrl)&&is_array($arrUrl)?count($arrUrl):99;
                if(is_string($resp[0])){
                    $respObj  = json_decode($resp[0]);
                }else{
                    $respObj  = $resp[0];
                }
                // $respObjLast = json_decode($resp[count($resp)-1]);
                if(!isset($respObj->error)){
                    $sfsi_job_queue->remove_finished_job($jobId);
                    $this->sfsi_update_fb_api_call_log($url_count);
                    $this->sfsi_save_multiple_url_facebook_count_for_caching("app29",$resp);
                }
                else{
                    $sfsi_premium_fb_batch_api_last_call_log = get_option('sfsi_premium_fb_batch_api_last_call_log',false);

                    if(gettype($sfsi_premium_fb_batch_api_last_call_log)=="string"){
                        $sfsi_premium_fb_batch_api_last_call_log = maybe_unserialize($sfsi_premium_fb_batch_api_last_call_log);
                    }
                    if(gettype($sfsi_premium_fb_batch_api_last_call_log)=="array"){
                        $sfsi_premium_fb_batch_api_last_call_log["last_95plus_time"] = time();
                        update_option('sfsi_premium_fb_batch_api_last_call_log',serialize($sfsi_premium_fb_batch_api_last_call_log));
                    }
                    $this->sfsi_add_api_error_log($respObj->error->message);
                }
            }
        }
    }

    public function sfsi_fbcount_single_batch_api($arrUrl,$access_token){

        $arrJson = json_encode($arrUrl);
        //  var_dump($arrJson );

        $apiUrl  = 'https://graph.facebook.com/v18.0/?ids='.$arrJson.'&fields=engagement&access_token='.$access_token;

        $request  = wp_remote_get( $apiUrl );

        if(!is_wp_error($request)){
            $usage = @json_decode(wp_remote_retrieve_header($request,"x-app-usage"));
            if(isset($usage) && isset($usage->call_count) && $usage->call_count>95 && isset($usage->total_time) && $usage->total_time>95 && isset($usage->total_cputime) && $usage->total_cputime>95 ){
                $sfsi_premium_fb_batch_api_last_call_log = get_option('sfsi_premium_fb_batch_api_last_call_log',false);
                $sfsi_premium_fb_batch_api_last_call_log["last_95plus_time"] = time();
                $sfsi_premium_fb_batch_api_last_call_log = update_option('sfsi_premium_fb_batch_api_last_call_log',$sfsi_premium_fb_batch_api_last_call_log);
            }
            $response = wp_remote_retrieve_body( $request );
            $caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options',"a:0:{}"));
            if(isset($caching_debug_option["on"]) && $caching_debug_option["on"]=="yes"){
                if(isset($caching_debug_option["for"]) && $caching_debug_option["for"]===sfsi_premium_get_client_ip()){
                    // var_dump("single response", $response,"status code",  wp_remote_retrieve_response_code($request));
                }
            }
            if (200 == wp_remote_retrieve_response_code($request)):

                $this->sfsi_process_facebook_count_for_caching("app29",$response);

                update_option('sfsi_premium_fb_batch_api_issue','');

            endif;

            // Update call log, last call time & increase counter
            $url_count = is_array($arrUrl)?count($arrUrl):99;

            $this->sfsi_update_fb_api_call_log($url_count);
        }else{
            $error = $request->get_error_message();
        }

    }

    public function sfsi_get_uncachedfbcount($url){

        $count = 0;

        if($this->sfsi_isfbCumulationCountOn()):

            $count = $this->sfsi_get_uncached_cumulative_fb($url);

        else:

            $count = $this->sfsi_get_uncached_uncumulative_fb($url);

        endif;

        return $count;
    }

    /* get facebook likes */
    public function sfsi_get_uncached_cumulative_fb($url){

        $decoded_url = urldecode($url);
        $option5     = maybe_unserialize(get_option('sfsi_premium_section5_options',false));

        if (strpos($decoded_url, '?') !== false && substr($decoded_url, -1) ==="/") {
            $decoded_url = substr($decoded_url, 0, -1);
        }
        if('no'==$option5['sfsi_plus_http_cumulative_count_active']){
            $httpUrl   = urlencode(str_replace(strtolower($option5['sfsi_plus_http_cumulative_count_new_domain']), strtolower($option5['sfsi_plus_http_cumulative_count_previous_domain']) , strtolower($decoded_url) ));
        }else{
            $httpUrl        = urlencode(preg_replace("/^https:/i", "http:", $decoded_url));
        }
        $httpsUrl       = urlencode($decoded_url);

        $access_token   = $this->sfsi_get_fb_access_token();

        $objCumulative  = new sfsiCumulativeCount($httpUrl,$httpsUrl,$access_token);

        $response_arr = $objCumulative->sfsi_fb_api("app29");
        if(empty($response_arr)){

            $json_string = $this->file_get_contents_curl('https://graph.facebook.com/?ids='.json_encode(array($httpUrl,$httpsUrl)));
            $jsonMulti   = json_decode($json_string, true);
            $count       = 0;

            if(isset($jsonMulti) && !empty($jsonMulti)){

                foreach($jsonMulti as $url=> $json){

                    $count['c']  += (isset($json['engagement'])?($json['engagement']['share_count']+$json['engagement']['comment_count']+$json['engagement']['reaction_count']+$json['engagement']['comment_plugin_count']):0);
                    $count['share_count'] = isset($json['engagement']['share_count']) ? $json['engagement']['share_count'] : 0 ;
                    $count['comment_count'] = isset($json['engagement']['comment_count']) ? $json['engagement']['comment_count'] : 0 ;
                    $count['reaction_count'] = isset($json['engagement']['reaction_count']) ? $json['engagement']['reaction_count'] : 0 ;
                    $count['comment_plugin_count'] = isset($json['engagement']['comment_plugin_count']) ? $json['engagement']['comment_plugin_count'] : 0 ;
                }

            }
            return $count;
        }
        return $objCumulative->sfsi_count_cumulative($response_arr);
    }


    public function sfsi_get_uncached_uncumulative_fb($url){
        $url   = trailingslashit($url);
        $count = [];

        $access_token   = $this->sfsi_get_fb_access_token();

        $json_30_string = $this->file_get_contents_curl('https://graph.facebook.com/v18.0/?id='.$url.'&fields=engagement&access_token='.$access_token);

        if(empty($json_30_string)){
            $json_30_string = $this->file_get_contents_curl('https://graph.facebook.com/?id='.$url);
        }

        $json   = json_decode(mb_convert_encoding( $json_30_string,'utf8'), true);
        if (isset($json['engagement'])) {
            $count['share_count'] = isset($json['engagement']['share_count']) ? $json['engagement']['share_count'] : 0 ;
            $count['comment_count'] = isset($json['engagement']['comment_count']) ? $json['engagement']['comment_count'] : 0 ;
            $count['reaction_count'] = isset($json['engagement']['reaction_count']) ? $json['engagement']['reaction_count'] : 0 ;
            $count['comment_plugin_count'] = isset($json['engagement']['comment_plugin_count']) ? $json['engagement']['comment_plugin_count'] : 0 ;
            $count['c'] = $count['share_count'] + $count['comment_count'] + $count['reaction_count']  + $count['comment_plugin_count'];
//            $count = $json['engagement']['reaction_count'];
        }

        return $count;
    }

    /////////////////////////////////////// Fb cached count getters  CLOSES  /////////////////////////////

    /* send curl request   */
    private function file_get_contents_curl($url)
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
            'user-agent'  =>  $_SERVER['HTTP_USER_AGENT'],
            'timeout'     =>  $this->timeout,
            'sslverify'   =>  false,
            'blocking'    =>  true,
            'redirection' =>  1,
        ));

        if (is_wp_error($curl)) {

            $curl->get_error_message();

        } else {

            $status = wp_remote_retrieve_response_code($curl);
            $body = $curl['body'];

            if (intval($status) == 200) {
                set_transient($transientName, $body, intval(60*60*1.5));
            }

            return $body;

        }
    }

}
