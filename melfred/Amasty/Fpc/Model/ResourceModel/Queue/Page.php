<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\ResourceModel\Queue;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Page extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_fpc_queue_page', 'id');
    }

    public function truncate()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    public function getMaxRate()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [new \Zend_Db_Expr('MAX(rate)')]);

        return $this->getConnection()->fetchOne($select);
    }
}
