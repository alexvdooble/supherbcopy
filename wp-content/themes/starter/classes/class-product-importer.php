<?php
/**
 * Product_importer class
 *
 * @package WordPress
 */

/**
 * Product_importer
 */
class Product_importer {

	/**
	 * Construct description]
	 */
	public function __construct() {
		$this->folder_name       = '/supherb_files';
		$this->folder_path       = UPLOADS_PATH . $this->folder_name;
		$this->folder_local_path = get_template_directory() . '/local_files';
		$this->product_file_arr  = array();

		add_filter( 'cron_schedules', array( $this, 'add_cron_interval' ) );

		if ( ! wp_next_scheduled( 'update_catalog_file' ) ) {
			wp_schedule_event( time(), 'hourly', 'update_catalog_file' );
		}

		add_action( 'update_catalog_file', array( $this, 'update_catalog_file' ) );

		if ( ! wp_next_scheduled( 'update_stock_file' ) ) {
			wp_schedule_event( time(), 'double_minute', 'update_stock_file' );
		}

		add_action( 'update_stock_file', array( $this, 'update_stock_file' ) );
	}

	/**
	 * [add_cron_interval description]
	 *
	 * @param array $schedules params.
	 */
	public function add_cron_interval( $schedules ) {
		$schedules['double_minute'] = array(
			'interval' => 120,
			'display'  => __( '2 Mintues' ),
		);
		return $schedules;
	}

	/**
	 * FTP connection credits
	 *
	 * @return array ftp server details
	 */
	public function get_ftp_credits() {
		$user_name  = 'Dooble';
		$user_pass  = 'Aa102030';
		$ftp_server = '185.83.221.20';
		$port       = 21;

		return array(
			'user_name'  => $user_name,
			'user_pass'  => $user_pass,
			'ftp_server' => $ftp_server,
			'port'       => $port,
		);
	}

	/**
	 * Update_catalog_file
	 *
	 * @param string $folder_path folder path.
	 */
	public function update_catalog_file( $folder_path = '' ) {
		if ( ! $folder_path ) {
			$files = scandir( $this->folder_path );
		} else {
			$files = scandir( $folder_path );
		}

		// $ftp_credits = $this->get_ftp_credits();
		// print_r( $ftp_credits );
		// $conn_id = ftp_connect( $ftp_credits['ftp_server'] );
		//
		// if ( $conn_id ) {
		// $login_result = ftp_login( $conn_id, $ftp_credits['user_name'], $ftp_credits['user_pass'] );
		// if ( $login_result ) {
		// if ( ftp_delete( $conn_id, '/Catalog/Catalog_1406220858.txt' ) ) {
		// echo "/Catalog/Catalog_1406220858.txt deleted successful\n";
		// } else {
		// echo 'could not delete /Catalog/Catalog_1406220858.txt';
		// }
		// }
		// }
		// ftp_close( $conn_id );
		// die();

		if ( $files ) {
			foreach ( $files as $key => $file_name ) {
				// if is catalog file.
				if ( strpos( $file_name, 'Catalog' ) !== false ) {
					$this->check_catalog_file( $file_name );
				}
			}
		}

	}

	/**
	 * Update_stock_file description
	 *
	 * @param string $folder_path folder path.
	 */
	public function update_stock_file( $folder_path = '' ) {
		if ( ! $folder_path ) {
			$files = scandir( $this->folder_path );
		} else {
			$files = scandir( $folder_path );
		}

		if ( $files ) {
			foreach ( $files as $key => $file_name ) {
				// if is stock file.
				if ( strpos( $file_name, 'Mlai' ) !== false ) {
					$this->check_stock_file( $file_name );
				}
			}
		}
	}

