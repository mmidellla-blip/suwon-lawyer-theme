<?php
/**
 * Template Name: 성범죄 대응정보
 * 성범죄 대응정보 게시판 페이지 (검색, 카테고리 사이드바, 글 목록, 페이지네이션)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$paged    = max( 1, get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : ( isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 ) );
$search   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ( isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '' );
$filter_cat = isset( $_GET['cat'] ) ? sanitize_text_field( wp_unslash( $_GET['cat'] ) ) : '';
if ( empty( $filter_cat ) && isset( $_GET['tag'] ) ) {
	$tag_label   = sanitize_text_field( wp_unslash( $_GET['tag'] ) );
	$tag_to_slug = array(
		'강간'     => 'rape',
		'강제추행' => 'sexual_assult',
		'군성범죄' => 'military_sexual_crimes',
		'성매매'   => 'sex_work',
		'불법촬영' => 'spycam_crime',
		'직장내'   => 'workplace',
		'아청법'   => 'achaeng',
	);
	if ( isset( $tag_to_slug[ $tag_label ] ) ) {
		$filter_cat = $tag_to_slug[ $tag_label ];
	}
}

/* 대 카테고리 (성공사례와 동일 slug): slug = WordPress 카테고리 slug */
$sidebar_main_cats = array(
	array( 'slug' => 'rape', 'label' => __( '강간', 'della-theme' ) ),
	array( 'slug' => 'sexual_assult', 'label' => __( '강제추행', 'della-theme' ) ),
	array( 'slug' => 'military_sexual_crimes', 'label' => __( '군성범죄', 'della-theme' ) ),
	array( 'slug' => 'sex_work', 'label' => __( '성매매', 'della-theme' ) ),
	array( 'slug' => 'spycam_crime', 'label' => __( '불법촬영', 'della-theme' ) ),
	array( 'slug' => 'workplace', 'label' => __( '직장내', 'della-theme' ) ),
);
/* WP에서 실제 사용하는 대 카테고리 slug 후보 (테마 slug와 다를 수 있음, 예: sex_work-response_details) */
$main_cat_wp_slug_alternatives = array(
	'rape'                  => array( 'rape-response_details' ),
	'sexual_assult'         => array( 'sexual_assult-response_details' ),
	'military_sexual_crimes' => array( 'military_sexual_crimes-response_details' ),
	'sex_work'              => array( 'sex_work-response_details' ),
	'spycam_crime'          => array( 'spycam_crime-response_details' ),
	'workplace'             => array( 'workplace-response_details' ),
);
/* 소 카테고리 (대별 동일 라벨, slug는 대+소 조합으로 구분) — 표시 순서 */
$sidebar_sub_defs = array(
	array( 'slug' => '법조문', 'label' => __( '법조문, 처벌·양형 기준', 'della-theme' ) ),
	array( 'slug' => '구성요건-핵심-쟁점-강간', 'label' => __( '구성요건, 핵심쟁점', 'della-theme' ) ),
	array( 'slug' => '판례', 'label' => __( '관련 판례', 'della-theme' ) ),
	array( 'slug' => '유형별-사건', 'label' => __( '유형별 사례', 'della-theme' ) ),
	array( 'slug' => '수사-재판-단계별-대응', 'label' => __( '대응가이드', 'della-theme' ) ),
	array( 'slug' => 'faq', 'label' => __( 'FAQ', 'della-theme' ) ),
	array( 'slug' => '최신판례-이슈', 'label' => __( '최신이슈', 'della-theme' ) ),
);
/* 테마 소카테고리 slug → WP 실제 slug 접미사 (강간-faq, 강간-관련판례 등) */
$sub_slug_to_wp_suffix = array(
	'법조문'                   => '법조문',
	'구성요건-핵심-쟁점-강간' => '구성요건',
	'판례'                     => '관련판례',
	'유형별-사건'             => '유형별사례',
	'수사-재판-단계별-대응'   => '대응가이드',
	'faq'                      => 'faq',
	'최신판례-이슈'           => '최신이슈',
);
$topic_tags = $sidebar_main_cats;

