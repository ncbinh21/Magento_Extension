<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 29/08/2018
 * Time: 12:46
 */

namespace Forix\ProductWizard\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface;
use Forix\ProductWizard\Api\Data\ProductRender\AttributeOptionDataInterfaceFactory;
use \Magento\Catalog\Api\Data\ProductAttributeInterface;

class CanShowPrice implements ProductRenderCollectorInterface
{
    protected $_productRenderExtensionFactory;

    public function __construct(
        \Magento\Catalog\Api\Data\ProductRenderExtensionFactory $productRenderExtensionFactory
    )
    {
        $this->_productRenderExtensionFactory = $productRenderExtensionFactory;
    }

    public function isProductCanShowPrice($product)
    {
        return true;
    }

    /**
     * Takes information from Product, map to render information and hydrate render object
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Magento\Catalog\Api\Data\ProductRenderInterface $productRender
     * @param array $data
     * @return void
     * @since 101.1.0
     */
    public function collect(\Magento\Catalog\Api\Data\ProductInterface $product, \Magento\Catalog\Api\Data\ProductRenderInterface $productRender)
    {
        $extensionAttributes = $productRender->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->_productRenderExtensionFactory->create();
        }
        $extensionAttributes->setCanShowPrice($this->isProductCanShowPrice($product));
        $productRender->setExtensionAttributes($extensionAttributes);
    }
}