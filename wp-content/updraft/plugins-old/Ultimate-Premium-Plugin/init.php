<?php

	/* Plugin version setup */
	$oldVersion = get_option( 'sfsi_premium_pluginVersion', false );

	if( false == $oldVersion || sfsi_premium_version_compare( $oldVersion, PLUGIN_CURRENT_VERSION, '<', '17.3' ) ) {
		add_action( 'init', 'sfsi_plus_update_plugin' );
	}

	/* Get verification code */
	if ( is_admin() ) {
		$code = sanitize_text_field( get_option( 'sfsi_premium_verificatiom_code' ) );
		$feed_id = sanitize_text_field( get_option( 'sfsi_premium_feed_id' ) );

		if ( empty( $code ) && !empty( $feed_id ) ) {
			add_action( 'init', 'sfsi_plus_getverification_code' );
		}
	}

	function sfsi_plus_getverification_code() {
		$feed_id = sanitize_text_field( get_option( 'sfsi_premium_feed_id' ) );
		$response = wp_remote_post( 'https://api.follow.it/wordpress/getVerifiedCode_plugin', array(
			'blocking' => true,
			'user-agent' => 'sf get verification',
			'body' => array(
				'feed_id' => $feed_id
			),
			'timeout' => 30,
			'sslverify' => true
		));

		/* Send the request & save response to $resp */
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
		} else {
			$resp = json_decode( $response['body'] );
			update_option( 'sfsi_premium_verificatiom_code', $resp->code );
		}
	}
