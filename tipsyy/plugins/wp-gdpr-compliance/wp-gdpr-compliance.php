<?php
/* WP GDPR Compliance support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'tipsy_wp_gdpr_compliance_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'tipsy_wp_gdpr_compliance_theme_setup9', 9 );
	function tipsy_wp_gdpr_compliance_theme_setup9() {
		if ( is_admin() ) {
			add_filter( 'tipsy_filter_tgmpa_required_plugins', 'tipsy_wp_gdpr_compliance_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'tipsy_wp_gdpr_compliance_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('tipsy_filter_tgmpa_required_plugins',	'tipsy_wp_gdpr_compliance_tgmpa_required_plugins');
	function tipsy_wp_gdpr_compliance_tgmpa_required_plugins( $list = array() ) {
		if ( tipsy_storage_isset( 'required_plugins', 'wp-gdpr-compliance' ) && tipsy_storage_get_array( 'required_plugins', 'wp-gdpr-compliance', 'install' ) !== false ) {
			$path = tipsy_get_plugin_source_path( 'plugins/wp-gdpr-compliance/wp-gdpr-compliance.zip' );
			if ( ! empty( $path ) || tipsy_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => tipsy_storage_get_array( 'required_plugins', 'wp-gdpr-compliance', 'title' ),
					'slug'     => 'wp-gdpr-compliance',
					'source'   => ! empty( $path ) ? $path : 'upload://wp-gdpr-compliance.zip',
					'version'  => '2.0.23',
					'required' => false,
				);
			}
		}
		return $list;
	}
}

// Check if this plugin installed and activated
if ( ! function_exists( 'tipsy_exists_wp_gdpr_compliance' ) ) {
	function tipsy_exists_wp_gdpr_compliance() {
//		Old way (before v.2.0)
//		Attention! In the v.2.0 and v.2.0.1 this check throw fatal error in their autoloader!
//		return class_exists( 'WPGDPRC\WPGDPRC' );
//		New way (to avoid error in wp_gdpr_compliance autoloader)
//		Check constants:	before v.2.0						after v.2.0
		return defined( 'WP_GDPR_C_ROOT_FILE' ) || defined( 'WPGDPRC_ROOT_FILE' );
	}
}
