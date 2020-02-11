<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\Order\Status\Plugin;

class History
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
    }

    public function afterSetIsCustomerNotified($subject, $result)
    {
        $amastyStatus = $this->_coreRegistry->registry('amorderstatus_history_status');
        if (!is_null($amastyStatus)) {
            if ($amastyStatus->getNotifyByEmail()) {
                $result->setData('is_customer_notified', true);
            }
        }
        return $result;
    }
}
