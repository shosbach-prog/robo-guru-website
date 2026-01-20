<?php

function sfsi_plus_icon_upload( $iconName, $imgUrl, $newIconPrefix, $customIconIndex, $funcSuccessHandler, $funcErrorHandler ) {

	$upload_dir = wp_upload_dir();

	$ThumbSquareSize      = 100; //Thumbnail will be 57X57
	$Quality              = 90; //jpeg quality
	$DestinationDirectory = $upload_dir['path'] . '/'; //specify upload directory ends with / (slash)
	$AcceessUrl           = $upload_dir['url'] . '/';
	$subdir               = substr( $upload_dir['subdir'], 1 );
	$key                  = $newIconPrefix . time();
	if ( ! empty( $imgUrl ) && filter_var( $imgUrl, FILTER_VALIDATE_URL ) ) :

		//$sfsi_custom_files[] = $custom_imgurl;

		// Get directory path of image to create new image 
		$custom_img_dirPath = sfsi_premium_get_image_directory_path( $imgUrl );

		if ( ! empty( $custom_img_dirPath ) ) :

			// Get image extension
			$imageExt    = pathinfo( $imgUrl, PATHINFO_EXTENSION );
			$ImageType   = 'image/' . $imageExt;
			$NewIconName = $newIconPrefix . time() . '.' . $imageExt;
			$iconPath    = $DestinationDirectory . $NewIconName; //Thumbnail name with destination directory

			if ( "gif" == $imageExt ) {
				// Crop gif image without loosing its animation
				$objUpload = sfsi_plus_resize_gif( $custom_img_dirPath, $ThumbSquareSize, $ThumbSquareSize, $iconPath );

				if ( isset( $objUpload->status ) && ! empty( $objUpload->status ) ) {

					$iconUploadPathToSave = $subdir . "/" . $NewIconName;
					call_user_func_array( $funcSuccessHandler, array(
						$iconName,
						$iconUploadPathToSave,
						$customIconIndex
					) );
				} // Imagick extension is not installed on user' server use same image without cropping
				else {

					$iconUploadPathToSave = $subdir . "/" . $NewIconName;
					call_user_func_array( $funcSuccessHandler, array(
						$iconName,
						$iconUploadPathToSave,
						$customIconIndex
					) );
				}
			} else {
				if ( 'png' == $imageExt ) {

					// Read file data
					$data = file_get_contents( $imgUrl );
					// Pixels per inch
					$ppi = 300;

					// Unit conversion PPI to PPM
					$ppm = round( $ppi * 100 / 2.54 );

					$ppm_bin = str_pad( base_convert( $ppm, 10, 2 ), 32, '0', STR_PAD_LEFT );

					// Split PNG data at first IDAT chunk
					$data_splitted = explode( 'IDAT', $data, 2 );

					// Generate "pHYs" chunk data

					// 4-byte data length
					$length_bin = '00000000000000000000000000001001';
					// 4-byte type or 'pHYs'
					$chunk_name_bin = '01110000010010000101100101110011';
					// 9-byte data
					$ppu_data_bin =
						$ppm_bin // Pixels per unit, x axis
						. $ppm_bin // Pixels per unit, y axis
						. '00000001'; // units - 1 for meters
					// Calculate 4-byte CRC
					$hash_buffer = '';
					foreach ( str_split( $chunk_name_bin . $ppu_data_bin, 8 ) as $b ) {
						$hash_buffer .= chr( bindec( $b ) );
					}

					$crc_bin = str_pad( base_convert( crc32( $hash_buffer ), 10, 2 ), 32, '0', STR_PAD_LEFT );
					// Create chunk binary string
					$binstring = $length_bin
					             . $chunk_name_bin
					             . $ppu_data_bin
					             . $crc_bin;

					// Convert binary string to raw
					$phys_chunk_raw = '';
					foreach ( str_split( $binstring, 8 ) as $b ) {
						$phys_chunk_raw .= chr( bindec( $b ) );
					}

					$newImageUrl1 = substr( $data_splitted[0], 0, strlen( $data_splitted[0] ) - 4 )
					                . $phys_chunk_raw
					                . substr( $data_splitted[0], strlen( $data_splitted[0] ) - 4, 4 )
					                . 'IDAT'
					                . $data_splitted[1];


					define( 'UPLOAD_DIR', $DestinationDirectory );
					$img  = base64_encode( file_get_contents( $imgUrl ) );
					$img  = str_replace( 'data:image/png;base64,', '', $img );
					$img  = str_replace( ' ', '+', $img );
					$data = base64_decode( $img );

					$basename = "custom_icon" . $key;
					// var_dump($DestinationDirectory,$img,$basename,$data,$newImageUrl1,UPLOAD_DIR);

					$file = UPLOAD_DIR . $basename . '.png';

					$success = file_put_contents( $file, $newImageUrl1 );

					$newImageUrl = $AcceessUrl . $basename . '.png';
					if ( $newImageUrl != "" ) {
						update_option( $key, $newImageUrl );
						$response['res']         = 'success';
						$response['newImageUrl'] = $newImageUrl;
						$response['key']         = $key;
						call_user_func_array( $funcSuccessHandler, array( $iconName, $newImageUrl, $customIconIndex ) );
					} else {
						$response['res'] = 'error';
						call_user_func( $funcErrorHandler, 'error' );
					}
				}
				/* if(extension_loaded('gd')){

					// Creat image resource using gd library					
					switch(strtolower($imageExt))
					{
						 	case 'png':
								// Create a new image from file 
								$CreatedImage =  imagecreatefrompng($custom_img_dirPath);
							
							break;
							
							case 'jpg':
							case 'jpeg':
							case 'pjpeg':
							
								$CreatedImage = imagecreatefromjpeg($custom_img_dirPath);
							
							break;
					}

					if(false === $CreatedImage){
						die(json_encode(array('res'=>'type_error','ext'=>$imageExt))); //output error and exit
					}

					list($CurWidth, $CurHeight) = getimagesize($custom_img_dirPath);

					//Create a square Thumbnail right after, this time we are using sfsiplus_cropImage() function
					if(sfsiplus_cropImage($CurWidth,$CurHeight,$ThumbSquareSize,$iconPath,$CreatedImage,$Quality,$ImageType)){
					
						$iconUploadPathToSave = $subdir."/".$NewIconName;
						call_user_func_array($funcSuccessHandler,array($iconName,$iconUploadPathToSave,$customIconIndex));
				    }
				    else{
				    	call_user_func($funcErrorHandler,'Image Cropping failed');
				    }						
				}
			    else{
			    	call_user_func($funcErrorHandler,'Image Cropping failed due to gd library extension not installed.');
			    } */
			}

		endif;

	else :
		call_user_func( $funcErrorHandler, 'Invalid image url' );

	endif;
}

