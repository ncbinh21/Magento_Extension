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
    const SOFTSTARSHOE = 'SoftStar Shoe';

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * InstallData constructor.
     * @param CategorySetupFactory $categorySetupFactory
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function  __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
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

        $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_faq');
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sss_faq',
            [
                'group' => self::SOFTSTARSHOE,
                'type' => 'text',
                'label' => 'FAQs',
                'input' => 'textarea',
                'sort_order' => 10,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'required' => false,
                'used_in_product_listing' => true,
            ]
        );

        $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_optional');
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sss_optional',
            [
                'group' => self::SOFTSTARSHOE,
                'type' => 'text',
                'label' => 'Optional Image',
                'input' => 'textarea',
                'sort_order' => 20,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'required' => false,
                'used_in_product_listing' => true,
            ]
        );

        $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_size_guide');
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sss_size_guide',
            [
                'group' => self::SOFTSTARSHOE,
                'type' => 'text',
                'label' => 'Sizing Guide',
                'input' => 'textarea',
                'sort_order' => 30,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'required' => false,
                'used_in_product_listing' => true,
            ]
        );

        $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_guide_download');
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sss_guide_download',
            [
                'group' => self::SOFTSTARSHOE,
                'type' => 'text',
                'label' => 'Guide Download',
                'input' => 'textarea',
                'sort_order' => 40,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'required' => false,
                'used_in_product_listing' => true,
            ]
        );

        $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_care');
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sss_care',
            [
                'group' => self::SOFTSTARSHOE,
                'type' => 'text',
                'label' => 'Care',
                'input' => 'textarea',
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'required' => false,
                'used_in_product_listing' => true,
            ]
        );

        $categorySetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sss_compare_runamocs');
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sss_compare_runamocs',
            [
                'group' => self::SOFTSTARSHOE,
                'type' => 'text',
                'label' => 'Compare Runamocs',
                'input' => 'textarea',
                'sort_order' => 60,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled' => true,
                'required' => false,
                'used_in_product_listing' => true,
            ]
        );

        $attributesList = ['sss_faq', 'sss_optional', 'sss_size_guide', 'sss_guide_download', 'sss_care', 'sss_compare_runamocs'];
        foreach ($attributesList as $attribute) {
            $categorySetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attribute,
                'is_wysiwyg_enabled',
                true
            );
        }
    }
}