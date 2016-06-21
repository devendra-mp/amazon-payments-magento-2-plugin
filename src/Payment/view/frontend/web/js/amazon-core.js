define([
    'jquery',
    'ko',
    './model/amazonPaymentConfig',
    'amazonWidgetsLoader',
    'bluebird'
], function($, ko, amazonPaymentConfig) {
    "use strict";

    var clientId = amazonPaymentConfig.getValue('clientId'),
        amazonDefined = ko.observable(false),
        amazonLoginError = ko.observable(false),
        accessToken = ko.observable(null);

    /**
     * getURLParamater from URL for access OAuth Token
     * @param name
     * @param source
     * @returns {string|null}
     */
    function getURLParameter(name, source) {
        return decodeURIComponent((new RegExp('[?|&|#]' + name + '=' +
                '([^&]+?)(&|#|;|$)').exec(source) || [,""])[1].replace(/\+/g,
                '%20')) || null;
    }

    /**
     * Set State Cache Auth Cookies if they aren't already set
     * @returns {boolean}
     */
    function setAuthStateCookies() {
        var token = getURLParameter("access_token", location.hash);
        //return false is the cookies are already set and token is null
        if ($.cookieStorage.get('amazon_Login_state_cache') !== null && $.cookieStorage.get('amazon_Login_accessToken') !== null && token === null) {
            return false;
        }
        var newObj = {
            access_token: token,
            max_age: getURLParameter('expires_in', location.hash),
            expiration_date: new Date().getTime() + (getURLParameter('expires_in', location.hash) * 1000),
            client_id: clientId,
            scope: getURLParameter('scope', location.hash)
        };

        if (typeof token === 'string' && token.match(/^Atza/)) {
            $.cookieStorage.set('amazon_Login_state_cache', JSON.stringify(newObj));
            $.cookieStorage.set('amazon_Login_accessToken', token);
        }
        return true;
    }

    /**
     * Set Client ID
     * @param cid
     */
    function setClientId(cid) {
        setAuthStateCookies();
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
                scope: amazonPaymentConfig.getValue('loginScope'),
                popup: true,
                interactive: 'never'
            };

            return new Promise(function(resolve, reject) {
                var authCookie = $.cookieStorage.get('amazon_Login_accessToken');
                if(authCookie !== null) {
                    amazon.Login.retrieveProfile(authCookie, function(response){
                        accessToken(authCookie);
                        return !response.error ? resolve(!response.error) : reject(response.error);
                    });
                    //if no cookie is set (i.e. come from redirect)
                } else {
                    amazon.Login.authorize (loginOptions, function(response) {
                        accessToken(response.access_token);
                        return !response.error ? resolve(!response.error) : reject(response.error);
                    });
                }
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
