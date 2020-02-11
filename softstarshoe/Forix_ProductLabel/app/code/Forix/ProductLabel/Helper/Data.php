<?php
/**
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Helper;

use Forix\ProductLabel\Model\ResourceModel\Rule\Collection;
use Magento\Catalog\Model\Product;
use Forix\ProductLabel\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\Data\CollectionFactory as DataCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Forix\ProductLabel\Model\Condition\Sql\Builder as SqlConditionBuilder;
use Forix\ProductLabel\Model\Source\Config\Type as LabelType;
use Magento\CatalogRule\Model\Rule as RuleModel;
use Forix\ProductLabel\Model\Attribute as ProductLabelAttribute;

/**
 * Class Data
 *
 * @package Forix\ProductLabel\Helper
 */
class Data
{

    /**
     * @var Collection
     */
    protected $_labelCollection;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DataCollectionFactory
     */
    protected $dataCollectionFactory;

    /**
     * @var DateTime
     */
    protected $_coreDate;

    /**
     * @var SqlConditionBuilder
     */
    protected $sqlBuilder;

    /**
     * @var RuleModel
     */
    protected $_catalogRule;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * Data constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param DataCollectionFactory $dataCollectionFactory
     * @param DateTime $dateTime
     * @param SqlConditionBuilder $sqlBuilder
     * @param RuleModel $catalogRule
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        DataCollectionFactory $dataCollectionFactory,
        DateTime $dateTime,
        SqlConditionBuilder $sqlBuilder,
        RuleModel $catalogRule
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->_coreDate = $dateTime;
        $this->sqlBuilder = $sqlBuilder;
        $this->_catalogRule = $catalogRule;
    }

    /**
     * @param $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $product;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Rule Collection
     *
     * @return Collection
     */
    public function getCollection()
    {
        if ($this->_labelCollection === null) {
            $datetime = $this->_coreDate->gmtDate();

            $startFilter = [
                'or' => [
                    0 => ['date' => true, 'to' => $datetime],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ];
            $endFilter = [
                'or' => [
                    0 => ['date' => true, 'from' => $datetime],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ];

            $collections = $this->collectionFactory->create()
                ->addActiveFilter()
                ->addFieldToFilter('from_date', $startFilter)
                ->addFieldToFilter('to_date', $endFilter)
                ->setOrder('priority', 'ASC')
                ->setOrder('category_position', 'ASC');

            $this->_labelCollection = $collections;
        }

        return $this->_labelCollection;
    }

    /**
     * @return \Magento\Framework\Data\Collection
     */
    public function getNewCollection()
    {
        return $this->dataCollectionFactory->create();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Framework\Data\Collection
     * @throws \Exception
     */
    public function getLabels($product)
    {
        $isNew = false;
        $isSale = false;
        $this->setProduct($product);
        $ruleList = explode(',', $product->getData(ProductLabelAttribute::LABEL_ATTRIBUTE_CODE));

        $collect = $this->getNewCollection();
        $collection = $this->getCollection();
        if ($product->getId() && !empty($ruleList)) {
            foreach ($ruleList as $ruleId) {
                if ($collection->getItemById($ruleId) !== null) {
                    /** @var \Forix\ProductLabel\Model\Rule $rule */
                    $rule = $collection->getItemById($ruleId);
                    $type = $rule->getType();
                    if ($this->isUsedNewLabel($isNew, $type)
                        || $this->isUsedSaleLabel($isSale, $type)
                    ) {
                        continue;
                    }
                    $collect->addItem($rule);
                    if ($type == LabelType::TYPE_NEW_LABEL) {
                        $isNew = true;
                    } elseif ($type == LabelType::TYPE_SALE_LABEL) {
                        $isSale = true;
                    }
                }
            }
        }

        return $collect;
    }

    /**
     * @param $isNew
     * @param $type
     * @return bool
     */
    protected function isUsedNewLabel($isNew, $type)
    {
        if ($isNew && $type == LabelType::TYPE_NEW_LABEL && $this->_product->isAvailable()) {
            return true;
        }

        return false;
    }

    /**
     * @param $isSale
     * @param $type
     * @return bool
     */
    protected function isUsedSaleLabel($isSale, $type)
    {
        if (!$isSale && $type == LabelType::TYPE_SALE_LABEL) {
            $isSaleProduct = $this->isSale();
            if ($isSale || !$isSaleProduct) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sales Product
     *
     * @return bool
     */
    public function isSale()
    {
        $product = $this->getProduct();
        $price = $product->getPrice();
        $finalFinal = $product->getFinalPrice();
        if ($price > $finalFinal) {
            return true;
        }

        $rule = $this->_catalogRule->calcProductPriceRule($product, $price);
        if ($rule) {
            return true;
        }

        if ($product->getTypeId() == 'configurable') {
            $allProducts = $product->getTypeInstance()->getUsedProducts($this->_product, null);
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($allProducts as $product) {
                $subPrice = $product->getPrice();
                $subFinalPrice = $product->getFinalPrice();
                if ($subPrice > $subFinalPrice) {
                    return true;
                }
            }
        } else if ($product->getTypeId() == 'bundle'){
            $minimalRegularPrice = $product->getPriceInfo()
                ->getPrice('regular_price')
                ->getMinimalPrice();
            $minimalFinalPrice = $product->getPriceInfo()
                ->getPrice('final_price')
                ->getMinimalPrice();

            if ($minimalRegularPrice > $minimalFinalPrice) {
                return true;
            }
        }

        return false;
    }
}
