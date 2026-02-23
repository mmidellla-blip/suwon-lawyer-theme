<?php
/**
 * Main template - blog index
 *
 * @package Della_Theme
 */

get_header();
?>

<?php if ( is_front_page() && is_home() ) : ?>
	<?php get_template_part( 'template-parts/hero' ); ?>
<?php else : ?>
<main id="main-content" class="site-main" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<?php if ( is_home() && ! is_front_page() ) : ?>
		<header class="page-header">
			<h1 class="page-title"><?php single_post_title(); ?></h1>
		</header>
	<?php endif; ?>

	<?php /* Index: no post list */ ?>

</main>
<?php endif; ?>

<?php get_footer(); ?>
