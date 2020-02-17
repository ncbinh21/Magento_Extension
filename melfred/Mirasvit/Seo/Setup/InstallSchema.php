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



namespace Mirasvit\Seo\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_seo_redirect')
        )
        ->addColumn(
            'redirect_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Redirect Id'
        )
        ->addColumn(
            'url_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            'Url From'
        )
        ->addColumn(
            'url_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            'Url To'
        )
        ->addColumn(
            'is_redirect_only_error_page',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Redirect Only Error Page'
        )
        ->addColumn(
            'comments',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Comments'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_seo_redirect_store')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Id'
        )
        ->addColumn(
            'redirect_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Redirect Id'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_seo_redirect_store', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_seo_redirect_store', ['redirect_id']),
                ['redirect_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_seo_redirect_store',
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
                    'mst_seo_redirect_store',
                    'redirect_id',
                    'mst_seo_redirect',
                    'redirect_id'
                ),
                'redirect_id',
                $installer->getTable('mst_seo_redirect'),
                'redirect_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_seo_rewrite')
        )
        ->addColumn(
            'rewrite_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Rewrite Id'
        )
        ->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Url'
        )
        ->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Title'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Description'
        )
        ->addColumn(
            'meta_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Meta Title'
        )
        ->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Meta Keywords'
        )
        ->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Meta Description'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_seo_rewrite_store')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Id'
        )
        ->addColumn(
            'rewrite_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Rewrite Id'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_seo_rewrite_store', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_seo_rewrite_store', ['rewrite_id']),
                ['rewrite_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_seo_rewrite_store',
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
                    'mst_seo_rewrite_store',
                    'rewrite_id',
                    'mst_seo_rewrite',
                    'rewrite_id'
                ),
                'rewrite_id',
                $installer->getTable('mst_seo_rewrite'),
                'rewrite_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_seo_template')
        )
        ->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Template Id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true],
            'Name'
        )
        ->addColumn(
            'meta_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Meta Title'
        )
        ->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Meta Keywords'
        )
        ->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Meta Description'
        )
        ->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Title'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Description'
        )
        ->addColumn(
            'short_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Short Description'
        )
        ->addColumn(
            'full_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Full Description'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Active'
        )
        ->addColumn(
            'rule_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rule Type'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Sort Order'
        )
        ->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            'Conditions Serialized'
        )
        ->addColumn(
            'actions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => false],
            'Actions Serialized'
        )
        ->addColumn(
            'stop_rules_processing',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Stop Rules Processing'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('mst_seo_template_store')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Id'
        )
        ->addColumn(
            'template_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Template Id'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )
            ->addIndex(
                $installer->getIdxName('mst_seo_template_store', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName('mst_seo_template_store', ['template_id']),
                ['template_id']
            )
            ->addForeignKey(
                $installer->getFkName(
                    'mst_seo_template_store',
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
                    'mst_seo_template_store',
                    'template_id',
                    'mst_seo_template',
                    'template_id'
                ),
                'template_id',
                $installer->getTable('mst_seo_template'),
                'template_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        $installer->getConnection()->createTable($table);
    }
}
