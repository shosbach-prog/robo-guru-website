<?php
	/* maybe_unserialize all saved option for  section 7 options */
    $option7=  maybe_unserialize(get_option('sfsi_premium_section7_options',false));

	/**
	 * Sanitize, escape and validate values
	 */
	$option7['sfsi_plus_popup_text'] 				=	isset($option7['sfsi_plus_popup_text']) && !empty($option7['sfsi_plus_popup_text']) ? $option7['sfsi_plus_popup_text'] : '';

	$option7['sfsi_plus_popup_background_color']	=	(isset($option7['sfsi_plus_popup_background_color']))
															? sfsi_plus_sanitize_hex_color($option7['sfsi_plus_popup_background_color'])
															: '';
	$option7['sfsi_plus_popup_border_color'] 		=	(isset($option7['sfsi_plus_popup_border_color']))
															? sfsi_plus_sanitize_hex_color($option7['sfsi_plus_popup_border_color'])
															: '';
	$option7['sfsi_plus_popup_border_thickness'] 	=	(isset($option7['sfsi_plus_popup_border_thickness']))
															? intval($option7['sfsi_plus_popup_border_thickness'])
															: '';
	$option7['sfsi_plus_popup_border_shadow'] 		=	(isset($option7['sfsi_plus_popup_border_shadow']))
															? sanitize_text_field($option7['sfsi_plus_popup_border_shadow'])
															: '';
	$option7['sfsi_plus_popup_font'] 				=	(isset($option7['sfsi_plus_popup_font']))
															? sanitize_text_field($option7['sfsi_plus_popup_font'])
															: '';
	$option7['sfsi_plus_popup_fontSize'] 			=	(isset($option7['sfsi_plus_popup_fontSize']))
															? intval($option7['sfsi_plus_popup_fontSize'])
															: '';
	$option7['sfsi_plus_popup_fontStyle'] 			=	(isset($option7['sfsi_plus_popup_fontStyle']))
															? sanitize_text_field($option7['sfsi_plus_popup_fontStyle'])
															: '';
	$option7['sfsi_plus_popup_fontColor'] 			=	(isset($option7['sfsi_plus_popup_fontColor']))
															? sfsi_plus_sanitize_hex_color($option7['sfsi_plus_popup_fontColor'])
															: '';
	$option7['sfsi_plus_Show_popupOn'] 				=	(isset($option7['sfsi_plus_Show_popupOn']))
															? sanitize_text_field($option7['sfsi_plus_Show_popupOn'])
															: '';
    $option7['sfsi_plus_Show_popupOn_somepages_blogpage']  =   (isset($option7['sfsi_plus_Show_popupOn_somepages_blogpage']))
                                                            ? sanitize_text_field($option7['sfsi_plus_Show_popupOn_somepages_blogpage'])
                                                            : '';
    $option7['sfsi_plus_Show_popupOn_somepages_selectedpage'] =  (isset($option7['sfsi_plus_Show_popupOn_somepages_selectedpage']))
                                                            ? sanitize_text_field($option7['sfsi_plus_Show_popupOn_somepages_selectedpage'])
                                                            : '';
	$option7['sfsi_plus_Shown_pop'] 				=	(isset($option7['sfsi_plus_Shown_pop']))
															? $option7['sfsi_plus_Shown_pop']
															: array('ETscroll');
	$option7['sfsi_plus_Shown_popupOnceTime'] 		=	(isset($option7['sfsi_plus_Shown_popupOnceTime']))
															? intval($option7['sfsi_plus_Shown_popupOnceTime'])
															: '';
    $option7['sfsi_plus_popup_type_iconsOrForm']    =   (isset($option7['sfsi_plus_popup_type_iconsOrForm']))
                                                            ? $option7['sfsi_plus_popup_type_iconsOrForm']
                                                            : 'icons';

    $option7['sfsi_plus_popup_show_on_desktop']    =   (isset($option7['sfsi_plus_popup_show_on_desktop']))
                                                            ? sanitize_text_field($option7['sfsi_plus_popup_show_on_desktop'])
                                                            : '';
    $option7['sfsi_plus_popup_show_on_mobile']     =   (isset($option7['sfsi_plus_popup_show_on_mobile']))
                                                            ? sanitize_text_field($option7['sfsi_plus_popup_show_on_mobile'])
                                                            : '';

    $iconsDisplay = ($option7['sfsi_plus_popup_type_iconsOrForm']=="icons") ? 'style="display:block"': 'style="display:none"';
    $iconsForm    = ($option7['sfsi_plus_popup_type_iconsOrForm']=="form") ? 'style="display:block"': 'style="display:none"';

    $sfsi_plus_Hide_popupOnScroll                   =   (isset($option7['sfsi_plus_Hide_popupOnScroll']))
                                                            ? sanitize_text_field($option7['sfsi_plus_Hide_popupOnScroll'])
                                                            : 'no';
    $sfsi_plus_Hide_popupOn_OutsideClick            =   (isset($option7['sfsi_plus_Hide_popupOn_OutsideClick']))
                                                            ? sanitize_text_field($option7['sfsi_plus_Hide_popupOn_OutsideClick'])
                                                            : 'no';

