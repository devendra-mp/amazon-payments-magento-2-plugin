define([
    'jquery',
    'jquery/ui',
    'amazonCore'
], function($) {
    "use strict";

    var _this,
        $button;

    $.widget('amazon.AmazonButton', {
        options: {
            merchantId: null,
            buttonType: 'LwA',
            buttonColor: 'Gold',
            buttonSize: 'medium',
            buttonLanguage: 'en-GB',
            widgetsScript: 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js',
            redirectURL: null
        },

        _create: function() {
            _this = this;
            $button = this.element;
            this._verifyAmazonConfig();
            _this._renderAmazonButton();
        },
        /**
         * Verify if checkout config is available
         * @private
         */
        _verifyAmazonConfig: function() {
            if(window.amazonPayment !== undefined) {
                _this.options.merchantId = window.amazonPayment.merchantId;
                _this.options.buttonColor = window.amazonPayment.buttonColor;
                _this.options.buttonSize = window.amazonPayment.buttonSize;
                _this.options.redirectURL = window.amazonPayment.redirectURL;
            }
        },
        /**
         * onAmazonPaymentsReady
         * @private
         */
        _renderAmazonButton: function() {
            var authRequest,
                loginOptions;

                OffAmazonPayments.Button($button.attr('id'), _this.options.merchantId, {
                    type: _this.options.buttonType,
                    color: _this.options.buttonColor,
                    size: _this.options.buttonSize,
                    language: _this.options.buttonLanguage,

                    authorization: function () {
                        loginOptions = {scope: "profile payments:widget payments:shipping_address"};
                        authRequest = amazon.Login.authorize(loginOptions, _this.options.redirectURL);
                    }
                });
        }
    });

    return $.amazon.AmazonButton;
});
