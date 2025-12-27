<?php
/**
 * Skin Setup
 *
 * @package TIPSY
 * @since TIPSY 1.76.0
 */


//--------------------------------------------
// SKIN DEFAULTS
//--------------------------------------------

// Return theme's (skin's) default value for the specified parameter
if ( ! function_exists( 'tipsy_theme_defaults' ) ) {
	function tipsy_theme_defaults( $name='', $value='' ) {
		$defaults = array(
			'page_width'          => 1290,
			'page_boxed_extra'  => 60,
			'page_fullwide_max' => 1920,
			'page_fullwide_extra' => 60,
			'sidebar_width'       => 410,
			'sidebar_gap'       => 40,
			'grid_gap'          => 30,
			'rad'               => 0
		);
		if ( empty( $name ) ) {
			return $defaults;
		} else {
			if ( $value === '' && isset( $defaults[ $name ] ) ) {
				$value = $defaults[ $name ];
			}
			return $value;
		}
	}
}


// WOOCOMMERCE SETUP
//--------------------------------------------------

// Allow extended layouts for WooCommerce
if ( ! function_exists( 'tipsy_skin_woocommerce_allow_extensions' ) ) {
	add_filter( 'tipsy_filter_load_woocommerce_extensions', 'tipsy_skin_woocommerce_allow_extensions' );
	function tipsy_skin_woocommerce_allow_extensions( $allow ) {
		return true;
	}
}


// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)


//--------------------------------------------
// SKIN SETTINGS
//--------------------------------------------
if ( ! function_exists( 'tipsy_skin_setup' ) ) {
	add_action( 'after_setup_theme', 'tipsy_skin_setup', 1 );
	function tipsy_skin_setup() {

		$GLOBALS['TIPSY_STORAGE'] = array_merge( $GLOBALS['TIPSY_STORAGE'], array(

			// Key validator: market[env|loc]-vendor[axiom|ancora|themerex]
			'theme_pro_key'       => 'env-themerex',

			'theme_doc_url'       => '//doc.themerex.net/tipsy/',

			'theme_demofiles_url' => '//demofiles.themerex.net/tipsy/',
			
			'theme_rate_url'      => '//themeforest.net/downloads',

			'theme_custom_url'    => '//themerex.net/offers/?utm_source=offers&utm_medium=click&utm_campaign=themeinstall',

			'theme_support_url'   => '//themerex.net/support/',

			'theme_download_url'  => '//themeforest.net/user/themerex/portfolio',								// ThemeREX

			'theme_video_url'     => '//www.youtube.com/channel/UCdIjRh7-lPVHqTTKpaf8PLA',						// ThemeREX

			'theme_privacy_url'   => '//themerex.net/privacy-policy/',											// ThemeREX

			'portfolio_url'       => '//themeforest.net/user/themerex/portfolio',								// ThemeREX

			// Comma separated slugs of theme-specific categories (for get relevant news in the dashboard widget)
			// (i.e. 'children,kindergarten')
			'theme_categories'    => '',
		) );
	}
}


// Add/remove/change Theme Settings
if ( ! function_exists( 'tipsy_skin_setup_settings' ) ) {
	add_action( 'after_setup_theme', 'tipsy_skin_setup_settings', 1 );
	function tipsy_skin_setup_settings() {
		// Example: enable (true) / disable (false) thumbs in the prev/next navigation
		tipsy_storage_set_array( 'settings', 'thumbs_in_navigation', false );
		tipsy_storage_set_array2( 'required_plugins', 'latepoint', 'install', false );
		tipsy_storage_set_array2( 'required_plugins', 'woocommerce', 'install', true );
		tipsy_storage_set_array2( 'required_plugins', 'woo-smart-quick-view', 'install', true );
		tipsy_storage_set_array2( 'required_plugins', 'ti-woocommerce-wishlist', 'install', true );
	}
}



