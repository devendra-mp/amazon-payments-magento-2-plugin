define([
    'jquery',
    'amazonPayment',
    'bluebird'
], function($) {
    "use strict";

    var orderReference,
        clientId = 'amzn1.application-oa2-client.15d69a1a3b83453a81ab480224d811cd';
        
    function setClientId(cid) {
        amazon.Login.setClientId(cid);
    }

    if(typeof amazon === 'undefined') {
        window.onAmazonLoginReady = function() {
           setClientId(clientId);
        }
    } else {
      setClientId(clientId);
    }

    return {
        _setOrderReference: function(or) {
            orderReference = or;
        },
        _getOrderReference: function() {
            return orderReference;
        },
        verifyAmazonLoggedIn: function() {
            var loginOptions = {
                scope: "profile payments:widget payments:shipping_address",
                popup: true,
                interactive: 'never'
            };

            return new Promise(function(resolve, reject) {
                amazon.Login.authorize (loginOptions, function(response) {
                    resolve(!response.error);
                });
            });
        }

    };
});
