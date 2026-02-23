<?php
/**
 * Page template
 *
 * @package Della_Theme
 */

get_header();
?>

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
