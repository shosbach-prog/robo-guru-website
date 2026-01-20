<!-- Loader Image section  -->
<div id="sfpluspageLoad">

</div>
<!-- END Loader Image section  -->
<!-- Error debug data from db. -->
<div id="fb_comulative_errors" style="display:none">
    <?php
    if (isset($_GET['debug']) && intval($_GET['debug']) == "1"){
        echo '<pre>';
        print_r( _get_cron_array() );
        echo '</pre>';
        var_dump('issue', get_option('sfsi_premium_fb_batch_api_issue'));
        $fb_helper = new sfsiFacebookSocialHelper();
        var_dump('cached_data', $fb_helper->sfsi_get_cached_data_fbcount());
        var_dump('last_Call_log', $fb_helper->sfsi_get_fb_api_last_call_log());
        var_dump('all_url', $fb_helper->sfsi_get_all_siteurls());
        $caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options',"a:0:{}"));
        var_dump("last_debug_option",$caching_debug_option);
        if(isset($_GET["caching_debug"]) && $_GET["caching_debug"]=="1"){
            if( isset($_GET["caching_debug_on"])  ) {
                $caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options',"a:0:{}"));
                $caching_debug_option["on"]="yes";
                $caching_debug_option["for"]= isset($_GET["caching_debug_for"])?$_GET["caching_debug_for"]:sfsi_premium_get_client_ip();
                update_option('sfsi_premium_cache_debug_options', serialize($caching_debug_option));
            }else if(isset($_GET["caching_debug_off"])){
                $caching_debug_option = maybe_unserialize(get_option('sfsi_premium_cache_debug_options',"a:0:{}"));
                $caching_debug_option["on"]="no";
                update_option('sfsi_premium_cache_debug_options', serialize($caching_debug_option));
            }
        }
    }
    ?>
</div>
<!-- End Error debug data from db. -->
<!-- javascript error loader  -->
<div class="error" id="sfsi_onload_errors" style="margin-left: 60px;display: none;">
    <p>
        <?php _e('We found errors in your javascript which may cause the plugin to not work properly. Please fix the error:', 'ultimate-social-media-plus'); ?>
    </p>
    <p id="sfsi_jerrors"></p>
</div>
<!-- END javascript error loader  -->

