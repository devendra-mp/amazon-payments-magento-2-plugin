define([
    'jquery',
    'amazonCore',
    'amazonPaymentConfig',
    'amazonCsrf',
    'jquery/ui'
], function($, amazonCore, amazonPaymentConfig, amazonCsrf) {
    "use strict";

    $.widget('amazon.AmazonRedirect', {

        /**
         * @private
         */
        _create: function() {
            // verify nonce first
            this.redirectOnInvalidState();

            // we don't have the customer's consent or invalid request
            this.redirectOnRequestWithError();

            this.setAuthStateCookies();
            this.redirect();
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
            //return false is the cookies are already set and token is null
            if ($.cookieStorage.get('amazon_Login_state_cache') !== null && $.cookieStorage.get('amazon_Login_accessToken') !== null && token === null) {
                return false;
            }
            var newObj = {
                access_token: token,
                max_age: this.getURLParameter('expires_in', location.hash),
                expiration_date: new Date().getTime() + (this.getURLParameter('expires_in', location.hash) * 1000),
                client_id: amazonPaymentConfig.getValue('clientId'),
                scope: this.getURLParameter('scope', location.hash)
            };

            if (typeof token === 'string' && token.match(/^Atza/)) {
                $.cookieStorage.set('amazon_Login_state_cache', JSON.stringify(newObj));
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