?>
<!-- Section 7 "Do you want to display a pop-up, asking people to subscribe?" main div Start -->
<div class="tab7">
    <?php
        $sfsi_plus_banner_popups = sanitize_text_field(get_option('sfsi_plus_banner_popups', false));
            if($sfsi_plus_banner_popups == "yes"){
        ?>
        <div id="sfsi_firsttime_offer" class="sfsi_new_prmium_follw  sfsi_banner_body">
            <div>
                <p>
                	<?php
                	printf(
					    __( 'Update: if you want many more features for your pop-ups, have a look at %1$sMyPopUps.%2$s', 'ultimate-social-media-plus' ),
					    '<a target="_blank" href="https://sellcodes.com/s/3NmlIE" style="color:#1a1d20 !important;font-weight: bold;border-bottom: 1px solid #1a1d20;">',
					    '</a>'
					);
					?>
                </p>
            </div>
            <div style="text-align:right;">
                <form method="post" class="sfsi_premiumNoticeDismiss">
                    <input type="hidden" name="sfsi-plus-banner-popups" value="true">
                    <input type="submit" name="dismiss" value="<?php  _e( 'Dismiss', 'ultimate-social-media-plus'  ); ?>" />

                </form>
            </div>
        </div>
    <?php
    }
    ?>
	<p>
    	<?php  _e( 'You can Increase the chances that people share or follow you by displaying a pop-up asking them to. You can define the design and layout below:', 'ultimate-social-media-plus'  ); ?>
    </p>
<!-- icons preview section -->
<div class="like_pop_box">

	<div class="sfsi_plus_Popinner">
	<h2>
		<?php
			if( !empty( $option7['sfsi_plus_popup_text'] ) ) {
				echo sfsi_premium_nl2br( $option7['sfsi_plus_popup_text'] );
			}
		?>
	</h2>
	<ul class="like_icon plus_sfsi_sample_icons" <?php echo $iconsDisplay; ?>>

        <?php

        $arrAllIcons = maybe_unserialize(SFSI_PLUS_ALLICONS);

        foreach ($arrAllIcons as $key => $iconName):

            $icon_image =  $iconName.".png";

            if("email"== $iconName){
                $icon_image = sfsi_premium_get_email_icon();
            }
            elseif("facebook"== $iconName){
                $icon_image = 'fb.png';
            }

            $icon_image = SFSI_PLUS_PLUGURL.'images/'.$icon_image;

            ?>

             <li class="sfsiplus_<?php echo $iconName; ?>_section">
                <div>
                    <img src="<?php echo $icon_image; ?>" alt="<?php echo ucfirst($iconName); ?>" />
                    <span class="sfsi_Cdisplay" id="sfsi_plus_<?php echo $icon_image; ?>_countsDisplay"><?php  _e( '12k', 'ultimate-social-media-plus' ); ?></span>
                </div>
            </li>

        <?php endforeach;

		if(isset($icons) && !empty($icons))
		{
			if(is_array($icons))
			{
				foreach($icons as $icn =>$img)
				{
					echo '<li class="sfsiplus_custom_section sfsiICON_'.$icn.'"  element-id="'.$icn.'" ><div><img src="'.$img.'" alt="Custom Icon" class="sfcm" /><span class="sfsi_Cdisplay">'.__( '12k', 'ultimate-social-media-plus' ).'</span></div></li>';
				}
			}
		}

		?>
	</ul>

    <ul class="like_icon plus_sfsi_subscribe_form" <?php echo $iconsForm; ?>>
        <?php echo do_shortcode("[USM_plus_form]");?>
    </ul>
