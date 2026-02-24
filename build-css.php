<?php
/**
 * Build: generate common.min.css, page-specific .min.css, and critical.min.css.
 * Run from theme dir: php build-css.php
 * Split sources: run php split-css.php first if you changed assets/css/style.css.
 */
define( 'DELLA_BUILD_CSS', true );
$theme_dir = __DIR__;
require $theme_dir . '/inc/class-della-css-minify.php';

$css_dir = $theme_dir . '/assets/css';

$sources = [
	'common'       => 'common.css',
	'front-page'   => 'front-page.css',
	'page-lawyers' => 'page-lawyers.css',
	'page-board'   => 'page-board.css',
	'page-sitemap' => 'page-sitemap.css',
];

foreach ( $sources as $key => $file ) {
	$path = $css_dir . '/' . $file;
	if ( ! is_readable( $path ) ) {
		continue;
	}
	$raw = file_get_contents( $path );
	$min = della_minify_css( $raw );
	$out = $css_dir . '/' . preg_replace( '/\.css$/', '.min.css', $file );
	if ( file_put_contents( $out, $min ) !== false ) {
		echo $key . ".min.css: " . strlen( $min ) . " bytes\n";
	}
}

// Critical = common + front-page up to della-critical-end (for LCP)
$common_path = $css_dir . '/common.css';
$front_path  = $css_dir . '/front-page.css';
if ( is_readable( $common_path ) && is_readable( $front_path ) ) {
	$common = file_get_contents( $common_path );
	$front_lines = explode( "\n", file_get_contents( $front_path ) );
	$critical_lines = array( $common );
	foreach ( $front_lines as $line ) {
		if ( strpos( $line, 'della-critical-end' ) !== false ) {
			$critical_lines[] = $line;
			break;
		}
		$critical_lines[] = $line;
	}
	$critical_raw = implode( "\n", $critical_lines );
	$critical_min = della_minify_css( $critical_raw );
	if ( file_put_contents( $css_dir . '/critical.min.css', $critical_min ) !== false ) {
		echo "critical.min.css: " . strlen( $critical_min ) . " bytes\n";
	}
}

echo "Done.\n";
