<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (!poco_is_woocommerce_activated()) {
    return;
}


use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Repeater;

/**
 * Elementor Poco_Categories_Carousel
 * @since 1.0.0
 */
class Poco_Categories_Carousel extends Elementor\Widget_Base
{
    public function get_categories() {
        return array('poco-addons');
    }

    /**
     * Get widget name.
     *
     * Retrieve tabs widget name.
     *
     * @return string Widget name.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'poco-categories-carousel';
    }

    /**
     * Get widget title.
     *
     * Retrieve tabs widget title.
     *
     * @return string Widget title.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_title() {
        return esc_html__('Poco Product Categories Carousel', 'poco');
    }

    /**
     * Get widget icon.
     *
     * Retrieve tabs widget icon.
     *
     * @return string Widget icon.
     * @since  1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-slider-push';
    }

    public function get_script_depends() {
        return ['poco-elementor-categories-carousel', 'slick'];
    }

    protected function register_controls()
    {

        //Section Query
        $this->start_controls_section(
            'section_setting',
            [
                'label' => esc_html__('Categories', 'poco'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        $repeater = new Repeater();

        $repeater->add_control(
            'categories_name',
            [
                'label' => esc_html__('Alternate Name', 'poco'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $repeater->add_control(
            'categories',
            [
                'label' => esc_html__('Categories', 'poco'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true,
                'options' => $this->get_product_categories(),
                'multiple' => false,
            ]
        );

        $repeater->add_control(
            'category_image',
            [
                'label' => esc_html__('Choose Image', 'poco'),
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
                'type' => Controls_Manager::MEDIA,
                'show_label' => false,
            ]

        );

        $repeater->add_control(
            'categories_bg_color',
            [
                'label'     => esc_html__('Background Color', 'poco'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .row {{CURRENT_ITEM}} .product-cat-link' => 'background-color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'categories_list',
            [
                'label' => esc_html__('Items', 'poco'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ categories }}}',
            ]
        );
        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `brand_image_size` and `brand_image_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'categories_layout',
            [
                'label'   => esc_html__('Layout', 'poco'),
                'type'    => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => esc_html__('Layout 1', 'poco'),
                    '2' => esc_html__('Layout 2', 'poco'),
                ],
                'prefix_class' => 'elementor-categories-layout-',

            ]
        );

        $this->add_control(
            'show_total_products',
            [
                'label' => esc_html__('Show Total Products', 'poco'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'show-total-products-',
                'selectors' => [
                    '{{WRAPPER}}.show-total-products-yes .cat-total' => 'display: block',
                ],
            ]
        );

        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__('Alignment', 'poco'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'poco'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'poco'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'poco'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'prefix_class' => 'elementor-alignment-',
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}}',
                    '{{WRAPPER}} .cat-title' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'box_vertical_position',
            [
                'label'        => esc_html__('Vertical Position', 'poco'),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => [
                    'flex-start'    => [
                        'title' => esc_html__('Top', 'poco'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__('Middle', 'poco'),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Bottom', 'poco'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
                'condition'      => [
                    'categories_layout' => '1',
                ],
                'prefix_class' => 'box-valign-',
                'separator'    => 'none',
                'selectors'  => [
                    '{{WRAPPER}}.elementor-categories-layout-1 .product-cat-caption' => 'justify-content:{{VALUE}} ;',#
                ],
            ]
        );

        $this->add_responsive_control(
            'column',
            [
                'label' => esc_html__('Columns', 'poco'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 1,
                'options' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8],
            ]
        );
        $this->add_responsive_control(
            'product_gutter',
            [
                'label' => esc_html__('Gutter', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} [data-elementor-columns] .column-item' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-right: calc({{SIZE}}{{UNIT}} / 2); margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .poco-carousel .column-item' => 'padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-right: calc({{SIZE}}{{UNIT}} / 2);',
                    '{{WRAPPER}} .row' => 'margin-left: calc({{SIZE}}{{UNIT}} / -2); margin-right: calc({{SIZE}}{{UNIT}} / -2);',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'product_cate_style',
            [
                'label' => esc_html__('Box', 'poco'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'box_padding',
            [
                'label' => esc_html__('Padding', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .product-cat-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'min-height',
            [
                'label' => esc_html__('Height', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'vh'],
                'selectors' => [
                    '{{WRAPPER}} .product-cat-link' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_control(
            'box_border_radius',
            [
                'label' => esc_html__('Border Radius', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .product-cat-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->start_controls_tabs('tab_box');
        $this->start_controls_tab(
            'tab_box_normal',
            [
                'label' => esc_html__('Normal', 'poco'),
            ]
        );
        $this->add_control(
            'box_color',
            [
                'label' => esc_html__('Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .product-cat-link' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_box_hover',
            [
                'label' => esc_html__('Hover', 'poco'),
            ]
        );
        $this->add_control(
            'box_bgcolor_hover',
            [
                'label' => esc_html__('Hover Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .product-cat-link:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'caption_style',
            [
                'label' => esc_html__('Caption', 'poco'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'caption_padding',
            [
                'label' => esc_html__('Padding', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .product-cat-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'image_style',
            [
                'label' => esc_html__('Image', 'poco'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'not_hover_divider',
            [
                'label' => esc_html__('Not Hover', 'poco'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'not-hover-img-'
            ]
        );


        $this->add_responsive_control(
            'img_alignment',
            [
                'label' => esc_html__('Alignment', 'poco'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Left', 'poco'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'poco'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Right', 'poco'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'prefix_class' => 'elementor-img-alignment-',
                'selectors' => [
                    '{{WRAPPER}}.elementor-categories-layout-1 .category-product-img' => 'justify-content: {{VALUE}}',
                ],
                'condition'      => [
                    'categories_layout' => '1',
                ],
            ]
        );

        $this->add_control(
            'img_vertical_position',
            [
                'label'        => esc_html__('Vertical Position', 'poco'),
                'type'         => Controls_Manager::CHOOSE,
                'options'      => [
                    'flex-start'    => [
                        'title' => esc_html__('Top', 'poco'),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__('Middle', 'poco'),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Bottom', 'poco'),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
                'condition'      => [
                    'categories_layout' => '1',
                ],
                'prefix_class' => 'img-valign-',
                'separator'    => 'none',
                'selectors'  => [
                    '{{WRAPPER}}.elementor-categories-layout-1 .category-product-img' => 'align-items:{{VALUE}} ;',#
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .category-product-img img',
            ]
        );


        $this->add_responsive_control(
            'image_width',
            [
                'label' => esc_html__('Width', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px', 'vw'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .category-product-img img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_max_width',
            [
                'label' => esc_html__('Max Width', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px', 'vw'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .category-product-img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_height',
            [
                'label' => esc_html__('Height', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .category-product-img img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->start_controls_tabs('tabs_img_style');
        $this->start_controls_tab(
            'tab_img_normal',
            [
                'label' => esc_html__('Normal', 'poco'),
            ]
        );
        $this->add_control(
            'img_layout_1_bacground',
            [
                'label' => esc_html__('Background Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .category-product-img:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'img_layout_1_opacity',
            [
                'label' => esc_html__('Opacity', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .category-product-img:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_img_hover',
            [
                'label' => esc_html__('Hover', 'poco'),
            ]
        );

        $this->add_control(
            'img_bacground_hover',
            [
                'label' => esc_html__('Background Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-cat:hover .category-product-img:before' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'img_opacity_hover',
            [
                'label' => esc_html__('Opacity', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .product-cat:hover .category-product-img:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_control(
            'image_border_radius',
            [
                'label' => esc_html__('Border Radius', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .category-product-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .category-product-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .layout-6 .product-cat' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'image_padding',
            [
                'label' => esc_html__('Padding', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .category-product-img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_margin',
            [
                'label' => esc_html__('Margin', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .category-product-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'title_style',
            [
                'label' => esc_html__('Title', 'poco'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'show_effect_hover',
            [
                'label' => esc_html__('Show Effect Hover', 'poco'),
                'type' => Controls_Manager::SWITCHER,
                'prefix_class' => 'show-effect-hover-',
                'selectors' => [
                    '{{WRAPPER}}.show-effect-hover-yes .cat-title span:before' => 'display: block',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tilte_typography',
                'selector' => '{{WRAPPER}} .cat-title',
            ]
        );


        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .cat-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_padding',
            [
                'label' => esc_html__('Padding', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .cat-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tab_title');
        $this->start_controls_tab(
            'tab_title_normal',
            [
                'label' => esc_html__('Normal', 'poco'),
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .cat-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_title_hover',
            [
                'label' => esc_html__('Hover', 'poco'),
            ]
        );
        $this->add_control(
            'title_color_hover',
            [
                'label' => esc_html__('Hover Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .cat-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'effect_color_hover',
            [
                'label' => esc_html__('Border Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .cat-title span:before' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_effect_hover' => 'yes',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'total_style',
            [
                'label' => esc_html__('Total Products', 'poco'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_total_products' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'total_typography',
                'selector' => '{{WRAPPER}} .cat-total',
            ]
        );


        $this->add_responsive_control(
            'total_margin',
            [
                'label' => esc_html__('Margin', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .cat-total' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'total_padding',
            [
                'label' => esc_html__('Padding', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .cat-total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tab_total');
        $this->start_controls_tab(
            'tab_total_normal',
            [
                'label' => esc_html__('Normal', 'poco'),
            ]
        );
        $this->add_control(
            'total_color',
            [
                'label' => esc_html__('Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .cat-total' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->start_controls_tab(
            'tab_total_hover',
            [
                'label' => esc_html__('Hover', 'poco'),
            ]
        );
        $this->add_control(
            'total_color_hover',
            [
                'label' => esc_html__('Hover Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .cat-total:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();

        // Carousel options
        $this->add_control_carousel();
    }

    protected function get_product_categories()
    {
        $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            )
        );
        $results = array();
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $results[$category->slug] = $category->name;
            }
        }
        return $results;
    }

    /**
     * Render tabs widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['categories_list']) && is_array($settings['categories_list'])) {
            $this->add_render_attribute('wrapper', 'class', 'elementor-categories-item-wrapper');

            // Row
            $this->add_render_attribute('row', 'class', 'row');

            // Carousel
            if ( $settings['enable_carousel'] === 'yes' ) {

                $this->add_render_attribute( 'row', 'class', 'poco-carousel' );
                $carousel_settings = $this->get_carousel_settings();
                $this->add_render_attribute( 'row', 'data-settings', wp_json_encode( $carousel_settings ) );

            } else {

                $this->add_render_attribute( 'row', 'data-elementor-columns', $settings['column'] );
                if ( ! empty( $settings['column_tablet'] ) ) {
                    $this->add_render_attribute( 'row', 'data-elementor-columns-tablet', $settings['column_tablet'] );
                }
                if ( ! empty( $settings['column_mobile'] ) ) {
                    $this->add_render_attribute( 'row', 'data-elementor-columns-mobile', $settings['column_mobile'] );
                }

            }
            // Item
            $this->add_render_attribute('item', 'class', 'column-item elementor-categories-item');

            ?>
            <div <?php echo poco_elementor_get_render_attribute_string('wrapper', $this); // WPCS: XSS ok. ?>>
                <div <?php echo poco_elementor_get_render_attribute_string('row', $this); // WPCS: XSS ok. ?>>
                    <?php foreach ($settings['categories_list'] as $index => $item): ?>

                        <?php
                        $class_item = 'elementor-repeater-item-' . $item['_id'];
                        $tab_title_setting_key = $this->get_repeater_setting_key('tab_title', 'tabs', $index);
                        $this->add_render_attribute($tab_title_setting_key, ['class' => ['product-cat', $class_item],]); ?>

                        <div <?php echo poco_elementor_get_render_attribute_string('item', $this); // WPCS: XSS ok. ?>>
                            <?php if (empty($item['categories'])) {
                                echo esc_html__('Choose Category', 'poco');
                                return;
                            }
                            $category = get_term_by('slug', $item['categories'], 'product_cat');
                            if (!is_wp_error($category) && !empty($category)) {
                                if (!empty($item['category_image']['id'])) {
                                    $image = Group_Control_Image_Size::get_attachment_image_src($item['category_image']['id'], 'image', $settings);
                                } else {
                                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                                    if (!empty($thumbnail_id)) {
                                        $image = wp_get_attachment_url($thumbnail_id);
                                    } else {
                                        $image = wc_placeholder_img_src();
                                    }
                                } ?>

                                <div <?php echo poco_elementor_get_render_attribute_string($tab_title_setting_key, $this); ?>>
                                    <a class="product-cat-link" href="<?php echo esc_url(get_term_link($category)); ?>"
                                       title="<?php echo esc_attr($category->name); ?>">
                                        <div class="category-product-img">
                                            <img src="<?php echo esc_url($image); ?>"
                                                 alt="<?php echo esc_attr($category->name); ?>">
                                        </div>
                                        <div class="product-cat-caption">
                                            <div class="cat-title">
                                                <span>
                                                <?php echo empty($item['categories_name']) ? esc_html($category->name) : sprintf('%s', $item['categories_name']); ?>
                                                </span>
                                            </div>
                                            <?php if ($settings['show_total_products'] === 'yes'){ ?>
                                                <div class="cat-total">
                                                    <span class="count"><?php echo esc_html($category->count); ?></span>
                                                    <span class="text"><?php echo esc_html__('products', 'poco'); ?></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
    }

    protected function add_control_carousel($condition = array())
    {
        $this->start_controls_section(
            'section_carousel_options',
            [
                'label' => esc_html__('Carousel Options', 'poco'),
                'type' => Controls_Manager::SECTION,
                'condition' => $condition,
            ]
        );

        $this->add_control(
            'enable_carousel',
            [
                'label' => esc_html__('Enable', 'poco'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );


        $this->add_control(
            'navigation',
            [
                'label' => esc_html__('Navigation', 'poco'),
                'type' => Controls_Manager::SELECT,
                'default' => 'dots',
                'options' => [
                    'both' => esc_html__('Arrows and Dots', 'poco'),
                    'arrows' => esc_html__('Arrows', 'poco'),
                    'dots' => esc_html__('Dots', 'poco'),
                    'none' => esc_html__('None', 'poco'),
                ],
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => esc_html__('Pause on Hover', 'poco'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__('Autoplay', 'poco'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => esc_html__('Autoplay Speed', 'poco'),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
                'frontend_available' => true,
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
                ],
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label' => esc_html__('Infinite Loop', 'poco'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'enable_carousel' => 'yes'
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'carousel_arrows',
            [
                'label' => esc_html__('Carousel Arrows', 'poco'),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'enable_carousel',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'navigation',
                            'operator' => '!==',
                            'value' => 'none',
                        ],
                        [
                            'name' => 'navigation',
                            'operator' => '!==',
                            'value' => 'dots',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'style_arrow',
            [
                'label'        => esc_html__('Style Arrow', 'poco'),
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    'style-1' => esc_html__('Style 1', 'poco'),
                    'style-2' => esc_html__('Style 2', 'poco'),
                    'style-3' => esc_html__('Style 3', 'poco'),
                ],
                'default'      => 'style-1',
                'prefix_class' => 'arrow-'
            ]
        );

        //add icon next size
        $this->add_responsive_control(
            'icon_size',
            [
                'label' => esc_html__('Size', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-arrow:before' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'carousel_width',
            [
                'label' => esc_html__('Width', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px', 'vw'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'carousel_height',
            [
                'label' => esc_html__('Height', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px', 'vw'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_border_radius',
            [
                'label' => esc_html__('Border Radius', 'poco'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'color_button',
            [
                'label' => esc_html__('Color', 'poco'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->start_controls_tabs('tabs_carousel_arrow_style');

        $this->start_controls_tab(
            'tab_carousel_arrow_normal',
            [
                'label' => esc_html__('Normal', 'poco'),
            ]
        );

        $this->add_control(
            'carousel_arrow_color_icon',
            [
                'label' => esc_html__('Color icon', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_border',
            [
                'label' => esc_html__('Color Border', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_background',
            [
                'label' => esc_html__('Color background', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_arrow_hover',
            [
                'label' => esc_html__('Hover', 'poco'),
            ]
        );

        $this->add_control(
            'carousel_arrow_color_icon_hover',
            [
                'label' => esc_html__('Color icon', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_border_hover',
            [
                'label' => esc_html__('Color Border', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_arrow_color_background_hover',
            [
                'label' => esc_html__('Color background', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-slider button.slick-prev:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .slick-slider button.slick-next:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'next_heading',
            [
                'label' => esc_html__('Next button', 'poco'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'next_vertical',
            [
                'label' => esc_html__('Next Vertical', 'poco'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top', 'poco'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'poco'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ]
            ]
        );

        $this->add_responsive_control(
            'next_vertical_value',
            [
                'type' => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-next' => 'top: unset; bottom: unset; {{next_vertical.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );
        $this->add_control(
            'next_horizontal',
            [
                'label' => esc_html__('Next Horizontal', 'poco'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'poco'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'poco'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'defautl' => 'right'
            ]
        );
        $this->add_responsive_control(
            'next_horizontal_value',
            [
                'type' => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-next' => 'left: unset; right: unset;{{next_horizontal.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'prev_heading',
            [
                'label' => esc_html__('Prev button', 'poco'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'prev_vertical',
            [
                'label' => esc_html__('Prev Vertical', 'poco'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top', 'poco'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'poco'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ]
            ]
        );

        $this->add_responsive_control(
            'prev_vertical_value',
            [
                'type' => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev' => 'top: unset; bottom: unset; {{prev_vertical.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );
        $this->add_control(
            'prev_horizontal',
            [
                'label' => esc_html__('Prev Horizontal', 'poco'),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'poco'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'poco'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'defautl' => 'left'
            ]
        );
        $this->add_responsive_control(
            'prev_horizontal_value',
            [
                'type' => Controls_Manager::SLIDER,
                'show_label' => false,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => -45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-prev' => 'left: unset; right: unset; {{prev_horizontal.value}}: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'carousel_dots',
            [
                'label' => esc_html__('Carousel Dots', 'poco'),
                'conditions' => [
                    'relation' => 'and',
                    'terms' => [
                        [
                            'name' => 'enable_carousel',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                        [
                            'name' => 'navigation',
                            'operator' => '!==',
                            'value' => 'none',
                        ],
                        [
                            'name' => 'navigation',
                            'operator' => '!==',
                            'value' => 'arrows',
                        ],
                    ],
                ],
            ]
        );

        $this->start_controls_tabs('tabs_carousel_dots_style');

        $this->start_controls_tab(
            'tab_carousel_dots_normal',
            [
                'label' => esc_html__('Normal', 'poco'),
            ]
        );

        $this->add_control(
            'carousel_dots_color',
            [
                'label' => esc_html__('Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity',
            [
                'label' => esc_html__('Opacity', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_dots_hover',
            [
                'label' => esc_html__('Hover', 'poco'),
            ]
        );

        $this->add_control(
            'carousel_dots_color_hover',
            [
                'label' => esc_html__('Color Hover', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:hover:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .slick-dots li button:focus:before' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity_hover',
            [
                'label' => esc_html__('Opacity', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button:hover:before' => 'opacity: {{SIZE}};',
                    '{{WRAPPER}} .slick-dots li button:focus:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_carousel_dots_activate',
            [
                'label' => esc_html__('Activate', 'poco'),
            ]
        );

        $this->add_control(
            'carousel_dots_color_activate',
            [
                'label' => esc_html__('Color', 'poco'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li.slick-active button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_dots_opacity_activate',
            [
                'label' => esc_html__('Opacity', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li.slick-active button:before' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'dots_vertical_value',
            [
                'label' => esc_html__('Spacing', 'poco'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -100,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.dots-style-2 .slick-dots' => 'margin-top: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'Alignment_text',
            [
                'label' => esc_html__('Alignment text', 'poco'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'poco'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'poco'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'poco'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .slick-dots' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();
    }

    protected function get_carousel_settings() {
        $settings = $this->get_settings_for_display();

        return array(
            'navigation'         => $settings['navigation'],
            'autoplayHoverPause' => $settings['pause_on_hover'] === 'yes' ? true : false,
            'autoplay'           => $settings['autoplay'] === 'yes' ? true : false,
            'autoplaySpeed'      => $settings['autoplay_speed'],
            'items'              => $settings['column'],
            'items_tablet'       => $settings['column_tablet'] ? $settings['column_tablet'] : $settings['column'],
            'items_mobile'       => $settings['column_mobile'] ? $settings['column_mobile'] : 1,
            'loop'               => $settings['infinite'] === 'yes' ? true : false,
            'rtl'                => is_rtl() ? true : false,
        );
    }
}
$widgets_manager->register(new Poco_Categories_Carousel());
