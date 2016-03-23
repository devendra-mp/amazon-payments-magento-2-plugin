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

            //load amazon global calls on window object
            //core._onAmazonLoginReady();

            _this._onAmazonPaymentsReady();


            //load amazon widgets script
            //core._loadAmazonWidgetsScript();
        },
        _verifyCheckoutConfig: function() {
            if(window.checkoutConfig.payment.amazonPayment !== undefined && _this.options.buttonType === 'PwA') {
                _this.options.buttonColor = window.checkoutConfig.payment.amazonPayment.buttonColor;
                _this.options.buttonSize = window.checkoutConfig.payment.amazonPayment.buttonSize;
                _this.options.redirectURL = window.checkoutConfig.payment.amazonPayment.redirectURL;
            }
        },
        /**
         * onAmazonPaymentsReady
         * @private
         */
        _onAmazonPaymentsReady: function() {

                //window.onAmazonPaymentsReady = function(){
                // render the button here
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

            //};
        }
    });

    return $.amazon.AmazonButton;
});
