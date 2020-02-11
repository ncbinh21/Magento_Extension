<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;

use Magento\Framework\App\Helper\Context;

class Category extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;

        return parent::__construct($context);
    }

    public function getMinimalPrice(\Magento\Catalog\Model\Category $category)
    {
        return $category->getProductCollection()->addMinimalPrice()->getMinPrice();
    }

    public function getReviewSummaryInfo(\Magento\Catalog\Model\Category $category)
    {
        $collection = $category->getProductCollection();
        $resource = $collection->getResource();

        $collection->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->reset(\Zend_Db_Select::ORDER)
            ->reset(\Zend_Db_Select::LIMIT_COUNT)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET)
            ->join(
                ['summary' => $resource->getTable('review_entity_summary')],
                'summary.entity_pk_value = e.entity_id',
                ['rating' => 'AVG(rating_summary)', 'reviews' => 'SUM(reviews_count)']
            )
            ->where('summary.store_id = ?', $this->storeManager->getStore()->getId())
            ->where('reviews_count > 0')
        ;

        return $resource->getConnection()->fetchRow($collection->getSelect());
    }
}
