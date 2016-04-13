/*global define*/
define(
    [
        'jquery',
        "underscore",
        'ko',
        'uiComponent',
        'Amazon_Payment/js/model/storage'
    ],
    function(
        $,
        _,
        ko,
        Component,
        amazonStorage
    ) {
        'use strict';

        var self;

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/notification'
            },
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            chargeOnOrder: ko.observable(window.amazonPayment.chargeOnOrder),
            initialize: function () {
                self = this;
                this._super();
            }
        });
    }
);
