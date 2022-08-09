<?php
/**
 * Theme functions and definitions.
 *
 * @package WordPress
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/*
 Disable WordPress Admin Bar for all users */
add_filter( 'show_admin_bar', '__return_false' );

/**
 * Defines
 */
define( 'THEME_URI', get_template_directory_uri() );    // <url>/wp-content/themes/starter
define( 'THEME_URL', get_template_directory() );        // /home/starter/public_html/wp-content/themes/starter
define( 'UPLOADS_PATH', wp_upload_dir()['basedir'] );        // /home/starter/public_html/wp-content/themes/starter
define( 'THEME_VER', wp_get_theme()->get( 'Version' ) );

/**
 * Includes
 */
get_template_part( 'classes/class', 'ftp' );
get_template_part( 'classes/class', 'h' );                      // Helper class.
get_template_part( 'classes/class', 'product-importer' );       // Product importer class.
get_template_part( 'admin/post-types' );
get_template_part( 'admin/ajax' );
get_template_part( 'admin/woocommerce-hooks' );                 // woocommerce.
get_template_part( 'admin/active-trail' );
get_template_part( 'admin/integration' );                       // remote FTP & Priority integration.
get_template_part( 'admin/cron' );
get_template_part( 'classes/class', 'mobile-menu-walker' );     // Mobile Menu Walker.

/**
 * Post Thumbnail Support
 */
function ystheme_theme_support() {

	// Add text domain.
	load_theme_textdomain( 'ystheme', THEME_URL . '/languages' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Add custom image sizes.
	add_image_size( 'post-thumbnail', 370, 250, true );
	add_image_size( 'header-image', 1900, 180, true );
	add_image_size( 'slider-hero', 1900, 500, true );
	add_image_size( 'slider-images', 500, 500, true );
	add_image_size( 'small', 200, 200, false );
}
add_action( 'after_setup_theme', 'ystheme_theme_support' );

/**
 * Enqueue Assets
 */
function ystheme_enqueue_assets() {
	// Assets - CSS.
	wp_enqueue_style( 'select2', THEME_URI . '/assets/css/select2.min.4.1.0.css', array(), THEME_VER );
	wp_enqueue_style( 'jquery-ui', THEME_URI . '/assets/css/jquery-ui.min.css', array(), THEME_VER );
	wp_enqueue_style( 'swiper', THEME_URI . '/assets/css/swiper-bundle.6.5.8.min.css', array(), THEME_VER );
	wp_enqueue_style( 'yBox', THEME_URI . '/assets/css/yBox.min.css', array(), THEME_VER );

	// Assets - JS.
	wp_enqueue_script( 'directive', THEME_URI . '/assets/js/directive.min.js', array(), THEME_VER, true );
	wp_enqueue_script( 'jquery-ui', THEME_URI . '/assets/js/jquery-ui.min.js', array(), THEME_VER, true );
	wp_enqueue_script( 'select2', THEME_URI . '/assets/js/select2.min.4.1.0.js', array(), THEME_VER, true );
	wp_enqueue_script( 'jquery-validate', THEME_URI . '/assets/js/jquery.validate.1.19.3.min.js', array(), THEME_VER, true );
	wp_enqueue_script( 'swiper', THEME_URI . '/assets/js/swiper-bundle.6.5.8.min.js', array(), THEME_VER, true );
	wp_enqueue_script( 'yBox', THEME_URI . '/assets/js/yBox.min.js', array(), THEME_VER, true );
}
add_action( 'wp_enqueue_scripts', 'ystheme_enqueue_assets' );

/**
 * Front-End Enqueue
 */
function ystheme_scripts_and_styles() {
	wp_enqueue_style( 'main-style', THEME_URI . '/build/css/style.css', array(), THEME_VER . '-' . time() );
	wp_enqueue_style( 'responsive', THEME_URI . '/build/css/responsive.css', array(), '2.1.0' );
	wp_enqueue_style( 'style-extra', THEME_URI . '/build/css/style-extra.css', array(), '2.1.0' );

	$google_maps_api_key = get_field( 'google_maps_api_key', 'option' );
	if ( $google_maps_api_key ) {
		wp_enqueue_script( 'google-maps', "https://maps.googleapis.com/maps/api/js?key={$google_maps_api_key}&sensor=false&language=he", array(), THEME_VER, true );
	}

	wp_enqueue_script( 'functions', THEME_URI . '/build/js/functions.js', array(), THEME_VER, true );
	wp_enqueue_script( 'scripts', THEME_URI . '/build/js/scripts.js', array(), THEME_VER, true );

}
add_action( 'wp_enqueue_scripts', 'ystheme_scripts_and_styles' );

/**
 * Localize Script
 */
function ystheme_localize_script() {
	$site_object = array(
		'ajaxurl'          => admin_url( 'admin-ajax.php' ),
		'homeurl'          => get_site_url(),
		'currentpagetitle' => wp_title( '|', false, 'right' ),
		'nonce'            => wp_create_nonce(),
		'themepath'        => get_stylesheet_directory_uri(),
	);

	wp_localize_script( 'scripts', 'siteObject', $site_object );
	wp_localize_script( 'functions', 'siteObject', $site_object );
	wp_localize_script( 'admin-js', 'siteObject', $site_object );
}
add_action( 'wp_enqueue_scripts', 'ystheme_localize_script' );

/**
 * Back-End Enqueue
 */
function ystheme_admin_enqueue_styles() {
	wp_enqueue_style( 'admin-style', THEME_URI . '/admin/admin.css', array(), THEME_VER );
}
add_action( 'admin_enqueue_scripts', 'ystheme_admin_enqueue_styles', 100 );

function ystheme_admin_enqueue_scripts() {
	wp_enqueue_script( 'admin-js', THEME_URI . '/admin/admin.js', array(), THEME_VER, false );
}
add_action( 'admin_enqueue_scripts', 'ystheme_admin_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'ystheme_localize_script' );

function themename_post_formats_setup() {
	add_theme_support( 'post-formats', array( 'video', 'audio' ) );
}
add_action( 'after_setup_theme', 'themename_post_formats_setup', 20 );
/**
 * Register Menus
 */
function ystheme_register_menus() {
	register_nav_menus(
		array(
			'main-menu'     => __( 'Main Menu', 'ystheme' ),
			'mobile-menu'   => __( 'Main Menu - Mobile', 'ystheme' ),
			'footer_bottom' => __( 'Footer Bottom', 'ystheme' ),
		)
	);
}
add_action( 'init', 'ystheme_register_menus' );

/**
 * Theme Options
 */
function ystheme_acf_init() {

	// Init options page.
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => __( 'Theme Options', 'ystheme' ),
				'menu_title' => __( 'Theme Options', 'ystheme' ),
				'menu_slug'  => 'theme-general-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
			)
		);
	}

	// Init Google Maps.
	acf_update_setting( 'google_api_key', get_field( 'google_maps_api_key', 'option' ) );
}
add_action( 'acf/init', 'ystheme_acf_init' );

