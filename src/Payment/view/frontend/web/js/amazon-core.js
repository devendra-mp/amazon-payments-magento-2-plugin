define([
    'jquery',
    'ko',
    'amazonPaymentConfig',
    'amazonWidgetsLoader',
    'bluebird',
    'mage/cookies'
], function($, ko, amazonPaymentConfig) {
    "use strict";

    var clientId = amazonPaymentConfig.getValue('clientId'),
        amazonDefined = ko.observable(false),
        amazonLoginError = ko.observable(false),
        accessToken = ko.observable(null);


    if(typeof amazon === 'undefined') {
        window.onAmazonLoginReady = function() {
            setClientId(clientId);
            doLogoutOnFlagCookie();
        }
    } else {
        setClientId(clientId);
        doLogoutOnFlagCookie();
    }

    /**
     * Set Client ID
     * @param cid
     */
    function setClientId(cid) {
        amazon.Login.setClientId(cid);
        amazonDefined(true);
    }

    /**
     * Log user out of amazon
     */
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

    //Check if login error / logout cookies are present
    function doLogoutOnFlagCookie() {
        var errorFlagCookie = 'amz_auth_err',
            amazonLogoutCookie = 'amz_auth_logout';

        $.cookieStorage.isSet(errorFlagCookie) ? amazonLogoutThrowError(errorFlagCookie) : false;
        $.cookieStorage.isSet(amazonLogoutCookie) ? amazonLogoutThrowError(amazonLogoutCookie) : false;
    }

    //handle deletion of cookie and log user out if present
    function amazonLogoutThrowError(cookieToRemove) {
        amazonLogout();
        $.mage.cookies.clear(cookieToRemove);
        amazonLoginError(true);
    }

    return {
        /**
         * Verify a user is logged into amazon
         */
        verifyAmazonLoggedIn: function() {
            return new Promise(function(resolve, reject) {

                var loginOptions = {
                    scope: amazonPaymentConfig.getValue('loginScope'),
                    popup: true,
                    interactive: 'never'
                };

                amazon.Login.authorize (loginOptions, function(response) {
                    var resolution;
                    if (response.error) {
                        resolution = reject(response.error);
                    } else {
                        accessToken(response.access_token);
                        resolution = resolve(!response.error);
                    }
                    return resolution;
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
