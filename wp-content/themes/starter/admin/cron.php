<?php
/**
 * Cron jobs functions
 *
 * @package WordPress
 */

add_filter( 'cron_schedules', 'supherb_new_interval' );
add_action( 'supherb_schedule_event', 'supherb_cron_generic_function' );
add_action( 'supherb_schedule_event50hrs', 'supherb_cron50hrs_generic_function' );
/**
 * Add once 10 minute interval to wp schedules
 *
 * @param  array $interval  cron intervals.
 * @return array           [description]
 */
function supherb_new_interval( $interval ) {

	$interval['minutes_10'] = array(
		'interval' => 10 * 60,
		'display'  => 'Once 10 minutes',
	);

	$interval['hours_50'] = array(
		'interval' => 180000, // = 50 hours.
		'display'  => 'Once 50 hours',
	);

	return $interval;
}

/**
 * Check approve_file_after10_minutes
 *
 * @param  string $order_id WC_Order ID.
 */
function check_approve_file_after10_minutes( $order_id ) {
	$time = strtotime( date( 'Y-m-d H:i' ) . ':00' ); //phpcs:ignore
	$args = array( 'order_id' => $order_id );
	wp_schedule_event( $time, 'minutes_10', 'supherb_schedule_event', $args );
	// or.
	// wp_schedule_single_event( strtotime( '+10 minutes' ), 'supherb_schedule_event', $args );
}
/**
 * Check order done file after 50 hours cron
 *
 * @param  string $order_id WC_Order ID.
 */
function check_done_file_after50_hrs( $order_id ) {
	$time = strtotime( date( 'Y-m-d H:i' ) . ':00' ); //phpcs:ignore
	$args = array( 'order_id' => $order_id );
	wp_schedule_event( $time, 'hours_50', 'supherb_schedule_event50hrs', $args );
	// or.
	// wp_schedule_single_event( strtotime( '+50 hour' ), 'supherb_schedule_event50hrs', $args );
}

/**
 * Cron_generic_function
 * Check if Approve file exists
 *
 * @param  string $order_id WC_Order ID.
 */
function supherb_cron_generic_function( $order_id ) {
	// wp_mail( 'alex.v@dooble.co.il', 'Order supherb_cron_generic_function', json_encode( $order_id ) );.
	$order_transient = get_transient( 'order_id_' . $order_id );

	// If email not set yet - send email to admin.
	if ( ! $order_transient ) {

		$user_name  = 'Dooble';
		$user_pass  = 'Aa102030';
		$ftp_server = '185.83.221.20';
		$port       = 21;
		$ftp_conn   = ftp_connect( $ftp_server ) or die( "Could not connect to $ftp_server" ); //phpcs:ignore
		$login      = ftp_login( $ftp_conn, $user_name, $user_pass );

		$catalog_contents = ftp_nlist( $ftp_conn, '/Orders' );

		// Check order APPROVE file .
		if ( $order_id && $catalog_contents ) {
			$approve_file_exists = false;
			foreach ( $catalog_contents as $item ) {
				if ( strpos( $item, '/Orders/ORD' . $order_id . 'Approve' ) === 0 ) {
					$approve_file_exists = true;
					break;
				}
			}

			if ( ! $approve_file_exists ) {
				// if not approved.
				send_order_status_check_email( $order_id );
			} else {
				// if approved.
				$approved_order = wc_get_order( $order_id );
				$approved_order->update_status( 'bebizua', '', true );
			}
		}
		// Last step, very important
		// Set transient to not call this cron action for this order.
		set_transient( 'order_id_' . $order_id, '10min_sent' );

		// Clear cron event.
		wp_clear_scheduled_hook( 'supherb_schedule_event', array( 'order_id' => $order_id ) );
	}
}

/**
 * Cron check if DONE fiel exists after 50hrs order submit
 * Check if DONE file exists
 *
 * @param  string $order_id WC_Order ID.
 */
function supherb_cron50hrs_generic_function( $order_id ) {
	$order_transient = get_transient( 'done_order_id_' . $order_id );

	// If email not set yet - send email to admin.
	if ( ! $order_transient ) {

		// Check order DONE file.
		$user_name  = 'Dooble';
		$user_pass  = 'Aa102030';
		$ftp_server = '185.83.221.20';
		$port       = 21;
		$ftp_conn   = ftp_connect( $ftp_server ) or die( "Could not connect to $ftp_server" ); //phpcs:ignore
		$login      = ftp_login( $ftp_conn, $user_name, $user_pass );

		$catalog_contents = ftp_nlist( $ftp_conn, '/Orders' );

		if ( ! $order_id ) {
			$order_id = 50929;
		}

		// ftp_delete( $ftp_conn, '/Orders/OrdNum-50929-250722120859.txt' );.

		if ( $order_id && $catalog_contents ) {
			$done_file_exists = false;
			foreach ( $catalog_contents as $item ) {
				if ( strpos( $item, '/Orders/ORD' . $order_id . 'Done' ) === 0 ) {
					$done_file_exists = true;
					break;
				}
			}

			if ( ! $done_file_exists ) {
				// If order DONE file NOT exists - send email.
				send_order_status_done_failed_email( $order_id );
			} else {
				// If order DONE file exists - update order status:.
				$done_order = wc_get_order( $order_id );
				$done_order->update_status( 'buza', '', true );
			}
		}

		// Last step, very important
		// Set transient to not call this cron action for this order.
		set_transient( 'done_order_id_' . $order_id, '50hrs_sent' );

		// Clear cron event.
		wp_clear_scheduled_hook( 'supherb_schedule_event50hrs', array( 'order_id' => $order_id ) );
	}
}

