/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Catalog/js/catalog-add-to-cart',
    'jquery/ui'
], function($) {
    "use strict";

    $.widget('amazon.catalogAddToCart', $.mage.catalogAddToCart, {

        _create: function() {
            //this is overridden here and ignores the redirect option until fixed by Magento (as of 2.1)
            this._bindSubmit();
        },

        _bindSubmit: function() {
            var self = this;
            this.element.mage('validation');
            this.element.on('submit', function(e) {
                e.preventDefault();
                if(self.element.valid()) {
                    self.submitForm($(this));
                }
            });
        }
    });

    return $.amazon.catalogAddToCart;
});
