<?php

namespace Forix\Quote\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable('sales_order_item'),
				'rig_id',
				[
					'type'=>Table::TYPE_INTEGER,
					'unsigned' => true,
					'nullable' => true,
					'comment'  => 'Attribute rig model id'
				]
			);
		}

		if (version_compare($context->getVersion(), '1.0.2', '<')) {
			$setup->getConnection()->changeColumn(
				$setup->getTable('sales_order_item'),
				'rig_id',
				'rig_model',
				[
					'type'    =>  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length'  =>  50,
					'comment' => 'Value Rig Model'
				]
			);
		}


		$setup->endSetup();
	}


}
