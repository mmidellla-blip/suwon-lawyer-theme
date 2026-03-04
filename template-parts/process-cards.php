<?php
/**
 * Process / Service cards section - 성범죄 사건 진행 절차 (SEO-friendly)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$process_case_url = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/' );
$process_info_url = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : home_url( '/성범죄-대응정보/' );

$uploads_2026_02 = content_url( 'uploads/2026/02/' );
$cards          = array(
	array(
		'num'     => '01',
		'image'   => '24-7-client-consultation.webp',
		'title'   => '성범죄 사건 1차 상담·사실관계 정리',
		'body'    => '',
		'bullets' => array(
			'사건 경위·관계·시간대·증거를 정리해 핵심 쟁점을 잡습니다.',
			'경찰 조사 전 진술 방향을 설계하고 불리한 표현을 사전에 차단합니다.',
			'긴급 상황(체포·압수수색·출석요구) 시 즉시 대응합니다.',
		),
	),
	array(
		'num'     => '02',
		'image'   => 'first-level-specialist-consultation.webp',
		'title'   => '초기 진술 전략·증거 보존',
		'body'    => '',
		'bullets' => array(
			'문자/카톡/DM/통화기록/위치기록 등 디지털 증거 보존을 안내합니다.',
			'CCTV·블랙박스 확보 가능성 판단 및 확보 요청을 진행합니다.',
			'진술서·의견서 초안을 준비해 조사 리스크를 낮춥니다.',
		),
	),
	array(
		'num'     => '03',
		'image'   => 'chief-partner-lawyer-consultatio.webp',
		'title'   => '전문가 자문·사건 전략 수립',
		'body'    => '',
		'bullets' => array(
			'강제추행·카메라촬영·아청법 등 죄종별로 법리 포인트를 정리합니다.',
			'쟁점(고의·동의·증거능력·신빙성)을 기준으로 방어 전략을 확정합니다.',
			'필요 시 디지털 포렌식/의학/심리 등 외부 자문을 연결합니다.',
		),
	),
	array(
		'num'     => '04',
		'image'   => 'consultation-by-field-real-expert.webp',
		'title'   => '경찰조사 동행·수사 대응',
		'body'    => '',
		'bullets' => array(
			'조사 출석 동행, 질문 의도 파악, 진술 정리로 불필요한 확대 해석을 막습니다.',
			'압수수색·휴대폰 포렌식·대질조사 등 주요 절차에 대응합니다.',
			'수사 단계에서 무혐의(불송치)·기소유예 목표를 설정합니다.',
		),
	),
	array(
		'num'     => '05',
		'image'   => 'lawyerteam-direct-support.webp',
		'title'   => '합의·처벌불원서·양형자료 준비',
		'body'    => '',
		'bullets' => array(
			'사건 유형에 맞는 합의 절차를 안내하고 처벌불원서 확보를 지원합니다.',
			'반성문·탄원서·교육이수·치료자료 등 양형자료를 체계적으로 준비합니다.',
			'합의가 어려운 경우에도 대안 전략(법리/증거)으로 대응합니다.',
		),
	),
	array(
		'num'     => '06',
		'image'   => 'first-level-specialist-consultation.webp',
		'title'   => '검찰 송치·재판 대응',
		'body'    => '',
		'bullets' => array(
			'의견서 제출, 공소사실 분석, 증거능력 다툼으로 기소 방어를 진행합니다.',
			'재판 시 공판 전략, 증인신문, 변론요지서로 결과를 만들기 위한 설계를 합니다.',
			'목표: 무죄 / 무혐의 / 기소유예 / 집행유예 / 감형 중 최적 결과를 선택합니다.',
		),
	),
	array(
		'num'     => '07',
		'image'   => 'stepwise-lawyerteam-support.webp',
		'title'   => '사후 관리·기록·재발 방지',
		'body'    => '',
		'bullets' => array(
			'처분/판결 이후 절차(교육·보호관찰·이행사항)를 안내합니다.',
			'기록/서류 보관 및 추후 문제 발생 시 즉시 대응 체계를 제공합니다.',
			'동일 유형 재발 방지 가이드(디지털/관계/행동 기준)를 제공합니다.',
		),
	),
);
?>

<section id="process-cards" class="process-cards" aria-labelledby="process-cards-heading">
	<div class="process-cards-inner">
		<header class="process-cards-header">
			<div class="process-cards-header-text">
				<h2 id="process-cards-heading" class="process-cards-title">성범죄 사건 진행 절차 | 수원 성범죄 전문변호사 팀 대응</h2>
				<p class="process-cards-desc">성범죄 사건은 초기 진술·증거 보존·합의 전략이 결과를 좌우합니다.<br>법무법인 동주는 수원 성범죄 전문변호사 팀이 경찰조사부터 재판까지 단계별로 대응합니다.</p>
			</div>
			<div class="process-cards-nav-wrap" aria-hidden="true">
				<button type="button" class="process-cards-nav process-cards-prev" id="process-cards-prev" aria-label="<?php esc_attr_e( '이전 진행 절차', 'della-theme' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button type="button" class="process-cards-nav process-cards-next" id="process-cards-next" aria-label="<?php esc_attr_e( '다음 진행 절차', 'della-theme' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
			</div>
		</header>
		<div class="process-cards-slider-wrap" id="process-cards-slider-wrap" role="region" aria-roledescription="slider" aria-label="<?php esc_attr_e( '진행 절차 슬라이드', 'della-theme' ); ?>">
			<div class="process-cards-slider-inner" id="process-cards-slider-inner">
			<ul class="process-cards-list lists" id="process-cards-list" role="list" aria-label="<?php esc_attr_e( '진행 절차 및 서비스 목록', 'della-theme' ); ?>" aria-live="polite" data-total="<?php echo (int) count( $cards ); ?>">
			<?php
			$cards_looped = array_merge( $cards, $cards );
			foreach ( $cards_looped as $index => $card ) :
				$original_index = $index % count( $cards );
				?>
				<li class="item process-card-item" role="listitem">
					<div class="block process-card-block">
						<div class="background process-card-background process-card-image-<?php echo esc_attr( $card['num'] ); ?>">
							<?php
							$img_url = ! empty( $card['image'] ) ? $uploads_2026_02 . $card['image'] : apply_filters( 'della_process_card_image', '', $original_index + 1, $card );
							if ( $img_url ) :
								?>
								<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" class="process-card-img" loading="<?php echo $original_index < 3 && $index < count( $cards ) ? 'eager' : 'lazy'; ?>" width="400" height="280">
							<?php endif; ?>
							<span class="badge process-card-badge" aria-hidden="true"><?php echo esc_html( $card['num'] ); ?></span>
						</div>
						<div class="context process-card-context">
							<h3 class="title process-card-heading"><?php echo esc_html( $card['title'] ); ?></h3>
							<div class="text process-card-text">
								<?php
								if ( ! empty( $card['body'] ) ) {
									echo '<p>' . esc_html( $card['body'] ) . '</p>';
								} elseif ( ! empty( $card['bullets'] ) && is_array( $card['bullets'] ) ) {
									echo '<ol class="process-card-list">';
									foreach ( $card['bullets'] as $bullet ) {
										echo '<li>' . esc_html( $bullet ) . '</li>';
									}
									echo '</ol>';
								}
								?>
							</div>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
			</ul>
			</div>
		</div>
		<div class="process-links">
			<a href="<?php echo esc_url( $process_case_url ); ?>">성범죄 성공사례 보기</a>
			<span class="dot" aria-hidden="true">·</span>
			<a href="<?php echo esc_url( $process_info_url ); ?>">성범죄 대응정보</a>
		</div>
		<?php
		$howto_steps = array();
		foreach ( $cards as $pos => $card ) {
			$text = '';
			if ( ! empty( $card['body'] ) ) {
				$text = $card['body'];
			} elseif ( ! empty( $card['bullets'] ) && is_array( $card['bullets'] ) ) {
				$text = implode( ' ', array_slice( $card['bullets'], 0, 2 ) );
			}
			$howto_steps[] = array(
				'@type'    => 'HowToStep',
				'position' => $pos + 1,
				'name'     => $card['title'],
				'text'     => $text ? wp_strip_all_tags( $text ) : $card['title'],
			);
		}
		$howto_schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'HowTo',
			'name'        => '성범죄 사건 진행 절차 | 수원 성범죄 전문변호사 팀 대응',
			'description' => '성범죄 사건은 초기 진술·증거 보존·합의 전략이 결과를 좌우합니다. 법무법인 동주는 수원 성범죄 전문변호사 팀이 경찰조사부터 재판까지 단계별로 대응합니다.',
			'step'        => $howto_steps,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $howto_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		?>
	</div>
</section>
