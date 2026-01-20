<?php 

	$rectSetting = (isset($option8['sfsi_plus_place_rect_shortcode']) && !empty($option8['sfsi_plus_place_rect_shortcode']) ) ? $option8['sfsi_plus_place_rect_shortcode']: 'no';

	$rectClass = ("yes" == $rectSetting) ? 'show': 'hide';

	$rectCheck = ("yes" == $rectSetting) ? 'checked="checked"' : '';
?>

<!--<ul class="sfsiplus_icn_listing8 sfsi_rectangle_ul" >-->

	<!-- <li class="sfsiplusplacethemanulywpr rectangle_icons_item_manually"> -->
			
 			<!-- <div style="margin-top: 20px !important;" class="radio_section tb_4_ck">

 				<input name="sfsi_plus_place_rectangle_icons_item_manually" <?php echo $rectCheck; ?>  id="sfsi_plus_place_rectangle_icons_item_manually" type="checkbox" value="yes" class="styled"  />

				<span class="sfsiplus_toglepstpgspn">
            		<?php  _e( 'Placing the rectangle icons via shortcode', 'ultimate-social-media-plus' ); ?>
            	</span>

 			</div> -->
			
                    
		<div class="sfsi_row">
			<p>
				<?php _e( 'You can also place the rectangle icons not only before/after posts, but anywhere you want.', 'ultimate-social-media-plus' ); ?>
			</p>
		</div>

    	<div class="sfsi_row">
			<p>
    		<?php _e( 'For that, please place the following string into your theme codes: &lt;?php echo DISPLAY_PREMIUM_RECTANGLE_ICONS(); ?&gt;', 'ultimate-social-media-plus' );?>
        	</p>
    	</div>

		<div class="sfsi_row">
			<p><?php _e( 'Or use the shortcode [DISPLAY_PREMIUM_RECTANGLE_ICONS]', 'ultimate-social-media-plus' ); ?></p>
		</div>
		<li style="margin-left: 61px;<?php echo ( $option8['sfsi_plus_display_button_type']=='responsive_button') ?  'display:none' : '' ;?>"  class="sfsi_premium_not_responsive">
             <div class="sfsidesktopmbilelabel"><span class="sfsiplus_toglepstpgspn"><?php _e( 'Show on:', 'ultimate-social-media-plus' ); ?></span></div>

            <ul class="sfsiplus_icn_listing8 sfsi_plus_closerli bfreAftrPostsDesktopMobileUl">
                <li class="">
					<div class="radio_section tb_4_ck">
                        <input name="sfsi_plus_rectangle_icons_shortcode_show_on_desktop" type="checkbox" value="yes" class="styled" <?php echo ($option8['sfsi_plus_rectangle_icons_shortcode_show_on_desktop']=='yes') ?  'checked="true"' : '' ;?>>
                    </div>
                    
                    <div class="sfsiplus_right_info"><?php _e( 'Desktop', 'ultimate-social-media-plus' ); ?></div>
                </li>
                <li class="">
                    
                    <div class="radio_section tb_4_ck">
                        <input name="sfsi_plus_rectangle_icons_shortcode_show_on_mobile"  type="checkbox" value="yes" class="styled" <?php echo ($option8['sfsi_plus_rectangle_icons_shortcode_show_on_mobile']=='yes') ?  'checked="true"' : '' ;?>>
                    </div>

                    <div class="sfsiplus_right_info"><?php _e( 'Mobile', 'ultimate-social-media-plus' ); ?></div>
                </li>
            </ul>
        </li>

				
	<!-- </li> -->

<!--</ul>-->