<?php
/**
 * Hero CTA bar - fixed at bottom on mobile (outside .hero so it stays visible on scroll)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$hero_cta_phone = get_theme_mod( 'della_phone', '1844-1087' );
$hero_cta_tel   = 'tel:' . preg_replace( '/[^0-9+]/', '', $hero_cta_phone );
?>
<div class="hero-cta-fixed-wrap">
	<div class="hero-cta" role="group" aria-label="<?php esc_attr_e( '상담 연락', 'della-theme' ); ?>">
		<p class="hero-cta-text"><span class="hero-cta-text-bold"><?php esc_html_e( '지금 바로 상담 가능', 'della-theme' ); ?></span> <?php esc_html_e( '성범죄 사건 상담전화', 'della-theme' ); ?></p>
		<a href="<?php echo esc_url( $hero_cta_tel ); ?>" class="hero-cta-phone hero-cta-action" aria-label="<?php echo esc_attr( sprintf( __( '상담 전화 걸기 %s', 'della-theme' ), $hero_cta_phone ) ); ?>" title="<?php echo esc_attr( $hero_cta_phone ); ?>">
			<span class="hero-cta-action-icon" aria-hidden="true">
				<svg width="24" height="24" viewBox="-2 -2 24 24" fill="currentColor" focusable="false"><path d="M9.05 12.95L7.2 14.8C6.81 15.19 6.19 15.19 5.79 14.81C5.68 14.7 5.57 14.6 5.46 14.49C4.43 13.45 3.5 12.36 2.67 11.22C1.85 10.08 1.19 8.94 0.71 7.81C0.24 6.67 0 5.58 0 4.54C0 3.86 0.12 3.21 0.36 2.61C0.6 2 0.98 1.44 1.51 0.94C2.15 0.31 2.85 0 3.59 0C3.87 0 4.15 0.06 4.4 0.18C4.66 0.3 4.89 0.48 5.07 0.74L7.39 4.01C7.57 4.26 7.7 4.49 7.79 4.71C7.88 4.92 7.93 5.13 7.93 5.32C7.93 5.56 7.86 5.8 7.72 6.03C7.59 6.26 7.4 6.5 7.16 6.74L6.4 7.53C6.29 7.64 6.24 7.77 6.24 7.93C6.24 8.01 6.25 8.08 6.27 8.16C6.3 8.24 6.33 8.3 6.35 8.36C6.53 8.69 6.84 9.12 7.28 9.64C7.73 10.16 8.21 10.69 8.73 11.22C8.83 11.32 8.94 11.42 9.04 11.52C9.44 11.91 9.45 12.55 9.05 12.95Z"/><path d="M19.97 16.33C19.97 16.61 19.92 16.9 19.82 17.18C19.79 17.26 19.76 17.34 19.72 17.42C19.55 17.78 19.33 18.12 19.04 18.44C18.55 18.98 18.01 19.37 17.4 19.62C17.39 19.62 17.38 19.63 17.37 19.63C16.78 19.87 16.14 20 15.45 20C14.43 20 13.34 19.76 12.19 19.27C11.04 18.78 9.89 18.12 8.75 17.29C8.36 17 7.97 16.71 7.6 16.4L10.87 13.13C11.15 13.34 11.4 13.5 11.61 13.61C11.66 13.63 11.72 13.66 11.79 13.69C11.87 13.72 11.95 13.73 12.04 13.73C12.21 13.73 12.34 13.67 12.45 13.56L13.21 12.81C13.46 12.56 13.7 12.37 13.93 12.25C14.16 12.11 14.39 12.04 14.64 12.04C14.83 12.04 15.03 12.08 15.25 12.17C15.47 12.26 15.7 12.39 15.95 12.56L19.26 14.91C19.52 15.09 19.7 15.3 19.81 15.55C19.91 15.8 19.97 16.05 19.97 16.33Z"/></svg>
			</span>
			<span class="hero-cta-tel"><?php echo esc_html( $hero_cta_phone ); ?></span>
		</a>
		<a href="<?php echo esc_url( 'https://sexcrimecenter-dongju.com/consultation' ); ?>" class="hero-cta-button hero-cta-action" aria-label="<?php esc_attr_e( '신속상담', 'della-theme' ); ?>">
			<span class="hero-cta-action-icon" aria-hidden="true">
				<svg width="24" height="24" viewBox="-2 -2 24 24" fill="currentColor" focusable="false"><path d="M16.75 11.9H14.9038C14.7057 11.9 14.5121 11.9588 14.3476 12.069L12.09 13.58C11.73 13.82 11.31 13.93 10.9 13.93C10.55 13.93 10.2 13.85 9.88 13.67C9.39821 13.4153 9.04432 12.9475 8.87051 12.423C8.7313 12.0029 8.45372 11.6136 8.05367 11.4244C7.59444 11.2073 7.18043 10.9204 6.83 10.57C5.97 9.71 5.5 8.5 5.5 7.15V3.25V3C5.5 2.44772 5.05228 2 4.5 2C1.8 2 0 3.35 0 6.5V11.9C0 15.05 1.8 16.4 4.5 16.4H8.25V19.25H5.4C4.99 19.25 4.65 19.59 4.65 20C4.65 20.41 4.99 20.75 5.4 20.75H12.6C13.01 20.75 13.35 20.41 13.35 20C13.35 19.59 13.01 19.25 12.6 19.25H9.75V16.4H13.5C15.8954 16.4 17.5824 15.3374 17.9326 12.898C18.011 12.3513 17.5523 11.9 17 11.9H16.75Z"/><path d="M16.75 0H10.25C8.76 0 7.64 0.76 7.2 2C7.07 2.38 7 2.8 7 3.25V7.15C7 8.12 7.32 8.94 7.89 9.51C8.46 10.08 9.28 10.4 10.25 10.4V11.79C10.25 12.3 10.83 12.61 11.26 12.33L14.15 10.4H16.75C17.2 10.4 17.62 10.33 18 10.2C19.24 9.76 20 8.64 20 7.15V3.25C20 1.3 18.7 0 16.75 0Z"/></svg>
			</span>
			<span class="hero-cta-button-text"><?php esc_html_e( '신속상담', 'della-theme' ); ?></span>
		</a>
	</div>
</div>
