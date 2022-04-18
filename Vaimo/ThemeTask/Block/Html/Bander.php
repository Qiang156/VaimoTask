<?php


namespace Vaimo\ThemeTask\Block\Html;

use Magento\Framework\View\Element\Template;

class Bander extends Template
{

    protected $_template = 'Vaimo_GenerateProductXML::html/bander.phtml';

    public function getSomething()
    {
        return 'Bander';
    }
}
