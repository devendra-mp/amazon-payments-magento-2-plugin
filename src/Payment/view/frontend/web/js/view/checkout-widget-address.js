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
        'Magento_Checkout/js/model/shipping-service'
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
        shippingService
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
            initAddressWidget: function() {
                self.renderAddressWidget();
            },
            /**
             * render Amazon address Widget
             */
            renderAddressWidget: function() {

                this.rates.subscribe(function(value) {
                    if (value.length > 0) {
                        self.toggleNextStepActivation(true);
                    }
                });

                new OffAmazonPayments.Widgets.AddressBook({
                    sellerId: self.options.sellerId,
                    onOrderReferenceCreate: function(orderReference) {
                        var orderid = orderReference.getAmazonOrderReferenceId();
                        amazonStorage.setOrderReference(orderid);
                    },
                    onAddressSelect: function (orderReference) {
                        var data = {
                                addressConsentToken : amazonStorage.getAddressConsentToken()
                            };

                        $.ajax({
                            type : 'PUT',
                            url: '/rest/default/V1/amazon-shipping-address/' + amazonStorage.getOrderReference(),
                            data: JSON.stringify(data),
                            dataType: 'json',
                            contentType: 'application/json; charset=utf-8'
                        }).done(function(data) {
                            var shippingAddress = quote.shippingAddress(),
                                addressData = data.shift();

                            for (var prop in addressData) {
                                shippingAddress[prop] = addressData[prop];
                            }

                            selectShippingAddress(shippingAddress);

                            //shippingProcessor.getRates(self.getCurrentShippingAddress());
                        }).always(function() {
                            //TODO: add error handling
                            self.toggleNextStepActivation(false);
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
            toggleNextStepActivation: function(value) {
                $('.continue', '#shipping-method-buttons-container').toggleClass('disabled', value);
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