/* upload custom Skins {Monad}*/
add_action( 'wp_ajax_plus_UploadSkins', 'sfsi_plus_UploadSkins' );
function sfsi_plus_UploadSkins() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "plus_UploadSkins" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}

	$upload_dir = wp_upload_dir();

	$ThumbSquareSize      = 100; //Thumbnail will be 57X57
	$Quality              = 90; //jpeg quality
	$DestinationDirectory = $upload_dir['path'] . '/'; //specify upload directory ends with / (slash)
	$AcceessUrl           = $upload_dir['url'] . '/';
	$ThumbPrefix          = "cmicon_";

	$data   = sanitize_text_field( $_REQUEST["custom_imgurl"] );
	$params = array();
	parse_str( $data, $params );

	$response = array( 'res' => 'Something went wrong' );

	if ( isset( $params ) && ! empty( $params ) && is_array( $params ) ) :

		foreach ( $params as $key => $value ) :

			$custom_imgurl = $value;
			if ( strtolower( substr( $custom_imgurl, 0, 4 ) ) !== "http" ) {
				$custom_imgurl = rtrim( home_url(), "/" ) . $custom_imgurl;
			}
			if ( ! empty( $custom_imgurl ) && filter_var( $custom_imgurl, FILTER_VALIDATE_URL ) ) :
				//$sfsi_custom_files[] = $custom_imgurl;

				// Get directory path of image to create new image 
				$custom_img_dirPath = sfsi_premium_get_image_directory_path( $custom_imgurl );

				if ( ! empty( $custom_img_dirPath ) ) :

					// Get image extension
					$imageExt = pathinfo( $custom_imgurl, PATHINFO_EXTENSION );

					$ImageType = 'image/' . $imageExt;

					$NewIconName = "custom_icon" . $key . time() . '.' . $imageExt;

					$iconPath = $DestinationDirectory . $NewIconName; //Thumbnail name with destination directory

					if ( "gif" == $imageExt ) {
						// Crop gif image without loosing its animation
						$objUpload = sfsi_plus_resize_gif( $custom_img_dirPath, $ThumbSquareSize, $ThumbSquareSize, $iconPath );

						if ( isset( $objUpload->status ) && ! empty( $objUpload->status ) ) {
							//update new image 
							$AccressImagePath = $AcceessUrl . $NewIconName;
							update_option( $key, $AccressImagePath );

							$response['res'] = 'success';
						} // Imagick extension is not installed on user' server use same image without cropping
						else {

							//update new image 
							update_option( $key, $custom_imgurl );
							$response['res'] = 'success';
						}
					} else {

						// Creat image resource using gd library					
						/* if(extension_loaded('gd')){
						
							switch(strtolower($imageExt))
							{
								 	case 'png':
										// Create a new image from file 
										$CreatedImage =  imagecreatefrompng($custom_img_dirPath);
									
									break;
									
									case 'jpg':
									case 'jpeg':
									case 'pjpeg':
									
										$CreatedImage = imagecreatefromjpeg($custom_img_dirPath);
									
									break;
							}

							if(false === $CreatedImage){
								die(json_encode(array('res'=>'type_error','ext'=>$imageExt))); //output error and exit
							}

							$sizes = getimagesize($custom_img_dirPath);

							if(false===$sizes){
								$sizes = getimagesize($custom_imgurl);
							}
							if(false===$sizes){
								update_option($key,$custom_imgurl);
								$response['res']     = 'success';
								$response['warning'] = 'Couldn\'t Crop the icon';
							}else{

								list($CurWidth, $CurHeight) = $sizes;

								//Create a square Thumbnail right after, this time we are using sfsiplus_cropImage() function
								if(sfsiplus_cropImage($CurWidth,$CurHeight,$ThumbSquareSize,$iconPath,$CreatedImage,$Quality,$ImageType)){
								
									//update database information 
									$AccressImagePath = $AcceessUrl.$NewIconName;
									update_option($key,$AccressImagePath);

									$response['res'] = 'success';
							   }
							}

						}else{
							update_option($key,$custom_imgurl);
							$response['res'] 	 = 'success';
							$response['warning'] = 'Gd library not loaded';
						} */

						if ( 'png' == $imageExt ) {

							// Read file data
							$data = file_get_contents( $custom_img_dirPath );

							// Pixels per inch
							$ppi = 300;

							// Unit conversion PPI to PPM
							$ppm = round( $ppi * 100 / 2.54 );

							$ppm_bin = str_pad( base_convert( $ppm, 10, 2 ), 32, '0', STR_PAD_LEFT );

							// Split PNG data at first IDAT chunk
							$data_splitted = explode( 'IDAT', $data, 2 );

							// Generate "pHYs" chunk data

							// 4-byte data length
							$length_bin = '00000000000000000000000000001001';
							// 4-byte type or 'pHYs'
							$chunk_name_bin = '01110000010010000101100101110011';
							// 9-byte data
							$ppu_data_bin =
								$ppm_bin // Pixels per unit, x axis
								. $ppm_bin // Pixels per unit, y axis
								. '00000001'; // units - 1 for meters
							// Calculate 4-byte CRC
							$hash_buffer = '';
							foreach ( str_split( $chunk_name_bin . $ppu_data_bin, 8 ) as $b ) {
								$hash_buffer .= chr( bindec( $b ) );
							}
							$crc_bin = str_pad( base_convert( crc32( $hash_buffer ), 10, 2 ), 32, '0', STR_PAD_LEFT );
							// Create chunk binary string
							$binstring = $length_bin
							             . $chunk_name_bin
							             . $ppu_data_bin
							             . $crc_bin;

							// Convert binary string to raw
							$phys_chunk_raw = '';
							foreach ( str_split( $binstring, 8 ) as $b ) {
								$phys_chunk_raw .= chr( bindec( $b ) );
							}

							$newImageUrl1 = substr( $data_splitted[0], 0, strlen( $data_splitted[0] ) - 4 )
							                . $phys_chunk_raw
							                . substr( $data_splitted[0], strlen( $data_splitted[0] ) - 4, 4 )
							                . 'IDAT'
							                . $data_splitted[1];


							define( 'UPLOAD_DIR', $DestinationDirectory );
							$img  = base64_encode( file_get_contents( $custom_img_dirPath ) );
							$img  = str_replace( 'data:image/png;base64,', '', $img );
							$img  = str_replace( ' ', '+', $img );
							$data = base64_decode( $img );

							$basename = "custom_icon" . $key;
							$file     = UPLOAD_DIR . $basename . '.png';

							$success = file_put_contents( $file, $data );

							$newImageUrl = $AcceessUrl . $basename . '.png';

							if ( $newImageUrl != "" ) {

								update_option( $key, $newImageUrl );
								$response['res']         = 'success';
								$response['newImageUrl'] = $newImageUrl;
								$response['key']         = $key;
							} else {
								$response['res'] = 'error';
							}
						}
					}

				endif;

			else :

				$response['res'] = __( 'Invalid image url', 'ultimate-social-media-plus' );

			endif;

		endforeach;

	else :
		$response['res'] = __( 'Invalid image url', 'ultimate-social-media-plus' );

	endif;

	die( json_encode( $response ) );
}

