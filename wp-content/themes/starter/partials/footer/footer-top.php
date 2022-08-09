<div class="footer-top">
	<div class="container">
		<div class="d-md-flex footer-cols">
			<?php while ( have_rows( 'footer_widgets', 'option' ) ) :
				the_row();
				$type  = get_sub_field( 'type' );
				$title = get_sub_field( 'title' );
				?>

				<div class="footer-col footer-col-<?php echo $type; ?>">
					<?php if ( $title ) : ?>
						<h4 class="entry-title">
							<?php echo $title; ?>
						</h4>
					<?php endif; ?>

					<?php if ( 'menu' === $type ) : ?>

						<nav class="footer-nav">
							<?php the_sub_field( 'menu' ); ?>
						</nav>

					<?php else : ?>

						<div class="entry-content">
							<?php the_sub_field( 'content' ); ?>
						</div>

					<?php endif; ?>
				</div>

			<?php endwhile; ?>
		</div>
	</div>
</div>
