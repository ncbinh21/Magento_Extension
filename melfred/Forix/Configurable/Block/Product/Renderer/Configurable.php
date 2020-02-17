<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/08/2018
 * Time: 16:16
 */

namespace Forix\Configurable\Block\Product\Renderer;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product as CatalogProduct;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Helper\Data;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Store\Model\ScopeInterface;
use Magento\Swatches\Helper\Data as SwatchData;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\Swatch;
use Magento\Framework\App\ObjectManager;
use Magento\Swatches\Model\SwatchAttributesProvider;
use \Forix\Configurable\Helper\RadioSwatchHelper;
use \Magento\Framework\Json\DecoderInterface;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as TypeConfigurable;
use Magento\Catalog\Model\CategoryFactory;


class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    const RADIO_SWATCH_RENDERER_TEMPLATE = 'Forix_Configurable::product/view/renderer.phtml';

    protected $_radioSwatchHelper;
    protected $_jsonDecoder;
	protected $_categoryFactory;
	protected $_registry;

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
        RadioSwatchHelper $radioSwatchHelper,
        Media $swatchMediaHelper,
        CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $registry,
        SwatchAttributesProvider $swatchAttributesProvider = null,
        array $data = []
    )
    {
        parent::__construct($context, $arrayUtils, $jsonEncoder, $helper, $catalogProduct, $currentCustomer, $priceCurrency, $configurableAttributeData, $swatchHelper, $swatchMediaHelper, $data, $swatchAttributesProvider);
        $this->_radioSwatchHelper = $radioSwatchHelper;
        $this->_jsonDecoder = $jsonDecoder;
        $this->_categoryFactory = $categoryFactory;
        $this->_registry = $registry;
    }

    protected function isProductHasRadioSwatchAttribute()
    {
        return $this->_radioSwatchHelper->isProductHasRadioSwatch($this->getProduct());
    }

    /**
     * Get Swatch config data
     *
     * @return string
     */
    public function getJsonSwatchConfig()
    {
        $config = $this->_jsonDecoder->decode(parent::getJsonSwatchConfig());
        /**
         *  Radio Options
         */
        if ($this->isProductHasRadioSwatchAttribute()) {
            $config = $this->_radioSwatchHelper->getRadioSwatchConfig($this->getProduct()) + $config;
        }
        return $this->jsonEncoder->encode($config);
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
        if ($this->isProductHasRadioSwatchAttribute()) {
            return self::RADIO_SWATCH_RENDERER_TEMPLATE;
        }
        return parent::getRendererTemplate();
    }

	/**
	 * Custom Cache for editoption cart
	 * @return array
	 *
	 */
    public function getCacheKeyInfo()
    {
        if('catalog_product_view' === $this->_request->getFullActionName()) {
            return parent::getCacheKeyInfo();
        }
        return [time()];
    }

    protected function getCacheLifetime()
    {
        if('catalog_product_view' != $this->_request->getFullActionName()) {
            return null;
        }
        return parent::getCacheLifetime();
    }

	public function requiredRigModel() {
		$current_product = $this->_registry->registry('current_product');
		if(!$current_product->hasData('required_rig_model')) {
            $current_product->setData('required_rig_model', false);
            $cateIds = $current_product->getData("category_ids");
            $collection = $this->_categoryFactory->create()->getCollection()
                ->addAttributeToSelect(["required_rig_model"])
                ->addAttributeToFilter('entity_id', ["in" => $cateIds]);

            $isRequired = 0;
            foreach ($collection as $item) {
                if ($item->getData("required_rig_model") == 1) {
                    $isRequired = 1;
                    break;
                }
            }
            $current_product->setData('required_rig_model', !!$isRequired);
        }
		return $current_product->getData('required_rig_model');
	}

}