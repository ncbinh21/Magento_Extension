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
use Mirasvit\Seo\Api\Service\Snippet\ProductSnippetInterface;
use Mirasvit\Seo\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Mirasvit\Seo\Api\Config\ProductSnippetConfigInterface;
use Mirasvit\Seo\Api\Service\PageDetectorInterface;
use Mirasvit\Seo\Model\Config;

class Snippet implements ObserverInterface
{
    /**
     * @var bool
     */
    public $applied = false;

    /**
     * @var bool
     */
    protected $appliedHomePage = false;

    /**
     * Snippet constructor.
     * @param Data $seoData
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     * @param ProductSnippetConfigInterface $productSnippetConfig
     * @param PageDetectorInterface $pageDetector
     * @param Config $config
     */
    public function __construct(
        Data $seoData,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        ProductSnippetConfigInterface $productSnippetConfig,
        PageDetectorInterface $pageDetector,
        Config $config
    ) {
        $this->seoData = $seoData;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
        $this->productSnippetConfig = $productSnippetConfig;
        $this->pageDetector = $pageDetector;
        $this->config = $config;
    }

    /**
     * @param string $e
     * @param bool|Magento\Framework\App\Response\Http $response
     *
     * @return bool|Magento\Framework\App\Response\Http
     */
    public function addProductSnippets($e, $response = false)
    {
        $product = $this->registry->registry('current_product');

        $applyForCache = ($response) ? true : false;

        if (!$this->appliedHomePage
            && $this->pageDetector->isHomePage()
            && $this->config->isDeleteWrongRichSnippetsForHomePage($this->storeManager->getStore()->getId())) {
            return $this->deleteHomePageWrongSnippets($e, $response, $applyForCache);
        }

        if ($applyForCache && (!$product
            || $this->applied
            || $this->seoData->isIgnoredActions())) {
            return $response;
        } elseif (!$applyForCache && (!is_object($e)
            || !$product
            || $this->applied
            || $this->seoData->isIgnoredActions())) {
            return $response;
        }

        if (!$applyForCache) {
            $response = $e->getResponse();
        }

        $body = $response->getBody();

        $this->registry->register(ProductSnippetInterface::DESCRIPTION_REGISTER_TAG,
            $this->getMetaDescription($body),
            true
        );

        if ($this->productSnippetConfig->isDeleteWrongRichSnippets()) {
            $body = $this->deleteWrongSnippets($body);
        }

        $response->setBody($body);

        $this->applied = true;

        if ($applyForCache) {
            return $response;
        }
    }

    /**
     * @param $e
     * @param \Magento\Framework\App\Response\Http $response
     * @param bool $applyForCache
     */
    protected function deleteHomePageWrongSnippets($e, $response, $applyForCache)
    {
        if (!$applyForCache) {
            $response = $e->getResponse();
        }

        $body = $response->getBody();
        $body = $this->deleteWrongSnippets($body);
        $response->setBody($body);

        $this->appliedHomePage = true;

        if ($applyForCache) {
            return $response;
        }
    }

    /**
     * @param string $html
     * @return array|string|null
     */
    protected function deleteWrongSnippets($html)
    {
        $breadcumbPattern = '/\\<span class="breadcrumbsbefore"\\>\\<\\/span\\>(.*?)'
                            .'\\<span class="breadcrumbsafter"\\>\\<\\/span\\>/ims';
        preg_match($breadcumbPattern, $html, $breadcumb);
        $pattern = ['/itemprop="(.*?)"/ims',
                        '/itemprop=\'(.*?)\'/ims',
                        '/itemtype="(.*?)"/ims',
                        '/itemtype=\'(.*?)\'/ims',
                        '/itemscope="(.*?)"/ims',
                        '/itemscope=\'(.*?)\'/ims',
                        '/itemscope=\'\'/ims',
                        '/itemscope=""/ims',
                        '/itemscope\s/ims',
                        ];
        $html = preg_replace($pattern, '', $html);
        if (isset($breadcumb[1]) && $breadcumb[1]) {
            $html = preg_replace($breadcumbPattern, $breadcumb[1], $html);
        }

        return $html;
    }

    /**
     * @param string $html
     * @return array|string|null
     */
    protected function getMetaDescription($body)
    {
        $description = '';
        preg_match('/meta name\\=\\"description\\" content\\=\\"(.*?)\\"\\/\\>/i', $body, $descriptionArray);
        if (isset($descriptionArray[1])) {
            $description = trim($descriptionArray[1]);
        }

        return $description;
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
        $this->addProductSnippets($observer);
    }
}
