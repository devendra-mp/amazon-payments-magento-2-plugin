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

        var isAmazonAccountLoggedIn = ko.observable(false),
            isAmazonEnabled = ko.observable(window.amazonPayment.isPwaEnabled),
            orderReference,
            addressConsentToken = amazonCore.accessToken,
            isAmazonDefined = amazonCore.amazonDefined.subscribe(checkAmazonDefined),
            amazonDeclineCode = ko.observable(false),
            sandboxSimulationReference = ko.observable('default'),
            isPlaceOrderDisabled = ko.observable(false);

        /**
         * Subscribes to amazonDefined observable which runs when amazon object becomes available
         * @param amazonDefined
         */
        function checkAmazonDefined(amazonDefined) {
           if(amazonDefined) {
               verifyAmazonLoggedIn();
               //remove subscription to amazonDefined once loaded
               isAmazonDefined.dispose();
           }
        }

        //run this on loading storage model. If not defined subscribe will trigger when true
        checkAmazonDefined(amazonCore.amazonDefined());

        /**
         * Verifies amazon user is logged in
         */
        function verifyAmazonLoggedIn() {
           amazonCore.verifyAmazonLoggedIn().then(function(response) {
               isAmazonAccountLoggedIn(response);
           });
        }

        return {
            isAmazonAccountLoggedIn: isAmazonAccountLoggedIn,
            isAmazonEnabled: isAmazonEnabled,
            amazonDeclineCode: amazonDeclineCode,
            sandboxSimulationReference: sandboxSimulationReference,
            isPlaceOrderDisabled: isPlaceOrderDisabled,
            amazonlogOut: function() {
                if(amazonCore.amazonDefined()) {
                    amazon.Login.logout();
                }
                this.isAmazonAccountLoggedIn(false);
            },
            setOrderReference: function(or) {
                orderReference = or;
            },
            getOrderReference: function() {
                return orderReference;
            },
            getAddressConsentToken: function() {
                return addressConsentToken();
            }
        }
    }
);
