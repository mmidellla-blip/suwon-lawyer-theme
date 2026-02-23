<?php
/**
 * Page content template part
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header>
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="post-thumbnail">
			<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy', 'alt' => get_the_title() ) ); ?>
		</figure>
	<?php endif; ?>
	<div class="entry-content">
		<?php
		the_content();
		wp_link_pages( array(
			'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page sections', 'della-theme' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'della-theme' ) . '</span>',
			'after'  => '</nav>',
		) );
		?>
	</div>
</article>
