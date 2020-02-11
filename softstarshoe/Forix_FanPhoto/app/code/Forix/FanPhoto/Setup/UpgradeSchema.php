<?php


namespace Forix\FanPhoto\Setup;

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
	    $tableItems = $installer->getTable('forix_fanphoto_photo');

	    if (version_compare($context->getVersion(), '1.0.1', '<')) {
		    $installer->getConnection()->modifyColumn(
			    $tableItems,
			    'is_active',
			    [
				    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
				    'unsigned' => true,
				    'nullable' => false,
				    'default' => '0',
				    'comment' => 'Is Active',
			    ]
		    );
	    }

	    $installer->endSetup();
    }
}
