/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/

define(
    [
        'jquery',
        "uiComponent",
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-shipping-address',
        'amazonCore'
    ],
    function(
        $,
        Component,
        ko,
        customer,
        quote,
        selectShippingAddress,
        amazonCore
    ) {
        'use strict';

        var self;
        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-widget-address'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            isAmazonAccountLoggedIn: ko.observable(false),
            initialize: function () {
                self = this;
                this._super();
                quote.shippingMethod.subscribe(function (value) {
                    console.log('shipping method');
                });

                amazonCore._onAmazonLoginReady();
                this.loadWidget();
                amazonCore._loadAmazonWidgetsScript();
            },
            testShippingAddress: function() {
                //console.log(quote.shippingAddress());

                // render the button here
                var authRequest,
                    loginOptions;

                    loginOptions = {scope: "profile payments:widget payments:shipping_address", popup: true, interactive: 'never' };

                    authRequest = amazon.Login.authorize (loginOptions, function(response) {
                        if(!response.error) {
                            self.isAmazonAccountLoggedIn(true);

                        }
                    });

            },
            loadWidget: function() {
                window.onAmazonPaymentsReady = function() {
                    self.testShippingAddress();
                    self.isAmazonAccountLoggedIn.subscribe(function(value) {
                        if(value) {
                            new OffAmazonPayments.Widgets.AddressBook({
                                sellerId: 'A1BJXVS5F6XP',
                                onOrderReferenceCreate: function(orderReference) {
                                    console.log('onOrderReferenceCreate fired');
                                    orderReference.getAmazonOrderReferenceId();
                                },
                                onAddressSelect: function(orderReference) {
                                    console.log('onAddressSelect fired');
                                    // Replace the following code with the action that you want to perform
                                    // after the address is selected.
                                    // The amazonOrderReferenceId can be used to retrieve
                                    // the address details by calling the GetOrderReferenceDetails
                                    // operation. If rendering the AddressBook and Wallet widgets on the
                                    // same page, you should wait for this event before you render the
                                    // Wallet widget for the first time.
                                    // The Wallet widget will re-render itself on all subsequent
                                    // onAddressSelect events, without any action from you. It is not
                                    // recommended that you explicitly refresh it.
                                },
                                design: {
                                    designMode: 'responsive'
                                },
                                onError: function(error) {
                                    // your error handling code
                                }
                            }).bind('addressBookWidgetDiv');
                        }
                    });
                }
            }
        });
    }
);