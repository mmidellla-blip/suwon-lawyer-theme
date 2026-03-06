<?php
/**
 * 성범죄 유형별 대응 허브 섹션 – 카드 + 문장 설명 (SEO 내부링크, 맥락형)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$info_url  = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );
$cases_url = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/성범죄-성공사례/' );

$types = array(
	array(
		'label'       => '수원 강제추행 변호사',
		'url'         => add_query_arg( array( 'cat' => 'sexual_assult' ), $info_url ),
		'description' => '강제추행 혐의의 구성요건, 수사·재판 단계별 대응, 합의 시 고려사항을 정리했습니다.',
	),
	array(
		'label'       => '수원 카메라촬영 변호사',
		'url'         => add_query_arg( array( 'cat' => 'spycam_crime' ), $info_url ),
		'description' => '불법촬영·카메라등이용촬영죄 처벌 기준, 압수수색 대응, 관련 판례를 안내합니다.',
	),
	array(
		'label'       => '수원 디지털 성범죄 변호사',
		'url'         => add_query_arg( array( 'cat' => 'spycam_crime' ), $info_url ),
		'description' => '디지털 성범죄 유형별 법조문, 촬영물 유통·삭제 관련 쟁점과 대응 가이드를 제공합니다.',
	),
	array(
		'label'       => '수원 아청법 변호사',
		'url'         => add_query_arg( array( 'cat' => 'achaeng' ), $info_url ),
		'description' => '아동·청소년 성보호법 위반 사건의 적용 조항, 양형 기준, 기소유예 가능성을 정리했습니다.',
	),
	array(
		'label'       => '성범죄 합의 변호사',
		'url'         => $info_url,
		'description' => '고소 전·후 합의 시점, 합의서 작성, 피해자 의사 반영 방식 등 합의 관련 대응정보입니다.',
	),
	array(
		'label'       => '성범죄 성공사례',
		'url'         => $cases_url,
		'description' => '강제추행·카메라촬영·아청법 사건에서 무혐의·기소유예·집행유예 등 실제 결과를 확인할 수 있습니다.',
	),
);
?>
<section id="response-type-hub" class="response-type-hub section-block" aria-labelledby="response-type-hub-heading">
	<div class="response-type-hub-inner">
		<h2 id="response-type-hub-heading" class="response-type-hub-title section-title">수원 성범죄 변호사 – 유형별 대응 안내</h2>
		<p class="response-type-hub-desc section-desc">사건 유형에 따라 법조문, 쟁점, 대응 방법이 다릅니다. 아래에서 필요한 정보를 선택해 보세요.</p>
		<ul class="response-type-hub-list" role="list">
			<?php foreach ( $types as $item ) : ?>
				<li class="response-type-hub-item" role="listitem">
					<a href="<?php echo esc_url( $item['url'] ); ?>" class="response-type-hub-link">
						<span class="response-type-hub-link-label"><?php echo esc_html( $item['label'] ); ?></span>
						<?php if ( ! empty( $item['description'] ) ) : ?>
							<span class="response-type-hub-link-desc"><?php echo esc_html( $item['description'] ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
