<?php
/**
 * Template Name: 성범죄 성공사례
 * 성범죄 성공사례 게시판 페이지 (검색, 대 카테고리 사이드바만, 글 목록, 페이지네이션)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$paged    = max( 1, get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : ( isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 ) );
$search_raw = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ( isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '' );
$search   = trim( $search_raw );
$filter_cat = isset( $_GET['cat'] ) ? sanitize_text_field( wp_unslash( $_GET['cat'] ) ) : '';

/* 검색어 없이 검색 버튼 눌렀을 때: 전체 결과 페이지로 이동 (q 제거) */
if ( isset( $_GET['q'] ) && $search === '' ) {
	$redirect_url = get_permalink();
	if ( $filter_cat ) {
		$redirect_url = add_query_arg( 'cat', $filter_cat, $redirect_url );
	}
	if ( $paged > 1 ) {
		$redirect_url = add_query_arg( 'paged', $paged, $redirect_url );
	}
	wp_safe_redirect( $redirect_url, 302 );
	exit;
}

/* 대 카테고리 (성공사례 하위): slug = WordPress 카테고리 slug, label = 표시명 */
$sidebar_main_cats = array(
	array( 'slug' => 'rape', 'label' => __( '강간', 'della-theme' ) ),
	array( 'slug' => 'sexual_assult', 'label' => __( '강제추행', 'della-theme' ) ),
	array( 'slug' => 'military_sexual_crimes', 'label' => __( '군성범죄', 'della-theme' ) ),
	array( 'slug' => 'sex_work', 'label' => __( '성매매', 'della-theme' ) ),
	array( 'slug' => 'spycam_crime', 'label' => __( '불법촬영', 'della-theme' ) ),
	array( 'slug' => 'workplace', 'label' => __( '직장내', 'della-theme' ) ),
);
$topic_tags = $sidebar_main_cats;

/* 이 6개 카테고리에 속한 글만 노출 (카테고리 글이 아니면 안 나오게) */
$allowed_cat_ids   = array();
$main_cat_id_to_label = array();
foreach ( $sidebar_main_cats as $item ) {
	$term = get_category_by_slug( $item['slug'] );
	if ( $term ) {
		$allowed_cat_ids[] = $term->term_id;
		$main_cat_id_to_label[ (int) $term->term_id ] = $item['label'];
	}
}

