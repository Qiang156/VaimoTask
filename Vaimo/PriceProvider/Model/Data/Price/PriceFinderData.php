<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Model\Data\Price;

use Vaimo\PriceProvider\Api\Data\Price\PriceFinderDataInterface;
use Vaimo\PriceProvider\Model\Config;

/**
 * Class PriceFinderData
 *
 * @package Vaimo\PriceProvider\Model\Data\Price
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class PriceFinderData implements PriceFinderDataInterface
{
    /**
     * \Vaimo\PriceProvider\Api\Data\Price\PriceFinderItemInterface[]
     */
    private $items;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var int|null
     */
    private $customerNumber;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var bool
     */
    private $isPreSeasonLookup;

    public function __construct() {}

    /**
     * @param \Vaimo\PriceProvider\Api\Data\Price\PriceFinderItemInterface[] $items
     * @return null|void
     */
    public function setItems($items)
    {
        $this->items =  $items;
    }

    /**
     * @return \Vaimo\PriceProvider\Api\Data\Price\PriceFinderItemInterface[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param \Vaimo\PriceProvider\Api\Data\Price\PriceFinderItemInterface $item
     * @return null|void
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * @param int $storeId
     * @return int
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @param int|null $customerNumber
     * @return int
     */
    public function setCustomerNumber($customerNumber)
    {
        $this->customerNumber = $customerNumber;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @return int|null
     */
    public function getCustomerNumber()
    {
        return $this->customerNumber ?: '000000';
    }

    /**
     * @param bool $isPreSeasonLookup
     * @return null
     */
    public function setIsPreSeasonLookup($isPreSeasonLookup = false)
    {
        $this->isPreSeasonLookup = $isPreSeasonLookup;
    }

    /**
     * @return bool
     */
    public function getIsPreSeasonLookup()
    {
        return $this->isPreSeasonLookup;
    }
}
