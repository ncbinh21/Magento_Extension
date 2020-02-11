<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;

use Magento\Framework\App\ResourceConnection;

class Modifyallprices extends \Amasty\Paction\Model\Command
{
    protected $_priceCodes = [];
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Amasty\Paction\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct();
        $this->_type = 'modifyallprices';
        $this->_info = [
            'confirm_title'   => 'Update All Types of Price',
            'confirm_message' => 'Are you sure you want to update all types of price?',
            'type'            => $this->_type,
            'label'           => 'Update All Types of Price',
            'fieldLabel'      => 'By',
            'placeholder'     => '+12.5, -12.5, +12.5%'
        ];

        $this->collectionFactory = $collectionFactory;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->eavConfig = $eavConfig;
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
            throw new \Amasty\Paction\Model\CustomException(__('Please provide the difference as +12.5, -12.5, +12.5% or -12.5%'));
        }
        
        $sign = substr($val, 0, 1);
        $val  = substr($val, 1);
        
        $percent = ('%' == substr($val, -1, 1));
        if ($percent)
            $val = substr($val, 0, -1);
            
        $val = floatval($val);
        if ($val < 0.00001) {
            throw new \Amasty\Paction\Model\CustomException(__('Please provide a non empty difference'));
        }

        if (!$this->_priceCodes) {
            $attributes = $this->collectionFactory->create()
                ->addVisibleFilter()
                ->addFieldToFilter('frontend_input', 'price');
            $priceCodes = [];
            foreach ($attributes as $attribute) {
                $priceCodes[$attribute->getId()] = $attribute->getAttributeCode();
            }
            $this->_priceCodes = $priceCodes;
        }

        $this->_updateAttributes($ids, $storeId, array(
            'sign' => $sign, 'val' => $val, 'percent' => $percent)
        );
        
        //TODO change indexer status
        
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
    protected function _updateAttributes($productIds, $storeId, $diff)
    {
        $attribute =  $this->eavConfig->getAttribute('catalog_product', 'price');

        $db    = $this->connection;
        $table = $attribute->getBackend()->getTable();
        $entityIdName = $this->helper->getEntityNameDependOnEdition();

        $where = array(
            $db->quoteInto($entityIdName . ' IN(?)', $productIds),
            $db->quoteInto('attribute_id IN(?)', array_keys($this->_priceCodes)),
        );

        $value = $diff['percent'] ? '`value` * ' . $diff['val'] . '/ 100' : $diff['val'];
        $value = '`value`' . $diff['sign'] . $value; 

        if ('fixed' == $this->helper->getModuleConfig('general/round', $storeId)) {
            $fixed = $this->helper->getModuleConfig('general/fixed', $storeId);
            if (!empty($fixed)) {
                $fixed = floatval($fixed);
                $value = 'FLOOR(' . $value . ') + ' . $fixed;
            }
        } else { // math
            $value = 'ROUND(' . $value . ',2)'; // probably we will need change 2 to additional setting
        }

        $defaultStoreId = $this->storeManager->getDefaultStoreView()->getId();
        if ($storeId
            && $defaultStoreId != $storeId) {
            $storeIds = $this->storeManager->getStore($storeId)->getWebsite()->getStoreIds(true);
            $where[] = $db->quoteInto('store_id IN(?)', $storeIds);
        } else { // all stores
            $storeIds = [];
            $stores = $this->storeManager->getStores(true);
            foreach ($stores as $store) {
                $storeIds[] = $store->getId();
            }
            $where[] = $db->quoteInto('store_id IN(?)', $storeIds);
        }

        $where[] = 'value IS NOT NULL';

        // update all price attributes
        $sql = $this->_prepareQuery($table, $value, $where);
        $db->query($sql);

        // update group prices
       /* $table = $this->resource->getTableName('catalog_product_entity_group_price');
        $sql = $this->_prepareQuery($table, $value, $where);
        $db->query($sql);

        // update tier prices
        $table = $this->resource->getTableName('catalog_product_entity_tier_price');
        $sql = $this->_prepareQuery($table, $value, $where);
        $db->query($sql);
       */

        //update tier price
        $websiteId = $this->storeManager->getStore($storeId)->getWebsite()->getId();
        $where = array(
            $db->quoteInto($entityIdName . ' IN(?)', $productIds),
            $db->quoteInto('website_id = ?', $websiteId),
        );
        $table =  $this->resource->getTableName('catalog_product_entity_tier_price');
        $sql = $this->_prepareQuery($table, $value, $where);
        $db->query($sql);
              
        return true;
    }

    protected function _prepareQuery($table, $value, $where)
    {
        $sql = "UPDATE $table SET `value` = $value WHERE " . join(' AND ', $where);
        return $sql;
    }
}
