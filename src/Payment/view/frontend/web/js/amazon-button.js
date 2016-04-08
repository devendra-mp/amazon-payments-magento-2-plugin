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
            merchantId: window.amazonPayment.merchantId,
            buttonType: window.amazonPayment.buttonTypePwa,
            buttonColor: window.amazonPayment.buttonColor,
            buttonSize: window.amazonPayment.buttonSize,
            buttonLanguage: 'en-GB',
            widgetsScript: 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets.js',
            redirectURL: window.amazonPayment.redirectURL
        },

        _create: function() {
            _this = this;
            $button = this.element;
            _this._renderAmazonButton();
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
