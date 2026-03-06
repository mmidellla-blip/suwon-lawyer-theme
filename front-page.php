<?php
/**
 * Front page template (when Settings → Reading → A static page)
 *
 * @package Della_Theme
 */

get_header();
?>

<?php get_template_part( 'template-parts/hero' ); ?>

<?php get_template_part( 'template-parts/success-stories' ); ?>

<?php get_template_part( 'template-parts/process-cards' ); ?>

<?php get_template_part( 'template-parts/major-services' ); ?>

<?php get_template_part( 'template-parts/response-info' ); ?>

<?php get_template_part( 'template-parts/hub-guide' ); ?>

<?php get_template_part( 'template-parts/response-type-hub' ); ?>

<?php get_template_part( 'template-parts/response-situation-hub' ); ?>

<?php get_template_part( 'template-parts/hub-faq' ); ?>

<?php get_template_part( 'template-parts/consultation-cta' ); ?>

<?php get_template_part( 'template-parts/directions' ); ?>

<main id="main-content" class="site-main" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<?php
	// 홈은 상단 섹션(hub-guide, response-type-hub, hub-faq 등)으로 구성되므로 페이지 본문 미출력.
	// 오시는 길 아래에 중복·떠있는 대응가이드 본문이 나오지 않도록 함.
	while ( have_posts() ) :
		the_post();
		// 정적 홈 페이지 본문은 출력하지 않음 (대체 콘텐츠는 이미 위 섹션에 있음).
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>

</main>

<?php get_footer(); ?>
