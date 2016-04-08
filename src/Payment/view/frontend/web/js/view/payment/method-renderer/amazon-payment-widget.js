define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/quote',
        'Amazon_Payment/js/model/storage'
    ],
    function(
        $,
        Component,
        ko,
        customer,
        customerData,
        quote,
        amazonStorage
    ) {
        'use strict';

        var self,
            countryData = customerData.get('directory-data');

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/payment/amazon-payment-widget'
            },

            options: {
                sellerId: window.amazonPayment.merchantId,
                paymentWidgetDOMId: 'walletWidgetDiv'
            },
            isCustomerLoggedIn: amazonStorage.isCustomerLoggedIn,
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isAmazonEnabled: ko.observable(window.amazonPayment.isPwaEnabled),
            address: quote.shippingAddress,
            initialize: function () {
                self = this;
                this._super();
            },
            initPaymentWidget: function() {
                self.renderPaymentWidget();
                $('#amazon_payment').trigger('click');
            },
            /**
             * render Amazon payment Widget
             */
            renderPaymentWidget: function() {
                new OffAmazonPayments.Widgets.Wallet({
                    sellerId: self.options.sellerId,
                    amazonOrderReferenceId: amazonStorage.getOrderReference(),
                    onPaymentSelect: function(orderReference) {

                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function(error) {
                        // Your error handling code.
                    }
                }).bind(self.options.paymentWidgetDOMId);
            },
            getCode: function() {
                return 'amazon_payment';
            },
            isActive: function() {
                return true;
            },
            getCountryName: function(countryId) {
                return (countryData()[countryId] != undefined) ? countryData()[countryId].name : "";
            },
            checkCountryName: function(countryId) {
                return (countryData()[countryId] != undefined);
            }
        });
    }
);