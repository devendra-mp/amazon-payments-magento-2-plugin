/*global define*/

define(
    [
        'jquery',
        "uiComponent",
        'ko',
        'Magento_Customer/js/model/customer',
        'Amazon_Payment/js/model/storage',
        'amazonPaymentConfig'
    ],
    function(
        $,
        Component,
        ko,
        customer,
        amazonStorage,
        amazonPaymentConfig
    ) {
        'use strict';
        return Component.extend({
             defaults: {
                template: 'Amazon_Login/login-button'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isLwaVisible: ko.observable(!amazonPaymentConfig.getValue('isLwaEnabled')),
            initialize: function () {
                var self = this;
                this._super();
            }
        });
    }
);
