<?php
/**
 * Della Theme - SEO-optimized WordPress theme
 *
 * @package Della_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DELLA_THEME_VERSION', '1.0.1' );

/**
 * CSS/JS 캐시 무효화용 공통 버전 (한 번에 전체 갱신)
 * dist/common.min.css, dist/critical.min.css 중
 * 가장 최근 수정 시각을 사용. 배포·빌드 시 쿼리스트링이 바뀌어 캐시 미사용.
 * 필터로 오버라이드 가능 (예: 배포 파이프라인에서 고정 버전 주입).
 *
 * @return string
 */
function della_theme_asset_version() {
	$version = apply_filters( 'della_theme_asset_version', null );
	if ( is_string( $version ) && $version !== '' ) {
		return $version;
	}
	$theme_dir = get_stylesheet_directory();
	$files = array(
		$theme_dir . '/assets/css/dist/common.min.css',
		$theme_dir . '/assets/css/dist/common.css',
		$theme_dir . '/assets/css/dist/critical.min.css',
	);
	$max = 0;
	foreach ( $files as $path ) {
		if ( file_exists( $path ) ) {
			$m = filemtime( $path );
			if ( $m > $max ) {
				$max = $m;
			}
		}
	}
	return $max > 0 ? (string) $max : DELLA_THEME_VERSION;
}

/**
 * 프론트엔드에서 관리자 바(워드프레스 정보·사이트명·SEO 메뉴 등) 비표시
 * 로그인해도 상단 흰색 메뉴바가 나오지 않음. 관리 화면에서는 그대로 표시.
 */
function della_theme_hide_admin_bar_on_front( $show ) {
	if ( is_admin() ) {
		return $show;
	}
	return false;
}
add_filter( 'show_admin_bar', 'della_theme_hide_admin_bar_on_front', 10, 1 );

/**
 * Theme setup
 */
function della_theme_setup() {
	load_theme_textdomain( 'della-theme', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
		'header-text' => array( 'site-title', 'site-description' ),
	) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'della-theme' ),
	) );
}
add_action( 'after_setup_theme', 'della_theme_setup' );

/**
 * 성범죄 전문 변호사 페이지 자동 생성 (404 방지)
 * 영문 슬러그 'lawyers' 사용 — 한글 슬러그는 서버에 따라 404 발생 가능
 */
function della_theme_ensure_lawyers_page() {
	// 이미 확인한 적 있으면 get_pages 등 DB 부하 없이 스킵 (0=확인함·페이지 없음, 1=페이지 있음)
	if ( get_option( 'della_lawyers_page_created' ) !== false ) {
		return;
	}
	$slug = 'lawyers';
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		update_option( 'della_lawyers_page_created', 1 );
		return;
	}
	$by_template = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-lawyers.php',
		'number'     => 1,
	) );
	if ( ! empty( $by_template ) ) {
		update_option( 'della_lawyers_page_created', 1 );
		return;
	}

	$page_id = wp_insert_post( array(
		'post_title'   => __( '성범죄 전문 변호사', 'della-theme' ),
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
		'post_content' => '',
	) );
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-lawyers.php' );
		update_option( 'della_lawyers_page_created', 1 );
	} else {
		update_option( 'della_lawyers_page_created', 0 );
	}
}
add_action( 'after_switch_theme', 'della_theme_ensure_lawyers_page' );
add_action( 'init', 'della_theme_ensure_lawyers_page', 0 );

/**
 * 변호사 상세 URL용 rewrite: /lawyers/{slug}/
 */
function della_theme_lawyer_rewrite_rules() {
	add_rewrite_rule( 'lawyers/([^/]+)/?$', 'index.php?pagename=lawyers&lawyer_slug=$matches[1]', 'top' );
}
add_action( 'init', 'della_theme_lawyer_rewrite_rules', 5 );

/**
 * query_var 추가: lawyer_slug
 */
function della_theme_lawyer_query_vars( $vars ) {
	$vars[] = 'lawyer_slug';
	return $vars;
}
add_filter( 'query_vars', 'della_theme_lawyer_query_vars' );

/**
 * 테마 전환 시 rewrite flush (lawyers/{slug} 반영). XML 사이트맵은 플러그인 사용.
 */
function della_theme_flush_rewrite_on_switch() {
	della_theme_ensure_lawyers_page();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'della_theme_flush_rewrite_on_switch', 99 );

/**
 * 한글 URL(/성범죄-전문-변호사/) 접속 시 실제 페이지(/lawyers/)로 리다이렉트 (404 방지)
 */
function della_theme_redirect_lawyers_korean_url() {
	if ( ! is_404() ) {
		return;
	}
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path        = trim( parse_url( $request_uri, PHP_URL_PATH ), '/' );
	$korean_slug = rawurldecode( $path );
	if ( $path === '성범죄-전문-변호사' || $korean_slug === '성범죄-전문-변호사' || strpos( $korean_slug, '성범죄-전문-변호사' ) === 0 ) {
		$url = della_theme_lawyers_page_url();
		if ( $url && $url !== home_url( '/' ) ) {
			$query = parse_url( $request_uri, PHP_URL_QUERY );
			if ( ! empty( $query ) ) {
				$url = $url . ( strpos( $url, '?' ) !== false ? '&' : '?' ) . $query;
			}
			wp_safe_redirect( $url, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'della_theme_redirect_lawyers_korean_url' );

/**
 * 성범죄 대응정보 게시판 페이지 자동 생성 (404 방지)
 */
function della_theme_ensure_response_board_page() {
	if ( get_option( 'della_response_board_page_created' ) !== false ) {
		return;
	}
	$slug = 'response-info';
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		update_option( 'della_response_board_page_created', 1 );
		return;
	}
	$by_template = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-response-board.php',
		'number'     => 1,
	) );
	if ( ! empty( $by_template ) ) {
		update_option( 'della_response_board_page_created', 1 );
		return;
	}

	$page_id = wp_insert_post( array(
		'post_title'   => __( '성범죄 대응정보', 'della-theme' ),
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
		'post_content' => '',
	) );
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-response-board.php' );
		update_option( 'della_response_board_page_created', 1 );
	} else {
		update_option( 'della_response_board_page_created', 0 );
	}
}
add_action( 'after_switch_theme', 'della_theme_ensure_response_board_page' );
add_action( 'init', 'della_theme_ensure_response_board_page', 0 );

/**
 * 성범죄 대응정보 게시판 페이지 URL (요청당 1회만 get_pages).
 */
function della_theme_response_board_page_url() {
	static $url = null;
	if ( $url !== null ) {
		return $url;
	}
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-response-board.php',
		'number'     => 1,
	) );
	$url = ! empty( $pages ) ? get_permalink( $pages[0] ) : home_url( '/response-info/' );
	return $url;
}

/** 온라인 상담 신청 페이지 URL (테마 설정 또는 기본 외부 링크). */
function della_theme_consultation_url() {
	$url = get_theme_mod( 'della_consultation_url', '' );
	if ( is_string( $url ) && trim( $url ) !== '' ) {
		return trim( $url );
	}
	return 'https://sexcrimecenter-dongju.com/bbs/board.php?bo_table=online&me_code=6010';
}

/**
 * 한글 URL(/성범죄-대응정보/) 접속 시 게시판 페이지로 리다이렉트
 */
function della_theme_redirect_response_board_korean_url() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path        = trim( parse_url( $request_uri, PHP_URL_PATH ), '/' );
	$korean_slug = rawurldecode( $path );
	if ( $path === '성범죄-대응정보' || $korean_slug === '성범죄-대응정보' || strpos( $korean_slug, '성범죄-대응정보' ) === 0 ) {
		$url = della_theme_response_board_page_url();
		if ( $url && $url !== home_url( '/' ) ) {
			$query = parse_url( $request_uri, PHP_URL_QUERY );
			if ( ! empty( $query ) ) {
				$url = $url . ( strpos( $url, '?' ) !== false ? '&' : '?' ) . $query;
			}
			wp_safe_redirect( $url, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'della_theme_redirect_response_board_korean_url' );

/**
 * 성범죄 성공사례 게시판 페이지 자동 생성 (404 방지)
 */
function della_theme_ensure_success_cases_page() {
	if ( get_option( 'della_success_cases_page_created' ) !== false ) {
		return;
	}
	$slug = 'success-cases';
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		update_option( 'della_success_cases_page_created', 1 );
		return;
	}
	$by_template = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-success-cases.php',
		'number'     => 1,
	) );
	if ( ! empty( $by_template ) ) {
		update_option( 'della_success_cases_page_created', 1 );
		return;
	}

	$page_id = wp_insert_post( array(
		'post_title'   => __( '성범죄 성공사례', 'della-theme' ),
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
		'post_content' => '',
	) );
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-success-cases.php' );
		update_option( 'della_success_cases_page_created', 1 );
	} else {
		update_option( 'della_success_cases_page_created', 0 );
	}
}
add_action( 'after_switch_theme', 'della_theme_ensure_success_cases_page' );
add_action( 'init', 'della_theme_ensure_success_cases_page', 0 );

/**
 * 성범죄 성공사례 게시판 페이지 URL (요청당 1회만 get_pages).
 */
function della_theme_success_cases_page_url() {
	static $url = null;
	if ( $url !== null ) {
		return $url;
	}
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-success-cases.php',
		'number'     => 1,
	) );
	$url = ! empty( $pages ) ? get_permalink( $pages[0] ) : home_url( '/success-cases/' );
	return $url;
}

/**
 * 한글 URL(/성범죄-성공사례/) 접속 시 성공사례 페이지로 리다이렉트
 */
function della_theme_redirect_success_cases_korean_url() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path        = trim( parse_url( $request_uri, PHP_URL_PATH ), '/' );
	$korean_slug = rawurldecode( $path );
	if ( $path === '성범죄-성공사례' || $korean_slug === '성범죄-성공사례' || strpos( $korean_slug, '성범죄-성공사례' ) === 0 ) {
		$url = della_theme_success_cases_page_url();
		if ( $url && $url !== home_url( '/' ) ) {
			$query = parse_url( $request_uri, PHP_URL_QUERY );
			if ( ! empty( $query ) ) {
				$url = $url . ( strpos( $url, '?' ) !== false ? '&' : '?' ) . $query;
			}
			wp_safe_redirect( $url, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'della_theme_redirect_success_cases_korean_url' );

/**
 * HTML 사이트맵 페이지 자동 생성 (푸터 /sitemap/ 링크용)
 */
function della_theme_ensure_sitemap_page() {
	if ( get_option( 'della_sitemap_page_created' ) !== false ) {
		return;
	}
	$slug = 'sitemap';
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		update_option( 'della_sitemap_page_created', 1 );
		return;
	}
	$by_template = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-sitemap.php',
		'number'     => 1,
	) );
	if ( ! empty( $by_template ) ) {
		update_option( 'della_sitemap_page_created', 1 );
		return;
	}
	$page_id = wp_insert_post( array(
		'post_title'   => __( '사이트맵', 'della-theme' ),
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
		'post_content' => '',
	) );
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-sitemap.php' );
		update_option( 'della_sitemap_page_created', 1 );
	} else {
		update_option( 'della_sitemap_page_created', 0 );
	}
}
add_action( 'after_switch_theme', 'della_theme_ensure_sitemap_page' );
add_action( 'init', 'della_theme_ensure_sitemap_page', 0 );

/**
 * 면책공고 페이지 자동 생성 (footer 링크용)
 */
function della_theme_ensure_disclaimer_page() {
	if ( get_option( 'della_disclaimer_page_created' ) !== false ) {
		return;
	}
	$slug = '면책공고';
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		update_option( 'della_disclaimer_page_created', 1 );
		return;
	}
	$by_template = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-disclaimer.php',
		'number'     => 1,
	) );
	if ( ! empty( $by_template ) ) {
		update_option( 'della_disclaimer_page_created', 1 );
		return;
	}
	$page_id = wp_insert_post( array(
		'post_title'   => __( '면책공고', 'della-theme' ),
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
		'post_content' => '',
	) );
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-disclaimer.php' );
		update_option( 'della_disclaimer_page_created', 1 );
	} else {
		update_option( 'della_disclaimer_page_created', 0 );
	}
}
add_action( 'after_switch_theme', 'della_theme_ensure_disclaimer_page' );
add_action( 'init', 'della_theme_ensure_disclaimer_page', 0 );

/**
 * 개인정보처리방침 페이지 자동 생성 (footer 링크용)
 */
function della_theme_ensure_privacy_policy_page() {
	if ( get_option( 'della_privacy_policy_page_created' ) !== false ) {
		return;
	}
	$slug = '개인정보처리방침';
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		update_option( 'della_privacy_policy_page_created', 1 );
		return;
	}
	$by_template = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-privacy-policy.php',
		'number'     => 1,
	) );
	if ( ! empty( $by_template ) ) {
		update_option( 'della_privacy_policy_page_created', 1 );
		return;
	}
	$page_id = wp_insert_post( array(
		'post_title'   => __( '개인정보처리방침', 'della-theme' ),
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
		'post_content' => '',
	) );
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'page-privacy-policy.php' );
		update_option( 'della_privacy_policy_page_created', 1 );
	} else {
		update_option( 'della_privacy_policy_page_created', 0 );
	}
}
add_action( 'after_switch_theme', 'della_theme_ensure_privacy_policy_page' );
add_action( 'init', 'della_theme_ensure_privacy_policy_page', 0 );

/**
 * body 클래스: 프론트(맨 위)에서 헤더 투명 적용용 / 변호사 페이지
 */
function della_theme_body_class_header( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'della-is-front';
	}
	if ( della_theme_is_lawyers_page() ) {
		if ( get_query_var( 'lawyer_slug' ) ) {
			$classes[] = 'lawyer-profile-page';
		} else {
			$classes[] = 'lawyer-list-page';
		}
	}
	if ( is_singular( 'page' ) && get_page_template_slug() === 'page-response-board.php' ) {
		$classes[] = 'response-board-page';
	}
	if ( is_singular( 'page' ) && get_page_template_slug() === 'page-success-cases.php' ) {
		$classes[] = 'success-cases-page';
	}
	return $classes;
}
add_filter( 'body_class', 'della_theme_body_class_header' );

/**
 * 성범죄 전문 변호사 페이지( page-lawyers.php ) 여부
 *
 * @return bool
 */
function della_theme_is_lawyers_page() {
	return is_singular( 'page' ) && get_page_template_slug() === 'page-lawyers.php';
}

/**
 * 성범죄 대응정보 페이지 여부 (SEO·메타 전용)
 */
function della_theme_is_response_board_page() {
	return is_singular( 'page' ) && get_page_template_slug() === 'page-response-board.php';
}

/**
 * 성범죄 성공사례 페이지 여부 (SEO·메타 전용)
 * 템플릿·슬러그·URL 비교로 판별 (wp_head에서도 동작).
 */
function della_theme_is_success_cases_page() {
	if ( ! is_singular( 'page' ) ) {
		return false;
	}
	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return false;
	}
	$template = get_page_template_slug( $post_id );
	$is_success = ( $template === 'page-success-cases.php' );
	if ( ! $is_success ) {
		$post = get_post( $post_id );
		$slug = $post && isset( $post->post_name ) ? $post->post_name : '';
		$is_success = in_array( $slug, array( 'success-cases', 'case', 'success', '성범죄-성공사례', '성공사례' ), true );
	}
	if ( ! $is_success && function_exists( 'della_theme_success_cases_page_url' ) ) {
		$current = get_permalink( $post_id );
		$sc_url  = della_theme_success_cases_page_url();
		if ( $current && $sc_url ) {
			$current_norm = rtrim( untrailingslashit( $current ), '/' );
			$sc_norm      = rtrim( untrailingslashit( $sc_url ), '/' );
			$is_success   = ( $current_norm === $sc_norm );
		}
	}
	return apply_filters( 'della_theme_is_success_cases_page', $is_success );
}

if ( ! defined( 'DELLA_THEME_CAT_TRANSIENT_TTL' ) ) {
	/** 대응정보/성공사례 카테고리 트랜지언트 TTL (초). */
	define( 'DELLA_THEME_CAT_TRANSIENT_TTL', 3600 );
}

/** @return int 카테고리 관련 트랜지언트 TTL(초). */
function della_theme_cat_transient_ttl() {
	return defined( 'DELLA_THEME_CAT_TRANSIENT_TTL' ) ? (int) DELLA_THEME_CAT_TRANSIENT_TTL : 3600;
}

/**
 * 성공사례 부모 카테고리(성범죄성공사례 등) 반환. 없으면 null.
 * 요청당 정적 캐시 + 요청 간 트랜지언트 캐시로 DB 조회 최소화.
 *
 * @return WP_Term|null
 */
