<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Model\ResourceModel\Template;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Amasty\OrderStatus\Model\Template',
            'Amasty\OrderStatus\Model\ResourceModel\Template'
        );
    }

    public function loadTemplateId($statusId, $storeId)
    {
        $connection = $this->getConnection();
        $sql = 'SELECT template_id FROM `' . $this->getMainTable() . '` WHERE `status_id` = "' . $statusId . '" AND `store_id` = "' . $storeId . '" ' ;
        return $connection->fetchOne($sql);
    }
}
