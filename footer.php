<?php
/**
 * Footer template - semantic HTML5
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

	</div><!-- .content-sidebar-wrap -->

	<?php get_template_part( 'template-parts/quick-menu' ); ?>

	<?php if ( is_front_page() ) : ?>
		<?php get_template_part( 'template-parts/hero-cta' ); ?>
	<?php endif; ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="footer-inner">
			<div class="footer-brand">
				<?php if ( has_custom_logo() ) : ?>
					<div class="footer-logo"><?php the_custom_logo(); ?></div>
				<?php else : ?>
					<div class="footer-logo-icon" aria-hidden="true">同</div>
				<?php endif; ?>
				<span class="footer-specialty" aria-hidden="true"> <?php esc_html_e( '수원 성범죄', 'della-theme' ); ?></span>
			</div>

			<div class="footer-divider" aria-hidden="true"></div>

			<!-- 데스크톱: 주소 항상 노출 -->
			<div class="footer-addresses footer-addresses-desk">
				<p class="footer-address-line"><?php esc_html_e( '서울사무소', 'della-theme' ); ?>: <?php esc_html_e( '서울 서초구 서초중앙로 123 (서초동), 13층 (서초동, 엘렌타워)', 'della-theme' ); ?></p>
				<p class="footer-address-line"><?php esc_html_e( '수원 사무소', 'della-theme' ); ?>: <?php esc_html_e( '수원시 영통구 광교중앙로248번길 7-2, B동 902-903호(하동, 원희캐슬광교)', 'della-theme' ); ?></p>
				<p class="footer-address-line"><?php esc_html_e( '인천 사무소', 'della-theme' ); ?>: <?php esc_html_e( '인천시 미추홀구 학익소로 66, 403-404호(학익동, 선정빌딩)', 'della-theme' ); ?></p>
			</div>

			<!-- 모바일: 지사 주소 아코디언 -->
			<details class="footer-addresses-accordion footer-addresses-mobile">
				<summary class="footer-accordion-summary">
					<span class="footer-accordion-title"><?php esc_html_e( '지사 주소', 'della-theme' ); ?></span>
					<span class="footer-accordion-icon" aria-hidden="true"></span>
				</summary>
				<div class="footer-addresses-content">
					<p class="footer-address-line"><?php esc_html_e( '서울사무소', 'della-theme' ); ?>: <?php esc_html_e( '서울 서초구 서초중앙로 123 (서초동), 13층 (서초동, 엘렌타워)', 'della-theme' ); ?></p>
					<p class="footer-address-line"><?php esc_html_e( '수원 사무소', 'della-theme' ); ?>: <?php esc_html_e( '수원시 영통구 광교중앙로248번길 7-2, B동 902-903호(하동, 원희캐슬광교)', 'della-theme' ); ?></p>
					<p class="footer-address-line"><?php esc_html_e( '인천 사무소', 'della-theme' ); ?>: <?php esc_html_e( '인천시 미추홀구 학익소로 66, 403-404호(학익동, 선정빌딩)', 'della-theme' ); ?></p>
				</div>
			</details>

			<div class="footer-contact">
				<p class="footer-contact-line">
					<span class="footer-contact-item"><strong><?php esc_html_e( '대표변호사', 'della-theme' ); ?></strong> <?php esc_html_e( '이세환 대표변호사', 'della-theme' ); ?></span>
					<span class="footer-contact-item"><strong><?php esc_html_e( '광고책임변호사', 'della-theme' ); ?></strong> <?php esc_html_e( '이세진 변호사', 'della-theme' ); ?></span>
					<span class="footer-contact-row">
						<span class="footer-contact-item"><strong><?php esc_html_e( '전화번호', 'della-theme' ); ?></strong> <a href="tel:15223394">1522-3394</a></span>
						<span class="footer-contact-item"><strong><?php esc_html_e( '팩스', 'della-theme' ); ?></strong> 02-523-7260</span>
						<span class="footer-contact-item"><strong><?php esc_html_e( '사업자등록번호', 'della-theme' ); ?></strong> 128-88-01756</span>
					</span>
				</p>
			</div>

			<div class="footer-divider" aria-hidden="true"></div>

			<div class="footer-bottom">
				<p class="footer-legal">
					<a href="<?php echo esc_url( home_url( '/sitemap/' ) ); ?>"><?php esc_html_e( '사이트맵', 'della-theme' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/면책공고/' ) ); ?>"><?php esc_html_e( '면책공고', 'della-theme' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/개인정보처리방침/' ) ); ?>"><?php esc_html_e( '개인정보처리방침', 'della-theme' ); ?></a>
				</p>
				<p class="footer-copyright">Copyright &copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. All rights reserved.</p>
			</div>
		</div>
	</footer>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
