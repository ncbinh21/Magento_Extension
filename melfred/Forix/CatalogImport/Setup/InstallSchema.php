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
 * @version   1.0.50
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Forix\CatalogImport\Setup;

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
            $installer->getTable('forix_import_product_relations')
        )
            ->addColumn(
                'relation_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Relation Id'
            )
            ->addColumn(
                'relation_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64',
                ['unsigned' => false, 'nullable' => false],
                'Relation Type'
            )
            ->addColumn(
                'sku_parent',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64',
                ['unsigned' => false, 'nullable' => false],
                'Parent SKU'
            )
            ->addColumn(
                'sku',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64',
                ['unsigned' => false, 'nullable' => true],
                'SKU'
            )
            ->addColumn(
                'attribute_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['unsigned' => false, 'nullable' => true],
                'Comments'
            )
            ->addColumn(
                'attribute_value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['unsigned' => false, 'nullable' => true],
                'Comments'
            )
            ->addColumn(
                'dependency',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64K',
                ['unsigned' => false, 'nullable' => true],
                'Dependency'
            )
            ->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('forix_import_product_relations', ['relation_type']),
                ['relation_type']
            )
            ->addIndex(
                $setup->getIdxName('forix_import_product_relations', ['sku_parent']),
                ['sku_parent']
            )
            ->addIndex(
                $setup->getIdxName('forix_import_product_relations', ['sku']),
                ['sku']
            );
        $installer->getConnection()->createTable($table);
    }
}
