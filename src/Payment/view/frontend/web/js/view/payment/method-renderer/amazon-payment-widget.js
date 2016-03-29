define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Amazon_Payment/js/model/storage'
    ],
    function(
        $,
        Component,
        ko,
        customer,
        quote,
        amazonStorage
    ) {
        'use strict';

        var self;

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/payment/amazon-payment-widget'
            },

            options: {
                sellerId: 'AUGT0HMCLQVX1',
                paymentWidgetDOMId: 'walletWidgetDiv'
            },
            isCustomerLoggedIn: amazonStorage.isCustomerLoggedIn,
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isAmazonEnabled: ko.observable(window.checkoutConfig.payment.amazonPayment.isEnabled),
            initialize: function () {
                self = this;
                this._super();
            },
            initPaymentWidget: function() {
                self.renderPaymentWidget();
            },
            /**
             * render Amazon payment Widget
             */
            renderPaymentWidget: function() {
                new OffAmazonPayments.Widgets.Wallet({
                    sellerId: self.options.sellerId,
                    amazonOrderReferenceId: amazonStorage.getOrderReference(),
                    onPaymentSelect: function(orderReference) {

                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function(error) {
                        // Your error handling code.
                    }
                }).bind(self.options.paymentWidgetDOMId);
            },
            getCode: function() {
                return 'amazon_payment';
            },
            isActive: function() {
                return true;
            }
        });
    }
);