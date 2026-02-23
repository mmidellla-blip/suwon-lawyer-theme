<?php
/**
 * Comments template
 *
 * @package Della_Theme
 */

if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments-area" aria-label="<?php esc_attr_e( 'Post comments', 'della-theme' ); ?>">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$della_comment_count = get_comments_number();
			printf(
				/* translators: 1: number of comments */
				esc_html( _n( '%1$s comment', '%1$s comments', $della_comment_count, 'della-theme' ) ),
				number_format_i18n( $della_comment_count )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 60,
				'callback'    => 'della_theme_comment_callback',
			) );
			?>
		</ol>

		<?php the_comments_navigation(); ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'della-theme' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form( array(
		'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
		'title_reply_after'  => '</h3>',
	) );
	?>

</section>
