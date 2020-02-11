<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Command;
use Magento\Framework\App\ResourceConnection;

class Addspecialbycost extends Addspecial
{
    public function __construct(
        \Amasty\Paction\Helper\Data $helper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ResourceConnection $resource
    ) {
        parent::__construct($helper, $objectManager, $eavConfig, $storeManager, $resource);

        $this->_type = 'addspecialbycost';
        $this->_info = array_merge( $this->_info, [
            'confirm_title'   => 'Modify Special Price using Cost',
            'confirm_message' => 'Are you sure you want to modify special price using cost?',
            'type'            =>  $this->_type,
            'label'           => 'Modify Special Price using Cost'
        ]);
    }

    protected function _getAttrCode()
    {
        return 'cost';
    }
}
