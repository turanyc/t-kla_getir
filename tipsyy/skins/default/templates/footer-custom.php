<?php
/**
 * The template to display default site footer
 *
 * @package TIPSY
 * @since TIPSY 1.0.10
 */

$tipsy_footer_id = tipsy_get_custom_footer_id();
$tipsy_footer_meta = get_post_meta( $tipsy_footer_id, 'trx_addons_options', true );
if ( ! empty( $tipsy_footer_meta['margin'] ) ) {
	tipsy_add_inline_css( sprintf( '.page_content_wrap{padding-bottom:%s}', esc_attr( tipsy_prepare_css_value( $tipsy_footer_meta['margin'] ) ) ) );
}
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr( $tipsy_footer_id ); ?> footer_custom_<?php echo esc_attr( sanitize_title( get_the_title( $tipsy_footer_id ) ) ); ?>
						<?php
						$tipsy_footer_scheme = tipsy_get_theme_option( 'footer_scheme' );
						if ( ! empty( $tipsy_footer_scheme ) && ! tipsy_is_inherit( $tipsy_footer_scheme  ) ) {
							echo ' scheme_' . esc_attr( $tipsy_footer_scheme );
						}
						?>
						">
	<?php
	// Custom footer's layout
	do_action( 'tipsy_action_show_layout', $tipsy_footer_id );
	?>
</footer><!-- /.footer_wrap -->
