<?php
/**
 * 404 template
 *
 * @package Della_Theme
 */

get_header();
?>

<main id="main-content" class="site-main" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<section class="error-404 not-found">
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Page not found', 'della-theme' ); ?></h1>
		</header>
		<div class="page-content">
			<p><?php esc_html_e( 'The page you are looking for might have been removed or is temporarily unavailable.', 'della-theme' ); ?></p>
			<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return to home', 'della-theme' ); ?></a></p>
		</div>
	</section>

</main>

<?php get_footer(); ?>
