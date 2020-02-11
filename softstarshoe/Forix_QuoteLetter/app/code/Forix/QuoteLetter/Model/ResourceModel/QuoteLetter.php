<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2 - Soft StartShoes
 * Date: 1/30/18
 * Time: 2:43 PM
 */

namespace Forix\QuoteLetter\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Exception\LocalizedException;


/**
 * Class QuoteLetter
 * @var $object \Forix\QuoteLetter\Model\QuoteLetter
 * @package Forix\QuoteLetter\Model\ResourceModel
 */
class QuoteLetter extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var DateTime
     */
    private $_date;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var
     */
    protected $_productSKUs;

    /**
     * @var
     */
    protected $_categoryIds;

    public function __construct(
        Context $context,
        DateTime $date,
        StoreManagerInterface $storeManager,
        $connectionName = NULL)
    {
        parent::__construct($context, $connectionName);
        $this->_date = $date;
        $this->storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_quoteletter', 'quoteletter_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeDelete(AbstractModel $object)
    {
        $productTable = $this->getTable('forix_quoteletter_product');
        $categoryTable = $this->getTable('forix_quoteletter_category');

        $condition = ['quoteletter_id = ?' => (int)$object->getId()];

        $connection = $this->getConnection();
        try {
            $connection->delete($productTable, $condition);
        } catch (\Exception $e) {
        }
        try {
            $connection->delete($categoryTable, $condition);
        } catch (\Exception $e) {
        }
        return parent::_beforeDelete($object);
    }

    /**
     * {inheritdoc}
     */
    public function _afterLoad(\Magento\Framework\Model\AbstractModel $object){
        /**
         * @var $object \Forix\QuoteLetter\Model\QuoteLetter
         */
        $productSKUs = $this->getProductSKUs($object->getId());
        $category_ids = $this->getCategoryIds($object->getId());
        $object->setProductSKUs($productSKUs);
        $object->setCategoryIds($category_ids);
        return parent::_afterLoad($object);
    }

    /**
     * Return Array SKUs product assign to quote letter
     * @param $id
     * @param int $storeId
     * @return array|null
     */
    public function getProductSKUs($id, $storeId = 0)
    {
        if (!$this->_productSKUs) {
            $connection = $this->_resources->getConnection();
            $productTable = $this->getTable('forix_quoteletter_product');
            $select = $connection->select()->from($productTable, ['product_sku'])
                ->where('quoteletter_id = ?', $id);
            //->where('store_id = ?', $storeId);
            $this->_productSKUs = $connection->fetchCol($select);
        }
        return $this->_productSKUs;
    }

    /**
     * @param $id
     * @param int $storeId
     * @return array
     */
    public function getCategoryIds($id, $storeId = 0)
    {
        if (!$this->_categoryIds) {
            $connection = $this->_resources->getConnection();
            $categoryTable = $this->getTable('forix_quoteletter_category');
            $select = $connection->select()->from($categoryTable, ['category_id'])
                ->where('quoteletter_id = ?', $id);
            //->where('store_id = ?', $storeId);
            $this->_categoryIds = $connection->fetchCol($select);
        }
        return $this->_categoryIds;
    }

    /**
     * {inheritdoc}
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /**
         * @var $object \Forix\QuoteLetter\Model\QuoteLetter
         */
        if (!$object->getId()) {
            $object->setCreatedAt($this->_date->gmtDate());
        }
        $object->setUpdatedAt($this->_date->gmtDate());
        return parent::_beforeSave($object);
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /**
         * @var $object \Forix\QuoteLetter\Model\QuoteLetter
         */
        $categoryIds = $object->getCategoryIds();
        $productSKUs = $object->getProductSKUs();
        $objectId = $object->getId();

        $categoryIds = is_string($categoryIds) ? explode(',', $categoryIds) : (array)$categoryIds;
        $productSKUs = is_string($productSKUs) ? explode(',', $productSKUs) : (array)$productSKUs;

        $productTable = $this->getTable('forix_quoteletter_product');
        $categoryTable = $this->getTable('forix_quoteletter_category');

        /**
         * Delete Old Data
         */
        try {
            $this->getConnection()->delete($productTable, ['quoteletter_id = ?' => $objectId]);

        } catch (\Exception $e) {
        }
        try {
            $this->getConnection()->delete($categoryTable, ['quoteletter_id = ?' => $objectId]);
        } catch (\Exception $e) {
        }

        /**
         * Insert New Data
         */
        try {
            $dataCatIds = [];
            if(!empty($categoryIds)) {
                foreach ($categoryIds as $catId) {
                    $dataCatIds[] = [
                        'quoteletter_id' => $objectId,
                        'category_id' => (int)$catId
                    ];
                }
                $this->getConnection()->insertMultiple($categoryTable, $dataCatIds);
            }
        } catch (\Exception $e) {
        }
        try {
            $dataProSKUs = [];
            if(!empty($productSKUs)) {
                foreach ($productSKUs as $sku) {
                    $dataProSKUs[] = [
                        'quoteletter_id' => $objectId,
                        'product_sku' => $sku
                    ];
                }
                $this->getConnection()->insertMultiple($productTable, $dataProSKUs);
            }
        } catch (\Exception $e) {
        }
        return parent::_afterSave($object); // TODO: Change the autogenerated stub
    }


}
