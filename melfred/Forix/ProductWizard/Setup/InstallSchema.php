<?php

namespace Forix\ProductWizard\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_forix_productwizard_group = $setup->getConnection()->newTable($setup->getTable('forix_productwizard_group'));

        
        $table_forix_productwizard_group->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        
        
        $table_forix_productwizard_group->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'title'
        );
        

        
        $table_forix_productwizard_group->addColumn(
            'step_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'step_id'
        );
        

        $table_forix_productwizard_group_item = $setup->getConnection()->newTable($setup->getTable('forix_productwizard_group_item'));

        
        $table_forix_productwizard_group_item->addColumn(
            'group_item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        

        
        $table_forix_productwizard_group_item->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'group_id'
        );
        

        
        $table_forix_productwizard_group_item->addColumn(
            'option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'option_id'
        );

        $table_forix_productwizard_group_item_option = $setup->getConnection()->newTable($setup->getTable('forix_productwizard_group_item_option'));

        
        $table_forix_productwizard_group_item_option->addColumn(
            'group_item_option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        
        

        
        $table_forix_productwizard_group_item_option->addColumn(
            'option_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'option_id'
        );
        

        
        $table_forix_productwizard_group_item_option->addColumn(
            'option_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'option_value'
        );
        

        
        $table_forix_productwizard_group_item->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'title'
        );
        

        
        $table_forix_productwizard_group_item->addColumn(
            'note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'note'
        );
        

        
        $table_forix_productwizard_group_item->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'sort_order'
        );
        

        
        $table_forix_productwizard_group_item_option->addColumn(
            'item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'item_id'
        );
        

        
        $table_forix_productwizard_group_item_option->addColumn(
            'depend_on',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'depend_on'
        );
        

        
        $table_forix_productwizard_group_item->addColumn(
            'template',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'template'
        );
        

        
        $table_forix_productwizard_group_item_option->addColumn(
            'best_on',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'best_on'
        );
        

        

        $setup->getConnection()->createTable($table_forix_productwizard_group_item_option);

        $setup->getConnection()->createTable($table_forix_productwizard_group_item);

        $setup->getConnection()->createTable($table_forix_productwizard_group);

        $setup->endSetup();
    }
}