/* Delete custom Skins {Monad}*/
add_action( 'wp_ajax_plus_DeleteSkin', 'sfsi_plus_DeleteSkin' );
function sfsi_plus_DeleteSkin() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "sfsi_plus_deleteCustomSkin" ) ) {
		echo json_encode( array( 'res' => "error" ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}

	$resp = array( 'res' => 'error' );

	if ( $_POST['action'] == 'plus_DeleteSkin' && isset( $_POST['iconname'] ) && ! empty( $_POST['iconname'] ) && current_user_can( 'manage_options' ) ) {
		$iconName  = sanitize_text_field( $_POST['iconname'] );
		$dbIconUrl = get_option( $iconName, false );

		if ( $dbIconUrl ) {
			$isDeleted = sfsi_plus_delete_image( $dbIconUrl );

			delete_option( $iconName );
			$resp['res'] = 'success';
		}
	}

	die( json_encode( $resp ) );
}

function plus_UploadMouseOverIcons() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "plus_MouseOverIcons" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}

	$iconName = sanitize_text_field( $_POST['iconName'] );

	$custom_imgurl = esc_url( $_POST['custom_imgurl'] );

	$iconPrefix = 'mouseover_icon_' . $iconName;

	$customIconIndex = isset( $customIconIndex ) ? intval( $customIconIndex ) : - 1;

	$option1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );

	$icons = ( $option1['sfsi_custom_desktop_icons'] ) ? maybe_unserialize( $option1['sfsi_custom_desktop_icons'] ) : array();

	sfsi_plus_icon_upload(
		$iconName,
		$custom_imgurl,
		$iconPrefix,
		$customIconIndex,


		function ( $iconName, $iconUploadPathToSave, $customIconIndex ) {

			if ( isset( $iconUploadPathToSave ) && ! empty( $iconUploadPathToSave ) ) {

				$option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );

				$arrMouseoverIcons = ( isset( $option3['sfsi_plus_mouseOver_other_icon_images'] ) ) ? maybe_unserialize( $option3['sfsi_plus_mouseOver_other_icon_images'] ) : array();

				$arrMouseoverIcons = is_array( $arrMouseoverIcons ) ? $arrMouseoverIcons : array();

				if ( 'custom' == $iconName && $customIconIndex > - 1 ) {

					if ( ! isset( $arrMouseoverIcons[ $iconName ] ) ) :

						$arrCustomIcons                     = array();
						$arrCustomIcons[ $customIconIndex ] = $iconUploadPathToSave;
						$arrMouseoverIcons[ $iconName ]     = $arrCustomIcons;

					else :

						$arrMouseoverIcons[ $iconName ][ $customIconIndex ] = $iconUploadPathToSave;

					endif;
				} else {
					$arrMouseoverIcons[ $iconName ] = $iconUploadPathToSave;
				}

				$option3['sfsi_plus_mouseOver_other_icon_images'] = serialize( $arrMouseoverIcons );
				update_option( 'sfsi_premium_section3_options', serialize( $option3 ) );
			}
			$response['status']  = true;
			$response['message'] = __( 'Icon sucessfully uploaded', 'ultimate-social-media-plus' );
			$response['url']     = $iconUploadPathToSave;
			echo json_encode( $response );
			die;
		},

		function ( $message ) {

			$response['status']  = false;
			$response['message'] = $message;

			echo json_encode( $response );
			die;
		}

	);
}

add_action( 'wp_ajax_plus_MouseOverIcons', 'plus_UploadMouseOverIcons' );

function sfsi_premium_deleteIcons() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "sfsi_premium_deleteIcons" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}


	$icon_name = sanitize_text_field( $_POST['icon_name'] );

	$serviceType = sanitize_text_field( $_POST['serviceType'] );

	$arrUploadDir     = wp_upload_dir();
	$uploadDirBaseDir = trailingslashit( $arrUploadDir['basedir'] );
	$uploadDirBaseUrl = trailingslashit( $arrUploadDir['baseurl'] );

	$customIconIndex = isset( $customIconIndex ) ? intval( $customIconIndex ) : - 1;

	$response = array(
		'status'  => false,
		'message' => __( 'Failed to delete the icon', 'ultimate-social-media-plus' )
	);

	if ( isset( $icon_name ) && ! empty( $icon_name ) && isset( $serviceType ) && ! empty( $serviceType ) ) {

		switch ( $serviceType ) {

			case 'mouseover':

				$option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );

				$arrMouseoverIcons = ( isset( $option3['sfsi_plus_mouseOver_other_icon_images'] ) ) ? maybe_unserialize( $option3['sfsi_plus_mouseOver_other_icon_images'] ) : array();

				$arrMouseoverIcons = is_array( $arrMouseoverIcons ) ? $arrMouseoverIcons : array();

				// if(isset($arrMouseoverIcons[$icon_name]) && !empty($arrMouseoverIcons[$icon_name])){
				// 	$iconDirPath = $uploadDirBaseDir.$arrMouseoverIcons[$icon_name];
				// 	sfsi_plus_delete_image_with_dir_path($iconDirPath);
				// }

				$defaultIcon = false;

				if (
					"custom" == $icon_name && $customIconIndex > - 1
					&& isset( $arrMouseoverIcons[ $icon_name ][ $customIconIndex ] )
					&& ! empty( $arrMouseoverIcons[ $icon_name ][ $customIconIndex ] )
				) {

					unset( $arrMouseoverIcons[ $icon_name ][ $customIconIndex ] );

					// Get all custom icons original images
					// $arrCustomIcons = sfsi_get_custom_icons_images();

					// if( isset($arrCustomIcons[$customIconIndex]) && !empty($arrCustomIcons[$customIconIndex]) ){

					// 	$defaultIcon = $arrCustomIcons[$customIconIndex];
					// }
				} else {

					unset( $arrMouseoverIcons[ $icon_name ] );

					//$defaultIcon = "facebook" == $icon_name ? sfsi_plus_get_icon_image("fb") : sfsi_plus_get_icon_image($icon_name);

				}


				$option3['sfsi_plus_mouseOver_other_icon_images'] = serialize( $arrMouseoverIcons );
				update_option( 'sfsi_premium_section3_options', serialize( $option3 ) );

				$response['status']  = true;
				$response['message'] = __( 'Icon successfully deleted', 'ultimate-social-media-plus' );
				//$response['defaultIcon'] = $defaultIcon;


				break;

			default:
				# code...
				break;
		}
	}

	echo json_encode( $response );
	die;
}

