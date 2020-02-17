<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Plugin;

use Mirasvit\Seo\Api\Config\CurrentPageProductsInterface;
use Mirasvit\Seo\Helper\Data as SeoDataHelper;
use Mirasvit\Seo\Model\Config;
use Mirasvit\Seo\Api\Service\PageDetectorInterface;
use Magento\Framework\Registry;
use Mirasvit\Seo\Model\ResourceModel\Template\CollectionFactory as TemplateCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Model\TemplateFactory;

class CurrentPageProducts
{
    /**
     * CurrentPageProducts constructor.
     * @param SeoDataHelper $seoData
     * @param Registry $registry
     * @param TemplateCollectionFactory $templateCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param TemplateFactory $templateFactory
     * @param PageDetectorInterface $pageDetector
     * @param Config $config
     */
    public function __construct(
        SeoDataHelper $seoData,
        Registry $registry,
        TemplateCollectionFactory $templateCollectionFactory,
        StoreManagerInterface $storeManager,
        TemplateFactory $templateFactory,
        PageDetectorInterface $pageDetector,
        Config $config
    ) {
        $this->seoData = $seoData;
        $this->registry = $registry;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->storeManager = $storeManager;
        $this->templateFactory = $templateFactory;
        $this->pageDetector = $pageDetector;
        $this->config = $config;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct                     $subject
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection
     *
     * @return \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetLoadedProductCollection($subject, $collection)
    {
        if ($this->pageDetector->isCategory()
            && $this->config->isUseShortDescriptionForCategories()
            && $collection
            && ($collection->getSize() > 0)) {
                $collectionItemsIds = [];
                foreach ($collection as $item) {
                    $collectionItemsIds[] = $item->getId();
                }

                $productIdsMap = $this->getIsAppliedProductMap($collectionItemsIds);
                foreach ($collection as $item) {
                    if (($seoTemplateRule = $this->seoData->checkTempalateRule(true,
                            false,
                            false,
                            false,
                            $item,
                            $productIdsMap))
                        && ($shortDescription = $seoTemplateRule->getData('short_description'))
                    ) {
                        $item->setData('short_description', $shortDescription);
                    }
                }
        }

        $this->registry->register(CurrentPageProductsInterface::PRODUCT_COLLECTION, $collection, true);

        return $collection;
    }

    /**
     * @param array $allProductsIds
     * @return array
     */
    protected function getIsAppliedProductMap($allProductsIds)
    {
        $productIdsMap = [];
        $collectionProduct = $this->templateCollectionFactory->create()
            ->addStoreFilter($this->storeManager->getStore())
            ->addFieldToFilter('rule_type', Config::PRODUCTS_RULE)
            ->addActiveFilter()
            ->addSortOrder();

        foreach ($collectionProduct as $item) {
            $productIdsMap[$item->getTemplateId()] = $this->templateFactory
                ->create()
                ->getRule($item->getTemplateId())
                ->isProductApplied($allProductsIds);
        }

        return $productIdsMap;
    }
}
