<?php
$allTaxonomies = get_taxonomies( array( 'public' => true, 'show_ui' => true ), 'objects', 'and' );
$tcount        = count( $allTaxonomies );

if ( isset( $option8['sfsi_plus_taxonomies_for_icons'] ) && is_string( $option8['sfsi_plus_taxonomies_for_icons'] ) ) {
	$arrSfsi_plus_taxonomies_for_icons = ( isset( $option8['sfsi_plus_taxonomies_for_icons'] ) ) ? maybe_unserialize( $option8['sfsi_plus_taxonomies_for_icons'] ) : array();
	$arrSfsi_plus_taxonomies_for_icons = is_array( $arrSfsi_plus_taxonomies_for_icons ) ? $arrSfsi_plus_taxonomies_for_icons : array();
} else if ( isset( $option8['sfsi_plus_taxonomies_for_icons'] ) && is_array( $option8['sfsi_plus_taxonomies_for_icons'] ) ) {
	$arrSfsi_plus_taxonomies_for_icons = ( isset( $option8['sfsi_plus_taxonomies_for_icons'] ) ) ? ( $option8['sfsi_plus_taxonomies_for_icons'] ) : array();
	$arrSfsi_plus_taxonomies_for_icons = is_array( $arrSfsi_plus_taxonomies_for_icons ) ? $arrSfsi_plus_taxonomies_for_icons : array();
}

$scount = is_array( $arrSfsi_plus_taxonomies_for_icons ) ? count( $arrSfsi_plus_taxonomies_for_icons ) : 0;
?>

<li>
    <div class="options sfsi_plus_choose_post_types_section"
         style="<?php echo ( $option8['sfsi_plus_display_button_type'] == 'responsive_button' ) ? 'display:none' : ''; ?>">

        <div class="sfsi_plus_choose_post_type_wrap">

            <label style="width:356px!important;">
                <p><?php _e( 'Do you also want to show the icon on taxonomy pages (e.g. category pages)? Select all where you want them to show:', 'ultimate-social-media-plus' ); ?></p>
            </label>
            <select multiple="multiple" name="sfsi_plus_taxonomies_for_icons" id="sfsi_plus_taxonomies_for_icons"
                    style="min-width: 327px;margin-top: 10px;width:50%!important">

                <option value=""><?php _e( '------------- Choose Taxonomies -------------', 'ultimate-social-media-plus' ); ?></option>

				<?php if ( $tcount > 0 ) {

					foreach ( $allTaxonomies as $taxonomy ) {

						$selected_box = ( $scount > 0 && in_array( $taxonomy->name, $arrSfsi_plus_taxonomies_for_icons ) ) ? 'selected="selected"' : '';
						?>
                        <option <?php echo $selected_box; ?>
                                value="<?php echo $taxonomy->name; ?>"><?php echo ucfirst( $taxonomy->label ); ?></option>
					<?php }
				} ?>

            </select>

            <div class="sfsi_ctrl_instruct cposttype"><?php _e( 'Please hold the CTRL key to select multiple taxonomies.', 'ultimate-social-media-plus' ); ?></div>

        </div>
    </div>
</li>