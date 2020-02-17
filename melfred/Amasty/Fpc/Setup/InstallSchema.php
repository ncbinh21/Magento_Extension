<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this
            ->createQueueTable($installer)
            ->createLogTable($installer);

        $installer->endSetup();
    }

    public function createQueueTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_fpc_queue_page'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'url',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'rate',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'store',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_fpc_queue_page',
                    'store',
                    'store',
                    'store_id'
                ),
                'store',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->addIndex(
                $installer->getIdxName('amasty_fpc_queue', ['rate']),
                ['rate']
            )
            ->setComment('Amasty FPC Queue Table');

        $installer->getConnection()->createTable($table);

        return $this;
    }

    public function createLogTable(SchemaSetupInterface $installer)
    {
        $describe = $installer->getConnection()->describeTable($installer->getTable('customer_group'));

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_fpc_log'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
            )
            ->addColumn(
                'url',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'store',
                Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            )
            ->addColumn(
                'currency',
                Table::TYPE_TEXT,
                3,
                ['nullable' => true]
            )
            ->addColumn(
                'customer_group',
                $describe['customer_group_id']['DATA_TYPE'] == 'int' ? Table::TYPE_INTEGER : Table::TYPE_SMALLINT,
                5,
                ['unsigned' => true, 'nullable' => true]
            )
            ->addColumn(
                'rate',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'load_time',
                Table::TYPE_FLOAT,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_fpc_log',
                    'store',
                    'store',
                    'store_id'
                ),
                'store',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'amasty_fpc_log',
                    'customer_group',
                    'customer_group',
                    'customer_group_id'
                ),
                'customer_group',
                $installer->getTable('customer_group'),
                'customer_group_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Amasty FPC Log Table');

        $installer->getConnection()->createTable($table);

        return $this;
    }
}
