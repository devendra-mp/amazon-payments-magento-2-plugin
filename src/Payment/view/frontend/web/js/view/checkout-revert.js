/*global define*/
define(
    [
        'jquery',
        "underscore",
        'ko',
        'uiComponent',
        'Amazon_Payment/js/model/storage',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor'
    ],
    function(
        $,
        _,
        ko,
        Component,
        amazonStorage,
        storage,
        errorProcessor
    ) {
        'use strict';

        var self;

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-revert'
            },
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isAmazonEnabled: ko.observable(window.amazonPayment.isPwaEnabled),
            initialize: function () {
                self = this;
                this._super();
            },
            revertCheckout: function() {
                var serviceUrl = 'rest/default/V1/amazon/order-ref';
                storage.delete(
                    serviceUrl
                ).done(
                    function() {
                        amazonStorage.amazonlogOut();
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                );
            }
        });
    }
);
