<?php
/**
 * 'Band' template to display the content
 *
 * Used for index/archive/search.
 *
 * @package TIPSY
 * @since TIPSY 1.71.0
 */

$tipsy_template_args = get_query_var( 'tipsy_template_args' );
if ( ! is_array( $tipsy_template_args ) ) {
	$tipsy_template_args = array(
								'type'    => 'band',
								'columns' => 1
								);
}

$tipsy_columns       = 1;

$tipsy_expanded      = ! tipsy_sidebar_present() && tipsy_get_theme_option( 'expand_content' ) == 'expand';

$tipsy_post_format   = get_post_format();
$tipsy_post_format   = empty( $tipsy_post_format ) ? 'standard' : str_replace( 'post-format-', '', $tipsy_post_format );

if ( is_array( $tipsy_template_args ) ) {
	$tipsy_columns    = empty( $tipsy_template_args['columns'] ) ? 1 : max( 1, $tipsy_template_args['columns'] );
	$tipsy_blog_style = array( $tipsy_template_args['type'], $tipsy_columns );
	if ( ! empty( $tipsy_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $tipsy_columns > 1 ) {
	    $tipsy_columns_class = tipsy_get_column_class( 1, $tipsy_columns, ! empty( $tipsy_template_args['columns_tablet']) ? $tipsy_template_args['columns_tablet'] : '', ! empty($tipsy_template_args['columns_mobile']) ? $tipsy_template_args['columns_mobile'] : '' );
				?><div class="<?php echo esc_attr( $tipsy_columns_class ); ?>"><?php
	}
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_band post_format_' . esc_attr( $tipsy_post_format ) );
	tipsy_add_blog_animation( $tipsy_template_args );
	?>
>
	<?php

	// Sticky label
	if ( is_sticky() && ! is_paged() ) {
		?>
		<span class="post_label label_sticky"></span>
		<?php
	}

	// Featured image
	$tipsy_hover      = ! empty( $tipsy_template_args['hover'] ) && ! tipsy_is_inherit( $tipsy_template_args['hover'] )
							? $tipsy_template_args['hover']
							: tipsy_get_theme_option( 'image_hover' );
	$tipsy_components = ! empty( $tipsy_template_args['meta_parts'] )
							? ( is_array( $tipsy_template_args['meta_parts'] )
								? $tipsy_template_args['meta_parts']
								: array_map( 'trim', explode( ',', $tipsy_template_args['meta_parts'] ) )
								)
							: tipsy_array_get_keys_by_value( tipsy_get_theme_option( 'meta_parts' ) );
	tipsy_show_post_featured( apply_filters( 'tipsy_filter_args_featured',
		array(
			'no_links'   => ! empty( $tipsy_template_args['no_links'] ),
			'hover'      => $tipsy_hover,
			'meta_parts' => $tipsy_components,
			'thumb_bg'   => true,
			'thumb_ratio'   => '1:1',
			'thumb_size' => ! empty( $tipsy_template_args['thumb_size'] )
								? $tipsy_template_args['thumb_size']
								: tipsy_get_thumb_size( 
								in_array( $tipsy_post_format, array( 'gallery', 'audio', 'video' ) )
									? ( strpos( tipsy_get_theme_option( 'body_style' ), 'full' ) !== false
										? 'full'
										: ( $tipsy_expanded 
											? 'big' 
											: 'medium-square'
											)
										)
									: 'masonry-big'
								)
		),
		'content-band',
		$tipsy_template_args
	) );

	?><div class="post_content_wrap"><?php

		// Title and post meta
		$tipsy_show_title = get_the_title() != '';
		$tipsy_show_meta  = count( $tipsy_components ) > 0 && ! in_array( $tipsy_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );
		if ( $tipsy_show_title ) {
			?>
			<div class="post_header entry-header">
				<?php
				// Categories
				if ( apply_filters( 'tipsy_filter_show_blog_categories', $tipsy_show_meta && in_array( 'categories', $tipsy_components ), array( 'categories' ), 'band' ) ) {
					do_action( 'tipsy_action_before_post_category' );
					?>
					<div class="post_category">
						<?php
						tipsy_show_post_meta( apply_filters(
															'tipsy_filter_post_meta_args',
															array(
																'components' => 'categories',
																'seo'        => false,
																'echo'       => true,
																'cat_sep'    => false,
																),
															'hover_' . $tipsy_hover, 1
															)
											);
						?>
					</div>
					<?php
					$tipsy_components = tipsy_array_delete_by_value( $tipsy_components, 'categories' );
					do_action( 'tipsy_action_after_post_category' );
				}
				// Post title
				if ( apply_filters( 'tipsy_filter_show_blog_title', true, 'band' ) ) {
					do_action( 'tipsy_action_before_post_title' );
					if ( empty( $tipsy_template_args['no_links'] ) ) {
						the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
					} else {
						the_title( '<h4 class="post_title entry-title">', '</h4>' );
					}
					do_action( 'tipsy_action_after_post_title' );
				}
				?>
			</div><!-- .post_header -->
			<?php
		}

		// Post content
		if ( ! isset( $tipsy_template_args['excerpt_length'] ) && ! in_array( $tipsy_post_format, array( 'gallery', 'audio', 'video' ) ) ) {
			$tipsy_template_args['excerpt_length'] = 13;
		}
		if ( apply_filters( 'tipsy_filter_show_blog_excerpt', empty( $tipsy_template_args['hide_excerpt'] ) && tipsy_get_theme_option( 'excerpt_length' ) > 0, 'band' ) ) {
			?>
			<div class="post_content entry-content">
				<?php
				// Post content area
				tipsy_show_post_content( $tipsy_template_args, '<div class="post_content_inner">', '</div>' );
				?>
			</div><!-- .entry-content -->
			<?php
		}
		// Post meta
		if ( apply_filters( 'tipsy_filter_show_blog_meta', $tipsy_show_meta, $tipsy_components, 'band' ) ) {
			if ( count( $tipsy_components ) > 0 ) {
				do_action( 'tipsy_action_before_post_meta' );
				tipsy_show_post_meta(
					apply_filters(
						'tipsy_filter_post_meta_args', array(
							'components' => join( ',', $tipsy_components ),
							'seo'        => false,
							'echo'       => true,
						), 'band', 1
					)
				);
				do_action( 'tipsy_action_after_post_meta' );
			}
		}
		// More button
		if ( apply_filters( 'tipsy_filter_show_blog_readmore', ! $tipsy_show_title || ! empty( $tipsy_template_args['more_button'] ), 'band' ) ) {
			if ( empty( $tipsy_template_args['no_links'] ) ) {
				do_action( 'tipsy_action_before_post_readmore' );
				tipsy_show_post_more_link( $tipsy_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'tipsy_action_after_post_readmore' );
			}
		}
		?>
	</div>
</article>
<?php

if ( is_array( $tipsy_template_args ) ) {
	if ( ! empty( $tipsy_template_args['slider'] ) || $tipsy_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
