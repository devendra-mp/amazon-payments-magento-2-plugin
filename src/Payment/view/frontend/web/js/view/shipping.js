/*global define*/
define(
    [
        'jquery',
        "underscore",
        'ko',
        'Magento_Checkout/js/view/shipping',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Amazon_Payment/js/model/storage',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/quote'
    ],
    function(
        $,
        _,
        ko,
        Component,
        customer,
        setShippingInformationAction,
        stepNavigator,
        amazonStorage,
        shippingService,
        quote
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/shipping'
            },
            isAmazonLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isLoading: ko.pureComputed(function() {
                return amazonStorage.isAmazonAccountLoggedIn() ? amazonStorage.isShippingMethodsLoading() : shippingService.isLoading();
            }, this),

            initialize: function () {
                var self = this;
                this._super();

                quote.shippingMethod.subscribe(function() {
                    if(typeof quote.shippingAddress().isAmazonAddress !== 'undefined') {
                        amazonStorage.isShippingMethodsLoading(false); //remove loader when shippingMethod is set
                        delete quote.shippingAddress().isAmazonAddress; //delete key now it's no longer needed
                    }
                });
            },

            /**
             * New setShipping Action for Amazon payments to bypass validation
             */
            setShippingInformation: function () {
                if(amazonStorage.isAmazonAccountLoggedIn()) {
                    this.isFormInline = false; //remove inline form constraint if logged into amazon
                }
                function setShippingInformationAmazon() {
                    setShippingInformationAction().done(
                        function() {
                            stepNavigator.next();
                        }
                    );
                }
                if(amazonStorage.isAmazonAccountLoggedIn() && customer.isLoggedIn()) {
                    setShippingInformationAmazon();
                } else {
                    //if using guest checkout or guest checkout with amazon pay we need to use the main validation
                    if (this.validateShippingInformation()) {
                        setShippingInformationAmazon();
                    }
                }
            }
        });
    }
);
