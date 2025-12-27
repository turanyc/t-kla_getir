<?php
/**
 * The template to display the background video in the header
 *
 * @package TIPSY
 * @since TIPSY 1.0.14
 */
$tipsy_header_video = tipsy_get_header_video();
$tipsy_embed_video  = '';
if ( ! empty( $tipsy_header_video ) && ! tipsy_is_from_uploads( $tipsy_header_video ) ) {
	if ( tipsy_is_youtube_url( $tipsy_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $tipsy_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php tipsy_show_layout( tipsy_get_embed_video( $tipsy_header_video ) ); ?></div>
		<?php
	}
}