/**
 * Send order_status_check_email
 *
 * @param  string $order_id WC_Order ID.
 */
function send_order_status_check_email( $order_id = '' ) {

	if ( ! $order_id ) {
		$order_id = 50929;
	}

	$from_email   = 'notify@supherbcopy.dooble.us';
	$mail_to      = get_field( 'order_status_check_email_to', 'option' );
	$mail_subject = get_field( 'order_status_check_email_subject', 'option' );
	$mail_body    = get_field( 'order_status_check_email_body', 'option' );

	if ( $mail_body ) {
		$mail_body = str_replace( '%%order_id%%', $order_id, $mail_body );
		ob_start();
		?>
		<html>
			<body>
				<div style="direction:rtl; text-align:right;">
					<?php echo wp_kses_post( $mail_body ); ?>
				</div>
			</body>
		</html>
		<?php
		$mail_body = ob_get_clean();
	}

	$headers  = 'From: ' . wp_strip_all_tags( $from_email ) . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

	$wp_send_mail = wp_mail( $mail_to, $mail_subject, $mail_body, $headers );

}

/**
 * Send order status DONE failed email
 *
 * @param  string $order_id WC_Order ID.
 */
function send_order_status_done_failed_email( $order_id = '' ) {

	if ( ! $order_id ) {
		$order_id = 50929;
	}

	$done_failed_transient = get_transient( 'done_failed_' . $order_id );

	if ( ! empty( $done_failed_transient ) ) {

		$from_email   = 'notify@supherbcopy.dooble.us';
		$mail_to      = get_field( 'order_status_done_email_to', 'option' );
		$mail_subject = get_field( 'order_status_done_email_subject', 'option' );
		if ( $mail_subject ) {
			$mail_subject = str_replace( '%%order_id%%', $order_id, $mail_subject );
		}
		$mail_body = get_field( 'order_status_done_email_body', 'option' );

		if ( $mail_body ) {
			$mail_body = str_replace( '%%order_id%%', $order_id, $mail_body );
			ob_start();
			?>
			<html>
				<body>
					<div style="direction:rtl; text-align:right;">
						<?php echo wp_kses_post( $mail_body ); ?>
					</div>
				</body>
			</html>
			<?php
			$mail_body = ob_get_clean();
		}

		$headers  = 'From: ' . wp_strip_all_tags( $from_email ) . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

		$wp_send_mail  = wp_mail( $mail_to, $mail_subject, $mail_body, $headers );
		$set_transient = set_transient( 'done_failed_' . $order_id, 'done_failed', 365 * DAY_IN_SECONDS );

		// remove cron job after execution.
		// wp_clear_scheduled_hook( 'my_new_event' );.

	}

}

function test_cron_check_order( $order_id ) {
	if ( ! $order_id ) {
		$order_id = 50929;
	}
}

add_action(
	'init',
	function() {
		if ( current_user_can( 'administrator' ) && isset( $_GET['ftplist'] ) ) {
			test_remote_ftp_files();
		}
	}
);

/**
 * TEST remote_ftp_files
 *
 * @param string/int $order_id WC_Order ID.
 */
function test_remote_ftp_files( $order_id = '' ) {
	$user_name  = 'Dooble';
	$user_pass  = 'Aa102030';
	$ftp_server = '185.83.221.20';
	$port       = 21;
	$ftp_conn   = ftp_connect( $ftp_server ) or die( "Could not connect to $ftp_server" ); //phpcs:ignore
	$login      = ftp_login( $ftp_conn, $user_name, $user_pass );

	$catalog_contents = ftp_nlist( $ftp_conn, '/Orders' );

	if ( ! $order_id ) {
		$order_id = 50929;
	}

	// ftp_delete( $ftp_conn, '/Orders/OrdNum-50929-250722120859.txt' );.

	if ( $order_id && $catalog_contents ) {
		$approve_file_exists = false;
		foreach ( $catalog_contents as $item ) {
			if ( strpos( $item, '/Orders/ORD' . $order_id . 'Approve' ) === 0 ) {
				$approve_file_exists = true;
				break;
			}
		}

		if ( ! $approve_file_exists ) {
			// if not approved.
			send_order_status_check_email( $order_id );
		} else {
			// if approved.
			$corder = wc_get_order( $order_id );
			$corder->update_status( 'bebizua', '', true );
		}
	}

	print_r( $catalog_contents );
	die();
}

// add_action(
// 'init',
// function() {
// $corder = wc_get_order( 51079 );
// $corder->update_status( 'bebizua', '', true );
// }
// );
