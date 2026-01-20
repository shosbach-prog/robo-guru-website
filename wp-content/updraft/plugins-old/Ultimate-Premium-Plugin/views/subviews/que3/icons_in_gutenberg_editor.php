<?php 
    $option8 = maybe_unserialize(get_option('sfsi_premium_section8_options', false));
    $option8['sfsi_plus_place_item_gutenberg'] 		= (isset($option8['sfsi_plus_place_item_gutenberg']))
    ? sanitize_text_field($option8['sfsi_plus_place_item_gutenberg'])
    : 'no';
?>
<!--Fifth Section-->
<li class="sfsiplusplaceusinggutenberg">
    <div class="radio_section tb_4_ck" onclick="checkforinfoslction(this);"><input name="sfsi_plus_place_item_gutenberg" <?php echo ($option8['sfsi_plus_place_item_gutenberg'] == 'yes') ? 'checked="true"' : ''; ?> id="sfsi_plus_place_item_gutenberg" type="checkbox" value="yes" class="styled" /></div>
    <div class="sfsiplus_right_info">
        <p>
            <span class="sfsiplus_toglepstpgspn">
                <?php _e( 'Show them in the Gutenberg editor', 'ultimate-social-media-plus' ); ?>
                <img src="<?php echo SFSI_PLUS_PLUGURL; ?>images/new.gif" alt="<?php _e( 'New', 'ultimate-social-media-plus' ); ?>">
            </span><br>
            <?php
            if ($option8['sfsi_plus_place_item_gutenberg'] == 'yes') {
                $label_style = 'style="display:block; font-size: 15px;"';
            } else {
                $label_style = 'style="font-size: 15px;"';
            }
            ?>
            <label class="sfsiplus_sub-subtitle ckckslctn" <?php echo $label_style; ?>>
                <?php _e( 'Look for this sign', 'ultimate-social-media-plus' ); ?> <img width="20" src="<?php echo SFSI_PLUS_PLUGURL ?>images/sfsi_block_icon.jpg" alt=""> <?php _e( ' in your Gutenberg editor and click on it. Then a new block with the icons will be added.', 'ultimate-social-media-plus' ); ?>
            </label>
        </p>
    </div>
</li>