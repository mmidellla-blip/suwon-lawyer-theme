<?php
/**
 * Single post template
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
		get_template_part( 'template-parts/content', 'single' );

		the_post_navigation( array(
			'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'della-theme' ) . '</span> <span class="nav-title">%title</span>',
			'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'della-theme' ) . '</span> <span class="nav-title">%title</span>',
		) );

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>

</main>

<?php get_footer(); ?>
