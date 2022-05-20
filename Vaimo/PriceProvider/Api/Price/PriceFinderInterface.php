<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Api\Price;

use Vaimo\PriceProvider\Api\Data\Price\PriceFinderDataInterface;

/**
 * Interface PriceInterface
 *
 * @package Vaimo\PriceProvider\Api
 */
interface PriceFinderInterface
{

    /**
     * @var string
     */
    const XPATH_PRICE_CACHE_LIFETIME = 'price_provider_cache/price/cache_lifetime';

    /**
     * @var string
     */
    const ENTITY_ID = 'sku';

    /**
     * @var float
     */
    const QUANTITY = 'quantity';

    /**
     * @var string
     */
    const UNIT_MEASURE = 'unitMeasure';

    /**
     * @var string
     */
    const CACHE_TAG = 'PPD_PRICE';

    /**
     * @var string
     */
    const CACHE_TAG_CUSTOMER = 'PPD_PRICE_CUSTOMER';

    /**
     * @var string
     */
    const IS_PRICE_CACHED = 'is_price_cached';

    /**
     * @var string
     */
    const TAX_CLASS_ID = 'tax_class_id';

    /**
     * @param string $params
     * @return mixed
     */
    public function getList(PriceFinderDataInterface $priceFinderData);

}
