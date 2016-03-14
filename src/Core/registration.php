<?php
/**
 * @todo: delete this require when interfaces have been split according to psr by amazon
 */
require __DIR__ . '/../../../../amzn/login-and-pay-with-amazon-sdk-php/PayWithAmazon/Interface.php';

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Amazon_Core',
    __DIR__
);
