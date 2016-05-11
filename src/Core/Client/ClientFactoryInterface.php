<?php

namespace Amazon\Core\Client;

use PayWithAmazon\ClientInterface;

interface ClientFactoryInterface
{
    /**
     * Create amazon client instance
     *
     * @param null|int|string $storeId
     * @return ClientInterface
     */
    public function create($storeId = null);
}
