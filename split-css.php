<?php
/**
 * One-time split: extract common.css and page-specific CSS from style.css.
 * Line numbers are 1-based; ranges are [start,end] inclusive.
 * Run from theme dir: php split-css.php
 */
$theme_dir = __DIR__;
$path = $theme_dir . '/assets/css/src/style.css';
if ( ! is_readable( $path ) ) {
	$path = $theme_dir . '/assets/css/style.css';
}
$full = file_get_contents( $path );
if ( $full === false ) {
	fwrite( STDERR, "Failed to read style.css (tried src/ and assets/css/)\n" );
	exit( 1 );
}

$lines = explode( "\n", $full );
$total = count( $lines );

function slice_lines( array $lines, array $ranges ) {
	$out = [];
	foreach ( $ranges as list( $start_1, $end_1 ) ) {
		$start = max( 0, $start_1 - 1 );
		$end   = min( count( $lines ) - 1, $end_1 - 1 );
		for ( $i = $start; $i <= $end; $i++ ) {
			$out[] = $lines[ $i ];
		}
	}
	return implode( "\n", $out );
}

// Ranges [start_line, end_line] inclusive (1-based)
$common_ranges = [
	[ 16, 787 ],    // base: root, layout, header, nav (skip theme header 1-15)
	[ 3398, 3625 ], // hero-cta (floating CTA bar)
	[ 4331, 4430 ], // breadcrumb, main, entry (공통)
	[ 4466, $total ], // entry-content links, entry-footer, archive, sidebar, footer, utilities
];
// First chunk is LCP/critical; marker used by build-css.php for critical.min.css
$front_page_chunk1 = slice_lines( $lines, [ [ 788, 1897 ] ] );
$front_page_chunk2 = slice_lines( $lines, [ [ 3626, 4330 ] ] );
$front_content  = "/* Della Theme – front page */\n" . $front_page_chunk1 . "\n/* della-critical-end */\n" . $front_page_chunk2;
$page_lawyers_ranges = [
	[ 1900, 2597 ], // lawyer list + lawyer profile
];
$page_board_ranges = [
	[ 2598, 3397 ], // response-board + success-cases page
];
$page_sitemap_ranges = [
	[ 4431, 4465 ], // .page-sitemap only
];

$out_dir = $theme_dir . '/assets/css/src';
if ( ! is_dir( $out_dir ) ) {
	mkdir( $out_dir, 0755, true );
}
$common_content = "/* Della Theme – common styles */\n" . slice_lines( $lines, $common_ranges );
$lawyers_content = "/* Della Theme – page: lawyers / lawyer profile */\n" . slice_lines( $lines, $page_lawyers_ranges );
$board_content   = "/* Della Theme – page: response board, success cases */\n" . slice_lines( $lines, $page_board_ranges );
$sitemap_content = "/* Della Theme – page: sitemap */\n" . slice_lines( $lines, $page_sitemap_ranges );

$ok = true;
$ok = file_put_contents( $out_dir . '/common.css', $common_content ) !== false && $ok;
$ok = file_put_contents( $out_dir . '/front-page.css', $front_content ) !== false && $ok;
$ok = file_put_contents( $out_dir . '/page-lawyers.css', $lawyers_content ) !== false && $ok;
$ok = file_put_contents( $out_dir . '/page-board.css', $board_content ) !== false && $ok;
$ok = file_put_contents( $out_dir . '/page-sitemap.css', $sitemap_content ) !== false && $ok;

if ( ! $ok ) {
	fwrite( STDERR, "Failed to write one or more files\n" );
	exit( 1 );
}

echo "common.css: " . strlen( $common_content ) . " bytes\n";
echo "front-page.css: " . strlen( $front_content ) . " bytes\n";
echo "page-lawyers.css: " . strlen( $lawyers_content ) . " bytes\n";
echo "page-board.css: " . strlen( $board_content ) . " bytes\n";
echo "page-sitemap.css: " . strlen( $sitemap_content ) . " bytes\n";
echo "Done.\n";
