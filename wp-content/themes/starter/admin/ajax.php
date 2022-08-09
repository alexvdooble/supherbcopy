<?php
/**
 * AJAX functions
 *
 * @package WordPress
 */

add_action( 'wp_ajax_nopriv_ajax_apply_coupon', 'ajax_apply_coupon' );
add_action( 'wp_ajax_ajax_apply_coupon', 'ajax_apply_coupon' );

add_action( 'wp_ajax_nopriv_ajax_quick_view', 'ajax_quick_view' );
add_action( 'wp_ajax_ajax_quick_view', 'ajax_quick_view' );

add_action( 'wp_ajax_nopriv_ajax_filter_products', 'ajax_filter_products' );
add_action( 'wp_ajax_ajax_filter_products', 'ajax_filter_products' );

add_action( 'wp_ajax_nopriv_ajax_filter_posts', 'ajax_filter_posts' );
add_action( 'wp_ajax_ajax_filter_posts', 'ajax_filter_posts' );

add_action( 'wp_ajax_nopriv_ajax_load_more', 'ajax_load_more' );
add_action( 'wp_ajax_ajax_load_more', 'ajax_load_more' );


add_action( 'wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart' );

add_action( 'wp_ajax_ajax_login', 'ajax_login' );
add_action( 'wp_ajax_nopriv_ajax_login', 'ajax_login' );

add_action( 'wp_ajax_ajax_register', 'ajax_register' );
add_action( 'wp_ajax_nopriv_ajax_register', 'ajax_register' );

add_action( 'wp_ajax_ajax_recommended_product', 'ajax_recommended_product' );
add_action( 'wp_ajax_nopriv_ajax_recommended_product', 'ajax_recommended_product' );

add_action( 'wp_ajax_ajax_mini_cart_edit_item_quantity', 'ajax_mini_cart_edit_item_quantity' );
add_action( 'wp_ajax_nopriv_ajax_mini_cart_edit_item_quantity', 'ajax_mini_cart_edit_item_quantity' );

/**
 * Ajax product quick view
 */
function ajax_quick_view() {
	$product_id = sanitize_text_field( $_POST['product'] );

	$results = array(
		'ok' => false,
	);

	if ( $product_id ) {

		$args = array(
			'post_type'      => 'product',
			'p'              => $product_id,
			'posts_per_page' => 1,
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			ob_start();
			while ( $query->have_posts() ) {
				$query->the_post();
				get_template_part( 'woocommerce/content', 'single-product', array( 'quick_view' => 1 ) );
			}

			wp_reset_postdata();

			$results = array(
				'ok'   => true,
				'html' => ob_get_clean(),
			);
		}
	}

	wp_send_json( $results );
}

function ajax_mini_cart_edit_item_quantity() {
	global $woocommerce;

	$item_key = sanitize_text_field( $_POST['item_key'] );
	$amount   = sanitize_text_field( $_POST['amount'] );

	if ( $item_key && $amount ) {
		$woocommerce->cart->set_quantity( $item_key, $amount );
	}

	WC_AJAX::get_refreshed_fragments();

	wp_die();
}
/**
 * AJAX recommended_products
 */
function ajax_recommended_product() {

	$add                       = isset( $_POST['add'] ) ? rest_sanitize_boolean( $_POST['add'] ) : '';
	$product_id                = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';
	$user_id                   = get_current_user_id();
	$user_recommended_products = get_user_meta( $user_id, 'sup_recommended', true );

	if ( $add ) {

		if ( empty( $user_recommended_products ) ) {
			$user_meta = update_user_meta( $user_id, 'sup_recommended', array( $product_id ), true );
		} else {
			$user_recommended_products[] = $product_id;

			$user_meta = update_user_meta( $user_id, 'sup_recommended', array_unique( $user_recommended_products ) );

		}
	} else {
		if ( ( $key = array_search( $product_id, $user_recommended_products ) ) !== false ) {
			unset( $user_recommended_products[ $key ] );
			update_user_meta( $user_id, 'sup_recommended', array_unique( $user_recommended_products ) );
		}
	}

	$results = array(
		'ok'      => true,
		'producs' => get_user_meta( $user_id, 'sup_recommended', true ),
	);
	wp_send_json( $results );
}

function ajax_login() {

	parse_str( $_POST['args'], $data );

	if ( is_email( $data['username'] ) ) {
		$info['user_login'] = get_user_by( 'email', $data['username'] );
		$info['user_login'] = $info['user_login']->user_login;
	} else {
		$info['user_login'] = sanitize_text_field( $data['username'] );
	}
	$info['user_password'] = sanitize_text_field( $data['password'] );
	$info['remember']      = $data['remember'] ? true : false;

	$user_signon = wp_signon( $info, false );

	if ( is_wp_error( $user_signon ) ) {

		if ( 'activation' === $user_signon->get_error_message() ) {
			$error = $user_signon->get_error_message();
		} else {
			$error = __( 'שם משתמש או סיסמא לא נכונים', 'supherb' );
		}
		$results = array(
			'status' => 'error',
			'error'  => $error,
		);
	} else {
		$results = array(
			'status'  => 'success',
			'message' => __( 'התחברת בהצלחה', 'ystheme' ),
		);
	}

	wp_send_json( $results );
}


