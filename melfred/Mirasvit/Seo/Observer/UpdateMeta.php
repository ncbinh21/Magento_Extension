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
use Mirasvit\Seo\Api\Config\CurrentPageProductsInterface;

class UpdateMeta extends \Magento\Framework\Model\AbstractModel implements ObserverInterface
{
    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Mirasvit\Seo\Helper\UpdateBody
     */
    protected $updateBody;

    /**
     * @var \Mirasvit\Seo\Observer\Robots
     */
    protected $robots;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var bool
     */
    protected $isPrevNextLinkAdded;

    /**
     * UpdateMeta constructor.
     * @param \Mirasvit\Seo\Helper\Data $seoData
     * @param \Mirasvit\Seo\Helper\UpdateBody $updateBody
     * @param \Magento\Framework\Registry $registry
     * @param Robots $robots
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Mirasvit\Seo\Model\Config $config
     * @param \Mirasvit\Seo\Model\Paging $paging
     */
    public function __construct(
        \Mirasvit\Seo\Helper\Data $seoData,
        \Mirasvit\Seo\Helper\UpdateBody $updateBody,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Observer\Robots $robots,
        \Magento\Framework\Module\Manager $moduleManager,
        \Mirasvit\Seo\Model\Config $config,
        \Mirasvit\Seo\Model\Paging $paging
    ) {
        $this->seoData = $seoData;
        $this->updateBody = $updateBody;
        $this->registry = $registry;
        $this->robots = $robots;
        $this->moduleManager = $moduleManager;
        $this->config = $config;
        $this->paging = $paging;
    }

    /**
     * @param string $e
     * @param bool|\Magento\Framework\App\Response\Http $response
     * @return bool|\Magento\Framework\App\Response\Http
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function modifyHtmlResponseMeta($e, $response = false)
    {
        $applyForCache = ($response) ? true : false;
        $action = $this->seoData->getFullActionCode();
        $isModifyAllowed = false;

        if ($action == 'blog_tag_view'
            && $this->moduleManager->isEnabled('Magefan_Blog')) {
                $isModifyAllowed = true;
        }

        if ($this->config->isUseHtmlSymbolsInMetaTags()
            && !$this->seoData->isIgnoredActions()) {
                $isModifyAllowed = true;
        }

        $isModifyPrevNext = $this->isModifyPrevNext();

        if (($action != 'contact_index_index' && !$isModifyAllowed && !$isModifyPrevNext)
            || $this->seoData->isIgnoredActions()) {
                return $response;
        }

        $seo = $this->seoData->getCurrentSeo();

        if (!$seo || (!$applyForCache && !is_object($e))) {
            return $response;
        }

        if (!$applyForCache) {
            $response = $e->getResponse();
        }

        $body = $response->getBody();

        if (!$this->updateBody->hasDoctype($body)) {
            return $response;
        }

        if ($isModifyPrevNext) {
            $response->setBody($this->getBodyWithPrevNext($body));
        }

        if ($isModifyAllowed) {
            $response->setBody($this->getBodyWithMeta($body, $seo));
        }

        if ($applyForCache) {
            return $response;
        }
    }


    /**
     * @return bool
     */
    protected function isModifyPrevNext()
    {
        $isModifyPrevNext = false;
        if (!$this->isPrevNextLinkAdded &&
            ($this->registry->registry(CurrentPageProductsInterface::PRODUCT_COLLECTION))
                && $this->config->isPagingPrevNextEnabled()
                && !$this->seoData->isIgnoredActions()
                && $this->isCategory()) {
                $isModifyPrevNext = true;
        }

        return $isModifyPrevNext;
    }

    /**
     * @param string $body
     * @return string
     */
    protected function getBodyWithPrevNext($body)
    {
        if (($productCollection = $this->registry->registry(CurrentPageProductsInterface::PRODUCT_COLLECTION))
            && ($prevNextLink = $this->paging->createLinks($productCollection))) {
                $this->updateBody->addPrevNextLink($body, $prevNextLink);
                $this->isPrevNextLinkAdded = true;
                    return $body;
        }

        return $body;
    }

    /**
     * @param string $body
     * @param \Mirasvit\Seo\Model\SeoObject\Category $seo
     * @return mixed
     */
    protected function getBodyWithMeta($body, $seo)
    {
        $seoTitle = trim($seo->getTitle());
        $seoMetaTitle = trim($seo->getMetaTitle());
        $seoMetaKeywords = trim($seo->getMetaKeywords());
        $seoMetaDescription = trim($seo->getMetaDescription());
        $robots = $this->robots->getRobots();

        if ($seoTitle) {
            $this->updateBody->replaceFirstLevelTitle($body, $seoTitle);
        }

        if ($seoMetaTitle) {
            $this->updateBody->replaceMetaTitle($body, $seoMetaTitle);
        }

        if ($seoMetaKeywords) {
            $this->updateBody->replaceMetaKeywords($body, $seoMetaKeywords);
        }

        if ($seoMetaDescription) {
            $this->updateBody->replaceMetaDescription($body, $seoMetaDescription);
        }

        if ($robots) {
            $this->updateBody->replaceRobots($body, $robots);
        }

        return $body;
    }

    /**
     *  Check if category page
     *
     * @return bool
     */
    protected function isCategory()
    {
        if (($category = $this->registry->registry('current_category'))
            && $category->getId() && !$this->registry->registry('current_product')) {
            return true;
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
        $this->modifyHtmlResponseMeta($observer);
    }
}
