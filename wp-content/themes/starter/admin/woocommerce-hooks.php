<?php
/**
 * WooCommerce hooks
 *
 * @package WordPress
 */

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

// Remove woocmerce styles.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
add_filter( 'woocommerce_default_catalog_orderby', 'default_catalog_orderby' );
add_filter( 'woocommerce_registration_redirect', 'wc_registration_redirect' );
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

add_action( 'after_setup_theme', 'add_woocommerce_support' );
add_action( 'after_setup_theme', 'wc_remove_theme_support', 100 );
add_action( 'init', 'my_init' );
add_action( 'woocommerce_sale_flash', 'sale_badge_percentage', 25 );
// add_filter( 'wp_authenticate_user', 'wp_authenticate_user', 10, 2 );
add_action( 'user_register', 'my_user_register', 10, 2 );
add_action( 'woocommerce_thankyou', 'adding_customers_details_to_thankyou', 10, 1 );

// Loop item changes.
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open' );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close' );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );

// Checkout page.
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
add_action( 'woocommerce_checkout_after_customer_details', 'woocommerce_checkout_payment', 20 );
add_action( 'woocommerce_before_shipping', 'woocommerce_checkout_login_form', 10 );

add_filter( 'woocommerce_form_field', 'my_woocommerce_form_field' );

add_action( 'woocommerce_proceed_to_checkout', 'check_proceed_to_checkout_data', 20 );
function check_proceed_to_checkout_data() {
	print_r( $_POST );
	die();
}

/**
 * Default_catalog_orderby
 *
 * @param  string $sort_by  sort string.
 * @return string          sort string
 */
function default_catalog_orderby( $sort_by ) {
	return 'date';
}

function wc_remove_theme_support() {
	remove_theme_support( 'wc-product-gallery-zoom' );
	remove_theme_support( 'wc-product-gallery-lightbox' );
	remove_theme_support( 'wc-product-gallery-slider' );
}
/**
 * Add woocommerce_support
 */
function add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}

// add_filter( 'woocommerce_page_title', 'woo_shop_page_title' );
function woo_shop_page_title( $page_title ) {
	if ( 'חנות' == $page_title ) {
		return 'מוצרי סופהרב';
	}
}

/**
 * Change number of products that are displayed per page (shop page)
 */
function new_loop_shop_per_page( $cols ) {

	if ( is_product_category() || is_shop() ) {
		$cols = 12;
	} else {
		$cols = 8;
	}

	return $cols;
}
/**
 * Sale_badge_percentage description]
 *
 * @param  boolean $is_account               [description]
 * @return [type]              [description]
 */
function sale_badge_percentage( $is_account = false ) {
	global $product;
	if ( ! $product->is_on_sale() ) {
		return;
	}
	if ( $product->is_type( 'simple' ) ) {
		$max_percentage = ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100;
	}

	if ( isset( $max_percentage ) && $max_percentage > 0 ) {

		$class = 'AjxPrdSaleTag';

		if ( $is_account ) {
			$class = 'dpglItemPricesCoupon1 bold500';

		} else {
			if ( ! is_product() ) {
				$class = 'hppItemCoupon1';
			}
		}

		echo '<div class="' . $class . '">' . round( $max_percentage ) . '% הנחה</div>';

	}
}

/**
 * Override loop template and show quantities next to add to cart buttons
 */
add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
	if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
		$html  = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart hpProductItemForm" method="post" enctype="multipart/form-data">';
		$html .= woocommerce_quantity_input( array(), $product, false );
		$html .= '<input type="hidden" name="product_id" value="' . $product->get_id() . '"/>';
		$html .= '<button type="submit" class="button alt greenBtn">' . esc_html( $product->add_to_cart_text() ) . '</button>';
		$html .= '</form>';
	}
	return $html;
}

add_action( 'woocommerce_single_product_summary', 'add_buttons_after_add_to_cart', 30 );

if ( ! function_exists( 'wc_add_custom_loop_btn' ) ) {
	function wc_add_custom_loop_btn() {
		echo '<a href="' . get_the_permalink() . '" class="greenBtn" rel="nofollow">' . __( 'ADD TO CART', 'supherb' ) . '</a>';
	}
}

