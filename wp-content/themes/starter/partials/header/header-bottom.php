<?php
/**
 * Header bottom
 *
 * @package WordPress
 */

$item_link = get_field( 'find_store_link', 'option' );
?>
<div class="headerBottom">
	<div class="container">
		<div class="tabletMenuBtns showOnTablet">
			<button type="button" class="tabletMenuBtnsItem tabletContrastBtn">
				<img src="<?php echo esc_url( THEME_URI ) . '/assets/'; ?>images/contrast.svg" alt="" />
			</button>

			<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="tabletMenuBtnsItem tabletPAreaBtn">
				<img src="<?php echo esc_url( THEME_URI ) . '/assets/'; ?>images/man.svg" alt="" />
			</a>

			<a href="#" class="tabletMenuBtnsItem tabletFavoratesBtn">
				<img src="<?php echo esc_url( THEME_URI ) . '/assets/'; ?>images/heart.svg" alt="" />
			</a>

			<button type="button" class="tabletCloseMenuBtn">
				<img src="<?php echo esc_url( THEME_URI ) . '/assets/'; ?>images/x.svg" alt="" />
			</button>
		</div>
		<div class="headerMenusWrap">
			<?php get_template_part( 'partials/header/header', 'filters' ); ?>
			<div class="headerBottomNavWrap">
				<?php get_template_part( 'partials/header/main', 'navigation' ); ?>

				<?php if ( $item_link ) : ?>
					<a href="<?php echo $item_link['url']; ?>" class="findStoreBtn">
						<svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M8 10C6.9 10 6 9.1 6 8C6 6.9 6.9 6 8 6C9.1 6 10 6.9 10 8C10 9.1 9.1 10 8 10ZM14 8.2C14 4.57 11.35 2 8 2C4.65 2 2 4.57 2 8.2C2 10.54 3.95 13.64 8 17.34C12.05 13.64 14 10.54 14 8.2ZM8 0C12.2 0 16 3.22 16 8.2C16 11.52 13.33 15.45 8 20C2.67 15.45 0 11.52 0 8.2C0 3.22 3.8 0 8 0Z" fill="#35563C"/>
						</svg>
						<?php echo $item_link['title']; ?>
					</a>
				<?php endif; ?>

			</div>
		</div>
	</div>
</div>
