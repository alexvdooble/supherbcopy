<?php
/**
 * Template Name: About Page
 *
 * @package WordPress
 */

get_header();

get_template_part( 'partials/header/header', 'image' );
get_template_part( 'partials/about/about', 'tabs' );
get_template_part( 'partials/about/about', 'video' );
get_template_part( 'partials/about/about', 'steps' );
get_template_part( 'partials/about/about', 'content-image' );
get_template_part( 'partials/about/about', 'content' );
get_template_part( 'partials/about/about', 'gallery' );
get_template_part( 'partials/about/about', 'contact' );

get_footer();
