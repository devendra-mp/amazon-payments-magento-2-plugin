define(
    [
        'jquery',
        'ko',
        'Magento_Customer/js/model/customer',
        'amazonCore'
    ],
    function(
        $,
        ko,
        customer,
        amazonCore
    ) {
        'use strict';

        var isCustomerLoggedIn = customer.isLoggedIn,
            isAmazonAccountLoggedIn = ko.observable(false),
            isAmazonEnabled = ko.observable(window.checkoutConfig.payment.amazonPayment.isEnabled);

        amazonCore.verifyAmazonLoggedIn().then(function(response) {
            isAmazonAccountLoggedIn(response);
        });

        return {
            isCustomerLoggedIn: isCustomerLoggedIn,
            isAmazonAccountLoggedIn: isAmazonAccountLoggedIn,
            isAmazonEnabled: isAmazonEnabled
        }
    }
);