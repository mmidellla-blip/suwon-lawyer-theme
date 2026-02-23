<?php
/**
 * Sidebar template part
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside id="secondary" class="sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'della-theme' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