/**
 * Add ACF Options to Admin Bar
 */
function ystheme_options_adminbar( $wp_admin_bar ) {
	if ( current_user_can( 'administrator' ) ) {
		$args = array(
			'id'    => 'theme_options',
			'title' => __( 'Theme Options', 'ystheme' ),
			'href'  => home_url() . '/wp-admin/admin.php?page=theme-general-settings',
		);
		$wp_admin_bar->add_node( $args );
	}
}
add_action( 'admin_bar_menu', 'ystheme_options_adminbar', 999 );


/**
 * Remove Comments From Admin Screen
 */
function ystheme_remove_menus() {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'ystheme_remove_menus' );

/**
 * Remove comments from admin bar
 */
function ys_remove_comments_admin_bar() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'comments' );
}
add_action( 'wp_before_admin_bar_render', 'ys_remove_comments_admin_bar' );

/**
 * Disable Gutenberg for pages
 */
function ystheme_disable_gutenberg() {
	// add_filter( 'use_block_editor_for_post', '__return_false', 10 );
}
add_action( 'current_screen', 'ystheme_disable_gutenberg' );

/**
 * Open head scripts
 */
function ystheme_wp_head_scripts() {
	the_field( 'scripts_head', 'option' );
}
add_action( 'wp_head', 'ystheme_wp_head_scripts' );

/**
 * Open body scripts
 */
function ystheme_wp_body_open_scripts() {
	the_field( 'scripts_open_body', 'option' );
}
add_action( 'wp_body_open', 'ystheme_wp_body_open_scripts' );

/**
 * Footer scripts
 */
function ystheme_wp_footer_scripts() {
	the_field( 'scripts_footer', 'option' );
}
add_action( 'wp_footer', 'ystheme_wp_footer_scripts', 20 );

/**
 * Body Class
 */