function ajax_register() {

	$step = sanitize_text_field( $_POST['step'] );

	parse_str( $_POST['args'], $data );

	$response = array(
		'ok' => false,
	);

	if ( 'step-2' === $step ) {
		$user_id = sanitize_text_field( $data['user_id'] );
		unset( $data['user_id'] );
		foreach ( $data as $key => $value ) {
			update_user_meta( $user_id, $key, $value );
		}

		$userdata = get_userdata( $user_id );

		$response = array(
			'ok'    => true,
			'email' => $userdata->user_email,
		);

	} else {
		$email    = sanitize_email( $data['email'] );
		$fname    = sanitize_text_field( $data['name'] );
		$lname    = sanitize_text_field( $data['lname'] );
		$password = sanitize_text_field( $data['password'] );

		$user_id = wp_create_user( $email, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			$response['message'] = $user_id->get_error_message();
		} else {

			update_user_meta( $user_id, 'billing_first_name', $fname );
			update_user_meta( $user_id, 'first_name', $fname );
			update_user_meta( $user_id, 'billing_last_name', $lname );
			update_user_meta( $user_id, 'last_name', $lname );

			$response = array(
				'ok'         => true,
				'user_email' => $email,
				'user_id'    => $user_id,
			);
		}
	}

	wp_send_json( $response );
}

function woocommerce_ajax_add_to_cart() {

	$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
	$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( $_POST['quantity'] );
	$variation_id      = absint( $_POST['variation_id'] );
	$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
	$product_status    = get_post_status( $product_id );

	if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {

		do_action( 'woocommerce_ajax_added_to_cart', $product_id );

		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
			wc_add_to_cart_message( array( $product_id => $quantity ), true );
		}

		WC_AJAX::get_refreshed_fragments();
	} else {

		$data = array(
			'error'       => true,
			'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
		);

		echo wp_send_json( $data );
	}

			wp_die();
}
/**
 * [ajax_apply_coupon description]
 *
 * @return [type] [description]
 */
function ajax_apply_coupon() {
	$coupon_code = sanitize_text_field( $_POST['coupon'] );

	$results = array(
		'ok' => false,
	);

	if ( $coupon_code ) {
		$current_coupon = WC()->cart->get_coupons();
		if ( empty( $current_coupon ) ) {
			WC()->cart->apply_coupon( $coupon_code );

			$results = array(
				'ok'      => true,
				'message' => __( 'קופון התווסף בהצלחה' ),

			);

		} elseif ( isset( $current_coupon[ strtolower( $coupon_code ) ] ) ) {

			$results = array(
				'ok'      => true,
				'message' => __( 'קופון קיים כבר בהזמנה שלך' ),

			);
		} else {
			$results = array(
				'ok'      => false,
				'message' => __( 'שגיאה, אנא נסו שוב מאוחר יותר' ),

			);
		}
	}

	wp_send_json( $results );
}
/**
 * Ajax load more
 */
function ajax_load_more() {
	$args = $_POST['args'];

	$offset                 = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : wp_send_json( 'Invalid offset param' );
	$load                   = isset( $_POST['load'] ) && $_POST['load'] < 30 ? intval( $_POST['load'] ) : wp_send_json( 'Invalid load param' );
	$template               = ! empty( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : 'loop-' . $args['post_type'];
	$search_query           = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
	$args['post_status']    = 'publish';
	$args['offset']         = $offset;
	$args['posts_per_page'] = $load;

	if ( 'post-archive' == $template ) {
		$args = get_selected_terms( 'article-load', $args );
	}

	if ( 'product' == $template ) {
		$lifestyles = $_POST['lifestyles'];
		$formats    = $_POST['formats'];

		$p_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $load,
			'offset'         => $offset,
			'tax_query'      => array(
				'relation' => 'AND',
			),
		);

		if ( $args['category'] && 'product' !== $args['category']['name'] ) {
			$p_args['tax_query'][] = array(
				array(
					'taxonomy' => $args['category']['taxonomy'],
					'terms'    => $args['category']['term_id'],
				),
			);
		}

		if ( $lifestyles ) {
			$p_args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_tag',
					'terms'    => $lifestyles,
				),
			);
		}

		if ( $formats ) {
			$p_args['tax_query'][] = array(
				array(
					'taxonomy' => 'pa_format',
					'terms'    => $formats,
				),
			);
		}

		$args = $p_args;

	}

	if ( $load > 30 ) {
		wp_send_json( 'Too many posts per load' );
	}
	// print_r( $args );

	ob_start();

	$query = new WP_Query( $args );

	$total = $query->found_posts;

	if ( 1 === $total ) {
		$result_count = __( 'Showing the single result', 'woocommerce' );

	} else {
		$result_count = sprintf( __( 'מציג  %1$d מתוך %2$d תוצאות', 'supherb' ), $query->post_count + $offset, $total );

	}
	while ( $query->have_posts() ) {
		$query->the_post();

		if ( 'product' == $template ) {
			?>
			<div class="productItem">
				<?php wc_get_template_part( 'content', 'product' ); ?>
			</div>
			<?php
		} else {
			get_template_part( 'partials/loop', $template, array( 'search_query' => $search_query ) );

		}
	}

	wp_reset_query();

	$results = array(
		'html'         => ob_get_clean(),
		'more'         => $offset + $load < $total ? true : false,
		'total'        => $total,
		'count'        => $query->post_count,
		'result_count' => $result_count,
	);

	wp_send_json( $results );
}

