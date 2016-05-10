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
        'Amazon_Payment/js/action/place-order',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/url-builder'
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
        placeOrderAction,
        getTotalsAction,
        errorProcessor,
        addressConverter,
        selectBillingAddress,
        additionalValidators,
        urlBuilder
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
            shippingAddress: quote.shippingAddress,
            billingAddress: quote.billingAddress,
            isPlaceOrderDisabled: amazonStorage.isPlaceOrderDisabled,
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
                        amazonStorage.isPlaceOrderDisabled(true);
                        self.setBillingAddressFromAmazon();
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function(error) {
                        errorProcessor.process(error);
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
                var serviceUrl = urlBuilder.createUrl('/amazon-billing-address/:amazonOrderReference', {amazonOrderReference: amazonStorage.getOrderReference()}),
                    payload = {
                        addressConsentToken : amazonStorage.getAddressConsentToken()
                    };

                fullScreenLoader.startLoader();

                storage.put(
                    serviceUrl,
                    JSON.stringify(payload)
                ).done(
                    function(data) {
                        var amazonAddress = data.shift();
                        var addressData = addressConverter.formAddressDataToQuoteAddress(amazonAddress);

                        selectBillingAddress(addressData);
                        amazonStorage.isPlaceOrderDisabled(false);
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
            },
            getData: function() {
                return {
                    "method": this.item.method,
                    "additional_data": {
                        "sandbox_simulation_reference": amazonStorage.sandboxSimulationReference()
                    }
                }
            },
            placeOrder: function (data, event) {
                var self = this,
                    placeOrder;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), this.redirectAfterPlaceOrder);

                    $.when(placeOrder).fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    }).done(this.afterPlaceOrder.bind(this));
                    return true;
                }
                return false;
            }
        });
    }
);