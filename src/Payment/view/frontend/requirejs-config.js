var config = {
    paths: {
        amazonPaymentWidget: 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets'
    },
    waitSeconds: 7,
    map: {
        '*': {
            amazonCore: 'Amazon_Payment/js/amazon-core',
            amazonButton: 'Amazon_Payment/js/amazon-button',
            bluebird: 'Amazon_Payment/js/lib/bluebird.min',
            'Magento_Checkout/js/view/shipping': 'Amazon_Payment/js/view/shipping',
            'Magento_Checkout/js/view/payment/list': 'Amazon_Payment/js/view/payment/list'
        }
    }
};
