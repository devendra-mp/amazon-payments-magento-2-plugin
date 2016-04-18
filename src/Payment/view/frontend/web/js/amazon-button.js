define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/section-config',
    'jquery/ui'
], function($, customerData, sectionConfig) {
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
            redirectUrl: null,
            loginPostUrl: null
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
                _this.options.redirectUrl = window.amazonPayment.redirectUrl;
                _this.options.loginPostUrl = window.amazonPayment.loginPostUrl;
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
                        loginOptions = {scope: "profile payments:widget payments:shipping_address payments:billing_address"};
                        authRequest = amazon.Login.authorize(loginOptions, function(event) {
                            var sections = sectionConfig.getAffectedSections(_this.options.loginPostUrl);
                            if (sections) {
                                customerData.invalidate(sections);
                            }
                            window.location = _this.options.redirectUrl + '?access_token=' + event.access_token;
                        });
                    }
                });
        }
    });

    return $.amazon.AmazonButton;
});
