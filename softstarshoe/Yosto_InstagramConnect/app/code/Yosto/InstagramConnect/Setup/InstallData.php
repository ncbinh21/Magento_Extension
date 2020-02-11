<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Yosto\InstagramConnect\Helper\Constant;

/**
 * Class InstallData
 * @package Yosto\InstagramConnect\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            Constant::INSTAGRAM_HASH_TAG,
            [
                'type' => 'varchar',
                'frontend_class' => 'validate-alphanum'
                    .' validate-length maximum-length-50',
                'note' => 'Please input only letters'
                    . ' (a-z or A-Z) or numbers (0-9)'
                    . ' without any space and other characters.'
                    .' The maximum length is 50.',
                'label' => 'Instagram Hash Tag',
                'input' => 'text',
                'required' => false,
                'visible_on_front' => true,
                'apply_to' =>
                    'simple,configurable,virtual,bundle,downloadable',
                'unique' => false,
                'group' => 'Instagram Connect',
                'default' => '',
                'position' => 7,
                'sort_order' => 7,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true
            ]
        );
    }
}