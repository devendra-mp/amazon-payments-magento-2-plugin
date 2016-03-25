<?php

namespace Helpers;

class StoreBaseUrlProvider
{
    const EU_BASE_URL = 'http://eu.m2.docker';
    const UK_BASE_URL = 'http://uk.m2.docker';
    const US_BASE_URL = 'http://us.m2.docker';
    const DEFAULT_STORE_BASE_URL = self::UK_BASE_URL;

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
