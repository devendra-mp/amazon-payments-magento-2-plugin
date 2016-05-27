define(
    [
        'jquery',
        'ko',
        'amazonCore'
    ],
    function(
        $,
        ko,
        amazonCore
    ) {
        'use strict';

        var isAmazonAccountLoggedIn = ko.observable(false),
            isAmazonEnabled = ko.observable(window.amazonPayment.isPwaEnabled),
            orderReference,
            addressConsentToken = amazonCore.accessToken,
            isAmazonDefined = amazonCore.amazonDefined.subscribe(checkAmazonDefined),
            amazonLoginError = amazonCore.amazonLoginError.subscribe(setAmazonLoggedOutIfLoginError),
            amazonDeclineCode = ko.observable(false),
            sandboxSimulationReference = ko.observable('default'),
            isPlaceOrderDisabled = ko.observable(false),
            isShippingMethodsLoading = ko.observable(true),
            isAmazonShippingAddressSelected = ko.observable(false);

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

        function setAmazonLoggedOutIfLoginError(isLoggedOut) {
            if (true === isLoggedOut) {
                isAmazonAccountLoggedIn(false);
                amazonLoginError.dispose();
            }
        }

        //run this on loading storage model. If not defined subscribe will trigger when true
        checkAmazonDefined(amazonCore.amazonDefined());
        setAmazonLoggedOutIfLoginError(amazonCore.amazonLoginError());

        /**
         * Verifies amazon user is logged in
         */
        function verifyAmazonLoggedIn() {
            amazonCore.verifyAmazonLoggedIn().then(function(response) {
                if (!amazonCore.amazonLoginError()) {
                    isAmazonAccountLoggedIn(response);
                }
            });
        }

        return {
            isAmazonAccountLoggedIn: isAmazonAccountLoggedIn,
            isAmazonEnabled: isAmazonEnabled,
            amazonDeclineCode: amazonDeclineCode,
            sandboxSimulationReference: sandboxSimulationReference,
            isPlaceOrderDisabled: isPlaceOrderDisabled,
            isShippingMethodsLoading: isShippingMethodsLoading,
            isAmazonShippingAddressSelected: isAmazonShippingAddressSelected,
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
