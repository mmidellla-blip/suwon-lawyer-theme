<?php
/**
 * Archive template (category, tag, author, date, etc.)
 *
 * @package Della_Theme
 */

get_header();
?>

<main id="main-content" class="site-main" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<header class="archive-header">
		<?php
		the_archive_title( '<h1 class="archive-title">', '</h1>' );
		the_archive_description( '<div class="archive-description">', '</div>' );
		?>
	</header>

	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'template-parts/content', get_post_type() ); ?>
		<?php endwhile; ?>

		<?php
		the_posts_pagination( array(
			'mid_size'  => 2,
			'prev_text' => __( '&larr; Previous', 'della-theme' ),
			'next_text' => __( 'Next &rarr;', 'della-theme' ),
		) );
		?>

	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