function add_buttons_after_add_to_cart() {
	get_template_part( 'partials/product/product', 'buttons' );
}

// Cart page.
function woocommerce_button_proceed_to_checkout() {
	$checkout_url = wc_get_checkout_url();
	?>
	<a href="<?php echo $checkout_url; ?>" class="greenBtn checkoutBtn checkout-button button alt wc-forward">
		<?php _e( 'המשך לתשלום', 'supherb' ); ?>
	</a>
	<?php
}

function my_woocommerce_form_field( $field ) {
	return preg_replace(
		'#<p class="form-row (.*?)"(.*?)>(.*?)<span class="woocommerce-input-wrapper">(.*?)</span></p>#',
		'<span class="form-row wpcf7-form-control-wrap $1"$2>$3 $4</span>',
		$field
	);
}



// WooCommerce Checkout Fields Hook
add_filter( 'woocommerce_checkout_fields', 'custom_wc_checkout_fields_no_label' );

// Our hooked in function - $fields is passed via the filter!
// Action: remove label from $fields
function custom_wc_checkout_fields_no_label( $fields ) {
	// loop by category.
	foreach ( $fields as $category => $value ) {
		// loop by fields.
		foreach ( $value as $field => $property ) {
			// remove label property
			$fields[ $category ][ $field ]['placeholder'] = $fields[ $category ][ $field ]['label'];
			unset( $fields[ $category ][ $field ]['label'] );
		}
	}
	return $fields;
}

add_filter( 'woocommerce_cart_shipping_method_full_label', 'change_cart_shipping_method_full_label', 10, 2 );
function change_cart_shipping_method_full_label( $label, $method ) {
	$has_cost = 0 < $method->cost;
	$price    = get_woocommerce_currency_symbol() . $method->cost;
	if ( ! $has_cost ) {
		$price = __( 'חינם', 'supherb' );
	}
	$label = '<span class="radioText"><span class="radioTextTitle bold700green">' . $method->label . '<span class="radioPrice">' . $price . '</span></span>';

	return $label;
}
add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );

add_filter( 'woocommerce_order_button_text', 'custom_button_text' );

function custom_button_text( $button_text ) {
	return __( 'שליחת הזמנה והזנת פרטי תשלום', 'supherb' );
}

/**
 * Override_checkout_fields
 *
 * @param  array $fields checkout fields array.
 * @return array  $fields
 */
