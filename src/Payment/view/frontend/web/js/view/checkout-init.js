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
            isAmazonEnabled: ko.observable(window.checkoutConfig.payment.amazonPayment.isEnabled),
            initialize: function () {
                var self = this;
                this._super();
            }
        });
    }
);
