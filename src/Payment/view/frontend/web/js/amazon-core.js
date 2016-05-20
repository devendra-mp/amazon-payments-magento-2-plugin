define([
    'jquery',
    'ko',
    'amazonWidgetsLoader',
    'bluebird'
], function($, ko) {
    "use strict";


    function getURLParameter(name, source) {
        return decodeURIComponent((new RegExp('[?|&|#]' + name + '=' +
                '([^&]+?)(&|#|;|$)').exec(source) || [,""])[1].replace(/\+/g,
                '%20')) || null;
    }

    var hi = getURLParameter("access_token", location.hash);
    if (typeof hi === 'string' && hi.match(/^Atza/)) {
        $.cookieStorage.set('amazon_Login_accessToken', hi);
        console.log(hi);
        //window.location = 'https://amazon-payment.dev/amazon/login/authorize/'  + '?access_token=' + hi;
    }

    //console.log($.cookieStorage.get('amazon_Login_accessToken'));


    var clientId = window.amazonPayment.clientId,
        amazonDefined = ko.observable(false),
        accessToken = ko.observable(null);
        
    function setClientId(cid) {
        amazonDefined(true);
        amazon.Login.setClientId(cid);
        if(window.location.protocol === 'http:') {
            amazon.Login.setUseCookie(true);
        }
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
                if(window.location.protocol === 'https:') {
                    amazon.Login.authorize (loginOptions, function(response) {
                        accessToken(response.access_token);
                        return !response.error ? resolve(!response.error) : reject(response.error);
                    });
                } else if(window.location.protocol === 'http:') {
                    var cookieAccessToken = $.cookieStorage.get('amazon_Login_accessToken');
                    if(cookieAccessToken !== '') {
                        accessToken(cookieAccessToken);
                        return true;
                    }
                    return false;
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
