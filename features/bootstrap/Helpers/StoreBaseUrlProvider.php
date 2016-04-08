<?php

namespace Helpers;

class StoreBaseUrlProvider
{
    const EU_BASE_URL = 'http://amazon-payment.dev';
    const UK_BASE_URL = 'http://amazon-payment.dev';
    const US_BASE_URL = 'http://amazon-payment.dev';
    const DEFAULT_STORE_BASE_URL = self::US_BASE_URL;

    private static $_baseUrl = self::DEFAULT_STORE_BASE_URL;

    public static function setBaseUrl($baseUrl)
    {
        self::$_baseUrl = $baseUrl;
    }

    public static function getBaseUrl()
    {
        return self::$_baseUrl;
    }

    public static function resetBaseUrl()
    {
        self::$_baseUrl = self::DEFAULT_STORE_BASE_URL;
    }
}
