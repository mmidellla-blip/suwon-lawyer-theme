<?php
/**
 * Front page template (when Settings → Reading → A static page)
 *
 * @package Della_Theme
 */

get_header();
?>

<?php get_template_part( 'template-parts/hero' ); ?>

<?php get_template_part( 'template-parts/success-stories' ); ?>

<?php get_template_part( 'template-parts/process-cards' ); ?>

<?php get_template_part( 'template-parts/major-services' ); ?>

<?php get_template_part( 'template-parts/response-info' ); ?>

<?php get_template_part( 'template-parts/hub-guide' ); ?>

<?php get_template_part( 'template-parts/response-type-hub' ); ?>

<?php get_template_part( 'template-parts/consultation-cta' ); ?>

<?php get_template_part( 'template-parts/directions' ); ?>

<main id="main-content" class="site-main" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'page' );

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>

</main>

<?php get_footer(); ?>
