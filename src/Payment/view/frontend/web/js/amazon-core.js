define([
    'jquery'
], function($) {
    "use strict";

    var options = {
            widgetsScript: 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'
        },
        orderReference;

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
        },
        _setOrderReference: function(or) {
            orderReference = or;
        },
        _getOrderReference: function() {
            return orderReference;
        }
    };
});
