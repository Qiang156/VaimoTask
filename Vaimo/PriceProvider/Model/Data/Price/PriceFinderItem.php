<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Model\Data\Price;

use Vaimo\PriceProvider\Api\Data\Price\PriceFinderItemInterface;

class PriceFinderItem implements PriceFinderItemInterface
{
    /**
     * @var string
     */
    private $itemNumber;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var string
     */
    private $unitMeasure;

    /**
     * @var string
     */
    private $orderDate;

    /**
     * @inheritdoc
     */
    public function setItemNumber($itemNumber)
    {
        $this->itemNumber = $itemNumber;
    }

    /**
     * @inheritdoc
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @inheritdoc
     */
    public function setUnitMeasure($unitMeasure)
    {
        $this->unitMeasure = $unitMeasure;
    }

    /**
     * @inheritdoc
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;
    }

    /**
     * @inheritdoc
     */
    public function getUnitMeasure()
    {
        return $this->unitMeasure;
    }

    /**
     * @inheritdoc
     */
    public function getItemNumber()
    {
        return $this->itemNumber;
    }

    /**
     * @inheritdoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritdoc
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }
}
