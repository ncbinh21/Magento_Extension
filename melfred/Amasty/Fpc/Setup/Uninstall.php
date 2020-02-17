<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $tablesToDrop = [
            'amasty_fpc_log',
            'amasty_fpc_queue_page'
        ];

        foreach ($tablesToDrop as $table) {
            $installer->getConnection()->dropTable(
                $installer->getTable($table)
            );
        }

        $installer->endSetup();
    }
}
