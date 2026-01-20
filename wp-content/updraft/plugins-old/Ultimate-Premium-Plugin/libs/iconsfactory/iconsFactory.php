<?php
function sfsi_plus_get_icon_image( $icon_name, $iconImgName = false ) {

	$icon = false;

	$option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );

	if ( isset( $option3['sfsi_plus_actvite_theme'] ) && ! empty( $option3['sfsi_plus_actvite_theme'] ) ) {

		$active_theme = $option3['sfsi_plus_actvite_theme'];

		$icons_baseUrl  = SFSI_PLUS_PLUGURL . "images/icons_theme/" . $active_theme . "/";
		$visit_iconsUrl = SFSI_PLUS_PLUGURL . "images/visit_icons/";

		if ( isset( $icon_name ) && ! empty( $icon_name ) ) :

			if ( $active_theme == 'custom_support' ) {
				$custom_icon_name = "pinterest" == strtolower( $icon_name ) ? "pintrest" : $icon_name;

				$key = "plus_" . $custom_icon_name . "_skin";

				$skin = get_option( $key, false );

				$scheme = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https" : "http";

				if ( $skin ) {
					$skin_url = parse_url( $skin );
					if ( $skin_url['scheme'] === 'http' && $scheme === 'https' ) {
						$icon = str_replace( 'http', 'https', $skin );
					} else if ( $skin_url['scheme'] === 'https' && $scheme === 'http' ) {
						$icon = str_replace( 'https', 'http', $skin );
					} else {
						$icon = $skin;
					}
				} else {
					$active_theme  = 'default';
					$icons_baseUrl = SFSI_PLUS_PLUGURL . "images/icons_theme/default/";

					$iconImgName = false != $iconImgName ? $iconImgName : $icon_name;
					$icon        = $icons_baseUrl . $active_theme . "_" . $iconImgName . ".png";
				}
			} else {
				$iconImgName = false != $iconImgName ? $iconImgName : $icon_name;
				$icon        = $icons_baseUrl . $active_theme . "_" . $iconImgName . ".png";
			}

		endif;
	}

	return $icon;
}

function sfsi_plus_get_icon_mouseover_text( $icon_name, $option5 = null ) {
	$alt_text = '';
	if ( isset( $icon_name ) && ! empty( $icon_name ) ) {
		$icon_name = strtolower( $icon_name );

		$key = 'sfsi_plus_' . $icon_name . '_MouseOverText';
		if ( $option5 == null ) {
			$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
		}

		if ( isset( $option5[ $key ] ) && ! empty( $option5[ $key ] ) ) {
			$alt_text = $option5[ $key ];
		}
	}

	return $alt_text;
}

function sfsi_plus_get_back_icon_img_url( $iconName, $customIconIndex = null ) {
	$iconImgUrl = null;

	$option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );

	if ( "yes" == $option3['sfsi_plus_mouseOver'] && "other_icons" == $option3['sfsi_plus_mouseOver_effect_type'] ) {

		$arrMouseOver_other_icon_images = array();

		if ( isset( $option3['sfsi_plus_mouseOver_other_icon_images'] ) ) {

			$arrMouseOver_other_icon_images = maybe_unserialize( $option3['sfsi_plus_mouseOver_other_icon_images'] );

			if ( ! is_array( $arrMouseOver_other_icon_images ) ) {

				$arrMouseOver_other_icon_images = array();
			}
		}

		if ( ! empty( $arrMouseOver_other_icon_images ) ) {

			$dbiconImg = sfsi_get_other_icon_image( $iconName, $arrMouseOver_other_icon_images, $customIconIndex );
			if ( false != $dbiconImg ) {

				$dbiconImg = filter_var( $dbiconImg, FILTER_SANITIZE_URL );
				if ( filter_var( $dbiconImg, FILTER_VALIDATE_URL ) !== false ) {
					$iconImgUrl = $dbiconImg;
				} else {
					$iconImgUrl = SFSI_PLUS_UPLOAD_DIR_BASEURL . $dbiconImg;
				}
			} else {
				$iconImgUrl = false;
			}
		} else {
			$iconImgUrl = false;
		}
	}

	return $iconImgUrl;
}

function sfsi_plus_get_single_icon_html( $iconName, $shallAddBackIcon, $backIconImgUrl, $frontIconImgUrl, $class, $noMouseOverEffectClass, $data_effect, $new_window, $url, $icon_opacity, $sfsi_plus_icon_bgColor_style, $no_follow_attr, $alt_text, $icons_size, $border_radius, $padding_top, $mouseOver_effect_type, $custom_whatsapp_txt = false, $link = false, $title = false, $onclick = "" ) {

	$icons = "";

	if ( ! wp_is_mobile() ) {

		$imgUrl = $shallAddBackIcon ? $backIconImgUrl : $frontIconImgUrl;

		$class      = $class . " sficn sciconback " . $noMouseOverEffectClass;
		$new_window = isset( $url ) && $url != "" ? $new_window : '';
		$href       = isset( $url ) && $url != "" ? $url : '';

		/* Set back icon opacity 0 default for flip effect */
		if ( 'flip' === $mouseOver_effect_type ) {
			$icon_opacity = 0;
		}

		$customAttr = $sfsi_custom_icon_class = "";

		if ( "whatsapp" == $iconName ) {

			if (
				isset( $custom_whatsapp_txt ) && ! empty( $custom_whatsapp_txt )
				&& isset( $link ) && ! empty( $link )
				&& isset( $title ) && ! empty( $title )
			) {

				$customAttr = "data-customtxt='" . $custom_whatsapp_txt . "' data-url='" . $link . "' data-text='" . $title . "'";
			}
		}
		if ( "wechat" == $iconName && $new_window == "onclick='sfsi_plus_new_window_popup(event)'" ) {
			$new_window = "";
		}

		if ( "custom" == $iconName ) {
			$sfsi_custom_icon_class .= " sciconcustomback";
		}

		/* Check if BackIcon is custom image or not */
		if ( $shallAddBackIcon ) {
			$sfsi_custom_icon_class .= " sciconcustomback";
		}

		$icons = "<a " . $customAttr . " class='" . $class . "' data-effect='" . $data_effect . "' " . $new_window . "  href='" . $href . "' " . $onclick . " style='opacity:" . $icon_opacity . ";height: " . $icons_size . "px;width: " . $icons_size . "px;" . $sfsi_plus_icon_bgColor_style . "' " . $no_follow_attr . ">";

		$icons .= "<img nopin='nopin' alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $imgUrl . "' height='" . $icons_size . "' width='" . $icons_size . "' style='" . $border_radius . $padding_top . "' class='sfcm sfsi_premium_wicon" . $sfsi_custom_icon_class . "' data-effect='" . $data_effect . "' />";
		$icons .= '</a>';
	}

	return $icons;
}

function sfsi_plus_get_no_follow_attr( $option5 = false ) {

	$str_no_follow = '';

	if ( is_null( $option5 ) ) {
		$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
	}

	if ( isset( $option5['sfsi_plus_nofollow_links'] ) && 'yes' == $option5['sfsi_plus_nofollow_links'] ) {
		$str_no_follow = "rel='nofollow'";
	}

	return $str_no_follow;
}

function sfsi_plus_get_style_margin_for_floating_icons( $mobileFloat = false, $sfsi_section8 = false ) {

	$sfsi_section8 = false != $sfsi_section8 ? $sfsi_section8 : maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );

	$keyToAdd = "";

	if ( false != $mobileFloat ) {
		$keyToAdd = "mobile";
	}

	$iconFloatPosition = $sfsi_section8[ 'sfsi_plus_float_page_' . $keyToAdd . 'position' ];

	$styleMargin = '';

	switch ( $iconFloatPosition ) {

		case "top-left":
		case "center-top":

			$styleMargin = "margin-top:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'top' ] . "px;margin-left:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'left' ] . "px;";
			break;

		case "top-right":

			$styleMargin = "margin-top:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'top' ] . "px;margin-right:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'right' ] . "px;";
			break;

		case "center-left":

			$styleMargin = "margin-left:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'left' ] . "px;";

			break;

		case "center-right":

			$styleMargin = "margin-right:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'right' ] . "px;";

			break;

		case "bottom-left":
		case "center-bottom":

			$styleMargin = "margin-bottom:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'bottom' ] . "px;margin-left:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'left' ] . "px;";

			break;

		case "bottom-right":

			$styleMargin = "margin-bottom:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'bottom' ] . "px;margin-right:" . $sfsi_section8[ 'sfsi_plus_icons_floatMargin_' . $keyToAdd . 'right' ] . "px;";

			break;
	}

	return $styleMargin;
}

function sfsi_plus_get_float_position_alignment( $iconPosition ) {

	$objPosition = new StdClass();

	$position = $top = "";

	switch ( $iconPosition ) {
		case "top-left":
			if ( is_admin_bar_showing() ) {
				$position .= "left:30px;top:35px;";
				$top      = "35";
			} else {
				$position .= "left:10px;top:10px;";
				$top      = "10";
			}
			break;
		case "top-right":
			if ( is_admin_bar_showing() ) {
				$position .= "right:30px;top:35px;";
				$top      = "35";
			} else {
				$position .= "right:10px;top:10px;";
				$top      = "10";
			}
			break;
		case "center-right":
			$position .= "right:30px;top:50%;";
			$top      = "center";
			break;
		case "center-left":
			$position .= "left:30px;top:50%;";
			$top      = "center";
			break;
		case "center-top":
			if ( is_admin_bar_showing() ) {
				$position .= "left:50%;top:35px;";
				$top      = "35";
			} else {
				$position .= "left:50%;top:10px;";
				$top      = "10";
			}
			break;
		case "center-bottom":
			$position .= "left:50%;bottom:0px;";
			$top      = "bottom";
			break;
		case "bottom-right":
			$position .= "right:30px;bottom:0px;";
			$top      = "bottom";
			break;
		case "bottom-left":
			$position .= "left:30px;bottom:0px;";
			$top      = "bottom";
			break;
	}

	$objPosition->position = $position;
	$objPosition->top      = $top;

	return $objPosition;
}

function sfsi_plus_get_float_position_script( $iconPosition, $top, $isMobileFloat = false, $sfsi_section8 = false ) {

	$sfsi_section8 = false != $sfsi_section8 ? $sfsi_section8 : maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );

	$jquery = $keyMobile = "";

	if ( false != $isMobileFloat ) {

		$keyMobile = "mobile";
	}

	$condFloat = 'float' == $sfsi_section8[ 'sfsi_plus_make_' . $keyMobile . 'icon' ];

	if ( 'center-right' == $iconPosition || 'center-left' == $iconPosition ) {
		$jquery .= "jQuery( document ).ready(function( $ )
				  {
					var topalign = ( jQuery(window).height() - jQuery('#sfsi_plus_floater').height() ) / 2;
					jQuery('#sfsi_plus_floater').css('top',topalign);";

		if ( $condFloat ) {
			$jquery .= "sfsi_plus_float_widget('" . $top . "');";
		}

		$jquery .= "sfsi_plus_align_icons_center_orientation('" . $iconPosition . "');";
		$jquery .= "});";
	} else if ( 'center-top' == $iconPosition || 'center-bottom' == $iconPosition ) {
		$jquery .= "jQuery( document ).ready(function( $ )
				  {
					var leftalign = ( jQuery(window).width() - jQuery('#sfsi_plus_floater').width() ) / 2;
					jQuery('#sfsi_plus_floater').css('left',leftalign);";

		if ( $condFloat ) {
			$jquery .= "sfsi_plus_float_widget('" . $top . "');";
		}

		$jquery .= "sfsi_plus_align_icons_center_orientation('" . $iconPosition . "');";

		$jquery .= "});";
	} else if ( $condFloat ) {
		$jquery .= "jQuery( document ).ready(function( $ ) { sfsi_plus_float_widget('" . $top . "')});";
	}

	return $jquery;
}

/* check the icon visiblity for desktop */
function sfsi_plus_check_visiblity( $isFloter = 0, $sfsi_section8 = null ) {
	global $wpdb;
	/* Access the saved settings in database  */
	$sfsi_section1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );
	$sfsi_section3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
	$sfsi_section5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );

	//options that are added on the third question
	if ( $sfsi_section8 == null ) {
		$sfsi_section8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );
	}

	/* calculate the width and icons display alignments */
	$icons_space   = $sfsi_section5['sfsi_plus_icons_spacing'];
	$icons_size    = $sfsi_section5['sfsi_plus_icons_size'];
	$icons_per_row = ( $sfsi_section5['sfsi_plus_icons_perRow'] ) ? $sfsi_section5['sfsi_plus_icons_perRow'] : '';

	$icons_alignment = $sfsi_section5['sfsi_plus_icons_Alignment'];
	$position        = 'position:absolute;';
	$position1       = 'position:absolute;';
	$jquery          = '<script>if("undefined" !== typeof jQuery && null!= jQuery){
    setTimeout(function() {
';

	$jquery .= 'jQuery(".sfsi_plus_widget").each(function( index ) {
		if(jQuery(this).attr("data-position") == "widget")
		{
			var wdgt_hght = jQuery(this).children(".sfsiplus_norm_row.sfsi_plus_wDiv").height();
			var title_hght = jQuery(this).parent(".widget.sfsi_plus").children(".widget-title").height();
			var totl_hght = parseInt( title_hght ) + parseInt( wdgt_hght );
			jQuery(this).parent(".widget.sfsi_plus").css("min-height", totl_hght+"px");
		}
	})    },5000    );';

	/* check if icons shuffling is activated in admin or not */
	if ( $sfsi_section5['sfsi_plus_icons_stick'] == "yes" ) {
		if ( is_admin_bar_showing() ) {
			$Ictop = "30px";
		} else {
			$Ictop = "0";
		}
		$jquery .= 'var s = jQuery(".sfsi_plus_widget");
			var pos = s.position();
			jQuery(window).scroll(function(){
			undefined!==sfsi_plus_stick_widget &&sfsi_plus_stick_widget("' . $Ictop . '");
		}); ';
	}

	/* check if icons floating  is activated in admin */
	/*settings under third question*/

	$pagePosition = $sfsi_section8['sfsi_plus_float_page_position'];

	if ( isset( $sfsi_section8['sfsi_plus_float_on_page'] ) && "yes" == $sfsi_section8['sfsi_plus_float_on_page'] ) {
		$top = "15";

		$position = "position:absolute;";

		if ( $sfsi_section8['sfsi_plus_make_icon'] == 'stay_same_place' ) {
			$position = "position:fixed;";
		}

		$objPosition = sfsi_plus_get_float_position_alignment( $pagePosition );
		$position    .= $objPosition->position;
		$top         = $objPosition->top;

		$jquery .= sfsi_plus_get_float_position_script( $pagePosition, $top, false, $sfsi_section8 );
	}

	$extra = 0;

	/* built the main widget div */
	$icons = "";

	$iconsCount = 0;
	if ( sfsi_premium_is_any_standard_icon_selected() ) {

		$newDesktopIconOrder = sfsi_premium_desktop_icons_order( $sfsi_section5, $sfsi_section1 );
		$arrData             = sfsi_premium_get_icons_html( $newDesktopIconOrder, $sfsi_section1 );

		$icons      .= $arrData['html'];
		$iconsCount = $arrData['count'];
	}

	$sfsi_section8["sfsi_plus_icons_total_displaying_desktop_icons"] = $iconsCount;
	update_option( "sfsi_premium_section8_options", serialize( $sfsi_section8 ) );

	if ( isset( $sfsi_section8["sfsi_plus_icons_total_displaying_desktop_icons"] ) && ! empty( $sfsi_section8["sfsi_plus_icons_total_displaying_desktop_icons"] ) ) {
		$totalDisplayingIcons = (int) $sfsi_section8["sfsi_plus_icons_total_displaying_desktop_icons"];

		if ( $icons_per_row > $totalDisplayingIcons ) {
			$icons_per_row = $totalDisplayingIcons;
		}
	}

	if ( "yes" == $sfsi_section3['sfsi_plus_shuffle_icons'] && $icons_per_row > 1 ) {
		$shuffleFirstLoad = $sfsi_section3['sfsi_plus_shuffle_Firstload'];
		$shuffleInterval  = $sfsi_section3['sfsi_plus_shuffle_interval'];

		$shuffle_time = ( isset( $sfsi_section3['sfsi_plus_shuffle_intervalTime'] ) ) ? $sfsi_section3['sfsi_plus_shuffle_intervalTime'] : 3;
		$shuffle_time = (int) $shuffle_time * 1000;

		if ( "yes" == $shuffleFirstLoad && "yes" == $shuffleInterval ) {
			$jquery .= "jQuery( document ).ready(function( $ ) { jQuery('.sfsi_plus_wDiv').each(function(){ console.log(this); new window.Manipulator( jQuery(this)); }); sfsi_plus_shuffle_new(); setTimeout(function(){sfsi_plus_shuffle_new();  jQuery('#sfsi_plus_wDiv').each(function(){ jQuery(this).click(); })},2000);  setInterval(function(){ sfsi_plus_shuffle_new(); jQuery('#sfsi_plus_wDiv').each(function(){ jQuery(this).click(); })}," . $shuffle_time . "); });";
		} else if ( "no" == $shuffleFirstLoad && "yes" == $shuffleInterval ) {
			$jquery .= "jQuery( document ).ready(function( $ ) {  jQuery('.sfsi_plus_wDiv').each(function(){ new window.Manipulator( jQuery(this)); });  setInterval(function(){ sfsi_plus_shuffle_new(); jQuery('#sfsi_plus_wDiv').each(function(){ jQuery(this).click(); })}," . $shuffle_time . "); });";
		} else {
			$jquery .= "jQuery( document ).ready(function( $ ) {  jQuery('.sfsi_plus_wDiv').each(function(){ new window.Manipulator( jQuery(this)); });  setTimeout(function(){ sfsi_plus_shuffle_new(); jQuery('#sfsi_plus_wDiv').each(function(){ jQuery(this).click(); })},2000); });";
		}
	}
	$jquery .= "}</script>";


	/* calculate the total width of widget according to icons  */
	// if (!empty($icons_per_row)) {
	// 	$width 		= ((int) $icons_space + (int) $icons_size) * (int) $icons_per_row;
	// 	$main_width = $width = $width + $extra;
	// 	$main_width = $main_width . "px";
	// } else {
	// 	$main_width = "35%";
	// }

	$widths = sfsi_get_beforeAfterposts_icon_space_and_container_width( 'float' );
	$width  = $widths->iconcontainerwidth;
	if ( $width !== "auto" ) {
		$main_width = ( $widths->iconcontainerwidth + $extra ) . "px";
	} else {
		$main_width = $width;
	}

	$margin = $width + 11;


	/* if floating of icons is active create a floater div */
	$icons_float = '';
	$styleMargin = '';

	if ( 1 == $isFloter ) {
		if ( "yes" == $sfsi_section8['sfsi_plus_float_on_page'] ) {
			$styleMargin = sfsi_plus_get_style_margin_for_floating_icons( false, $sfsi_section8 );
			$icons_float = '<style type="text/css">#sfsi_plus_floater { ' . $styleMargin . ' }</style>';
			$icons_float .= '<amp-script width="200" height="50" script="hello-world">';

			$icons_float .= '<div class="sfsiplus_norm_row sfsi_plus_wDiv" id="sfsi_plus_floater" style="z-index: 9999;width:' . $main_width . ';text-align:' . $icons_alignment . ';' . $position . '">';
			$icons_float .= $icons;
			$icons_float .= "<input type='hidden' id='sfsi_plus_floater_sec' value='" . $sfsi_section8['sfsi_plus_float_page_position'] . "' />";
			$icons_float .= "</div>" . $jquery;
			$icons_float .= '</amp-script>';

		}
	} else {

		$icons_float = '<div class="sfsiplus_norm_row sfsi_plus_wDiv"  style="width:' . $main_width . ';text-align:' . $icons_alignment . ';' . $position1 . '">';
		$icons_float .= $icons;
		$icons_float .= '</div>';

		$icons_float .= '<div id="sfsi_holder" class="sfsi_plus_holders" style="position: relative; float: left;width:100%;z-index:-1;"></div >' . $jquery;
	}

	return $icons_float;
}

