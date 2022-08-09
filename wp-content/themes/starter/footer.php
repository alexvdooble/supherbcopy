<?php
/**
 * Weibsite footer
 *
 * @package WordPress
 */

$footer_credit_cards = get_field( 'footer_credit_cards', 'option' );
get_template_part( 'partials/footer/footer', 'newsletter' );
?>
</main>

	<footer id="footer">
		<div class="container">
			<div class="footerInner">
				<?php if ( ! $footer_credit_cards ) : ?>
					<a href="<?php echo esc_url( home_url() ); ?>" aria-label="<?php esc_html_e( 'Logo', 'ystheme' ); ?>" class="footerLogoLink hideOnTablet" title="לחצו כאן למעבר לדף הבית">
						<?php
						H::render_image( 'logo', 'full', 'footerLogoImg hideOnContrast' );
						H::render_image( 'dark_logo', 'full', 'footerLogoImg showOnContrast' );
						?>
					</a>
				<?php else : ?>
					<div class="footer-logo-credits-cards">
						<a href="<?php echo esc_url( home_url() ); ?>" aria-label="<?php esc_html_e( 'Logo', 'ystheme' ); ?>" class="footerLogoLink hideOnTablet" title="לחצו כאן למעבר לדף הבית">
							<?php
							H::render_image( 'logo', 'full', 'footerLogoImg hideOnContrast' );
							H::render_image( 'dark_logo', 'full', 'footerLogoImg showOnContrast' );
							?>
						</a>
						<img src="<?php echo esc_url( $footer_credit_cards['url'] ); ?>" alt="כרטיסי אשראי">
					</div>
				<?php endif; ?>

				<?php get_template_part( 'partials/footer/footer', 'cols' ); ?>
			</div>
			<?php get_template_part( 'partials/footer/footer', 'bottom' ); ?>

			<div class="showOnTablet footerLogoLinkTablet">
				<a href="<?php echo esc_url( home_url() ); ?>" aria-label="<?php esc_html_e( 'Logo', 'ystheme' ); ?>" class="footerLogoLink" title="לחצו כאן למעבר לדף הבית">
					<?php
					H::render_image( 'logo', 'full', 'footerLogoImg hideOnContrast' );
					H::render_image( 'dark_logo', 'full', 'footerLogoImg showOnContrast' );
					?>
				</a>
				<div class="doobleWrap">
					<a href="https://www.dooble.co.il" class="dooble" target="_blank">dooble web design</a>
				</div>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>
	</body>
</html>
