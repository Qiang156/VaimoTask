<?php


namespace Vaimo\GenerateProductXML\Block\Product\Renderer;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product as CatalogProduct;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\ConfigurableProduct\Helper\Data;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Swatches\Helper\Data as SwatchData;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\SwatchAttributesProvider;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable implements \Magento\Framework\DataObject\IdentityInterface
{

    const COLOR_HEX_ID = 245;
    const COLOR_HEX_CODE = 'color_hex';

    const COLOR_NAME_ID = 244;
    const COLOR_NAME_CODE = 'color_name';

    const COLOR_HEX_RENDERER_TEMPLATE = 'Vaimo_GenerateProductXML::product/view/renderer.phtml';

    private $jsonDecode;

    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        Data $helper,
        CatalogProduct $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        SwatchData $swatchHelper,
        Media $swatchMediaHelper,
        array $data = [],
        SwatchAttributesProvider $swatchAttributesProvider = null,
        UrlBuilder $imageUrlBuilder = null
    ) {
        $this->jsonDecode = $jsonDecoder;
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            $swatchHelper,
            $swatchMediaHelper,
            $data,
            $swatchAttributesProvider,
            $imageUrlBuilder
        );
    }


    /**
     * Return renderer template
     *
     * Template for product with swatches is different from product without swatches
     *
     * @return string
     */
    protected function getRendererTemplate()
    {
        if( $this->hasColorHex() ) {
            return self::COLOR_HEX_RENDERER_TEMPLATE;
        } else {
            if ($this->isProductHasSwatchAttribute()) {
                return self::SWATCH_RENDERER_TEMPLATE;
            } else {
                return self::CONFIGURABLE_RENDERER_TEMPLATE;
            }
        }
    }


    /**
     * @param $attribute
     * @return bool
     */
    public function hasColorHex()
    {
        $product = $this->getProduct();
        if ($product->getTypeId() !== \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return [];
        }
        $attributes = $this->getAllowAttributes();
        foreach ($attributes->getData() as $attribute) {
            if($attribute['attribute_id'] == self::COLOR_NAME_ID) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     * @since 100.1.0
     */
    public function getIdentities()
    {
        if ($this->product instanceof \Magento\Framework\DataObject\IdentityInterface) {
            return $this->product->getIdentities();
        } else {
            return [];
        }
    }

    /**
     * @return void
     */
    public function getColorNames()
    {
        $config = [];
        $allProducts = $this->getAllowProducts();
        foreach ($allProducts as $product) {
            $config['name'][$product->getId()] = $product->getAttributeText(self::COLOR_NAME_CODE);
            $config['hex'][$product->getId()] = $product->getAttributeText(self::COLOR_HEX_CODE);
        }
        return $this->jsonEncoder->encode($config);
    }

    /**
     * Get Swatch config data
     *
     * @return string
     */
    public function getJsonSwatchConfig()
    {
        $config = $this->jsonDecode->decode(parent::getJsonSwatchConfig());
        if( $this->hasColorHex() ) {
            $currentProduct = $this->getProduct();
            $allowProducts = $this->getAllowProducts();
            foreach($allowProducts as $product) {
                $colorValue[$product->getId()] = $product->getAttributeText(self::COLOR_HEX_CODE);
            }
            $options = $this->helper->getOptions($currentProduct, $allowProducts );
            $attributesData = $this->configurableAttributeData->getAttributesData($currentProduct, $options);

            foreach($attributesData['attributes'][self::COLOR_NAME_ID]['options'] as $item) {
                $config[self::COLOR_NAME_ID][$item['id']] = ['type'=>1, 'value'=>$colorValue[$item['products'][0]], 'label'=>$item['label']];
            }


//            $config[self::COLOR_NAME_ID][255] = ['type'=>1, 'value'=>'#DBB7A9', 'label'=>'DBB7A9'];
//            $config[self::COLOR_NAME_ID][253] = ['type'=>1, 'value'=>'#D78A89', 'label'=>'D78A89'];
//            $config[self::COLOR_NAME_ID][251] = ['type'=>1, 'value'=>'#9B7163', 'label'=>'9B7163'];

            $config[self::COLOR_NAME_ID]["additional_data"] = $this->jsonEncoder->encode([
                "update_product_preview_image" => "1",
                "use_product_image_for_swatch" => "1",
                "swatch_input_type"=>"text"
            ]);
        }

        return $this->jsonEncoder->encode($config);

    }

}
