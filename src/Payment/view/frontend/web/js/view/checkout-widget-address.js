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
        'amazonCore'
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
        amazonCore
    ) {
        'use strict';
        var self;

        var amazonOrderReferenceId = null;
        var addressConsentToken = null;

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-widget-address'
            },
            options: {
                sellerId: 'AUGT0HMCLQVX1',
                addressWidgetDOMId: 'addressBookWidgetDiv'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            isAmazonAccountLoggedIn: ko.observable(false),
            isAmazonEnabled: ko.observable(window.checkoutConfig.payment.amazonPayment.isEnabled),
            initialize: function () {
                self = this;
                this._super();
                quote.shippingMethod.subscribe(function (value) {
                    //console.log('shipping method');
                });

                amazonCore._onAmazonLoginReady();
                this.setupAddressWidget();
                amazonCore._loadAmazonWidgetsScript();
            },
            /**
             * Check to see whether user is currently logged into Amazon
             */
            verifyAmazonLoggedIn: function() {
                var loginOptions = {
                    scope: "profile payments:widget payments:shipping_address",
                    popup: true,
                    interactive: 'never'
                };
                amazon.Login.authorize (loginOptions, function(response) {
                    if(!response.error) {
                        addressConsentToken = response.access_token;
                        self.isAmazonAccountLoggedIn(true);
                    }
                });
            },
            /**
             * Setup events and bindings for the Amazon Address widget
             */
            setupAddressWidget: function() {
                window.onAmazonPaymentsReady = function() {
                    self.isAmazonAccountLoggedIn.subscribe(function(value) {
                        if(value) {
                            setTimeout(function() {
                                self.renderAddressWidget();
                            },2000);
                        }
                    });
                    self.verifyAmazonLoggedIn();
                }
            },
            /**
             * render Amazon address Widget
             */
            renderAddressWidget: function() {
                new OffAmazonPayments.Widgets.AddressBook({
                    sellerId: self.options.sellerId,
                    onOrderReferenceCreate: function(orderReference) {
                        amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();
                    },
                    onAddressSelect: function(orderReference) {
                        amazonCore._setOrderReference(orderReference);

                        var data = {
                            amazonOrderReferenceId : amazonOrderReferenceId,
                            addressConsentToken : addressConsentToken
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
                    onError: function(error) {
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