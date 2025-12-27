<?php
/**
 * The template to display the widgets area in the header
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

// Header sidebar
$tipsy_header_name    = tipsy_get_theme_option( 'header_widgets' );
$tipsy_header_present = ! tipsy_is_off( $tipsy_header_name ) && is_active_sidebar( $tipsy_header_name );
if ( $tipsy_header_present ) {
	tipsy_storage_set( 'current_sidebar', 'header' );
	$tipsy_header_wide = tipsy_get_theme_option( 'header_wide' );
	ob_start();
	if ( is_active_sidebar( $tipsy_header_name ) ) {
		dynamic_sidebar( $tipsy_header_name );
	}
	$tipsy_widgets_output = ob_get_contents();
	ob_end_clean();
	if ( ! empty( $tipsy_widgets_output ) ) {
		$tipsy_widgets_output = preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $tipsy_widgets_output );
		$tipsy_need_columns   = strpos( $tipsy_widgets_output, 'columns_wrap' ) === false;
		if ( $tipsy_need_columns ) {
			$tipsy_columns = max( 0, (int) tipsy_get_theme_option( 'header_columns' ) );
			if ( 0 == $tipsy_columns ) {
				$tipsy_columns = min( 6, max( 1, tipsy_tags_count( $tipsy_widgets_output, 'aside' ) ) );
			}
			if ( $tipsy_columns > 1 ) {
				$tipsy_widgets_output = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $tipsy_columns ) . ' widget', $tipsy_widgets_output );
			} else {
				$tipsy_need_columns = false;
			}
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo ! empty( $tipsy_header_wide ) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<?php do_action( 'tipsy_action_before_sidebar_wrap', 'header' ); ?>
			<div class="header_widgets_inner widget_area_inner">
				<?php
				if ( ! $tipsy_header_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $tipsy_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'tipsy_action_before_sidebar', 'header' );
				tipsy_show_layout( $tipsy_widgets_output );
				do_action( 'tipsy_action_after_sidebar', 'header' );
				if ( $tipsy_need_columns ) {
					?>
					</div>	<!-- /.columns_wrap -->
					<?php
				}
				if ( ! $tipsy_header_wide ) {
					?>
					</div>	<!-- /.content_wrap -->
					<?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
			<?php do_action( 'tipsy_action_after_sidebar_wrap', 'header' ); ?>
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
