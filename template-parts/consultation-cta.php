<?php
/**
 * Consultation CTA (상담 신청) section - SEO-friendly, dark overlay, centered copy, CTA button
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$consultation_url = function_exists( 'della_theme_consultation_url' ) ? della_theme_consultation_url() : 'https://sexcrimecenter-dongju.com/bbs/board.php?bo_table=online&me_code=6010';

$consultation_bg = content_url( 'uploads/2026/02/consultation-bg.webp' );
?>
<section id="consultation-cta" class="consultation-cta" aria-labelledby="consultation-cta-heading">
	<div class="consultation-cta-bg" style="background-image: url('<?php echo esc_url( $consultation_bg ); ?>');" aria-hidden="true"></div>
	<div class="consultation-cta-overlay" aria-hidden="true"></div>
	<div class="consultation-cta-inner">
		<h2 id="consultation-cta-heading" class="consultation-cta-title consultation-cta-line1">수원 성범죄 상담 | 강제추행·카메라촬영·아청법 사건 대응</h2>
		<p class="consultation-cta-line2">수원 성범죄 전문변호사가 경찰조사부터 재판까지 단계별 대응 전략을 설계합니다.</p>
		<p class="consultation-cta-line3">강제추행·카메라촬영·아청법 사건, 지금 상황에 맞는 상담을 받아보세요.</p>
		<p class="consultation-cta-note">경찰 출석·압수수색·긴급 상황도 즉시 대응합니다.</p>
		<a href="<?php echo esc_url( $consultation_url ); ?>" class="consultation-cta-button" aria-label="수원 성범죄 전문변호사 상담 신청">수원 성범죄 상담 신청 &gt;</a>
	</div>
</section>
