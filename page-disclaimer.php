<?php
/**
 * Template Name: 면책공고 (Disclaimer)
 * SEO: 단일 H1, 시맨틱 마크업, 법률 사이트 필수 고지
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

	<article class="page-disclaimer entry-content-wrap" itemscope itemtype="https://schema.org/WebPage">
		<header class="entry-header">
			<h1 class="entry-title"><?php esc_html_e( '면책공고', 'della-theme' ); ?></h1>
		</header>
		<div class="entry-content">
			<?php
			if ( get_the_content() ) {
				the_content();
				wp_link_pages( array(
					'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page sections', 'della-theme' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'della-theme' ) . '</span>',
					'after'  => '</nav>',
				) );
			} else {
				?>
				<section class="disclaimer-section" aria-labelledby="disclaimer-intro">
					<h2 id="disclaimer-intro" class="screen-reader-text"><?php esc_html_e( '면책공고 안내', 'della-theme' ); ?></h2>
					<p><?php esc_html_e( '본 웹사이트에 게재된 내용은 법적 자문이 아니며, 일반적인 정보 제공 목적입니다. 구체적인 사건에 대한 법률 자문이 필요하신 경우 변호사와 직접 상담하시기 바랍니다.', 'della-theme' ); ?></p>
				</section>
				<section class="disclaimer-section" aria-labelledby="disclaimer-ad">
					<h2 id="disclaimer-ad"><?php esc_html_e( '광고의 목적', 'della-theme' ); ?></h2>
					<p><?php esc_html_e( '본 사이트는 변호사법 제30조에 따른 변호사 광고입니다. 성범죄 사건 등 형사 사건에 대한 상담·의뢰를 안내하는 목적으로 운영됩니다.', 'della-theme' ); ?></p>
				</section>
				<section class="disclaimer-section" aria-labelledby="disclaimer-contact">
					<h2 id="disclaimer-contact"><?php esc_html_e( '문의', 'della-theme' ); ?></h2>
					<p><?php echo esc_html( get_bloginfo( 'name' ) ); ?> <?php esc_html_e( '수원 성범죄 전문 변호사. 상담·문의는 전화 또는 방문 상담을 이용해 주세요.', 'della-theme' ); ?></p>
				</section>
				<?php
			}
			?>
		</div>
	</article>

</main>

<?php get_footer(); ?>
