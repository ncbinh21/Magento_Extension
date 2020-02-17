<?php


namespace Forix\NetTerm\Setup;

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
        $table_forix_netterm_netterm = $setup->getConnection()->newTable($setup->getTable('forix_netterm_netterm'));

        $table_forix_netterm_netterm->addColumn(
            'netterm_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_forix_netterm_netterm->addColumn(
            'business',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'business'
        );

        $table_forix_netterm_netterm->addColumn(
            'type_business',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'type_business'
        );

        $table_forix_netterm_netterm->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'address'
        );

        $table_forix_netterm_netterm->addColumn(
            'year_established',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'year_established'
        );

        $table_forix_netterm_netterm->addColumn(
            'owners_officers',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'owners_officers'
        );

        $table_forix_netterm_netterm->addColumn(
            'company_references',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'company_references'
        );

        $table_forix_netterm_netterm->addColumn(
            'full_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'full_name'
        );

        $table_forix_netterm_netterm->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'title'
        );

        $table_forix_netterm_netterm->addColumn(
            'date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'date'
        );

        $setup->getConnection()->createTable($table_forix_netterm_netterm);
    }
}
