<?php
/**
 * Product buttons
 *
 * @package WordPress
 */

$show_on_mobile = isset( $args['showOnMobile'] ) ? true : false;
$store          = get_field( 'find_store_link', 'option' );
$show_btn       = false;

$add_to_products_button_tooltip = get_field( 'add_to_products_button_tooltip', 'option' );

if ( is_user_logged_in() ) {
	$show_btn                  = true;
	$is_added                  = false;
	$user_id                   = get_current_user_id();
	$user_recommended_products = get_user_meta( $user_id, 'sup_recommended', true );

	if ( in_array( get_the_id(), $user_recommended_products ) ) {
		$is_added = true;
	}
}
?>
<div class="AjxPrdBtns2Wrap <?php echo $show_on_mobile ? 'showOnMobile' : 'hideOnMobile'; ?>">

	<?php if ( is_user_logged_in() ) : ?>
		<button type="button" class="AjxPrdBtns2 js_addToProds <?php echo $is_added ? 'active' : ''; ?>"
			data-added="<?php echo $is_added ? true : false; ?>"
			data-product="<?php echo esc_html( get_the_id() ); ?>"
			<?php if ( $add_to_products_button_tooltip ) : ?>
				title="<?php echo esc_html( $add_to_products_button_tooltip ); ?>"
			<?php endif; ?>
			>
			<div class="AjxPrdBtns2inner addText">
				<img src="<?php echo esc_url( THEME_URI ); ?>/assets/images/add.svg" alt="" class="AjxPrdBtns2icon">
				<?php esc_html_e( 'הוספה למוצרים הקבועים שלי', 'supherb' ); ?>
			</div>
			<div class="AjxPrdBtns2inner removeText">
				<img src="<?php echo esc_url( THEME_URI ); ?>/assets/images/remove.svg" alt="" class="AjxPrdBtns2icon">
				<?php esc_html_e( 'הסרה מהמוצרים הקבועים שלי', 'supherb' ); ?>
			</div>
		</button>
	<?php else : ?>
		<button type="button" class="AjxPrdBtns2 open-login">
			<div class="AjxPrdBtns2inner addText">
				<img src="<?php echo esc_url( THEME_URI ); ?>/assets/images/add.svg" alt="" class="AjxPrdBtns2icon">
				<?php esc_html_e( 'הוספה למוצרים הקבועים שלי', 'supherb' ); ?>
			</div>

		</button>
	<?php endif; ?>


	<?php if ( $store ) : ?>
		<a href="<?php echo esc_url( $store['url'] ); ?>" type="button" class="AjxPrdBtns2">
			<img src="<?php echo esc_url( THEME_URI ); ?>/assets/images/address.svg" alt="" class="AjxPrdBtns2icon">
			<?php echo wp_kses_post( $store['title'] ); ?>
		</a>
	<?php endif; ?>

	<div class="shippingPolicyWrap">
		<a href="#shippingPolicy" class="AjxPrdBtns2 yBox yBoxFocus" data-ybox-class="yBoxStyle1">
			<img src="<?php echo esc_url( THEME_URI ); ?>/assets/images/car.svg" alt="" class="AjxPrdBtns2icon">
			<?php esc_html_e( 'מדיניות משלוחים', 'supherb' ); ?>
		</a>
		<div style="display:none;">
			<div id="shippingPolicy">
				<div class="shipPopTitle">
					<?php esc_html_e( 'משלוחים', 'supherb' ); ?>
				</div>
				<?php the_field( 'shipping_text', 'option' ); ?>
				<br>
				<br>
				<div class="shipPopTitle">
					<?php esc_html_e( 'החזרות', 'supherb' ); ?>
				</div>
				<?php the_field( 'returns_text', 'option' ); ?>
			</div>
		</div>
	</div>
</div>
