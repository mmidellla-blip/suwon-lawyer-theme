<?php
define( 'DELLA_BUILD_CSS', true );
$theme_dir = __DIR__;
require $theme_dir . '/inc/class-della-css-minify.php';

$src_dir  = $theme_dir . '/assets/css/src';
$dist_dir = $theme_dir . '/assets/css/dist';

if ( ! is_dir( $dist_dir ) ) {
    mkdir( $dist_dir, 0755, true );
}

$sources = [
    'common'       => 'common.css',
    'front-page'   => 'front-page.css',
    'page-lawyers' => 'page-lawyers.css',
    'page-board'   => 'page-board.css',
    'page-sitemap' => 'page-sitemap.css',
];

foreach ( $sources as $key => $file ) {
    $path = $src_dir . '/' . $file;
    if ( ! is_readable( $path ) ) {
        continue;
    }
    $raw = file_get_contents( $path );
    $min = della_minify_css( $raw );
    $out = $dist_dir . '/' . preg_replace( '/\.css$/', '.min.css', $file );
    if ( file_put_contents( $out, $min ) !== false ) {
        echo $key . ".min.css: " . strlen( $min ) . " bytes\n";
    }
}

// Critical CSS
$common_path = $src_dir . '/common.css';
$front_path  = $src_dir . '/front-page.css';
if ( is_readable( $common_path ) && is_readable( $front_path ) ) {
    $common      = file_get_contents( $common_path );
    $front_lines = explode( "\n", file_get_contents( $front_path ) );
    $critical_lines = [ $common ];
    foreach ( $front_lines as $line ) {
        $critical_lines[] = $line;
        if ( strpos( $line, 'della-critical-end' ) !== false ) {
            break;
        }
    }
    $critical_raw = implode( "\n", $critical_lines );
    $critical_min = della_minify_css( $critical_raw );
    if ( file_put_contents( $dist_dir . '/critical.min.css', $critical_min ) !== false ) {
        echo "critical.min.css: " . strlen( $critical_min ) . " bytes\n";
    }
}

echo "Done.\n";