function ystheme_body_class( $classes ) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone, $is_edge;

	if ( is_tax() ) {
		$object = get_queried_object();

		if ( $object->parent ) {
			$classes[] = 'has_parent';
		} else {
			$classes[] = 'no_parent';
		}
	}
	if ( $is_lynx ) {
		$classes[] = 'lynx';
	} elseif ( $is_gecko ) {
		$classes[] = 'firefox';
	} elseif ( $is_opera ) {
		$classes[] = 'opera';
	} elseif ( $is_NS4 ) {
		$classes[] = 'ns4';
	} elseif ( $is_safari ) {
		$classes[] = 'safari';
	} elseif ( $is_chrome ) {
		$classes[] = 'chrome';
	} elseif ( $is_edge ) {
		$classes[] = 'edge';
	} elseif ( $is_IE ) {
		$classes[] = 'ie';
	} else {
		$classes[] = 'unknown';
	}

	if ( $is_iphone ) {
		$classes[] = 'iphone';
	}

	return $classes;
}
add_filter( 'body_class', 'ystheme_body_class' );

/**
 * Enable font size in the editor
 */
function ystheme_mce_buttons( $buttons ) {
	array_unshift( $buttons, 'fontsizeselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'ystheme_mce_buttons' );

/**
 * Customize font sizes in the editor
 */
function ystheme_mce_text_sizes( $init_array ) {
	$init_array['fontsize_formats'] = '1rem 1.2rem 1.3rem 1.4rem 1.6rem 1.8rem 2.0rem 2.2rem 2.4rem 2.6rem 2.8rem 3.0rem 3.2rem 3.4rem 3.6rem';
	return $init_array;
}
add_filter( 'tiny_mce_before_init', 'ystheme_mce_text_sizes' );

/**
 * Allow SVG Upload
 */
function ystheme_cc_mime_types( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['webp'] = 'image/webp';

	return $mimes;
}
add_filter( 'upload_mimes', 'ystheme_cc_mime_types' );




/**
 * Render block by device
 *
 * @param string $block_content The block content about to be appended.
 * @param array  $block The full block, including name and attributes.
 * @return void
 */
function render_block_by_device( $block_content, $block ) {
	$visibility = isset( $block['attrs']['data']['visibility'] ) ? $block['attrs']['data']['visibility'] : '';

	if ( ! $visibility ) {
		return $block_content;
	}

	if ( ( wp_is_mobile() && 'desktop' === $visibility ) || ! wp_is_mobile() && 'mobile' === $visibility ) {
		$block_content = '';
	}

	return $block_content;
}
add_filter( 'render_block', 'render_block_by_device', 10, 2 );


function get_crumb_array() {

	$crumb = array();

	// Get all preceding links before the current page
	$dom = new DOMDocument();
	$dom->loadHTML( mb_convert_encoding( yoast_breadcrumb( '', '', false ), 'HTML-ENTITIES', 'UTF-8' ) );
	$items = $dom->getElementsByTagName( 'a' );

	foreach ( $items as $tag ) {
		$crumb[] = array(
			'text' => $tag->nodeValue,
			'href' => $tag->getAttribute( 'href' ),
		);
	}

	// Get the current page text and href
	$items = new DOMXpath( $dom );
	$dom   = $items->query( '//*[contains(@class, "breadcrumb_last")]' );

	$crumb[] = array(
		'text' => $dom->item( 0 )->nodeValue,
	);
	return $crumb;
}

function crumb_nav( $crumb, $is_shop = false ) {
	$html = '';
	if ( $crumb ) {

		$items = count( $crumb ) - 1;

		$html = '<ul class="breadCrumbsList">';

		foreach ( $crumb as $k => $v ) {

			if ( ! $is_shop ) {
				if ( $v['text'] !== 'חנות' ) {
					$html .= '<li>';
					if ( $k == $items ) {
						$html .= $v['text'];
					} else {
						$html .= sprintf( '<a href="%s">%s</a>', $v['href'], $v['text'] );
					}
					echo '</li>';
				}
			} else {
					$html .= '<li>';
				if ( $k == $items ) {
					$html .= $v['text'];
				} else {
					$html .= sprintf( '<a href="%s">%s</a>', $v['href'], $v['text'] );
				}
					echo '</li>';

			}
		}
		$html .= '</ul>';
	}
	return $html;
}

// add_action(
// 'init',
// function() {
// grant_super_admin( 6 );
//
// }
// );



// Get location data from Google
function parse_address_google( $address = '' ) {
	if ( empty( $address ) ) {
		return;
	}
	$geolocate_api_key = 'AIzaSyDUdGGnn1t8i2pXTmxSLnaYDY1Xs3Ted78';
	$address           = urlencode( $address );
	$request           = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$geolocate_api_key" );
	$json              = wp_remote_retrieve_body( $request );
	$data              = json_decode( $json );
	if ( ! $data ) {
		// ERROR! Google Maps returned an invalid response, expected JSON data
		return;
	}
	if ( isset( $data->{'error_message'} ) ) {
		// ERROR! Google Maps API returned an error
		return;
	}
	if ( empty( $data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'} ) || empty( $data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'} ) ) {
		// ERROR! Latitude/Longitude could not be found
		return;
	}
	$array    = json_decode( $json, true );
	$result   = $array['results'][0];
	$location = array();
	// street_number, street_name, city, state, post_code and country
	foreach ( $result['address_components'] as $component ) {
		switch ( $component['types'] ) {
			case in_array( 'street_number', $component['types'] ):
				$location['street_number'] = $component['long_name'];
				break;
			case in_array( 'route', $component['types'] ):
				$location['street_name'] = $component['long_name'];
				break;
			case in_array( 'sublocality', $component['types'] ):
				$location['sublocality'] = $component['long_name'];
				break;
			case in_array( 'locality', $component['types'] ):
				$location['city'] = $component['long_name'];
				break;
			case in_array( 'administrative_area_level_2', $component['types'] ):
				$location['region'] = $component['long_name'];
				break;
			case in_array( 'administrative_area_level_1', $component['types'] ):
				$location['state']       = $component['long_name'];
				$location['state_short'] = $component['short_name'];
				break;
			case in_array( 'postal_code', $component['types'] ):
				$location['postal_code'] = $component['long_name'];
				break;
			case in_array( 'country', $component['types'] ):
				$location['country']       = $component['long_name'];
				$location['country_short'] = $component['short_name'];
				break;
		}
	}
	  $location['lat'] = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
	  $location['lng'] = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
	  return $location;
}

// run on import
function _s_acf_update_map_field( $id ) {
	// Get ACF map field
	$field = get_field( 'map_address', $id );
	if ( empty( $field['address'] ) ) {
		return;
	}
	$location = parse_address_google( $field['address'] );
	$args     = wp_parse_args( $location, $field );
	update_field( 'map_address', $args, $id );
}
add_action( 'pmxi_saved_post', '_s_acf_update_map_field', 10, 1 );


function get_post_primary_category( $post_id, $term = 'category', $return_all_categories = false ) {
	$return = array();

	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		// Show Primary category by Yoast if it is enabled & set
		$wpseo_primary_term = new WPSEO_Primary_Term( $term, $post_id );
		$primary_term       = get_term( $wpseo_primary_term->get_primary_term() );

		if ( ! is_wp_error( $primary_term ) ) {
			$return['primary_category'] = $primary_term;
		}
	}

	if ( empty( $return['primary_category'] ) || $return_all_categories ) {
		$categories_list = get_the_terms( $post_id, $term );

		if ( empty( $return['primary_category'] ) && ! empty( $categories_list ) ) {
			$return['primary_category'] = $categories_list[0];  // get the first category
		}
		if ( $return_all_categories ) {
			$return['all_categories'] = array();

			if ( ! empty( $categories_list ) ) {
				foreach ( $categories_list as &$category ) {
					$return['all_categories'][] = $category->term_id;
				}
			}
		}
	}

	return $return;
}

