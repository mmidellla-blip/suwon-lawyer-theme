<?php
/**
 * Default post/content template part for loop (index, archive)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		<div class="entry-meta">
			<span class="posted-on">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			</span>
			<span class="byline"> <?php esc_html_e( 'by', 'della-theme' ); ?>
				<span class="author vcard"><a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></span>
			</span>
		</div>
	</header>
	<div class="entry-content">
		<?php the_excerpt(); ?>
	</div>
	<footer class="entry-footer">
		<a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e( 'Read more', 'della-theme' ); ?></a>
	</footer>
</article>
