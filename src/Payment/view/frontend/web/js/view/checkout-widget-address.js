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
        'Amazon_Payment/js/model/storage'

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
        amazonStorage
    ) {
        'use strict';
        var self;

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-widget-address'
            },
            options: {
                sellerId: 'AUGT0HMCLQVX1',
                addressWidgetDOMId: 'addressBookWidgetDiv'
            },
            isCustomerLoggedIn: amazonStorage.isCustomerLoggedIn,
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isAmazonEnabled: ko.observable(window.checkoutConfig.payment.amazonPayment.isEnabled),
            initialize: function () {
                self = this;
                this._super();
            },
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
                        var shippingAddress = quote.shippingAddress(),
                            data = {
                                addressConsentToken : amazonStorage.getAddressConsentToken()
                            };

                        $.ajax({
                            type : 'PUT',
                            url: '/rest/default/V1/amazon-shipping-address/' + amazonStorage.getOrderReference(),
                            data: JSON.stringify(data),
                            dataType: 'json',
                            contentType: 'application/json; charset=utf-8'
                        }).done(function(data) {
                            var shippingAddress = quote.shippingAddress();

                            $.each(data, function(k, v) {
                                for (var prop in v) {
                                    shippingAddress[prop] = v[prop];
                                }
                            });

                            selectShippingAddress(shippingAddress);

                            console.log(shippingAddress);

                            //shippingProcessor.getRates(self.getCurrentShippingAddress());
                        });
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function (error) {
                        // your error handling code
                    }
                }).bind(self.options.addressWidgetDOMId);
            },
            /**
             * Get the current Shipping address set in the quote model
             */
            getCurrentShippingAddress: function() {
                return quote.shippingAddress();
            },
            /**
             * Set the shipping address in the quote model
             * @param address
             */
            setCurrentShippingAddress: function(address) {
                quote.shippingAddress(address);
            }
        });
    }
);