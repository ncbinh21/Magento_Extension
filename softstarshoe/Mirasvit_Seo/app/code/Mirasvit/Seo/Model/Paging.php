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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model;

class Paging
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;
    /**
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $toolbar;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $layer;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Module\Manager                $moduleManager
     * @param \Magento\Catalog\Model\Layer\Resolver            $layerResolver
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver
    ) {
        $this->moduleManager = $moduleManager;
        // To avoid Ajax failing with Exception #0 (RuntimeException): Catalog Layer has been already created
        if ($this->moduleManager->isOutputEnabled('Manadev_LayeredNavigation')) {
            $this->layer = false;
        } else {
            $this->layer = $layerResolver->get();
        }
        $this->context = $context;
        $this->urlManager = $context->getUrlBuilder();
    }

    /**
     * @return $this
     */
    protected function _initProductCollection()
    {
        if ($layer = $this->layer) {
            $this->productCollection = $layer->getProductCollection();

            $limit = (int) $this->_getToolbar()->getLimit();
            if ($limit) {
                $this->productCollection->setPageSize($limit);
            }
        }

        return $this;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function _getToolbar()
    {
        if ($this->toolbar === null) {
            $this->toolbar = $this->context->getLayout()->createBlock(
                '\Magento\Catalog\Block\Product\ProductList\Toolbar'
            );
        }

        return $this->toolbar;
    }

    /**
     * @return \Mirasvit\Seo\Block\Html\Pager
     */
    protected function _getPager()
    {
        return $this->context->getLayout()->createBlock('\Magento\Theme\Block\Html\Pager')
            ->setLimit($this->_getToolbar()->getLimit())
            ->setCollection($this->productCollection);
    }

    /**
     * @return $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function createLinks()
    {
        $this->_initProductCollection();
        $pager = $this->_getPager();

        $numPages = count($pager->getPages());

        $previousPageUrl = $pager->getPreviousPageUrl();
        $nextPageUrl = $pager->getNextPageUrl();

        //we have html_entity_decode because somehow manento encodes '&'
        if (!$pager->isFirstPage() && !$pager->isLastPage() && $numPages > 2) {
            $this->addLink('prev', $previousPageUrl);
            $this->addLink('next', $nextPageUrl);
        } elseif ($pager->isFirstPage() && $numPages > 1) {
            $this->addLink('next', $nextPageUrl);
        } elseif ($pager->isLastPage() && $numPages > 1 && $numPages >= $this->getCurrentPage($numPages)) {
            $this->addLink('prev', $previousPageUrl);
        } elseif ($pager->isLastPage() && $numPages > 1 && $numPages < $this->getCurrentPage($numPages)) {
            $this->addLink('prev', $pager->getPageUrl($numPages - 1));
        }

        return $this;
    }

    /**
     * @return int
     */
    protected function getCurrentPage()
    {
        $currentPageNumber = 0;
        $currentUrl = $this->urlManager->getCurrentUrl();
        preg_match('/p=\d{1,9}/', $currentUrl, $page);

        if ($page && isset($page[0])) {
            $currentPageNumber = (int) trim(str_replace('p=', '', $page[0]));
        }

        return $currentPageNumber;
    }

    /**
     * @param string $type
     * @param string $url
     *
     * @return void
     */
    protected function addLink($type, $url)
    {
        $pageConfig = $this->context->getPageConfig();
        $pageConfig->addRemotePageAsset(
            html_entity_decode($url),
            $type,
            ['attributes' => ['rel' => $type]]
        );
    }
}
