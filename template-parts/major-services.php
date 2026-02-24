<?php
/**
 * Major Services section - 전문팀 소개 + 경찰/검찰/재판 단계
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uploads = content_url( 'uploads/2026/02/' );
$stages = array(
	array(
		'title'     => '경찰 단계',
		'content'   => '혐의가 인정되지 않는 상황이라면 경찰단계에서 사건이 마무리될 수 있도록 조력한 뒤 \'불송치\', 즉 무혐의 결정을 이끌어내기 위해 조력하고 있습니다.',
		'image'     => 'dongju-police-stage.png',
		'image_alt' => '경찰 단계',
		'order'     => 'text-first',
	),
	array(
		'title'     => '검찰 단계',
		'content'   => '혐의가 인정되지만 각종 양형사유를 분석하여 기소유예 선처를 이끌어 내도록 하는 전략을 세웁니다. 혐의가 인정되지 않는다면 검찰단계에서 무혐의 처분이 나오도록 조력하고 있습니다.',
		'image'     => 'dongju-prosecution-stage.png',
		'image_alt' => '검찰 단계',
		'order'     => 'image-first',
	),
	array(
		'title'     => '재판 단계',
		'content'   => '중대한 사안이라도 실험이 나오지 않도록 정황을 분석하여 전략을 세운 뒤 집행유예 처분 혹은 양형사유를 찾아 유리한 판결을 이끌어 내기 위해 조력하고 있습니다.',
		'image'     => 'dongju-court-trial.png',
		'image_alt' => '재판 단계',
		'order'     => 'text-first',
	),
);
?>

<section id="major-services" class="major-services" aria-labelledby="major-services-heading">
	<div class="major-services-inner">
		<h2 id="major-services-heading" class="major-services-title">주요 서비스</h2>
		<p class="major-services-desc">
			법무법인 동주 수원 성범죄연구센터는 오로지 성범죄사건을 위해 존재하고 있습니다. 사건분석팀, 증거분석팀, 합의대행팀, 법률분석팀, 조사대비팀에서 전문가들이 사건에 조력하고 있습니다.
		</p>

		<div class="major-services-diagram">
			<img src="<?php echo esc_url( $uploads . 'dongju-legal-consultation.png' ); ?>" alt="<?php esc_attr_e( '분야별 전문가 팀 구성', 'della-theme' ); ?>" class="major-services-diagram-img" width="600" height="400" loading="lazy">
		</div>

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
						</div>
					<?php else : ?>
						<div class="major-services-stage-content">
							<h3 class="major-services-stage-title"><?php echo esc_html( $stage['title'] ); ?></h3>
							<p class="major-services-stage-text"><?php echo esc_html( $stage['content'] ); ?></p>
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
			$list_items[] = array(
				'@type'    => 'ListItem',
				'position' => $pos + 1,
				'item'     => array(
					'@type'       => 'Service',
					'name'        => $stage['title'],
					'description' => $stage['content'],
				),
			);
		}
		$itemlist_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'ItemList',
			'name'            => __( '주요 서비스', 'della-theme' ),
			'description'     => __( '법무법인 동주 수원 성범죄연구센터의 경찰·검찰·재판 단계별 서비스', 'della-theme' ),
			'itemListElement' => $list_items,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $itemlist_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
		?>
	</div>
</section>
