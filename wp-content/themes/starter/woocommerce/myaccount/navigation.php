<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );


$customer_account_menu = wc_get_account_menu_items();
unset( $customer_account_menu['downloads'] );

?>

<div class="myAccountSideMenuWrap woocommerce-MyAccount-navigation">
	<button type="button" class="showOnMobile pAreaMenuMobileBtn bold700green">
		<?php

		if ( is_wc_endpoint_url( 'orders' ) ) {
			esc_html_e( 'ההזמנות שלי', 'supherb' );
		} elseif ( is_wc_endpoint_url( 'edit-address' ) ) {
			esc_html_e( 'כתובות', 'supherb' );
		} elseif ( is_wc_endpoint_url( 'edit-account' ) ) {
			esc_html_e( 'פרטי יצירת קשר', 'supherb' );
		} else {
			esc_html_e( 'דשבורד', 'supherb' );
		}

		?>
	</button>
	<ul class="myAccountSideMenu">
		<?php foreach ( $customer_account_menu as $endpoint => $label ) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="<?php echo 'customer-logout' === $endpoint ? 'logoutBtn' : ''; ?>">
					<?php if ( 'customer-logout' === $endpoint ) : ?>
						<img src="<?php echo THEME_URI; ?>/assets/images/logout.svg" alt="" height="18">
					<?php endif; ?>

					<?php echo esc_html( $label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
