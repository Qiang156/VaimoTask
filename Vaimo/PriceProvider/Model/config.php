<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\PriceProvider\Model;

use Vaimo\PriceProvider\Api\Stock\StockFinderInterface;

/**
 * Class Config
 *
 * @package Vaimo\LmagricultureApiMagento\Model
 */
class Config implements \Vaimo\PriceProvider\Api\ConfigInterface
{
    const XPAT_GUEST_CUSTOMER_ID = 'lma_integration/valid_price/guest_customer_id';
    const XPAT_DEFAULT_ORDER_TYPE = 'lma_integration/valid_price/order_type';
    const XPAT_DEFAULT_WEBSITE_ID = 'lma_integration/valid_price/website_id';
    const XPATH_LIST_PRICE_FIELD = 'lma_integration/valid_price/list_price_field';
    const XPAT_DISCOUNT_RANGES = 'lma_integration/ranges/ranges';
    const XPAT_CUSTOMER_GROUP_MAP = 'lma_integration/customer_group_map/groups';
    const XPAT_DAYS_AHEAD_GET_CALENDAR = 'lma_integration/developer/calendar_days_ahead';
    const XPAT_SHIPPING_PICK_UP = 'lma_integration/code_mapping/pick_up_methods';
    const XPAT_SHIPPING_NON_BULK = 'lma_integration/code_mapping/non_bulk_methods';
    const XPAT_DEVELOPER_DO_NOT_USE_APIS = 'lma_integration/developer/do_not_use_apis';
    const XPAT_DEVELOPER_ADD_FAKE_API_REPLIES = 'lma_integration/developer/generate_fake_api';
    const XPAT_DEVELOPER_PACKAGE_SIZE_TON_TEXT = 'lma_integration/developer/package_size_ton_text';
    const XPAT_FF_USE_FREIGHT_CLASS = 'lma_integration/feature_flags/use_freight_class';
    const XPAT_FF_USE_ADDRESS_QUEUE = 'lma_integration/feature_flags/use_customer_address_queue';
    const XPAT_SHIPPING_JOINT_DELIVERY = 'lma_integration/code_mapping/joint_delivery';
    const XPATH_USER_ID_FIELD_FOR_LM2 = 'lma_integration/behaviour/lm2_user_id_field';
    const XPATH_LIST_PRICE_PERMISSION_ENABLED = 'lma_permissions/list_price/enabled';
    const XPATH_LIST_PRICE_PERMISSION_ROLES = 'lma_permissions/list_price/roles';
    const XPATH_PRICE_DOWNLOAD_PERMISSION_ENABLED = 'lma_permissions/price_download/enabled';
    const XPATH_PRICE_DOWNLOAD_PERMISSION_ROLES = 'lma_permissions/price_download/roles';
    const XPATH_SUB_CUSTOMER_PERMISSION_ENABLED = 'lma_permissions/sub_customers/enabled';
    const XPATH_SUB_CUSTOMER_PERMISSION_ROLES = 'lma_permissions/sub_customers/roles';
    const XPAT_CATEGORIES_WITHOUT_PRICES = 'lma_integration/valid_price/categories_without_prices';
    const XPAT_FF_SAVE_CHECKOUT_SELECTION = 'lma_integration/feature_flags/enable_saving_checkout_selection';
    const XPATH_PRE_SEASON_CAMPAIGN_TYPES = 'lma_integration/valid_price/pre_season_campaign_types';
    const XPATH_SUB_CUSTOMER_PERMISSION_BANNER_ROLE_ID = 'lma_permissions/sub_customers/banner_role_id';

    // Not ideal, needs a better solution later
    const XPAT_DEFAULT_M3_SHIPPING_PRICE = 'carriers/m3_shipping/price';