/* check the icon visiblity for mobile */
function sfsi_plus_check_mobile_visiblity( $isFloter = 0, $sfsi_section8 = null ) {
	global $wpdb;
	/* Access the saved settings in database  */
	$sfsi_premium_section1_options = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );
	$sfsi_section3                 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
	$sfsi_section5                 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
	//options that are added on the third question
	if ( $sfsi_section8 == null ) {
		$sfsi_section8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );
	}

	/* calculate the width and icons display alignments */
	if ( $sfsi_section5['sfsi_plus_mobile_icon_setting'] == 'yes' ) {
		$icons_space = ( ! empty( $sfsi_section5['sfsi_plus_icons_mobilespacing'] ) )
			? $sfsi_section5['sfsi_plus_icons_mobilespacing']
			: 5;
		$icons_size  = ( ! empty( $sfsi_section5['sfsi_plus_icons_mobilesize'] ) )
			? $sfsi_section5['sfsi_plus_icons_mobilesize']
			: 30;
	} else {
		$icons_space = ( ! empty( $sfsi_section5['sfsi_plus_icons_spacing'] ) )
			? $sfsi_section5['sfsi_plus_icons_spacing']
			: 5;
		$icons_size  = ( ! empty( $sfsi_section5['sfsi_plus_icons_size'] ) )
			? $sfsi_section5['sfsi_plus_icons_size']
			: 30;
	}

	if ( $sfsi_section5['sfsi_plus_mobile_icon_alignment_setting'] == 'yes' ) {
		$icons_per_row = ( $sfsi_section5['sfsi_plus_mobile_icons_perRow'] ) ? $sfsi_section5['sfsi_plus_mobile_icons_perRow'] : '';
	} else {
		$icons_per_row = ( $sfsi_section5['sfsi_plus_icons_perRow'] ) ? $sfsi_section5['sfsi_plus_icons_perRow'] : '';
	}

	$icons_alignment = $sfsi_section5['sfsi_plus_icons_Alignment'];
	$position        = 'position:absolute;';
	$position1       = 'position:absolute;';
	$jquery          = '<script>';

	$jquery .= 'jQuery(".sfsi_plus_widget").each(function( index ) {
		if(jQuery(this).attr("data-position") == "widget")
		{
			var wdgt_hght = jQuery(this).children(".sfsiplus_norm_row.sfsi_plus_wDiv").height();
			var title_hght = jQuery(this).parent(".widget.sfsi_plus").children(".widget-title").height();
			var totl_hght = parseInt( title_hght ) + parseInt( wdgt_hght );
			jQuery(this).parent(".widget.sfsi_plus").css("min-height", totl_hght+"px");
		}
	});';

	/* check if icons shuffling is activated in admin or not */
	if ( $sfsi_section5['sfsi_plus_icons_stick'] == "yes" ) {
		if ( is_admin_bar_showing() ) {
			$Ictop = "30px";
		} else {
			$Ictop = "0";
		}
		$jquery .= 'var s = jQuery(".sfsi_plus_widget");
			var pos = s.position();
			jQuery(window).scroll(function(){
			undefined!==sfsi_plus_stick_widget && sfsi_plus_stick_widget("' . $Ictop . '");
		}); ';
	}

	/* check if icons floating  is activated in admin */
	/*settings under third question*/
	if ( $sfsi_section8['sfsi_plus_float_on_page'] == "yes" ) {
		$top = "15";

		if ( $sfsi_section8['sfsi_plus_mobile_float'] == 'yes' ) {
			$position = "position:absolute;";

			if ( 'stay_same_place' == $sfsi_section8['sfsi_plus_make_mobileicon'] ) {
				$position = "position:fixed;";
			}

			$iconMPosition = $sfsi_section8['sfsi_plus_float_page_mobileposition'];

			$objPosition = sfsi_plus_get_float_position_alignment( $iconMPosition );
			$position    .= $objPosition->position;
			$top         = $objPosition->top;

			$jquery .= sfsi_plus_get_float_position_script( $iconMPosition, $top, true, $sfsi_section8 );
		} else {
			$position = "position:absolute;";

			if ( 'stay_same_place' == $sfsi_section8['sfsi_plus_make_icon'] ) {
				$position = "position:fixed;";
			}

			$iconPosition = $sfsi_section8['sfsi_plus_float_page_position'];

			$objPosition = sfsi_plus_get_float_position_alignment( $iconPosition );
			$position    .= $objPosition->position;
			$top         = $objPosition->top;

			$jquery .= sfsi_plus_get_float_position_script( $iconPosition, $top, false, $sfsi_section8 );;
		}
	}
	$extra = 0;

	if ( "yes" == $sfsi_section3['sfsi_plus_shuffle_icons'] ) {
		if ( $sfsi_section3['sfsi_plus_shuffle_Firstload'] == "yes" && $sfsi_section3['sfsi_plus_shuffle_interval'] == "yes" ) {
			$shuffle_time = ( isset( $sfsi_section3['sfsi_plus_shuffle_intervalTime'] ) ) ? $sfsi_section3['sfsi_plus_shuffle_intervalTime'] : 3;
			$shuffle_time = $shuffle_time * 1000;
			$jquery       .= "jQuery( document ).ready(function( $ ) {  jQuery('.sfsi_plus_wDiv').each(function(){ new window.Manipulator( jQuery(this)); });  setTimeout(function(){  jQuery('#sfsi_plus_wDiv').each(function(){ jQuery(this).click(); })},2000);  setInterval(function(){  jQuery('#sfsi_plus_wDiv').each(function(){ jQuery(this).click(); })}," . $shuffle_time . "); });";
		} else if ( $sfsi_section3['sfsi_plus_shuffle_Firstload'] == "no" && $sfsi_section3['sfsi_plus_shuffle_interval'] == "yes" ) {
			$shuffle_time = ( isset( $sfsi_section3['sfsi_plus_shuffle_intervalTime'] ) ) ? $sfsi_section3['sfsi_plus_shuffle_intervalTime'] : 3;
			$shuffle_time = $shuffle_time * 1000;
			$jquery       .= "jQuery( document ).ready(function( $ ) {  jQuery('.sfsi_plus_wDiv').each(function(){ new window.Manipulator( jQuery(this)); });  setInterval(function(){  jQuery('#sfsi_plus_wDiv').each(function(){ jQuery(this).click(); })}," . $shuffle_time . "); });";
		} else {
			$jquery .= "jQuery( document ).ready(function( $ ) {  jQuery('.sfsi_plus_wDiv').each(function(){ new window.Manipulator( jQuery(this)); });  setTimeout(function(){  jQuery('#sfsi_plus_wDiv').each(function(){ jQuery(this).click(); })},2000); });";
		}
	}
	$jquery .= "</script>";


	$icons      = "";
	$iconsCount = 0;

	if ( sfsi_premium_is_any_standard_icon_selected() ) {

		$arrOrderIcons = sfsi_premium_get_icons_order( $sfsi_section5, $sfsi_premium_section1_options );
		$arrData       = sfsi_premium_get_icons_html( $arrOrderIcons, $sfsi_premium_section1_options );

		$icons      .= $arrData['html'];
		$iconsCount = $arrData['count'];
	}

	$final_total_icons                                              = ( $icons_per_row > $iconsCount ) ? $iconsCount : $icons_per_row;
	$sfsi_section8["sfsi_plus_icons_total_displaying_mobile_icons"] = $final_total_icons;
	update_option( "sfsi_premium_section8_options", serialize( $sfsi_section8 ) );

	if ( isset( $sfsi_section8["sfsi_plus_icons_total_displaying_mobile_icons"] ) && ! empty( $sfsi_section8["sfsi_plus_icons_total_displaying_mobile_icons"] ) ) {
		$totalDisplayingIcons = (int) $sfsi_section8["sfsi_plus_icons_total_displaying_mobile_icons"];

		if ( $icons_per_row > $totalDisplayingIcons ) {
			$icons_per_row = $totalDisplayingIcons;
		}
	}

	/* calculate the total width of widget according to icons  */
	// if (!empty($icons_per_row)) {
	// 	$width = ((int) $icons_space + (int) $icons_size) * (int) $icons_per_row;
	// 	$main_width = $width = $width + $extra;
	// 	$main_width = $main_width . "px";
	// 	// var_dump($main_width);
	// } else {
	// 	$main_width = "35%";
	// }
	$widths = sfsi_get_beforeAfterposts_icon_space_and_container_width( 'float' );
	$width  = $widths->iconcontainerwidth;

	if ( $width !== "auto" ) {
		$main_width = ( $widths->iconcontainerwidth + $extra ) . "px";
	} else {
		$main_width = $width;
	}

	/* built the main widget div */
	$margin = $width + 11;

	/* if floating of icons is active create a floater div */
	$icons_float = '';

	if ( "yes" == $sfsi_section8['sfsi_plus_float_on_page'] && $isFloter == 1 ) {
		if ( $sfsi_section8['sfsi_plus_mobile_float'] == 'yes' ) {
			$mPagePosition = $sfsi_section8['sfsi_plus_float_page_mobileposition'];

			$styleMargin = '';

			$styleMargin = sfsi_plus_get_style_margin_for_floating_icons( true, $sfsi_section8 );

			$icons_float = '<style type="text/css">#sfsi_plus_floater { ' . ( isset( $styleMargin ) ? $styleMargin : '' ) . ' }</style>';
			$icons_float .= '<amp-script width="200" height="50" script="hello-world">';

			$icons_float .= '<div class="sfsiplus_norm_row sfsi_plus_wDiv" id="sfsi_plus_floater"  style="z-index: 999999;width:' . $main_width . ';text-align:' . $icons_alignment . ';' . $position . '">';
			$icons_float .= $icons;
			$icons_float .= "<input type='hidden' id='sfsi_plus_floater_sec' value='" . $sfsi_section8['sfsi_plus_float_page_mobileposition'] . "' />";
			$icons_float .= "</div>" . $jquery;
			$icons_float .= '</amp-script>';

		} else {
			$styleMargin = '';
			$styleMargin = sfsi_plus_get_style_margin_for_floating_icons( false );
			$icons_float = '<style type="text/css">#sfsi_plus_floater { ' . ( isset( $styleMargin ) ? $styleMargin : '' ) . ' }</style>';
			$icons_float .= '<amp-script width="200" height="50" script="hello-world">';
			$icons_float .= '<div class="sfsiplus_norm_row sfsi_plus_wDiv22 sfsi_plus_mobile_floater" id="sfsi_plus_floater"  style="z-index: 999999;width:' . $main_width . ';text-align:' . $icons_alignment . ';' . $position . '">';
			$icons_float .= $icons;
			$icons_float .= "<input type='hidden' id='sfsi_plus_floater_sec' value='" . $sfsi_section8['sfsi_plus_float_page_position'] . "' />";
			$icons_float .= "</div>" . $jquery;
			$icons_float .= '</amp-script>';
		}
	} else {
		$icons_float = '<div class="sfsiplus_norm_row sfsi_plus_wDiv"  style="width:' . $main_width . ';text-align:' . $icons_alignment . ';' . $position1 . '">';
		$icons_float .= $icons;
		$icons_float .= '</div >';

		$icons_float .= '<div id="sfsi_holder" class="sfsi_plus_holders" style="position: relative; float: left;width:100%;z-index:-1;"></div >' . $jquery;
	}

	$icons_data = $icons_float;

	return $icons_data;
}

