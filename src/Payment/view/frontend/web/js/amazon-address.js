define([
    'jquery',
    'amazonCore',
    'jquery/ui'
], function($, core) {
    "use strict";

    var _this,
        $addressWidget;

    $.widget('amazon.AmazonAddress', {
        options: {
            widgetsScript: 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js'
        },
        _create: function() {
            _this = this;
            $addressWidget = this.element;

            //load amazon widgets script
            //core._onAmazonLoginReady();
            //this._loadAmazonAddressWidget();

            //load it
            //core._loadAmazonWidgetsScript();
        },
        /**
         * loadAmazonAddressWidget
         * @private
         */
        _loadAmazonAddressWidget: function() {
            window.onAmazonPaymentsReady = function(){
                new OffAmazonPayments.Widgets.AddressBook({
                    sellerId: 'A1BJXVS5F6XP',
                    onOrderReferenceCreate: function(orderReference) {
                        console.log('onOrderReferenceCreate fired');
                        orderReference.getAmazonOrderReferenceId();
                    },
                    onAddressSelect: function(orderReference) {
                        console.log('onAddressSelect fired');
                        // Replace the following code with the action that you want to perform
                        // after the address is selected.
                        // The amazonOrderReferenceId can be used to retrieve
                        // the address details by calling the GetOrderReferenceDetails
                        // operation. If rendering the AddressBook and Wallet widgets on the
                        // same page, you should wait for this event before you render the
                        // Wallet widget for the first time.
                        // The Wallet widget will re-render itself on all subsequent
                        // onAddressSelect events, without any action from you. It is not
                        // recommended that you explicitly refresh it.
                    },
                    design: {
                        designMode: 'responsive'
                    },
                    onError: function(error) {
                        // your error handling code
                    }
                }).bind($addressWidget.attr('id'));
            }
        }
    });

    return $.amazon.AmazonAddress;
});

