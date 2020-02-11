<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Forix\ProductLabel\Model\ImageUploader;
use Forix\ProductLabel\Model\Attribute as LabelAttribute;

/**
 * Class Label
 * @package Forix\ProductLabel\Model\ResourceModel
 */
class Rule extends AbstractDb
{

    /**@#%
     * @const
     */
    const PRODUCT_LABEL_STORE_TABLE = 'forix_productlabel_store';
    const PRODUCT_LABEL_CUSTOMER_GROUPS_TABLE = 'forix_productlabel_customer_groups';

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @var LabelAttribute
     */
    protected $labelAttribute;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param ImageUploader $imageUploader
     * @param LabelAttribute $labelAttribute
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader,
        LabelAttribute $labelAttribute,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->imageUploader = $imageUploader;
        $this->labelAttribute = $labelAttribute;
    }

    /**
     *  Initialize resource model
     */
    public function _construct()
    {
        $this->_init('forix_productlabel_rule', 'rule_id');
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $this->processImage($object, 'category_image');
        $this->processImage($object, 'product_image');

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     * @param $key
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processImage(AbstractModel $object, $key)
    {
        $images = $object->getData($key);
        if (is_array($images) && !empty($images)) {
            foreach ($images as $image) {
                $name = $image['name'];
                if (isset($image['type'])) {
                    $this->imageUploader->moveFileFromTmp($name);
                }
                $object->unsetData($key);
                $object->setData($key, $name);
            }
        } else {
            $object->setData($key, null);
        }

        return $this;
    }

    /**
     * Perform operations after object load
     *
     * @param AbstractModel $object
     * @return mixed
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_ids', $stores);
            $groups = $this->lookupCustomerGroupsIds($object->getId());
            $object->setData('customer_group_ids', $groups);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $rule
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterDelete(AbstractModel $rule)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable(self::PRODUCT_LABEL_STORE_TABLE),
            ['rule_id=?' => $rule->getId()]
        );
        $connection->delete(
            $this->getTable(self::PRODUCT_LABEL_CUSTOMER_GROUPS_TABLE),
            ['rule_id=?' => $rule->getId()]
        );
        $this->labelAttribute->deleteRule($rule);

        return parent::_afterDelete($rule);
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    protected function _afterSave(AbstractModel $object)
    {
        /** @var \Forix\ProductLabel\Model\Rule $object */
        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array)$object->getStoreIds();
        if (empty($newIds)) {
            $newIds = (array)$object->getStoreId();
        }
        $this->_updateForeignKey(
            $object,
            $newIds,
            $oldIds,
            self::PRODUCT_LABEL_STORE_TABLE,
            'store_id'
        );

        $newIds = (array)$object->getCustomerGroupIds();
        if (is_array($newIds)) {
            $oldIds = $this->lookupCustomerGroupsIds($object->getId());
            $this->_updateForeignKey(
                $object,
                $newIds,
                $oldIds,
                self::PRODUCT_LABEL_CUSTOMER_GROUPS_TABLE,
                'customer_group_id'
            );
        }
        $this->labelAttribute->setRule($object);
        $this->labelAttribute->saveAttributes();

        return parent::_afterSave($object);
    }

    /**
     * Update Foreign Key connections
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @param  array $newRelatedIds
     * @param  array $oldRelatedIds
     * @param  String $tableName
     * @param  String  $field
     * @return void
     */
    protected function _updateForeignKey(
        AbstractModel $object,
        array $newRelatedIds,
        array $oldRelatedIds,
        $tableName,
        $field
    ) {
        $table = $this->getTable($tableName);
        $insert = array_diff($newRelatedIds, $oldRelatedIds);
        $delete = array_diff($oldRelatedIds, $newRelatedIds);

        if ($delete) {
            $where = [
                'rule_id = ?' => (int)$object->getId(),
                $field.' IN (?)' => $delete
            ];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'rule_id' => (int)$object->getId(),
                    $field => (int)$storeId
                ];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * Get Website ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        return $this->_lookupIds(
            $id,
            self::PRODUCT_LABEL_STORE_TABLE,
            'store_id'
        );
    }

    /**
     * @param $id
     * @return array
     */
    protected function lookupCustomerGroupsIds($id)
    {
        return $this->_lookupIds(
            $id,
            self::PRODUCT_LABEL_CUSTOMER_GROUPS_TABLE,
            'customer_group_id'
        );
    }

    /**
     * Get ids to which specified item is assigned
     *
     * @param  int $id
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($id, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'rule_id = ?',
            (int)$id
        );

        return $adapter->fetchCol($select);
    }
}
