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
                    simulationValue: 1
                },
                {
                    labelText: 'Authorization - Declined - InvalidPaymentMethod: Authorization soft decline',
                    simulationValue: 2
                },
                {
                    labelText: 'Authorization - Declined - AmazonRejected: Authorization hard decline',
                    simulationValue: 3
                },
                {
                    labelText: 'Authorization - Declined - TransactionTimedOut: Authorization time out',
                    simulationValue: 4
                },
                {
                    labelText: 'Capture - Declined - AmazonRejected: Capture declined',
                    simulationValue: 5
                }
            ]);

        return Component.extend({
            defaults: {
                template: 'Amazon_Payment/checkout-sandbox-simulator'
            },
            isAmazonAccountLoggedIn: amazonStorage.isAmazonAccountLoggedIn,
            isSandboxEnabled: ko.observable(window.amazonPayment.isSandboxEnabled),
            sandboxSimulationString: amazonStorage.sandboxSimulationString,
            sandboxSimulationScenarios: sandboxSimulationScenarios,
            initialize: function () {
                self = this;
                this._super();
            }
        });
    }
);