<?php
$items = count( get_field( 'footer_widgets', 'option' ) );
$fb = get_field( 'facebook', 'option' );
$yt = get_field( 'youtube', 'option' );
$wa = get_field( 'whatsapp', 'option' );
$ig = get_field( 'instagram', 'option' );
$tw = get_field( 'twitter', 'option' );
$linkedin = get_field( 'linkedin', 'option' );
?>
<div class="footerLinksWrap2">
	<?php
	while ( have_rows( 'footer_widgets', 'option' ) ) :
		the_row();
		$type  = get_sub_field( 'type' );
		$title = get_sub_field( 'title' );
		?>
		<div class="footerLinksWrap">
			<?php if ( $title ) : ?>
				<div class="footerLinksTitle">
					<?php echo $title; ?>
				</div>
			<?php endif; ?>
			<?php
			if ( 'menu' === $type ) :
				wp_nav_menu(
					array(
						'menu'            => get_sub_field( 'menu' ),
						'menu_class'      => 'footerLinksList', // (string) CSS class to use for the ul element which forms the menu. Default 'menu'.
						'container'       => '', // (string) Whether to wrap the ul, and what to wrap it with. Default 'div'.
						'container_class' => '', // (string) Class that is applied to the container. Default 'menu-{menu slug}-container'.
					)
				);
				?>
			<?php else : ?>
				<div class="entry-content">
					<?php the_sub_field( 'content' ); ?>
				</div>
			<?php endif; ?>
			<?php if ( get_row_index() == $items ) : ?>
				<div class="footerLinksTitle socialsTitle hideOnMobile">
					<?php esc_html_e( 'עקבו אחרינו', 'supherb' ); ?>
				</div>
				<ul class="footerSocials">
					<?php if ( $yt ) : ?>
						<li>
							<a href="<?php echo $yt['url']; ?>" rel="nofollow" target="_blank" title="<?php echo $yt['title']; ?>">
								<img src="<?php echo THEME_URI . '/assets/'; ?>images/youtube.svg" alt="YouTube" />
							</a>
						</li>
					<?php endif; ?>
					<?php if ( $tw ) : ?>
						<li>
							<a href="<?php echo $tw['url']; ?>" rel="nofollow" target="_blank" title="<?php echo $tw['title']; ?>">
								<img src="<?php echo THEME_URI . '/assets/'; ?>images/twitter.svg" alt="Twitter" />
							</a>
						</li>
					<?php endif; ?>
					<?php if ( $fb ) : ?>
						<li>
							<a href="<?php echo $fb['url']; ?>" rel="nofollow" target="_blank" title="<?php echo $fb['title']; ?>">
								<img src="<?php echo THEME_URI . '/assets/'; ?>images/facebook.svg" alt="Facebook" />
							</a>
						</li>
					<?php endif; ?>
					<?php if ( $wa ) : ?>
						<li>
							<a href="<?php echo $wa['url']; ?>" rel="nofollow" target="_blank" title="<?php echo $wa['title']; ?>">
								<img src="<?php echo THEME_URI . '/assets/'; ?>images/whatsapp.svg" alt="Whatsapp" />
							</a>
						</li>
					<?php endif; ?>
					<?php if ( $ig ) : ?>
						<li>
							<a href="<?php echo $ig['url']; ?>" rel="nofollow" target="_blank" title="<?php echo $ig['title']; ?>">
								<img src="<?php echo THEME_URI . '/assets/'; ?>images/ig.svg" alt="Instagram" />
							</a>
						</li>
					<?php endif; ?>
					<?php if ( $linkedin ) : ?>
						<li>
							<a href="<?php echo $linkedin['url']; ?>" rel="nofollow" target="_blank" title="<?php echo $linkedin['title']; ?>">
								<img src="<?php echo THEME_URI . '/assets/'; ?>images/linkedin.svg" alt="Linkedin" />
							</a>
						</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</div>