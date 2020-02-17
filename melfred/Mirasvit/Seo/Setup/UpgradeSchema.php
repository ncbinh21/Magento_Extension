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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('cms_page'),
                    'alternate_group',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'Alternate group',
                    ]
                );
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_template'),
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
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_template'),
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
        }

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_template'),
                    'apply_for_child_categories',
                    [
                        'type' =>  \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'length' => '5',
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                        'comment' => 'Apply for child categories',
                    ]
                );
        }

        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            include_once 'Upgrade_1_0_5.php';

            Upgrade_1_0_5::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.6') < 0) {
            include_once 'Upgrade_1_0_6.php';

            Upgrade_1_0_6::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.7') < 0) {
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable('mst_seo_redirect'),
                    'redirect_type',
                    [
                        'type' =>  \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => 301,
                        'comment' => 'Redirect type',
                    ]
                );
        }

        if (version_compare($context->getVersion(), '1.0.8') < 0) {
            include_once 'Upgrade_1_0_8.php';

            Upgrade_1_0_8::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.9') < 0) {
            include_once 'Upgrade_1_0_9.php';

            Upgrade_1_0_9::upgrade($installer, $context);
        }

        $installer->endSetup();
    }
}
