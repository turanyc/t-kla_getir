<?php
/**
 * The template to display single post
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

// Full post loading
$full_post_loading          = tipsy_get_value_gp( 'action' ) == 'full_post_loading';

// Prev post loading
$prev_post_loading          = tipsy_get_value_gp( 'action' ) == 'prev_post_loading';
$prev_post_loading_type     = tipsy_get_theme_option( 'posts_navigation_scroll_which_block', 'article' );

// Position of the related posts
$tipsy_related_position   = tipsy_get_theme_option( 'related_position', 'below_content' );

// Type of the prev/next post navigation
$tipsy_posts_navigation   = tipsy_get_theme_option( 'posts_navigation' );
$tipsy_prev_post          = false;
$tipsy_prev_post_same_cat = (int)tipsy_get_theme_option( 'posts_navigation_scroll_same_cat', 1 );

// Rewrite style of the single post if current post loading via AJAX and featured image and title is not in the content
if ( ( $full_post_loading 
		|| 
		( $prev_post_loading && 'article' == $prev_post_loading_type )
	) 
	&& 
	! in_array( tipsy_get_theme_option( 'single_style' ), array( 'style-6' ) )
) {
	tipsy_storage_set_array( 'options_meta', 'single_style', 'style-6' );
}

do_action( 'tipsy_action_prev_post_loading', $prev_post_loading, $prev_post_loading_type );

get_header();

while ( have_posts() ) {

	the_post();

	// Type of the prev/next post navigation
	if ( 'scroll' == $tipsy_posts_navigation ) {
		$tipsy_prev_post = get_previous_post( $tipsy_prev_post_same_cat );  // Get post from same category
		if ( ! $tipsy_prev_post && $tipsy_prev_post_same_cat ) {
			$tipsy_prev_post = get_previous_post( false );                    // Get post from any category
		}
		if ( ! $tipsy_prev_post ) {
			$tipsy_posts_navigation = 'links';
		}
	}

	// Override some theme options to display featured image, title and post meta in the dynamic loaded posts
	if ( $full_post_loading || ( $prev_post_loading && $tipsy_prev_post ) ) {
		tipsy_sc_layouts_showed( 'featured', false );
		tipsy_sc_layouts_showed( 'title', false );
		tipsy_sc_layouts_showed( 'postmeta', false );
	}

	// If related posts should be inside the content
	if ( strpos( $tipsy_related_position, 'inside' ) === 0 ) {
		ob_start();
	}

	// Display post's content
	get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/content', 'single-' . tipsy_get_theme_option( 'single_style' ) ), 'single-' . tipsy_get_theme_option( 'single_style' ) );

	// If related posts should be inside the content
	if ( strpos( $tipsy_related_position, 'inside' ) === 0 ) {
		$tipsy_content = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action( 'tipsy_action_related_posts' );
		$tipsy_related_content = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $tipsy_related_content ) ) {
			$tipsy_related_position_inside = max( 0, min( 9, tipsy_get_theme_option( 'related_position_inside' ) ) );
			if ( 0 == $tipsy_related_position_inside ) {
				$tipsy_related_position_inside = mt_rand( 1, 9 );
			}

			$tipsy_p_number         = 0;
			$tipsy_related_inserted = false;
			$tipsy_in_block         = false;
			$tipsy_content_start    = strpos( $tipsy_content, '<div class="post_content' );
			$tipsy_content_end      = strrpos( $tipsy_content, '</div>' );

			for ( $i = max( 0, $tipsy_content_start ); $i < min( strlen( $tipsy_content ) - 3, $tipsy_content_end ); $i++ ) {
				if ( $tipsy_content[ $i ] != '<' ) {
					continue;
				}
				if ( $tipsy_in_block ) {
					if ( strtolower( substr( $tipsy_content, $i + 1, 12 ) ) == '/blockquote>' ) {
						$tipsy_in_block = false;
						$i += 12;
					}
					continue;
				} else if ( strtolower( substr( $tipsy_content, $i + 1, 10 ) ) == 'blockquote' && in_array( $tipsy_content[ $i + 11 ], array( '>', ' ' ) ) ) {
					$tipsy_in_block = true;
					$i += 11;
					continue;
				} else if ( 'p' == $tipsy_content[ $i + 1 ] && in_array( $tipsy_content[ $i + 2 ], array( '>', ' ' ) ) ) {
					$tipsy_p_number++;
					if ( $tipsy_related_position_inside == $tipsy_p_number ) {
						$tipsy_related_inserted = true;
						$tipsy_content = ( $i > 0 ? substr( $tipsy_content, 0, $i ) : '' )
											. $tipsy_related_content
											. substr( $tipsy_content, $i );
					}
				}
			}
			if ( ! $tipsy_related_inserted ) {
				if ( $tipsy_content_end > 0 ) {
					$tipsy_content = substr( $tipsy_content, 0, $tipsy_content_end ) . $tipsy_related_content . substr( $tipsy_content, $tipsy_content_end );
				} else {
					$tipsy_content .= $tipsy_related_content;
				}
			}
		}

		tipsy_show_layout( $tipsy_content );
	}

	// Comments
	do_action( 'tipsy_action_before_comments' );
	comments_template();
	do_action( 'tipsy_action_after_comments' );

	// Related posts
	if ( 'below_content' == $tipsy_related_position
		&& ( 'scroll' != $tipsy_posts_navigation || (int)tipsy_get_theme_option( 'posts_navigation_scroll_hide_related', 0 ) == 0 )
		&& ( ! $full_post_loading || (int)tipsy_get_theme_option( 'open_full_post_hide_related', 1 ) == 0 )
	) {
		do_action( 'tipsy_action_related_posts' );
	}

	// Post navigation: type 'scroll'
	if ( 'scroll' == $tipsy_posts_navigation && ! $full_post_loading ) {
		?>
		<div class="nav-links-single-scroll"
			data-post-id="<?php echo esc_attr( get_the_ID( $tipsy_prev_post ) ); ?>"
			data-post-link="<?php echo esc_attr( get_permalink( $tipsy_prev_post ) ); ?>"
			data-post-title="<?php the_title_attribute( array( 'post' => $tipsy_prev_post ) ); ?>"
			data-cur-post-link="<?php echo esc_attr( get_permalink() ); ?>"
			data-cur-post-title="<?php the_title_attribute(); ?>"
			<?php do_action( 'tipsy_action_nav_links_single_scroll_data', $tipsy_prev_post ); ?>
		></div>
		<?php
	}
}

get_footer();
