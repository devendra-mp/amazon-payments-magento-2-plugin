define([
    'jquery',
    'jquery/ui'
], function($) {
    "use strict";

    var _this,
        $button;

    $.widget('amazon.AmazonButton', {
        options: {
            buttonType: 'LwA',
            buttonColor: 'Gold',
            buttonSize: 'medium',
            buttonLanguage: 'en-GB',
            widgetsScript: 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js',
            redirectURL: 'https://amazon-payment.dev/customer/account'
        },

        _create: function() {
            _this = this;
            $button = this.element;

            //load amazon global calls on window object
            this._onAmazonLoginReady();
            this._onAmazonPaymentsReady();

            //load amazon widgets script
            this._loadAmazonWidgetsScript();
        },
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
         * onAmazonPaymentsReady
         * @private
         */
        _onAmazonPaymentsReady: function() {
            window.onAmazonPaymentsReady = function(){
                // render the button here
                var authRequest,
                    loginOptions;

                OffAmazonPayments.Button($button.attr('id'), "A1BJXVS5F6XP", {
                    type:  _this.options.buttonType,
                    color: _this.options.buttonColor,
                    size: _this.options.buttonSize,
                    language: _this.options.buttonLanguage,

                    authorization: function() {
                        loginOptions = {scope: "profile payments:widget payments:shipping_address"};
                        authRequest = amazon.Login.authorize (loginOptions, _this.options.redirectURL);
                    }
                });
            };
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
