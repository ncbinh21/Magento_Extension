<?php

namespace Forix\ProductAttribute\Setup;

use Magento\Catalog\Model\Product;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $_attributeSetFactory;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * UpgradeData constructor.
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     */
    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
    )
    {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->_resourceConfig = $resourceConfig;
        $this->_attributeSetFactory = $attributeSetFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

            $softStarShoeTabName = 'SoftStar Shoe';

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_color');
            $categorySetup->addAttribute($entityTypeId, 'sss_color',
                array(
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Color Scheme',
                    'input' => 'select',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => true,
                    'default' => '',
                    'filterable' => true,
                    'used_in_product_listing' => true,
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                    'option' => array(
                        'values' => array(
                            0 => 'Black',
                            1 => 'Medical White',
                            2 => 'Matte White',
                            3 => 'Light Gray',
                            4 => 'Metallic Gray',
                            5 => 'Light Beige',
                        )
                    ),
                ));

            $categorySetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'sss_color',
                'additional_data',
                json_encode(array(
                    'swatch_input_type' => 'visual',
                    'update_product_preview_image' => '1',
                    'use_product_image_for_swatch' => '1',
                ))
            );
            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_size');
            $categorySetup->addAttribute($entityTypeId, 'sss_size',
                array(
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Size',
                    'input' => 'select',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => true,
                    'default' => '',
                    'filterable' => true,
                    'swatch_input_type' => 'visual',
                    'used_in_product_listing' => true,
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                    'option' => array(
                        'values' => array(
                            0 => 'Adult Size 4U',
                            1 => 'Adult Size 5U',
                            2 => 'Adult Size 6U',
                            3 => 'Adult Size 7U',
                            4 => 'Adult Size 8U',
                            5 => 'Adult Size 9U',
                            6 => 'Adult Size 10U',
                            7 => 'Adult Size 11U',
                            8 => 'Adult Size 12U',
                            9 => 'Adult Size 13U',
                            10 => 'Adult Size 14U',
                            11 => 'Adult Size 15U',
                        )
                    ),
                ));

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_width');
            $categorySetup->addAttribute($entityTypeId, 'sss_width',
                array(
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Width',
                    'input' => 'select',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => true,
                    'default' => '',
                    'filterable' => true,
                    'swatch_input_type' => 'visual',
                    'used_in_product_listing' => true,
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                    'option' => array(
                        'values' => array(
                            0 => 'Regular',
                            1 => 'Wide',
                            2 => 'Narrow',
                            3 => 'Wide XL'
                        )
                    ),
                ));

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_sole');
            $categorySetup->addAttribute($entityTypeId, 'sss_sole',
                array(
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Sole',
                    'input' => 'select',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => true,
                    'default' => '',
                    'filterable' => true,
                    'swatch_input_type' => 'visual',
                    'used_in_product_listing' => true,
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                    'option' => array(
                        'values' => array(
                            0 => 'Option 1',
                            1 => 'Option 2',
                            2 => 'Option 3',
                            3 => 'Option 4'
                        )
                    ),
                ));

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_tooltip');
            $categorySetup->addAttribute($entityTypeId, 'sss_tooltip',
                array(
                    'group' => $softStarShoeTabName,
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Tool Tip',
                    'input' => 'textarea',
                    'sort_order' => 5,
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => true,
                    'default' => '',
                    'filterable' => true,
                    'swatch_input_type' => 'visual',
                    'required' => false,
                    'used_in_product_listing' => true,
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                )
            );
        }//end upgrade 2.0.1

        if (version_compare($context->getVersion(), '2.0.2') < 0) {

            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

            $softStarShoeTabName = 'SoftStar Shoe';
            $categorySetup->removeAttribute($entityTypeId, 'sss_customize_link');
            $categorySetup->addAttribute(
                $entityTypeId,
                'sss_customize_link',
                array(
                    'group' => $softStarShoeTabName,
                    'type' => 'varchar',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Product Customize',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'sort_order' => 2,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible_on_front' => true,
                    'required' => false,
                    'unique' => false,
                    'user_defined' => true,
                    'used_in_product_listing' => true,
                    'apply_to' => 'configurable',
                )
            );


        }//end upgrade 2.0.1

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

            $softStarShoeTabName = 'SoftStar Shoe';

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_style');
            $categorySetup->addAttribute($entityTypeId, 'sss_style',
                array(
                    'group' => $softStarShoeTabName,
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Style',
                    'input' => 'select',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => true,
                    'default' => '',
                    'filterable' => true,
                    'used_in_product_listing' => true,
                    'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                    'option' => array(
                        'values' => array(
                            0 => 'Ballerine',
                            1 => 'Hawthorne Chukka',
                            2 => 'Merry Jane',
                            3 => 'Metro',
                            4 => 'Moccasin',
                            5 => 'Rambler',
                            6 => 'RunAmoc - Dash',
                            7 => 'RunAmoc - Moc3',
                            8 => 'Style 1',
                            9 => 'Style 2',
                            10 => 'Style 3'
                        )
                    ),
                ));

        }//end upgrade 2.0.2

        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

            $softStarShoeTabName = 'SoftStar Shoe';

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_restriction');
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'sss_restriction',
                array(
                    'group' => $softStarShoeTabName,
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Product Restriction',
                    'input' => 'boolean',
                    'sort_order' => 99,
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible_on_front' => true,
                    'required' => false,
                    'user_defined' => false,
                    'used_in_product_listing' => true,
                )
            );
        }//end upgrade 2.0.3

        if (version_compare($context->getVersion(), '2.0.4') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $attributesList = ['sss_faq', 'sss_optional', 'sss_size_guide', 'sss_guide_download',
                'sss_care', 'sss_compare_runamocs'];

            foreach ($attributesList as $attribute) {
                $categorySetup->updateAttribute(
                    Product::ENTITY,
                    $attribute,
                    'is_visible_on_front',
                    true
                );
            }
        }//end upgrade 2.0.4

	    if (version_compare($context->getVersion(), '2.0.5') < 0) {
		    $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
		    $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
		    $softStarShoeTabName = 'SoftStar Shoe';

		    $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_quality');
		    $categorySetup->addAttribute($entityTypeId, 'sss_quality',
			    array(
				    'group' => $softStarShoeTabName,
				    'type' => 'int',
				    'backend' => '',
				    'frontend' => '',
				    'label' => 'Quality',
				    'input' => 'select',
				    'class' => '',
				    'source' => '',
				    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
				    'visible' => true,
				    'user_defined' => true,
				    'default' => '',
				    'used_in_product_listing' => true,
				    'apply_to' => 'simple,configurable',
				    'option' => array(
					    'values' => array(
						    0 => '1st Quality ',
						    1 => 'Cosmetic Defect',
						    2 => 'IRREGULAR'
					    )
				    ),
				    'required' => false,
				    'used_in_product_listing' => true,
			    ));

		    $categorySetup->removeAttribute($entityTypeId, 'sss_color_description');
		    $categorySetup->addAttribute(
			    $entityTypeId,
			    'sss_color_description',
			    array(
				    'group' => $softStarShoeTabName,
				    'type' => 'varchar',
				    'backend' => '',
				    'frontend' => '',
				    'label' => 'Color Description',
				    'input' => 'text',
				    'class' => '',
				    'source' => '',
				    'sort_order' => 0,
				    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
				    'visible_on_front' => true,
				    'required' => false,
				    'unique' => false,
				    'user_defined' => true,
				    'used_in_product_listing' => true,
				    'apply_to' => 'simple',
			    )
		    );
	    }//end upgrade 2.0.5

        if (version_compare($context->getVersion(), '2.0.6') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_style');
            $attributesList = ['sss_faq', 'sss_optional', 'sss_size_guide', 'sss_guide_download',
                'sss_care', 'sss_compare_runamocs'];

            foreach ($attributesList as $attribute) {
                $categorySetup->updateAttribute(
                    Product::ENTITY,
                    $attribute,
                    'is_html_allowed_on_front',
                    true
                );
            }
        }//end upgrade 2.0.6

        if (version_compare($context->getVersion(), '2.0.7') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_color');
        }//end upgrade 2.0.7

        if (version_compare($context->getVersion(), '2.0.8') < 0) {
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

            $softStarShoeTabName = 'SoftStar Shoe';

            $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_most_loved_styles');
            $categorySetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'sss_most_loved_styles',
                array(
                    'group' => $softStarShoeTabName,
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Most Loved Styles',
                    'input' => 'boolean',
                    'sort_order' => 1,
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible_on_front' => true,
                    'required' => false,
                    'user_defined' => false,
                    'used_in_product_listing' => true,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                )
            );
        }//end upgrade 2.0.8
    }
}