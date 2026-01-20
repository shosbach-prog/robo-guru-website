<?php
function sfsi_set_social_icons_desktop_mobile_css_settings_from_question3()
{ ?>

	<?php
		//Don't show on desktop
		if (isset($option8['sfsi_plus_show_on_desktop']) && isset($option8['sfsi_plus_show_on_mobile']) && $option8['sfsi_plus_show_on_desktop'] != 'yes' && $option8['sfsi_plus_show_on_mobile'] != 'yes') {
			?>
		.widget.sfsi_plus, #sfsi_plus_floater, .sfsibeforpstwpr, .sfsiaftrpstwpr
		{
		display: none;
		}
	<?php
		} elseif (isset($option8['sfsi_plus_show_on_desktop']) && $option8['sfsi_plus_show_on_desktop'] != 'yes') {
			?>
		.widget.sfsi_plus, #sfsi_plus_floater, .sfsibeforpstwpr, .sfsiaftrpstwpr
		{
		display: none;
		}
		@media (max-width: 767px)
		{
		.widget.sfsi_plus, #sfsi_plus_floater, .sfsibeforpstwpr, .sfsiaftrpstwpr
		{
		display: block;
		}
		}
	<?php
		} elseif (isset($option8['sfsi_plus_show_on_mobile']) && $option8['sfsi_plus_show_on_mobile'] != 'yes') {
			?>
		@media (max-width: 767px)
		{
		.widget.sfsi_plus, #sfsi_plus_floater, .sfsibeforpstwpr, .sfsiaftrpstwpr
		{
		display: none;
		}
		}
	<?php
		}
		?>
	<?php }


	function sfsi_get_icon_space_and_container_width($location)
	{

		$returnObj = new StdClass();
		$returnObj->iconspace = '';
		$returnObj->iconcontainerwidth = '';
		$extra = 4.5;
		$option8 			= maybe_unserialize(get_option('sfsi_premium_section8_options', false));
		$option5 			= maybe_unserialize(get_option('sfsi_premium_section5_options', false));
		$sfsi_premium_is_mobile = wp_is_mobile();

		if (isset($option5['sfsi_plus_mobile_icon_alignment_setting']) && 'yes' === $option5['sfsi_plus_mobile_icon_alignment_setting'] && $sfsi_premium_is_mobile) {
			$icons_per_row   = isset($option5['sfsi_plus_mobile_icons_perRow']) && !empty($option5['sfsi_plus_mobile_icons_perRow']) && $option5['sfsi_plus_mobile_icons_perRow'] > 1 ? intval($option5['sfsi_plus_mobile_icons_perRow']) : 5;
		} else {
			$icons_per_row = isset($option5['sfsi_plus_icons_perRow']) && !empty($option5['sfsi_plus_icons_perRow']) && $option5['sfsi_plus_icons_perRow'] > 1 ? intval($option5['sfsi_plus_icons_perRow']) : 5;
		}
		if ($sfsi_premium_is_mobile && isset($option8["sfsi_plus_mobile_" . $location]) && $option8["sfsi_plus_mobile_" . $location] == "yes") {
			$icons_space 	 = ($option5['sfsi_plus_mobile_icon_setting'] == "yes") ? $option5['sfsi_plus_icons_mobilespacing'] : $option5['sfsi_plus_icons_spacing'];
			$icons_size 	 = ($option5['sfsi_plus_mobile_icon_setting'] == "yes") ? $option5['sfsi_plus_icons_mobilesize'] : $option5['sfsi_plus_icons_size'];
			$mkeyName = "sfsi_plus_" . $location . "_mobile_horizontal_verical_Alignment";

			if (isset($option8[$mkeyName]) &&  "Vertical" == $option8[$mkeyName]) {
				$icons_per_row = 1;
			}

			if (isset($option8[$mkeyName]) &&  "Horizontal" == $option8[$mkeyName]) {

				$option5['sfsi_plus_mobile_icons_perRow'] = ("no" == $option5['sfsi_plus_mobile_icon_alignment_setting']) ? $icons_per_row : $icons_per_row;

				if (isset($option8["sfsi_plus_icons_total_displaying_mobile_icons"]) && !empty($option8["sfsi_plus_icons_total_displaying_mobile_icons"])) {

					$activeMIconsCount = $option8["sfsi_plus_icons_total_displaying_mobile_icons"];

					if ($icons_per_row > $activeMIconsCount) {
						$icons_per_row = $activeMIconsCount;
					}
				}
			}
		} else {
			$icons_space 		 = $option5['sfsi_plus_icons_spacing'];
			$icons_size 		 = $option5['sfsi_plus_icons_size'];
			$keyName = "sfsi_plus_" . $location . "_horizontal_verical_Alignment";

			if (isset($option8[$keyName]) &&  "Vertical" == $option8[$keyName]) {
				$icons_per_row = 1;
			}

			if (isset($option8[$keyName]) && "Horizontal" == $option8[$keyName]) {

				if (isset($option8["sfsi_plus_icons_total_displaying_desktop_icons"]) && !empty($option8["sfsi_plus_icons_total_displaying_desktop_icons"])) {

					$activeIconsCount = $option8["sfsi_plus_icons_total_displaying_desktop_icons"];

					if ($icons_per_row > $activeIconsCount) {
						$icons_per_row = $activeIconsCount;
					}
				}
			}
			if (wp_is_mobile()) {
				$icons_space 	 = ($option5['sfsi_plus_mobile_icon_setting'] == "yes") ? $option5['sfsi_plus_icons_mobilespacing'] : $option5['sfsi_plus_icons_spacing'];
				$icons_size 	 = ($option5['sfsi_plus_mobile_icon_setting'] == "yes") ? $option5['sfsi_plus_icons_mobilesize'] : $option5['sfsi_plus_icons_size'];
			}

			if (wp_is_mobile() && $option8["sfsi_plus_mobile_" . $location] == "yes") {
				$icons_m_per_row   = isset($option5['sfsi_plus_mobile_icons_perRow']) && !empty($option5['sfsi_plus_mobile_icons_perRow']) && $option5['sfsi_plus_mobile_icons_perRow'] > 1 ? intval($option5['sfsi_plus_mobile_icons_perRow']) : 5;

				$icons_per_row = $icons_m_per_row;

				$mkeyName = "sfsi_plus_" . $location . "_mobile_horizontal_verical_Alignment";

				if (isset($option8[$mkeyName]) &&  "Vertical" == $option8[$mkeyName]) {
					$icons_per_row = 1;
				}

				if (isset($option8[$mkeyName]) &&  "Horizontal" == $option8[$mkeyName]) {

					$option5['sfsi_plus_mobile_icons_perRow'] = ("no" == $option5['sfsi_plus_mobile_icons_perRow']) ? $icons_d_per_row : $icons_m_per_row;

					if (isset($option8["sfsi_plus_icons_total_displaying_mobile_icons"]) && !empty($option8["sfsi_plus_icons_total_displaying_mobile_icons"])) {

						$activeMIconsCount = $option8["sfsi_plus_icons_total_displaying_mobile_icons"];
					}
				}
			}
		}
		// echo "/* sfsi_get_icon_space_and_container_width ".$location;
		// var_dump("icons_per_row",$icons_per_row,$icons_size,$icons_space);
		// echo "*/";
		// if($location=="widget"){
		// 	var_dump(wp_is_mobile(),$location,$icons_per_row,$icons_space,$icons_size,"test");die();
		// }
		// $containerWidth = ((int) $icons_per_row * round( (int) $icons_size + (int) $icons_space + $extra ));
		$containerWidth = ((int) $icons_per_row * round((int) $icons_size + (int) $icons_space));


		$returnObj->iconspace 		   = $icons_space;
		$returnObj->iconcontainerwidth = $containerWidth;

		return $returnObj;
	}

	function sfsi_get_beforeAfterposts_icon_space_and_container_width($location)
	{

		$returnObj = new StdClass();
		$returnObj->iconspace = '';
		$returnObj->iconcontainerwidth = 'auto';

		$icons_per_row      = false;
		$containerWidth     = 'auto';

		$option8 			= maybe_unserialize(get_option('sfsi_premium_section8_options', false));
		$option5 			= maybe_unserialize(get_option('sfsi_premium_section5_options', false));
		$option1  			= maybe_unserialize(get_option('sfsi_premium_section1_options', false));

		//----test---



		$icons = "";
		$iconsCount = 0;
		if ($option5['sfsi_plus_mobile_icon_alignment_setting'] == 'yes') {
			$icons_per_row 	= $option5['sfsi_plus_mobile_icons_perRow'];
		} else {
			$icons_per_row 	= $option5['sfsi_plus_icons_perRow'];
		}

		if (wp_is_mobile() && sfsi_premium_is_any_standard_icon_selected()) {

			$arrOrderIcons = sfsi_premium_get_icons_order($option5, $option1);
			$arrData       = sfsi_premium_get_icons_html($arrOrderIcons, $option1);

			$icons .= $arrData['html'];
			$iconsCount = $arrData['count'];
			$final_total_icons = ($icons_per_row > $iconsCount) ? $iconsCount : $icons_per_row;

			$option8["sfsi_plus_icons_total_displaying_mobile_icons"] = $final_total_icons;
			update_option("sfsi_premium_section8_options", serialize($option8));
		}


		// update_option("sfsi_premium_section8_options", serialize($sfsi_section8));
		//----update option of total icons required-----
		//----test---

		if (isset($option8["sfsi_plus_mobile_" . $location])) {

			if ($location !== "beforeafterposts") {
				if ($option5['sfsi_plus_mobile_icon_setting'] == "yes" && wp_is_mobile()) {
					$icons_size = $option5['sfsi_plus_icons_mobilesize'];
					$icons_space = $option5['sfsi_plus_icons_mobilespacing'];
				} else {
					$icons_size = $option5['sfsi_plus_icons_size'];
					$icons_space = $option5['sfsi_plus_icons_spacing'];
				}
			} else {
				if ($option8['sfsi_plus_mobile_size_space_beforeafterposts'] == "yes" && wp_is_mobile()) {
					$icons_size 	= $option8['sfsi_plus_post_mobile_icons_size'];
					$icons_space 	= $option8['sfsi_plus_post_mobile_icons_spacing'];
				} else {
					$icons_size 	= $option8['sfsi_plus_post_icons_size'];
					$icons_space 	= $option8['sfsi_plus_post_icons_spacing'];
				}
			}
			if (!wp_is_mobile()) {
				// Check setting from Question 3-> Show them before or after posts -> Alignments
				if (isset($option8["sfsi_plus_" . $location . "_horizontal_verical_Alignment"]) && $option8["sfsi_plus_" . $location . "_horizontal_verical_Alignment"] == "Vertical") {
					$icons_per_row  = 1;
				}

				// Check setting from Question 3-> Show them before or after posts -> Alignments	    		
				if (isset($option8["sfsi_plus_" . $location . "_horizontal_verical_Alignment"]) && $option8["sfsi_plus_" . $location . "_horizontal_verical_Alignment"] == "Horizontal") {
					if (isset($option5['sfsi_plus_icons_perRow'])) {
						$icons_per_row  = $option5["sfsi_plus_icons_perRow"];
					}
				}
			}

			if (wp_is_mobile()) {
				if (isset($option8["sfsi_plus_" . $location . "_mobile_horizontal_verical_Alignment"]) && $option8["sfsi_plus_" . $location . "_mobile_horizontal_verical_Alignment"] == "Vertical") {
					$icons_per_row  = 1;
				}

				if (isset($option8["sfsi_plus_" . $location . "_horizontal_verical_Alignment"]) && $option8["sfsi_plus_" . $location . "_mobile_horizontal_verical_Alignment"] == "Horizontal") {

					if ($option5['sfsi_plus_mobile_icon_alignment_setting'] == "yes" && isset($option5['sfsi_plus_mobile_icons_perRow'])) {
						$icons_per_row  = $option5["sfsi_plus_mobile_icons_perRow"];
					} else {
						$icons_per_row  = $option5["sfsi_plus_icons_perRow"];
					}
				}
			}

			if (false !== $icons_per_row) {
				$containerWidth =  $icons_per_row * ($icons_size + $icons_space);

				if (wp_is_mobile()) {
					if ($icons_per_row > $option8["sfsi_plus_icons_total_displaying_mobile_icons"]) {
						$containerWidth = $option8["sfsi_plus_icons_total_displaying_mobile_icons"] * ($icons_size + $icons_space);
					}
				} else {
					if (sfsi_premium_is_any_standard_icon_selected()) {
						$newDesktopIconOrder = sfsi_premium_desktop_icons_order($option5, $option1);
						$arrData       		 = sfsi_premium_get_icons_html($newDesktopIconOrder, $option1);

						$iconsCount = $arrData['count'];
					}
					$option8["sfsi_plus_icons_total_displaying_desktop_icons"] = $iconsCount;
					update_option("sfsi_premium_section8_options", serialize($option8));
					if ($icons_per_row > $option8["sfsi_plus_icons_total_displaying_desktop_icons"]) {
						$containerWidth = $option8["sfsi_plus_icons_total_displaying_desktop_icons"] * ($icons_size + $icons_space);
					}
				}
			}
			// var_dump($icons_per_row);die();

			// var_dump($icons_per_row,$containerWidth,$option8["sfsi_plus_icons_total_displaying_desktop_icons"],'testing1');
			// echo "/* sfsi_get_icon_space_and_container_width ".$location;
			// var_dump("icons_per_row",$icons_per_row,$icons_size,$icons_space);
			// echo "*/";


			$returnObj->iconspace 		   = $icons_space;
			$returnObj->iconcontainerwidth = $containerWidth;
		}
		return $returnObj;
	}

	add_action( 'wp_footer', 'sfsi_plus_addStyleFunction', 0 );
	function sfsi_plus_addStyleFunction()
	{
		if (sfsi_is_icons_showing_on_front() && false != License_Manager::validate_license()) {

			$option9 			= maybe_unserialize(get_option('sfsi_premium_section9_options', false));
			$option8 			= maybe_unserialize(get_option('sfsi_premium_section8_options', false));
			$option5 			= maybe_unserialize(get_option('sfsi_premium_section5_options', false));

			$sfsi_plus_feediid 	= sanitize_text_field(get_option('sfsi_premium_feed_id'));
			$url 				= "https://api.follow.it/subscription-form/";
			echo $return = '';

			?>
		<script>
			if (typeof jQuery != 'undefined') {

				function sfsi_plus_align_icons_center_orientation(_centerPosition) {

					function applyOrientation() {

						var elemF = jQuery('#sfsi_plus_floater');

						if (elemF.length > 0) {

							switch (_centerPosition) {
								case 'center-right':
								case 'center-left':
									var toptalign = (jQuery(window).height() - elemF.height()) / 2;
									elemF.css('top', toptalign);
									break;

								case 'center-top':
								case 'center-bottom':
									var leftalign = (jQuery(window).width() - elemF.width()) / 2;
									elemF.css('left', leftalign);

									break;
							}
						}
					}

					var prev_onresize = window.onresize;
					window.onresize = function(event) {

						if ('function' === typeof prev_onload) {
							prev_onresize(), applyOrientation();
						} else {
							applyOrientation();
						}
					}
				}

				function sfsi_plus_processfurther(ref) {
					var feed_id = '<?php echo $sfsi_plus_feediid ?>';
					var feedtype = 8;
					var email = jQuery(ref).find('input[name="email"]').val();
					var filter = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					if ((email != "Enter your email") && (filter.test(email))) {
						if (feedtype == "8") {
							var url = "<?php echo $url; ?>" + feed_id + "/" + feedtype;
							window.open(url, "popupwindow", "scrollbars=yes,width=1080,height=760");
							return true;
						}
					} else {
						alert("Please enter email address");
						jQuery(ref).find('input[name="email"]').focus();
						return false;
					}
				}
			}
		</script>
		<style type="text/css">
		<?php

			$option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));

			// Widget icons
			/*if (isset($option8['sfsi_plus_show_via_widget']) && $option8['sfsi_plus_show_via_widget'] == "yes") {

				$widgetObj = sfsi_get_icon_space_and_container_width('widget');

				if (isset($widgetObj->iconcontainerwidth)) { ?>.sfsi_plus_widget_sub_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				width: <?php echo $widgetObj->iconcontainerwidth; ?>px <?php echo  $widgetObj->iconcontainerwidth !== 5 ? '!important' : ''; ?>;
				}

				<?php }
			}

			// Shortcode icons
			if ( isset($option8['sfsi_plus_place_item_manually'] ) && $option8['sfsi_plus_place_item_manually'] == "yes" ) {

				$shortcodeObj = sfsi_get_icon_space_and_container_width( 'shortcode' );

				if (isset($shortcodeObj->iconcontainerwidth)) { ?>.sfsi_plus_shortcode_container .sfsiplus_norm_row.sfsi_plus_wDiv {
					width: <?php echo $shortcodeObj->iconcontainerwidth; ?>px <?php echo  $shortcodeObj->iconcontainerwidth !== 5 ? '!important' : ''; ?>;
				}

				<?php }
			}*/

			// Floating icons  
			if ( isset( $option8['sfsi_plus_float_on_page'] ) && $option8['sfsi_plus_float_on_page'] == "yes" ) {

				$floatObj = sfsi_get_icon_space_and_container_width( 'float' );

				if (isset($floatObj->iconcontainerwidth)) { ?>
				/* @media screen and (min-width: 600px) {
						#sfsi_plus_floater{
						width:<?php echo $floatObj->iconcontainerwidth; ?>px !important;
					}	
				} */

			<?php }

			$_mobile_float = isset($option5['sfsi_plus_mobile_float']) && !empty($option5['sfsi_plus_mobile_float']) ? $option5['sfsi_plus_mobile_float'] : false;

			$_hv_alignment = isset($option5['sfsi_plus_float_horizontal_verical_Alignment']) && !empty($option5['sfsi_plus_float_horizontal_verical_Alignment']) ? $option5['sfsi_plus_float_horizontal_verical_Alignment'] : false;

			if (($_mobile_float == 'no' && $_hv_alignment == "Horizontal") || ($_mobile_float == 'yes' && $_hv_alignment == "Horizontal")) { ?>.sfsi_plus_mobile_floater .sfsi_premium_wicons {
				display: inline-block !important;
			}

			<?php }
			}

			// Before / After posts icons
			/*if (isset($option8['sfsi_plus_show_item_onposts']) && $option8['sfsi_plus_show_item_onposts'] == "yes" && $option8['sfsi_plus_display_button_type'] == 'normal_button') {

				$beforeAfterPostsObj = sfsi_get_beforeAfterposts_icon_space_and_container_width('beforeafterposts');
				// var_dump($beforeAfterPostsObj,'before_after_test');
				if (isset($beforeAfterPostsObj->iconcontainerwidth) && is_numeric($beforeAfterPostsObj->iconcontainerwidth)) {

					?>#sfsi_plus_wDivothrWid { width: <?php echo $beforeAfterPostsObj->iconcontainerwidth; ?>px !important; }
				<?php }
			}*/

			// Before / After posts icons

			if ($option5['sfsi_plus_tooltip_alighn'] == 'down') {
			?>.sfsi_plus_Tlleft {
				top: 110%;
				left: 50%;
				bottom: auto
			}

			.sfsi_plus_tool_tip_2 .bot_arow {
				top: -10px;
			}

			<?php
					}
					?><?php

								if (wp_is_mobile() && $option5['sfsi_plus_mobile_icon_alignment_setting'] == 'yes') {
									if ($option5['sfsi_plus_mobile_icons_Alignment_via_widget'] == 'left') {
										?>.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				text-align: left;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				float: left;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						} elseif ($option5['sfsi_plus_mobile_icons_Alignment_via_widget'] == 'right') {
							?>.sfsi_plus.sfsi_plus_widget_main_container {
				text-align: right;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				float: right;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
				float: right;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						} elseif ($option5['sfsi_plus_mobile_icons_Alignment_via_widget'] == 'center') {
							?>.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				text-align: center;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
				float: none;
				margin: 0 auto;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						}
						if ($option5['sfsi_plus_mobile_icons_Alignment_via_shortcode'] == 'left') {
							?>.sfsi_plus_shortcode_container {
				/* float: left; */
			}

			.sfsi_plus_shortcode_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
			}

			.sfsi_plus_shortcode_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						} elseif ($option5['sfsi_plus_mobile_icons_Alignment_via_shortcode'] == 'right') {
							?>.sfsi_plus_shortcode_container {
				/* float: right; */
			}

			.sfsi_plus_shortcode_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
				float: right;
			}

			.sfsi_plus_shortcode_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						} elseif ($option5['sfsi_plus_mobile_icons_Alignment_via_shortcode'] == 'center') {
							?>.sfsi_plus_shortcode_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
				float: none;
				margin: 0 auto;
			}

			.sfsi_plus_shortcode_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						}
					} else {
						if ($option5['sfsi_plus_icons_Alignment_via_widget'] == 'left') {
							?>.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				text-align: left;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				float: left;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						} elseif ($option5['sfsi_plus_icons_Alignment_via_widget'] == 'right') {
							?>.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				/* text-align: right; */
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				float: right;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
				float: right;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						} elseif ($option5['sfsi_plus_icons_Alignment_via_widget'] == 'center') {
							?>.sfsi_plus_widget.sfsi_plus_widget_sub_container {
				text-align: center;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
				float: none;
				margin: 0 auto;
			}

			.sfsi_plus_widget.sfsi_plus_widget_sub_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						}

						if ($option5['sfsi_plus_icons_Alignment_via_shortcode'] == 'left') {
							?>.sfsi_plus_shortcode_container {
				/* float: left; */
			}

			.sfsi_plus_shortcode_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
			}

			.sfsi_plus_shortcode_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						} elseif ($option5['sfsi_plus_icons_Alignment_via_shortcode'] == 'right') {
							?>.sfsi_plus_shortcode_container {
				/* float: right; */
			}

			.sfsi_plus_shortcode_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
				float: right;
			}

			.sfsi_plus_shortcode_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						} elseif ($option5['sfsi_plus_icons_Alignment_via_shortcode'] == 'center') {
							?>.sfsi_plus_shortcode_container .sfsiplus_norm_row.sfsi_plus_wDiv {
				position: relative !important;
				float: none;
				margin: 0 auto;
			}

			.sfsi_plus_shortcode_container .sfsi_plus_holders {
				display: none;
			}

			<?php
						}
					}
					?>.sfsiaftrpstwpr .sfsi_plus_Sicons div:first-child span,
			.sfsibeforpstwpr .sfsi_plus_Sicons div:first-child span {
				font-size: <?php echo ($option8['sfsi_plus_textBefor_icons_font_size'] != 0) ? $option8['sfsi_plus_textBefor_icons_font_size'] : 20; ?>px;
				
				<?php if(isset($option8['sfsi_plus_textBefor_icons_font_type']) && $option8['sfsi_plus_textBefor_icons_font_type'] == 'bold') {?>
					font-weight: <?php echo $option8['sfsi_plus_textBefor_icons_font_type']; ?> !important;
				<?php } else { ?>
					font-style: <?php echo $option8['sfsi_plus_textBefor_icons_font_type']; ?> !important;
				<?php } ?>

				font-family: <?php echo $option8['sfsi_plus_textBefor_icons_font']; ?>;
				color: <?php echo $option8['sfsi_plus_textBefor_icons_fontcolor']; ?>;
			}

			.sfsibeforpstwpr,
			.sfsiaftrpstwpr {
				margin-top: <?php echo (!empty($option8['sfsi_plus_marginAbove_postIcon'])) ? $option8['sfsi_plus_marginAbove_postIcon'] : "5"; ?>px !important;
				margin-bottom: <?php echo (!empty($option8['sfsi_plus_marginBelow_postIcon'])) ? $option8['sfsi_plus_marginBelow_postIcon'] : "5"; ?>px !important;
			}

			.sfsi_plus_rectangle_icons_shortcode_container {
				margin-top: <?php echo (!empty($option8['sfsi_plus_marginAbove_postIcon'])) ? $option8['sfsi_plus_marginAbove_postIcon'] : "5"; ?>px !important;
				margin-bottom: <?php echo (!empty($option8['sfsi_plus_marginBelow_postIcon'])) ? $option8['sfsi_plus_marginBelow_postIcon'] : "5"; ?>px !important;
			}

			.sfsi_plus_subscribe_Popinner {
				<?php if ($option9['sfsi_plus_form_adjustment'] == 'yes') : ?>width: 100% !important;
				height: auto !important;
				<?php else : ?>width: <?php echo $option9['sfsi_plus_form_width'] ?>px !important;
				height: <?php echo $option9['sfsi_plus_form_height'] ?>px !important;
				<?php endif; ?><?php if ($option9['sfsi_plus_form_border'] == 'yes') : ?>border: <?php echo $option9['sfsi_plus_form_border_thickness'] . "px solid " . $option9['sfsi_plus_form_border_color']; ?> !important;
				<?php endif; ?>padding: 18px 0px !important;
				background-color: <?php echo $option9['sfsi_plus_form_background'] ?> !important;
			}

			@media screen and (max-width: 768px) {
				.sfsi_premium_responsive_fixed_width .sfsi_premium_responsive_icon_item_container.sfsi_premium_medium_button {
					<?php
							$sfsi_premium_responsive_icons = $option8["sfsi_plus_responsive_icons"];

							if ($sfsi_premium_responsive_icons["settings"]["responsive_mobile_icons"] == "no") {
								?>    
								padding: 10px !important;
								text-align: center !important;
								display: flex;
								align-items: center;
								justify-content: center;
					<?php }else{?>
								width: 37px !important;
								height: 37px !important;
								padding: 10px !important;
								border-radius: 30px !important;
								text-align: center !important;
								display: flex;
								align-items: center;
								justify-content: center;
					<?php } ?>
				}
			}

			.sfsi_plus_subscribe_Popinner form {
				margin: 0 20px !important;
			}

			.sfsi_plus_subscribe_Popinner h5 {
				font-family: <?php echo $option9['sfsi_plus_form_heading_font'] ?>;
				<?php if ($option9['sfsi_plus_form_heading_fontstyle'] != 'bold') { ?>font-style: <?php echo $option9['sfsi_plus_form_heading_fontstyle'] ?> !important;
				<?php } else { ?>font-weight: <?php echo $option9['sfsi_plus_form_heading_fontstyle'] ?> !important;
				<?php } ?>color: <?php echo $option9['sfsi_plus_form_heading_fontcolor'] ?> !important;
				font-size: <?php echo $option9['sfsi_plus_form_heading_fontsize'] . "px" ?> !important;
				text-align: <?php echo $option9['sfsi_plus_form_heading_fontalign'] ?> !important;
				margin: 0 0 10px !important;
				padding: 0 !important;
			}

			.sfsi_plus_subscription_form_field {
				margin: 5px 0 !important;
				width: 100% !important;
				display: inline-flex;
				display: -webkit-inline-flex;
			}

			.sfsi_plus_subscription_form_field input {
				width: 100% !important;
				padding: 10px 0px !important;
			}

			.sfsi_plus_subscribe_Popinner input[type=email] {
				font-family: <?php echo $option9['sfsi_plus_form_field_font'] ?>;
				<?php if ($option9['sfsi_plus_form_field_fontstyle'] != 'bold') { ?>font-style: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } else { ?>font-weight: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } ?>color: <?php echo $option9['sfsi_plus_form_field_fontcolor'] ?> !important;
				font-size: <?php echo $option9['sfsi_plus_form_field_fontsize'] . "px" ?> !important;
				text-align: <?php echo $option9['sfsi_plus_form_field_fontalign'] ?> !important;
			}

			.sfsi_plus_subscribe_Popinner input[type=email]::-webkit-input-placeholder {
				font-family: <?php echo $option9['sfsi_plus_form_field_font'] ?> !important;
				<?php if ($option9['sfsi_plus_form_field_fontstyle'] != 'bold') { ?>font-style: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } else { ?>font-weight: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } ?>color: <?php echo $option9['sfsi_plus_form_field_fontcolor'] ?> !important;
				font-size: <?php echo $option9['sfsi_plus_form_field_fontsize'] . "px" ?> !important;
				text-align: <?php echo $option9['sfsi_plus_form_field_fontalign'] ?> !important;
			}

			.sfsi_plus_subscribe_Popinner input[type=email]:-moz-placeholder {
				/* Firefox 18- */
				font-family: <?php echo $option9['sfsi_plus_form_field_font'] ?> !important;
				<?php if ($option9['sfsi_plus_form_field_fontstyle'] != 'bold') { ?>font-style: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } else { ?>font-weight: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } ?>color: <?php echo $option9['sfsi_plus_form_field_fontcolor'] ?> !important;
				font-size: <?php echo $option9['sfsi_plus_form_field_fontsize'] . "px" ?> !important;
				text-align: <?php echo $option9['sfsi_plus_form_field_fontalign'] ?> !important;
			}

			.sfsi_plus_subscribe_Popinner input[type=email]::-moz-placeholder {
				/* Firefox 19+ */
				font-family: <?php echo $option9['sfsi_plus_form_field_font'] ?> !important;
				<?php if ($option9['sfsi_plus_form_field_fontstyle'] != 'bold') { ?>font-style: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } else { ?>font-weight: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } ?>color: <?php echo $option9['sfsi_plus_form_field_fontcolor'] ?> !important;
				font-size: <?php echo $option9['sfsi_plus_form_field_fontsize'] . "px" ?> !important;
				text-align: <?php echo $option9['sfsi_plus_form_field_fontalign'] ?> !important;
			}

			.sfsi_plus_subscribe_Popinner input[type=email]:-ms-input-placeholder {
				font-family: <?php echo $option9['sfsi_plus_form_field_font'] ?> !important;
				<?php if ($option9['sfsi_plus_form_field_fontstyle'] != 'bold') { ?>font-style: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } else { ?>font-weight: <?php echo $option9['sfsi_plus_form_field_fontstyle'] ?> !important;
				<?php } ?>color: <?php echo $option9['sfsi_plus_form_field_fontcolor'] ?> !important;
				font-size: <?php echo $option9['sfsi_plus_form_field_fontsize'] . "px" ?> !important;
				text-align: <?php echo $option9['sfsi_plus_form_field_fontalign'] ?> !important;
			}

			.sfsi_plus_subscribe_Popinner input[type=submit] {
				font-family: <?php echo $option9['sfsi_plus_form_button_font'] ?> !important;
				<?php if ($option9['sfsi_plus_form_button_fontstyle'] != 'bold') { ?>font-style: <?php echo $option9['sfsi_plus_form_button_fontstyle'] ?> !important;
				<?php } else { ?>font-weight: <?php echo $option9['sfsi_plus_form_button_fontstyle'] ?> !important;
				<?php } ?>color: <?php echo $option9['sfsi_plus_form_button_fontcolor'] ?> !important;
				font-size: <?php echo $option9['sfsi_plus_form_button_fontsize'] . "px" ?> !important;
				text-align: <?php echo $option9['sfsi_plus_form_button_fontalign'] ?> !important;
				background-color: <?php echo $option9['sfsi_plus_form_button_background'] ?> !important;
			}
		</style>
	<?php }
	}

	function sfsi_plus_custom_css_from_Que6()
	{

		if (sfsi_is_icons_showing_on_front()) {
			$option5   = maybe_unserialize(get_option('sfsi_premium_section5_options', false));

			$customCss = (isset($option5['sfsi_plus_custom_css'])) ? maybe_unserialize($option5['sfsi_plus_custom_css']) : '';
			$customCss = wp_kses($customCss, array('\'', '\"'));
			$customCss = str_replace('&gt;', '>', $customCss);
			ob_start();
			?>
		<style type="text/css">
			<?php echo $customCss; ?>
		</style>
	<?php
			echo ob_get_clean();
		}
	}
	add_action('wp_head', 'sfsi_plus_custom_css_from_Que6');

	function sfsi_plus_custom_admin_css_from_Que6()
	{

		if (isset($_GET['page']) && 'sfsi-plus-options' == $_GET['page']) {
			$option5   = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
			if (isset($option5['sfsi_plus_custom_admin_css']) && is_string($option5['sfsi_plus_custom_admin_css'])) {
				$customCss = (isset($option5['sfsi_plus_custom_admin_css'])) ? maybe_unserialize($option5['sfsi_plus_custom_admin_css']) : '';
			} else if (isset($option5['sfsi_plus_custom_admin_css']) && is_array($option5['sfsi_plus_custom_admin_css'])) {
				$customCss = (isset($option5['sfsi_plus_custom_admin_css'])) ? ($option5['sfsi_plus_custom_admin_css']) : '';
			}
			$customCss = wp_kses($customCss, array('\'', '\"'));
			$customCss = str_replace('&gt;', '>', $customCss);
			ob_start();
			?>
		<style type="text/css">
			<?php echo $customCss; ?>
		</style>
	<?php
			echo ob_get_clean();
		} else {
			?>
		<style type="text/css">
			#adminmenu .wp-menu-image img {
				padding-top: 11px !important;
			}
		</style>
	<?php
		}
	}
	add_action('admin_head', 'sfsi_plus_custom_admin_css_from_Que6');


	function sfsi_icon_tooltip_css_two_options($icon, $tooltip_direction, $tooltip_icons = 2)
	{

		if ($tooltip_direction == 'right') {
			$option5   = maybe_unserialize(get_option('sfsi_premium_section5_options', false));
			$option5['sfsi_plus_icons_size'];
			?>

		<?php
				$size_of_icon = $option5['sfsi_plus_icons_size'];
				$total_padding_size = 18;
				$size_of_tooltip_icon = 20;
				$size_of_tooltip = ($tooltip_icons * $size_of_tooltip_icon) + $total_padding_size;
				$tooltip_center_to_icon = -($size_of_tooltip / 2) + $size_of_icon / 2;
				$size_of_arrow = 21;
				$tooltip_arrow = ($size_of_tooltip / 2) - ($size_of_arrow / 2 + 3);
				?>
		<style type="text/css">
			#sfsi_plus_floater <?php echo $icon; ?>.sfsi_plus_tool_tip_2.sfsi_premium_tooltip_right {
				bottom: <?php echo $tooltip_center_to_icon; ?>px;
				left: <?php echo $size_of_icon; ?>px;
				margin-left: unset !important;
				margin: unset !important;

			}

			#sfsi_plus_floater <?php echo $icon; ?>.sfsi_plus_tool_tip_2.sfsi_premium_tooltip_right .bot_arow {
				bottom: <?php echo $tooltip_arrow ?>px;
				left: 3%;
				transform: rotate(-30deg);
			}
		</style>
	<?php
		} else if ($tooltip_direction == 'left') {
			?>
		<style type="text/css">
			#sfsi_plus_floater <?php echo $icon; ?>.sfsi_plus_tool_tip_2.sfsi_premium_tooltip_left {
				bottom: -54%;
				left: -200%;
			}

			#sfsi_plus_floater <?php echo $icon; ?>.sfsi_plus_tool_tip_2.sfsi_premium_tooltip_left .bot_arow {
				bottom: 22px;
				left: 104%;
				transform: rotate(30deg);

			}
		</style>
