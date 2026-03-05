<?php
/**
 * Header template - semantic HTML5
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<meta name="theme-color" content="#ffffff">
	<meta name="format-detection" content="telephone=yes">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<meta name="robots" content="index,follow,max-image-preview:large" />
	<?php
	if ( function_exists( 'della_theme_get_canonical_url' ) ) {
		$della_canonical = della_theme_get_canonical_url();
		if ( ! empty( $della_canonical ) ) {
			echo '<link rel="canonical" href="' . esc_url( $della_canonical ) . '" />' . "\n";
		}
	}
	// LCP: 히어로 이미지 preload + preconnect (프론트만)
	if ( is_front_page() ) {
		$della_ud = wp_upload_dir();
		if ( ! empty( $della_ud['baseurl'] ) ) {
			$hero_url = $della_ud['baseurl'] . '/2026/02/dongju-law-hero-banner.webp';
			echo '<link rel="preload" href="' . esc_url( $hero_url ) . '" as="image" fetchpriority="high">' . "\n";
			$parsed = parse_url( $della_ud['baseurl'] );
			if ( ! empty( $parsed['scheme'] ) && ! empty( $parsed['host'] ) ) {
				echo '<link rel="preconnect" href="' . esc_attr( $parsed['scheme'] . '://' . $parsed['host'] ) . '">' . "\n";
			}
		}
	}
	?>
	<?php wp_head(); ?>
	<?php
	// AIOSEO에 description이 없을 때를 대비해 테마에서 1회 출력 (기존 description 유지)
	if ( function_exists( 'della_theme_get_fallback_description' ) ) {
		$della_desc = della_theme_get_fallback_description();
		if ( is_string( $della_desc ) && trim( $della_desc ) !== '' ) {
			echo '<meta name="description" content="' . esc_attr( trim( $della_desc ) ) . '" />' . "\n";
		}
	}
	?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
// 맨 위/스크롤 body 클래스를 즉시 적용 (footer 전에 한 번 실행 → header-at-top CSS 적용)
if ( is_front_page() ) :
	?>
	<script>
	(function(){
		var d=document.body;
		if(!d) return;
		var on=window.scrollY<=50;
		d.classList.toggle('header-scrolled',!on);
		d.classList.toggle('header-at-top',on);
	})();
	</script>
	<?php
endif;
?>

<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'della-theme' ); ?></a>

<div id="page" class="site">

	<header id="masthead" class="site-header" role="banner">
		<div class="header-inner">
			<div class="site-branding">
				<?php if ( has_custom_logo() ) : ?>
					<div class="site-logo"><?php the_custom_logo(); ?></div>
				<?php endif; ?>
				<div class="site-brand-text">
					<p class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					</p>
					<?php if ( get_bloginfo( 'description' ) ) : ?>
						<p class="site-description"><?php bloginfo( 'description' ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( get_theme_mod( 'della_header_specialty', '' ) !== '' ) : ?>
					<span class="header-sep" aria-hidden="true">|</span>
					<span class="header-specialty"><?php echo esc_html( get_theme_mod( 'della_header_specialty', '' ) ); ?></span>
				<?php endif; ?>
			</div>

			<button type="button" class="menu-toggle" id="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'della-theme' ); ?>">
				<span class="menu-toggle-inner">
					<span class="menu-toggle-bar"></span>
					<span class="menu-toggle-bar"></span>
					<span class="menu-toggle-bar"></span>
				</span>
			</button>

			<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'della-theme' ); ?>">
				<div class="mobile-menu-backdrop" id="mobile-menu-backdrop" aria-hidden="true"></div>
				<div class="mobile-menu-panel">
					<div class="mobile-menu-content">
						<button type="button" class="mobile-menu-close" id="mobile-menu-close" aria-label="<?php esc_attr_e( 'Close menu', 'della-theme' ); ?>">
							<span aria-hidden="true">&times;</span>
						</button>
						<?php
						wp_nav_menu( array(
							'theme_location' => 'primary',
							'menu_id'        => 'primary-menu',
							'menu_class'     => 'nav-menu',
							'container'      => false,
							'fallback_cb'    => false,
						) );
						?>
						<?php if ( ! has_nav_menu( 'primary' ) ) : ?>
							<ul class="nav-menu" id="primary-menu">
								<li><a href="<?php echo esc_url( della_theme_lawyers_page_url() ); ?>"><?php esc_html_e( '성범죄 전문 변호사', 'della-theme' ); ?></a></li>
								<li><a href="<?php echo esc_url( della_theme_response_board_page_url() ); ?>"><?php esc_html_e( '성범죄 대응정보', 'della-theme' ); ?></a></li>
								<li><a href="<?php echo esc_url( function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : home_url( '/성범죄-성공사례/' ) ); ?>"><?php esc_html_e( '성범죄 성공사례', 'della-theme' ); ?></a></li>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</nav>
		</div>
	</header>

	<?php
	$show_board_breadcrumb = is_singular( 'page' ) && in_array( get_page_template_slug(), array( 'page-response-board.php', 'page-success-cases.php' ), true );
	if ( $show_board_breadcrumb ) :
		?>
		<div class="response-board-breadcrumb-wrap"><?php get_template_part( 'template-parts/breadcrumb' ); ?></div>
	<?php endif; ?>

	<div id="content" class="content-sidebar-wrap">