<!-- START Admin view for plugin-->
<div class="wapper sfsi_mainContainer" id="usm-plus-main-wrapper-box">

    <!-- Top content area of plugin -->
    <div class="main_contant">

        <div class="sfsi_plus_heading">
            <img src="<?php echo SFSI_PLUS_PLUGURL . "/images/premium-logo.png" ?>" alt="premium-logo" width="35" height="35" />
            <h1>
                <?php _e('Welcome to the Ultimate Social Media PREMIUM plugin!', 'ultimate-social-media-plus'); ?>
            </h1>
        </div>

        <p>
            <?php _e('Simply answer the questions below (at least the first 3) - that`s it!', 'ultimate-social-media-plus'); ?>
        </p>
        <p>
            <?php _e('If you have questions, or something doesn`t work as it should, please raise a ', 'ultimate-social-media-plus'); ?>

            <!--<a href="https://goo.gl/MU6pTN#no-topic-0" target="_blank">
                <?php //_e(' Support Forum','ultimate-social-media-plus');
                ?>
            </a>

            <?php //_e('.&nbsp;We\'ll try to respond quickly!.','ultimate-social-media-plus');
            ?>-->

            <?php //_e('&nbsp;or, if your question is not answered in the FAQ, please contact us', 'ultimate-social-media-plus' );
            ?>

            <!--<a href="<?php //echo License_Manager::supportLink();
                            ?>" target="_blank" class="lit_txt">-->
            <a href="<?php echo License_Manager::supportLink(true); ?>" target="_blank">
                <?php _e( 'Support Ticket', 'ultimate-social-media-plus' ); ?>
            </a>
        </p>
        <p><?php
            printf(
                __( '%1$sNew:%2$s Share the plugin with friends and earn 40&#37; of every sale you helped to generate! %3$sLearn more%4$s', 'ultimate-social-media-plus' ),
                '<a style="text-decoration:none;" href="javascript:void(0);">',
                '</a>',
                '<a class="learnmore" href="javascript:void(0);">',
                '</a>'
            );
        ?></p>

    </div>
    <!-- END Top content area of plugin -->

    <!-- step 1 end  here -->
    <div id="accordion">
        <h3><span>1</span>
            <?php _e('Which icons do you want to show on your site?', 'ultimate-social-media-plus'); ?>
        </h3>
        <!-- step 1 end  here -->
        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_option_view1.php'); ?>
        <!-- step 1 end here -->

        <!-- step 2 start here -->
        <h3><span>2</span>
            <?php _e('What do you want the icon to do?', 'ultimate-social-media-plus'); ?>
        </h3>
        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_option_view2.php'); ?>
        <!-- step 2 END here -->

        <!-- step new 3 start here -->
        <h3><span>3</span>
            <?php _e('Where shall they be displayed?', 'ultimate-social-media-plus'); ?>
        </h3>
        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_option_view8.php'); ?>
        <!-- step new3 end here -->
    </div>
    <h2 class="optional">
        <?php _e('Optional', 'ultimate-social-media-plus'); ?>
    </h2>
    <div id="accordion1">
        <!-- step old 3 start here -->
        <h3><span>4</span>
            <?php _e('What design and animation do you want to give your icons?', 'ultimate-social-media-plus'); ?>
        </h3>
        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_option_view3.php'); ?>
        <!-- step old 3 END here -->

        <!-- step old 4 Start here -->
        <h3><span>5</span>
            <?php _e('Do you want to display "counts" next to your main icons?', 'ultimate-social-media-plus'); ?>
        </h3>
        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_option_view4.php'); ?>
        <!-- step old 4 END here -->

        <!-- step old 5 Start here -->
        <h3><span>6</span>
            <?php _e('Any other wishes for your main icons?', 'ultimate-social-media-plus'); ?>
        </h3>
        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_option_view5.php'); ?>
        <!-- step old 5 END here -->

        <!-- step old 7 Start here -->
        <h3><span>7</span>
            <?php _e('Do you want to display a pop-up, asking people to subscribe?', 'ultimate-social-media-plus'); ?>
        </h3>
        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_option_view7.php'); ?>
        <!-- step old 7 END here -->

        <!-- step old 8 Start here -->
        <h3><span>8</span>
            <?php _e('Do you want to show a subscription form (increases sign ups)?', 'ultimate-social-media-plus'); ?>
        </h3>
        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_option_view9.php'); ?>
        <!-- step old 8 END here -->


    </div>
    <div class="tab10">
        <div class="save_export">
            <div class="save_button">
                <img src="<?php echo SFSI_PLUS_PLUGURL; ?>images/ajax-loader.gif" alt="loader" class="loader-img" />
                <a href="javascript:;" id="save_plus_all_settings" title="<?php _e('Save All Settings', 'ultimate-social-media-plus'); ?>">
                    <?php _e('Save All Settings', 'ultimate-social-media-plus'); ?>
                </a>
            </div>
            <?php $nonce = wp_create_nonce("sfsi_premium_save_export"); ?>

            <div class="export_selections">
                <div class="sfsi_premium_export" id="sfsi_premium_save_export" data-nonce="<?php echo $nonce; ?>">
                    <?php _e('Export', 'ultimate-social-media-plus'); ?>
                </div>
                <div style="font-size: 18px;"><?php _e('or ', 'ultimate-social-media-plus'); ?></div>
                <div class="sfsi_premium_import">
                    <input type="file" id="sfsi_premium_file_input" accept="file_extension" name="pic" class="open_file hidden" placeholder="<?php _e('Import', 'ultimate-social-media-plus'); ?>" data-nonce="<?php echo $nonce; ?>">
                    <div onclick="importFileOpen()">
                        <?php _e('Import', 'ultimate-social-media-plus'); ?>
                    </div>
                </div>
                <div style="font-size: 18px;"><?php _e('selections', 'ultimate-social-media-plus'); ?></div>
            </div>
        </div>
        <p class="red_txt errorMsg" style="display:none; font-size:21px"> </p>
        <p class="green_txt sucMsg" style="display:none;font-size:21px"> </p>

        <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_affiliate_banner.php'); ?>

    </div>
    <!-- all pops of plugin under sfsi_pop_content.php file -->
    <?php include(SFSI_PLUS_DOCROOT . '/views/sfsi_pop_content.php'); ?>
</div>

<br><br>
<div class="usm-banner-container">
  <?php do_action('ins_global_print_carrousel'); ?>
</div>
