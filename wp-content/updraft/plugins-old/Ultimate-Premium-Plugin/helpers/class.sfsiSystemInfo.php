<?php

if (!class_exists('sfsiSystemInfo')):

    class sfsiSystemInfo{

        private $agent = "";
        private $info = array();

        protected static $instance = null;

        function __construct(){
            $this->init();
        }

        private function init(){
            $this->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
            $this->getBrowser();
            $this->getOS();
            $this->getSiteInfo();
            $this->getWpConfigInfo();
            $this->getUsmPluginInfo();
            $this->getActivePluginsInfo();
            $this->getServerInfo();
            $this->getPHPconfigInfo();
            $this->getPHPextensionsInfo();
        }

        public static function getInstance(){

            if (!isset(static::$instance)) {
                static::$instance = new static;
            }

            return static::$instance;
        }

        public function getBrowser(){

            $browser = array(
                            "Navigator"            => "/Navigator(.*)/i",
                             "Firefox"              => "/Firefox(.*)/i",
                             "Internet Explorer"    => "/MSIE(.*)/i",
                             "Google Chrome"        => "/chrome(.*)/i",
                             "MAXTHON"              => "/MAXTHON(.*)/i",
                             "Opera"                => "/Opera(.*)/i",
                    );

            foreach($browser as $key => $value){
                if(preg_match($value, $this->agent)){
                    $this->info['browserInfo']['Browser'] = $key;
                    $this->info['browserInfo']['Version'] = $this->getBrowserVersion($key, $value, $this->agent);
                    break;
                }else{
                    $this->info['browserInfo']['Browser'] = "UnKnown";
                    $this->info['browserInfo']['Version'] = "UnKnown";
                }
            }
        }

        public function getOS(){
           
            $OS = array("Windows"   =>   "/Windows/i",
                        "Linux"     =>   "/Linux/i",
                        "Unix"      =>   "/Unix/i",
                        "Mac"       =>   "/Mac/i"
                        );

            $this->info['browserInfo']['platform'] = "UnKnown";

            foreach($OS as $key => $value){

                if(preg_match($value, $this->agent)){

                    $this->info['browserInfo']['platform'] = $key;
                    break;
                }
            }
            
        }

        public function getBrowserVersion($browser, $search, $string){

            $version = "";
            $browser = strtolower($browser);
            preg_match_all($search,$string,$match);

            switch($browser){
                case "firefox": $version = str_replace("/","",$match[1][0]);
                break;

                case "internet explorer": $version = substr($match[1][0],0,4);
                break;

                case "opera": $version = str_replace("/","",substr($match[1][0],0,5));
                break;

                case "navigator": $version = substr($match[1][0],1,7);
                break;

                case "maxthon": $version = str_replace(")","",$match[1][0]);
                break;

                case "google chrome": $version = substr($match[1][0],1,10);
            }
            return $version;
        }

        public function getSiteInfo(){

            $arrSiteInfo    = array(
                     'siteUrl'       =>  site_url(),
                     'homeUrl'       =>  home_url(),
                     'isMultisite'   =>  is_multisite()
            );

            $this->info['siteInfo'] = $arrSiteInfo;
        }

        public function getWpConfigInfo(){

            global $wp_version;

            $arrWpConfigInfo    = array(
                'wpVersion'        =>  $wp_version,
                'language'         =>  get_locale(),
                'WP_DEBUG'         =>  ( defined('WP_DEBUG') ? WP_DEBUG : null ),
                'WP_MEMORY_LIMIT'  =>  ( defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : null )
            );

            $this->info['wpConfigInfo'] = $arrWpConfigInfo;
        }

        public function getUsmPluginInfo(){

            $usmPluginVersion = get_option('sfsi_premium_pluginVersion',false);

            $this->info['usmPluginInfo'] = array(
                     'Version'   =>  $usmPluginVersion
                 );
        }

        public function getActivePluginsInfo(){
            $this->info['activePluginsInfo'] = is_multisite() ? array() : sfsi_premium_active_plugins();
        }

        public function getMySqlVersion(){
            global $wpdb; 
            return $wpdb->db_version();
        }

        public function getServerInfo(){
                        
            $this->info['serverInfo'] = array(
                "phpVersion"   => phpversion(),
                "mySqlVersion" => $this->getMySqlVersion()
            );             
        }

        public function getPHPconfigInfo(){
            
            $this->info['phpConfigInfo'] = array(
                "memory_limit"         => ini_get('memory_limit'),
                "max_file_uploads"     => ini_get('max_file_uploads'),
                "upload_max_filesize"  => ini_get('upload_max_filesize'),
                "max_execution_time"   => ini_get('max_execution_time'),
                "max_input_vars"       => ini_get('max_input_vars'),
                "display_errors"       => ini_get('display_errors')
            );
        }

        public function getPHPextensionsInfo(){
            
            $this->info['phpExtensionInfo'] = array(
                "curl"       => function_exists('curl_version') ? true : false,
                "multiCurl"  => function_exists('curl_multi_select') ? true : false,
                "gd"         => function_exists('gd_info') ? true : false,
                "imagick"    => extension_loaded('imagick')? true : false
            );
        }
        public function showInfo(){

            return $this->info;

        }

    }

endif;