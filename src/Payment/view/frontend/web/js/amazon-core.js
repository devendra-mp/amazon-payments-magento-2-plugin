define([
    'jquery',
    'ko',
    'amazonPaymentWidget',
    'bluebird'
], function($, ko) {
    "use strict";

    var clientId = 'amzn1.application-oa2-client.15d69a1a3b83453a81ab480224d811cd',
        amazonDefined = ko.observable(false),
        accessToken = ko.observable(null);
        
    function setClientId(cid) {
        amazonDefined(true);
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
        /**
         * Verify a user is logged into amazon
         * @returns {*}
         */
        verifyAmazonLoggedIn: function() {
            var loginOptions = {
                scope: "profile payments:widget payments:shipping_address",
                popup: true,
                interactive: 'never'
            };

            return new Promise(function(resolve, reject) {
                amazon.Login.authorize (loginOptions, function(response) {
                    accessToken(response.access_token);
                    !response.error ? resolve(!response.error) : reject(response.error);
                });
            }).catch(function(e) {
                console.log('error: ' + e);
            });
        },
        amazonDefined: amazonDefined,
        accessToken: accessToken
    };
});
