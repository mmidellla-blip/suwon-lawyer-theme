<?php
/**
 * Single post content template part
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
		<div class="entry-meta">
			<span class="posted-on">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			</span>
			<span class="byline"> <?php esc_html_e( 'by', 'della-theme' ); ?>
				<span class="author vcard"><a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php the_author(); ?></a></span>
			</span>
			<?php if ( has_category() ) : ?>
				<span class="cat-links"><?php echo get_the_category_list( ', ' ); ?></span>
			<?php endif; ?>
		</div>
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
			'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Post pages', 'della-theme' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'della-theme' ) . '</span>',
			'after'  => '</nav>',
		) );
		?>
	</div>
	<footer class="entry-footer">
		<?php if ( has_tag() ) : ?>
			<span class="tags-links"><?php the_tags( '', ', ', '' ); ?></span>
		<?php endif; ?>
	</footer>
</article>
