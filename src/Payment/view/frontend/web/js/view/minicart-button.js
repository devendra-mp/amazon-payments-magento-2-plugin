/*global define*/

define(
    [
        'jquery',
        "uiComponent",
        'ko',
        'Amazon_Payment/js/model/storage'
    ],
    function(
        $,
        Component,
        ko,
        amazonStorage
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/minicart/minicart-button'
            },
            isAmazonEnabled: ko.observable(window.amazonPayment.isPwaEnabled),
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            initialize: function () {
                var self = this;
                this._super();
            }
        });
    }
);