add_action( 'wp_ajax_sfsi_premium_deleteIcons', 'sfsi_premium_deleteIcons' );

/* add ajax action for custom icons upload {Monad}*/
add_action( 'wp_ajax_plus_UploadIcons', 'sfsi_plus_UploadIcons' );
apply_filters( 'image_strip_meta', false );

function my_upload_dir( $upload ) {

	$upload['subdir'] = '/sub-dir-to-use' . $upload['subdir'];

	$upload['path'] = $upload['basedir'] . $upload['subdir'];

	$upload['url'] = $upload['baseurl'] . $upload['subdir'];

	return $upload;
}

/* uplaod custom icon {change by monad}*/
function sfsi_plus_UploadIcons() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "plus_UploadIcons" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}

	$custom_imgurl        = esc_url( $_POST['custom_imgurl'] );
	$upload_dir           = wp_upload_dir();
	$ThumbSquareSize      = 100;
	$Quality              = 90; //quality
	$DestinationDirectory = $upload_dir['path'] . '/'; //specify upload directory ends with / (slash)
	$AcceessUrl           = $upload_dir['url'] . '/';
	$ThumbPrefix          = "cmicon_";
	$response             = array( "res" => 'Something went wrong' );
	if ( strtolower( substr( $custom_imgurl, 0, 4 ) ) !== "http" ) {
		$custom_imgurl = rtrim( home_url(), "/" ) . $custom_imgurl;
	}
	$custom_img_dirPath = sfsi_premium_get_image_directory_path( $custom_imgurl );
	if ( ! empty( $custom_img_dirPath ) ) :

		// Get image extension
		$imageExt  = pathinfo( $custom_imgurl, PATHINFO_EXTENSION );
		$ImageType = 'image/' . $imageExt;

		$sec_options = ( get_option( 'sfsi_premium_section1_options', false ) ) ? maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) ) : '';

		$icons = ( is_array( maybe_unserialize( $sec_options['sfsi_custom_files'] ) ) ) ? maybe_unserialize( $sec_options['sfsi_custom_files'] ) : array();

		$dicons = ( is_array( maybe_unserialize( $sec_options['sfsi_custom_desktop_icons'] ) ) ) ? maybe_unserialize( $sec_options['sfsi_custom_desktop_icons'] ) : array();

		$micons = ( is_array( maybe_unserialize( $sec_options['sfsi_custom_mobile_icons'] ) ) ) ? maybe_unserialize( $sec_options['sfsi_custom_mobile_icons'] ) : array();

		if ( empty( $icons ) ) {
			end( $icons );
			$new = 0;
		} else {
			end( $icons );
			$cnt = key( $icons );
			$new = $cnt + 1;
		}

		$NewIconName = "plus_custom_icon" . $new . '.' . $imageExt;
		$iconPath    = $DestinationDirectory . $NewIconName; //Thumbnail name with destination directory

		$newImageUrl = $AcceessUrl . $NewIconName;
		if ( "jpeg" == $imageExt || "jpg" == $imageExt ) {
			$response = array( "res" => 'jpgError' );
		}
		if ( "gif" == $imageExt ) {
			// Crop gif image without loosing its animation
			$objUpload = sfsi_plus_resize_gif( $custom_img_dirPath, $ThumbSquareSize, $ThumbSquareSize, $iconPath );

			if ( false !== $objUpload->status ) {

				$response['res'] = 'success';
			} // Imagick extension is not installed on user's server use same image without cropping
			else {
				$newImageUrl     = $custom_imgurl;
				$response['res'] = 'success';
			}
		} else {

			/* if(extension_loaded('gd')){

						switch(strtolower($imageExt))
						{
							 	case 'png':
									// Create a new image from file 
									$CreatedImage =  imagecreatefrompng($custom_img_dirPath);
								
								break;
								
								case 'jpg':
								case 'jpeg':
								case 'pjpeg':
								
									$CreatedImage = imagecreatefromjpeg($custom_img_dirPath);
								
								break;
						}

						if(false === $CreatedImage){
							die(json_encode(array('res'=>'type_error','ext'=>$imageExt))); //output error and exit
						}

						list($CurWidth, $CurHeight) = getimagesize($custom_img_dirPath);

						//Create a square Thumbnail right after, this time we are using sfsiplus_cropImage() function
						if(sfsiplus_cropImage($CurWidth,$CurHeight,$ThumbSquareSize,$iconPath,$CreatedImage,$Quality,$ImageType)){
							
							$response['res'] = 'success';
					    }
					} */
			if ( 'png' == $imageExt ) {
				// Read file data
				$data = file_get_contents( $custom_img_dirPath );

				// Pixels per inch
				$ppi = 300;

				// Unit conversion PPI to PPM
				$ppm = round( $ppi * 100 / 2.54 );

				$ppm_bin = str_pad( base_convert( $ppm, 10, 2 ), 32, '0', STR_PAD_LEFT );

				// Split PNG data at first IDAT chunk
				$data_splitted = explode( 'IDAT', $data, 2 );

				// Generate "pHYs" chunk data

				// 4-byte data length
				$length_bin = '00000000000000000000000000001001';
				// 4-byte type or 'pHYs'
				$chunk_name_bin = '01110000010010000101100101110011';
				// 9-byte data
				$ppu_data_bin =
					$ppm_bin // Pixels per unit, x axis
					. $ppm_bin // Pixels per unit, y axis
					. '00000001'; // units - 1 for meters
				// Calculate 4-byte CRC
				$hash_buffer = '';
				foreach ( str_split( $chunk_name_bin . $ppu_data_bin, 8 ) as $b ) {
					$hash_buffer .= chr( bindec( $b ) );
				}

				$crc_bin = str_pad( base_convert( crc32( $hash_buffer ), 10, 2 ), 32, '0', STR_PAD_LEFT );
				// Create chunk binary string
				$binstring = $length_bin
				             . $chunk_name_bin
				             . $ppu_data_bin
				             . $crc_bin;

				// Convert binary string to raw
				$phys_chunk_raw = '';
				foreach ( str_split( $binstring, 8 ) as $b ) {
					$phys_chunk_raw .= chr( bindec( $b ) );
				}


				$newImageUrl1 = substr( $data_splitted[0], 0, strlen( $data_splitted[0] ) - 4 )
				                . $phys_chunk_raw
				                . substr( $data_splitted[0], strlen( $data_splitted[0] ) - 4, 4 )
				                . 'IDAT'
				                . $data_splitted[1];


				define( 'UPLOAD_DIR', $DestinationDirectory );
				$img  = base64_encode( file_get_contents( $custom_img_dirPath ) );
				$img  = str_replace( 'data:image/png;base64,', '', $img );
				$img  = str_replace( ' ', '+', $img );
				$data = base64_decode( $img );

				$basename = "plus_" . $new . "_" . uniqid();
				$file     = UPLOAD_DIR . $basename . '.png';

				$success     = file_put_contents( $file, $newImageUrl1 );
				$newImageUrl = $AcceessUrl . $basename . '.png';

				if ( $newImageUrl != "" ) {
					$response['res'] = 'success';
				} else {
					$response['res'] = 'error';
				}
			}
		}


		if ( 'success' == $response['res'] ) {

			//update database information 			
			$icons  = array_filter( $icons );
			$dicons = array_filter( $dicons );
			$micons = array_filter( $micons );

			$icons[]  = $newImageUrl;
			$dicons[] = $newImageUrl;
			$micons[] = $newImageUrl;

			$sec_options['sfsi_custom_files']         = serialize( $icons );
			$sec_options['sfsi_custom_desktop_icons'] = serialize( $dicons );
			$sec_options['sfsi_custom_mobile_icons']  = serialize( $micons );

			$total_uploads = count( $icons );
			end( $icons );
			$key = key( $icons );

			update_option( 'sfsi_premium_section1_options', serialize( $sec_options ) );

			// Updating order of icons in Quesiton 6
			sfsi_plus_add_custom_icon_in_order_desktop_and_mobile( $key );

			$response = array(
				'res'      => 'success',
				'img_path' => $newImageUrl,
				'element'  => $total_uploads,
				'key'      => $key
			);
		}

		die( json_encode( $response ) );

	endif;
}


