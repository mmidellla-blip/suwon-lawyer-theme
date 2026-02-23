<?php
/**
 * Success Stories section - posts from category "성공사례"
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

/* 전체보기 = 성범죄 성공사례 페이지(page-success-cases.php) */
$archive_url = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/' );
$query       = null;
if ( $cat ) {
	$query = new WP_Query( array(
		'category_name' => $cat->slug,
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
				<h2 id="success-stories-heading" class="success-stories-title"><span class="success-stories-title-accent">성공사례</span> 모음</h2>
				<a href="<?php echo esc_url( $archive_url ); ?>" class="success-stories-cta" title="<?php esc_attr_e( '성공사례 전체 목록 보기', 'della-theme' ); ?>" aria-label="<?php esc_attr_e( '성공사례 전체 목록 보기', 'della-theme' ); ?>">성공사례 전체보기</a>
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
					$thumb  = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
					$img_alt = get_the_title() ? sprintf( /* translators: %s: post title */ __( '성공사례: %s', 'della-theme' ), get_the_title() ) : '';
					$itemlist_items[] = array(
						'@type'    => 'ListItem',
						'position' => $position,
						'url'      => get_permalink(),
						'name'     => get_the_title(),
					);
					?>
					<article class="success-story-card" itemscope itemtype="https://schema.org/Article">
						<link itemprop="url" href="<?php echo esc_url( get_permalink() ); ?>">
						<a href="<?php the_permalink(); ?>" class="success-story-link" title="<?php the_title_attribute( array( 'echo' => false ) ); ?>">
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
								<h3 class="success-story-title" itemprop="headline"><?php the_title(); ?></h3>
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
				'name'            => __( '성공사례 모음', 'della-theme' ),
				'description'     => __( '성범죄 사건 성공사례 목록', 'della-theme' ),
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
