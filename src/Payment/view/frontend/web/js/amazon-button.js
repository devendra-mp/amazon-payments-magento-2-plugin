define([
    'jquery',
    'jquery/ui'
], function($) {
    "use strict";

    var _this;

    $.widget('amazon.AmazonButton', {

        options: {
            buttonType: 'LwA',
            widgetsScript: 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'
        },

        _create: function() {
            _this = this;
            window.onAmazonLoginReady = function() {
                amazon.Login.setClientId('amzn1.application-oa2-client.fe5d817cfb2b45dcaf1c2c15966454bb');
            };

            window.onAmazonPaymentsReady = function(){
                // render the button here
                var authRequest,
                    loginOptions;

                OffAmazonPayments.Button("LoginWithAmazon", "A1BJXVS5F6XP", {
                    type:  _this.options.buttonType,
                    color: "Gold",
                    language: "en-GB",

                    authorization: function() {
                        loginOptions = {scope: "profile payments:widget payments:shipping_address"};
                        authRequest = amazon.Login.authorize (loginOptions, "https://amazon-payment.dev/customer/account");
                    }
                });
            };
            this._loadAmazonWidgetsScript();
        },
        /**
         * Load amazon widgets script after global window functions have been declared
         * @private
         */
        _loadAmazonWidgetsScript: function() {
            var scriptTag = document.createElement('script');
            scriptTag.setAttribute('src', _this.options.widgetsScript);
            document.head.appendChild(scriptTag);
        }

    });

    return $.amazon.AmazonButton;
});