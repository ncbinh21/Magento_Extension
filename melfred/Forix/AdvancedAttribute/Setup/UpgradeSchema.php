<?php

namespace Forix\AdvancedAttribute\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();


        if (version_compare($context->getVersion(), '2.0.4') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_attribute_option_banner'),
                'is_active',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment' => 'Is Active'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.0.7') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_attribute_option_banner'),
                'url_key',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Url Key'
                ]
            );
            $installer->getConnection()->query('ALTER TABLE `forix_attribute_option_banner` ADD UNIQUE(`url_key`)');
        }

        if (version_compare($context->getVersion(), '2.0.9') < 0) {
			$installer->getConnection()->addColumn(
				$installer->getTable('forix_attribute_option_banner'),
				'name',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'nullable' => true,
					'comment' => 'Name display'
				]
			);
		}

		if (version_compare($context->getVersion(), '2.1.0') < 0) {
			$installer->getConnection()->addColumn(
				$installer->getTable('forix_attribute_option_banner'),
				'mb_oem',
				[
					'type'   => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'length' => 11,
					'nullable' => true,
					'comment'  => 'ID of manufacture'
				]
			);

			$installer->getConnection()->addColumn(
				$installer->getTable('forix_attribute_option_banner'),
				'link_rig_model',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'nullable' => true,
					'comment' => 'Link of rig model'
				]
			);

		}

		if (version_compare($context->getVersion(), '2.1.1') < 0) {
        	$installer->getConnection()->query('ALTER TABLE forix_attribute_option_banner DROP COLUMN link_rig_model');
		}


        if (version_compare($context->getVersion(), '2.1.2') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_attribute_option_banner'),
                'icon_page',
                [
                    'type'   => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment'  => 'GroundCondition Icon Page'
                ]
            );
        }

		if (version_compare($context->getVersion(), '2.1.3') < 0) {
			$installer->getConnection()->addColumn(
				$installer->getTable('forix_attribute_option_banner'),
				'mb_manufacturer_color',
				[
					'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length'   => 15,
					'nullable' => true,
					'comment'  => 'Color border'
				]
			);
		}

        if (version_compare($context->getVersion(), '2.2.3') < 0) {
            $sql = <<<SQL
update catalog_eav_attribute cea
    join  eav_attribute ea on (ea.attribute_id = cea.attribute_id and ea.frontend_input = 'select' and
    ea.attribute_code like 'mb_%')set cea.apply_to = 'simple,configurable,virtual,bundle'
    where cea.apply_to = 'simple,configurable,bundle';
SQL;
            $installer->getConnection()->query($sql);
        }


        if (version_compare($context->getVersion(), '2.3.3') < 0) {
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_attribute_option_banner'),
                'mb_oem',
                [
                    'type'   => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'comment'  => 'ID of OEM'
                ]
            );
        }
        $installer->endSetup();
    }
}