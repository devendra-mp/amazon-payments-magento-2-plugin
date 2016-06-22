define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Customer/js/section-config',
    'Amazon_Payment/js/model/amazonPaymentConfig',
    'amazonCore',
    'jquery/ui'
], function($, customerData, sectionConfig, amazonPaymentConfig) {
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

            if (!amazonPaymentConfig.getValue('isLwaEnabled')) {
                return;
            }

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
            if(amazonPaymentConfig.isDefined()) {
                _this.options.merchantId = amazonPaymentConfig.getValue('merchantId');
                _this.options.buttonType = (_this.options.buttonType == 'LwA') ? amazonPaymentConfig.getValue('buttonTypeLwa') : amazonPaymentConfig.getValue('buttonTypePwa');
                _this.options.buttonColor = amazonPaymentConfig.getValue('buttonColor');
                _this.options.buttonSize = amazonPaymentConfig.getValue('buttonSize');
                _this.options.redirectUrl = amazonPaymentConfig.getValue('redirectUrl');
                _this.options.loginPostUrl = amazonPaymentConfig.getValue('loginPostUrl');
                _this.options.loginScope = amazonPaymentConfig.getValue('loginScope');
            }
        },
        secureHttpsCallback: function(event) {
            var sections = sectionConfig.getAffectedSections(_this.options.loginPostUrl);
            if (sections) {
                customerData.invalidate(sections);
            }
            window.location = _this.options.redirectUrl + '?access_token=' + event.access_token;
        },
        _popupCallback: function() {
            return (window.location.protocol === 'https:') ? _this.secureHttpsCallback : amazonPaymentConfig.getValue('oAuthHashRedirectUrl');
        },
        getPopUp: function() {
            return (window.location.protocol === 'https:');
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
                    loginOptions = {scope: _this.options.loginScope, popup: _this.getPopUp()};
                    authRequest = amazon.Login.authorize(loginOptions, _this._popupCallback());
                }
            });
        }
    });

    return $.amazon.AmazonButton;
});
