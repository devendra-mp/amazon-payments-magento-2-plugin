define([
    'jquery',
    'ko',
    'amazonPaymentConfig',
    'amazonCsrf',
    'amazonWidgetsLoader',
    'bluebird'
], function($, ko, amazonPaymentConfig, amazonCsrf) {
    "use strict";

    var clientId = amazonPaymentConfig.getValue('clientId'),
        amazonDefined = ko.observable(false),
        amazonLoginError = ko.observable(false),
        accessToken = ko.observable(null),
        authCookie = $.cookieStorage.get('amazon_Login_accessToken');

    /**
     * Set Client ID
     * @param cid
     */
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
            return new Promise(function(resolve, reject) {
                if(authCookie !== null) {
                    amazon.Login.retrieveProfile(authCookie, function(response){
                        accessToken(authCookie);
                        return !response.error ? resolve(!response.error) : reject(response.error);
                    });
                    //if no cookie is set (i.e. come from redirect)
                } else {
                    var loginOptions = {
                        scope: amazonPaymentConfig.getValue('loginScope'),
                        popup: true,
                        interactive: 'never',
                        state: amazonCsrf.generateNewValue()
                    };

                    amazon.Login.authorize (loginOptions, function(response) {
                        var resolution;

                        if (response.error) {
                            resolution = reject(response.error);
                        // no error: check the nonce
                        } else if (!response.hasOwnProperty('state') || !response.state || !amazonCsrf.isValid(response.state)) {
                            resolution = reject('Invalid state');
                        } else {
                            accessToken(response.access_token);
                            resolution = resolve(!response.error);
                        }

                        amazonCsrf.clear(); // always clear nonce
                        return resolution;
                    });
                }
            }).catch(function(e) {
                amazonCsrf.clear();
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
