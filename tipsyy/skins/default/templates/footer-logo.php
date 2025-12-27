<?php
/**
 * The template to display the site logo in the footer
 *
 * @package TIPSY
 * @since TIPSY 1.0.10
 */

// Logo
if ( tipsy_is_on( tipsy_get_theme_option( 'logo_in_footer' ) ) ) {
	$tipsy_logo_image = tipsy_get_logo_image( 'footer' );
	$tipsy_logo_text  = get_bloginfo( 'name' );
	if ( ! empty( $tipsy_logo_image['logo'] ) || ! empty( $tipsy_logo_text ) ) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if ( ! empty( $tipsy_logo_image['logo'] ) ) {
					$tipsy_attr = tipsy_getimagesize( $tipsy_logo_image['logo'] );
					echo '<a href="' . esc_url( home_url( '/' ) ) . '">'
							. '<img src="' . esc_url( $tipsy_logo_image['logo'] ) . '"'
								. ( ! empty( $tipsy_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $tipsy_logo_image['logo_retina'] ) . ' 2x"' : '' )
								. ' class="logo_footer_image"'
								. ' alt="' . esc_attr__( 'Site logo', 'tipsy' ) . '"'
								. ( ! empty( $tipsy_attr[3] ) ? ' ' . wp_kses_data( $tipsy_attr[3] ) : '' )
							. '>'
						. '</a>';
				} elseif ( ! empty( $tipsy_logo_text ) ) {
					echo '<h1 class="logo_footer_text">'
							. '<a href="' . esc_url( home_url( '/' ) ) . '">'
								. esc_html( $tipsy_logo_text )
							. '</a>'
						. '</h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
