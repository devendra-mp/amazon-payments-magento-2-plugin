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
            /**
             * Check to see whether user is currently logged into Amazon
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
                    onOrderReferenceCreate: function (orderReference) {
                        var orderid = orderReference.getAmazonOrderReferenceId();
                        amazonStorage.setOrderReference(orderid);
                    },
                    onAddressSelect: function (orderReference) {
                        console.log(orderReference);

                        //need to call GetOrderReferenceDetails (PHP) so need to do a proxy
                        //ajax call which sends the orderReference and gets back the address
                        //once we have the address we need to set it via the quote model
                        //then call the below function via the shippingProcessor in order
                        //to get the new rates based on the address
                        var shippingAddress = quote.shippingAddress();

                        //update the current address model from the quote model
                        shippingAddress.city = 'liverpool';
                        shippingAddress.company = 'Session';
                        shippingAddress.firstname = 'David';
                        shippingAddress.countryId = 'GB';
                        shippingAddress.lastname = 'Jones';
                        shippingAddress.street = ['17 conway drive'];
                        shippingAddress.region = 'merseyside';
                        shippingAddress.postcode = 'wc3 h76';
                        shippingAddress.regionId = 0;
                        shippingAddress.telephone = '02058956587';

                        //assign the shipping address to the quote model via method
                        selectShippingAddress(shippingAddress);

                        //shippingProcessor.getRates(self.getCurrentShippingAddress());
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