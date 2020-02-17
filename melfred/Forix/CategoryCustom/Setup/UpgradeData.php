<?php

namespace Forix\CategoryCustom\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 * @package Forix\CategoryCustom\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.1') < 0) {

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'category_ads_block');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_ads_block',
                [
                    'type' => 'text',
                    'label' => 'Category Ads Block',
                    'input' => 'textarea',
                    'required' => false,
                    'visible' => true,
                    'user_defined' => true,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Display Settings',
                ]
            );
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.2') < 0) {
			$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
			$eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'short_desc');
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Category::ENTITY,
				'short_desc',
				[
					'type'  => 'text',
					'label' => 'Short Description',
					'input' => 'textarea',
					'required' => false,
					'visible' => true,
					'user_defined' => true,
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
					'wysiwyg_enabled' => true,
					'is_html_allowed_on_front' => true,
					'group' => 'Display Settings',
				]
			);
		}

		if ($context->getVersion() && version_compare($context->getVersion(), '1.0.3') < 0)
		{
			$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
			$eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'icon_image');
			$eavSetup->addAttribute(
				\Magento\Catalog\Model\Category::ENTITY,
				'icon_image',
				[
					'type'  => 'varchar',
					'label' => 'Icon Image',
					'input' => 'image',
					'backend'  => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
					'required' => false,
					'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
					'group' => 'Display Settings',
					'is_used_in_grid' => false,
					'is_visible_in_grid' => false,
					'is_filterable_in_grid' => false
				]
			);
		}

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.4') < 0)
        {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'is_bit_reamer');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'is_bit_reamer',
                [
                    'type' => 'varchar',
                    'label' => 'Is Bits/Reamers',
                    'input' => 'select',
                    'source' => \Forix\CategoryCustom\Model\Category\BitReamer::class,
                    'required' => false,
                    'sort_order' => 100,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Display Settings',
                ]
            );
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.5') < 0)
        {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'forix_product_list_template');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'forix_product_list_template',
                [
                    'type' => 'varchar',
                    'label' => 'Ground Condition Template',
                    'input' => 'select',
                    'sort_order' => 600,
                    'source' => 'Forix\CategoryCustom\Model\Category\Attribute\Source\Templates',
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => null,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Forix',
                    'backend' => ''
                ]
            );
        }

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.6') < 0) {
	        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
	        $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'required_rig_model');
	        
	        $eavSetup->addAttribute(
		        \Magento\Catalog\Model\Category::ENTITY,
		        'required_rig_model',
		        [
			        'type' => 'int',
			        'label' => 'Is Required Rig Model',
			        'input' => 'boolean',
			        'required' => false,
			        'visible' => true,
			        'user_defined' => true,
			        'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
			        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
			        'is_html_allowed_on_front' => true,
			        'group' => 'Display Settings',
		        ]
	        );
        }
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.9') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'is_use_ground_condition');

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'is_use_ground_condition',
                [
                    'type' => 'int',
                    'label' => 'Is Use Ground Condition',
                    'input' => 'boolean',
                    'required' => false,
                    'visible' => true,
                    'user_defined' => true,
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Display Settings',
                ]
            );
        }

        $setup->endSetup();
    }
}