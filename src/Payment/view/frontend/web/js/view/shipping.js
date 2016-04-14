/*global define*/
define(
    [
        'jquery',
        "underscore",
        'ko',
        'Magento_Checkout/js/view/shipping',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Amazon_Payment/js/model/storage'
    ],
    function(
        $,
        _,
        ko,
        Component,
        setShippingInformationAction,
        stepNavigator,
        amazonStorage
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/shipping'
            },
            isAmazonLoggedIn: amazonStorage.isAmazonAccountLoggedIn,

            initialize: function () {
                var self = this;
                this._super();
            },

            /**
             * New setShipping Action for Amazon payments to bypass validation
             */
            setShippingInformation: function () {
                function setShippingInformationAmazon() {
                    setShippingInformationAction().done(
                        function() {
                            stepNavigator.next();
                        }
                    );
                }
                if(amazonStorage.isAmazonAccountLoggedIn()) {
                    setShippingInformationAmazon();
                } else {
                    if (this.validateShippingInformation()) {
                        setShippingInformationAmazon();
                    }
                }
            }
        });
    }
);
