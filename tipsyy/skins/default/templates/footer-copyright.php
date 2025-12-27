<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package TIPSY
 * @since TIPSY 1.0.10
 */

// Copyright area
?> 
<div class="footer_copyright_wrap
<?php
$tipsy_copyright_scheme = tipsy_get_theme_option( 'copyright_scheme' );
if ( ! empty( $tipsy_copyright_scheme ) && ! tipsy_is_inherit( $tipsy_copyright_scheme  ) ) {
	echo ' scheme_' . esc_attr( $tipsy_copyright_scheme );
}
?>
				">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text">
			<?php
				$tipsy_copyright = tipsy_get_theme_option( 'copyright' );
			if ( ! empty( $tipsy_copyright ) ) {
				// Replace {{Y}} or {Y} with the current year
				$tipsy_copyright = str_replace( array( '{{Y}}', '{Y}' ), date( 'Y' ), $tipsy_copyright );
				// Replace {{...}} and ((...)) on the <i>...</i> and <b>...</b>
				$tipsy_copyright = tipsy_prepare_macros( $tipsy_copyright );
				// Display copyright
				echo wp_kses( nl2br( $tipsy_copyright ), 'tipsy_kses_content' );
			}
			?>
			</div>
		</div>
	</div>
</div>
