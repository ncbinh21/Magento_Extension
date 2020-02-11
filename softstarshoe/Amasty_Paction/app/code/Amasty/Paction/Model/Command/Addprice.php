<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Addprice extends Modifyprice
{
    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        parent::__construct($helper, $objectManager, $eavConfig, $storeManager, $resource);

        $this->_type = 'addprice';
        $this->_info = array_merge( $this->_info, [
            'confirm_title'   => 'Modify Price using Cost',
            'confirm_message' => 'Are you sure you want to modify price using cost?',
            'type'            => 'addprice',
            'label'           => 'Modify Price using Cost'
        ]);
    }
    
    protected function _prepareQuery($table, $value, $where)
    {
        $where[] = 't.`value` > 0 ';
        $id = $attribute = $this->_eavConfig
            ->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'price')
            ->getAttributeId();
        $entityIdName = $this->_helper->getEntityNameDependOnEdition();
            
        $value = str_replace('`value`', 't.`value`', $value);    
        $sql = "INSERT INTO $table (attribute_id , store_id, $entityIdName, `value`) "
             . " SELECT $id, store_id, $entityIdName, $value FROM $table AS t"
             . " WHERE " . join(' AND ', $where)
             . " ON DUPLICATE KEY UPDATE `value` = $value";
        return $sql;
    }
    
    protected function _getAttrCode()
    {
        return 'cost';
    }
}