    const SE_RETAIL_WEBSITE_CODE = 'seretail';
    const SE_HARVEST_WEBSITE_CODE = 'seharvest';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param string $xpath
     *
     * @return mixed
     */
    public function lookupConfigurationValue($xpath)
    {
        return $this->scopeConfig->getValue(
            $xpath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getUserIdFieldForLM2()
    {
        return$this->lookupConfigurationValue(self::XPATH_USER_ID_FIELD_FOR_LM2);
    }

    /**
     * Get customer number
     *
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->lookupConfigurationValue(self::XPAT_GUEST_CUSTOMER_ID);
    }

    /**
     * Get order type
     *
     * @return mixed
     */
    public function getOrderType()
    {
        return $this->lookupConfigurationValue(self::XPAT_DEFAULT_ORDER_TYPE);
    }

    /**
     * Get default website id
     *
     * @return mixed
     */
    public function getDefaultWebsiteId()
    {
        return $this->lookupConfigurationValue(self::XPAT_DEFAULT_WEBSITE_ID);
    }

    /**
     * @return int|null
     */
    public function getStockCacheLifeTime()
    {
        return $this->lookupConfigurationValue(StockFinderInterface::XPATH_STOCK_CACHE_LIFETIME) ?: null;
    }

    /**
     * Get customer number
     *
     * @return mixed
     */
    public function getDiscountRanges()
    {
        $result = $this->lookupConfigurationValue(self::XPAT_DISCOUNT_RANGES);

        return $result ? $this->jsonSerializer->unserialize($result) : [];
    }

    /**
     * @return mixed
     */
    public function getDefaultFreightPrice()
    {
        return $this->lookupConfigurationValue(self::XPAT_DEFAULT_M3_SHIPPING_PRICE);
    }

    /**
     * @return mixed
     */
    public function getDaysAheadForGetCalendar()
    {
        return $this->lookupConfigurationValue(self::XPAT_DAYS_AHEAD_GET_CALENDAR);
    }

    /**
     * @return mixed
     */
    public function getDeveloperDoNotUseApis()
    {
        return $this->lookupConfigurationValue(self::XPAT_DEVELOPER_DO_NOT_USE_APIS);
    }

    /**
     * @return mixed
     */
    public function getDevelopAddFakeApiReplies()
    {
        return $this->lookupConfigurationValue(self::XPAT_DEVELOPER_ADD_FAKE_API_REPLIES);
    }

    /**
     * @return mixed
     */
    public function getDevelopPackageSizeTonText()
    {
        return $this->lookupConfigurationValue(self::XPAT_DEVELOPER_PACKAGE_SIZE_TON_TEXT);
    }

    /**
     * @return mixed
     */
    public function getCustomerGroupMap()
    {
        $groupMap = $this->lookupConfigurationValue(self::XPAT_CUSTOMER_GROUP_MAP);

        return $groupMap ? $this->jsonSerializer->unserialize($groupMap) : [];
    }

    /**
     * @return array
     */
    public function getPickUpShippingMethods()
    {
        $methods = $this->lookupConfigurationValue(self::XPAT_SHIPPING_PICK_UP);

        return $methods ? \explode(',', \str_replace(' ', '', $methods)) : [];
    }

    /**
     * @return array
     */
    public function getNonBulkShippingMethods()
    {
        $methods = $this->lookupConfigurationValue(self::XPAT_SHIPPING_NON_BULK);

        return $methods ? \explode(',', \str_replace(' ', '', $methods)) : [];
    }

    /**
     * @return bool
     * phpcs:disable BooleanGetMethodName
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getFFUseFreightClass()
    {
        return $this->lookupConfigurationValue(self::XPAT_FF_USE_FREIGHT_CLASS);
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getJointDeliveryFlag()
    {
        return $this->lookupConfigurationValue(self::XPAT_SHIPPING_JOINT_DELIVERY) == 1;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getFFUseAddressQueue()
    {
        return $this->lookupConfigurationValue(self::XPAT_FF_USE_ADDRESS_QUEUE);
    }

    /**
     * @return bool
     */
    public function isListPriceViewPermissionEnabled()
    {
        return (bool)$this->lookupConfigurationValue(self::XPATH_LIST_PRICE_PERMISSION_ENABLED);
    }

    /**
     * @return string[]
     */
    public function getListPriceViewRoleIds()
    {
        $roles = $this->lookupConfigurationValue(self::XPATH_LIST_PRICE_PERMISSION_ROLES);

        return $roles ? \explode(',', \str_replace(' ', '', $roles)) : [];
    }

    /**
     * @return bool
     */
    public function isPriceDownloadEnabled()
    {
        return (bool)$this->lookupConfigurationValue(self::XPATH_PRICE_DOWNLOAD_PERMISSION_ENABLED);
    }

    /**
     * @return string[]
     */
    public function getPriceDownloadRoleIds()
    {
        $roles = $this->lookupConfigurationValue(self::XPATH_PRICE_DOWNLOAD_PERMISSION_ROLES);

        return $roles ? \explode(',', \str_replace(' ', '', $roles)) : [];
    }

    /**
     * @return bool
     */
    public function isSubCustomerSwitchEnabled()
    {
        return (bool)$this->lookupConfigurationValue(self::XPATH_SUB_CUSTOMER_PERMISSION_ENABLED);
    }

    /**
     * @return string[]
     */
    public function getSubCustomerSwitchRoleIds()
    {
        $roles = $this->lookupConfigurationValue(self::XPATH_SUB_CUSTOMER_PERMISSION_ROLES);

        return $roles ? \explode(',', \str_replace(' ', '', $roles)) : [];
    }

    /**
     * return the list price field, that should be used from API, depending on website
     * @return string
     */
    public function getListPriceField()
    {
        return $this->lookupConfigurationValue(self::XPATH_LIST_PRICE_FIELD);
    }

    /**
     * Get an array of categories that should have no prices
     *
     * @return array|null
     */
    public function getCategoriesWithoutPrices()
    {
        $resultStr = $this->lookupConfigurationValue(self::XPAT_CATEGORIES_WITHOUT_PRICES);

        return !empty($resultStr) ? \explode(",", $resultStr) : null;
    }

    /**
     * @return bool
     */
    public function isCheckoutSelectionSaved()
    {
        return (bool)$this->lookupConfigurationValue(self::XPAT_FF_SAVE_CHECKOUT_SELECTION);
    }

    /**
     * Get an array campaign types which indicate a campaign is for pre-season sales.
     *
     * @return string[]
     */
    public function getPreSeasonCampaignCategories()
    {
        $resultStr = $this->lookupConfigurationValue(self::XPATH_PRE_SEASON_CAMPAIGN_TYPES);

        return !empty($resultStr) ? \array_map('trim', \explode(',', $resultStr)) : [];
    }

    /**
     * @return mixed
     */
    public function getSubCustomerBannerRoleId()
    {
        return $this->lookupConfigurationValue(self::XPATH_SUB_CUSTOMER_PERMISSION_BANNER_ROLE_ID);
    }
}
