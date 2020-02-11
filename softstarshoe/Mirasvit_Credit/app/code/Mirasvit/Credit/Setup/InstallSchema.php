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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $connection = $installer->getConnection();

        $installer->startSetup();
        $table = $connection->newTable(
            $installer->getTable('mst_credit_balance')
        )->addColumn(
            'balance_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Balance Id'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Customer Id'
        )->addColumn(
            'amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['unsigned' => false, 'nullable' => true],
            'Amount'
        )->addColumn(
            'is_subscribed',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Subscribed'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Updated At'
        )->addIndex(
            $installer->getIdxName('mst_credit_balance', ['customer_id']),
            ['customer_id']
        )->addForeignKey(
            $installer->getFkName(
                'mst_credit_balance',
                'customer_id',
                'customer_entity',
                'entity_id'
            ),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_SET_NULL
        );
        $connection->createTable($table);

        $table = $connection->newTable(
            $installer->getTable('mst_credit_transaction')
        )->addColumn(
            'transaction_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Transaction Id'
        )->addColumn(
            'balance_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Balance Id'
        )->addColumn(
            'balance_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['unsigned' => false, 'nullable' => true],
            'Balance Amount'
        )->addColumn(
            'balance_delta',
            Table::TYPE_DECIMAL,
            '12,4',
            ['unsigned' => false, 'nullable' => true],
            'Balance Delta'
        )->addColumn(
            'action',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Action'
        )->addColumn(
            'message',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Message'
        )->addColumn(
            'is_notified',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Notified'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Updated At'
        )->addIndex(
            $installer->getIdxName('mst_credit_transaction', ['balance_id']),
            ['balance_id']
        )->addForeignKey(
            $installer->getFkName(
                'mst_credit_transaction',
                'balance_id',
                'mst_credit_balance',
                'balance_id'
            ),
            'balance_id',
            $installer->getTable('mst_credit_balance'),
            'balance_id',
            Table::ACTION_CASCADE
        );
        $connection->createTable($table);

        $connection->addColumn($installer->getTable('sales_creditmemo'), 'base_credit_amount', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit amount',
        ]);
        $connection->addColumn($installer->getTable('sales_creditmemo'), 'credit_amount', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit amount',
        ]);
        $connection->addColumn($installer->getTable('sales_creditmemo'), 'base_credit_total_refunded', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit total refunded',
        ]);
        $connection->addColumn($installer->getTable('sales_creditmemo'), 'credit_total_refunded', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit total refunded',
        ]);
        $connection->addColumn($installer->getTable('sales_invoice'), 'base_credit_amount', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit amount',
        ]);
        $connection->addColumn($installer->getTable('sales_invoice'), 'credit_amount', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit amount',
        ]);
        $connection->addColumn($installer->getTable('sales_order'), 'base_credit_amount', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit amount',
        ]);
        $connection->addColumn($installer->getTable('sales_order'), 'credit_amount', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit amount',
        ]);
        $connection->addColumn($installer->getTable('sales_order'), 'base_credit_invoiced', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit invoced',
        ]);
        $connection->addColumn($installer->getTable('sales_order'), 'credit_invoiced', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit invoced',
        ]);
        $connection->addColumn($installer->getTable('sales_order'), 'base_credit_refunded', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit refunded',
        ]);
        $connection->addColumn($installer->getTable('sales_order'), 'credit_refunded', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit refunded',
        ]);
        $connection->addColumn($installer->getTable('sales_order'), 'base_credit_total_refunded', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit total refunded',
        ]);
        $connection->addColumn($installer->getTable('sales_order'), 'credit_total_refunded', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit total refunded',
        ]);
        $connection->addColumn($installer->getTable('quote'), 'use_credit', [
            'type'     => Table::TYPE_SMALLINT,
            'nullable' => false,
            'length'   => 1,
            'default'  => 0,
            'comment'  => 'use credit',
        ]);
        $connection->addColumn($installer->getTable('quote'), 'base_credit_amount_used', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit amount used',
        ]);
        $connection->addColumn($installer->getTable('quote'), 'credit_amount_used', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit amount used',
        ]);
        $connection->addColumn($installer->getTable('quote_address'), 'base_credit_amount', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'base credit amount',
        ]);
        $connection->addColumn($installer->getTable('quote_address'), 'credit_amount', [
            'type'     => Table::TYPE_DECIMAL,
            'nullable' => false,
            'length'   => '12,4',
            'default'  => 0,
            'comment'  => 'credit amount',
        ]);
    }
}
