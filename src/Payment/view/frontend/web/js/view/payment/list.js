define([
    'jquery',
    'underscore',
    'ko',
    'Magento_Checkout/js/view/payment/list',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Amazon_Payment/js/model/storage'
], function ($, _, ko, Component, paymentMethods, checkoutDataResolver, amazonStorage) {
    'use strict';

    return Component.extend({
        /**
         * Initialize view.
         *
         * @returns {Component} Chainable.
         */
        initialize: function () {
            paymentMethods.subscribe(
                function (changes) {
                    checkoutDataResolver.resolvePaymentMethod();
                    //remove renderer for "deleted" payment methods
                    _.each(changes, function (change) {
                        if(amazonStorage.isAmazonAccountLoggedIn() && change.value.method !== 'amazon_payment') {
                            this.removeRenderer(change.value.method);
                            change.status = 'deleted';
                        }
                    }, this);
                }, this, 'arrayChange');

            this._super();
            this._setupDeclineHandler();

            return this;
        },
        /**
         * handle decline codes
         * @private
         */
        _setupDeclineHandler: function() {
            amazonStorage.amazonDeclineCode.subscribe(function(declined) {
                switch(declined) {
                    //hard decline
                    case 4273:
                        amazonStorage.amazonlogOut();
                        this._reloadPaymentMethods();
                        amazonStorage.amazonDeclineCode(false);
                        break;
                    //soft decline
                    case 7638:
                        amazonStorage.isPlaceOrderDisabled(true);
                        this._reInitializeAmazonWalletWidget();
                        this._removeDiscountCodesOption();
                        amazonStorage.amazonDeclineCode(false);
                        break;
                    default:
                        amazonStorage.amazonDeclineCode(false);
                        break;
                }
            }, this);
        },
        /**
         * reload payment methods on decline
         * @private
         */
        _reloadPaymentMethods: function() {
            _.each(paymentMethods(), function (paymentMethodData) {
                if (paymentMethodData.method === 'amazon_payment' && !amazonStorage.isAmazonAccountLoggedIn()) {
                    this.removeRenderer(paymentMethodData.method);
                } else {
                    this.createRenderer(paymentMethodData);
                }
            }, this);
        },
        /**
         * re-intialises Amazon wallet widget
         * @private
         */
        _reInitializeAmazonWalletWidget: function() {
            var items = this.getRegion('payment-method-items');
            _.find(items(), function (value) {
                if (value.index === 'amazon_payment') {
                    value.renderPaymentWidget();
                }
            }, this);
        },
        /**
         * removes discount codes option from payment
         * @private
         */
        _removeDiscountCodesOption: function() {
            $('.payment-option.discount-code', '#payment').remove();
        }
    });
});
