<?php
/**
 * The custom template to display the content
 *
 * Used for index/archive/search.
 *
 * @package TIPSY
 * @since TIPSY 1.0.50
 */

$tipsy_template_args = get_query_var( 'tipsy_template_args' );
if ( is_array( $tipsy_template_args ) ) {
	$tipsy_columns    = empty( $tipsy_template_args['columns'] ) ? 2 : max( 1, $tipsy_template_args['columns'] );
	$tipsy_blog_style = array( $tipsy_template_args['type'], $tipsy_columns );
} else {
	$tipsy_template_args = array();
	$tipsy_blog_style = explode( '_', tipsy_get_theme_option( 'blog_style' ) );
	$tipsy_columns    = empty( $tipsy_blog_style[1] ) ? 2 : max( 1, $tipsy_blog_style[1] );
}
$tipsy_blog_id       = tipsy_get_custom_blog_id( join( '_', $tipsy_blog_style ) );
$tipsy_blog_style[0] = str_replace( 'blog-custom-', '', $tipsy_blog_style[0] );
$tipsy_expanded      = ! tipsy_sidebar_present() && tipsy_get_theme_option( 'expand_content' ) == 'expand';
$tipsy_components    = ! empty( $tipsy_template_args['meta_parts'] )
							? ( is_array( $tipsy_template_args['meta_parts'] )
								? join( ',', $tipsy_template_args['meta_parts'] )
								: $tipsy_template_args['meta_parts']
								)
							: tipsy_array_get_keys_by_value( tipsy_get_theme_option( 'meta_parts' ) );
$tipsy_post_format   = get_post_format();
$tipsy_post_format   = empty( $tipsy_post_format ) ? 'standard' : str_replace( 'post-format-', '', $tipsy_post_format );

$tipsy_blog_meta     = tipsy_get_custom_layout_meta( $tipsy_blog_id );
$tipsy_custom_style  = ! empty( $tipsy_blog_meta['scripts_required'] ) ? $tipsy_blog_meta['scripts_required'] : 'none';

if ( ! empty( $tipsy_template_args['slider'] ) || $tipsy_columns > 1 || ! tipsy_is_off( $tipsy_custom_style ) ) {
	?><div class="
		<?php
		if ( ! empty( $tipsy_template_args['slider'] ) ) {
			echo 'slider-slide swiper-slide';
		} else {
			echo esc_attr( ( tipsy_is_off( $tipsy_custom_style ) ? 'column' : sprintf( '%1$s_item %1$s_item', $tipsy_custom_style ) ) . "-1_{$tipsy_columns}" );
		}
		?>
	">
	<?php
}
?>
<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>"
	<?php
	post_class(
			'post_item post_item_container post_format_' . esc_attr( $tipsy_post_format )
					. ' post_layout_custom post_layout_custom_' . esc_attr( $tipsy_columns )
					. ' post_layout_' . esc_attr( $tipsy_blog_style[0] )
					. ' post_layout_' . esc_attr( $tipsy_blog_style[0] ) . '_' . esc_attr( $tipsy_columns )
					. ( ! tipsy_is_off( $tipsy_custom_style )
						? ' post_layout_' . esc_attr( $tipsy_custom_style )
							. ' post_layout_' . esc_attr( $tipsy_custom_style ) . '_' . esc_attr( $tipsy_columns )
						: ''
						)
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
	// Custom layout
	do_action( 'tipsy_action_show_layout', $tipsy_blog_id, get_the_ID() );
	?>
</article><?php
if ( ! empty( $tipsy_template_args['slider'] ) || $tipsy_columns > 1 || ! tipsy_is_off( $tipsy_custom_style ) ) {
	?></div><?php
	// Need opening PHP-tag above just after </div>, because <div> is a inline-block element (used as column)!
}
