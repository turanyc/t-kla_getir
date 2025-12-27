<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

$tipsy_template_args = get_query_var( 'tipsy_template_args' );
$tipsy_columns = 1;
if ( is_array( $tipsy_template_args ) ) {
	$tipsy_columns    = empty( $tipsy_template_args['columns'] ) ? 1 : max( 1, $tipsy_template_args['columns'] );
	$tipsy_blog_style = array( $tipsy_template_args['type'], $tipsy_columns );
	if ( ! empty( $tipsy_template_args['slider'] ) ) {
		?><div class="slider-slide swiper-slide">
		<?php
	} elseif ( $tipsy_columns > 1 ) {
	    $tipsy_columns_class = tipsy_get_column_class( 1, $tipsy_columns, ! empty( $tipsy_template_args['columns_tablet']) ? $tipsy_template_args['columns_tablet'] : '', ! empty($tipsy_template_args['columns_mobile']) ? $tipsy_template_args['columns_mobile'] : '' );
		?>
		<div class="<?php echo esc_attr( $tipsy_columns_class ); ?>">
		<?php
	}
} else {
	$tipsy_template_args = array();
}
$tipsy_expanded    = ! tipsy_sidebar_present() && tipsy_get_theme_option( 'expand_content' ) == 'expand';
$tipsy_post_format = get_post_format();
$tipsy_post_format = empty( $tipsy_post_format ) ? 'standard' : str_replace( 'post-format-', '', $tipsy_post_format );
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class( 'post_item post_item_container post_layout_excerpt post_format_' . esc_attr( $tipsy_post_format ) );
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
			'thumb_size' => ! empty( $tipsy_template_args['thumb_size'] )
							? $tipsy_template_args['thumb_size']
							: tipsy_get_thumb_size( strpos( tipsy_get_theme_option( 'body_style' ), 'full' ) !== false
								? 'full'
								: ( $tipsy_expanded 
									? 'huge' 
									: 'big' 
									)
								),
		),
		'content-excerpt',
		$tipsy_template_args
	) );

	// Title and post meta
	$tipsy_show_title = get_the_title() != '';
	$tipsy_show_meta  = count( $tipsy_components ) > 0 && ! in_array( $tipsy_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $tipsy_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			if ( apply_filters( 'tipsy_filter_show_blog_title', true, 'excerpt' ) ) {
				do_action( 'tipsy_action_before_post_title' );
				if ( empty( $tipsy_template_args['no_links'] ) ) {
					the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
				} else {
					the_title( '<h3 class="post_title entry-title">', '</h3>' );
				}
				do_action( 'tipsy_action_after_post_title' );
			}
			?>
		</div><!-- .post_header -->
		<?php
	}

	// Post content
	if ( apply_filters( 'tipsy_filter_show_blog_excerpt', empty( $tipsy_template_args['hide_excerpt'] ) && tipsy_get_theme_option( 'excerpt_length' ) > 0, 'excerpt' ) ) {
		?>
		<div class="post_content entry-content">
			<?php

			// Post meta
			if ( apply_filters( 'tipsy_filter_show_blog_meta', $tipsy_show_meta, $tipsy_components, 'excerpt' ) ) {
				if ( count( $tipsy_components ) > 0 ) {
					do_action( 'tipsy_action_before_post_meta' );
					tipsy_show_post_meta(
						apply_filters(
							'tipsy_filter_post_meta_args', array(
								'components' => join( ',', $tipsy_components ),
								'seo'        => false,
								'echo'       => true,
							), 'excerpt', 1
						)
					);
					do_action( 'tipsy_action_after_post_meta' );
				}
			}

			if ( tipsy_get_theme_option( 'blog_content' ) == 'fullpost' ) {
				// Post content area
				?>
				<div class="post_content_inner">
					<?php
					do_action( 'tipsy_action_before_full_post_content' );
					the_content( '' );
					do_action( 'tipsy_action_after_full_post_content' );
					?>
				</div>
				<?php
				// Inner pages
				wp_link_pages(
					array(
						'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'tipsy' ) . '</span>',
						'after'       => '</div>',
						'link_before' => '<span>',
						'link_after'  => '</span>',
						'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'tipsy' ) . ' </span>%',
						'separator'   => '<span class="screen-reader-text">, </span>',
					)
				);
			} else {
				// Post content area
				tipsy_show_post_content( $tipsy_template_args, '<div class="post_content_inner">', '</div>' );
			}

			// More button
			if ( apply_filters( 'tipsy_filter_show_blog_readmore',  ! isset( $tipsy_template_args['more_button'] ) || ! empty( $tipsy_template_args['more_button'] ), 'excerpt' ) ) {
				if ( empty( $tipsy_template_args['no_links'] ) ) {
					do_action( 'tipsy_action_before_post_readmore' );
					if ( tipsy_get_theme_option( 'blog_content' ) != 'fullpost' ) {
						tipsy_show_post_more_link( $tipsy_template_args, '<p>', '</p>' );
					} else {
						tipsy_show_post_comments_link( $tipsy_template_args, '<p>', '</p>' );
					}
					do_action( 'tipsy_action_after_post_readmore' );
				}
			}

			?>
		</div><!-- .entry-content -->
		<?php
	}
	?>
</article>
<?php

if ( is_array( $tipsy_template_args ) ) {
	if ( ! empty( $tipsy_template_args['slider'] ) || $tipsy_columns > 1 ) {
		?>
		</div>
		<?php
	}
}
