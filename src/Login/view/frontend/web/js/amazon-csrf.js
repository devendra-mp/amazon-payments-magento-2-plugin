define([
    'sjcl',
    'jquery',
    'mage/cookies'
], function(sjcl, $) {
    "use strict";

    return {
        options: {
            wordsLength: 8,
            cookieName: 'amazon-csrf-state'
        },
        generateNewValue: function() {
            var randomString = sjcl.codec.base64.fromBits(sjcl.random.randomWords(this.options.wordsLength));
            $.mage.cookies.set(this.options.cookieName, randomString);
            return randomString;
        },
        isValid: function(stateString) {
            var isValid = $.mage.cookies.get(this.options.cookieName) === stateString;
            this.clear(); // always clear nonce when validating
            return isValid;
        },
        clear: function() {
            $.mage.cookies.clear(this.options.cookieName);
        }
    }
});
