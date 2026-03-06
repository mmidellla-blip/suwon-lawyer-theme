<?php
/**
 * Hub FAQ 섹션 – 성범죄 초동·조사·합의·기소유예 등 (SEO, FAQPage 스키마는 wp_head에서 출력)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$faq_items = function_exists( 'della_theme_get_hub_faq_items' ) ? della_theme_get_hub_faq_items() : array();
?>
<section id="hub-faq" class="hub-faq section-block" aria-labelledby="hub-faq-heading">
	<div class="hub-faq-inner">
		<h2 id="hub-faq-heading" class="hub-faq-title section-title">성범죄 사건 자주 묻는 질문</h2>
		<p class="hub-faq-desc section-desc">초동 대응, 경찰조사, 합의, 압수수색, 기소유예 등 자주 문의되는 내용을 정리했습니다.</p>
		<dl class="hub-faq-list">
			<?php foreach ( $faq_items as $item ) : ?>
				<div class="hub-faq-item">
					<dt class="hub-faq-q">
						<span class="hub-faq-q-text"><?php echo esc_html( $item['question'] ); ?></span>
					</dt>
					<dd class="hub-faq-a">
						<p><?php echo esc_html( $item['answer'] ); ?></p>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>
