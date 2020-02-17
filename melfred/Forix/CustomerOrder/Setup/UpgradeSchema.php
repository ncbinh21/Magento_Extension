<?php

namespace Forix\CustomerOrder\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Forix\CustomerOrder\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $tableName = $setup->getTable('company');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'distributor_zipcode' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Distributor Zipcode List',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
        $setup->endSetup();
    }
}