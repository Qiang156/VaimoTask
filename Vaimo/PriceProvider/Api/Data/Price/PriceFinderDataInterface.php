<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Api\Data\Price;

/**
 * Interface PriceFinderDataInterface
 * @package Vaimo\LmagricultureApiMagento\Api\Data
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
interface PriceFinderDataInterface
{
    /**
     * @param \Vaimo\PriceProvider\Api\Data\Price\PriceFinderItemInterface[] $items
     * @return null
     */
    public function setItems($items);

    /**
     * @param int $storeId
     * @return null
     */
    public function setStoreId($storeId);

    /**
     * @param string|null $customerNumber
     * @return null
     */
    public function setCustomerNumber($customerNumber);

    /**
     * @return \Vaimo\PriceProvider\Api\Data\Price\PriceFinderItemInterface[]
     */
    public function getItems();

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @return string|null
     */
    public function getCustomerNumber();

    /**
     * @param bool $isPreSeasonLookup
     * @return null
     */
    public function setIsPreSeasonLookup($isPreSeasonLookup = false);

    /**
     * @return bool
     */
    public function getIsPreSeasonLookup();
}