function della_theme_get_success_case_parent_category() {
	static $cached = null;
	if ( $cached !== null ) {
		return $cached;
	}
	$transient_key = 'della_success_parent_term_id';
	$term_id = get_transient( $transient_key );
	if ( false !== $term_id && $term_id > 0 ) {
		$term = get_term( (int) $term_id, 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			$cached = $term;
			return $cached;
		}
	}
	$cat = get_category_by_slug( '성범죄성공사례' );
	if ( $cat ) {
		set_transient( $transient_key, $cat->term_id, della_theme_cat_transient_ttl() );
		$cached = $cat;
		return $cat;
	}
	$cat = get_category_by_slug( '성공사례' );
	if ( $cat ) {
		set_transient( $transient_key, $cat->term_id, della_theme_cat_transient_ttl() );
		$cached = $cat;
		return $cat;
	}
	$cat = get_category_by_slug( 'success-cases' );
	if ( $cat ) {
		set_transient( $transient_key, $cat->term_id, della_theme_cat_transient_ttl() );
		$cached = $cat;
		return $cat;
	}
	$cat = get_category_by_slug( 'success-case' );
	if ( $cat ) {
		set_transient( $transient_key, $cat->term_id, della_theme_cat_transient_ttl() );
		$cached = $cat;
		return $cat;
	}
	$terms = get_terms( array( 'taxonomy' => 'category', 'slug' => array( '성범죄성공사례', '성공사례', 'success-cases', 'success-case' ), 'hide_empty' => false, 'number' => 4 ) );
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		set_transient( $transient_key, $terms[0]->term_id, della_theme_cat_transient_ttl() );
		$cached = $terms[0];
		return $cached;
	}
	$cached = null;
	return null;
}

/**
 * 단일 글(포스트)이 성공사례 카테고리에 속하는지 여부 (상세 페이지 메타용).
 * 캐시된 성공사례 term_id 배열 사용으로 get_terms 중복 제거.
 *
 * @param WP_Post|null $post 포스트 객체. null이면 현재 쿼리 포스트.
 * @return bool
 */
function della_theme_is_success_case_post( $post = null ) {
	if ( ! $post ) {
		$post = get_queried_object();
	}
	if ( ! $post instanceof WP_Post || $post->post_type !== 'post' ) {
		return false;
	}
	$term_ids = function_exists( 'della_theme_get_success_case_exclude_term_ids' ) ? della_theme_get_success_case_exclude_term_ids() : array();
	return ! empty( $term_ids ) && has_category( $term_ids, $post );
}

/**
 * 대응정보 부모 카테고리(성범죄대응정보 등) 반환. 없으면 null.
 * 요청당 정적 캐시 + 요청 간 트랜지언트 캐시로 DB 조회 최소화.
 *
 * @return WP_Term|null
 */
function della_theme_get_response_info_parent_category() {
	static $cached = null;
	if ( $cached !== null ) {
		return $cached;
	}
	$transient_key = 'della_resp_info_parent_term_id';
	$term_id = get_transient( $transient_key );
	if ( false !== $term_id && $term_id > 0 ) {
		$term = get_term( (int) $term_id, 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			$cached = $term;
			return $cached;
		}
	}
	$cat = get_category_by_slug( '성범죄대응정보' );
	if ( $cat ) {
		set_transient( $transient_key, $cat->term_id, della_theme_cat_transient_ttl() );
		$cached = $cat;
		return $cat;
	}
	$cat = get_category_by_slug( '대응정보' );
	if ( $cat ) {
		set_transient( $transient_key, $cat->term_id, della_theme_cat_transient_ttl() );
		$cached = $cat;
		return $cat;
	}
	$cat = get_category_by_slug( 'response-info' );
	if ( $cat ) {
		set_transient( $transient_key, $cat->term_id, della_theme_cat_transient_ttl() );
		$cached = $cat;
		return $cat;
	}
	$terms = get_terms( array( 'taxonomy' => 'category', 'slug' => array( '성범죄대응정보', '대응정보', 'response-info' ), 'hide_empty' => false, 'number' => 3 ) );
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		set_transient( $transient_key, $terms[0]->term_id, della_theme_cat_transient_ttl() );
		$cached = $terms[0];
		return $cached;
	}
	$cached = null;
	return null;
}

/**
 * 대응정보 페이지 쿼리에서 제외할 카테고리 term_id 배열 (성공사례 부모 + 자식).
 * 요청당 정적 캐시 + 요청 간 트랜지언트 캐시로 DB 조회 최소화.
 *
 * @return int[]
 */
function della_theme_get_success_case_exclude_term_ids() {
	static $cached = null;
	if ( $cached !== null ) {
		return $cached;
	}
	$transient_key = 'della_success_exclude_term_ids';
	$stored = get_transient( $transient_key );
	if ( false !== $stored && is_array( $stored ) ) {
		$cached = array_map( 'intval', $stored );
		return $cached;
	}
	$success_cat = function_exists( 'della_theme_get_success_case_parent_category' ) ? della_theme_get_success_case_parent_category() : null;
	if ( ! $success_cat ) {
		$cached = array();
		return $cached;
	}
	$exclude_ids = array( (int) $success_cat->term_id );
	$children = get_terms( array( 'taxonomy' => 'category', 'parent' => $success_cat->term_id, 'hide_empty' => false, 'fields' => 'ids' ) );
	if ( ! is_wp_error( $children ) && ! empty( $children ) ) {
		$exclude_ids = array_merge( $exclude_ids, array_map( 'intval', $children ) );
	}
	set_transient( $transient_key, $exclude_ids, della_theme_cat_transient_ttl() );
	$cached = $exclude_ids;
	return $cached;
}

/**
 * 대응정보 카테고리 term_id 배열 (부모 + 자식). is_response_info_post 등에서 사용.
 * 요청당 정적 캐시 + 트랜지언트로 get_terms 중복 제거.
 *
 * @return int[]
 */
function della_theme_get_response_info_term_ids() {
	static $cached = null;
	if ( $cached !== null ) {
		return $cached;
	}
	$transient_key = 'della_resp_info_term_ids';
	$stored = get_transient( $transient_key );
	if ( false !== $stored && is_array( $stored ) ) {
		$cached = array_map( 'intval', $stored );
		return $cached;
	}
	$info_cat = function_exists( 'della_theme_get_response_info_parent_category' ) ? della_theme_get_response_info_parent_category() : null;
	if ( ! $info_cat ) {
		$cached = array();
		return $cached;
	}
	$term_ids = array( (int) $info_cat->term_id );
	$children = get_terms( array( 'taxonomy' => 'category', 'parent' => $info_cat->term_id, 'hide_empty' => false, 'fields' => 'ids' ) );
	if ( ! is_wp_error( $children ) && ! empty( $children ) ) {
		$term_ids = array_merge( $term_ids, array_map( 'intval', $children ) );
	}
	set_transient( $transient_key, $term_ids, della_theme_cat_transient_ttl() );
	$cached = $term_ids;
	return $cached;
}

/**
 * 카테고리 관련 트랜지언트 삭제 (관리자에서 카테고리 수정/삭제 시 캐시 무효화).
 */
function della_theme_flush_category_transients() {
	delete_transient( 'della_success_parent_term_id' );
	delete_transient( 'della_resp_info_parent_term_id' );
	delete_transient( 'della_success_exclude_term_ids' );
	delete_transient( 'della_resp_info_term_ids' );
	delete_transient( 'della_success_allowed_cat_data' );
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_della_resp_filter_%'" );
}

/**
 * 성공사례 페이지 대 카테고리 목록 (slug, label). 페이지·캐시 헬퍼와 동기화용.
 *
 * @return array[] [ ['slug' => ..., 'label' => ...], ... ]
 */
function della_theme_get_success_case_sidebar_main_cats() {
	return array(
		array( 'slug' => 'rape', 'label' => __( '강간', 'della-theme' ) ),
		array( 'slug' => 'sexual_assult', 'label' => __( '강제추행', 'della-theme' ) ),
		array( 'slug' => 'public_place_sex_crimes', 'label' => __( '공공장소성범죄', 'della-theme' ) ),
		array( 'slug' => 'military_sexual_crimes', 'label' => __( '군성범죄', 'della-theme' ) ),
		array( 'slug' => 'spycam_crime', 'label' => __( '디지털성범죄', 'della-theme' ) ),
		array( 'slug' => 'minor_targeted_sex_crimes', 'label' => __( '미성년자성범죄', 'della-theme' ) ),
		array( 'slug' => 'sex_work', 'label' => __( '성매매', 'della-theme' ) ),
		array( 'slug' => 'workplace', 'label' => __( '직장내성범죄', 'della-theme' ) ),
	);
}

/**
 * 성공사례 허브 필터용 허용 카테고리 ID 목록 및 term_id→label 매핑.
 * 요청당 정적 캐시 + 트랜지언트로 get_category_by_slug 반복 제거.
 *
 * @return array { ids: int[], id_to_label: int[] }
 */
function della_theme_get_success_case_allowed_cat_data() {
	static $cached = null;
	if ( $cached !== null ) {
		return $cached;
	}
	$transient_key = 'della_success_allowed_cat_data';
	$stored = get_transient( $transient_key );
	if ( false !== $stored && is_array( $stored ) && isset( $stored['ids'], $stored['id_to_label'] ) ) {
		if ( ! isset( $stored['slug_to_id'] ) || ! is_array( $stored['slug_to_id'] ) ) {
			$stored['slug_to_id'] = array();
		}
		$cached = $stored;
		return $cached;
	}
	$allowed_cat_ids = array();
	$main_cat_id_to_label = array();
	$slug_to_id = array();
	$items = della_theme_get_success_case_sidebar_main_cats();
	foreach ( $items as $item ) {
		$term = get_category_by_slug( $item['label'] . '-성공사례' );
		if ( ! $term ) {
			$term = get_category_by_slug( $item['slug'] );
		}
		if ( $term ) {
			$tid = (int) $term->term_id;
			$allowed_cat_ids[] = $term->term_id;
			$main_cat_id_to_label[ $tid ] = $item['label'];
			$slug_to_id[ $item['slug'] ] = $tid;
		}
	}
	$cached = array( 'ids' => $allowed_cat_ids, 'id_to_label' => $main_cat_id_to_label, 'slug_to_id' => $slug_to_id );
	set_transient( $transient_key, $cached, della_theme_cat_transient_ttl() );
	return $cached;
}

add_action( 'edited_term', 'della_theme_flush_category_transients_on_term_change', 10, 3 );
add_action( 'delete_term', 'della_theme_flush_category_transients_on_term_change', 10, 3 );

/**
 * taxonomy가 category일 때만 트랜지언트 플러시.
 *
 * @param int    $term_id  Term ID.
 * @param int    $tt_id    Term taxonomy ID.
 * @param string $taxonomy Taxonomy slug.
 */
function della_theme_flush_category_transients_on_term_change( $term_id, $tt_id, $taxonomy = '' ) {
	if ( $taxonomy === 'category' ) {
		della_theme_flush_category_transients();
	}
}

/**
 * 단일 글이 대응정보 카테고리에 속하는지 여부 (상세 페이지 메타·키워드용).
 * 캐시된 대응정보 term_id 배열 사용으로 get_terms 중복 제거.
 *
 * @param WP_Post|null $post 포스트 객체. null이면 현재 쿼리 포스트.
 * @return bool
 */
function della_theme_is_response_info_post( $post = null ) {
	if ( ! $post ) {
		$post = get_queried_object();
	}
	if ( ! $post instanceof WP_Post || $post->post_type !== 'post' ) {
		return false;
	}
	$term_ids = function_exists( 'della_theme_get_response_info_term_ids' ) ? della_theme_get_response_info_term_ids() : array();
	return ! empty( $term_ids ) && has_category( $term_ids, $post );
}

/**
 * 성공사례 상세 글 전용 유니크 meta description (글마다 중복 없이).
 *
 * @param WP_Post $post 성공사례 포스트.
 * @return string 120~320자 내 정리된 설명.
 */
function della_theme_success_case_unique_description( $post ) {
	if ( ! $post instanceof WP_Post ) {
		return '';
	}
	$max = defined( 'DELLA_THEME_DESC_MAX' ) ? DELLA_THEME_DESC_MAX - 20 : 300;
	$law = __( '법무법인 동주', 'della-theme' );

	// 1) 커스텀 SEO 설명이 있으면 우선 사용
	$custom = get_post_meta( $post->ID, 'della_case_seo_description', true );
	if ( is_string( $custom ) && trim( $custom ) !== '' ) {
		$out = wp_strip_all_tags( $custom );
		$out = preg_replace( '/\s+/', ' ', trim( $out ) );
		if ( function_exists( 'mb_strlen' ) && mb_strlen( $out ) > $max ) {
			$out = ( function_exists( 'mb_substr' ) ? mb_substr( $out, 0, $max - 3 ) : substr( $out, 0, $max - 3 ) ) . '…';
		} elseif ( strlen( $out ) > $max ) {
			$out = substr( $out, 0, $max - 3 ) . '…';
		}
		return della_theme_trim_meta_description( $out );
	}

	// 2) 제목(또는 SEO 제목)으로 시작해 글마다 구분
	$title = get_post_meta( $post->ID, 'della_case_seo_title', true );
	if ( ! is_string( $title ) || trim( $title ) === '' ) {
		$title = get_the_title( $post );
	}
	$title = wp_strip_all_tags( $title );
	$title = preg_replace( '/\s+/', ' ', trim( $title ) );

	// 3) 요약: 요약문 있으면 사용, 없으면 본문 앞 80단어
	$summary = '';
	if ( has_excerpt( $post ) ) {
		$summary = get_the_excerpt( $post );
	} else {
		$summary = wp_trim_words( get_the_content( null, false, $post ), 80 );
	}
	$summary = wp_strip_all_tags( $summary );
	$summary = preg_replace( '/\s+/', ' ', trim( $summary ) );

	// 4) 결과 유형(무혐의·기소유예 등) 있으면 붙여 유니크하게
	$result = get_post_meta( $post->ID, 'della_case_result', true );
	$result = is_string( $result ) ? trim( $result ) : '';
	if ( $result !== '' ) {
		$result = $result . ' 사례. ';
	} else {
		$result = '';
	}

	// 조합: "제목. 요약. [결과] 사례. 법무법인 동주"
	$out = $title;
	if ( $summary !== '' ) {
		$out .= '. ' . $summary;
	}
	$out .= '. ' . $result . $law;

	$out = preg_replace( '/\s+/', ' ', trim( $out ) );
	if ( function_exists( 'mb_strlen' ) && mb_strlen( $out ) > $max ) {
		$out = ( function_exists( 'mb_substr' ) ? mb_substr( $out, 0, $max - 3 ) : substr( $out, 0, $max - 3 ) ) . '…';
	} elseif ( strlen( $out ) > $max ) {
		$out = substr( $out, 0, $max - 3 ) . '…';
	}
	return della_theme_trim_meta_description( $out );
}

/**
 * 대응정보 상세 글 전용 유니크 meta description (글마다 중복 없이).
 *
 * @param WP_Post $post 대응정보 포스트.
 * @return string 120~320자 내 정리된 설명.
 */
function della_theme_response_info_unique_description( $post ) {
	if ( ! $post instanceof WP_Post ) {
		return '';
	}
	$max = defined( 'DELLA_THEME_DESC_MAX' ) ? DELLA_THEME_DESC_MAX - 20 : 300;
	$law = __( '법무법인 동주', 'della-theme' );

	// 1) 커스텀 SEO 설명이 있으면 우선 사용
	$custom = get_post_meta( $post->ID, 'della_info_seo_description', true );
	if ( is_string( $custom ) && trim( $custom ) !== '' ) {
		$out = wp_strip_all_tags( $custom );
		$out = preg_replace( '/\s+/', ' ', trim( $out ) );
		if ( function_exists( 'mb_strlen' ) && mb_strlen( $out ) > $max ) {
			$out = ( function_exists( 'mb_substr' ) ? mb_substr( $out, 0, $max - 3 ) : substr( $out, 0, $max - 3 ) ) . '…';
		} elseif ( strlen( $out ) > $max ) {
			$out = substr( $out, 0, $max - 3 ) . '…';
		}
		return della_theme_trim_meta_description( $out );
	}

	// 2) 제목(또는 SEO 제목)으로 시작해 글마다 구분
	$title = get_post_meta( $post->ID, 'della_info_seo_title', true );
	if ( ! is_string( $title ) || trim( $title ) === '' ) {
		$title = get_the_title( $post );
	}
	$title = wp_strip_all_tags( $title );
	$title = preg_replace( '/\s+/', ' ', trim( $title ) );

	// 3) 요약: 요약문 있으면 사용, 없으면 본문 앞 80단어
	$summary = '';
	if ( has_excerpt( $post ) ) {
		$summary = get_the_excerpt( $post );
	} else {
		$summary = wp_trim_words( get_the_content( null, false, $post ), 80 );
	}
	$summary = wp_strip_all_tags( $summary );
	$summary = preg_replace( '/\s+/', ' ', trim( $summary ) );

	// 4) 카테고리(대응정보 제외)로 주제 보강 — 유니크하게
	$cat_label = '';
	$cats = get_the_category( $post->ID );
	if ( $cats ) {
		$names = array();
		foreach ( $cats as $c ) {
			if ( $c->name === '성범죄대응정보' || $c->name === '대응정보' || $c->name === '대응 정보' ) {
				continue;
			}
			$n = trim( $c->name );
			if ( $n !== '' ) {
				$names[] = $n;
			}
		}
		if ( ! empty( $names ) ) {
			$cat_label = implode( '·', array_slice( $names, 0, 3 ) ) . ' 대응 가이드. ';
		}
	}

	// 조합: "제목. 요약. [카테고리] 대응 가이드. 법무법인 동주"
	$out = $title;
	if ( $summary !== '' ) {
		$out .= '. ' . $summary;
	}
	$out .= '. ' . $cat_label . $law;

	$out = preg_replace( '/\s+/', ' ', trim( $out ) );
	if ( function_exists( 'mb_strlen' ) && mb_strlen( $out ) > $max ) {
		$out = ( function_exists( 'mb_substr' ) ? mb_substr( $out, 0, $max - 3 ) : substr( $out, 0, $max - 3 ) ) . '…';
	} elseif ( strlen( $out ) > $max ) {
		$out = substr( $out, 0, $max - 3 ) . '…';
	}
	return della_theme_trim_meta_description( $out );
}

