<?php

class Poco_Merlin_Config {
	private $config = [];

	private $wizard;

	public function __construct() {
		$this->init();

		add_action('merlin_import_files', [$this, 'import_files']);
		add_action('merlin_after_all_import', [$this, 'after_import_setup'], 10, 1);
		add_filter('merlin_generate_child_functions_php', [$this, 'render_child_functions_php']);

        add_action( 'admin_post_custom_setup_data', [$this, 'custom_setup_data' ]);
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'), 10);

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action('import_start', function () {
            add_filter('wxr_importer.pre_process.post_meta', [$this, 'fiximport_elementor'], 10, 1);
        });
	}

	public function fiximport_elementor($post_meta) {
        if ('_elementor_data' === $post_meta['key']) {
            $post_meta['value'] = wp_slash($post_meta['value']);
        }

        return $post_meta;
    }

	public function admin_scripts() {
        global $poco_version;
        wp_enqueue_script('poco-admin-script', get_template_directory_uri() . '/assets/js/admin/admin.js', array('jquery'), $poco_version, true);
    }

	private function init() {
		$this->wizard = new Merlin(
			$config = array(
				// Location / directory where Merlin WP is placed in your theme.
				'merlin_url'         => 'merlin',
				// The wp-admin page slug where Merlin WP loads.
				'parent_slug'        => 'themes.php',
				// The wp-admin parent page slug for the admin menu item.
				'capability'         => 'manage_options',
				// The capability required for this menu to be displayed to the user.
				'dev_mode'           => true,
				// Enable development mode for testing.
				'license_step'       => false,
				// EDD license activation step.
				'license_required'   => false,
				// Require the license activation step.
				'license_help_url'   => '',
				// URL for the 'license-tooltip'.
				'edd_remote_api_url' => '',
				'directory'          => 'inc/merlin',
				// EDD_Theme_Updater_Admin remote_api_url.
				'edd_item_name'      => '',
				// EDD_Theme_Updater_Admin item_name.
				'edd_theme_slug'     => '',
				// EDD_Theme_Updater_Admin item_slug.
			),
			$strings = array(
				'admin-menu'          => esc_html__('Theme Setup', 'poco'),

				/* translators: 1: Title Tag 2: Theme Name 3: Closing Title Tag */
				'title%s%s%s%s'       => esc_html__('%1$s%2$s Themes &lsaquo; Theme Setup: %3$s%4$s', 'poco'),
				'return-to-dashboard' => esc_html__('Return to the dashboard', 'poco'),
				'ignore'              => esc_html__('Disable this wizard', 'poco'),

				'btn-skip'                 => esc_html__('Skip', 'poco'),
				'btn-next'                 => esc_html__('Next', 'poco'),
				'btn-start'                => esc_html__('Start', 'poco'),
				'btn-no'                   => esc_html__('Cancel', 'poco'),
				'btn-plugins-install'      => esc_html__('Install', 'poco'),
				'btn-child-install'        => esc_html__('Install', 'poco'),
				'btn-content-install'      => esc_html__('Install', 'poco'),
				'btn-import'               => esc_html__('Import', 'poco'),
				'btn-license-activate'     => esc_html__('Activate', 'poco'),
				'btn-license-skip'         => esc_html__('Later', 'poco'),

				/* translators: Theme Name */
				'license-header%s'         => esc_html__('Activate %s', 'poco'),
				/* translators: Theme Name */
				'license-header-success%s' => esc_html__('%s is Activated', 'poco'),
				/* translators: Theme Name */
				'license%s'                => esc_html__('Enter your license key to enable remote updates and theme support.', 'poco'),
				'license-label'            => esc_html__('License key', 'poco'),
				'license-success%s'        => esc_html__('The theme is already registered, so you can go to the next step!', 'poco'),
				'license-json-success%s'   => esc_html__('Your theme is activated! Remote updates and theme support are enabled.', 'poco'),
				'license-tooltip'          => esc_html__('Need help?', 'poco'),

				/* translators: Theme Name */
				'welcome-header%s'         => esc_html__('Welcome to %s', 'poco'),
				'welcome-header-success%s' => esc_html__('Hi. Welcome back', 'poco'),
				'welcome%s'                => esc_html__('This wizard will set up your theme, install plugins, and import content. It is optional & should take only a few minutes.', 'poco'),
				'welcome-success%s'        => esc_html__('You may have already run this theme setup wizard. If you would like to proceed anyway, click on the "Start" button below.', 'poco'),

				'child-header'         => esc_html__('Install Child Theme', 'poco'),
				'child-header-success' => esc_html__('You\'re good to go!', 'poco'),
				'child'                => esc_html__('Let\'s build & activate a child theme so you may easily make theme changes.', 'poco'),
				'child-success%s'      => esc_html__('Your child theme has already been installed and is now activated, if it wasn\'t already.', 'poco'),
				'child-action-link'    => esc_html__('Learn about child themes', 'poco'),
				'child-json-success%s' => esc_html__('Awesome. Your child theme has already been installed and is now activated.', 'poco'),
				'child-json-already%s' => esc_html__('Awesome. Your child theme has been created and is now activated.', 'poco'),

				'plugins-header'         => esc_html__('Install Plugins', 'poco'),
				'plugins-header-success' => esc_html__('You\'re up to speed!', 'poco'),
				'plugins'                => esc_html__('Let\'s install some essential WordPress plugins to get your site up to speed.', 'poco'),
				'plugins-success%s'      => esc_html__('The required WordPress plugins are all installed and up to date. Press "Next" to continue the setup wizard.', 'poco'),
				'plugins-action-link'    => esc_html__('Advanced', 'poco'),

				'import-header'      => esc_html__('Import Content', 'poco'),
				'import'             => esc_html__('Let\'s import content to your website, to help you get familiar with the theme.', 'poco'),
				'import-action-link' => esc_html__('Advanced', 'poco'),

				'ready-header'      => esc_html__('All done. Have fun!', 'poco'),

				/* translators: Theme Author */
				'ready%s'           => esc_html__('Your theme has been all set up. Enjoy your new theme by %s.', 'poco'),
				'ready-action-link' => esc_html__('Extras', 'poco'),
				'ready-big-button'  => esc_html__('View your website', 'poco'),
				'ready-link-1'      => sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://wordpress.org/support/', esc_html__('Explore WordPress', 'poco')),
				'ready-link-2'      => sprintf('<a href="%1$s" target="_blank">%2$s</a>', 'https://themebeans.com/contact/', esc_html__('Get Theme Support', 'poco')),
				'ready-link-3'      => sprintf('<a href="%1$s">%2$s</a>', admin_url('customize.php'), esc_html__('Start Customizing', 'poco')),
			)
		);

		add_action('widgets_init', [$this, 'widgets_init']);
	}