	/**
	 * Check_stock_file
	 *
	 * @param  string $file_name               [description]
	 * @return [type]            [description]
	 */
	public function check_stock_file( $file_name ) {

		// $file = file( $this->folder_path . '/' . $file_name );
		$file = file( $this->folder_local_path . '/Mlai/' . $file_name );

		// echo '<pre style="direction:ltr">';
		// print_r( $file[87] );
		// print_r( $file[88] );
		// print_r( $file[90] );
		// print_r( $file[99] );
		// print_r( $file[106] );
		// echo '</pre>';
		// die();

		if ( $file ) {
			foreach ( $file as $key => $row ) {
				$row_arr = preg_split( '/\s+/', $row, null, PREG_SPLIT_NO_EMPTY );

				$row_array = array();
				$lines     = explode( PHP_EOL, $row );
				$l         = 0;
				foreach ( $lines as $line ) {
					if ( $line ) {
						$row_array[ $l ] = explode( "\t", $line );
						$l++;
					}
				}
				$row_array = reset( $row_array );

				// if SKU is exists.
				if ( isset( $row_array[0] ) && ! empty( $row_array[0] ) ) {

					$sku = $row_array[0];

					// title.
					$stock = $row_array[1];

					// stock quantity.
					$stock = $row_array[2];
					$stock = (int) round( $row_array[2], 0 );

					if ( $stock < 0 ) {
						$stock = 0;
					}

					$is_variable_product = false;

					// variation sku.
					$variation_sku = isset( $row_array[3] ) ? $row_array[3] : '';
					// variation name.
					$variation_name = isset( $row_array[4] ) ? $row_array[4] : '';

					if ( $variation_sku && $variation_name ) {
						$is_variable_product = true;
					}

					$product_id = $this->get_product_by_sku( $sku );

					if ( $product_id && ! $is_variable_product ) {

						$this_product = new WC_Product( $product_id );

						update_post_meta( $product_id, '_manage_stock', 'yes' );

						if ( $stock <= 20 ) {
							update_post_meta( $product_id, '_stock_status', wc_clean( 'outofstock' ) );
							wp_set_post_terms( $product_id, 'outofstock', 'product_visibility', true );
							$this_product->set_stock_status( wc_clean( 'outofstock' ) );

						} else {
							update_post_meta( $product_id, '_stock_status', wc_clean( 'instock' ) );
							wp_set_post_terms( $product_id, 'outofstock', 'product_visibility', false );
							$this_product->set_stock_status( wc_clean( 'instock' ) );
						}

						update_post_meta( $product_id, '_stock', (int) $stock );
						wc_delete_product_transients( $product_id );

					} elseif ( $product_id && ! $is_variable_product ) {

					}
				}
			}
		}
	}

	/**
	 * Check_catalog_file
	 *
	 * @param  [string] $file_name               [description].
	 */
	public function check_catalog_file( $file_name ) {

		$file = file( $this->folder_local_path . '/Catalog/' . $file_name );

		if ( $file ) {

			$all_products = array();

			foreach ( $file as $key => $row ) {

				// split text row to array.
				$row_arr = preg_split( '/\s+/', $row, null, PREG_SPLIT_NO_EMPTY );

				// Create product array devided by TAB.
				$product_array = array();
				$lines         = explode( PHP_EOL, $row );

				$l = 0;
				foreach ( $lines as $line ) {
					if ( $line ) {
						$product_array[ $l ] = explode( "\t", $line );
						$l++;
					}
				}

				$product_array = reset( $product_array );
				if ( $product_array ) {
					$product_sku            = isset( $product_array[0] ) ? $product_array[0] : '';
					$product_title          = isset( $product_array[1] ) ? $product_array[1] : '';
					$product_price          = isset( $product_array[2] ) ? $product_array[2] : '';
					$product_variation_id   = isset( $product_array[3] ) ? $product_array[3] : '';
					$product_variation_name = isset( $product_array[4] ) ? $product_array[4] : '';

					if ( $product_variation_id ) {
						$all_products[ $product_variation_id ][] = array(
							'product_sku'            => $product_sku,
							'product_title'          => $product_title,
							'product_price'          => $product_price,
							'product_variation_id'   => $product_variation_id,
							'product_variation_name' => $product_variation_name,
						);
					} else {
						// collect all simple products from the catalog file.
						$all_products[] = array(
							'product_sku'            => $product_sku,
							'product_title'          => $product_title,
							'product_price'          => $product_price,
							'product_variation_id'   => '',
							'product_variation_name' => '',
						);
					}
				}

				if ( $product_sku ) {
					// Check if product SKU starts with SU string.
					// if ( str_starts_with( $product_sku, 'SU' ) ) {
					// $this->product_file_arr[] = array(
					// 'title' => $product_title,
					// 'sku'   => $product_sku,
					// 'price' => $product_price,
					// );
					// }

					$this->product_file_arr[] = array(
						'title' => $product_title,
						'sku'   => $product_sku,
						'price' => $product_price,
					);
				}
			}

			$variable_product_items = array();
			$simple_product_items   = array();

			if ( $all_products ) {
				foreach ( $all_products as $product_item ) {
					if ( count( $product_item ) === count( $product_item, COUNT_RECURSIVE ) ) {
						// Upload simple WC Product.
						$simple_product_items[] = $product_item;
							$this->upload_products_from_arr( $product_item );
					} else {
						$variable_product_items[] = $product_item;
					}
				}
			}

			$imported_product_ids = array();
			if ( $variable_product_items ) {
				foreach ( $variable_product_items as $product_item ) {
					$imported_product_ids[] = $this->upload_variable_product_from_array( $product_item );
				}
			}

			return $imported_product_ids;

			// print_r( $imported_product_ids );
			// die();
		}
	}

	/**
	 * Check if parent variable product exists
	 *
	 * @param  string $sku  parent product SKU.
	 * @return boolean      true/false
	 */
	public function parent_product_exists( $sku ) {
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'meta_key'       => '_sku',					//phpcs:ignore
			'meta_value'     => $sku,	//phpcs:ignore
			'post_status'    => array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit' ),
		);
		$id   = false;
		$find = new WP_Query( $args );

