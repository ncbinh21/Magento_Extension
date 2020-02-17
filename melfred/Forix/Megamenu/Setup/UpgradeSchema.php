<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Forix\Megamenu\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1') < 0) {

			$installer = $setup;
			$installer->startSetup();
			$tableItems = $installer->getTable('ves_megamenu_item');

			$installer->getConnection()->addColumn(
				$tableItems,
				'attribute_code',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'nullable' => true,
					'after'     => 'category',
					'comment' => 'Attribute Code'
				]
			);
			$installer->getConnection()->addColumn(
				$tableItems,
				'attribute_value',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'nullable' => true,
					'after'     => 'attribute_code',
					'comment' => 'Attribute Value'
				]
			);
			$installer->endSetup();
        }

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
			$installer = $setup;
			$installer->startSetup();
			$tableItems = $installer->getTable('ves_megamenu_item');

			$installer->getConnection()->addColumn(
				$tableItems,
				'image_hover',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => 255,
					'nullable' => true,
					'after'   => 'category',
					'comment' => 'Image hover category'
				]
			);
			$installer->endSetup();
		}

		if (version_compare($context->getVersion(), '2.0.3') < 0) {
			$installer = $setup;
			$installer->startSetup();
			$tableItems = $installer->getTable('ves_megamenu_item');

			$installer->getConnection()->addColumn(
				$tableItems,
				'short_desc',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'length' => '64k',
					'nullable' => true,
					'after'   => 'image_hover',
					'comment' => 'short description'
				]
			);
			$installer->endSetup();
		}

    }
}
