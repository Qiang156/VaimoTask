<?php


namespace Vaimo\PriceProvider\Block;

use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Magento\Framework\Locale\Format;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Vaimo\PriceProvider\Api\Data\Price\PriceFinderDataInterfaceFactory;
use Vaimo\PriceProvider\Api\Data\Price\PriceFinderItemInterfaceFactory;

class PriceData extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{

    private $priceFinder;
    private $jsonEncode;
    private $jsonDecode;
    private $priceFinderDataFactory;
    private $priceFinderItemFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecode,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        array $data = [],
        Format $localeFormat = null,
        Session $customerSession = null,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices $variationPrices = null,
        \Vaimo\PriceProvider\Model\Price\PriceFinder $priceFinder,
        PriceFinderDataInterfaceFactory $priceFinderDataFactory,
        PriceFinderItemInterfaceFactory $priceFinderItemFactory
    ) {
        $this->priceFinderDataFactory = $priceFinderDataFactory;
        $this->priceFinderItemFactory = $priceFinderItemFactory;

        $this->priceFinder = $priceFinder;
        $this->jsonEncode = $jsonEncoder;
        $this->jsonDecode = $jsonDecode;
        parent::__construct($context, $arrayUtils, $jsonEncoder, $helper, $catalogProduct, $currentCustomer, $priceCurrency, $configurableAttributeData, $data, $localeFormat, $customerSession, $variationPrices);
    }

    /**
     * Create api address
     * @param string $route
     * @return string
     */
    public function getApiUrl(string $route)
    {
        //return $this->getUrl($route, ['_secure' => $this->getRequest()->isSecure()]);
        return $this->getUrl(null, ['_secure' => $this->getRequest()->isSecure()]).$route;
    }

    /**
     * @return void
     */
    public function getJsonConfig()
    {
        $jsonConfig = $this->jsonDecode->decode(parent::getJsonConfig());

        $priceFinderData = $this->priceFinderDataFactory->create();

        foreach($jsonConfig['optionPrices'] as $key => $item) {
            $priceFinderItem = $this->priceFinderItemFactory->create();
            /** @var  \Vaimo\PriceProvider\Model\Data\Price\PriceFinderItem $priceFinderItem */
            $priceFinderItem->setItemNumber($key);
            $priceFinderData->addItem($priceFinderItem);
        };
        //add primary product id
//        $priceFinderItem = $this->priceFinderItemFactory->create();
//        $priceFinderItem->setItemNumber($jsonConfig['productId']);
//        $priceFinderData->addItem($priceFinderItem);

        $prices = $this->jsonDecode->decode($this->priceFinder->getList($priceFinderData));
        $jsonConfig['optionPrices'] = [];
        foreach($prices as $item) {
            $jsonConfig['optionPrices'][$item['sku']]['baseOldPrice']['amount'] = $item['priceIncVat'];
            $jsonConfig['optionPrices'][$item['sku']]['oldPrice']['amount'] = $item['priceIncVat'];
            $jsonConfig['optionPrices'][$item['sku']]['basePrice']['amount'] = $item['priceIncVat'];
            $jsonConfig['optionPrices'][$item['sku']]['finalPrice']['amount'] = $item['priceIncVat'];
            $jsonConfig['optionPrices'][$item['sku']]['tierPrices'] = [];
        }
        $jsonConfig['prices']['baseOldPrice']['amount'] = 11.00;
        $jsonConfig['prices']['basePrice']['amount'] = 12.00;
        $jsonConfig['prices']['finalPrice']['amount'] = 13.00;
        $jsonConfig['prices']['OldPrice']['amount'] = 14.00;

        $jsonConfig['template'] = 'SEK <%- data.price %>';

        return $this->jsonEncoder->encode($jsonConfig);
    }


}
