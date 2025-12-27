<?php
/**
 * Checks if the current page is a product archive
 *
 * @return boolean
 */
function poco_is_product_archive() {
	if (is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag()) {
		return true;
	} else {
		return false;
	}
}

/**
 * @param $product WC_Product
 */
function poco_product_get_image($product) {
	return $product->get_image();
}

/**
 * @param $product WC_Product
 */
function poco_product_get_price_html($product) {
	return $product->get_price_html();
}

/**
 * Retrieves the previous product.
 *
 * @param bool $in_same_term Optional. Whether post should be in a same taxonomy term. Default false.
 * @param array|string $excluded_terms Optional. Comma-separated list of excluded term IDs. Default empty.
 * @param string $taxonomy Optional. Taxonomy, if $in_same_term is true. Default 'product_cat'.
 * @return WC_Product|false Product object if successful. False if no valid product is found.
 * @since 2.4.3
 *
 */
function poco_get_previous_product($in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat') {
	$product = new Poco_WooCommerce_Adjacent_Products($in_same_term, $excluded_terms, $taxonomy, true);
	return $product->get_product();
}

/**
 * Retrieves the next product.
 *
 * @param bool $in_same_term Optional. Whether post should be in a same taxonomy term. Default false.
 * @param array|string $excluded_terms Optional. Comma-separated list of excluded term IDs. Default empty.
 * @param string $taxonomy Optional. Taxonomy, if $in_same_term is true. Default 'product_cat'.
 * @return WC_Product|false Product object if successful. False if no valid product is found.
 * @since 2.4.3
 *
 */
function poco_get_next_product($in_same_term = false, $excluded_terms = '', $taxonomy = 'product_cat') {
	$product = new Poco_WooCommerce_Adjacent_Products($in_same_term, $excluded_terms, $taxonomy);
	return $product->get_product();
}


function poco_is_woocommerce_extension_activated($extension = 'WC_Bookings') {
	if ($extension == 'YITH_WCQV') {
		return class_exists($extension) && class_exists('YITH_WCQV_Frontend') ? true : false;
	}

	return class_exists($extension) ? true : false;
}

function osf_woocommerce_pagination_args($args) {
	$args['prev_text'] = '<i class="poco-icon poco-icon-angle-left"></i><span>' . __('PREVIOUS', 'poco') . '</span>';
	$args['next_text'] = '<span>' . __('NEXT', 'poco') . '</span><i class="poco-icon poco-icon-angle-right"></i>';
	return $args;
}

add_filter('woocommerce_pagination_args', 'osf_woocommerce_pagination_args', 10, 1);

if (!function_exists('wvs_get_wc_attribute_taxonomy')) {
	function wvs_get_wc_attribute_taxonomy($attribute_name) {

		$transient_name = sprintf('wvs_attribute_taxonomy_%s', $attribute_name);

		$cache = new Woo_Variation_Swatches_Cache($transient_name, 'wvs_attribute_taxonomy');
		if (isset($_GET['wvs_clear_transient'])) {
			$cache->delete_transient();
		}

		if (false === ($attribute_taxonomy = $cache->get_transient())) {

			global $wpdb;

			$attribute_name = str_replace('pa_', '', wc_sanitize_taxonomy_name($attribute_name));

			$attribute_taxonomy = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name='{$attribute_name}'");

			$cache->set_transient($attribute_taxonomy);
		}

		return apply_filters('wvs_get_wc_attribute_taxonomy', $attribute_taxonomy, $attribute_name);
	}
}

if (!function_exists('wvs_taxonomy_meta_fields')) {
	function wvs_taxonomy_meta_fields($field_id = false) {

		$fields = array();

		$fields['color'] = array(
			array(
				'label' => esc_html__('Color', 'poco'), // <label>
				'desc'  => esc_html__('Choose a color', 'poco'), // description
				'id'    => 'product_attribute_color', // name of field
				'type'  => 'color'
			)
		);

		$fields['image'] = array(
			array(
				'label' => esc_html__('Image', 'poco'), // <label>
				'desc'  => esc_html__('Choose an Image', 'poco'), // description
				'id'    => 'product_attribute_image', // name of field
				'type'  => 'image'
			)
		);

		$fields = apply_filters('wvs_product_taxonomy_meta_fields', $fields);

		if ($field_id) {
			return isset($fields[$field_id]) ? $fields[$field_id] : array();
		}

		return $fields;

	}
}

class Poco_Custom_Walker_Category extends Walker_Category {

