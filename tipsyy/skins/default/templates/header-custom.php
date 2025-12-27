<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package TIPSY
 * @since TIPSY 1.0.06
 */

$tipsy_header_css   = '';
$tipsy_header_image = get_header_image();
$tipsy_header_video = tipsy_get_header_video();
if ( ! empty( $tipsy_header_image ) && tipsy_trx_addons_featured_image_override( is_singular() || tipsy_storage_isset( 'blog_archive' ) || is_category() ) ) {
	$tipsy_header_image = tipsy_get_current_mode_image( $tipsy_header_image );
}

$tipsy_header_id = tipsy_get_custom_header_id();
$tipsy_header_meta = get_post_meta( $tipsy_header_id, 'trx_addons_options', true );
if ( ! empty( $tipsy_header_meta['margin'] ) ) {
	tipsy_add_inline_css( sprintf( '.page_content_wrap{padding-top:%s}', esc_attr( tipsy_prepare_css_value( $tipsy_header_meta['margin'] ) ) ) );
}

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr( $tipsy_header_id ); ?> top_panel_custom_<?php echo esc_attr( sanitize_title( get_the_title( $tipsy_header_id ) ) ); ?>
				<?php
				echo ! empty( $tipsy_header_image ) || ! empty( $tipsy_header_video )
					? ' with_bg_image'
					: ' without_bg_image';
				if ( '' != $tipsy_header_video ) {
					echo ' with_bg_video';
				}
				if ( '' != $tipsy_header_image ) {
					echo ' ' . esc_attr( tipsy_add_inline_css_class( 'background-image: url(' . esc_url( $tipsy_header_image ) . ');' ) );
				}
				if ( is_single() && has_post_thumbnail() ) {
					echo ' with_featured_image';
				}
				if ( tipsy_is_on( tipsy_get_theme_option( 'header_fullheight' ) ) ) {
					echo ' header_fullheight tipsy-full-height';
				}
				$tipsy_header_scheme = tipsy_get_theme_option( 'header_scheme' );
				if ( ! empty( $tipsy_header_scheme ) && ! tipsy_is_inherit( $tipsy_header_scheme  ) ) {
					echo ' scheme_' . esc_attr( $tipsy_header_scheme );
				}
				?>
">
	<?php

	// Background video
	if ( ! empty( $tipsy_header_video ) ) {
		get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/header-video' ) );
	}

	// Custom header's layout
	do_action( 'tipsy_action_show_layout', $tipsy_header_id );

	// Header widgets area
	get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/header-widgets' ) );

	?>
</header>
