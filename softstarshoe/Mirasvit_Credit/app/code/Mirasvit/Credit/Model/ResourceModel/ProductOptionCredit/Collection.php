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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit;

use Mirasvit\Credit\Api\Data\ProductOptionCreditInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\DB\Select;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Credit\Model\ProductOptionCredit',
            'Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit');
    }

    /**
     * @param int $typeId
     * @param int $storeId
     *
     * @return $this
     */
    public function addTypeFilter($typeId, $storeId)
    {
        $this->getSelect()
            ->where('option_type_id = ?', intval($typeId))
            ->where('store_id in (?)', [0, $storeId])
        ;

        return $this;
    }

    /**
     * @param int $productId
     *
     * @return $this
     */
    public function addProductFilter($productId)
    {
        $this->getSelect()
            ->where(ProductOptionCreditInterface::KEY_OPTION_PRODUCT_ID.' = ?', intval($productId))
        ;

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()
            ->where(ProductOptionCreditInterface::KEY_STORE_ID.' IN (?, 0)', intval($storeId))
        ;

        return $this;
    }
}