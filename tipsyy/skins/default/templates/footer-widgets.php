<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package TIPSY
 * @since TIPSY 1.0.10
 */

// Footer sidebar
$tipsy_footer_name    = tipsy_get_theme_option( 'footer_widgets' );
$tipsy_footer_present = ! tipsy_is_off( $tipsy_footer_name ) && is_active_sidebar( $tipsy_footer_name );
if ( $tipsy_footer_present ) {
	tipsy_storage_set( 'current_sidebar', 'footer' );
	$tipsy_footer_wide = tipsy_get_theme_option( 'footer_wide' );
	ob_start();
	if ( is_active_sidebar( $tipsy_footer_name ) ) {
		dynamic_sidebar( $tipsy_footer_name );
	}
	$tipsy_out = trim( ob_get_contents() );
	ob_end_clean();
	if ( ! empty( $tipsy_out ) ) {
		$tipsy_out          = preg_replace( "/<\\/aside>[\r\n\s]*<aside/", '</aside><aside', $tipsy_out );
		$tipsy_need_columns = true;   //or check: strpos($tipsy_out, 'columns_wrap')===false;
		if ( $tipsy_need_columns ) {
			$tipsy_columns = max( 0, (int) tipsy_get_theme_option( 'footer_columns' ) );			
			if ( 0 == $tipsy_columns ) {
				$tipsy_columns = min( 4, max( 1, tipsy_tags_count( $tipsy_out, 'aside' ) ) );
			}
			if ( $tipsy_columns > 1 ) {
				$tipsy_out = preg_replace( '/<aside([^>]*)class="widget/', '<aside$1class="column-1_' . esc_attr( $tipsy_columns ) . ' widget', $tipsy_out );
			} else {
				$tipsy_need_columns = false;
			}
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo ! empty( $tipsy_footer_wide ) ? ' footer_fullwidth' : ''; ?> sc_layouts_row sc_layouts_row_type_normal">
			<?php do_action( 'tipsy_action_before_sidebar_wrap', 'footer' ); ?>
			<div class="footer_widgets_inner widget_area_inner">
				<?php
				if ( ! $tipsy_footer_wide ) {
					?>
					<div class="content_wrap">
					<?php
				}
				if ( $tipsy_need_columns ) {
					?>
					<div class="columns_wrap">
					<?php
				}
				do_action( 'tipsy_action_before_sidebar', 'footer' );
				tipsy_show_layout( $tipsy_out );
				do_action( 'tipsy_action_after_sidebar', 'footer' );
				if ( $tipsy_need_columns ) {
					?>
					</div><!-- /.columns_wrap -->
					<?php
				}
				if ( ! $tipsy_footer_wide ) {
					?>
					</div><!-- /.content_wrap -->
					<?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
			<?php do_action( 'tipsy_action_after_sidebar_wrap', 'footer' ); ?>
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
