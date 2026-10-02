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
	'telephone'   => function_exists( 'della_theme_format_telephone_for_schema' ) ? della_theme_format_telephone_for_schema( get_theme_mod( 'della_phone', '1844-1087' ) ) : get_theme_mod( 'della_phone', '1844-1087' ),
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

$hero_consult_url = 'https://sexcrimecenter-dongju.com/consultation';
$hero_url_cases   = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/성범죄-성공사례/' );
$hero_url_info   = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );
$hero_phone      = get_theme_mod( 'della_phone', '1844-1087' );
$hero_phone_tel  = 'tel:' . preg_replace( '/[^0-9+]/', '', $hero_phone );
?>
<section id="hero" class="hero" aria-labelledby="hero-title" style="background-image: url(<?php echo esc_url( $bg_url ); ?>);" itemscope itemtype="https://schema.org/LegalService">
	<div class="hero-overlay" aria-hidden="true"></div>
	<div class="hero-inner">
		<h1 class="sr-only">수원 성범죄 전문변호사 | <?php echo esc_html( della_theme_firm_name() ); ?></h1>
		<p class="hero-subtitle">하나보다 여섯이 우월하기에, <br class="hero-br-mo">우리는 함께 대응합니다.</p>
		<h2 id="hero-title" class="hero-title">‘같은’ 사건에 자신있는 <br class="hero-br-pc"><span class="hero-title-line2">‘다른’ <br class="hero-br-mo">변호사들이 모여 만드는 시너지</span></h2>
		<p id="hero-intro" class="hero-seo-intro">강제추행 · 카메라촬영 · 아청법 사건 대응<br>경찰조사부터 재판까지 형사전문변호사가 <br class="hero-br-mo">직접 함께합니다.<br class="hero-intro-links-br"><a href="<?php echo esc_url( $hero_url_cases ); ?>" class="hero-intro-link">성범죄 성공사례</a><span class="hero-intro-link-sep" aria-hidden="true"> · </span><a href="<?php echo esc_url( $hero_url_info ); ?>" class="hero-intro-link">성범죄 대응정보</a></p>

		<div class="hero-lawyers" role="region" aria-label="변호사 프로필 (<?php echo count( $lawyers ); ?>명)" tabindex="0">
			<?php foreach ( $lawyers as $lawyer_idx => $lawyer ) : ?>
				<?php
				$profile_url = della_theme_lawyer_profile_url( isset( $lawyer['slug'] ) ? $lawyer['slug'] : '' );
				$img_src     = della_theme_lawyer_image_url( $lawyer['image'], $hero_base, $hero_dir );
				$img_srcset  = della_theme_lawyer_image_srcset( $lawyer['image'], $hero_base, $hero_dir );
				$img_alt     = $lawyer['name'] . ' ' . $lawyer['title'] . ' 프로필 사진';
				// 모바일 카드: Figma 372:3762 누끼 사진 (카드 177×334 프레이밍 그대로, 테마 내 assets/images/hero-lawyers)
				$mo_slug     = preg_replace( '/^dongju-|-lawyer$/', '', pathinfo( $lawyer['image'], PATHINFO_FILENAME ) );
				$mo_img_rel  = '/assets/images/hero-lawyers/' . $mo_slug;
				$mo_srcset   = file_exists( get_template_directory() . $mo_img_rel . '.webp' )
					? esc_url( get_template_directory_uri() . $mo_img_rel . '.webp' ) . ' 1x, ' . esc_url( get_template_directory_uri() . $mo_img_rel . '@2x.webp' ) . ' 2x, ' . esc_url( get_template_directory_uri() . $mo_img_rel . '@3x.webp' ) . ' 3x'
					: '';
				// PC 카드: Figma 328:6656 누끼 사진 (카드 184×382 중 사진 영역 184×276 프레이밍)
				$pc_img_rel  = $mo_img_rel . '-pc';
				$pc_srcset   = file_exists( get_template_directory() . $pc_img_rel . '.webp' )
					? esc_url( get_template_directory_uri() . $pc_img_rel . '.webp' ) . ' 1x, ' . esc_url( get_template_directory_uri() . $pc_img_rel . '@2x.webp' ) . ' 2x, ' . esc_url( get_template_directory_uri() . $pc_img_rel . '@3x.webp' ) . ' 3x'
					: '';
				?>
				<article class="hero-lawyer-card" itemscope itemtype="https://schema.org/Person">
					<?php if ( $profile_url ) : ?><a href="<?php echo esc_url( $profile_url ); ?>" class="hero-lawyer-card-link" aria-label="<?php echo esc_attr( $lawyer['name'] . ' ' . $lawyer['title'] . ' 변호사 정보 보기' ); ?>"><?php endif; ?>
					<div class="hero-lawyer-image-wrap">
						<picture>
							<?php if ( $mo_srcset ) : ?><source media="(max-width: 767px)" srcset="<?php echo esc_attr( $mo_srcset ); ?>" width="177" height="334" type="image/webp" /><?php endif; ?>
							<?php if ( $pc_srcset ) : ?><source media="(min-width: 768px)" srcset="<?php echo esc_attr( $pc_srcset ); ?>" width="184" height="276" type="image/webp" /><?php endif; ?>
							<img src="<?php echo esc_url( $img_src ); ?>" <?php if ( $img_srcset ) : ?>srcset="<?php echo esc_attr( $img_srcset ); ?>" sizes="200px"<?php endif; ?> alt="<?php echo esc_attr( $img_alt ); ?>" width="400" height="533" loading="<?php echo $lawyer_idx < 2 ? 'eager' : 'lazy'; ?>" decoding="async" class="hero-lawyer-image"<?php echo ( $lawyer_idx === 0 ) ? ' fetchpriority="high"' : ''; ?> />
						</picture>
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
		<?php /* 모바일: Figma 340:5818 카드 가로 스크롤 진행 막대 */ ?>
		<div class="hero-lawyers-progress" aria-hidden="true"><span class="hero-lawyers-progress-thumb"></span></div>
		<script>
		(function () {
			var list = document.querySelector('.hero-lawyers');
			var bar = document.querySelector('.hero-lawyers-progress');
			if (!list || !bar) return;
			var thumb = bar.firstElementChild;
			function update() {
				var ratio = list.scrollWidth ? Math.min(1, list.clientWidth / list.scrollWidth) : 1;
				var max = list.scrollWidth - list.clientWidth;
				var pos = max > 0 ? list.scrollLeft / max : 0;
				thumb.style.width = ( ratio * 100 ) + '%';
				thumb.style.transform = 'translateX(' + ( pos * ( 1 / ratio - 1 ) * 100 ) + '%)';
			}
			list.addEventListener('scroll', update, { passive: true });
			window.addEventListener('resize', update);
			update();
		})();
		</script>

		<div class="hero-cta hero-cta-in-hero" role="group" aria-label="상담 연락">
			<p class="hero-cta-text"><span class="hero-cta-text-bold">지금 바로 상담 가능</span> 성범죄 사건 상담전화</p>
			<a href="<?php echo esc_url( $hero_phone_tel ); ?>" class="hero-cta-phone hero-cta-action" aria-label="<?php echo esc_attr( sprintf( __( '상담 전화 걸기 %s', 'della-theme' ), $hero_phone ) ); ?>" title="<?php echo esc_attr( $hero_phone ); ?>">
				<span class="hero-cta-action-icon" aria-hidden="true">
					<svg width="24" height="24" viewBox="-2 -2 24 24" fill="currentColor" focusable="false"><path d="M9.05 12.95L7.2 14.8C6.81 15.19 6.19 15.19 5.79 14.81C5.68 14.7 5.57 14.6 5.46 14.49C4.43 13.45 3.5 12.36 2.67 11.22C1.85 10.08 1.19 8.94 0.71 7.81C0.24 6.67 0 5.58 0 4.54C0 3.86 0.12 3.21 0.36 2.61C0.6 2 0.98 1.44 1.51 0.94C2.15 0.31 2.85 0 3.59 0C3.87 0 4.15 0.06 4.4 0.18C4.66 0.3 4.89 0.48 5.07 0.74L7.39 4.01C7.57 4.26 7.7 4.49 7.79 4.71C7.88 4.92 7.93 5.13 7.93 5.32C7.93 5.56 7.86 5.8 7.72 6.03C7.59 6.26 7.4 6.5 7.16 6.74L6.4 7.53C6.29 7.64 6.24 7.77 6.24 7.93C6.24 8.01 6.25 8.08 6.27 8.16C6.3 8.24 6.33 8.3 6.35 8.36C6.53 8.69 6.84 9.12 7.28 9.64C7.73 10.16 8.21 10.69 8.73 11.22C8.83 11.32 8.94 11.42 9.04 11.52C9.44 11.91 9.45 12.55 9.05 12.95Z"/><path d="M19.97 16.33C19.97 16.61 19.92 16.9 19.82 17.18C19.79 17.26 19.76 17.34 19.72 17.42C19.55 17.78 19.33 18.12 19.04 18.44C18.55 18.98 18.01 19.37 17.4 19.62C17.39 19.62 17.38 19.63 17.37 19.63C16.78 19.87 16.14 20 15.45 20C14.43 20 13.34 19.76 12.19 19.27C11.04 18.78 9.89 18.12 8.75 17.29C8.36 17 7.97 16.71 7.6 16.4L10.87 13.13C11.15 13.34 11.4 13.5 11.61 13.61C11.66 13.63 11.72 13.66 11.79 13.69C11.87 13.72 11.95 13.73 12.04 13.73C12.21 13.73 12.34 13.67 12.45 13.56L13.21 12.81C13.46 12.56 13.7 12.37 13.93 12.25C14.16 12.11 14.39 12.04 14.64 12.04C14.83 12.04 15.03 12.08 15.25 12.17C15.47 12.26 15.7 12.39 15.95 12.56L19.26 14.91C19.52 15.09 19.7 15.3 19.81 15.55C19.91 15.8 19.97 16.05 19.97 16.33Z"/></svg>
				</span>
				<span class="hero-cta-tel"><?php echo esc_html( $hero_phone ); ?></span>
			</a>
			<a href="<?php echo esc_url( $hero_consult_url ); ?>" class="hero-cta-button hero-cta-action" aria-label="<?php esc_attr_e( '신속상담 바로가기', 'della-theme' ); ?>">
				<span class="hero-cta-action-icon" aria-hidden="true">
					<svg width="24" height="24" viewBox="-2 -2 24 24" fill="currentColor" focusable="false"><path d="M16.75 11.9H14.9038C14.7057 11.9 14.5121 11.9588 14.3476 12.069L12.09 13.58C11.73 13.82 11.31 13.93 10.9 13.93C10.55 13.93 10.2 13.85 9.88 13.67C9.39821 13.4153 9.04432 12.9475 8.87051 12.423C8.7313 12.0029 8.45372 11.6136 8.05367 11.4244C7.59444 11.2073 7.18043 10.9204 6.83 10.57C5.97 9.71 5.5 8.5 5.5 7.15V3.25V3C5.5 2.44772 5.05228 2 4.5 2C1.8 2 0 3.35 0 6.5V11.9C0 15.05 1.8 16.4 4.5 16.4H8.25V19.25H5.4C4.99 19.25 4.65 19.59 4.65 20C4.65 20.41 4.99 20.75 5.4 20.75H12.6C13.01 20.75 13.35 20.41 13.35 20C13.35 19.59 13.01 19.25 12.6 19.25H9.75V16.4H13.5C15.8954 16.4 17.5824 15.3374 17.9326 12.898C18.011 12.3513 17.5523 11.9 17 11.9H16.75Z"/><path d="M16.75 0H10.25C8.76 0 7.64 0.76 7.2 2C7.07 2.38 7 2.8 7 3.25V7.15C7 8.12 7.32 8.94 7.89 9.51C8.46 10.08 9.28 10.4 10.25 10.4V11.79C10.25 12.3 10.83 12.61 11.26 12.33L14.15 10.4H16.75C17.2 10.4 17.62 10.33 18 10.2C19.24 9.76 20 8.64 20 7.15V3.25C20 1.3 18.7 0 16.75 0Z"/></svg>
				</span>
				<span class="hero-cta-button-text"><?php esc_html_e( '신속상담 바로가기 >', 'della-theme' ); ?></span>
			</a>
		</div>
	</div>
	<script type="application/ld+json"><?php echo wp_json_encode( $hero_legal_script ); ?></script>
</section>
