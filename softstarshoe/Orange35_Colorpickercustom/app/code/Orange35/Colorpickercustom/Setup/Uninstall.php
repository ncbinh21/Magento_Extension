<?php
namespace Orange35\Colorpickercustom\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * @codeCoverageIgnore
 */
class Uninstall implements UninstallInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $adapter = $installer->getConnection();
        $option = $installer->getTable('catalog_product_option');
        $value = $installer->getTable('catalog_product_option_type_value');
        $adapter->dropColumn($option, 'is_colorpicker');
        $adapter->dropColumn($value, 'image');
        $adapter->dropColumn($value, 'color');
        $adapter->dropColumn($value, 'swatch_width');
        $adapter->dropColumn($value, 'swatch_height');

        $installer->endSetup();
    }

}