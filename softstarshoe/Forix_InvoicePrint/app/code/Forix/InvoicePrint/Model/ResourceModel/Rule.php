<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 27
 * Time: 15:29
 */

namespace Forix\InvoicePrint\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Rule
 * @package Forix\InvoicePrint\Model\ResourceModel
 */
class Rule extends AbstractDb
{

    /**@#%
     * @const
     */
    const INVOICE_PRINT_STORE_TABLE = 'forix_invoiceprint_store';



    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     *  Initialize resource model
     */
    public function _construct()
    {
        $this->_init('forix_invoiceprint_rule', 'rule_id');
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
        }

        return parent::_afterLoad($object);
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if(!$object->getCreatedAt()){
            $object->setCreatedAt(gmdate('Y-m-d H:i:s'));
        }
        $object->setUpdatedAt(gmdate('Y-m-d H:i:s'));
        return parent::_beforeSave($object); // TODO: Change the autogenerated stub
    }

    /**
     * Delete foreign key
     *
     * @param $rule
     * @return mixed
     */
    protected function _afterDelete(AbstractModel $rule)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable(self::INVOICE_PRINT_STORE_TABLE),
            ['rule_id=?' => $rule->getId()]
        );

        return parent::_afterDelete($rule);
    }

    /**
     * Assign rule to store views
     *
     * @param AbstractModel $object
     * @return mixed
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
            self::INVOICE_PRINT_STORE_TABLE,
            'store_id'
        );
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
            self::INVOICE_PRINT_STORE_TABLE,
            'store_id'
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
