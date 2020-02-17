<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_fpc_log', 'id');
    }

    public function deleteWithLimit($limit)
    {
        if ($limit <= 0) {
            return;
        }

        $limit = (int)$limit;

        $query = "DELETE FROM `{$this->getMainTable()}` LIMIT $limit";

        $this->getConnection()->query($query);
    }

    public function flush()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    public function getStatsByStatus()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['status', 'COUNT(id)'])
            ->group('status');

        return $this->getConnection()->fetchPairs($select);
    }

    public function getStatsByDay()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['period' => 'DATE(created_at)', 'count' => 'COUNT(id)'])
            ->order('period')
            ->group('DATE(created_at)');

        return $this->getConnection()->fetchAll($select);
    }
}
