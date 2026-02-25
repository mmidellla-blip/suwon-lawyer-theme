<?php
/**
 * 변호사 상세 프로필: 상단 가로 풀 이미지(_profile) 위에 프로필 오버레이
 *
 * @package Della_Theme
 * @var array $args [ 'lawyer' => array, 'img_base' => string, 'img_dir' => string ]
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$lawyer   = isset( $args['lawyer'] ) ? $args['lawyer'] : array();
$img_base = isset( $args['img_base'] ) ? $args['img_base'] : '';
$img_dir  = isset( $args['img_dir'] ) ? $args['img_dir'] : '';
if ( empty( $lawyer ) ) {
	return;
}

// 상세 페이지용: _profile 이미지가 있으면 사용, 없으면 목록용 이미지
$profile_file = isset( $lawyer['image_profile'] ) ? $lawyer['image_profile'] : '';
$use_profile  = $profile_file !== '' && $img_dir !== '' && file_exists( trailingslashit( $img_dir ) . $profile_file );
$detail_image = $use_profile ? $profile_file : $lawyer['image'];

$img_src    = trailingslashit( $img_base ) . $detail_image;
$img_srcset = $use_profile ? '' : della_theme_lawyer_image_srcset( $lawyer['image'], $img_base, $img_dir );
$img_alt    = $lawyer['name'] . ' ' . $lawyer['title'] . ' ' . __( '프로필 사진', 'della-theme' );
$has_quote  = ! empty( $lawyer['quote'] );
?>
<article class="lawyer-profile" itemscope itemtype="https://schema.org/Person">
	<div class="lawyer-profile-hero" style="background-image: url(<?php echo esc_url( $img_src ); ?>);">
		<div class="lawyer-profile-overlay" aria-hidden="true"></div>
		<img
			src="<?php echo esc_url( $img_src ); ?>"
			alt="<?php echo esc_attr( $img_alt ); ?>"
			class="lawyer-profile-photo-sr"
			itemprop="image"
		/>
		<div class="lawyer-profile-content">
			<?php if ( $has_quote ) : ?>
				<blockquote class="lawyer-profile-quote">
					<span class="lawyer-profile-quote-mark" aria-hidden="true">"</span>
					<p class="lawyer-profile-quote-text"><?php echo esc_html( $lawyer['quote'] ); ?></p>
				</blockquote>
			<?php endif; ?>
			<header class="lawyer-profile-header">
				<h2 class="lawyer-profile-name"><span itemprop="name"><?php echo esc_html( $lawyer['name'] ); ?></span></h2>
				<span class="lawyer-profile-title" itemprop="jobTitle"><?php echo esc_html( $lawyer['title'] ); ?></span>
			</header>
			<?php if ( ! empty( $lawyer['items'] ) ) : ?>
				<?php $profile_list_items = array_slice( $lawyer['items'], 0, 3 ); ?>
				<ul class="lawyer-profile-list" aria-label="<?php echo esc_attr( $lawyer['name'] . ' ' . __( '변호사 경력', 'della-theme' ) ); ?>">
					<?php foreach ( $profile_list_items as $item ) : ?>
						<li><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</article>
