/*global define*/

define(
    [
        'jquery',
        "uiComponent",
        'ko',
        'Magento_Customer/js/model/customer'
    ],
    function(
        $,
        Component,
        ko,
        customer
    ) {
        'use strict';
        return Component.extend({
             defaults: {
                template: 'Amazon_Payment/checkout-init'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            initialize: function () {
                var self = this;
                this._super();
            },
            isEnabled: function () {
                return window.checkoutConfig.payment.amazonPayment.isEnabled;
            },
            getButtonData: function () {
                var buttonData = {
                    'amazonButton': {
                        'buttonType': window.checkoutConfig.payment.amazonPayment.buttonType,
                        'buttonColor': window.checkoutConfig.payment.amazonPayment.buttonColor,
                        'buttonSize': window.checkoutConfig.payment.amazonPayment.buttonSize
                    }
                };
                return buttonData;
            }
        });
    }
);
