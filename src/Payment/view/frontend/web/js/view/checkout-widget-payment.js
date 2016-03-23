/*global define*/

define(
    [
        'jquery',
        "uiComponent",
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'amazonCore'
        //'amazonPayment'
    ],
    function(
        $,
        Component,
        ko,
        customer,
        quote,
        amazonCore
    ) {
        'use strict';
        var self;
        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-widget-payment'
            },
            options: {
                sellerId: 'A1BJXVS5F6XP',
                paymentWidgetDOMId: 'walletWidgetDiv'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            isAmazonAccountLoggedIn: ko.observable(false),
            isAmazonEnabled: ko.observable(window.checkoutConfig.payment.amazonPayment.isEnabled),
            initialize: function () {
                self = this;
                this._super();
                this.setupPaymentWidget();
            },
            /**
             * Check to see whether user is currently logged into Amazon
             */
            verifyAmazonLoggedIn: function() {
                var loginOptions = {
                    scope: "profile payments:widget payments:shipping_address",
                    popup: true,
                    interactive: 'never'
                };
                amazon.Login.authorize (loginOptions, function(response) {
                    if(!response.error) {
                        self.isAmazonAccountLoggedIn(true);
                    }
                });
            },
            /**
             * Setup events and bindings for the Amazon Address widget
             */
            setupPaymentWidget: function() {
                self.renderPaymentWidget();
            },
            /**
             * render Amazon payment Widget
             */
            renderPaymentWidget: function() {
                new OffAmazonPayments.Widgets.Wallet({
                    sellerId: self.options.sellerId,
                    amazonOrderReferenceId: amazonCore._getOrderReference(), //the one you created before, most likely in the addressBook widget
                    //amazonOrderReferenceId: '', //the one you created before, most likely in the addressBook widget
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