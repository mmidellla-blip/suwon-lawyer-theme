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
 * CSS/JS 캐시 무효화용 버전 (실서버 배포 시 바로 반영)
 * 파일 수정시간(filemtime) 기반 — 배포 시 쿼리스트링이 바뀌어 캐시 미사용
 *
 * @param string $relative_path 테마 내 상대 경로. 예: 'style.css', 'js/theme.js'
 * @return string
 */
function della_theme_asset_version( $relative_path = 'style.css' ) {
	$file = get_stylesheet_directory() . '/' . ltrim( $relative_path, '/' );
	if ( file_exists( $file ) ) {
		$mtime = filemtime( $file );
		if ( $mtime ) {
			return (string) $mtime;
		}
	}
	return DELLA_THEME_VERSION;
}

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
	if ( get_option( 'della_lawyers_page_created' ) ) {
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
 * 테마 전환 시 rewrite flush (lawyers/{slug} 반영)
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
	if ( get_option( 'della_response_board_page_created' ) ) {
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
	}
}
add_action( 'after_switch_theme', 'della_theme_ensure_response_board_page' );
add_action( 'init', 'della_theme_ensure_response_board_page', 0 );

/**
 * 성범죄 대응정보 게시판 페이지 URL
 */
function della_theme_response_board_page_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-response-board.php',
		'number'     => 1,
	) );
	if ( ! empty( $pages ) ) {
		return get_permalink( $pages[0] );
	}
	return home_url( '/response-info/' );
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
	if ( get_option( 'della_success_cases_page_created' ) ) {
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
	}
}
add_action( 'after_switch_theme', 'della_theme_ensure_success_cases_page' );
add_action( 'init', 'della_theme_ensure_success_cases_page', 0 );

/**
 * 성범죄 성공사례 게시판 페이지 URL
 */
function della_theme_success_cases_page_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-success-cases.php',
		'number'     => 1,
	) );
	if ( ! empty( $pages ) ) {
		return get_permalink( $pages[0] );
	}
	return home_url( '/success-cases/' );
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
 */
function della_theme_is_success_cases_page() {
	return is_singular( 'page' ) && get_page_template_slug() === 'page-success-cases.php';
}

/**
 * SEO: Front page / 성범죄 전문 변호사 / 성범죄 대응정보 / 성범죄 성공사례 페이지 document title
 */
function della_theme_document_title_parts( $parts ) {
	if ( is_front_page() ) {
		$parts['title'] = __( '수원성범죄변호사', 'della-theme' ) . ' · ' . __( '수원성범죄전문변호사', 'della-theme' ) . ' | ' . get_bloginfo( 'name' );
		if ( isset( $parts['tagline'] ) ) {
			unset( $parts['tagline'] );
		}
		return $parts;
	}
	if ( della_theme_is_lawyers_page() ) {
		$parts['title'] = __( '성범죄 전문 변호사', 'della-theme' ) . ' | ' . get_bloginfo( 'name' );
		if ( isset( $parts['tagline'] ) ) {
			unset( $parts['tagline'] );
		}
		return $parts;
	}
	if ( della_theme_is_response_board_page() ) {
		$parts['title'] = __( '성범죄 대응정보', 'della-theme' ) . ' | ' . get_bloginfo( 'name' );
		if ( isset( $parts['tagline'] ) ) {
			unset( $parts['tagline'] );
		}
		return $parts;
	}
	if ( della_theme_is_success_cases_page() ) {
		$parts['title'] = __( '성범죄 성공사례', 'della-theme' ) . ' | ' . get_bloginfo( 'name' );
		if ( isset( $parts['tagline'] ) ) {
			unset( $parts['tagline'] );
		}
		return $parts;
	}
	return $parts;
}
add_filter( 'document_title_parts', 'della_theme_document_title_parts', 10, 1 );

/**
 * SEO: meta keywords (네이버·다음 등 국내 검색엔진 대응)
 * 키워드 우선순위: 1순위·2순위·블루오션
 */
