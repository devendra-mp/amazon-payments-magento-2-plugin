define([
    'jquery'
], function($) {
    "use strict";

    var options = {
        widgetsScript: 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js'
    };

    return {
        /**
         * onAmazonLoginReady
         * @private
         */
        _onAmazonLoginReady: function() {
            window.onAmazonLoginReady = function() {
                amazon.Login.setClientId('amzn1.application-oa2-client.15d69a1a3b83453a81ab480224d811cd');
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
