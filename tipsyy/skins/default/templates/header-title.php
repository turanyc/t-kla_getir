<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

// Page (category, tag, archive, author) title

if ( tipsy_need_page_title() ) {
	tipsy_sc_layouts_showed( 'title', true );
	tipsy_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								tipsy_show_post_meta(
									apply_filters(
										'tipsy_filter_post_meta_args', array(
											'components' => join( ',', tipsy_array_get_keys_by_value( tipsy_get_theme_option( 'meta_parts' ) ) ),
											'counters'   => join( ',', tipsy_array_get_keys_by_value( tipsy_get_theme_option( 'counters' ) ) ),
											'seo'        => tipsy_is_on( tipsy_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$tipsy_blog_title           = tipsy_get_blog_title();
							$tipsy_blog_title_text      = '';
							$tipsy_blog_title_class     = '';
							$tipsy_blog_title_link      = '';
							$tipsy_blog_title_link_text = '';
							if ( is_array( $tipsy_blog_title ) ) {
								$tipsy_blog_title_text      = $tipsy_blog_title['text'];
								$tipsy_blog_title_class     = ! empty( $tipsy_blog_title['class'] ) ? ' ' . $tipsy_blog_title['class'] : '';
								$tipsy_blog_title_link      = ! empty( $tipsy_blog_title['link'] ) ? $tipsy_blog_title['link'] : '';
								$tipsy_blog_title_link_text = ! empty( $tipsy_blog_title['link_text'] ) ? $tipsy_blog_title['link_text'] : '';
							} else {
								$tipsy_blog_title_text = $tipsy_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $tipsy_blog_title_class ); ?>">
								<?php
								$tipsy_top_icon = tipsy_get_term_image_small();
								if ( ! empty( $tipsy_top_icon ) ) {
									$tipsy_attr = tipsy_getimagesize( $tipsy_top_icon );
									?>
									<img src="<?php echo esc_url( $tipsy_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'tipsy' ); ?>"
										<?php
										if ( ! empty( $tipsy_attr[3] ) ) {
											tipsy_show_layout( $tipsy_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $tipsy_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $tipsy_blog_title_link ) && ! empty( $tipsy_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $tipsy_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $tipsy_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'tipsy_action_breadcrumbs' );
						$tipsy_breadcrumbs = ob_get_contents();
						ob_end_clean();
						tipsy_show_layout( $tipsy_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
