<?php
namespace Orange35\Colorpickercustom\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $adapter = $setup->getConnection();
            $adapter->addColumn(
                $setup->getTable('catalog_product_option'),
                'tooltip',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'default' => 2,
                    'comment' => 'Tooltip type'
                ]
            );
        }

        $setup->endSetup();
    }

}