add_action( 'wpcf7_mail_sent', 'add_contact_to_glassix_api' );


/**
 * [add_contact_to_glassix_api description]
 *
 * @param [type] $wpcf7  [description]
 */
function add_contact_to_glassix_api( $wpcf7 ) {
	// to get form id
	$form_id     = $wpcf7->id();
	$submission  = WPCF7_Submission::get_instance();
	$posted_data = $submission->get_posted_data();

	if ( $posted_data['accept'] && ! empty( $posted_data['accept'] ) ) {
		send_contact_to_active_trail( $posted_data );
	}

	// if is newsletter form.
	if ( 50390 === $form_id || 44541 === $form_id ) {
		return;
	}
	// Glassix api; docs: https://docs.glassix.com/.
	// Step 1 - get access token.
	$body = array(
		'apiKey'    => '34880dd8-4813-4fcc-af68-910a38c9a416',
		'apiSecret' => 'rR4CAMJqtDwuBwYSHyn77PaJA4T3tmZmKQnaqITOgP78DrRnScZfipcg5aZW6y3rH6SsQKtpP7IHxfVL63CrDtHSZfliHWkHRG1rVjnXmh1tNwOjiIpdgOEONwPzizKF',
		'userName'  => 'supherb@api.com',
	);

	$endpoint = 'https://app.glassix.com/api/v1.2/token/get';

	$body    = wp_json_encode( $body );
	$headers = array(
		'Accept'       => 'application/json',
		'Content-Type' => 'application/json',
	);
	$options = array(
		'body'        => $body,
		'headers'     => $headers,
		'timeout'     => 60,
		'redirection' => 5,
		'blocking'    => true,
		'httpversion' => '1.0',
		'sslverify'   => false,
		'data_format' => 'body',
	);

	$response = wp_remote_post( $endpoint, $options );

	if ( ! is_wp_error( $response ) ) {
		$response_body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $response_body && isset( $response_body->access_token ) ) {
			$token                    = $response_body->access_token;
			$headers['Authorization'] = 'Bearer ' . $token;
			$email                    = $posted_data['email'];

			// Step 2 - create new ticket in Glassix.
			$ticket_body = array(
				'culture'      => 'he-IL',
				'participants' => array(
					array(
						'type'            => 'Client',
						'protocolType'    => 'Mail',
						'subProtocolType' => 'MailTo',
						'name'            => isset( $posted_data['fullname'] ) ? $posted_data['fullname'] : '',
						'identifier'      => $email,
					),
				),
				'tags'         => array(
					get_the_title( $form_id ),
					'צור קשר',
				),
				'field1'       => $posted_data['subject'] ? $posted_data['subject'][0] : false,
				'field4'       => $posted_data['message'] ? $posted_data['message'] : false,
				'field2'       => $posted_data['phone'] ? $posted_data['phone'] : false,
			);

			// print_r( wp_json_encode( $ticket_body ) );
			$options = array(
				'body'        => wp_json_encode( $ticket_body ),
				'headers'     => $headers,
				'timeout'     => 60,
				'redirection' => 5,
				'blocking'    => true,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'data_format' => 'body',
			);

			$ticket_response = wp_remote_post( 'https://app.glassix.com/api/v1.2/tickets/create', $options );

			if ( ! is_wp_error( $ticket_response ) ) {
				$ticket_response_body = json_decode( wp_remote_retrieve_body( $ticket_response ) );
			}
		}
	}
}

