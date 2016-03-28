define([
    'jquery',
    'amazonCore',
    'jquery/ui'
], function($, core) {
    "use strict";

    var _this;

    $.widget('amazon.AmazonLogout', {
        options: {
            onInit: false
        },
        /**
         * Create Amazon Logout Widget
         * @private
         */
        _create: function() {
            _this = this;
            if(this.options.onInit) {
                core.AmazonLogout(); //logout amazon user on init
            }
        },
        /**
         * Logs out a user if called directly
         * @private
         */
        _logoutAmazonUser: function() {
            core.AmazonLogout();
        }
    });

    return $.amazon.AmazonLogout;
});
