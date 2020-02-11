<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Yosto\InstagramConnect\Helper\Constant;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 * @package Yosto\InstagramConnect\Setup
 */
class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0') < 0) {
            if ($setup
                    ->getConnection()
                    ->isTableExists(Constant::INSTAGRAM_TEMPLATE_TABLE) != true
            ) {
                $table = $setup->getConnection()
                    ->newTable($setup->getTable(Constant::INSTAGRAM_TEMPLATE_TABLE))
                    ->addColumn(
                        Constant::INSTAGRAM_TEMPLATE_ID,
                        Table::TYPE_INTEGER,
                        null,
                        [
                            Constant::IS_IDENTITY => true,
                            Constant::IS_UNSIGNED => true,
                            Constant::IS_NULLABLE => false,
                            Constant::IS_PRIMARY => true
                        ],
                        Constant::INSTAGRAM_TEMPLATE_TABLE_ID_COMMENT
                    )
                    ->addColumn(
                        Constant::INSTAGRAM_TEMPLATE_TABLE_TITLE,
                        Table::TYPE_TEXT,
                        255,
                        [
                            Constant::IS_NULLABLE => false,
                            Constant::DEFAULT_PROPERTY => ''
                        ],
                        Constant::INSTAGRAM_TEMPLATE_TABLE_TITLE_COMMENT
                    )
                    ->addColumn(
                        Constant::INSTAGRAM_TEMPLATE_TABLE_DESCRIPTION,
                        Table::TYPE_TEXT,
                        255,
                        [
                            Constant::IS_NULLABLE => true,
                            Constant::DEFAULT_PROPERTY => ''
                        ],
                        Constant::INSTAGRAM_TEMPLATE_TABLE_DESCRIPTION_COMMENT
                    )
                    ->addColumn(
                        Constant::INSTAGRAM_TEMPLATE_TABLE_STATUS,
                        Table::TYPE_BOOLEAN,
                        null,
                        [
                            Constant::IS_NULLABLE => false
                        ],
                        Constant::INSTAGRAM_TEMPLATE_TABLE_STATUS_COMMENT
                    )
                    ->addColumn(
                        Constant::INSTAGRAM_TEMPLATE_TABLE_PC_CODE,
                        Table::TYPE_TEXT,
                        '64k',
                        [
                            Constant::IS_NULLABLE => true,
                            Constant::DEFAULT_PROPERTY => ''
                        ],
                        Constant::INSTAGRAM_TEMPLATE_TABLE_PC_CODE_COMMENT
                    )
                    ->addColumn(
                        Constant::INSTAGRAM_TEMPLATE_TABLE_TABLET_CODE,
                        Table::TYPE_TEXT,
                        '64k',
                        [
                            Constant::IS_NULLABLE => true,
                            Constant::DEFAULT_PROPERTY => ''
                        ],
                        Constant::INSTAGRAM_TEMPLATE_TABLE_TABLET_CODE_COMMENT
                    )
                    ->addColumn(
                        Constant::INSTAGRAM_TEMPLATE_TABLE_MOBILE_CODE,
                        Table::TYPE_TEXT,
                        '64k',
                        [
                            Constant::IS_NULLABLE => true,
                            Constant::DEFAULT_PROPERTY => ''
                        ],
                        Constant::INSTAGRAM_TEMPLATE_TABLE_MOBILE_CODE_COMMENT
                    )
                    ->setComment(Constant::INSTAGRAM_TEMPLATE_TABLE_COMMENT)
                    ->setOption(Constant::DB_TYPE, Constant::INNO_DB)
                    ->setOption(Constant::CHARSET, Constant::UTF8);
                $setup->getConnection()->createTable($table);
            }
        }

        $setup->endSetup();
    }
}