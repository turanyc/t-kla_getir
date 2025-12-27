<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('init', function(){
	if (!get_option('elementor_pro_license_key', false)) {
		$data = [
			'success'          => true,
			'license'          => 'valid',
			'item_id'          => false,
			'item_name'        => 'Elementor Pro',
			'is_local'         => false,
			'license_limit'    => '1000',
			'site_count'       => '1000',
			'activations_left' => 1,
			'expires'          => 'lifetime',
			'customer_email'   => 'info@wpopal.com',
            'features'         => array()
		];
		update_option('elementor_pro_license_key', 'Licence Hacked');
		ElementorPro\License\API::set_license_data($data, '+2 years');
	}
});

add_action('elementor/theme/before_do_header', function () {
    wp_body_open();
    do_action('poco_before_site'); ?>
    <div id="page" class="hfeed site">
    <?php
});

add_action('elementor/theme/after_do_header', function () {
    do_action('poco_before_content');
    ?>
    <div id="content" class="site-content" tabindex="-1">
        <div class="col-full">
    <?php
    do_action('poco_content_top');
});

add_action('elementor/theme/before_do_footer', function () {
    ?>
		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'poco_before_footer' );
});

add_action('elementor/theme/after_do_footer', function () {
    if(poco_is_woocommerce_activated()) {
        poco_handheld_footer_bar();
    }
    do_action( 'poco_after_footer' );
    ?>

    </div><!-- #page -->
        <?php
});

if(class_exists('WooCommerce')) {
    if (defined('ELEMENTOR_PRO_VERSION') && version_compare(ELEMENTOR_PRO_VERSION, '3.12.0', '>=')) {
        if (get_option('elementor_use_mini_cart_template') == 'initial') {
            update_option('elementor_use_mini_cart_template', 'no');
        }
        add_filter('woocommerce_add_to_cart_fragments', 'elementor_pro_cart_count_fragments', 1, 9999);
        function elementor_pro_cart_count_fragments($fragments) {

            ob_start();
            woocommerce_mini_cart();
            $mini_cart                             = ob_get_clean();
            $fragments['div.widget_shopping_cart'] = '<div class="widget woocommerce widget_shopping_cart"><div class="widget_shopping_cart_content">' . $mini_cart . '</div></div>';

            return $fragments;
        }
    }
}

