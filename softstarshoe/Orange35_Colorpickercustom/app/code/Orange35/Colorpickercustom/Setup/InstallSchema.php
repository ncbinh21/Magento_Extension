<?php
namespace Orange35\Colorpickercustom\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $adapter = $installer->getConnection();
        $option = $installer->getTable('catalog_product_option');
        $value = $installer->getTable('catalog_product_option_type_value');

        $adapter->addColumn(
            $option,
            'is_colorpicker',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'If option is colorpicker'
            ]
        );
        $adapter->addColumn(
            $option,
            'swatch_width',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 30,
                'comment' => 'Swatch width'
            ]
        );
        $adapter->addColumn(
            $option,
            'swatch_height',
            [
                'type' => Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 20,
                'comment' => 'Swatch height'
            ]
        );
        $adapter->addColumn(
            $option,
            'tooltip',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 2,
                'comment' => 'Tooltip type'
            ]
        );

        $adapter->addColumn(
            $value,
            'color',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'default' => null,
                'comment' => 'Color'
            ]
        );
        $adapter->addColumn(
            $value,
            'image',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'default' => null,
                'comment' => 'Image'
            ]
        );

        $installer->endSetup();
    }

}