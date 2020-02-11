<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

class Modifyprice extends \Amasty\Paction\Model\Command
{
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $_helper;

    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        $this->_helper = $helper;
        $this->objectManager = $objectManager;
        $this->resource = $resource;
        $this->_eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->connection = $resource->getConnection();

        parent::__construct();

        $this->_type = 'modifyprice';
        $this->_info = [
            'confirm_title'   => 'Update Price',
            'confirm_message' => 'Are you sure you want to update price?',
            'type'            => 'modifyprice',
            'label'           => 'Update Price',
            'fieldLabel'      => 'By',
            'placeholder'     => '+12.5, -12.5, +12.5%'
        ];
    }
        
    /**
     * Executes the command
     *
     * @param array $ids product ids
     * @param int $storeId store id
     * @param string $val field value
     * @return string success message if any
     */    
    public function execute($ids, $storeId, $val)
    {
        if (!preg_match('/^[+-][0-9]+(\.[0-9]+)?%?$/', $val)) {
            throw new LocalizedException(__('Please provide the difference as +12.5, -12.5, +12.5% or -12.5%'));
        }
        
        $sign = substr($val, 0, 1);
        $val  = substr($val, 1);
        
        $percent = ('%' == substr($val, -1, 1));
        if ($percent) {
            $val = substr($val, 0, -1);
        }
            
        $val = floatval($val);
        if ($val < 0.00001) {
            throw new LocalizedException(__('Please provide a non empty difference'));
        }
        
        $attrCode = $this->_getAttrCode();
        $this->_updateAttribute(
            $attrCode,
            $ids,
            $storeId,
            [
                'sign' => $sign, 'val' => $val, 'percent' => $percent
            ]
        );
        
        $success = __('Total of %1 products(s) have been successfully updated', count($ids));
        return $success;
    }
    
    /**
     * Mass update attribute value
     *
     * @param string $attrCode attribute code, price or special price
     * @param array $productIds applied product ids
     * @param int $storeId store id
     * @param array $diff difference data (sign, value, if percentage)
     * @return bool true
     */
    protected function _updateAttribute($attrCode, $productIds, $storeId, $diff)
    {
        $attribute = $this->_eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attrCode);
        $db     = $this->connection;
        $table  = $attribute->getBackend()->getTable();
        $entityIdName = $this->_helper->getEntityNameDependOnEdition();

        $where = [
            $db->quoteInto($entityIdName . ' IN(?)', $productIds),
            $db->quoteInto('attribute_id=?', $attribute->getAttributeId()),
        ];
        
        /**
         * If we work in single store mode all values should be saved just
         * for default store id. In this case we clear all not default values
         */
        
        $defaultStoreId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        if ($this->storeManager->isSingleStoreMode()) {
            $db->delete($table, join(' AND ', array_merge($where, [
                $db->quoteInto('store_id <> ?', $defaultStoreId)
            ])));
        }
        
        $value = $diff['percent'] ? '`value` * ' . $diff['val'] . '/ 100' : $diff['val'];
        $value = '`value`' . $diff['sign'] . $value;

        if ('fixed' == $this->_helper->getModuleConfig('general/round', $storeId)) {
            $fixed = $this->_helper->getModuleConfig('general/fixed', $storeId);
            if (!empty($fixed)) {
                $fixed = floatval($fixed);
                $value = 'FLOOR(' . $value . ') + ' . $fixed;
            }
        } else { // math
            $value = 'ROUND(' . $value . ',2)'; // probably we will need change 2 to additional setting
        }

        $storeIds  = [];
        if ($attribute->isScopeStore()) {
            $where[] = $db->quoteInto('store_id = ?', $storeId);
            $storeIds[] = $storeId;
        } elseif ($attribute->isScopeWebsite() && $storeId != $defaultStoreId) {
            $storeIds = $this->storeManager->getStore($storeId)->getWebsite()->getStoreIds(true);
            $where[] = $db->quoteInto('store_id IN(?)', $storeIds);
        } else {
            $where[] = $db->quoteInto('store_id = ?', $defaultStoreId);
        }
        
        // in case of store-view or website scope we need to insert default values
        // first, to be able to update them. 
        // @todo: Special price can be null in the base store
        if ($storeIds) {
            $cond = [
                $db->quoteInto('t.' . $entityIdName .' IN(?)', $productIds),
                $db->quoteInto('t.attribute_id=?', $attribute->getAttributeId()),
                't.store_id = ' . $defaultStoreId,
            ];
            foreach ($storeIds as $id) {
                // copy attr value from global scope if current attr value does not exists.
                $sql = "INSERT IGNORE INTO $table (`entity_type_id`, `attribute_id`, `store_id`, `$entityIdName`, `value`) "
                     . " SELECT t.`entity_type_id`, t.`attribute_id`, $id, t.`$entityIdName`, t.`value` FROM $table AS t"
                     . " WHERE " . join(' AND ', $cond)
                ;
                $db->query($sql);
            }
        }
        
        $sql = $this->_prepareQuery($table, $value, $where);
        $db->query($sql);
              
        return true;
    }

    protected function _prepareQuery($table, $value, $where)
    {
        $sql = "UPDATE $table SET `value` = $value WHERE " . join(' AND ', $where);
        return $sql;
    }
    
    protected function _getAttrCode()
    {
        return 'price';
    }
}