		if ( $find->have_posts() ) {
			while ( $find->have_posts() ) :
				$find->the_post();
				$id = get_the_ID();
			endwhile;
			wp_reset_postdata();
		}
		return $id;
	}

	/**
	 * Upload_variable_product_from_array
	 *
	 * @param  array $product_data  product array.
	 */
	public function upload_variable_product_from_array( $product_data ) {

		$product_variations_values = array();

		foreach ( $product_data as $product_item_data ) {
			$variation_name              = (int) $product_item_data['product_variation_name'];
			$product_variations_values[] = $variation_name . ' כמוסות';
		}

		$parent_product_sku = $product_data[0]['product_sku'] . '_parent';
		$product_exists     = $this->parent_product_exists( $parent_product_sku );

		if ( ! $product_exists ) {
			// Creating a variable product.
			$product = new WC_Product_Variable();
			$product->set_name( $product_data[0]['product_title'] );
			$product->set_sku( $product_data[0]['product_sku'] . '_parent' );

			// one available for variation attribute.
			$attribute = new WC_Product_Attribute();
			$attribute->set_name( 'Size' );
			$attribute->set_options( $product_variations_values );
			$attribute->set_position( 0 );
			$attribute->set_visible( true );
			$attribute->set_variation( true );

			$product->set_attributes( array( $attribute ) );

			// save the changes and go on.
			$product->save();

			if ( $product_data ) {
				foreach ( $product_data as $product_variation ) {
					$variation = new WC_Product_Variation();
					$variation->set_parent_id( $product->get_id() );
					$variation_name = (int) $product_variation['product_variation_name'] . ' כמוסות';
					$variation->set_attributes( array( 'size' => $variation_name ) );
					$variation->set_regular_price( $product_variation['product_price'] );

					// Check if simple product exists and override his sku with suffix "_old".
					$check_sku = $this->get_product_by_sku( $product_variation['product_sku'] );
					if ( $check_sku ) {
						$sku_product = wc_get_product( $check_sku );
						$sku_product->set_sku( $product_variation['product_sku'] . '_old' );
						$sku_product->save();
					}
					$variation->set_sku( $product_variation['product_sku'] );
					$variation->save();
				}
				$product->set_default_attributes( array( 'size' => (int) $product_data[0]['product_variation_name'] . ' כומסות' ) );
			}
		} else {
			$product = wc_get_product( $product_exists );
		}

		return $product->get_id();

	}

	/**
	 * [get_product_by_sku description]
	 *
	 * @param  [type] $sku
	 * @return [type]      [description]
	 */
	private function get_product_by_sku( $sku ) {
		$args = array(
			'post_type'      => 'product',
			'meta_key'       => '_sku',
			'meta_value'     => $sku,
			'post_status'    => 'any',
			'posts_per_page' => 1,
		);

		$query = new WP_Query( $args );

		$id = false;

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id = get_the_ID();
			}
			wp_reset_postdata();
		}

		return $id;
	}

	/**
	 * Upload_products_from_arr description]
	 */
	public function upload_products_from_arr( $product_item = array() ) {

		if ( $product_item ) {
			$product_id = $this->get_product_by_sku( trim( $product_item['product_sku'] ) );

			if ( ! $product_id ) {

				$args = array(
					'post_content' => '',
					'post_title'   => wp_strip_all_tags( $product_item['product_title'] ),
					'post_type'    => 'product',
				);

				// Create Post.
				$post_id = wp_insert_post( $args );

				$this_product = new WC_Product( $post_id );

				$this_product->set_regular_price( trim( $product_item['product_price'] ) );
				$this_product->set_price( trim( $product_item['product_price'] ) );
				$this_product->set_sku( trim( $product_item['product_sku'] ) );

			} else {
				$this_product = new WC_Product( $product_id );

				$this_product->set_name( wp_strip_all_tags( $product_item['product_title'] ) );
				$this_product->set_regular_price( trim( $product_item['product_price'] ) );
				$this_product->set_price( trim( $product_item['product_price'] ) );
			}

			$date_time = new WC_DateTime();
			$this_product->set_date_created( $date_time );
			$this_product->save();
		} else {
			foreach ( $this->product_file_arr as $key => $product ) {

				$product_id = $this->get_product_by_sku( trim( $product['sku'] ) );

				if ( ! $product_id ) {

					$args = array(
						'post_content' => '',
						'post_title'   => wp_strip_all_tags( $product['title'] ),
						'post_type'    => 'product',
					);

					// Create Post.
					$post_id = wp_insert_post( $args );

					$this_product = new WC_Product( $post_id );

					$this_product->set_regular_price( trim( $product['price'] ) );
					$this_product->set_price( trim( $product['price'] ) );
					$this_product->set_sku( trim( $product['sku'] ) );

				} else {
					$this_product = new WC_Product( $product_id );

					$this_product->set_name( wp_strip_all_tags( $product['title'] ) );
					$this_product->set_regular_price( trim( $product['price'] ) );
					$this_product->set_price( trim( $product['price'] ) );
				}

				$date_time = new WC_DateTime();
				$this_product->set_date_created( $date_time );
				$this_product->save();

			}

			$this->product_file_arr = array();
		}

	}
}

$product_importer = new Product_importer();
