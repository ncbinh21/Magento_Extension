<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */

namespace Amasty\ShopbySeo\Helper;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Amasty\ShopbySeo\Model\Source\IndexMode;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\View\Page\Config;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Meta extends AbstractHelper
{
    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $dataHelper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var boolean
     */
    private $isFollowingAllowed;

    /**
     * @var \Magento\Framework\View\LayoutInterface\Proxy
     */
    private $layout;

    /**
     * @var Config
     */
    private $pageConfig;
    
    /**
     * @var Resolver
     */
    private $layerResolver;

    public function __construct(
        Context $context,
        \Amasty\Shopby\Helper\Data $dataHelper,
        \Magento\Framework\View\LayoutInterface\Proxy $layout,
        Registry $registry,
        Resolver $layerResolver
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->registry = $registry;
        $this->layerResolver = $layerResolver;
        $this->layout = $layout;
    }

    /**
     * @param Config $pageConfig
     * @return void
     */
    public function setPageTags(Config $pageConfig)
    {
        $this->pageConfig = $pageConfig;
        if ($this->scopeConfig->getValue('amasty_shopby_seo/robots/control_robots', ScopeInterface::SCOPE_STORE)) {
            $this->setRobots();
        }

        if ($this->scopeConfig->getValue('amasty_shopby_seo/other/prev_next', ScopeInterface::SCOPE_STORE)) {
            $this->addPrevNext();
        }
    }

    /**
     * @return void
     */
    private function setRobots()
    {
        $index = true;
        $follow = true;
        $robots = $this->pageConfig->getRobots();
        $appliedFiltersSettings = $this->dataHelper->getSelectedFiltersSettings();
        foreach ($appliedFiltersSettings as $row) {
            $data = new DataObject([
                'setting' => $row['setting'],
                'filter' => $row['filter']
            ]);
            $index = $index ? $this->getIndexTagByData($index, $data) : $index;
            $follow = $follow ? $this->getFollowTagByData($follow, $data) : $follow;
        }
        $isNoIndexWithMultiple = $this->scopeConfig->getValue(
            'amasty_shopby_seo/robots/noindex_multiple',
            ScopeInterface::SCOPE_STORE
        );

        if (!$index || ($isNoIndexWithMultiple && count($appliedFiltersSettings) > 1)) {
            $robots = preg_replace('/\w*index/i', 'noindex', $robots);
        }

        if (!$follow) {
            $robots = preg_replace('/\w*follow/i', 'nofollow', $robots);
        }

        $this->isFollowingAllowed = $follow;
        $this->pageConfig->setRobots($robots);
    }

    /**
     * Enhanced in plugins.
     *
     * @param bool $indexTag
     * @param DataObject $data
     * @return bool
     */
    public function getIndexTagByData($indexTag, DataObject $data)
    {
        return $this->getTagByData(FilterSettingInterface::INDEX_MODE, $indexTag, $data);
    }

    /**
     * Enhanced in plugins.
     *
     * @param bool $followTag
     * @param DataObject $data
     * @return bool
     */
    public function getFollowTagByData($followTag, DataObject $data)
    {
        return $this->getTagByData(FilterSettingInterface::FOLLOW_MODE, $followTag, $data);
    }

    /**
     * @param $tagKey
     * @param $tagValue
     * @param $data
     * @return bool
     */
    public function getTagByData($tagKey, $tagValue, $data)
    {
        /** @var FilterSettingInterface $setting */
        $setting = $data['setting'];
        /** @var FilterInterface $filter */
        $filter = $data['filter'];
        $value = $this->_getRequest()->getParam($filter->getRequestVar());
        $count = count(explode(',', $value));
        $mode = $tagKey == FilterSettingInterface::INDEX_MODE ? $setting->getIndexMode() : $setting->getFollowMode();

        if ($mode == IndexMode::MODE_NEVER || ($mode == IndexMode::MODE_SINGLE_ONLY && $count > 1)) {
            $tagValue = false;
        }

        return $tagValue;
    }

    /**
     * @return bool
     */
    public function isFollowingAllowed()
    {
        return $this->isFollowingAllowed;
    }

    /**
     * @return void
     */
    private function addPrevNext()
    {
        $toolbar = $this->getToolbarBlock();
        if (!$toolbar) {
            return;
        }

        $currPage = $toolbar->getCurrentPage();
        if (!$toolbar->isFirstPage()) {
            $prevPage = ($currPage == 2) ? null : ($currPage - 1);
            $prevUrl = $toolbar->getPagerUrl(['p' => $prevPage]);
            $this->pageConfig->addRemotePageAsset(
                $prevUrl,
                'link_rel',
                ['attributes' => ['rel' => 'prev']]
            );
        }

        $lastPage = $toolbar->getLastPageNum();
        if ($currPage < $lastPage) {
            $nextUrl = $toolbar->getPagerUrl(['p' => $currPage + 1]);
            $this->pageConfig->addRemotePageAsset(
                $nextUrl,
                'link_rel',
                ['attributes' => ['rel' => 'next']]
            );
        }
    }

    /**
     * @return Toolbar|null
     */
    private function getToolbarBlock()
    {
        /** @var Toolbar $toolbar */
        $toolbar = $this->layout->getBlock('product_list_toolbar');
        if ($toolbar) {
            $toolbar->setCollection($this->layerResolver->get()->getProductCollection());
        }

        return $toolbar;
    }

}