/** SEO: page title 30–65 chars, meta description 120–320 chars */
if ( ! defined( 'DELLA_THEME_TITLE_MAX' ) ) {
	define( 'DELLA_THEME_TITLE_MAX', 65 );
}
if ( ! defined( 'DELLA_THEME_DESC_MIN' ) ) {
	define( 'DELLA_THEME_DESC_MIN', 120 );
}
if ( ! defined( 'DELLA_THEME_DESC_MAX' ) ) {
	define( 'DELLA_THEME_DESC_MAX', 320 );
}

/**
 * SEO: Trim page title to 30–65 characters.
 */
function della_theme_trim_document_title( $title ) {
	if ( ! is_string( $title ) || $title === '' ) {
		return $title;
	}
	$max = defined( 'DELLA_THEME_TITLE_MAX' ) ? DELLA_THEME_TITLE_MAX : 65;
	if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
		if ( mb_strlen( $title ) > $max ) {
			return mb_substr( $title, 0, $max - 1 ) . '…';
		}
		return $title;
	}
	if ( strlen( $title ) > $max ) {
		return substr( $title, 0, $max - 1 ) . '…';
	}
	return $title;
}

/**
 * SEO: Trim meta description to 120–320 characters (cap at max).
 */
function della_theme_trim_meta_description( $description ) {
	if ( ! is_string( $description ) || $description === '' ) {
		return $description;
	}
	$max = defined( 'DELLA_THEME_DESC_MAX' ) ? DELLA_THEME_DESC_MAX : 320;
	if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) ) {
		if ( mb_strlen( $description ) > $max ) {
			return mb_substr( $description, 0, $max - 3 ) . '…';
		}
		return $description;
	}
	if ( strlen( $description ) > $max ) {
		return substr( $description, 0, $max - 3 ) . '…';
	}
	return $description;
}

/**
 * SEO: Ensure meta description is at least 120 chars when possible (append firm name once if short).
 */
function della_theme_ensure_description_length( $description, $law_firm_name = '' ) {
	if ( ! is_string( $description ) || $description === '' ) {
		return $description;
	}
	$min = defined( 'DELLA_THEME_DESC_MIN' ) ? DELLA_THEME_DESC_MIN : 120;
	$max = defined( 'DELLA_THEME_DESC_MAX' ) ? DELLA_THEME_DESC_MAX : 320;
	$len = function_exists( 'mb_strlen' ) ? mb_strlen( $description ) : strlen( $description );
	if ( $len >= $min || $len >= $max ) {
		return $description;
	}
	if ( $law_firm_name !== '' && strpos( $description, $law_firm_name ) === false ) {
		$suffix = ' | ' . $law_firm_name;
		$candidate = $description . $suffix;
		$clen = function_exists( 'mb_strlen' ) ? mb_strlen( $candidate ) : strlen( $candidate );
		if ( $clen <= $max ) {
			return $candidate;
		}
	}
	return $description;
}

/**
 * SEO: 문서당 하나의 <title>, 페이지별 고유·정확한 제목 (30–65자, 네이버 검색로봇 대응)
 * 메인은 100점 메타 세트와 동일한 title 사용 (중복 방지).
 */
function della_theme_document_title_parts( $parts ) {
	$site = get_bloginfo( 'name' );
	if ( is_404() ) {
		$parts['title'] = della_theme_trim_document_title( __( '페이지를 찾을 수 없습니다', 'della-theme' ) . ' | ' . $site );
		unset( $parts['tagline'], $parts['site'], $parts['page'] );
		return $parts;
	}
	if ( is_front_page() ) {
		$parts['title'] = '수원 성범죄 전문변호사 | 강제추행·카메라촬영 대응 | 법무법인 동주';
		unset( $parts['tagline'], $parts['site'], $parts['page'] );
		return $parts;
	}
	// 성공사례 페이지: URL 경로로 확실히 설정 (플러그인/캐시와 무관)
	$req_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path    = trim( (string) parse_url( $req_uri, PHP_URL_PATH ), '/' );
	$path    = preg_replace( '#/+#', '/', $path );
	if ( $path === 'success-cases' || strpos( $path, 'success-cases/' ) === 0 || in_array( $path, array( 'case', 'success', '성범죄-성공사례', '성공사례' ), true ) ) {
		$parts['title'] = della_theme_trim_document_title( '수원 성범죄 성공사례 | 강제추행·카메라촬영 무혐의·기소유예 사례 | 법무법인 동주' );
		unset( $parts['tagline'], $parts['site'], $parts['page'] );
		return $parts;
	}
	if ( della_theme_is_lawyers_page() ) {
		$lawyer_slug = get_query_var( 'lawyer_slug' );
		if ( $lawyer_slug && function_exists( 'della_theme_get_lawyer_by_slug' ) ) {
			$lawyer = della_theme_get_lawyer_by_slug( $lawyer_slug );
			if ( $lawyer && ! empty( $lawyer['name'] ) ) {
				$parts['title'] = della_theme_trim_document_title( $lawyer['name'] . ' 변호사 프로필 | 수원 성범죄 전문변호사 | 법무법인 동주' );
				unset( $parts['tagline'], $parts['site'], $parts['page'] );
				return $parts;
			}
		}
		$parts['title'] = della_theme_trim_document_title( '성범죄 전문변호사 소개 | 형사법 전문 변호사 팀 | 법무법인 동주' );
		unset( $parts['tagline'], $parts['site'], $parts['page'] );
		return $parts;
	}
	if ( della_theme_is_response_board_page() ) {
		$parts['title'] = della_theme_trim_document_title( '성범죄 대응정보 가이드 | 강제추행·불법촬영·아청법·판례·FAQ | 법무법인 동주' );
		unset( $parts['tagline'], $parts['site'], $parts['page'] );
		return $parts;
	}
	if ( della_theme_is_success_cases_page() ) {
		$parts['title'] = della_theme_trim_document_title( '수원 성범죄 성공사례 | 강제추행·카메라촬영 무혐의·기소유예 사례 | 법무법인 동주' );
		unset( $parts['tagline'], $parts['site'], $parts['page'] );
		return $parts;
	}
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$parts['title'] = della_theme_trim_document_title( get_the_title( $post ) . ' | ' . $site );
			unset( $parts['tagline'], $parts['site'], $parts['page'] );
		}
	}
	// Catch-all: ensure any title part is within 30–65 chars
	if ( ! empty( $parts['title'] ) && is_string( $parts['title'] ) ) {
		$parts['title'] = della_theme_trim_document_title( $parts['title'] );
	}
	return $parts;
}
add_filter( 'document_title_parts', 'della_theme_document_title_parts', 10, 1 );

/**
 * SEO: 프론트 페이지 전용 LegalService JSON-LD만 출력 (description/og/twitter/canonical/robots는 AIOSEO에서 관리).
 */
function della_theme_front_page_meta_100() {
	if ( ! is_front_page() ) {
		return;
	}
	$road     = get_theme_mod( 'della_road_address', '경기 수원시 영통구 광교중앙로248번길 7-2' );
	$road2    = get_theme_mod( 'della_road_address2', '원희캐슬광교 B동 902호, 903호' );
	$street   = trim( $road . ' ' . $road2 );
	$phone    = get_theme_mod( 'della_phone', '031-216-1155' );
	$base_url = home_url( '/' );
	$og_image = home_url( '/img/og-sexcrime-dongju.jpg' );
	$logo_url = home_url( '/img/logo.png' );
	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode(
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'LegalService',
			'name'        => '법무법인 동주',
			'url'         => $base_url,
			'image'       => $og_image,
			'logo'        => $logo_url,
			'description' => '수원 성범죄 전문변호사 법무법인 동주. 고소 전 합의부터 경찰조사, 검찰송치, 재판까지 직접 대응.',
			'address'     => array(
				'@type'           => 'PostalAddress',
				'addressCountry'  => 'KR',
				'addressRegion'   => '경기도',
				'addressLocality' => '수원',
				'streetAddress'   => $street,
			),
			'areaServed'  => array( '수원', '용인', '성남', '화성', '동탄', '안양', '의왕', '안산', '오산', '평택', '안성', '이천' ),
			'telephone'   => $phone,
			'priceRange'  => '$$',
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	);
	echo "\n" . '</script>' . "\n";
}
add_action( 'wp_head', 'della_theme_front_page_meta_100', 0 );

/**
 * Google Analytics 4 (GA4) — head에 gtag 스크립트 출력 (복수 측정 ID 지원)
 */
function della_theme_ga4_script() {
	$ga_ids = apply_filters( 'della_theme_ga4_measurement_ids', array( 'G-C817947ECG', 'G-9Z7QP4ZBZ9' ) );
	if ( ! is_array( $ga_ids ) ) {
		$ga_ids = array_filter( array( $ga_ids ) );
	}
	$ga_ids = array_filter( array_map( 'trim', $ga_ids ) );
	if ( empty( $ga_ids ) ) {
		return;
	}
	$first_id = reset( $ga_ids );
	?>
<!-- della GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $first_id ); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
<?php foreach ( $ga_ids as $id ) : ?>
gtag('config', '<?php echo esc_js( $id ); ?>');
<?php endforeach; ?>
</script>
	<?php
}
add_action( 'wp_head', 'della_theme_ga4_script', 5 );

/**
 * SEO: Front page default meta description (키워드 포함, 120–320자)
 */
function della_theme_front_page_description() {
	if ( ! is_front_page() ) {
		return null;
	}
	$custom = get_bloginfo( 'description' );
	$firm   = __( '법무법인 동주', 'della-theme' );
	if ( $custom && strlen( $custom ) >= 20 ) {
		$kw = __( '수원성범죄전문변호사', 'della-theme' );
		if ( strpos( $custom, $kw ) === false ) {
			return $kw . ' — ' . $custom . ( strpos( $custom, $firm ) === false ? ' | ' . $firm : '' );
		}
		return $custom . ( strpos( $custom, $firm ) === false ? ' | ' . $firm : '' );
	}
	return __( '수원성범죄변호사, 수원성범죄전문변호사 ', 'della-theme' ) . __( '법무법인 동주', 'della-theme' ) . __( ' 수원 성범죄연구센터. 성범죄피해자변호사·형사전문변호사 상담. 수원 광교 신속 상담.', 'della-theme' );
}

/**
 * Header specialty text (e.g. practice area) - Customizer
 */