/* 모바일: 현재 대 카테고리 (filter_cat에서 추출 또는 첫 번째) */
$current_main_slug = $sidebar_main_cats[0]['slug'];
foreach ( $sidebar_main_cats as $m ) {
	if ( $filter_cat === $m['slug'] || strpos( $filter_cat, $m['slug'] . '-' ) === 0 ) {
		$current_main_slug = $m['slug'];
		break;
	}
}

/* 쿼리: 대응정보(또는 선택한 대/소 카테고리) + 검색어 */
$query_args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 15,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'paged'          => $paged,
);
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
/* 성공사례 카테고리 제외 — 대응정보만 조회 */
$success_cat = get_category_by_slug( '성공사례' );
if ( ! $success_cat ) {
	$success_cat = get_category_by_slug( 'success-cases' );
}
if ( ! $success_cat ) {
	$all_cats = get_categories( array( 'hide_empty' => false ) );
	foreach ( $all_cats as $c ) {
		if ( $c->name === '성공사례' || $c->name === '성공 사례' ) {
			$success_cat = $c;
			break;
		}
	}
}
if ( $success_cat ) {
	$query_args['category__not_in'] = array( (int) $success_cat->term_id );
}
if ( $cat ) {
	$query_args['cat'] = $cat->term_id;
}
if ( $search ) {
	$query_args['s'] = $search;
}
if ( $filter_cat ) {
	$filter_category = null;
	$use_include_children = true;
	$main_slug = $filter_cat;
	$sub_slug = '';
	if ( strpos( $filter_cat, '-' ) !== false ) {
		$main_slug = strtok( $filter_cat, '-' );
		$sub_slug = substr( $filter_cat, strlen( $main_slug ) + 1 );
	}
	// 소카테고리 선택 시: WP slug(강간-faq, 강제추행-faq 등)로 1회 조회 후 해당 term만 사용 — 빠른 쿼리
	if ( $sub_slug !== '' ) {
		$main_label = '';
		foreach ( $sidebar_main_cats as $m ) {
			if ( $m['slug'] === $main_slug ) {
				$main_label = $m['label'];
				break;
			}
		}
		if ( $main_label !== '' ) {
			$wp_suffix = isset( $sub_slug_to_wp_suffix[ $sub_slug ] ) ? $sub_slug_to_wp_suffix[ $sub_slug ] : $sub_slug;
			$wp_sub_slug = $main_label . '-' . $wp_suffix;
			$filter_category = get_category_by_slug( $wp_sub_slug );
			if ( $filter_category ) {
				$use_include_children = false;
			}
		}
	}
	// 소카테고리 미매칭 또는 대 카테고리만 선택: 부모 카테고리로 조회 후 include_children
	if ( $filter_category === null ) {
		$slug_for_query = $main_slug;
		$filter_category = get_category_by_slug( $slug_for_query );
		if ( ! $filter_category ) {
			foreach ( $sidebar_main_cats as $m ) {
				if ( $m['slug'] !== $slug_for_query ) {
					continue;
				}
				$filter_category = get_category_by_slug( $m['label'] );
				if ( ! $filter_category && ! empty( $main_cat_wp_slug_alternatives[ $m['slug'] ] ) ) {
					foreach ( $main_cat_wp_slug_alternatives[ $m['slug'] ] as $alt_slug ) {
						$filter_category = get_category_by_slug( $alt_slug );
						if ( $filter_category ) {
							break;
						}
					}
				}
				if ( ! $filter_category ) {
					$all_cats = get_categories( array( 'hide_empty' => false ) );
					foreach ( $all_cats as $c ) {
						if ( $c->name === $m['label'] || $c->slug === $m['label'] ) {
							$filter_category = $c;
							break 2;
						}
					}
				}
				break;
			}
		}
	}
	if ( $filter_category ) {
		if ( $cat ) {
			unset( $query_args['cat'] );
			$query_args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => array( (int) $cat->term_id ),
				),
				array(
					'taxonomy'         => 'category',
					'field'            => 'term_id',
					'terms'            => array( (int) $filter_category->term_id ),
					'include_children' => $use_include_children,
				),
			);
		} else {
			$query_args['cat'] = $filter_category->term_id;
		}
	}
}

