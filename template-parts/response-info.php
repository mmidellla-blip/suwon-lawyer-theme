<?php
/**
 * Response Information (대응 정보) section - card list
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

$response_info_bg = content_url( 'uploads/2026/02/response-info-bg.webp' );

// Schema.org ItemList + BlogPosting용 데이터 수집 (SEO)
$list_items_schema = array();
if ( $query && $query->have_posts() ) {
	$position = 0;
	while ( $query->have_posts() ) {
		$query->the_post();
		$list_items_schema[] = array(
			'@type'    => 'ListItem',
			'position' => ++$position,
			'item'     => array(
				'@type'         => 'BlogPosting',
				'headline'      => get_the_title(),
				'url'           => get_the_permalink(),
				'datePublished' => get_the_date( 'c' ),
				'dateModified'  => get_the_modified_date( 'c' ),
				'description'   => has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 ),
			),
		);
	}
	$query->rewind_posts();
}
?>
<section id="response-info" class="response-info" aria-labelledby="response-info-heading" style="background-image: url('<?php echo esc_url( $response_info_bg ); ?>');">
	<div class="response-info-inner">
		<header class="response-info-header">
			<h2 id="response-info-heading" class="response-info-title"><?php esc_html_e( '대응 정보', 'della-theme' ); ?></h2>
			<a href="<?php echo esc_url( della_theme_response_board_page_url() ); ?>" class="response-info-cta" aria-label="<?php esc_attr_e( '대응 정보 전체 목록 보기', 'della-theme' ); ?>"><?php esc_html_e( '더 보러 가기', 'della-theme' ); ?> &gt;</a>
		</header>

		<?php if ( $query && $query->have_posts() ) : ?>
		<?php
		// Schema.org ItemList JSON-LD (SEO)
		if ( ! empty( $list_items_schema ) ) {
			$itemlist_schema = array(
				'@context'        => 'https://schema.org',
				'@type'           => 'ItemList',
				'name'            => __( '대응 정보', 'della-theme' ),
				'description'     => __( '성범죄 대응 정보 및 법률 상담 관련 최신 글 목록', 'della-theme' ),
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
					$cats    = get_the_category();
					$cat_name = ! empty( $cats[0] ) ? $cats[0]->name : '';
					$card_title = get_the_title();
					?>
					<li class="response-info-card" role="listitem">
						<article class="response-info-card-article">
							<a href="<?php the_permalink(); ?>" class="response-info-card-link" aria-label="<?php echo esc_attr( sprintf( __( '%s 보기', 'della-theme' ), $card_title ) ); ?>">
								<?php if ( $cat_name ) : ?>
									<span class="response-info-card-tag" aria-hidden="true"><?php echo esc_html( $cat_name ); ?></span>
								<?php endif; ?>
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
		<?php else : ?>
		<p class="response-info-empty"><?php esc_html_e( '등록된 대응 정보가 없습니다.', 'della-theme' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
if ( $query ) {
	wp_reset_postdata();
}
