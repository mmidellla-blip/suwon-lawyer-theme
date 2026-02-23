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

// 목록 페이지
$lawyers = della_theme_get_lawyers();
get_header();
?>

<main id="main-content" class="site-main lawyer-list-page" role="main">

	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<article class="lawyer-list-article">
		<header class="lawyer-list-header">
			<h1 class="lawyer-list-title"><?php esc_html_e( '성범죄 전문 변호사', 'della-theme' ); ?></h1>
			<p class="lawyer-list-desc"><?php echo esc_html( get_bloginfo( 'name' ) ); ?> <?php esc_html_e( '수원 성범죄 전문 변호사 팀을 소개합니다.', 'della-theme' ); ?></p>
			<p class="lawyer-list-intro"><?php esc_html_e( '강간·강제추행·불법촬영·디지털성범죄 등 성범죄 사건의 초기 대응부터 재판까지, 전문 변호사가 함께합니다.', 'della-theme' ); ?></p>
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
					$img_src    = della_theme_lawyer_image_url( $lawyer['image'], $img_base, $img_dir );
					$img_srcset = della_theme_lawyer_image_srcset( $lawyer['image'], $img_base, $img_dir );
					$img_alt    = $lawyer['name'] . ' ' . $lawyer['title'] . ' ' . __( '프로필 사진', 'della-theme' );
					?>
					<article class="lawyer-list-card" itemscope itemtype="https://schema.org/Person">
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
									<ul class="lawyer-list-card-list" aria-label="<?php echo esc_attr( $lawyer['name'] . ' ' . __( '변호사 경력', 'della-theme' ) ); ?>">
										<?php foreach ( $lawyer['items'] as $item ) : ?>
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
	</article>

</main>

<?php get_footer(); ?>
