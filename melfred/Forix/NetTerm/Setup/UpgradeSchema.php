<?php

namespace Forix\NetTerm\Setup;

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
        if (version_compare($context->getVersion(), '2.0.1', '<')) { // start setup 2.0.1
            $installer->getConnection()->changeColumn(
                $installer->getTable('forix_netterm_netterm'),
                'address',
                'location_since',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Present Location Since'
                ]
            );

            $installer->getConnection()->changeColumn(
                $installer->getTable('forix_netterm_netterm'),
                'date',
                'date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                ]
            );
        } // end setup 2.0.1

        if (version_compare($context->getVersion(), '2.0.2', '<')) { // start setup 2.0.2
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_netterm_netterm'),
                'is_active',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment' => 'Enable Account Netterm'
                )
            );
        } // end setup 2.0.2

        if (version_compare($context->getVersion(), '2.0.3', '<')) { // start setup 2.0.3
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_netterm_netterm'),
                'max_price',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Max Price'
                )
            );
        } // end setup 2.0.3

        if (version_compare($context->getVersion(), '2.0.4', '<')) { // start setup 2.0.4
            $installer->getConnection()->addColumn(
                $installer->getTable('forix_netterm_netterm'),
                'company_name',
                array(
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Company Name'
                )
            );
        } // end setup 2.0.4
        $setup->endSetup();
    }
}