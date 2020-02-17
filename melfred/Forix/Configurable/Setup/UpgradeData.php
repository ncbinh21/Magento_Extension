<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Configurable\Setup;


use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    protected $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */

    public function __construct(
        EavSetupFactory $eavSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $groupname = 'MelfredBorzall';
            /**
             * @var $eavSetup \Magento\Eav\Setup\EavSetup
             */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $eavSetup->removeAttribute($entityTypeId, 'mb_option_image');
            $eavSetup->addAttribute(
                $entityTypeId,
                'mb_option_image',
                [
                    'type' => 'varchar',
                    'label' => 'Radio Option Image',
                    'input' => 'media_image',
                    'frontend' => \Magento\Catalog\Model\Product\Attribute\Frontend\Image::class,
                    'required' => false,
                    'sort_order' => 3,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General',
                ]
            );

            /// Add New Attribute
            $attributes = [
                'mb_carbide_cutter_option' => [
                    'group' => $groupname,
                    'type' => 'int',
                    'label' => 'Carbide Cutter Option',
                    'input' => 'select',
                    'required' => false,
                    'apply_to' => 'simple,configurable,virtual,bundle'
                ]
            ];
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $eavSetup->addAttribute($entityTypeId, $attributeCode,
                    array_merge(
                        [
                            'used_in_product_listing' => false,
                            'system' => 0,
                            'user_defined' => true,
                            'comparable' => false,
                            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,

                        ],
                        $attributeInfo
                    )
                );
            }
        }

        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('eav_attribute'),
                'option_template',
                [
                    'type'     => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment'  =>'Choose Template For Configurable'
                ]
            );
        }
        $setup->endSetup();
    }


}
