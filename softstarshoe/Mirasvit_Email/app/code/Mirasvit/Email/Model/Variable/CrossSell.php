<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Email\Model\Variable;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Mirasvit\EmailDesigner\Model\Variable\Context;
use Magento\Framework\View\LayoutInterface;

class CrossSell
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @param Context                  $context
     * @param ProductCollectionFactory $productCollectionFactory
     * @param LayoutInterface          $layout
     */
    public function __construct(
        Context $context,
        ProductCollectionFactory $productCollectionFactory,
        LayoutInterface $layout
    ) {
        $this->context = $context;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->layout = $layout;
    }

    /**
     * @return string
     */
    public function getCrossSellHtml()
    {
        $collection = $this->getCollection();

        /** @var \Mirasvit\Email\Block\CrossSell $crossBlock */
        $crossBlock = $this->layout->createBlock('Mirasvit\Email\Block\CrossSell');

        $crossBlock->setCollection($collection);

        return $crossBlock->toHtml();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getCollection()
    {
        $productIds = $this->getProductIds();
        $productIds[] = 0;

        $collection = $this->productCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('name')
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite();

        $collection->getSelect()->reset('order');

        return $collection;
    }

    /**
     * @return array
     */
    protected function getProductIds()
    {
        if ($this->context->getData('preview')) {
            $collection = $this->productCollectionFactory->create();
            $collection->getSelect()
                ->orderRand()
                ->limit(20);

            return $collection->getAllIds(20);
        } else {
            /** @var \Magento\Sales\Model\Order $order */
            $baseProducts = $this->getBaseProducts();

            $result = [];
            /** @var \Magento\Catalog\Model\Product $baseProduct */
            foreach ($baseProducts as $baseProduct) {
                if ($baseProduct instanceof \Magento\Catalog\Model\Product) {
                    foreach ($baseProduct->getRelatedProductIds() as $id) {
                        $result[] = $id;
                    }
                    foreach ($baseProduct->getUpSellProductIds() as $id) {
                        $result[] = $id;
                    }
                    foreach ($baseProduct->getCrossSellProductIds() as $id) {
                        $result[] = $id;
                    }
                }
            }

            return $result;
        }
    }

    /**
     * @return array
     */
    protected function getBaseProducts()
    {
        $result = [];

        if ($this->context->getOrder()) {
            foreach ($this->context->getData('order')->getAllVisibleItems() as $item) {
                $result[] = $item->getProduct();
            }
        }

        if ($this->context->getQuote() && count($result) == 0) {
            foreach ($this->context->getData('quote')->getAllVisibleItems() as $item) {
                $result[] = $item->getProduct();
            }
        }

        return array_filter($result);
    }
}