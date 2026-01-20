<?php
$classForStickyBar = ($option8['sfsi_plus_sticky_bar'] == 'yes') ? "show" : "hide";
$sfsi_plus_sticky_icons_default = array(
    "default_icons" => array(
        "facebook" => array("active" => "yes", "url" => ""),
        "Twitter" => array("active" => "yes", "url" => ""),
        "Follow" => array("active" => "yes",  "url" => ""),
        "pinterest" => array("active" => "no", "url" => ""),
        "Linkedin" => array("active" => "no", "url" => ""),
        "Whatsapp" => array("active" => "no", "url" => ""),
        "vk" => array("active" => "no", "url" => ""),
        "Odnoklassniki" => array("active" => "no", "url" => ""),
        "Telegram" => array("active" => "no", "url" => ""),
        "Weibo" => array("active" => "no", "url" => ""),
        "QQ2" => array("active" => "no", "url" => ""),
        "xing" => array("active" => "no", "url" => ""),
    ),
    "custom_icons" => array(),
    "settings" => array(
        "desktop" => "no",
        "desktop_width" => 782,
        "desktop_placement" => "left",
        "display_position" => 0,
        "desktop_placement_direction" => "up",
        "mobile" => "no",
        "mobile_width" => 784,
        "mobile_placement" => "left",
        "counts" => 0,
        "bg_color" => "#000000",
        "color" => "#ffffff",
        "share_count_text" => __( "SHARE", "usm-premium-icons" ),
    )
);

$sfsi_premium_sticky_icons = (isset($option8["sfsi_plus_sticky_icons"]) ? $option8["sfsi_plus_sticky_icons"] : $sfsi_plus_sticky_icons_default);

$sfsi_premium_sticky_icons_custom_count = 0;
if( isset( $sfsi_premium_sticky_icons["custom_icons"] ) ) {
    $sfsi_premium_sticky_icons["custom_icons"] = array_values( array_filter( $sfsi_premium_sticky_icons["custom_icons"], function($cus_icon){
        return ($cus_icon["added"] === "yes");
    }));
    $sfsi_premium_sticky_icons_custom_count = count( $sfsi_premium_sticky_icons["custom_icons"] );
}
?>

