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
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Seo\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Magento\Framework\DB\Ddl\Table;
use Mirasvit\Seo\Api\Data\CanonicalRewriteStoreInterface;

class Upgrade_1_0_6
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();
        $tableCanonicalRewrite = $connection->newTable(
            $setup->getTable(CanonicalRewriteInterface::TABLE_NAME)
        )->addColumn(
            CanonicalRewriteInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Canonical Rewrite Id'
        )->addColumn(
            CanonicalRewriteInterface::IS_ACTIVE,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Active'
        )->addColumn(
            CanonicalRewriteInterface::CANONICAL,
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            'Canonical'
        )->addColumn(
            CanonicalRewriteInterface::REG_EXPRESSION,
            Table::TYPE_TEXT,
            1024,
            ['unsigned' => false, 'nullable' => true],
            'Regular expression'
        )->addColumn(
            CanonicalRewriteInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            'Conditions Serialized'
        )->addColumn(
            CanonicalRewriteInterface::ACTIONS_SERIALIZED,
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            'Actions Serialized'
        )->addColumn(
            CanonicalRewriteInterface::SORT_ORDER,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Sort Order'
        )->addColumn(
            CanonicalRewriteInterface::COMMENTS,
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Comments'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable(CanonicalRewriteInterface::TABLE_NAME),
                [CanonicalRewriteInterface::CANONICAL],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            [CanonicalRewriteInterface::CANONICAL],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        );
        $connection->createTable($tableCanonicalRewrite);

        $tableCanonicalRewriteStore = $connection->newTable(
            $setup->getTable(CanonicalRewriteStoreInterface::TABLE_NAME)
        )->addColumn(
            CanonicalRewriteStoreInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Id'
        )->addColumn(
            CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Canonical Rewrite Id'
        )->addColumn(
            CanonicalRewriteStoreInterface::STORE_ID,
            Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addIndex(
            $setup->getIdxName(CanonicalRewriteStoreInterface::TABLE_NAME,
                [CanonicalRewriteStoreInterface::STORE_ID]),
            [CanonicalRewriteStoreInterface::STORE_ID]
        )->addIndex(
            $setup->getIdxName(CanonicalRewriteStoreInterface::TABLE_NAME,
                [CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID]),
            [CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID]
        )->addForeignKey(
            $setup->getFkName(
                CanonicalRewriteStoreInterface::TABLE_NAME,
                CanonicalRewriteStoreInterface::STORE_ID,
                'store',
                CanonicalRewriteStoreInterface::STORE_ID
            ),
            CanonicalRewriteStoreInterface::STORE_ID,
            $setup->getTable('store'),
            CanonicalRewriteStoreInterface::STORE_ID,
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                CanonicalRewriteStoreInterface::TABLE_NAME,
                CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID,
                CanonicalRewriteInterface::TABLE_NAME,
                CanonicalRewriteInterface::ID
            ),
            CanonicalRewriteStoreInterface::CANONICAL_REWRITE_ID,
            $setup->getTable(CanonicalRewriteInterface::TABLE_NAME),
            CanonicalRewriteInterface::ID,
            Table::ACTION_CASCADE
        );
        $connection->createTable($tableCanonicalRewriteStore);
    }
}