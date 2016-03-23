<?php
use Magento\Framework\Component\ComponentRegistrar;

$registrar = new ComponentRegistrar();

if ($registrar->getPath(ComponentRegistrar::MODULE, 'Amazon_Core') === null) {
    require __DIR__ . '/../../../../amzn/login-and-pay-with-amazon-sdk-php/PayWithAmazon/Interface.php';
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Amazon_Core', __DIR__);
}
