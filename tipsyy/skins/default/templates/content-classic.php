<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

$tipsy_template_args = get_query_var( 'tipsy_template_args' );

if ( is_array( $tipsy_template_args ) ) {
	$tipsy_columns    = empty( $tipsy_template_args['columns'] ) ? 2 : max( 1, $tipsy_template_args['columns'] );
	$tipsy_blog_style = array( $tipsy_template_args['type'], $tipsy_columns );
    $tipsy_columns_class = tipsy_get_column_class( 1, $tipsy_columns, ! empty( $tipsy_template_args['columns_tablet']) ? $tipsy_template_args['columns_tablet'] : '', ! empty($tipsy_template_args['columns_mobile']) ? $tipsy_template_args['columns_mobile'] : '' );
} else {
	$tipsy_template_args = array();
	$tipsy_blog_style = explode( '_', tipsy_get_theme_option( 'blog_style' ) );
	$tipsy_columns    = empty( $tipsy_blog_style[1] ) ? 2 : max( 1, $tipsy_blog_style[1] );
    $tipsy_columns_class = tipsy_get_column_class( 1, $tipsy_columns );
}
$tipsy_expanded   = ! tipsy_sidebar_present() && tipsy_get_theme_option( 'expand_content' ) == 'expand';

$tipsy_post_format = get_post_format();
$tipsy_post_format = empty( $tipsy_post_format ) ? 'standard' : str_replace( 'post-format-', '', $tipsy_post_format );

?><div class="<?php
	if ( ! empty( $tipsy_template_args['slider'] ) ) {
		echo ' slider-slide swiper-slide';
	} else {
		echo ( tipsy_is_blog_style_use_masonry( $tipsy_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $tipsy_columns ) : esc_attr( $tipsy_columns_class ) );
	}
?>"><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $tipsy_post_format )
				. ' post_layout_classic post_layout_classic_' . esc_attr( $tipsy_columns )
				. ' post_layout_' . esc_attr( $tipsy_blog_style[0] )
				. ' post_layout_' . esc_attr( $tipsy_blog_style[0] ) . '_' . esc_attr( $tipsy_columns )
	);
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
								: explode( ',', $tipsy_template_args['meta_parts'] )
								)
							: tipsy_array_get_keys_by_value( tipsy_get_theme_option( 'meta_parts' ) );

	tipsy_show_post_featured( apply_filters( 'tipsy_filter_args_featured',
		array(
			'thumb_size' => ! empty( $tipsy_template_args['thumb_size'] )
				? $tipsy_template_args['thumb_size']
				: tipsy_get_thumb_size(
				'classic' == $tipsy_blog_style[0]
						? ( strpos( tipsy_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $tipsy_columns > 2 ? 'big' : 'huge' )
								: ( $tipsy_columns > 2
									? ( $tipsy_expanded ? 'square' : 'square' )
									: ($tipsy_columns > 1 ? 'square' : ( $tipsy_expanded ? 'huge' : 'big' ))
									)
							)
						: ( strpos( tipsy_get_theme_option( 'body_style' ), 'full' ) !== false
								? ( $tipsy_columns > 2 ? 'masonry-big' : 'full' )
								: ($tipsy_columns === 1 ? ( $tipsy_expanded ? 'huge' : 'big' ) : ( $tipsy_columns <= 2 && $tipsy_expanded ? 'masonry-big' : 'masonry' ))
							)
			),
			'hover'      => $tipsy_hover,
			'meta_parts' => $tipsy_components,
			'no_links'   => ! empty( $tipsy_template_args['no_links'] ),
        ),
        'content-classic',
        $tipsy_template_args
    ) );

	// Title and post meta
	$tipsy_show_title = get_the_title() != '';
	$tipsy_show_meta  = count( $tipsy_components ) > 0 && ! in_array( $tipsy_hover, array( 'border', 'pull', 'slide', 'fade', 'info' ) );

	if ( $tipsy_show_title ) {
		?>
		<div class="post_header entry-header">
			<?php

			// Post meta
			if ( apply_filters( 'tipsy_filter_show_blog_meta', $tipsy_show_meta, $tipsy_components, 'classic' ) ) {
				if ( count( $tipsy_components ) > 0 ) {
					do_action( 'tipsy_action_before_post_meta' );
					tipsy_show_post_meta(
						apply_filters(
							'tipsy_filter_post_meta_args', array(
							'components' => join( ',', $tipsy_components ),
							'seo'        => false,
							'echo'       => true,
						), $tipsy_blog_style[0], $tipsy_columns
						)
					);
					do_action( 'tipsy_action_after_post_meta' );
				}
			}

			// Post title
			if ( apply_filters( 'tipsy_filter_show_blog_title', true, 'classic' ) ) {
				do_action( 'tipsy_action_before_post_title' );
				if ( empty( $tipsy_template_args['no_links'] ) ) {
					the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
				} else {
					the_title( '<h4 class="post_title entry-title">', '</h4>' );
				}
				do_action( 'tipsy_action_after_post_title' );
			}

			if( !in_array( $tipsy_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
				// More button
				if ( apply_filters( 'tipsy_filter_show_blog_readmore', ! $tipsy_show_title || ! empty( $tipsy_template_args['more_button'] ), 'classic' ) ) {
					if ( empty( $tipsy_template_args['no_links'] ) ) {
						do_action( 'tipsy_action_before_post_readmore' );
						tipsy_show_post_more_link( $tipsy_template_args, '<div class="more-wrap">', '</div>' );
						do_action( 'tipsy_action_after_post_readmore' );
					}
				}
			}
			?>
		</div><!-- .entry-header -->
		<?php
	}

	// Post content
	if( in_array( $tipsy_post_format, array( 'quote', 'aside', 'link', 'status' ) ) ) {
		ob_start();
		if (apply_filters('tipsy_filter_show_blog_excerpt', empty($tipsy_template_args['hide_excerpt']) && tipsy_get_theme_option('excerpt_length') > 0, 'classic')) {
			tipsy_show_post_content($tipsy_template_args, '<div class="post_content_inner">', '</div>');
		}
		// More button
		if(! empty( $tipsy_template_args['more_button'] )) {
			if ( empty( $tipsy_template_args['no_links'] ) ) {
				do_action( 'tipsy_action_before_post_readmore' );
				tipsy_show_post_more_link( $tipsy_template_args, '<div class="more-wrap">', '</div>' );
				do_action( 'tipsy_action_after_post_readmore' );
			}
		}
		$tipsy_content = ob_get_contents();
		ob_end_clean();
		tipsy_show_layout($tipsy_content, '<div class="post_content entry-content">', '</div><!-- .entry-content -->');
	}
	?>

</article></div><?php
// Need opening PHP-tag above, because <div> is a inline-block element (used as column)!
