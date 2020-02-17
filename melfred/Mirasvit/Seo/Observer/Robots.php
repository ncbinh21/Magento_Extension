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



namespace Mirasvit\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;

class Robots implements ObserverInterface
{
    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
     */
    protected $filterableAttributeList;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @param \Mirasvit\Seo\Helper\Data                                     $seoData
     * @param \Magento\Framework\View\Element\Template\Context              $context
     * @param \Magento\Framework\ObjectManagerInterface                     $objectManager
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filterableAttributeList
     * @param \Mirasvit\Seo\Model\Config                                    $config
     */
    public function __construct(
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filterableAttributeList,
        \Mirasvit\Seo\Model\Config $config
    ) {
        $this->seoData = $seoData;
        $this->context = $context;
        $this->objectManager = $objectManager;
        $this->request = $context->getRequest();
        $this->registry = $registry;
        $this->filterableAttributeList = $filterableAttributeList;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applyRobots(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->seoData->isIgnoredActions()
            && $this->seoData->getFullActionCode() != 'customer_address_form') {
                return $this;
        }

        $pageConfig = $this->context->getPageConfig();
        if ($robots = $this->getRobots()) {
            $pageConfig->setRobots($robots);
        }
    }

    /**
     * Returns string like 'NOINDEX,NOFOLLOW'.
     *
     * @return bool|string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRobots()
    {
        if ($this->context->getStoreManager()->getStore()->isCurrentlySecure()
            && ($robotsCode = $this->config->getHttpsNoindexPages())) {
            return $this->seoData->getMetaRobotsByCode($robotsCode);
        }

        if ($product = $this->registry->registry('current_product')) {
            if ($robots = $this->seoData->getMetaRobotsByCode($product->getSeoMetaRobots())) {
                return $robots;
            }
        }
        $fullAction = $this->request->getFullActionName();
        foreach ($this->config->getNoindexPages() as $record) {
            //for patterns like filterattribute_(arttribte_code) and filterattribute_(Nlevel)
            if (strpos($record['pattern'], 'filterattribute_(') !== false
                && $fullAction == 'catalog_category_view') {
                if ($this->_checkFilterPattern($record['pattern'])) {
                    return $this->seoData->getMetaRobotsByCode($record->getOption());
                }
            }

            if ($this->seoData->checkPattern($fullAction, $record->getPattern())
                || $this->seoData->checkPattern($this->seoData->getBaseUri(), $record['pattern'])) {
                return $this->seoData->getMetaRobotsByCode($record->getOption());
            }
        }

        return false;
    }

    /**
     * @param string $pattern
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _checkFilterPattern($pattern)
    {
        if (!$this->filterableAttributeList && !$this->filterableAttributeList->getList()) {
            return false;
        }

        $urlParams = $this->request->getQuery();
        $urlParams = $urlParams->toArray();
        $currentFilters = $this->filterableAttributeList->getList()->getData();
        $filterArr = [];
        foreach ($currentFilters as $filterAttr) {
            if (isset($filterAttr['attribute_code'])) {
                $filterArr[] = $filterAttr['attribute_code'];
            }
        }

        $usedFilters = [];
        if (!empty($filterArr) && $urlParams) {
            foreach (array_keys($urlParams) as $keyParam) {
                if (in_array($keyParam, $filterArr)) {
                    $usedFilters[] = $keyParam;
                }
            }
        }

        if (!empty($usedFilters)) {
            if(trim($pattern) == 'filterattribute_(alllevel)') {
                return true;
            }
            $usedFiltersCount = count($usedFilters);
            if (strpos($pattern, 'level)') !== false) {
                preg_match('/filterattribute_\\((\d{1})level/', trim($pattern), $levelNumber);
                if (isset($levelNumber[1])) {
                    if ($levelNumber[1] == $usedFiltersCount) {
                        return true;
                    }
                }
            }

            foreach ($usedFilters as $useFilterVal) {
                if (strpos($pattern, '('.$useFilterVal.')') !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->applyRobots($observer);
    }
}
