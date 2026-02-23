<?php
/**
 * 404 template — HTTP 404 상태는 template_redirect 또는 della_theme_trigger_404()에서 전송
 * SEO: noindex,nofollow는 wp_head(della_theme_seo_robots_meta)에서 출력
 *
 * @package Della_Theme
 */

get_header();
?>

<main id="main-content" class="site-main" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<article class="error-404 not-found" aria-labelledby="error-404-title" itemscope itemtype="https://schema.org/WebPage">
		<header class="page-header">
			<h1 id="error-404-title" class="page-title"><?php esc_html_e( '페이지를 찾을 수 없습니다', 'della-theme' ); ?></h1>
		</header>
		<div class="page-content">
			<p><?php esc_html_e( '요청하신 페이지가 없거나 주소가 변경되었을 수 있습니다. 아래 링크에서 원하시는 내용을 찾아 보세요.', 'della-theme' ); ?></p>
			<nav class="error-404-nav" aria-label="<?php esc_attr_e( '404 페이지 내비게이션', 'della-theme' ); ?>">
				<ul class="error-404-links">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '홈으로 돌아가기', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/sitemap/' ) ); ?>"><?php esc_html_e( '사이트맵', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( della_theme_lawyers_page_url() ); ?>"><?php esc_html_e( '성범죄 전문 변호사', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( della_theme_response_board_page_url() ); ?>"><?php esc_html_e( '성범죄 대응정보', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/' ) ); ?>"><?php esc_html_e( '성범죄 성공사례', 'della-theme' ); ?></a></li>
				</ul>
			</nav>
		</div>
	</article>

</main>

<?php get_footer(); ?>
