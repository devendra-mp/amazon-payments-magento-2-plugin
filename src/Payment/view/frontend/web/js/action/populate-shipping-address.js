/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'uiRegistry',
        'Magento_Checkout/js/checkout-data',
        'Amazon_Payment/js/model/storage'
    ],
    function ($, addressConverter, quote, registry, checkoutData, amazonStorage) {
        'use strict';

        function populateShippingForm() {
            var shippingAddressData = checkoutData.getShippingAddressFromData();
            
            registry.async('checkoutProvider')(function (checkoutProvider) {
                checkoutProvider.set(
                    'shippingAddress',
                    $.extend({}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                );
            });

        }

        /**
         * Populate shipping address form in shipping step from quote model
         * @private
         */
        return function() {
            //check to see if user is logged out of amazon (otherwise shipping form won't be in DOM)
            if(!amazonStorage.isAmazonAccountLoggedIn) {
                populateShippingForm();
            }
            //subscribe to logout and trigger shippingform population when logged out.
            amazonStorage.isAmazonAccountLoggedIn.subscribe(function(loggedIn) {
                if(!loggedIn) {
                    populateShippingForm();
                }
            });
        }
    }
);