	public function import_files(){
            return array(
                array(
					'import_file_name'           => 'home 1',
					'home'                       => 'home-1',
					'local_import_file'          => get_theme_file_path('/dummy-data/content.xml'),
					'homepage'                   => get_theme_file_path('/dummy-data/homepage/home-1.xml'),
					'local_import_widget_file'   => get_theme_file_path('/dummy-data/widgets.json'),
					'local_import_redux'         => array(
						array(
							'file_path'   => get_theme_file_path('/dummy-data/redux.json'),
							'option_name' => 'poco_options',
						),
					),
					
					'import_preview_image_url'   => get_theme_file_uri('/assets/images/oneclick/home_1.jpg'),
					'import_notice'              => esc_html__( 'After you import this demo, you will have to setup the slider separately.', 'poco' ),
					'preview_url'                => 'https://demo2.pavothemes.com/poco/home-1',
				),

                array(
					'import_file_name'           => 'home 2',
					'home'                       => 'home-2',
					'local_import_file'          => get_theme_file_path('/dummy-data/content.xml'),
					'homepage'                   => get_theme_file_path('/dummy-data/homepage/home-2.xml'),
					'local_import_widget_file'   => get_theme_file_path('/dummy-data/widgets.json'),
					'local_import_redux'         => array(
						array(
							'file_path'   => get_theme_file_path('/dummy-data/redux.json'),
							'option_name' => 'poco_options',
						),
					),
					
					'import_preview_image_url'   => get_theme_file_uri('/assets/images/oneclick/home_2.jpg'),
					'import_notice'              => esc_html__( 'After you import this demo, you will have to setup the slider separately.', 'poco' ),
					'preview_url'                => 'https://demo2.pavothemes.com/poco/home-2',
				),

                array(
					'import_file_name'           => 'home 3',
					'home'                       => 'home-3',
					'local_import_file'          => get_theme_file_path('/dummy-data/content.xml'),
					'homepage'                   => get_theme_file_path('/dummy-data/homepage/home-3.xml'),
					'local_import_widget_file'   => get_theme_file_path('/dummy-data/widgets.json'),
					'local_import_redux'         => array(
						array(
							'file_path'   => get_theme_file_path('/dummy-data/redux.json'),
							'option_name' => 'poco_options',
						),
					),
					
					'import_preview_image_url'   => get_theme_file_uri('/assets/images/oneclick/home_3.jpg'),
					'import_notice'              => esc_html__( 'After you import this demo, you will have to setup the slider separately.', 'poco' ),
					'preview_url'                => 'https://demo2.pavothemes.com/poco/home-3',
				),

                array(
					'import_file_name'           => 'home 4',
					'home'                       => 'home-4',
					'local_import_file'          => get_theme_file_path('/dummy-data/content.xml'),
					'homepage'                   => get_theme_file_path('/dummy-data/homepage/home-4.xml'),
					'local_import_widget_file'   => get_theme_file_path('/dummy-data/widgets.json'),
					'local_import_redux'         => array(
						array(
							'file_path'   => get_theme_file_path('/dummy-data/redux.json'),
							'option_name' => 'poco_options',
						),
					),
					
					'import_preview_image_url'   => get_theme_file_uri('/assets/images/oneclick/home_4.jpg'),
					'import_notice'              => esc_html__( 'After you import this demo, you will have to setup the slider separately.', 'poco' ),
					'preview_url'                => 'https://demo2.pavothemes.com/poco/home-4',
				),

                array(
					'import_file_name'           => 'home 5',
					'home'                       => 'home-5',
					'local_import_file'          => get_theme_file_path('/dummy-data/content.xml'),
					'homepage'                   => get_theme_file_path('/dummy-data/homepage/home-5.xml'),
					'local_import_widget_file'   => get_theme_file_path('/dummy-data/widgets.json'),
					'local_import_redux'         => array(
						array(
							'file_path'   => get_theme_file_path('/dummy-data/redux.json'),
							'option_name' => 'poco_options',
						),
					),
					'import_rev_slider_file_url' => 'http://source.wpopal.com/poco/dummy_data/revsliders/home-5/slider-home5.zip',
                'import_more_revslider_file_url' => [],
					'import_preview_image_url'   => get_theme_file_uri('/assets/images/oneclick/home_5.jpg'),
					'import_notice'              => esc_html__( 'After you import this demo, you will have to setup the slider separately.', 'poco' ),
					'preview_url'                => 'https://demo2.pavothemes.com/poco/home-5',
				),

                array(
					'import_file_name'           => 'home 6',
					'home'                       => 'home-6',
					'local_import_file'          => get_theme_file_path('/dummy-data/content.xml'),
					'homepage'                   => get_theme_file_path('/dummy-data/homepage/home-6.xml'),
					'local_import_widget_file'   => get_theme_file_path('/dummy-data/widgets.json'),
					'local_import_redux'         => array(
						array(
							'file_path'   => get_theme_file_path('/dummy-data/redux.json'),
							'option_name' => 'poco_options',
						),
					),
					'import_rev_slider_file_url' => 'http://source.wpopal.com/poco/dummy_data/revsliders/home-6/slider-home6.zip',
                'import_more_revslider_file_url' => [],
					'import_preview_image_url'   => get_theme_file_uri('/assets/images/oneclick/home_6.jpg'),
					'import_notice'              => esc_html__( 'After you import this demo, you will have to setup the slider separately.', 'poco' ),
					'preview_url'                => 'https://demo2.pavothemes.com/poco/home-6',
				),

                array(
					'import_file_name'           => 'home 7',
					'home'                       => 'home-7',
					'local_import_file'          => get_theme_file_path('/dummy-data/content.xml'),
					'homepage'                   => get_theme_file_path('/dummy-data/homepage/home-7.xml'),
					'local_import_widget_file'   => get_theme_file_path('/dummy-data/widgets.json'),
					'local_import_redux'         => array(
						array(
							'file_path'   => get_theme_file_path('/dummy-data/redux.json'),
							'option_name' => 'poco_options',
						),
					),
					'import_rev_slider_file_url' => 'http://source.wpopal.com/poco/dummy_data/revsliders/home-7/slider-home7.zip',
                'import_more_revslider_file_url' => [],
					'import_preview_image_url'   => get_theme_file_uri('/assets/images/oneclick/home_7.jpg'),
					'import_notice'              => esc_html__( 'After you import this demo, you will have to setup the slider separately.', 'poco' ),
					'preview_url'                => 'https://demo2.pavothemes.com/poco/home-7',
				),

                array(
					'import_file_name'           => 'home 8',
					'home'                       => 'home-8',
					'local_import_file'          => get_theme_file_path('/dummy-data/content.xml'),
					'homepage'                   => get_theme_file_path('/dummy-data/homepage/home-8.xml'),
					'local_import_widget_file'   => get_theme_file_path('/dummy-data/widgets.json'),
					'local_import_redux'         => array(
						array(
							'file_path'   => get_theme_file_path('/dummy-data/redux.json'),
							'option_name' => 'poco_options',
						),
					),
					'import_rev_slider_file_url' => 'http://source.wpopal.com/poco/dummy_data/revsliders/home-8/slider_home8.zip',
                'import_more_revslider_file_url' => [],
					'import_preview_image_url'   => get_theme_file_uri('/assets/images/oneclick/home_8.jpg'),
					'import_notice'              => esc_html__( 'After you import this demo, you will have to setup the slider separately.', 'poco' ),
					'preview_url'                => 'https://demo2.pavothemes.com/poco/home-8',
				),
            );           
        }

