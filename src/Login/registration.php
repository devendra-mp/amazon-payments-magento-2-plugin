<?php
use Magento\Framework\Component\ComponentRegistrar;

$registrar = new ComponentRegistrar();

if ($registrar->getPath(ComponentRegistrar::MODULE, 'Amazon_Login') === null) {
    ComponentRegistrar::register(ComponentRegistrar::MODULE, 'Amazon_Login', __DIR__);
}