<?php
	}
}


	function sfsi_set_sticky_icon()
	{
		$option8 			= maybe_unserialize(get_option('sfsi_premium_section8_options', false));
		?>
	<style type="text/css">
			<?php
		if( !wp_is_mobile()) {

				if (isset($option8['sfsi_plus_sticky_bar']) && $option8['sfsi_plus_sticky_bar'] == 'yes') {
					if (isset($option8['sfsi_plus_sticky_icons']['settings']['desktop']) && $option8['sfsi_plus_sticky_icons']['settings']['desktop'] == "yes") {
						if (isset($option8['sfsi_plus_sticky_icons']['settings']['desktop_placement_direction']) && ($option8['sfsi_plus_sticky_icons']['settings']['desktop_placement_direction']) == "down") {
							?>.sfsi_premium_sticky_icons_container.sfsi_premium_sticky_down {
				top: calc(50% + <?php echo $option8['sfsi_plus_sticky_icons']['settings']['display_position']; ?>px);
			}

			<?php } elseif (isset($option8['sfsi_plus_sticky_icons']['settings']['desktop_placement_direction']) && ($option8['sfsi_plus_sticky_icons']['settings']['desktop_placement_direction']) == "up") {
							?>.sfsi_premium_sticky_icons_container.sfsi_premium_sticky_up {
				top: calc(50% - <?php echo $option8['sfsi_plus_sticky_icons']['settings']['display_position']; ?>px);
			}
			<?php }
					} 
				}
		}

				?>
	</style>
<?php
}

