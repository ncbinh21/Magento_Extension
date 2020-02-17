<?php
/**
 * Copyright (c) 2016.
 * Created by Hidro Le.
 * User: Hidro
 */

namespace Forix\Company\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.2','<')) {
            $installer->getConnection()->dropColumn($installer->getTable('company'), 'customer_no');
            $installer->getConnection()->addColumn(
                $installer->getTable('company'),
                'customer_no',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Customer No'
                ]
            );
        } //end setup 1.0.2

        if (version_compare($context->getVersion(), '1.0.3','<')) {
            $installer->getConnection()->dropColumn($installer->getTable('company'), 'contact_code');
            $installer->getConnection()->addColumn(
                $installer->getTable('company'),
                'contact_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Contact Code'
                ]
            );
        } //end setup 1.0.3

        if (version_compare($context->getVersion(), '1.0.4','<')) {
            $installer->getConnection()->dropColumn($installer->getTable('company'), 'terms_code');
            $installer->getConnection()->addColumn(
                $installer->getTable('company'),
                'terms_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '255',
                    'nullable' => true,
                    'comment' => 'Terms Code'
                ]
            );
        } //end setup 1.0.4
        $installer->endSetup();
    }
}





