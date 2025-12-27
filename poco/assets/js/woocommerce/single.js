(function ($) {
    'use strict';

    function singleProductGalleryImages() {
        var rtl = $('body').hasClass('rtl') ? true : false;

        var lightbox = $('.single-product .woocommerce-product-gallery__image > a');
        if (lightbox.length) {
            lightbox.attr("data-elementor-open-lightbox", "no");
        }

        if ($('.flex-control-thumbs', '.woocommerce-product-gallery').children().length > 4) {
            $('.woocommerce-product-gallery.woocommerce-product-gallery-horizontal .flex-control-thumbs').css({
                display: "block",
                "max-width": 480,
                "padding-right": 30,
            }).slick({
                rtl: rtl,
                infinite: false,
                slidesToShow: 4,
            });
            $('.woocommerce-product-gallery.woocommerce-product-gallery-vertical .flex-control-thumbs').slick({
                rtl: rtl,
                infinite: false,
                slidesToShow: 4,
                vertical: true,
                verticalSwiping: true,
            });
        }



    }

    function sizechart_popup() {

        $('.sizechart-button').on('click', function (e) {
            e.preventDefault();
            $('.sizechart-popup').toggleClass('active');
        });

        $('.sizechart-close,.sizechart-overlay').on('click', function (e) {
            e.preventDefault();
            $('.sizechart-popup').removeClass('active');
        });
    }

    $('.woocommerce-product-gallery').on('wc-product-gallery-after-init', function () {
        singleProductGalleryImages();
    });

    function onsale_gallery_vertical(){
		$('.woocommerce-product-gallery.woocommerce-product-gallery-vertical:not(:has(.flex-control-thumbs))').css('max-width','660px').next().css('left','10px');
	}


    function productTogerther() {

        var $fbtProducts = $('.poco-frequently-bought');

        if ($fbtProducts.length <= 0) {
            return;
        }
        var priceAt = $fbtProducts.find('.poco-total-price .woocommerce-Price-amount'),
            $button = $fbtProducts.find('.poco_add_to_cart_button'),
            totalPrice = parseFloat($fbtProducts.find('#poco-data_price').data('price')),
            currency = $fbtProducts.data('currency'),
            thousand = $fbtProducts.data('thousand'),
            decimal = $fbtProducts.data('decimal'),
            price_decimals = $fbtProducts.data('price_decimals'),
            currency_pos = $fbtProducts.data('currency_pos');

        let formatNumber = function(number){
            let n = number;
            if (parseInt(price_decimals) > 0) {
                number = number.toFixed(price_decimals) + '';
                var x = number.split('.');
                var x1 = x[0],
                    x2 = x.length > 1 ? decimal + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + thousand + '$2');
                }

                n = x1 + x2
            }
            switch (currency_pos) {
                case 'left' :
                    return currency + n;
                    break;
                case 'right' :
                    return n + currency;
                    break;
                case 'left_space' :
                    return currency + ' ' + n;
                    break;
                case 'right_space' :
                    return n + ' ' + currency;
                    break;
            }
        }

        $fbtProducts.find('input[type=checkbox]').on('change', function () {
            let id = $(this).val();
            $(this).closest('label').toggleClass('uncheck');
            let currentPrice = parseFloat($(this).closest('label').data('price'));
            if ($(this).closest('label').hasClass('uncheck')) {
                $fbtProducts.find('#fbt-product-' + id).addClass('un-active');
                totalPrice -= currentPrice;

            } else {
                $fbtProducts.find('#fbt-product-' + id).removeClass('un-active');
                totalPrice += currentPrice;
            }

            let $product_ids = $fbtProducts.data('current-id');
            $fbtProducts.find('label.select-item').each(function () {
                if (!$(this).hasClass('uncheck')) {
                    $product_ids += ',' + $(this).find('input[type=checkbox]').val();
                }
            });

            $button.attr('value', $product_ids);

            priceAt.html(formatNumber(totalPrice));
        });

        // Add to cart ajax
        $fbtProducts.on('click', '.poco_add_to_cart_button.ajax_add_to_cart', function () {
            var $singleBtn = $(this);
            $singleBtn.addClass('loading');

            var currentURL = window.location.href;

            $.ajax({
                url: pocoAjax.ajaxurl,
                dataType: 'json',
                method: 'post',
                data: {
                    action: 'poco_woocommerce_fbt_add_to_cart',
                    product_ids: $singleBtn.attr('value')
                },
                error: function () {
                    window.location = currentURL;
                },
                success: function (response) {
                    if (typeof wc_add_to_cart_params !== 'undefined') {
                        if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
                            window.location = wc_add_to_cart_params.cart_url;
                            return;
                        }
                    }

                    $(document.body).trigger('updated_wc_div');
                    $(document.body).on('wc_fragments_refreshed', function () {
                        $singleBtn.removeClass('loading');
                    });
                    $('body').trigger('added_to_cart');

                }
            });

        });

    }

    $(document).ready(function () {
        sizechart_popup();
		onsale_gallery_vertical();
        productTogerther();
    });

})(jQuery);
