<?php
/**
 * Quick Menu (퀵메뉴) - 우측 플로팅, 재사용 가능
 * get_template_part( 'template-parts/quick-menu' ); 로 어디서든 호출
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$consultation_url = get_theme_mod( 'della_consultation_url', '' );
if ( ! $consultation_url ) {
	$page = get_page_by_path( 'consultation' );
	if ( ! $page ) {
		$page = get_page_by_path( 'contact' );
	}
	if ( ! $page ) {
		$page = get_page_by_path( '상담신청' );
	}
	$consultation_url = $page ? get_permalink( $page ) : home_url( '/#consultation' );
}

$quick_menu_items = array(
	array(
		'key'   => 'tel',
		'label' => '전화상담',
		'url'   => 'tel:1688-3971',
		'external' => false,
		'icon'  => 'phone',
	),
	array(
		'key'   => 'kakao',
		'label' => '카톡상담',
		'url'   => 'https://pf.kakao.com/_Rpbxmxb/chat',
		'external' => true,
		'icon'  => 'kakao',
	),
	array(
		'key'   => 'map',
		'label' => '오시는 길',
		'url'   => home_url( '/#directions' ),
		'external' => false,
		'icon'  => 'map',
	),
	array(
		'key'   => 'blog',
		'label' => '블로그',
		'url'   => 'https://blog.naver.com/dongjulaw',
		'external' => true,
		'icon'  => 'blog',
	),
	array(
		'key'   => 'youtube',
		'label' => '유튜브',
		'url'   => 'https://www.youtube.com/@Dong_Ju_LawFirm',
		'external' => true,
		'icon'  => 'youtube',
	),
);

$quick_menu_items = apply_filters( 'della_quick_menu_items', $quick_menu_items );
$quick_menu_hours = apply_filters( 'della_quick_menu_hours', '09:00 ~ 22:00' );

$quick_menu_icons = array(
	'selftest' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>',
	'phone'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.913.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.897.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
	'kakao'    => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3c5.8 0 10.5 3.66 10.5 8.18 0 4.52-4.7 8.18-10.5 8.18-1.07 0-2.1-.15-3.05-.43l-3.26 1.02.35-3.4C2.9 14.5 1.5 11.97 1.5 11.18 1.5 6.66 6.2 3 12 3z"/></svg>',
	'online'   => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
	'map'      => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
	'blog'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>',
	'youtube'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
	'time'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
);
?>
<nav id="quickMenu" class="quick-menu" aria-label="퀵메뉴">
	<ul class="quick-menu-list">
		<?php foreach ( $quick_menu_items as $item ) : ?>
			<li class="quick-menu-item quick-menu-item-<?php echo esc_attr( $item['key'] ); ?>">
				<a
					href="<?php echo esc_url( $item['url'] ); ?>"
					class="quick-menu-link"
					<?php if ( ! empty( $item['external'] ) ) : ?>
						target="_blank"
						rel="noopener noreferrer"
					<?php endif; ?>
					aria-label="<?php echo esc_attr( $item['label'] ); ?>"
				>
					<span class="quick-menu-icon" aria-hidden="true"><?php echo isset( $quick_menu_icons[ $item['icon'] ] ) ? $quick_menu_icons[ $item['icon'] ] : ''; ?></span>
					<span class="quick-menu-text"><?php echo esc_html( $item['label'] ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
