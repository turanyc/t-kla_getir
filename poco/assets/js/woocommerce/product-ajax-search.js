(function ($) {
    'use strict';
    function ajax_live_search() {
        var $parent = $('.woocommerce-product-search'),
            $inputsearch = $('.ajax-search .woocommerce-product-search .search-field'),
            $result = $('.ajax-search-result'),
            template = wp.template('ajax-live-search-template'),
            $dropdown = $('.input-dropdown-inner'),
            $list = $('> .list-wrapper', $dropdown);

        $('body').on('click', function () {
            $result.hide();
            $list.slideUp(100);
            $dropdown.removeClass('dd-shown');
        });

        $('.input-dropdown-inner > a').on('click', function (e) {
            e.preventDefault();
            if ($dropdown.hasClass('dd-shown')) {
                $dropdown.removeClass('dd-shown');
                $list.slideUp(100);
            } else {
                $dropdown.addClass('dd-shown');
                $list.slideDown(100);
            }
            $result.hide();
            return false;
        });

        $('.input-dropdown-inner > .list-wrapper').on('click', 'a', function (e) {
            e.preventDefault();
            var value = $(this).data('val');
            var label = $(this).text();

            $('.input-dropdown-inner > .list-wrapper').find('.current-item').removeClass('current-item');
            $(this).parent().addClass('current-item');
            if (value != 0) {
                $list.find('ul:not(.children) > li:first-child').show();
            } else if (value == 0) {
                $list.find('ul:not(.children) > li:first-child').hide();
            }

            $('.input-dropdown-inner > a span').text(label);
            $('.input-dropdown-inner > select').val(value).trigger('cat_selected');

            $list.slideUp(100);
            $dropdown.removeClass('dd-shown');

        });

        $('.input-dropdown-inner > select').change(function () {

            var value = $(this).val();
            var $selected = $(this).find('option:selected');
            var label = $selected.text();
            $('.input-dropdown-inner > .list-wrapper').find('.current-item').removeClass('current-item');
            $(this).parent().addClass('current-item');
            if (value != 0) {
                $list.find('ul:not(.children) > li:first-child').show();
            } else if (value == 0) {
                $list.find('ul:not(.children) > li:first-child').hide();
            }

            $('.input-dropdown-inner > a span').text(label);

        });

        if ($inputsearch.length) {
            $inputsearch.keyup(function () {
                if (this.value.length > 2) {
                    var product_cat = $('select[name="product_cat"]', $parent).val();
                    $.ajax({
                        url: pocoAjax.ajaxurl,
                        type: 'post',
                        data: {
                            action: 'poco_ajax_search_products',
                            query: this.value,
                            product_cat: product_cat
                        },
                        beforeSend: function () {
                            $parent.addClass('loading');
                        },
                        success: function (data) {
                            $parent.removeClass('loading');
                            var $data = $.parseJSON(data);
                            $result.empty();
                            $result.show();
                            $.each($data, function (i, item) {
                                $result.append(template({
                                    url: item.url,
                                    title: item.value,
                                    img: item.img,
                                    price: item.price
                                }));
                            });
                        }
                    });
                } else {
                    $result.hide();
                }
            })
                .on('click', function (e) {
                    e.stopPropagation();
                })
                .on('focus', function (event) {
                    $list.slideUp(100);
                    $dropdown.removeClass('dd-shown');
                    if (this.value.length > 2) {
                        $result.show();
                    }
                });
        }
    }

    $(document).ready(function () {
        ajax_live_search();
    });

})(jQuery);
