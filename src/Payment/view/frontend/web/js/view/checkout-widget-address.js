/*global define*/

define(
    [
        'jquery',
        "uiComponent",
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address',
        'Magento_Checkout/js/action/set-shipping-information',
        'Amazon_Payment/js/model/storage',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/address-converter',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/url-builder'
    ],
    function(
        $,
        Component,
        ko,
        customer,
        quote,
        selectShippingAddress,
        shippingProcessor,
        setShippingInformationAction,
        amazonStorage,
        shippingService,
        addressConverter,
        storage,
        fullScreenLoader,
        errorProcessor,
        urlBuilder
    ) {
        'use strict';
        var self;

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-widget-address'
            },
            options: {
                sellerId: window.amazonPayment.merchantId,
                addressWidgetDOMId: 'addressBookWidgetDiv'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isAmazonEnabled: ko.observable(window.amazonPayment.isPwaEnabled),
            rates: shippingService.getShippingRates(),
            initialize: function () {
                self = this;
                this._super();
            },
            /**
             * Call when component template is rendered
             */
            initAddressWidget: function() {
                self.renderAddressWidget();
            },
            /**
             * render Amazon address Widget
             */
            renderAddressWidget: function() {
                new OffAmazonPayments.Widgets.AddressBook({
                    sellerId: self.options.sellerId,
                    onOrderReferenceCreate: function(orderReference) {
                        var orderid = orderReference.getAmazonOrderReferenceId();
                        amazonStorage.setOrderReference(orderid);
                    },
                    onAddressSelect: function (orderReference) {
                        self.getShippingAddressFromAmazon();
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function (error) {
                    }
                }).bind(self.options.addressWidgetDOMId);
            },

            /**
             * Get shipping address from Amazon API
             */
            getShippingAddressFromAmazon: function() {
                amazonStorage.isShippingMethodsLoading(true);
                var serviceUrl = urlBuilder.createUrl('/amazon-shipping-address/:amazonOrderReference', {amazonOrderReference: amazonStorage.getOrderReference()}),
                    payload = {
                        addressConsentToken: amazonStorage.getAddressConsentToken()
                    };

                storage.put(
                    serviceUrl,
                    JSON.stringify(payload)
                ).done(
                    function (data) {
                        var amazonAddress = data.shift(),
                            addressData = addressConverter.formAddressDataToQuoteAddress(amazonAddress);

                        addressData.isAmazonAddress = true;
                        selectShippingAddress(addressData);
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        //remove shipping loader and set shipping rates to 0 on a fail
                        shippingService.setShippingRates([]);
                        amazonStorage.isShippingMethodsLoading(false);
                    }
                );
            },

            getAmazonOrderReference: function()
            {
                return amazonStorage.getOrderReference();
            },

            getAddressConsentToken: function()
            {
                return amazonStorage.getAddressConsentToken();
            }
        });
    }
);