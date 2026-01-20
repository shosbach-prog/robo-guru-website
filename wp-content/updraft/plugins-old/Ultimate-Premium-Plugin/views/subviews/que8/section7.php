<div class="sfsi_plus_tab8_subcontainer">

	<h5 class="sfsi_plus_section_subtitle">
    	<?php  _e( 'Privacy Notice', 'ultimate-social-media-plus' ); ?>
    </h5>

    <!--Left Section-->
    <div class="sfsi_plus_left_container">
    	<?php get_sfsi_plus_SubscriptionForm("privacynotice"); ?>
    </div>

    <!--Right Section-->
    <div class="sfsi_plus_right_container">

        <div class="row_tab privacyrow">

            <label class="sfsi_plus_heding privacyHeading">
                <?php  _e( 'Do you want to show a privacy notice?', 'ultimate-social-media-plus' ); ?>
            </label>

            <ul class="border_shadow ulPrivacyDisplay">

                <li>
                    <input type="radio" class="styled" value="yes" name="sfsi_plus_shall_display_privacy_notice"
                        <?php echo sfsi_plus_isChecked($privacynotice_display, 'yes'); ?> >
                    <label>
                        <?php _e( 'Yes', 'ultimate-social-media-plus' ); ?>
                    </label>
                </li>

                <li>
                    <input type="radio" class="styled" value="no" name="sfsi_plus_shall_display_privacy_notice"
                        <?php echo sfsi_plus_isChecked($privacynotice_display, 'no'); ?> >
                    <label>
                        <?php  _e( 'No', 'ultimate-social-media-plus' ); ?>
                    </label>
                </li>

            </ul>

        </div>

        <?php

            $privacynotice_section_display_class = "yes" == $privacynotice_display ? "show" : "hide";

        ?>

    	<div class="row_tab <?php echo $privacynotice_section_display_class; ?>">

            <label class="sfsi_plus_heding fixwidth sfsi_plus_same_width privacyLabel">
            	<?php  _e( 'Enter text to show below Subscribe button:', 'ultimate-social-media-plus' ); ?>
            </label>

            <div class="sfsi_plus_field">

                <input type="text" class="small new-inp" name="sfsi_plus_form_privacynotice_text"
                    value="<?php echo ($privacynotice_text!='')
								? esc_attr($privacynotice_text) : '' ;
							?>"/>
            </div>
        </div>

        <div class="row_tab <?php echo $privacynotice_section_display_class; ?>">
            <p><?php  _e( 'If you want to enter links: Put the text you want to link in curly brackets, followed by the url in curly brackets, e.g. {Privacy Policy} {https://www.ultimatelysocial.com/privacy} will turn the text «Privacy Policy» into a link (and underlined)', 'ultimate-social-media-plus' ); ?></p>
        </div>

        <!--Row Section-->
        <div class="row_tab <?php echo $privacynotice_section_display_class; ?>">

        	<div class="sfsi_plus_field">

            	<label class="sfsi_plus_same_width">
                	<?php  _e( 'Font:', 'ultimate-social-media-plus' ); ?>
                </label>

                <?php sfsi_plus_get_font("sfsi_plus_form_privacynotice_font", $privacynotice_font); ?>

            </div>

            <div class="sfsi_plus_field">

            	<label>
                	<?php  _e( 'Font style:', 'ultimate-social-media-plus' ); ?>
                </label>

                <?php sfsi_plus_get_fontstyle("sfsi_plus_form_privacynotice_fontstyle", $privacynotice_fontstyle); ?>

            </div>

        </div>

        <!--Row Section-->
        <div class="row_tab <?php echo $privacynotice_section_display_class; ?>">

        	<div class="sfsi_plus_field">

            	<label class="sfsi_plus_same_width">
                	<?php  _e( 'Font color', 'ultimate-social-media-plus' ); ?>
                </label>

                <input type="text" name="sfsi_plus_form_privacynotice_fontcolor" data-default-color="#000000" id="sfsi_plus_form_privacynotice_fontcolor" value="<?php echo ($privacynotice_fontcolor!='')
								? esc_attr($privacynotice_fontcolor) : '' ;
							?>">
            </div>
            <div class="sfsi_plus_field">

            	<label>
                	<?php  _e( 'Font size', 'ultimate-social-media-plus' ); ?>
                </label>

                <input min="12" type="number" class="small rec-inp" name="sfsi_plus_form_privacynotice_fontsize"
                	value="<?php echo ($privacynotice_fontsize!='')
								? $privacynotice_fontsize : '' ;?>"/>

                <span class="pix">
                	<?php  _e( 'pixels', 'ultimate-social-media-plus' ); ?>
                </span>

            </div>
        </div>

        <!--Row Section-->
        <div class="row_tab <?php echo $privacynotice_section_display_class; ?>">

        	<div class="sfsi_plus_field">

            	<label class="sfsi_plus_same_width">
                	<?php  _e( 'Alignment:', 'ultimate-social-media-plus' ); ?>
                </label>

                <?php sfsi_plus_get_alignment("sfsi_plus_form_privacynotice_fontalign", $privacynotice_fontalign); ?>

            </div>

        </div>

        <!--End Section-->
    </div>

</div>
