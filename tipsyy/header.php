<?php
/**
 * The Header: Logo and main menu
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js<?php
	// Class scheme_xxx need in the <html> as context for the <body>!
	echo ' scheme_' . esc_attr( tipsy_get_theme_option( 'color_scheme' ) );
?>">

<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	if ( function_exists( 'wp_body_open' ) ) {
		wp_body_open();
	} else {
		do_action( 'wp_body_open' );
	}
	do_action( 'tipsy_action_before_body' );
	?>

	<div class="<?php echo esc_attr( apply_filters( 'tipsy_filter_body_wrap_class', 'body_wrap' ) ); ?>" <?php do_action('tipsy_action_body_wrap_attributes'); ?>>

		<?php do_action( 'tipsy_action_before_page_wrap' ); ?>

		<div class="<?php echo esc_attr( apply_filters( 'tipsy_filter_page_wrap_class', 'page_wrap' ) ); ?>" <?php do_action('tipsy_action_page_wrap_attributes'); ?>>

			<?php do_action( 'tipsy_action_page_wrap_start' ); ?>

			<?php
			$tipsy_full_post_loading = ( tipsy_is_singular( 'post' ) || tipsy_is_singular( 'attachment' ) ) && tipsy_get_value_gp( 'action' ) == 'full_post_loading';
			$tipsy_prev_post_loading = ( tipsy_is_singular( 'post' ) || tipsy_is_singular( 'attachment' ) ) && tipsy_get_value_gp( 'action' ) == 'prev_post_loading';

			// Don't display the header elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ! $tipsy_full_post_loading && ! $tipsy_prev_post_loading ) {

				// Short links to fast access to the content, sidebar and footer from the keyboard
				?>
				<a class="tipsy_skip_link skip_to_content_link" href="#content_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'tipsy_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to content", 'tipsy' ); ?></a>
				<?php if ( tipsy_sidebar_present() ) { ?>
				<a class="tipsy_skip_link skip_to_sidebar_link" href="#sidebar_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'tipsy_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to sidebar", 'tipsy' ); ?></a>
				<?php } ?>
				<a class="tipsy_skip_link skip_to_footer_link" href="#footer_skip_link_anchor" tabindex="<?php echo esc_attr( apply_filters( 'tipsy_filter_skip_links_tabindex', 1 ) ); ?>"><?php esc_html_e( "Skip to footer", 'tipsy' ); ?></a>

				<?php
				do_action( 'tipsy_action_before_header' );

				// Header
				$tipsy_header_type = tipsy_get_theme_option( 'header_type' );
				if ( 'custom' == $tipsy_header_type && ! tipsy_is_layouts_available() ) {
					$tipsy_header_type = 'default';
				}
				get_template_part( apply_filters( 'tipsy_filter_get_template_part', "templates/header-" . sanitize_file_name( $tipsy_header_type ) ) );

				// Side menu
				if ( in_array( tipsy_get_theme_option( 'menu_side', 'none' ), array( 'left', 'right' ) ) ) {
					get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/header-navi-side' ) );
				}

				// Mobile menu
				if ( apply_filters( 'tipsy_filter_use_navi_mobile', true ) ) {
					get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/header-navi-mobile' ) );
				}

				do_action( 'tipsy_action_after_header' );

			}
			?>

			<?php do_action( 'tipsy_action_before_page_content_wrap' ); ?>

			<div class="page_content_wrap<?php
				if ( tipsy_is_off( tipsy_get_theme_option( 'remove_margins' ) ) ) {
					if ( empty( $tipsy_header_type ) ) {
						$tipsy_header_type = tipsy_get_theme_option( 'header_type' );
					}
					if ( 'custom' == $tipsy_header_type && tipsy_is_layouts_available() ) {
						$tipsy_header_id = tipsy_get_custom_header_id();
						if ( $tipsy_header_id > 0 ) {
							$tipsy_header_meta = tipsy_get_custom_layout_meta( $tipsy_header_id );
							if ( ! empty( $tipsy_header_meta['margin'] ) ) {
								?> page_content_wrap_custom_header_margin<?php
							}
						}
					}
					$tipsy_footer_type = tipsy_get_theme_option( 'footer_type' );
					if ( 'custom' == $tipsy_footer_type && tipsy_is_layouts_available() ) {
						$tipsy_footer_id = tipsy_get_custom_footer_id();
						if ( $tipsy_footer_id ) {
							$tipsy_footer_meta = tipsy_get_custom_layout_meta( $tipsy_footer_id );
							if ( ! empty( $tipsy_footer_meta['margin'] ) ) {
								?> page_content_wrap_custom_footer_margin<?php
							}
						}
					}
				}
				do_action( 'tipsy_action_page_content_wrap_class', $tipsy_prev_post_loading );
				?>"<?php
				if ( apply_filters( 'tipsy_filter_is_prev_post_loading', $tipsy_prev_post_loading ) ) {
					?> data-single-style="<?php echo esc_attr( tipsy_get_theme_option( 'single_style' ) ); ?>"<?php
				}
				do_action( 'tipsy_action_page_content_wrap_data', $tipsy_prev_post_loading );
			?>>
				<?php
				do_action( 'tipsy_action_page_content_wrap', $tipsy_full_post_loading || $tipsy_prev_post_loading );

				// Single posts banner
				if ( apply_filters( 'tipsy_filter_single_post_header', tipsy_is_singular( 'post' ) || tipsy_is_singular( 'attachment' ) ) ) {
					if ( $tipsy_prev_post_loading ) {
						if ( tipsy_get_theme_option( 'posts_navigation_scroll_which_block', 'article' ) != 'article' ) {
							do_action( 'tipsy_action_between_posts' );
						}
					}
					// Single post thumbnail and title
					$tipsy_path = apply_filters( 'tipsy_filter_get_template_part', 'templates/single-styles/' . tipsy_get_theme_option( 'single_style' ) );
					if ( tipsy_get_file_dir( $tipsy_path . '.php' ) != '' ) {
						get_template_part( $tipsy_path );
					}
				}

				// Widgets area above page
				$tipsy_body_style   = tipsy_get_theme_option( 'body_style' );
				$tipsy_widgets_name = tipsy_get_theme_option( 'widgets_above_page', 'hide' );
				$tipsy_show_widgets = ! tipsy_is_off( $tipsy_widgets_name ) && is_active_sidebar( $tipsy_widgets_name );
				if ( $tipsy_show_widgets ) {
					if ( 'fullscreen' != $tipsy_body_style ) {
						?>
						<div class="content_wrap">
							<?php
					}
					tipsy_create_widgets_area( 'widgets_above_page' );
					if ( 'fullscreen' != $tipsy_body_style ) {
						?>
						</div>
						<?php
					}
				}

				// Content area
				do_action( 'tipsy_action_before_content_wrap' );
				?>
				<div class="content_wrap<?php echo 'fullscreen' == $tipsy_body_style ? '_fullscreen' : ''; ?>">

					<?php do_action( 'tipsy_action_content_wrap_start' ); ?>

					<div class="content">
						<?php
						do_action( 'tipsy_action_page_content_start' );

						// Skip link anchor to fast access to the content from keyboard
						?>
						<a id="content_skip_link_anchor" class="tipsy_skip_link_anchor" href="#"></a>
						<?php
						// Single posts banner between prev/next posts
						if ( ( tipsy_is_singular( 'post' ) || tipsy_is_singular( 'attachment' ) )
							&& $tipsy_prev_post_loading 
							&& tipsy_get_theme_option( 'posts_navigation_scroll_which_block', 'article' ) == 'article'
						) {
							do_action( 'tipsy_action_between_posts' );
						}

						// Widgets area above content
						tipsy_create_widgets_area( 'widgets_above_content' );

						do_action( 'tipsy_action_page_content_start_text' );
