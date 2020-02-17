<?php
/**
 * Copyright (c) 2016.
 * Created by Hidro Le.
 * User: Hidro
 */

namespace Forix\Product\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{

    protected $_eavSetupFactory;
    protected $collectionCategoryFactory;
    protected $categoryRepository;
    protected $collectionProductFactory;
    protected $categoryLinkManagement;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionCategoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionProductFactory,
        \Magento\Catalog\Model\CategoryLinkManagement $categoryLinkManagement,
        \Magento\Framework\App\State $state
    )
    {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->categoryRepository = $categoryRepository;
        $this->collectionCategoryFactory = $collectionCategoryFactory;
        $this->collectionProductFactory = $collectionProductFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            $this->createTextAreaAttribute($installer, [
                'mb_message_in_stock' => "In Stock Message",
                'mb_message_back_order' => "Back Order Message"
            ], 'MelfredBorzall', 1000);
        }
        if (version_compare($context->getVersion(), '2.2.4') < 0) {
            $this->moveProductToGrondCondition();
        }
        $installer->endSetup();
    }

    public function moveProductToGrondCondition()
    {
        $categoryLists = $this->collectionCategoryFactory->create();
        $categoryBit = $categoryReamer = $categoryGroundCondition = null;
        if($categoryLists->getSize() > 0) {
            foreach ($categoryLists as $categoryItem) {
                $category = $this->categoryRepository->get($categoryItem->getId());
                if($category->getData('is_bit_reamer') == 'bits') {
                    $categoryBit = $category;
                }
                if($category->getData('is_bit_reamer') == 'reamers') {
                    $categoryReamer = $category;
                }
                if($category->getData('is_bit_reamer') == 'Bits/Reamers') {
                    $categoryGroundCondition = $category;
                }
            }
        }
        $productList = $this->collectionProductFactory->create();
        if($productList->getSize() > 0) {
            foreach ($productList as $product) {
                $arayList = $product->getCategoryIds();
                if(($categoryBit->getId() && in_array($categoryBit->getId(), $arayList)) || ($categoryReamer->getId() && in_array($categoryReamer->getId(), $arayList))) {
                    if($categoryGroundCondition->getId() && !in_array($categoryGroundCondition->getId(), $arayList)) {
                        array_push($arayList, $categoryGroundCondition->getId());
                        $this->categoryLinkManagement->assignProductToCategories($product->getSku(), $arayList);
                    }
                }
            }
        }
    }
    /**
     * @param $setup
     * @param $textAttributeCodes
     * @param $group
     * @param int $sortOrder
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createTextAreaAttribute($setup, $textAttributeCodes, $group, $sortOrder = 3)
    {
        /**
         * @var $categorySetup \Magento\Catalog\Setup\CategorySetup
         */
        $categorySetup = $this->_eavSetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        foreach ($textAttributeCodes as $key => $label) {
            $categorySetup->removeAttribute($entityTypeId, $key);
            $categorySetup->addAttribute(
                'catalog_product',
                $key,
                [
                    'type' => 'varchar',
                    'label' => $label,
                    'input' => 'textarea',
                    'length' => 5000,
                    'required' => false,
                    'sort_order' => $sortOrder,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'user_defined' => true,
                    'visible_on_front' => true,
                    'is_html_allowed_on_front' => true,
                    'group' => $group,
                ]
            );
        }
    }
}





