<?php
/**
 * Hero CTA bar - fixed at bottom on mobile (outside .hero so it stays visible on scroll)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="hero-cta-fixed-wrap">
	<div class="hero-cta" role="group" aria-label="<?php esc_attr_e( '상담 연락', 'della-theme' ); ?>">
		<p class="hero-cta-text"><span class="hero-cta-text-bold"><?php esc_html_e( '지금 바로 상담 가능', 'della-theme' ); ?></span> <?php esc_html_e( '성범죄 사건 상담전화', 'della-theme' ); ?></p>
		<a href="tel:+82-1688-3971" class="hero-cta-phone hero-cta-action" aria-label="<?php esc_attr_e( '상담 전화 걸기 1688-3971', 'della-theme' ); ?>" title="1688-3971">
			<span class="hero-cta-action-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
			</span>
			<span class="hero-cta-tel">1688-3971</span>
		</a>
		<?php
		$hero_home_url = get_theme_mod( 'della_hero_home_url', 'https://sexcrimecenter-dongju.com/' );
		$hero_home_url = apply_filters( 'della_hero_home_url', $hero_home_url );
		?>
		<a href="<?php echo esc_url( $hero_home_url ); ?>" class="hero-cta-button hero-cta-action" aria-label="<?php esc_attr_e( '홈페이지 바로가기', 'della-theme' ); ?>">
			<span class="hero-cta-action-icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
			</span>
			<span class="hero-cta-button-text"><?php esc_html_e( '홈페이지 바로가기', 'della-theme' ); ?> &gt;</span>
		</a>
	</div>
</div>
