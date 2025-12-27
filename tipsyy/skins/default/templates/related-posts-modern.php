<?php
/**
 * The template 'Style 1' to displaying related posts
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

$tipsy_link        = get_permalink();
$tipsy_post_format = get_post_format();
$tipsy_post_format = empty( $tipsy_post_format ) ? 'standard' : str_replace( 'post-format-', '', $tipsy_post_format );
?><div id="post-<?php the_ID(); ?>" <?php post_class( 'related_item post_format_' . esc_attr( $tipsy_post_format ) ); ?> data-post-id="<?php the_ID(); ?>">
	<?php
	tipsy_show_post_featured(
		array(
			'thumb_size'    => apply_filters( 'tipsy_filter_related_thumb_size', tipsy_get_thumb_size( (int) tipsy_get_theme_option( 'related_posts' ) == 1 ? 'huge' : 'big' ) ),
			'post_info'     => '<div class="post_header entry-header">'
									. '<div class="post_categories">' . wp_kses( tipsy_get_post_categories( '' ), 'tipsy_kses_content' ) . '</div>'
									. '<h6 class="post_title entry-title"><a href="' . esc_url( $tipsy_link ) . '">'
										. wp_kses_data( '' == get_the_title() ? esc_html__( '- No title -', 'tipsy' ) : get_the_title() )
									. '</a></h6>'
									. ( in_array( get_post_type(), array( 'post', 'attachment' ) )
											? '<div class="post_meta"><a href="' . esc_url( $tipsy_link ) . '" class="post_meta_item post_date">' . wp_kses_data( tipsy_get_date() ) . '</a></div>'
											: '' )
								. '</div>',
		)
	);
	?>
</div>
