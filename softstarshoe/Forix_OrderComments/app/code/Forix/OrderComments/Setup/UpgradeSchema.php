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

	    $installer->endSetup();
    }
}
