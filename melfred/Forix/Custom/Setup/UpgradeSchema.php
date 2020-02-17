<?php

namespace Forix\Custom\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'contact_area',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact Area'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'contact',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 1'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'district',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 1 District'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'district',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 1 District'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'contact_two',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 2'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'district_two',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 2 District'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'phone_two',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact Phone 2'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'email_two',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact Email 2'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'toll_free_phone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Toll-Free Phone'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'office_phone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Office Phone'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'fax',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Fax'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'sales_zip_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Sales Zip Codes'
                ]
            );
        } // end setup 2.0.1

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_amlocator_location'),
                'district',
                'contact_district',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 1 District'
                ]
            );

            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_amlocator_location'),
                'district_two',
                'contact_district_two',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 2 District'
                ]
            );

            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_amlocator_location'),
                'phone_two',
                'contact_phone_two',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 2 Phone'
                ]
            );

            $installer->getConnection()->changeColumn(
                $installer->getTable('amasty_amlocator_location'),
                'email_two',
                'contact_email_two',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact 2 Email'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('amasty_amlocator_location'),
                'contact_phone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'Contact Phone 2'
                ]
            );
        } // end setup 2.0.2

	    if (version_compare($context->getVersion(), '2.0.3', '<')) {
		    $installer->getConnection()->changeColumn(
			    $installer->getTable('amasty_amlocator_location'),
			    'schedule',
			    'schedule',
			    [
				    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
				    'length'   => '1M',
				    'nullable' => false,
				    'comment'  => 'Contact 1 District'
			    ]
		    );
	    }


        $setup->endSetup();
    }
}