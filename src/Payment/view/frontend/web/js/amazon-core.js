define([
    'jquery'
], function($) {
    "use strict";

    var options = {
        widgetsScript: 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'
    };

    return {
        /**
         * onAmazonLoginReady
         * @private
         */
        _onAmazonLoginReady: function() {
            window.onAmazonLoginReady = function() {
                amazon.Login.setClientId('amzn1.application-oa2-client.fe5d817cfb2b45dcaf1c2c15966454bb');
            };
        },
        /**
         * Load amazon widgets script after global window functions have been declared
         * @private
         */
        _loadAmazonWidgetsScript: function() {
            var scriptTag = document.createElement('script');
            scriptTag.setAttribute('src', options.widgetsScript);
            document.head.appendChild(scriptTag);
        }
    };
});
