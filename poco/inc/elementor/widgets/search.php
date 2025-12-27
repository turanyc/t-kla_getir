<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

class Poco_Elementor_Search extends Elementor\Widget_Base {
    public function get_name() {
        return 'poco-search';
    }

    public function get_title() {
        return esc_html__('Poco Search Form', 'poco');
    }

    public function get_icon() {
        return 'eicon-site-search';
    }

    public function get_categories() {
        return array('poco-addons');
    }

    protected function register_controls() {
        $this->start_controls_section(
            'search-form-style',
            [
                'label' => esc_html__('Style', 'poco'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'layout_style',
            [
                'label'   => esc_html__('Layout Style', 'poco'),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'layout-1' => esc_html__('Layout 1', 'poco'),
                    'layout-2' => esc_html__('Layout 2', 'poco'),
                ],
                'default' => 'layout-1',
            ]
        );

        $this->add_control(
            'hide_search',
            [
                'label'        => esc_html__('Hide filter categories', 'poco'),
                'type'         => Controls_Manager::SWITCHER,
                'condition'    => [
                    'layout_style' => 'layout-1',
                ],
                'prefix_class' => 'search-form-hide-search-',
            ]
        );

        $this->add_responsive_control(
            'border_width',
            [
                'label'      => esc_html__('Border width', 'poco'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors'  => [
                    '{{WRAPPER}} form input[type=search]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label'     => esc_html__('Border Color', 'poco'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} form input[type=search]' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_color_focus',
            [
                'label'     => esc_html__('Border Color Focus', 'poco'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} form input[type=search]:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_form',
            [
                'label'     => esc_html__('Background Form', 'poco'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} form input[type=search]' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius_input',
            [
                'label'      => esc_html__('Border Radius Input', 'poco'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .widget_product_search form input[type=search]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'input_padding',
            [
                'label' => esc_html__('Padding', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .widget_product_search form input[type=search]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // submit
        $this->start_controls_section(
            'search-submit-style',
            [
                'label' => esc_html__('Submit', 'poco'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'size_icon',
            [
                'label' => esc_html__('Size Icon', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget_product_search form button:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'border_radius_submit',
            [
                'label'      => esc_html__('Border Radius', 'poco'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .widget_product_search form button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'submit_padding',
            [
                'label' => esc_html__('Padding', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .widget_product_search form button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->start_controls_tabs('search_submit');
        $this->start_controls_tab(
            'search_submit_normal',
            [
                'label' => esc_html__('Normal', 'poco'),
            ]
        );
        $this->add_control(
            'submit_color',
            [
                'label' => esc_html__('Background', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .widget_product_search form button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color_form',
            [
                'label'     => esc_html__('Color Icon', 'poco'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .widget_product_search form button:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'search_submit_hover',
            [
                'label' => esc_html__('Hover', 'poco'),
            ]
        );
        $this->add_control(
            'submit_color_hover',
            [
                'label' => esc_html__('Background', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .widget_product_search form button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color_hover',
            [
                'label'     => esc_html__('Color Icon', 'poco'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}}  .widget_product_search form button:hover:before' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $hide_search = $settings['hide_search'] == 'yes' ? true : false;
        if ($settings['layout_style'] === 'layout-1'):{
            if(poco_is_woocommerce_activated()){
                poco_product_search($hide_search);
            }else{
                ?>
                <div class="site-search widget_search">
                    <?php get_search_form(); ?>
                </div>
                <?php
            }

        }
        endif;

        if ($settings['layout_style'] === 'layout-2'):{
            wp_enqueue_script('poco-search-popup');
            add_action('wp_footer', 'poco_header_search_popup', 1);
            ?>
            <div class="site-header-search">
                <a href="#" class="button-search-popup">
                    <i class="poco-icon-search"></i>
                    <span class="content"><?php echo esc_html__('Search', 'poco'); ?></span>
                </a>
            </div>
            <?php
        }
        endif;
    }
}

$widgets_manager->register(new Poco_Elementor_Search());
