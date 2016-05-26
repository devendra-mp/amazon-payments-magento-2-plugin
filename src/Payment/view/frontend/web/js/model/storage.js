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
            doLogoutOnFlagCookie();
            if(amazonDefined) {
               verifyAmazonLoggedIn();
               //remove subscription to amazonDefined once loaded
               isAmazonDefined.dispose();
            }
        }

        function doLogoutOnFlagCookie() {
            var errorFlagCookie = 'amz_auth_err';
            if($.cookieStorage.isSet(errorFlagCookie)) {
                amazonCore.AmazonLogout();
                isAmazonAccountLoggedIn(false);
                document.cookie = errorFlagCookie + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
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
