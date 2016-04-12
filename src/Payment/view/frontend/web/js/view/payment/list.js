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
                        if(change.value.method === 'amazon_payment') {
                            if(!amazonStorage.isAmazonAccountLoggedIn()) {
                                change.status = 'deleted';
                            }
                        } else {
                            if(amazonStorage.isAmazonAccountLoggedIn()) {
                                change.status = 'deleted';
                            }
                        }
                        if (change.status === 'deleted') {
                            this.removeRenderer(change.value.method);
                        }
                    }, this);

                    //add renderer for "added" payment methods
                    _.each(changes, function (change) {
                        if (change.status === 'added') {
                            this.createRenderer(change.value);
                        }
                    }, this);
                }, this, 'arrayChange');

            this._super();

            return this;
        }
    });
});
