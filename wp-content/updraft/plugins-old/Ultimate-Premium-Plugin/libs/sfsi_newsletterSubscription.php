<?php
add_action('wp_ajax_mailchimpSubscription','sfsi_plus_mailchimpSubscription'); 
add_action('wp_ajax_nopriv_mailchimpSubscription','sfsi_plus_mailchimpSubscription');        
function sfsi_plus_mailchimpSubscription()
{
	if ( !wp_verify_nonce( $_POST['nonce'], "mailchimpSubscription")) {
        echo  json_encode(array('res'=>'wrong_nonce')); exit;
	}
	$apikey = '98aed08a6e1374c449ad0479f04bf6e1-us13';
	$auth = base64_encode( 'user:'.$apikey );

	$data = array(
		'apikey'        => $apikey,
		'email_address' => "deepak@monadinfotech.com",
		'status'        => 'subscribed',
		'merge_fields'  => array(
			'FNAME' 		=> "deepak",
			'LNAME' 		=> "Joshi",
		)
	);
	$json_data = json_encode($data);


    $curl = wp_remote_post('https://us13.api.mailchimp.com/3.0/lists/8d57733824/members/', array(
		'headers' => array('Content-Type: application/json', 'Authorization: Basic '.$auth),
		'user-agent' => 'PHP-MCAPI/2.0',
		'blocking' => true,
		'timeout'     => 30,
		'sslverify' => true,
		'body'    	=>  $json_data		
	));
	

	print_r(json_decode($curl['body']));
}