</div>
</div>
<!-- END icons preview section -->

<div class="row">

    <h4><?php  _e( 'Content of Popup:', 'ultimate-social-media-plus' ); ?></h4>

    <ul class="sfsi_plus_cl_popup_type_iconsOrForm">
        <li>
         <input name="sfsi_plus_popup_type_iconsOrForm" <?php echo ($option7['sfsi_plus_popup_type_iconsOrForm']=='icons') ? 'checked="true"' : '' ;?> type="radio" value="icons" class="styled"  />
         <label>
            <?php  _e( 'Social Media Icons', 'ultimate-social-media-plus' ); ?>
        </label>
        </li>

         <li>
            <input name="sfsi_plus_popup_type_iconsOrForm" <?php echo ($option7['sfsi_plus_popup_type_iconsOrForm']=='form') ? 'checked="true"' : '' ;?>  type="radio" value="form" class="styled" />
            <label><?php  _e( 'Subscription form (see Question 8)', 'ultimate-social-media-plus' ); ?></label>
        </li>
    </ul>
</div>

<!-- icons controllers section -->
<div class="space">

    <h4><?php  _e( 'Text and Design', 'ultimate-social-media-plus' ); ?></h4>

    <div class="text_options">

        <h3><?php  _e( 'Text Options', 'ultimate-social-media-plus' ); ?></h3>

        <div class="sfsiplus_row_tab">
            <label><?php  _e( 'Text:', 'ultimate-social-media-plus' ); ?></label>
