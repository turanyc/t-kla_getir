<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

$tipsy_args = get_query_var( 'tipsy_logo_args' );

// Site logo
$tipsy_logo_type   = isset( $tipsy_args['type'] ) ? $tipsy_args['type'] : '';
$tipsy_logo_image  = tipsy_get_logo_image( $tipsy_logo_type );
$tipsy_logo_text   = tipsy_is_on( tipsy_get_theme_option( 'logo_text' ) ) ? get_bloginfo( 'name' ) : '';
$tipsy_logo_slogan = get_bloginfo( 'description', 'display' );
if ( ! empty( $tipsy_logo_image['logo'] ) || ! empty( $tipsy_logo_text ) ) {
	?><a class="sc_layouts_logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php
		if ( ! empty( $tipsy_logo_image['logo'] ) ) {
			if ( empty( $tipsy_logo_type ) && function_exists( 'the_custom_logo' ) && is_numeric($tipsy_logo_image['logo']) && (int) $tipsy_logo_image['logo'] > 0 ) {
				the_custom_logo();
			} else {
				$tipsy_attr = tipsy_getimagesize( $tipsy_logo_image['logo'] );
				echo '<img src="' . esc_url( $tipsy_logo_image['logo'] ) . '"'
						. ( ! empty( $tipsy_logo_image['logo_retina'] ) ? ' srcset="' . esc_url( $tipsy_logo_image['logo_retina'] ) . ' 2x"' : '' )
						. ' alt="' . esc_attr( $tipsy_logo_text ) . '"'
						. ( ! empty( $tipsy_attr[3] ) ? ' ' . wp_kses_data( $tipsy_attr[3] ) : '' )
						. '>';
			}
		} else {
			tipsy_show_layout( tipsy_prepare_macros( $tipsy_logo_text ), '<span class="logo_text">', '</span>' );
			tipsy_show_layout( tipsy_prepare_macros( $tipsy_logo_slogan ), '<span class="logo_slogan">', '</span>' );
		}
		?>
	</a>
	<?php
}
