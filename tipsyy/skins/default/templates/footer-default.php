<?php
/**
 * The template to display default site footer
 *
 * @package TIPSY
 * @since TIPSY 1.0.10
 */

?>
<footer class="footer_wrap footer_default
<?php
$tipsy_footer_scheme = tipsy_get_theme_option( 'footer_scheme' );
if ( ! empty( $tipsy_footer_scheme ) && ! tipsy_is_inherit( $tipsy_footer_scheme  ) ) {
	echo ' scheme_' . esc_attr( $tipsy_footer_scheme );
}
?>
				">
	<?php

	// Footer widgets area
	get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/footer-widgets' ) );

	// Logo
	get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/footer-logo' ) );

	// Socials
	get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/footer-socials' ) );

	// Copyright area
	get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/footer-copyright' ) );

	?>
</footer><!-- /.footer_wrap -->
