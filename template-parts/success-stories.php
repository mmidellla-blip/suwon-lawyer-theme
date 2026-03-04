<?php
/**
 * Success Stories section - posts from category "성공사례" (SEO-friendly)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cat = get_category_by_slug( '성공사례' );
if ( ! $cat ) {
	$cat = get_category_by_slug( 'success-case' );
}
if ( ! $cat ) {
	$cats = get_categories( array( 'hide_empty' => false ) );
	foreach ( $cats as $c ) {
		if ( $c->name === '성공사례' ) {
			$cat = $c;
			break;
		}
	}
}

$archive_url = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/' );
$info_url   = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );
$query      = null;
if ( $cat ) {
	$query = new WP_Query( array(
		'category_name'  => $cat->slug,
		'posts_per_page' => 12,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	) );
}
?>

<section id="success-stories" class="success-stories" aria-labelledby="success-stories-heading">
	<div class="success-stories-inner">
		<header class="success-stories-header">
			<div class="success-stories-header-left">
				<h2 id="success-stories-heading" class="success-stories-title section-title">성범죄 성공사례</h2>
				<p class="section-desc">강제추행, 불법촬영, 아청법 사건에서 무혐의·기소유예 등 결과를 이끈 실제 사건 사례입니다.</p>
				<div class="case-links">
					<a href="<?php echo esc_url( $archive_url ); ?>">성범죄 성공사례</a>
					<span class="dot" aria-hidden="true">·</span>
					<a href="<?php echo esc_url( $info_url ); ?>">성범죄 대응정보</a>
					<span class="dot" aria-hidden="true">·</span>
					<a href="<?php echo esc_url( add_query_arg( array( 'tag' => '강제추행', 'paged' => 1 ), $info_url ) ); ?>">강제추행 대응 방법 보기</a>
					<span class="dot" aria-hidden="true">·</span>
					<a href="<?php echo esc_url( add_query_arg( array( 'tag' => '불법촬영', 'paged' => 1 ), $info_url ) ); ?>">불법촬영 처벌 기준 보기</a>
				</div>
				<a href="<?php echo esc_url( $archive_url ); ?>" class="success-stories-cta case-btn" title="<?php esc_attr_e( '성공사례 전체 목록 보기', 'della-theme' ); ?>" aria-label="<?php esc_attr_e( '성공사례 전체 목록 보기', 'della-theme' ); ?>">성공사례 전체보기 →</a>
			</div>
			<?php if ( $query && $query->have_posts() ) : ?>
			<div class="success-stories-nav-wrap" aria-hidden="true">
				<button type="button" class="success-stories-nav success-stories-prev" id="success-stories-prev" aria-label="<?php esc_attr_e( '이전 성공사례', 'della-theme' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button type="button" class="success-stories-nav success-stories-next" id="success-stories-next" aria-label="<?php esc_attr_e( '다음 성공사례', 'della-theme' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
			</div>
			<?php endif; ?>
		</header>

		<?php if ( $query && $query->have_posts() ) : ?>
		<div class="success-stories-carousel-wrap">
			<div class="success-stories-carousel" id="success-stories-carousel" role="region" aria-roledescription="carousel" aria-label="<?php esc_attr_e( '성공사례 목록', 'della-theme' ); ?>">
				<?php
				$itemlist_items = array();
				$position       = 0;
				while ( $query->have_posts() ) :
					$query->the_post();
					$position++;
					$post_id   = get_the_ID();
					$thumb     = get_the_post_thumbnail_url( $post_id, 'medium_large' );
					$case_type   = get_post_meta( $post_id, 'della_case_type', true );
					$case_result = get_post_meta( $post_id, 'della_case_result', true );
					$seo_title   = get_post_meta( $post_id, 'della_case_seo_title', true );
					if ( $case_type && $case_result ) {
						$img_alt = '수원 ' . $case_type . ' ' . $case_result . ' 성공사례 판결문';
					} else {
						$img_alt = '수원 성범죄 성공사례 판결문';
					}
					if ( $seo_title ) {
						$card_title = $seo_title;
					} elseif ( $case_type && $case_result ) {
						$card_title = $case_type . ' ' . $case_result . ' 받은 사례';
					} else {
						$card_title = get_the_title();
					}
					$itemlist_items[] = array(
						'@type'    => 'ListItem',
						'position' => $position,
						'url'      => get_permalink(),
						'name'     => $card_title,
					);
					?>
					<article class="success-story-card" itemscope itemtype="https://schema.org/Article">
						<link itemprop="url" href="<?php echo esc_url( get_permalink() ); ?>">
						<a href="<?php echo esc_url( get_permalink() ); ?>" class="success-story-link" title="<?php echo esc_attr( $card_title ); ?>">
							<div class="success-story-doc">
								<?php if ( $thumb ) : ?>
									<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" class="success-story-doc-img" loading="lazy" width="280" height="360" itemprop="image">
								<?php else : ?>
									<div class="success-story-doc-placeholder" aria-hidden="true">
										<span class="success-story-doc-placeholder-label"><?php esc_html_e( 'Document', 'della-theme' ); ?></span>
									</div>
								<?php endif; ?>
							</div>
							<div class="success-story-body">
								<h3 class="success-story-title" itemprop="headline"><?php echo esc_html( $card_title ); ?></h3>
								<?php if ( has_excerpt() ) : ?>
									<p class="success-story-excerpt" itemprop="description"><?php echo esc_html( get_the_excerpt() ); ?></p>
								<?php endif; ?>
							</div>
						</a>
					</article>
				<?php endwhile; ?>
			</div>
		</div>
		<?php
		if ( ! empty( $itemlist_items ) ) {
			$itemlist_schema = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'ItemList',
				'name'            => '수원 성범죄 성공사례',
				'description'     => '수원 성범죄 전문변호사가 해결한 강제추행·카메라촬영·아청법 사건의 실제 결과',
				'numberOfItems'   => count( $itemlist_items ),
				'itemListElement' => $itemlist_items,
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $itemlist_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		}
		?>
		<?php else : ?>
		<p class="success-stories-empty"><?php esc_html_e( '등록된 성공사례가 없습니다. 글에 카테고리 "성공사례"를 지정해 주세요.', 'della-theme' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
if ( $query ) {
	wp_reset_postdata();
}
