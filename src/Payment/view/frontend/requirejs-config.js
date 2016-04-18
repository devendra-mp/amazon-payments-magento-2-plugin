var config = {
    paths: {
        amazonPaymentWidget: 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets'
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
