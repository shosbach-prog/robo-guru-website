<?php
class Ultimate_License_API_Manager extends License_API_Manager{

	public function activate_license($license_key){

			$message = '';

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license_key,
				'item_name'  => urlencode($this->item_id), // the name of our product in EDD
				'url'        => $this->siteurl
			);
			
			// Call the custom API.
			$response = wp_remote_post( $this->apiurl, array( 'timeout' => 30, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$errors = $response->get_error_message();
				if(empty($errors)){
					$message =  __( 'An error occurred, please try again.', 'ultimate-social-media-plus' );
				}elseif(false!==strpos($errors, 'Network is unreachable')){
					$message =  __( 'Connecting to Ultimatelysocial servers (for license check) didn’t work. This is probably due restrictions on outgoing connections on your server, e.g. because of a firewall. Please ask your hosting company / server team to either a) temporarily de-activate the firewall (port 443) for a minute (at least the outgoing connections) or b) whitelist 188.165.53.179 and 192.81.249.240 as IPs so that your server can communicate with our servers.', 'ultimate-social-media-plus' );
				}elseif(false!==strpos($errors, 'No working transports found')){
					$message =  __( 'CURL doesn\'t seem to be installed on your server. This is required so that your license can be activated. Please ask your web developer or hosting company to enable it.', 'ultimate-social-media-plus' );
				}
				else{
					$getError = $response->get_error_message();

					$message =  ( is_wp_error( $response ) && ! empty($getError) ) ? $getError : __( 'An error occurred, please try again.', 'ultimate-social-media-plus' );
				}
			}
			else
			{
				$license_data = json_decode(wp_remote_retrieve_body($response));

				if(false === $license_data->success)
				{
					switch( $license_data->error )
					{
						case 'expired' :
							$message = sprintf(
								__( 'Your license key expired on %s.', 'ultimate-social-media-plus' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
						break;

						case 'revoked' :
							$message = __( 'Your license key has been disabled.', 'ultimate-social-media-plus' );
						break;

						case 'missing' :
							$message = __( 'Invalid license. Please check that you have selected the correct option (if you bought the plugin on Sellcodes or directly on Ultimatelysocial)', 'ultimate-social-media-plus');
						break;

						case 'invalid' :
						case 'site_inactive' :
							$message = __( 'Your license is not active for this URL.', 'ultimate-social-media-plus' );
						break;

						case 'item_name_mismatch' :
							$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'ultimate-social-media-plus' ), $this->item_id );
						break;

						case 'no_activations_left':
							$message = __( 'Your license key has reached its activation limit.', 'ultimate-social-media-plus' );
						break;

						default :
							$message = __( 'An error occurred, please try again.', 'ultimate-social-media-plus' );
						break;
					}
				}
				else{
					$message = $license_data;
				}
			}
			return $message;
	}

	public function deactivate_license($license_key){

		$message = "";

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
			'item_name'  => urlencode($this->item_id), // the name of our product in EDD
			'url'        => SITEURL
		);

		// Call the custom API.
		$response = wp_remote_post( $this->apiurl, array( 'timeout' => 30, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$getError = $response->get_error_message();
			$message  = ( is_wp_error( $response ) && ! empty($getError) ) ? $getError : __( 'An error occurred, please try again.', 'ultimate-social-media-plus' );
		}
		else{
			
			$license_data = json_decode(wp_remote_retrieve_body($response));
			
			if(isset($license_data->license) && !empty($license_data->license) && strtolower($license_data->license) == 'deactivated' || strtolower($license_data->license) == 'failed') {
				$message = "";
			}
			else{
				$message = __( 'License key deactivation failed for your site.', 'ultimate-social-media-plus' );
			}
		}
		return $message;
	}

	public static function check_license(){

		$license_api_name = get_sfsi_active_license_api_name();
		$license 		  = get_option($license_api_name.'_license_key');

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license,
			'item_name'  => urlencode(ULTIMATELYSOCIAL_PRODUCT),
			'url'        => SITEURL
		);

		// Call the custom API.
		$response = wp_remote_post(ULTIMATELYSOCIAL_API_URL, array( 'timeout' => 30, 'sslverify' => false, 'body' => $api_params ) );
		if (is_wp_error($response)){

			return false;
		}

		$license_data = json_decode(wp_remote_retrieve_body($response));
		if('valid' == strtolower($license_data->license))
		{
			$expiration = get_option($license_api_name.'_license_expiry');
			if(isset($expiration) && !empty($expiration) && "unlimited" !== strtolower($expiration))
			{
				$expiryDate 	= strtotime($expiration);
				$currentDate	= strtotime(date("Y-m-d H:i:s"));
				
				// var_dump($expiryDate , $currentDate, $expiryDate < $currentDate);die();
				
				if($expiryDate < $currentDate)
				{
					$api_params = array(
						'edd_action' => 'activate_license',
						'license'    => $license,
						'item_name'  => urlencode(ULTIMATELYSOCIAL_PRODUCT), // the name of our product in EDD
						'url'        => home_url()
					);

					// Call the custom API.
					$response = wp_remote_post( ULTIMATELYSOCIAL_API_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
					if ( !is_wp_error( $response ) || 200 === wp_remote_retrieve_response_code( $response ) ) {
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
						if(false !== $license_data->success){
							update_option( 'ultimate_license_expiry', $license_data->expires);
						}
					}
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}		
}
?>