/* delete uploaded icons */
add_action( 'wp_ajax_plus_deleteIcons', 'sfsi_plus_deleteIcons' );

function sfsi_plus_deleteIcons() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "plus_deleteIcons" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}

	if ( isset( $_POST['icon_name'] ) && ! empty( $_POST['icon_name'] ) ) {
		/* get icons details to delete it from plugin folder */
		$custom_icon = explode( '_', $_POST['icon_name'] );

		$sec_options1 = ( get_option( 'sfsi_premium_section1_options', false ) ) ? maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) ) : array();
		$sec_options2 = ( get_option( 'sfsi_premium_section2_options', false ) ) ? maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) ) : array();

		$up_icons = ( is_array( maybe_unserialize( $sec_options1['sfsi_custom_files'] ) ) ) ? maybe_unserialize( $sec_options1['sfsi_custom_files'] ) : array();

		$up_dicons = ( is_array( maybe_unserialize( $sec_options1['sfsi_custom_desktop_icons'] ) ) ) ? maybe_unserialize( $sec_options1['sfsi_custom_desktop_icons'] ) : array();
		$up_micons = ( is_array( maybe_unserialize( $sec_options1['sfsi_custom_mobile_icons'] ) ) ) ? maybe_unserialize( $sec_options1['sfsi_custom_mobile_icons'] ) : array();

		$icons_links = ( is_array( maybe_unserialize( $sec_options2['sfsi_plus_CustomIcon_links'] ) ) ) ? maybe_unserialize( $sec_options2['sfsi_plus_CustomIcon_links'] ) : array();

		if ( isset( $up_icons[ $custom_icon[1] ] ) && ! empty( $up_icons[ $custom_icon[1] ] ) ) {

			$deleteIndex = $custom_icon[1];

			$icon_path = $up_icons[ $deleteIndex ];
			$path      = pathinfo( $icon_path );

			// Changes By {Monad}
			$imgpath = parse_url( $icon_path, PHP_URL_PATH );

			if ( is_file( $_SERVER['DOCUMENT_ROOT'] . $imgpath ) && 'gif' != $path['extension'] ) {
				unlink( $_SERVER['DOCUMENT_ROOT'] . $imgpath );
			}

			unset( $up_icons[ $deleteIndex ] );
			unset( $up_dicons[ $deleteIndex ] );
			unset( $up_micons[ $deleteIndex ] );
			unset( $icons_links[ $deleteIndex ] );

			// Question 4->Mouse-Over effects -> Show other icons on mouse-over
			sfsi_plus_remove_custom_icon_mouseover_icon( $deleteIndex );

			sfsi_plus_remove_custom_icon_in_order_desktop_and_mobile( $deleteIndex );

			/* update database after delete */
			$sec_options1['sfsi_custom_files']          = serialize( $up_icons );
			$sec_options1['sfsi_custom_desktop_icons']  = serialize( $up_dicons );
			$sec_options1['sfsi_custom_mobile_icons']   = serialize( $up_micons );
			$sec_options2['sfsi_plus_CustomIcon_links'] = serialize( $icons_links );

			end( $up_icons );

			$key = ( key( $up_icons ) ) ? key( $up_icons ) : $custom_icon[1];

			$total_uploads = count( $up_icons );

			update_option( 'sfsi_premium_section1_options', serialize( $sec_options1 ) );
			update_option( 'sfsi_premium_section2_options', serialize( $sec_options2 ) );

			die( json_encode( array( 'res' => 'success', 'last_index' => $key, 'total_up' => $total_uploads ) ) );
		} else {
			die( json_encode( array( 'res' => 'fail' ) ) );
		}
	}
}

// function sfsi_plus_UploadIcons()
// {
// 	extract($_POST);

// 	$upload_dir				= wp_upload_dir();
// 	$ThumbSquareSize 		= 100; //Thumbnail will be 57X57
// 	$Quality 			    = 90; //jpeg quality
// 	$DestinationDirectory   = $upload_dir['path'].'/'; //specify upload directory ends with / (slash)
// 	$AcceessUrl             = $upload_dir['url'].'/';
// 	$ThumbPrefix			= "cmicon_";

//    if(!empty($custom_imgurl))
// 	{
// 		//$sfsi_custom_files[] = $custom_imgurl;	

// 		$imgData 		= wp_get_attachment_metadata(attachment_url_to_postid($custom_imgurl));
// 		$custom_imgurl  = trailingslashit($upload_dir['basedir']).$imgData['file'];

// 		$custom_img_dirPath = sfsi_premium_get_image_directory_path($custom_imgurl);

// 		list($CurWidth, $CurHeight) = getimagesize($custom_imgurl);

// 		$info 	  = explode("/", $custom_imgurl);
// 		$iconName = array_pop($info);
// 		$ImageExt = substr($iconName, strrpos($iconName, '.'));
// 		$ImageExt = str_replace('.','',$ImageExt);

// 		$iconName  = str_replace(' ','-',strtolower($iconName)); // get image name
// 		$ImageType = 'image/'.$ImageExt;


