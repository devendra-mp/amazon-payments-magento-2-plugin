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
            client_id: "<?php echo $config['client_id'] ?>",
            scope: getURLParameter('scope', location.hash)
        };

        if (typeof token === 'string' && token.match(/^Atza/)) {
            $.cookieStorage.set('amazon_Login_state_cache', JSON.stringify(newObj));
            $.cookieStorage.set('amazon_Login_accessToken', token);
        }

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
                scope: window.amazonPayment.loginScope,
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
