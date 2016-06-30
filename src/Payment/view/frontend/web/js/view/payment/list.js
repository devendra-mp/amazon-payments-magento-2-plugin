define([
    'jquery',
    'underscore',
    'ko',
    'Magento_Checkout/js/view/payment/list',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    'Amazon_Payment/js/model/storage'

], function ($, _, ko, Component, paymentMethods, checkoutDataResolver, addressConverter, quote, amazonStorage) {
    'use strict';

    var self;

    return Component.extend({
        /**
         * Initialize view.
         *
         * @returns {Component} Chainable.
         */
        initialize: function () {

            self = this;
            this._hidePaymentMethodsOnLoad(); //hide methods on load

            //subscribe to payment methods to remove other payment methods from render list
            paymentMethods.subscribe(function (changes) {
                checkoutDataResolver.resolvePaymentMethod();
                //remove renderer for "deleted" payment methods
                _.each(changes, function (change) {
                    if(amazonStorage.isAmazonAccountLoggedIn() && change.value.method !== 'amazon_payment') {
                        this.removeRenderer(change.value.method);
                        change.status = 'deleted';
                    }
                }, this);
            }, this, 'arrayChange');

            this._setupDeclineHandler();
            this._super();

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
                        this._populateShippingAddressForm();
                        amazonStorage.amazonlogOut();
                        this._reloadPaymentMethods();
                        amazonStorage.amazonDeclineCode(false);
                        break;
                    //soft decline
                    case 7638:
                        amazonStorage.isPlaceOrderDisabled(true);
                        this._reInitializeAmazonWalletWidget();
                        this._hideEditableOptions();
                        amazonStorage.amazonDeclineCode(false);
                        break;
                    default:
                        amazonStorage.amazonDeclineCode(false);
                        break;
                }
            }, this);
        },
        /**
         * When payment methods exist on load hook into widget render to remove when widget has rendered
         * @private
         */
        _hidePaymentMethodsOnLoad: function() {
            if(paymentMethods().length > 0) {
                //if the payment methods are already set
                $(document).on('rendered', '#amazon_payment', function () {
                    _.each(paymentMethods(), function (payment) {
                        if (amazonStorage.isAmazonAccountLoggedIn() && payment.method !== 'amazon_payment') {
                            this.removeRenderer(payment.method);
                        }
                    }, self);
                });
            }
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
         * hides editable content and links to prevent unexptect behaviour
         * @private
         */
        _hideEditableOptions: function() {
            $('.payment-option.discount-code', '#payment').remove();
            $('.action-edit', '.shipping-information').remove();
            $('.opc-progress-bar-item._complete', '.opc-progress-bar').addClass('lock-step');
        },
        /**
         * Populate shipping address form in shipping step from quote model
         * @private
         */
        _populateShippingAddressForm: function() {
            var shippingAddress = quote.shippingAddress(),
                addressData = addressConverter.formAddressDataToQuoteAddress(shippingAddress);

            function populateShippingForm() {
                //Copy form data to quote shipping address object
                for (var field in addressData) {
                    //populate form
                    if (addressData.hasOwnProperty(field)
                        && shippingAddress.hasOwnProperty(field)
                        && typeof addressData[field] != 'function'
                    ) {
                        $('input[name="' + field + '"]', '#co-shipping-form').val(addressData[field]);
                    }
                }
            }

            //check to see if user is logged out of amazon (otherwise shipping form won't be in DOM)
            if(!this.isAmazonAccountLoggedIn) {
                populateShippingForm();
            }
            //subscribe to logout and trigger shippingform population when logged out.
            this.isAmazonAccountLoggedIn.subscribe(function(loggedIn) {
                if(!loggedIn) {
                    populateShippingForm();
                }
            });
        }
    });
});
