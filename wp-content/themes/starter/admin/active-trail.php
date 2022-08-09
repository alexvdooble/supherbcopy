<?php
/**
 * Active trail integration
 *
 * @package WordPress
 */

add_action( 'init', 'test_active_trail' );

function test_active_trail() {
	if ( isset( $_GET['tat'] ) ) {
		$data = array(
			'email'      => 'tester100@gmail.com',
			'first_name' => 'alex',
			'last_name'  => 'tester',
			'phone1'     => '0502565656',
		);
		print_r( active_trail_send_lead_to_group( $data ) );
		die();
	}
}

/**
 * Active trail send_lead_to_group
 *
 * @param  array $data  user data array.
 */
function active_trail_send_lead_to_group( $data = array() ) {
	$group_id     = get_field( 'at_group_id', 'option' ); // 440088.
	$api_token    = get_field( 'at_token', 'option' ); // '0X8A5661E5575AD447E22A3294435F43640B7EA3D75A6639FB4386379AE387562A8DC8B5BF9C512283B7BBAD7879281591'.
	$endpoint_url = 'http://webapi.mymarketing.co.il/api/groups/' . $group_id . '/members';

	$email      = isset( $data['email'] ) ? sanitize_text_field( $data['email'] ) : '';
	$first_name = isset( $data['first_name'] ) ? sanitize_text_field( $data['first_name'] ) : '';
	$last_name  = isset( $data['last_name'] ) ? sanitize_text_field( $data['last_name'] ) : '';
	$phone      = isset( $data['phone1'] ) ? sanitize_text_field( $data['phone1'] ) : '';

	$api_data = array(
		'mailing_list' => '1',
		'group'        => $group_id,
		'contacts'     => array(
			array(
				'email'          => $email,
				'sms'            => $phone,
				'first_name'     => $first_name,
				'last_name'      => $last_name,
				'date1'          => str_replace( 'GMT', 'T', gmdate( 'Y-m-dTH:i:s' ) ), // '2022-06-24T14:12:12',
				'is_do_not_mail' => false,
				'is_deleted'     => false,
			),
		),
	);

	$request  = wp_remote_post(
		'http://webapi.mymarketing.co.il/api/contacts/Import',
		array(
			'method'      => 'POST',
			'timeout'     => 1000,
			'body'        => wp_json_encode( $api_data ),
			'data_format' => 'body',
			'headers'     => array(
				'Authorization' => $api_token,
				'content-type'  => 'application/json',
			),
		)
	);
	$response = wp_remote_retrieve_body( $request );
	return $response;
}
