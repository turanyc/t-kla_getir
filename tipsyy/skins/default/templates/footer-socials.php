<?php
/**
 * The template to display the socials in the footer
 *
 * @package TIPSY
 * @since TIPSY 1.0.10
 */


// Socials
if ( tipsy_is_on( tipsy_get_theme_option( 'socials_in_footer' ) ) ) {
	$tipsy_output = tipsy_get_socials_links();
	if ( '' != $tipsy_output ) {
		?>
		<div class="footer_socials_wrap socials_wrap">
			<div class="footer_socials_inner">
				<?php tipsy_show_layout( $tipsy_output ); ?>
			</div>
		</div>
		<?php
	}
}