	public function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {
		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters(
			'list_cats',
			esc_attr($category->name),
			$category
		);

		// Don't generate an element if the category name is empty.
		if (!$cat_name) {
			return;
		}

		$link = '<a class="pf-value" href="' . esc_url(get_term_link($category)) . '" data-val="' . esc_attr($category->slug) . '" data-title="' . esc_attr($category->name) . '" ';
		if ($args['use_desc_for_title'] && !empty($category->description)) {
			/**
			 * Filters the category description for display.
			 *
			 * @param string $description Category description.
			 * @param object $category Category object.
			 *
			 * @since 1.2.0
			 *
			 */
			$link .= 'title="' . esc_attr(strip_tags(apply_filters('category_description', $category->description, $category))) . '"';
		}

		$link .= '>';
		$link .= $cat_name . '</a>';

		if (!empty($args['feed_image']) || !empty($args['feed'])) {
			$link .= ' ';

			if (empty($args['feed_image'])) {
				$link .= '(';
			}

			$link .= '<a href="' . esc_url(get_term_feed_link($category->term_id, $category->taxonomy, $args['feed_type'])) . '"';

			if (empty($args['feed'])) {
				$alt = ' alt="' . sprintf(esc_html__('Feed for all posts filed under %s', 'poco'), $cat_name) . '"';
			} else {
				$alt  = ' alt="' . $args['feed'] . '"';
				$name = $args['feed'];
				$link .= empty($args['title']) ? '' : $args['title'];
			}

			$link .= '>';

			if (empty($args['feed_image'])) {
				$link .= $name;
			} else {
				$link .= "<img src='" . $args['feed_image'] . "'$alt" . ' />';
			}
			$link .= '</a>';

			if (empty($args['feed_image'])) {
				$link .= ')';
			}
		}

		if (!empty($args['show_count'])) {
			$link .= ' (' . number_format_i18n($category->count) . ')';
		}
		if ('list' == $args['style']) {
			$output      .= "\t<li";
			$css_classes = array(
				'cat-item',
				'cat-item-' . $category->term_id,
			);

			if (!empty($args['current_category'])) {
				// 'current_category' can be an array, so we use `get_terms()`.
				$_current_terms = get_terms(
					$category->taxonomy,
					array(
						'include'    => $args['current_category'],
						'hide_empty' => false,
					)
				);

				foreach ($_current_terms as $_current_term) {
					if ($category->term_id == $_current_term->term_id) {
						$css_classes[] = 'current-cat pf-active';
					} elseif ($category->term_id == $_current_term->parent) {
						$css_classes[] = 'current-cat-parent';
					}
					while ($_current_term->parent) {
						if ($category->term_id == $_current_term->parent) {
							$css_classes[] = 'current-cat-ancestor';
							break;
						}
						$_current_term = get_term($_current_term->parent, $category->taxonomy);
					}
				}
			}

			/**
			 * Filters the list of CSS classes to include with each category in the list.
			 *
			 * @param array $css_classes An array of CSS classes to be applied to each list item.
			 * @param object $category Category data object.
			 * @param int $depth Depth of page, used for padding.
			 * @param array $args An array of wp_list_categories() arguments.
			 *
			 * @since 4.2.0
			 *
			 * @see wp_list_categories()
			 *
			 */
			$css_classes = implode(' ', apply_filters('category_css_class', $css_classes, $category, $depth, $args));

			$output .= ' class="' . $css_classes . '"';
			$output .= ">$link\n";
		} elseif (isset($args['separator'])) {
			$output .= "\t$link" . $args['separator'] . "\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}
}

if (!function_exists('poco_show_categories_dropdown')) {
	function poco_show_categories_dropdown() {
		static $id = 0;
		$args  = array(
			'hide_empty' => 1,
			'parent'     => 0
		);
		$terms = get_terms('product_cat', $args);
		if (!empty($terms) && !is_wp_error($terms)) {
			?>
			<div class="search-by-category input-dropdown">
				<div class="input-dropdown-inner poco-scroll-content">
					<!--                    <input type="hidden" name="product_cat" value="0">-->
					<a href="#" data-val="0"><span><?php esc_html_e('All category', 'poco'); ?></span></a>
					<?php
					$args_dropdown = array(
						'id'               => 'product_cat' . $id++,
						'show_count'       => 0,
						'class'            => 'dropdown_product_cat_ajax',
						'show_option_none' => esc_html__('All category', 'poco'),
					);
					wc_product_dropdown_categories($args_dropdown);
					?>
					<div class="list-wrapper poco-scroll">
						<ul class="poco-scroll-content">
							<li class="d-none">
								<a href="#" data-val="0"><?php esc_html_e('All category', 'poco'); ?></a></li>
							<?php
							if (!apply_filters('poco_show_only_parent_categories_dropdown', false)) {
								$args_list = array(
									'title_li'           => false,
									'taxonomy'           => 'product_cat',
									'use_desc_for_title' => false,
									'walker'             => new Poco_Custom_Walker_Category(),
								);
								wp_list_categories($args_list);
							} else {
								foreach ($terms as $term) {
									?>
									<li>
										<a href="#" data-val="<?php echo esc_attr($term->slug); ?>"><?php echo esc_attr($term->name); ?></a>
									</li>
									<?php
								}
							}
							?>
						</ul>
					</div>
				</div>
			</div>
			<?php
		}
	}
}

if (!function_exists('poco_product_search')) {
	/**
	 * Display Product Search
	 *
	 * @return void
	 * @uses  poco_is_woocommerce_activated() check if WooCommerce is activated
	 * @since  1.0.0
	 */
	function poco_product_search($hide_search = false) {
		if (poco_is_woocommerce_activated()) {
			static $index = 0;
			$index++;
			?>
			<div class="site-search ajax-search">
				<div class="widget woocommerce widget_product_search">
					<div class="ajax-search-result d-none"></div>
					<form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url(home_url('/')); ?>">
						<label class="screen-reader-text" for="woocommerce-product-search-field-<?php echo isset($index) ? absint($index) : 0; ?>"><?php esc_html_e('Search for:', 'poco'); ?></label>
						<input type="search" id="woocommerce-product-search-field-<?php echo isset($index) ? absint($index) : 0; ?>" class="search-field" placeholder="<?php echo esc_attr__('Search products&hellip;', 'poco'); ?>" autocomplete="off" value="<?php echo get_search_query(); ?>" name="s"/>
						<button type="submit" value="<?php echo esc_attr_x('Search', 'submit button', 'poco'); ?>"><?php echo esc_html_x('Search', 'submit button', 'poco'); ?></button>
						<input type="hidden" name="post_type" value="product"/>

						<?php
						if (!$hide_search) {
							poco_show_categories_dropdown();
						}
						?>
					</form>
				</div>
			</div>
			<?php
		}
	}
}
