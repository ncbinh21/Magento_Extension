<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Forix\ShopbyBrand\Setup;

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
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
			$this->addColumnRig($setup);
        }
        if (version_compare($context->getVersion(),'1.0.2','<')) {
            $this->addColumnManuFacture($setup);
        }

        $setup->endSetup();
    }

    private function addColumnRig(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amshopby_option_setting'),
            'rig_description',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'size' => 10000,
                'default' => '',
                'comment' => 'Description for rig-model'
            ]
        );
    }

    private  function addColumnManuFacture(SchemaSetupInterface $setup) {
        $setup->getConnection()->addColumn(
            $setup->getTable('amasty_amshopby_option_setting'),
            'rig_manufacture',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 11,
                'nullable' => true,
                'comment'  => 'ID of OEM'
            ]
        );
    }
}
