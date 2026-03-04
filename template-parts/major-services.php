<?php
/**
 * Major Services section - 수원 성범죄 주요 서비스 (SEO-friendly)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uploads = content_url( 'uploads/2026/02/' );

$service_keywords = array(
	'성범죄 사건 분석',
	'성범죄 증거 분석',
	'성범죄 법률 분석',
	'성범죄 경찰조사 대응',
	'성범죄 합의 대응',
);

$stages = array(
	array(
		'title'     => '성범죄 경찰조사 대응',
		'content'   => '성범죄 사건은 경찰 조사 단계에서의 대응이 매우 중요합니다. 수원 성범죄 전문변호사가 조사 전 진술 전략을 준비하고, 사건의 사실관계와 증거를 분석하여 의뢰인이 불리한 상황에 놓이지 않도록 조력합니다.',
		'image'     => 'dongju-police-stage.webp',
		'image_alt' => '성범죄 경찰조사 대응',
		'order'     => 'text-first',
		'bullets'   => array(
			'경찰 조사 전 진술 전략 설계',
			'디지털 증거(CCTV, 메신저, 통화기록) 분석',
			'강제추행, 카메라촬영, 아청법 사건 조사 대응',
		),
	),
	array(
		'title'     => '성범죄 검찰 송치 대응',
		'content'   => '경찰 수사가 종료되면 사건은 검찰로 송치됩니다. 수원 성범죄 전문변호사는 사건 기록과 증거를 재검토하여 기소 여부에 영향을 미칠 수 있는 법리적 쟁점을 정리하고 대응 전략을 수립합니다.',
		'image'     => 'dongju-prosecution-stage.webp',
		'image_alt' => '성범죄 검찰 송치 대응',
		'order'     => 'image-first',
		'bullets'   => array(
			'사건 기록 및 증거 재분석',
			'의견서 제출 및 법리 대응',
			'기소유예 및 무혐의 전략 수립',
		),
	),
	array(
		'title'     => '성범죄 재판 대응',
		'content'   => '성범죄 사건이 기소될 경우 재판 단계에서의 변론 전략이 중요합니다. 수원 성범죄 전문변호사는 증거와 진술을 종합적으로 검토하여 의뢰인의 상황에 맞는 재판 전략을 설계합니다.',
		'image'     => 'dongju-court-trial.webp',
		'image_alt' => '성범죄 재판 대응',
		'order'     => 'text-first',
		'bullets'   => array(
			'증거 및 진술 신빙성 검토',
			'법정 변론 전략 수립',
			'무죄·집행유예·감형 대응',
		),
	),
);
?>

<section id="major-services" class="major-services" aria-labelledby="major-services-heading">
	<div class="major-services-inner">
		<h2 id="major-services-heading" class="major-services-title section-title">수원 성범죄 주요 서비스<br>강제추행·카메라촬영·아청법<br>사건 대응</h2>
		<p class="major-services-desc section-desc">
			법무법인 동주는 수원 성범죄 전문변호사 팀이 강제추행, 카메라촬영, 아청법 사건 등 다양한 성범죄 사건을 대응하고 있습니다.<br>
			경찰 조사 단계부터 검찰 송치, 재판 대응까지 사건 단계별 전략을 수립하여 의뢰인의 권리를 보호합니다.
		</p>

		<div class="major-services-diagram">
			<img src="<?php echo esc_url( $uploads . 'dongju-legal-consultation.webp' ); ?>" alt="<?php esc_attr_e( '분야별 전문가 팀 구성', 'della-theme' ); ?>" class="major-services-diagram-img" width="600" height="400" loading="lazy">
		</div>
		<p class="major-services-keywords" aria-hidden="true">
			<?php echo esc_html( implode( ' · ', $service_keywords ) ); ?>
		</p>

		<div class="major-services-stages" role="list" aria-label="<?php esc_attr_e( '주요 서비스 단계 목록', 'della-theme' ); ?>">
			<?php foreach ( $stages as $stage ) : ?>
				<article class="major-services-stage major-services-stage--<?php echo esc_attr( $stage['order'] ); ?>" role="listitem">
					<?php if ( $stage['order'] === 'image-first' ) : ?>
						<div class="major-services-stage-media">
							<?php if ( ! empty( $stage['image'] ) ) : ?>
								<img src="<?php echo esc_url( $uploads . $stage['image'] ); ?>" alt="<?php echo esc_attr( $stage['image_alt'] ); ?>" class="major-services-stage-img" loading="lazy" width="600" height="400">
							<?php else : ?>
								<div class="major-services-stage-placeholder" aria-hidden="true">
									<span><?php echo esc_html( $stage['title'] ); ?></span>
								</div>
							<?php endif; ?>
						</div>
						<div class="major-services-stage-content">
							<h3 class="major-services-stage-title"><?php echo esc_html( $stage['title'] ); ?></h3>
							<p class="major-services-stage-text"><?php echo esc_html( $stage['content'] ); ?></p>
							<?php if ( ! empty( $stage['bullets'] ) ) : ?>
								<ul class="major-services-stage-list">
									<?php foreach ( $stage['bullets'] as $bullet ) : ?>
										<li><?php echo esc_html( $bullet ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<div class="major-services-stage-content">
							<h3 class="major-services-stage-title"><?php echo esc_html( $stage['title'] ); ?></h3>
							<p class="major-services-stage-text"><?php echo esc_html( $stage['content'] ); ?></p>
							<?php if ( ! empty( $stage['bullets'] ) ) : ?>
								<ul class="major-services-stage-list">
									<?php foreach ( $stage['bullets'] as $bullet ) : ?>
										<li><?php echo esc_html( $bullet ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
						<div class="major-services-stage-media">
							<?php if ( ! empty( $stage['image'] ) ) : ?>
								<img src="<?php echo esc_url( $uploads . $stage['image'] ); ?>" alt="<?php echo esc_attr( $stage['image_alt'] ); ?>" class="major-services-stage-img" loading="lazy" width="600" height="400">
							<?php else : ?>
								<div class="major-services-stage-placeholder" aria-hidden="true">
									<span><?php echo esc_html( $stage['title'] ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>

		<?php
		$list_items = array();
		foreach ( $stages as $pos => $stage ) {
			$desc = $stage['content'];
			if ( ! empty( $stage['bullets'] ) ) {
				$desc .= ' ' . implode( ' ', $stage['bullets'] );
			}
			$list_items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos + 1,
				'item'     => array(
					'@type'       => 'Service',
					'name'        => $stage['title'],
					'description' => $desc,
				),
			);
		}
		$itemlist_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'ItemList',
			'name'            => '수원 성범죄 주요 서비스 | 강제추행·카메라촬영·아청법 사건 대응',
			'description'     => '법무법인 동주는 수원 성범죄 전문변호사 팀이 강제추행, 카메라촬영, 아청법 사건 등 다양한 성범죄 사건을 대응합니다. 경찰 조사 단계부터 검찰 송치, 재판 대응까지 사건 단계별 전략을 수립합니다.',
			'itemListElement' => $list_items,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $itemlist_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		?>
	</div>
</section>
