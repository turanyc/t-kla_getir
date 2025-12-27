<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

							do_action( 'tipsy_action_page_content_end_text' );
							
							// Widgets area below the content
							tipsy_create_widgets_area( 'widgets_below_content' );
						
							do_action( 'tipsy_action_page_content_end' );
							?>
						</div>
						<?php
						
						do_action( 'tipsy_action_after_page_content' );

						// Show main sidebar
						get_sidebar();

						do_action( 'tipsy_action_content_wrap_end' );
						?>
					</div>
					<?php

					do_action( 'tipsy_action_after_content_wrap' );

					// Widgets area below the page and related posts below the page
					$tipsy_body_style = tipsy_get_theme_option( 'body_style' );
					$tipsy_widgets_name = tipsy_get_theme_option( 'widgets_below_page', 'hide' );
					$tipsy_show_widgets = ! tipsy_is_off( $tipsy_widgets_name ) && is_active_sidebar( $tipsy_widgets_name );
					$tipsy_show_related = tipsy_is_single() && tipsy_get_theme_option( 'related_position', 'below_content' ) == 'below_page';
					if ( $tipsy_show_widgets || $tipsy_show_related ) {
						if ( 'fullscreen' != $tipsy_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $tipsy_show_related ) {
							do_action( 'tipsy_action_related_posts' );
						}

						// Widgets area below page content
						if ( $tipsy_show_widgets ) {
							tipsy_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $tipsy_body_style ) {
							?>
							</div>
							<?php
						}
					}
					do_action( 'tipsy_action_page_content_wrap_end' );
					?>
			</div>
			<?php
			do_action( 'tipsy_action_after_page_content_wrap' );

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! tipsy_is_singular( 'post' ) && ! tipsy_is_singular( 'attachment' ) ) || ! in_array ( tipsy_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<a id="footer_skip_link_anchor" class="tipsy_skip_link_anchor" href="#"></a>
				<?php

				do_action( 'tipsy_action_before_footer' );

				// Footer
				$tipsy_footer_type = tipsy_get_theme_option( 'footer_type' );
				if ( 'custom' == $tipsy_footer_type && ! tipsy_is_layouts_available() ) {
					$tipsy_footer_type = 'default';
				}
				get_template_part( apply_filters( 'tipsy_filter_get_template_part', "templates/footer-" . sanitize_file_name( $tipsy_footer_type ) ) );

				do_action( 'tipsy_action_after_footer' );

			}
			?>

			<?php do_action( 'tipsy_action_page_wrap_end' ); ?>

		</div>

		<?php do_action( 'tipsy_action_after_page_wrap' ); ?>

	</div>

	<?php do_action( 'tipsy_action_after_body' ); ?>

	<?php wp_footer(); ?>

</body>
</html>