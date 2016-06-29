define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'amazonCore',
    'jquery/ui'
], function($, customerData) {
    "use strict";

    var _this,
        addedViaAmazon = false;

    $.widget('amazon.AmazonProductAdd', {
        options: {
            addToCartForm: '#product_addtocart_form'
        },
        _create: function() {
            _this = this;
            this.setupTriggers();
        },
        /**
         * Setup triggers when item added to cart if amazon pay button pressed
         */
        setupTriggers: function() {
            this.cart = customerData.get('cart');

            //subscribe to add to cart event
            this.cart.subscribe(function() {
                //only trigger the amazon button click if the user has chosen to add to cart via this method
                if(addedViaAmazon) {
                    addedViaAmazon = false;
                    $('.login-with-amazon img').trigger('click');
                }
            }, this);

            //setup binds for click
            $('#amazon-addtoCart').on('click', function(e) {
                if($(_this.options.addToCartForm).valid()) {
                    addedViaAmazon = true;
                    $(_this.options.addToCartForm).submit();
                }
            });
        }
       
    });

    return $.amazon.AmazonProductAdd;
});
