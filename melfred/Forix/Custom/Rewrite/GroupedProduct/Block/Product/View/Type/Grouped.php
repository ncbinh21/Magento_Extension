<?php

namespace Forix\Custom\Rewrite\GroupedProduct\Block\Product\View\Type;

class Grouped extends \Magento\GroupedProduct\Block\Product\View\Type\Grouped
{
    protected $productRepository;
    protected $_currency;

    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Directory\Model\Currency $currency

    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $arrayUtils, $data=[]);
        $this->_currency = $currency;
    }

    public function getTotalPrice() {
        $total = 0;
        $_associatedProducts = $this->getAssociatedProducts();
        foreach ($_associatedProducts as $_item) {
            $total+= $_item->getFinalPrice()*($_item->getQty() * 1);
        }
        return $total;
    }

    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    public function getProductFromId($item)
    {
        return $this->productRepository->getById($item->getId());
    }
}
