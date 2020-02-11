<?php

namespace Forix\Checkout\Block\Cart;

use Magento\CatalogInventory\Helper\Stock as StockHelper;

class CrosssellCustom extends \Magento\Checkout\Block\Cart\Crosssell
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory,
        \Magento\Quote\Model\Quote\Item\RelatedProducts $itemRelationsList,
        StockHelper $stockHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $productVisibility,
            $productLinkFactory,
            $itemRelationsList,
            $stockHelper,
            $data
        );
        // this variable you can change what you need
        $this->_maxItemCount = 12;
    }
}