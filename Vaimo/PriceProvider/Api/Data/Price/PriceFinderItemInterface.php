<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Api\Data\Price;

/**
 * Interface PriceFinderItemInterface
 * @package Vaimo\PriceProvider\Api\Data\Price
 */
interface PriceFinderItemInterface
{
    /**
     * @param string $itemNumber
     * @return null
     */
    public function setItemNumber($itemNumber);

    /**
     * @param string $quantity
     * @return null
     */
    public function setQuantity($quantity);

    /**
     * @param string|null $unitMeasure
     * @return mixed
     */
    public function setUnitMeasure($unitMeasure);

    /**
     * @param string $orderDate
     * @return void
     */
    public function setOrderDate($orderDate);

    /**
     * @return string
     */
    public function getItemNumber();

    /**
     * @return string
     */
    public function getQuantity();

    /**
     * @return mixed
     */
    public function getUnitMeasure();

    /**
     * @return string
     */
    public function getOrderDate();
}