function custom_override_checkout_fields( $fields ) {
	unset( $fields['order']['order_comments'] );
	unset( $fields['billing']['billing_company'] );

	// billing custom fields.
	$fields['billing']['billing_building_number'] = array(
		'label'       => __( 'מספר בית', 'woocommerce' ),
		'label_class' => array( 'screen-reader-text' ),
		'placeholder' => _x( 'מספר בית', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'class'       => array( 'form-row-wide' ),
		'clear'       => false,
		'priority'    => 20,
	);

	$fields['billing']['billing_app_number'] = array(
		'label'       => __( 'מספר דירה', 'woocommerce' ),
		'label_class' => array( 'screen-reader-text' ),
		'placeholder' => _x( 'מספר דירה', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'class'       => array( 'form-row-wide' ),
		'clear'       => false,
		'priority'    => 20,
	);

	$fields['billing']['billing_floor_number'] = array(
		'label'       => __( 'מספר קומה', 'woocommerce' ),
		'label_class' => array( 'screen-reader-text' ),
		'placeholder' => _x( 'מספר קומה', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'class'       => array( 'form-row-wide' ),
		'clear'       => false,
		'priority'    => 21,
	);

	// shipping custom fields.
	$fields['shipping']['shipping_building_number'] = array(
		'label'       => __( 'מספר בית', 'woocommerce' ),
		'label_class' => array( 'screen-reader-text' ),
		'placeholder' => _x( 'מספר בית', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'class'       => array( 'form-row-wide' ),
		'clear'       => false,
		'priority'    => 20,
	);

	$fields['shipping']['shipping_app_number'] = array(
		'label'       => __( 'מספר דירה', 'woocommerce' ),
		'label_class' => array( 'screen-reader-text' ),
		'placeholder' => _x( 'מספר דירה', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'class'       => array( 'form-row-wide' ),
		'clear'       => false,
		'priority'    => 21,
	);

	$fields['shipping']['shipping_floor_number'] = array(
		'label'       => __( 'מספר קומה', 'woocommerce' ),
		'label_class' => array( 'screen-reader-text' ),
		'placeholder' => _x( 'מספר קומה', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'class'       => array( 'form-row-wide' ),
		'clear'       => false,
		'priority'    => 22,
	);

	$fields['shipping']['shipping_phone_number'] = array(
		'label'       => __( 'מספר טלפון', 'woocommerce' ),
		'label_class' => array( 'screen-reader-text' ),
		'placeholder' => _x( 'מספר טלפון', 'placeholder', 'woocommerce' ),
		'required'    => false,
		'class'       => array( 'form-row-wide' ),
		'clear'       => false,
		'priority'    => 23,
	);

	$fields['billing']['billing_address_1']['priority']  = 20;
	$fields['billing']['billing_address_1']['maxlength'] = 30;

	$fields['billing']['billing_email']['maxlength'] = 48;

	$fields['billing']['billing_city']['maxlength']              = 40;
	$fields['billing']['billing_postcode']['maxlength']          = 10;
	$fields['billing']['billing_phone']['maxlength']             = 20;
	$fields['shipping']['shipping_phone_number']['maxlength']    = 20;
	$fields['shipping']['shipping_building_number']['maxlength'] = 20;
	$fields['billing']['billing_floor_number']['maxlength']      = 20;

	$fields['shipping']['shipping_address_1']['priority'] = 20;

	return $fields;
}


add_action( 'woocommerce_checkout_update_order_meta', 'supherb_custom_checkout_field_update_order_meta' );
/**
 * Update the order meta with field value
 *
 * @param string $order_id WC_Order ID.
 */
function supherb_custom_checkout_field_update_order_meta( $order_id ) {

	// Acceptance to send Active trail lead.
	if ( isset( $_POST['acceptance-718'] ) && 'on' === $_POST['acceptance-718'] ) {
		update_post_meta( $order_id, 'active_trail_lead', 'אני מאשר קבלת מסרים שיווקיים לכתובת דואר אלקטרוני ו/או SMS מטעם חברת אמברוזיה סופהרב בע"מ' );
	}
	// billing custom fields.
	if ( ! empty( $_POST['billing_building_number'] ) ) {	//phpcs:ignore
		update_post_meta( $order_id, 'billing_building_number', sanitize_text_field( $_POST['billing_building_number'] ) );
	}
	if ( ! empty( $_POST['billing_app_number'] ) ) {	//phpcs:ignore
		update_post_meta( $order_id, 'billing_app_number', sanitize_text_field( $_POST['billing_app_number'] ) );
	}
	if ( ! empty( $_POST['billing_floor_number'] ) ) {	//phpcs:ignore
		update_post_meta( $order_id, 'billing_floor_number', sanitize_text_field( $_POST['billing_floor_number'] ) );
	}
	// shipping custom fields.
	if ( isset( $_POST['ship_to_different_address'] ) ) {

		update_post_meta( $order_id, 'custom_ship_to_different_address', true );

		if ( ! empty( $_POST['shipping_building_number'] ) ) {	//phpcs:ignore
			update_post_meta( $order_id, 'shipping_building_number', sanitize_text_field( $_POST['shipping_building_number'] ) );
		}
		if ( ! empty( $_POST['shipping_app_number'] ) ) {	//phpcs:ignore
			update_post_meta( $order_id, 'shipping_app_number', sanitize_text_field( $_POST['shipping_app_number'] ) );
		}
		if ( ! empty( $_POST['shipping_floor_number'] ) ) {	//phpcs:ignore
			update_post_meta( $order_id, 'shipping_floor_number', sanitize_text_field( $_POST['shipping_floor_number'] ) );
		}
		if ( ! empty( $_POST['shipping_phone_number'] ) ) {	//phpcs:ignore
			update_post_meta( $order_id, 'shipping_phone_number', sanitize_text_field( $_POST['shipping_phone_number'] ) );
		}
	}
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

/**
 * Checkout_field_display_admin_order_meta
 *
 * @param  object $order WC_Order class.
 */
function my_custom_checkout_field_display_admin_order_meta( $order ) {

	$billing_building_number = get_post_meta( $order->get_id(), 'billing_building_number', true );
	$billing_app_number      = get_post_meta( $order->get_id(), 'billing_app_number', true );
	$billing_floor_number    = get_post_meta( $order->get_id(), 'billing_floor_number', true );

	$shipping_building_number = get_post_meta( $order->get_id(), 'shipping_building_number', true );
	$shipping_app_number      = get_post_meta( $order->get_id(), 'shipping_app_number', true );
	$shipping_floor_number    = get_post_meta( $order->get_id(), 'shipping_floor_number', true );
	$shipping_phone_number    = get_post_meta( $order->get_id(), 'shipping_phone_number', true );

	$custom_ship_to_different_address = get_post_meta( $order->get_id(), 'custom_ship_to_different_address', true );

	$active_trail_lead = get_post_meta( $order->get_id(), 'active_trail_lead', true );

	if ( $billing_building_number ) {
		echo '<p><strong>מספר בית:</strong> ' . esc_html( $billing_building_number ) . '</p>';
	}
	if ( $billing_app_number ) {
		echo '<p><strong>מספר דירה:</strong> ' . esc_html( $billing_app_number ) . '</p>';
	}
	if ( $billing_floor_number ) {
		echo '<p><strong>מספר קומה:</strong> ' . esc_html( $billing_floor_number ) . '</p>';
	}

	if ( $shipping_building_number ) {
		echo '<p><strong>מספר בית:</strong> ' . esc_html( $shipping_building_number ) . '</p>';
	}
	if ( $shipping_app_number ) {
		echo '<p><strong>מספר דירה:</strong> ' . esc_html( $shipping_app_number ) . '</p>';
	}
	if ( $shipping_floor_number ) {
		echo '<p><strong>מספר קומה:</strong> ' . esc_html( $shipping_floor_number ) . '</p>';
	}
	if ( $shipping_phone_number ) {
		echo '<p><strong>מספר טלפון:</strong> ' . esc_html( $shipping_phone_number ) . '</p>';
	}
	if ( $active_trail_lead ) {
		echo '<p><strong>אישור קבלת מסרים:</strong> ' . esc_html( $active_trail_lead ) . '</p>';
	}
	if ( $custom_ship_to_different_address ) {
		echo '<p><strong>משלוח לכתובת אחרת:</strong> כן</p>';
	} else {
		echo '<p><strong>משלוח לכתובת אחרת:</strong> לא</p>';
	}
}

// Single product page.
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

// Sale flash.
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'after_single_price', 'woocommerce_show_product_sale_flash', 10 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

// Single meta.
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 1 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 5 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );


// Remove Stock html.
add_filter( 'woocommerce_get_stock_html', '__return_empty_string', 10, 2 );

// Related products.
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
add_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products', 20 );
add_filter( 'woocommerce_product_description_heading', '__return_null' );

add_filter( 'woocommerce_get_price_html', 'price_html', 100, 2 );
function price_html( $price, $product ) {

	if ( $product->is_on_sale() ) {
		return '<div class="AjxPrdOldPrice">' . str_replace( '<ins>', '</div><div class="AjxPrdNewPrice">', $price ) . '</div>';
	} else {
		return '<div class="AjxPrdNewPrice">' . $price . '</div>';
	}
}

add_action( 'woocommerce_after_add_to_cart_button', 'add_wishlist_btn' );

function add_wishlist_btn() {

	?>
	<a href="#" class="chat-btn greenBtn bordered">
		<svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M8 8C10.21 8 12 6.21 12 4C12 1.79 10.21 0 8 0C5.79 0 4 1.79 4 4C4 6.21 5.79 8 8 8ZM8 2C9.1 2 10 2.9 10 4C10 5.1 9.1 6 8 6C6.9 6 6 5.1 6 4C6 2.9 6.9 2 8 2Z" fill="#35563C"></path>
			<path d="M2 14C2.22 13.28 5.31 12 8 12C8 11.3 8.13 10.63 8.35 10.01C5.62 9.91002 0 11.27 0 14V16H9.54C9.02 15.42 8.61 14.75 8.35 14H2Z" fill="#35563C"></path>
			<path d="M17.43 14.02C17.79 13.43 18 12.74 18 12C18 9.79 16.21 8 14 8C11.79 8 10 9.79 10 12C10 14.21 11.79 16 14 16C14.74 16 15.43 15.78 16.02 15.43C16.95 16.36 17.64 17.05 18.59 18L20 16.59C18.5 15.09 19.21 15.79 17.43 14.02ZM14 14C12.9 14 12 13.1 12 12C12 10.9 12.9 10 14 10C15.1 10 16 10.9 16 12C16 13.1 15.1 14 14 14Z" fill="#35563C"></path>
		</svg>
		<?php esc_html_e( 'ייעוץ מומחה', 'supherb' ); ?>
	</a>


	<?php
	echo do_shortcode( '[ti_wishlists_addtowishlist]' );
}
// Single products tabs.
add_filter( 'woocommerce_product_tabs', 'wc_new_product_tabs' );
function wc_new_product_tabs( $tabs ) {

	unset( $tabs['additional_information'] );
	$tabs['description']['title'] = __( 'תיאור מוצר', 'supherb' );

	if ( have_rows( 'facts' ) ) {
		$tabs['facts'] = array(
			'title'    => __( 'עובדות נוספות', 'supherb' ),
			'priority' => 15,
			'callback' => 'render_facts_tab',
		);
	}

	if ( get_field( 'main_use' ) ) {
		$tabs['main_use'] = array(
			'title'    => __( 'Main uses', 'supherb' ),
			'priority' => 10,
			'callback' => 'render_uses_tab',
		);
	}

	if ( get_field( 'advantages' ) ) {
		$tabs['adv'] = array(
			'title'    => __( 'יתרונות', 'supherb' ),
			'priority' => 20,
			'callback' => 'render_adv_tab',
		);
	}

	if ( get_field( 'usage' ) ) {
		$tabs['usage'] = array(
			'title'    => __( 'הנחיות שימוש', 'supherb' ),
			'priority' => 20,
			'callback' => 'render_usage_tab',
		);
	}
	return $tabs;

}

function render_uses_tab() {
	the_field( 'main_use' );

}
function render_adv_tab() {
	the_field( 'advantages' );
}

function render_usage_tab() {
	the_field( 'usage' );
}


function render_facts_tab() {
	?>
	<table cellpadding="0" cellspacing="0" border="0" class="table">
		<thead class="hideOnMobile">
			<tr>
				<th>
					<?php esc_html_e( 'Component', 'supherb' ); ?>
				</th>
				<th>
					<?php esc_html_e( 'Amount Per Serving', 'supherb' ); ?>
				</th>
				<th>
					<?php esc_html_e( 'Daily Value %', 'supherb' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			while ( have_rows( 'facts' ) ) :
					the_row();
				?>
				<tr>
					<td>
						<div class="showOnMobile mobileTableTitles">
							<?php esc_html_e( 'Component', 'supherb' ); ?>
						</div>
						<?php the_sub_field( 'item' ); ?>
					</td>
					<td>
						<div class="showOnMobile mobileTableTitles">
							<?php esc_html_e( 'Amount Per Serving', 'supherb' ); ?>
						</div>
						<?php the_sub_field( 'quantity' ); ?>
					</td>
					<td>
						<div class="showOnMobile mobileTableTitles">
							<?php esc_html_e( 'Daily Value %', 'supherb' ); ?>
						</div>
						<?php the_sub_field( 'value' ); ?>
					</td>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
	<?php

}


add_filter( 'woocommerce_add_to_cart_fragments', 'update_cart_counter' );
add_filter( 'woocommerce_add_to_cart_fragments', 'update_mini_cart' );


// Header cart.
function update_cart_counter( $fragments ) {
	ob_start();
	?>
	<div class="miniCartItemsCount">
		<?php echo WC()->cart->get_cart_contents_count(); ?>
	</div>

	<?php
	$fragments['div.miniCartItemsCount'] = ob_get_clean();

	return $fragments;

}

function update_mini_cart( $fragments ) {
	ob_start();
	?>
	<div class="cart-wrap">
		<?php woocommerce_mini_cart(); ?>
	</div>

	<?php
	$fragments['div.cart-wrap'] = ob_get_clean();

	return $fragments;

}

add_action(
	'woocommerce_widget_shopping_cart_buttons',
	function() {
		// Removing Buttons
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart', 10 );
		remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );

		// Adding customized Buttons
		add_action( 'woocommerce_widget_shopping_cart_buttons', 'custom_widget_shopping_cart_button_view_cart', 10 );
		add_action( 'woocommerce_widget_shopping_cart_buttons', 'custom_widget_shopping_cart_proceed_to_checkout', 20 );
	},
	1
);

/**
 * Custom cart button
 */
function custom_widget_shopping_cart_button_view_cart() {
	$original_link = wc_get_cart_url();
	echo '<a href="' . esc_url( $original_link ) . '" class="button wc-forward transparentBtn">' . esc_html__( 'עריכת העגלה', 'supherb' ) . '</a>';
}

/**
 * Custom Checkout button
 */
function custom_widget_shopping_cart_proceed_to_checkout() {
	$original_link = wc_get_checkout_url();
	echo '<a href="' . esc_url( $original_link ) . '" class="button checkout wc-forward greenBtn">' . esc_html__( 'רכישה', 'supherb' ) . '</a>';
}

// add_filter( 'woocommerce_widget_cart_item_quantity', 'add_minicart_quantity_fields', 10, 3 );
// function add_minicart_quantity_fields( $html, $cart_item, $cart_item_key ) {
// $product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $cart_item['data'] ), $cart_item, $cart_item_key );
// return woocommerce_quantity_input( array( 'input_value' => $cart_item['quantity'] ), $cart_item['data'], false );
// }


// My Account.
add_action( 'wp_logout', 'auto_redirect_after_logout' );

function auto_redirect_after_logout() {

	wp_redirect( home_url() );
	exit();

}
add_filter( 'woocommerce_my_account_my_orders_columns', 'edit_orders_table_cols' );
function edit_orders_table_cols( $columns ) {

	$columns['order-actions'] = esc_html__( 'פרטים', 'supherb' );
	$columns['order-number']  = esc_html__( 'הזמנה#', 'supherb' );
	$columns['order-date']    = esc_html__( 'תאריך הזמנה', 'supherb' );
	$columns['order-status']  = esc_html__( 'סטטוס הזמנה', 'supherb' );
	$columns['order-total']   = esc_html__( 'מחיר', 'supherb' );

	return $columns;
}
add_filter( 'woocommerce_account_menu_items', 'rename_myaccount_nav' );

function rename_myaccount_nav( $menu_links ) {

	$menu_links['orders']       = __( 'ההזמנות שלי', 'supherb' );
	$menu_links['dashboard']    = __( 'דשבורד', 'supherb' );
	$menu_links['edit-account'] = __( 'פרטי יצירת קשר', 'supherb' );
	$menu_links['edit-address'] = __( 'כתובות', 'supherb' );
	$new                        = array( 'my-products' => __( 'המוצרים שלי', 'supherb' ) );

	$menu_links = array_slice( $menu_links, 0, 1, true )
	+ $new
	+ array_slice( $menu_links, 1, null, true );
	return $menu_links;
}

add_action( 'init', 'add_custom_endpoint' );
function add_custom_endpoint() {

	add_rewrite_endpoint( 'my-products', EP_PAGES );

}

add_action( 'woocommerce_account_my-products_endpoint', 'custom_account_endpoint_content' );
function custom_account_endpoint_content() {
	wc_get_template( 'myaccount/my-products.php' );

}

// quantity buttons.
add_action( 'woocommerce_after_quantity_input_field', 'ts_quantity_minus_sign' );
add_action( 'woocommerce_before_quantity_input_field', 'ts_quantity_plus_sign' );
function ts_quantity_minus_sign() {
	echo '<button type="button" class="minus js_minusAmount"></button>';
}

function ts_quantity_plus_sign() {
	echo '<button type="button" class="plus js_plusAmount">+</button>';
}


// Registration.
// this is just to prevent the user log in automatically after register

function wc_registration_redirect( $redirect_to ) {

	wp_logout();

	wp_redirect( '/?q=' );

	exit;

}

// when user login, we will check whether this guy email is verify

function wp_authenticate_user( $userdata ) {

	$isActivated = get_user_meta( $userdata->ID, 'is_activated', true );

	if ( ! $isActivated ) {

		$userdata = new WP_Error(
			'activation',
			__(
				'החשבון שלך לא מאומת עדיין. לאימות <a href="' . get_site_url() . '?u=' . $userdata->ID . '">לחצו כאן</a>',
				'supherb'
			)
		);

	}
	return $userdata;

}

// when a user register we need to send them an email to verify their account

function my_user_register( $user_id ) {

	// get user data

	$user_info = get_userdata( $user_id );

	// create md5 code to verify later
	$code = md5( time() );

	// make it into a code to send it to user via email

	$string = array(
		'id'   => $user_id,
		'code' => $code,
	);

	// create the activation code and activation status

	update_user_meta( $user_id, 'is_activated', 0 );

	update_user_meta( $user_id, 'activationcode', $code ); // create the url

	$url = get_site_url() . '?activation=' . base64_encode( serialize( $string ) );

	// basically we will edit here to make this nicer

	$html  = __( 'לחצו על הלינק הבא לאימות החשבון:' ) . '<br>';
	$html .= '<a href="' . $url . '">' . __( 'לחצו כאן', 'supherb' ) . '</a>';

	// send an email out to user

	wc_mail(
		$user_info->user_email,
		__( 'אימות חשבון' ),
		$html
	);

}

// we need this to handle all the getty hacks i made

function my_init() {

	// check whether we get the activation message

	if ( isset( $_GET['p'] ) ) {

		$data = unserialize( base64_decode( $_GET['activation'] ) );

		$code = get_user_meta( $data['id'], 'activationcode', true );

		// check whether the code given is the same as ours

		if ( $code == $data['code'] ) {
			  // update the db on the activation process

			 update_user_meta( $data['id'], 'is_activated', 1 );

			wc_add_notice( __( '<strong>Success:</strong> Your account has been activated! ', 'supherb' ), 'activation' );

		} else {
			wc_add_notice(
				__( '<strong>Error:</strong>; Activation fails, please contact our administrator' ),
				'activation'
			);
		}
	}

	if ( isset( $_GET['q'] ) ) {

		wc_add_notice( __( '<strong>Error:</strong> Your account has to be activated before you can login. Please check your email.', 'inkfool' ) );

	}

	if ( isset( $_GET['u'] ) ) {

		my_user_register( $_GET['u'] );

		wc_add_notice( __( 'אימייל אימות נשלח בהצלחה לחשבון המייל שלכם!', 'supherb' ) );

	}

}

// hooks handler.
function adding_customers_details_to_thankyou( $order_id ) {
	// Only for non logged in users.
	if ( ! $order_id || is_user_logged_in() ) {
		return;
	}

	$order = wc_get_order( $order_id ); // Get an instance of the WC_Order object.
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}

add_action( 'init', 'register_bebizua_order_status' );
/**
 * Register new order status
 */
function register_bebizua_order_status() {
	register_post_status(
		'wc-bebizua',
		array(
			'label'                     => 'בביצוע',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'exclude_from_search'       => false,
			'label_count'               => _n_noop( 'בביצוע (%s)', 'בביצוע (%s)' ), //phpcs:ignore.
		)
	);
	register_post_status(
		'wc-buza',
		array(
			'label'                     => 'בוצעה',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true,
			'exclude_from_search'       => false,
			'label_count'               => _n_noop( 'בוצעה (%s)', 'בוצעה (%s)' ), //phpcs:ignore.
		)
	);
}

add_filter( 'wc_order_statuses', 'add_wait_call_to_order_statuses' );
/**
 * Add custom status to order status list
 *
 * @param array $order_statuses
 */
function add_wait_call_to_order_statuses( $order_statuses ) {
	$new_order_statuses = array();
	foreach ( $order_statuses as $key => $status ) {
		$new_order_statuses[ $key ] = $status;
		// if ( 'wc-on-hold' === $key ) {
		// $new_order_statuses['wc-bebizua'] = 'בביצוע';
		// }
	}
	$new_order_statuses['wc-bebizua'] = 'בביצוע';
	$new_order_statuses['wc-buza']    = 'בוצעה';
	return $new_order_statuses;
}
