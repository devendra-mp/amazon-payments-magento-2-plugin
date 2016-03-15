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
        'Magento_Customer/js/model/customer'
    ],
    function(
        $,
        Component,
        ko,
        customer
    ) {
        'use strict';
        return Component.extend({
             defaults: {
                template: 'Amazon_Payment/checkout-init'
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            initialize: function () {
                var self = this;
                this._super();
            }
        });
    }
);