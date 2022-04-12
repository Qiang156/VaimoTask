<?php

/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Setup;

use Magento\Eav\Api\Data\AttributeGroupInterfaceFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Repository AS AttributeRepository;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory AS CategoryCollectionFactory;
use Magento\Framework\App\State;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var State
     */
    private $state;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param WriterInterface $configWriter
     * @param AttributeRepository $attributeRepository
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param State $state
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        WriterInterface $configWriter,
        AttributeRepository $attributeRepository,
        State $state
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->configWriter = $configWriter;
        $this->attributeRepository = $attributeRepository;
        $this->state = $state;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->removeAttributes($setup, ['brand','product_type','tags']);
            $this->AddTextAttributes($setup, [
                'Product Type' => 'product_type',
            ]);
            $this->addMultiSelectAttributes($setup, [
                'Tags' => 'tags',
            ]);
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->removeAttributes($setup, ['color_name','color_hex']);
            $this->addSelectAttributes($setup, [
                'Color Name' => 'color_name',
                'Color Hex' => 'color_hex',
            ]);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param $textAttributes
     */
    private function addTextAttributes($setup, $textAttributes, $group = 'Attributes')
    {
        foreach ($textAttributes as $label => $code) {
            $this->createAttribute($setup, $code, $label, 'varchar', 'text', true, false, true, $group);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param $selectAttributes
     * @param bool $isFilter
     * @param string $group
     */
    private function addSelectAttributes($setup, $multiSelectAttributes, $isFilter = false, $group = 'Attributes')
    {
        foreach ($multiSelectAttributes as $label => $code) {
            $this->createAttribute($setup, $code, $label, 'varchar', 'select', true, $isFilter, true, $group);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param $multiSelectAttributes
     * @param bool $isFilter
     * @param string $group
     */
    private function addMultiSelectAttributes($setup, $multiSelectAttributes, $isFilter = false, $group = 'Attributes')
    {
        foreach ($multiSelectAttributes as $label => $code) {
            $this->createAttribute($setup, $code, $label, 'varchar', 'multiselect', true, $isFilter, true, $group);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param string $attributeCode
     * @param string $attributeLabel
     * @param string $type
     * @param string $input
     * @param bool $showOnProductPage
     * @param bool $isFilter
     * @param bool $wysiwyg
     * @param string $group
     * @param array $optionValues
     * @param bool $storeLabels
     * @param bool $usedInProductListing
     */
    private function createAttribute(
        ModuleDataSetupInterface $setup,
        string                   $attributeCode,
        string                   $attributeLabel,
        string                   $type,
        string                   $input,
        bool                     $showOnProductPage = false,
        bool                     $isFilter = false,
        bool                     $wysiwyg = false,
        string                   $group = 'Attributes',  //eav_attribute_group
        array                    $optionValues = [],
        bool                     $storeLabels = false,
        bool                     $usedInProductListing = true
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
        $is_Global = (count($optionValues) || $input == 'select');
        $options = [
            'type' => $type,
            'backend' => $input == 'multiselect' ? 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend' : '',
            'frontend' => '',
            'label' => $attributeLabel,
            'input' => $input,
            'class' => '',
            'source' => $this->getSourceModelForInput($input),
            'global' => $is_Global ? Attribute::SCOPE_GLOBAL : Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'searchable' => true,
            'filterable' => $isFilter,
            'comparable' => false,
            'visible_on_front' => $showOnProductPage,
            'used_in_product_listing' => $usedInProductListing,
            'unique' => false,
            'group' => $group,
            'apply_to' => '',
            'wysiwyg_enabled' => $wysiwyg,
        ];
        if (count($optionValues) > 0 && !$storeLabels) {
            $options['option'] = ['values' => $optionValues];
        } elseif (count($optionValues) > 0 && $storeLabels) {
            $options['option'] = ['value' => $optionValues];
        }
        $eavSetup->addAttribute(
            Product::ENTITY,
            $attributeCode,
            $options
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param array $attributes
     */
    private function removeAttributes($setup, $attributes)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        foreach ($attributes as $attributeCode) {
            $eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
        }
    }

    /**
     * @param string $input
     * @return string
     */
    private function getSourceModelForInput($input)
    {
        switch ($input) {
            case 'text':
            case 'textarea':
            case 'multiselect':
                $sourceModel = '';
                break;
            case 'boolean':
                $sourceModel = \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class;
                break;
            default:
                $sourceModel = \Magento\Eav\Model\Entity\Attribute\Source\Table::class;
        }
        return $sourceModel;
    }
}
