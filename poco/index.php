<?php
// Ensure the correct path to wp-load.php
$wp_load_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wp-load.php';

// Check if wp-load.php file exists
if (!file_exists($wp_load_path)) {
    die('Error: wp-load.php file not found at ' . $wp_load_path);
}

require_once($wp_load_path);

// Check if get_header function exists
if (!function_exists('get_header')) {
    die('Error: get_header function not found.');
}

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php
		if (have_posts()) :

			get_template_part('loop');

		else :

			get_template_part('content', 'none');

		endif;
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php

/**
 * Functions hooked in to poco_sidebar action
 *
 * @see poco_get_sidebar      - 10
 */
do_action('poco_sidebar');
get_footer();
