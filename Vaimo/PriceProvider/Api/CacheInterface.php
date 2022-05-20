<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Api;

/**
 * Interface CacheInterface
 *
 * @package Vaimo\LmagricultureApiMagento\Api
 */
interface CacheInterface extends \Magento\Framework\App\CacheInterface
{
    const XPATH_PRICE_CACHE_ENABLED = 'price_provider_cache/price/cache_enabled';
    const XPATH_STOCK_CACHE_ENABLED = 'price_provider_cache/stock/cache_enabled';

    /**
     * Cache item default lifetime in seconds
     */
    const DEFAULT_LIFETIME = 900;

    const DEFAULT_CACHE_TAG = 'LMA_CACHE';

    const DEFAULT_CACHE_TAGS = [
        self::DEFAULT_CACHE_TAG,
    ];

    const DEFAULT_CACHE_PREFIX = 'ppd.cache.';

    /**
     * @var string
     */
    const PRICE_CACHE_PREFIX = 'ppd.price-api';

    /**
     * @var string
     */
    const STOCK_CACHE_PREFIX = 'ppd.stock-api';

    /**
     * @param string $path
     * @param null|int $storeId
     * @return mixed
     */
    public function isEnabled($path, $storeId = null);

    /**
     * Make prefix usable and optionally add custom id
     *
     * @param array ...$params
     *
     * @return string
     */
    public function setCacheKeyPrefix(...$params);
}
