var config = {
    map: {
        '*': {
            amazonCore: 'Amazon_Payment/js/amazon-core',
            amazonWidgetsLoader: 'Amazon_Payment/js/amazon-widgets-loader',
            amazonButton: 'Amazon_Payment/js/amazon-button',
            amazonProductAdd: 'Amazon_Payment/js/amazon-product-add',
            bluebird: 'Amazon_Payment/js/lib/bluebird.min',
            amazonPaymentConfig: 'Amazon_Payment/js/model/amazonPaymentConfig',
            sjcl: 'Amazon_Payment/js/lib/sjcl.min',
            //this is a fix for Magento 2.1 (ajax / validation fails on add to cart)
            catalogAddToCart: 'Amazon_Payment/js/catalog-add-to-cart'
        }
    }
};
