<?php

    $sfsi_plus_float_horizontal_verical_Alignment = isset( $option8['sfsi_plus_float_horizontal_verical_Alignment'] ) ? $option8['sfsi_plus_float_horizontal_verical_Alignment'] : "Horizontal";

    $sfsi_plus_float_mobile_horizontal_verical_Alignment = isset( $option8['sfsi_plus_float_mobile_horizontal_verical_Alignment'] ) ? $option8['sfsi_plus_float_mobile_horizontal_verical_Alignment'] : "Horizontal";

?>
<li class="sfsiplusLocationli">

    <div class="radio_section tb_4_ck cstmfltonpgstck" onclick="sfsiplus_toggleflotpage(this);">
        <input name="sfsi_plus_float_on_page" <?php echo ( isset( $option8['sfsi_plus_float_on_page'] ) && $option8['sfsi_plus_float_on_page'] == 'yes' ) ? 'checked="true"' : '' ;?>  id="sfsi_plus_float_on_page" type="checkbox" value="yes" class="styled" />
    </div>

	<div class="sfsiplus_right_info">
		<p>
			<span class="sfsiplus_toglepstpgspn">
            	<?php _e( 'Floating/sticky, over your websites\'s pages', 'ultimate-social-media-plus' ); ?>
            </span>
		</p>

        <?php $style = ( isset( $option8['sfsi_plus_float_on_page'] ) && $option8['sfsi_plus_float_on_page'] == "yes" ) ? 'display: block;' : "display: none;"; ?>

        <div class="sfsiplus_tab_3_icns" <?php echo 'style="'.$style.'"';?>>
			<label style="color:#999;font-size:18px" class="sfsiplus_sub-subtitle"><?php _e( 'Define the location:', 'ultimate-social-media-plus' ); ?></label>
            <ul class="sfsiplus_tab_3_icns flthmonpg">
                <li>
                    <input name="sfsi_plus_float_page_position" <?php echo ( $option8['sfsi_plus_float_page_position']=='top-left') ?  'checked="true"' : '' ;?> type="radio" value="top-left" class="styled"  />
                    <span class="sfsi_flicnsoptn3 sfsioptntl">
                   		<?php _e( 'Top left', 'ultimate-social-media-plus' ); ?>
                    </span>
                    <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/top_left.png" alt="<?php _e( 'Top left icons', 'ultimate-social-media-plus' ); ?>" /></label>
                </li>
                <li>
                    <input name="sfsi_plus_float_page_position" <?php echo ( $option8['sfsi_plus_float_page_position']=='center-top') ?  'checked="true"' : '' ;?> type="radio" value="center-top" class="styled"  />
                    <span class="sfsi_flicnsoptn3 sfsioptncl">
                    	<?php _e( 'Center top', 'ultimate-social-media-plus' ); ?>
                    </span>
                    <label class="sfsi_float_position_icon_label"><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/float_position_icon.png" alt="<?php _e( 'Center top icons', 'ultimate-social-media-plus' ); ?>"/></label>
                </li>
                <li>
                    <input name="sfsi_plus_float_page_position" <?php echo ( $option8['sfsi_plus_float_page_position']=='top-right') ?  'checked="true"' : '' ;?> type="radio" value="top-right" class="styled"  />
                    <span class="sfsi_flicnsoptn3 sfsioptntr">
                    	<?php _e( 'Top right', 'ultimate-social-media-plus' ); ?>
                    </span>
                    <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/top_right.png" alt="<?php _e( 'Top right icons', 'ultimate-social-media-plus' ); ?>" /></label>
                </li>
                <li>
                    <input name="sfsi_plus_float_page_position" <?php echo ( $option8['sfsi_plus_float_page_position']=='center-left') ?  'checked="true"' : '' ;?> type="radio" value="center-left" class="styled"  />
                    <span class="sfsi_flicnsoptn3 sfsioptncl">
                    	<?php _e( 'Center left', 'ultimate-social-media-plus' ); ?>
                    </span>
                    <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/center_left.png" alt="<?php _e( 'Center left icons', 'ultimate-social-media-plus' ); ?>"/></label>
                </li>
                <li></li>
                <li>
                    <input name="sfsi_plus_float_page_position" <?php echo ( $option8['sfsi_plus_float_page_position']=='center-right') ?  'checked="true"' : '' ;?> type="radio" value="center-right" class="styled"  />
                    <span class="sfsi_flicnsoptn3 sfsioptncr">
                    	<?php _e( 'Center right', 'ultimate-social-media-plus' ); ?>
                    </span>
                    <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/center_right.png" alt="<?php _e( 'Center right icons', 'ultimate-social-media-plus' ); ?>"/></label>
                </li>
                <li>
                    <input name="sfsi_plus_float_page_position" <?php echo ( $option8['sfsi_plus_float_page_position']=='bottom-left') ?  'checked="true"' : '' ;?> type="radio" value="bottom-left" class="styled"  />
                    <span class="sfsi_flicnsoptn3 sfsioptnbl">
                    	<?php _e( 'Bottom left', 'ultimate-social-media-plus' ); ?>
                    </span>
                    <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/bottom_left.png" alt="<?php _e( 'Bottom left icons', 'ultimate-social-media-plus' ); ?>"/></label>
                </li>
                <li>
                    <input name="sfsi_plus_float_page_position" <?php echo ( $option8['sfsi_plus_float_page_position']=='center-bottom') ?  'checked="true"' : '' ;?> type="radio" value="center-bottom" class="styled"  />
                    <span class="sfsi_flicnsoptn3 sfsioptncr">
                    	<?php _e( 'Center bottom', 'ultimate-social-media-plus' ); ?>
                    </span>
                    <label class="sfsi_float_position_icon_label sfsi_center_botttom"><img class="sfsi_img_center_bottom" src="<?php echo SFSI_PLUS_PLUGURL;?>images/float_position_icon.png" alt="<?php _e( 'Center bottom icons', 'ultimate-social-media-plus' ); ?>"/></label>
                </li>
                <li>
                    <input name="sfsi_plus_float_page_position" <?php echo ( $option8['sfsi_plus_float_page_position']=='bottom-right') ?  'checked="true"' : '' ;?> type="radio" value="bottom-right" class="styled"  />
                    <span class="sfsi_flicnsoptn3 sfsioptnbr">
                    	<?php _e( 'Bottom right', 'ultimate-social-media-plus' ); ?>
                    </span>
                    <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/bottom_right.png" alt="<?php _e( 'Bottom right icons', 'ultimate-social-media-plus' ); ?>"/></label>
                </li>
            </ul>

            <div class="sfsi_premium_sm_margin_form_m" style="width: 88%; float: left; margin:25px 0 0 0">
            	<h4>
               		<?php _e( 'Margin From:', 'ultimate-social-media-plus' ); ?>
                </h4>
                <ul class="sfsi_plus_floaticon_margin_sec">
                    <li>
                        <label>
                        	<?php _e( 'Top:', 'ultimate-social-media-plus' ); ?>
                        </label>
                        <input name="sfsi_plus_icons_floatMargin_top" type="text" value="<?php echo ($option8['sfsi_plus_icons_floatMargin_top']!='') ?  esc_attr($option8['sfsi_plus_icons_floatMargin_top']) : '' ;?>" />
                        <ins>
                        	<?php _e( 'Pixels', 'ultimate-social-media-plus' ); ?>
                        </ins>
                    </li>
                    <li>
                        <label>
                        	<?php _e( 'Bottom:', 'ultimate-social-media-plus' ); ?>
                        </label>
                        <input name="sfsi_plus_icons_floatMargin_bottom" type="text" value="<?php echo ($option8['sfsi_plus_icons_floatMargin_bottom'] != '') ?  esc_attr($option8['sfsi_plus_icons_floatMargin_bottom']) : '' ;?>" />
                        <ins>
                        	<?php _e( 'Pixels', 'ultimate-social-media-plus' ); ?>
                        </ins>
                    </li>
                    <li>
                        <label>
                     		<?php _e( 'Left:', 'ultimate-social-media-plus' ); ?>
                        </label>
                        <input name="sfsi_plus_icons_floatMargin_left" type="text" value="<?php echo ($option8['sfsi_plus_icons_floatMargin_left']!='') ?  esc_attr($option8['sfsi_plus_icons_floatMargin_left']) : '' ;?>" />
                        <ins>
                      		<?php _e( 'Pixels', 'ultimate-social-media-plus' ); ?>
                        </ins>
                    </li>
                    <li>
                        <label>
                        	<?php _e( 'Right:', 'ultimate-social-media-plus' ); ?>
                        </label>
                        <input name="sfsi_plus_icons_floatMargin_right" type="text" value="<?php echo ($option8['sfsi_plus_icons_floatMargin_right']!='') ?  esc_attr($option8['sfsi_plus_icons_floatMargin_right']) : '' ;?>" />
                        <ins>
                        	<?php _e( 'Pixels', 'ultimate-social-media-plus' ); ?>
                        </ins>
                    </li>
                </ul>
            </div>

            <div class="sfsi_premium_sm_margin_form_m" style="width: 100%; float: left; margin:25px 0 0 0">
            	<h4>
               		<?php _e( 'Make icons…', 'ultimate-social-media-plus' ); ?>
                </h4>
                <ul class="sfsi_plus_make_icons sfsi_premium_makeIcons_container">
                    <li>
                        <input name="sfsi_plus_make_icon" <?php echo ( $option8['sfsi_plus_make_icon']=='float') ?  'checked="true"' : '' ;?> type="radio" value="float" class="styled"/>
                        <span class="sfsi_flicnsoptn3">
                   			<?php _e( 'Float', 'ultimate-social-media-plus' ); ?>
                    	</span>
					</li>
                    <li>
                        <input name="sfsi_plus_make_icon" <?php echo ( $option8['sfsi_plus_make_icon']=='stay') ?  'checked="true"' : '' ;?> type="radio" value="stay" class="styled"  />
                        <span class="sfsi_flicnsoptn3">
                   			<?php _e( 'Stay at the same place A', 'ultimate-social-media-plus' ); ?>
                   			<h5><?php _e( '(not visible if a user scrolls down)', 'ultimate-social-media-plus' ); ?></h5>
                    	</span>
                    </li>
                    <li>
						<input name="sfsi_plus_make_icon" type="radio" value="stay_same_place" class="styled"
							<?php echo ( $option8['sfsi_plus_make_icon']=='stay_same_place') ?  'checked="true"' : '' ;?>/>
                        <span class="sfsi_flicnsoptn3">
                   			<?php _e( 'Stay at the same place B', 'ultimate-social-media-plus' ); ?>
                   			<h5><?php _e( '(still visible if a user scrolls down)', 'ultimate-social-media-plus' ); ?></h5>
                   		</span>
                   	</li>
                </ul>
            </div>

            <div class="row sfsi_plus_float_icons_alignment">
                <h4 style="padding-top: 0;">
                    <?php _e( 'Alignments', 'ultimate-social-media-plus' ); ?>
                </h4>
                <div class="icons_size">
                    <ul class="sfsi_plus_new_alignment_option">
                        <li>
                            <span class="sfsi_plus_new_alignment_span" style="line-height: 48px;">
                                <?php _e( 'Show icons', 'ultimate-social-media-plus' ); ?>
                            </span>
                            <div class="field">
                                 <select name="sfsi_plus_float_horizontal_verical_Alignment" id="sfsi_plus_float_horizontal_verical_Alignment">
                                    <option value="Horizontal" <?php echo ( $sfsi_plus_float_horizontal_verical_Alignment == 'Horizontal' ) ? 'selected="selected"' : ''; ?>>
                                        <?php _e( 'Horizontally', 'ultimate-social-media-plus' ); ?>
                                    </option>
                                    <option value="Vertical" <?php echo ( $sfsi_plus_float_horizontal_verical_Alignment == 'Vertical' ) ? 'selected="selected"' : ''; ?>>
                                        <?php _e( 'Vertically', 'ultimate-social-media-plus' ); ?>
                                    </option>
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="sfsi_premium_sm_margin_form_m" style="width: 88%; float: left; margin:25px 0 0 0">
            	<h4>
               		<?php _e( 'Need different selections for mobile?', 'ultimate-social-media-plus' ); ?>
                </h4>
                <ul class="sfsi_plus_make_icons sfsi_plus_mobile_float">
                    <li>
                        <input name="sfsi_plus_mobile_float" <?php echo ( $option8['sfsi_plus_mobile_float']=='no') ?  'checked="true"' : '' ;?> type="radio" value="no" class="styled"/>
                        <span class="sfsi_flicnsoptn3">
                   			<?php _e( 'No', 'ultimate-social-media-plus' ); ?>
                    	</span>
					</li>
                    <li>
                        <input name="sfsi_plus_mobile_float" <?php echo ( $option8['sfsi_plus_mobile_float']=='yes') ?  'checked="true"' : '' ;?> type="radio" value="yes" class="styled"  />
                        <span class="sfsi_flicnsoptn3">
                   			<?php _e( 'Yes', 'ultimate-social-media-plus' ); ?>
                    	</span>
                    </li>
                </ul>
            </div>

            <div class="sfsi_plus_mobile_float_section" style="<?php echo (isset($option8['sfsi_plus_mobile_float']) && $option8['sfsi_plus_mobile_float'] == 'yes')? 'display:block' : 'display:none'; ?>">
            	<ul class="sfsiplus_tab_3_icns flthmonpg" style="overflow: visible;">
                    <li>
                        <input name="sfsi_plus_float_page_mobileposition" <?php echo ( $option8['sfsi_plus_float_page_mobileposition']=='top-left') ?  'checked="true"' : '' ;?> type="radio" value="top-left" class="styled"  />
                        <span class="sfsi_flicnsoptn3 sfsioptntl">
                            <?php _e( 'Top left', 'ultimate-social-media-plus' ); ?>
                        </span>
                        <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/top_left.png" alt="<?php _e( 'Top left icons', 'ultimate-social-media-plus' ); ?>" /></label>
                    </li>
                   <li>
                        <input name="sfsi_plus_float_page_mobileposition" <?php echo ( $option8['sfsi_plus_float_page_mobileposition']=='center-top') ?  'checked="true"' : '' ;?> type="radio" value="center-top" class="styled"  />
                        <span class="sfsi_flicnsoptn3 sfsioptncl">
                        	<?php _e( 'Center top', 'ultimate-social-media-plus' ); ?>
                        </span>
                        <label class="sfsi_float_position_icon_label"><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/float_position_icon.png" alt="<?php _e( 'Center top icons', 'ultimate-social-media-plus' ); ?>" /></label>
                    </li>
                    <li>
                        <input name="sfsi_plus_float_page_mobileposition" <?php echo ( $option8['sfsi_plus_float_page_mobileposition']=='top-right') ?  'checked="true"' : '' ;?> type="radio" value="top-right" class="styled"  />
                        <span class="sfsi_flicnsoptn3 sfsioptntr">
                            <?php _e( 'Top right', 'ultimate-social-media-plus' ); ?>
                        </span>
                        <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/top_right.png" alt="<?php _e( 'Top right icons', 'ultimate-social-media-plus' ); ?>" /></label>
                    </li>
                    <li>
                        <input name="sfsi_plus_float_page_mobileposition" <?php echo ( $option8['sfsi_plus_float_page_mobileposition']=='center-left') ?  'checked="true"' : '' ;?> type="radio" value="center-left" class="styled"  />
                        <span class="sfsi_flicnsoptn3 sfsioptncl">
                            <?php _e( 'Center left', 'ultimate-social-media-plus' ); ?>
                        </span>
                        <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/center_left.png" alt="<?php _e( 'Center left icons', 'ultimate-social-media-plus' ); ?>" /></label>
                    </li>
                    <li></li>
                    <li>
                        <input name="sfsi_plus_float_page_mobileposition" <?php echo ( $option8['sfsi_plus_float_page_mobileposition']=='center-right') ?  'checked="true"' : '' ;?> type="radio" value="center-right" class="styled"  />
                        <span class="sfsi_flicnsoptn3 sfsioptncr">
                            <?php _e( 'Center right', 'ultimate-social-media-plus' ); ?>
                        </span>
                        <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/center_right.png" alt="<?php _e( 'Center right icons', 'ultimate-social-media-plus' ); ?>" /></label>
                    </li>
                    <li>
                        <input name="sfsi_plus_float_page_mobileposition" <?php echo ( $option8['sfsi_plus_float_page_mobileposition']=='bottom-left') ?  'checked="true"' : '' ;?> type="radio" value="bottom-left" class="styled"  />
                        <span class="sfsi_flicnsoptn3 sfsioptnbl">
                            <?php _e( 'Bottom left', 'ultimate-social-media-plus' ); ?>
                        </span>
                        <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/bottom_left.png" alt="<?php _e( 'Bottom left icons', 'ultimate-social-media-plus' ); ?>" /></label>
                    </li>
                    <li>
                        <input name="sfsi_plus_float_page_mobileposition" <?php echo ( $option8['sfsi_plus_float_page_mobileposition']=='center-bottom') ?  'checked="true"' : '' ;?> type="radio" value="center-bottom" class="styled"  />
                        <span class="sfsi_flicnsoptn3 sfsioptncr">
                        	<?php _e( 'Center bottom', 'ultimate-social-media-plus' ); ?>
                        </span>
                        <label class="sfsi_float_position_icon_label sfsi_center_botttom"><img class="sfsi_img_center_bottom" src="<?php echo SFSI_PLUS_PLUGURL;?>images/float_position_icon.png" alt="<?php _e( 'Center bottom icons', 'ultimate-social-media-plus' ); ?>" /></label>
                    </li>
                    <li>
                        <input name="sfsi_plus_float_page_mobileposition" <?php echo ( $option8['sfsi_plus_float_page_mobileposition']=='bottom-right') ?  'checked="true"' : '' ;?> type="radio" value="bottom-right" class="styled"  />
                        <span class="sfsi_flicnsoptn3 sfsioptnbr">
                            <?php _e( 'Bottom right', 'ultimate-social-media-plus' ); ?>
                        </span>
                        <label><img src="<?php echo SFSI_PLUS_PLUGURL;?>images/bottom_right.png" alt="<?php _e( 'Bottom right icons', 'ultimate-social-media-plus' ); ?>" /></label>
                    </li>
                </ul>

                <div style="width: 88%; float: left; margin:25px 0 0 47px">
                    <h4>
                        <?php _e( 'Margin From:', 'ultimate-social-media-plus' ); ?>
                    </h4>
                    <ul class="sfsi_plus_floaticon_margin_sec">
                        <li>
                            <label>
                                <?php _e( 'Top:', 'ultimate-social-media-plus' ); ?>
                            </label>
                            <input name="sfsi_plus_icons_floatMargin_mobiletop" type="text" value="<?php echo ($option8['sfsi_plus_icons_floatMargin_mobiletop']!='') ?  esc_attr($option8['sfsi_plus_icons_floatMargin_mobiletop']) : '' ;?>" />
                            <ins>
                                <?php _e( 'Pixels', 'ultimate-social-media-plus' ); ?>
                            </ins>
                        </li>
                        <li>
                            <label>
                                <?php _e( 'Bottom:', 'ultimate-social-media-plus' ); ?>
                            </label>
                            <input name="sfsi_plus_icons_floatMargin_mobilebottom" type="text" value="<?php echo ($option8['sfsi_plus_icons_floatMargin_mobilebottom'] != '') ?  esc_attr($option8['sfsi_plus_icons_floatMargin_mobilebottom']) : '' ;?>" />
                            <ins>
                                <?php _e( 'Pixels', 'ultimate-social-media-plus' ); ?>
                            </ins>
                        </li>
                        <li>
                            <label>
                                <?php _e( 'Left:', 'ultimate-social-media-plus' ); ?>
                            </label>
                            <input name="sfsi_plus_icons_floatMargin_mobileleft" type="text" value="<?php echo ($option8['sfsi_plus_icons_floatMargin_mobileleft']!='') ?  esc_attr($option8['sfsi_plus_icons_floatMargin_mobileleft']) : '' ;?>" />
                            <ins>
                                <?php _e( 'Pixels', 'ultimate-social-media-plus' ); ?>
                            </ins>
                        </li>
                        <li>
                            <label>
                                <?php _e( 'Right:', 'ultimate-social-media-plus' ); ?>
                            </label>
                            <input name="sfsi_plus_icons_floatMargin_mobileright" type="text" value="<?php echo ($option8['sfsi_plus_icons_floatMargin_mobileright']!='') ?  esc_attr($option8['sfsi_plus_icons_floatMargin_mobileright']) : '' ;?>" />
                            <ins>
                                <?php _e( 'Pixels', 'ultimate-social-media-plus' ); ?>
                            </ins>
                        </li>
                    </ul>
                </div>

                <div style="width: 100%; float: left; margin:25px 0 0 47px">
                    <h4>
                        <?php _e( 'Make icons…', 'ultimate-social-media-plus' ); ?>
                    </h4>
                    <ul class="sfsi_plus_make_icons sfsi_premium_makeIcons_container">
                        <li>
                            <input name="sfsi_plus_make_mobileicon" <?php echo ( $option8['sfsi_plus_make_mobileicon']=='float') ?  'checked="true"' : '' ;?> type="radio" value="float" class="styled"/>
                            <span class="sfsi_flicnsoptn3">
                                <?php _e( 'Float', 'ultimate-social-media-plus' ); ?>
                            </span>
                        </li>
                        <li>
                            <input name="sfsi_plus_make_mobileicon" <?php echo ( $option8['sfsi_plus_make_mobileicon']=='stay') ?  'checked="true"' : '' ;?> type="radio" value="stay" class="styled"  />
                            <span class="sfsi_flicnsoptn3">
                                <?php _e( 'Stay at the same place A', 'ultimate-social-media-plus' ); ?>
                                <h5><?php _e( '(not visible if a user scrolls down)', 'ultimate-social-media-plus' ); ?></h5>
                            </span>
                        </li>
                        <li>
							<input name="sfsi_plus_make_mobileicon" type="radio" value="stay_same_place" class="styled"
								<?php echo ( $option8['sfsi_plus_make_mobileicon']=='stay_same_place') ?  'checked="true"' : '' ;?>/>
                            <span class="sfsi_flicnsoptn3">
                       			<?php _e( 'Stay at the same place B', 'ultimate-social-media-plus' ); ?>
                       			<h5><?php _e( '(still visible if a user scrolls down)', 'ultimate-social-media-plus' ); ?></h5>
                       		</span>
                       	</li>
                    </ul>
                </div>

                <div class="row sfsi_plus_float_icons_alignment">
                    <h4 style="padding-top: 0;">
                        <?php _e( 'Alignments', 'ultimate-social-media-plus' ); ?>
                    </h4>
                    <div class="icons_size">
                        <ul class="sfsi_plus_new_alignment_option">
                            <li>
                                <span class="sfsi_plus_new_alignment_span" style="line-height: 48px;">
                                    <?php _e( 'Show icons', 'ultimate-social-media-plus' ); ?>
                                </span>
                                <div class="field">
                                     <select name="sfsi_plus_float_mobile_horizontal_verical_Alignment" id="sfsi_plus_float_mobile_horizontal_verical_Alignment">
                                        <option value="Horizontal" <?php echo ( $sfsi_plus_float_mobile_horizontal_verical_Alignment == 'Horizontal' ) ? 'selected="selected"' : ''; ?>>
                                            <?php _e( 'Horizontally', 'ultimate-social-media-plus' ); ?>
                                        </option>
                                        <option value="Vertical" <?php echo ( $sfsi_plus_float_mobile_horizontal_verical_Alignment == 'Vertical' ) ? 'selected="selected"' : ''; ?>>
                                            <?php _e( 'Vertically', 'ultimate-social-media-plus' ); ?>
                                        </option>
                                    </select>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>

        </div>

		<div class="sfsiplus_show_desktop_mobile_setting_li floatDesktopMobileLi" style="<?php echo esc_attr($style); ?>">

				<div class="sfsidesktopmbilelabel"><span class="sfsiplus_toglepstpgspn"><?php _e( 'Show on:', 'ultimate-social-media-plus' ); ?></span></div>

				<ul class="sfsiplus_icn_listing8 sfsi_plus_closerli floatDesktopMobileul">

			    	<li class="">

						<div class="radio_section tb_4_ck">
			            	<input name="sfsi_plus_float_show_on_desktop" type="checkbox" value="yes" class="styled" <?php echo ($option8['sfsi_plus_float_show_on_desktop']=='yes') ?  'checked="true"' : '' ;?>>
			            </div>

						<div class="sfsiplus_right_info">
							<p>
								<span class="sfsiplus_toglepstpgspn"><?php _e( 'Desktop', 'ultimate-social-media-plus' ); ?></span>
                            </p>
						</div>
					</li>

			        <li class="">

						<div class="radio_section tb_4_ck">
			            	<input name="sfsi_plus_float_show_on_mobile"  type="checkbox" value="yes" class="styled" <?php echo ($option8['sfsi_plus_float_show_on_mobile']=='yes') ?  'checked="true"' : '' ;?>>
			            </div>

						<div class="sfsiplus_right_info">
							<p><span class="sfsiplus_toglepstpgspn"><?php _e( 'Mobile', 'ultimate-social-media-plus' ); ?></span></p>
						</div>
					</li>
			    </ul>
		</div>
	</div>
</li>
