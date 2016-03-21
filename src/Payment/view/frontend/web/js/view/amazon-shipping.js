define(['Magento_Checkout/js/view/shipping'], function (shippingComponent) {
    'use strict';

    return shippingComponent.extend({
        validateShippingInformation: function() {
            console.log('use this');
        }
    });
});