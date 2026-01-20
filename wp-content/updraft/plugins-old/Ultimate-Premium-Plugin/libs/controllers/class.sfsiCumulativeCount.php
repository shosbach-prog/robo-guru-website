<?php
class sfsiCumulativeCount
{
   private $urlHttp;
   private $urlHttps;
   private $access_token;

   public function __construct($urlHttp=false,$urlHttps=false,$access_token='') {
        $this->urlHttp      = $urlHttp;
        $this->urlHttps     = $urlHttps;
        $this->access_token = $access_token;
    }
    
    public function sfsi_get_multi_requests($data, $options = array(),$outputTypeJson=false) {
     
      // array of request handles
      $curly = array();
      // data to be returned
      $result = array();
      // loop through $data and create curl handles
      // then add them to the multi-handle
      foreach ($data as $id => $d) {
        $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
        $curly[$id]=array();
        $curly[$id]["url"]=$url;

        // post?
        if (is_array($d)) {
          if (!empty($d['post'])) {
            $curly[$id]["type"]="POST";
            $curly[$id]["data"]=$d["post"];
          }
        }
      }
      $responses = \WpOrg\Requests\Requests::request_multiple($curly);
      foreach($responses as $index=>$response){
          if(is_wp_error($response)){
            $erroInfo      = array("Url" =>$data[$index], "error" => array());
              $erroInfo['error']['message'] =  $response->get_error_message();
              $result[$id]   = ($outputTypeJson) ? json_encode($erroInfo) : $erroInfo;
          }else{
            $responsedata = wp_remote_retrieve_body($response);
            $result[$id] = ($outputTypeJson) ? $responsedata : json_decode($responsedata);
          }
      }
      $usage = @json_decode(wp_remote_retrieve_header($request,"x-app-usage"));
       if(isset($usage) && isset($usage->call_count) && $usage->call_count>95 && isset($usage->total_time) && $usage->total_time>95 && isset($usage->total_cputime) && $usage->total_cputime>95 ){
          $sfsi_premium_fb_batch_api_last_call_log = $get_option('sfsi_premium_fb_batch_api_last_call_log',false);
          $sfsi_premium_fb_batch_api_last_call_log["last_95plus_time"] = time();
       }
      return $result;
    }

