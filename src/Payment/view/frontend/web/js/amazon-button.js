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

            if (typeof window.amazonPayments !== 'undefined') {
                this._verifyCheckoutConfig();
            }
            _this._renderAmazonButton();
        },
        /**
         * Verify if checkout config is available
         * @private
         */
        _verifyCheckoutConfig: function() {
            if(window.amazonPayments !== undefined) {
                _this.options.merchantId = window.amazonPayments.merchantId;
                _this.options.buttonType = window.amazonPayments.buttonTypePwa;
                _this.options.buttonColor = window.amazonPayments.buttonColor;
                _this.options.buttonSize = window.amazonPayments.buttonSize;
                _this.options.redirectURL = window.amazonPayments.redirectURL;
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
