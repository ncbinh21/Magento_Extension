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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Upgrade_1_0_5
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function upgrade(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->getConnection()->addIndex(
            $installer->getTable('mst_seo_rewrite'),
            'Seo rewrite fulltext index',
            ['url'],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );

        $installer->getConnection()->addIndex(
            $installer->getTable('mst_seo_redirect'),
            'Seo redirect Request URL fulltext index',
            ['url_from'],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );

        $installer->getConnection()->addIndex(
            $installer->getTable('mst_seo_redirect'),
            'Seo redirect Target URL fulltext index',
            ['url_to'],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );

        $installer->getConnection()->addIndex(
            $installer->getTable('mst_seo_template'),
            'Seo template fulltext index',
            ['name'],
            AdapterInterface::INDEX_TYPE_FULLTEXT
        );
    }
}