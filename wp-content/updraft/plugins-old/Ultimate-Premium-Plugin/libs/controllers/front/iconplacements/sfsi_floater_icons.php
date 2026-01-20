<?php
/* make icons float icons even widget is not active  */
function sfsi_plus_frontFloter ($content)
{
	//if(!sfsi_plus_icon_exclude() && false!= License_Manager::validate_license())
	if(sfsi_plus_shall_show_icons('round_icon_define_location'))
	{
		$sfsi_section8 = maybe_unserialize(get_option('sfsi_premium_section8_options',false));

		if(isset($sfsi_section8['sfsi_plus_float_on_page']) && $sfsi_section8['sfsi_plus_float_on_page']=="yes") :
			sfsi_set_tooltip_center_to_icon();
			ob_start();
			/* call the all icons function under sfsi_plus_widget.php file */
			if (wp_is_mobile())
			{
				if(isset($sfsi_section8['sfsi_plus_float_show_on_mobile']) && $sfsi_section8['sfsi_plus_float_show_on_mobile'] == 'yes')
				{
					echo sfsi_plus_check_mobile_visiblity(1,$sfsi_section8);
				}
			}
			else
			{

				if(isset($sfsi_section8['sfsi_plus_float_show_on_desktop']) && $sfsi_section8['sfsi_plus_float_show_on_desktop'] == 'yes')
				{
					echo sfsi_plus_check_visiblity(1,$sfsi_section8);
				}
			}
			echo $output=ob_get_clean();
			$res= $content.$output;
			return $res; exit;
		endif;
	}
}

function sfsi_plus_sticky_bar_front() {
	$sfsi_section8 = maybe_unserialize( get_option( 'sfsi_premium_section8_options', false ) );

   if (!sfsi_plus_shall_show_icons('icon_sticky_bar')){

       return true;
   }
	echo sfsi_premium_sticky_bar_icons( null, $sfsi_section8 );
}