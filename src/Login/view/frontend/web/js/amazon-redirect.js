define([
    'jquery',
    'amazonCore',
    'amazonPaymentConfig',
    'amazonCsrf',
    'jquery/ui'
], function($, amazonCore, amazonPaymentConfig, amazonCsrf) {
    "use strict";

    var self;

    $.widget('amazon.AmazonRedirect', {

        /**
         * @private
         */
        _create: function() {

            self = this;
            // verify nonce first
            this.redirectOnInvalidState();

            // we don't have the customer's consent or invalid request
            this.redirectOnRequestWithError();
            this.setAuthStateCookies();
            amazonCore.amazonDefined.subscribe(function() {
                //only set this on the redirect page
                amazon.Login.setUseCookie(true);
                amazonCore.verifyAmazonLoggedIn().then(function(loggedIn) {
                    if(loggedIn) {
                        self.redirect()
                    }
                });
            }, this);
        },

        /**
         * getURLParamater from URL for access OAuth Token
         * @param name
         * @param source
         * @returns {string|null}
         */
        getURLParameter: function(name, source) {
            return decodeURIComponent((new RegExp('[?|&|#]' + name + '=' +
                    '([^&]+?)(&|#|;|$)').exec(source) || [,""])[1].replace(/\+/g,
                    '%20')) || null;
        },

        /**
         * Set State Cache Auth Cookies if they aren't already set
         * @returns {boolean}
         */
        setAuthStateCookies: function() {
            var token = this.getURLParameter("access_token", location.hash);
            if (typeof token === 'string' && token.match(/^Atza/)) {
                $.cookieStorage.set('amazon_Login_accessToken', token);
            }
            return true;
        },

        redirect: function() {
            window.location = amazonPaymentConfig.getValue('redirectUrl') + '?access_token=' + this.getURLParameter('access_token', location.hash);
        },
        redirectOnInvalidState: function() {
            var state = this.getURLParameter('state', location.hash);
            if (!state || !amazonCsrf.isValid(state)) {
                window.location = amazonPaymentConfig.getValue('customerLoginPageUrl');
            }
        },
        redirectOnRequestWithError: function() {
            if (this.getURLParameter('error', window.location)) {
                window.location = amazonPaymentConfig.getValue('customerLoginPageUrl');
            }
        }
    });

    return $.amazon.AmazonRedirect;
});
