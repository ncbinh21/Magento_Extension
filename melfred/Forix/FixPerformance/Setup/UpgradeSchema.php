<?php

namespace Forix\FixPerformance\Setup;

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
        if (version_compare($context->getVersion(), '0.1.1', '<')) {
            $installer->getConnection()->addIndex($installer->getConnection()->getTableName('cron_schedule'),
                'cron_schedule_status_index', 'status');
        }
        $setup->endSetup();
    }
}