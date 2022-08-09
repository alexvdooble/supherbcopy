<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />

		<title><?php wp_title( '' ); ?></title>

		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php
			wp_body_open();
			wc_print_notices();
		?>

		<?php
		if ( is_front_page() ) {
			get_template_part( 'partials/global/newsletter', 'popup' );
		}

		if ( is_product_category() ) {
			get_template_part( 'partials/product-category/category', 'popup' );
		}
		?>

		<div class="headerWaypoint"></div>
		<header id="header">
			<?php
			get_template_part( 'partials/header/header', 'top' );
			get_template_part( 'partials/header/header', 'middle' );
			get_template_part( 'partials/header/header', 'bottom' );
			?>
		</header>

		<main id="main">
