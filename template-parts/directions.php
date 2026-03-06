<?php
/**
 * Directions (오시는 길) section - map placeholder, address, consultation hours
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$road_address   = get_theme_mod( 'della_road_address', '경기 수원시 영통구 광교중앙로248번길 7-2' );
$road_address2  = get_theme_mod( 'della_road_address2', '원희캐슬광교 B동 902호, 903호' );
$lot_address    = get_theme_mod( 'della_lot_address', '경기 수원시 영통구 하동 989' );
$phone          = get_theme_mod( 'della_phone', '1522-3394' );
$naver_reserve_url = get_theme_mod( 'della_naver_reserve_url', 'https://naver.me/FQVkj6Eh' );
if ( ! $naver_reserve_url ) {
	$naver_reserve_url = 'https://naver.me/FQVkj6Eh';
}

$full_street = trim( $road_address . ' ' . $road_address2 );
$directions_schema = array(
	'@context'    => 'https://schema.org',
	'@type'       => 'LegalService',
	'name'        => get_bloginfo( 'name' ),
	'description' => __( '성범죄 전문 법무법인, 수원 오시는 길 및 상담 예약', 'della-theme' ),
	'url'         => home_url( '/' ),
	'telephone'   => function_exists( 'della_theme_format_telephone_for_schema' ) ? della_theme_format_telephone_for_schema( $phone ) : $phone,
	'address'     => array(
		'@type'           => 'PostalAddress',
		'streetAddress'  => $full_street,
		'addressLocality' => __( '수원시', 'della-theme' ),
		'addressRegion'   => __( '경기', 'della-theme' ),
	),
	'geo'         => array(
		'@type'     => 'GeoCoordinates',
		'latitude'  => 37.2914787038032,
		'longitude' => 127.06464575446904,
	),
	'openingHoursSpecification' => array(
		array( '@type' => 'OpeningHoursSpecification', 'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ), 'opens' => '09:00', 'closes' => '19:00' ),
		array( '@type' => 'OpeningHoursSpecification', 'dayOfWeek' => array( 'Saturday', 'Sunday' ), 'opens' => '09:00', 'closes' => '18:00' ),
	),
);
?>
<section id="directions" class="directions" aria-labelledby="directions-heading">
	<div class="directions-inner">
		<header class="directions-header">
			<h2 id="directions-heading" class="directions-title"><?php esc_html_e( '오시는 길', 'della-theme' ); ?></h2>
			<p class="directions-note">*<?php esc_html_e( '주말 : 토요일 방문상담 가능', 'della-theme' ); ?></p>
		</header>

		<div class="directions-map-wrap" role="region" aria-label="<?php esc_attr_e( '오시는 길 지도', 'della-theme' ); ?>">
			<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3174.1542162437577!2d127.06464575446904!3d37.2914787038032!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357b5b13d373e1ad%3A0x8fbd3f4b48e1f519!2z6rK96riw64-EIOyImOybkOyLnCDsmIHthrXqtawg6rSR6rWQ7KSR7JWZ66GcMjQ467KI6ri4IDctMg!5e0!3m2!1sko!2skr!4v1770765985898!5m2!1sko!2skr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php esc_attr_e( '오시는 길 지도', 'della-theme' ); ?>"></iframe>
		</div>

		<div class="directions-info">
			<div class="directions-block directions-address">
				<h3 class="directions-block-title"><?php esc_html_e( '주소', 'della-theme' ); ?></h3>
				<div class="directions-block-content directions-address-cols">
					<div class="directions-address-col">
						<p class="directions-label">
							<?php esc_html_e( '도로명 주소', 'della-theme' ); ?>
							<button type="button" class="directions-icon-copy" data-copy-text="<?php echo esc_attr( $full_street ); ?>" data-original-aria-label="<?php esc_attr_e( '도로명 주소 복사', 'della-theme' ); ?>" aria-label="<?php esc_attr_e( '도로명 주소 복사', 'della-theme' ); ?>" title="<?php esc_attr_e( '복사', 'della-theme' ); ?>"></button>
						</p>
						<p class="directions-value">
							<?php echo esc_html( $road_address ); ?> <?php echo esc_html( $road_address2 ); ?>
						</p>
					</div>
					<div class="directions-address-col">
						<p class="directions-label">
							<?php esc_html_e( '지번', 'della-theme' ); ?>
							<button type="button" class="directions-icon-copy" data-copy-text="<?php echo esc_attr( $lot_address ); ?>" data-original-aria-label="<?php esc_attr_e( '지번 복사', 'della-theme' ); ?>" aria-label="<?php esc_attr_e( '지번 복사', 'della-theme' ); ?>" title="<?php esc_attr_e( '복사', 'della-theme' ); ?>"></button>
						</p>
						<p class="directions-value"><?php echo esc_html( $lot_address ); ?></p>
					</div>
				</div>
			</div>

			<div class="directions-divider" aria-hidden="true"></div>

			<div class="directions-block directions-hours">
				<h3 class="directions-block-title"><?php esc_html_e( '상담시간', 'della-theme' ); ?></h3>
				<div class="directions-block-content directions-hours-cols">
					<div class="directions-hours-col">
						<div class="directions-hours-col-content">
							<p class="directions-label"><?php esc_html_e( '전화상담', 'della-theme' ); ?></p>
							<p class="directions-value">
								<?php esc_html_e( '월-금 09:00-19:00', 'della-theme' ); ?><br>
								<?php esc_html_e( '토-일 09:00-18:00', 'della-theme' ); ?>
							</p>
							<p class="directions-small">*<?php esc_html_e( '영업시간 외 카카오톡 게시판 문의는 확인 즉시 순차적으로 연락드리고 있습니다.', 'della-theme' ); ?></p>
						</div>
						<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="directions-btn directions-btn-phone" aria-label="<?php echo esc_attr( sprintf( __( '전화 걸기: %s', 'della-theme' ), $phone ) ); ?>">
							<span class="directions-btn-icon directions-btn-icon-phone" aria-hidden="true"></span>
							<?php echo esc_html( $phone ); ?>
						</a>
					</div>
					<div class="directions-hours-col">
						<div class="directions-hours-col-content">
							<p class="directions-label"><?php esc_html_e( '방문상담', 'della-theme' ); ?></p>
							<p class="directions-value"><?php esc_html_e( '월-토 10:00-18:00', 'della-theme' ); ?></p>
							<p class="directions-highlight directions-value"><?php esc_html_e( '일요일 정기 휴무', 'della-theme' ); ?></p>
							<p class="directions-small">*<?php esc_html_e( '방문 상담은 사전 예약 후 이용 가능', 'della-theme' ); ?></p>
							<p class="directions-small">**<?php esc_html_e( '영업시간 외 방문은 유선으로 시간 조율 후 가능합니다.', 'della-theme' ); ?></p>
						</div>
						<p class="directions-small"><?php esc_html_e( '상담 시간 선택 후 빠르게 연락드립니다.', 'della-theme' ); ?></p>
						<a href="<?php echo esc_url( $naver_reserve_url ); ?>" class="directions-btn directions-btn-naver" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( '네이버 예약으로 상담 시간 선택', 'della-theme' ); ?>" aria-label="<?php esc_attr_e( '네이버 상담 예약 바로가기', 'della-theme' ); ?>" onclick="if(typeof gtag==='function'){gtag('event','naver_reservation_click',{event_category:'consult',event_label:'naver_reservation'});}">
							<span class="directions-btn-icon directions-btn-icon-naver" aria-hidden="true">N</span>
							<?php esc_html_e( '네이버예약', 'della-theme' ); ?> &gt;
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="directions-notes-wrap directions-notes-mobile">
			<div class="directions-divider directions-notes-divider" aria-hidden="true"></div>
			<div class="directions-notes">
				<h3 class="directions-notes-title"><?php esc_html_e( '비고', 'della-theme' ); ?></h3>
				<ul class="directions-notes-list">
					<li><?php esc_html_e( '수원지방법원, 수원검찰청 맞은편 광교 원희캐슬법조타운 건물 지하주차장 이용', 'della-theme' ); ?></li>
					<li><?php esc_html_e( 'B동 가까운 곳에 주차 후, 반드시 B동 엘리베이터 이용 &gt; B동 9층으로 오시면 됩니다.', 'della-theme' ); ?></li>
					<li><?php esc_html_e( "지하주차장 입구는 '광교동태전문점' 우측에 위치해있습니다.(기본 2시간 무료, 최대 3시간까지 지원 가능)", 'della-theme' ); ?></li>
				</ul>
			</div>
		</div>
		<script type="application/ld+json"><?php echo wp_json_encode( $directions_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	</div>
</section>
