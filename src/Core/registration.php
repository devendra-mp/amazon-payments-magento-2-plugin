<?php
use Magento\Framework\Component\ComponentRegistrar;

$registrar = new ComponentRegistrar();

if ($registrar->getPath(ComponentRegistrar::MODULE, 'Amazon_Core') === null) {
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Amazon_Core', __DIR__);
}
