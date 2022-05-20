<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Api\Data\Price;

use Vaimo\LmagricultureApiMagento\Api\Data\Price\PriceFinderDataInterface;
use Vaimo\LmagricultureApiMagento\Api\Data\Price\PriceStructureInterface;

/**
 * Interface PriceCollectionInterface
 *
 * @package Vaimo\LmagricultureApiMagento\Api\Data\Price
 */
interface RemoteCollectionInterface
{
    /**
     * @param array $productIds
     * @return array
     */
    public function getPriceDataByIds(array $productIds);
}
