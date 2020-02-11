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
use Mirasvit\Seo\Model\Config as Config;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Opengraph implements ObserverInterface
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $catalogImage;

    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;

    /**
     * @var \Mirasvit\Seo\Api\Config\BlogMxInterface
     */
    protected $blogMx;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Store\Model\Information
     */
    protected $_storeInfo;

    /**
     * @param \Magento\Catalog\Model\ProductFactory      $productFactory
     * @param \Mirasvit\Seo\Model\Config                 $config
     * @param \Magento\Catalog\Helper\Image              $catalogImage
     * @param \Mirasvit\Seo\Helper\Data                  $seoData
     * @param \Magento\Framework\UrlInterface            $urlManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Backend\Model\Auth                $auth
     * @param \Magento\Framework\ObjectManagerInterface  $objectManager
     * @param \Magento\Store\Model\Information  $_storeInfo
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Mirasvit\Seo\Model\Config $config,
        \Magento\Catalog\Helper\Image $catalogImage,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Auth $auth,
        \Mirasvit\Seo\Api\Config\BlogMxInterface $blogMx,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\Information $storeInfo
    ) {
        $this->productFactory = $productFactory;
        $this->config = $config;
        $this->catalogImage = $catalogImage;
        $this->seoData = $seoData;
        $this->urlManager = $urlManager;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->auth = $auth;
        $this->blogMx = $blogMx;
        $this->objectManager = $objectManager;
        $this->_storeInfo = $storeInfo;
    }

    /**
     * @param \Magento\Framework\Event\Observer $e
     * @param bool|Magento\Framework\App\Response\Http $response
     *
     * @return bool|\Magento\Framework\App\Response\Http
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function modifyHtmlResponse($e, $response = false)
    {
        $tags = [];

        $applyForCache = ($response) ? true : false;

        if ((!$this->config->getCategoryOpenGraph() && !$this->config->isCmsOpenGraphEnabled())
            || $this->seoData->isIgnoredActions()
            || $this->seoData->isIgnoredUrls()
            || $this->registry->registry('current_product')
            || $this->auth->getUser()
            || (!$applyForCache && !is_object($e))) {
                return $response;
        }

        if (!$applyForCache) {
            $response = $e->getResponse();
        }

        $body = $response->getBody();

        if (!$this->hasDoctype(trim($body))) {
            return $response;
        }

        $label = '<!-- mirasvit open graph begin -->';
        if (strpos($body, $label) !== false) {
            return $response;
        }

        $fullActionCode = $this->seoData->getFullActionCode();
        if (($this->config->getCategoryOpenGraph() && $fullActionCode == 'catalog_category_view')
            || ($this->config->isCmsOpenGraphEnabled() && $fullActionCode == 'cms_page_view')
            || ($this->config->isCmsOpenGraphEnabled() && $fullActionCode == 'cms_index_index')
            || ($this->blogMx->isOgEnabled() && in_array($fullActionCode, $this->blogMx->getActions())) ) {
            $tags[] = $label;
            $tags[] = $this->createMetaTag('type', 'website');
            $tags[] = $this->createMetaTag('url', $this->urlManager->getCurrentUrl());
            preg_match('/<title>(.*?)<\\/title>/i', $body, $titleArray);
            if (isset($titleArray[1])) {
                $tags[] = $this->createMetaTag('title', $titleArray[1]);
            }
            if ($logo = $this->seoData->getLogoUrl()) {
                $tags['image'] = $this->createMetaTag('image', $logo);
            }
            if ($fullActionCode == 'catalog_category_view'
                && ($productCollection = $this->registry->registry('category_product_for_snippets'))
                && $this->config->getCategoryOpenGraph() == Config::OPENGRAPH_PRODUCT_IMAGE) {
                if ($productCollection->count()) {
                    $tags['image'] = $this->createMetaTag(
                        'image',
                        $this->catalogImage->init($productCollection->getFirstItem(), 'product_base_image')->getUrl()
                    );
                }
            }

            if ($fullActionCode == 'blog_post_view'
                && ($featuredImageUrl = $this->objectManager->get('\Mirasvit\Blog\Block\Post\View')
                    ->getPost()->getFeaturedImageUrl())) {
                        $tags['image'] = $this->createMetaTag('image', $featuredImageUrl);
            }

            if ($storeName = $this->_storeInfo->getStoreInformationObject($this->storeManager->getStore())->getName()) {
                $tags[] = $this->createMetaTag('site_name', $storeName);
            }
            preg_match('/meta name\\=\\"description\\" content\\=\\"(.*?)\\"\\/\\>/i', $body, $descriptionArray);
            if (isset($descriptionArray[1])) {
                $tags[] = $this->createMetaTag('description', $descriptionArray[1]);
            }
        }

        if ($tags) {
            $tags[] = '<!-- mirasvit open graph end -->';
            $tags = array_unique($tags);
            $search = [
                '<head>',
                '<head >',
            ];
            $replace = [
                "<head>\n".implode($tags, "\n"),
                "<head >\n".implode($tags, "\n"),
            ];
            $body = str_replace($search, $replace, $body);
        }

        $response->setBody($body);

        if ($applyForCache) {
            return $response;
        }
    }

    /**
     * @param string $property
     * @param string $value
     * @return string
     */
    protected function createMetaTag($property, $value)
    {
        $value = $this->seoData->cleanMetaTag($value);

        return "<meta property=\"og:$property\" content=\"$value\"/>";
    }

    /**
     * @param string $body
     * @return bool
     */
    protected function hasDoctype($body)
    {
        $doctypeCode = ['<!doctype html', '<html', '<?xml'];
        foreach ($doctypeCode as $doctype) {
            if (stripos($body, $doctype) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->modifyHtmlResponse($observer);
    }
}
