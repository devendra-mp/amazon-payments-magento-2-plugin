define(
    [],
    function() {
        'use strict';

        var config = window.amazonPayment || {};

        return {
            getValue: function(key, defaultValue) {
                if (config.hasOwnProperty(key)) {
                    return config[key];
                } else if (defaultValue !== undefined) {
                    return defaultValue;
                }
            },
            isDefined: function() {
                return window.amazonPayment !== undefined
            }
        };
    }
);
