define([
    'jquery',
    'ko',
    'amazonWidgetsLoader',
    'bluebird'
], function($, ko) {
    "use strict";

    var clientId = window.amazonPayment.clientId,
        amazonDefined = ko.observable(false),
        accessToken = ko.observable(null);
        
    function setClientId(cid) {
        amazonDefined(true);
        amazon.Login.setClientId(cid);
    }

    if(typeof amazon === 'undefined') {
        window.onAmazonLoginReady = function() {
           setClientId(clientId);
        }
    } else {
      setClientId(clientId);
    }

    return {
        /**
         * Verify a user is logged into amazon
         * @returns {*}
         */
        verifyAmazonLoggedIn: function() {
            var loginOptions = {
                scope: "profile payments:widget payments:shipping_address payments:billing_address",
                popup: true,
                interactive: 'never'
            };

            return new Promise(function(resolve, reject) {
                amazon.Login.authorize (loginOptions, function(response) {
                    accessToken(response.access_token);
                    !response.error ? resolve(!response.error) : reject(response.error);
                });
            }).catch(function(e) {
                console.log('error: ' + e);
            });
        },
        /**
         * Log user out of Amazon
         * @constructor
         */
        AmazonLogout: function() {
            if(amazonDefined()) {
                amazon.Login.logout();
            } else {
                var logout = amazonDefined.subscribe(function(defined) {
                    if(defined) {
                        amazon.Login.logout();
                        logout.dispose(); //remove subscribe
                    }
                })
            }

        },
        amazonDefined: amazonDefined,
        accessToken: accessToken
    };

});
