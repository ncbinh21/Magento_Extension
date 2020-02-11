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
use \Mirasvit\Seo\Model\Config as Config;

class SeoDataObserver implements ObserverInterface
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
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Mirasvit\Seo\Helper\Data                        $seoData
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface        $objectManager
     * @param \Magento\Catalog\Helper\Output                   $catalogOutput
     */
    public function __construct(
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Helper\Output $catalogOutput
    ) {
        $this->seoData = $seoData;
        $this->registry = $registry;
        $this->design = $context->getDesignPackage();
        $this->context = $context;
        $this->objectManager = $objectManager;
        $this->catalogOutput = $catalogOutput;
    }

    /**
     * @var bool
     */
    protected $isProductTitlePrinted = false;
    /**
     * @var bool
     */
    protected $isProductShortDescriptionPrinted = false;
    /**
     * @var bool
     */
    protected $isProductDescriptionPrinted = false;
    /**
     * @var Array
     */
    protected $processedProductAttributes = ['name', 'short_description', 'description'];

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Mirasvit\Seo\Model\SeoData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applyMeta(\Magento\Framework\Event\Observer $observer)
    {
        if ((!$seo = $this->seoData->getCurrentSeo())
            || $this->seoData->isIgnoredActions()) {
            return $seo;
        }
        $pageConfig = $this->context->getPageConfig();

        if ($seo->getMetaTitle()) {
            $pageConfig->getTitle()->set($this->seoData->cleanMetaTag($seo->getMetaTitle()));
        }

        if ($seo->getMetaDescription()) {
            //Removes HTML tags and unnecessary whitespaces from Description Meta Tag
            $description = $seo->getMetaDescription();
            $description = $this->seoData->cleanMetaTag($description);
            $pageConfig->setDescription($description);
        }

        if ($seo->getMetaKeywords()) {
            $pageConfig->setKeywords($this->seoData->cleanMetaTag($seo->getMetaKeywords()));
        }

        //$layout = $this->objectManager->get('\Magento\Framework\View\LayoutInterface');
        $layout = $this->context->getLayout();
        if ($seo->getTitle() && $pageMainTitle = $layout->getBlock('page.main.title')) {
            $pageMainTitle->setPageTitle($seo->getTitle());
        }
    }

    /**
     * @param string $outputHelper
     * @param string $outputHtml
     * @param string $params
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function categoryAttribute($outputHelper, $outputHtml, $params)
    {
        if (!$this->registry->registry('current_category')) {
            return $outputHtml;
        }

        $seo = $this->seoData->getCurrentSeo();
        switch ($params['attribute']) {
            case 'name':
                $outputHtml = $seo->getTitle();
                break;
            case 'description':
                break;
        }

        return $outputHtml;
    }

    /**
     * @param string $outputHelper
     * @param string $outputHtml
     * @param string $params
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function productAttribute($outputHelper, $outputHtml, $params)
    {
        if (!$currentProduct = $this->registry->registry('current_product')) {
            return $outputHtml;
        }

        if (!in_array($params['attribute'], $this->processedProductAttributes)) {
            return $outputHtml;
        }

        if ($params['attribute'] == 'name' && $this->isProductTitlePrinted) {
            return $outputHtml;
        }

        if ($params['attribute'] == 'short_description' && $this->isProductShortDescriptionPrinted) {
            return $outputHtml;
        }

        if ($params['attribute'] == 'description' && $this->isProductDescriptionPrinted) {
            return $outputHtml;
        }

        $seo = $this->seoData->getCurrentSeo();
        switch ($params['attribute']) {
            case 'name':
                if ($currentProduct->getName() != $seo->getTitle()) {
                    $this->isProductTitlePrinted = true;
                    $outputHtml = $seo->getTitle();
                }
                break;
            case 'short_description':
                $this->isProductShortDescriptionPrinted = true;

                if ($shortDescription = $seo->getShortDescription()) {
                    $outputHtml = $shortDescription;
                }

                if ($seo->getDescription()
                    && $seo->getDescriptionPosition() == Config::UNDER_SHORT_DESCRIPTION) {
                        $outputHtml .= $seo->getDescription();
                }
                break;
            case 'description':
                $this->isProductDescriptionPrinted = true;

                if ($fullDescription = $seo->getFullDescription()) {
                    $outputHtml = $fullDescription;
                }

                if ($seo->getDescription()
                    && $seo->getDescriptionPosition() == Config::UNDER_FULL_DESCRIPTION) {
                        $outputHtml .= $seo->getDescription();
                }
                break;
        }

        return $outputHtml;
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
        $this->applyMeta($observer);
        $this->catalogOutput->addHandler('productAttribute', $this);
        $this->catalogOutput->addHandler('categoryAttribute', $this);
    }
}