// 		switch(strtolower($ImageType))
// 		{
// 			 	case 'image/png':
// 						// Create a new image from file 
// 						$CreatedImage =  imagecreatefrompng($custom_imgurl);
// 						break;
// 				case 'image/gif':
// 						$CreatedImage =  imagecreatefromgif($custom_imgurl);
// 						break;
// 				case 'image/jpg':
// 						$CreatedImage = imagecreatefromjpeg($custom_imgurl);
// 						break;					
// 				case 'image/jpeg':
// 				case 'image/pjpeg':
// 						$CreatedImage = imagecreatefromjpeg($custom_imgurl);
// 						break;
// 				default:
// 						 die(json_encode(array('res'=>'type_error'))); //output error and exit
// 		}

// 		$ImageName = preg_replace("/\\.[^.\\s]{3,4}$/", "", $iconName);

// 		$sec_options= (get_option('sfsi_premium_section1_options',false)) ? maybe_unserialize(get_option('sfsi_premium_section1_options',false)) : '' ;

// 		$icons = (is_array(maybe_unserialize($sec_options['sfsi_custom_files']))) ? maybe_unserialize($sec_options['sfsi_custom_files']) : array();

//        	$dicons  = (is_array(maybe_unserialize($sec_options['sfsi_custom_desktop_icons']))) ? maybe_unserialize($sec_options['sfsi_custom_desktop_icons']) : array();

// 		$micons = (is_array(maybe_unserialize($sec_options['sfsi_custom_mobile_icons']))) ? maybe_unserialize($sec_options['sfsi_custom_mobile_icons']) : array();

// 		if(empty($icons))
// 		{   
// 			end($icons);
// 			$new = 0;
// 		}    
// 		else {
// 			end($icons);
// 			$cnt = key($icons);
// 			$new = $cnt+1;
// 		}

// 		$NewIconName = "plus_custom_icon".$new.'.'.$ImageExt;
//         $iconPath 	 = $DestinationDirectory.$NewIconName; //Thumbnail name with destination directory

// 		//Create a square Thumbnail right after, this time we are using sfsiplus_cropImage() function
// 		if(sfsiplus_cropImage($CurWidth,$CurHeight,$ThumbSquareSize,$iconPath,$CreatedImage,$Quality,$ImageType))
// 		{
// 		 	//update database information 
// 			$AccressImagePath = $AcceessUrl.$NewIconName;                                        

// 			$icons  = array_filter($icons);
// 			$dicons = array_filter($dicons);
// 			$micons = array_filter($micons);

// 			$icons[]  = $AccressImagePath;
// 			$dicons[] = $AccressImagePath;
// 			$micons[] = $AccressImagePath;

// 			$sec_options['sfsi_custom_files'] 		  = serialize($icons);
// 			$sec_options['sfsi_custom_desktop_icons'] = serialize($dicons);
// 			$sec_options['sfsi_custom_mobile_icons']  = serialize($micons);

// 			$total_uploads = count($icons); end($icons); $key = key($icons);

// 			update_option('sfsi_premium_section1_options',serialize($sec_options));

// 			// Updating order of icons in Quesiton 6
// 			sfsi_plus_add_custom_icon_in_order_desktop_and_mobile($key);

// 			die(json_encode(array('res'=>'success','img_path'=>$AccressImagePath,'element'=>$total_uploads,'key'=>$key)));
// 	   }
// 	   else
// 	   {        
// 		   die(json_encode(array('res'=>'thumb_error')));
// 	   }

// 	}
// }

function sfsi_plus_get_Imdone_returnHtml( $iconName, $classNumber, $bgPosition ) {

	$rhtml = '';
	if ( isset( $iconName ) && ! empty( $iconName ) ) {

		$iconName = "pinterest" == strtolower( $iconName ) ? "pintrest" : $iconName;

		$dbkey = "plus_" . $iconName . "_skin";

		$iconUrl = get_option( $dbkey, false );

		$addTimeStampToStopImageCaching = "?" . strtotime( "now" );

		if ( $iconUrl ) {
			$icon  = $iconUrl . $addTimeStampToStopImageCaching;
			$rhtml = '<span class="sfsiplus_row_17_' . $classNumber . ' sfsiplus_' . $iconName . '_section sfsi_plus-bgimage" style="background: url(' . $icon . ') no-repeat;">		
			</span>';
		} else {
			$rhtml = '<span class="sfsiplus_row_17_' . $classNumber . ' sfsiplus_' . $iconName . '_section" style="background-position:' . $bgPosition . ';"></span>';
		}
	}

	return $rhtml;
}

