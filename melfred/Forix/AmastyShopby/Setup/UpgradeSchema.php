<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Forix\AmastyShopby\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const VISIBLE_EVERYWHERE = 'visible_everywhere';

    /**
     * @var \Amasty\ShopbyBase\Helper\Data
     */
    private $helper;

    /**
     * UpgradeSchema constructor.
     * @param \Amasty\ShopbyBase\Helper\Data $helper
     */
    public function __construct(\Amasty\ShopbyBase\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $version = $context->getVersion();
        if ($this->helper->isShopbyInstalled() && version_compare($version, '1.0.1', '<')) {
            $this->addModeCheckbox($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addModeCheckbox(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amshopby_filter_setting'),
            'mode_checkbox_cdp',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
	            'nullable' => false,
	            'default' => false,
	            'comment' => 'checkbox for cdp'
            ]
        );
    }


}
