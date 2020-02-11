<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Modifyspecial extends Modifyprice
{
    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        parent::__construct($helper, $objectManager, $eavConfig, $storeManager, $resource);
        $this->_type = 'modifyspecial';
        $this->_info = array_merge( $this->_info, [
            'confirm_title'   => 'Update Special Price',
            'confirm_message' => 'Are you sure you want to update special price?',
            'type'            =>  $this->_type,
            'label'           => 'Update Special Price'
        ]);
    }
    
    protected function _getAttrCode()
    {
        return 'special_price';
    }
}