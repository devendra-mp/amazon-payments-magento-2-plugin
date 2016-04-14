define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/quote',
        'Amazon_Payment/js/model/storage',
        'mage/storage',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/error-processor'
    ],
    function(
        $,
        Component,
        ko,
        customer,
        customerData,
        quote,
        amazonStorage,
        storage,
        fullScreenLoader,
        getTotalsAction,
        errorProcessor
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
            isCustomerLoggedIn: customer.isLoggedIn,
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isAmazonEnabled: ko.observable(window.amazonPayment.isPwaEnabled),
            address: quote.shippingAddress,
            initialize: function () {
                self = this;
                this._super();
            },
            initPaymentWidget: function() {
                self.renderPaymentWidget();
                $('#amazon_payment').trigger('click'); //activate amazon payments method on render
            },
            /**
             * render Amazon payment Widget
             */
            renderPaymentWidget: function() {
                new OffAmazonPayments.Widgets.Wallet({
                    sellerId: self.options.sellerId,
                    amazonOrderReferenceId: amazonStorage.getOrderReference(),
                    onPaymentSelect: function(orderReference) {
                        self.setBillingAddressFromAmazon();
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
            },
            setBillingAddressFromAmazon: function() {
                var serviceUrl = 'rest/default/V1/amazon-billing-address/' + amazonStorage.getOrderReference(),
                    payload = {
                        addressConsentToken : amazonStorage.getAddressConsentToken()
                    };

                fullScreenLoader.startLoader();

                storage.put(
                    serviceUrl,
                    JSON.stringify(payload)
                ).done(
                    function() {
                        if (!quote.isVirtual()) {
                            getTotalsAction([]);
                        }
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                    }
                ).always(
                    function() {
                        fullScreenLoader.stopLoader();
                    }
                );
            }
        });
    }
);