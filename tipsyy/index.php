<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: //codex.wordpress.org/Template_Hierarchy
 *
 * @package TIPSY
 * @since TIPSY 1.0
 */

$tipsy_template = apply_filters( 'tipsy_filter_get_template_part', tipsy_blog_archive_get_template() );

if ( ! empty( $tipsy_template ) && 'index' != $tipsy_template ) {

	get_template_part( $tipsy_template );

} else {

	tipsy_storage_set( 'blog_archive', true );

	get_header();

	if ( have_posts() ) {

		// Query params
		$tipsy_stickies   = is_home()
								|| ( in_array( tipsy_get_theme_option( 'post_type' ), array( '', 'post' ) )
									&& (int) tipsy_get_theme_option( 'parent_cat' ) == 0
									)
										? get_option( 'sticky_posts' )
										: false;
		$tipsy_post_type  = tipsy_get_theme_option( 'post_type' );
		$tipsy_args       = array(
								'blog_style'     => tipsy_get_theme_option( 'blog_style' ),
								'post_type'      => $tipsy_post_type,
								'taxonomy'       => tipsy_get_post_type_taxonomy( $tipsy_post_type ),
								'parent_cat'     => tipsy_get_theme_option( 'parent_cat' ),
								'posts_per_page' => tipsy_get_theme_option( 'posts_per_page' ),
								'sticky'         => tipsy_get_theme_option( 'sticky_style', 'inherit' ) == 'columns'
															&& is_array( $tipsy_stickies )
															&& count( $tipsy_stickies ) > 0
															&& get_query_var( 'paged' ) < 1
								);

		tipsy_blog_archive_start();

		do_action( 'tipsy_action_blog_archive_start' );

		if ( is_author() ) {
			do_action( 'tipsy_action_before_page_author' );
			get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/author-page' ) );
			do_action( 'tipsy_action_after_page_author' );
		}

		if ( tipsy_get_theme_option( 'show_filters', 0 ) ) {
			do_action( 'tipsy_action_before_page_filters' );
			tipsy_show_filters( $tipsy_args );
			do_action( 'tipsy_action_after_page_filters' );
		} else {
			do_action( 'tipsy_action_before_page_posts' );
			tipsy_show_posts( array_merge( $tipsy_args, array( 'cat' => $tipsy_args['parent_cat'] ) ) );
			do_action( 'tipsy_action_after_page_posts' );
		}

		do_action( 'tipsy_action_blog_archive_end' );

		tipsy_blog_archive_end();

	} else {

		if ( is_search() ) {
			get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		} else {
			get_template_part( apply_filters( 'tipsy_filter_get_template_part', 'templates/content', 'none-archive' ), 'none-archive' );
		}
	}

	get_footer();
}
