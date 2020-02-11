<?php


namespace Forix\ProductListTemplate\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Category;
class InstallData implements InstallDataInterface
{

    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttributeGroup(
            Category::ENTITY,
            $eavSetup->getDefaultAttributeSetId(Category::ENTITY),
            'Forix',
            99
        );
        $eavSetup->addAttribute(
            Category::ENTITY,
            'forix_product_list_template',
            [
                'type' => 'varchar',
                'label' => 'Product Listing Template',
                'input' => 'select',
                'sort_order' => 600,
                'source' => 'Forix\ProductListTemplate\Model\Category\Attribute\Source\Product\Listing\Templates',
                'visible' => true,
                'required' => true,
                'user_defined' => true,
                'default' => null,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Forix',
                'backend' => ''
            ]
        );

        //Your install script
    }
}