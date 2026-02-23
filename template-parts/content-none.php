<?php
/**
 * No content — 검색 결과 없음 / 아카이브 0건 등 (SEO: noindex 처리된 페이지)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php $is_sub = is_search() || is_archive(); ?>
<section class="no-results not-found" aria-labelledby="no-results-title">
	<header class="page-header">
		<?php if ( $is_sub ) : ?>
		<h2 id="no-results-title" class="page-title">
		<?php else : ?>
		<h1 id="no-results-title" class="page-title">
		<?php endif; ?>
			<?php
			if ( is_search() ) {
				esc_html_e( '검색 결과가 없습니다', 'della-theme' );
			} elseif ( is_archive() ) {
				esc_html_e( '등록된 글이 없습니다', 'della-theme' );
			} else {
				esc_html_e( '찾으시는 내용이 없습니다', 'della-theme' );
			}
			?>
		<?php if ( $is_sub ) : ?></h2><?php else : ?></h1><?php endif; ?>
	</header>
	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'della-theme' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>
		<?php elseif ( is_search() ) : ?>
			<p><?php esc_html_e( '검색어에 맞는 글이 없습니다. 다른 키워드로 다시 검색해 보세요.', 'della-theme' ); ?></p>
		<?php else : ?>
			<p><?php esc_html_e( '해당 조건에 맞는 내용이 없습니다.', 'della-theme' ); ?></p>
		<?php endif; ?>
		<p class="no-results-links">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '홈으로', 'della-theme' ); ?></a>
		</p>
	</div>
</section>
