define([
    'underscore',
    'ko',
    'Magento_Checkout/js/view/payment/list',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Amazon_Payment/js/model/storage'
], function (_, ko, Component, paymentMethods, checkoutDataResolver, amazonStorage) {
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
            this._handleDeclines();

            return this;
        },
        _changeDecline: function(value) {
            amazonStorage.amazonDeclineCode(value);
        },
        _handleDeclines: function() {
            //amazonStorage
            amazonStorage.amazonDeclineCode.subscribe(function(declined) {
                if(declined === 4273) {
                    this._logoutOfAmazon();
                    this._reloadPaymentMethods();
                } else {
                    this._removePaymentMethods();
                }
            }, this);
        },
        _logoutOfAmazon: function() {
            console.log('logout');
            amazon.Login.logout();
            amazonStorage.setAmazonAccountLoggedOut();
        },
        _reloadPaymentMethods: function() {
            _.each(paymentMethods(), function (paymentMethodData) {
                if (paymentMethodData.method === 'amazon_payment') {
                    this.removeRenderer(paymentMethodData.method);
                } else {
                    this.createRenderer(paymentMethodData); // rerender other payment methods
                }
            }, this);
        },
        _removePaymentMethods: function() {
            _.each(paymentMethods(), function (paymentMethodData) {
                if (paymentMethodData.method !== 'amazon_payment') {
                    this.removeRenderer(paymentMethodData.method);
                } else {
                    this.createRenderer(paymentMethodData);
                }
            }, this);
        }
    });
});
