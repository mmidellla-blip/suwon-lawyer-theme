<?php
/**
 * Process / Service cards section - luum.vn style (block + background + context)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uploads_2026_02 = content_url( 'uploads/2026/02/' );
$cards          = array(
	array(
		'num'   => '01',
		'image' => '24-7-client-consultation.png',
		'title' => '모든 의뢰인을 위한 24/7 상담가능',
		'body'  => '',
		'bullets' => array(
			'당장 변호사가 필요한데 주말이라서, 영업 시간이 끝나서 도움을 받지 못하는 일은 없어야 합니다.',
			'부득이한 시간에 상담을 원하시는 분들의 사정을 알기에, 동주는 24(모든 시간)/7(모든 요일) 상담을 운영하고 있습니다. 형사 사건은 시간과의 싸움이기에, 단 몇 시간의 지체로도 결과가 달라질 수 있다는 것을 잘 알고 있기 때문입니다.'
		),
	),
	array(
		'num'   => '02',
		'image' => 'first-level-specialist-consultation.png',
		'title' => '1차 전문 상담진 상담',
		'body'  => '',
		'bullets' => array(
			'형사사건의 시작은 언제나 당혹스럽습니다.',
			'1차 상담은 단순한 접수가 아니라, 사건 경험이 풍부한 전문가가 직접 듣고 조언하는 시간입니다.',
			'동주의 1차 전문 상담진은 경찰 출신, 군대령 출신, 공무원 출신, 행정사 등으로 구성되어 있습니다.',
			'풍부한 경험을 토대로 불안한 마음을 이해하고, 대응 전략의 방향을 잡아 드립니다.',
		),
	),
	array(
		'num'   => '03',
		'image' => 'chief-partner-lawyer-consultatio.png',
		'title' => '대표변호사 · 파트너변호사가 직접 상담',
		'body'  => '',
		'bullets' => array(
			'첫 상담은 사건의 방향성을 결정하는 가장 중요한 단계입니다. 첫 상담의 결과가 곧 사건의 결과가 되기도 합니다.',
			'이렇게 중요한 과정이기 때문에, 동주에서는 첫 상담을 10년 경력 이상의 베테랑 변호사가 직접 주도합니다.',
			'사건을 책임수행할 변호사가 초기부터 관여함으로써, 말뿐인 안내가 아닌 실질적 해결을 위한 전략을 함께 세워 드립니다.',
		),
	),
	array(
		'num'   => '04',
		'image' => 'consultation-by-field-real-expert.jpg',
		'title' => "분야별 '진짜' 전문가 자문단",
		'body'  => '',
		'bullets' => array(
			'성범죄, 마약, 군형사, 디지털 범죄 등 분야별 사건 경험이 풍부한 실무 전문가들이 자문단으로 참여하고 있습니다.',
			'단순한 명함 속 직함이 아닌, 실제로 수백 건의 사건을 다뤄본 `진짜` 전문가들이 문제의 핵심을 짚습니다.',
		),
	),
	array(
		'num'   => '05',
		'image' => 'lawyerteam-direct-support.png',
		'title' => "개인 변호사가 아닌 '로이어팀' 조력",
		'body'  => '',
		'bullets' => array(
			'한 명의 변호사가 아닌, 팀 단위로 사건을 분석하고 대응하는 `로이어팀` 체계를 운영합니다.',
			'부장검사출신변호사, 형사법전문변호사, 군형사변호사, 행정법전문변호사 등 각기 다른 전문 분야를 가진 변호사 들이 한 사건에 집중함으로써, 어떤 돌발 상황에도 입체적이고 빈틈없는 전략으로 대응할 수 있도록 합니다.',
			'혼자가 아닌 팀이기 때문에 가능한 대응, 그것이 동주의 방식입니다.',
		),
	),
	array(
		'num'   => '06',
		'image' => 'first-level-specialist-consultation.png',
		'title' => '단계별 자문팀 동시 조력',
		'body'  => '',
		'bullets' => array(
			'경찰 단계부터 재판까지, 동주의 전담팀이 각 단계마다 사건을 끌고 갑니다.',
			'초기 대응은 사건분석팀이 방향을 설계하고, 진술 대비는 조사대비팀이 함께 준비하며, 증거 해석은 증거분석팀이 유불리를 분석합니다. 법률전략을 법률분석팀이 정리하고, 합의 조정은 합의대행팀이 조심스럽게 다가갑니다.',
			'한 사람의 변호사가 아닌, 다섯 개의 전문팀이 하나의 사건을 움직입니다.',
		),
	),
	array(
		'num'   => '07',
		'image' => 'stepwise-lawyerteam-support.jpg',
		'title' => "수행 변호사가 의뢰인과 '직접' 소통",
		'body'  => '',
		'bullets' => array(
			'사건을 맡은 변호사는 의뢰인과 직접 연락이 가능하도록 개인 전화번호를 공유합니다.',
			'진행 상황이 궁금하거나 급하게 확인할 일이 생겼을 때, 중간 단계를 거치지 않고 바로 소통할 수 있습니다.',
			'책임지고, 끝까지 함께 가는 조력자가 있다는 것을 느끼게 해드립니다.',
		),
	),
	array(
		'num'   => '08',
		'image' => 'postcase-continuous-support.jpg',
		'title' => '사건 종결 후에도 지속 조력',
		'body'  => '',
		'bullets' => array(
			'사건이 끝났다고 동주의 조력이 곧바로 끝나지 않습니다. 수사나 재판 이후, 사회 복귀와 명예 회복까지 이어지는 과정도 함께합니다.',
			'필요시 기록 삭제·정보 보호·재범 방지 컨설팅 등 후속 조치까지 도와드리며, 직장·학교 등 주변 관계 회복에 필요한 조언이나 정서적 안정까지 함께 고민합니다.',
			'두려움 없이 일상으로 돌아갈 수 있도록 마지막까지 함께하겠습니다.',
		),
	),
);

?>

<section id="process-cards" class="process-cards" aria-labelledby="process-cards-heading">
	<div class="process-cards-inner">
		<header class="process-cards-header">
			<h2 id="process-cards-heading" class="process-cards-title">진행 절차</h2>
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
			'name'        => __( '진행 절차', 'della-theme' ),
			'description' => __( '형사사건 대응 진행 절차 및 서비스 단계', 'della-theme' ),
			'step'        => $howto_steps,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $howto_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		?>
	</div>
</section>
