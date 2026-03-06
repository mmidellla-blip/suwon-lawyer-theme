<?php
/**
 * 성범죄 대응 상황별 허브 – 경찰조사·압수수색·합의·구속·재판 (SEO, response-type-hub 톤 통일)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$info_url = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );

$situations = array(
	array(
		'label'       => '경찰조사 대응',
		'url'         => add_query_arg( array( 'situation' => 'police' ), $info_url ),
		'description' => '출석 요청, 진술 방향, 변호인 동석 등 경찰 수사 단계 대응 방법을 안내합니다.',
	),
	array(
		'label'       => '압수수색 대응',
		'url'         => add_query_arg( array( 'situation' => 'seizure' ), $info_url ),
		'description' => '휴대폰·PC·저장매체 압수수색 영장 범위와 절차적 대응을 정리했습니다.',
	),
	array(
		'label'       => '성범죄 합의',
		'url'         => add_query_arg( array( 'situation' => 'settlement' ), $info_url ),
		'description' => '합의 시점, 합의서 작성, 피해자 측과의 협의 시 고려사항을 안내합니다.',
	),
	array(
		'label'       => '구속 대응',
		'url'         => add_query_arg( array( 'situation' => 'custody' ), $info_url ),
		'description' => '구속 영장 청구·실질심사·보석 등 구속 단계 대응 정보를 제공합니다.',
	),
	array(
		'label'       => '재판 대응',
		'url'         => add_query_arg( array( 'situation' => 'trial' ), $info_url ),
		'description' => '공소제기 이후 재판 절차, 양형 쟁점, 선처 자료 등 재판 단계 대응을 정리했습니다.',
	),
);
?>
<section id="response-situation-hub" class="response-situation-hub section-block" aria-labelledby="response-situation-hub-heading">
	<div class="response-situation-hub-inner">
		<h2 id="response-situation-hub-heading" class="response-situation-hub-title section-title">성범죄 대응 상황별 안내</h2>
		<p class="response-situation-hub-desc section-desc">성범죄 사건은 경찰조사 단계, 압수수색, 합의 시도, 재판 대응 등 현재 상황에 따라 필요한 대응 전략이 달라질 수 있습니다. 아래에서 상황별 대응 정보를 확인하시기 바랍니다.</p>
		<ul class="response-situation-hub-list" role="list">
			<?php foreach ( $situations as $item ) : ?>
				<li class="response-situation-hub-item" role="listitem">
					<a href="<?php echo esc_url( $item['url'] ); ?>" class="response-situation-hub-link">
						<span class="response-situation-hub-link-label"><?php echo esc_html( $item['label'] ); ?></span>
						<?php if ( ! empty( $item['description'] ) ) : ?>
							<span class="response-situation-hub-link-desc"><?php echo esc_html( $item['description'] ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
