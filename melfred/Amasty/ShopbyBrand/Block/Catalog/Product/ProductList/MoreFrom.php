<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Block\Catalog\Product\ProductList;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class MoreFrom extends \Magento\Catalog\Block\Product\AbstractProduct
{
    const DEFAULT_PRODUCT_LIMIT = 7;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    private $stockHelper;

    /**
     * Item collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_itemCollection;

    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        \Amasty\ShopbyBrand\Helper\Data $helper,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helper = $helper;
        $this->stockHelper = $stockHelper;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getItemCollection()
    {
        return $this->_itemCollection;
    }

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->_coreRegistry->registry('product');
        $attributeCode = $this->helper->getBrandAttributeCode();
        $attributeValue = $product->getData($attributeCode);

        if (!$attributeValue) {
            return $this;
        }
        $attributeValue = explode(',', $attributeValue);

        $limit = $this->helper->getModuleConfig('more_from_brand/count') ? : self::DEFAULT_PRODUCT_LIMIT;

        $this->_itemCollection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter($attributeCode, ['in' => $attributeValue])
            ->addFieldToFilter('entity_id', ['neq' => $product->getId()])
            ->addStoreFilter()
            ->setPage(1, $limit);

        $this->stockHelper->addInStockFilterToCollection($this->_itemCollection);
        $this->_itemCollection->getSelect()->orderRand();

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if ($this->helper->getModuleConfig('more_from_brand/enable')
            && $this->getItemCollection()
            && $this->getItemCollection()->getSize()
        ) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    /**
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getTitle()
    {
        $title = $this->helper->getModuleConfig('more_from_brand/title');
        preg_match_all('@\{(.+?)\}@', $title, $matches);
        if (isset($matches[1]) && !empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $value = '';
                switch ($match) {
                    case 'brand_name':
                        /** @var \Magento\Catalog\Model\Product $product */
                        $product = $this->_coreRegistry->registry('product');
                        $attributeCode = $this->helper->getBrandAttributeCode();
                        $attributeValue = $product->getData($attributeCode);
                        $attribute = $product->getResource()->getAttribute($attributeCode);
                        if ($attribute && $attribute->usesSource()) {
                            $value = $attribute->getSource()->getOptionText($attributeValue);
                        }

                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        break;
                }
                $title = str_replace('{' . $match . '}', $value, $title);
            }
        }

        $title = $title ?: __('More from this Brand');

        return $title;
    }
}
