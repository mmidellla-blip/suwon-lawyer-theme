<?php
/**
 * 성범죄 유형별 대응 허브 섹션 – 4개 카드/링크 (SEO 내부링크)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$info_url = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );
$types = array(
	array(
		'label' => '강제추행 대응',
		'url'   => add_query_arg( array( 'tag' => '강제추행', 'paged' => 1 ), $info_url ),
	),
	array(
		'label' => '불법촬영 대응',
		'url'   => add_query_arg( array( 'tag' => '불법촬영', 'paged' => 1 ), $info_url ),
	),
	array(
		'label' => '아청법 대응',
		'url'   => add_query_arg( array( 'tag' => '아청법', 'paged' => 1 ), $info_url ),
	),
	array(
		'label' => '디지털 성범죄 대응',
		'url'   => add_query_arg( array( 'tag' => '불법촬영', 'paged' => 1 ), $info_url ),
	),
);
?>
<section id="response-type-hub" class="response-type-hub section-block" aria-labelledby="response-type-hub-heading">
	<div class="response-type-hub-inner">
		<h2 id="response-type-hub-heading" class="response-type-hub-title section-title">성범죄 유형별 대응</h2>
		<ul class="response-type-hub-list" role="list">
			<?php foreach ( $types as $item ) : ?>
				<li class="response-type-hub-item" role="listitem">
					<a href="<?php echo esc_url( $item['url'] ); ?>" class="response-type-hub-link"><?php echo esc_html( $item['label'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
