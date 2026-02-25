<?php
/**
 * Consultation CTA (상담 신청) section - dark overlay, centered copy, CTA button
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$consultation_url = get_theme_mod( 'della_consultation_url', '' );
if ( ! $consultation_url ) {
	$consultation_url = 'https://sexcrimecenter-dongju.com/';
}

$consultation_bg = content_url( 'uploads/2026/02/consultation-bg.webp' );
?>
<section id="consultation-cta" class="consultation-cta" aria-labelledby="consultation-cta-heading">
	<div class="consultation-cta-bg" style="background-image: url('<?php echo esc_url( $consultation_bg ); ?>');" aria-hidden="true"></div>
	<div class="consultation-cta-overlay" aria-hidden="true"></div>
	<div class="consultation-cta-inner">
		<h2 id="consultation-cta-heading" class="consultation-cta-title screen-reader-text"><?php esc_html_e( '상담 신청', 'della-theme' ); ?></h2>
		<p class="consultation-cta-line1"><?php esc_html_e( "성범죄 사건에 있어 '대응의 질'은 결과를 좌우합니다.", 'della-theme' ); ?></p>
		<p class="consultation-cta-line2"><?php esc_html_e( '체계적으로 상담받고, 전략적으로 대응하세요.', 'della-theme' ); ?></p>
		<p class="consultation-cta-line3"><?php echo esc_html__( '동주가 ', 'della-theme' ); ?><span class="consultation-cta-highlight"><?php esc_html_e( '여러분의 방패', 'della-theme' ); ?></span><?php echo esc_html__( '가 되어드리겠습니다.', 'della-theme' ); ?></p>
		<a href="<?php echo esc_url( $consultation_url ); ?>" class="consultation-cta-button" aria-label="<?php esc_attr_e( '상담 신청하기', 'della-theme' ); ?>"><?php esc_html_e( '상담 신청', 'della-theme' ); ?> &gt;</a>
	</div>
</section>
