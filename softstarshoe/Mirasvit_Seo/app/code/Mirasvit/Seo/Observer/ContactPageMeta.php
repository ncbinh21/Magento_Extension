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



namespace Mirasvit\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;

class ContactPageMeta extends \Magento\Framework\Model\AbstractModel implements ObserverInterface
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
     * @param \Mirasvit\Seo\Helper\Data   $seoData
     * @param \Mirasvit\Seo\Helper\UpdateBody $updateBody,
     * @param \Magento\Framework\Registry $registry
     * @param \Mirasvit\Seo\Observer\Robots $robots
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Mirasvit\Seo\Helper\Data $seoData,
        \Mirasvit\Seo\Helper\UpdateBody $updateBody,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Observer\Robots $robots,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->seoData = $seoData;
        $this->updateBody = $updateBody;
        $this->registry = $registry;
        $this->robots = $robots;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param string $e
     * @param bool|Magento\Framework\App\Response\Http $response
     *
     * @return bool|\Magento\Framework\App\Response\Http
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function modifyHtmlResponseTitle($e, $response = false)
    {
        $applyForCache = ($response) ? true : false;
        $action = $this->seoData->getFullActionCode();
        $isModifyAllowed = false;


        if ($action == 'blog_tag_view'
            && $this->moduleManager->isEnabled('Magefan_Blog')) {
                $isModifyAllowed = true;
        }

        if (($action != 'contact_index_index' && !$isModifyAllowed)
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

        $response->setBody($body);

        if ($applyForCache) {
            return $response;
        }
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
        $this->modifyHtmlResponseTitle($observer);
    }
}