<li class="sfsiplus_sticky_bar">

    <div class="radio_section tb_4_ck" onclick="checkforinfoslction(this);"><input name="sfsi_plus_sticky_bar" <?php echo ($option8['sfsi_plus_sticky_bar'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_plus_sticky_bar" type="checkbox" value="yes" class="styled" /></div>

    <div class="sfsiplus_right_info">
        <p>
            <span class="sfsiplus_toglepstpgspn">
                <?php _e( 'Sticky bar', 'ultimate-social-media-plus' ); ?>
                <img src="<?php echo SFSI_PLUS_PLUGURL; ?>images/new.gif" alt="<?php _e( 'New', 'ultimate-social-media-plus' ); ?>">
            </span><br>

            <?php

            $_widget_desktop_mobile_setting_style = '';

            if ($option8['sfsi_plus_sticky_bar'] == 'yes') {
                $label_style = 'style="display:block; font-size: 16px;"';
                $_widget_desktop_mobile_setting_style = 'display:block';
            } else {
                $label_style = 'style="font-size: 16px;"';
            }
            ?>
            <label class="sfsiplus_sub-subtitle ckckslctn" <?php echo $label_style; ?>>
                <?php _e('This is independent from the icons selected elsewhere in the plugin.', 'ultimate-social-media-plus'); ?>
                <!-- <a href="<?php echo admin_url('widgets.php'); ?>">
                    <?php _e('Click here', 'ultimate-social-media-plus'); ?>
                </a> -->
            </label>
        </p>

        <!----------- Select Sticky icons ---------------->
        <div class="row sfsi_plus_sticky_bar <?php echo $classForStickyBar; ?>">
            <h4 style="padding-top: 0;">
                <?php _e('Select icons:', 'ultimate-social-media-plus'); ?>
            </h4>
            <div class="icons_size sfsi_plus_sticky_bar_icons_size" style="margin-left: 23px;">
                <ul class="sfsi_plus_new_alignment_option">
                    <li>
                        <div>
                            <ul>
                                <?php foreach ($sfsi_premium_sticky_icons['default_icons'] as $icon => $icon_config) :
                                    ?>
                                    <?php if ($icon !== "GooglePlus") { ?>
                                        <li class="sfsi_premium_sticky_default_icon_container" style="width: 44%;">
                                            <div class="radio_section tb_4_ck">
                                                <input name="sfsi_premium_sticky_<?php echo $icon; ?>_display" <?php echo ($icon_config['active'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_premium_sticky_<?php echo $icon; ?>_display" type="checkbox" value="yes" class="styled" data-icon="<?php echo $icon; ?>" />
                                            </div>
                                            <span class="sfsi_premium_icon_container">
                                                <div class="sfsi_premium_sticky_icon_item_container sfsi_premium_responsive_icon_<?php echo strtolower($icon); ?>_container" style="word-break:break-all;padding-left:0">
                                                    <div style="display: inline-block;height: 40px;width: 40px;text-align: center;vertical-align: middle!important;float: left;display:flex;justify-content:center">
                                                        <img style="float:none" src="<?php echo SFSI_PLUS_PLUGURL; ?>images/responsive-icon/<?php echo $icon; ?><?php echo 'Follow' === $icon ? '.png' : '.svg'; ?>" alt=""></div>
                                                </div>
                                            </span>
                                            <a href="#" class="sfsi_premium_sticky_default_url_toggler" style=""><?php _e('Define url*', 'ultimate-social-media-plus'); ?></a>
                                            <input style="display:none" class="sfsi_premium_sticky_url_input" type="text" placeholder="Enter url" name="sfsi_premium_sticky_<?php echo esc_attr($icon) ?>_url_input" value="<?php echo esc_attr($icon_config["url"]); ?>" />
                                            <a href="#" class="sfsi_premium_sticky_default_url_hide" style="display:none"><span class="sfsi_premium_cancel_text"><?php _e('Cancel', 'ultimate-social-media-plus'); ?></span></a>
                                        </li>
                                    <?php } ?>
                                <?php endforeach; ?>

                                <?php for ($sfsi_premium_rcic = 0; $sfsi_premium_rcic < 5; $sfsi_premium_rcic++) : ?>
                                    <li class="sfsi_premium_sticky_custom_icon_container sfsi_premium_sticky_custom_icon sfsi_premium_sticky_custom_icon_<?php echo $sfsi_premium_rcic; ?>_container" style="<?php echo isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) && (isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]['added']) && $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]['added'] === 'yes') ? 'display:inline-block' : 'display:none'; ?>">
                                        <div style="display: flex;align-items: center;">
                                            <div class="radio_section tb_4_ck">
                                                <input type="hidden" name="sfsi_premium_sticky_custom_<?php echo $sfsi_premium_rcic; ?>_added" value="<?php echo isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) && isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]['added']) ? $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]['added'] : 'no'; ?>">
                                                <input name="sfsi_premium_sticky_custom_<?php echo $sfsi_premium_rcic; ?>_display" <?php echo (isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) && $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]['active'] == 'yes') ?  'checked=true' : ''; ?> id="sfsi_premium_sticky_<?php echo $sfsi_premium_rcic; ?>_display" type="checkbox" value="yes" class="styled" data-custom-index=<?php echo $sfsi_premium_rcic; ?> />
                                            </div>
                                            <span class="sfsi_premium_icon_container">
                                                <div class="sfsi_premium_sticky_icon_item_container sfsi_premium_sticky_icon_custom_<?php echo $sfsi_premium_rcic; ?>_container" style="background-color:<?php echo (isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) && $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["bg-color"] !== "" ? $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["bg-color"] : '#729fcf') ?>; word-break:break-all;padding-left:0">
                                                    <div style="display: inline-block;height: 40px;width: 60px;text-align: center;vertical-align: middle!important;float: left;display:flex;justify-content:center">
                                                        <img alt="" style="max-width: 25px;float:none;max-height: 25px;display: <?php echo (isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) && $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["icon"] !== "") ? 'inline-block' : 'none'; ?>" src="<?php echo (isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) && $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["icon"] !== "") ? $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["icon"] : ''; ?>">
                                                    </div>
                                                </div>
                                            </span>
                                            <input type="text" class="sfsi_premium_sticky_url_input" name="sfsi_premium_sticky_custom_<?php echo esc_attr($sfsi_premium_rcic) ?>_url_input" value="<?php echo (isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) ? esc_attr($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["url"]) : ''); ?>" placeholder="<?php _e('Enter url the icon should link to', 'ultimate-social-media-plus'); ?>" style="width: 240px;"/>
                                            <a href="#" class="sfsi_premium_sticky_custom_delete_btn" id="sfsi_premium_sticky_custom_delete_<?php echo $i =1; ?>_btn" data-id="<?php echo $sfsi_premium_rcic; ?>" style="display:<?php echo ($sfsi_premium_sticky_icons_custom_count - 1) == $sfsi_premium_rcic ? 'inline' : 'none'; ?>"><?php _e('Delete', 'ultimate-social-media-plus'); ?></a>
                                        </div>

                                        <ul style="margin-left: 61px;">
                                            <li style="height: 40px;">
                                                <div style="width: 140px;"><label><?php _e('Background Color', 'ultimate-social-media-plus'); ?></label></div>
                                                <div><input name="sfsi_plus_sticky_icon_<?php echo $sfsi_premium_rcic; ?>_bg_color" class="sfsi_premium_bg-color-picker sfsi_premium_sticky_bg-color-picker" data-default-color="#729fcf" type="text" value="<?php echo isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) && $sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["bg-color"] !== "" ? esc_attr($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["bg-color"]) : '#729fcf' ?>" /></div>
                                            </li>
                                            <li>
                                                <div style="width: 140px;"><label><?php _e('Logo', 'ultimate-social-media-plus'); ?></label></div>
                                                <div><button class=" sfsi_premium_sticky_custom_logo sfsi_premium_logo_custom_<?php echo $sfsi_premium_rcic;  ?>_upload" data-custom-index="<?php echo $sfsi_premium_rcic;  ?>" style="margin-top: 10px; height: 20px; width: 89px; font-size: 15px;line-height: 20px;"><?php _e('Upload Logo', 'ultimate-social-media-plus'); ?></button>
                                                    <input type="hidden" name=sfsi_premium_sticky_icons_custom_<?php echo $sfsi_premium_rcic;  ?>_icon" value="<?php echo isset($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]) ? esc_attr($sfsi_premium_sticky_icons['custom_icons'][$sfsi_premium_rcic]["icon"]) : '' ?>">
                                                </div>
                                            </li>
                                        </ul>
                                    </li>

                                <?php endfor; ?>
                                <li class="sfsi_premium_sticky_custom_icon_container sfsi_premium_sticky_custom_check_option" style="<?php count($sfsi_premium_sticky_icons['custom_icons']) > 4 ? 'display:none' : ''; ?>">
                                    <div class="radio_section tb_4_ck">
                                        <input name="sfsi_premium_sticky_custom_new_display" id="sfsi_premium_sticky_custom_new_display" type="checkbox" class="styled" />
                                    </div>
                                    <span class="sfsi_premium_icon_container">
                                        <div style="text-align: left; margin-top: 0px;padding-left:0">
                                            <span style="color:#69737C;font-size: 20px;font-weight: 700;padding-left:0"><?php _e('Custom', 'ultimate-social-media-plus'); ?></span>
                                        </div>
                                    </span>

                                </li>

                            </ul>
                            <span style="font-size: 16px;width: 96%;margin-left: 7px;line-height: 25px !important;"><?php _e('*All icons have «sharing» feature enabled by default. If you want to give them a different function (e.g. link to your Facebook page) then please click on «Define URL» next to the icon.', 'ultimate-social-media-plus'); ?></span>

                        </div>
                    </li>
                </ul>
            </div>

            <!-----------End Select Sticky icons ---------------->

            <h4 style="padding-top: 0;">
                <?php _e('Show the bar on:', 'ultimate-social-media-plus'); ?>
            </h4>
            <div class="sfsi_premium_sticky_bar section" style="margin-left: 39px;margin-top: 16px;">
                <!------------------ Desktop------------------->
                <div class="sfsi_premium_desktop">
                    <div style="display: flex; align-items: center;">
                        <div>
                            <input name="sfsi_plus_sticky_bar_desktop" <?php echo (isset($sfsi_premium_sticky_icons['settings']['desktop']) && $sfsi_premium_sticky_icons['settings']['desktop'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_plus_sticky_bar_desktop" type="checkbox" value="yes" class="styled">
                        </div>
                        <div style="margin-left: 16px;">
                            <span class="sfsiplus_toglepstpgspn">
                                <?php _e('Desktop', 'ultimate-social-media-plus'); ?>
                            </span>
                        </div>
                    </div>
                    <div style="margin-left: 47px;display: flow-root;">
                        <div class="icons_size">
                            <div class="sfsi_plus_post_icons_size_alignments_element" style="width:100%">
                                <span class="last" style="width: 39%;"><?php _e('Definition of “Desktop”: Screens larger than', 'ultimate-social-media-plus'); ?></span>
                                <input name="sfsi_plus_sticky_bar_desktop_width" type="text" value="<?php echo (isset($sfsi_premium_sticky_icons['settings']['desktop_width']) && $sfsi_premium_sticky_icons['settings']['desktop_width'] != '') ? esc_attr($sfsi_premium_sticky_icons['settings']['desktop_width']) : 782; ?>" />
                                <ins><?php _e('pixels (width)', 'ultimate-social-media-plus'); ?></ins>
                            </div>
                        </div>

                        <div class="icons_size">
                            <span class="sfsi_plus_new_alignment_span" style="line-height: 45px;width: 39%;"><?php _e('Placement of (vertical) sticky bar:', 'ultimate-social-media-plus'); ?></span>
                            <div class="field" style="line-height: 38px;">
                                <select name="sfsi_plus_sticky_bar_desktop_placement" id="sfsi_plus_sticky_bar_desktop_placement">
                                    <option value="left" <?php echo (isset($sfsi_premium_sticky_icons['settings']['desktop_placement']) && $sfsi_premium_sticky_icons['settings']['desktop_placement'] == 'left') ?  'selected="selected"' : ''; ?>>
                                        <?php _e('Left', 'ultimate-social-media-plus'); ?>
                                    </option>
                                    <option value="right" <?php echo (isset($sfsi_premium_sticky_icons['settings']['desktop_placement']) && $sfsi_premium_sticky_icons['settings']['desktop_placement'] == 'right') ?  'selected="selected"' : ''; ?>>
                                        <?php _e('Right', 'ultimate-social-media-plus'); ?>
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="icons_size">

                            <div class="sfsi_plus_post_icons_size_alignments_element" style="margin-right:0;width: 100%;">
                                <span class="sfsi_plus_new_alignment_span" style="line-height: 44px;width: 39%;"><?php _e('Adjust positioning: move the bar… ', 'ultimate-social-media-plus'); ?></span>
                                <input name="sfsi_plus_sticky_bar_display_position" type="text" value="<?php echo (isset($sfsi_premium_sticky_icons['settings']['display_position']) && $sfsi_premium_sticky_icons['settings']['display_position'] != '') ? esc_attr($sfsi_premium_sticky_icons['settings']['display_position']) : 0; ?>" />
                                <ins><?php _e('pixels', 'ultimate-social-media-plus'); ?></ins>
                                <ins style="margin-right: 8px; margin-left: 15px;"><?php _e('more', 'ultimate-social-media-plus'); ?></ins>
                                <div class="field" style="line-height: normal;">
                                    <select name="sfsi_plus_sticky_bar_desktop_placement_direction" id="sfsi_plus_sticky_bar_desktop_placement_direction">
                                        <option value="up" <?php echo (isset($sfsi_premium_sticky_icons['settings']['desktop_placement_direction']) && $sfsi_premium_sticky_icons['settings']['desktop_placement_direction'] == 'up') ?  'selected="selected"' : ''; ?>>
                                            <?php _e('up', 'ultimate-social-media-plus'); ?>
                                        </option>
                                        <option value="down" <?php echo (isset($sfsi_premium_sticky_icons['settings']['desktop_placement_direction']) && $sfsi_premium_sticky_icons['settings']['desktop_placement_direction'] == 'down') ?  'selected="selected"' : ''; ?>>
                                            <?php _e('down', 'ultimate-social-media-plus'); ?>
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <span style="width: 39%;color:#fff">`</span>
                                <span class="sfsi_plus_post_icons_align_center" style="font-size: 16px;width: 54%;line-height: 25px !important;"><?php _e('The sticky bar is always aligned centrally (vertically). If you want to move it higher or lower, please fill the above. ', 'ultimate-social-media-plus'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!------------------End Desktop------------------->

                <!------------------ mobile------------------->
                <div class="sfsi_premium_mobile">
                    <div style="display: flex; align-items: center;">
                        <div>
                            <input name="sfsi_plus_sticky_bar_mobile" <?php echo (isset($sfsi_premium_sticky_icons['settings']['mobile']) && $sfsi_premium_sticky_icons['settings']['mobile'] == 'yes') ?  'checked="true"' : ''; ?> id="sfsi_plus_sticky_bar_mobile" type="checkbox" value="yes" class="styled">
                        </div>
                        <div style="margin-left: 16px;">
                            <span class="sfsiplus_toglepstpgspn">
                                <?php _e('Mobile', 'ultimate-social-media-plus'); ?>
                            </span>
                        </div>
                    </div>
                    <div style="margin-left: 47px;display: flow-root">
                        <div class="icons_size">
                            <div class="sfsi_plus_post_icons_size_alignments_element" style="width: 100%;">
                                <span class="last" style="width: 39%;"><?php _e('Definition of “Mobile”: Screens smaller than', 'ultimate-social-media-plus'); ?></span>
                                <input name="sfsi_plus_sticky_bar_mobile_width" type="text" value="<?php echo (isset($sfsi_premium_sticky_icons['settings']['mobile_width']) && $sfsi_premium_sticky_icons['settings']['mobile_width'] != '') ? esc_attr($sfsi_premium_sticky_icons['settings']['mobile_width']) : 783; ?>" />
                                <ins><?php _e('pixels (width)', 'ultimate-social-media-plus'); ?></ins>
                            </div>
                        </div>

                        <div class="icons_size">
                            <span class="sfsi_plus_new_alignment_span" style="line-height: 48px;width: 39%;"><?php _e('Placement of (horizontal) sticky bar:', 'ultimate-social-media-plus'); ?></span>
                            <div class="field">
                                <select name="sfsi_plus_sticky_bar_mobile_placement" id="sfsi_plus_sticky_bar_mobile_placement">
                                    <option value="top" <?php echo (isset($sfsi_premium_sticky_icons['settings']['mobile_placement']) && $sfsi_premium_sticky_icons['settings']['mobile_placement'] == 'top') ?  'selected="selected"' : ''; ?>>
                                        <?php _e('Top', 'ultimate-social-media-plus'); ?>
                                    </option>
                                    <option value="bottom" <?php echo (isset($sfsi_premium_sticky_icons['settings']['mobile_placement']) && $sfsi_premium_sticky_icons['settings']['mobile_placement'] == 'bottom') ?  'selected="selected"' : ''; ?>>
                                        <?php _e('Bottom', 'ultimate-social-media-plus'); ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!------------------End mobile------------------->
            </div>

        </div>
        <!------------------ Share counts------------------->
        <div class="sfsi_premium_share_counts sfsi_plus_place_beforeAfterPosts sfsi_plus_sticky_bar <?php echo $classForStickyBar; ?>">
            <ul class="sfsiplus_tab_3_icns sfsiplus_shwthmbfraftr">
                <li class="sfsi_premium_responsive_icon_option_li" style="width:100%!important;margin-left: -20px;border-left: 0px solid transparent;">
                    <label class="heading-label">
                        <?php _e('Share count', 'ultimate-social-media-plus'); ?>
                    </label>
                    <div class="options">
                        <label style="width:auto!important">
                            <?php _e('Show the total share count on the left of your icons. It will only be visible if the individual counts are set up under <a href="#" onclick="event.preventDefault();sfsi_premium_scroll_to_div(\'ui-id-9\')" >question 5</a>.', 'ultimate-social-media-plus'); ?>
                        </label>
                    </div>
                    <ul class="sfsiplus_tab_3_icns sfsiplus_shwthmbfraftr" <?php echo ($option4['sfsi_plus_display_counts'] != "yes") ? 'style="display: none;"' : ''; ?>>

                        <li style="width:30%!important" class="col-1-3" onclick="sfsi_premium_responsive_icon_counter_tgl(null, 'sfsi_premium_sticky_bar_share_count', this);sfsi_premium_responsive_toggle_count();" class="clckbltglcls">
                            <input name="sfsi_plus_sticky_bar_counts" <?php echo (isset($sfsi_premium_sticky_icons['settings']['counts']) && $sfsi_premium_sticky_icons['settings']['counts'] == 'yes') ?  'checked="true"' : ''; ?> type="radio" value="yes" class="styled" />
                            <label class="labelhdng4">
                                <?php _e('Yes', 'ultimate-social-media-plus'); ?>
                            </label>
                        </li>
                        <li style="width:30%!important" class="col-1-3" onclick="sfsi_premium_responsive_icon_counter_tgl('sfsi_premium_sticky_bar_share_count', null, this);sfsi_premium_responsive_toggle_count();" class="clckbltglcls">
                            <input name="sfsi_plus_sticky_bar_counts" <?php echo (isset($sfsi_premium_sticky_icons['settings']['counts']) && $sfsi_premium_sticky_icons['settings']['counts'] == 'no') ?  'checked="true"' : ''; ?> type="radio" value="no" class="styled" />
                            <label class="labelhdng4">
                                <?php _e('No', 'ultimate-social-media-plus'); ?>
                            </label>
                        </li>
                        <div class="sfsi_premium_sticky_bar_share_count" <?php echo (isset($sfsi_premium_sticky_icons['settings']['counts']) && $sfsi_premium_sticky_icons['settings']['counts'] != "yes") ? 'style="display: none";' : 0; ?>>
                            <div class="options sfsi_plus_inputSec textBefor_icons_fontcolor">
                                <label class="first">
                                    <?php _e('Background color:', 'ultimate-social-media-plus'); ?>
                                </label>
                                <input name="sfsi_plus_sticky_bar_bg_color" id="sfsi_plus_sticky_bar_bg_color" data-default-color="#000000" type="text" value="<?php echo (isset($sfsi_premium_sticky_icons['settings']['bg_color']) && $sfsi_premium_sticky_icons['settings']['bg_color'] != '') ? esc_attr($sfsi_premium_sticky_icons['settings']['bg_color']) : '#000000'; ?>" />
                            </div>
                            <div class="options sfsi_plus_inputSec textBefor_icons_fontcolor">
                                <label class="first">
                                    <?php _e('Font color (of counts):', 'ultimate-social-media-plus'); ?>
                                </label>
                                <input name="sfsi_plus_sticky_bar_color" id="sfsi_plus_sticky_bar_color" data-default-color="#aaaaaa" type="text" value="<?php echo (isset($sfsi_premium_sticky_icons['settings']['color']) && $sfsi_premium_sticky_icons['settings']['color'] != '') ? esc_attr($sfsi_premium_sticky_icons['settings']['color']) : '#aaaaaa'; ?>" />
                            </div>
                            <div class="options sfsi_plus_inputSec">
                                <label class="first">
                                    <?php _e('Share count text:', 'ultimate-social-media-plus'); ?>
                                </label>
                                <div class="field">
                                    <input name="sfsi_plus_sticky_bar_share_count_text" type="text" value="<?php echo (isset($sfsi_premium_sticky_icons['settings']['share_count_text']) && $sfsi_premium_sticky_icons['settings']['share_count_text'] != '') ? esc_attr($sfsi_premium_sticky_icons['settings']['share_count_text']) : 'SHARES'; ?>" />
                                </div>
                            </div>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
        <!------------------End Share counts------------------->
    </div>

</li>