	public function after_import_setup($selected_import) {
		$selected_import = ($this->import_files())[$selected_import];
		$check_oneclick  = get_option('poco_check_oneclick', []);

		$this->set_demo_menus();
		wp_delete_post(1, true);

//		 setup Home page
//		if (!isset($check_oneclick[$selected_import['home']])) {
//            $this->wizard->importer->import(get_parent_theme_file_path('dummy-data/homepage/' . $selected_import['home'] . '.xml'));
//            $check_oneclick[$selected_import['home']] = true;
//        }

		$home = get_page_by_path($selected_import['home']);

		if ($home) {
			update_option('show_on_front', 'page');
			update_option('page_on_front', $home->ID);
		}

		// Setup Options
		$options = $this->get_all_options();
		// Elementor
		$active_kit_id = Elementor\Plugin::$instance->kits_manager->get_active_id();
		update_post_meta($active_kit_id, '_elementor_page_settings', $options['elementor']);

		$this->setup_header_footer($selected_import['home']);


		$this->license_elementor_pro();

		 $this->update_nav_menu_item();
        $this->remove_quick_table_enable();
		update_option('poco_check_oneclick', $check_oneclick);

	}
	//remove quick_table_enable
    private function remove_quick_table_enable() {
        $qte = get_option('woosc_settings');
        if ($qte) {
            if ($qte['quick_table_enable'] == 'yes') {
                $qte['quick_table_enable'] = 'no';
                update_option('woosc_settings', $qte);
            }
        } else {
            $qte                       = array();
            $qte['quick_table_enable'] = 'no';
            add_option('woosc_settings', $qte);
        }

    }
	private function update_nav_menu_item() {
        $params = array(
            'posts_per_page' => -1,
            'post_type'      => [
                'nav_menu_item',
            ],
        );
        $query  = new WP_Query($params);
        while ($query->have_posts()): $query->the_post();
            wp_update_post(array(
                // Update the `nav_menu_item` Post Title
                'ID'         => get_the_ID(),
                'post_title' => get_the_title()
            ));
        endwhile;

    }

