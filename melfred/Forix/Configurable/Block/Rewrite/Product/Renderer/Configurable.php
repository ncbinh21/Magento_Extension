<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/08/2018
 * Time: 16:16
 */

namespace Forix\Configurable\Block\Rewrite\Product\Renderer;

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
class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cackeinfo = parent::getCacheKeyInfo();
        if('catalog_product_view' != $this->_request->getFullActionName()) {
            return $cackeinfo;
        }
        return [time()];
    }

    /**
     * @return int|null
     */
    protected function getCacheLifetime()
    {
        if('catalog_product_view' != $this->_request->getFullActionName()) {
            return null;
        }
        return parent::getCacheLifetime();
    }
}