<?php


namespace Vaimo\ThemeTask\Block\Html;

use Magento\Framework\View\Element\Template;

class Header extends Template
{

    protected $_template = 'Vaimo_GenerateProductXML::html/header.phtml';

    public function getSomething()
    {
        return 'Header';
    }
}
