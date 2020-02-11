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


use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Mirasvit\Email\Api\Data\TriggerChainInterface;

class UpgradeSchema104 implements UpgradeSchemaInterface, VersionableInterface
{
    const VERSION = '1.0.4';

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
        $connection = $setup->getConnection();

        $connection->addColumn(
            $setup->getTable($setup->getTable(TriggerChainInterface::TABLE_NAME)),
            TriggerChainInterface::DAY,
            [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'comment'  => TriggerChainInterface::DAY,
                'after'    => TriggerChainInterface::DELAY
            ]
        );
        $connection->addColumn(
            $setup->getTable($setup->getTable(TriggerChainInterface::TABLE_NAME)),
            TriggerChainInterface::HOUR,
            [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'comment'  => TriggerChainInterface::HOUR,
                'after'    => TriggerChainInterface::DAY
            ]
        );
        $connection->addColumn(
            $setup->getTable($setup->getTable(TriggerChainInterface::TABLE_NAME)),
            TriggerChainInterface::MINUTE,
            [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'comment'  => TriggerChainInterface::MINUTE,
                'after'    => TriggerChainInterface::HOUR
            ]
        );
        $connection->addColumn(
            $setup->getTable($setup->getTable(TriggerChainInterface::TABLE_NAME)),
            TriggerChainInterface::SEND_FROM,
            [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'comment'  => TriggerChainInterface::SEND_FROM,
                'after'    => TriggerChainInterface::MINUTE
            ]
        );
        $connection->addColumn(
            $setup->getTable($setup->getTable(TriggerChainInterface::TABLE_NAME)),
            TriggerChainInterface::SEND_TO,
            [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'comment'  => TriggerChainInterface::SEND_TO,
                'after'    => TriggerChainInterface::SEND_FROM
            ]
        );
        $connection->addColumn(
            $setup->getTable($setup->getTable(TriggerChainInterface::TABLE_NAME)),
            TriggerChainInterface::SEND_TO,
            [
                'type'     => Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'comment'  => TriggerChainInterface::SEND_TO,
                'after'    => TriggerChainInterface::SEND_FROM
            ]
        );
        $connection->addColumn(
            $setup->getTable($setup->getTable(TriggerChainInterface::TABLE_NAME)),
            TriggerChainInterface::EXCLUDE_DAYS,
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 255,
                'nullable' => true,
                'comment'  => TriggerChainInterface::EXCLUDE_DAYS,
                'after'    => TriggerChainInterface::SEND_TO
            ]
        );
    }
}
