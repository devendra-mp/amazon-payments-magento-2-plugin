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
                        break;
                    //soft decline
                    case 7638:
                        this._reInitializeAmazonWalletWidget();
                        break;
                    default:
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
         * handles soft decline
         * @private
         */
        _reInitializeAmazonWalletWidget: function() {
            //soft decline
        }
    });
});
