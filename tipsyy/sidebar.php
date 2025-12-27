<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

if ( tipsy_sidebar_present() ) {
	
	$tipsy_sidebar_type = tipsy_get_theme_option( 'sidebar_type' );
	if ( 'custom' == $tipsy_sidebar_type && ! tipsy_is_layouts_available() ) {
		$tipsy_sidebar_type = 'default';
	}
	
	// Catch output to the buffer
	ob_start();
	if ( 'default' == $tipsy_sidebar_type ) {
		// Default sidebar with widgets
		$tipsy_sidebar_name = tipsy_get_theme_option( 'sidebar_widgets' );
		tipsy_storage_set( 'current_sidebar', 'sidebar' );
		if ( is_active_sidebar( $tipsy_sidebar_name ) ) {
			dynamic_sidebar( $tipsy_sidebar_name );
		}
	} else {
		// Custom sidebar from Layouts Builder
		$tipsy_sidebar_id = tipsy_get_custom_sidebar_id();
		do_action( 'tipsy_action_show_layout', $tipsy_sidebar_id );
	}
	$tipsy_out = trim( ob_get_contents() );
	ob_end_clean();
	
	// If any html is present - display it
	if ( ! empty( $tipsy_out ) ) {
		$tipsy_sidebar_position    = tipsy_get_theme_option( 'sidebar_position' );
		$tipsy_sidebar_position_ss = tipsy_get_theme_option( 'sidebar_position_ss', 'below' );
		?>
		<div class="sidebar widget_area
			<?php
			echo ' ' . esc_attr( $tipsy_sidebar_position );
			echo ' sidebar_' . esc_attr( $tipsy_sidebar_position_ss );
			echo ' sidebar_' . esc_attr( $tipsy_sidebar_type );

			$tipsy_sidebar_scheme = apply_filters( 'tipsy_filter_sidebar_scheme', tipsy_get_theme_option( 'sidebar_scheme', 'inherit' ) );
			if ( ! empty( $tipsy_sidebar_scheme ) && ! tipsy_is_inherit( $tipsy_sidebar_scheme ) && 'custom' != $tipsy_sidebar_type ) {
				echo ' scheme_' . esc_attr( $tipsy_sidebar_scheme );
			}
			?>
		" role="complementary">
			<?php

			// Skip link anchor to fast access to the sidebar from keyboard
			?>
			<a id="sidebar_skip_link_anchor" class="tipsy_skip_link_anchor" href="#"></a>
			<?php

			do_action( 'tipsy_action_before_sidebar_wrap', 'sidebar' );

			// Button to show/hide sidebar on mobile
			if ( in_array( $tipsy_sidebar_position_ss, array( 'above', 'float' ) ) ) {
				$tipsy_title = apply_filters( 'tipsy_filter_sidebar_control_title', 'float' == $tipsy_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'tipsy' ) : '' );
				$tipsy_text  = apply_filters( 'tipsy_filter_sidebar_control_text', 'above' == $tipsy_sidebar_position_ss ? esc_html__( 'Show Sidebar', 'tipsy' ) : '' );
				?>
				<a href="#" class="sidebar_control" title="<?php echo esc_attr( $tipsy_title ); ?>"><?php echo esc_html( $tipsy_text ); ?></a>
				<?php
			}
			?>
			<div class="sidebar_inner">
				<?php
				do_action( 'tipsy_action_before_sidebar', 'sidebar' );
				tipsy_show_layout( preg_replace( "/<\/aside>[\r\n\s]*<aside/", '</aside><aside', $tipsy_out ) );
				do_action( 'tipsy_action_after_sidebar', 'sidebar' );
				?>
			</div>
			<?php

			do_action( 'tipsy_action_after_sidebar_wrap', 'sidebar' );

			?>
		</div>
		<div class="clearfix"></div>
		<?php
	}
}
