<?php

namespace Forix\Catalog\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(),'1.0.1','<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable('eav_attribute'),
				'is_fitment',
				[
					'type'=>Table::TYPE_SMALLINT,
					'nullable' => true,
					'comment'  =>'custom field yes no'
				]
			);
		}

		$setup->endSetup();
	}
}