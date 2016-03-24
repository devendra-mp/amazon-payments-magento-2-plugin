/*global define*/

define(
    [
        'jquery',
        "uiComponent",
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
                template: 'Amazon_Payment/checkout-widget-payment'
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
                        // Replace this code with the action that you want to perform
                        // after the payment method is selected.

                        // Ideally this would enable the next action for the buyer
                        // including either a "Continue" or "Place Order" button.
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function(error) {
                        // Your error handling code.
                        // During development you can use the following
                        // code to view error messages:
                        // console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
                        // See "Handling Errors" for more information.
                    }
                }).bind(self.options.paymentWidgetDOMId);
            }
        });
    }
);