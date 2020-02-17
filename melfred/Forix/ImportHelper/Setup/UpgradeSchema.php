<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 4:28 PM
 */
namespace Forix\ImportHelper\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.2.0') < 0) {
            $table_forix_rawdata = $setup->getConnection()->newTable($setup->getTable('forix_rawdata'));


            $table_forix_rawdata->addColumn(
                'rawdata_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,),
                'Entity ID'
            );
            $table_forix_rawdata->addColumn(
                'file_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                array('nullable' => false,),
                'Import from file'
            );

            $setup->getConnection()->createTable($table_forix_rawdata);
        }
        $setup->endSetup();
    }
}