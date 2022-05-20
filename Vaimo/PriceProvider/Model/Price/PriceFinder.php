<?php

/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Model\Price;

use Magento\Framework\Json\EncoderInterface;
use Vaimo\PriceProvider\Api\CacheInterface;
use Vaimo\PriceProvider\Api\Data\Price\RemoteCollectionInterface;
use Vaimo\PriceProvider\Api\Price\PriceFinderInterface;
use Vaimo\PriceProvider\Api\Data\Price\PriceFinderDataInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;

//use Vaimo\LmagricultureApiMagento\Api\Data\Price\PriceCollectionInterface;

//use Vaimo\LmagricultureApiMagento\Api\Data\Price\PriceSearchResultsInterfaceFactory;
//use Vaimo\LmagricultureApiMagento\Api\Data\Price\PriceStructureInterface;
//use Vaimo\LmagricultureApiMagento\Api\Data\Price\PriceStructureInterfaceFactory;
//use Vaimo\LmagricultureApiMagento\Api\Price\PriceFinderInterface;
//use Vaimo\LmagricultureApiMagento\Helper\ApiAccessValidate;

/**
 * Class CustomerPrice
 * Magento FE asks Magento BE for price, if it's not cached then it will ask Laravel and cache it.
 *
 * @package Vaimo\LmagricultureApi\Model
 */
class PriceFinder implements \Vaimo\PriceProvider\Api\Price\PriceFinderInterface
{

    private $jsonEncoder;
    private $remotePriceCollection;
    private $priceCache;
    private $serializer;
    private $scopeConfig;

    public function __construct(
        CacheInterface $priceCache,
        RemoteCollectionInterface $remotePriceCollection,
        Json $serializer,
        ScopeConfigInterface $scopeConfig,
        EncoderInterface $jsonEncoder
    ) {
        $this->priceCache = $priceCache;
        $this->jsonEncoder = $jsonEncoder;
        $this->serializer = $serializer;
        $this->scopeConfig = $scopeConfig;
        $this->remotePriceCollection = $remotePriceCollection;
    }

    /**
     * @param $skus
     * @return mixed
     */
    public function getList(PriceFinderDataInterface $priceFinderData)
    {
        //get Price from Cache
        $cachedPrices = $this->getCachedItems($priceFinderData);
        //get Price from API
        $unCachedPrices = [];
        if(count($cachedPrices) != count($priceFinderData->getItems())) {
            $inputArr = [];
            foreach ($priceFinderData->getItems() as $item) {
                $inputArr[] = $item->getItemNumber();
            }
            $tmp = [];
            foreach ($cachedPrices as $item) {
                $tmp[] = $item['sku'];
            }
            $input = array_diff($inputArr, $tmp);
            $unCachedPrices = $this->getUncachedItems($input, $priceFinderData);
        }

        $prices = array_merge($cachedPrices, $unCachedPrices);
        return $this->jsonEncoder->encode($prices);
    }

    /**
     * @param PriceFinderDataInterface $priceFinderData
     * @return array
     */
    private function getCachedItems(PriceFinderDataInterface $priceFinderData)
    {

        $cachedData = [];
        $this->priceCache->setCacheKeyPrefix(
            ...$this->getPriceCachePrefix($priceFinderData)
        );

        foreach ($priceFinderData->getItems() as $item) {
            $cacheId = $this->createCacheId($item->getItemNumber(), $item->getQuantity());
            // phpcs:ignore MEQP1.Performance.Loop.ModelLSD
            $cachedItem = $this->priceCache->load($cacheId);
            if ($cachedItem) {
                $cachedItem = $this->serializer->unserialize($cachedItem);
                if ($cachedItem) {
                    $cachedItem[PriceFinderInterface::IS_PRICE_CACHED] = true;
                    $cachedData[] = $cachedItem;
                }
            }
        }

        return $cachedData;
    }

    /**
     * @param PriceFinderDataInterface $priceFinderData
     * @return array
     */
    private function getPriceCachePrefix(PriceFinderDataInterface $priceFinderData)
    {
        return [
            CacheInterface::PRICE_CACHE_PREFIX,
//            (string)$priceFinderData->getCustomerNumber(),
//            (string)\implode(',', $this->apiAccessValidate->getCustomerRoles()),
            (int)$priceFinderData->getStoreId(),
        ];
    }

    /**
     * @param $productIds
     * @param PriceFinderDataInterface $priceFinderData
     * @return array
     */
    private function getUncachedItems($productIds, PriceFinderDataInterface $priceFinderData)
    {
        $items = [];
        if (!empty($productIds)) {
            $items = $this->remotePriceCollection->getPriceDataByIds($productIds);
            $this->addItemsIntoCache($items, $priceFinderData);
        }
        return $items;
    }

    /**
     * @param $items
     * @param PriceFinderDataInterface $priceFinderData
     * @return void
     */
    private function addItemsIntoCache($items, PriceFinderDataInterface $priceFinderData)
    {
        if (!$items) {
            return;
        }

        $cacheIsEnabled = $this->priceCache->isEnabled(
            CacheInterface::XPATH_PRICE_CACHE_ENABLED,
            $priceFinderData->getStoreId()
        );

        $this->priceCache->setCacheKeyPrefix(
            ...$this->getPriceCachePrefix($priceFinderData)
        );

        foreach ($items as $productData) {
            if (!empty($productData['priceIncVat'])) {
                $productId = $productData[PriceFinderInterface::ENTITY_ID];
                $cacheId = $this->createCacheId($productId,1);
                $customerNumber = $priceFinderData->getCustomerNumber() ?? '000000';

                $tags = [
                    PriceFinderInterface::CACHE_TAG,
                    PriceFinderInterface::CACHE_TAG_CUSTOMER,
                    PriceFinderInterface::CACHE_TAG . '_' . $cacheId,
                    PriceFinderInterface::CACHE_TAG_CUSTOMER . '_' . $customerNumber,
                ];

                // phpcs:ignore MEQP1.Performance.Loop.ModelLSD
                $this->priceCache->save(
                    $this->serializer->serialize($productData),
                    $cacheId,
                    $tags,
                    $this->getCacheLifeTime()
                );
            }
        }
    }

    /**
     * @return int|null
     */
    private function getCacheLifeTime()
    {
        return (int)$this->scopeConfig->getValue(
            self::XPATH_PRICE_CACHE_LIFETIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: null;
    }

    /**
     * @param string $itemId
     * @param string $qty
     * @return string
     */
    private function createCacheId($itemId, $qty)
    {
        return $itemId . ":" . (int)$qty;
    }

}
