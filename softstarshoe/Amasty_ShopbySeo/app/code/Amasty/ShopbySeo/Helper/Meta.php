<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbySeo\Helper;

use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\Shopby\Model\Resolver;
use Amasty\ShopbyBrand\Helper\Content;
use Amasty\ShopbyPage\Api\Data\PageInterface;
use Amasty\ShopbyPage\Model\Page as PageEntity;
use Amasty\ShopbySeo\Model\Source\IndexMode;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Meta extends AbstractHelper
{
    /**
     * @var  \Amasty\Shopby\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var  RequestInterface
     */
    protected $request;

    /**
     * @var  Registry
     */
    protected $registry;

    /**
     * @var  Content
     */
    private $contentHelper;

    /**
     * @var  boolean
     */
    private $isFollowingAllowed;

    /**
     * @var  Config
     */
    private $pageConfig;

    /**
     * @var  Toolbar
     */
    private $toolbar;

    public function __construct(
        Context $context,
        \Amasty\Shopby\Helper\Data $dataHelper,
        Registry $registry,
        Content $contentHelper,
        Toolbar $toolbar,
        Resolver $resolver
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->request = $context->getRequest();
        $this->registry = $registry;
        $this->contentHelper = $contentHelper;
        $toolbar->setCollection($resolver->get()->getProductCollection());
        $this->toolbar = $toolbar;
    }

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

    private function setRobots()
    {
        $robots = $this->pageConfig->getRobots();

        $index = true;
        $follow = true;

        $forcePageIndex
            = $this->registry->registry(PageEntity::MATCHED_PAGE_MATCH_TYPE) == PageEntity::MATCH_TYPE_STRICT;
        $branding = $this->contentHelper->getCurrentBranding();

        $appliedFiltersSettings = $this->dataHelper->getSelectedFiltersSettings();
        foreach ($appliedFiltersSettings as $row) {
            /** @var FilterSettingInterface $setting */
            $setting = $row['setting'];

            /** @var FilterInterface $filter */
            $filter = $row['filter'];

            if ($branding && $branding->getFilterCode() == $setting->getFilterCode()) {
                continue;
            }

            $value = $this->request->getParam($filter->getRequestVar());
            $count = count(explode(',', $value));

            if (!$forcePageIndex) {
                if ($setting->getIndexMode() == IndexMode::MODE_NEVER) {
                    $index = false;
                } elseif ($setting->getIndexMode() == IndexMode::MODE_SINGLE_ONLY && $count >= 2) {
                    $index = false;
                }
            }

            if ($setting->getFollowMode() == IndexMode::MODE_NEVER) {
                $follow = false;
            } elseif ($setting->getFollowMode() == IndexMode::MODE_SINGLE_ONLY && $count >= 2) {
                $follow = false;
            }
        }

        if (!$index) {
            $robots = preg_replace('/\w*index/i', 'noindex', $robots);
        }
        if (!$follow) {
            $robots = preg_replace('/\w*follow/i', 'nofollow', $robots);
        }

        $this->isFollowingAllowed = $follow;
        $this->pageConfig->setRobots($robots);
    }

    public function isFollowingAllowed()
    {
        return $this->isFollowingAllowed;
    }

    private function addPrevNext()
    {
        $p = $this->toolbar->getCurrentPage();

        if (!$this->toolbar->isFirstPage()) {
            $prevPage = $p == 2 ? null : $p - 1;
            $prevUrl = $this->toolbar->getPagerUrl(['p' => $prevPage]);
            $this->pageConfig->addRemotePageAsset(
                $prevUrl,
                'link_rel',
                ['attributes' => ['rel' => 'prev']]
            );
        }

        $lastPage = $this->toolbar->getLastPageNum();
        if ($p < $lastPage) {
            $nextUrl = $this->toolbar->getPagerUrl(['p' => $p + 1]);
            $this->pageConfig->addRemotePageAsset(
                $nextUrl,
                'link_rel',
                ['attributes' => ['rel' => 'next']]
            );
        }
    }
}
