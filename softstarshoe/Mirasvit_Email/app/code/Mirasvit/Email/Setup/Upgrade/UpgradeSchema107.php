<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Setup\Upgrade;


use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mirasvit\Email\Api\Data\TriggerInterface;

class UpgradeSchema107 implements UpgradeSchemaInterface, VersionableInterface
{
    const VERSION = '1.0.7';

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * {@inheritDoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        return;
        $connection = $setup->getConnection();

        $this->upgradeTriggerTable($setup, $connection);
        $this->upgradeRuleTable($connection);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param AdapterInterface     $connection
     */
    private function upgradeTriggerTable(SchemaSetupInterface $setup, AdapterInterface $connection)
    {
        $connection->dropColumn($setup->getTable(TriggerInterface::TABLE_NAME), 'run_rule_id');
        $connection->dropColumn($setup->getTable(TriggerInterface::TABLE_NAME), 'stop_rule_id');
    }

    /**
     * @param AdapterInterface     $connection
     */
    private function upgradeRuleTable(AdapterInterface $connection)
    {
        $connection->dropTable('mst_email_rule');
    }
}
