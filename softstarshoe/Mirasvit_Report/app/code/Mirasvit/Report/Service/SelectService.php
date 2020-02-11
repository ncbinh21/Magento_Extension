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

use Magento\Framework\DB\Adapter\AdapterInterface;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Api\Service\MapServiceInterface;
use Mirasvit\Report\Api\Service\SelectServiceInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mirasvit\Report\Api\Factory\TableDescriptorFactoryInterface;
use Mirasvit\Report\Api\Service\TableDescriptorInterface;

class SelectService implements SelectServiceInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var TableDescriptorFactoryInterface
     */
    private $tableDescriptorFactory;

    /**
     * @var array
     */
    private static $replicatedTables = [];

    /**
     * @var MapRepositoryInterface
     */
    private $mapRepository;

    public function __construct(
        TableDescriptorFactoryInterface $tableDescriptorFactory,
        MapRepositoryInterface $mapRepository,
        ResourceConnection $resource,
        TimezoneInterface $timezone
    ) {
        $this->tableDescriptorFactory = $tableDescriptorFactory;
        $this->mapRepository = $mapRepository;
        $this->resource = $resource;
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function replicateTable(TableInterface $table, TableInterface $baseTable)
    {
        if ($table->getConnectionName() == $baseTable->getConnectionName()) {
            return true;
        }

        $baseConnection = $this->resource->getConnection($baseTable->getConnectionName());

        $tableName = $this->resource->getTableName($table->getName());

        if (!$baseConnection->isTableExists($tableName)
            && !in_array($tableName, self::$replicatedTables)
        ) {
            $tblDescriptor = $this->tableDescriptorFactory->create($table);
            $schema = $tblDescriptor->describeTable();

            $temporaryTable = $baseConnection->newTable($tableName);

            $usedColumns = $this->getUsedColumns($tableName, $schema);

            foreach ($schema as $column) {
                $type = $column['DATA_TYPE'];
                if ($column['DATA_TYPE'] == 'int') {
                    $type = 'integer';
                } elseif ($column['DATA_TYPE'] == 'varchar') {
                    $type = 'text';
                } elseif ($column['DATA_TYPE'] == 'tinyint') {
                    $type = 'smallint';
                }

                if (isset($usedColumns[$column['COLUMN_NAME']])) {
                    $temporaryTable->setColumn([
                        'COLUMN_NAME'      => $column['COLUMN_NAME'],
                        'TYPE'             => $type,
                        'LENGTH'           => $column['LENGTH'],
                        'COLUMN_POSITION'  => $column['COLUMN_POSITION'],
                        'PRIMARY'          => $column['PRIMARY'],
                        'PRIMARY_POSITION' => $column['PRIMARY_POSITION'],
                        'NULLABLE'         => $column['PRIMARY'] ? false : $column['NULLABLE'],
                        'COMMENT'          => $column['COLUMN_NAME'],
                    ]);
                }
            }

            try {
                $baseConnection->createTemporaryTable($temporaryTable);

                $offset = 1;
                while (true) {
                    $rows = $tblDescriptor->fetchAll($offset, 1000);

                    foreach ($rows as $idx => $row) {
                        $row = array_intersect_key($row, $usedColumns);
                        $rows[$idx] = $row;
                    }

                    if (count($rows)) {
                        $baseConnection->insertMultiple($tableName, $rows);
                    } else {
                        break;
                    }

                    $offset++;

                    if ($offset > 30) {
                        break;
                    }
                }
            } catch (\Exception $e) {
            }

            self::$replicatedTables[] = $tableName;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function applyTimeZone(AdapterInterface $connection)
    {
        $utc = $connection->fetchOne('SELECT CURRENT_TIMESTAMP');
        $offset = (new \DateTimeZone($this->timezone->getConfigTimezone()))->getOffset(new \DateTime($utc));
        $h = floor($offset / 3600);
        $m = floor(($offset - $h * 3600) / 60);
        $offset = sprintf("%02d:%02d", $h, $m);

        if (substr($offset, 0, 1) != "-") {
            $offset = "+" . $offset;
        }

        $connection->query("SET time_zone = '$offset'");

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function restoreTimeZone(AdapterInterface $connection)
    {
        $connection->query("SET time_zone = '+00:00'");

        return $this;
    }

    /**
     * Get columns used for table replication.
     *
     * @param string $tableName
     *
     * @return array
     */
    private function getUsedColumns($tableName, $newSchema)
    {
        $usedColumns = [
            'item_id'        => 1,
            'order_id'       => 1,
            'product_id'     => 1,
            'parent_item_id' => 1,
        ];

        if (strpos($tableName, TableDescriptorInterface::TMP_TABLE_SUFFIX) === false) {
            $mapTable = $this->mapRepository->getTable($tableName);
            foreach ($mapTable->getColumns() as $column) {
                foreach ($column->getFields() as $field) {
                    $usedColumns[$field->getName()] = 1;
                }
            }
        } else { // temporary tables do not exist yet, so we can only use declared columns
            foreach ($newSchema as $column) {
                $usedColumns[$column['COLUMN_NAME']] = 1;
            }
        }

        return $usedColumns;
    }
}