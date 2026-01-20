<?php
class Sellcodes_License_API_Manager extends License_API_Manager{
	
	public function activate_license($license_key){

		$message = '';

		// data to send in our API request
		$api_params = array(
			'product_id' 	=> $this->item_id,
			'license_key'   => $license_key,
			'baseurl'       => home_url()
		);
		// Call the custom API.
		$response = wp_remote_post( $this->apiurl."/activate", array( 'timeout' => 30, 'sslverify' => false, 'body' => $api_params ) );
		if(is_wp_error($response)){
			$errors = $response->get_error_messages();
			if(is_array($errors)){
				if(empty($errors)){
					$message =  __( 'An error occurred, please try again.', 'ultimate-social-media-plus' );
				}else{
					$regrex = '/(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/';
					if (strpos($errors[0], 'Network is unreachable') !== false && preg_match($regrex, $errors[0])) {
						$message =  __( 'For license activation your server needs to connect to the Sellcodes server ,however, it seems that your server resolves the address of the Sellcodes server to an ipv6 address while it only supports ipv4 connections. Please contact your hosting provider to solve the issue. Sorry for the hassle, but it is not the plugin\'s fault, but your server\'s.', 'ultimate-social-media-plus' );
					}elseif(false!==strpos($errors[0], 'Network is unreachable')){
						$message =  __( 'Connecting to Sellecodes servers (for license check) didn’t work. This is probably due restrictions on outgoing connections on your server, e.g. because of a firewall. Please ask your hosting company / server team to temporarily de-activate the firewall (port 443) for a minute (at least the outgoing connections)', 'ultimate-social-media-plus' );
					}
					elseif(false!==strpos($errors[0], 'No working transports found')){
						$message =  __( 'CURL doesn\'t seem to be installed on your server. This is required so that your license can be activated. Please ask your web developer or hosting company to enable it.', 'ultimate-social-media-plus' );
					}
					else{
						$message =  __( 'An error occurred, please try again. ', 'ultimate-social-media-plus' ).preg_replace( '/cURL error \d+:/', '', $errors[0] );
					}
				}
			}else{
				$message =  $errors;	
			}
		}else{
			// make sure the response came back okay
			$httpCode = wp_remote_retrieve_response_code( $response );
			if ( 200 !== $httpCode) {

				switch ($httpCode) {				
					
					case 500:case 0;
					
						$message =  __( 'An error occurred, please try again.', 'ultimate-social-media-plus' );				
					
					break;

					case 400:
						
						$license_data = json_decode(str_replace("\xEF\xBB\xBF",'',wp_remote_retrieve_body($response)));

						if ( false === $license_data->success){
							$message = $license_data->message;
						}

					break;
					case 403:
						$message =  __( 'For license activation your server needs to connect to the Sellcodes server ,however, it seems that your SERVER IP is blacklisted by Cloudflayer. Please contact us with this detail to resolve the issue as soon as possible.', 'ultimate-social-media-plus' );
					break;

				}
			}
			else
			{
				$license_data = json_decode(str_replace("\xEF\xBB\xBF",'',wp_remote_retrieve_body($response)));
				if ( false === $license_data->success){
					$message = $license_data->message;
				}
				else{

					if(isset($license_data->License_key_valid) && false === $license_data->License_key_valid){
						$message = __( 'Your license key has been disabled.', 'ultimate-social-media-plus' );
					}

					if(isset($license_data->Activation_ok) && false === $license_data->Activation_ok){
						
						if($license_data->Activation_count == $license_data->Maximum_activations){

							$message = __( 'The maximum number of activations for this license has been reached. Please acquire a new license key <a href=https://sellcodes.com/XdHlrQnc target=_blank >here</a> (use the discount code SUPPORTEDREPEATEDPURCHASE to get it 20% off). If you have any questions about it, please reach out to <a href=mailto:help@ultimatelysocial.com?subject=Question_about_additional_license target=_blank >help@ultimatelysocial.com</a>.', 'ultimate-social-media-plus' );
						}
					}

					if(empty($message)){
						$message = (object) $message;
						$message->license = "valid";
						$message->expires = "unlimited" === strtolower($license_data->Expiry_date) ? $license_data->Expiry_date: date('Y-m-d H:i:s', $license_data->Expiry_date);
					}				
				}
			}
		}

		return $message;		

	}
	public function deactivate_license($license_key){

		$message = '';

        // data to send in our API request
        $api_params = array(
            'product_id'     => $this->item_id,
            'license_key'    => $license_key,
            'baseurl'        => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( $this->apiurl."/deactivate", array( 'timeout' => 30, 'sslverify' => false, 'body' => $api_params ) );
        
        // make sure the response came back okay
        if (200 !== wp_remote_retrieve_response_code($response)) {
            
            switch ($httpCode) {                
                
                case 500:case 0;
                
                    $message =  __( 'An error occurred, please try again.', 'ultimate-social-media-plus' );                
                
                break;

                case 400:
                    
                    $license_data = json_decode(str_replace("\xEF\xBB\xBF",'',wp_remote_retrieve_body($response)));

                    if ( false === $license_data->success){
                        $message = $license_data->message;
                    }

                break;
            }
        }
        else{

        	// decode the license data
        	$license_data = json_decode(str_replace("\xEF\xBB\xBF",'',wp_remote_retrieve_body($response)));

	        // $license_data->license will be either "deactivated" or "failed"
	        if(isset($license_data->success) && isset($license_data->license)) {

	            if(false != strtolower($license_data->success)){

	                if('deactivated' == strtolower($license_data->license)){
	                    $message  = "";                    
	                }
	            }
	            else{
	                if('site_inactive' == strtolower($license_data->license)){
	                    $message  = _( 'License is not active for the your site.', 'ultimate-social-media-plus' );
	                }
	                if('revoked' == strtolower($license_data->license)){
	                    $message = __( 'Your license key has been disabled.', 'ultimate-social-media-plus' );
	                }                                                
	            }						
	        }
        }

        return $message;		

	}
	public static function check_license(){

		$license = trim( get_option( SELLCODES_LICENSING.'_license_key' ) );

		$check_api_url = SELLCODES_API_URL."/check_license";

		$api_params = array(
			'product_id'	=> SELLCODES_PRODUCT,
			'license_key' 	=> $license,
			'baseurl'       => SITEURL
		);

		$response = wp_remote_post( $check_api_url, array( 'timeout' => 30, 'sslverify' => false, 'body' => $api_params ) );
		
		$httpCode = wp_remote_retrieve_response_code($response);

		if ( 500 == $httpCode || 0 == $httpCode )
			return false;

		$license_data  = json_decode( str_replace("\xEF\xBB\xBF",'',wp_remote_retrieve_body($response)));

		$license_check = false;

		if(false != isset($license_data->license) && !empty($license_data->license) && strtolower($license_data->license) == 'valid'){

			// Check for updates only when offer is wordpress product & allowing automatic updates
			if(false != isset($license_data->is_wordpress_product)  && false != isset($license_data->offering_automatic_updates)){

				if(false != $license_data->is_wordpress_product && false != $license_data->offering_automatic_updates){
					$license_check = true;
					update_option('sellcodes_usm_license_expiry', $license_data->expires);		
				}
			}
		}
		return $license_check;
	}	
}
?>