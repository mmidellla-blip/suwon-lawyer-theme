<?php
/**
 * Template Name: 성범죄 전문 변호사
 * 변호사 리스트 카드 또는 상세 프로필 (URL: /lawyers/ , /lawyers/{slug}/)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lawyer_slug = get_query_var( 'lawyer_slug' );
$upload_dir  = wp_upload_dir();
$img_base    = $upload_dir['baseurl'] . '/2026/02';
$img_dir     = $upload_dir['basedir'] . '/2026/02';

// 상세 프로필: /lawyers/{slug}/ 로 들어온 경우 — 잘못된 slug는 404 처리
if ( $lawyer_slug ) {
	$lawyer = della_theme_get_lawyer_by_slug( $lawyer_slug );
	if ( ! $lawyer ) {
		della_theme_trigger_404();
	}
	get_header();
	?>
	<main id="main-content" class="site-main lawyer-profile-page" role="main">
		<div class="lawyer-profile-breadcrumb-wrap" aria-hidden="true"><?php get_template_part( 'template-parts/breadcrumb' ); ?></div>
		<div class="lawyer-profile-page-inner">
			<?php get_template_part( 'template-parts/lawyer-profile', null, array( 'lawyer' => $lawyer, 'img_base' => $img_base, 'img_dir' => $img_dir ) ); ?>
		</div>
		<?php get_template_part( 'template-parts/lawyer-profile-detail', null, array( 'lawyer' => $lawyer ) ); ?>
		<p class="lawyer-profile-back-wrap">
			<a href="<?php echo esc_url( della_theme_lawyers_page_url() ); ?>" class="lawyer-profile-back"><?php esc_html_e( '← 성범죄 전문 변호사 목록', 'della-theme' ); ?></a>
		</p>
	</main>
	<?php
	get_footer();
	return;
}

// 목록 페이지: 카드용 이미지는 성범죄 전용 프로필 이미지 사용 (상세 페이지는 기존 image_profile 유지)
$lawyers       = della_theme_get_lawyers();
$list_img_map  = array(
	'dongju-park-dongjin' => 'park-dongjin-sexcrime-lawyer-profile.webp',
	'dongju-leesewhan'    => 'lee-sehwan-sexcrime-lawyer-profile.webp',
	'dongju-jo-wonjin'    => 'cho-jowonjin-sexcrime-profile.webp',
	'dongju-kim-yunseo'   => 'kim-yunseo-sexcrime-profile.webp',
	'dongju-oh-seojin'    => 'oh-seojin-sexcrime-profile.webp',
	'dongju-isejin'       => 'lee-sejin-sexcrime-profile.webp',
);
get_header();
?>

<main id="main-content" class="site-main lawyer-list-page" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<article class="lawyer-list-article">
		<header class="lawyer-list-header">
			<h1 class="lawyer-list-title">수원 성범죄 전문변호사<br>강제추행·카메라촬영·디지털성범죄 대응 변호사</h1>
			<p class="lawyer-list-desc"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> 수원 성범죄 전문 변호사 팀 소개</p>
			<p class="lawyer-list-intro">강제추행·카메라촬영·디지털성범죄·아청법 등 성범죄 사건을 전문 대응합니다. 경찰 조사부터 검찰·재판까지 체계적으로 전략을 수립해 의뢰인 권리를 보호합니다.</p>
			<p class="lawyer-list-intro">초기 대응이 결과를 좌우합니다. 수원 성범죄 전문변호사 팀이 사건에 맞는 맞춤 전략으로 함께합니다.</p>
		</header>

		<section class="lawyer-list-section" aria-labelledby="lawyer-list-heading">
			<h2 id="lawyer-list-heading" class="screen-reader-text"><?php esc_html_e( '변호사 프로필 목록', 'della-theme' ); ?></h2>
			<div class="lawyer-list-grid">
				<?php foreach ( $lawyers as $lawyer ) : ?>
					<?php
					$profile_url = della_theme_lawyer_profile_url( isset( $lawyer['slug'] ) ? $lawyer['slug'] : '' );
					if ( ! $profile_url ) {
						continue;
					}
					$list_image = isset( $lawyer['slug'] ) && isset( $list_img_map[ $lawyer['slug'] ] ) ? $list_img_map[ $lawyer['slug'] ] : $lawyer['image'];
					$img_src    = della_theme_lawyer_image_url( $list_image, $img_base, $img_dir );
					$img_srcset = della_theme_lawyer_image_srcset( $list_image, $img_base, $img_dir );
					$img_alt    = '수원 성범죄 전문변호사 ' . $lawyer['name'];
					?>
					<article class="lawyer-list-card" itemscope itemtype="https://schema.org/Person">
						<h2 class="screen-reader-text">수원 성범죄 전문변호사 <?php echo esc_html( $lawyer['name'] ); ?></h2>
						<a href="<?php echo esc_url( $profile_url ); ?>" class="lawyer-list-card-link">
							<div class="lawyer-list-card-image-wrap">
								<img
									src="<?php echo esc_url( $img_src ); ?>"
									<?php if ( $img_srcset ) : ?>srcset="<?php echo $img_srcset; ?>" sizes="(min-width: 900px) 33.333vw, (min-width: 640px) 50vw, 100vw"<?php endif; ?>
									alt="<?php echo esc_attr( $img_alt ); ?>"
									width="400"
									height="533"
									loading="lazy"
									decoding="async"
									class="lawyer-list-card-image"
								/>
							</div>
							<div class="lawyer-list-card-body">
								<h3 class="lawyer-list-card-name">
									<span itemprop="name"><?php echo esc_html( $lawyer['name'] ); ?></span>
									<span class="lawyer-list-card-title" itemprop="jobTitle"><?php echo esc_html( $lawyer['title'] ); ?></span>
								</h3>
								<?php if ( ! empty( $lawyer['items'] ) ) : ?>
									<?php $list_items = array_slice( $lawyer['items'], 0, 3 ); ?>
									<ul class="lawyer-list-card-list" aria-label="<?php echo esc_attr( $lawyer['name'] . ' ' . __( '변호사 경력', 'della-theme' ) ); ?>">
										<?php foreach ( $list_items as $item ) : ?>
											<li><?php echo esc_html( $item ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<section class="lawyer-list-areas lawyer-list-block" aria-labelledby="lawyer-list-areas-heading">
			<h2 id="lawyer-list-areas-heading" class="lawyer-list-block-label">주요 대응 분야</h2>
			<ul class="lawyer-list-areas-list">
				<li>강제추행</li>
				<li>카메라촬영·불법촬영</li>
				<li>디지털성범죄</li>
				<li>아청법</li>
				<li>경찰조사 대응</li>
			</ul>
		</section>

		<div class="internal-links internal-links-ctr">
			<p class="internal-links-label">다음에 보면 좋은 안내</p>
			<div class="internal-links-actions">
				<a href="<?php echo esc_url( function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/success-cases/' ) ); ?>">성공사례 보기</a>
				<a href="<?php echo esc_url( function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/response-info/' ) ); ?>">대응정보</a>
				<a href="<?php echo esc_url( function_exists( 'della_theme_consultation_url' ) ? della_theme_consultation_url() : 'https://sexcrimecenter-dongju.com/bbs/board.php?bo_table=online&me_code=6010' ); ?>">상담 신청</a>
			</div>
		</div>
	</article>

</main>

<?php
$lawyers_page_schema = array(
	'@context'    => 'https://schema.org',
	'@type'       => 'LegalService',
	'name'        => '법무법인 동주 성범죄 전문변호사',
	'areaServed'  => 'Suwon',
	'serviceType' => 'Criminal Defense Lawyer',
	'description' => '수원 성범죄 전문변호사 팀이 강제추행, 카메라촬영, 디지털성범죄 사건을 대응합니다.',
);
?>
<script type="application/ld+json"><?php echo wp_json_encode( $lawyers_page_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
<?php get_footer(); ?>