    public function sfsi_get_multi_curl($data, $options = array(),$outputTypeJson=false) {
        if(method_exists('\WpOrg\Requests\Requests','request_multiple')){
          $requests = array();
          foreach ($data as $id => $d) {
            $single_req = array();
            $single_req['url'] = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            if (is_array($d)) {
              if (!empty($d['post'])) {
                $single_req['type'] = "POST";
                $single_req['data'] = $d[post];  
              }else{
                $single_req['type'] = "GET";
              }
            }
            array_push($requests,$single_req);
          }
          $responses = \WpOrg\Requests\Requests::request_multiple($requests);
          $result = array();
          foreach($responses as $id=>$response){
            $data = array();
            if( is_a( $response, 'WpOrg\Requests\Response' ) ){
             $response_text = $response->body;
             $response_data = @json_decode( $response_text );
             if(is_null($response_data)){
               $response_data = $response_text;
             }
             array_push($result , $response_data);
            }else{
              // var_dump($response);
            }
          }
          return $result;
        // if(function_exists('curl_multi_select')){
        }elseif(function_exists('curl_multi_select')){
          
          // array of curl handles
          $curly = array();
          // data to be returned
          $result = array();
         
          // multi handle
          $mh = curl_multi_init();
          $headers = array();
          // loop through $data and create curl handles
          // then add them to the multi-handle
          foreach ($data as $id => $d) {
          
            $curly[$id] = curl_init();

            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
         
            curl_setopt($curly[$id], CURLOPT_URL,            $url);
            curl_setopt($curly[$id], CURLOPT_HEADER,         0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
         
            // post?
            if (is_array($d)) {
              if (!empty($d['post'])) {
                curl_setopt($curly[$id], CURLOPT_POST,       1);
                curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
              }
            }
         
            // extra options?
            if (!empty($options)) {
              curl_setopt_array($curly[$id], $options);
            }
            curl_setopt($curly[$id], CURLOPT_HEADERFUNCTION,
              function($curl, $header) use (&$headers)
              {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                  return $len;

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
              }
            );

            curl_multi_add_handle($mh, $curly[$id]);
          }
         
          // execute the handles
          $running = null;
          do {
            curl_multi_exec($mh, $running);
          } while($running > 0);
                  
          // get content and remove handles
          foreach($curly as $id => $c) {
            
            if(curl_errno($c)){
              $errno         = curl_errno($c);
              $error_message = curl_strerror($errno);
              $erroInfo      = array("Url" =>curl_getinfo($c, CURLINFO_EFFECTIVE_URL), "error" => curl_getinfo($c));
              $erroInfo['error']['message'] =  $error_message;
              $result[$id]   = ($outputTypeJson) ? json_encode($erroInfo) : $erroInfo;
            }
            else{
              $result[$id] = ($outputTypeJson) ? curl_multi_getcontent($c) : json_decode(curl_multi_getcontent($c));
            }

            curl_multi_remove_handle($mh, $c);
          }
          // all done
          curl_multi_close($mh);
          if(strpos($url,'facebook') !== false){
            $usage = @json_decode($headers["x-app-usage"]);
            if(isset($usage) && isset($usage->call_count) && $usage->call_count>95 && isset($usage->total_time) && $usage->total_time>95 && isset($usage->total_cputime) && $usage->total_cputime>95 ){
                $sfsi_premium_fb_batch_api_last_call_log = $get_option('sfsi_premium_fb_batch_api_last_call_log',false);
                $sfsi_premium_fb_batch_api_last_call_log["last_95plus_time"] = time();
            }
          }
          return $result;
       }

      return false;
    }

    /********************************** Methods to get facebook cumulatitve count STARTS **************************/

    public function sfsi_fb_api($apiType){

        $arrFbData = array();

        $apiUrl    = $this->sfsi_get_api_url($apiType);
        
        $request   = wp_remote_get($apiUrl);
        $response  = wp_remote_retrieve_body($request);

        if (200 == wp_remote_retrieve_response_code($request)){
          
          $arrFbData = array_values( json_decode($response,true) );
          $arrFbData = sfsi_premium_arrayToObject($arrFbData); 

        }

        return $arrFbData;
    }

    private function sfsi_get_api_url($apiType,$urlArr=false){

        $urlArr    = empty($urlArr) ? array($this->urlHttp,$this->urlHttps) : $urlArr;
        $arrJson   = json_encode($urlArr);

        $apiUrl    = 'https://graph.facebook.com/v18.0/?ids='.$arrJson.'&fields=engagement&access_token='.$this->access_token;

        return $apiUrl;
    }

    private function sfsi_fb_cumulative_api_version_29(){

        $apiType = "app29";
        
        $arrFbData = $this->sfsi_fb_api($this->sfsi_get_api_url($apiType));

        return $arrFbData;
    }

    private function sfsi_get_fb_count_api_data(){

        $arrResp = array();

        $arrApi29Data = $this->sfsi_fb_cumulative_api_version_29();

        if(false == $this->sfsi_is_error_key_exists($arrApi29Data) && false != $this->sfsi_is_share_key_exists($arrApi29Data)):

          $arrResp = array( "api"=>"app29","data"=> $arrApi29Data );
      
       endif;

       return $arrResp;
    }

    private function sfsi_is_share_key_exists($arrFbData){

        $check = false;

        if(!empty($arrFbData)){

            foreach ($arrFbData as $fbData) {
                
                if( isset($fbData->og_object->engagement->count) ) {
                    $check = true;
                    break;
                }
            }
        }

        return $check;
    }

    private function sfsi_is_error_key_exists($arrFbData){

        $check = false;

        if(!empty($arrFbData)){

            foreach ($arrFbData as $fbData) {
                
                if(isset($fbData->error)){
                    $check = true;
                    break;
                }
            }
        }

        return $check;
    }

    public function sfsi_count_cumulative($arrFbData = false){

        $count = 0;
        $arrFbData = (false == $arrFbData) ? $this->sfsi_get_fb_count_api_data() : $arrFbData;
    
        if(!empty($arrFbData)){
            if(is_array($arrFbData)&&isset($arrFbData['data'])){
              $arr = is_object($arrFbData['data']) ? array_values( (array) $arrFbData['data'] ) : $arrFbData['data'];
            }else{
              $arr = is_object($arrFbData) ? array_values( (array) $arrFbData) : $arrFbData;
            }

            $httpData  = isset($arr[0]) && !empty($arr[0]) ? $arr[0]: false;
            $httpsData = isset($arr[1]) && !empty($arr[1]) ? $arr[1]: false;
            $httpDataWSlash  = isset($arr[2]) && !empty($arr[2]) ? $arr[2]: false;
            $httpsDataWSlash = isset($arr[3]) && !empty($arr[3]) ? $arr[3]: false;
            if(isset( $httpData->engagement ) || isset( $httpsData->engagement )):

                if ($httpsData->engagement){
                    $httpsCount['reaction_count'] = isset( $httpsData->engagement->reaction_count) ? $httpsData->engagement->reaction_count : 0;
                    $httpsCount['comment_count'] = isset( $httpsData->engagement->comment_count) ? $httpsData->engagement->comment_count : 0;
                    $httpsCount['comment_plugin_count'] = isset( $httpsData->engagement->comment_plugin_count) ? $httpsData->engagement->comment_plugin_count : 0;
                    $httpsCount['share_count'] = isset( $httpsData->engagement->share_count) ? $httpsData->engagement->share_count : 0;
                    $httpsCount['c'] = $httpsCount['reaction_count'] +  $httpsCount['comment_count'] + $httpsCount['comment_plugin_count'] + $httpsCount['share_count'];
                    $count = $httpsCount;
                }

                if ($httpData->engagement) {
                    $httpCount['reaction_count'] = isset($httpData->engagement->reaction_count) ? $httpData->engagement->reaction_count : 0;
                    $httpCount['comment_count'] = isset($httpData->engagement->comment_count) ? $httpData->engagement->comment_count : 0;
                    $httpCount['comment_plugin_count'] = isset($httpData->engagement->comment_plugin_count) ? $httpData->engagement->comment_plugin_count : 0;
                    $httpCount['share_count'] = isset($httpData->engagement->share_count) ? $httpData->engagement->share_count : 0;
                    $httpCount['c'] = $httpCount['reaction_count'] + $httpCount['comment_count'] + $httpCount['comment_plugin_count'] + $httpCount['share_count'];
                    $count = $httpCount;
                }

                if( isset( $httpDataWSlash ) && false !== $httpDataWSlash ) {
                    $httpDataWSlashCount['reaction_count'] = isset( $httpData->engagement->reaction_count) ? $httpData->engagement->reaction_count : 0;
                    $httpDataWSlashCount['comment_count'] = isset( $httpData->engagement->comment_count) ? $httpData->engagement->comment_count : 0;
                    $httpDataWSlashCount['comment_plugin_count'] = isset( $httpData->engagement->comment_plugin_count) ? $httpData->engagement->comment_plugin_count : 0;
                    $httpDataWSlashCount['share_count'] = isset( $httpData->engagement->share_count) ? $httpData->engagement->share_count : 0;
                    $httpDataWSlashCount['c'] = $httpDataWSlashCount['reaction_count'] +  $httpDataWSlashCount['comment_count'] + $httpDataWSlashCount['comment_plugin_count'] + $httpDataWSlashCount['share_count'];
                    $count = $httpDataWSlashCount;
//                  $httpDataWSlashCount = isset( $httpDataWSlash->engagement->reaction_count ) ? $httpDataWSlash->engagement->reaction_count : 0;
                }

                if( isset( $httpsDataWSlash ) && false !== $httpsDataWSlash ) {
                    $httspDataWSlashCount['reaction_count'] = isset( $httpData->engagement->reaction_count) ? $httpData->engagement->reaction_count : 0;
                    $httspDataWSlashCount['comment_count'] = isset( $httpData->engagement->comment_count) ? $httpData->engagement->comment_count : 0;
                    $httspDataWSlashCount['comment_plugin_count'] = isset( $httpData->engagement->comment_plugin_count) ? $httpData->engagement->comment_plugin_count : 0;
                    $httspDataWSlashCount['share_count'] = isset( $httpData->engagement->share_count) ? $httpData->engagement->share_count : 0;
                    $httspDataWSlashCount['c'] = $httspDataWSlashCount['reaction_count'] +  $httspDataWSlashCount['comment_count'] + $httspDataWSlashCount['comment_plugin_count'] + $httspDataWSlashCount['share_count'];
                    $count = $httspDataWSlashCount;
                }

//                $count = ( isset( $httpCount['c'] ) ? $httpCount['c'] : 0 ) + ( isset( $httpsCount['c'] ) ? $httpsCount['c'] : 0 ) + ( isset( $httpDataWSlashCount['c'] ) ? $httpDataWSlashCount['c'] : 0 ) + ( isset( $httspDataWSlashCount['c'] ) ? $httspDataWSlashCount['c'] : 0 );
            endif;
        }
        return $count;
    }

    /********************************** Methods to get facebook cumulatitve count CLOSES **********************/

    public function sfsi_pinterest_get_count(){

        $arrRespData = array();
        $count = 0;

        $apiUrlArr = array(
            'https://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url='.$this->urlHttp,
            'https://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url='.$this->urlHttps
        );
        
        $arrRespData = $this->sfsi_get_multi_curl($apiUrlArr,array(),true); 
       
        if(!empty($arrRespData)){

            foreach ($arrRespData as $respJson) {
                  $json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $respJson);
                  $json        = json_decode($json_string, true);
                  $count       = $count + (isset($json['count'])? intval($json['count']):0);
            }
        }
        return $count;
    }   

}