$board_query = new WP_Query( $query_args );
$base_url    = get_permalink();
// 잘못된 페이지 번호(paged) 시 404 처리
if ( $paged > 1 && $board_query->max_num_pages > 0 && $paged > $board_query->max_num_pages ) {
	della_theme_trigger_404();
}
get_header();
?>

<main id="main-content" class="site-main response-board-page" role="main">
	<div class="response-board-top">
		<div class="response-board-top-inner">
			<header class="response-board-header">
				<h1 class="response-board-title"><?php esc_html_e( '성범죄 대응정보', 'della-theme' ); ?></h1>
				<p class="response-board-desc"><?php esc_html_e( '강간·강제추행·군성범죄·불법촬영·성매매·직장내 성희롱 등 법조문, 판례, FAQ, 수사·재판 단계별 대응 가이드.', 'della-theme' ); ?></p>
				<p class="response-board-intro">성범죄 사건은 초기 진술과 증거 보존이 결과에 큰 영향을 미칩니다. 이 페이지는 강제추행, 불법촬영, 아청법, 군성범죄 등 유형별 핵심 쟁점과 법조문·판례·FAQ를 정리해 제공합니다. 또한 경찰 조사, 검찰 송치, 재판 단계별로 확인해야 할 대응 포인트를 안내합니다. 필요한 주제를 선택해 빠르게 확인하세요.</p>
			</header>

			<form class="response-board-search" role="search" method="get" action="<?php echo esc_url( $base_url ); ?>" aria-label="<?php esc_attr_e( '대응정보 검색', 'della-theme' ); ?>">
				<input type="hidden" name="paged" value="1" />
				<?php if ( $filter_cat ) : ?>
					<input type="hidden" name="cat" value="<?php echo esc_attr( $filter_cat ); ?>" />
				<?php endif; ?>
				<label for="response-board-search-scope" class="screen-reader-text"><?php esc_html_e( '검색 범위', 'della-theme' ); ?></label>
				<select id="response-board-search-scope" name="scope" class="response-board-search-scope" aria-label="<?php esc_attr_e( '검색 범위', 'della-theme' ); ?>">
					<option value="title"><?php esc_html_e( '제목', 'della-theme' ); ?></option>
				</select>
				<label for="response-board-search-input" class="screen-reader-text"><?php esc_html_e( '검색어', 'della-theme' ); ?></label>
				<input type="search" id="response-board-search-input" name="q" class="response-board-search-input" placeholder="<?php esc_attr_e( '검색어를 입력해주세요.', 'della-theme' ); ?>" value="<?php echo esc_attr( $search ); ?>" />
				<button type="submit" class="response-board-search-submit" aria-label="<?php esc_attr_e( '검색', 'della-theme' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M16 16l4 4"/></svg>
				</button>
			</form>

			<div class="response-board-tags">
				<?php foreach ( $topic_tags as $tag ) : ?>
					<?php
					$tag_url  = add_query_arg( array( 'tag' => $tag['label'], 'paged' => 1 ), $base_url );
					$is_active = ( $filter_cat === $tag['slug'] || strpos( $filter_cat, $tag['slug'] . '-' ) === 0 );
					?>
					<a href="<?php echo esc_url( $tag_url ); ?>" class="response-board-tag <?php echo $is_active ? 'is-active' : ''; ?>">#<?php echo esc_html( $tag['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="response-board">
		<div class="response-board-body">
			<nav class="response-board-sidebar" aria-label="<?php esc_attr_e( '카테고리', 'della-theme' ); ?>">
				<!-- 모바일: 대·소 카테고리 가로 스크롤 -->
				<div class="response-board-nav-mobile">
					<div class="response-board-nav-main-row" role="tablist" aria-label="<?php esc_attr_e( '대 카테고리', 'della-theme' ); ?>">
						<?php foreach ( $sidebar_main_cats as $main_item ) : ?>
							<?php
							$main_url = add_query_arg( array( 'cat' => $main_item['slug'], 'paged' => 1 ), $base_url );
							$main_active = ( $current_main_slug === $main_item['slug'] );
							?>
							<a href="<?php echo esc_url( $main_url ); ?>" class="response-board-nav-main-tab <?php echo $main_active ? 'is-active' : ''; ?>" role="tab"><?php echo esc_html( $main_item['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
					<div class="response-board-nav-sub-row" role="tablist" aria-label="<?php esc_attr_e( '소 카테고리', 'della-theme' ); ?>">
						<?php foreach ( $sidebar_sub_defs as $sub_def ) : ?>
							<?php
							$compound_slug = $current_main_slug . '-' . $sub_def['slug'];
							$sub_url = add_query_arg( array( 'cat' => $compound_slug, 'paged' => 1 ), $base_url );
							$sub_active = ( $filter_cat === $compound_slug );
							?>
							<a href="<?php echo esc_url( $sub_url ); ?>" class="response-board-nav-sub-tab <?php echo $sub_active ? 'is-active' : ''; ?>" role="tab"><?php echo esc_html( $sub_def['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
				<ul class="response-board-nav-list">
					<?php
					foreach ( $sidebar_main_cats as $index => $main_item ) :
						$main_id = 'response-board-main-' . sanitize_title( $main_item['slug'] );
						$main_sub_slugs = array();
						foreach ( $sidebar_sub_defs as $sub_def ) {
							$main_sub_slugs[] = $main_item['slug'] . '-' . $sub_def['slug'];
						}
						$is_open = empty( $filter_cat ) ? false : ( $filter_cat === $main_item['slug'] || in_array( $filter_cat, $main_sub_slugs, true ) );
						$main_link_url = add_query_arg( array( 'cat' => $main_item['slug'], 'paged' => 1 ), $base_url );
						?>
						<li class="response-board-nav-group <?php echo $is_open ? 'is-open' : ''; ?>" data-group="<?php echo esc_attr( $main_item['slug'] ); ?>">
							<a href="<?php echo esc_url( $main_link_url ); ?>" class="response-board-nav-main" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $main_id ); ?>" id="<?php echo esc_attr( $main_id ); ?>-btn">
								<span class="response-board-nav-main-label"><?php echo esc_html( $main_item['label'] ); ?></span>
								<span class="response-board-nav-main-icon" aria-hidden="true"></span>
							</a>
							<div class="response-board-nav-sublist-wrap" role="region" aria-label="<?php echo esc_attr( $main_item['label'] ); ?> 서브메뉴">
								<ul id="<?php echo esc_attr( $main_id ); ?>" class="response-board-nav-sublist" role="list" aria-hidden="<?php echo $is_open ? 'false' : 'true'; ?>">
								<?php foreach ( $sidebar_sub_defs as $sub_def ) : ?>
									<?php
									$compound_slug = $main_item['slug'] . '-' . $sub_def['slug'];
									$sub_url = add_query_arg( array( 'cat' => $compound_slug, 'paged' => 1 ), $base_url );
									$sub_active = ( $filter_cat === $compound_slug );
									?>
									<li role="listitem">
										<a href="<?php echo esc_url( $sub_url ); ?>" class="response-board-nav-link is-sub <?php echo $sub_active ? 'is-active' : ''; ?>"><?php echo esc_html( $sub_def['label'] ); ?></a>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<section class="response-board-main" aria-label="<?php esc_attr_e( '대응정보 글 목록', 'della-theme' ); ?>">
				<ul class="response-board-list" role="list">
					<?php
					if ( $board_query->have_posts() ) :
						while ( $board_query->have_posts() ) :
							$board_query->the_post();
							$post_cats = get_the_category();
							$post_cat_name = ! empty( $post_cats[0] ) ? $post_cats[0]->name : '';
							?>
							<?php
							$item_title = get_post_meta( get_the_ID(), 'della_info_seo_title', true );
							if ( ! $item_title ) {
								$item_title = get_the_title();
							}
							?>
							<li class="response-board-item" role="listitem">
								<a href="<?php the_permalink(); ?>" class="response-board-item-link" title="<?php echo esc_attr( $item_title ); ?>">
									<?php if ( $post_cat_name ) : ?>
										<span class="response-board-item-cat">[<?php echo esc_html( $post_cat_name ); ?>]</span>
									<?php endif; ?>
									<span class="response-board-item-title"><?php echo esc_html( $item_title ); ?></span>
									<time class="response-board-item-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
								</a>
							</li>
						<?php
						endwhile;
					else :
						?>
						<li class="response-board-empty"><?php echo $search ? esc_html__( '검색어에 맞는 글이 없습니다.', 'della-theme' ) : esc_html__( '등록된 글이 없습니다.', 'della-theme' ); ?></li>
					<?php endif; ?>
				</ul>

				<?php
				$total = $board_query->max_num_pages;
				if ( $total > 1 ) :
					$current = $paged;
					$pagination_base = $base_url;
					if ( $search ) {
						$pagination_base = add_query_arg( 'q', $search, $pagination_base );
					}
					if ( $filter_cat ) {
						$pagination_base = add_query_arg( 'cat', $filter_cat, $pagination_base );
					}
					$pagination_base = add_query_arg( 'paged', '%#%', $pagination_base );
					$links = paginate_links( array(
						'current'   => $current,
						'total'     => $total,
						'prev_text' => '&laquo;',
						'next_text' => '&raquo;',
						'type'      => 'array',
						'base'      => $pagination_base,
					) );
					if ( ! empty( $links ) ) :
						?>
						<nav class="response-board-pagination" aria-label="<?php esc_attr_e( '목록 페이지 내비게이션', 'della-theme' ); ?>">
							<ul class="response-board-pagination-list">
								<?php foreach ( $links as $link ) : ?>
									<li><?php echo $link; ?></li>
								<?php endforeach; ?>
							</ul>
						</nav>
					<?php endif; ?>
				<?php endif; ?>
				<div class="response-board-internal-links internal-links internal-links-ctr">
					<p class="internal-links-label">다음에 보면 좋은 안내</p>
					<div class="internal-links-actions">
						<a href="<?php echo esc_url( function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/success-cases/' ) ); ?>">성범죄 성공사례 보기</a>
						<a href="<?php echo esc_url( function_exists( 'della_theme_lawyers_page_url' ) ? della_theme_lawyers_page_url() : home_url( '/lawyers/' ) ); ?>">성범죄 전문변호사 소개</a>
						<a href="<?php echo esc_url( home_url( '/#consultation-cta' ) ); ?>">상담 신청</a>
					</div>
				</div>
			</section>
		</div>
	</div>
</main>

<?php
wp_reset_postdata();
?>
<script>
(function() {
	var sidebar = document.querySelector('.response-board-sidebar');
	if (!sidebar) return;
	sidebar.addEventListener('click', function(e) {
		var btn = e.target.closest('.response-board-nav-main');
		if (!btn) return;
		if (btn.tagName === 'A') return;
		e.preventDefault();
		var expanded = btn.getAttribute('aria-expanded') === 'true';
		var group = btn.closest('.response-board-nav-group');
		var targetId = btn.getAttribute('aria-controls');
		var panel = targetId ? document.getElementById(targetId) : null;
		if (group) {
			group.classList.toggle('is-open', !expanded);
		}
		btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
		if (panel) {
			panel.setAttribute('aria-hidden', expanded ? 'true' : 'false');
		}
	});
})();
</script>
<?php
get_footer();
