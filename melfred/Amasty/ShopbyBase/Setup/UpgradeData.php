<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Class UpgradeData
 * @package Amasty\Shopby\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(
        \Magento\Framework\App\State $state,
        CategoryCollectionFactory $categoryFactory
    ) {
        try {
            $state->setAreaCode('adminhtml');
        } catch (\Exception $e) {
            //Area code is already set
        }

        $this->categoryCollectionFactory = $categoryFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($context->getVersion(), '2.1.7', '<')) {
            $this->applyIsAnchorForRootCategories();
        }
    }

    /**
     * @return void
     */
    private function applyIsAnchorForRootCategories()
    {
        try {
            $rootCategories = $this->categoryCollectionFactory->create();
            $rootCategories
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('level', 1);
            foreach ($rootCategories as $category) {
                $category->setIsAnchor(true);
            }

            $rootCategories->save();
        } catch (\Exception $e) {
            // "Invalid attribute name: level" while running unit tests in some cases
        }
    }
}
