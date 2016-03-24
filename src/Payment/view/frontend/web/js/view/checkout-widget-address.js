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
                        //need to call GetOrderReferenceDetails (PHP) so need to do a proxy
                        //ajax call which sends the orderReference and gets back the address
                        //once we have the address we need to set it via the quote model
                        //then call the below function via the shippingProcessor in order
                        //to get the new rates based on the address
                        var shippingAddress = quote.shippingAddress(),
                            data = {
                                amazonOrderReferenceId : amazonStorage.getOrderReference(),
                                addressConsentToken : amazonStorage.getAddressConsentToken()
                            };

                        $.ajax({
                            type : 'POST',
                            url: '/amazonpay/checkout/shipping',
                            data: data,
                            dataType: 'json'
                        }).done(function(data) {
                            var shippingAddress = quote.shippingAddress();

                            for (var prop in data) {
                                shippingAddress[prop] = data[prop];
                            }

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