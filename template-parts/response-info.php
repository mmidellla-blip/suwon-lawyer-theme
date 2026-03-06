<?php
/**
 * Response Information (대응 정보) section - SEO-friendly card list
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cat = get_category_by_slug( '대응정보' );
if ( ! $cat ) {
	$cat = get_category_by_slug( 'response-info' );
}
if ( ! $cat ) {
	$cats = get_categories( array( 'hide_empty' => false ) );
	foreach ( $cats as $c ) {
		if ( $c->name === '대응 정보' || $c->name === '대응정보' ) {
			$cat = $c;
			break;
		}
	}
}

$query = null;
if ( $cat ) {
	$query = new WP_Query( array(
		'category_name'  => $cat->slug,
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	) );
}
if ( ! $query || ! $query->have_posts() ) {
	$query = new WP_Query( array(
		'post_type'      => 'post',
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	) );
}

$response_info_bg  = content_url( 'uploads/2026/02/response-info-bg.webp' );
$info_url          = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );
$case_url          = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/' );

// 카테고리 slug → 표시용 태그 (성공사례, 대응정보, 처벌기준 통일)
$response_info_tag_map = array(
	'성공사례'     => '성공사례',
	'success'      => '성공사례',
	'성공'         => '성공사례',
	'처벌기준'     => '처벌기준',
	'penalty'      => '처벌기준',
	'양형'         => '처벌기준',
	'강제추행'     => '대응정보',
	'sexual_assult' => '대응정보',
	'rape'         => '대응정보',
	'카메라촬영'   => '대응정보',
	'spycam'       => '대응정보',
	'불법촬영'     => '대응정보',
	'아청법'       => '대응정보',
	'대응정보'     => '대응정보',
	'response-info' => '대응정보',
);

$list_items_schema = array();
if ( $query && $query->have_posts() ) {
	$position = 0;
	while ( $query->have_posts() ) {
		$query->the_post();
		$position++;
		$list_items_schema[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'url'      => get_permalink(),
			'name'     => get_the_title(),
		);
	}
	$query->rewind_posts();
}
?>
<section id="response-info" class="response-info" aria-labelledby="response-info-heading" style="background-image: url('<?php echo esc_url( $response_info_bg ); ?>');">
	<div class="response-info-inner">
		<header class="response-info-header">
			<div class="response-info-header-text">
				<h2 id="response-info-heading" class="response-info-title section-title">성범죄 대응 정보와 성공사례</h2>
				<p class="response-info-desc section-desc">사건 유형별 대응 방법, 실제 진행 흐름, 처분 결과에 관한 정보를 함께 확인할 수 있도록 정리했습니다.</p>
			</div>
			<a href="<?php echo esc_url( $info_url ); ?>" class="response-info-cta" aria-label="<?php esc_attr_e( '대응정보·성공사례 전체 보기', 'della-theme' ); ?>"><?php esc_html_e( '대응정보 전체 보기', 'della-theme' ); ?> &gt;</a>
		</header>

		<?php if ( $query && $query->have_posts() ) : ?>
		<?php
		if ( ! empty( $list_items_schema ) ) {
			$itemlist_schema = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'ItemList',
				'name'            => '성범죄 대응 정보와 성공사례',
				'description'     => '사건 유형별 대응 방법, 진행 흐름, 처분 결과. 수원 성범죄 전문변호사 대응정보·성공사례 허브 확장 영역.',
				'numberOfItems'   => count( $list_items_schema ),
				'itemListElement' => $list_items_schema,
			);
			echo '<script type="application/ld+json">' . wp_json_encode( $itemlist_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		}
		?>
		<div class="response-info-list-wrap" role="region" aria-label="<?php esc_attr_e( '대응 정보 카드 목록', 'della-theme' ); ?>">
			<ul class="response-info-list" id="response-info-list" role="list" aria-label="<?php esc_attr_e( '대응 정보 글 목록', 'della-theme' ); ?>">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$post_id     = get_the_ID();
					$card_title  = get_post_meta( $post_id, 'della_info_seo_title', true ) ? get_post_meta( $post_id, 'della_info_seo_title', true ) : get_the_title();
					$post_cats   = get_the_category();
					$tag_label   = '대응정보';
					if ( ! empty( $post_cats[0]->slug ) && isset( $response_info_tag_map[ $post_cats[0]->slug ] ) ) {
						$tag_label = $response_info_tag_map[ $post_cats[0]->slug ];
					} else {
						foreach ( $post_cats as $pc ) {
							if ( isset( $response_info_tag_map[ $pc->slug ] ) ) {
								$tag_label = $response_info_tag_map[ $pc->slug ];
								break;
							}
						}
					}
					$card_url = get_permalink();
					?>
					<li class="response-info-card" role="listitem">
						<article class="response-info-card-article">
							<a href="<?php echo esc_url( $card_url ); ?>" class="response-info-card-link" aria-label="<?php echo esc_attr( $card_title . ' 보기' ); ?>">
								<span class="response-info-card-tag" aria-hidden="true"><?php echo esc_html( $tag_label ); ?></span>
								<h3 class="response-info-card-title"><?php echo esc_html( $card_title ); ?></h3>
								<?php if ( has_excerpt() ) : ?>
									<p class="response-info-card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
								<?php endif; ?>
								<time class="response-info-card-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
							</a>
						</article>
					</li>
				<?php endwhile; ?>
			</ul>
		</div>
		<div class="info-links">
			<a href="<?php echo esc_url( $case_url ); ?>">성범죄 성공사례 보기</a>
			<span class="dot" aria-hidden="true">·</span>
			<a href="<?php echo esc_url( $info_url ); ?>">성범죄 대응정보</a>
		</div>
		<?php else : ?>
		<p class="response-info-empty"><?php esc_html_e( '등록된 대응 정보가 없습니다.', 'della-theme' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
if ( $query ) {
	wp_reset_postdata();
}
