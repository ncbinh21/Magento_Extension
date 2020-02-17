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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoAutolink\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     *
     * {@inheritdoc}
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_seoautolink_link')
        )
        ->addColumn(
            'link_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Link Id'
        )
        ->addColumn(
            'keyword',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Keyword'
        )
        ->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            'Url'
        )
        ->addColumn(
            'url_target',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true],
            'Url Target'
        )
        ->addColumn(
            'url_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true],
            'Url Title'
        )
        ->addColumn(
            'is_nofollow',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Nofollow'
        )
        ->addColumn(
            'max_replacements',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Max Replacements'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )
        ->addColumn(
            'occurence',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Occurence'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Active'
        )
        ->addColumn(
            'active_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active From'
        )
        ->addColumn(
            'active_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Active To'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Created At'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Updated At'
        )
            ->addIndex(
                $installer->getIdxName('mst_seoautolink_link', ['keyword']),
                ['keyword']
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_seoautolink_link_store')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Id'
        )
        ->addColumn(
            'link_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Link Id'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_seoautolink_link_store', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_seoautolink_link_store', ['link_id']),
                ['link_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_seoautolink_link_store',
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_seoautolink_link_store',
                    'link_id',
                    'mst_seoautolink_link',
                    'link_id'
                ),
                'link_id',
                $installer->getTable('mst_seoautolink_link'),
                'link_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }
}
