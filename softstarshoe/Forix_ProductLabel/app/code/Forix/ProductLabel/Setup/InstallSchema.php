<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

namespace Forix\ProductLabel\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SetupInterface;

/**
 * Class InstallSchema
 * @package Forix\ProductLabel\Setup
 */
class InstallSchema implements InstallSchemaInterface
{

    /**@#%
     * @const
     */
    const PREFIX_TABLE_NAME = 'forix_productlabel_';

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this->createProductlabelRuleTable($installer);
        $this->createProductlabelStoreTable($installer);
        $this->createProductlabelCustomerGroupsTable($installer);

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createProductlabelRuleTable($installer)
    {
        $tableName = self::PREFIX_TABLE_NAME . 'rule';
        if (!$installer->tableExists($tableName)) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable($tableName)
            )->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true],
                'Rule ID'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Rule Name'
            )->addColumn(
                'type',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Type'
            )->addColumn(
                'description',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Description'
            )->addColumn(
                'status',
                Table::TYPE_SMALLINT,
                null,
                [],
                'Status'
            )->addColumn(
                'from_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'From Date'
            )->addColumn(
                'to_date',
                Table::TYPE_DATETIME,
                null,
                ['nullable' => true],
                'To Date'
            )->addColumn(
                'priority',
                Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Priority'
            )->addColumn(
                'conditions_serialized',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'Conditions Serialized'
            )->addColumn(
                'category_css_background',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'CSS Style Code'
            )->addColumn(
                'category_display',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false],
                'Category Display'
            )->addColumn(
                'category_position',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Category Position'
            )->addColumn(
                'category_image',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Category Image'
            )->addColumn(
                'category_text',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Category Text'
            )->addColumn(
                'product_display',
                Table::TYPE_INTEGER,
                1,
                ['nullable' => false],
                'Product Display'
            )->addColumn(
                'product_position',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Category Position'
            )->addColumn(
                'product_image',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Product Image'
            )->addColumn(
                'product_css_background',
                Table::TYPE_TEXT,
                '2M',
                ['nullable' => true],
                'CSS Style Code'
            )->addColumn(
                'product_text',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Product Text'
            )->setComment(
                'Product Labels Table'
            );

            $installer->getConnection()->createTable($table);
        }
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createProductlabelStoreTable($installer)
    {
        $tableName = self::PREFIX_TABLE_NAME . 'store';
        $ruleTableName = self::PREFIX_TABLE_NAME . 'rule';
        $table = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->addIndex(
                $installer->getIdxName($tableName, ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    $tableName,
                    'rule_id',
                    $ruleTableName,
                    'rule_id'
                ),
                'rule_id',
                $installer->getTable($ruleTableName),
                'rule_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    $tableName,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Product Label Rules To Store Relations');

        $installer->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function createProductlabelCustomerGroupsTable($installer)
    {
        $tableName = self::PREFIX_TABLE_NAME . 'customer_groups';
        $ruleTableName = self::PREFIX_TABLE_NAME . 'rule';
        $table = $installer->getConnection()
            ->newTable($installer->getTable($tableName))
            ->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Rule Id'
            )
            ->addColumn(
                'customer_group_id',
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Customer Group Id'
            )
            ->addIndex(
                $installer->getIdxName($tableName, ['customer_group_id']),
                ['customer_group_id']
            )
            ->addForeignKey(
                $installer->getFkName($tableName, 'rule_id', $ruleTableName, 'rule_id'),
                'rule_id',
                $installer->getTable($ruleTableName),
                'rule_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    $tableName,
                    'customer_group_id',
                    'customer_group',
                    'customer_group_id'
                ),
                'customer_group_id',
                $installer->getTable('customer_group'),
                'customer_group_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Product Label Rules To Customer Groups Relations');

        $installer->getConnection()->createTable($table);
    }
}
