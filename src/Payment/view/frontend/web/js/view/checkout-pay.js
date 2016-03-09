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
        'amazonPayment'
    ],
    function(
        $,
        Component,
        ko
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-pay'
            },
            initialize: function () {
                var self = this;
                this._super();
                console.log('hi');
            }
        });
    }
);