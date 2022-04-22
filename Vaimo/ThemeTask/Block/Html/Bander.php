<?php


namespace Vaimo\ThemeTask\Block\Html;

use Magento\Framework\View\Element\Template;

class Bander extends Template
{

    protected $_template = 'Vaimo_GenerateProductXML::html/bander.phtml';

    public function isHomePage()
    {
        $currentUrl = $this->getUrl('', ['_current' => true]);
        $urlRewrite = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $currentUrl == $urlRewrite;
    }

    public function getImages()
    {
        $images = [
            ['src' => '/pub/media/bander/1.jpg', 'description'=>'image one'],
            ['src' => '/pub/media/bander/2.jpg', 'description'=>'image two'],
            ['src' => '/pub/media/bander/3.jpg', 'description'=>'image three'],
            ['src' => '/pub/media/bander/4.jpg', 'description'=>'image four'],
            ['src' => '/pub/media/bander/5.jpg', 'description'=>'image five']
        ];
        return $images;
    }
}