	public function render_child_functions_php() {
		$output
			= "<?php
/**
 * Theme functions and definitions.
 */

		 ";

		return $output;
	}

	public function widgets_init() {
		require_once get_parent_theme_file_path('/inc/merlin/includes/recent-post.php');
		register_widget('Poco_WP_Widget_Recent_Posts');
		if (poco_is_woocommerce_activated()) {
			require_once get_parent_theme_file_path('/inc/merlin/includes/class-wc-widget-layered-nav.php');
			register_widget('Poco_Widget_Layered_Nav');
		}
	}

	private function setup_header_footer($id) {
		$this->reset_header_footer();
		$options = ($this->get_all_header_footer())[$id];
		foreach ($options['header'] as $header_options) {
			$header = get_page_by_path($header_options['slug'], OBJECT, 'elementor_library');
			if ($header) {
				update_post_meta($header->ID, '_elementor_conditions', $header_options['conditions']);
			}
		}

		foreach ($options['footer'] as $footer_options) {
			$footer = get_page_by_path($footer_options['slug'], OBJECT, 'elementor_library');
			if ($footer) {
				update_post_meta($footer->ID, '_elementor_conditions', $footer_options['conditions']);
			}
		}

		$cache = new ElementorPro\Modules\ThemeBuilder\Classes\Conditions_Cache();
		$cache->regenerate();
	}

