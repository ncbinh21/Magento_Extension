<?php

namespace Forix\ProductAttribute\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    protected $categorySetupFactory;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;
    protected $_objectManager;

    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->_resourceConfig = $resourceConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $tabName = 'MelfredBorzall';

        $categorySetup->removeAttribute($entityTypeId, 'mb_badge_heavy');
        $categorySetup->addAttribute(
            $entityTypeId,
            'mb_badge_heavy',
            [
                'group' => $tabName,
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Heavy Badge',
                'note' => 'Show Heavy Badge in cart page',
                'input' => 'boolean',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '',
                'sort_order' => 1
            ]
        );

    }
}