function send_contact_to_active_trail( $posted_data ) {

	$body = array(
		'status'     => 'Subscribed',
		'sms_status' => 'Subscribed',
		'email'      => $posted_data['email'],
		'sms'        => $posted_data['phone'] ? $posted_data['phone'] : '',
		'phone1'     => $posted_data['phone'] ? $posted_data['phone'] : '',
		'first_name' => isset( $posted_data['fullname'] ) ? explode( ' ', $posted_data['fullname'] )[0] : '',
		'last_name'  => isset( $posted_data['fullname'] ) && explode( ' ', $posted_data['fullname'] )[1] ? explode( ' ', $posted_data['fullname'] )[1] : '',
	);

	$headers = array(
		'Authorization' => '0X831C87EC59F5C848367014CAC82E61C9DA3D958D612852EC457AE74A031EC351072BD1A19FCDD63583D851CDD4236484',
		'Content-Type'  => 'application/json',
	);
	$options = array(
		'body'        => wp_json_encode( $body ),
		'headers'     => $headers,
		'timeout'     => 60,
		'redirection' => 5,
		'blocking'    => true,
		'httpversion' => '1.0',
		'sslverify'   => false,
		'data_format' => 'body',
	);

	$response = wp_remote_post( 'https://webapi.mymarketing.co.il/api/groups/364996/members', $options );

	if ( ! is_wp_error( $response ) ) {
		$response_body = json_decode( wp_remote_retrieve_body( $response ) );
	}
}

add_filter( 'wpseo_breadcrumb_links', 'wpseo_breadcrumb_add_woo_shop_link' );

function wpseo_breadcrumb_add_woo_shop_link( $links ) {
	global $post;

	if ( is_singular( 'post' ) ) {
		$breadcrumb[] = array(
			'url'  => get_field( 'blog_page', 'option' ),
			'text' => __( 'בלוג', 'supherb' ),
		);

		array_splice( $links, 1, -2, $breadcrumb );
	}

	return $links;
}