	private function get_all_header_footer() {
		return [
			'home-1' => [
				'header' => [
					[
						'slug'       => 'headerbuilder-1',
						'conditions' => ['include/general'],
					]
				],
				'footer' => [
					[
						'slug'       => 'footerbuilder-1',
						'conditions' => ['include/general'],
					]
				]
			],
			'home-2' => [
				'header' => [
					[
						'slug'       => 'headerbuilder-1',
						'conditions' => ['include/general'],
					]
				],
				'footer' => [
					[
						'slug'       => 'footerbuilder-2',
						'conditions' => ['include/general'],
					]
				]
			],
			'home-3' => [
				'header' => [
					[
						'slug'       => 'headerbuilder-2',
						'conditions' => ['include/general'],
					]
				],
				'footer' => [
					[
						'slug'       => 'footerbuilder-1',
						'conditions' => ['include/general'],
					]
				]
			],
			'home-4' => [
				'header' => [
					[
						'slug'       => 'headerbuilder-3',
						'conditions' => ['include/general'],
					]
				],
				'footer' => [
					[
						'slug'       => 'footerbuilder-3',
						'conditions' => ['include/general'],
					]
				]
			],
			'home-5' => [
				'header' => [
					[
						'slug'       => 'headerbuilder-4',
						'conditions' => ['include/general'],
					]
				],
				'footer' => [
					[
						'slug'       => 'footerbuilder-4',
						'conditions' => ['include/general'],
					]
				]
			],
			'home-6' => [
				'header' => [
					[
						'slug'       => 'headerbuilder-5',
						'conditions' => ['include/general'],
					]
				],
				'footer' => [
					[
						'slug'       => 'footerbuilder-5',
						'conditions' => ['include/general'],
					]
				]
			],
			'home-7' => [
				'header' => [
					[
						'slug'       => 'headerbuilder-6',
						'conditions' => ['include/general'],
					]
				],
				'footer' => [
					[
						'slug'       => 'footerbuilder-6',
						'conditions' => ['include/general'],
					]
				]
			],
			'home-8' => [
				'header' => [
					[
						'slug'       => 'headerbuilder-7',
						'conditions' => ['include/general'],
					]
				],
				'footer' => [
					[
						'slug'       => 'footerbuilder-7',
						'conditions' => ['include/general'],
					]
				]
			],
		];
	}

