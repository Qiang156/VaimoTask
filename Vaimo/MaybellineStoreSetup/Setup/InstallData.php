<?php

/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */

namespace Vaimo\MaybellineStoreSetup\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Vaimo\StoresSetup\Model\StoresFactory;

class InstallData implements InstallDataInterface {
    /**
     * @var StoresFactory
     */
    private $storesFactory;

    /**
     * UpgradeData constructor.
     * @param StoresFactory $storesFactory
     */
    public function __construct(
        StoresFactory $storesFactory
    ) {
        $this->storesFactory = $storesFactory;
    }

    /**
     * Install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->storesFactory->create()->save([
            'Vaimo_MaybellineStoreSetup::fixtures/maybelline-site-2022-04-26.yaml'
        ]);
        $setup->endSetup();
    }
}
