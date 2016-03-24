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
            isAmazonEnabled = ko.observable(window.checkoutConfig.payment.amazonPayment.isEnabled),
            orderReference,
            isAmazonDefined = amazonCore.amazonDefined.subscribe(checkAmazonDefined);

        /**
         * Subscribes to amazonDefined observable which runs when amazon object becomes available
         * @param value
         */
        function checkAmazonDefined(amazonDefined) {
           if(amazonDefined) {
               verifyAmazonLoggedIn();
               //remove subscription to amazonDefined
               isAmazonDefined.dispose();
           }
        }

        /**
         * Verifies amazon user is logged in
         */
        function verifyAmazonLoggedIn() {
           amazonCore.verifyAmazonLoggedIn().then(function(response) {
               isAmazonAccountLoggedIn(response);
           });
        }

        return {
            isCustomerLoggedIn: isCustomerLoggedIn,
            isAmazonAccountLoggedIn: isAmazonAccountLoggedIn,
            isAmazonEnabled: isAmazonEnabled,
            setOrderReference: function(or) {
                orderReference = or;
            },
            getOrderReference: function() {
                return orderReference;
            }
        }
    }
);