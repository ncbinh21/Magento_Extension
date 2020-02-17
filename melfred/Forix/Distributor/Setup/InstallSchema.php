<?php


namespace Forix\Distributor\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_forix_distributor_zipcode = $setup->getConnection()->newTable($setup->getTable('forix_distributor_zipcode'));

        $table_forix_distributor_zipcode->addColumn(
            'zipcode_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_forix_distributor_zipcode->addColumn(
            'distributor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Linked with store locator id'
        );

        $table_forix_distributor_zipcode->addColumn(
            'zipcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'zipcode'
        );

        $table_forix_distributor_zipcode->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'city'
        );

        $table_forix_distributor_zipcode->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'country'
        );

        $setup->getConnection()->createTable($table_forix_distributor_zipcode);
    }
}
