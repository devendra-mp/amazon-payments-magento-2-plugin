define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form'
    ],
    function ($, Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/payment/amazon-form'
            },

            getCode: function() {
                return 'amazon_payment';
            },

            isActive: function() {
                return true;
            }
        });
    }
);