function della_theme_seo_meta_keywords() {
	$keywords = array(
		__( '수원성범죄변호사', 'della-theme' ),
		__( '수원성범죄전문변호사', 'della-theme' ),
		__( '성범죄피해자변호사', 'della-theme' ),
		__( '성범죄전문변호사', 'della-theme' ),
		__( '성범죄변호사', 'della-theme' ),
		__( '형사전문변호사', 'della-theme' ),
		__( '형사변호사', 'della-theme' ),
		__( '수원성추행변호사', 'della-theme' ),
		__( '성범죄일부노출', 'della-theme' ),
		get_bloginfo( 'name' ),
	);
	if ( della_theme_is_response_board_page() ) {
		$keywords = array_merge( array(
			__( '성범죄 대응정보', 'della-theme' ),
			__( '성범죄 법조문', 'della-theme' ),
			__( '성범죄 판례', 'della-theme' ),
			__( '강간 강제추행 대응', 'della-theme' ),
			__( '불법촬영 군성범죄', 'della-theme' ),
		), $keywords );
	}
	if ( della_theme_is_success_cases_page() ) {
		$keywords = array_merge( array(
			__( '성범죄 성공사례', 'della-theme' ),
			__( '성범죄 판례', 'della-theme' ),
			__( '강간 강제추행 성공사례', 'della-theme' ),
			__( '군성범죄 불법촬영 성공사례', 'della-theme' ),
		), $keywords );
	}
	echo '<meta name="keywords" content="' . esc_attr( implode( ', ', $keywords ) ) . '" />' . "\n";
}
add_action( 'wp_head', 'della_theme_seo_meta_keywords', 1 );

/**
 * SEO: Front page default meta description (키워드 포함, 155자 내외)
 */
