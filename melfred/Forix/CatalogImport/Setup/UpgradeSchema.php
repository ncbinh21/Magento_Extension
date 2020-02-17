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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $mCategorySetupFactory;
    public function __construct(
        \Forix\CatalogImport\Setup\CategorySetupFactory $mCategorySetupFactory
    )
    {
        $this->mCategorySetupFactory = $mCategorySetupFactory;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            // Get module table
            $tableName = $setup->getTable('forix_import_product_relations');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'required' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => true,
                        'comment' => 'Required',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            /** @var \Forix\CatalogImport\Setup\CategorySetup $mCategorySetup */
            $mCategorySetup = $this->mCategorySetupFactory->create();
            $mCategorySetup->installEntities();
        }
        if (version_compare($context->getVersion(), '2.2.1') < 0) {
            $connection = $setup->getConnection();
            $eavModel = $connection->quote(\Forix\CatalogImport\Model\Import\Melfredborzall::class);
            $connection->query("
                update eav_entity_type set entity_model = {$eavModel} where entity_type_code = 'melfred_catalog_product';
            ");
        }
        $setup->endSetup();
    }
}
