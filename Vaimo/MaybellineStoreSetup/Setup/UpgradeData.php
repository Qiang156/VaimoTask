<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\MaybellineStoreSetup\Setup;

use Magento\Catalog\Api\CategoryListInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Helper\DefaultCategory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\SharedCatalog\Api\CategoryManagementInterface;
use Magento\SharedCatalog\Api\ProductManagementInterface;
use Magento\SharedCatalog\Api\SharedCatalogRepositoryInterface;
use Magento\SharedCatalog\Model\SharedCatalogFactory;
use Vaimo\Menu\Model\Mage;
use Vaimo\StoresSetup\Model\StoresFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Theme\Model\ThemeFactory;
use Magento\Theme\Model\Theme;
use Magento\Theme\Model\Config;

/**
 * Class UpgradeData
 * @package Vaimo\LmagricultureStoresSetup\Setup
 */
class UpgradeData implements UpgradeDataInterface
{

    const THEME_MAYBELLINE_CODE = "Vaimo/maybelline";
    const MAYBELLINE_WEBSITE_CODE = 'maybelline';
    const DEFAULT_CURRENCY = 'EUR';

    /**
     * @var Mage
     */
    private $mage;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var StoresFactory
     */
    private $storesFactory;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepositoryInterface;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var DefaultCategory
     */
    protected $defaultCategoryHelper;

    /**
     * @var \Magento\Theme\Model\Config
     */
    protected $config;

    /**
     * @var ThemeFactory
     */
    protected $themeFactory;

    /**
     * @var Theme
     */
    protected $theme;
    /**
     * @var SharedCatalogFactory
     */
    private $sharedCatalogFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;
    /**
     * @var SharedCatalogRepositoryInterface
     */
    private $sharedCatalogRepository;

    /**
     * UpgradeData constructor.
     * @param WriterInterface $configWriter
     * @param EavSetupFactory $eavSetupFactory
     * @param StoresFactory $storesFactory
     * @param Store $store
     * @param DefaultCategory $defaultCategoryHelper
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepositoryInterface
     * @param CategoryRepository $categoryRepository
     * @param Config $config
     * @param ThemeFactory $themeFactory
     * @param Theme $theme
     * @param StoreManager $storeManager
     * @param State $state
     * @param Mage $mage
     * @param SharedCatalogFactory $sharedCatalogFactory
     * @param SharedCatalogRepositoryInterface $sharedCatalogRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GroupRepositoryInterface $groupRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ProductManagementInterface $productManagement
     * @param CategoryManagementInterface $categoryManagement
     * @param CategoryListInterface $categoryList
     */
    public function __construct(
        WriterInterface $configWriter,
        EavSetupFactory $eavSetupFactory,
        StoresFactory $storesFactory,
        Store $store,
        DefaultCategory $defaultCategoryHelper,
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepositoryInterface,
        CategoryRepository $categoryRepository,
        Config $config,
        ThemeFactory $themeFactory,
        Theme $theme,
        StoreManager $storeManager,
        State $state,
        Mage $mage,
        SharedCatalogFactory $sharedCatalogFactory,
        SharedCatalogRepositoryInterface $sharedCatalogRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GroupRepositoryInterface $groupRepository
    ) {

        $this->configWriter = $configWriter;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->storesFactory = $storesFactory;
        $this->store = $store;
        $this->defaultCategoryHelper = $defaultCategoryHelper;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->categoryRepository = $categoryRepository;
        $this->config = $config;
        $this->themeFactory = $themeFactory;
        $this->theme = $theme;
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->mage = $mage;
        $this->sharedCatalogFactory = $sharedCatalogFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->groupRepository = $groupRepository;
        $this->sharedCatalogRepository = $sharedCatalogRepository;
    }

    /**
     * Upgrade Data
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $this->configWriter->save(
                'dev/static/sign',
                1,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                '1'
            );

            $theme = $this->themeFactory->create()->load(self::THEME_MAYBELLINE_CODE, 'code');

            if ($theme) {
                $this->configWriter->save(
                    'design/theme/theme_id',
                    $theme->getId(),
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    Store::DEFAULT_STORE_ID
                );
            }

            $this->configWriter->save(
                'currency/options/default',
                self::DEFAULT_CURRENCY,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                0
            );
        }

        $setup->endSetup();
    }


    /**
     * @param $themeCode
     * @param $storeCode
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function assignTheme($themeCode, $storeCode)
    {
        /**
         * @var \Magento\Theme\Model\Theme $theme
         */
        $theme = $this->theme->load($themeCode, 'code');
        $store = $this->store->load($storeCode);
        $storeId = $store->getId();

        if ($theme) {
            $this->config->assignToStore(
                $theme,
                [$storeId],
                'stores'
            );
        }
    }

    /**
     * @param $row
     * @param $category
     */
    protected function setAdditionalData($row, $category)
    {
        $additionalAttributes = [
            'position',
            'display_mode',
            'page_layout',
            'custom_layout_update',
        ];
        foreach ($additionalAttributes as $categoryAttribute) {
            if (!empty($row[$categoryAttribute])) {
                $attributeData = [$categoryAttribute => $row[$categoryAttribute]];
                $category->addData($attributeData);
            }
        }
    }
}
