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
        var self,
            sandboxSimulationScenarios = ko.observableArray([
                {
                    labelText: 'Default',
                    simulationValue: 'default'
                },
                {
                    labelText: 'Authorization - Declined - InvalidPaymentMethod: Authorization soft decline',
                    simulationValue: 'Authorization:Declined:InvalidPaymentMethod'
                },
                {
                    labelText: 'Authorization - Declined - AmazonRejected: Authorization hard decline',
                    simulationValue: 'Authorization:Declined:AmazonRejected'
                },
                {
                    labelText: 'Authorization - Declined - TransactionTimedOut: Authorization time out',
                    simulationValue: 'Authorization:Declined:TransactionTimedOut'
                },
                {
                    labelText: 'Capture - Declined - AmazonRejected: Capture declined',
                    simulationValue: 'Capture:Declined:AmazonRejected'
                }
            ]);

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-sandbox-simulator'
            },
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isSandboxEnabled: ko.observable(window.amazonPayment.isSandboxEnabled),
            sandboxSimulationReference: amazonStorage.sandboxSimulationReference,
            sandboxSimulationScenarios: sandboxSimulationScenarios,
            initialize: function () {
                self = this;
                this._super();
            }
        });
    }
);