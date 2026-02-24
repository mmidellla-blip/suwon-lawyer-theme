<?php
/**
 * Template Name: 개인정보처리방침 (Privacy Policy)
 * SEO: 단일 H1, 시맨틱 마크업, 개인정보보호법 대응
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

	<article class="page-privacy-policy entry-content-wrap" itemscope itemtype="https://schema.org/WebPage">
		<header class="entry-header">
			<h1 class="entry-title"><?php esc_html_e( '개인정보처리방침', 'della-theme' ); ?></h1>
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
				<section class="privacy-section" aria-labelledby="privacy-intro">
					<h2 id="privacy-intro"><?php esc_html_e( '1. 개인정보의 수집·이용 목적', 'della-theme' ); ?></h2>
					<p><?php echo esc_html( get_bloginfo( 'name' ) ); ?> <?php esc_html_e( '는 법률 상담·의뢰 접수, 사건 처리, 연락 및 안내 목적으로 필요한 범위에서만 개인정보를 수집·이용합니다.', 'della-theme' ); ?></p>
				</section>
				<section class="privacy-section" aria-labelledby="privacy-items">
					<h2 id="privacy-items"><?php esc_html_e( '2. 수집하는 개인정보 항목', 'della-theme' ); ?></h2>
					<p><?php esc_html_e( '성명, 연락처(전화·휴대전화), 이메일, 상담 내용 등 상담 및 의뢰에 필요한 최소한의 정보를 수집할 수 있습니다.', 'della-theme' ); ?></p>
				</section>
				<section class="privacy-section" aria-labelledby="privacy-retention">
					<h2 id="privacy-retention"><?php esc_html_e( '3. 보유·이용 기간', 'della-theme' ); ?></h2>
					<p><?php esc_html_e( '수집 목적 달성 후 지체 없이 파기하며, 관계 법령에서 보존 의무가 있는 경우 해당 기간 동안 보관합니다.', 'della-theme' ); ?></p>
				</section>
				<section class="privacy-section" aria-labelledby="privacy-rights">
					<h2 id="privacy-rights"><?php esc_html_e( '4. 이용자의 권리', 'della-theme' ); ?></h2>
					<p><?php esc_html_e( '이용자는 개인정보 열람·정정·삭제·처리정지를 요청할 수 있으며, 요청 시 관계 법령에 따라 조치하겠습니다.', 'della-theme' ); ?></p>
				</section>
				<section class="privacy-section" aria-labelledby="privacy-contact">
					<h2 id="privacy-contact"><?php esc_html_e( '5. 문의', 'della-theme' ); ?></h2>
					<p><?php esc_html_e( '개인정보 처리와 관련한 문의·요청은 사이트 내 연락처를 통해 하실 수 있습니다.', 'della-theme' ); ?></p>
				</section>
				<?php
			}
			?>
		</div>
	</article>

</main>

<?php get_footer(); ?>
