<?php
/**
 * Copyright (c) 2016.
 * Created by Hidro Le.
 * User: Hidro
 */

namespace Forix\ProductWizard\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{


    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $tableName = $installer->getTable('forix_productwizard_group');
            $installer->getConnection()->addColumn(
                $tableName,
                'sort_order',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Group Sort Order'
                )
            );
        }
        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $tableName = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $tableName,
                'attribute_code',
                array(
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Group Sort Order'
                )
            );
            $installer->getConnection()->addColumn(
                $tableName,
                'select_type',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Select Type (Single/Multi)'
                )
            );

            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'sort_order',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Group Sort Order'
                )
            );

            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'title',
                array(
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Group Sort Order'
                )
            );
        }
        if (version_compare($context->getVersion(), '1.0.4') < 0) {

            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->dropColumn($tableNameItemOption, 'option_id');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'option_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Option Id'
                )
            );

        }
        if (version_compare($context->getVersion(), '1.0.5') < 0) {

            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'is_generated',
                array(
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment' => 'This option auto generated'
                )
            );

        }
        if (version_compare($context->getVersion(), '1.0.6') < 0) {

            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'auto_add_item_option',
                array(
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment' => 'Will Auto Render Item'
                )
            );

        }

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $data = [
                'link_type_id' => \Forix\ProductWizard\Model\ResourceModel\Product\Link::LINK_TYPE_RELATION,
                'code' => \Forix\ProductWizard\Model\ResourceModel\Product\Link::LINK_TYPE_RELATION_CODE
            ];
            $setup->getConnection()
                ->insertOnDuplicate($setup->getTable('catalog_product_link_type'), $data);
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            /**
             * install product link attributes
             */
            $data = [
                [
                    'link_type_id' => \Forix\ProductWizard\Model\ResourceModel\Product\Link::LINK_TYPE_RELATION,
                    'product_link_attribute_code' => 'position',
                    'data_type' => 'int',
                ]
            ];

            $setup->getConnection()
                ->insertMultiple($setup->getTable('catalog_product_link_attribute'), $data);
        }


        if (version_compare($context->getVersion(), '1.1.2') < 0) {

            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'product_sku',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'comment' => 'Product Sku'
                )
            );

        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {

            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'is_required',
                array(
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'default' => '0',
                    'comment' => 'Is Required'
                )
            );

        }

        if (version_compare($context->getVersion(), '1.1.4') < 0) {

            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'created_at',
                array(
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Create Time'
                )
            );
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'updated_at',
                array(
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Update Time'
                )
            );


            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'created_at',
                array(
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Create Time'
                )
            );
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'updated_at',
                array(
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Update Time'
                )
            );


            $tableNameItemOption = $installer->getTable('forix_productwizard_group');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'created_at',
                array(
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Create Time'
                )
            );
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'updated_at',
                array(
                    'type' => Table::TYPE_DATETIME,
                    'nullable' => true,
                    'comment' => 'Update Time'
                )
            );

        }


        if (version_compare($context->getVersion(), '1.1.5') < 0) {

            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->dropColumn($tableNameItemOption, 'option_id');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'option_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Option Id'
                )
            );
            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->dropColumn($tableNameItemOption, 'next_to');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'next_to',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 20,
                    'nullable' => true,
                    'comment' => 'Option Id'
                )
            );
        }

        if (version_compare($context->getVersion(), '1.1.9') < 0) {
            /**
             * install product link attributes
             */
            $data = [
                [
                    'link_type_id' => \Forix\ProductWizard\Model\ResourceModel\Product\Link::LINK_TYPE_RELATION,
                    'product_link_attribute_code' => 'attribute_set_id',
                    'data_type' => 'int',
                ]
            ];

            $setup->getConnection()
                ->insertMultiple($setup->getTable('catalog_product_link_attribute'), $data);
        }

        if (version_compare($context->getVersion(), '2.1.0') < 0) {

            $table_forix_productwizard_wizard = $setup->getConnection()->newTable($setup->getTable('forix_productwizard_wizard'));


            $table_forix_productwizard_wizard->addColumn(
                'wizard_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,),
                'Entity ID'
            );

            $table_forix_productwizard_wizard->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'title'
            );


            $table_forix_productwizard_wizard->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'category_id'
            );


            $table_forix_productwizard_wizard->addColumn(
                'identifier',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'identifier'
            );

            $setup->getConnection()->createTable($table_forix_productwizard_wizard);
        }

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $table_forix_productwizard_wizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_wizard,
                'template_update',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => false,
                    'comment' => 'TEMPLATE_UPDATE'
                )
            );

            /**
             * Create table 'cms_page_store'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('forix_wizard_store')
            )->addColumn(
                'wizard_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'primary' => true],
                'Wizard ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $installer->getIdxName('forix_wizard_store', ['store_id']),
                ['store_id']
            )->setComment(
                'CMS Page To Store Linkage Table'
            );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '2.1.2') < 0) {
            $table_forix_productwizard_wizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_wizard,
                'is_active',
                array(
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'Is Active'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.1.3') < 0) {
            $table_forix_productwizard_group_item = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group_item,
                'attribute_set_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Product Attribute Set'
                )
            );

            $table_forix_productwizard_group = $installer->getTable('forix_productwizard_group');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'wizard_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'wizard item id'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.2.1') < 0) {
            $table_forix_productwizard_group = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'wizard_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'wizard item id'
                )
            );

            $forix_productwizard_group_item_option = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->addColumn(
                $forix_productwizard_group_item_option,
                'wizard_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'wizard item id'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.2.2') < 0) {
            $table_forix_productwizard_group = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'attribute_set_ids',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => false,
                    'comment' => 'wizard item id'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.2.3') < 0) {
            $table_forix_productwizard_group = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'attribute_set_id',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'comment' => 'wizard item id'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.2.4') < 0) {
            $table_forix_productwizard_group = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'product_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'wizard item id'
                )
            );
        }


        if (version_compare($context->getVersion(), '2.2.5') < 0) {
            $table_forix_productwizard_group = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'back_to',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'comment' => 'wizard item id'
                )
            );
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'back_to_title',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'comment' => 'wizard item id'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.2.6') < 0) {
            $table_forix_productwizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard,
                'sort_order',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Wizard Sort Order'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.2.7') < 0) {
            $table_forix_productwizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard,
                'attr_set_warning_message',
                array(
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Attribute set warning message'
                )
            );


            $table_forix_productwizard_group = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'is_show_view_all',
                array(
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'wizard item id'
                )
            );
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'show_all_message',
                array(
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'wizard item id'
                )
            );
            $installer->getConnection()->addColumn(
                $table_forix_productwizard_group,
                'button_text',
                array(
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'wizard item id'
                )
            );
        }


        if (version_compare($context->getVersion(), '2.2.8') < 0) {
            $table_forix_productwizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard,
                'config_group',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 100,
                    'nullable' => false,
                    'comment' => 'Config Title Group'
                )
            );

        }

        if (version_compare($context->getVersion(), '2.2.9') < 0) {
            $table_forix_productwizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard,
                'base_image',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'comment' => 'Wizard Header Image'
                )
            );

        }

        if (version_compare($context->getVersion(), '2.2.9.1') < 0) {
            $table_forix_productwizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard,
                'skip_notification_message',
                array(
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Skip step notification message'
                )
            );

            $forix_productwizard_group_item = $installer->getTable('forix_productwizard_group_item');
            $forix_productwizard_group_item_option = $installer->getTable('forix_productwizard_group_item_option');
            $sql = "Update {$forix_productwizard_group_item} set wizard_id = 1 where wizard_id = 0";
            $installer->getConnection()->query($sql);
            $sql = "Update {$forix_productwizard_group_item_option} set wizard_id = 1 where wizard_id = 0";
            $installer->getConnection()->query($sql);
        }

        if (version_compare($context->getVersion(), '2.3.0') < 0) {
            $forix_productwizard_group_item = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $forix_productwizard_group_item,
                'enable_find_option',
                array(
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Enable Select 2 Script.'
                )
            );
        }
        if (version_compare($context->getVersion(), '2.3.1') < 0) {
            $forix_productwizard_group_item_option = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->addColumn(
                $forix_productwizard_group_item_option,
                'item_set_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Item Set ID'
                )
            );
        }
        if (version_compare($context->getVersion(), '2.3.2') < 0) {
            $forix_productwizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->dropColumn($forix_productwizard,'attribute_set_ids');
            $forix_productwizard_group_item_option = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->dropColumn($forix_productwizard_group_item_option,'attribute_set_id');
        }

        if (version_compare($context->getVersion(), '2.3.3') < 0) {
            $forix_productwizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $forix_productwizard,
                'step_count',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 3,
                    'comment' => 'Max Step Of wizard'
                )
            );
            $installer->getConnection()->dropColumn($forix_productwizard,'config_group');
        }
        if (version_compare($context->getVersion(), '2.3.4') < 0) {
            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'item_set_id',
                array(
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Item Set ID'
                )
            );
        }

        if (version_compare($context->getVersion(), '2.3.5') < 0) {
            $table_forix_productwizard = $installer->getTable('forix_productwizard_wizard');
            $installer->getConnection()->addColumn(
                $table_forix_productwizard,
                'banner_image',
                array(
                    'type' => Table::TYPE_TEXT,
                    'size' => 255,
                    'nullable' => true,
                    'comment' => 'Wizard Banner Image'
                )
            );

        }

        if (version_compare($context->getVersion(), '2.3.6') < 0) {
            $tableNameItemOption = $installer->getTable('forix_productwizard_group_item');
            $installer->getConnection()->dropColumn('forix_productwizard_group_item', 'item_set_id');
            $installer->getConnection()->addColumn(
                $tableNameItemOption,
                'item_set_id',
                array(
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Item Set'
                )
            );
            $forix_productwizard_group_item_option = $installer->getTable('forix_productwizard_group_item_option');
            $installer->getConnection()->dropColumn('forix_productwizard_group_item_option', 'item_set_id');
            $installer->getConnection()->addColumn(
                $forix_productwizard_group_item_option,
                'item_set_id',
                array(
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Item Set'
                )
            );
        }
        $installer->endSetup();
    }
}