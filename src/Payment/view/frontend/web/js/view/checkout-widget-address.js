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
                    //console.log('shipping method');
                });

                amazonCore._onAmazonLoginReady();
                this.setupAddressWidget();
                amazonCore._loadAmazonWidgetsScript();
            },
            checkAmazonLoggedIn: function() {
                var loginOptions = {scope: "profile payments:widget payments:shipping_address", popup: true, interactive: 'never' };

                amazon.Login.authorize (loginOptions, function(response) {
                    if(!response.error) {
                        self.isAmazonAccountLoggedIn(true);
                    }
                });

            },
            setupAddressWidget: function() {
                window.onAmazonPaymentsReady = function() {
                    self.renderAddressWidget();
                    self.checkAmazonLoggedIn();
                }
            },
            renderAddressWidget: function() {
                //subscribe to loggedIn value
                this.isAmazonAccountLoggedIn.subscribe(function(value) {
                    if(value) {
                        setTimeout(function() {
                            new OffAmazonPayments.Widgets.AddressBook({
                                sellerId: 'A1BJXVS5F6XP',
                                onOrderReferenceCreate: function(orderReference) {
                                    console.log('onOrderReferenceCreate fired');
                                    orderReference.getAmazonOrderReferenceId();
                                },
                                onAddressSelect: function(orderReference) {
                                    console.log('onAddressSelect fired');
                                },
                                design: {
                                    designMode: 'responsive'
                                },
                                onError: function(error) {
                                    // your error handling code
                                }
                            }).bind('addressBookWidgetDiv');
                        }, 2000);

                    }
                });
            }
        });
    }
);