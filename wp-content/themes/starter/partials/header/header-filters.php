<?php
/**
 * Header filters
 *
 * @package WordPress
 */

$header_search_by_label = get_field( 'header_search_by_label', 'option' );

$categories = get_terms(
	array(
		'taxonomy' => 'product_cat',
	)
);
$lifestyles = get_terms(
	array(
		'taxonomy' => 'product_tag',
	)
);
?>
<div class="headerFilters">

	<div class="headerFiltersTitle">
		<?php echo esc_html( $header_search_by_label ); ?>
	</div>

	<ul class="headerFiltersList">
		<?php if ( $categories ) : ?>
			<li class="hflType">
				<button type="button" class="headerFilterBtn">
					<div class="showOnTablet headerFilterBtnTabletTitle">
						<?php esc_html_e( 'חפשו לפי', 'supherb' ); ?>
					</div>
					<?php esc_html_e( 'קטגוריה', 'supherb' ); ?>
				</button>
				<div class="filtersMegaMenu">
					<div class="container noContrast">
						<ul class="filtersMegaMenuList">

							<?php
							foreach ( $categories as $cat ) :
								$color = get_field( 'color', $cat );
								$img   = get_field( 'megamenu_img', $cat );
								if ( 18 !== $cat->term_id ) :
									?>
								<li>
									<a href="<?php echo esc_url( get_term_link( $cat, 'product_cat' ) ); ?>" class="filtersMegaMenuListBtn">
										<div class="hflBtnColor noContrast" style="background:<?php echo $color; ?>;"></div>
										<?php echo $cat->name; ?>
									</a>
									<?php if ( $img ) : ?>
										<div class="megaMenuItemContent">
											<?php
											if ( $img ) {
												 echo wp_get_attachment_image( $img, 'full', false, array( 'class' => 'imgCenter' ) );
											} else {
												H::render_image( 'megamenu_img', 'full', 'imgCenter' );
											}
											?>
										</div>
									<?php endif; ?>

								</li>
									<?php
									endif;
								endforeach;
							?>
						</ul>
						<div class="megaMenuItemContentHere imgCenterWrap"></div>
					</div>
					<div class="filtersMegaMenuImgWrap imgCenterWrap hideOnTablet">
						<?php H::render_image( 'megamenu_def', 'full', 'imgCenter' ); ?>

					</div>
				</div>
			</li>
		<?php endif; ?>

		<?php if ( $lifestyles ) : ?>
			<li>
				<button type="button" class="headerFilterBtn">
					<div class="showOnTablet headerFilterBtnTabletTitle">
						<?php esc_html_e( 'חפשו לפי:', 'supherb' ); ?>
					</div>
					<?php esc_html_e( 'סגנון חיים', 'supherb' ); ?>
				</button>
				<div class="filtersMegaMenu">
					<div class="container noContrast">
						<ul class="filtersMegaMenuList">
							<?php
							foreach ( $lifestyles as $lifestyle ) :

								if ( ! $lifestyle->parent ) :
									?>
								<li>
									<a href="<?php echo get_term_link( $lifestyle, 'lifestyle' ); ?>">
										<?php echo $lifestyle->name; ?>
									</a>
								</li>

									<?php
							endif;
							endforeach;
							?>
						</ul>
					</div>
					<div class="filtersMegaMenuImgWrap imgCenterWrap hideOnTablet">
						<?php H::render_image( 'megamenu_lifestyle', 'full', 'imgCenter' ); ?>
					</div>
				</div>
			</li>
		<?php endif; ?>

	</ul>
</div>
