<?php

namespace Amazon\Core\Client;

use PayWithAmazon\ClientInterface;

interface ClientFactoryInterface
{
    /**
     * Create amazon client instance
     *
     * @return ClientInterface
     */
    public function create($storeId = null);
}