/* add ajax action for custom skin done & save{Monad}*/
add_action( 'wp_ajax_plus_Iamdone', 'sfsi_plus_Iamdone' );
function sfsi_plus_Iamdone() {
	$option1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );
	if ( ! wp_verify_nonce( $_POST['nonce'], "plus_Iamdone" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$return = '';

	$iconsArr = array(

		array( "iconName" => "rss", "classNumber" => 1, "bgPosition" => "-3px 6px" ),
		array( "iconName" => "email", "classNumber" => 2, "bgPosition" => "-51px 6px" ),
		array( "iconName" => "facebook", "classNumber" => 3, "bgPosition" => "-98px 6px" ),
		array( "iconName" => "twitter", "classNumber" => 5, "bgPosition" => "-192px 6px" ),
		array( "iconName" => "share", "classNumber" => 6, "bgPosition" => "-238px 6px" ),
		array( "iconName" => "youtube", "classNumber" => 7, "bgPosition" => "-285px 6px" ),
		array( "iconName" => "pinterest", "classNumber" => 8, "bgPosition" => "-332px 6px" ),
		array( "iconName" => "linkedin", "classNumber" => 9, "bgPosition" => "-379px 6px" ),
		array( "iconName" => "instagram", "classNumber" => 10, "bgPosition" => "-426px 6px" ),
		array( "iconName" => "threads", "classNumber" => 35, "bgPosition" => "8px" ),
		array( "iconName" => "bluesky", "classNumber" => 36, "bgPosition" => "8px" ),
		array( "iconName" => "houzz", "classNumber" => 11, "bgPosition" => "-566px 6px" ),
		array( "iconName" => "snapchat", "classNumber" => 12, "bgPosition" => "-613px 6px" ),
		array( "iconName" => "whatsapp", "classNumber" => 13, "bgPosition" => "-660px 6px" ),
		array( "iconName" => "skype", "classNumber" => 14, "bgPosition" => "-706px 6px" ),
		array( "iconName" => "phone", "classNumber" => 28, "bgPosition" => "-660px 6px" ),
		array( "iconName" => "vimeo", "classNumber" => 15, "bgPosition" => "-752px 6px" ),
		array( "iconName" => "soundcloud", "classNumber" => 16, "bgPosition" => "-799px 6px" ),
		array( "iconName" => "yummly", "classNumber" => 17, "bgPosition" => "-845px 6px" ),
		array( "iconName" => "flickr", "classNumber" => 18, "bgPosition" => "-892px 6px" ),
		array( "iconName" => "reddit", "classNumber" => 19, "bgPosition" => "-940px 6px" ),
		array( "iconName" => "tumblr", "classNumber" => 20, "bgPosition" => "-986px 6px" ),

		array( "iconName" => "fbmessenger", "classNumber" => 21, "bgPosition" => "-1038px 6px" ),
		array( "iconName" => "gab", "classNumber" => 30, "bgPosition" => "-1362px 6px" ),
		array( "iconName" => "mix", "classNumber" => 22, "bgPosition" => "-1086px 6px" ),
		array( "iconName" => "ok", "classNumber" => 23, "bgPosition" => "-1132px 6px" ),
		array( "iconName" => "telegram", "classNumber" => 24, "bgPosition" => "-1178px 6px" ),
		array( "iconName" => "vk", "classNumber" => 25, "bgPosition" => "-1226px 6px" ),
		array( "iconName" => "weibo", "classNumber" => 26, "bgPosition" => "-1270px 6px" ),
		array( "iconName" => "xing", "classNumber" => 27, "bgPosition" => "-1320px 6px" ),
		array( "iconName" => "copylink", "classNumber" => 31, "bgPosition" => "0 0" ),
		array( "iconName" => "mastodon", "classNumber" => 32, "bgPosition" => "0 0" ),
	);

	foreach ( $iconsArr as $arrIconData ) {
		if ( 'yes' === $option1[ 'sfsi_plus_' . ( strtolower( $arrIconData["iconName"] ) ) . '_display' ] ) {
			$return .= sfsi_plus_get_Imdone_returnHtml( $arrIconData['iconName'], $arrIconData["classNumber"], $arrIconData["bgPosition"] );
		}
	}

	die( $return );
}

function sfsi_plus_add_custom_icon_in_order_desktop_and_mobile( $key ) {

	if ( isset( $key ) && false !== $key ) {

		$option5 = ( get_option( 'sfsi_premium_section5_options', false ) ) ? maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) ) : '';

		$desktopIconOrder = ( is_array( maybe_unserialize( $option5['sfsi_order_icons_desktop'] ) ) ) ? maybe_unserialize( $option5['sfsi_order_icons_desktop'] ) : array();

		$mobileIconOrder = ( is_array( maybe_unserialize( $option5['sfsi_order_icons_mobile'] ) ) ) ? maybe_unserialize( $option5['sfsi_order_icons_mobile'] ) : array();

		$iconDesktopData                       = array();
		$iconDesktopData['iconName']           = 'custom';
		$iconDesktopData['display']            = true;
		$iconDesktopData['customElementIndex'] = $key;

		$iconMobileData                       = array();
		$iconMobileData['iconName']           = 'custom';
		$iconMobileData['display']            = true;
		$iconMobileData['customElementIndex'] = $key;

		$iconDesktopData['index'] = key( array_slice( $desktopIconOrder, - 1, true ) ) + 1;
		$iconMobileData['index']  = key( array_slice( $mobileIconOrder, - 1, true ) ) + 1;

		$desktopIconOrder[] = $iconDesktopData;
		$mobileIconOrder[]  = $iconMobileData;

		$option5['sfsi_order_icons_desktop'] = serialize( $desktopIconOrder );
		$option5['sfsi_order_icons_mobile']  = serialize( $mobileIconOrder );

		update_option( 'sfsi_premium_section5_options', serialize( $option5 ) );
	}
}

function sfsi_plus_remove_custom_icon_in_order( $arrData, $customElementIndexToMatch ) {

	if ( isset( $arrData ) && ! empty( $arrData ) && is_array( $arrData ) ) {

		$arrIndexKey = array();

		foreach ( $arrData as $index => $iconData ) :

			if (
				'custom' == $iconData['iconName'] && isset( $iconData['customElementIndex'] )

				&& $iconData['customElementIndex'] == $customElementIndexToMatch
			) {

				array_push( $arrIndexKey, $index );
			}

		endforeach;

		if ( ! empty( $arrIndexKey ) ) {

			foreach ( $arrIndexKey as $value ) :

				unset( $arrData[ $value ] );

			endforeach;
		}

		$arrData = array_values( $arrData );

		return $arrData;
	}
}

function sfsi_plus_remove_custom_icon_in_order_desktop_and_mobile( $key ) {

	if ( isset( $key ) && false !== $key ) {

		$option5 = ( get_option( 'sfsi_premium_section5_options', false ) ) ? maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) ) : '';

		$desktopIconOrder = ( is_array( maybe_unserialize( $option5['sfsi_order_icons_desktop'] ) ) ) ? maybe_unserialize( $option5['sfsi_order_icons_desktop'] ) : array();

		$mobileIconOrder = ( is_array( maybe_unserialize( $option5['sfsi_order_icons_mobile'] ) ) ) ? maybe_unserialize( $option5['sfsi_order_icons_mobile'] ) : array();

		$desktopIconOrder = sfsi_plus_remove_custom_icon_in_order( $desktopIconOrder, $key );
		$mobileIconOrder  = sfsi_plus_remove_custom_icon_in_order( $mobileIconOrder, $key );

		$option5['sfsi_order_icons_desktop'] = serialize( $desktopIconOrder );
		$option5['sfsi_order_icons_mobile']  = serialize( $mobileIconOrder );

		update_option( 'sfsi_premium_section5_options', serialize( $option5 ) );
	}
}

function sfsi_plus_remove_custom_icon_mouseover_icon( $customIconIndex ) {

	// Removing saved icons Question 4-> Mouse-Over effects -> Show other icons on mouse-over       
	$option3 = ( get_option( 'sfsi_premium_section3_options', false ) ) ? maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) ) : array();

	$arrMouseoverIcons = isset( $option3['sfsi_plus_mouseOver_other_icon_images'] ) ? maybe_unserialize( $option3['sfsi_plus_mouseOver_other_icon_images'] ) : array();

	if ( ( - 1 == $customIconIndex ) && isset( $arrMouseoverIcons['custom'] ) && ! empty( $arrMouseoverIcons['custom'] )
	     && is_array( $arrMouseoverIcons['custom'] )
	) {

		unset( $arrMouseoverIcons['custom'][ $customIconIndex ] );

		$option3['sfsi_plus_mouseOver_other_icon_images']['custom'] = $arrMouseoverIcons['custom'];

		update_option( 'sfsi_premium_section3_options', serialize( $option3 ) );
	}
}

function sfsi_plus_image_create_gd_lib( $imageExt, $imgDirPath ) {

	$createdImage = false;

	switch ( strtolower( $imageExt ) ) {
		case 'png':
			// Create a new image from file 
			$CreatedImage = imagecreatefrompng( $imgDirPath );

			break;

		case 'jpg':
		case 'jpeg':
		case 'pjpeg':

			$CreatedImage = imagecreatefromjpeg( $imgDirPath );

			break;
	}

	return $createdImage;
}

