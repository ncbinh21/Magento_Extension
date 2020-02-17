<?php


namespace Forix\CategoryCustom\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallData
 * @package Forix\CategoryCustom\Setup
 */
class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'category_video');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'category_video', [
            'type' => 'varchar',
            'label' => 'Category Youtube Url',
            'input' => 'text',
            'visible' => true,
            'default' => '',
            'required' => false,
            'user_defined' => true,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Display Settings',
        ]);
    }
}







