<?php
/**
 * Copyright (c) 2016.
 * Created by Hidro Le.
 * User: Hidro
 */
namespace Forix\Product\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeSchema implements UpgradeSchemaInterface {

    protected $_categorySetupFactory;
    protected $_eavSetupFactory;
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CategorySetupFactory $categorySetupFactory
    )
    {
        $this->_categorySetupFactory = $categorySetupFactory;
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $setup->getConnection()->addColumn($setup->getTable('catalog_product_super_link'),
                'recommend_sku',
                [
                    'type' => Table::TYPE_INTEGER,
                    'comment' => 'Recommend For Configurable Options',
                    'default' => '0'
                ]
            );
        }
        $installer->endSetup();
    }
}





