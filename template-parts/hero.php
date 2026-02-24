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
?>
<?php
// JSON-LD: LegalService (로컬/검색 노출용)
$hero_legal_script = array(
	'@context'        => 'https://schema.org',
	'@type'           => 'LegalService',
	'name'            => get_bloginfo( 'name' ),
	'description'     => get_bloginfo( 'description' ) ? get_bloginfo( 'description' ) : '형사사건 전문 변호사 팀. 같은 사건에 자신 있는 다른 변호사들이 모여 만드는 시너지.',
	'url'             => home_url( '/' ),
	'image'           => array( array( '@type' => 'ImageObject', 'url' => $bg_url ) ),
	'telephone'       => '+82-1688-3971',
	'areaServed'      => array( '@id' => 'https://www.wikidata.org/wiki/Q884' ), // Korea
	'priceRange'      => '상담 후 안내',
	'employee'        => array(),
);
foreach ( $lawyers as $lawyer ) {
	$hero_legal_script['employee'][] = array(
		'@type'      => 'Person',
		'name'       => $lawyer['name'],
		'jobTitle'   => $lawyer['title'],
		'description' => ! empty( $lawyer['items'] ) ? implode( ', ', $lawyer['items'] ) : '',
	);
}
?>
<?php
$hero_home_url = get_theme_mod( 'della_hero_home_url', home_url( '/' ) );
$hero_home_url = apply_filters( 'della_hero_home_url', $hero_home_url );
?>
<section id="hero" class="hero" aria-labelledby="hero-title" style="background-image: url(<?php echo esc_url( $bg_url ); ?>);" itemscope itemtype="https://schema.org/LegalService">
	<div class="hero-overlay" aria-hidden="true"></div>
	<div class="hero-inner">
		<p class="hero-subtitle">하나보다 여섯이 우월하기에, 우리는 함께 대응합니다.</p>
		<h1 id="hero-title" class="hero-title">'같은' 사건에 자신있는<br><span class="hero-title-line2">'다른' 변호사들이 모여 만드는 시너지</span></h1>
		<p id="hero-intro" class="hero-seo-intro"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> 수원 성범죄 전문 변호사 팀이 강간·강제추행·불법촬영·디지털성범죄 등 성범죄 사건 초기 대응부터 재판까지 함께합니다.</p>
		<div class="hero-lawyers" role="region" aria-label="변호사 프로필 (<?php echo count( $lawyers ); ?>명)" tabindex="0">
			<?php foreach ( $lawyers as $lawyer_idx => $lawyer ) : ?>
				<?php
				$lawyer_img_src = della_theme_lawyer_image_url( $lawyer['image'], $hero_base, $hero_dir );
				$lawyer_srcset  = della_theme_lawyer_image_srcset( $lawyer['image'], $hero_base, $hero_dir );
				$lawyer_alt     = $lawyer['name'] . ' ' . $lawyer['title'] . ' 프로필 사진';
				$is_above_fold  = $lawyer_idx < 2;
				$is_lcp_candidate = ( $lawyer_idx === 0 );
				?>
				<article class="hero-lawyer-card" itemscope itemtype="https://schema.org/Person">
					<div class="hero-lawyer-image-wrap">
						<img
							src="<?php echo esc_url( $lawyer_img_src ); ?>"
							<?php if ( $lawyer_srcset ) : ?>srcset="<?php echo $lawyer_srcset; ?>" sizes="200px"<?php endif; ?>
							alt="<?php echo esc_attr( $lawyer_alt ); ?>"
							width="400"
							height="533"
							loading="<?php echo $is_above_fold ? 'eager' : 'lazy'; ?>"
							decoding="async"
							class="hero-lawyer-image"
							<?php echo $is_lcp_candidate ? ' fetchpriority="high"' : ''; ?>
						/>
					</div>
					<h2 class="hero-lawyer-name">
						<span itemprop="name"><?php echo esc_html( $lawyer['name'] ); ?></span>
						<span class="hero-lawyer-title" itemprop="jobTitle"><?php echo esc_html( $lawyer['title'] ); ?></span>
					</h2>
					<?php if ( ! empty( $lawyer['items'] ) ) : ?>
						<ul class="hero-lawyer-list" itemprop="description" aria-label="<?php echo esc_attr( $lawyer['name'] . ' 변호사 경력' ); ?>">
							<?php foreach ( $lawyer['items'] as $item ) : ?>
								<li><?php echo esc_html( $item ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="hero-cta hero-cta-in-hero" role="group" aria-label="상담 연락">
			<p class="hero-cta-text"><span class="hero-cta-text-bold">지금 바로 상담 가능</span> 성범죄 사건 상담전화</p>
			<a href="tel:+82-1688-3971" class="hero-cta-phone hero-cta-action" aria-label="상담 전화 걸기 1688-3971" title="1688-3971">
				<span class="hero-cta-action-icon" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
				</span>
				<span class="hero-cta-tel">1688-3971</span>
			</a>
			<a href="<?php echo esc_url( $hero_home_url ); ?>" class="hero-cta-button hero-cta-action" aria-label="홈페이지 바로가기">
				<span class="hero-cta-action-icon" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
				</span>
				<span class="hero-cta-button-text">홈페이지 바로가기 &gt;</span>
			</a>
		</div>
	</div>
	<script type="application/ld+json"><?php echo wp_json_encode( $hero_legal_script ); ?></script>
</section>
