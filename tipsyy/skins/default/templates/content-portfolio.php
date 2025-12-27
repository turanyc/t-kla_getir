<?php
/**
 * The Portfolio template to display the content
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

$tipsy_post_format = get_post_format();
$tipsy_post_format = empty( $tipsy_post_format ) ? 'standard' : str_replace( 'post-format-', '', $tipsy_post_format );

?><div class="
<?php
if ( ! empty( $tipsy_template_args['slider'] ) ) {
	echo ' slider-slide swiper-slide';
} else {
	echo ( tipsy_is_blog_style_use_masonry( $tipsy_blog_style[0] ) ? 'masonry_item masonry_item-1_' . esc_attr( $tipsy_columns ) : esc_attr( $tipsy_columns_class ));
}
?>
"><article id="post-<?php the_ID(); ?>" 
	<?php
	post_class(
		'post_item post_item_container post_format_' . esc_attr( $tipsy_post_format )
		. ' post_layout_portfolio'
		. ' post_layout_portfolio_' . esc_attr( $tipsy_columns )
		. ( 'portfolio' != $tipsy_blog_style[0] ? ' ' . esc_attr( $tipsy_blog_style[0] )  . '_' . esc_attr( $tipsy_columns ) : '' )
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

	$tipsy_hover   = ! empty( $tipsy_template_args['hover'] ) && ! tipsy_is_inherit( $tipsy_template_args['hover'] )
								? $tipsy_template_args['hover']
								: tipsy_get_theme_option( 'image_hover' );

	if ( 'dots' == $tipsy_hover ) {
		$tipsy_post_link = empty( $tipsy_template_args['no_links'] )
								? ( ! empty( $tipsy_template_args['link'] )
									? $tipsy_template_args['link']
									: get_permalink()
									)
								: '';
		$tipsy_target    = ! empty( $tipsy_post_link ) && tipsy_is_external_url( $tipsy_post_link )
								? ' target="_blank" rel="nofollow"'
								: '';
	}
	
	// Meta parts
	$tipsy_components = ! empty( $tipsy_template_args['meta_parts'] )
							? ( is_array( $tipsy_template_args['meta_parts'] )
								? $tipsy_template_args['meta_parts']
								: explode( ',', $tipsy_template_args['meta_parts'] )
								)
							: tipsy_array_get_keys_by_value( tipsy_get_theme_option( 'meta_parts' ) );

	// Featured image
	tipsy_show_post_featured( apply_filters( 'tipsy_filter_args_featured',
        array(
			'hover'         => $tipsy_hover,
			'no_links'      => ! empty( $tipsy_template_args['no_links'] ),
			'thumb_size'    => ! empty( $tipsy_template_args['thumb_size'] )
								? $tipsy_template_args['thumb_size']
								: tipsy_get_thumb_size(
									tipsy_is_blog_style_use_masonry( $tipsy_blog_style[0] )
										? (	strpos( tipsy_get_theme_option( 'body_style' ), 'full' ) !== false || $tipsy_columns < 3
											? 'masonry-big'
											: 'masonry'
											)
										: (	strpos( tipsy_get_theme_option( 'body_style' ), 'full' ) !== false || $tipsy_columns < 3
											? 'square'
											: 'square'
											)
								),
			'thumb_bg' => tipsy_is_blog_style_use_masonry( $tipsy_blog_style[0] ) ? false : true,
			'show_no_image' => true,
			'meta_parts'    => $tipsy_components,
			'class'         => 'dots' == $tipsy_hover ? 'hover_with_info' : '',
			'post_info'     => 'dots' == $tipsy_hover
										? '<div class="post_info"><h5 class="post_title">'
											. ( ! empty( $tipsy_post_link )
												? '<a href="' . esc_url( $tipsy_post_link ) . '"' . ( ! empty( $target ) ? $target : '' ) . '>'
												: ''
												)
												. esc_html( get_the_title() ) 
											. ( ! empty( $tipsy_post_link )
												? '</a>'
												: ''
												)
											. '</h5></div>'
										: '',
            'thumb_ratio'   => 'info' == $tipsy_hover ?  '100:102' : '',
        ),
        'content-portfolio',
        $tipsy_template_args
    ) );
	?>
</article></div><?php
// Need opening PHP-tag above, because <article> is a inline-block element (used as column)!