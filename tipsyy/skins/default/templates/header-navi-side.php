<?php
/**
 * The template to display the side menu
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */
?>
<div class="menu_side_wrap
<?php
echo ' menu_side_' . esc_attr( tipsy_get_theme_option( 'menu_side_icons' ) > 0 ? 'icons' : 'dots' );
$tipsy_menu_scheme = tipsy_get_theme_option( 'menu_scheme' );
$tipsy_header_scheme = tipsy_get_theme_option( 'header_scheme' );
if ( ! empty( $tipsy_menu_scheme ) && ! tipsy_is_inherit( $tipsy_menu_scheme  ) ) {
	echo ' scheme_' . esc_attr( $tipsy_menu_scheme );
} elseif ( ! empty( $tipsy_header_scheme ) && ! tipsy_is_inherit( $tipsy_header_scheme ) ) {
	echo ' scheme_' . esc_attr( $tipsy_header_scheme );
}
?>
				">
	<span class="menu_side_button icon-menu-2"></span>

	<div class="menu_side_inner">
		<?php
		// Logo
		set_query_var( 'tipsy_logo_args', array( 'type' => 'side' ) );
		get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/header-logo' ) );
		set_query_var( 'tipsy_logo_args', array() );
		// Main menu button
		?>
		<div class="toc_menu_item"
			<?php
			if ( tipsy_mouse_helper_enabled() ) {
				echo ' data-mouse-helper="click" data-mouse-helper-axis="y" data-mouse-helper-text="' . esc_attr__( 'Open main menu', 'tipsy' ) . '"';
			}
			?>
		>
			<a href="#" class="toc_menu_description menu_mobile_description"><span class="toc_menu_description_title"><?php esc_html_e( 'Main menu', 'tipsy' ); ?></span></a>
			<a class="menu_mobile_button toc_menu_icon icon-menu-2" href="#"></a>
		</div>		
	</div>

</div><!-- /.menu_side_wrap -->