//--------------------------------------------
// SKIN FONTS
//--------------------------------------------
if ( ! function_exists( 'tipsy_skin_setup_fonts' ) ) {
	add_action( 'after_setup_theme', 'tipsy_skin_setup_fonts', 1 );
	function tipsy_skin_setup_fonts() {
		// Fonts to load when theme start
		// It can be:
		// - Google fonts (specify name, family and styles)
		// - Adobe fonts (specify name, family and link URL)
		// - uploaded fonts (specify name, family), placed in the folder css/font-face/font-name inside the skin folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		// example: font name 'TeX Gyre Termes', folder 'TeX-Gyre-Termes'
		tipsy_storage_set(
			'load_fonts', array(
				array(
					'name'   => 'halyard-display',
					'family' => 'sans-serif',
					'link'   => 'https://use.typekit.net/xog3vbp.css',
					'styles' => ''
				),
				// Google font
				array(
					'name'   => 'DM Sans',
					'family' => 'sans-serif',
					'link'   => '',
					'styles' => 'ital,wght@0,400;0,500;0,700;1,400;1,500;1,700',     // Parameter 'style' used only for the Google fonts
				),
			)
		);

		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		tipsy_storage_set( 'load_fonts_subset', 'latin,latin-ext' );

		// Settings of the main tags.
		// Default value of 'font-family' may be specified as reference to the array $load_fonts (see above)
		// or as comma-separated string.
		// In the second case (if 'font-family' is specified manually as comma-separated string):
		//    1) Font name with spaces in the parameter 'font-family' will be enclosed in the quotes and no spaces after comma!
		//    2) If font-family inherit a value from the 'Main text' - specify 'inherit' as a value
		// example:
		// Correct:   'font-family' => tipsy_get_load_fonts_family_string( $load_fonts[0] )
		// Correct:   'font-family' => 'Roboto,sans-serif'
		// Correct:   'font-family' => '"PT Serif",sans-serif'
		// Incorrect: 'font-family' => 'Roboto, sans-serif'
		// Incorrect: 'font-family' => 'PT Serif,sans-serif'

		$font_description = esc_html__( 'Font settings for the %s of the site. To ensure that the elements scale properly on mobile devices, please use only the following units: "rem", "em" or "ex"', 'tipsy' );

		tipsy_storage_set(
			'theme_fonts', array(
				'p'       => array(
					'title'           => esc_html__( 'Main text', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'main text', 'tipsy' ) ),
					'font-family'     => '"DM Sans",sans-serif',
					'font-size'       => '1rem',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.68em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '0em',
					'margin-bottom'   => '1.7em',
				),
				'post'    => array(
					'title'           => esc_html__( 'Article text', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'article text', 'tipsy' ) ),
					'font-family'     => '',			// Example: '"PR Serif",serif',
					'font-size'       => '',			// Example: '1.286rem',
					'font-weight'     => '',			// Example: '400',
					'font-style'      => '',			// Example: 'normal',
					'line-height'     => '',			// Example: '1.75em',
					'text-decoration' => '',			// Example: 'none',
					'text-transform'  => '',			// Example: 'none',
					'letter-spacing'  => '',			// Example: '',
					'margin-top'      => '',			// Example: '0em',
					'margin-bottom'   => '',			// Example: '1.4em',
				),
				'h1'      => array(
					'title'           => esc_html__( 'Heading 1', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H1', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '3.353em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.04em',
					'margin-bottom'   => '0.46em',
				),
				'h2'      => array(
					'title'           => esc_html__( 'Heading 2', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H2', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '2.765em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.021em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '0.67em',
					'margin-bottom'   => '0.56em',
				),
				'h3'      => array(
					'title'           => esc_html__( 'Heading 3', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H3', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '2.059em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.029em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '0.94em',
					'margin-bottom'   => '0.72em',
				),
				'h4'      => array(
					'title'           => esc_html__( 'Heading 4', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H4', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '1.647em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.036em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.15em',
					'margin-bottom'   => '0.83em',
				),
				'h5'      => array(
					'title'           => esc_html__( 'Heading 5', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H5', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '1.412em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.083em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.3em',
					'margin-bottom'   => '0.84em',
				),
				'h6'      => array(
					'title'           => esc_html__( 'Heading 6', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'tag H6', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '1.118em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.263em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '1.75em',
					'margin-bottom'   => '1.1em',
				),
				'logo'    => array(
					'title'           => esc_html__( 'Logo text', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'text of the logo', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '1.7em',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.25em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'button'  => array(
					'title'           => esc_html__( 'Buttons', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'buttons', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '14px',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '21px',
					'text-decoration' => 'none',
					'text-transform'  => 'uppercase',
					'letter-spacing'  => '1.5px',
				),
				'input'   => array(
					'title'           => esc_html__( 'Input fields', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'input fields, dropdowns and textareas', 'tipsy' ) ),
					'font-family'     => 'inherit',
					'font-size'       => '16px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',     // Attention! Firefox don't allow line-height less then 1.5em in the select
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'info'    => array(
					'title'           => esc_html__( 'Post meta', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'post meta (author, categories, publish date, counters, share, etc.)', 'tipsy' ) ),
					'font-family'     => 'inherit',
					'font-size'       => '14px',  // Old value '13px' don't allow using 'font zoom' in the custom blog items
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
					'margin-top'      => '0.4em',
					'margin-bottom'   => '',
				),
				'menu'    => array(
					'title'           => esc_html__( 'Main menu', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'main menu items', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
					'font-size'       => '17px',
					'font-weight'     => '500',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'submenu' => array(
					'title'           => esc_html__( 'Dropdown menu', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'dropdown menu items', 'tipsy' ) ),
					'font-family'     => '"DM Sans",sans-serif',
					'font-size'       => '15px',
					'font-weight'     => '400',
					'font-style'      => 'normal',
					'line-height'     => '1.5em',
					'text-decoration' => 'none',
					'text-transform'  => 'none',
					'letter-spacing'  => '0px',
				),
				'other' => array(
					'title'           => esc_html__( 'Other', 'tipsy' ),
					'description'     => sprintf( $font_description, esc_html__( 'specific elements', 'tipsy' ) ),
					'font-family'     => 'halyard-display,sans-serif',
				),
			)
		);

		// Font presets
		tipsy_storage_set(
			'font_presets', array(
				'karla' => array(
					'title'  => esc_html__( 'Karla', 'tipsy' ),
					'load_fonts' => array(
						// Google font
						array(
							'name'   => 'Dancing Script',
							'family' => 'fantasy',
							'link'   => '',
							'styles' => '300,400,700',
						),
						// Google font
						array(
							'name'   => 'Sansita Swashed',
							'family' => 'fantasy',
							'link'   => '',
							'styles' => '300,400,700',
						),
					),
					'theme_fonts' => array(
						'p'       => array(
							'font-family'     => '"Dancing Script",fantasy',
							'font-size'       => '1.25rem',
						),
						'post'    => array(
							'font-family'     => '',
						),
						'h1'      => array(
							'font-family'     => '"Sansita Swashed",fantasy',
							'font-size'       => '4em',
						),
						'h2'      => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
						'h3'      => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
						'h4'      => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
						'h5'      => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
						'h6'      => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
						'logo'    => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
						'button'  => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
						'input'   => array(
							'font-family'     => 'inherit',
						),
						'info'    => array(
							'font-family'     => 'inherit',
						),
						'menu'    => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
						'submenu' => array(
							'font-family'     => '"Sansita Swashed",fantasy',
						),
					),
				),
				'roboto' => array(
					'title'  => esc_html__( 'Roboto', 'tipsy' ),
					'load_fonts' => array(
						// Google font
						array(
							'name'   => 'Noto Sans JP',
							'family' => 'serif',
							'link'   => '',
							'styles' => '300,300italic,400,400italic,700,700italic',
						),
						// Google font
						array(
							'name'   => 'Merriweather',
							'family' => 'sans-serif',
							'link'   => '',
							'styles' => '300,300italic,400,400italic,700,700italic',
						),
					),
					'theme_fonts' => array(
						'p'       => array(
							'font-family'     => '"Noto Sans JP",serif',
						),
						'post'    => array(
							'font-family'     => '',
						),
						'h1'      => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'h2'      => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'h3'      => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'h4'      => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'h5'      => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'h6'      => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'logo'    => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'button'  => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'input'   => array(
							'font-family'     => 'inherit',
						),
						'info'    => array(
							'font-family'     => 'inherit',
						),
						'menu'    => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
						'submenu' => array(
							'font-family'     => 'Merriweather,sans-serif',
						),
					),
				),
				'garamond' => array(
					'title'  => esc_html__( 'Garamond', 'tipsy' ),
					'load_fonts' => array(
						// Adobe font
						array(
							'name'   => 'Europe',
							'family' => 'sans-serif',
							'link'   => 'https://use.typekit.net/qmj1tmx.css',
							'styles' => '',
						),
						// Adobe font
						array(
							'name'   => 'Sofia Pro',
							'family' => 'sans-serif',
							'link'   => 'https://use.typekit.net/qmj1tmx.css',
							'styles' => '',
						),
					),
					'theme_fonts' => array(
						'p'       => array(
							'font-family'     => '"Sofia Pro",sans-serif',
						),
						'post'    => array(
							'font-family'     => '',
						),
						'h1'      => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'h2'      => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'h3'      => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'h4'      => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'h5'      => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'h6'      => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'logo'    => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'button'  => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'input'   => array(
							'font-family'     => 'inherit',
						),
						'info'    => array(
							'font-family'     => 'inherit',
						),
						'menu'    => array(
							'font-family'     => 'Europe,sans-serif',
						),
						'submenu' => array(
							'font-family'     => 'Europe,sans-serif',
						),
					),
				),
			)
		);
	}
}


//--------------------------------------------
// COLOR SCHEMES
//--------------------------------------------
if ( ! function_exists( 'tipsy_skin_setup_schemes' ) ) {
	add_action( 'after_setup_theme', 'tipsy_skin_setup_schemes', 1 );
	function tipsy_skin_setup_schemes() {

		// Theme colors for customizer
		// Attention! Inner scheme must be last in the array below
		tipsy_storage_set(
			'scheme_color_groups', array(
				'main'    => array(
					'title'       => esc_html__( 'Main', 'tipsy' ),
					'description' => esc_html__( 'Colors of the main content area', 'tipsy' ),
				),
				'alter'   => array(
					'title'       => esc_html__( 'Alter', 'tipsy' ),
					'description' => esc_html__( 'Colors of the alternative blocks (sidebars, etc.)', 'tipsy' ),
				),
				'extra'   => array(
					'title'       => esc_html__( 'Extra', 'tipsy' ),
					'description' => esc_html__( 'Colors of the extra blocks (dropdowns, price blocks, table headers, etc.)', 'tipsy' ),
				),
				'inverse' => array(
					'title'       => esc_html__( 'Inverse', 'tipsy' ),
					'description' => esc_html__( 'Colors of the inverse blocks - when link color used as background of the block (dropdowns, blockquotes, etc.)', 'tipsy' ),
				),
				'input'   => array(
					'title'       => esc_html__( 'Input', 'tipsy' ),
					'description' => esc_html__( 'Colors of the form fields (text field, textarea, select, etc.)', 'tipsy' ),
				),
			)
		);

		tipsy_storage_set(
			'scheme_color_names', array(
				'bg_color'    => array(
					'title'       => esc_html__( 'Background color', 'tipsy' ),
					'description' => esc_html__( 'Background color of this block in the normal state', 'tipsy' ),
				),
				'bg_hover'    => array(
					'title'       => esc_html__( 'Background hover', 'tipsy' ),
					'description' => esc_html__( 'Background color of this block in the hovered state', 'tipsy' ),
				),
				'bd_color'    => array(
					'title'       => esc_html__( 'Border color', 'tipsy' ),
					'description' => esc_html__( 'Border color of this block in the normal state', 'tipsy' ),
				),
				'bd_hover'    => array(
					'title'       => esc_html__( 'Border hover', 'tipsy' ),
					'description' => esc_html__( 'Border color of this block in the hovered state', 'tipsy' ),
				),
				'text'        => array(
					'title'       => esc_html__( 'Text', 'tipsy' ),
					'description' => esc_html__( 'Color of the text inside this block', 'tipsy' ),
				),
				'text_dark'   => array(
					'title'       => esc_html__( 'Text dark', 'tipsy' ),
					'description' => esc_html__( 'Color of the dark text (bold, header, etc.) inside this block', 'tipsy' ),
				),
				'text_light'  => array(
					'title'       => esc_html__( 'Text light', 'tipsy' ),
					'description' => esc_html__( 'Color of the light text (post meta, etc.) inside this block', 'tipsy' ),
				),
				'text_link'   => array(
					'title'       => esc_html__( 'Link', 'tipsy' ),
					'description' => esc_html__( 'Color of the links inside this block', 'tipsy' ),
				),
				'text_hover'  => array(
					'title'       => esc_html__( 'Link hover', 'tipsy' ),
					'description' => esc_html__( 'Color of the hovered state of links inside this block', 'tipsy' ),
				),
				'text_link2'  => array(
					'title'       => esc_html__( 'Accent 2', 'tipsy' ),
					'description' => esc_html__( 'Color of the accented texts (areas) inside this block', 'tipsy' ),
				),
				'text_hover2' => array(
					'title'       => esc_html__( 'Accent 2 hover', 'tipsy' ),
					'description' => esc_html__( 'Color of the hovered state of accented texts (areas) inside this block', 'tipsy' ),
				),
				'text_link3'  => array(
					'title'       => esc_html__( 'Accent 3', 'tipsy' ),
					'description' => esc_html__( 'Color of the other accented texts (buttons) inside this block', 'tipsy' ),
				),
				'text_hover3' => array(
					'title'       => esc_html__( 'Accent 3 hover', 'tipsy' ),
					'description' => esc_html__( 'Color of the hovered state of other accented texts (buttons) inside this block', 'tipsy' ),
				),
			)
		);

		// Default values for each color scheme
		$schemes = array(

			// Color scheme: 'default'
			'default' => array(
				'title'    => esc_html__( 'Default', 'tipsy' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#FAF9F6',
					'bd_color'         => '#E5E2D8',

					// Text and links colors
					'text'             => '#85837E',
					'text_light'       => '#AFAEAC',
					'text_dark'        => '#1D1B18',
					'text_link'        => '#EB9E5B',
					'text_hover'       => '#E4944D',
					'text_link2'       => '#588C73',
					'text_hover2'      => '#4A8166',
					'text_link3'       => '#D96459',
					'text_hover3'      => '#C95348',

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#FFFFFF',
					'alter_bg_hover'   => '#F4F2EC',
					'alter_bd_color'   => '#E5E2D8',
					'alter_bd_hover'   => '#DEDACD',
					'alter_text'       => '#85837E',
					'alter_light'      => '#AFAEAC',
					'alter_dark'       => '#1D1B18',
					'alter_link'       => '#EB9E5B',
					'alter_hover'      => '#E4944D',
					'alter_link2'      => '#588C73',
					'alter_hover2'     => '#4A8166',
					'alter_link3'      => '#D96459',
					'alter_hover3'     => '#C95348',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1B1B1A',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#31312F',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#CDCDC6',
					'extra_light'      => '#A6A69E',
					'extra_dark'       => '#FFFEFE',
					'extra_link'       => '#EB9E5B',
					'extra_hover'      => '#FFFEFE',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent',
					'input_bg_hover'   => 'transparent',
					'input_bd_color'   => '#E5E2D8',
					'input_bd_hover'   => '#DEDACD',
					'input_text'       => '#AFAEAC',
					'input_light'      => '#AFAEAC',
					'input_dark'       => '#1D1B18',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#85837E',
					'inverse_bd_hover' => '#FFFFFF',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#1D1B18',
					'inverse_link'     => '#FFFEFE',
					'inverse_hover'    => '#FFFEFE',

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'dark'
			'dark'    => array(
				'title'    => esc_html__( 'Dark', 'tipsy' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#151514',
					'bd_color'         => '#31312F',

					// Text and links colors
					'text'             => '#CDCDC6',
					'text_light'       => '#A6A69E',
					'text_dark'        => '#FFFEFE',
					'text_link'        => '#EB9E5B',
					'text_hover'       => '#E4944D',
					'text_link2'       => '#588C73',
					'text_hover2'      => '#4A8166',
					'text_link3'       => '#D96459',
					'text_hover3'      => '#C95348',

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#252525',
					'alter_bg_hover'   => '#1E1E1E',
					'alter_bd_color'   => '#31312F',
					'alter_bd_hover'   => '#3F3F3D',
					'alter_text'       => '#CDCDC6',
					'alter_light'      => '#A6A69E',
					'alter_dark'       => '#FFFEFE',
					'alter_link'       => '#EB9E5B',
					'alter_hover'      => '#E4944D',
					'alter_link2'      => '#588C73',
					'alter_hover2'     => '#4A8166',
					'alter_link3'      => '#D96459',
					'alter_hover3'     => '#C95348',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1B1B1A',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#31312F',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#CDCDC6',
					'extra_light'      => '#A6A69E',
					'extra_dark'       => '#FFFEFE',
					'extra_link'       => '#EB9E5B',
					'extra_hover'      => '#FFFEFE',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#transparent',
					'input_bg_hover'   => '#transparent',
					'input_bd_color'   => '#31312F',
					'input_bd_hover'   => '#3F3F3D',
					'input_text'       => '#CDCDC6',
					'input_light'      => '#CDCDC6',
					'input_dark'       => '#FFFEFE',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#FFFEFE',
					'inverse_bd_hover' => '#2E2D2D',
					'inverse_text'     => '#F9F9F9',
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#1D1B18',
					'inverse_link'     => '#FFFEFE',
					'inverse_hover'    => '#1D1B18',

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'light'
			'light' => array(
				'title'    => esc_html__( 'Light', 'tipsy' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#FFFFFF',
					'bd_color'         => '#E5E2D8',

					// Text and links colors
					'text'             => '#85837E',
					'text_light'       => '#AFAEAC',
					'text_dark'        => '#1D1B18',
					'text_link'        => '#EB9E5B',
					'text_hover'       => '#E4944D',
					'text_link2'       => '#588C73',
					'text_hover2'      => '#4A8166',
					'text_link3'       => '#D96459',
					'text_hover3'      => '#C95348',

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#FAF9F6',
					'alter_bg_hover'   => '#F4F2EC',
					'alter_bd_color'   => '#E5E2D8',
					'alter_bd_hover'   => '#DEDACD',
					'alter_text'       => '#85837E',
					'alter_light'      => '#AFAEAC',
					'alter_dark'       => '#1D1B18',
					'alter_link'       => '#EB9E5B',
					'alter_hover'      => '#E4944D',
					'alter_link2'      => '#588C73',
					'alter_hover2'     => '#4A8166',
					'alter_link3'      => '#D96459',
					'alter_hover3'     => '#C95348',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1B1B1A',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#31312F',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#CDCDC6',
					'extra_light'      => '#A6A69E',
					'extra_dark'       => '#FFFEFE',
					'extra_link'       => '#EB9E5B',
					'extra_hover'      => '#FFFEFE',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent',
					'input_bg_hover'   => 'transparent',
					'input_bd_color'   => '#E5E2D8',
					'input_bd_hover'   => '#DEDACD',
					'input_text'       => '#AFAEAC',
					'input_light'      => '#AFAEAC',
					'input_dark'       => '#1D1B18',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#85837E',
					'inverse_bd_hover' => '#FFFFFF',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#1D1B18',
					'inverse_link'     => '#FFFEFE',
					'inverse_hover'    => '#FFFEFE',

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'extra_default'
			'extra_default' => array(
				'title'    => esc_html__( 'Extra Default', 'tipsy' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#F6F6EB',
					'bd_color'         => '#DFDDD8',

					// Text and links colors
					'text'             => '#8F8C85',
					'text_light'       => '#BEBCB9',
					'text_dark'        => '#2B2824',
					'text_link'        => '#BD9F63',
					'text_hover'       => '#B0904E',
					'text_link2'       => '#565C2E',
					'text_hover2'      => '#494F20',
					'text_link3'       => '#883131',
					'text_hover3'      => '#7C2626',

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#FFFFFF',
					'alter_bg_hover'   => '#EDEDDC',
					'alter_bd_color'   => '#DFDDD8',
					'alter_bd_hover'   => '#D6D3CE',
					'alter_text'       => '#8F8C85',
					'alter_light'      => '#BEBCB9',
					'alter_dark'       => '#2B2824',
					'alter_link'       => '#BD9F63',
					'alter_hover'      => '#B0904E',
					'alter_link2'      => '#565C2E',
					'alter_hover2'     => '#494F20',
					'alter_link3'      => '#883131',
					'alter_hover3'     => '#7C2626',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1B1B1A',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#3E3D3C',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#D0CCC6',
					'extra_light'      => '#ADA8A0',
					'extra_dark'       => '#FFFEFE',
					'extra_link'       => '#BD9F63',
					'extra_hover'      => '#FFFEFE',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent',
					'input_bg_hover'   => 'transparent',
					'input_bd_color'   => '#DFDDD8',
					'input_bd_hover'   => '#D6D3CE',
					'input_text'       => '#BEBCB9',
					'input_light'      => '#BEBCB9',
					'input_dark'       => '#2B2824',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#8F8C85',
					'inverse_bd_hover' => '#FFFFFF',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#2B2824',
					'inverse_link'     => '#FFFEFE',
					'inverse_hover'    => '#FFFEFE',

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'extra_dark'
			'extra_dark'    => array(
				'title'    => esc_html__( 'Extra Dark', 'tipsy' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#201E1E',
					'bd_color'         => '#3E3D3C',

					// Text and links colors
					'text'             => '#D0CCC6',
					'text_light'       => '#ADA8A0',
					'text_dark'        => '#FFFEFE',
					'text_link'        => '#BD9F63',
					'text_hover'       => '#B0904E',
					'text_link2'       => '#565C2E',
					'text_hover2'      => '#494F20',
					'text_link3'       => '#883131',
					'text_hover3'      => '#7C2626',

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#100F0F',
					'alter_bg_hover'   => '#232631',
					'alter_bd_color'   => '#3E3D3C',
					'alter_bd_hover'   => '#515050',
					'alter_text'       => '#D0CCC6',
					'alter_light'      => '#ADA8A0',
					'alter_dark'       => '#FFFEFE',
					'alter_link'       => '#BD9F63',
					'alter_hover'      => '#B0904E',
					'alter_link2'      => '#565C2E',
					'alter_hover2'     => '#494F20',
					'alter_link3'      => '#883131',
					'alter_hover3'     => '#7C2626',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1B1B1A',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#3E3D3C',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#D0CCC6',
					'extra_light'      => '#ADA8A0',
					'extra_dark'       => '#FFFEFE',
					'extra_link'       => '#BD9F63',
					'extra_hover'      => '#FFFEFE',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => '#transparent',
					'input_bg_hover'   => '#transparent',
					'input_bd_color'   => '#3E3D3C',
					'input_bd_hover'   => '#515050',
					'input_text'       => '#D0CCC6',
					'input_light'      => '#D0CCC6',
					'input_dark'       => '#FFFEFE',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#FFFEFE',
					'inverse_bd_hover' => '#2E2D2D',
					'inverse_text'     => '#F9F9F9',
					'inverse_light'    => '#6f6f6f',
					'inverse_dark'     => '#2B2824',
					'inverse_link'     => '#FFFEFE',
					'inverse_hover'    => '#2B2824',

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),

			// Color scheme: 'extra_light'
			'extra_light' => array(
				'title'    => esc_html__( 'Extra Light', 'tipsy' ),
				'internal' => true,
				'colors'   => array(

					// Whole block border and background
					'bg_color'         => '#FFFFFF',
					'bd_color'         => '#DFDDD8',

					// Text and links colors
					'text'             => '#8F8C85',
					'text_light'       => '#BEBCB9',
					'text_dark'        => '#2B2824',
					'text_link'        => '#BD9F63',
					'text_hover'       => '#B0904E',
					'text_link2'       => '#565C2E',
					'text_hover2'      => '#494F20',
					'text_link3'       => '#883131',
					'text_hover3'      => '#7C2626',

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'   => '#F6F6EB',
					'alter_bg_hover'   => '#EDEDDC',
					'alter_bd_color'   => '#DFDDD8',
					'alter_bd_hover'   => '#D6D3CE',
					'alter_text'       => '#8F8C85',
					'alter_light'      => '#BEBCB9',
					'alter_dark'       => '#2B2824',
					'alter_link'       => '#BD9F63',
					'alter_hover'      => '#B0904E',
					'alter_link2'      => '#565C2E',
					'alter_hover2'     => '#494F20',
					'alter_link3'      => '#883131',
					'alter_hover3'     => '#7C2626',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'   => '#1B1B1A',
					'extra_bg_hover'   => '#3f3d47',
					'extra_bd_color'   => '#3E3D3C',
					'extra_bd_hover'   => '#575757',
					'extra_text'       => '#D0CCC6',
					'extra_light'      => '#ADA8A0',
					'extra_dark'       => '#FFFEFE',
					'extra_link'       => '#BD9F63',
					'extra_hover'      => '#FFFEFE',
					'extra_link2'      => '#80d572',
					'extra_hover2'     => '#8be77c',
					'extra_link3'      => '#ddb837',
					'extra_hover3'     => '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'   => 'transparent',
					'input_bg_hover'   => 'transparent',
					'input_bd_color'   => '#DFDDD8',
					'input_bd_hover'   => '#D6D3CE',
					'input_text'       => '#BEBCB9',
					'input_light'      => '#BEBCB9',
					'input_dark'       => '#2B2824',

					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color' => '#8F8C85',
					'inverse_bd_hover' => '#FFFFFF',
					'inverse_text'     => '#1d1d1d',
					'inverse_light'    => '#333333',
					'inverse_dark'     => '#2B2824',
					'inverse_link'     => '#FFFEFE',
					'inverse_hover'    => '#FFFEFE',

					// Additional (skin-specific) colors.
					// Attention! Set of colors must be equal in all color schemes.
					//---> For example:
					//---> 'new_color1'         => '#rrggbb',
					//---> 'alter_new_color1'   => '#rrggbb',
					//---> 'inverse_new_color1' => '#rrggbb',
				),
			),			
		);
		tipsy_storage_set( 'schemes', $schemes );
		tipsy_storage_set( 'schemes_original', $schemes );

		// Add names of additional colors
		//---> For example:
		//---> tipsy_storage_set_array( 'scheme_color_names', 'new_color1', array(
		//---> 	'title'       => __( 'New color 1', 'tipsy' ),
		//---> 	'description' => __( 'Description of the new color 1', 'tipsy' ),
		//---> ) );


		// Additional colors for each scheme
		// Parameters:	'color' - name of the color from the scheme that should be used as source for the transformation
		//				'alpha' - to make color transparent (0.0 - 1.0)
		//				'hue', 'saturation', 'brightness' - inc/dec value for each color's component
		tipsy_storage_set(
			'scheme_colors_add', array(
				'bg_color_0'        => array(
					'color' => 'bg_color',
					'alpha' => 0,
				),
				'bg_color_02'       => array(
					'color' => 'bg_color',
					'alpha' => 0.2,
				),
				'bg_color_07'       => array(
					'color' => 'bg_color',
					'alpha' => 0.7,
				),
				'bg_color_08'       => array(
					'color' => 'bg_color',
					'alpha' => 0.8,
				),
				'bg_color_09'       => array(
					'color' => 'bg_color',
					'alpha' => 0.9,
				),
				'alter_bg_color_07' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.7,
				),
				'alter_bg_color_08' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.8,
				),
				'alter_bg_color_04' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.4,
				),
				'alter_bg_color_00' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0,
				),
				'alter_bg_color_02' => array(
					'color' => 'alter_bg_color',
					'alpha' => 0.2,
				),
				'alter_bd_color_02' => array(
					'color' => 'alter_bd_color',
					'alpha' => 0.2,
				),
				'alter_dark_015'     => array(
					'color' => 'alter_dark',
					'alpha' => 0.15,
				),
				'alter_dark_02'     => array(
					'color' => 'alter_dark',
					'alpha' => 0.2,
				),
				'alter_dark_05'     => array(
					'color' => 'alter_dark',
					'alpha' => 0.5,
				),
				'alter_dark_08'     => array(
					'color' => 'alter_dark',
					'alpha' => 0.8,
				),
				'alter_link_02'     => array(
					'color' => 'alter_link',
					'alpha' => 0.2,
				),
				'alter_link_07'     => array(
					'color' => 'alter_link',
					'alpha' => 0.7,
				),
				'extra_bg_color_05' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.5,
				),
				'extra_bg_color_07' => array(
					'color' => 'extra_bg_color',
					'alpha' => 0.7,
				),
				'extra_link_02'     => array(
					'color' => 'extra_link',
					'alpha' => 0.2,
				),
				'extra_link_07'     => array(
					'color' => 'extra_link',
					'alpha' => 0.7,
				),
				'text_dark_003'      => array(
					'color' => 'text_dark',
					'alpha' => 0.03,
				),
				'text_dark_005'      => array(
					'color' => 'text_dark',
					'alpha' => 0.05,
				),
				'text_dark_008'      => array(
					'color' => 'text_dark',
					'alpha' => 0.08,
				),
				'text_dark_015'      => array(
					'color' => 'text_dark',
					'alpha' => 0.15,
				),
				'text_dark_02'      => array(
					'color' => 'text_dark',
					'alpha' => 0.2,
				),
				'text_dark_03'      => array(
					'color' => 'text_dark',
					'alpha' => 0.3,
				),
				'text_dark_05'      => array(
					'color' => 'text_dark',
					'alpha' => 0.5,
				),
				'text_dark_07'      => array(
					'color' => 'text_dark',
					'alpha' => 0.7,
				),
				'text_dark_08'      => array(
					'color' => 'text_dark',
					'alpha' => 0.8,
				),
				'text_link_007'      => array(
					'color' => 'text_link',
					'alpha' => 0.07,
				),
				'text_link_02'      => array(
					'color' => 'text_link',
					'alpha' => 0.2,
				),
				'text_link_03'      => array(
					'color' => 'text_link',
					'alpha' => 0.3,
				),
				'text_link_04'      => array(
					'color' => 'text_link',
					'alpha' => 0.4,
				),
				'text_link_07'      => array(
					'color' => 'text_link',
					'alpha' => 0.7,
				),
				'text_link2_08'      => array(
					'color' => 'text_link2',
					'alpha' => 0.8,
				),
				'text_link2_007'      => array(
					'color' => 'text_link2',
					'alpha' => 0.07,
				),
				'text_link2_02'      => array(
					'color' => 'text_link2',
					'alpha' => 0.2,
				),
				'text_link2_03'      => array(
					'color' => 'text_link2',
					'alpha' => 0.3,
				),
				'text_link2_05'      => array(
					'color' => 'text_link2',
					'alpha' => 0.5,
				),
				'text_link3_007'      => array(
					'color' => 'text_link3',
					'alpha' => 0.07,
				),
				'text_link3_02'      => array(
					'color' => 'text_link3',
					'alpha' => 0.2,
				),
				'text_link3_03'      => array(
					'color' => 'text_link3',
					'alpha' => 0.3,
				),
				'inverse_text_03'      => array(
					'color' => 'inverse_text',
					'alpha' => 0.3,
				),
				'inverse_link_08'      => array(
					'color' => 'inverse_link',
					'alpha' => 0.8,
				),
				'inverse_hover_08'      => array(
					'color' => 'inverse_hover',
					'alpha' => 0.8,
				),
				'text_dark_blend'   => array(
					'color'      => 'text_dark',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
				'text_link_blend'   => array(
					'color'      => 'text_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
				'alter_link_blend'  => array(
					'color'      => 'alter_link',
					'hue'        => 2,
					'saturation' => -5,
					'brightness' => 5,
				),
			)
		);

		// Simple scheme editor: lists the colors to edit in the "Simple" mode.
		// For each color you can set the array of 'slave' colors and brightness factors that are used to generate new values,
		// when 'main' color is changed
		// Leave 'slave' arrays empty if your scheme does not have a color dependency
		tipsy_storage_set(
			'schemes_simple', array(
				'text_link'        => array(),
				'text_hover'       => array(),
				'text_link2'       => array(),
				'text_hover2'      => array(),
				'text_link3'       => array(),
				'text_hover3'      => array(),
				'alter_link'       => array(),
				'alter_hover'      => array(),
				'alter_link2'      => array(),
				'alter_hover2'     => array(),
				'alter_link3'      => array(),
				'alter_hover3'     => array(),
				'extra_link'       => array(),
				'extra_hover'      => array(),
				'extra_link2'      => array(),
				'extra_hover2'     => array(),
				'extra_link3'      => array(),
				'extra_hover3'     => array(),
			)
		);

		// Parameters to set order of schemes in the css
		tipsy_storage_set(
			'schemes_sorted', array(
				'color_scheme',
				'header_scheme',
				'menu_scheme',
				'sidebar_scheme',
				'footer_scheme',
			)
		);

		// Color presets
		tipsy_storage_set(
			'color_presets', array(
				'autumn' => array(
					'title'  => esc_html__( 'Autumn', 'tipsy' ),
					'colors' => array(
						'default' => array(
							'text_link'  => '#d83938',
							'text_hover' => '#f2b232',
						),
						'dark' => array(
							'text_link'  => '#d83938',
							'text_hover' => '#f2b232',
						)
					)
				),
				'green' => array(
					'title'  => esc_html__( 'Natural Green', 'tipsy' ),
					'colors' => array(
						'default' => array(
							'text_link'  => '#75ac78',
							'text_hover' => '#378e6d',
						),
						'dark' => array(
							'text_link'  => '#75ac78',
							'text_hover' => '#378e6d',
						)
					)
				),
			)
		);
	}
}


//Enqueue skin-specific scripts
if ( ! function_exists( 'tipsy_skin_upgrade_style' ) ) {
	add_action( 'wp_enqueue_scripts', 'tipsy_skin_upgrade_style', 2060 );
	function tipsy_skin_upgrade_style() {
		$tipsy_url = tipsy_get_file_url( tipsy_skins_get_current_skin_dir() . 'skin-upgrade-style.css' );	
		if ( '' != $tipsy_url ) {
			wp_enqueue_style( 'tipsy-skin-upgrade-style' . esc_attr( tipsy_skins_get_current_skin_name() ), $tipsy_url, array(), null );
		}
	}
}


// Activation methods
if ( ! function_exists( 'tipsy_skin_filter_activation_methods2' ) ) {
	add_filter( 'trx_addons_filter_activation_methods', 'tipsy_skin_filter_activation_methods2', 11, 1 );
	function tipsy_skin_filter_activation_methods2( $args ) {
		$args['elements_key'] = true;
		return $args;
	}
}
