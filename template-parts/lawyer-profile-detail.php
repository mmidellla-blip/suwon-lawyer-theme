<?php
/**
 * 변호사 상세: 바디 사진 끝나는 지점 하얀 배경 프로필 정보 (대변협 전문분야, 변호사 정보, 학력, 경력)
 *
 * @package Della_Theme
 * @var array $args [ 'lawyer' => array ]
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$lawyer = isset( $args['lawyer'] ) ? $args['lawyer'] : array();
if ( empty( $lawyer ) ) {
	return;
}

$specialties = isset( $lawyer['specialties'] ) && is_array( $lawyer['specialties'] ) ? $lawyer['specialties'] : array();
$education   = isset( $lawyer['education'] ) && is_array( $lawyer['education'] ) ? $lawyer['education'] : array();
$career      = isset( $lawyer['items'] ) && is_array( $lawyer['items'] ) ? $lawyer['items'] : array();
$media       = isset( $lawyer['media'] ) && is_array( $lawyer['media'] ) ? $lawyer['media'] : array();
$has_quote   = ! empty( $lawyer['quote'] );
$info_tabs   = $specialties;
?>
<section class="lawyer-profile-detail" role="region" aria-labelledby="lawyer-profile-detail-heading" aria-label="<?php echo esc_attr( $lawyer['name'] . ' ' . __( '상세 프로필', 'della-theme' ) ); ?>">
	<div class="lawyer-profile-detail-inner">
		<nav class="lawyer-profile-detail-nav" aria-label="<?php esc_attr_e( '대변협 등록 전문분야', 'della-theme' ); ?>">
			<p class="lawyer-profile-detail-nav-title"><?php esc_html_e( '대변협 등록 전문분야', 'della-theme' ); ?></p>
			<ul class="lawyer-profile-detail-nav-list">
				<li><a href="#lawyer-info" class="lawyer-profile-detail-nav-link"><?php esc_html_e( '변호사 정보', 'della-theme' ); ?></a></li>
				<li><a href="#lawyer-education" class="lawyer-profile-detail-nav-link"><?php esc_html_e( '학력', 'della-theme' ); ?></a></li>
				<li><a href="#lawyer-career" class="lawyer-profile-detail-nav-link"><?php esc_html_e( '경력', 'della-theme' ); ?></a></li>
				<?php if ( ! empty( $media ) ) : ?>
				<li><a href="#lawyer-media" class="lawyer-profile-detail-nav-link"><?php esc_html_e( '언론 및 강연', 'della-theme' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</nav>

		<div class="lawyer-profile-detail-main">
			<h2 id="lawyer-profile-detail-heading" class="screen-reader-text"><?php echo esc_html( $lawyer['name'] . ' ' . __( '상세 프로필', 'della-theme' ) ); ?></h2>

			<div class="lawyer-profile-detail-block lawyer-profile-detail-specialties-wrap" data-section="specialties">
				<h3 class="lawyer-profile-detail-heading"><?php esc_html_e( '대한변호사협회 등록 전문분야', 'della-theme' ); ?></h3>
				<?php if ( ! empty( $specialties ) ) : ?>
					<div class="lawyer-specialties">
						<?php
						$icon_index = 0;
						foreach ( $specialties as $spec ) :
							$is_first = ( 0 === $icon_index );
							$icon_index++;
							// 첫 번째: 법망치(형사법), 그 외: 건물(행정법 등) — 흰 원 안 검정 아웃라인
							if ( $is_first ) {
								// 법망치(형사법) — 흰 원 안 검정 아웃라인
								$icon_svg = '<svg class="lawyer-specialty-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 22V10M9 10h6"/></svg>';
							} else {
								$icon_svg = '<svg class="lawyer-specialty-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 21h18M3 10h18M5 10v11M19 10v11M9 21V10M15 21V10M9 5h6v5H9z"/></svg>';
							}
							?>
							<span class="lawyer-specialty-pill">
								<span class="lawyer-specialty-icon-circle">
									<?php echo $icon_svg; ?>
								</span>
								<span class="lawyer-specialty-label"><?php echo esc_html( $spec ); ?></span>
							</span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div id="lawyer-info" class="lawyer-profile-detail-block">
				<h3 class="lawyer-profile-detail-heading"><?php esc_html_e( '변호사 정보', 'della-theme' ); ?></h3>
				<?php if ( ! empty( $info_tabs ) ) : ?>
				<div class="lawyer-info-tabs" role="tablist">
					<?php foreach ( $info_tabs as $tab ) : ?>
						<button type="button" class="lawyer-info-tab" role="tab"><?php echo esc_html( $tab ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<?php if ( $has_quote ) : ?>
					<blockquote class="lawyer-quote-box">
						<span class="lawyer-quote-box-mark" aria-hidden="true">"</span>
						<p class="lawyer-quote-box-text"><?php echo esc_html( $lawyer['quote'] ); ?></p>
					</blockquote>
				<?php endif; ?>
			</div>

			<div id="lawyer-education" class="lawyer-profile-detail-block">
				<h3 class="lawyer-profile-detail-heading"><?php esc_html_e( '학력', 'della-theme' ); ?></h3>
				<?php if ( ! empty( $education ) ) : ?>
					<ul class="lawyer-profile-detail-list">
						<?php foreach ( $education as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p class="lawyer-profile-detail-empty"><?php esc_html_e( '등록된 내용이 없습니다.', 'della-theme' ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $career ) ) : ?>
				<div id="lawyer-career" class="lawyer-profile-detail-block">
					<h3 class="lawyer-profile-detail-heading"><?php esc_html_e( '경력', 'della-theme' ); ?></h3>
					<ul class="lawyer-profile-detail-list">
						<?php foreach ( $career as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $media ) ) : ?>
			<div id="lawyer-media" class="lawyer-profile-detail-block">
				<h3 class="lawyer-profile-detail-heading"><?php esc_html_e( '언론 및 강연', 'della-theme' ); ?></h3>
				<ul class="lawyer-profile-detail-list">
					<?php foreach ( $media as $item ) : ?>
						<li><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<script>
(function() {
	var nav = document.querySelector('.lawyer-profile-detail-nav');
	if (!nav) return;
	var links = nav.querySelectorAll('.lawyer-profile-detail-nav-link');
	function setActive() {
		var hash = window.location.hash || '#lawyer-info';
		links.forEach(function(a) {
			a.classList.toggle('is-active', a.getAttribute('href') === hash);
		});
	}
	setActive();
	window.addEventListener('hashchange', setActive);
})();
</script>