	private function reset_header_footer() {
		$footer_args = array(
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_elementor_template_type',
					'compare' => 'IN',
					'value'   => ['footer', 'header']
				),
			)
		);
		$footer      = new WP_Query($footer_args);
		while ($footer->have_posts()) : $footer->the_post();
			update_post_meta(get_the_ID(), '_elementor_conditions', []);
		endwhile;
		wp_reset_postdata();
	}

	private function license_elementor_pro() {
		if (defined('ELEMENTOR_PRO_VERSION')) {
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
                'features' => []
			];
			update_option('elementor_pro_license_key', 'Licence Activate');
			ElementorPro\License\API::set_license_data($data, '+2 years');
		}
	}

	public function get_all_options(){
            $options = [];
            $options['elementor'] = json_decode('{"default_generic_fonts":"Sans-serif","container_width":{"unit":"px","size":"1290","sizes":[]},"viewport_md":"","viewport_lg":"","system_colors":[{"_id":"primary","title":"Primary","color":"#FFC222"},{"_id":"secondary","title":"Secondary","color":"#00A149"},{"_id":"text","title":"Body","color":"#808080"},{"_id":"accent","title":"Heading","color":"#1E1D23"}],"custom_colors":[{"_id":"1d12e908","title":"Light","color":"#999999"},{"_id":"76261577","title":"Color Border","color":"#E5E5E5"},{"_id":"74ad7d3c","title":"Saved Color #3","color":"#7A7A7A"},{"_id":"2154a27d","title":"Saved Color #4","color":"#61CE70"},{"_id":"1401abc0","title":"Saved Color #5","color":"#4054B2"},{"_id":"5bdd244e","title":"Saved Color #6","color":"#23A455"},{"_id":"48e5bc8c","title":"Saved Color #7","color":"#000"},{"_id":"26c59e90","title":"Saved Color #8","color":"#FFF"}],"system_typography":[{"_id":"primary","title":"Primary Headline","typography_typography":"custom"},{"_id":"secondary","title":"Secondary Headline","typography_typography":"custom"},{"_id":"text","title":"Body Text","typography_typography":"custom"},{"_id":"accent","title":"Accent Text","typography_typography":"custom"}],"site_name":"poco","site_description":"Just another WordPress site","page_title_selector":"h1.entry-title","activeItemIndex":1,"custom_typography":[],"__globals__":{"button_typography_typography":"","button_text_color":"globals/colors?id=accent","button_background_color":""},"button_background_color":"#FFC222","button_border_radius":{"unit":"px","top":"8","right":"8","bottom":"8","left":"8","isLinked":true},"button_hover_text_color":"#FFFFFF","button_padding":{"unit":"px","top":"20","right":"45","bottom":"20","left":"45","isLinked":false},"button_typography_typography":"custom","button_typography_text_transform":"uppercase","button_typography_line_height":{"unit":"em","size":1,"sizes":[]},"button_typography_font_size":{"unit":"px","size":14,"sizes":[]},"button_hover_background_color":"#EEAC00","button_typography_font_weight":"700","viewport_mobile":"","viewport_tablet":""}', true);
            
            return $options;
        }

	public function set_demo_menus() {
		$main_menu = get_term_by('name', 'Main Menu', 'nav_menu');

		set_theme_mod(
			'nav_menu_locations',
			array(
				'primary'  => $main_menu->term_id,
				'handheld' => $main_menu->term_id,
			)
		);
	}

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
            add_options_page(
            'Custom Setup Theme',
            'Custom Setup Theme',
            'manage_options',
            'custom-setup-settings',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option('poco_options_setup');

        $header_data = $this->get_data_elementor_template('header');
        $footer_data = $this->get_data_elementor_template('footer');

        $profile = $this->get_all_header_footer();

        $homepage = [];
        foreach ($profile as $key=>$value){
            $homepage[$key] = ucfirst( str_replace('-', ' ', $key) );
        }
        ?>
        <div class="wrap">
        <h1><?php esc_html_e('Custom Setup Themes', 'poco') ?></h1>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <table class="form-table">
                <tr>
                    <th>
                        <label><?php esc_html_e('Setup Themes', 'poco') ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <ul>
                                <li>
                                    <label><?php esc_html_e('Setup Theme', 'poco') ?>:
                                        <select name="setup-theme">
                                            <option value="profile" selected>Select Profile</option>
                                             <option value="custom_theme">Custom Header and Footer</option>
                                        </select>
                                    </label>
                                </li>
                                <li class="profile setup-theme">
                                    <label><?php esc_html_e('Profile', 'poco') ?>:
                                        <select name="opal-data-home">
                                            <option value="" selected> Select Profile</option>
                                            <?php foreach ($homepage as $id => $home) { ?>
                                                <option value="<?php echo esc_attr($id); ?>">
                                                    <?php echo esc_attr($home); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </label>
                                </li>
                                <li class="custom_theme setup-theme">
                                    <label><?php esc_html_e('Header', 'poco') ?>:
                                        <select name="header">
                                            <option value="" selected>Select Header</option>
                                            <?php foreach ($header_data as $id => $header) { ?>
                                                <option value="<?php echo esc_attr($id); ?>">
                                                    <?php echo esc_attr($header); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </label>
                                </li>
                                <li class="custom_theme setup-theme">
                                    <label><?php esc_html_e('Footer', 'poco') ?>:
                                        <select name="footer">
                                            <option value="" selected >Select Footer</option>
                                            <?php foreach ($footer_data as $id => $footer) { ?>
                                                <option value="<?php echo esc_attr($id); ?>">
                                                    <?php echo esc_attr($footer); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </label>
                                </li>
                                <li>
                                    <input type="checkbox" id="update_elementor" name="opal-setup-data-elementor" value="1">
                                    <label><?php esc_html_e('Update Elementor Content', 'poco') ?></label>
                                </li>
                                <li>
                                    <input type="checkbox" id="update_elementor" name="opal-setup-data-elementor-options" value="1">
                                    <label><?php esc_html_e('Update Elementor Options', 'poco') ?></label>
                                </li>
                            </ul>
                        </fieldset>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="action" value="custom_setup_data">
            <?php submit_button(esc_html('Setup Now!')); ?>
        </form>
        <?php  if (isset($_GET['saved'])) { ?>
            <div class="updated">
                <p><?php esc_html_e('Success! Have been setup for your website', 'poco'); ?></p>
            </div>
        <?php }
    }

    private function get_data_elementor_template($type){
        $args = array(
            'post_type'      => 'elementor_library',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_elementor_template_type',
                    'compare' => '=',
                    'value'   => $type
                ),
            )
        );
        $data = new WP_Query($args);
        $select_data = [];
        while ($data->have_posts()): $data->the_post();
            $select_data[get_the_ID()] = get_the_title();
        endwhile;
        wp_reset_postdata();

        return $select_data;
    }

    private function reset_elementor_conditions($type) {
		$args = array(
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_elementor_template_type',
					'compare' => '=',
					'value'   => $type
				),
			)
		);
		$query = new WP_Query($args);
		while ($query->have_posts()) : $query->the_post();
			update_post_meta(get_the_ID(), '_elementor_conditions', []);
		endwhile;
		wp_reset_postdata();
	}

    public function custom_setup_data(){
        if(isset($_POST)){

            if(isset($_POST['setup-theme'])){
                if( $_POST['setup-theme'] == 'profile'){
                    if (isset($_POST['opal-data-home']) && !empty($_POST['opal-data-home'])) {
                        $home = (isset($_POST['opal-data-home']) && $_POST['opal-data-home']) ? $_POST['opal-data-home'] : 'home-1';
                         $this->reset_elementor_conditions('header');
                         $this->reset_elementor_conditions('footer');
                        $this->setup_header_footer($home);
                    }
                }else{

                     if(isset($_POST['header']) && !empty($_POST['header'])){
                        $header = $_POST['header'];
                        $this->reset_elementor_conditions('header');
                        update_post_meta($header, '_elementor_conditions', ['include/general']);

                    }

                    if(isset($_POST['footer']) && !empty($_POST['footer'])){
                        $footer= $_POST['footer'];
                        $this->reset_elementor_conditions('footer');
                        update_post_meta($footer, '_elementor_conditions', ['include/general']);
                    }

                }

            }

            if (isset($_POST['opal-setup-data-elementor-options'])) {
                $options = $this->get_all_options();
                // Elementor
                $active_kit_id = Elementor\Plugin::$instance->kits_manager->get_active_id();
                update_post_meta($active_kit_id, '_elementor_page_settings', $options['elementor']);

                $cache = new ElementorPro\Modules\ThemeBuilder\Classes\Conditions_Cache();
                $cache->regenerate();

                Elementor\Plugin::$instance->files_manager->clear_cache();
            }



            wp_redirect(admin_url('options-general.php?page=custom-setup-settings&saved=1'));
            exit;
        }
    }
}

return new Poco_Merlin_Config();
