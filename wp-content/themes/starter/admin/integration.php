<?php
/**
 * Remote FTP & Priority integration
 *
 * @package WordPress
 */

add_action( 'woocommerce_payment_complete', 'supherb_payment_complete' );
add_action( 'woocommerce_payment_complete_order_status_processing', 'supherb_payment_complete' );
add_action( 'woocommerce_payment_complete_order_status_completed', 'supherb_payment_complete' );

add_action(
	'init',
	function() {
		if ( current_user_can( 'administrator' ) && isset( $_GET['test_order'] ) ) {
			$order_id = isset( $_GET['test_order'] ) ? $_GET['test_order'] : 51079;
			supherb_payment_complete( $order_id, true );
		}
	}
);

/**
 * Payment_complete hook
 *
 * @param  string $order_id  Order ID.
 */
function supherb_payment_complete( $order_id, $debug = false ) {

	$order = wc_get_order( $order_id );

	$order_file_name_preffix = 'OrdNum-' . $order->get_id() . '-' . gmdate( 'dmyHis' ) . '.txt';

	$order_file_name = './wp-content/themes/starter/local_files/Order/' . $order_file_name_preffix;

	$order_txt_file  = fopen( $order_file_name, 'w' ) or die( 'Unable to open file!' ); //phpcs:ignore

	$currency = $order->get_currency();

	if ( 'ILS' === $currency ) {
		$client_number = 'CRMSUP';
		$client_name   = 'CRM SUPHERB';
	} elseif ( 'USD' === $currency ) {
		$client_number = 'CRMSUP$';
		$client_name   = '$ CRM SUPHERB';
	} else {
		$client_number = 'CRMSUPE';
		$client_name   = 'CRM SUPHERB יורו';
	}

	// Get order data:.
	$order_total           = $order->get_total();
	$order_total_tax       = $order->get_total_tax();
	$order_shipping_method = $order->get_shipping_method();
	$customer_id           = $order->get_customer_id();

	$order_shipping_code = 0;
	if ( 'איסוף עצמי' === $order_shipping_method ) {
		$order_shipping_code  = 0;
		$order_shipping_desc  = 'ללא';
		$order_shipping_price = 0;
	} elseif ( 'משלוח חינם' === $order_shipping_method ) {
		$order_shipping_code  = 10;
		$order_shipping_desc  = $order_shipping_method;
		$order_shipping_price = 0;
	} elseif ( 'שליח עד הבית' === $order_shipping_method ) {
		$order_shipping_code  = 20;
		$order_shipping_desc  = $order_shipping_method;
		$order_shipping_price = 29.90;
	} elseif ( 'שליח מוזל' === $order_shipping_method ) {
		$order_shipping_code  = 30;
		$order_shipping_desc  = $order_shipping_method;
		$order_shipping_price = 14.90;
	} elseif ( 'בוקסיט' === $order_shipping_method ) {
		$order_shipping_code  = 40;
		$order_shipping_desc  = $order_shipping_method;
		$order_shipping_price = 14.90;
	}

	$billing_first_name = $order->get_billing_first_name() ? $order->get_billing_first_name() : '0';
	$billing_last_name  = $order->get_billing_last_name() ? $order->get_billing_last_name() : '0';
	$billing_full_name  = $billing_first_name . ' ' . $billing_last_name;

	$utf8_preffix = "\xEF\xBB\xBF";

	// Line #1 Customer details line.
	$line1 = '1' . "\t" . $client_number . "\t" . $billing_full_name . "\t" . $order_id . "\t" . $customer_id . "\t" . $order_shipping_code . "\n";

	fwrite( $order_txt_file, $utf8_preffix.$line1 ); //phpcs:ignore

	$billing_address_1 = $order->get_billing_address_1() ? $order->get_billing_address_1() : '0';
	$billing_city      = $order->get_billing_city() ? $order->get_billing_city() . "\t" : '0' . "\t";
	$billing_state     = $order->get_billing_state() ? $order->get_billing_state() : '0';
	$billing_postcode  = $order->get_billing_postcode() ? $order->get_billing_postcode() : '0';
	$billing_country   = $order->get_billing_country() ? $order->get_billing_country() : '0';
	$billing_email     = $order->get_billing_email() ? $order->get_billing_email() . "\t" : '0' . "\t";
	$billing_phone     = $order->get_billing_phone() ? $order->get_billing_phone() : '0';
	// custom fields building, appartment, floor.
	$billing_building_number = get_post_meta( $order->get_id(), 'billing_building_number', true ) ? get_post_meta( $order->get_id(), 'billing_building_number', true ) : '0';
	$billing_app_number      = get_post_meta( $order->get_id(), 'billing_app_number', true ) ? get_post_meta( $order->get_id(), 'billing_app_number', true ) : '0';
	$billing_floor_number    = get_post_meta( $order->get_id(), 'billing_floor_number', true ) ? get_post_meta( $order->get_id(), 'billing_floor_number', true ) : '0';

	$ship_to_different_address = get_post_meta( $order->get_id(), 'custom_ship_to_different_address', true );

	// Check if send this lead to active trail.
	$active_trail_lead = get_post_meta( $order->get_id(), 'active_trail_lead', true );
	if ( $active_trail_lead ) {
		$active_trail_data = array(
			'email'      => $billing_email,
			'first_name' => $billing_first_name,
			'last_name'  => $billing_last_name,
			'phone1'     => $billing_phone,
		);
		$lead_response     = active_trail_send_lead_to_group( $active_trail_data );
	}

	// Line #2 Billing address line.
	$line2 = '2' . "\t" . $billing_address_1 . ' ' . $billing_building_number . "\t" . $billing_city . $billing_email . $billing_phone . "\t" . $billing_postcode . "\t" . $billing_app_number . "\t" . $billing_floor_number;

	// Shipping address line.
	$shipping_address_1       = $order->get_shipping_address_1() ? $order->get_shipping_address_1() : '0';
	$shipping_city            = $order->get_shipping_city() ? $order->get_shipping_city() : '0';
	$shipping_state           = $order->get_shipping_state() ? $order->get_shipping_state() : '0';
	$shipping_postcode        = $order->get_shipping_postcode() ? $order->get_shipping_postcode() : '0';
	$shipping_country         = $order->get_shipping_country() ? $order->get_shipping_country() : '0';
	$shipping_building_number = get_post_meta( $order->get_id(), 'shipping_building_number', true ) ? get_post_meta( $order->get_id(), 'shipping_building_number', true ) : '0';
	$shipping_phone_number    = get_post_meta( $order->get_id(), 'shipping_phone_number', true ) ? get_post_meta( $order->get_id(), 'shipping_phone_number', true ) : '0';
	$shipping_app_number      = get_post_meta( $order->get_id(), 'shipping_app_number', true ) ? get_post_meta( $order->get_id(), 'shipping_app_number', true ) : '0';
	$shipping_floor_number    = get_post_meta( $order->get_id(), 'shipping_floor_number', true ) ? get_post_meta( $order->get_id(), 'shipping_floor_number', true ) : '0';

	$shipping_first_name = $order->get_shipping_first_name() ? $order->get_shipping_first_name() : '0'; // Get shipping first name.
	$shipping_last_name  = $order->get_shipping_last_name() ? $order->get_shipping_last_name() : '0'; // Get shipping_last_name.
	$shipping_full_name  = $shipping_first_name . ' ' . $shipping_last_name;

	$addon_fields = "\t" . $shipping_app_number . "\t" . $shipping_floor_number;

	if ( $ship_to_different_address ) {
		if ( $shipping_address_1 || $shipping_city || $shipping_postcode || $shipping_full_name ) {
			$line2 = $line2 . "\t" . $shipping_address_1 . ' ' . $shipping_building_number . "\t" . $shipping_full_name . "\t" . $shipping_city . "\t" . $shipping_phone_number . "\t" . $shipping_postcode . $addon_fields . "\n";
		} else {
			$line2 = $line2 . "\t" . $line2 . $addon_fields . "\n";
		}
	} else {
		$line2 = $line2 . "\t" . $billing_address_1 . ' ' . $billing_building_number . "\t" . $billing_full_name . "\t" . $billing_city . "\t" . $billing_phone . "\t" . $billing_postcode . "\t" . $billing_app_number . "\t" . $billing_floor_number . "\n";
	}

	fwrite( $order_txt_file, $utf8_preffix.$line2 ); //phpcs:ignore

	// Line #3 Credits card + total + payments.
	$number_of_payments = 1;
	$line3              = '3' . "\t" . '40' . "\t" . '0000' . "\t" . $order_total . "\t" . $number_of_payments . "\n";
	fwrite( $order_txt_file, $utf8_preffix.$line3 ); //phpcs:ignore

	$product_variation_ids = array();

	$coupon_applied = count( $order->get_coupon_codes() );
	// Line #4 Makat / Quantity / UnitpriceBeforeTax per product/item.
	foreach ( $order->get_items() as $item_id => $item ) {

		$product_id              = $item->get_product_id();
		$variation_id            = $item->get_variation_id();
		$product_variation_ids[] = $variation_id;

		$item_sku      = $item->get_product()->get_sku();
		$item_quantity = $item->get_quantity();
		$item_price    = $item->get_total();

		$order_product = $item->get_product();
		$active_price  = $order_product->get_price();           // The product active raw price.
		$regular_price = $order_product->get_sale_price();      // The product raw sale price.
		$sale_price    = $order_product->get_regular_price();   // The product raw regular price.

		if ( $coupon_applied ) {
			$active_price = $item_price / $item_quantity;
		}

		$line4 = '4' . "\t" . $item_sku . "\t" . $item_quantity . "\t" . $active_price . "\n";
		fwrite( $order_txt_file, $line4 ); //phpcs:ignore
	}

	// print_r( $product_variation_ids );
	// die();

	fclose( $order_txt_file ); //phpcs:ignore

	upload_order_file_to_remote_ftp_server( $order_file_name );

	// check approve file exists after 10 minutes.
	check_approve_file_after10_minutes( $order->get_id() ); // cron.php.

	// check done file exists after 50 hours.
	check_done_file_after50_hrs( $order->get_id() ); // cron.php.

	if ( $debug ) {
		print_r( $order_file_name );
		?>
		<a target="_blank" href="<?php echo str_replace( './', 'https://supherbcopy.dooble.us/', $order_file_name ); ?>">
			Show file
		</a>
		<?php
		die();
	}

}

