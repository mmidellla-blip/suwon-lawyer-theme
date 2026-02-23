<?php
/**
 * Breadcrumb navigation - template part
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = della_theme_get_breadcrumb_items();
if ( count( $items ) <= 1 ) {
	return;
}
?>
<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'della-theme' ); ?>">
	<ol>
		<?php foreach ( $items as $index => $item ) : ?>
			<li>
				<?php if ( ! empty( $item['url'] ) && $index < count( $items ) - 1 ) : ?>
					<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
				<?php else : ?>
					<span><?php echo esc_html( $item['label'] ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
