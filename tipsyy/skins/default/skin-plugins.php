<?php
/**
 * Required plugins
 *
 * @package TIPSY
 * @since TIPSY 1.76.0
 */

// THEME-SUPPORTED PLUGINS
// If plugin not need - remove its settings from next array
//----------------------------------------------------------
$tipsy_theme_required_plugins_groups = array(
	'core'          => esc_html__( 'Core', 'tipsy' ),
	'page_builders' => esc_html__( 'Page Builders', 'tipsy' ),
	'ecommerce'     => esc_html__( 'E-Commerce & Donations', 'tipsy' ),
	'socials'       => esc_html__( 'Socials and Communities', 'tipsy' ),
	'events'        => esc_html__( 'Events and Appointments', 'tipsy' ),
	'content'       => esc_html__( 'Content', 'tipsy' ),
	'other'         => esc_html__( 'Other', 'tipsy' ),
);
$tipsy_theme_required_plugins        = array(
	'trx_addons'                 => array(
		'title'       => esc_html__( 'ThemeREX Addons', 'tipsy' ),
		'description' => esc_html__( "Will allow you to install recommended plugins, demo content, and improve the theme's functionality overall with multiple theme options", 'tipsy' ),
		'required'    => true,
		'logo'        => 'trx_addons.png',
		'group'       => $tipsy_theme_required_plugins_groups['core'],
	),
	'elementor'                  => array(
		'title'       => esc_html__( 'Elementor', 'tipsy' ),
		'description' => esc_html__( "Is a beautiful PageBuilder, even the free version of which allows you to create great pages using a variety of modules.", 'tipsy' ),
		'required'    => false,
		'logo'        => 'elementor.png',
		'group'       => $tipsy_theme_required_plugins_groups['page_builders'],
	),
	'gutenberg'                  => array(
		'title'       => esc_html__( 'Gutenberg', 'tipsy' ),
		'description' => esc_html__( "It's a posts editor coming in place of the classic TinyMCE. Can be installed and used in parallel with Elementor", 'tipsy' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'gutenberg.png',
		'group'       => $tipsy_theme_required_plugins_groups['page_builders'],
	),
	'js_composer'                => array(
		'title'       => esc_html__( 'WPBakery PageBuilder', 'tipsy' ),
		'description' => esc_html__( "Popular PageBuilder which allows you to create excellent pages", 'tipsy' ),
		'required'    => false,
		'install'     => false,          // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'js_composer.jpg',
		'group'       => $tipsy_theme_required_plugins_groups['page_builders'],
	),
	'woocommerce'                => array(
		'title'       => esc_html__( 'WooCommerce', 'tipsy' ),
		'description' => esc_html__( "Connect the store to your website and start selling now", 'tipsy' ),
		'required'    => false,
		'install'     => false,
		'logo'        => 'woocommerce.png',
		'group'       => $tipsy_theme_required_plugins_groups['ecommerce'],
	),
	'elegro-payment'             => array(
		'title'       => esc_html__( 'Elegro Crypto Payment', 'tipsy' ),
		'description' => esc_html__( "Extends WooCommerce Payment Gateways with an elegro Crypto Payment", 'tipsy' ),
		'required'    => false,
		'install'     => false, // TRX_addons has marked the "Elegro Crypto Payment" plugin as obsolete and no longer recommends it for installation, even if it had been previously recommended by the theme
		'logo'        => 'elegro-payment.png',
		'group'       => $tipsy_theme_required_plugins_groups['ecommerce'],
	),
	'instagram-feed'             => array(
		'title'       => esc_html__( 'Instagram Feed', 'tipsy' ),
		'description' => esc_html__( "Displays the latest photos from your profile on Instagram", 'tipsy' ),
		'required'    => false,
		'logo'        => 'instagram-feed.png',
		'group'       => $tipsy_theme_required_plugins_groups['socials'],
	),
	'mailchimp-for-wp'           => array(
		'title'       => esc_html__( 'MailChimp for WP', 'tipsy' ),
		'description' => esc_html__( "Allows visitors to subscribe to newsletters", 'tipsy' ),
		'required'    => false,
		'logo'        => 'mailchimp-for-wp.png',
		'group'       => $tipsy_theme_required_plugins_groups['socials'],
	),
	'booked'                     => array(
		'title'       => esc_html__( 'Booked Appointments', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'booked.png',
		'group'       => $tipsy_theme_required_plugins_groups['events'],
	),
	'quickcal'                     => array(
		'title'       => esc_html__( 'QuickCal', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'quickcal.png',
		'group'       => $tipsy_theme_required_plugins_groups['events'],
	),
	'the-events-calendar'        => array(
		'title'       => esc_html__( 'The Events Calendar', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'the-events-calendar.png',
		'group'       => $tipsy_theme_required_plugins_groups['events'],
	),
	'contact-form-7'             => array(
		'title'       => esc_html__( 'Contact Form 7', 'tipsy' ),
		'description' => esc_html__( "CF7 allows you to create an unlimited number of contact forms", 'tipsy' ),
		'required'    => false,
		'logo'        => 'contact-form-7.png',
		'group'       => $tipsy_theme_required_plugins_groups['content'],
	),

	'latepoint'                  => array(
		'title'       => esc_html__( 'LatePoint', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'logo'        => tipsy_get_file_url( 'plugins/latepoint/latepoint.png' ),
		'group'       => $tipsy_theme_required_plugins_groups['events'],
	),
	'advanced-popups'                  => array(
		'title'       => esc_html__( 'Advanced Popups', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'logo'        => tipsy_get_file_url( 'plugins/advanced-popups/advanced-popups.jpg' ),
		'group'       => $tipsy_theme_required_plugins_groups['content'],
	),
	'devvn-image-hotspot'                  => array(
		'title'       => esc_html__( 'Image Hotspot by DevVN', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => tipsy_get_file_url( 'plugins/devvn-image-hotspot/devvn-image-hotspot.png' ),
		'group'       => $tipsy_theme_required_plugins_groups['content'],
	),
	'ti-woocommerce-wishlist'                  => array(
		'title'       => esc_html__( 'TI WooCommerce Wishlist', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => tipsy_get_file_url( 'plugins/ti-woocommerce-wishlist/ti-woocommerce-wishlist.png' ),
		'group'       => $tipsy_theme_required_plugins_groups['ecommerce'],
	),
	'woo-smart-quick-view'                  => array(
		'title'       => esc_html__( 'WPC Smart Quick View for WooCommerce', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => tipsy_get_file_url( 'plugins/woo-smart-quick-view/woo-smart-quick-view.png' ),
		'group'       => $tipsy_theme_required_plugins_groups['ecommerce'],
	),
	'twenty20'                  => array(
		'title'       => esc_html__( 'Twenty20 Image Before-After', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => tipsy_get_file_url( 'plugins/twenty20/twenty20.png' ),
		'group'       => $tipsy_theme_required_plugins_groups['content'],
	),
	'essential-grid'             => array(
		'title'       => esc_html__( 'Essential Grid', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'install'     => false,
		'logo'        => 'essential-grid.png',
		'group'       => $tipsy_theme_required_plugins_groups['content'],
	),
	'revslider'                  => array(
		'title'       => esc_html__( 'Revolution Slider', 'tipsy' ),
		'description' => '',
		'required'    => false,
		'logo'        => 'revslider.png',
		'group'       => $tipsy_theme_required_plugins_groups['content'],
	),
	'sitepress-multilingual-cms' => array(
		'title'       => esc_html__( 'WPML - Sitepress Multilingual CMS', 'tipsy' ),
		'description' => esc_html__( "Allows you to make your website multilingual", 'tipsy' ),
		'required'    => false,
		'install'     => false,      // Do not offer installation of the plugin in the Theme Dashboard and TGMPA
		'logo'        => 'sitepress-multilingual-cms.png',
		'group'       => $tipsy_theme_required_plugins_groups['content'],
	),
	'wp-gdpr-compliance'         => array(
		'title'       => esc_html__( 'Cookie Information', 'tipsy' ),
		'description' => esc_html__( "Allow visitors to decide for themselves what personal data they want to store on your site", 'tipsy' ),
		'required'    => false,
		'install'     => false,
		'logo'        => 'wp-gdpr-compliance.png',
		'group'       => $tipsy_theme_required_plugins_groups['other'],
	),
	'gdpr-framework'         => array(
		'title'       => esc_html__( 'The GDPR Framework', 'tipsy' ),
		'description' => esc_html__( "Tools to help make your website GDPR-compliant. Fully documented, extendable and developer-friendly.", 'tipsy' ),
		'required'    => false,
		'install'     => false,
		'logo'        => 'gdpr-framework.png',
		'group'       => $tipsy_theme_required_plugins_groups['other'],
	),
	'trx_updater'                => array(
		'title'       => esc_html__( 'ThemeREX Updater', 'tipsy' ),
		'description' => esc_html__( "Update theme and theme-specific plugins from developer's upgrade server.", 'tipsy' ),
		'required'    => false,
		'logo'        => 'trx_updater.png',
		'group'       => $tipsy_theme_required_plugins_groups['other'],
	),
);

if ( TIPSY_THEME_FREE ) {
	unset( $tipsy_theme_required_plugins['js_composer'] );
	unset( $tipsy_theme_required_plugins['booked'] );
	unset( $tipsy_theme_required_plugins['quickcal'] );
	unset( $tipsy_theme_required_plugins['the-events-calendar'] );
	unset( $tipsy_theme_required_plugins['calculated-fields-form'] );
	unset( $tipsy_theme_required_plugins['essential-grid'] );
	unset( $tipsy_theme_required_plugins['revslider'] );
	unset( $tipsy_theme_required_plugins['sitepress-multilingual-cms'] );
	unset( $tipsy_theme_required_plugins['trx_updater'] );
	unset( $tipsy_theme_required_plugins['trx_popup'] );
}

// Add plugins list to the global storage
tipsy_storage_set( 'required_plugins', $tipsy_theme_required_plugins );