/**
 * Upload_order_file_to_remote_ftp_server
 *
 * @param  string $local_file  local file name.
 */
function upload_order_file_to_remote_ftp_server( $local_file ) {

	$user_name  = 'Dooble';
	$user_pass  = 'Aa102030';
	$ftp_server = '185.83.221.20';
	$port       = 21;

	$ftp_conn = ftp_connect( $ftp_server ) or die( "Could not connect to $ftp_server" );
	$login    = ftp_login( $ftp_conn, $user_name, $user_pass );

	// $catalog_contents = ftp_nlist( $ftp_conn, '/Orders' );
	// print_r( $catalog_contents );
	// die();

	$server_file = '/Orders/' . basename( $local_file );

	// initiate upload.
	$d = ftp_nb_put( $ftp_conn, $server_file, $local_file, FTP_BINARY );

	while ( FTP_MOREDATA === $d ) {
		$d = ftp_nb_continue( $ftp_conn );
	}

	if ( FTP_FINISHED !== $d ) {
		echo "Error uploading $local_file";
		exit( 1 );
	}

	// close connection.
	ftp_close( $ftp_conn );
}

add_action( 'init', 'test_import' );

function test_import() {
	if ( current_user_can( 'administrator' ) ) {
		if ( isset( $_GET['import'] ) && $_GET['import'] ) {

			$import_type = sanitize_text_field( $_GET['import'] );

			$user_name  = 'Dooble';
			$user_pass  = 'Aa102030';
			$ftp_server = '185.83.221.20';
			$port       = 21;

			// Logging in in the established ftp connection.
			$ftp_conn = ftp_connect( $ftp_server, $port )
				or die( "Could not connect to $ftp_server" );

			$ftp_login = ftp_login( $ftp_conn, $user_name, $user_pass );

			if ( $ftp_login ) {

				$product_importer = new Product_importer();

				// Import Catalog.
				if ( 'Catalog' === $_GET['import'] ) {
					$catalog_contents = ftp_nlist( $ftp_conn, '/Catalog' );

					if ( $catalog_contents ) {
						$local_file = '';
						foreach ( $catalog_contents as $file_key => $file_name ) {

							$local_file  = './wp-content/themes/starter/local_files' . $file_name;
							$server_file = $file_name;
							$copied      = false;

							if ( ftp_get( $ftp_conn, $local_file, $server_file, FTP_BINARY ) ) {
								$copied = true;
								$product_importer->update_catalog_file( './wp-content/themes/starter/local_files/Catalog' );
							}
							// var_dump( $copied );
							return $copied;
						}
					}
				}

				// Import Mlai.
				if ( 'Mlai' === $_GET['import'] ) {
					$mlai_contents = ftp_nlist( $ftp_conn, '/Mlai' );

					if ( $mlai_contents ) {
						$local_file = '';
						foreach ( $mlai_contents as $file_key => $file_name ) {

							$local_file  = './wp-content/themes/starter/local_files' . $file_name;
							$server_file = $file_name;
							$copied      = false;

							if ( ftp_get( $ftp_conn, $local_file, $server_file, FTP_BINARY ) ) {
								$copied = true;
								$product_importer->update_stock_file( './wp-content/themes/starter/local_files/Mlai' );
							}

							return $copied;
						}
					} else {
						$copied = true;
						$product_importer->update_stock_file( './wp-content/themes/starter/local_files/Mlai' );

						return $copied;
					}
				}
			} else {
				return false;
			}
		}
	}
}
