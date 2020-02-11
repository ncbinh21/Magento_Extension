<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

/**
 * Class UpgradeData
 * @package Amasty\Shopby\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Amasty\Base\Helper\Deploy
     */
    private $deployHelper;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * UpgradeData constructor.
     * @param \Amasty\Base\Helper\Deploy $deployHelper
     * @param \Magento\Framework\App\State $state
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        \Amasty\Base\Helper\Deploy $deployHelper,
        \Magento\Framework\App\State $state,
        CategorySetupFactory $categorySetupFactory
    ) {
        try {
            $state->setAreaCode('adminhtml');
        } catch (\Exception $e) {
            //Area code already set
        }
        $this->deployHelper = $deployHelper;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '1.6.3', '<')) {
            $this->deployPub();
        }

        if (version_compare($context->getVersion(), '2.1.5', '<')) {
            $this->addCategoryThumbnail($setup);
        }
    }

    protected function deployPub()
    {
        $dir = BP . '/app/code/Amasty/Shopby/Setup';

        $p = strrpos($dir, DIRECTORY_SEPARATOR);
        $modulePath = $p ? substr($dir, 0, $p) : $dir;
        $modulePath .= '/pub';

        $this->deployHelper->deployFolder($modulePath);
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return $this|ModuleDataSetupInterface
     */
    private function addCategoryThumbnail(ModuleDataSetupInterface $setup)
    {
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        if ($categorySetup->getAttribute('catalog_category', 'thumbnail')) {
            return $setup;
        }
        $categorySetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'thumbnail', [
            'type' => 'varchar',
            'label' => 'Thumbnail',
            'input' => 'image',
            'backend' => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
            'required' => false,
            'sort_order' => 5,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'General Information',
        ]);

        $idGroup = $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'General Information');
        $categorySetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $idGroup,
            'thumbnail',
            45
        );
        return $this;
    }
}
