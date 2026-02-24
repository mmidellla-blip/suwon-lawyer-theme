<?php
/**
 * CSS minification — 주석·공백 제거 (디자인 변경 없음)
 *
 * @package Della_Theme
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'DELLA_BUILD_CSS' ) ) {
	exit;
}

/**
 * Minify CSS string: remove comments, collapse whitespace.
 *
 * @param string $css Raw CSS.
 * @return string Minified CSS.
 */
function della_minify_css( $css ) {
	// Remove /* ... */ comments (including multi-line). Preserve ! in /*! ... */ if needed.
	$css = preg_replace( '/\/\*[\s\S]*?\*\//u', '', $css );
	// Collapse all whitespace (spaces, newlines, tabs) to single space.
	$css = preg_replace( '/\s+/u', ' ', $css );
	// Remove space after { and before }
	$css = preg_replace( '/\s*\{\s*/', '{', $css );
	$css = preg_replace( '/\s*\}\s*/', '}', $css );
	// Remove space after ; (between declarations)
	$css = preg_replace( '/;\s*/', ';', $css );
	// Remove space after : in property: value (only first colon in block - safe to do globally for CSS)
	$css = preg_replace( '/:\s+/', ':', $css );
	return trim( $css );
}
