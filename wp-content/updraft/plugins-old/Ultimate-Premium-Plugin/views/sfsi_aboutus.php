<style media="screen">
	#wpfooter {
		display: none;
	}
</style>
<h1 class="abt_titl">
	<?php _e( 'Get started by entering your license key ', 'ultimate-social-media-plus'); ?>
	<a href="<?php echo admin_url( 'plugins.php?page='.PLUGIN_ADMIN_SETTING_PAGE); ?>"><?php _e( 'here', 'ultimate-social-media-plus'); ?></a>

</h1>
<div style="display:none">
	<?php
		if(isset($_GET['debug']) && intval($_GET['debug'])=="1"){
		$url = SELLCODES_API_URL;
		// $ch = curl_init($url);
		$curl = wp_remote_get($url, array(

		));

		// curl_setopt($ch, CURLOPT_NOBODY, true);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		// curl_exec($ch);
		// $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// curl_close($ch);
		// var_dump($retcode);
		};
	?>
</div>