function della_theme_customize_register( $wp_customize ) {
	if ( ! isset( $wp_customize ) ) {
		return;
	}
	$wp_customize->add_section( 'della_header_section', array(
		'title'    => __( 'Header', 'della-theme' ),
		'priority' => 30,
	) );
	$wp_customize->add_setting( 'della_header_specialty', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'della_header_specialty', array(
		'label'   => __( 'Header specialty text (after brand)', 'della-theme' ),
		'section' => 'della_header_section',
		'type'    => 'text',
	) );

	/* Hero: 홈페이지 바로가기 링크 */
	$wp_customize->add_section( 'della_hero_section', array(
		'title'    => __( 'Hero (메인 배너)', 'della-theme' ),
		'priority' => 35,
	) );
	$wp_customize->add_setting( 'della_hero_home_url', array(
		'default'           => home_url( '/' ),
		'sanitize_callback'  => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'della_hero_home_url', array(
		'label'       => __( '홈페이지 바로가기 URL', 'della-theme' ),
		'description' => __( '예: https://sexcrimecenter-dongju.com/', 'della-theme' ),
		'section'     => 'della_hero_section',
		'type'        => 'url',
	) );

	/* 오시는 길 */
	$wp_customize->add_section( 'della_directions_section', array(
		'title'    => __( '오시는 길', 'della-theme' ),
		'priority' => 90,
	) );
	$wp_customize->add_setting( 'della_road_address', array(
		'default'           => '경기 수원시 영통구 광교중앙로248번길 7-2',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'della_road_address', array(
		'label'   => __( '도로명 주소', 'della-theme' ),
		'section' => 'della_directions_section',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'della_road_address2', array(
		'default'           => '원희캐슬광교 B동 902호, 903호',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'della_road_address2', array(
		'label'   => __( '도로명 주소 (상세)', 'della-theme' ),
		'section' => 'della_directions_section',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'della_lot_address', array(
		'default'           => '경기 수원시 영통구 하동 989',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'della_lot_address', array(
		'label'   => __( '지번', 'della-theme' ),
		'section' => 'della_directions_section',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'della_phone', array(
		'default'           => '031-216-1155',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'della_phone', array(
		'label'   => __( '전화번호', 'della-theme' ),
		'section' => 'della_directions_section',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'della_naver_reserve_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'della_naver_reserve_url', array(
		'label'       => __( '네이버 예약 URL', 'della-theme' ),
		'description' => __( '비우면 버튼이 링크되지 않습니다.', 'della-theme' ),
		'section'     => 'della_directions_section',
		'type'        => 'url',
	) );
	$wp_customize->add_setting( 'della_postal_code', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'della_postal_code', array(
		'label'       => __( '우편번호 (스키마용)', 'della-theme' ),
		'description' => __( '성공사례 등 JSON-LD 주소에 사용. 비우면 출력하지 않습니다.', 'della-theme' ),
		'section'     => 'della_directions_section',
		'type'        => 'text',
	) );
	$wp_customize->add_setting( 'della_naver_place_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'della_naver_place_url', array(
		'label'       => __( '네이버 플레이스 URL (스키마 sameAs)', 'della-theme' ),
		'section'     => 'della_directions_section',
		'type'        => 'url',
	) );
	$wp_customize->add_setting( 'della_naver_blog_url', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'della_naver_blog_url', array(
		'label'       => __( '네이버 블로그 URL (스키마 sameAs)', 'della-theme' ),
		'section'     => 'della_directions_section',
		'type'        => 'url',
	) );
}
add_action( 'customize_register', 'della_theme_customize_register' );

/**
 * Custom logo: SEO-friendly alt with site name (for screen readers and SEO)
 */
function della_theme_custom_logo_alt( $html, $logo_id = 0 ) {
	if ( ! is_string( $html ) || $html === '' ) {
		return $html;
	}
	$site_name = get_bloginfo( 'name' );
	if ( empty( $site_name ) ) {
		return $html;
	}
	$alt_text = $site_name . ' ' . __( '로고', 'della-theme' );
	$alt      = 'alt="' . esc_attr( $alt_text ) . '"';
	if ( preg_match( '/<img[^>]+>/', $html, $m ) ) {
		if ( strpos( $m[0], 'alt=' ) !== false ) {
			$html = preg_replace( '/alt="[^"]*"/', $alt, $html );
		} else {
			$html = preg_replace( '/<img/', '<img ' . $alt . ' ', $html );
		}
	}
	return $html;
}
add_filter( 'get_custom_logo', 'della_theme_custom_logo_alt', 10, 2 );

/**
 * CSS/JS 로드 최적화 — 미니파이 버전, filemtime 버전링, 비동기/지연 로드
 */
function della_theme_scripts() {
	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();
	$asset_ver = della_theme_asset_version();

	// Google Fonts (기본 비활성화 — 요청 수 절감. 폰트 사용 시 add_filter( 'della_theme_load_google_fonts', '__return_true' );)
	if ( apply_filters( 'della_theme_load_google_fonts', false ) ) {
		wp_enqueue_style(
			'della-google-fonts',
			'https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700&family=Noto+Sans+KR:wght@400;600;700&display=swap',
			array(),
			null
		);
	}

	// CSS — dist 폴더에서 로드, 공통 버전으로 캐시 무효화
	$css_dist = $theme_uri . '/assets/css/dist';
	$css_dist_path = $theme_dir . '/assets/css/dist';
	$common_min = $css_dist_path . '/common.min.css';
	$common_css = $css_dist_path . '/common.css';
	if ( file_exists( $common_min ) ) {
		wp_enqueue_style(
			'della-theme-style',
			$css_dist . '/common.min.css',
			array(),
			$asset_ver
		);
	} else {
		wp_enqueue_style(
			'della-theme-style',
			$css_dist . '/common.css',
			array(),
			$asset_ver
		);
	}

	$page_styles = array();
	if ( is_front_page() ) {
		$page_styles[] = array( 'front-page', 'della-theme-front' );
	}
	if ( della_theme_is_lawyers_page() ) {
		$page_styles[] = array( 'page-lawyers', 'della-theme-page-lawyers' );
	}
	if ( is_singular( 'page' ) && in_array( get_page_template_slug(), array( 'page-response-board.php', 'page-success-cases.php' ), true ) ) {
		$page_styles[] = array( 'page-board', 'della-theme-page-board' );
	}
	if ( is_singular( 'page' ) && get_page_template_slug() === 'page-sitemap.php' ) {
		$page_styles[] = array( 'page-sitemap', 'della-theme-page-sitemap' );
	}
	foreach ( $page_styles as list( $file, $handle ) ) {
		$min = $css_dist_path . '/' . $file . '.min.css';
		$full = $css_dist_path . '/' . $file . '.css';
		if ( file_exists( $min ) ) {
			wp_enqueue_style( $handle, $css_dist . '/' . $file . '.min.css', array( 'della-theme-style' ), $asset_ver );
		} elseif ( file_exists( $full ) ) {
			wp_enqueue_style( $handle, $css_dist . '/' . $file . '.css', array( 'della-theme-style' ), $asset_ver );
		}
	}

	// JS — 테마 전용 외부 스크립트 없음 (메뉴/헤더/슬라이더 등은 인라인 스크립트로만 동작).
}
add_action( 'wp_enqueue_scripts', 'della_theme_scripts' );

/**
 * JS defer — 테마 외부 JS 제거로 미사용. 필요 시 스크립트 핸들 추가.
 */
function della_theme_script_defer( $tag, $handle, $src ) {
	return $tag;
}

/**
 * 불필요한 WordPress 기본 스크립트/스타일 제거 (프론트 전용, 로딩 경량화)
 * SEO 플러그인(Yoast, Rank Math 등)의 프론트엔드 미리보기 스타일(FacebookPreview.css 등) 제거
 */
function della_theme_remove_unused_wp_assets() {
	if ( is_admin() ) {
		return;
	}
	// oEmbed용 스크립트 — 외부 URL 임베드 미사용 시 제거
	wp_dequeue_script( 'wp-embed' );
	// 블록 테마용 스타일 — 클래식 테마에서는 미사용
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wp-block-library-theme-inline' );
	// WP 5.9+ 클래식 호환용 전역 스타일 — 테마 자체 CSS로 대체
	wp_dequeue_style( 'global-styles' );

	// SEO 플러그인 프론트엔드용 미리보기/소셜 스타일 제거 (관리자·편집 화면용이 프론트에 불필요하게 로드되는 경우)
	$seo_frontend_styles = array(
		'wpseo-block-editor',           // Yoast SEO 블록 에디터/소셜 미리보기
		'yoast-seo-block-editor',        // Yoast (다른 버전)
		'yoast-social-preview',         // Yoast 소셜 미리보기
		'rank-math-block-editor',       // Rank Math 블록 에디터
		'rank-math-frontend',            // Rank Math 프론트 (미리보기용)
		'aioseo-block-editor',          // All in One SEO
		'aioseo-social-preview',        // All in One SEO 소셜 미리보기
	);
	foreach ( $seo_frontend_styles as $handle ) {
		wp_dequeue_style( $handle );
	}

	// 로그인 시 상단 admin bar·dashicons 스타일 제거 (프론트 로딩 경량화). 부작용: 로그인 사용자 admin bar 스타일 없음.
	// 필요 시 아래 두 줄 제거하거나 della_theme_remove_admin_bar_styles 필터로 false 반환하여 복원.
	if ( apply_filters( 'della_theme_remove_admin_bar_styles', true ) ) {
		wp_dequeue_style( 'admin-bar' );
		wp_dequeue_style( 'dashicons' );
	}
}
add_action( 'wp_enqueue_scripts', 'della_theme_remove_unused_wp_assets', 100 );

/**
 * SEO 플러그인 스타일 중 URL에 FacebookPreview 등 포함된 항목 프론트엔드에서 제거
 * (핸들을 모를 때 style_loader_src로 제거)
 */
function della_theme_remove_seo_preview_styles_by_src( $href, $handle ) {
	if ( is_admin() ) {
		return $href;
	}
	$remove_patterns = array(
		'FacebookPreview',
		'TwitterPreview',
		'SocialPreview',
		'GoogleSearchPreview',
		'seo-preview',
		'ProBadge',
		'Tabs',
		'Button.',
		'Index.',
		'app.',
		'admin-bar',  // 관리자 바 스타일(admin-bar.d9a8e9bb.css 등) 프론트 미로드
	);
	foreach ( $remove_patterns as $pattern ) {
		if ( $href && strpos( $href, $pattern ) !== false ) {
			return false;
		}
	}
	return $href;
}
add_filter( 'style_loader_src', 'della_theme_remove_seo_preview_styles_by_src', 10, 2 );

/**
 * (디버그) 프론트엔드에 로드된 모든 스타일 핸들·URL 출력 — FacebookPreview.css 등 정확한 핸들 확인용
 * 사용법: 아래 주석을 해제하고 프론트 페이지 새로고침 후 HTML 소스 또는 페이지 하단에 출력된 목록에서
 * "FacebookPreview"가 포함된 src를 찾아 해당 handle을 위 $seo_frontend_styles 배열에 추가.
 * 확인 후 반드시 주석 다시 처리할 것.
 */
/*
add_action( 'wp_print_styles', function() {
	if ( is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wp_styles;
	if ( ! isset( $wp_styles->queue ) ) {
		return;
	}
	echo "\n<!-- Enqueued styles (handle => src): ";
	foreach ( $wp_styles->queue as $handle ) {
		if ( isset( $wp_styles->registered[ $handle ]->src ) ) {
			echo $handle . ' => ' . $wp_styles->registered[ $handle ]->src . ' | ';
		}
	}
	echo " -->\n";
}, 999 );
*/

/**
 * jQuery 제거 — 테마는 바닐라 JS만 사용. 플러그인에서 jQuery 필요 시 이 블록 제거.
 */
function della_theme_remove_jquery() {
	if ( is_admin() ) {
		return;
	}
	wp_deregister_script( 'jquery' );
	wp_dequeue_script( 'jquery' );
}
add_action( 'wp_enqueue_scripts', 'della_theme_remove_jquery', 100 );

/**
 * 크리티컬 CSS 인라인 출력 (above-the-fold) — 첫 화면 즉시 렌더, 디자인 변경 없음
 * 내용은 transient로 캐시하여 매 요청마다 디스크에서 32KB 읽기를 방지함.
 */
function della_theme_inline_critical_css() {
	$theme_dir = get_stylesheet_directory();
	$critical_file = $theme_dir . '/assets/css/dist/critical.min.css';
	if ( ! file_exists( $critical_file ) ) {
		$critical_file = $theme_dir . '/assets/css/critical.min.css';
	}
	if ( ! file_exists( $critical_file ) ) {
		return;
	}
	$cache_key = 'della_critical_css_' . della_theme_asset_version();
	$css = get_transient( $cache_key );
	if ( $css === false ) {
		$css = file_get_contents( $critical_file );
		if ( $css === false || $css === '' ) {
			return;
		}
		$css = wp_strip_all_tags( $css );
		set_transient( $cache_key, $css, MONTH_IN_SECONDS );
	}
	if ( $css === '' ) {
		return;
	}
	echo '<style id="della-critical-css">' . "\n" . $css . "\n" . '</style>' . "\n";
}
add_action( 'wp_head', 'della_theme_inline_critical_css', 0 );

/**
 * 메인·페이지별 스타일시트 비동기 로드 (미니파이 사용 시) — 렌더 블로킹 제거
 */
function della_theme_async_full_css_tag( $html, $handle, $href ) {
    // common(메뉴 포함)은 동기 로드 — FOUC 방지
    if ( $handle === 'della-theme-style' ) {
        return $html;
    }

    // 페이지별 CSS만 비동기 로드
    $async_handles = array(
        'della-theme-front',
        'della-theme-page-lawyers',
        'della-theme-page-board',
        'della-theme-page-sitemap'
    );
    if ( ! in_array( $handle, $async_handles, true ) ) {
        return $html;
    }
    $theme_dir = get_stylesheet_directory();
    return '<link rel="stylesheet" id="' . esc_attr( $handle ) . '-css" href="' . esc_url( $href ) . '" media="print" onload="this.media=\'all\'" />' . "\n<noscript>" . $html . '</noscript>';
}
add_filter( 'style_loader_tag', 'della_theme_async_full_css_tag', 10, 3 );

/**
 * Google Fonts 비차단 로드 — 렌더 블로킹 제거로 FCP/LCP 개선 (디자인 동일)
 */
function della_theme_fonts_async_tag( $html, $handle, $href ) {
	if ( $handle !== 'della-google-fonts' ) {
		return $html;
	}
	return '<link rel="stylesheet" id="' . esc_attr( $handle ) . '-css" href="' . esc_url( $href ) . '" media="print" onload="this.media=\'all\'" />' . "\n<noscript>" . $html . '</noscript>';
}
add_filter( 'style_loader_tag', 'della_theme_fonts_async_tag', 10, 3 );

/**
 * 폰트 도메인 preconnect — 폰트 요청 지연 감소
 * Google Fonts 미사용 시(필터로 비활성화) preconnect 생략하여 요청 수 절감
 */
function della_theme_preconnect_fonts() {
	if ( ! apply_filters( 'della_theme_load_google_fonts', true ) ) {
		return;
	}
	echo '<link rel="preconnect" href="https://fonts.googleapis.com" />' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";
}
add_action( 'wp_head', 'della_theme_preconnect_fonts', -1 );

/**
 * 변호사 프로필 이미지 — 고해상도 우선 URL (화질 개선)
 * @2x 파일이 있으면 해당 URL 반환 → 브라우저가 다운스케일하여 선명하게 표시
 *
 * @param string $image_filename 예: dongju-kim-yunseo-lawyer.png
 * @param string $base_url       예: uploads baseurl + /2026/02
 * @param string $base_dir       예: uploads basedir + /2026/02
 * @return string 이미지 URL
 */
function della_theme_lawyer_image_url( $image_filename, $base_url, $base_dir ) {
	static $exists_cache = array();
	$path_info = pathinfo( $image_filename );
	$name      = isset( $path_info['filename'] ) ? $path_info['filename'] : '';
	$ext       = isset( $path_info['extension'] ) ? $path_info['extension'] : 'png';
	$base_dir  = trailingslashit( $base_dir );
	$base_url  = trailingslashit( $base_url );

	// WebP 있으면 우선 사용 (다운로드 시간 단축)
	if ( ! empty( $name ) ) {
		$webp_2x = $name . '@2x.webp';
		$webp_1x = $name . '.webp';
		$path_webp_2x = $base_dir . $webp_2x;
		$path_webp_1x = $base_dir . $webp_1x;
		if ( ! isset( $exists_cache[ $path_webp_2x ] ) ) {
			$exists_cache[ $path_webp_2x ] = file_exists( $path_webp_2x );
		}
		if ( ! isset( $exists_cache[ $path_webp_1x ] ) ) {
			$exists_cache[ $path_webp_1x ] = file_exists( $path_webp_1x );
		}
		if ( $exists_cache[ $path_webp_2x ] ) {
			return $base_url . $webp_2x;
		}
		if ( $exists_cache[ $path_webp_1x ] ) {
			return $base_url . $webp_1x;
		}
	}

	$file_2x = $name . '@2x.' . $ext;
	$path_2x = $base_dir . $file_2x;
	if ( ! empty( $name ) ) {
		if ( ! isset( $exists_cache[ $path_2x ] ) ) {
			$exists_cache[ $path_2x ] = file_exists( $path_2x );
		}
		if ( $exists_cache[ $path_2x ] ) {
			return $base_url . $file_2x;
		}
	}
	return $base_url . $image_filename;
}

/**
 * 변호사 프로필 이미지 srcset (고해상도 2x 지원)
 * @2x 있으면 1x/2x 분리, 없으면 동일 URL을 1x·2x로 지정해 레티나에서 원본으로 선명 표시
 *
 * @param string $image_filename 예: dongju-kim-yunseo-lawyer.png
 * @param string $base_url       예: uploads baseurl + /2026/02
 * @param string $base_dir       예: uploads basedir + /2026/02
 * @return string srcset 속성값 (항상 반환하여 화질 개선)
 */
function della_theme_lawyer_image_srcset( $image_filename, $base_url, $base_dir ) {
	static $exists_cache = array();
	$path_info = pathinfo( $image_filename );
	$name      = isset( $path_info['filename'] ) ? $path_info['filename'] : '';
	$ext       = isset( $path_info['extension'] ) ? $path_info['extension'] : 'png';
	$base_dir  = trailingslashit( $base_dir );
	$base_url  = trailingslashit( $base_url );
	$url_1x_default = $base_url . $image_filename;

	// WebP 있으면 srcset도 WebP로 (용량 감소)
	if ( ! empty( $name ) ) {
		$webp_1x = $name . '.webp';
		$webp_2x = $name . '@2x.webp';
		$path_webp_1x = $base_dir . $webp_1x;
		$path_webp_2x = $base_dir . $webp_2x;
		foreach ( array( $path_webp_1x, $path_webp_2x ) as $p ) {
			if ( ! isset( $exists_cache[ $p ] ) ) {
				$exists_cache[ $p ] = file_exists( $p );
			}
		}
		if ( $exists_cache[ $path_webp_1x ] ) {
			$u1 = $base_url . $webp_1x;
			$u2 = $exists_cache[ $path_webp_2x ] ? ( $base_url . $webp_2x ) : $u1;
			return esc_url( $u1 ) . ' 1x, ' . esc_url( $u2 ) . ' 2x';
		}
	}

	$file_2x = $name . '@2x.' . $ext;
	$path_2x = $base_dir . $file_2x;
	if ( ! empty( $name ) ) {
		if ( ! isset( $exists_cache[ $path_2x ] ) ) {
			$exists_cache[ $path_2x ] = file_exists( $path_2x );
		}
		if ( $exists_cache[ $path_2x ] ) {
			$url_2x = $base_url . $file_2x;
			return esc_url( $url_1x_default ) . ' 1x, ' . esc_url( $url_2x ) . ' 2x';
		}
	}
	return esc_url( $url_1x_default ) . ' 1x, ' . esc_url( $url_1x_default ) . ' 2x';
}

/**
 * 성범죄 전문 변호사 페이지 URL (GNB·사이트맵 등)
 * 템플릿 적용된 페이지가 있으면 해당 주소, 없으면 고정 슬러그 반환 (요청당 1회 쿼리)
 *
 * @return string
 */
function della_theme_lawyers_page_url() {
	static $cached_url = null;
	if ( $cached_url !== null ) {
		return $cached_url;
	}
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-lawyers.php',
		'number'     => 1,
	) );
	if ( ! empty( $pages ) ) {
		$cached_url = get_permalink( $pages[0] );
	} else {
		$cached_url = home_url( '/lawyers/' );
	}
	return $cached_url;
}

/**
 * 슬러그로 변호사 한 명 반환 (상세 페이지용)
 *
 * @param string $slug 영문 슬러그 (예: dongju-kim-yunseo)
 * @return array|null 변호사 배열 또는 없으면 null
 */
function della_theme_get_lawyer_by_slug( $slug ) {
	if ( ! is_string( $slug ) || $slug === '' ) {
		return null;
	}
	$lawyers = della_theme_get_lawyers();
	foreach ( $lawyers as $lawyer ) {
		if ( isset( $lawyer['slug'] ) && $lawyer['slug'] === $slug ) {
			return $lawyer;
		}
	}
	return null;
}

/**
 * 변호사 상세 페이지 URL
 *
 * @param string $slug 변호사 슬러그 (빈 문자열이면 빈 문자열 반환)
 * @return string
 */
function della_theme_lawyer_profile_url( $slug ) {
	if ( ! is_string( $slug ) || $slug === '' ) {
		return '';
	}
	$base = untrailingslashit( della_theme_lawyers_page_url() );
	return $base . '/' . sanitize_file_name( $slug ) . '/';
}

/* 변호사 상세: 프로필 사진은 .lawyer-profile-hero 배경으로만 사용 (body 배경 아님) */

/**
 * 변호사 리스트 (hero·성범죄 전문 변호사 페이지 공통)
 *
 * @return array { slug, name, title, image, image_profile?, specialties[], education[], items[] }
 */
function della_theme_get_lawyers() {
	return array(
		array(
			'slug'         => 'dongju-park-dongjin',
			'name'         => '박동진',
			'title'        => '성범죄전문변호사',
			'image'        => 'dongju-park-dongjin-lawyer.png',
			'image_profile'=> 'dongju-park-dongjin_profile.webp',
			'specialties'  => array( '형사법' ),
			'education'    => array( '계성고등학교 졸업', '서울대학교 법과대학 법학과 졸업', '서울대학교 대학원 법학과 졸업(법학석사)', '러시아 모스크바법과대학 연수', '사법시험 합격', '사법연수원 21기' ),
			'items'        => array( '전 서울중앙지검 부장판사','전 부산지검 부장검사 변호사', '전 대구지검 경주지청 지청장', 
			'서울고등검찰청 검사(2006, 2016-2018)',
			'광주고검 전주지부 검사(2008), 제주지부 검사(2009)',
			'법무연수원 연구위원(2011)',
			'창원지검 진주지청장(2012)',
			'춘천지검 원주지청장(2013)',
			'국민권익위원회 위원장 보좌관(2014)',
			'부산지검 중요경제사범조사단 부장검사(2019, 2020)',
			'중앙지검 중요경제사범조사단장 및 부장검사(2021, 2022)',
			'現 법무법인동주'
			)
		),
		array(
			'slug'         => 'dongju-leesewhan',
			'name'         => '이세환',
			'title'        => '성범죄전문변호사',
			'image'        => 'dongju-leesewhan-lawyer.png',
			'image_profile'=> 'dongju-leesewhan_profile.webp',
			'specialties'  => array( '형사법' ),
			'education'    => array( '연세대학교 법과대학 졸업', '연세대학교 법학전문박사과정(형사법)', '변호사시험 합격' ),
			'items'        => array(
				'강력 성범죄 책임변호사',
				'前 대법원 국선변호인',
				'現 수원남부경찰서 상담변호사',
				'前 법무법인 더쌤 (서울사무소)',
				'前 법률사무소 지음',
				'前 공동법률사무소 동주',
				'前 대법원 국선변호인',
				'現 법무법인 동주',
				'前 대한변호사협회 대의원',
				'수원남부경찰서 수사민원상담변호사',
				'수원지방검찰청 피해자 국선변호사',
				'대한치과협회 법률지원변호사',
				'평택시 청북면 마을변호사 (공익활동)',
				'경기도 수원교육지원청 자문변호사',
				'수원 한일전산고등학교 고문변호사',
				'수원 한봄고등학교 학교폭력대책자치위원회 외부위원',
				'flick contents Lab (성범죄 피해자를 위한 게임제작 업체) 자문변호사',
				'네이버지식in전문상담위원 (형사소송분야)',
				'화성오산교육청 학교폭력대책 심의위원(특별소위원회전문위원)',
			),
			'media'        => array(
				'SBS뉴스 법률 자문 출연',
				'SBS 김태현의 정치쇼 법률 자문 출연',
				'삼프로TV 유튜브 법률 자문 출연',
				'YTN 이슈더이슈 법률 자문 출연',
				'TV조선 탐사보도세븐 법률 자문 출연',
				'연합뉴스TV 법률 자문 출연',
				'시사저널 법률 인터뷰',
				'경향신문 법률 인터뷰',
			),
		),
		array(
			'slug'         => 'dongju-jo-wonjin',
			'name'         => '조원진',
			'title'        => '성범죄전문변호사',
			'image'        => 'dongju-jo-wonjin-lawyer.png',
			'image_profile'=> 'dongju-jo-wonjin_profile.webp',
			'specialties'  => array( '형사법' ),
			'education'    => array( '경희대학교 법과대학 졸업', '법학전문석사', '변호사시험 합격' ),
			'items'        => array(
				'형사전문변호사',
				'前 국가정보원(NIS) 기획조정실 변호사',
				'前 해군본부 군검찰 국선변호인',
				'現 인천광역시의회 법률·입법 고문변호사',
				'대한변호사협회 학교폭력전문변호사',
				'前 법무법인 명문',
				'前 법률사무소 송향',
				'前 인천광역시 · 인천광역시교육청 2019회계연도 결산검사위원',
				'現 대법원 국선변호인',
				'現 인천광역시 남동구 고문변호사',
				'現 인천광역시 교육청 고문변호사',
				'現 인천광역시 연수구 선거관리위원회 위원',
				'現 인천광역시 출자기관 운영심의위원회 위원',
				'現 인천광역시 남동구 스포츠공정위원회 위원',
				'現 인천광역시 미추홀구 공직자윤리위원회 위원',
				'現 인천광역시 미추홀구 구정평가위원회 위원',
				'現 인천광역시 강화교육지원청 학교폭력심의위원회 위원',
			),
		),
		array(
			'slug'         => 'dongju-kim-yunseo',
			'name'         => '김윤서',
			'title'        => '성범죄전문변호사',
			'image'        => 'dongju-kim-yunseo-lawyer.png',
			'image_profile'=> 'dongju-kim-yunseo_profile.webp',
			'specialties'  => array( '형사법' ),
			'education'    => array( '숙명여자대학교 법과대학 최우수졸업(수석졸업)', '고려대학교 법학과 (석사) 졸업 형사법 전공', '고려대학교 법학과 (박사) 수료 형사법 전공','변호사시험합격'),
			'items'        => array(
				'성범죄 합의 책임변호사',
				'前 안양시의회 입법 전문위원',
				'前 헌법재판소·경찰청 실무실습',
				'現 안양시 산업진흥원 기업심사평가위원',
				'現 IBK기업은행 미래성장성 기업심의전문평가위원',
				'現 법무법인 동주',
			),
		),
		array(
			'slug'         => 'dongju-oh-seojin',
			'name'         => '오서진',
			'title'        => '성범죄전문변호사',
			'image'        => 'dongju-oh-seojin-lawyer.png',
			'image_profile'=> 'dongju-oh-seojin_profile.webp',
			'specialties'  => array( '형사법' ),
			'education'    => array( '연세대학교 법학전문박사과정(형사법)','법학전문석사','변호사시험 합격' ),
			'items'        => array(
				'디지털성범죄 책임변호사',
				'前 서울중앙지방법원 조정위원',
				'現 인천교육청 징계심의위원',
				'現 미추홀구 인사위원',
				'前 인천광역시교권보호위원회 위원',
				'前 인천광역시교육청 변호사',
				'現 법무법인 동주',
				'現 인천광역시교육청 소청심사위원회 위원',
				'現 인천광역시상담지원자문위원회 위원',
				'現 인천광역시학교안전공제회보상심사위원회 위원',
			),
		),
		array(
			'slug'         => 'dongju-isejin',
			'name'         => '이세진',
			'title'        => '성범죄전문변호사',
			'image'        => 'dongju-isejin-lawyer.png',
			'image_profile'=> 'dongju-isejin_profile.webp',
			'specialties'  => array( '형사법', '민사법' ),
			'education'    => array( '고려대학교 법과대학 졸업','법학전문석사','변호사시험 합격'),
			'items'        => array(
				'여성·청소년 성범죄 책임변호사',
				'前 대법원 국선변호인',
				'現 수원지검 피해자 국선변호사',
				'現 일산서부경찰서 법률자문변호사',
				'대한변호사협회 이혼전문변호사',
				'前 법무법인 현재',
				'前 법률사무소 지음',
				'前 대한변호사협회 대의원',
				'수원지방검찰청 피해자 국선변호사',
				'대한장애인론볼연맹 이사',
				'네이버지식in전문상담위원',
				'김포시 통진읍 마을 변호사 (공익활동)',
				'서울창신초등학교 고문변호사',
				'現 법무법인 동주',
			),
		),
	);
}

/**
 * Mobile menu toggle (hamburger) - inline script for fast load
 */
function della_theme_menu_toggle_script() {
	?>
	<script>
	(function() {
		var btn = document.getElementById('menu-toggle');
		var nav = document.getElementById('site-navigation');
		var closeBtn = document.getElementById('mobile-menu-close');
		var backdrop = document.getElementById('mobile-menu-backdrop');
		if (!btn || !nav) return;
		function closeMenu() {
			nav.classList.remove('toggled');
			if (btn) {
				btn.setAttribute('aria-expanded', 'false');
				btn.setAttribute('aria-label', '<?php echo esc_js( __( 'Open menu', 'della-theme' ) ); ?>');
			}
		}
		function openMenu() {
			if (btn) {
				btn.setAttribute('aria-expanded', 'true');
				btn.setAttribute('aria-label', '<?php echo esc_js( __( 'Close menu', 'della-theme' ) ); ?>');
			}
		}
		btn.addEventListener('click', function() {
			var open = nav.classList.toggle('toggled');
			if (open) openMenu(); else closeMenu();
		});
		if (closeBtn) closeBtn.addEventListener('click', closeMenu);
		if (backdrop) backdrop.addEventListener('click', closeMenu);
		nav.addEventListener('click', function(e) {
			if (e.target.tagName === 'A') closeMenu();
		});
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && nav.classList.contains('toggled')) closeMenu();
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'della_theme_menu_toggle_script' );

/**
 * Header: body에 header-at-top / header-scrolled 클래스만 토글. 스타일은 CSS 최하단 오버라이드로만 적용.
 * #masthead 인라인 style 제거 → 다른 스크립트/캐시가 넣은 background, boxShadow, borderBottom 등 제거
 */
function della_theme_header_scroll_script() {
	?>
	<script>
	(function() {
		var scrollThreshold = 50;
		var isFront = document.body && document.body.classList.contains('della-is-front');
		var userHasScrolled = false;
		function clearMastheadInlineStyle() {
			var el = document.getElementById('masthead');
			if (!el) return;
			el.style.removeProperty('background');
			el.style.removeProperty('background-color');
			el.style.removeProperty('background-image');
			el.style.removeProperty('box-shadow');
			el.style.removeProperty('border-bottom');
			el.style.removeProperty('border');
		}
		function updateHeader() {
			var atTop = window.scrollY <= scrollThreshold;
			var scrolled = isFront ? (userHasScrolled && !atTop) : !atTop;
			document.body.classList.toggle('header-scrolled', scrolled);
			document.body.classList.toggle('header-at-top', atTop);
			clearMastheadInlineStyle();
		}
		updateHeader();
		window.addEventListener('scroll', function() {
			userHasScrolled = true;
			updateHeader();
		}, { passive: true });
		window.addEventListener('load', clearMastheadInlineStyle);
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'della_theme_header_scroll_script', 5 );

/**
 * Hero + Success stories: 공통 스크롤 헬퍼 (휠·prev/next)
 */
function della_theme_hero_success_scroll_script() {
	if ( ! is_front_page() ) {
		return;
	}
	?>
	<script>
	(function() {
		function scrollContainer(el, deltaX, smooth) {
			if (!el) return;
			el.scrollBy({ left: deltaX, behavior: smooth ? 'smooth' : 'auto' });
		}

		/* Hero: 모바일에서 휠로 가로 스크롤 */
		var heroEl = document.querySelector('.hero-lawyers');
		if (heroEl && window.matchMedia && window.matchMedia('(max-width: 767px)').matches) {
			heroEl.addEventListener('wheel', function(e) {
				if (e.deltaY === 0) return;
				e.preventDefault();
				scrollContainer(heroEl, e.deltaY, false);
			}, { passive: false });
		}

		/* Success stories: prev/next 클릭 */
		var carousel = document.getElementById('success-stories-carousel');
		var prev = document.getElementById('success-stories-prev');
		var next = document.getElementById('success-stories-next');
		if (carousel && prev && next) {
			var step = 280 + 20;
			prev.addEventListener('click', function() { scrollContainer(carousel, -step, true); });
			next.addEventListener('click', function() { scrollContainer(carousel, step, true); });
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'della_theme_hero_success_scroll_script', 15 );

/**
 * Process cards: 자동재생(3초) + translate3d 전환, prev/next. (IO·호버 일시정지 제거로 경량화)
 */
function della_theme_process_cards_slider_script() {
	if ( ! is_front_page() ) {
		return;
	}
	?>
	<script>
	(function() {
		var list = document.getElementById('process-cards-list');
		var prev = document.getElementById('process-cards-prev');
		var next = document.getElementById('process-cards-next');
		if (!list || !prev || !next) return;
		var items = list.querySelectorAll('.process-card-item');
		var total = items.length;
		if (total === 0) return;
		var totalOriginal = parseInt(list.getAttribute('data-total'), 10) || Math.floor(total / 2);
		var mq = window.matchMedia('(min-width: 768px)');
		var currentIndex = 0;
		var autoTimer = null;
		var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var transitionDuration = 650;

		function isMobile() {
			return window.innerWidth < 768;
		}

		function getCardWidth() {
			return items[0] ? items[0].offsetWidth : (mq.matches ? 360 : 320);
		}

		function applyTransform(index) {
			var cw = getCardWidth();
			var x = -index * cw;
			list.style.transform = 'translate3d(' + x + 'px, 0, 0)';
		}

		function onTransitionEnd() {
			if (currentIndex >= totalOriginal) {
				list.style.transition = 'none';
				currentIndex = currentIndex - totalOriginal;
				applyTransform(currentIndex);
				list.offsetHeight;
				list.style.transition = 'transform 0.65s cubic-bezier(0.25, 0.1, 0.25, 1)';
			}
		}

		function goTo(index) {
			if (isMobile()) {
				currentIndex = (index + totalOriginal) % totalOriginal;
				list.style.transform = 'none';
				return;
			}
			if (index < 0 && currentIndex === 0) {
				list.style.transition = 'none';
				currentIndex = totalOriginal;
				applyTransform(currentIndex);
				list.offsetHeight;
				list.style.transition = 'transform 0.65s cubic-bezier(0.25, 0.1, 0.25, 1)';
				currentIndex = totalOriginal - 1;
				applyTransform(currentIndex);
				return;
			}
			currentIndex = (index + total) % total;
			if (currentIndex < 0) currentIndex += total;
			applyTransform(currentIndex);
			if (currentIndex >= totalOriginal) {
				setTimeout(onTransitionEnd, transitionDuration);
			}
		}

		function goNext() {
			goTo(currentIndex + 1);
		}

		function startAuto() {
			if (reducedMotion || autoTimer || isMobile()) return;
			autoTimer = setInterval(goNext, 3000);
		}

		function stopAuto() {
			if (autoTimer) {
				clearInterval(autoTimer);
				autoTimer = null;
			}
		}

		list.style.transition = 'transform 0.65s cubic-bezier(0.25, 0.1, 0.25, 1)';
		if (isMobile()) {
			list.style.transform = 'none';
		} else {
			currentIndex = 0;
			applyTransform(0);
			startAuto();
		}
		window.addEventListener('resize', function() {
			if (isMobile()) {
				stopAuto();
				list.style.transform = 'none';
			} else {
				goTo(currentIndex);
				startAuto();
			}
		});

		prev.addEventListener('click', function() {
			goTo(currentIndex - 1);
			if (!reducedMotion) { stopAuto(); startAuto(); }
		});
		next.addEventListener('click', function() {
			goTo(currentIndex + 1);
			if (!reducedMotion) { stopAuto(); startAuto(); }
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'della_theme_process_cards_slider_script', 20 );

/**
 * Response Info: 가로 스크롤 (휠·드래그·키보드). 프로그레스 바 제거로 경량화.
 */
function della_theme_response_info_scroll_script() {
	if ( ! is_front_page() ) {
		return;
	}
	?>
	<script>
	(function() {
		'use strict';
		var list = document.getElementById('response-info-list');
		if (!list) return;

		function scrollBy(deltaX) {
			list.scrollLeft = list.scrollLeft + deltaX;
		}

		list.addEventListener('wheel', function(e) {
			var maxScroll = list.scrollWidth - list.clientWidth;
			if (maxScroll <= 0) return;
			var delta = e.deltaX !== 0 ? e.deltaX : e.deltaY;
			if (delta !== 0) { e.preventDefault(); scrollBy(delta); }
		}, { passive: false });

		var dragging = false, startX, didDrag = false;
		list.addEventListener('mousedown', function(e) {
			if (e.button !== 0) return;
			if (list.scrollWidth <= list.clientWidth) return;
			dragging = true;
			didDrag = false;
			startX = e.pageX;
			list.style.cursor = 'grabbing';
			list.style.userSelect = 'none';
		});
		document.addEventListener('mousemove', function(e) {
			if (!dragging) return;
			if (Math.abs(startX - e.pageX) > 3) didDrag = true;
			e.preventDefault();
			list.scrollLeft = list.scrollLeft + (startX - e.pageX);
			startX = e.pageX;
		});
		document.addEventListener('mouseup', function() {
			if (dragging) {
				dragging = false;
				list.style.cursor = 'grab';
				list.style.userSelect = '';
				if (didDrag) {
					function preventClick(ev) {
						ev.preventDefault();
						ev.stopPropagation();
						list.removeEventListener('click', preventClick, true);
					}
					list.addEventListener('click', preventClick, true);
				}
			}
		});

		list.setAttribute('tabindex', '0');
		list.addEventListener('keydown', function(e) {
			var step = 280 + 20;
			if (e.key === 'ArrowLeft') { e.preventDefault(); scrollBy(-step); }
			else if (e.key === 'ArrowRight') { e.preventDefault(); scrollBy(step); }
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'della_theme_response_info_scroll_script', 21 );

/**
 * Directions: 주소 복사 버튼 (도로명/지번)
 */
function della_theme_directions_copy_script() {
	if ( ! is_front_page() ) {
		return;
	}
	$copied_text = __( '복사됨', 'della-theme' );
	?>
	<script>
	(function() {
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.directions-icon-copy').forEach(function(btn) {
				if (!btn.dataset.copyText) return;
				btn.addEventListener('click', function() {
					var text = btn.getAttribute('data-copy-text');
					if (!text) return;
					if (navigator.clipboard && navigator.clipboard.writeText) {
						navigator.clipboard.writeText(text).then(function() {
							dellaCopyFeedback(btn);
						}).catch(function() {
							dellaCopyFallback(btn, text);
						});
					} else {
						dellaCopyFallback(btn, text);
					}
				});
			});
			function dellaCopyFeedback(btn) {
				var originalLabel = btn.getAttribute('data-original-aria-label') || '';
				btn.classList.add('copied');
				btn.setAttribute('aria-label', '<?php echo esc_js( $copied_text ); ?>');
				setTimeout(function() {
					btn.classList.remove('copied');
					if (originalLabel) btn.setAttribute('aria-label', originalLabel);
				}, 1500);
			}
			function dellaCopyFallback(btn, text) {
				var ta = document.createElement('textarea');
				ta.value = text;
				ta.setAttribute('readonly', '');
				ta.style.position = 'absolute';
				ta.style.left = '-9999px';
				document.body.appendChild(ta);
				ta.select();
				try {
					document.execCommand('copy');
					dellaCopyFeedback(btn);
				} catch (e) {}
				document.body.removeChild(ta);
			}
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'della_theme_directions_copy_script', 22 );

/**
 * SEO: HTTP 상태 코드 — 오류·페이지 이동 시 적절한 상태 코드 지정
 * 404 시 404 명시 및 검색로봇 캐시 방지
 */
function della_theme_seo_http_status() {
	if ( is_404() ) {
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'della_theme_seo_http_status', 1 );

/**
 * 잘못된 파라미터/존재하지 않는 리소스 시 404 처리 (상태 코드 + 테마 404 템플릿)
 * 변호사 슬러그 없음, 잘못된 경로 등에서 호출.
 */
function della_theme_trigger_404() {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	$template = get_query_template( '404' );
	if ( $template ) {
		include $template;
	} else {
		// 폴백: 최소 404 본문
		if ( ! headers_sent() ) {
			header( 'HTTP/1.1 404 Not Found' );
		}
		echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . esc_html__( '페이지를 찾을 수 없습니다', 'della-theme' ) . '</title></head><body><h1>' . esc_html__( '페이지를 찾을 수 없습니다', 'della-theme' ) . '</h1><p><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( '홈으로', 'della-theme' ) . '</a></p></body></html>';
	}
	exit;
}

/**
 * SEO: 현재 요청의 canonical URL 반환 (AIOSEO 사용 시 테마는 head에서 1회만 출력).
 * 404는 빈 문자열 반환.
 */
function della_theme_get_canonical_url() {
	if ( is_404() ) {
		return '';
	}
	if ( is_front_page() || ( is_home() && get_option( 'show_on_front' ) === 'posts' ) ) {
		$canonical = home_url( '/' );
	} elseif ( function_exists( 'della_theme_is_lawyers_page' ) && della_theme_is_lawyers_page() ) {
		if ( get_query_var( 'lawyer_slug' ) ) {
			$canonical = function_exists( 'della_theme_lawyer_profile_url' ) ? della_theme_lawyer_profile_url( get_query_var( 'lawyer_slug' ) ) : '';
		} else {
			$canonical = function_exists( 'della_theme_lawyers_page_url' ) ? della_theme_lawyers_page_url() : get_permalink();
		}
	} elseif ( function_exists( 'della_theme_is_success_cases_page' ) && della_theme_is_success_cases_page() ) {
		$canonical = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : get_permalink();
		$tag = isset( $_GET['tag'] ) ? sanitize_text_field( wp_unslash( $_GET['tag'] ) ) : '';
		$cat = isset( $_GET['cat'] ) ? sanitize_text_field( wp_unslash( $_GET['cat'] ) ) : '';
		if ( $tag || $cat ) {
			$canonical = add_query_arg( array_filter( array( 'tag' => $tag ? $tag : null, 'cat' => $cat ? $cat : null ) ), $canonical );
		}
	} elseif ( function_exists( 'della_theme_is_response_board_page' ) && della_theme_is_response_board_page() ) {
		$canonical = function_exists( 'della_theme_response_board_page_url' ) ? della_theme_response_board_page_url() : get_permalink();
		$tag = isset( $_GET['tag'] ) ? sanitize_text_field( wp_unslash( $_GET['tag'] ) ) : '';
		$cat = isset( $_GET['cat'] ) ? sanitize_text_field( wp_unslash( $_GET['cat'] ) ) : '';
		if ( $tag || $cat ) {
			$canonical = add_query_arg( array_filter( array( 'tag' => $tag ? $tag : null, 'cat' => $cat ? $cat : null ) ), $canonical );
		}
	} else {
		$canonical = function_exists( 'wp_get_canonical_url' ) ? wp_get_canonical_url() : null;
		if ( empty( $canonical ) ) {
			if ( is_singular() ) {
				$canonical = get_permalink();
			} elseif ( is_archive() || is_search() ) {
				$canonical = get_pagenum_link( 1, false );
			}
		}
	}
	if ( empty( $canonical ) ) {
		return '';
	}
	return remove_query_arg( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' ), $canonical );
}

/**
 * SEO: 성범죄 전문 변호사(목록) 페이지 메타 — AIOSEO에서 관리하므로 테마에서는 출력하지 않음.
 */
function della_theme_lawyers_page_meta() {
	// No output: description, canonical, robots, og, twitter are handled by AIOSEO.
}

/**
 * SEO: 변호사 상세(/lawyers/{slug}/) 메타 — AIOSEO에서 관리하므로 테마에서는 출력하지 않음.
 */
function della_theme_lawyer_profile_page_meta() {
	// No output: title, description, canonical, og, twitter are handled by AIOSEO.
}

/**
 * 현재 요청에 맞는 meta description 한 줄 반환 (플러그인·캐시와 무관하게 최종 폴백용).
 * header.php에서 wp_head() 직후 호출해 </head> 직전에 출력하면 항상 description 보장.
 *
 * @return string 비면 출력하지 않음.
 */
function della_theme_get_fallback_description() {
	$law = __( '법무법인 동주', 'della-theme' );

	if ( is_404() ) {
		return wp_strip_all_tags( __( '페이지를 찾을 수 없습니다. 요청하신 주소가 잘못되었거나 변경되었을 수 있습니다. ', 'della-theme' ) . $law . ' ' . __( ' 수원 성범죄 전문 변호사 사이트 홈 또는 사이트맵으로 이동해 주세요.', 'della-theme' ) );
	}
	if ( is_front_page() ) {
		return '수원 성범죄 전문변호사 법무법인 동주. 중앙지검 부장검사·국정원·경찰청 경력 변호사가 고소 전 합의부터 경찰조사, 검찰송치, 재판까지 직접 대응합니다. 수원 광교.';
	}

	// 변호사 상세 — 쿼리 변수로 먼저 확인 (permalink 미사용/서버 차이 대응)
	$lawyer_slug = get_query_var( 'lawyer_slug' );
	if ( $lawyer_slug && function_exists( 'della_theme_get_lawyer_by_slug' ) ) {
		$lawyer = della_theme_get_lawyer_by_slug( $lawyer_slug );
		if ( $lawyer && ! empty( $lawyer['name'] ) ) {
			$specs = isset( $lawyer['specialties'] ) && is_array( $lawyer['specialties'] ) ? $lawyer['specialties'] : array();
			$spec  = ! empty( $specs ) ? implode( '·', array_slice( $specs, 0, 3 ) ) : '강제추행·카촬·디지털성범죄';
			$spec  = str_replace( array( '불법촬영', '카메라촬영' ), '카촬', $spec );
			$desc  = $lawyer['name'] . ' 변호사 프로필. 수원 성범죄 전문변호사로 ' . $spec . ' 등 형사사건을 초기 상담부터 조사·재판까지 직접 대응합니다. 경력·전문분야 안내.';
			return function_exists( 'della_theme_trim_meta_description' ) ? della_theme_trim_meta_description( $desc ) : $desc;
		}
	}

	$req_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path    = trim( (string) parse_url( $req_uri, PHP_URL_PATH ), '/' );
	$path    = preg_replace( '#/+#', '/', $path );

	// 성공사례 허브
	if ( $path === 'success-cases' || strpos( $path, 'success-cases/' ) === 0 || strpos( $path, '/success-cases' ) !== false || in_array( $path, array( 'case', 'success', '성범죄-성공사례', '성공사례' ), true ) ) {
		return '수원 성범죄 사건 성공사례를 확인하세요. 강제추행·카메라촬영·아청법 사건에서 무혐의·기소유예·집행유예 등 실제 결과를 공개합니다. 경찰조사부터 재판까지 법무법인 동주가 직접 대응합니다.';
	}

	// 변호사 상세 — 경로로 한 번 더 (쿼리 변수 없을 때)
	$lawyers_pos = strpos( $path, 'lawyers/' );
	if ( $lawyers_pos !== false && function_exists( 'della_theme_get_lawyer_by_slug' ) ) {
		$after = substr( $path, $lawyers_pos + 8 );
		$parts = explode( '/', $after );
		$slug  = isset( $parts[0] ) ? trim( $parts[0] ) : '';
		if ( $slug !== '' ) {
			$lawyer = della_theme_get_lawyer_by_slug( $slug );
			if ( $lawyer && ! empty( $lawyer['name'] ) ) {
				$specs = isset( $lawyer['specialties'] ) && is_array( $lawyer['specialties'] ) ? $lawyer['specialties'] : array();
				$spec  = ! empty( $specs ) ? implode( '·', array_slice( $specs, 0, 3 ) ) : '강제추행·카촬·디지털성범죄';
				$spec  = str_replace( array( '불법촬영', '카메라촬영' ), '카촬', $spec );
				$desc  = $lawyer['name'] . ' 변호사 프로필. 수원 성범죄 전문변호사로 ' . $spec . ' 등 형사사건을 초기 상담부터 조사·재판까지 직접 대응합니다. 경력·전문분야 안내.';
				return function_exists( 'della_theme_trim_meta_description' ) ? della_theme_trim_meta_description( $desc ) : $desc;
			}
		}
	}

	// 변호사 목록 페이지
	if ( function_exists( 'della_theme_is_lawyers_page' ) && della_theme_is_lawyers_page() && ! get_query_var( 'lawyer_slug' ) ) {
		return '법무법인 동주 성범죄 전문변호사 팀을 소개합니다. 형사법 전문 변호사들이 성범죄 사건에서 경찰조사·검찰송치·재판까지 단계별 대응 전략을 체계적으로 설계합니다. 상담 전 변호사 경력과 전문분야를 확인하세요.';
	}
	// 대응정보 허브
	if ( function_exists( 'della_theme_is_response_board_page' ) && della_theme_is_response_board_page() ) {
		return '성범죄 대응정보를 유형별로 정리했습니다. 강제추행·불법촬영·아청법·군성범죄 등 법조문, 판례, FAQ와 수사·재판 단계별 대응 포인트를 한곳에서 확인하세요.';
	}
	// 성공사례 허브 (템플릿 기준)
	if ( function_exists( 'della_theme_is_success_cases_page' ) && della_theme_is_success_cases_page() ) {
		return '수원 성범죄 사건 성공사례를 확인하세요. 강제추행·카메라촬영·아청법 사건에서 무혐의·기소유예·집행유예 등 실제 결과를 공개합니다. 경찰조사부터 재판까지 법무법인 동주가 직접 대응합니다.';
	}

	// 단일 글 (성공사례·대응정보·일반 글 상세)
	if ( is_singular( 'post' ) ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$d = '';
			if ( function_exists( 'della_theme_is_success_case_post' ) && della_theme_is_success_case_post( $post ) && function_exists( 'della_theme_success_case_unique_description' ) ) {
				$d = della_theme_success_case_unique_description( $post );
			}
			if ( ( $d === '' || trim( $d ) === '' ) && function_exists( 'della_theme_is_response_info_post' ) && della_theme_is_response_info_post( $post ) && function_exists( 'della_theme_response_info_unique_description' ) ) {
				$d = della_theme_response_info_unique_description( $post );
			}
			if ( $d === '' || trim( $d ) === '' ) {
				$max = 300;
				$d   = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( get_the_content( null, false, $post ), 50 );
				$d   = wp_strip_all_tags( $d );
				$d   = preg_replace( '/\s+/', ' ', trim( $d ) );
				if ( $d === '' ) {
					$d = get_the_title( $post ) . ' - ' . $law . ' ' . __( '수원 성범죄 전문 변호사.', 'della-theme' );
				} else {
					if ( function_exists( 'mb_strlen' ) && mb_strlen( $d ) > $max ) {
						$d = ( function_exists( 'mb_substr' ) ? mb_substr( $d, 0, $max - 3 ) : substr( $d, 0, $max - 3 ) ) . '…';
					} elseif ( strlen( $d ) > $max ) {
						$d = substr( $d, 0, $max - 3 ) . '…';
					}
					$d = $d . ' | ' . $law;
				}
			}
			$d = function_exists( 'della_theme_trim_meta_description' ) ? della_theme_trim_meta_description( $d ) : $d;
			if ( $d !== '' ) {
				return $d;
			}
		}
	}

	// 단일 페이지(페이지 포스트타입)
	if ( is_singular( 'page' ) ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$d = get_the_title( $post ) . ' - ' . $law . ' ' . __( '수원 성범죄 전문 변호사.', 'della-theme' );
			return function_exists( 'della_theme_trim_meta_description' ) ? della_theme_trim_meta_description( $d ) : $d;
		}
	}

	// 아카이브 등
	return get_bloginfo( 'name' ) . ( get_bloginfo( 'description' ) ? ' - ' . get_bloginfo( 'description' ) : '' );
}

/**
 * SEO: 성범죄 대응정보 허브 페이지 메타 — AIOSEO에서 관리하므로 테마에서는 출력하지 않음.
 */
function della_theme_response_board_page_meta() {
	// No output: description, robots, og, twitter are handled by AIOSEO.
}

/**
 * SEO: 성공사례 허브 페이지 메타 — AIOSEO에서 관리하므로 테마에서는 출력하지 않음.
 */
function della_theme_render_success_cases_meta() {
	// No output: title, description, canonical, og, twitter are handled by AIOSEO.
}

/**
 * 성공사례 페이지 여부 (상수·is_success_cases_page·REQUEST_URI).
 */
function della_theme_is_on_success_cases_request() {
	if ( defined( 'DELLA_IS_SUCCESS_CASES_TEMPLATE' ) && DELLA_IS_SUCCESS_CASES_TEMPLATE ) {
		return true;
	}
	if ( function_exists( 'della_theme_is_success_cases_page' ) && della_theme_is_success_cases_page() ) {
		return true;
	}
	$req_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path    = trim( (string) parse_url( $req_uri, PHP_URL_PATH ), '/' );
	$path    = preg_replace( '#/+#', '/', $path );
	return in_array( $path, array( 'success-cases', 'case', 'success', '성범죄-성공사례', '성공사례' ), true )
		|| strpos( $path, 'success-cases/' ) === 0;
}

/**
 * 성공사례 페이지 core title 제거 — AIOSEO가 title 관리하므로 테마에서는 제거하지 않음.
 */
function della_theme_success_cases_remove_core_title() {
	// No action: title is managed by AIOSEO.
}

/**
 * 성공사례 페이지 메타 — AIOSEO에서 출력.
 */
function della_theme_success_cases_page_meta() {
	// No output: handled by AIOSEO.
}

/**
 * SEO: Robots meta — header.php에서 index,follow,max-image-preview:large 1회만 출력. AIOSEO가 404/검색 등 특수 케이스 처리.
 */
function della_theme_seo_robots_meta() {
	// No output: single robots tag is output in header.php; AIOSEO handles noindex for 404/search etc.
}

/**
 * SEO: 404 meta description — AIOSEO에서 관리.
 */
function della_theme_404_meta_description() {
	// No output: description is handled by AIOSEO.
}

/**
 * 글 상세(단일 포스트) 페이지에 meta description 항상 출력.
 * og_twitter_meta는 플러그인 사용 시 비활성화되므로, 글 상세만 별도로 description 보장.
 */
function della_theme_single_post_meta_description() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	$law = __( '법무법인 동주', 'della-theme' );
	$max = defined( 'DELLA_THEME_DESC_MAX' ) ? DELLA_THEME_DESC_MAX - 20 : 300;

	if ( function_exists( 'della_theme_is_success_case_post' ) && della_theme_is_success_case_post( $post ) && function_exists( 'della_theme_success_case_unique_description' ) ) {
		$description = della_theme_success_case_unique_description( $post );
	} elseif ( function_exists( 'della_theme_is_response_info_post' ) && della_theme_is_response_info_post( $post ) && function_exists( 'della_theme_response_info_unique_description' ) ) {
		$description = della_theme_response_info_unique_description( $post );
	} else {
		$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( get_the_content( null, false, $post ), 50 );
		$description = wp_strip_all_tags( $description );
		$description = preg_replace( '/\s+/', ' ', trim( $description ) );
		if ( function_exists( 'mb_strlen' ) && mb_strlen( $description ) > $max ) {
			$description = ( function_exists( 'mb_substr' ) ? mb_substr( $description, 0, $max - 3 ) : substr( $description, 0, $max - 3 ) ) . '…';
		} elseif ( strlen( $description ) > $max ) {
			$description = substr( $description, 0, $max - 3 ) . '…';
		}
		$description = $description . ' | ' . $law;
		$description = della_theme_trim_meta_description( $description );
	}
	if ( empty( $description ) ) {
		$description = get_the_title( $post ) . ' - ' . $law . ' ' . __( '수원 성범죄 전문 변호사.', 'della-theme' );
		$description = della_theme_trim_meta_description( $description );
	}
	// No output: description is handled by AIOSEO.
}

/**
 * 대응정보 상세 글 전용 meta keywords (글별 유니크·보조 SEO).
 * 공통 키워드 + 글 카테고리·제목 기반으로 조합. (검색엔진은 주로 사용하지 않으나 보조·일부 로봇 참고용)
 */
function della_theme_response_info_post_meta_keywords() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	if ( ! function_exists( 'della_theme_is_response_info_post' ) || ! della_theme_is_response_info_post( $post ) ) {
		return;
	}

	$base = array(
		'수원 성범죄 대응',
		'성범죄 대응정보',
		'법무법인 동주',
		'강제추행',
		'불법촬영',
		'아청법',
		'성범죄 변호사',
		'성범죄 법조문',
		'성범죄 판례',
	);
	$post_keywords = array();
	$cats = get_the_category( $post->ID );
	if ( $cats ) {
		foreach ( $cats as $c ) {
			if ( $c->name === '성범죄대응정보' || $c->name === '대응정보' || $c->name === '대응 정보' ) {
				continue;
			}
			$name = trim( $c->name );
			if ( $name !== '' && ! in_array( $name, $post_keywords, true ) ) {
				$post_keywords[] = $name;
			}
		}
	}
	$title = get_the_title( $post );
	$custom_title = get_post_meta( $post->ID, 'della_info_seo_title', true );
	if ( is_string( $custom_title ) && trim( $custom_title ) !== '' ) {
		$title = $custom_title;
	}
	$title = wp_strip_all_tags( $title );
	$title_len = function_exists( 'mb_strlen' ) ? mb_strlen( $title ) : strlen( $title );
	if ( $title !== '' && $title_len <= 30 && ! in_array( $title, $post_keywords, true ) ) {
		$post_keywords[] = $title;
	}

	$all = array_merge( $base, array_slice( $post_keywords, 0, 6 ) );
	$all = array_unique( array_filter( $all ) );
	$keywords = implode( ', ', array_slice( $all, 0, 15 ) );
	// No output: meta keywords/description are handled by AIOSEO.
}

/**
 * LCP 히어로 이미지 preload는 header.php에서 head 최상단으로 출력 (요청 탐색 지연 방지).
 * 업로드 도메인 preconnect도 동일 위치에서 출력.
 */

/**
 * Open Graph and Twitter Card meta tags
 * SEO 플러그인에서 이미 출력하므로 테마는 출력하지 않음 (og:site_name, og:url, og:title, twitter:title 중복 제거).
 * 플러그인 미사용 시 테마에서 출력하려면: add_filter( 'della_theme_disable_og_twitter_meta', '__return_false' );
 */
function della_theme_og_twitter_meta() {
	// 기본값 true: 테마 OG/Twitter 미출력 → 플러그인만 사용 (중복 제거)
	if ( apply_filters( 'della_theme_disable_og_twitter_meta', true ) ) {
		return;
	}
	if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath', false ) || class_exists( 'All_in_One_SEO_Pack', false ) ) {
		return;
	}
	// 성범죄 전문 변호사 목록은 della_theme_lawyers_page_meta(), 상세는 della_theme_lawyer_profile_page_meta()에서 출력
	if ( function_exists( 'della_theme_is_lawyers_page' ) && della_theme_is_lawyers_page() ) {
		return;
	}
	// 성범죄 대응정보 허브 페이지는 전용 메타에서 출력
	if ( function_exists( 'della_theme_is_response_board_page' ) && della_theme_is_response_board_page() ) {
		return;
	}
	// 성범죄 성공사례 허브 페이지는 전용 메타에서 출력
	if ( function_exists( 'della_theme_is_success_cases_page' ) && della_theme_is_success_cases_page() ) {
		return;
	}
	if ( ! is_singular() && ! is_front_page() ) {
		return;
	}

	$law_firm_name = __( '법무법인 동주', 'della-theme' );
	$title         = wp_get_document_title();
	$description   = '';
	$url           = is_singular() ? get_permalink() : home_url( '/' );
	if ( ! $url || ! is_string( $url ) ) {
		$url = home_url( '/' );
	}
	$image = '';
	$type  = is_singular() ? 'article' : 'website';

	if ( empty( $title ) ) {
		$title = della_theme_trim_document_title(
			get_bloginfo( 'name' ) . ( get_bloginfo( 'description' ) ? ' - ' . get_bloginfo( 'description' ) : '' )
		);
	} else {
		$title = della_theme_trim_document_title( $title );
	}

	if ( is_front_page() ) {
		$description = della_theme_front_page_description();
	} elseif ( della_theme_is_lawyers_page() ) {
		$description = $law_firm_name . ' ' . __( '수원 성범죄 전문 변호사 팀 6인 소개. 강간·강제추행·불법촬영·디지털성범죄 등 성범죄 사건 초기 대응부터 재판까지 전문 변호사가 함께합니다.', 'della-theme' );
		$lawyers = della_theme_get_lawyers();
		$upload_dir = wp_upload_dir();
		$img_base = $upload_dir['baseurl'] . '/2026/02';
		if ( ! empty( $lawyers[0]['image'] ) ) {
			$image = della_theme_lawyer_image_url( $lawyers[0]['image'], $img_base, $upload_dir['basedir'] . '/2026/02' );
		}
	} elseif ( della_theme_is_response_board_page() ) {
		$description = '성범죄 대응정보를 유형별로 정리했습니다. 강제추행·불법촬영·아청법·군성범죄 등 법조문, 판례, FAQ와 수사·재판 단계별 대응 포인트를 한곳에서 확인하세요.';
		$description = della_theme_trim_meta_description( $description );
		$type = 'website';
	} elseif ( della_theme_is_success_cases_page() ) {
		$description = __( '성범죄 성공사례: 강간·강제추행·군성범죄·불법촬영·성매매·직장내 성희롱 등 수원 성범죄 전문 변호사 성공 사례 모음.', 'della-theme' );
		$description = wp_strip_all_tags( $description ) . ' | ' . $law_firm_name;
		$description = della_theme_trim_meta_description( $description );
		$type = 'website';
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			// 성공사례 상세: 유니크 description. 대응정보 상세: 유니크 description. 그 외: 요약+법인명
			if ( function_exists( 'della_theme_is_success_case_post' ) && della_theme_is_success_case_post( $post ) && function_exists( 'della_theme_success_case_unique_description' ) ) {
				$description = della_theme_success_case_unique_description( $post );
			} elseif ( function_exists( 'della_theme_is_response_info_post' ) && della_theme_is_response_info_post( $post ) && function_exists( 'della_theme_response_info_unique_description' ) ) {
				$description = della_theme_response_info_unique_description( $post );
			} else {
				$max_pre = defined( 'DELLA_THEME_DESC_MAX' ) ? DELLA_THEME_DESC_MAX - 20 : 300;
				$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( get_the_content( null, false, $post ), 50 );
				$description = wp_strip_all_tags( $description );
				$description = preg_replace( '/\s+/', ' ', trim( $description ) );
				if ( function_exists( 'mb_strlen' ) && mb_strlen( $description ) > $max_pre ) {
					$description = mb_substr( $description, 0, $max_pre - 3 ) . '…';
				} elseif ( strlen( $description ) > $max_pre ) {
					$description = substr( $description, 0, $max_pre - 3 ) . '…';
				}
				$description = $description . ' | ' . $law_firm_name;
				$description = della_theme_trim_meta_description( $description );
			}
			if ( has_post_thumbnail( $post ) ) {
				$image = get_the_post_thumbnail_url( $post, 'large' );
			}
		}
	}

	if ( empty( $description ) ) {
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post instanceof WP_Post ) {
				$description = get_the_title( $post ) . ' - ' . $law_firm_name . ' ' . __( '수원 성범죄 전문 변호사.', 'della-theme' );
			} else {
				$description = get_bloginfo( 'description' );
			}
		} else {
			$description = get_bloginfo( 'description' );
		}
	}
	if ( empty( $image ) ) {
		$image = get_site_icon_url( 512 );
	}

	$description = is_string( $description ) ? wp_strip_all_tags( $description ) : '';
	$description = preg_replace( '/\s+/', ' ', trim( $description ) );
	$description = della_theme_trim_meta_description( $description );
	$description = della_theme_ensure_description_length( $description, $law_firm_name );
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	}
	?>
	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
	<?php if ( $image ) : ?>
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>" />
	<?php endif; ?>
	<meta property="og:site_name" content="<?php echo esc_attr( $law_firm_name ); ?>" />
	<meta property="og:locale" content="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>" />
	<meta name="twitter:card" content="<?php echo $image ? 'summary_large_image' : 'summary'; ?>" />
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>" />
	<?php if ( $image ) : ?>
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" />
	<?php endif;
	// AIOSEO 사용 시 테마에서는 OG/Twitter 미출력 (add_action 제거됨).
}

/**
 * Schema.org JSON-LD structured data
 */
function della_theme_schema_json_ld() {
	$schema           = array();
	$schema_breadcrumb = array();

	// WebSite (front page: static or blog). 메인은 100점 메타에서 LegalService 출력 → 여기서 WebSite 생략(중복 방지).
	$schema['@context'] = 'https://schema.org';
	if ( is_front_page() ) {
		// della_theme_front_page_meta_100() 에서 LegalService 출력.
	} elseif ( is_home() && get_option( 'show_on_front' ) === 'posts' ) {
		$schema['@type'] = 'WebSite';
		$schema['name'] = get_bloginfo( 'name' );
		$fp_desc = della_theme_front_page_description();
		if ( $fp_desc ) {
			$schema['description'] = wp_strip_all_tags( $fp_desc );
		} elseif ( get_bloginfo( 'description' ) ) {
			$schema['description'] = get_bloginfo( 'description' );
		}
		$schema['url'] = home_url( '/' );
		$schema['inLanguage'] = 'ko-KR';
		$schema['potentialAction'] = array(
			'@type'       => 'SearchAction',
			'target'      => array( '@type' => 'EntryPoint', 'urlTemplate' => home_url( '/?s={search_term_string}' ) ),
			'query-input' => 'required name=search_term_string',
		);
	}

	// Article (single post)
	if ( is_singular( 'post' ) ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$schema['@type'] = 'Article';
			$schema['headline'] = get_the_title( $post );
			$schema['datePublished'] = get_the_date( 'c', $post );
			$schema['dateModified'] = get_the_modified_date( 'c', $post );
			$schema['author'] = array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', $post->post_author ),
			);
			$schema['publisher'] = array(
				'@type' => 'Organization',
				'name'  => get_bloginfo( 'name' ),
				'url'   => home_url( '/' ),
			);
			if ( has_post_thumbnail( $post ) ) {
				$schema['image'] = get_the_post_thumbnail_url( $post, 'large' );
			}
			$schema['mainEntityOfPage'] = array(
				'@type' => 'WebPage',
				'@id'   => get_permalink( $post ) . '#webpage',
			);
		}
	}

	// WebPage (single page) / CollectionPage (성범죄 대응정보). 성공사례는 아래 @graph에서만 출력.
	if ( is_singular( 'page' ) && ! della_theme_is_success_cases_page() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$schema['@type'] = 'WebPage';
			$schema['name'] = get_the_title( $post );
			$schema['url'] = get_permalink( $post );
			$schema['dateModified'] = get_the_modified_date( 'c', $post );
			if ( della_theme_is_response_board_page() ) {
				$schema['@type'] = array( 'WebPage', 'CollectionPage' );
				$schema['description'] = __( '성범죄 대응정보: 강간·강제추행·군성범죄·불법촬영·성매매·직장내 성희롱 등 법조문, 판례, FAQ, 수사·재판 단계별 대응 가이드.', 'della-theme' );
				$schema['inLanguage'] = 'ko-KR';
			}
		}
	}

	// BreadcrumbList
	$breadcrumb = della_theme_get_breadcrumb_items();
	if ( ! empty( $breadcrumb ) ) {
		$schema_breadcrumb = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(),
		);
		foreach ( $breadcrumb as $pos => $item ) {
			$schema_breadcrumb['itemListElement'][] = array(
				'@type'    => 'ListItem',
				'position' => $pos + 1,
				'name'     => $item['label'],
				'item'     => $item['url'],
			);
		}
	}

	if ( ! empty( $schema['@type'] ) ) {
		echo '<script type="application/ld+json">' . "\n" . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" . '</script>' . "\n";
	}

	// 성범죄 전문 변호사 페이지: ItemList + Person (SEO)
	if ( della_theme_is_lawyers_page() ) {
		$lawyers   = della_theme_get_lawyers();
		$upload_dir = wp_upload_dir();
		$img_base   = $upload_dir['baseurl'] . '/2026/02';
		$img_dir    = $upload_dir['basedir'] . '/2026/02';
		$persons    = array();
		foreach ( $lawyers as $lawyer ) {
			$img_url = della_theme_lawyer_image_url( $lawyer['image'], $img_base, $img_dir );
			$persons[] = array(
				'@type'     => 'Person',
				'name'      => $lawyer['name'],
				'jobTitle'  => $lawyer['title'],
				'image'     => $img_url,
			);
		}
		$lawyers_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'ItemList',
			'name'            => __( '성범죄 전문 변호사', 'della-theme' ),
			'description'     => get_bloginfo( 'name' ) . ' ' . __( '수원 성범죄 전문 변호사 팀 소개', 'della-theme' ),
			'url'             => della_theme_lawyers_page_url(),
			'numberOfItems'   => count( $persons ),
			'itemListElement' => array(),
		);
		foreach ( $persons as $pos => $person ) {
			$lawyers_schema['itemListElement'][] = array(
				'@type'    => 'ListItem',
				'position' => $pos + 1,
				'item'     => $person,
			);
		}
		echo '<script type="application/ld+json">' . "\n" . wp_json_encode( $lawyers_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" . '</script>' . "\n";
	}

	// 성범죄 대응정보 페이지: ItemList (목록 구조화, 모바일·검색 리치 결과)
	if ( della_theme_is_response_board_page() ) {
		$rb_cat = function_exists( 'della_theme_get_response_info_parent_category' ) ? della_theme_get_response_info_parent_category() : null;
		$rb_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
		);
		if ( $rb_cat ) {
			$rb_args['cat'] = $rb_cat->term_id;
		}
		$rb_query = new WP_Query( $rb_args );
		$rb_items = array();
		if ( $rb_query->have_posts() ) {
			$pos = 0;
			foreach ( $rb_query->posts as $pid ) {
				$pos++;
				$rb_items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos,
					'url'      => get_permalink( $pid ),
				);
			}
		}
		wp_reset_postdata();
		$rb_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'ItemList',
			'name'            => __( '성범죄 대응정보 목록', 'della-theme' ),
			'description'     => __( '성범죄 대응정보 게시판 글 목록. 법조문, 판례, FAQ, 수사·재판 대응 가이드.', 'della-theme' ),
			'url'             => get_permalink(),
			'numberOfItems'   => count( $rb_items ),
			'itemListElement' => $rb_items,
		);
		echo '<script type="application/ld+json">' . "\n" . wp_json_encode( $rb_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" . '</script>' . "\n";
	}

	// 성범죄 성공사례 페이지: @graph (WebSite, LegalService, WebPage, BreadcrumbList, ItemList)
	if ( della_theme_is_success_cases_page() ) {
		$base_url    = home_url( '/' );
		$sc_page_url = function_exists( 'della_theme_success_cases_page_url' ) ? della_theme_success_cases_page_url() : get_permalink();
		$road        = get_theme_mod( 'della_road_address', '경기 수원시 영통구 광교중앙로248번길 7-2' );
		$road2       = get_theme_mod( 'della_road_address2', '원희캐슬광교 B동 902호, 903호' );
		$street      = trim( $road . ' ' . $road2 );
		$phone       = get_theme_mod( 'della_phone', '031-216-1155' );
		$postal_code = get_theme_mod( 'della_postal_code', '' );
		$logo_id     = get_theme_mod( 'custom_logo', 0 );
		$logo_url    = $logo_id ? wp_get_attachment_image_url( (int) $logo_id, 'full' ) : '';
		if ( ! $logo_url ) {
			$logo_url = home_url( '/img/logo.png' );
		}
		$og_image = apply_filters( 'della_theme_schema_og_image', home_url( '/assets/og/dongju-sexcrime-lawyer-1200x630.jpg' ) );
		if ( ! $og_image ) {
			$og_image = home_url( '/img/og-sexcrime-dongju.jpg' );
		}
		$same_as = array_filter( array(
			get_theme_mod( 'della_naver_place_url', '' ),
			get_theme_mod( 'della_naver_blog_url', '' ),
		) );
		$same_as = array_values( array_filter( array_map( 'esc_url_raw', $same_as ) ) );

		$sc_cat = function_exists( 'della_theme_get_success_case_parent_category' ) ? della_theme_get_success_case_parent_category() : null;
		$sc_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		if ( $sc_cat ) {
			$sc_args['cat'] = (int) $sc_cat->term_id;
		} else {
			$sc_args['category__in'] = array( 0 );
		}
		$sc_query = new WP_Query( $sc_args );
		$sc_list_items = array();
		if ( $sc_query->have_posts() ) {
			$pos = 0;
			foreach ( $sc_query->posts as $post ) {
				$pos++;
				$item_name = get_the_title( $post );
				$seo_title = get_post_meta( $post->ID, 'della_case_seo_title', true );
				if ( is_string( $seo_title ) && trim( $seo_title ) !== '' ) {
					$item_name = trim( $seo_title );
				}
				$sc_list_items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos,
					'url'      => get_permalink( $post ),
					'name'     => $item_name,
				);
			}
		}
		wp_reset_postdata();

		$address = array(
			'@type'           => 'PostalAddress',
			'addressCountry'  => 'KR',
			'addressRegion'   => '경기도',
			'addressLocality' => '수원시',
			'streetAddress'   => $street,
		);
		if ( $postal_code !== '' ) {
			$address['postalCode'] = $postal_code;
		}

		$legal_service = array(
			'@type'        => 'LegalService',
			'@id'          => $base_url . '#legalservice',
			'name'         => '법무법인 동주',
			'url'          => $base_url,
			'logo'         => $logo_url,
			'image'        => array( $og_image ),
			'telephone'    => $phone,
			'priceRange'   => '상담 문의',
			'areaServed'   => array(
				array( '@type' => 'AdministrativeArea', 'name' => '수원' ),
				array( '@type' => 'AdministrativeArea', 'name' => '경기도' ),
			),
			'serviceType'  => array( '성범죄 변호', '강제추행 변호', '카메라등이용촬영죄 변호', '아청법 변호' ),
			'address'      => $address,
		);
		if ( ! empty( $same_as ) ) {
			$legal_service['sameAs'] = $same_as;
		}

		$graph = array(
			array(
				'@type'       => 'WebSite',
				'@id'         => $base_url . '#website',
				'url'         => $base_url,
				'name'        => get_bloginfo( 'name' ),
				'inLanguage'  => 'ko-KR',
			),
			$legal_service,
			array(
				'@type'      => 'WebPage',
				'@id'        => $sc_page_url . '#webpage',
				'url'        => $sc_page_url,
				'name'       => '수원 성범죄 성공사례 | 법무법인 동주',
				'isPartOf'   => array( '@id' => $base_url . '#website' ),
				'about'      => array( '@id' => $base_url . '#legalservice' ),
				'inLanguage' => 'ko-KR',
				'breadcrumb' => array( '@id' => $sc_page_url . '#breadcrumb' ),
			),
			array(
				'@type'           => 'BreadcrumbList',
				'@id'             => $sc_page_url . '#breadcrumb',
				'itemListElement' => array(
					array( '@type' => 'ListItem', 'position' => 1, 'name' => '홈', 'item' => $base_url ),
					array( '@type' => 'ListItem', 'position' => 2, 'name' => '성공사례', 'item' => $sc_page_url ),
				),
			),
			array(
				'@type'           => 'ItemList',
				'@id'             => $sc_page_url . '#case-list',
				'name'            => '수원 성범죄 성공사례 목록',
				'itemListOrder'   => 'https://schema.org/ItemListOrderDescending',
				'numberOfItems'   => count( $sc_list_items ),
				'itemListElement' => $sc_list_items,
			),
		);

		$sc_schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);
		echo '<script type="application/ld+json">' . "\n" . wp_json_encode( $sc_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" . '</script>' . "\n";
	}

	// Sitemap structure ItemList (front page, SEO)
	if ( is_front_page() ) {
		$sitemap_items = array(
			array( 'name' => __( '메인 홈페이지', 'della-theme' ), 'url' => home_url( '/' ) ),
			array( 'name' => __( '성공 사례', 'della-theme' ), 'url' => home_url( '/#success-stories' ) ),
			array( 'name' => __( '진행 절차', 'della-theme' ), 'url' => home_url( '/#process-cards' ) ),
			array( 'name' => __( '주요 서비스', 'della-theme' ), 'url' => home_url( '/#major-services' ) ),
			array( 'name' => __( '대응 정보', 'della-theme' ), 'url' => home_url( '/성범죄-대응정보/' ) ),
			array( 'name' => __( '상담 신청', 'della-theme' ), 'url' => ( function_exists( 'della_theme_consultation_url' ) ? della_theme_consultation_url() : home_url( '/' ) ) ),
			array( 'name' => __( '오시는 길', 'della-theme' ), 'url' => home_url( '/#directions' ) ),
		);
		$sitemap_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'ItemList',
			'name'            => __( '사이트맵', 'della-theme' ),
			'description'     => __( '수원 성범죄 전문 변호사 사이트 주요 페이지 목록', 'della-theme' ),
			'itemListElement' => array(),
		);
		foreach ( $sitemap_items as $pos => $item ) {
			$sitemap_schema['itemListElement'][] = array(
				'@type'    => 'ListItem',
				'position' => $pos + 1,
				'name'     => $item['name'],
				'url'      => $item['url'],
			);
		}
		echo '<script type="application/ld+json">' . "\n" . wp_json_encode( $sitemap_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" . '</script>' . "\n";
	}

	if ( ! empty( $schema_breadcrumb['itemListElement'] ) && ! della_theme_is_success_cases_page() ) {
		echo '<script type="application/ld+json">' . "\n" . wp_json_encode( $schema_breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n" . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'della_theme_schema_json_ld', 3 );

/**
 * Get breadcrumb items for current request
 *
 * @return array List of items with 'label' and 'url'
 */
function della_theme_get_breadcrumb_items() {
	$items = array();

	$items[] = array( 'label' => __( 'Home', 'della-theme' ), 'url' => home_url( '/' ) );

	if ( is_front_page() ) {
		return $items;
	}

	if ( is_category() ) {
		$cat = get_queried_object();
		if ( $cat && ( $cat instanceof WP_Term ) ) {
			$items[] = array( 'label' => single_cat_title( '', false ), 'url' => get_category_link( $cat ) );
		}
	} elseif ( is_tag() ) {
		$tag = get_queried_object();
		if ( $tag && ( $tag instanceof WP_Term ) ) {
			$items[] = array( 'label' => single_tag_title( '', false ), 'url' => get_tag_link( $tag ) );
		}
	} elseif ( is_author() ) {
		$items[] = array( 'label' => get_the_author(), 'url' => get_author_posts_url( get_queried_object_id() ) );
	} elseif ( is_year() ) {
		$items[] = array( 'label' => get_the_date( 'Y' ), 'url' => get_year_link( get_query_var( 'year' ) ) );
	} elseif ( is_month() ) {
		$items[] = array( 'label' => get_the_date( 'F Y' ), 'url' => get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) ) );
	} elseif ( is_day() ) {
		$items[] = array( 'label' => get_the_date(), 'url' => get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) ) );
	} elseif ( is_post_type_archive() ) {
		$pt = get_query_var( 'post_type' );
		$pt = is_array( $pt ) ? reset( $pt ) : $pt;
		if ( $pt && is_string( $pt ) ) {
			$url = get_post_type_archive_link( $pt );
			if ( $url ) {
				$items[] = array( 'label' => post_type_archive_title( '', false ), 'url' => $url );
			}
		}
	} elseif ( is_search() ) {
		$items[] = array( 'label' => sprintf( __( 'Search: %s', 'della-theme' ), get_search_query() ), 'url' => get_search_link() );
	} elseif ( is_404() ) {
		$items[] = array( 'label' => __( 'Page not found', 'della-theme' ), 'url' => '' );
	}

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			if ( is_singular( 'post' ) ) {
				$cats = get_the_category( $post->ID );
				if ( ! empty( $cats ) ) {
					$cat = $cats[0];
					$items[] = array( 'label' => $cat->name, 'url' => get_category_link( $cat ) );
				}
			}
			$items[] = array( 'label' => get_the_title( $post ), 'url' => get_permalink( $post ) );
		}
	}

	return $items;
}

/**
 * 콘텐츠 img 태그에서 첨부 ID 추출 (class wp-image-N 또는 src로 검색)
 *
 * @param string $img_tag <img ...> 태그 전체
 * @return int 0 when not found
 */
function della_theme_content_image_attachment_id( $img_tag ) {
	if ( preg_match( '/\bwp-image-(\d+)\b/i', $img_tag, $m ) ) {
		return (int) $m[1];
	}
	if ( preg_match( '/\ssrc\s*=\s*(["\'])([^\1]+)\1/i', $img_tag, $m ) && ! empty( $m[2] ) ) {
		$id = attachment_url_to_postid( $m[2] );
		return $id ? $id : 0;
	}
	return 0;
}

/**
 * SEO: 콘텐츠 이미지 alt/title — 미디어에서 새로 추가·수정한 값 적용 후, 누락분만 보강
 * 글쓰기/업데이트 시 미디어 라이브러리 alt·title이 노출되도록 함.
 */
function della_theme_content_images_alt_title( $content ) {
	if ( ! is_string( $content ) || $content === '' || ! is_singular() ) {
		return $content;
	}
	$post = get_post();
	if ( ! $post instanceof WP_Post ) {
		return $content;
	}
	$page_title = get_the_title( $post );
	$context    = __( '수원 성범죄 전문 변호사', 'della-theme' ) . ' ' . __( '법무법인 동주', 'della-theme' );
	$fallback_alt = ( $page_title !== '' ? $page_title . ' - ' : '' ) . $context . ' ' . __( '관련 이미지', 'della-theme' );
	$fallback_alt = trim( $fallback_alt );

	$content = preg_replace_callback(
		'/<img\s([^>]*)>/i',
		function ( $m ) use ( $fallback_alt ) {
			$img_tag = $m[0];
			$attr_id = della_theme_content_image_attachment_id( $img_tag );
			$alt_from_media   = '';
			$title_from_media = '';
			if ( $attr_id > 0 ) {
				$alt_from_media   = get_post_meta( $attr_id, '_wp_attachment_image_alt', true );
				$title_from_media = get_the_title( $attr_id );
				if ( is_string( $alt_from_media ) ) {
					$alt_from_media = trim( $alt_from_media );
				} else {
					$alt_from_media = '';
				}
				if ( is_string( $title_from_media ) ) {
					$title_from_media = trim( $title_from_media );
				} else {
					$title_from_media = '';
				}
			}
			$current_alt = '';
			$current_title = '';
			if ( preg_match( '/\salt\s*=\s*(["\'])([^\1]*)\1/i', $img_tag, $alt_m ) ) {
				$current_alt = trim( $alt_m[2] );
			}
			if ( preg_match( '/\stitle\s*=\s*(["\'])([^\1]*)\1/i', $img_tag, $tit_m ) ) {
				$current_title = trim( $tit_m[2] );
			}
			// 미디어에서 새로 넣은 alt/title 우선 (글 업데이트 시 반영), 없으면 현재 태그값, 마지막으로 fallback
			$use_alt   = $attr_id > 0 && $alt_from_media !== '' ? $alt_from_media : ( $current_alt !== '' ? $current_alt : $fallback_alt );
			$use_title = $attr_id > 0 && $title_from_media !== '' ? $title_from_media : ( $current_title !== '' ? $current_title : '' );
			$out = $img_tag;
			$out = preg_replace( '/\salt\s*=\s*["\'][^"\']*["\']/i', ' alt="' . esc_attr( $use_alt ) . '"', $out, 1 );
			if ( ! preg_match( '/\salt\s*=/i', $out ) ) {
				$out = preg_replace( '/<img\s/', '<img alt="' . esc_attr( $use_alt ) . '" ', $out, 1 );
			}
			if ( $use_title !== '' ) {
				$out = preg_replace( '/\stitle\s*=\s*["\'][^"\']*["\']/i', ' title="' . esc_attr( $use_title ) . '"', $out, 1 );
				if ( ! preg_match( '/\stitle\s*=/i', $out ) ) {
					$out = preg_replace( '/<img\s/', '<img title="' . esc_attr( $use_title ) . '" ', $out, 1 );
				}
			}
			return $out;
		},
		$content
	);
	return $content;
}
add_filter( 'the_content', 'della_theme_content_images_alt_title', 8 );

/**
 * Add loading="lazy" to content images
 */
function della_theme_lazy_load_images( $content ) {
	if ( ! is_singular() ) {
		return $content;
	}
	return preg_replace( '/<img(?=\s)/', '<img loading="lazy"', $content );
}
add_filter( 'the_content', 'della_theme_lazy_load_images', 10 );

/**
 * 프론트 페이지 본문 내 H1 → H2 (메인 H1은 히어로만 유지, SEO 중복 방지)
 * 블록 에디터 등에서 본문에 넣은 제목 1을 제목 2로 바꿈.
 */
function della_theme_front_page_content_h1_to_h2( $content ) {
	if ( ! is_front_page() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	if ( ! is_string( $content ) || $content === '' ) {
		return $content;
	}
	$content = preg_replace( '/<h1(\s[^>]*)?>/i', '<h2$1>', $content );
	$content = str_ireplace( '</h1>', '</h2>', $content );
	return $content;
}
add_filter( 'the_content', 'della_theme_front_page_content_h1_to_h2', 15 );

/**
 * SEO: 대표 이미지(썸네일) alt/title — 미디어에서 설정한 값 우선, 없으면 글 제목 기반
 */
function della_theme_post_thumbnail_alt_title( $html, $post_id, $thumbnail_id ) {
	if ( ! is_string( $html ) || $html === '' ) {
		return $html;
	}
	$current_alt = '';
	$current_title = '';
	if ( preg_match( '/\salt\s*=\s*(["\'])([^\1]*)\1/i', $html, $alt_m ) ) {
		$current_alt = trim( $alt_m[2] );
	}
	if ( preg_match( '/\stitle\s*=\s*(["\'])([^\1]*)\1/i', $html, $tit_m ) ) {
		$current_title = trim( $tit_m[2] );
	}
	$alt_from_media   = '';
	$title_from_media = '';
	if ( $thumbnail_id ) {
		$alt_from_media   = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
		$title_from_media = get_the_title( $thumbnail_id );
		if ( is_string( $alt_from_media ) ) {
			$alt_from_media = trim( $alt_from_media );
		} else {
			$alt_from_media = '';
		}
		if ( is_string( $title_from_media ) ) {
			$title_from_media = trim( $title_from_media );
		} else {
			$title_from_media = '';
		}
	}
	// 미디어에서 새로 넣은 alt/title 우선 적용 (글 업데이트 시 반영)
	$use_alt   = $alt_from_media !== '' ? $alt_from_media : ( $current_alt !== '' ? $current_alt : '' );
	if ( $use_alt === '' ) {
		$fallback = $post_id ? get_the_title( $post_id ) : '';
		if ( $fallback === '' && $thumbnail_id ) {
			$fallback = get_the_title( $thumbnail_id );
		}
		if ( $fallback === '' ) {
			$fallback = get_bloginfo( 'name' );
		}
		$use_alt = $fallback . ' ' . __( '대표 이미지', 'della-theme' );
	}
	$use_title = $title_from_media !== '' ? $title_from_media : $current_title;
	$html = preg_replace( '/\salt\s*=\s*["\'][^"\']*["\']/i', ' alt="' . esc_attr( $use_alt ) . '"', $html, 1 );
	if ( ! preg_match( '/\salt\s*=/i', $html ) ) {
		$html = preg_replace( '/<img\s/', '<img alt="' . esc_attr( $use_alt ) . '" ', $html, 1 );
	}
	if ( $use_title !== '' ) {
		$html = preg_replace( '/\stitle\s*=\s*["\'][^"\']*["\']/i', ' title="' . esc_attr( $use_title ) . '"', $html, 1 );
		if ( ! preg_match( '/\stitle\s*=/i', $html ) ) {
			$html = preg_replace( '/<img\s/', '<img title="' . esc_attr( $use_title ) . '" ', $html, 1 );
		}
	}
	return $html;
}
add_filter( 'post_thumbnail_html', 'della_theme_post_thumbnail_alt_title', 10, 3 );

/**
 * Register widget areas
 */
function della_theme_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'della-theme' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here.', 'della-theme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s" aria-label="' . esc_attr__( 'Sidebar widget', 'della-theme' ) . '">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'della_theme_widgets_init' );

/**
 * Comment list callback - outputs semantic markup with lazy-loaded avatars
 */
function della_theme_comment_callback( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent', $comment ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, isset( $args['avatar_size'] ) ? $args['avatar_size'] : 60, '', '', array( 'loading' => 'lazy' ) ); ?>
					<?php printf( '<b class="fn">%s</b>', get_comment_author_link( $comment ) ); ?>
				</div>
				<div class="comment-metadata">
					<time datetime="<?php comment_time( 'c' ); ?>"><?php comment_date( '', $comment ); ?> <?php comment_time( '', $comment ); ?></time>
					<?php edit_comment_link( __( 'Edit', 'della-theme' ), '<span class="edit-link">', '</span>' ); ?>
				</div>
			</footer>
			<div class="comment-content">
				<?php comment_text( $comment ); ?>
			</div>
			<?php
			comment_reply_link( array_merge( $args, array(
				'add_below' => 'div-comment',
				'depth'     => $depth,
				'max_depth' => $args['max_depth'],
				'before'    => '<div class="reply">',
				'after'     => '</div>',
			) ) );
			?>
		</article>
	<?php
}

/**
 * SEO: 주기적 크롤·색인 모니터링 안내 (관리자 대시보드)
 * 불용 문서 증가 방지를 위해 네이버 서치어드바이저·Google Search Console 점검 권장
 */
function della_theme_seo_monitoring_notice() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->id !== 'dashboard' ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$dismissed = get_option( 'della_seo_monitoring_dismissed', 0 );
	if ( $dismissed && ( time() - (int) $dismissed ) < ( 30 * DAY_IN_SECONDS ) ) {
		return;
	}
	?>
	<div class="notice notice-info is-dismissible della-seo-monitoring-notice" data-nonce="<?php echo esc_attr( wp_create_nonce( 'della_seo_dismiss' ) ); ?>">
		<p><strong><?php esc_html_e( 'SEO 점검', 'della-theme' ); ?></strong> &mdash;
			<?php esc_html_e( '불용 문서가 늘지 않도록 크롤·색인 상태를 주기적으로 확인하세요.', 'della-theme' ); ?>
			<a href="https://searchadvisor.naver.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '네이버 서치어드바이저', 'della-theme' ); ?></a>,
			<a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Google Search Console', 'della-theme' ); ?></a>.
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'della_theme_seo_monitoring_notice' );

/**
 * SEO 모니터링 안내 닫기 시 30일간 미표시 (AJAX)
 */
function della_theme_seo_monitoring_dismiss() {
	check_ajax_referer( 'della_seo_dismiss', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error();
	}
	update_option( 'della_seo_monitoring_dismissed', time() );
	wp_send_json_success();
}
add_action( 'wp_ajax_della_seo_monitoring_dismiss', 'della_theme_seo_monitoring_dismiss' );

/**
 * 대시보드에서 SEO 안내 닫기 버튼 클릭 시 AJAX로 저장
 */
function della_theme_seo_monitoring_dismiss_script() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->id !== 'dashboard' ) {
		return;
	}
	?>
	<script>
	(function(){
		var el = document.querySelector('.della-seo-monitoring-notice');
		if (!el) return;
		var btn = el.querySelector('.notice-dismiss');
		if (!btn) return;
		btn.addEventListener('click', function(){
			var n = el.getAttribute('data-nonce');
			if (n) {
				var r = new XMLHttpRequest();
				r.open('POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>');
				r.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				r.send('action=della_seo_monitoring_dismiss&nonce=' + encodeURIComponent(n));
			}
		});
	})();
	</script>
	<?php
}
add_action( 'admin_footer', 'della_theme_seo_monitoring_dismiss_script' );
