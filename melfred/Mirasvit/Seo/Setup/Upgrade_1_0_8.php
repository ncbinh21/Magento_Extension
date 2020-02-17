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

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Magento\Framework\DB\Ddl\Table;
use Mirasvit\Seo\Api\Data\CanonicalRewriteStoreInterface;

class Upgrade_1_0_8
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_rewrite'),
                    'description_position',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length' => 5,
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '1',
                        'comment' => 'SEO description position',
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_rewrite'),
                    'description_template',
                    [
                        'type' =>  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '64K',
                        'unsigned' => false,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'Template for adding SEO description',
                    ]
                );

            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_rewrite'),
                    'sort_order',
                    [
                        'type' =>  \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => false,
                        'nullable' => false,
                        'default' => 10,
                        'comment' => 'Sort order',
                    ]
                );
    }
}