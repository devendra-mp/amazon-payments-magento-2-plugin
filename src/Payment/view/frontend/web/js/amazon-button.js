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

            if (typeof window.checkoutConfig !== 'undefined') {
                this._verifyCheckoutConfig();
            }
            _this._renderAmazonButton();
        },
        /**
         * Verify if checkout config is available
         * @private
         */
        _verifyCheckoutConfig: function() {
            if(window.checkoutConfig.payment.amazonPayment !== undefined) {
                _this.options.buttonType = window.checkoutConfig.payment.amazonPayment.buttonType;
                _this.options.buttonColor = window.checkoutConfig.payment.amazonPayment.buttonColor;
                _this.options.buttonSize = window.checkoutConfig.payment.amazonPayment.buttonSize;
                _this.options.redirectURL = window.checkoutConfig.payment.amazonPayment.redirectURL;
            }
        },
        /**
         * onAmazonPaymentsReady
         * @private
         */
        _renderAmazonButton: function() {
            var authRequest,
                loginOptions;

                OffAmazonPayments.Button($button.attr('id'), "AUGT0HMCLQVX1", {
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
