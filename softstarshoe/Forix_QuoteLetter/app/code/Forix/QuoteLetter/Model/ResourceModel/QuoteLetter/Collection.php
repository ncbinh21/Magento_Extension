<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2 - Soft StartShoes
 * Date: 1/30/18
 * Time: 2:43 PM
 */

namespace Forix\QuoteLetter\Model\ResourceModel\QuoteLetter;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Forix\QuoteLetter\Model\QuoteLetter',
            'Forix\QuoteLetter\Model\ResourceModel\QuoteLetter'
        );
    }

    public function addStoreToFilter($storeId)
    {
        $this->addFieldToFilter('store_id', $storeId);
        return $this;
    }


    /**
     * @param array $productSkus
     * @return $this
     */
    public function addProductToFilter($productSkus)
    {
        $this->_select->joinInner(
            [
                'pro' => $this->getTable('forix_quoteletter_product')
            ],
            'main_table.quoteletter_id = pro.quoteletter_id'
        )->where('pro.product_sku in (?)', $productSkus);
        return $this;
    }

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function addCategoryToFilter($categoryIds)
    {
        $this->_select->joinInner(
            [
                'cat' => $this->getTable('forix_quoteletter_category')
            ],
            'main_table.quoteletter_id = cat.quoteletter_id'
        )->where('cat.category_id in (?)', $categoryIds);
        return $this;
    }
}