function get_selected_terms( $type, $args ) {

	if ( 'article' === $type ) {
		$types      = $_POST['types'];
		$categories = $_POST['categories'];
	} else {
		$types      = $_POST['article_types'];
		$categories = $_POST['article_categories'];
	}

	$types_query      = array();
	$categories_query = array();

	if ( $types ) {
		$types_query = array(
			array(
				'taxonomy' => 'custom_type',
				'terms'    => $types,
			),
		);

	}

	if ( $categories ) {
		$categories_query = array(
			array(
				'taxonomy' => 'category',
				'terms'    => $categories,
			),
		);

	}

	// $args = array(
	// 'post_type'      => 'post',
	// 'posts_per_page' => 12,
	// 'offset'         => $offset,
	//
	// );

	if ( $categories || $types ) {
		$args['tax_query'] = array(
			'relation' => 'OR',
			$types_query,
			$categories_query,

		);
	}

	return $args;
}

function ajax_filter_posts() {

	$results = array(
		'ok' => false,
	);

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
	);

	$args = get_selected_terms( 'article', $args );

	// print_r( $args );
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {

		ob_start();
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'partials/loop', 'post-archive' );

		}

		wp_reset_postdata();

		$results = array(
			'ok'    => true,
			'html'  => ob_get_clean(),
			'total' => $query->found_posts,
			'more'  => 8 < $query->found_posts ? true : false,

		);
	}

	wp_send_json( $results );
}


function ajax_filter_products() {

	$results = array(
		'ok' => false,
	);

	$atts       = $_POST['atts'];
	$category   = (object) $atts['category'];
	$per_page   = (int) $atts['per_page'];
	$paged      = (int) $atts['paged'];
	$type       = $atts['type'];
	$lifestyles = $_POST['lifestyles'];
	$formats    = $_POST['formats'];
	$banner     = get_field( 'category_ad', $category );

	$args = array(
		'post_status'    => 'publish',
		'post_type'      => 'product',
		'posts_per_page' => $per_page,
		'tax_query'      => array(
			'relation' => 'AND',
		),
	);

	if ( $category && $category->term_id ) {

		if ( 'tag' == $type ) {
			$args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_tag',
					'terms'    => $category->term_id,
				),
			);
		} else {
			$args['tax_query'][] = array(
				array(
					'taxonomy' => 'product_cat',
					'terms'    => $category->term_id,
				),
			);
		}
	}

	if ( $lifestyles ) {
		$args['tax_query'][] = array(
			array(
				'taxonomy' => 'product_tag',
				'terms'    => $lifestyles,
			),
		);
	}

	if ( $formats ) {
		$args['tax_query'][] = array(
			array(
				'taxonomy' => 'pa_format',
				'terms'    => $formats,
			),
		);
	}

	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {
		$key = 1;
		ob_start();
		while ( $query->have_posts() ) {
			$query->the_post();
			$key++;
			?>
			<div class="productItem">
				<?php wc_get_template_part( 'content', 'product' ); ?>
			</div>
			<?php if ( 3 == $key && $banner ) : ?>
				<div class="productItem bannerItem">
					<div class="bannerItemInner">
						<?php
							echo wp_get_attachment_image( get_field( 'category_ad', $category ), 'full', false, array( 'class' => 'cardView hideOnMobile' ) );
							echo wp_get_attachment_image( get_field( 'category_ad_mobile', $category ), 'full', false, array( 'class' => 'cardView showOnMobile' ) );
							echo wp_get_attachment_image( get_field( 'category_ad_wide', $category ), 'full', false, array( 'class' => 'listView hideOnMobile' ) );
							echo wp_get_attachment_image( get_field( 'category_ad_wide_mobile', $category ), 'full', false, array( 'class' => 'listView showOnMobile' ) );
						?>
					</div>
				</div>

				<?php
				endif;
		}

		wp_reset_postdata();

		$results = array(
			'ok'    => true,
			'html'  => ob_get_clean(),
			'total' => $query->found_posts,
		);
	}

	wp_send_json( $results );
}
