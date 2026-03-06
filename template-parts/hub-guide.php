<?php
/**
 * Hub guide block – 수원 성범죄 변호사 허브 소개 (SEO, 내부링크 강화)
 * 하위 허브 페이지에서는 홈 링크 시 home_url('/') 또는 #hub-guide, #response-type-hub, #response-situation-hub 앵커 사용 권장.
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$info_url    = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );
$cases_url   = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/성범죄-성공사례/' );
$link_force  = add_query_arg( array( 'cat' => 'sexual_assult' ), $info_url );
$link_camera = add_query_arg( array( 'cat' => 'spycam_crime' ), $info_url );
$link_digital = add_query_arg( array( 'cat' => 'spycam_crime' ), $info_url );
$link_achaeng = add_query_arg( array( 'cat' => 'achaeng' ), $info_url );
$type_hub_id  = 'response-type-hub';
$situation_hub_id = 'response-situation-hub';
?>
<section id="hub-guide" class="hub-guide section-block" aria-labelledby="hub-guide-heading">
	<div class="hub-guide-inner">
		<h2 id="hub-guide-heading" class="hub-guide-title section-title">수원 성범죄 변호사 허브 – 사건 대응의 핵심</h2>
		<div class="hub-guide-card">
			<div class="hub-guide-body">
				<p class="hub-guide-lead">수원 성범죄 변호사 관점에서 보면, 성범죄 사건은 유형별로 대응 방식이 다릅니다. 강제추행, 카메라촬영, 디지털 성범죄, 아청법, 합의, 경찰조사 대응 등 쟁점과 필요한 전략이 사건마다 달라지므로, 현재 상황에 맞는 정보를 먼저 확인하는 것이 중요합니다.</p>

				<p>아래 <a href="#<?php echo esc_attr( $type_hub_id ); ?>">사건유형별 대응 허브</a>에서는 <a href="<?php echo esc_url( $link_force ); ?>">수원 강제추행 변호사</a>, <a href="<?php echo esc_url( $link_camera ); ?>">수원 카메라촬영 변호사</a>, <a href="<?php echo esc_url( $link_digital ); ?>">수원 디지털 성범죄 변호사</a>, <a href="<?php echo esc_url( $link_achaeng ); ?>">수원 아청법 변호사</a>, <a href="<?php echo esc_url( $info_url ); ?>">성범죄 합의 변호사</a>, <a href="<?php echo esc_url( $cases_url ); ?>">성범죄 성공사례</a> 등 유형별 안내로 이동할 수 있습니다.</p>

				<p><a href="#<?php echo esc_attr( $situation_hub_id ); ?>">대응상황별 허브</a>에서는 경찰조사 대응, 압수수색 대응, 성범죄 합의, 구속 대응, 재판 대응 등 현재 겪고 있는 단계에 맞는 정보를 확인할 수 있습니다. 사건유형 허브와 대응상황 허브를 함께 참고하시면 수원 성범죄 변호사와 상담 시 전략을 정리하는 데 도움이 됩니다.</p>
			</div>
		</div>
	</div>
</section>
