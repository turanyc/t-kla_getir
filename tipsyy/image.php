<?php
/**
 * The template to display the attachment
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */


get_header();

while ( have_posts() ) {
	the_post();

	// Display post's content
	get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/content', 'single-' . tipsy_get_theme_option( 'single_style' ) ), 'single-' . tipsy_get_theme_option( 'single_style' ) );

	// Parent post navigation.
	$tipsy_posts_navigation = tipsy_get_theme_option( 'posts_navigation' );
	if ( 'links' == $tipsy_posts_navigation ) {
		?>
		<div class="nav-links-single<?php
			if ( ! tipsy_is_off( tipsy_get_theme_option( 'posts_navigation_fixed', 0 ) ) ) {
				echo ' nav-links-fixed fixed';
			}
		?>">
			<?php
			the_post_navigation( apply_filters( 'tipsy_filter_post_navigation_args', array(
					'prev_text' => '<span class="nav-arrow"></span>'
						. '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Published in', 'tipsy' ) . '</span> '
						. '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'tipsy' ) . '</span> '
						. '<h5 class="post-title">%title</h5>'
						. '<span class="post_date">%date</span>',
			), 'image' ) );
			?>
		</div>
		<?php
	}

	// Comments
	do_action( 'tipsy_action_before_comments' );
	comments_template();
	do_action( 'tipsy_action_after_comments' );
}

get_footer();
