<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\Plugin;

class Order
{
    protected $_objectManager;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->_objectManager = $objectManager;
    }

    public function afterAddStatusHistoryComment($subject, $result)
    {
        // checking is the new status is one of ours
        /** @var \Amasty\OrderStatus\Model\ResourceModel\Status\Collection $statusCollection */
        $statusCollection = $this->_objectManager->get('Amasty\OrderStatus\Model\ResourceModel\Status\Collection');
        $statusCollection->addFieldToFilter('is_system', array('eq' => 0));
        $status = $result->getStatus();
        foreach ($statusCollection as $statusModel) {
            $strposResult = strpos($status, '_');
            if ($strposResult !== false) {
                if ($statusModel->getAlias() == substr($status, $strposResult + 1)) {
                    // this is it!
                    $this->_coreRegistry->register('amorderstatus_history_status', $statusModel, true);
                }
            }
        }

        return $result;
    }
}
