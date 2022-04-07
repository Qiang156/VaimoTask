<?php

namespace Vaimo\GenerateProductXML\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Vaimo\IntegrationBase\Model\Config as integrationConfig;

class Config
{
    protected $config;

    public function __construct(integrationConfig $config)
    {
        $this->config = $config;
    }

}
