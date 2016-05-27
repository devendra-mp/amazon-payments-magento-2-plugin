define([
    'jquery',
    'ko',
    'amazonWidgetsLoader',
    'bluebird'
], function($, ko) {
    "use strict";

    var clientId = window.amazonPayment.clientId,
        amazonDefined = ko.observable(false),
        amazonLoginError = ko.observable(false),
        accessToken = ko.observable(null);
        
    function setClientId(cid) {
        amazonDefined(true);
        amazon.Login.setClientId(cid);
    }

    function amazonLogout() {
        if(amazonDefined()) {
            amazon.Login.logout();
        } else {
            var logout = amazonDefined.subscribe(function(defined) {
                if(defined) {
                    amazon.Login.logout();
                    logout.dispose(); //remove subscribe
                }
            });
        }
    }

    function doLogoutOnFlagCookie() {
        var errorFlagCookie = 'amz_auth_err';
        if($.cookieStorage.isSet(errorFlagCookie)) {
            amazonLogout();
            document.cookie = errorFlagCookie + '=; Path=/;  expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            amazonLoginError(true);
        }
    }

    if(typeof amazon === 'undefined') {
        window.onAmazonLoginReady = function() {
           setClientId(clientId);
           doLogoutOnFlagCookie();
        }
    } else {
      setClientId(clientId);
      doLogoutOnFlagCookie();
    }

    return {
        /**
         * Verify a user is logged into amazon
         */
        verifyAmazonLoggedIn: function() {
            var loginOptions = {
                scope: window.amazonPayment.loginScope,
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
         */
        AmazonLogout: amazonLogout,

        amazonDefined: amazonDefined,
        accessToken: accessToken,
        amazonLoginError: amazonLoginError
    };

});
