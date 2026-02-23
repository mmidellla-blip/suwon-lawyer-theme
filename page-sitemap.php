<?php
/**
 * Template Name: 사이트맵 (Sitemap)
 * SEO sitemap page - 키워드 연동 URL 구조 (디자인 유지)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main-content" class="site-main" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<article class="page-sitemap entry-content-wrap">
		<header class="entry-header">
			<h1 class="entry-title"><?php esc_html_e( '사이트맵', 'della-theme' ); ?></h1>
			<p class="sitemap-desc"><?php esc_html_e( '수원 성범죄 전문 변호사 사이트 주요 페이지 목록입니다.', 'della-theme' ); ?></p>
		</header>

		<div class="entry-content sitemap-content">
			<section class="sitemap-section" aria-labelledby="sitemap-main-heading">
				<h2 id="sitemap-main-heading" class="sitemap-section-title"><?php esc_html_e( '메인 홈페이지', 'della-theme' ); ?></h2>
				<ul class="sitemap-list">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '홈', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#success-stories' ) ); ?>"><?php esc_html_e( '성공 사례', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#process-cards' ) ); ?>"><?php esc_html_e( '진행 절차', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#major-services' ) ); ?>"><?php esc_html_e( '주요 서비스', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#response-info' ) ); ?>"><?php esc_html_e( '대응 정보', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#consultation-cta' ) ); ?>"><?php esc_html_e( '상담 신청', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#directions' ) ); ?>"><?php esc_html_e( '오시는 길', 'della-theme' ); ?></a></li>
				</ul>
			</section>

			<section class="sitemap-section" aria-labelledby="sitemap-info-heading">
				<h2 id="sitemap-info-heading" class="sitemap-section-title"><?php esc_html_e( '법률 정보·상담', 'della-theme' ); ?></h2>
				<ul class="sitemap-list">
					<li><a href="<?php echo esc_url( della_theme_lawyers_page_url() ); ?>"><?php esc_html_e( '성범죄 전문 변호사', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( della_theme_response_board_page_url() ); ?>"><?php esc_html_e( '성범죄 대응정보', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/성범죄-성공사례/' ) ); ?>"><?php esc_html_e( '성범죄 성공사례', 'della-theme' ); ?></a></li>
				</ul>
			</section>

			<section class="sitemap-section" aria-labelledby="sitemap-legal-heading">
				<h2 id="sitemap-legal-heading" class="sitemap-section-title"><?php esc_html_e( '이용 안내', 'della-theme' ); ?></h2>
				<ul class="sitemap-list">
					<li><a href="<?php echo esc_url( home_url( '/면책공고/' ) ); ?>"><?php esc_html_e( '면책공고', 'della-theme' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/개인정보처리방침/' ) ); ?>"><?php esc_html_e( '개인정보처리방침', 'della-theme' ); ?></a></li>
				</ul>
			</section>
		</div>
	</article>

</main>

<?php get_footer(); ?>