<!--                     <input class="mkPop" name="sfsi_plus_popup_text" type="text" value="<?php //echo ($option7['sfsi_plus_popup_text']!='') ?  $option7['sfsi_plus_popup_text'] : '' ;?>" /> -->

            <textarea name="sfsi_plus_popup_text" id="sfsi_plus_popup_text" type="text" class="medium" placeholder="Enjoy this blog? Please spread the word :)"><?php echo !empty($option7['sfsi_plus_popup_text']) ? esc_attr(trim($option7['sfsi_plus_popup_text'])) : 'Enjoy this blog? Please spread the word :)'; ?></textarea>

		</div>
		<div class="sfsiplus_row_tab">
			<label><?php _e( 'Font:', 'ultimate-social-media-plus' ); ?></label>
			<div class="field">
				<select name="sfsi_plus_popup_font" id="sfsi_plus_popup_font" class="styled">
					<option value="Arial, Helvetica, sans-serif" <?php echo ($option7['sfsi_plus_popup_font']=='Arial, Arial, Helvetica, sans-serif') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Arial', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Arial Black, Gadget, sans-serif" <?php echo ($option7['sfsi_plus_popup_font']=='Arial Black, Gadget, sans-serif') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Arial Black', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Calibri" <?php echo ($option7['sfsi_plus_popup_font']=='Calibri') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Calibri', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Comic Sans MS" <?php echo ($option7['sfsi_plus_popup_font']=='Comic Sans MS') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Comic Sans MS', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Courier New" <?php echo ($option7['sfsi_plus_popup_font']=='Courier New') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Courier New', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Georgia" <?php echo ($option7['sfsi_plus_popup_font']=='Georgia') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Georgia', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Helvetica,Arial,sans-serif" <?php echo ($option7['sfsi_plus_popup_font']=='Helvetica,Arial,sans-serif') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Helvetica', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Impact" <?php echo ($option7['sfsi_plus_popup_font']=='Impact') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Impact', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Lucida Console" <?php echo ($option7['sfsi_plus_popup_font']=='Lucida Console') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Lucida Console', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Tahoma,Geneva" <?php echo ($option7['sfsi_plus_popup_font']=='Tahoma,Geneva') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Tahoma', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Times New Roman" <?php echo ($option7['sfsi_plus_popup_font']=='Times New Roman') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Times New Roman', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Trebuchet MS" <?php echo ($option7['sfsi_plus_popup_font']=='Trebuchet MS') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Trebuchet MS', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="Verdana" <?php echo ($option7['sfsi_plus_popup_font']=='Verdana') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Verdana', 'ultimate-social-media-plus' ); ?>
					</option>
				</select>
			</div>
		</div>
		<div class="sfsiplus_row_tab">
			<label><?php  _e( 'Font style:', 'ultimate-social-media-plus' ); ?></label>
			<div class="field">
				<select name="sfsi_plus_popup_fontStyle" id="sfsi_plus_popup_fontStyle" class="styled">
					<option value="normal" <?php echo ($option7['sfsi_plus_popup_fontStyle']=='normal') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Normal', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="inherit" <?php echo ($option7['sfsi_plus_popup_fontStyle']=='inherit') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Inherit', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="oblique" <?php echo ($option7['sfsi_plus_popup_fontStyle']=='oblique') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Oblique', 'ultimate-social-media-plus' ); ?>
					</option>
					<option value="italic" <?php echo ($option7['sfsi_plus_popup_fontStyle']=='italic') ? 'selected="true"' : '' ;?>>
						<?php _e( 'Italic', 'ultimate-social-media-plus' ); ?>
					</option>
				</select>
			</div>
		</div>
		<div class="sfsiplus_row_tab">
			<label><?php  _e( 'Font color:', 'ultimate-social-media-plus' ); ?></label>
            <input name="sfsi_plus_popup_fontColor" data-default-color="#b5b5b5" id="sfsi_plus_popup_fontColor" type="text" value="<?php echo ($option7['sfsi_plus_popup_fontColor']!='') ? esc_attr($option7['sfsi_plus_popup_fontColor']) : '' ;?>" />
		</div>
		<div class="sfsiplus_row_tab">
			<label>
            	<?php  _e( 'Font size:', 'ultimate-social-media-plus' ); ?>
            </label>
            <input min="16" name="sfsi_plus_popup_fontSize" type="number" value="<?php echo ($option7['sfsi_plus_popup_fontSize']!='') ?  $option7['sfsi_plus_popup_fontSize'] : '' ;?>" class="small" />
		</div>
	</div>
	<div class="text_options layout">
		<h3>
        	<?php  _e( 'Icon Box Layout', 'ultimate-social-media-plus' ); ?>
        </h3>
		<div class="sfsiplus_row_tab">
			<label>
            	<?php  _e( 'Background Color:', 'ultimate-social-media-plus' ); ?>
			</label>
            <input name="sfsi_plus_popup_background_color" data-default-color="#b5b5b5" id="sfsi_plus_popup_background_color" type="text" value="<?php echo ($option7['sfsi_plus_popup_background_color']!='') ? esc_attr($option7['sfsi_plus_popup_background_color']) : '' ;?>" />
		</div>
		<div class="sfsiplus_row_tab">
			<label class="border">
           		<?php  _e( 'Border Color:', 'ultimate-social-media-plus' ); ?>
            </label>
			<input name="sfsi_plus_popup_border_color" data-default-color="#b5b5b5" id="sfsi_plus_popup_border_color" type="text" value="<?php echo ($option7['sfsi_plus_popup_border_color']!='') ? esc_attr($option7['sfsi_plus_popup_border_color']) : '' ;?>"  />
		</div>
		<div class="sfsiplus_row_tab">
			<label>
            	<?php  _e( 'Border thickness:', 'ultimate-social-media-plus' ); ?>
			</label>
			<div class="field">
            	<input min="0" name="sfsi_plus_popup_border_thickness" type="number" value="<?php echo ($option7['sfsi_plus_popup_border_thickness']!='') ?  $option7['sfsi_plus_popup_border_thickness'] : '' ;?>" class="small" />
			</div>
		</div>
		<div class="sfsiplus_row_tab">
			<label style="padding-top: 14px !important;">
             	<?php  _e( 'Border Shadow:', 'ultimate-social-media-plus' ); ?>
            </label>
			<ul class="border_shadow sfsi_plus_border_shadow">
  				<li>
                	<input name="sfsi_plus_popup_border_shadow" <?php echo ($option7['sfsi_plus_popup_border_shadow']=='yes') ? 'checked="true"' : '' ;?> type="radio" value="yes" class="styled"  />
    				<label>
						<?php  _e( 'On', 'ultimate-social-media-plus' ); ?>
                    </label>
                </li>
    			<li>
                	<input name="sfsi_plus_popup_border_shadow" <?php echo ($option7['sfsi_plus_popup_border_shadow']=='no') ? 'checked="true"' : '' ;?>  type="radio" value="no" class="styled" />
                    <label>
                        <?php  _e( 'Off', 'ultimate-social-media-plus' ); ?>
                    </label>
                </li>
  			</ul>
		</div>
	</div>
