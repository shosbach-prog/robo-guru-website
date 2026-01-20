<?php
$option5 = maybe_unserialize(get_option('sfsi_premium_section5_options', false));

// Fetch all saved data
$noPicUrl                = SFSI_PLUS_PLUGURL . "images/no-image.jpg";
$sfsiSocialMediaImage    = isset($option5['sfsiSocialMediaImage']) ? $option5['sfsiSocialMediaImage'] : "";

// Set Image for Preview
$imgSfsiSocialMediaImage = ($sfsiSocialMediaImage != null && isset($sfsiSocialMediaImage) && strlen($sfsiSocialMediaImage) > 0) ? $sfsiSocialMediaImage : $noPicUrl;

// Set value in input hidden field
$valsfsiSocialMediaImage = ($sfsiSocialMediaImage != null && isset($sfsiSocialMediaImage) && strlen($sfsiSocialMediaImage) > 0 && $sfsiSocialMediaImage != $noPicUrl) ? $sfsiSocialMediaImage : "";

$sfsiSocailTitleMaxLength = 95;
$sfsiSocialTitle       = (isset($option5['sfsiSocialtTitleTxt'])) ? $option5['sfsiSocialtTitleTxt'] : "";
$remainsfsiSocialTitle = (isset($sfsiSocialTitle)) ? $sfsiSocailTitleMaxLength - strlen($sfsiSocialTitle) : $sfsiSocailTitleMaxLength;

$sfsiSocailDescMaxLength = 297;
$sfsiSocialDesc        = (isset($option5['sfsiSocialDescription'])) ? $option5['sfsiSocialDescription'] : "";
$remainsfsiSocialDesc  = (isset($sfsiSocialDesc)) ? $sfsiSocailDescMaxLength - strlen($sfsiSocialDesc) : $sfsiSocailDescMaxLength;

// Set Image for Preview
$sfsiPinterestImage    = $option5['sfsiSocialPinterestImage'];
$imgSfsiPinterestImage = ($sfsiPinterestImage != null && isset($sfsiPinterestImage) && strlen($sfsiPinterestImage) > 0) ? $sfsiPinterestImage : $noPicUrl;

// Set value in input hidden field
$valsfsiPinterestImage = ($sfsiPinterestImage != null && isset($sfsiPinterestImage) && strlen($sfsiPinterestImage) > 0 && $imgSfsiPinterestImage != $noPicUrl) ? $sfsiPinterestImage : "";

$sfsiPinterestDesc      = (isset($option5['sfsiSocialPinterestDesc'])) ? $option5['sfsiSocialPinterestDesc'] : "";

$sfsiTwitterDescMaxLength = 106;
$sfsiTwitterDesc       = (isset($option5['sfsiSocialTwitterDesc'])) ? $option5['sfsiSocialTwitterDesc'] : "";
$remainsfsiTwitterDesc = (isset($sfsiTwitterDesc)) ? $sfsiTwitterDescMaxLength - strlen($sfsiTwitterDesc) : $sfsiTwitterDescMaxLength;
?>