/* make all icons with saved settings in admin */
function sfsi_plus_prepairIcons( $icon_name, $is_front = 0, $onpost = "no", $fromPost = null ) {
	global $wpdb;
	global $socialObj;
	global $post;

	$socialObj = new sfsi_plus_SocialHelper(); /* global object to access 3rd party icon's actions */

	$mouse_hover_effect          = '';
	$active_theme                = 'official';
	$sfsi_plus_shuffle_Firstload = 'no';
	$sfsi_plus_display_counts    = "no";

	$icon = $url = $alt_text = $new_window = $class = '';

	/* access all saved settings in admin */
	$option1 = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );
	$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );
	$option3 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
	$option4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
	$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
	$option6 = maybe_unserialize( get_option( 'sfsi_premium_section6_options', false ) );
	$option7 = maybe_unserialize( get_option( 'sfsi_premium_section7_options', false ) );
	$option8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );

	$customIcons = array();

	if ( isset( $option1['sfsi_custom_files'] ) && ! empty( $option1['sfsi_custom_files'] ) && is_string( $option1['sfsi_custom_files'] ) ) {
		$customIcons = maybe_unserialize( $option1['sfsi_custom_files'] );
		$customIcons = is_array( $customIcons ) ? array_filter( $customIcons ) : $customIcons;
	}

	$minCountToDisplayCountBox = isset( $option4['sfsi_plus_min_display_counts'] ) ? $option4['sfsi_plus_min_display_counts'] : 1;

	/* get active theme */
	$border_radius = '';
	$active_theme  = $option3['sfsi_plus_actvite_theme'];


	/* shuffle effect */
	if ( $option3['sfsi_plus_shuffle_icons'] == 'yes' ) {
		$sfsi_plus_shuffle_Firstload = $option3["sfsi_plus_shuffle_Firstload"];

		if ( $option3["sfsi_plus_shuffle_interval"] == "yes" ) {
			$sfsi_plus_shuffle_interval = $option3["sfsi_plus_shuffle_intervalTime"];
		}
	}

	/* define the main url for icon access */
	$icons_baseUrl  = SFSI_PLUS_PLUGURL . "images/icons_theme/" . $active_theme . "/";
	$visit_iconsUrl = SFSI_PLUS_PLUGURL . "images/visit_icons/";
	$share_iconsUrl = SFSI_PLUS_PLUGURL . "images/share_icons/";

	$hoverSHow = 0;

	/* check is icon is a custom icon or default icon */
	$icon_n = null;

	if ( is_numeric( $icon_name ) ) {
		$icon_n    = $icon_name;
		$icon_name = "custom";
	}

	$counts      = $twit_tolCls = $twt_margin = $padding_top = '';
	$icons_space = $option5['sfsi_plus_icons_spacing'];

	$scheme = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https" : "http";
	//$current_url = $scheme.'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	$post_id = $socialObj->sfsi_get_the_ID();

	if ( $fromPost == 'yes' && ! empty( $post_id ) ) {
		$current_url = urldecode( get_permalink( $post_id ) );
	} else {
		$current_url = urldecode( sfsi_plus_current_url() );
	}
	if ( sfsi_premium_is_site_url( $current_url ) && isset( $option5['sfsi_premium_static_path'] ) && $option5['sfsi_premium_static_path'] !== "" ) {
		$current_url = $option5['sfsi_premium_static_path'] . strrev( str_replace( strrev( site_url() ), '', strrev( $current_url ) ) );
	}
	$url            = "";
	$cmcls          = '';
	$toolClass      = '';
	$icons_language = $option5['sfsi_plus_icons_language'];
	if ( $icons_language == 'ar' ) {
		$icons_language = 'ar_AR';
	}
	if ( $icons_language == "ja" ) {
		$icons_language = "ja_JP";
	}
	if ( $icons_language == "el" ) {
		$icons_language = "el_GR";
	}
	if ( $icons_language == "fi" ) {
		$icons_language = "fi_FI";
	}
	if ( $icons_language == "th" ) {
		$icons_language = "th_TH";
	}
	if ( $icons_language == "vi" ) {
		$icons_language = "vi_VN";
	}
	if ( "automatic" == $icons_language ) {
		if ( function_exists( 'icl_object_id' ) && has_filter( 'wpml_current_language' ) ) {
			$icons_language = apply_filters( 'wpml_current_language', null );
			if ( ! empty( $icons_language ) ) {
				$icons_language = sfsi_premium_wordpress_locale_from_locale_code( $icons_language );
			}
		} else {
			$icons_language = get_locale();
		}
	}

	/* For Flat icons bg color */
	$sfsi_plus_icon_bgColor = $sfsi_plus_icon_bgColor_style = '';

	/* Get show count*/
	$sfsi_section8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );

	if ( $sfsi_section8['sfsi_plus_icons_DisplayCounts'] == "yes" ) {
		$show_count = 1;
	} else {
		$show_count = 0;
	}

	switch ( $icon_name ) {
		case "rss":
			$url                  = ( $option2['sfsi_plus_rss_url'] ) ? $option2['sfsi_plus_rss_url'] : '';
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				$option4['sfsi_plus_rss_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_rss_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "rss" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "rss" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_rss_bgColor'] ) && $option3['sfsi_plus_rss_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_rss_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#FF9845';
				}
			}

			break;

		case "email":

			$hoverdiv = '';

			if ( isset( $option2['sfsi_plus_email_icons_functions'] ) && $option2['sfsi_plus_email_icons_functions'] == 'sf' ) {
				$url = ( isset( $option2['sfsi_plus_email_url'] ) )
					? $option2['sfsi_plus_email_url']
					: 'https://follow.it/now';
			} elseif ( isset( $option2['sfsi_plus_email_icons_functions'] ) && $option2['sfsi_plus_email_icons_functions'] == 'contact' ) {
				$url = ( isset( $option2['sfsi_plus_email_icons_contact'] ) && ! empty( $option2['sfsi_plus_email_icons_contact'] ) )
					? "mailto:" . $option2['sfsi_plus_email_icons_contact']
					: '';
			} elseif ( isset( $option2['sfsi_plus_email_icons_functions'] ) && $option2['sfsi_plus_email_icons_functions'] == 'page' ) {
				$url = ( isset( $option2['sfsi_plus_email_icons_pageurl'] ) && ! empty( $option2['sfsi_plus_email_icons_pageurl'] ) )
					? $option2['sfsi_plus_email_icons_pageurl']
					: '';
			} elseif ( isset( $option2['sfsi_plus_email_icons_functions'] ) && $option2['sfsi_plus_email_icons_functions'] == 'share_email' ) {
				$subject = stripslashes( $option2['sfsi_plus_email_icons_subject_line'] );
				$subject = str_replace( '${title}', $socialObj->sfsi_get_the_title(), $subject );
				$subject = str_replace( '"', '', str_replace( "'", '', $subject ) );
				$subject = html_entity_decode( strip_tags( $subject ), ENT_QUOTES, 'UTF-8' );
				$subject = str_replace( "%26%238230%3B", "...", $subject );
				$subject = rawurlencode( $subject );

				$body = stripslashes( $option2['sfsi_plus_email_icons_email_content'] );
				$body = str_replace( '${title}', $socialObj->sfsi_get_the_title(), $body );
				$body = str_replace( '${link}', $socialObj->sfsi_get_custom_share_link( 'email' ), $body );
				$body = str_replace( '"', '', str_replace( "'", '', $body ) );
				$body = html_entity_decode( strip_tags( $body ), ENT_QUOTES, 'UTF-8' );
				$body = str_replace( "%26%238230%3B", "...", $body );
				$body = rawurlencode( $body );
				$url  = "mailto:?subject=$subject&body=$body";
			} else {
				$url = ( isset( $option2['sfsi_plus_email_url'] ) )
					? $option2['sfsi_plus_email_url']
					: 'https://follow.it/now';
			}

			/*elseif(isset($option2['sfsi_plus_email_icons_functions']) && $option2['sfsi_plus_email_icons_functions'] == 'newsletter')
			{
				$url = '';
				$newsletterSubscription = 'mailchimp';
			}*/


			$toolClass            = "email_tool_bdr";
			$arsfsiplus_row_class = "bot_eamil_arow";

			/* fecth no of counts if active in admin section */
			if (
				$option4['sfsi_plus_email_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				if ( $option4['sfsi_plus_email_countsFrom'] == "manual" ) {
					$counts = $option4['sfsi_plus_email_manualCounts'];
				} else {
					$counts = $socialObj->SFSI_getFeedSubscriber( sanitize_text_field( get_option( 'sfsi_premium_feed_id', false ) ) );
				}
				$counts = (string) $counts;
			}


			$alt_text = sfsi_plus_get_icon_mouseover_text( "email" );

			//Custom Skin Support {Monad}
			if ( $active_theme == 'custom_support' ) {
				if ( get_option( "plus_email_skin" ) ) {
					$icon = get_option( "plus_email_skin" );
				} else {
					$active_theme  = 'default';
					$icons_baseUrl = SFSI_PLUS_PLUGURL . "images/icons_theme/default/";

					if ( $option2['sfsi_plus_rss_icons'] == "sfsi" ) {
						$icon = $icons_baseUrl . $active_theme . "_sf.png";
					} elseif ( $option2['sfsi_plus_rss_icons'] == "email" ) {
						$icon = $icons_baseUrl . $active_theme . "_email.png";
					} else {
						$icon = $icons_baseUrl . $active_theme . "_subscribe.png";
					}
				}
			} else {
				if ( $option2['sfsi_plus_rss_icons'] == "sfsi" ) {
					$icon = $icons_baseUrl . $active_theme . "_sf.png";
				} elseif ( $option2['sfsi_plus_rss_icons'] == "email" ) {
					$icon = $icons_baseUrl . $active_theme . "_email.png";
				} else {
					$icon = $icons_baseUrl . $active_theme . "_subscribe.png";
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_email_bgColor'] ) && $option3['sfsi_plus_email_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_email_bgColor'];
				} else {
					if ( $option2['sfsi_plus_rss_icons'] == "sfsi" ) {
						$sfsi_plus_icon_bgColor = '#05B04E';
					} elseif ( $option2['sfsi_plus_rss_icons'] == "email" ) {
						$sfsi_plus_icon_bgColor = '#343D44';
					} else {
						$sfsi_plus_icon_bgColor = '#16CB30';
					}
				}
			}

			break;

		case "facebook":
			$width                = 62;
			$totwith              = $width + 28 + $icons_space;
			$twt_margin           = $totwith / 2;
			$toolClass            = "sfsi_plus_fb_tool_bdr";
			$arsfsiplus_row_class = "bot_fb_arow";

			/* check for the over section */
			$alt_text = sfsi_plus_get_icon_mouseover_text( "facebook" );

			$facebook_icons_lang = $option5['sfsi_plus_facebook_icons_language'];
			if ( "automatic_visit_us" == $facebook_icons_lang || "automatic_visit_me" == $facebook_icons_lang ) {
				if ( function_exists( 'icl_object_id' ) && has_filter( 'wpml_current_language' ) ) {
					$icon_me_or_us       = $facebook_icons_lang;
					$facebook_icons_lang = apply_filters( 'wpml_current_language', null );

					if ( ! empty( $facebook_icons_lang ) ) {
						$facebook_icons_lang = sfsi_premium_map_language_values( $facebook_icons_lang, $icon_me_or_us );
					}
				} else {
					$facebook_icons_lang = get_locale();
				}
			}
			$visit_icon_svg = SFSI_PLUS_DOCROOT . '/images/visit_icons/Visit_us_fb/icon_' . $facebook_icons_lang . '.svg';
			$visit_icon_png = SFSI_PLUS_DOCROOT . '/images/visit_icons/Visit_us_fb/icon_' . $facebook_icons_lang . '.png';

			if ( file_exists( $visit_icon_png ) ) {
				$visit_icon = $visit_iconsUrl . "Visit_us_fb/icon_" . $facebook_icons_lang . ".png";
			} elseif ( file_exists( $visit_icon_svg ) ) {
				$visit_icon = $visit_iconsUrl . "Visit_us_fb/icon_" . $facebook_icons_lang . ".svg";
			} else {
				$visit_icon = $visit_iconsUrl . "fb.png";
			}

			$url = ( "yes" == $option2['sfsi_plus_facebookPage_option'] && $option2['sfsi_plus_facebookPage_url'] ) ? $option2['sfsi_plus_facebookPage_url'] : '';

			// Start facbook options count
			$fbOptionsCount = 0;

			$fbOptionsCount = "yes" == $option2['sfsi_plus_facebookPage_option'] ? $fbOptionsCount + 1 : $fbOptionsCount;
			$fbOptionsCount = "yes" == $option2['sfsi_plus_facebookLike_option'] ? $fbOptionsCount + 1 : $fbOptionsCount;
			//$fbOptionsCount = "yes" == $option2['sfsi_plus_facebookFollow_option'] ? $fbOptionsCount+1 : $fbOptionsCount;
			$fbOptionsCount = "yes" == $option2['sfsi_plus_facebookShare_option'] ? $fbOptionsCount + 1 : $fbOptionsCount;

			if ( 1 == $fbOptionsCount ) {

				// Only single option is active, dont's show tooltip For Visit, Follow & Share

				if ( "yes" == $option2['sfsi_plus_facebookPage_option'] ) {
					$customShare = true;
					$shareUrl    = ( $option2['sfsi_plus_facebookPage_url'] ) ? $option2['sfsi_plus_facebookPage_url'] : '';
				}
				// if("yes" == $option2['sfsi_plus_facebookFollow_option']){
				// 	$customShare =  true;
				// 	$shareUrl 	 =	($option2['sfsi_plus_facebookProfile_url']) ? $option2['sfsi_plus_facebookProfile_url']: '';
				// }


				if ( "yes" == $option2['sfsi_plus_facebookShare_option'] ) {

					/*$hoverSHow	= 1;
				 	$hoverdiv	= '';

					$current_url = $socialObj->sfsi_get_custom_share_link('facebook');
					$hoverdiv.="<div class='icon3'>".$socialObj->sfsiFB_Share($current_url)."</div>";*/

					$customShare = true;
					$current_url = $socialObj->sfsi_get_custom_share_link( 'facebook' );

					//For gutenberg blocks use request URL {Mukesh}
					if ( '' !== $current_url ) {
						if ( isset( $_GET['url'] ) ) {
							$current_url = $_GET['url'];
						}
					}

					$shareUrl = "https://www.facebook.com/sharer/sharer.php?u=" . ( ( strpos( $current_url, '?' ) == false ) ? trailingslashit( $current_url ) : $current_url );
				}

				// Show facebook option in tooltip
				if ( "yes" == $option2['sfsi_plus_facebookLike_option'] ) {


					$hoverSHow = 1;
					$hoverdiv  = '';

					if ( $option5['sfsi_plus_Facebook_linking'] == "facebookcustomurl" ) {
						$userDefineLink = ( $option5['sfsi_plus_facebook_linkingcustom_url'] );
						$hoverdiv       .= "<div class='icon2'>" . $socialObj->sfsi_plus_FBlike( $userDefineLink, $show_count ) . "</div>";
					} else {
						$current_url = $socialObj->sfsi_get_custom_share_link( 'facebook' );
						$hoverdiv    .= "<div class='icon2'>" . $socialObj->sfsi_plus_FBlike( $current_url, $show_count ) . "</div>";
					}
				}
			} // fb option count is greater than two, show options in tooltip
			else {

				$hoverSHow = 1;
				$hoverdiv  = '';

				if ( $option2['sfsi_plus_facebookPage_option'] == "yes" ) {
					$hoverdiv .= "<div class='icon1'><a href='" . $url . "' " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";
				}
				if ( $option2['sfsi_plus_facebookLike_option'] == "yes" ) {
					if ( $option5['sfsi_plus_Facebook_linking'] == "facebookcustomurl" ) {
						$userDefineLink = ( $option5['sfsi_plus_facebook_linkingcustom_url'] );
						$hoverdiv       .= "<div class='icon2'>" . $socialObj->sfsi_plus_FBlike( $userDefineLink, $show_count ) . "</div>";
					} else {
						$current_url = $socialObj->sfsi_get_custom_share_link( 'facebook' );
						$hoverdiv    .= "<div class='icon2'>" . $socialObj->sfsi_plus_FBlike( $current_url, $show_count ) . "</div>";
					}
				}
				if ( $option2['sfsi_plus_facebookShare_option'] == "yes" ) {
					/* $option5['sfsiSocialtTitleTxt'] */
					$current_url = $socialObj->sfsi_get_custom_share_link( 'facebook' );
					$hoverdiv    .= "<div class='icon3'>" . $socialObj->sfsiFB_Share_Custom( $current_url ) . "</div>";
				}

				// if($option2['sfsi_plus_facebookFollow_option'] == "yes")
				// {
				// //  $hoverdiv.="<div class='icon4'>".$socialObj->sfsiFB_Follow($profileUrl)."</div>";
				// $url = ($option2['sfsi_plus_facebookProfile_url'])    ? $option2['sfsi_plus_facebookProfile_url'] : '';
				// }
			}


			if ( $option4['sfsi_plus_facebook_countsDisplay'] == "yes" && $option4['sfsi_plus_display_counts'] == "yes" ) {
				if ( $option4['sfsi_plus_facebook_countsFrom'] == "manual" ) {
					$counts = $option4['sfsi_plus_facebook_manualCounts'];
				} else if ( $option4['sfsi_plus_facebook_countsFrom'] == "likes" ) {
					$counts = $socialObj->sfsi_get_fb( $current_url );
				} else if ( $option4['sfsi_plus_facebook_countsFrom'] == "followers" ) {
					$counts = $socialObj->sfsi_get_fb( $current_url );
				} else if ( $option4['sfsi_plus_facebook_countsFrom'] == "mypage" ) {
					$current_url = $option4['sfsi_plus_facebook_mypageCounts'];
					$counts      = $socialObj->sfsi_get_fb_pagelike( $current_url );
				}
			}

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "facebook", "fb" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_facebook_bgColor'] ) && $option3['sfsi_plus_facebook_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_facebook_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#336699';
				}
			}

			break;

		case "twitter":
			$toolClass            = "sfsi_plus_twt_tool_bdr";
			$arsfsiplus_row_class = "bot_twt_arow";

			$url = ( $option2['sfsi_plus_twitter_pageURL'] ) ? $option2['sfsi_plus_twitter_pageURL'] : '';

			// changes aboutPageText get from question 6
			$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
			$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );

			$twitter_user = $option2['sfsi_plus_twitter_followUserName'];
			// $twitter_text = $option5['sfsi_plus_twitter_aboutPageText'];

			$twitter_text = $socialObj->sfsi_get_custom_tweet_text();

			$width      = 59;
			$totwith    = $width + 28 + $icons_space;
			$twt_margin = $totwith / 2;

			/* check for icons to display */
			$hoverdiv = '';

			$twitter_icons_lang = $option5['sfsi_plus_twitter_icons_language'];
			if ( "automatic_visit_us" == $twitter_icons_lang || "automatic_visit_me" == $twitter_icons_lang ) {
				if ( function_exists( 'icl_object_id' ) && has_filter( 'wpml_current_language' ) ) {
					$icon_me_or_us      = $twitter_icons_lang;
					$twitter_icons_lang = apply_filters( 'wpml_current_language', null );
					if ( ! empty( $twitter_icons_lang ) ) {
						$twitter_icons_lang = sfsi_premium_map_language_values( $twitter_icons_lang, $icon_me_or_us );
					}
				} else {
					$twitter_icons_lang = get_locale();
				}
			}
			$visit_icon        = SFSI_PLUS_DOCROOT . '/images/visit_icons/Visit_us_twitter/icon_' . $twitter_icons_lang . '.png';
			$tweet_icon        = SFSI_PLUS_PLUGURL . 'images/share_icons/Twitter_Tweet/' . $icons_language . '_Tweet.svg';
			$tweet_follow_icon = SFSI_PLUS_PLUGURL . 'images/share_icons/Twitter_Follow/' . $icons_language . '_Follow.svg';

			if ( file_exists( $visit_icon ) ) {
				$visit_icon = $visit_iconsUrl . "Visit_us_twitter/icon_" . $twitter_icons_lang . ".png";
			} else {
				$visit_icon = $visit_iconsUrl . "twitter.png";
			}

			if ( $icons_language == 'nn_NO' ) {
				$icons_language = 'no';
			}

			// **************** Get value tweetAboutPage from Question 2  STARTS *****************//
			$tweetAboutPage = 'no';

			if ( isset( $option2['sfsi_plus_twitter_aboutPage'] ) ) {
				$tweetAboutPage = sanitize_text_field( $option2['sfsi_plus_twitter_aboutPage'] );
			}

			// **************** Get value tweetAboutPage from Question 2 CLOSES *****************//
			if ( $option2['sfsi_plus_twitter_page'] != "yes" && $option2['sfsi_plus_twitter_followme'] != "yes" && $tweetAboutPage == "yes" ) {
				$customShare  = true;
				$twitter_text = urlencode( $twitter_text );
				$shareUrl     = "https://x.com/intent/post?text=" . $twitter_text . "&url=";
			} elseif ( $option2['sfsi_plus_twitter_followme'] == "yes" || $tweetAboutPage == "yes" ) {
				$hoverSHow  = 1;
				$follow_usr = $option2['sfsi_plus_twitter_followUserName'];
				$followurl  = 'https://twitter.com/intent/user?screen_name=' . $follow_usr;
				//Visit twitter page {Monad}
				if ( $option2['sfsi_plus_twitter_page'] == "yes" ) {
					$hoverdiv .= "<div class='cstmicon1 '><a href='" . $url . "' " . sfsi_plus_checkNewWindow( $url ) . "><img nopin=nopin class='sfsi_premium_wicon' alt='Visit Us' title='Visit Us' src='" . $visit_icon . "' /></a></div>";
				}
				if ( $option2['sfsi_plus_twitter_followme'] == "yes" && ! empty( $twitter_user ) ) {
					$hoverdiv .= "<div class='icon1'><a href='" . $followurl . "' " . sfsi_plus_checkNewWindow( $url ) . "><img nopin=nopin src='" . $tweet_follow_icon . "' class='sfsi_premium_wicon' alt='Follow Me' title='Follow Me' ></a></div>";
				}
				if ( $tweetAboutPage == "yes" ) {
					// $current_url = $socialObj->sfsi_get_custom_share_link('twitter');
					// var_dump($twitter_text,$option2['sfsi_plus_twitter_page'],$option2['sfsi_plus_twitter_followme'],$tweetAboutPage,$current_url);die();
// var_dump()
					$hoverdiv .= "<div class='icon2'><a href='https://x.com/intent/post?text=" . urlencode( $twitter_text ) . "' " . sfsi_plus_checkNewWindow( $url ) . "><img nopin=nopin class='sfsi_premium_wicon' src='" . $tweet_icon . "' alt='Tweet' title='Tweet' ></a></div>";
				}
			}

			/* fecth no of counts if active in admin section */
			if ( $option4['sfsi_plus_twitter_countsDisplay'] == "yes" && $option4['sfsi_plus_display_counts'] == "yes" ) {
				if ( $option4['sfsi_plus_twitter_countsFrom'] == "manual" ) {
					$counts = $option4['sfsi_plus_twitter_manualCounts'];
				} else if ( $option4['sfsi_plus_twitter_countsFrom'] == "source" ) {
					$tw_settings = array(
						'sfsiplus_tw_consumer_key'              => $option4['sfsiplus_tw_consumer_key'],
						'sfsiplus_tw_consumer_secret'           => $option4['sfsiplus_tw_consumer_secret'],
						'sfsiplus_tw_oauth_access_token'        => $option4['sfsiplus_tw_oauth_access_token'],
						'sfsiplus_tw_oauth_access_token_secret' => $option4['sfsiplus_tw_oauth_access_token_secret']
					);

					$counts = $socialObj->sfsi_get_tweets( $twitter_user, $tw_settings );
				}
			}

			//Giving alternative text to image
			$alt_text = sfsi_plus_get_icon_mouseover_text( "twitter" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "twitter" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_twitter_bgColor'] ) && $option3['sfsi_plus_twitter_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_twitter_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#000000';
				}
			}

			break;
		case "threads":
			$toolClass            = "sfsi_plus_twt_tool_bdr";
			$arsfsiplus_row_class = "bot_twt_arow";

			if ( $option2['sfsi_plus_threadsShare_option'] == "yes" ) {
				$url = "https://www.threads.net/intent/post?text=Check%20out%20this%20amazing%20article!&url={$current_url}";
			}


			// changes aboutPageText get from question 6
			$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
			$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );

			$width      = 59;
			$totwith    = $width + 28 + $icons_space;
			$twt_margin = $totwith / 2;

			//Giving alternative text to image
			$alt_text = sfsi_plus_get_icon_mouseover_text( "threads" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "threads" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_threads_bgColor'] ) && $option3['sfsi_plus_threads_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_threads_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#252525';
				}
			}

			/* fecth no of counts if active in admin section */
			if ( $option4['sfsi_plus_threads_countsDisplay'] == "yes" && $option4['sfsi_plus_display_counts'] == "yes" ) {
					$counts = $option4['sfsi_plus_threads_manualCounts'];
			}


			break;

		case "bluesky":
			$toolClass            = "sfsi_plus_twt_tool_bdr";
			$arsfsiplus_row_class = "bot_twt_arow";

			if ( $option2['sfsi_plus_blueskyShare_option'] == "yes" ) {
				$url = "https://bsky.app/intent/compose?text=Check%20out%20this%20amazing%20article!%20{$current_url}";
			}


			// changes aboutPageText get from question 6
			$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
			$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );

			$width      = 59;
			$totwith    = $width + 28 + $icons_space;
			$twt_margin = $totwith / 2;

			//Giving alternative text to image
			$alt_text = sfsi_plus_get_icon_mouseover_text( "bluesky" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "bluesky" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_bluesky_bgColor'] ) && $option3['sfsi_plus_bluesky_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_bluesky_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#1185fe';
				}
			}
			/* fecth no of counts if active in admin section */
			if ( $option4['sfsi_plus_bluesky_countsDisplay'] == "yes" && $option4['sfsi_plus_display_counts'] == "yes" ) {
                $counts = $option4['sfsi_plus_bluesky_manualCounts'];
			}

			break;

		case "share":
			$url   = ""; //"http://www.addthis.com/bookmark.php?v=250";
			$class = "addthis_button";

			/*fecth no of counts if active in admin section */
			if ( $option4['sfsi_plus_shares_countsDisplay'] == "yes" && $option4['sfsi_plus_display_counts'] == "yes" ) {
				if ( $option4['sfsi_plus_shares_countsFrom'] == "manual" ) {
					$counts = $option4['sfsi_plus_shares_manualCounts'];
				} else if ( $option4['sfsi_plus_shares_countsFrom'] == "shares" ) {
					$counts = $socialObj->sfsi_get_atthis();
				}
			}

			//Giving alternative text to image
			if ( ! empty( $option5['sfsi_plus_share_MouseOverText'] ) ) {
				$alt_text = $option5['sfsi_plus_share_MouseOverText'];
			} else {
				$alt_text = __( 'SHARE', 'ultimate-social-media-plus' );
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "share" );
			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "share" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_share_bgColor'] ) && $option3['sfsi_plus_share_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_share_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#26AD62';
				}
			}

			break;

		case "youtube":
			$toolClass            = "utube_tool_bdr";
			$arsfsiplus_row_class = "bot_utube_arow";

			$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );

			$width        = 96;
			$totwith      = $width + 28 + $icons_space;
			$twt_margin   = $totwith / 2;
			$youtube_user = ( isset( $option4['sfsi_plus_youtube_user'] ) && ! empty( $option4['sfsi_plus_youtube_user'] ) ) ? $option4['sfsi_plus_youtube_user'] : 'follow.it';
			$visit_icon   = $visit_iconsUrl . "youtube.png";

			$url = ( $option2['sfsi_plus_youtube_pageUrl'] ) ? $option2['sfsi_plus_youtube_pageUrl'] : '';

			//Giving alternative text to image
			$alt_text           = sfsi_plus_get_icon_mouseover_text( "youtube" );
			$youtube_icons_lang = ( $option5['sfsi_plus_youtube_icons_language'] );
			if ( "automatic_visit_us" == $youtube_icons_lang || "automatic_visit_me" == $youtube_icons_lang ) {
				if ( function_exists( 'icl_object_id' ) && has_filter( 'wpml_current_language' ) ) {
					$icon_me_or_us      = $youtube_icons_lang;
					$youtube_icons_lang = apply_filters( 'wpml_current_language', null );
					if ( ! empty( $youtube_icons_lang ) ) {
						$youtube_icons_lang = sfsi_premium_map_language_values( $youtube_icons_lang, $icon_me_or_us );
					}
				} else {
					$youtube_icons_lang = get_locale();
				}
			}

			$visit_icon = SFSI_PLUS_DOCROOT . '/images/visit_icons/Visit_us_youtube/icon_' . $youtube_icons_lang . '.svg';

			if ( file_exists( $visit_icon ) ) {
				$visit_icon = $visit_iconsUrl . "Visit_us_youtube/icon_" . $youtube_icons_lang . ".svg";
			} else {
				$visit_icon = $visit_iconsUrl . "youtube.png";
			}

			/* check for icons to display */
			$hoverdiv = "";

			if ( $option2['sfsi_plus_youtube_follow'] == "yes" ) {
				$hoverSHow = 1;

				if ( $option2['sfsi_plus_youtube_page'] == "yes" ) {
					$hoverdiv .= "<div class='icon1'><a href='" . $url . "'  " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";
				}
				if ( $option2['sfsi_plus_youtube_follow'] == "yes" ) {
					$hoverdiv .= "<div class='icon2' data-channel='" . $youtube_user . "'>" . $socialObj->sfsi_YouTubeSub( $youtube_user ) . "</div>";
				}
			}

			/* fecth no of counts if active in admin section */
			if ( $option4['sfsi_plus_youtube_countsDisplay'] == "yes" && $option4['sfsi_plus_display_counts'] == "yes" ) {
				if ( $option4['sfsi_plus_youtube_countsFrom'] == "manual" ) {
					$counts = $option4['sfsi_plus_youtube_manualCounts'];
				} else if ( $option4['sfsi_plus_youtube_countsFrom'] == "subscriber" ) {
					$counts = $socialObj->sfsi_get_youtube( $youtube_user );
				}
			}

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "youtube" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_youtube_bgColor'] ) && $option3['sfsi_plus_youtube_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_youtube_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = 'linear-gradient(141.52deg, #E02F2F 14.26%, #E02F2F 48.98%, #C92A2A 49.12%, #C92A2A 85.18%)';
				}
			}

			break;

		case "linkedin":

			$width                = 66;
			$toolClass            = "sfsi_plus_linkedin_tool_bdr";
			$arsfsiplus_row_class = "bot_linkedin_arow";
			$linkedIn_compayId    = $option2['sfsi_plus_linkedin_followCompany'];
			$linkedIn_compay      = $option2['sfsi_plus_linkedin_followCompany'];
			$linkedIn_ProductId   = $option2['sfsi_plus_linkedin_recommendProductId'];
			$linkedIn_icons_lang  = isset( $option5['sfsi_plus_linkedin_icons_language'] ) ? $option5['sfsi_plus_linkedin_icons_language'] : 'en_US';
			if ( "automatic_visit_us" == $linkedIn_icons_lang || "automatic_visit_me" == $linkedIn_icons_lang ) {
				if ( function_exists( 'icl_object_id' ) && has_filter( 'wpml_current_language' ) ) {
					$icon_me_or_us       = $linkedIn_icons_lang;
					$linkedIn_icons_lang = apply_filters( 'wpml_current_language', null );
					if ( ! empty( $linkedIn_icons_lang ) ) {
						$linkedIn_icons_lang = sfsi_premium_wordpress_locale_from_locale_code( $linkedIn_icons_lang, $icon_me_or_us );
					}
				} else {
					$linkedIn_icons_lang = get_locale();
				}
			}
			$visit_icon = $visit_iconsUrl . "Visit_us_linkedin/icon_" . $linkedIn_icons_lang . ".svg";

			$linkedin_share_icon = SFSI_PLUS_PLUGURL . "images/share_icons/Linkedin_Share/" . $icons_language . "_share.svg";


			$alt_text = sfsi_plus_get_icon_mouseover_text( "linkedin" );
			/*check for icons to display */
			$url = ( $option2['sfsi_plus_linkedin_pageURL'] ) ? $option2['sfsi_plus_linkedin_pageURL'] : '';


			if (
				$option2['sfsi_plus_linkedin_page'] != "yes" &&
				$option2['sfsi_plus_linkedin_follow'] != "yes" &&
				$option2['sfsi_plus_linkedin_recommendBusines'] != "yes" &&
				$option2['sfsi_plus_linkedin_SharePage'] == "yes"
			) {
				$customShare = true;
				$shareUrl    = "http://www.linkedin.com/shareArticle?mini=true&url=" . urlencode( $current_url );
			} elseif (
				$option2['sfsi_plus_linkedin_follow'] == "yes" ||
				$option2['sfsi_plus_linkedin_SharePage'] == "yes" ||
				$option2['sfsi_plus_linkedin_recommendBusines'] == "yes"
			) {
				$hoverSHow = 1;
				$hoverdiv  = '';
				if ( $option2['sfsi_plus_linkedin_page'] == "yes" ) {
					$hoverdiv .= "<div class='icon4'><a href='" . $url . "' " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' style='width: 100%;' /></a></div>";
				}
				if ( $option2['sfsi_plus_linkedin_follow'] == "yes" ) {
					$hoverdiv .= "<div class='icon1'>" . $socialObj->sfsi_LinkedInFollow( $linkedIn_compayId ) . "</div>";
				}
				if ( $option2['sfsi_plus_linkedin_SharePage'] == "yes" ) {
					$current_url = $socialObj->sfsi_get_custom_share_link( 'linkedin' );
					$hoverdiv    .= "<div class='icon2'><a href='https://www.linkedin.com/shareArticle?url=" . urlencode( $current_url ) . "'  " . sfsi_plus_checkNewWindow( $url ) . " ><img class='sfsi_premium_wicon' nopin=nopin alt='Share' title='Share' src='" . $linkedin_share_icon . "' /></a></div>";
				}
				if ( $option2['sfsi_plus_linkedin_recommendBusines'] == "yes" ) {
					$hoverdiv .= "<div class='icon3'>" . $socialObj->sfsi_LinkedInRecommend( $linkedIn_compay, $linkedIn_ProductId ) . "</div>";
					$width    = 99;
				}
			}

			/* fecth no of counts if active in admin section */
			/*if(
					$fromPost == 'yes' && !empty($post) &&
					$option4['sfsi_plus_linkedIn_countsDisplay']=="yes" &&
					$option4['sfsi_plus_display_counts']=="yes"
				)
				{
					$followers=$socialObj->sfsi_get_linkedin($current_url);
					$counts=$socialObj->format_num($followers);
					if(empty($counts))
					{
						$counts = (string) "0";
					}
				}
				else
				{ */
			if (
				$option4['sfsi_plus_linkedIn_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				if ( $option4['sfsi_plus_linkedIn_countsFrom'] == "manual" ) {
					$counts = $option4['sfsi_plus_linkedIn_manualCounts'];
				} else if ( $option4['sfsi_plus_linkedIn_countsFrom'] == "follower" ) {
					$linkedIn_compay = $option4['sfsi_plus_ln_company'];
					$ln_settings     = array(
						'sfsi_plus_ln_api_key'          => $option4['sfsi_plus_ln_api_key'],
						'sfsi_plus_ln_secret_key'       => $option4['sfsi_plus_ln_secret_key'],
						'sfsi_plus_ln_oAuth_user_token' => $option4['sfsi_plus_ln_oAuth_user_token']
					);

					$counts = $socialObj->sfsi_getlinkedin_follower( $linkedIn_compay, $ln_settings );
				}
			}
			/*}*/

			$totwith    = $width + 28 + $icons_space;
			$twt_margin = $totwith / 2;

			//Giving alternative text to image
			if ( ! empty( $option5['sfsi_plus_linkedIn_MouseOverText'] ) ) {
				$alt_text = $option5['sfsi_plus_linkedIn_MouseOverText'];
			} else {
				$alt_text = "";
			}

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "linkedin" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_linkedin_bgColor'] ) && $option3['sfsi_plus_linkedin_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_linkedin_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#0877B5';
				}
			}

			break;

		case "pinterest":
			$width                = 73;
			$totwith              = $width + 28 + $icons_space;
			$twt_margin           = $totwith / 2;
			$toolClass            = "sfsi_plus_printst_tool_bdr";
			$arsfsiplus_row_class = "bot_pintst_arow";

			$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );

			$pinterest_user = ( isset( $option4['sfsi_plus_pinterest_user'] ) )
				? $option4['sfsi_plus_pinterest_user'] : '';

			$pinterest_board = ( isset( $option4['sfsi_plus_pinterest_board'] ) )
				? $option4['sfsi_plus_pinterest_board'] : '';

			$visit_icon = $visit_iconsUrl . "pinterest.png";

			$pinterest_save = SFSI_PLUS_PLUGURL . 'images/share_icons/Pinterest_Save/' . $icons_language . '_save.svg';


			$url = ( isset( $option2['sfsi_plus_pinterest_pageUrl'] ) ) ? $option2['sfsi_plus_pinterest_pageUrl'] : '';

			//Giving alternative text to image
			$alt_text = esc_attr( sfsi_plus_get_icon_mouseover_text( "pinterest" ) );

			/* check for icons to display */
			$hoverdiv = "";
			if ( $option2['sfsi_plus_pinterest_pingBlog'] == "yes" && $option2['sfsi_plus_pinterest_page'] == "yes" ) {
				$hoverSHow = 1;

				$hoverdiv               .= "<div class='icon1'><a href='" . $url . "' " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";
				$sfsi_plus_SocialHelper = new sfsi_plus_SocialHelper();
				$media                  = $sfsi_plus_SocialHelper->sfsi_pinit_image();
				$description            = $sfsi_plus_SocialHelper->sfsi_pinit_description();
				$description            = str_replace( "&quot;", '"', $description );
				$encoded_description    = urlencode( $description );
				$encoded_description    = str_replace( "+", "%20", $encoded_description );
				// var_dump('pindesc',$description,$option5['sfsi_premium_pinterest_sharing_texts_and_pics'],"https://pinterest.com/pin/create/?url=" . urlencode($current_url) . "&media=" . urlencode($media) . "&description=" . ($encoded_description));
				$description_escaped = $encoded_description;
				// var_dump($option5['sfsi_plus_social_sharing_options']);

				if ( $option5['sfsi_premium_pinterest_sharing_texts_and_pics'] === "yes" ) {
					$hoverdiv .= "<div class='icon2'><a class ='sfsi_premium_pinterest_create' onclick='sfsi_premium_pinterest_modal_images(\"" . urlencode( $current_url ) . "\",\"" . ( $description_escaped ) . "\")'><img class='sfsi_premium_wicon' data-pin-nopin='true'  nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $pinterest_save . "' /></a></div>";
				} else {
					if ( $media !== '' ) {
						$hoverdiv .= "<div class='icon2'><a data-pin-custom='true' style='cursor:pointer' href='https://pinterest.com/pin/create/button/?url=" . urlencode( $current_url ) . "&media=" . urlencode( $media ) . "&description=" . ( $encoded_description ) . "'" . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $pinterest_save . "' /></a></div>";
					} else {
						$hoverdiv .= "<div class='icon2'><a class ='sfsi_premium_pinterest_create' onclick='sfsi_premium_pinterest_modal_images(\"" . urlencode( $current_url ) . "\",\"" . ( $description_escaped ) . "\")'><img class='sfsi_premium_wicon' data-pin-nopin='true'  nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $pinterest_save . "' /></a></div>";
					}
				}
			}
			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_pinterest_countsDisplay'] ) &&
				$option4['sfsi_plus_pinterest_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				if ( $option4['sfsi_plus_pinterest_countsFrom'] == "manual" ) {
					$counts = $option4['sfsi_plus_pinterest_manualCounts'];
				} else {
					$pins   = $socialObj->sfsi_get_pinterest( $current_url );
					$counts = ( empty( $pins ) ) ? (string) "0" : $pins;
				}
			}

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "pinterest" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_pinterest_bgColor'] ) && $option3['sfsi_plus_pinterest_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_pinterest_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#CC3333';
				}
			}

			break;

		case "instagram":
			$toolClass            = "instagram_tool_bdr";
			$arsfsiplus_row_class = "bot_pintst_arow";
			$url                  = ( isset( $option2['sfsi_plus_instagram_pageUrl'] ) ) ? $option2['sfsi_plus_instagram_pageUrl'] : '';
			$instagram_user_name  = $option4['sfsi_plus_instagram_User'];

			$hoverdiv = "";
			/* fecth no of counts if active in admin section */
			if ( $option4['sfsi_plus_instagram_countsDisplay'] == "yes" && $option4['sfsi_plus_display_counts'] == "yes" ) {
				if ( $option4['sfsi_plus_instagram_countsFrom'] == "manual" ) {
					$counts = $option4['sfsi_plus_instagram_manualCounts'];
				} else if ( $option4['sfsi_plus_instagram_countsFrom'] == "followers" ) {
					$counts = $socialObj->sfsi_get_instagramFollowers( $instagram_user_name );

					$counts = $socialObj->format_num_back( $counts );

				}
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "instagram" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "instagram" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_instagram_bgColor'] ) && $option3['sfsi_plus_instagram_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_instagram_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#336699';
				}
			}

			break;

		case "ria":
			$url                  = isset( $option2['sfsi_plus_ria_pageUrl'] ) ? $option2['sfsi_plus_ria_pageUrl'] : '';
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_ria_countsDisplay'] ) &&
				$option4['sfsi_plus_ria_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_ria_manualCounts'];
			}


			$alt_text = sfsi_plus_get_icon_mouseover_text( "ria" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "ria" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_ria_bgColor'] ) && $option3['sfsi_plus_ria_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_ria_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#10A9A0';
				}
			}

			break;

		case "inha":
			$url                  = isset( $option2['sfsi_plus_inha_pageUrl'] ) ? $option2['sfsi_plus_inha_pageUrl'] : '';
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_inha_countsDisplay'] ) &&
				$option4['sfsi_plus_inha_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_inha_manualCounts'];
			}


			$alt_text = sfsi_plus_get_icon_mouseover_text( "inha" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "inha" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_inha_bgColor'] ) && $option3['sfsi_plus_inha_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_inha_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#348cbc';
				}
			}
			break;

		case "houzz":

			$url                  = '';
			$toolClass            = "sfsi_plus_houzz_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_houzz_countsDisplay'] ) &&
				$option4['sfsi_plus_houzz_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_houzz_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "houzz" );

			$icon       = sfsi_plus_get_icon_image( "houzz" );
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";

			$post_title = $socialObj->sfsi_get_the_title();


			// $isVisitActive =  isset($option2['sfsi_plus_houzzVisit_option'])
			// 	&& !empty($option2['sfsi_plus_houzzVisit_option'])
			// 	&& "yes" == $option2['sfsi_plus_houzzVisit_option']
			// 	&& isset($option2['sfsi_plus_houzz_pageUrl']) && !empty($option2['sfsi_plus_houzz_pageUrl']);

			//if($isVisitActive){

			$url = $option2['sfsi_plus_houzz_pageUrl'];

			//}
			//  $isSharingActive = isset($option2['sfsi_plus_houzzShare_option'])
			// 		&& !empty($option2['sfsi_plus_houzzShare_option'])
			// 		&& ("yes" == $option2['sfsi_plus_houzzShare_option'])
			// 		&& isset($option2['sfsi_plus_houzz_websiteId'])
			// 		&& !empty($option2['sfsi_plus_houzz_websiteId']);


			// 	$hoverSHow = 1;
			// 	$hoverdiv  = "";

			// if(
			//  	("yes" == $option2['sfsi_plus_houzzVisit_option'])
			//  	/*&& ("yes" == $option2['sfsi_plus_houzzShare_option'])*/
			// ){


			// 	if(isset($option2['sfsi_plus_houzz_pageUrl']) && !empty($option2['sfsi_plus_houzz_pageUrl'])){

			// 		$visitUrl = $option2['sfsi_plus_houzz_pageUrl'];

			// 		$hoverdiv.="<div class='icon1'><a href='".$visitUrl."'  ".sfsi_plus_checkNewWindow($visitUrl)."><img nopin=nopin alt='".$alt_text."' title='".$alt_text."' src='".$visit_icon."' /></a></div>";
			// 	}

			// 	if($isSharingActive){
			// 		$houzz_websiteId = $option2['sfsi_plus_houzz_websiteId'];
			// 		$hoverdiv.="<div class='icon2'>".$socialObj->get_houzz_save_button($current_url,$houzz_websiteId)."</div>";
			// 	}

			// }
			// else{
			//  		$hoverSHow = 1;

			// 		if($isSharingActive){
			// 			$houzz_websiteId = $option2['sfsi_plus_houzz_websiteId'];
			// 			$hoverdiv.="<div class='icon2'>".$socialObj->get_houzz_save_button($current_url,$houzz_websiteId)."</div>";
			// 		}
			// 		else if($isVisitActive){

			// 			$visitUrl = $option2['sfsi_plus_houzz_pageUrl'];

			// 			$hoverdiv.="<div class='icon1'><a href='".$visitUrl."'  ".sfsi_plus_checkNewWindow($visitUrl)."><img nopin=nopin alt='".$alt_text."' title='".$alt_text."' src='".$visit_icon."' /></a></div>";
			// 		}
			// }

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_houzz_bgColor'] ) && $option3['sfsi_plus_houzz_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_houzz_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#7BC043';
				}
			}

			break;

		case "snapchat":
			$url                  = ( $option2['sfsi_plus_snapchat_pageUrl'] ) ? $option2['sfsi_plus_snapchat_pageUrl'] : '';
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_snapchat_countsDisplay'] ) &&
				$option4['sfsi_plus_snapchat_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_snapchat_manualCounts'];
			}


			$alt_text = sfsi_plus_get_icon_mouseover_text( "snapchat" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "snapchat" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_snapchat_bgColor'] ) && $option3['sfsi_plus_snapchat_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_snapchat_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#EDEC1F';
				}
			}

			break;

		case "whatsapp":
			if ( ! is_null( $post ) ) {
				$sfsi_premium_current_url = get_permalink( $post->ID );
			} else {
				global $wp;
				$sfsi_premium_current_url = home_url( $wp->request );
			}
			if ( ( $option2['sfsi_plus_whatsapp_url_type'] == 'message' ) && ( $option2['sfsi_plus_my_whatsapp_number'] == '' ) ) {
				$msg = ! empty( $option2['sfsi_plus_whatsapp_message'] ) ? $option2['sfsi_plus_whatsapp_message'] : "Hey, I like your website";
				$msg = stripslashes( $msg );
				$msg = str_replace( '${title}', $socialObj->sfsi_get_the_title(), $msg );
				$msg = str_replace( '${link}', trailingslashit( $sfsi_premium_current_url ), $msg );
				$msg = str_replace( '"', '', str_replace( "'", '', $msg ) );
				$msg = html_entity_decode( strip_tags( $msg ), ENT_QUOTES, 'UTF-8' );
				$msg = rawurlencode( $msg );
				$msg = str_replace( "%26%238230%3B", "...", $msg );
				$url = 'https://api.whatsapp.com/send?text=' . $msg;
			} elseif ( $option2['sfsi_plus_whatsapp_url_type'] == 'message' ) {
				$msg = ! empty( $option2['sfsi_plus_whatsapp_message'] ) ? $option2['sfsi_plus_whatsapp_message'] : 'Hey, I like your website';
				$msg = stripslashes( $msg );
				$msg = str_replace( '${title}', $socialObj->sfsi_get_the_title(), $msg );
				$msg = str_replace( '${link}', trailingslashit( $sfsi_premium_current_url ), $msg );
				$msg = str_replace( '"', '', str_replace( "'", '', $msg ) );
				$msg = html_entity_decode( strip_tags( $msg ), ENT_QUOTES, 'UTF-8' );
				$msg = rawurlencode( $msg );
				$msg = str_replace( "%26%238230%3B", "...", $msg );
				$url = 'https://api.whatsapp.com/send?text=' . $msg . '&phone=' . $option2['sfsi_plus_my_whatsapp_number'];
			} elseif ( $option2['sfsi_plus_whatsapp_url_type'] == 'share_page' ) {
				$url = ! empty( $option2['sfsi_plus_whatsapp_share_page'] ) ? $option2['sfsi_plus_whatsapp_share_page'] : '${title} ${link}';
				$url = stripslashes( $option2['sfsi_plus_whatsapp_share_page'] );
				$url = str_replace( '${title}', get_the_title( $post->ID ), $url );
				$url = str_replace( '${link}', $sfsi_premium_current_url, $url );
				$url = str_replace( "'", '', str_replace( '"', '', $url ) );

				$url = 'https://api.whatsapp.com/send?text=' . $url;
			} else {
				$url = '';
			}

			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_whatsapp_countsDisplay'] ) &&
				$option4['sfsi_plus_whatsapp_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_whatsapp_manualCounts'];
			}


			$alt_text = sfsi_plus_get_icon_mouseover_text( "whatsapp" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "whatsapp" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_whatsapp_bgColor'] ) && $option3['sfsi_plus_whatsapp_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_whatsapp_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#3ED946';
				}
			}

			break;

		case "skype":

			$url = '';

			if ( $option2['sfsi_plus_skype_options'] == "call" && isset( $option2['sfsi_plus_skype_pageUrl'] ) ) {
				$url = "skype:" . $option2['sfsi_plus_skype_pageUrl'] . "?call";
			} else if ( $option2['sfsi_plus_skype_options'] == "chat" && isset( $option2['sfsi_plus_skype_pageUrl'] ) ) {
				$url = "skype:" . $option2['sfsi_plus_skype_pageUrl'] . "?chat";
			}

			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_skype_countsDisplay'] ) &&
				$option4['sfsi_plus_skype_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_skype_manualCounts'];
			}


			$alt_text = sfsi_plus_get_icon_mouseover_text( "skype" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "skype" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_skype_bgColor'] ) && $option3['sfsi_plus_skype_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_skype_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#00A9F1';
				}
			}

			break;

		case "phone":
			if ( isset( $option2['sfsi_plus_whatsapp_number'] ) && "" !== $option2['sfsi_plus_whatsapp_number'] ) {
				$url = 'tel:' . $option2['sfsi_plus_whatsapp_number'];
			} else {
				$url = '';
			}

			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_phone_countsDisplay'] ) &&
				$option4['sfsi_plus_phone_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_phone_manualCounts'];
			}


			$alt_text = sfsi_plus_get_icon_mouseover_text( "phone" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "phone" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_phone_bgColor'] ) && $option3['sfsi_plus_phone_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_phone_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#51AD47';
				}
			}

			break;

		case "vimeo":

			$url                  = ( $option2['sfsi_plus_vimeo_pageUrl'] ) ? $option2['sfsi_plus_vimeo_pageUrl'] : '';
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_vimeo_countsDisplay'] ) &&
				$option4['sfsi_plus_vimeo_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_vimeo_manualCounts'];
			}


			$alt_text = sfsi_plus_get_icon_mouseover_text( "vimeo" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "vimeo" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_vimeo_bgColor'] ) && $option3['sfsi_plus_vimeo_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_vimeo_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#1AB7EA';
				}
			}

			break;

		case "soundcloud":
			$url                  = ( $option2['sfsi_plus_soundcloud_pageUrl'] ) ? $option2['sfsi_plus_soundcloud_pageUrl'] : '';
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_soundcloud_countsDisplay'] ) &&
				$option4['sfsi_plus_soundcloud_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_soundcloud_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "soundcloud" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "soundcloud" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_soundcloud_bgColor'] ) && $option3['sfsi_plus_soundcloud_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_soundcloud_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#FF541C';
				}
			}

			break;

		case "yummly":

			$toolClass = "sfsi_plus_yummly_tool_bdr";
			$hoverdiv  = '';

			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_yummly_countsDisplay'] ) &&
				$option4['sfsi_plus_yummly_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				if ( $option4['sfsi_plus_yummly_countsFrom'] == 'manual' ) {
					$counts = $option4['sfsi_plus_yummly_manualCounts'];
				} else {
					$counts = $socialObj->sfsi_yummly_share_count( trailingslashit( $current_url ) );
				}
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "yummly" );

			$icon       = sfsi_plus_get_icon_image( "yummly" );
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";
			$share_icon = $share_iconsUrl . $icon_name . ".svg";

			$post_title = $socialObj->sfsi_get_the_title();

			$url = "";

			$sharingUrl = "https://www.yummly.com/urb/verify?url=" . trailingslashit( urlencode( $current_url ) ) . "&title=" . $post_title . "&yumtype=button";

			if (
				( "yes" == $option2['sfsi_plus_yummlyVisit_option'] && "yes" == $option2['sfsi_plus_yummlyShare_option'] )
			) {
				$hoverSHow = 1;
				$hoverdiv  = "";

				if (
					isset( $option2['sfsi_plus_yummlyVisit_option'] ) && ! empty( $option2['sfsi_plus_yummlyVisit_option'] )
					&& "yes" == $option2['sfsi_plus_yummlyVisit_option']
					&& isset( $option2['sfsi_plus_yummly_pageUrl'] ) && ! empty( $option2['sfsi_plus_yummly_pageUrl'] )
				) {

					$visitUrl = ! preg_match( "/^(http|https):/", $option2['sfsi_plus_yummly_pageUrl'] ) ? "http://" . $option2['sfsi_plus_yummly_pageUrl'] : $option2['sfsi_plus_yummly_pageUrl'];

					$hoverdiv .= "<div class='icon1'><a href='" . $visitUrl . "'  " . sfsi_plus_checkNewWindow( $visitUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_yummlyShare_option'] ) && ! empty( $option2['sfsi_plus_yummlyShare_option'] )
					&& "yes" == $option2['sfsi_plus_yummlyShare_option']
				) {

					$hoverdiv .= "<div class='icon2'><a href='" . $sharingUrl . "'  " . sfsi_plus_checkNewWindow( $sharingUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' /></a></div>";
				}
			} else {

				$hoverSHow = 0;

				if ( "yes" == $option2['sfsi_plus_yummlyShare_option'] && isset( $option2['sfsi_plus_yummlyShare_option'] ) && ! empty( $option2['sfsi_plus_yummlyShare_option'] ) ) {

					$url = $sharingUrl;
				} else if ( "yes" == $option2['sfsi_plus_yummlyVisit_option'] && isset( $option2['sfsi_plus_yummly_pageUrl'] ) && ! empty( $option2['sfsi_plus_yummly_pageUrl'] ) ) {

					$url = $option2['sfsi_plus_yummly_pageUrl'];
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_yummly_bgColor'] ) && $option3['sfsi_plus_yummly_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_yummly_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#E36308';
				}
			}

			break;

		case "flickr":
			$url                  = ( $option2['sfsi_plus_flickr_pageUrl'] ) ? $option2['sfsi_plus_flickr_pageUrl'] : '';
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_flickr_countsDisplay'] ) &&
				$option4['sfsi_plus_flickr_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_flickr_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "flickr" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "flickr" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_flickr_bgColor'] ) && $option3['sfsi_plus_flickr_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_flickr_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#FF0084';
				}
			}

			break;

		case "reddit":
			$current_url = urlencode( $socialObj->sfsi_get_custom_share_link( 'reddit' ) );
			if ( $option2['sfsi_plus_reddit_url_type'] == 'share' ) {
				$url = 'https://reddit.com/submit?url=' . urlencode( $current_url ) . '&title=' . get_the_title();
			} elseif ( $option2['sfsi_plus_reddit_url_type'] == 'url' ) {
				$url = $option2['sfsi_plus_reddit_pageUrl'];
			} else {
				$url = '';
			}

			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_reddit_countsDisplay'] ) &&
				$option4['sfsi_plus_reddit_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_reddit_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "reddit" );

			//Custom Skin Support {Monad}
			$icon = sfsi_plus_get_icon_image( "reddit" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_reddit_bgColor'] ) && $option3['sfsi_plus_reddit_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_reddit_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#FF642C';
				}
			}

			break;

		case "tumblr":
			$url                  = ( $option2['sfsi_plus_tumblr_pageUrl'] ) ? $option2['sfsi_plus_tumblr_pageUrl'] : '';
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_tumblr_countsDisplay'] ) &&
				$option4['sfsi_plus_tumblr_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_tumblr_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "tumblr" );

			$icon = sfsi_plus_get_icon_image( "tumblr" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_tumblr_bgColor'] ) && $option3['sfsi_plus_tumblr_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_tumblr_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#36465F';
				}
			}

			break;

		case "fbmessenger":

			$toolClass            = "sfsi_plus_fbmessenger_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_fbmessenger_countsDisplay'] ) &&
				"yes" == $option4['sfsi_plus_fbmessenger_countsDisplay'] &&
				"yes" == $option4['sfsi_plus_display_counts']
			) {
				$counts = $option4['sfsi_plus_fbmessenger_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "fbmessenger" );

			$icon       = sfsi_plus_get_icon_image( "fbmessenger" );
			$share_icon = $share_iconsUrl . $icon_name . ".svg";
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";

			if ( wp_is_mobile() ) {
				$url = "fb-messenger://share/?link=" . trailingslashit( urlencode( $current_url ) );
			} else {
				if ( isset( $option2['sfsi_plus_fbmessengerShare_app_id'] ) && "" !== $option2['sfsi_plus_fbmessengerShare_app_id'] ) {
					$app_id = $option2['sfsi_plus_fbmessengerShare_app_id'];
				} else {
					$app_id = "244819978951470";
				}
				$url = "https://www.facebook.com/dialog/send?app_id=" . $app_id . "&display=popup&link=" . trailingslashit( urlencode( $current_url ) ) . "&redirect_uri=" . trailingslashit( urlencode( $current_url ) );
			}

			if ( "yes" == $option2['sfsi_plus_fbmessengerShare_option'] && "yes" == $option2['sfsi_plus_fbmessengerContact_option'] ) {
				$hoverSHow = 1;

				if ( isset( $option2['sfsi_plus_fbmessengerContact_url'] ) && ! empty( $option2['sfsi_plus_fbmessengerContact_url'] ) ) {

					$contactUrl = $option2['sfsi_plus_fbmessengerContact_url'];

					$hoverdiv .= "<div class='icon1'><a href='" . $contactUrl . "'  " . sfsi_plus_checkNewWindow( $contactUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";

					$hoverdiv .= "<div class='icon2'><a href='" . $url . "'  " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' /></a></div>";
				} else {

					$hoverSHow = 0;
				}
			} else {
				if ( "yes" == $option2['sfsi_plus_fbmessengerContact_option'] ) {
					$url = $option2['sfsi_plus_fbmessengerContact_url'];
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_fbmessenger_bgColor'] ) && $option3['sfsi_plus_fbmessenger_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_fbmessenger_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#447BBF';
				}
			}

			break;

		case "gab":

			$post_title           = $socialObj->sfsi_get_the_title();
			$url                  = "https://gab.com/compose?url=" . trailingslashit( urlencode( $current_url ) ) . "&text=" . $post_title;
			$toolClass            = "rss_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_gab_countsDisplay'] ) &&
				$option4['sfsi_plus_gab_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_gab_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "gab" );

			$icon = sfsi_plus_get_icon_image( "gab" );

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_gab_bgColor'] ) && $option3['sfsi_plus_gab_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_gab_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#25CC80';
				}
			}

			break;

		case "mix":

			$toolClass            = "sfsi_plus_mix_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_mix_countsDisplay'] ) &&
				$option4['sfsi_plus_mix_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_mix_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "mix" );
			$icon     = sfsi_plus_get_icon_image( "mix" );

			$share_icon = $share_iconsUrl . $icon_name . ".svg";
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";

			$url = "https://mix.com/mixit?url=" . trailingslashit( urlencode( $current_url ) );

			if ( "yes" == $option2['sfsi_plus_mixShare_option'] && "yes" == $option2['sfsi_plus_mixVisit_option'] ) {
				$hoverSHow = 1;

				if ( isset( $option2['sfsi_plus_mixVisit_url'] ) && ! empty( $option2['sfsi_plus_mixVisit_url'] ) ) {

					$visitUrl = $option2['sfsi_plus_mixVisit_url'];

					$hoverdiv .= "<div class='icon1'><a href='" . $visitUrl . "'  " . sfsi_plus_checkNewWindow( $visitUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";

					$hoverdiv .= "<div class='icon2'><a href='" . $url . "'  " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' /></a></div>";
				} else {
					$hoverSHow = 0;
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_mix_bgColor'] ) && $option3['sfsi_plus_mix_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_mix_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = 'conic-gradient(from 180deg at 50% 50%, #DE201D 0deg, #DE201D 117.02deg, #FF8126 117.58deg, #FFA623 230.42deg, #FFD51F 231.6deg, #FFD51F 360deg)';
				}
			}

			break;

		case "ok":

			$toolClass            = "sfsi_plus_ok_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_ok_countsDisplay'] ) &&
				"yes" == $option4['sfsi_plus_ok_countsDisplay'] &&
				"yes" == $option4['sfsi_plus_display_counts']
			) {
				$counts = $option4['sfsi_plus_ok_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "ok" );

			$icon = sfsi_plus_get_icon_image( "ok" );

			$like_icon  = $share_iconsUrl . $icon_name . ".svg";
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";
			$sub_icon   = $visit_iconsUrl . "ok_subscribe.svg";

			$url = "https://connect.ok.ru/offer?url=" . trailingslashit( urlencode( $current_url ) );

			if (
				( "yes" == $option2['sfsi_plus_okLike_option'] && "yes" == $option2['sfsi_plus_okVisit_option'] ) || ( "yes" == $option2['sfsi_plus_okLike_option'] && "yes" == $option2['sfsi_plus_okSubscribe_option'] )
				|| ( "yes" == $option2['sfsi_plus_okVisit_option'] && "yes" == $option2['sfsi_plus_okSubscribe_option'] )
			) {
				$hoverSHow = 1;
				$hoverdiv  = "";

				if ( isset( $option2['sfsi_plus_okVisit_url'] ) && ! empty( $option2['sfsi_plus_okVisit_url'] ) ) {

					$visitUrl = $option2['sfsi_plus_okVisit_url'];

					$hoverdiv .= "<div class='icon1'><a href='" . $visitUrl . "'  " . sfsi_plus_checkNewWindow( $visitUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_okLike_option'] ) && ! empty( $option2['sfsi_plus_okLike_option'] )
					&& "yes" == $option2['sfsi_plus_okLike_option']
				) {

					$hoverdiv .= "<div class='icon2'><a href='" . $url . "'  " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $like_icon . "' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_okSubscribe_option'] )
					&& ! empty( $option2['sfsi_plus_okSubscribe_option'] )
					&& "yes" == $option2['sfsi_plus_okSubscribe_option']
					&& isset( $option2['sfsi_plus_okSubscribe_userid'] ) && ! empty( $option2['sfsi_plus_okSubscribe_userid'] )
				) {
					$urlSubscribe = "https://ok.ru/dk?st.cmd=anonymMain&st.redirect=group(" . $option2['sfsi_plus_okSubscribe_userid'] . ")&st._aid=AltGroupTopCardButtonsJoin";

					$hoverdiv .= "<div class='icon3'><a href='" . $urlSubscribe . "'  " . sfsi_plus_checkNewWindow( $urlSubscribe ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $sub_icon . "' /></a></div>";
				}
			} else {
				$hoverSHow = 0;
				if ( "yes" == $option2['sfsi_plus_okVisit_option'] && isset( $option2['sfsi_plus_okVisit_url'] ) && ! empty( $option2['sfsi_plus_okVisit_url'] ) ) {

					$url = $option2['sfsi_plus_okVisit_url'];
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_ok_bgColor'] ) && $option3['sfsi_plus_ok_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_ok_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#F58220';
				}
			}

			break;

		case "telegram":

			$toolClass            = "sfsi_plus_telegram_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_telegram_countsDisplay'] ) &&
				$option4['sfsi_plus_telegram_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_telegram_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "telegram" );

			$icon = sfsi_plus_get_icon_image( "telegram" );

			$messageus_icon = $visit_iconsUrl . $icon_name . "_message.svg";
			$share_icon     = $share_iconsUrl . $icon_name . ".svg";
			$visit_icon     = $visit_iconsUrl . "telegram.svg";

			$url = "https://telegram.me/share/url?url=" . trailingslashit( urlencode( $current_url ) );

			if ( "yes" == $option2['sfsi_plus_telegramShare_option'] && "yes" == $option2['sfsi_plus_telegramMessage_option'] ) {
				$hoverSHow = 1;

				if (
					! empty( $option2['sfsi_plus_telegram_message'] ) && ! empty( $option2['sfsi_plus_telegram_username'] )
				) {
					$tg_username = $option2['sfsi_plus_telegram_username'];
					$tg_msg      = stripslashes( $option2['sfsi_plus_telegram_message'] );
					$tg_msg      = str_replace( '"', '', str_replace( "'", '', $tg_msg ) );
					$tg_msg      = html_entity_decode( strip_tags( $tg_msg ), ENT_QUOTES, 'UTF-8' );
					$tg_msg      = str_replace( "%26%238230%3B", "...", $tg_msg );
					$tg_msg      = rawurlencode( $tg_msg );

					$msgUrl = "https://t.me/" . $tg_username . "?text=" . $tg_msg;

					$hoverdiv .= "<div class='icon1'><a href='" . $msgUrl . "'  " . sfsi_plus_checkNewWindow( $msgUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $messageus_icon . "' /></a></div>";

					$hoverdiv .= "<div class='icon2'><a href='" . $url . "'  " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' /></a></div>";
				} else {
					$hoverSHow = 0;
				}
			} elseif ( "yes" == $option2['sfsi_plus_telegramShare_option'] && "yes" != $option2['sfsi_plus_telegramMessage_option'] ) {
				$hoverSHow = 0;
				$url       = "https://telegram.me/share/url?url=" . trailingslashit( urlencode( $current_url ) );
			} elseif ( "yes" != $option2['sfsi_plus_telegramShare_option'] && "yes" == $option2['sfsi_plus_telegramMessage_option'] ) {
				$hoverSHow   = 0;
				$tg_username = $option2['sfsi_plus_telegram_username'];
				$tg_msg      = stripslashes( $option2['sfsi_plus_telegram_message'] );
				$tg_msg      = str_replace( '"', '', str_replace( "'", '', $tg_msg ) );
				$tg_msg      = html_entity_decode( strip_tags( $tg_msg ), ENT_QUOTES, 'UTF-8' );
				$tg_msg      = str_replace( "%26%238230%3B", "...", $tg_msg );
				$tg_msg      = rawurlencode( $tg_msg );
				$url         = "https://t.me/" . $tg_username . "?text=" . $tg_msg;
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_telegram_bgColor'] ) && $option3['sfsi_plus_telegram_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_telegram_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#33A1D1';
				}
			}

			break;

		case "vk":

			$toolClass            = "sfsi_plus_vk_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_vk_countsDisplay'] ) &&
				$option4['sfsi_plus_vk_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_vk_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "vk" );
			$icon     = sfsi_plus_get_icon_image( "vk" );

			$share_icon = $share_iconsUrl . $icon_name . ".svg";
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";

			$url = "https://vk.com/share.php?url=" . trailingslashit( urlencode( $current_url ) );

			if (
				( isset( $option2['sfsi_plus_vkVisit_option'] ) && "yes" == $option2['sfsi_plus_vkVisit_option'] ) || ( isset( $option2['sfsi_plus_vkShare_option'] ) && "yes" == $option2['sfsi_plus_vkShare_option'] && isset( $option2['sfsi_plus_vkFollow_option'] ) && "yes" == $option2['sfsi_plus_vkFollow_option'] )
				|| ( isset( $option2['sfsi_plus_vkFollow_option'] ) && "yes" == $option2['sfsi_plus_vkFollow_option'] )
			) {
				$hoverSHow = 1;
				$hoverdiv  = "";

				if (
					isset( $option2['sfsi_plus_vkVisit_option'] ) && ! empty( $option2['sfsi_plus_vkVisit_option'] )
					&& "yes" == $option2['sfsi_plus_vkVisit_option']
					&& isset( $option2['sfsi_plus_vkVisit_url'] ) && ! empty( $option2['sfsi_plus_vkVisit_url'] )
				) {

					$visitUrl = $option2['sfsi_plus_vkVisit_url'];

					$hoverdiv .= "<div class='icon1'><a href='" . $visitUrl . "'  " . sfsi_plus_checkNewWindow( $visitUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_vkShare_option'] ) && ! empty( $option2['sfsi_plus_vkShare_option'] )
					&& "yes" == $option2['sfsi_plus_vkShare_option']
				) {

					$hoverdiv .= "<div class='icon2'><a href='" . $url . "'  " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' /></a></div>";
				}

				/*if(isset($option2['sfsi_plus_vkFollow_option'])
					&& !empty($option2['sfsi_plus_vkFollow_option'])
					&& "yes" == $option2['sfsi_plus_vkFollow_option']
					&& isset($option2['sfsi_plus_vkFollow_url']) && !empty($option2['sfsi_plus_vkFollow_url'])
				){
					$urlFollow = $option2['sfsi_plus_vkFollow_url'];

					$hoverdiv.="<div style='width:51px;height:51px;' class='icon3'><a href='".$urlFollow."'  ".sfsi_plus_checkNewWindow($urlFollow)."><img nopin=nopin alt='".$alt_text."' title='".$alt_text."' src='".$icon."' /></a></div>";
				}*/
			} else {

				$hoverSHow = 0;

				if ( "yes" == $option2['sfsi_plus_vkVisit_option'] && isset( $option2['sfsi_plus_vkVisit_url'] ) && ! empty( $option2['sfsi_plus_vkVisit_url'] ) ) {

					$url = $option2['sfsi_plus_vkVisit_url'];
				} else if ( isset( $option2['sfsi_plus_vkFollow_option'] ) && "yes" == $option2['sfsi_plus_vkFollow_option'] && isset( $option2['sfsi_plus_vkFollow_url'] ) && ! empty( $option2['sfsi_plus_vkFollow_url'] ) ) {

					$url = $option2['sfsi_plus_vkFollow_url'];
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_vk_bgColor'] ) && $option3['sfsi_plus_vk_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_vk_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#4E77A2';
				}
			}

			break;

		case "weibo":

			$toolClass            = "sfsi_plus_weibo_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if ( isset( $option4['sfsi_plus_weibo_countsDisplay'] ) && "yes" == $option4['sfsi_plus_weibo_countsDisplay'] && "yes" == $option4['sfsi_plus_display_counts'] ) {

				$counts = $option4['sfsi_plus_weibo_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( $icon_name );
			$icon     = sfsi_plus_get_icon_image( $icon_name );

			$share_icon = $share_iconsUrl . $icon_name . ".svg";
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";

			$url = "http://service.weibo.com/share/share.php?url=" . trailingslashit( urlencode( $current_url ) );

			if (
				( isset( $option2['sfsi_plus_weiboShare_option'] ) && isset( $option2['sfsi_plus_weiboVisit_option'] ) && "yes" == $option2['sfsi_plus_weiboShare_option'] && "yes" == $option2['sfsi_plus_weiboVisit_option'] ) || ( isset( $option2['sfsi_plus_weiboShare_option'] ) && "yes" == $option2['sfsi_plus_weiboShare_option'] && isset( $option2['sfsi_plus_weiboLike_option'] ) && "yes" == $option2['sfsi_plus_weiboLike_option'] )
				|| ( isset( $option2['sfsi_plus_weiboVisit_option'] ) && "yes" == $option2['sfsi_plus_weiboVisit_option'] && isset( $option2['sfsi_plus_weiboLike_option'] ) && "yes" == $option2['sfsi_plus_weiboLike_option'] )
			) {
				$hoverSHow = 1;
				$hoverdiv  = "";

				if (
					isset( $option2['sfsi_plus_weiboVisit_option'] ) && ! empty( $option2['sfsi_plus_weiboVisit_option'] ) && "yes" == $option2['sfsi_plus_weiboVisit_option']

					&& isset( $option2['sfsi_plus_weiboVisit_url'] ) && ! empty( $option2['sfsi_plus_weiboVisit_url'] )
				) {

					$visitUrl = $option2['sfsi_plus_weiboVisit_url'];

					$hoverdiv .= "<div class='icon1'><a href='" . $visitUrl . "'  " . sfsi_plus_checkNewWindow( $visitUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_weiboShare_option'] ) && ! empty( $option2['sfsi_plus_weiboShare_option'] )
					&& "yes" == $option2['sfsi_plus_weiboShare_option']
				) {

					$hoverdiv .= "<div class='icon2'><a href='" . $url . "'  " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_weiboLike_option'] )
					&& ! empty( $option2['sfsi_plus_weiboLike_option'] )
					&& "yes" == $option2['sfsi_plus_weiboLike_option']
				) {

					$hoverdiv .= "<div style='width:51px;' class='icon3'><wb:like type='simple'></wb:like></div>";
				}
			} else {

				$hoverSHow = 0;

				if ( "yes" == $option2['sfsi_plus_weiboVisit_option'] && isset( $option2['sfsi_plus_weiboVisit_option'] ) && ! empty( $option2['sfsi_plus_weiboVisit_url'] ) ) {

					$url = $option2['sfsi_plus_weiboVisit_url'];
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_weibo_bgColor'] ) && $option3['sfsi_plus_weibo_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_weibo_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#E6162D';
				}
			}

			break;

		case "wechat":

			$toolClass            = "sfsi_plus_wechat_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if ( isset( $option4['sfsi_plus_wechat_countsDisplay'] ) && "yes" == $option4['sfsi_plus_wechat_countsDisplay'] && "yes" == $option4['sfsi_plus_display_counts'] ) {

				$counts = $option4['sfsi_plus_wechat_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( $icon_name );
			$icon     = sfsi_plus_get_icon_image( $icon_name );

			$share_icon = $share_iconsUrl . $icon_name . ".svg";
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";

			// $url = "http://service.weibo.com/share/share.php?url=".trailingslashit($current_url);
			$url = "";

			if (
				( isset( $option2['sfsi_plus_wechatFollow_option'] ) && "yes" == $option2['sfsi_plus_wechatFollow_option'] ) && ( isset( $option2['sfsi_plus_wechatShare_option'] ) && "yes" == $option2['sfsi_plus_wechatShare_option'] )
			) {
				$hoverSHow = 1;
				$hoverdiv  = "";

				if (
					isset( $option2['sfsi_plus_wechatFollow_option'] ) && ! empty( $option2['sfsi_plus_wechatFollow_option'] ) && "yes" == $option2['sfsi_plus_wechatFollow_option']

					&& isset( $option2['sfsi_premium_wechat_scan_image'] ) && ! empty( $option2['sfsi_premium_wechat_scan_image'] )
				) {

					$image_url = $option2['sfsi_premium_wechat_scan_image'];

					$hoverdiv .= "<div class='icon1' style='text-align:center'><a href='' onclick='event.preventDefault();sfsi_premium_wechat_follow(\"" . $option2['sfsi_premium_wechat_scan_image'] . "\")' ><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' style='height:25px' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_wechatShare_option'] ) && ! empty( $option2['sfsi_plus_wechatShare_option'] )
					&& "yes" == $option2['sfsi_plus_wechatShare_option']
				) {

					$hoverdiv .= "<div class='icon2' style='text-align:center' ><a href='" . $url . "' onclick='event.preventDefault();sfsi_premium_wechat_share(\"" . $option2['sfsi_premium_wechat_scan_image'] . "\")' ><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' style='height:25px' /></a></div>";
				}
			} else {
				if (
					isset( $option2['sfsi_plus_wechatFollow_option'] ) && ! empty( $option2['sfsi_plus_wechatFollow_option'] ) && "yes" == $option2['sfsi_plus_wechatFollow_option']

					&& isset( $option2['sfsi_premium_wechat_scan_image'] ) && ! empty( $option2['sfsi_premium_wechat_scan_image'] )
				) {

					$sfsi_premium_wechat_onclick = "event.preventDefault();sfsi_premium_wechat_follow(\"" . $option2['sfsi_premium_wechat_scan_image'] . "\")";
				}

				if (
					isset( $option2['sfsi_plus_wechatShare_option'] ) && ! empty( $option2['sfsi_plus_wechatShare_option'] )
					&& "yes" == $option2['sfsi_plus_wechatShare_option']
				) {

					$sfsi_premium_wechat_onclick = "event.preventDefault();sfsi_premium_wechat_share(\"" . $option2['sfsi_premium_wechat_scan_image'] . "\")";
				}
				$hoverSHow = 0;
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_wechat_bgColor'] ) && $option3['sfsi_plus_wechat_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_wechat_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#4BAD33';
				}
			}

			break;

		case "xing":

			$toolClass            = "sfsi_plus_xing_tool_bdr";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_rss_arow";

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_xing_countsDisplay'] ) &&
				$option4['sfsi_plus_xing_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_xing_manualCounts'];
			}

			$alt_text = sfsi_plus_get_icon_mouseover_text( "xing" );
			$icon     = sfsi_plus_get_icon_image( "xing" );

			$share_icon = $share_iconsUrl . $icon_name . ".svg";
			$visit_icon = $visit_iconsUrl . $icon_name . ".svg";

			$url = "https://www.xing.com/app/user?op=share&url=" . trailingslashit( urlencode( $current_url ) );

			if (
				( "yes" == $option2['sfsi_plus_xingShare_option'] && "yes" == $option2['sfsi_plus_xingVisit_option'] ) || ( "yes" == $option2['sfsi_plus_xingShare_option'] && "yes" == $option2['sfsi_plus_xingFollow_option'] )
				|| ( "yes" == $option2['sfsi_plus_xingVisit_option'] && "yes" == $option2['sfsi_plus_xingFollow_option'] )
			) {
				$hoverSHow = 1;
				$hoverdiv  = "";

				if (
					isset( $option2['sfsi_plus_xingVisit_option'] ) && ! empty( $option2['sfsi_plus_xingVisit_option'] )
					&& "yes" == $option2['sfsi_plus_xingVisit_option']
					&& isset( $option2['sfsi_plus_xingVisit_url'] ) && ! empty( $option2['sfsi_plus_xingVisit_url'] )
				) {

					$visitUrl = $option2['sfsi_plus_xingVisit_url'];

					$hoverdiv .= "<div class='icon1'><a href='" . $visitUrl . "'  " . sfsi_plus_checkNewWindow( $visitUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_xingShare_option'] ) && ! empty( $option2['sfsi_plus_xingShare_option'] )
					&& "yes" == $option2['sfsi_plus_xingShare_option']
				) {

					$hoverdiv .= "<div class='icon2'><a href='" . $url . "'  " . sfsi_plus_checkNewWindow( $url ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_xingFollow_option'] )
					&& ! empty( $option2['sfsi_plus_xingFollow_option'] )
					&& "yes" == $option2['sfsi_plus_xingFollow_option']
					&& isset( $option2['sfsi_plus_xingFollow_url'] ) && ! empty( $option2['sfsi_plus_xingFollow_url'] )
				) {
					$urlFollow = $option2['sfsi_plus_xingFollow_url'];

					$hoverdiv .= "<div style='width:70px;' class='icon3'>
					<div alt='" . $alt_text . "' title='" . $alt_text . "' data-type='xing/follow' data-url=" . $urlFollow . "></div></div>";
				}
			} else {

				$hoverSHow = 0;

				if ( "yes" == $option2['sfsi_plus_xingVisit_option'] && isset( $option2['sfsi_plus_xingVisit_url'] ) && ! empty( $option2['sfsi_plus_xingVisit_url'] ) ) {
					$url = $option2['sfsi_plus_xingVisit_url'];

				} else if ( "yes" == $option2['sfsi_plus_xingFollow_option'] && isset( $option2['sfsi_plus_xingFollow_url'] ) && ! empty( $option2['sfsi_plus_xingFollow_url'] ) ) {

					$url = $option2['sfsi_plus_xingFollow_url'];
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_xing_bgColor'] ) && $option3['sfsi_plus_xing_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_xing_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#005A60';
				}
			}

			break;

		case "copylink":

			$post_title           = $socialObj->sfsi_get_the_title();
			$url                  = "javascript:void(0);";
			$toolClass            = "copylink_tool_bdr";
			$class                = "sfsi_copylink";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_copylink_arow";

			$alt_text = sfsi_plus_get_icon_mouseover_text( "copylink" );

			$icon = sfsi_plus_get_icon_image( "copylink" );

            /* fecth no of counts if active in admin section */
            if (
                    isset( $option4['sfsi_plus_copylink_countsDisplay'] ) &&
                    $option4['sfsi_plus_copylink_countsDisplay'] == "yes" &&
                    $option4['sfsi_plus_display_counts'] == "yes"
            ) {
                $counts = $option4['sfsi_plus_copylink_manualCounts'];
            }

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_copylink_bgColor'] ) && $option3['sfsi_plus_copylink_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_copylink_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = 'linear-gradient(180deg, #C295FF 0%, #4273F7 100%)';
				}
			}
			echo '
  <style>
    /* Styles for the success alert */
    .success-alert {
      display: none;
      position: fixed;
      top: 100px; /* Adjust the distance from the top */
      right: 10px; /* Adjust the distance from the right */
      background-color: #4CAF50;
      color: white;
      text-align: center;
      padding: 15px;
      border-radius: 5px;
      z-index: 99999;
    }

    /* Intro animation */
    @keyframes intro-animation {
      0% {
        transform: translateX(100%);
      }
      100% {
        transform: translateX(0);
      }
    }

    /* Keyframes animation to fade out */
    @keyframes fade-out {
      0% {
        opacity: 1;
      }
      90% {
        opacity: 1;
      }
      100% {
        opacity: 0;
      }
    }
  </style>
<div id="success-alert" class="success-alert">' . __( 'URL has been copied successfully!', 'ultimate-social-media-plus' ) . '</div>';

			break;

		case "mastodon":
			$post_title           = $socialObj->sfsi_get_the_title();
			$toolClass            = "mastodon_tool_bdr";
			$class                = "sfsi_mastodon";
			$hoverdiv             = '';
			$arsfsiplus_row_class = "bot_mastodon_arow";

			$alt_text = sfsi_plus_get_icon_mouseover_text( "mastodon" );

			$icon = sfsi_plus_get_icon_image( "mastodon" );

			/* fecth no of counts if active in admin section */
			if (
				isset( $option4['sfsi_plus_mastodon_countsDisplay'] ) &&
				$option4['sfsi_plus_mastodon_countsDisplay'] == "yes" &&
				$option4['sfsi_plus_display_counts'] == "yes"
			) {
				$counts = $option4['sfsi_plus_mastodon_manualCounts'];
			}

			// $url = "https://mastodon.social/share?text=" . urlencode($post_title) . "&url=" . trailingslashit($current_url);
			$url                 = "";
			$encoded_post_title  = urlencode( $post_title );
			$trailed_current_url = trailingslashit( $current_url );

			$share_icon              = $share_iconsUrl . $icon_name . ".png";
			$visit_icon              = $visit_iconsUrl . $icon_name . ".png";
			$mastodon_share_btn_html = "
				<script src='https://unpkg.com/mastodon-share-button@latest/dist/mastodon-share-button/mastodon-share-button.js' defer></script>

				<style>
					.mastodon-styles {
						--share-button-height: 0;
						--img-width: 0;
						--img-height: 0;
						--share-button-padding: 0;
						--share-button-margin: 0;
						--share-button-border-radius: 0;
						--share-button-background-color: transparent;
						--share-button-color: transparent;

						/* Remove on hover effects*/
						--share-button-background-color-hover: transparent;
					}
				</style>

				<mastodon-share-button
					id='sfsi_plus_mastodon_share_button_hidden'
					share_message='Checkout this page ($encoded_post_title}) at {$trailed_current_url}'

					icon_url=''
					share_button_text=''
					style='visibility:hidden; position: absolute; top: 0; left: 0; z-index: 9999999999;'
					class='mastodon-styles'
				></mastodon-share-button>
			";

			if (
				( isset( $option2['sfsi_plus_mastodonShare_option'] ) && "yes" == $option2['sfsi_plus_mastodonShare_option'] && "yes" == $option2['sfsi_plus_mastodonVisit_option'] )
			) {
				$hoverSHow = 1;
				$hoverdiv  = "";

				if (
					isset( $option2['sfsi_plus_mastodonVisit_option'] ) && ! empty( $option2['sfsi_plus_mastodonVisit_option'] )
					&& "yes" == $option2['sfsi_plus_mastodonVisit_option']
					&& isset( $option2['sfsi_plus_mastodonVisit_url'] ) && ! empty( $option2['sfsi_plus_mastodonVisit_url'] )
				) {

					$visitUrl = $option2['sfsi_plus_mastodonVisit_url'];
					$hoverdiv .= "<div class='icon1' style='text-align:center; padding-bottom: 3px;'><a href='" . $visitUrl . "'  " . sfsi_plus_checkNewWindow( $visitUrl ) . "><img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $visit_icon . "' style='height:25px' /></a></div>";
				}

				if (
					isset( $option2['sfsi_plus_mastodonShare_option'] ) && ! empty( $option2['sfsi_plus_mastodonShare_option'] )
					&& "yes" == $option2['sfsi_plus_mastodonShare_option']
				) {

					$hoverdiv .= "<div class='icon2' style='text-align:center'>
						<a href='' onclick='event.preventDefault();SFSI(SFSI(\"#sfsi_plus_mastodon_share_button_hidden\")[0].shadowRoot).find(\"div > .share-button\").click();'>
							<img class='sfsi_premium_wicon' nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $share_icon . "' style='height:25px' />
						</a>
					</div>";

					// Include mastodon share button js
					echo $mastodon_share_btn_html;
				}
			} else {

				$hoverSHow = 0;

				if ( isset( $option2['sfsi_plus_mastodonVisit_option'] ) && "yes" == $option2['sfsi_plus_mastodonVisit_option'] && isset( $option2['sfsi_plus_mastodonVisit_url'] ) && ! empty( $option2['sfsi_plus_mastodonVisit_url'] ) ) {
					$url = $option2['sfsi_plus_mastodonVisit_url'];
				} else {
					// Js void
					$url = "javascript:void(0);";
				}

				if ( isset( $option2['sfsi_plus_mastodonShare_option'] ) && "yes" == $option2['sfsi_plus_mastodonShare_option'] && "yes" != $option2['sfsi_plus_mastodonVisit_option'] ) {
					echo $mastodon_share_btn_html;
					$sfsi_premium_wechat_onclick = 'event.preventDefault();SFSI(SFSI("#sfsi_plus_mastodon_share_button_hidden")[0].shadowRoot).find("div > .share-button").click();';
				}
			}

			/* For Flat icons bg color */
			if ( $active_theme == 'flat' ) {
				if ( isset( $option3['sfsi_plus_mastodon_bgColor'] ) && $option3['sfsi_plus_mastodon_bgColor'] != '' ) {
					$sfsi_plus_icon_bgColor = $option3['sfsi_plus_mastodon_bgColor'];
				} else {
					$sfsi_plus_icon_bgColor = '#583ED1';
				}
			}

			break;

		default:

			$arrCustomDisplay = isset( $option1['sfsi_custom_desktop_icons'] ) && ! empty( $option1['sfsi_custom_desktop_icons'] ) ? $option1['sfsi_custom_desktop_icons'] : array();

			if ( wp_is_mobile() && isset( $option1['sfsi_plus_icons_onmobile'] ) && ! empty( $option1['sfsi_plus_icons_onmobile'] ) && "yes" == $option1['sfsi_plus_icons_onmobile'] ) {

				$arrCustomDisplay = $option1['sfsi_custom_mobile_icons'];
			}

			if ( ! empty( $customIcons ) && ! empty( $arrCustomDisplay ) ) {

				$border_radius = "";
				$cmcls         = "cmcls";
				$padding_top   = "";

				$custom_icon_urls = maybe_unserialize( $option2['sfsi_plus_CustomIcon_links'] );

				$url = ( isset( $custom_icon_urls[ $icon_n ] ) && ! empty( $custom_icon_urls[ $icon_n ] ) ) ? do_shortcode( $custom_icon_urls[ $icon_n ] ) : '';

				$toolClass             = "custom_lkn";
				$arsfsiplus_row_class  = "";
				$custom_icons_hoverTxt = maybe_unserialize( $option5['sfsi_plus_custom_MouseOverTexts'] );

				$icon = $customIcons[ $icon_n ];

				//Giving alternative text to image
				if ( ! empty( $custom_icons_hoverTxt[ $icon_n ] ) ) {
					$alt_text = $custom_icons_hoverTxt[ $icon_n ];
				} else {
					$alt_text = __( 'SOCIALICON', 'ultimate-social-media-plus' );
				}
			}


			break;
	}

	// Add Href to the icon
	switch ( $icon_name ) {
		case 'facebook':
		case 'twitter':
			if ( empty( $shareUrl ) ) {
				$customShare = true;
				$shareUrl    = $url;
			}
			break;

		default:
			// Do nothing
	}

	$icons = "";

	/* apply size of icon */
	$icons_space = '';
	if ( wp_is_mobile() && $option5['sfsi_plus_mobile_icon_setting'] == 'yes' ) {
		if ( $is_front == 0 ) {
			$icons_size = $option5['sfsi_plus_icons_mobilesize'];
		} else {
			$icons_size = 51;
		}

		/* spacing and no of icons per row */
		$icons_space = $option5['sfsi_plus_icons_mobilespacing'];
		$icon_width  = (int) $icons_size;
	} else {
		if ( $is_front == 0 ) {
			$icons_size = $option5['sfsi_plus_icons_size'];
		} else {
			$icons_size = 51;
		}

		/* spacing and no of icons per row */
		$icons_space = $option5['sfsi_plus_icons_spacing'];
		$icon_width  = (int) $icons_size;
	}

	/* check for mouse hover effect */
	$icon_opacity = "1";

	if ( 'yes' == $option3['sfsi_plus_mouseOver'] && "same_icons" == $option3['sfsi_plus_mouseOver_effect_type'] ) {
		$mouse_hover_effect = $option3["sfsi_plus_mouseOver_effect"];

		if ( $mouse_hover_effect == "fade_in" || $mouse_hover_effect == "combo" ) {
			$icon_opacity = "0.6";
		}
	}

	$toolT_cls = '';
	if ( (int) $icon_width <= 49 && (int) $icon_width >= 30 ) {
		$bt_class  = "";
		$toolT_cls = "sfsi_plus_Tlleft";
	} else if ( (int) $icon_width <= 20 ) {
		$bt_class  = "sfsiSmBtn";
		$toolT_cls = "sfsi_plus_Tlleft";
	} else {
		$bt_class  = "";
		$toolT_cls = "sfsi_plus_Tlleft";
	}
	if ( $toolClass == "rss_tool_bdr" || $toolClass == "custom_lkn" || $toolClass == "instagram_tool_bdr" ) {
		$new_window = sfsi_plus_checkNewWindow();
		$url        = $url;
	} elseif ( $toolClass == 'email_tool_bdr' && $option2['sfsi_plus_email_icons_functions'] == 'contact' ) {
		$url = $url;
	} elseif ( $toolClass == 'email_tool_bdr' && $option2['sfsi_plus_email_icons_functions'] == 'share_email' ) {
		$url = $url;
	} else if ( $hoverSHow ) {
		$new_window = '';
		$url        = "";

		if ( ! wp_is_mobile() ) {
			$new_window = sfsi_plus_checkNewWindow();
			$url        = $url;
		}
	} else {
		$new_window = sfsi_plus_checkNewWindow();
		$url        = $url;
	}

	if ( wp_is_mobile() && $option5['sfsi_plus_mobile_icon_setting'] == 'yes' ) {
		$margin_bot = $option5['sfsi_plus_icons_verical_mobilespacing'];
	} else {
		$margin_bot = $option5['sfsi_plus_icons_verical_spacing'];
	}

	// if ($option4['sfsi_plus_display_counts'] == "yes") {
	// 	$margin_bot = "30";
	// }

	// Prepare the icon
	if ( isset( $icon ) && ! empty( $icon ) && preg_match( '/^https?:\/\/[^\s\/$.?#].[^\s]*$/i', $icon ) ) {
		if ( isset( $customShare ) && $customShare == true ) {
			$url = $shareUrl;
			// $new_window = "window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";
			// $new_window = 'onclick="'.$new_window.'"';
		}

		if ( isset( $newsletterSubscription ) && $newsletterSubscription == 'mailchimp' ) {
			$class      .= " mailchimpSubscription";
			$new_window = '';
			$nonce      = wp_create_nonce( 'mailchimpSubscription' );
		}

		$sfsi_whatsapp_url_type = $option2['sfsi_plus_whatsapp_url_type'];

		$no_follow_attr = sfsi_plus_get_no_follow_attr( $option5 );

		$mouseOver_effect_type = isset( $option3['sfsi_plus_mouseOver_effect_type'] ) && ! empty( $option3['sfsi_plus_mouseOver_effect_type'] ) ? $option3['sfsi_plus_mouseOver_effect_type'] : "same_icons";

		$iconBackImgUrl = sfsi_plus_get_back_icon_img_url( $icon_name, $icon_n );

		$shallAddBackIcon = null !== $iconBackImgUrl && false !== $iconBackImgUrl ? true : false;

		$mouseover_other_icons_transition_effect = ( "yes" === $option3['sfsi_plus_mouseOver'] && "other_icons" === $option3['sfsi_plus_mouseOver_effect_type'] ) ? $option3['sfsi_plus_mouseover_other_icons_transition_effect'] : "";

		$noMouseOverEffectClass      = "noeffect" == $mouseover_other_icons_transition_effect ? 'sfsihide' : '';
		$noMouseOverEffectFrontClass = "noeffect" == $mouseover_other_icons_transition_effect ? 'sfsishow' : '';

		$addFrontClass = ' sciconfront ' . $noMouseOverEffectFrontClass;

		$icons_space_value = $icons_space / 2;

		//Main div wrpr
		$icons .= "<div style='width:" . $icon_width . "px;height:auto;margin-left:" . $icons_space_value . "px;margin-right:" . $icons_space_value . "px;margin-bottom:" . $margin_bot . "px;transform: none !important;' class='sfsi_premium_wicons shuffeldiv" . $cmcls . " sfsi_premium_tooltip_align_" . strtolower( $option5['sfsi_plus_tooltip_alighn'] ) . " ' >";

		$set_height_for_parent_tag = 'line-height:0;';
		if ( 'flip' === $mouseover_other_icons_transition_effect ) {
			$set_height_for_parent_tag .= ' height: auto;';
		}

		$icons .= "<div style='" . $set_height_for_parent_tag . "' class='sfsiplus_inerCnt' data-othericoneffect='" . $mouseover_other_icons_transition_effect . "'>";

		if ( $sfsi_plus_icon_bgColor ) {
			$sfsi_plus_icon_bgColor_style = "background:" . $sfsi_plus_icon_bgColor . ";";
		}

		if ( $icon_name == "whatsapp" && isset( $sfsi_whatsapp_url_type ) && $sfsi_whatsapp_url_type == 'share_page' ) {

			$addClass = ( strlen( $class ) > 0 ) ? 'clWhatsapp ".$class." sficn' : 'clWhatsapp sficn';

			$socialObj = new sfsi_plus_SocialHelper();
			$link      = $socialObj->sfsi_get_custom_share_link( 'whatsapp' );
			$title     = $socialObj->sfsi_get_the_title();

			$custom_whatsapp_txt = stripslashes( $option2['sfsi_plus_whatsapp_share_page'] );

			$icons .= "<a class='" . $addClass . $addFrontClass . "' data-customtxt='" . $custom_whatsapp_txt . "' data-url='" . $link . "' data-text='" . $title . "' data-effect='" . $mouse_hover_effect . "' " . ( isset( $url ) && $url != "" ? $new_window : '' ) . " style='cursor:pointer;opacity:" . $icon_opacity . ";height: " . $icons_size . "px;width: " . $icons_size . "px;" . $sfsi_plus_icon_bgColor_style . "' " . $no_follow_attr . ">";

			$icons .= "<img nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $icon . "' height='" . $icons_size . "' width='" . $icons_size . "' style='" . $border_radius . $padding_top . "' class='sfcm sfsi_premium_wicon' data-effect='" . $mouse_hover_effect . "' />";

			$icons .= '</a>';

			// Add icon for Question 4->Mouse-Over effects->Show other icons on mouse-over
			if ( $mouseover_other_icons_transition_effect ) {
				$icons .= sfsi_plus_get_single_icon_html(
					$icon_name,
					$shallAddBackIcon,
					$iconBackImgUrl,
					$icon,
					$class,
					$noMouseOverEffectClass,
					isset( $data_effect ) ? $data_effect : '',
					$new_window,
					$url,
					$icon_opacity,
					! empty( $sfsi_plus_icon_bgColor_style ) ? $sfsi_plus_icon_bgColor_style : '',
					$no_follow_attr,
					$alt_text,
					$icons_size,
					$border_radius,
					$padding_top,
					$mouseOver_effect_type,
					$custom_whatsapp_txt,
					$link,
					$title
				);
			}

		} else if ( $icon_name == "pinterest" && ( false != isset( $option2['sfsi_plus_pinterest_page'] ) ) && ( "yes" != $option2['sfsi_plus_pinterest_page'] ) && ( false != isset( $option2['sfsi_plus_pinterest_pingBlog'] ) ) && ( "yes" == $option2['sfsi_plus_pinterest_pingBlog'] ) ) {

			$addClas = $class . ' sficn sciconfront ' . $noMouseOverEffectFrontClass;
			$iconImg = "<img nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $icon . "' height='" . $icons_size . "' width='" . $icons_size . "' style='" . $border_radius . $padding_top . "' class='sfcm sfsi_premium_wicon sfsi_premium_pinterest_icon' data-effect='" . $mouse_hover_effect . "' />";

			$icons .= $socialObj->sfsi_PinIt( $mouse_hover_effect, $addClas, $current_url, $iconImg, $icon_opacity, $sfsi_plus_icon_bgColor_style, $icons_size );

			if ( $mouseover_other_icons_transition_effect ) {

				$addClass = '';
				if ( $shallAddBackIcon ) {
					$addClass .= ' sciconback ' . $noMouseOverEffectClass;

					$iconImg = "<img nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $iconBackImgUrl . "' height='" . $icons_size . "' width='" . $icons_size . "' style='" . $border_radius . $padding_top . "' class='sfcm sfsi_premium_wicon' data-effect='" . $mouse_hover_effect . "' />";

					$icons .= $socialObj->sfsi_PinIt( $mouse_hover_effect, $addClass, $current_url, $iconImg, $icon_opacity, $sfsi_plus_icon_bgColor_style, $icons_size );
				} else {

					$addClass .= ' sciconback ' . $noMouseOverEffectClass;

					$iconImg = "<img nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $icon . "' height='" . $icons_size . "' width='" . $icons_size . "' style='" . $border_radius . $padding_top . "' class='sfcm sfsi_premium_wicon' data-effect='" . $mouse_hover_effect . "' />";

					$icons .= $socialObj->sfsi_PinIt( $mouse_hover_effect, $addClass, $current_url, $iconImg, $icon_opacity, $sfsi_plus_icon_bgColor_style, $icons_size );
				}
			}

		} else if ( $icon_name == "facebook" ) {

			$window = $new_window;

			$fbOpcount = 0;
			$fbOpcount = $option2['sfsi_plus_facebookPage_option'] == "yes" ? $fbOpcount + 1 : $fbOpcount;
			$fbOpcount = $option2['sfsi_plus_facebookLike_option'] == "yes" ? $fbOpcount + 1 : $fbOpcount;
			$fbOpcount = $option2['sfsi_plus_facebookShare_option'] == "yes" ? $fbOpcount + 1 : $fbOpcount;

			if ( 1 !== $fbOpcount ) {
				// if Visit, Like and share options are active,  don't open icons link in new tab
				if ( $option2['sfsi_plus_facebookPage_option'] == "yes" || $option2['sfsi_plus_facebookLike_option'] == "yes" || $option2['sfsi_plus_facebookShare_option'] == "yes" ) {
					$window = "";
				}
			}

			$icons .= "<a class='" . $class . " sficn " . $addFrontClass . "' data-effect='" . $mouse_hover_effect . "' $window  href='" . ( isset( $url ) && $url != "" ? $url : '' ) . "' " . ( isset( $url ) && $url != "" ? $new_window : '' ) . " style='opacity:" . $icon_opacity . ";height: " . $icons_size . "px;width: " . $icons_size . "px;" . $sfsi_plus_icon_bgColor_style . "' " . $no_follow_attr . ">";

			$icons .= "<img nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $icon . "' height='" . $icons_size . "' width='" . $icons_size . "' style='" . $border_radius . $padding_top . "' class='sfcm sfsi_premium_wicon' data-effect='" . $mouse_hover_effect . "' />";
			$icons .= '</a>';

			// Add icon for Question 4->Mouse-Over effects->Show other icons on mouse-over
			if ( $mouseover_other_icons_transition_effect ) {
				$icons .= sfsi_plus_get_single_icon_html(
					$icon_name,
					$shallAddBackIcon,
					$iconBackImgUrl,
					$icon,
					$class,
					$noMouseOverEffectClass,
					$mouse_hover_effect,
					$new_window,
					$url,
					$icon_opacity,
					! empty( $sfsi_plus_icon_bgColor_style ) ? $sfsi_plus_icon_bgColor_style : '',
					$no_follow_attr,
					$alt_text,
					$icons_size,
					$border_radius,
					$padding_top,
					$mouseOver_effect_type
				);
			}
		} else {
			if ( $icon_name == "wechat" ) {
				$border_radius = "border-radius:" . $icons_size . "px;";
			}

			if ( "wechat" == $icon_name && $new_window == "onclick='sfsi_plus_new_window_popup(event)'" ) {
				$new_window = "";
			}

			$icons .= "<a class='" . $class . " sficn " . $addFrontClass . "' " . ( isset( $nonce ) ? 'data-nonce=' . $nonce : '' ) . " data-effect='" . $mouse_hover_effect . "' href='" . ( isset( $url ) && $url != "" ? $url : '' ) . "' " . ( isset( $url ) && $url != "" ? $new_window : '' ) . " style='opacity:" . $icon_opacity . ";height: " . $icons_size . "px;width: " . $icons_size . "px;" . $sfsi_plus_icon_bgColor_style . "' " . $no_follow_attr . ( isset( $sfsi_premium_wechat_onclick ) ? 'onclick=\'' . $sfsi_premium_wechat_onclick . "'" : '' ) . "  >";
			$icons .= "<img nopin=nopin alt='" . $alt_text . "' title='" . $alt_text . "' src='" . $icon . "' height='" . $icons_size . "' width='" . $icons_size . "' style='" . $border_radius . $padding_top . "' class='sfcm sfsi_premium_wicon sfsi_premium_" . $icon_name . "_icon' data-effect='" . $mouse_hover_effect . "' />";
			$icons .= '</a>';

			// Add icon for Question 4->Mouse-Over effects->Show other icons on mouse-over
			if ( $mouseover_other_icons_transition_effect ) {
				$icons .= sfsi_plus_get_single_icon_html(
					$icon_name,
					$shallAddBackIcon,
					$iconBackImgUrl,
					$icon,
					$class,
					$noMouseOverEffectClass,
					$mouse_hover_effect,
					$new_window,
					$url,
					$icon_opacity,
					! empty( $sfsi_plus_icon_bgColor_style ) ? $sfsi_plus_icon_bgColor_style : '',
					$no_follow_attr,
					$alt_text,
					$icons_size,
					$border_radius,
					$padding_top,
					$mouseOver_effect_type,
					false,
					false,
					false,
					( isset( $sfsi_premium_wechat_onclick ) ? 'onclick=\'' . $sfsi_premium_wechat_onclick . "'" : '' )
				);
			}
		}

		if ( $icon_name == 'facebook' && is_array( $counts ) && ! empty( $counts ) ) {
			if ( isset( $counts['c'] ) && $counts['c'] !== '' && $onpost == "no" ) {
				if ( intval( $counts['c'] ) >= $minCountToDisplayCountBox ) {
					if ( isset( $option5['sfsi_plus_change_number_format'] ) && "no" == ( $option5['sfsi_plus_change_number_format'] ) ) {
						$counts['c'] = $socialObj->format_num( $counts['c'] );
					}
					$icons .= '<span class="bot_no ' . $bt_class . '">' . $counts['c'] . '</span>';
				}
			}
		} else {
			if ( isset( $counts ) && $counts !== '' && $onpost == "no" ) {
				if ( intval( $counts ) >= $minCountToDisplayCountBox ) {
					if ( isset( $option5['sfsi_plus_change_number_format'] ) && "no" == ( $option5['sfsi_plus_change_number_format'] ) ) {
						$counts = $socialObj->format_num( $counts );
					}
					$counts = empty( $counts ) ? 0 : $counts;
					$icons  .= '<span class="bot_no ' . $bt_class . '">' . $counts . '</span>';
				}
			}
		}


		if ( $hoverSHow && ! empty( $hoverdiv ) ) {
			$tooltip_background_color = $option5['sfsi_plus_tooltip_Color'];
			$tooltip_border_color     = $option5['sfsi_plus_tooltip_border_Color'];
			// Ashutosh: for twiter display is set to block so that it can render automatic buttons correctly. it is hidden after rendering.
			$icons .= '<div id="sfsiplusid_' . $icon_name . '" class="sfsi_plus_tool_tip_2 sfsi_premium_tooltip_' . strtolower( $option5['sfsi_plus_tooltip_alighn'] ) . ' ' . $toolClass . ' ' . $toolT_cls . '" style="display:block;background:' . $tooltip_background_color . '; border:1px solid ' . $tooltip_border_color . '; opacity:0;z-index:-1;">';
			$icons .= '<span class="bot_arow ' . $arsfsiplus_row_class . '"></span>';
			$icons .= '<div class="sfsi_plus_inside">' . $hoverdiv . "</div>";
			$icons .= "</div>";
		}

		$icons .= "</div></div>";

	}

	return $icons;
}


/* make url for new window */
function sfsi_plus_checkNewWindow() {
	$option5    = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
	$new_window = '';

	if ( wp_is_mobile() && isset( $option5["sfsi_plus_mobile_open_type_setting"] ) && "yes" == $option5["sfsi_plus_mobile_open_type_setting"] ) {
		if ( $option5['sfsi_plus_icons_mobile_ClickPageOpen'] == "window" ) {
			$new_window = "onclick='sfsi_plus_new_window_popup(event)'";
		} elseif ( $option5['sfsi_plus_icons_mobile_ClickPageOpen'] == "tab" ) {
			$new_window = "target='_blank'";
		} else {
			return '';
		}

		if ( $option5['sfsi_plus_icons_AddNoopener'] == "yes" ) {
			$new_window .= " rel='noopener'";
		}

		return $new_window;

	} else {
		if ( $option5['sfsi_plus_icons_ClickPageOpen'] == "window" ) {
			$new_window = "onclick='sfsi_plus_new_window_popup(event)'";
		} elseif ( $option5['sfsi_plus_icons_ClickPageOpen'] == "tab" ) {
			$new_window = "target='_blank'";
		} else {
			return '';
		}

		if ( isset( $option5['sfsi_plus_icons_AddNoopener'] ) && $option5['sfsi_plus_icons_AddNoopener'] == "yes" ) {
			$new_window .= " rel='noopener'";
		}

		return $new_window;
	}
}

function sfsi_plus_check_posts_visiblity( $isFloter = 0, $fromPost = null, $sfsi_section5 = null, $sfsi_section8 = null ) {
	if ( sfsi_premium_is_any_standard_icon_selected() ) {

		global $wpdb;
		/* Access the saved settings in database  */
		$sfsi_premium_section1_options = maybe_unserialize( get_option( 'sfsi_premium_section1_options', false ) );
		$sfsi_section3                 = maybe_unserialize( get_option( 'sfsi_premium_section3_options', false ) );
		if ( $sfsi_section5 == null ) {
			$sfsi_section5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
		}

		//options that are added on the third question
		if ( $sfsi_section8 == null ) {
			$sfsi_section8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );
		}

		/* calculate the width and icons display alignments */
		if ( $sfsi_section8['sfsi_plus_mobile_size_space_beforeafterposts'] == "yes" && wp_is_mobile() ) {
			$icons_size           = $sfsi_section8['sfsi_plus_post_mobile_icons_size'];
			$icons_space          = $sfsi_section8['sfsi_plus_post_mobile_icons_spacing'];
			$icons_space_vertical = ( isset( $sfsi_section8['sfsi_plus_post_mobile_icons_vertical_spacing'] ) && ! empty( $sfsi_section8['sfsi_plus_post_mobile_icons_vertical_spacing'] ) ) ? $sfsi_section8['sfsi_plus_post_mobile_icons_vertical_spacing'] : 5;
		} else {
			$icons_size           = $sfsi_section8['sfsi_plus_post_icons_size'];
			$icons_space_vertical = ( isset( $sfsi_section8['sfsi_plus_post_icons_vertical_spacing'] ) && ! empty( $sfsi_section8['sfsi_plus_post_icons_vertical_spacing'] ) ) ? $sfsi_section8['sfsi_plus_post_icons_vertical_spacing'] : 5;
			$icons_space          = $sfsi_section8['sfsi_plus_post_icons_spacing'];
		}

		$extra = 0;

		/* built the main widget div */
		$icons_main = '<div class="sfsiplus_norm_row sfsi_plus_wDivothr" id="sfsi_plus_wDivothrWid">';

		$icons = "";

		/* loop through icons and bulit the icon with all settings applied in admin */
		if ( wp_is_mobile() && 'yes' == $sfsi_section8['sfsi_plus_beforeafterposts_show_on_mobile'] ) {
			// Show on mobile yes
			$arrOrderIcons = sfsi_premium_get_icons_order( $sfsi_section5, $sfsi_premium_section1_options );
			$arrData       = sfsi_premium_get_icons_html( $arrOrderIcons, $sfsi_premium_section1_options );
			$icons         .= $arrData['html'];
		} else if ( $sfsi_section8['sfsi_plus_beforeafterposts_show_on_desktop'] == 'yes' ) {
			$arrOrderIcons = sfsi_premium_get_icons_order( $sfsi_section5, $sfsi_premium_section1_options );
			$arrData       = sfsi_premium_get_icons_html( $arrOrderIcons, $sfsi_premium_section1_options );
			$icons         .= $arrData['html'];
		}

		$icons      .= '</div >';
		$icons_main .= $icons;

		/* if floating of icons is active create a floater div */
		$icons_float = '';
		$icons_data  = $icons_main . $icons_float;

		return $icons_data;
	}
}

function sfsi_woocomerce_icon_render( $option8 ) {
	$sfsi_plus_display_button_type               = $option8['sfsi_plus_display_button_type'];
	$txt                                         = ( isset( $option8['sfsi_plus_textBefor_icons'] ) ) ? $option8['sfsi_plus_textBefor_icons'] : "Please follow and like us:";
	$float                                       = $option8['sfsi_plus_icons_alignment'];
	$icons_after                                 = '';
	$lineheight                                  = $option8['sfsi_plus_post_icons_size'];
	$lineheight                                  = sfsi_plus_getlinhght( $lineheight );
	$sfsi_plus_responsive_icon_before_after_post = sfsi_plus_shall_show_icons( 'responsive_icon_before_after_post' );
	if ( $float == "center" ) {
		$style_parent = 'display:flex;justify-content:center;';
		$style        = 'float:none; display: inline-block;';
	} else if ( $float == "left" ) {
		$style_parent = 'display:flex;justify-content:flex-start;';
		$style        = 'float:' . $float . ';';
	} else if ( $float == "right" ) {
		$style_parent = 'display:flex;justify-content:flex-end;';
		$style        = 'float:' . $float . ';';
	}
	$icons_after .= '<div class="sfsiaftrpstwpr" style="' . $style_parent . '">';

	if ( $sfsi_plus_display_button_type == 'standard_buttons' ) {
		// if($sfsi_plus_rect_icon_before_after_post){
		$icons_after .= sfsi_plus_social_buttons_below( "", false );
		// }
	} elseif ( $sfsi_plus_display_button_type == 'responsive_button' ) {

		if ( $sfsi_plus_responsive_icon_before_after_post ) {

			$icons_after .= sfsi_premium_social_responsive_buttons( null, $option8 );
		}
	} else if ( sfsi_premium_is_any_standard_icon_selected() ) {
		$icons_after .= "<div class='sfsi_plus_Sicons' style='" . $style . "'>";
		$icons_after .= "<div style='float:left;margin:0 0px; line-height:" . $lineheight . "px !important'><span>" . $txt . "</span></div>";
		$icons_after .= sfsi_plus_check_posts_visiblity( 0, "yes" );
		$icons_after .= "</div><div style='clear:both'></div>";
	}
	$icons_after .= '</div>';
	echo $icons_after;

	return $icons_after;
}

function sfsi_premium_sticky_bar_icons( $content, $option8, $server_side = false, $option5 = null ) {
	global $post;

	if ( isset( $option8["sfsi_plus_sticky_bar"] ) && $option8["sfsi_plus_sticky_bar"] != "yes" ) {
		return "";
	}

	if ( ( isset( $option8['sfsi_plus_sticky_icons']['settings']['desktop'] ) && $option8['sfsi_plus_sticky_icons']['settings']['desktop'] == "yes" && ! wp_is_mobile() ) || ( isset( $option8['sfsi_plus_sticky_icons']['settings']['mobile'] ) && $option8['sfsi_plus_sticky_icons']['settings']['mobile'] == "yes" && wp_is_mobile() ) ) :


		$option2 = maybe_unserialize( get_option( 'sfsi_premium_section2_options', false ) );
		$option4 = maybe_unserialize( get_option( 'sfsi_premium_section4_options', false ) );
		if ( $option5 == null ) {
			$option5 = maybe_unserialize( get_option( 'sfsi_premium_section5_options', false ) );
		}

		$icons                  = "";
		$sfsi_plus_sticky_icons = ( isset( $option8["sfsi_plus_sticky_icons"] ) ? $option8["sfsi_plus_sticky_icons"] : null );
		if ( is_null( $sfsi_plus_sticky_icons ) ) {
			return "";
		}

		ob_start();
		?>
        <style>
            .sfsi_premium_desktop_display {
                display: none;
            }

            @media (min-width: <?php echo $sfsi_plus_sticky_icons['settings']['desktop_width'] ?>px) {
                .sfsi_premium_desktop_display {
                    display: block !important;
                }
            }

            .sfsi_premium_mobile_display {
                display: none;
            }

            @media (max-width: <?php echo $sfsi_plus_sticky_icons['settings']['mobile_width'] ?>px) {
                .sfsi_premium_mobile_display {
                    display: flex !important;
                    z-index: 10000;
                }

                .sfsi_premium_sticky_icon_item_container.sfsi_premium_sticky_custom_icon {
                    width: inherit !important;
                }
            }
        </style>

		<?php
		if ( $sfsi_plus_sticky_icons['settings']['desktop'] == "yes" && ! wp_is_mobile() ) {
			$mouse_hover_effect = sfsi_premium_mouseOver_effect_classlist();
			$icons              .= "\t<div class='$mouse_hover_effect sfsi_premium_desktop_display sfsi_premium_sticky_icons_container sfsi_premium_sticky_" . strtolower( $sfsi_plus_sticky_icons['settings']['desktop_placement'] ) . "_button_container sfsi_premium_sticky_" . strtolower( $sfsi_plus_sticky_icons['settings']['desktop_placement_direction'] ) . "  ' style='text-align:center;' >";
		} elseif ( $sfsi_plus_sticky_icons['settings']['mobile'] == "yes" && wp_is_mobile() ) {
			$icons .= "\t<div class='sfsi_premium_mobile_display sfsi_premium_sticky_mobile_icons_container sfsi_premium_sticky_mobile_" . strtolower( $sfsi_plus_sticky_icons['settings']['mobile_placement'] ) . "  ' style='text-align:center;' >";
		}
		?>

        <div class="sfsi_premium_sticky_icons_count sfsi_premium_sticky_count_container"
             style='text-align:center;display:<?php echo $sfsi_plus_sticky_icons['settings']['counts'] == "yes" ? "block" : "none"; ?> ;background-color:<?php echo $sfsi_plus_sticky_icons['settings']['bg_color']; ?>;color:<?php echo $sfsi_plus_sticky_icons['settings']['color']; ?>;'>
		<span class="sfsi_premium_count" style="color:<?php echo $sfsi_plus_sticky_icons['settings']['color']; ?>; ">
			<?php
			//  var_dump($sfsi_plus_sticky_icons['default_icons']);
			echo sfsi_premium_sticky_total_count( $sfsi_plus_sticky_icons['default_icons'] ); ?></span>
            <span class="sfsi_premium_count_text"
                  style="color:<?php echo $sfsi_plus_sticky_icons['settings']['color']; ?>;">
			<?php echo $sfsi_plus_sticky_icons['settings']["share_count_text"]; ?></span>
        </div>
		<?php
		$icons .= ob_get_contents();
		ob_end_clean();
		?>
        <div>
		<?php

		$socialObj = new sfsi_plus_SocialHelper();

		$is_pinterest = false;
		sfsi_set_sticky_icon( $option8 );

		foreach ( $sfsi_plus_sticky_icons['default_icons'] as $icon => $icon_config ) {

			/* Allow only active sharing options */
			if ( isset( $icon_config['active'] ) && 'yes' !== $icon_config['active'] ) {
				continue;
			}

			$current_url = $socialObj->sfsi_get_custom_share_link( strtolower( $icon ), $option5 );
			switch ( $icon ) {
				case "facebook":
					$share_url = "https://www.facebook.com/sharer/sharer.php?u=" . trailingslashit( $current_url );
					break;
				case "Twitter":
					$twitter_text = urlencode( $socialObj->sfsi_get_custom_tweet_text( $option5 ) );
					$share_url    = "https://x.com/intent/post?text=" . $twitter_text . "&url=";
					break;
				case "Follow":
					if ( isset( $option2['sfsi_plus_email_icons_functions'] ) && $option2['sfsi_plus_email_icons_functions'] == 'sf' ) {
						$share_url = ( isset( $option2['sfsi_plus_email_url'] ) )
							? $option2['sfsi_plus_email_url']
							: 'https://specificfeeds.com/follow';
					} elseif ( isset( $option2['sfsi_plus_email_icons_functions'] ) && $option2['sfsi_plus_email_icons_functions'] == 'contact' ) {
						$share_url = ( isset( $option2['sfsi_plus_email_icons_contact'] ) && ! empty( $option2['sfsi_plus_email_icons_contact'] ) )
							? "mailto:" . $option2['sfsi_plus_email_icons_contact']
							: 'javascript:';
					} elseif ( isset( $option2['sfsi_plus_email_icons_functions'] ) && $option2['sfsi_plus_email_icons_functions'] == 'page' ) {
						$share_url = ( isset( $option2['sfsi_plus_email_icons_pageurl'] ) && ! empty( $option2['sfsi_plus_email_icons_pageurl'] ) )
							? $option2['sfsi_plus_email_icons_pageurl']
							: 'javascript:';
					} elseif ( isset( $option2['sfsi_plus_email_icons_functions'] ) && $option2['sfsi_plus_email_icons_functions'] == 'share_email' ) {
						$subject = stripslashes( $option2['sfsi_plus_email_icons_subject_line'] );
						$subject = str_replace( '${title}', $socialObj->sfsi_get_the_title(), $subject );
						$subject = str_replace( '"', '', str_replace( "'", '', $subject ) );
						$subject = html_entity_decode( strip_tags( $subject ), ENT_QUOTES, 'UTF-8' );
						$subject = str_replace( "%26%238230%3B", "...", $subject );
						$subject = rawurlencode( $subject );

						$body      = stripslashes( $option2['sfsi_plus_email_icons_email_content'] );
						$body      = str_replace( '${title}', $socialObj->sfsi_get_the_title(), $body );
						$body      = str_replace( '${link}', trailingslashit( $socialObj->sfsi_get_custom_share_link( 'email', $option5 ) ), $body );
						$body      = str_replace( '"', '', str_replace( "'", '', $body ) );
						$body      = html_entity_decode( strip_tags( $body ), ENT_QUOTES, 'UTF-8' );
						$body      = str_replace( "%26%238230%3B", "...", $body );
						$body      = rawurlencode( $body );
						$share_url = "mailto:?subject=$subject&body=$body";
					} else {
						$share_url = ( isset( $option2['sfsi_plus_email_url'] ) )
							? $option2['sfsi_plus_email_url']
							: 'https://specificfeeds.com/follow';
					}
					break;

				case "pinterest":
					$share_url    = 'https://www.pinterest.com/pin/create/link/?url=' . urlencode( $current_url ) . '&media=' . $socialObj->sfsi_pinit_image( $option5 ) . '&description=' . $socialObj->sfsi_pinit_description( $option5 );
					$is_pinterest = true;
					break;

				case "Linkedin":
					$share_url = "http://www.linkedin.com/shareArticle?mini=true&url=" . urlencode( $current_url );
					break;
				case "Whatsapp":
					$share_url = wp_is_mobile() ? 'https://api.whatsapp.com/send?text=' . urlencode( $current_url ) : 'https://web.whatsapp.com/send?text=' . urlencode( $current_url );
					break;
				case "vk":
					$share_url = "http://vk.com/share.php?url=" . trailingslashit( urlencode( $current_url ) );
					break;
				case "Odnoklassniki":
					$share_url = "https://connect.ok.ru/offer?url=" . trailingslashit( urlencode( $current_url ) );
					break;
				case "Telegram":
					$share_url = "https://telegram.me/share/url?url=" . trailingslashit( urlencode( $current_url ) );
					break;
				case "Weibo":
					$share_url = "http://service.weibo.com/share/share.php?url=" . trailingslashit( urlencode( $current_url ) );
					break;
				case "QQ2":
					$share_url = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=" . trailingslashit( urlencode( $current_url ) );
					break;
				case "xing":
					$share_url = "https://www.xing.com/app/user?op=share&url=" . trailingslashit( urlencode( $current_url ) );
					break;
				case "mastodon":
					$share_url = "https://mastodon.social/share?text=" . trailingslashit( urlencode( $current_url ) );
					break;
			}
			if ( false == $is_pinterest || ( true == $is_pinterest && $icon_config['url'] !== "" ) ) {
				$mouse_hover_effect = sfsi_premium_mouseOver_effect_classlist();

				$icons .= "<a " . sfsi_plus_checkNewWindow() . " href='" . ( $icon_config['url'] == "" ? $share_url : do_shortcode( $icon_config['url'] ) ) . "'>";
				$icons .= "<div class='$mouse_hover_effect sfsi_premium_sticky_icon_item_container sfsi_premium_responsive_icon_" . strtolower( $icon ) . "_container' >";
				$icons .= "<img alt='' style='max-height: 25px;display:unset;margin:0' class='sfsi_premium_wicon' src='" . SFSI_PLUS_PLUGURL . "images/responsive-icon/" . $icon . ( 'Follow' === $icon ? '.png' : '.svg' ) . "'>";
				$icons .= "</div>";
				$icons .= "</a>";
			} else {
				$icons2 = "<div class='sfsi_premium_sticky_icon_item_container  sfsi_premium_responsive_icon_" . strtolower( $icon ) . "_container' >";
				$icons2 .= "<img alt='' style='max-height: 25px' onclick='event.target.parentNode.click()' src='" . SFSI_PLUS_PLUGURL . "images/responsive-icon/" . $icon . '.svg' . "'>";
				$icons2 .= "</div>";
				$icons  .= $socialObj->sfsi_PinIt( '', '', $current_url, $icons2, 1, '' );
			}
			$is_pinterest = false;
		}
		$sfsi_plus_sticky_icons_custom_icons = array();

		if ( ! isset( $sfsi_plus_sticky_icons['custom_icons'] ) || ! empty( $sfsi_plus_sticky_icons['custom_icons'] ) ) {
			$sfsi_plus_sticky_icons_custom_icons = $sfsi_plus_sticky_icons['custom_icons'];
		} else {
			$count = 5;
			for ( $i = 0; $i < $count; $i ++ ) {
				array_push( $sfsi_plus_sticky_icons_custom_icons, array(
					'added'    => 'no',
					'active'   => 'no',
					'text'     => __( 'Share', 'ultimate-social-media-plus' ),
					'bg-color' => '#729fcf',
					'url'      => '',
					'icon'     => ''
				) );
			}
		}

		foreach ( $sfsi_plus_sticky_icons_custom_icons as $icon => $icon_config ) {

			/* Allow only active sharing options */
			if ( ( isset( $icon_config['active'] ) && 'yes' === $icon_config['active'] ) && ( isset( $icon_config['added'] ) && 'yes' === $icon_config['added'] ) ) {
				$mouse_hover_effect = sfsi_premium_mouseOver_effect_classlist();

				$current_url = $socialObj->sfsi_get_custom_share_link( strtolower( $icon ), $option5 );
				$icons       .= "<a " . sfsi_plus_checkNewWindow() . " href='" . ( $icon_config['url'] == "" ? "" : do_shortcode( $icon_config['url'] ) ) . "'>";
				$icons       .= "<div class='$mouse_hover_effect sfsi_premium_sticky_icon_item_container sfsi_premium_sticky_custom_icon sfsi_premium_sticky_icon_" . strtolower( $icon ) . "_container' style='background-color:" . ( isset( $icon_config['bg-color'] ) ? $icon_config['bg-color'] : '#73d17c' ) . "'>";
				if ( isset( $icon_config['icon'] ) && ! empty( $icon_config['icon'] ) ) {
					$icons .= "<img alt='' style='max-height: 25px;display:unset;margin:0' class='sfsi_premium_wicon' src='" . $icon_config['icon'] . "'>";
				}
				$icons .= "</div>";
				$icons .= "</a>";
			}
		}
		$icons .= "</div></div><!--end responsive_icons-->";

		return $icons;
	endif;
	?>
    </div>
	<?php
}
