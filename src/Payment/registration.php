<?php
use Magento\Framework\Component\ComponentRegistrar;

$registrar = new ComponentRegistrar();

if ($registrar->getPath(ComponentRegistrar::MODULE, 'Amazon_Payment') === null) {
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Amazon_Payment', __DIR__);
}
