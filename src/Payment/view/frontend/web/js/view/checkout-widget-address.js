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
        amazonCore
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
            isCustomerLoggedIn: customer.isLoggedIn,
            isAmazonAccountLoggedIn: ko.observable(false),
            isAmazonEnabled: ko.observable(window.checkoutConfig.payment.amazonPayment.isEnabled),
            initialize: function () {
                self = this;
                this._super();
                quote.shippingMethod.subscribe(function (value) {
                    //console.log('shipping method');
                });

                console.log(window.checkoutConfig.payment.amazonPayment.isEnabled);

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
                    console.log(response);
                    if(!response.error) {
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
                            },0);
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
                        orderReference.getAmazonOrderReferenceId();
                    },
                    onAddressSelect: function(orderReference) {
                        //need to call GetOrderReferenceDetails (PHP) so need to do a proxy
                        //ajax call which sends the orderReference and gets back the address
                        //once we have the address we need to set it via the quote model
                        //then call the below function via the shippingProcessor in order
                        //to get the new rates based on the address

                        //quote.shippingAddress();
                        //shippingProcessor.getRates(self.getCurrentShippingAddress());
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