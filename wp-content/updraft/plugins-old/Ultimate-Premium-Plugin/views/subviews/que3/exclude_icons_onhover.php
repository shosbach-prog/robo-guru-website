<ul class="sfsiplus_icn_listing8 sfsi_plus_closerli sfsi_exclude_ul ">

	<li class="">
		<div class="radio_section tb_4_ck">
        	<input name="sfsi_plus_icon_hover_exclude_home" <?php echo ($option8['sfsi_plus_icon_hover_exclude_home']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled" />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php _e( 'Homepage', 'ultimate-social-media-plus' ); ?>
                </span>
            </p>
		</div>
	</li>

    <li class="">
		<div class="radio_section tb_4_ck">
        	<input name="sfsi_plus_icon_hover_exclude_page" <?php echo ($option8['sfsi_plus_icon_hover_exclude_page']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled" />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php _e( 'Other internal pages (not homepage, ...)', 'ultimate-social-media-plus' ); ?>
                </span>
            </p>
		</div>
	</li>

    <li class="">
		<div class="radio_section tb_4_ck">
        	<input name="sfsi_plus_icon_hover_exclude_post" <?php echo ($option8['sfsi_plus_icon_hover_exclude_post']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled" />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php _e( 'Single posts pages', 'ultimate-social-media-plus' ); ?>
                </span>
            </p>
		</div>
	</li>

    <li class="">
		<div class="radio_section tb_4_ck">
        	<input name="sfsi_plus_icon_hover_exclude_tag" <?php echo ($option8['sfsi_plus_icon_hover_exclude_tag']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled"  />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php _e( 'Tag pages', 'ultimate-social-media-plus' ); ?>
                </span>
            </p>
		</div>
	</li>

    <li class="">
		<div class="radio_section tb_4_ck">
        	<input name="sfsi_plus_icon_hover_exclude_category" <?php echo ($option8['sfsi_plus_icon_hover_exclude_category']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled"  />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php _e( 'Category pages', 'ultimate-social-media-plus' ); ?>
                </span>
            </p>
		</div>
	</li>

    <li class="">
		<div class="radio_section tb_4_ck">
        	<input name="sfsi_plus_icon_hover_exclude_date_archive" <?php echo ($option8['sfsi_plus_icon_hover_exclude_date_archive']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled"  />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php _e( 'Date based archives pages', 'ultimate-social-media-plus' ); ?>
                </span>
            </p>
		</div>
	</li>

    <!-- <li class="">
		<div class="radio_section tb_4_ck">
        	<input name="sfsi_plus_icon_hover_exclude_author_archive" <?php echo ($option8['sfsi_plus_icon_hover_exclude_author_archive']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled"  />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php  _e( 'author archives pages', 'ultimate-social-media-plus' ); ?>
                </span
			></p>
		</div>
	</li> -->

    <li class="">
		<div class="radio_section tb_4_ck">
        	<input name="sfsi_plus_icon_hover_exclude_search" <?php echo ($option8['sfsi_plus_icon_hover_exclude_search']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled"  />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php _e( 'Search results pages', 'ultimate-social-media-plus' ); ?>
                </span>
            </p>
		</div>
	</li>

    <!-- Exclude rules for Post Types & Taxonomies  STARTS  here -->
    	<?php include(SFSI_PLUS_DOCROOT.'/views/subviews/que3/exclude_postTypes_taxonomies_onhover.php'); ?>
    <!-- Exclude rules for Post Types & Taxonomies  CLOSES  here -->

    <li class="">
		<div class="radio_section tb_4_ck sfsi_plus_icon_hover_exclude_url_checkSec">
        	<input name="sfsi_plus_icon_hover_exclude_url" <?php echo ($option8['sfsi_plus_icon_hover_exclude_url']=='yes') ?  'checked="true"' : '';?>  type="checkbox" value="yes" class="styled"  />
        </div>
		<div class="sfsiplus_right_info">
			<p>
				<span class="sfsiplus_toglepstpgspn">
                	<?php _e( 'URLs which contain at least one of the following strings:', 'ultimate-social-media-plus' ); ?>
                </span>
            </p>
		</div>
        <div class="sfsi_plus_icon_hover_keywords_container excludecontainter_icon_hover"
        	style="width:100%; display:<?php echo ($option8['sfsi_plus_icon_hover_exclude_url']=='yes') ?  'inline-block' : 'none';?>">
        	<?php
				if(isset($option8['sfsi_plus_icon_hover_exclude_urlKeywords']) && !empty($option8['sfsi_plus_icon_hover_exclude_urlKeywords']) && is_array($option8['sfsi_plus_icon_hover_exclude_urlKeywords']))
				{
					$count = count($option8['sfsi_plus_icon_hover_exclude_urlKeywords']);
					for($i = 0; $i < $count; $i++)
					{
						$serial = $i+1;
						echo '<fieldset>
							<label>'.__( 'String ', 'ultimate-social-media-plus' ).esc_attr($serial).':</label>
							<input type="text" name="sfsi_plus_icon_hover_exclude_urlKeywords[]" value="'.sanitize_text_field($option8['sfsi_plus_icon_hover_exclude_urlKeywords'][$i]).'" />
							<a href="javascript:" class="sfsi_plus_icon_hover_deleteKeywordField">'.__( 'Delete', 'ultimate-social-media-plus' ).'</a>
						</fieldset>';
					}
				}
				else
				{
					$count = 1;
			?>
                <fieldset>
                    <label><?php _e( 'String 1:', 'ultimate-social-media-plus' ); ?></label>
                    <input type="text" name="sfsi_plus_icon_hover_exclude_urlKeywords[]" value="" />
                    <!--<a href="javascript:" class="sfsi_plus_deleteKeywordField">Delete</a>-->
                </fieldset>
            <?php } ?>
        </div>

        <a href="javascript:" class="sfsi_plus_icon_hover_exclude_addAnotherFiled" data-count="<?php echo $count; ?>"
        	style="width:100%;display:<?php echo ($option8['sfsi_plus_icon_hover_exclude_url']=='yes') ?  'inline-block' : 'none';?>">
        	<?php _e( 'Add another one', 'ultimate-social-media-plus' ); ?>
        </a>
	</li>

</ul>
