<?php


namespace Forix\OrderComments\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
	    $installer = $setup;
	    $installer->startSetup();
	    $tableItems = $installer->getTable('sales_order_status_history');

	    if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $installer->getConnection()->addColumn(
                $tableItems,
                'is_order_note',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Is Order Note'
                ]
            );
	    } // end upgrade version 2.0.1
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $installer->getConnection()->addColumn(
                $tableItems,
                'is_po_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Is Po Number'
                ]
            );
        } // end upgrade version 2.0.2

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $installer->getConnection()->addColumn(
                'sales_order',
                'order_note',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Order Notes'
                ]
            );
            $installer->getConnection()->addColumn(
                'sales_order',
                'po_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'PO Number'
                ]
            );
            $installer->getConnection()->addColumn(
                'sales_order_grid',
                'order_note',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Order Notes'
                ]
            );
            $installer->getConnection()->addColumn(
                'sales_order_grid',
                'po_number',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'PO Number'
                ]
            );
        } // end upgrade version 2.0.3
	    $installer->endSetup();
    }
}
