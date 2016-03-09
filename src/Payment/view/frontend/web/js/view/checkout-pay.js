/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/

define(
    [
        'jquery',
        "uiComponent",
        'ko'
    ],
    function(
        $,
        Component,
        ko
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-pay'
            },
            initialize: function () {
                var self = this;
                this._super();

                window.onAmazonLoginReady = function() {
                    amazon.Login.setClientId('amzn1.application-oa2-client.fe5d817cfb2b45dcaf1c2c15966454bb');
                };

                window.onAmazonPaymentsReady = function(){
                    // render the button here
                    var authRequest,
                        loginOptions;

                    OffAmazonPayments.Button("LoginWithAmazon", "A1BJXVS5F6XP", {
                        type:  "PwA",
                        color: "Gold",
                        language: "en-GB",

                        authorization: function() {
                            loginOptions = {scope: "profile payments:widget payments:shipping_address"};
                            authRequest = amazon.Login.authorize (loginOptions, "https://amazon-payment.dev/customer/account");
                        },

                        onError: function(error) {
                            // your error handling code.
                            // alert("The following error occurred: "
                            //        + error.getErrorCode()
                            //        + ' - ' + error.getErrorMessage());
                        }
                    });
                };

                setTimeout(function() {
                    var my_awesome_script = document.createElement('script');

                    my_awesome_script.setAttribute('src','https://static-eu.payments-amazon.com/OffAmazonPayments/uk/sandbox/lpa/js/Widgets.js');

                    document.head.appendChild(my_awesome_script);

                    console.log('new');
                }, 800);

            }
        });
    }
);