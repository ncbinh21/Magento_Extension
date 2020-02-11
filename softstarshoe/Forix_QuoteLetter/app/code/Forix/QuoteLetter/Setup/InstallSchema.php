<?php
/**
 * Hidro Forix Webdesign. 
 * Copyright (C) 2017  Hidro Le
 * 
 * This file included in Forix/QuoteLetter is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Forix\QuoteLetter\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_forix_quoteletter_quoteletter = $setup->getConnection()->newTable($setup->getTable('forix_quoteletter'));

        
        $table_forix_quoteletter_quoteletter->addColumn(
            'quoteletter_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );



        $table_forix_quoteletter_quoteletter->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false]
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false]
        );


        $table_forix_quoteletter_quoteletter->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => False],
            'Customer Name'
        );
        

        
        $table_forix_quoteletter_quoteletter->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Customer Position'
        );
        

        
        $table_forix_quoteletter_quoteletter->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => False],
            'Customer letter content'
        );
        

        
        $table_forix_quoteletter_quoteletter->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'address'
        );


        $table_forix_quoteletter_quoteletter->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['default' => 1],
            'Is active status.'
        );


        $table_forix_quoteletter_quoteletter->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '0'],
            'Store Id'
        );

        $table_forix_quoteletter_quoteletter->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '0'],
            'Customer Position'
        );


        $setup->getConnection()->createTable($table_forix_quoteletter_quoteletter);


        $forix_quoteletter_product = $setup->getConnection()->newTable($setup->getTable('forix_quoteletter_product'));
        $forix_quoteletter_product->addColumn(
            'quoteletter_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'unsigned' => true,),
            'Entity ID'
        );
        $forix_quoteletter_product->addColumn(
            'product_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'quote letter product sku'
        );

        $forix_quoteletter_product->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '0'],
            'Store Id'
        );

        $setup->getConnection()->createTable($forix_quoteletter_product);



        $forix_quoteletter_category = $setup->getConnection()->newTable($setup->getTable('forix_quoteletter_category'));
        $forix_quoteletter_category->addColumn(
            'quoteletter_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'unsigned' => true,),
            'Entity ID'
        );



        $forix_quoteletter_category->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'quote letter category id'
        );

        $forix_quoteletter_category->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['default' => '0'],
            'Store Id'
        );
        $setup->getConnection()->createTable($forix_quoteletter_category);
        $setup->endSetup();
    }
}
