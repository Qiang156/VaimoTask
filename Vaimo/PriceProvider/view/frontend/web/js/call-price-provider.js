define([
        "jquery",
    ], function($){
        "use strict";

        //$('#js-ajaxblocks-loader').loader().show();
        return function(config) {

            let getData = function(config) {
                let obj = {
                    'priceFinderData':{
                        'items':[]
                    }
                };
                if(config.type == 'product') {
                    obj.priceFinderData.items.push(
                        {
                            'itemNumber': $('div[data-role="priceBox"]').attr('data-product-id'),
                            'quantity':1,
                            'unitMeasure':'',
                            'orderDate':'0000-00-00'
                        });
                } else {
                    $(".product-item-details").find(".price-box").each(function (product) {
                        obj.priceFinderData.items.push(
                            {
                                'itemNumber': $(this).data('product-id'),
                                'quantity':1,
                                'unitMeasure':'',
                                'orderDate':'0000-00-00'
                            });
                    });
                }
                $.ajax({
                    method: "POST",
                    dataType: "json",
                    url: config.api,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify(obj)

                }).done(function (data) {
                    $.each(JSON.parse(data), function(key,item) {
                        $('#product-price-'+item.sku).find('span:eq(0)').html(item.priceIncVatFormatted);
                    });
                });
            }
            getData(config);

            // var galleryOptions = {
            //     "[data-role=swatch-options]": {
            //         "Magento_Swatches/js/swatch-renderer": {
            //             "showTooltip": 2
            //         }
            //     },
            // };
            // jsInitModifier.setJsInitBlockOptions('Magento_Swatches/js/swatch-renderer', galleryOptions);

            // $('#js-ajaxblocks-loader').loader().hide();
        }
    }
)
