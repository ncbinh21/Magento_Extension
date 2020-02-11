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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mst_credit_earning_rule')
            )
                ->addColumn(
                    'earning_rule_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                    'Earning Rule Id')
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['unsigned' => false, 'nullable' => false],
                    'Name')
                ->addColumn(
                    'description',
                    Table::TYPE_TEXT,
                    '64K',
                    ['unsigned' => false, 'nullable' => true],
                    'Description')
                ->addColumn(
                    'is_active',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => true],
                    'Is Active')
                ->addColumn(
                    'active_from',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['unsigned' => false, 'nullable' => true],
                    'Active From')
                ->addColumn(
                    'active_to',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['unsigned' => false, 'nullable' => true],
                    'Active To')
                ->addColumn(
                    'type',
                    Table::TYPE_TEXT,
                    255,
                    ['unsigned' => false, 'nullable' => false],
                    'Type')
                ->addColumn(
                    'conditions_serialized',
                    Table::TYPE_TEXT,
                    '64K',
                    ['unsigned' => false, 'nullable' => true],
                    'Conditions Serialized')
                ->addColumn(
                    'actions_serialized',
                    Table::TYPE_TEXT,
                    '64K',
                    ['unsigned' => false, 'nullable' => true],
                    'Actions Serialized')
                ->addColumn(
                    'earning_type',
                    Table::TYPE_TEXT,
                    255,
                    ['unsigned' => false, 'nullable' => false],
                    'Earning Type')
                ->addColumn(
                    'earning_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['unsigned' => false, 'nullable' => true],
                    'Earn Amount')
                ->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => true],
                    'Sort Order')
                ->addColumn(
                    'is_stop_processing',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false, 'default' => 0],
                    'Is Stop Processing')
                ->addColumn(
                    'message',
                    Table::TYPE_TEXT,
                    '64K',
                    ['unsigned' => false, 'nullable' => true],
                    'Message')
                ->addColumn(
                    'store_ids',
                    Table::TYPE_TEXT,
                    255,
                    ['unsigned' => false, 'nullable' => false],
                    'Store Ids')
                ->addColumn(
                    'group_ids',
                    Table::TYPE_TEXT,
                    255,
                    ['unsigned' => false, 'nullable' => false],
                    'Customer Group Ids');
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            include_once 'Upgrade_1_0_2.php';
            Upgrade_1_0_2::upgrade($installer, $context);
        }

        $installer->endSetup();
    }
}
