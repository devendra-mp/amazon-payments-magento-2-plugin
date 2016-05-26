<?php

namespace Page\Store;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class CustomerSection extends Page
{
    protected $path = '/customer/section/load/?sections=customer';

    public function isLoggedIn()
    {
        $script
            = 'return (function() { 
            var response = null;
            jQuery.ajax({
                url: "' . $this->getUrl([]) . '",
                success: function(data) {
                    response = data; 
                },
                async: false
            });
            return response;
        })();';

        $response = $this->getDriver()->evaluateScript($script);

        return ($response && isset($response['customer']['firstname']));
    }
}