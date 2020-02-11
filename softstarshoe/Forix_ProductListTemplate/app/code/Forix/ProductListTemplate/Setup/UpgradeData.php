<?php

namespace Forix\ProductListTemplate\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    const SOFTSTARSHOE = 'SoftStar Shoe';
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    
    protected $categorySetupFactory;
    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->removeAttribute(Category::ENTITY, 'sss_visible_in_tab');
            $eavSetup->addAttribute(Category::ENTITY, 'sss_visible_in_tab',
                [
                    'group' => 'Forix',
                    'type' => 'int',
                    'label' => 'Visible in category tabs',
                    'sort_order' => 99,
                    'input' => 'boolean',
                    'required' => false,
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'default' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::VALUE_YES,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'user_defined' => false
                ]
            );
        }//end upgrade 2.0.1

        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(Category::ENTITY, 'forix_product_list_template', 'required', false);
        }//end upgrade 2.0.3

        if (version_compare($context->getVersion(), '2.0.4') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Category::ENTITY,
                'sss_custom_tab_title',
                [
                    'type' => 'varchar',
                    'label' => 'Tab Title',
                    'input' => 'text',
                    'sort_order' => 600,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => NULL,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Forix',
                ]
            );
        }//end upgrade 2.0.4

        if (version_compare($context->getVersion(), '2.0.5') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Category::ENTITY,
                'sss_category_cms_modal',
                [
                    'type' => 'text',
                    'label' => 'Description',
                    'input' => 'textarea',
                    'required' => false,
                    'sort_order' => 4,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Forix',
                ]
            );
            $eavSetup->addAttribute(
                Category::ENTITY,
                'sss_category_footer_cms',
                [
                    'type' => 'text',
                    'label' => 'Description',
                    'input' => 'textarea',
                    'required' => false,
                    'sort_order' => 5,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Forix',
                ]
            );
        }//end upgrade 2.0.5

        /**
         * Add attributes to the eav/attribute
         */
        if (version_compare($context->getVersion(), '2.0.8') < 0) {

            $attributes = [
                'sss_product_name_image' => 'Product Name Image'
            ];
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            foreach ($attributes as $key => $label) {
                $eavSetup->removeAttribute(Product::ENTITY, $key);
            }
        }//end upgrade 2.0.8


        if (version_compare($context->getVersion(), '2.1.3') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->removeAttribute(Category::ENTITY, 'sss_category_tab_ids');
            $eavSetup->addAttribute(
                Category::ENTITY,
                'sss_category_tab_ids',
                [
                    'type' => 'text',
                    'label' => 'Category Include Tab Ids',
                    'input' => 'multiselect',
                    'source' => \Forix\ProductListTemplate\Model\Category\Attribute\Source\Category::class,
                    'backend' => \Forix\ProductListTemplate\Model\Category\Attribute\Backend\Category::class,
                    'sort_order' => 40,
                    'required' => false,
                    'visible' => true,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Forix',
                ]
            );
        }//end upgrade 2.1.3

        if (version_compare($context->getVersion(), '2.1.4') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $attributeInt = [
                'sss_parent_tab_id' => 'Parent Tab Id',
                'sss_tab_position' => 'Tab Position',
            ];
            foreach ($attributeInt as $key => $label) {
                $eavSetup->removeAttribute(Category::ENTITY, $key);
                $eavSetup->addAttribute(Category::ENTITY,
                    $key,
                    [
                        'type' => 'int',
                        'label' => $label,
                        'input' => 'text',
                        'sort_order' => 600,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => true,
                        'default' => NULL,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'Forix',
                    ]
                );
            }
            
            //Setup Create Product Attribute.
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            
            $categorySetup->removeAttribute($entityTypeId, 'sss_category_note');
            $categorySetup->addAttribute(
                $entityTypeId,
                'sss_category_note',
                array(
                    'group' => 'General',
                    'type' => 'varchar',
                    'label' => 'Category Product Note', //Visible After Product Name
                    'note' => 'Visible After Product Name In Category Product Listing Page',
                    'input' => 'text',
                    'sort_order' => 100,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible_on_front' => true,
                    'required' => false,
                    'unique' => false,
                    'user_defined' => true,
                    'used_in_product_listing' => true,
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                )
            );
            
        }//end upgrade 2.1.4

        if (version_compare($context->getVersion(), '2.1.5') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(Category::ENTITY, 'forix_product_list_template', 'is_required', 0);
        }//end upgrade 2.1.5


        if (version_compare($context->getVersion(), '2.1.6') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->removeAttribute(Category::ENTITY, 'sss_category_left_cms');
            $eavSetup->addAttribute(
                Category::ENTITY,
                'sss_category_left_cms',
                [
                    'type' => 'text',
                    'label' => 'Category Left Content CMS',
                    'input' => 'textarea',
                    'required' => false,
                    'sort_order' => 110,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'wysiwyg_enabled' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => 'Forix',
                ]
            );
        }//end upgrade 2.1.6
        if (version_compare($context->getVersion(), '2.1.7') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Category::ENTITY,
                'sss_banner_title_category',
                [
                    'type' => 'varchar',
                    'label' => 'Banner Title',
                    'input' => 'text',
                    'sort_order' => 600,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => NULL,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Forix',
                ]
            );
        }//end upgrade 2.1.7
        if (version_compare($context->getVersion(), '2.1.8') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Category::ENTITY,
                'sss_image_title_seo',
                [
                    'type' => 'varchar',
                    'label' => 'Image Title Seo',
                    'input' => 'text',
                    'sort_order' => 45,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => NULL,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Content',
                ]
            );
            $eavSetup->addAttribute(
                Category::ENTITY,
                'sss_image_alt_seo',
                [
                    'type' => 'varchar',
                    'label' => 'Image Alt Seo',
                    'input' => 'text',
                    'sort_order' => 46,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => NULL,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Content',
                ]
            );
        }//end upgrade 2.1.8
    }
}