</div>

<div class="row">
	<h4>
    	<?php  _e( 'Where shall the pop-up be shown?', 'ultimate-social-media-plus' ); ?>
    </h4>
	<div class="pop_up_show">
    	<input name="sfsi_plus_Show_popupOn" <?php echo ($option7['sfsi_plus_Show_popupOn']=='none') ? 'checked="true"' : '' ;?> type="radio" value="none" class="styled" />
        <label>
        	<?php  _e( 'Nowhere', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>
	<div class="pop_up_show">
    	<input name="sfsi_plus_Show_popupOn" <?php echo ($option7['sfsi_plus_Show_popupOn']=='everypage') ? 'checked="true"' : '' ;?> type="radio" value="everypage" class="styled" />
        <label>
            <?php  _e( 'On all pages', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>
    <div class="pop_up_show">

        <div class="popup_somepages">
            <input name="sfsi_plus_Show_popupOn" <?php echo ($option7['sfsi_plus_Show_popupOn']=='somepages') ? 'checked="true"' : '' ;?> type="radio" value="somepages" class="styled"/>
            <label>
                <?php  _e( 'On some pages', 'ultimate-social-media-plus' ); ?>
            </label>
        </div>

        <div class="popup_containter" style="<?php echo ($option7['sfsi_plus_Show_popupOn']=='somepages') ? 'display:block;' : 'display:none;' ;?>">

            <div class="pop_up_show">
                <input name="sfsi_plus_Show_popupOn_somepages_blogpage" <?php echo ($option7['sfsi_plus_Show_popupOn_somepages_blogpage']=='blogpage') ? 'checked="true"' : '' ;?> type="checkbox" value="blogpage" class="styled"/>
                <label>
                    <?php  _e( 'All blog posts', 'ultimate-social-media-plus' ); ?>
                </label>
            </div>

            <div class="pop_up_show">
                <input name="sfsi_plus_Show_popupOn_somepages_selectedpage" <?php echo ($option7['sfsi_plus_Show_popupOn_somepages_selectedpage']=='selectedpage') ? 'checked="true"' : '' ;?>  type="checkbox" value="selectedpage" class="styled"/>
                <label>
                    <?php  _e( 'Selected pages', 'ultimate-social-media-plus' ); ?>
                </label>
                <div class="field" style="width:50%">
                    <select multiple="multiple" name="sfsi_plus_Show_popupOn_PageIDs" id="sfsi_plus_Show_popupOn_PageIDs" style="width:60%;min-height: 150px;">
                    <?php
                    $select=  (isset($option7['sfsi_plus_Show_popupOn_PageIDs'])) ? maybe_unserialize($option7['sfsi_plus_Show_popupOn_PageIDs']) :array();
                    $get_pages = get_pages( array(
                        'offset'        => 1,
                        'hierarchical'  => 1,
                        'sort_order'    => 'DESC',
                        'sort_column'   => 'post_date',
                        'posts_per_page'=> -1,
                        'post_status'   => 'publish'
                    ));
                    if($get_pages)
                    {
                        foreach($get_pages as $page)
                        {
                            $selected_box = '';
                            $class = '';

                            if(!empty($select))
                            {
                                if( in_array( $page->ID, $select) )
                                {
                                    $selected_box = 'selected="selected"';
                                    $class = 'class="sel-active"';
                                }
                            }
                            echo '<option value="'.$page->ID.'" style="margin-bottom: 3px;" '.$selected_box.' '.$class.'>'.$page->post_title.'</option>';
                        }

                    }
                    ?>
                    </select><br/>
                    <?php  _e( 'Please hold the CTRL key to select multiple pages', 'ultimate-social-media-plus' ); ?>.
                </div>
            </div>

        </div>

    </div>

    <div class="pop_up_show">

        <?php  $popupDesktopMobile = ($option7['sfsi_plus_Show_popupOn']!='none') ? 'display:block': '';?>

        <div class="sfsiplus_show_desktop_mobile_setting_li popupDesktopMobileLi" style="<?php echo esc_attr($popupDesktopMobile);?>">

            <div class="sfsidesktopmbilelabel"><span class="sfsiplus_toglepstpgspn"><?php  _e( 'Show on:', 'ultimate-social-media-plus' ); ?></span></div>

                <ul class="sfsiplus_icn_listing8 sfsi_plus_closerli popupDesktopMobileUl">

                     <li class="">

                        <div class="radio_section tb_4_ck">
                             <input name="sfsi_plus_popup_show_on_desktop" type="checkbox" value="yes" class="styled" <?php echo ($option7['sfsi_plus_popup_show_on_desktop']=='yes') ? 'checked="true"' : '' ;?>>
                         </div>

                        <div class="sfsiplus_right_info">
                            <p><span class="sfsiplus_toglepstpgspn"><?php  _e( 'Desktop', 'ultimate-social-media-plus' ); ?></span></p>
                        </div>

                    </li>

                    <li class="">

                        <div class="radio_section tb_4_ck">
                             <input name="sfsi_plus_popup_show_on_mobile"  type="checkbox" value="yes" class="styled" <?php echo ($option7['sfsi_plus_popup_show_on_mobile']=='yes') ? 'checked="true"' : '' ;?>>
                        </div>

                        <div class="sfsiplus_right_info">
                            <p><span class="sfsiplus_toglepstpgspn"><?php  _e( 'Mobile', 'ultimate-social-media-plus' ); ?></span></p>
                        </div>
                    </li>

                </ul>
        </div>

</div>



</div>
<div class="row">
	<h4>
    	<?php  _e( 'When shall the pop-up be shown?', 'ultimate-social-media-plus' ); ?>
    </h4>
	<div class="pop_up_show">
    	<input name="sfsi_plus_Shown_pop[]" type="checkbox" value="once" class="styled"
        	<?php echo (in_array('once', $option7['sfsi_plus_Shown_pop'])) ? 'checked="true"' : ''; ?>/>
        <label class="sfsi_premium_sm_pop_up">
            <?php  _e( 'Once', 'ultimate-social-media-plus' ); ?>
            <input name="sfsi_plus_Shown_popupOnceTime" min="0" type="number" class="seconds"
            	value="<?php echo ($option7['sfsi_plus_Shown_popupOnceTime']!='') ?  $option7['sfsi_plus_Shown_popupOnceTime'] : '' ;?>"/>
            <?php  _e( 'seconds after the user arrived on the page', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>
	<div class="pop_up_show">
    	<input name="sfsi_plus_Shown_pop[]" type="checkbox" value="ETscroll" class="styled"
        	<?php echo (in_array('ETscroll', $option7['sfsi_plus_Shown_pop'])) ? 'checked="true"' : ''; ?> />
        <label class="sfsi_premium_sm_pop_up">
            <?php  _e( 'Every time user scrolls to the end of the page', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>
    <div class="pop_up_show">
    	<input name="sfsi_plus_Shown_pop[]" type="checkbox" value="leavePage" class="styled"
        	<?php echo (in_array('leavePage', $option7['sfsi_plus_Shown_pop'])) ? 'checked="true"' : ''; ?> />
        <label class="sfsi_premium_sm_pop_up">
            <?php  _e( 'When the user leaves the page (i.e. moves over the top part in their browser)', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>
</div>


<div class="row">

    <h4>
        <?php  _e( 'After showing the pop-up, when shall it disappear again?', 'ultimate-social-media-plus' ); ?>
    </h4>

    <div class="pop_up_show">

        <input name="sfsi_plus_Hide_popupOnScroll" <?php echo ($sfsi_plus_Hide_popupOnScroll=='yes') ? 'checked="true"' : '' ;?> type="checkbox" value="<?php echo $sfsi_plus_Hide_popupOnScroll; ?>" class="styled" />
        <label>
            <?php  _e( 'When user scrolls', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>

    <div class="pop_up_show">

        <input name="sfsi_plus_Hide_popupOn_OutsideClick" <?php echo ($sfsi_plus_Hide_popupOn_OutsideClick=='yes') ? 'checked="true"' : '' ;?> type="checkbox" value="<?php echo $sfsi_plus_Hide_popupOn_OutsideClick; ?>" class="styled" />
        <label class="sfsi_premium_sm_pop_up">
            <?php  _e( 'When the user clicks outside of the pop-up area', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>

</div>

<div class="row">
	<h4>
    	<?php  _e( 'Do you want to set a limit on how often the pop-up is shown?', 'ultimate-social-media-plus' ); ?>
    </h4>
	<div class="pop_up_show sfsi_plus_popup_timelimit_radioCheck">
    	<input name="sfsi_plus_popup_limit" type="radio" value="no" class="styled" id="sfsi_popup_limit_no"
        	<?php echo ($option7['sfsi_plus_popup_limit'] == 'no') ? 'checked="true"' : ''; ?>/>
        <label>
            <?php  _e( 'No', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>
	<div class="pop_up_show sfsi_plus_popup_timelimit_radioCheck">
    	<input name="sfsi_plus_popup_limit" type="radio" value="yes" class="styled" id="sfsi_popup_limit_yes"
        	<?php echo ($option7['sfsi_plus_popup_limit'] == 'yes') ? 'checked="true"' : ''; ?>/>
        <label>
            <?php  _e( 'Yes', 'ultimate-social-media-plus' ); ?>
        </label>
    </div>
    <div class="pop_up_show sfsi_plus_popup_timelimit"
    	style="display:<?php echo ($option7['sfsi_plus_popup_limit']=='yes') ? 'block' : 'none';?>">
    	<p>
			<?php _e('Show the popup to the same user max once per', 'ultimate-social-media-plus' ); ?>
        </p>

        <input min="1" name="sfsi_plus_popup_limit_count" type="number" class="seconds"
            	value="<?php echo ($option7['sfsi_plus_popup_limit_count']!='') ?  $option7['sfsi_plus_popup_limit_count'] : '' ;?>"/>

        <div class="field">
        	<select class="styled" name="sfsi_plus_popup_limit_type">
                <option value="day" <?php echo ( $option7['sfsi_plus_popup_limit_type'] == 'day' ) ? 'selected="selected"' : '' ?> ><?php _e('Day(s)', 'ultimate-social-media-plus' ); ?></option>
                <option value="hour" <?php echo ( $option7['sfsi_plus_popup_limit_type'] == 'hour' ) ? 'selected="selected"' : '' ?> ><?php _e('Hour(s)', 'ultimate-social-media-plus' ); ?></option>
                <option value="minute" <?php echo ( $option7['sfsi_plus_popup_limit_type'] == 'minute' ) ? 'selected="selected"' : '' ?> ><?php _e('Minute(s)', 'ultimate-social-media-plus' ); ?></option>
            </select>
       </div>

       <div class="sfsi_plus_limitpop_note">(<?php _e('this applies across the entire website, not only selected pages', 'ultimate-social-media-plus' ); ?>)</div>

    </div>
</div>

<!-- SAVE BUTTON SECTION   -->
<div class="save_button">
	<img src="<?php echo SFSI_PLUS_PLUGURL ?>images/ajax-loader.gif" alt="loader" class="loader-img" />
    <?php  $nonce = wp_create_nonce("update_plus_step7"); ?>
    <a href="javascript:;" id="sfsi_plus_save7" title="Save" data-nonce="<?php echo $nonce;?>">
        <?php  _e( 'Save', 'ultimate-social-media-plus' ); ?>
    </a>
</div>

<!-- END SAVE BUTTON SECTION   -->
<a class="sfsiColbtn closeSec" href="javascript:;">
    <?php  _e( 'Collapse area', 'ultimate-social-media-plus' ); ?>
</a>
<label class="closeSec"></label>

<!-- ERROR AND SUCCESS MESSAGE AREA-->
<p class="red_txt errorMsg" style="display:none;"> </p>
<p class="green_txt sucMsg" style="display:none;"> </p>
<div class="clear"></div>

</div>
<!-- END Section 7 "Do you want to display a pop-up, asking people to subscribe?" main div Start -->
