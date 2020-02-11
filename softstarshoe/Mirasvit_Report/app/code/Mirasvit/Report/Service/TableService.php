<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Service;


use Magento\Framework\App\ResourceConnection;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Mirasvit\Report\Api\Service\TableDescriptorInterface;

class TableService implements TableDescriptorInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;
    /**
     * @var TableInterface
     */
    private $table;

    /**
     * TableService constructor.
     *
     * @param TableInterface     $table
     * @param ResourceConnection $resource
     */
    public function __construct(
        TableInterface $table,
        ResourceConnection $resource
    ) {
        $this->table = $table;
        $this->resource = $resource;
        $this->connection = $resource->getConnection($table->getConnectionName());
    }

    /**
     * {@inheritdoc}
     */
    public function describeTable()
    {
        return $this->connection->describeTable($this->resource->getTableName($this->table->getName()));
    }

    /**
     * {@inheritDoc}
     */
    public function fetchAll($offset, $limit)
    {
        list($from, $to) = $this->getFilters();

        $select = $this->connection->select()
            ->from($this->resource->getTableName($this->table->getName()))
            ->limitPage($offset, $limit);

        if ($from && $to) {
            $select->where('created_at >= "'.$from.'"');
            $select->where('created_at <= "'.$to.'"');
        }


        return $this->connection->fetchAll($select);
    }

    private function getFilters()
    {
        $from = null;
        $to = null;

        if (isset($_GET['filters']) && isset($_GET['filters']['sales_order|created_at'])) {
            $from = $_GET['filters']['sales_order|created_at']['from'];
            $to = $_GET['filters']['sales_order|created_at']['to'];
        }

        return [$from, $to];
    }
}