function della_theme_front_page_description() {
	if ( ! is_front_page() ) {
		return null;
	}
	$custom = get_bloginfo( 'description' );
	if ( $custom && strlen( $custom ) >= 20 ) {
		$kw = __( '수원성범죄전문변호사', 'della-theme' );
		if ( strpos( $custom, $kw ) === false ) {
			return $kw . ' — ' . $custom;
		}
		return $custom;
	}
	return __( '수원성범죄변호사, 수원성범죄전문변호사 ', 'della-theme' ) . get_bloginfo( 'name' ) . __( ' 수원 성범죄연구센터. 성범죄피해자변호사·형사전문변호사 상담. 수원 광교 신속 상담.', 'della-theme' );
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
	$alt = 'alt="' . esc_attr( $site_name ) . '"';
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
 * Enqueue scripts and styles (minimal for fast loading)
 */
function della_theme_scripts() {
	wp_enqueue_style(
		'della-google-fonts',
		'https://fonts.googleapis.com/css2?family=Nanum+Myeongjo:wght@400;700&family=Noto+Sans+KR:wght@400;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'della-theme-style',
		get_stylesheet_uri(),
		array( 'della-google-fonts' ),
		della_theme_asset_version( 'style.css' )
	);

	$theme_js = get_theme_file_path( 'js/theme.js' );
	if ( file_exists( $theme_js ) ) {
		wp_enqueue_script(
			'della-theme-js',
			get_theme_file_uri( 'js/theme.js' ),
			array(),
			della_theme_asset_version( 'js/theme.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'della_theme_scripts' );

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
	$path_info = pathinfo( $image_filename );
	$name      = isset( $path_info['filename'] ) ? $path_info['filename'] : '';
	$ext       = isset( $path_info['extension'] ) ? $path_info['extension'] : 'png';
	$file_2x   = $name . '@2x.' . $ext;
	$path_2x   = trailingslashit( $base_dir ) . $file_2x;
	if ( ! empty( $name ) && file_exists( $path_2x ) ) {
		return trailingslashit( $base_url ) . $file_2x;
	}
	return trailingslashit( $base_url ) . $image_filename;
}

/**
 * 변호사 프로필 이미지 srcset (고해상도 2x 지원)
 * uploads/2026/02/ 에 파일명@2x.확장자 있으면 2x로 사용
 *
 * @param string $image_filename 예: dongju-kim-yunseo-lawyer.png
 * @param string $base_url       예: uploads baseurl + /2026/02
 * @param string $base_dir       예: uploads basedir + /2026/02
 * @return string srcset 속성값 또는 빈 문자열
 */
function della_theme_lawyer_image_srcset( $image_filename, $base_url, $base_dir ) {
	$path_info = pathinfo( $image_filename );
	$name      = isset( $path_info['filename'] ) ? $path_info['filename'] : '';
	$ext       = isset( $path_info['extension'] ) ? $path_info['extension'] : 'png';
	$file_2x   = $name . '@2x.' . $ext;
	$path_2x   = trailingslashit( $base_dir ) . $file_2x;
	if ( ! empty( $name ) && file_exists( $path_2x ) ) {
		$url_1x = trailingslashit( $base_url ) . $image_filename;
		$url_2x = trailingslashit( $base_url ) . $file_2x;
		return esc_url( $url_1x ) . ' 1x, ' . esc_url( $url_2x ) . ' 2x';
	}
	return '';
}

/**
 * 성범죄 전문 변호사 페이지 URL (GNB·사이트맵 등)
 * 템플릿 적용된 페이지가 있으면 해당 주소, 없으면 고정 슬러그 반환
 *
 * @return string
 */
function della_theme_lawyers_page_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-lawyers.php',
		'number'     => 1,
	) );
	if ( ! empty( $pages ) ) {
		return get_permalink( $pages[0] );
	}
	return home_url( '/lawyers/' );
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
 * @return array { slug, name, title, image, image_profile?, quote?, specialties[], education[], items[] }
 */
function della_theme_get_lawyers() {
	return array(
		array(
			'slug'         => 'dongju-kim-yunseo',
			'name'         => '김윤서',
			'title'        => '파트너변호사',
			'image'        => 'dongju-kim-yunseo-lawyer.png',
			'image_profile'=> 'dongju-kim-yunseo_profile.jpg',
			'quote'        => '',
			'specialties'  => array( '형사법', '소년법' ),
			'education'    => array( '고려대학교 법학과 졸업', '고려대학교 대학원 법학박사(형사법) 수료' ),
			'items'        => array( '대한변협[형사법] 전문 변호사', '대현변협[소년법] 전문 변호사', '고려대 법학박사(형사법) 수료' ),
		),
		array(
			'slug'         => 'dongju-jo-wonjin',
			'name'         => '조원진',
			'title'        => '파트너변호사',
			'image'        => 'dongju-jo-wonjin-lawyer.png',
			'image_profile'=> 'dongju-jo-wonjin_profile.jpg',
			'quote'        => '',
			'specialties'  => array( '민사법' ),
			'education'    => array(),
			'items'        => array( '대한변협[민사법] 전문 변호사', '수원지방검찰청 국선변호사', '일산서부경찰서 법률자문변호사' ),
		),
		array(
			'slug'         => 'dongju-oh-seojin',
			'name'         => '오서진',
			'title'        => '파트너변호사',
			'image'        => 'dongju-oh-seojin-lawyer.png',
			'image_profile'=> 'dongju-oh-seojin_profile.jpg',
			'quote'        => '',
			'specialties'  => array( '형사법' ),
			'education'    => array( '연세대학교 법학전문박사과정(형사법)' ),
			'items'        => array( '대한변협[형사법] 전문 변호사', '연세대 법학전문박사과정(형사법)', '전 대법원 국선변호인' ),
		),
		array(
			'slug'         => 'dongju-park-dongjin',
			'name'         => '박동진',
			'title'        => '파트너변호사',
			'image'        => 'dongju-park-dongjin-lawyer.png',
			'image_profile'=> 'dongju-park-dongjin_profile.jpg',
			'quote'        => '의뢰인들의 78%가 박동진 변호사를 노련함이라 평가했습니다.',
			'specialties'  => array( '형사법', '학교폭력' ),
			'education'    => array( '계성고등학교 졸업', '서울대학교 법과대학 법학과 졸업', '서울대학교 대학원 법학과 졸업(법학석사)', '러시아 모스크바법과대학 연수', '사법시험 합격', '사법연수원 21기' ),
			'items'        => array( '대한변협[형사법] 전문 변호사', '전 부산지검 부장검사','전 대구지검 경주지청 지청장')
		),
		array(
			'slug'         => 'dongju-isejin',
			'name'         => '이세진',
			'title'        => '파트너변호사',
			'image'        => 'dongju-isejin-lawyer.png',
			'image_profile'=> 'dongju-isejin_profile.jpg',
			'quote'        => '',
			'specialties'  => array( '형사법' ),
			'education'    => array(),
			'items'        => array( '대한변협[형사법] 전문 변호사', '전 국가정보원 기획조정실 변호사', '현 해군본부 군검찰 국선변호인' ),
		),
		array(
			'slug'         => 'dongju-leesewhan',
			'name'         => '이세환',
			'title'        => '파트너변호사',
			'image'        => 'dongju-leesewhan-lawyer.png',
			'image_profile'=> 'dongju-leesewhan_profile.jpg',
			'quote'        => '',
			'specialties'  => array( '형사법' ),
			'education'    => array( '연세대학교 법학전문박사과정(형사법)' ),
			'items'        => array( '대한변협[형사법] 전문 변호사', '연세대 법학전문박사과정(형사법)', '전 대법원 국선변호인' ),
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
 * Add canonical URL
 */
function della_theme_canonical() {
	if ( is_singular() ) {
		echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '" />' . "\n";
	} elseif ( is_home() && get_option( 'show_on_front' ) === 'posts' ) {
		echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '" />' . "\n";
	} elseif ( is_archive() ) {
		echo '<link rel="canonical" href="' . esc_url( get_pagenum_link( 1, false ) ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'della_theme_canonical', 1 );

/**
 * Preload hero background image on front page (LCP, mobile Core Web Vitals)
 */
function della_theme_hero_preload() {
	if ( ! is_front_page() ) {
		return;
	}
	$upload_dir = wp_upload_dir();
	if ( empty( $upload_dir['baseurl'] ) ) {
		return;
	}
	$hero_url = $upload_dir['baseurl'] . '/2026/02/dongju-law-hero-banner.webp';
	echo '<link rel="preload" href="' . esc_url( $hero_url ) . '" as="image" />' . "\n";
}
add_action( 'wp_head', 'della_theme_hero_preload', 0 );

/**
 * Open Graph and Twitter Card meta tags
 */
function della_theme_og_twitter_meta() {
	if ( ! is_singular() && ! is_front_page() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = '';
	$url         = is_singular() ? get_permalink() : home_url( '/' );
	if ( ! $url || ! is_string( $url ) ) {
		$url = home_url( '/' );
	}
	$image       = '';
	$type        = is_singular() ? 'article' : 'website';

	if ( empty( $title ) ) {
		$title = get_bloginfo( 'name' ) . ( get_bloginfo( 'description' ) ? ' - ' . get_bloginfo( 'description' ) : '' );
	}

	if ( is_front_page() ) {
		$description = della_theme_front_page_description();
	} elseif ( della_theme_is_lawyers_page() ) {
		$description = get_bloginfo( 'name' ) . ' ' . __( '수원 성범죄 전문 변호사 팀 6인 소개. 강간·강제추행·불법촬영·디지털성범죄 등 성범죄 사건 초기 대응부터 재판까지 전문 변호사가 함께합니다.', 'della-theme' );
		$lawyers = della_theme_get_lawyers();
		$upload_dir = wp_upload_dir();
		$img_base = $upload_dir['baseurl'] . '/2026/02';
		if ( ! empty( $lawyers[0]['image'] ) ) {
			$image = della_theme_lawyer_image_url( $lawyers[0]['image'], $img_base, $upload_dir['basedir'] . '/2026/02' );
		}
	} elseif ( della_theme_is_response_board_page() ) {
		$description = __( '성범죄 대응정보: 강간·강제추행·군성범죄·불법촬영·성매매·직장내 성희롱 등 법조문, 판례, FAQ, 수사·재판 단계별 대응 가이드. 수원 성범죄 전문 변호사.', 'della-theme' );
		$description = wp_strip_all_tags( $description );
		if ( function_exists( 'mb_strlen' ) && mb_strlen( $description ) > 155 ) {
			$description = mb_substr( $description, 0, 152 ) . '…';
		} elseif ( strlen( $description ) > 155 ) {
			$description = substr( $description, 0, 152 ) . '…';
		}
		$type = 'website';
	} elseif ( della_theme_is_success_cases_page() ) {
		$description = __( '성범죄 성공사례: 강간·강제추행·군성범죄·불법촬영·성매매·직장내 성희롱 등 수원 성범죄 전문 변호사 성공 사례 모음.', 'della-theme' );
		$description = wp_strip_all_tags( $description );
		if ( function_exists( 'mb_strlen' ) && mb_strlen( $description ) > 155 ) {
			$description = mb_substr( $description, 0, 152 ) . '…';
		} elseif ( strlen( $description ) > 155 ) {
			$description = substr( $description, 0, 152 ) . '…';
		}
		$type = 'website';
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( get_the_content( null, false, $post ), 30 );
			$description = wp_strip_all_tags( $description );
			if ( has_post_thumbnail( $post ) ) {
				$image = get_the_post_thumbnail_url( $post, 'large' );
			}
		}
	}

	if ( empty( $description ) ) {
		$description = get_bloginfo( 'description' );
	}
	if ( empty( $image ) ) {
		$image = get_site_icon_url( 512 );
	}

	$description = is_string( $description ) ? wp_strip_all_tags( $description ) : '';
	$description = preg_replace( '/\s+/', ' ', $description );
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
	<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
	<meta property="og:locale" content="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>" />
	<meta name="twitter:card" content="<?php echo $image ? 'summary_large_image' : 'summary'; ?>" />
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>" />
	<?php if ( $image ) : ?>
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" />
	<?php endif;
}
add_action( 'wp_head', 'della_theme_og_twitter_meta', 2 );

/**
 * Schema.org JSON-LD structured data
 */
function della_theme_schema_json_ld() {
	$schema           = array();
	$schema_breadcrumb = array();

	// WebSite (front page: static or blog)
	$schema['@context'] = 'https://schema.org';
	if ( is_front_page() ) {
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

	// WebPage (single page) / CollectionPage (성범죄 대응정보)
	if ( is_singular( 'page' ) ) {
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
			if ( della_theme_is_success_cases_page() ) {
				$schema['@type'] = array( 'WebPage', 'CollectionPage' );
				$schema['description'] = __( '성범죄 성공사례: 강간·강제추행·군성범죄·불법촬영·성매매·직장내 성희롱 등 수원 성범죄 전문 변호사 성공 사례 모음.', 'della-theme' );
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
		$rb_cat = get_category_by_slug( '대응정보' );
		if ( ! $rb_cat ) {
			$rb_cat = get_category_by_slug( 'response-info' );
		}
		if ( ! $rb_cat ) {
			$cats = get_categories( array( 'hide_empty' => false ) );
			foreach ( $cats as $c ) {
				if ( $c->name === '대응 정보' || $c->name === '대응정보' ) {
					$rb_cat = $c;
					break;
				}
			}
		}
		$rb_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 15,
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
					'name'     => get_the_title( $pid ),
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

	// 성범죄 성공사례 페이지: ItemList (목록 구조화, SEO)
	if ( della_theme_is_success_cases_page() ) {
		$sc_slugs = array( 'rape', 'sexual_assult', 'military_sexual_crimes', 'sex_work', 'spycam_crime', 'workplace' );
		$sc_cat_ids = array();
		foreach ( $sc_slugs as $slug ) {
			$term = get_category_by_slug( $slug );
			if ( $term ) {
				$sc_cat_ids[] = $term->term_id;
			}
		}
		$sc_args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 15,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
		);
		if ( ! empty( $sc_cat_ids ) ) {
			$sc_args['category__in'] = $sc_cat_ids;
		} else {
			$sc_args['category__in'] = array( 0 );
		}
		$sc_query = new WP_Query( $sc_args );
		$sc_items = array();
		if ( $sc_query->have_posts() ) {
			$pos = 0;
			foreach ( $sc_query->posts as $pid ) {
				$pos++;
				$sc_items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos,
					'url'      => get_permalink( $pid ),
					'name'     => get_the_title( $pid ),
				);
			}
		}
		wp_reset_postdata();
		$sc_schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'ItemList',
			'name'            => __( '성범죄 성공사례 목록', 'della-theme' ),
			'description'     => __( '성범죄 성공사례 게시판. 강간·강제추행·군성범죄·불법촬영·성매매·직장내 성희롱 등 수원 성범죄 전문 변호사 성공 사례.', 'della-theme' ),
			'url'             => get_permalink(),
			'numberOfItems'   => count( $sc_items ),
			'itemListElement' => $sc_items,
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
			array( 'name' => __( '상담 신청', 'della-theme' ), 'url' => home_url( '/#consultation-cta' ) ),
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

	if ( ! empty( $schema_breadcrumb['itemListElement'] ) ) {
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
 * Add loading="lazy" to content images
 */
function della_theme_lazy_load_images( $content ) {
	if ( ! is_singular() ) {
		return $content;
	}
	return preg_replace( '/<img(?=\s)/', '<img loading="lazy"', $content );
}
add_filter( 'the_content', 'della_theme_lazy_load_images' );

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
