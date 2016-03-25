var config = {
    paths: {
        amazonPaymentWidget: 'https://static-na.payments-amazon.com/OffAmazonPayments/us/sandbox/js/Widgets'
    },
    waitSeconds: 7,
    map: {
        '*': {
            amazonCore: 'Amazon_Payment/js/amazon-core',
            amazonButton: 'Amazon_Payment/js/amazon-button',
            bluebird: 'Amazon_Payment/js/lib/bluebird.min'
        }
    }
};
