<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\ResourceModel\Order\Status\Plugin;

class Collection
{
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    public function afterToOptionArray($subject, $result)
    {
        /** @var \Amasty\OrderStatus\Model\ResourceModel\Status\Collection $amastyOrdersCollection */
        $amastyOrdersCollection = $this->_objectManager->get('Amasty\OrderStatus\Model\ResourceModel\Status\Collection');

        $amastyOrderOptions = $amastyOrdersCollection->toOptionArray();

        return $amastyOrderOptions;
    }
}