/*  This function resize gif image without loosing its animation.
	Dependency: Image Magick php extension
	https://www.inmotionhosting.com/support/website/software/is-imagemagick-installed-on-the-servers
	https://www.web-development-blog.com/archives/image-manipulations-with-imagemagick/
*/

function sfsi_plus_resize_gif( $srcImageDirPath, $width, $height, $outImageDirPath ) {

	$imageCroppedResult          = new stdClass();
	$imageCroppedResult->status  = false;
	$imageCroppedResult->message = __( 'Something went wrong', 'ultimate-social-media-plus' );

	if (
		isset( $srcImageDirPath ) && ! empty( $srcImageDirPath ) && isset( $width ) && ! empty( $width )
		&& isset( $height ) && ! empty( $height ) && isset( $outImageDirPath ) && ! empty( $outImageDirPath )
	) {
		if ( extension_loaded( 'imagick' ) ) {

			try {

				exec( "convert " . $srcImageDirPath . " -coalesce -repage 0x0 -resize " . $width . "x" . $height . " -layers Optimize " . $outImageDirPath, $output, $ret );

				if ( isset( $ret ) && file_exists( $outImageDirPath ) ) {
					$imageCroppedResult->status  = true;
					$imageCroppedResult->message = "";
				}
			} //catch exception
			catch ( Exception $e ) {
				$imageCroppedResult->message = $e->getMessage();
			}
		} else {
			$imageCroppedResult->message = __( 'Image Magick extension is required for cropping gif images', 'ultimate-social-media-plus' );
		}
	}

	return $imageCroppedResult;
}


/*  This function will proportionally resize image */
function sfsiplusresizeImage( $CurWidth, $CurHeight, $MaxSize, $DestFolder, $SrcImage, $Quality, $ImageType ) {
	/* Check Image size is not 0 */
	if ( $CurWidth <= 0 || $CurHeight <= 0 ) {
		return false;
	}
	/* Construct a proportional size of new image */
	$ImageScale = min( $MaxSize / $CurWidth, $MaxSize / $CurHeight );
	$NewWidth   = ceil( $ImageScale * $CurWidth );
	$NewHeight  = ceil( $ImageScale * $CurHeight );
	$NewCanves  = imagecreatetruecolor( $NewWidth, $NewHeight );

	/* Resize Image */
	if ( imagecopyresampled( $NewCanves, $SrcImage, 0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight ) ) {
		return $ImageType;
		switch ( strtolower( $ImageType ) ) {
			case 'image/png':
				imagepng( $NewCanves, $DestFolder );
				break;
			case 'image/gif':
				imagegif( $NewCanves, $DestFolder );
				break;
			case 'image/jpg':
				imagejpeg( $NewCanves, $DestFolder, $Quality );
				break;
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg( $NewCanves, $DestFolder, $Quality );
				break;
			default:
				return false;
		}
		/* Destroy image, frees memory	*/
		if ( is_resource( $NewCanves ) ) {
			imagedestroy( $NewCanves );
		}

		return true;
	}
}

/* This function corps image to create exact square images, no matter what its original size! */
function sfsiplus_cropImage( $CurWidth, $CurHeight, $iSize, $DestFolder, $SrcImage, $Quality, $ImageType ) {
	//Check Image size is not 0
	if ( $CurWidth <= 0 || $CurHeight <= 0 ) {
		return false;
	}

	if ( $CurWidth > $CurHeight ) {
		$y_offset    = 0;
		$x_offset    = ( $CurWidth - $CurHeight ) / 2;
		$square_size = $CurWidth - ( $x_offset * 2 );
	} else {
		$x_offset    = 0;
		$y_offset    = ( $CurHeight - $CurWidth ) / 2;
		$square_size = $CurHeight - ( $y_offset * 2 );
	}

	$NewCanves = imagecreatetruecolor( $iSize, $iSize );
	imagealphablending( $NewCanves, false );
	imagesavealpha( $NewCanves, true );
	$white         = imagecolorallocate( $NewCanves, 255, 255, 255 );
	$alpha_channel = imagecolorallocatealpha( $NewCanves, 255, 255, 255, 127 );
	imagecolortransparent( $NewCanves, $alpha_channel );
	$maketransparent = imagecolortransparent( $NewCanves, $white );
	imagefill( $NewCanves, 0, 0, $maketransparent );

	/*
	 * Change offset for increase image quality ($x_offset, $y_offset)
	 * imagecopyresampled($NewCanves, $SrcImage,0, 0, $x_offset, $y_offset, $iSize, $iSize, $square_size, $square_size)
	 */
	if ( imagecopyresampled( $NewCanves, $SrcImage, 0, 0, 0, 0, $iSize, $iSize, $square_size, $square_size ) ) {
		imagesavealpha( $NewCanves, true );
		switch ( strtolower( $ImageType ) ) {
			case 'image/png':
				imagepng( $NewCanves, $DestFolder );
				break;
			case 'image/gif':
				imagegif( $NewCanves, $DestFolder );
				break;
			case 'image/jpg':
				imagejpeg( $NewCanves, $DestFolder, $Quality );
				break;
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg( $NewCanves, $DestFolder, $Quality );
				break;
			default:
				return false;
		}

		/* Destroy image, frees memory	*/
		if ( is_resource( $NewCanves ) ) {
			imagedestroy( $NewCanves );
		}

		return true;
	} else {
		return false;
	}
}

add_action( 'wp_ajax_sfsi_plus_deleteWebChatFollow', 'sfsi_premium_deleteWebChatFollow' );
function sfsi_premium_deleteWebChatFollow() {
	if ( ! wp_verify_nonce( $_POST['nonce'], "sfsi_plus_deleteWebChatFollow" ) ) {
		echo json_encode( array( 'res' => 'wrong_nonce' ) );
		exit;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		echo json_encode( array( __( 'You should be admin to take this action', 'ultimate-social-media-plus' ) ) );
		exit;
	}
	$option2 = ( get_option( 'sfsi_premium_section2_options', false ) ) ? maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) ) : '';
	if ( is_array( $option2 ) ) {
		$option2["sfsi_premium_wechat_scan_image"] = "";
		update_option( 'sfsi_premium_section2_options', serialize( $option2 ) );
		echo json_encode( array(
			'res'     => 'success',
			'message' => __( 'removed the scan image for wechat', 'ultimate-social-media-plus' )
		) );
	} else {
		echo json_encode( array(
			'res'     => 'failed',
			'message' => __( 'Couldn\'t load options', 'ultimate-social-media-plus' )
		) );
	}
	die;
}
