<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Model;

use Magento\Framework\App\Cache\Proxy as CacheProxy;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Vaimo\PriceProvider\Api\CacheInterface;

/**
 * Class Cache
 *
 * @package Vaimo\LmagricultureApiMagento\Model
 */
class Cache extends CacheProxy implements CacheInterface
{
    /**
     * @var string
     */
    private $cacheKeyPrefix = self::DEFAULT_CACHE_PREFIX;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $cacheTags;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * Cache constructor.
     *
     * @param ObjectManagerInterface    $objectManager
     * @param ScopeConfigInterface      $scopeConfig
     * @param array                     $cacheTags
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig,
        array $cacheTags = self::DEFAULT_CACHE_TAGS
    ) {
        parent::__construct($objectManager);

        $this->scopeConfig = $scopeConfig;
        $this->cacheTags = $cacheTags;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled($path, $storeId = null)
    {
        if ($this->isEnabled === null) {
            $this->isEnabled = ($this->getFrontend()->getBackend() instanceof \Zend_Cache_Backend_ExtendedInterface);

            if ($this->isEnabled) {
                $this->isEnabled = (bool)$this->scopeConfig->getValue(
                    $path,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );
            }
        }

        return $this->isEnabled;
    }

    /**
     * @inheritdoc
     */
    public function load($identifier)
    {
        return parent::load($this->cacheKeyPrefix . $identifier);
    }

    /**
     * @inheritdoc
     */
    public function save($data, $identifier, $tags = [], $lifeTime = null)
    {
        $tags = $this->getTags($tags);

        if ($lifeTime === null) {
            $lifeTime = self::DEFAULT_LIFETIME;
        }

        return parent::save($data, $this->cacheKeyPrefix . $identifier, $tags, $lifeTime);
    }

    /**
     * @inheritdoc
     */
    public function remove($identifier)
    {
        return parent::remove($this->cacheKeyPrefix . $identifier);
    }

    /**
     * @inheritdoc
     */
    public function clean($tags = [])
    {
        $tags = $this->getTags($tags);

        return parent::clean($tags);
    }

    /**
     * @inheritdoc
     */
    public function setCacheKeyPrefix(...$params)
    {
        $this->cacheKeyPrefix = \implode('.', $params);
    }

    /**
     * @param array $tags
     * @return array
     */
    private function getTags($tags = [])
    {
        return \array_unique($tags + $this->cacheTags);
    }
}