<div class="container-fluid noleftrightpadding">

    <div class="row noborder noMariginPaddintTop">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 social_data_container_first">

            <!--********************************** Image for Social Networks (Facebook, LinkedIn & Twitter) STARTS ***********************************************-->

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 noleftrightpadding sfsi_custom_social_data_container">

                <div class="imgTopTxt">
                    <?php _e('<strong>Picture </strong>(for social media sharing)', 'ultimate-social-media-plus'); ?></div>

                <div class="imgContainer imgpicker">

                    <img id="sfsi-social-media-image-preview" src="<?php echo esc_url($imgSfsiSocialMediaImage); ?>" />

                    <?php

                    $uploadBtnGTitle = __( 'Add Picture', 'ultimate-social-media-plus' );
                    $overLayGDisplay = "display:none";

                    if ($noPicUrl != $imgSfsiSocialMediaImage) {

                        $uploadBtnGTitle = __( 'Change Picture', 'ultimate-social-media-plus' );
                        $overLayGDisplay = "display:block";
                    } ?>

                    <div class="usm-overlay"></div>
                    <a style="<?php echo $overLayGDisplay; ?>" data-noimageurl="<?php echo $noPicUrl; ?>"
                        href="javascript:void(0)" class="usm-remove-media" title="<?php _e('Remove', 'ultimate-social-media-plus'); ?>"><span
                            class="dashicons dashicons-no-alt"></span></a>

                </div>

                <div class="imgUploadBtn"><input type="button" id="sfsi-social-media-image-button" class="button"
                        value="<?php _e('Add Picture', 'ultimate-social-media-plus'); ?>" /></div>

                <input type="hidden" name="sfsi-social-media-image" id="sfsi-social-media-image"
                    class="inpt_sfsi_social_media_image" value="<?php echo esc_url($valsfsiSocialMediaImage); ?>" />
            </div>

            <!--********************************** Image for Social Networks (Facebook, LinkedIn & Twitter) CLOSES ***********************************************-->



            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 sfsi_social_title_description_container">

                <!--********************************** TITLE for Social Networks (Facebook, LinkedIn & Twitter) STARTS ***********************************************-->

                <div class="globalTitle    sfsi_custom_social_titlePlusDescription">

                    <div class="sfsi_titlePlusDescription">

                        <div class="sfsi_custom_social_data_title">

                            <div class="imgTopTxt">
                                <?php _e('<strong>Title </strong>(leave blank to use the post title)', 'ultimate-social-media-plus'); ?>
                            </div>

                            <div class="social_title"><textarea name="social_fbGLTw_title_textarea"
                                    class="sfsi_textarea" id="social_fbGLTw_title_textarea"
                                    maxlength="<?php echo $sfsiSocailTitleMaxLength; ?>"><?php echo $sfsiSocialTitle; ?></textarea>
                            </div>

                            <div class="social_description">
                                <?php _e('This title will be used when shared on Facebook, Linkedin and WhatsApp. Leave it blank to use the post title. [Developers: this is used by the open graph meta tag «og:title»]', 'ultimate-social-media-plus'); ?>
                            </div>

                            <div class="remaining_char_box" id="remaining_char_title">
                                <?php echo '<span id="sfsi_title_remaining_char">'.$remainsfsiSocialTitle.'</span> '.__( 'Characters Remaining', 'ultimate-social-media-plus' ); ?>
                            </div>

                        </div>

                    </div>

                </div>

                <!--********************************** TITLE for Social Networks (Facebook, LinkedIn & Twitter) CLOSES ***********************************************-->


                <!--********************************** DESCRIPTION for Social Networks (Facebook, LinkedIn & Twitter) STARTS ***********************************************-->

                <div class="sfsi_custom_social_data_description">

                    <div class="imgTopTxt">
                        <?php _e('<strong>Description </strong>(leave blank to use the post exerpt)', 'ultimate-social-media-plus'); ?>
                    </div>

                    <div class="social_description_container"><textarea name="social_fbGLTw_description_textarea"
                            class="sfsi_textarea" id="social_fbGLTw_description_textarea"
                            maxlength="<?php echo $sfsiSocailDescMaxLength; ?>"><?php echo $sfsiSocialDesc; ?></textarea>
                    </div>

                    <div class="social_description_hint">
                        <?php _e('This description will be used when shared on Facebook, Linkedin, X (Twitter) and WhatsApp (if you use ‘X (Twitter) cards’). Leave it blank to use the post excerpt. [Developers: this is used by the open graph meta tag «og:description»]', 'ultimate-social-media-plus'); ?>
                    </div>

                    <div class="remaining_char_box" id="remaining_char_description">
                        <?php echo '<span id="sfsi_desc_remaining_char">'.$remainsfsiSocialDesc.'</span> '.__( 'Characters Remaining', 'ultimate-social-media-plus' ); ?>
                    </div>

                    <?php sfsi_premium_social_image_issues_support_link(); ?>

                </div>

                <!--********************************** DESCRIPTION for Social Networks (Facebook , LinkedIn & Twitter) CLOSES ***********************************************-->
            </div>

        </div><!-- social_data_container_first closes-->

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 noleftpadding social_data_container_second">


            <!--********************************** Image for PINTEREST STARTS ***********************************************-->

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 sfsi_custom_social_data_container pinterest_global_image">

                <div class="imgTopTxt"><?php _e('<strong>Pinterest image </strong>', 'ultimate-social-media-plus'); ?></div>

                <div class="imgContainer imgpicker">

                    <img id="sfsi-social-pinterest-image-preview"
                        src="<?php echo esc_url($imgSfsiPinterestImage); ?>" />

                    <?php

                        $uploadBtnTitle = __( 'Add Picture', 'ultimate-social-media-plus' );
                        $overLayDisplay = "display:none";

                        if ($noPicUrl != $imgSfsiPinterestImage) {

                            $uploadBtnTitle = __( 'Change Picture', 'ultimate-social-media-plus' );
                            $overLayDisplay = "display:block";
                        } ?>

                    <div class="usm-overlay"></div>
                    <a style="<?php echo $overLayDisplay; ?>" data-noimageurl="<?php echo $noPicUrl; ?>"
                        href="javascript:void(0)" class="usm-remove-media" title="<?php _e('Remove', 'ultimate-social-media-plus'); ?>"><span
                            class="dashicons dashicons-no-alt"></span></a>

                </div>

                <div class="imgUploadBtn"><input type="button" id="sfsi-social-pinterest-image-button" class="button"
                        value="<?php echo $uploadBtnTitle; ?>" /></div>

                <div class="imgTxt">
                    <span><?php _e('The best size is a 1,200px X 630px image (aspect ratio 1.9:1).', 'ultimate-social-media-plus'); ?></span>
                </div>
      
                <input type="hidden" name="sfsi-social-pinterest-image" id="sfsi-social-pinterest-image"
                    placeholder="<?php _e('Enter image url or choose from media library', 'ultimate-social-media-plus'); ?>"
                    value="<?php echo esc_url($valsfsiPinterestImage); ?>" />

            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 imgTopTxt pinterest_gap"><?php _e('<strong></strong>', 'ultimate-social-media-plus'); ?></div>

            <!--********************************** Image for PINTEREST CLOSES ***********************************************-->

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 sfsi_custom_social_titlePlusDescription">

                <div class="sfsi_titlePlusDescription">

                    <!--***************** DESCRIPTION for PINTEREST STARTS *******************************************-->

                    <div class="sfsi_custom_social_data_title">
                        <div class="imgTopTxt">
                            <?php _e('<strong>Pinterest description </strong>(leave blank to use the post title)', 'ultimate-social-media-plus'); ?>
                        </div>

                        <div class="social_title"><textarea name="social_pinterest_description_textarea"
                                class="sfsi_textarea"
                                id="social_pinterest_description_textarea"><?php echo $sfsiPinterestDesc; ?></textarea>
                        </div>

                        <div class="social_description">
                            <?php _e('This description will be used when this post is shared on Pinterest. Leave it blank to use the post title.', 'ultimate-social-media-plus'); ?>
                        </div>
                        <?php sfsi_premium_social_image_issues_support_link(); ?>

                    </div>

                    <!--********************* DESCRIPTION for PINTEREST CLOSES ***********************************************-->

                    <!--************************ TITLE for Twitter STARTS ***********************************************-->

                    <div class="sfsi_custom_social_data_description"> 
                        
                            <div class="imgTopTxt"><?php _e('<strong>X post </strong>', 'ultimate-social-media-plus'); ?></div>

                            <div class="social_description_container"><textarea name="social_twitter_description_textarea" class="sfsi_textarea" id="social_twitter_description_textarea" maxlength="<?php echo $sfsiTwitterDescMaxLength; ?>"><?php echo $sfsiTwitterDesc; ?></textarea></div>

                            <div class="social_description_hint">
                                <?php _e('This will be used as X post-text (the link which get shared will be automatically the added at the end). If you don’t enter anything here the X post text will be used which you defined globally under question 6 on the plugin’s settings page. ', 'ultimate-social-media-plus'); ?>
                            </div>

                            <div class="remaining_char_box" id="remaining_twiter_char_description">
                                <?php echo '<span id="sfsi_twitter_desc_remaining_char">'.$remainsfsiTwitterDesc.'</span> '.__( 'Characters Remaining', 'ultimate-social-media-plus' ); ?>
                            </div>

                        </div>

                    <!--******************************** TITLE for Twitter CLOSES ***********************************************-->

                </div>
            </div><!-- social_data_container_second closes -->

        </div>

    </div><!-- row closes -->

</div><!-- container-fluid closes -->