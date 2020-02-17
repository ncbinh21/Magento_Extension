<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\ProductWizard\Ui\DataProvider\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRenderInterface;

/**
 * Composite, which holds collectors, that collect enought information for
 * product render
 */
class ProductRenderCollectorComposite implements \Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface
{
    /**
     * @var \Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface[]
     */
    private $productProviders = [];

    /**
     * ProductRenderCollectorComposite constructor.
     * @param array $productProviders
     */
    public function __construct(array $productProviders = [])
    {
        $this->productProviders = $productProviders;
    }

    /**
     * @inheritdoc
     */
    public function collect(ProductInterface $product, ProductRenderInterface $productRender)
    {
        foreach ($this->productProviders as $provider) {
            $provider->collect($product, $productRender);
        }
    }
}
