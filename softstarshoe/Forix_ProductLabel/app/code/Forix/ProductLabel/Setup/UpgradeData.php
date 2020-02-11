<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var array
     */
    protected $_attributeSelect = [
        'forix_label_ids' => 'Badge'
    ];

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->upgradeDataVer101();
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->upgradeDataVer102();
        }

        $installer->endSetup();
    }

    /**
     * Upgrade Data to ver 1.0.1
     */
    private function upgradeDataVer101()
    {
        $eavSetup = $this->eavSetupFactory->create();
        foreach ($this->_attributeSelect as $attributeCode => $attributeLabel) {
            $eavSetup->removeAttribute(Product::ENTITY,$attributeCode);
            $eavSetup->addAttribute(
                Product::ENTITY,
                $attributeCode,
                [
                    'type' => 'varchar',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'frontend' => '',
                    'label' => $attributeLabel,
                    'input' => 'multiselect',
                    'class' => '',
                    'source' => 'Forix\ProductLabel\Model\Source\Config\Badge',
                    'global' => Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'is_used_in_grid' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'group' => 'General',
                    'wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => true
                ]
            );
        }
    }

    /**
     * Upgrade Data to ver 1.0.2
     */
    private function upgradeDataVer102()
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(Product::ENTITY,'forix_editable_badge');
        $eavSetup->addAttribute(
            Product::ENTITY,
            'forix_editable_badge',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Editable Badge',
                'input' => 'boolean',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'group' => 'General',
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );
    }

}