$query_args = array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 4,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'paged'          => $paged,
);
if ( $search ) {
	$query_args['s'] = $search;
}
if ( $filter_cat ) {
	$filter_term = get_category_by_slug( $filter_cat );
	if ( $filter_term && ! empty( $allowed_cat_ids ) && in_array( (int) $filter_term->term_id, $allowed_cat_ids, true ) ) {
		$query_args['cat'] = $filter_term->term_id;
	} elseif ( ! empty( $allowed_cat_ids ) ) {
		$query_args['category__in'] = $allowed_cat_ids;
	} else {
		$query_args['category__in'] = array( 0 );
	}
} else {
	if ( ! empty( $allowed_cat_ids ) ) {
		$query_args['category__in'] = $allowed_cat_ids;
	} else {
		$query_args['category__in'] = array( 0 );
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

<main id="main-content" class="site-main response-board-page success-cases-page" role="main">
	<div class="response-board-top">
		<div class="response-board-top-inner">
			<header class="response-board-header">
				<h1 class="response-board-title"><?php esc_html_e( '성범죄 성공사례', 'della-theme' ); ?></h1>
				<p class="response-board-desc"><?php esc_html_e( '성범죄 성공사례에 대해서 한줄 요약 설명', 'della-theme' ); ?></p>
			</header>

			<form class="response-board-search" role="search" method="get" action="<?php echo esc_url( $base_url ); ?>" aria-label="<?php esc_attr_e( '성공사례 검색', 'della-theme' ); ?>">
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
					$tag_url = add_query_arg( array( 'cat' => $tag['slug'], 'paged' => 1 ), $base_url );
					$is_active = ( $filter_cat === $tag['slug'] );
					?>
					<a href="<?php echo esc_url( $tag_url ); ?>" class="response-board-tag <?php echo $is_active ? 'is-active' : ''; ?>">#<?php echo esc_html( $tag['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<div class="response-board">
		<div class="response-board-body">
			<nav class="response-board-sidebar" aria-label="<?php esc_attr_e( '카테고리', 'della-theme' ); ?>">
				<!-- 모바일: 대 카테고리만 가로 스크롤 -->
				<div class="response-board-nav-mobile">
					<div class="response-board-nav-main-row" role="tablist" aria-label="<?php esc_attr_e( '대 카테고리', 'della-theme' ); ?>">
						<?php foreach ( $sidebar_main_cats as $main_item ) : ?>
							<?php
							$main_url = add_query_arg( array( 'cat' => $main_item['slug'], 'paged' => 1 ), $base_url );
							$main_active = ( $filter_cat === $main_item['slug'] );
							?>
							<a href="<?php echo esc_url( $main_url ); ?>" class="response-board-nav-main-tab <?php echo $main_active ? 'is-active' : ''; ?>" role="tab"><?php echo esc_html( $main_item['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
				<!-- 데스크톱: 대 카테고리만 리스트 (서브 없음) -->
				<ul class="response-board-nav-list success-cases-nav-list">
					<?php foreach ( $sidebar_main_cats as $main_item ) : ?>
						<?php
						$main_url = add_query_arg( array( 'cat' => $main_item['slug'], 'paged' => 1 ), $base_url );
						$main_active = ( $filter_cat === $main_item['slug'] );
						?>
						<li>
							<a href="<?php echo esc_url( $main_url ); ?>" class="response-board-nav-link <?php echo $main_active ? 'is-active' : ''; ?>"><?php echo esc_html( $main_item['label'] ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<section class="response-board-main" aria-label="<?php esc_attr_e( '성공사례 글 목록', 'della-theme' ); ?>">
				<ul class="response-board-list success-cases-card-list" role="list">
					<?php
					if ( $board_query->have_posts() ) :
						while ( $board_query->have_posts() ) :
							$board_query->the_post();
							$post_cats   = get_the_category();
							$post_cat_name = '';
							foreach ( $post_cats as $pc ) {
								if ( isset( $main_cat_id_to_label[ (int) $pc->term_id ] ) ) {
									$post_cat_name = $main_cat_id_to_label[ (int) $pc->term_id ];
									break;
								}
							}
							if ( ! $post_cat_name ) {
								foreach ( $post_cats as $pc ) {
									foreach ( $sidebar_main_cats as $main_item ) {
										if ( isset( $pc->slug ) && $pc->slug === $main_item['slug'] ) {
											$post_cat_name = $main_item['label'];
											break 2;
										}
									}
								}
							}
							if ( ! $post_cat_name && ! empty( $post_cats ) ) {
								$parent_slugs = array( '성공사례', '성공 사례', 'success-cases' );
								foreach ( $post_cats as $pc ) {
									$s = isset( $pc->slug ) ? $pc->slug : '';
									if ( ! in_array( $s, $parent_slugs, true ) ) {
										$post_cat_name = $pc->name;
										break;
									}
								}
							}
							if ( ! $post_cat_name && ! empty( $post_cats[0] ) ) {
								$post_cat_name = $post_cats[0]->name;
							}
							$thumb       = get_the_post_thumbnail_url( null, 'medium_large' );
							$img_alt     = get_the_title() ? sprintf( /* translators: %s: post title */ __( '성공사례: %s', 'della-theme' ), get_the_title() ) : '';
							?>
							<li class="success-cases-card" role="listitem">
								<a href="<?php the_permalink(); ?>" class="success-cases-card-link">
									<div class="success-cases-card-doc">
										<?php if ( $thumb ) : ?>
											<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" class="success-cases-card-doc-img" loading="lazy" width="400" height="520">
										<?php else : ?>
											<div class="success-cases-card-doc-placeholder" aria-hidden="true">
												<span class="success-cases-card-doc-placeholder-label"><?php esc_html_e( 'Document', 'della-theme' ); ?></span>
											</div>
										<?php endif; ?>
									</div>
									<div class="success-cases-card-body">
										<?php if ( $post_cat_name ) : ?>
											<span class="success-cases-card-cat">[<?php echo esc_html( $post_cat_name ); ?>]</span>
										<?php endif; ?>
										<h3 class="success-cases-card-title"><?php the_title(); ?></h3>
										<?php if ( has_excerpt() ) : ?>
											<p class="success-cases-card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
										<?php endif; ?>
									</div>
								</a>
							</li>
						<?php
						endwhile;
					else :
						?>
						<li class="response-board-empty"><?php echo $search ? esc_html__( '검색어에 맞는 성공사례가 없습니다.', 'della-theme' ) : esc_html__( '등록된 글이 없습니다.', 'della-theme' ); ?></li>
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
			</section>
		</div>
	</div>
</main>

<?php
wp_reset_postdata();
get_footer();