function sfsi_set_tooltip_center_to_icon()
{
	$option2  			= maybe_unserialize(get_option('sfsi_premium_section2_options', false));

	$option5  			= maybe_unserialize(get_option('sfsi_premium_section5_options', false));
	$sfsi_premium_tooltip = $option5['sfsi_plus_tooltip_alighn'];
	if (($option2['sfsi_plus_facebookLike_option'] == "yes" && $option2['sfsi_plus_facebookShare_option'] == "yes" && $option2['sfsi_plus_facebookPage_option'] == "yes")) {

		sfsi_icon_tooltip_css_two_options('#sfsiplusid_facebook', $sfsi_premium_tooltip, 3);
	} else {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_facebook', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_twitter_page'] == "yes" && $option2['sfsi_plus_twitter_followme'] == "yes" && $option2['sfsi_plus_twitter_aboutPage'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_twitter', $sfsi_premium_tooltip, 3);
	} else {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_twitter', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_youtube_page'] == "yes" && $option2['sfsi_plus_youtube_follow'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_youtube', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_pinterest_page'] == "yes" && $option2['sfsi_plus_pinterest_pingBlog'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_pinterest', $sfsi_premium_tooltip);
	}

	// if (($option2['sfsi_plus_linkedin_page']== "yes" &&
	// 	$option2['sfsi_plus_linkedin_follow']== "yes" &&
	// 	$option2['sfsi_plus_linkedin_SharePage']== "yes" &&
	// 	$option2['sfsi_plus_linkedin_recommendBusines']== "yes")) {
	// 	sfsi_icon_tooltip_css_three_or_more_options('#sfsiplusid_linkedin', $sfsi_premium_tooltip);
	// } else if (($option2['sfsi_plus_linkedin_page']== "yes" &&
	// 		$option2['sfsi_plus_linkedin_follow']== "yes" &&
	// 		$option2['sfsi_plus_linkedin_SharePage']== "yes") || ($option2['sfsi_plus_linkedin_recommendBusines']== "yes" &&
	// 		$option2['sfsi_plus_linkedin_follow']== "yes" &&
	// 		$option2['sfsi_plus_linkedin_SharePage']== "yes") || ($option2['sfsi_plus_linkedin_follow']== "yes" &&
	// 		$option2['sfsi_plus_linkedin_follow']== "yes" &&
	// 		$option2['sfsi_plus_linkedin_recommendBusines']== "yes")
	if (($option2['sfsi_plus_linkedin_page'] == "yes" &&
		$option2['sfsi_plus_linkedin_follow'] == "yes" &&
		$option2['sfsi_plus_linkedin_SharePage'] == "yes" &&
		$option2['sfsi_plus_linkedin_recommendBusines'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_linkedin', $sfsi_premium_tooltip, 4);
	} else {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_linkedin', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_yummlyVisit_option'] == "yes" && $option2['sfsi_plus_yummlyShare_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_yummly', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_fbmessengerShare_option'] == "yes" && $option2['sfsi_plus_fbmessengerContact_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_fbmessenger', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_mixShare_option'] == "yes" && $option2['sfsi_plus_mixVisit_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_mix', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_okVisit_option'] == "yes" && $option2['sfsi_plus_okLike_option'] == "yes" && $option2['sfsi_plus_okSubscribe_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_ok', $sfsi_premium_tooltip, 3);
	} else {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_ok', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_telegramShare_option'] == "yes" && $option2['sfsi_plus_telegramMessage_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_telegram', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_vkVisit_option'] == "yes" && $option2['sfsi_plus_vkShare_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_vk', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_weiboVisit_option'] == "yes" && $option2['sfsi_plus_weiboShare_option'] == "yes"  && $option2['sfsi_plus_weiboLike_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_weibo', $sfsi_premium_tooltip, 3);
	} else {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_weibo', $sfsi_premium_tooltip);
	}

	if (($option2['sfsi_plus_wechatFollow_option'] == "yes" && $option2['sfsi_plus_wechatFollow_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_wechat', $sfsi_premium_tooltip, 1);
	}

	if (($option2['sfsi_plus_xingVisit_option'] == "yes" && $option2['sfsi_plus_xingFollow_option'] == "yes" && $option2['sfsi_plus_xingShare_option'] == "yes")) {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_xing', $sfsi_premium_tooltip, 3);
	} else {
		sfsi_icon_tooltip_css_two_options('#sfsiplusid_xing', $sfsi_premium_tooltip);
	}
}