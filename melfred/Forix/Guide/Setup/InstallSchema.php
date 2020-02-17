<?php


namespace Forix\Guide\Setup;

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
        $table_forix_guide_download = $setup->getConnection()->newTable($setup->getTable('forix_guide_download'));

        $table_forix_guide_download->addColumn(
            'download_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_forix_guide_download->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'customer_id'
        );

        $table_forix_guide_download->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'name'
        );

        $table_forix_guide_download->addColumn(
            'company',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'company'
        );

        $table_forix_guide_download->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'email'
        );

        $setup->getConnection()->createTable($table_forix_guide_download);
    }
}
