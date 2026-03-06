<?php
/**
 * Hero section - front page (SEO-friendly semantic markup)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$upload_dir = wp_upload_dir();
$hero_base  = $upload_dir['baseurl'] . '/2026/02';
$hero_dir   = $upload_dir['basedir'] . '/2026/02';
$bg_url     = $hero_base . '/dongju-law-hero-banner.webp';
$lawyers    = della_theme_get_lawyers();

$hero_legal_script = array(
	'@context'    => 'https://schema.org',
	'@type'       => 'LegalService',
	'name'        => get_bloginfo( 'name' ),
	'description' => get_bloginfo( 'description' ) ?: '형사사건 전문 변호사 팀. 같은 사건에 자신 있는 다른 변호사들이 모여 만드는 시너지.',
	'url'         => home_url( '/' ),
	'image'       => array( array( '@type' => 'ImageObject', 'url' => $bg_url ) ),
	'telephone'   => get_theme_mod( 'della_phone', '1522-3394' ),
	'areaServed'  => array( '@id' => 'https://www.wikidata.org/wiki/Q884' ),
	'priceRange'  => '상담 후 안내',
	'employee'    => array(),
);
foreach ( $lawyers as $lawyer ) {
	$hero_legal_script['employee'][] = array(
		'@type'       => 'Person',
		'name'        => $lawyer['name'],
		'jobTitle'    => $lawyer['title'],
		'description' => ! empty( $lawyer['items'] ) ? implode( ', ', $lawyer['items'] ) : '',
	);
}

$hero_home_url   = apply_filters( 'della_hero_home_url', get_theme_mod( 'della_hero_home_url', home_url( '/' ) ) );
$hero_url_cases  = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/성범죄-성공사례/' );
$hero_url_info   = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );
$hero_phone      = get_theme_mod( 'della_phone', '1522-3394' );
$hero_phone_tel  = 'tel:' . preg_replace( '/[^0-9+]/', '', $hero_phone );
?>
<section id="hero" class="hero" aria-labelledby="hero-title" style="background-image: url(<?php echo esc_url( $bg_url ); ?>);" itemscope itemtype="https://schema.org/LegalService">
	<div class="hero-overlay" aria-hidden="true"></div>
	<div class="hero-inner">
		<h1 class="sr-only">수원 성범죄 전문변호사 | <?php echo esc_html( della_theme_firm_name() ); ?></h1>
		<p class="hero-subtitle">하나보다 여섯이 우월하기에, 우리는 함께 대응합니다.</p>
		<h2 id="hero-title" class="hero-title">'같은' 사건에 자신있는 <br class="hero-br-pc"><span class="hero-title-line2">'다른' <br class="hero-br-mo">변호사들이 모여 만드는 시너지</span></h2>
		<p id="hero-intro" class="hero-seo-intro">강제추행 · 카메라촬영 · 아청법 사건 대응<br>경찰조사부터 재판까지 형사전문변호사가 직접 함께합니다.<br><a href="<?php echo esc_url( $hero_url_cases ); ?>" class="hero-intro-link">성범죄 성공사례</a><span class="hero-intro-link-sep" aria-hidden="true"> · </span><a href="<?php echo esc_url( $hero_url_info ); ?>" class="hero-intro-link">성범죄 대응정보</a></p>

		<div class="hero-lawyers" role="region" aria-label="변호사 프로필 (<?php echo count( $lawyers ); ?>명)" tabindex="0">
			<?php foreach ( $lawyers as $lawyer_idx => $lawyer ) : ?>
				<?php
				$profile_url = della_theme_lawyer_profile_url( isset( $lawyer['slug'] ) ? $lawyer['slug'] : '' );
				$img_src     = della_theme_lawyer_image_url( $lawyer['image'], $hero_base, $hero_dir );
				$img_srcset  = della_theme_lawyer_image_srcset( $lawyer['image'], $hero_base, $hero_dir );
				$img_alt     = $lawyer['name'] . ' ' . $lawyer['title'] . ' 프로필 사진';
				?>
				<article class="hero-lawyer-card" itemscope itemtype="https://schema.org/Person">
					<?php if ( $profile_url ) : ?><a href="<?php echo esc_url( $profile_url ); ?>" class="hero-lawyer-card-link" aria-label="<?php echo esc_attr( $lawyer['name'] . ' ' . $lawyer['title'] . ' 변호사 정보 보기' ); ?>"><?php endif; ?>
					<div class="hero-lawyer-image-wrap">
						<img src="<?php echo esc_url( $img_src ); ?>" <?php if ( $img_srcset ) : ?>srcset="<?php echo esc_attr( $img_srcset ); ?>" sizes="200px"<?php endif; ?> alt="<?php echo esc_attr( $img_alt ); ?>" width="400" height="533" loading="<?php echo $lawyer_idx < 2 ? 'eager' : 'lazy'; ?>" decoding="async" class="hero-lawyer-image"<?php echo ( $lawyer_idx === 0 ) ? ' fetchpriority="high"' : ''; ?> />
					</div>
					<h2 class="hero-lawyer-name">
						<span itemprop="name"><?php echo esc_html( $lawyer['name'] ); ?></span>
						<span class="hero-lawyer-title" itemprop="jobTitle"><?php echo esc_html( $lawyer['title'] ); ?></span>
					</h2>
					<?php if ( ! empty( $lawyer['items'] ) ) : ?>
						<ul class="hero-lawyer-list" itemprop="description" aria-label="<?php echo esc_attr( $lawyer['name'] . ' 변호사 경력' ); ?>">
							<?php foreach ( array_slice( $lawyer['items'], 0, 3 ) as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $profile_url ) : ?></a><?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="hero-cta hero-cta-in-hero" role="group" aria-label="상담 연락">
			<p class="hero-cta-text"><span class="hero-cta-text-bold">지금 바로 상담 가능</span> 성범죄 사건 상담전화</p>
			<a href="<?php echo esc_url( $hero_phone_tel ); ?>" class="hero-cta-phone hero-cta-action" aria-label="<?php echo esc_attr( sprintf( __( '상담 전화 걸기 %s', 'della-theme' ), $hero_phone ) ); ?>" title="<?php echo esc_attr( $hero_phone ); ?>">
				<span class="hero-cta-action-icon" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
				</span>
				<span class="hero-cta-tel"><?php echo esc_html( $hero_phone ); ?></span>
			</a>
			<a href="<?php echo esc_url( $hero_home_url ); ?>" class="hero-cta-button hero-cta-action" aria-label="홈페이지 바로가기">
				<span class="hero-cta-action-icon" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
				</span>
				<span class="hero-cta-button-text">홈페이지 바로가기 &gt;</span>
			</a>
		</div>
	</div>
	<script type="application/ld+json"><?php echo wp_json_encode( $hero_legal_script ); ?></script>
</section>
