<?php $option5['sfsi_plus_custom_admin_css'] =	(isset($option5['sfsi_plus_custom_admin_css']) && !empty($option5['sfsi_plus_custom_admin_css'])) ? maybe_unserialize($option5['sfsi_plus_custom_admin_css']) :'';?>

<div class="row customcss">
	<h4>
        <?php  _e( 'Custom CSS (plugin setting page)', 'ultimate-social-media-plus' ); ?>
    </h4>
	<p>
		<?php  _e( 'Here you can correct conflicts (e.g. caused by other plugins) which make the USM\'s settings page (the page you\'re currently on) unusable. Please use "!important" so that it actually overwrites the conflicting CSS', 'ultimate-social-media-plus' ); ?>
	</p>

     <textarea style="display: inline-block !important;" name="sfsi_plus_custom_admin_css" id="sfsi_plus_custom_admin_css" type="text" class="add_txt" placeholder="" /><?php echo sanitize_text_field($option5['sfsi_plus_custom_admin_css']); ?></textarea>
</div>
