<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class ProductInitAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->coreRegistry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->scopeConfig->getValue(
            'amseorichdata/breadcrumbs/extend',
            ScopeInterface::SCOPE_STORE
        )) {
            return;
        }

        $category = $this->coreRegistry->registry('current_category');

        if ($category)
            return;

        $product = $observer->getProduct();

        $categories = $product->getCategoryCollection();

        $select = $categories->getSelect()
            ->reset(\Zend_Db_Select::COLUMNS)
            ->columns(['entity_id'])
            ->order('level DESC')
            ->limit(1);
        ;

        $categoryId = $categories->getConnection()->fetchOne($select);

        if ($categoryId) {
            $category = $this->categoryRepository->get($categoryId);

            if ($category) {
                $this->coreRegistry->register('current_category', $category);
            }
        }
    }
}
