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



namespace Mirasvit\Seo\Service\Data\BlogMx;

class Post implements \Mirasvit\Seo\Api\Data\BlogMx\PostInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Mirasvit\Seo\Helper\BlogMx
     */
    protected $blogMx;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Mirasvit\Seo\Helper\Data $seoData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mirasvit\Seo\Helper\BlogMx $blogMx
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Seo\Helper\BlogMx $blogMx,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->seoData = $seoData;
        $this->storeManager = $storeManager;
        $this->blogMx = $blogMx;
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Mirasvit\Blog\Model\Config
     */
    public function getConfig()
    {
        return $this->objectManager->get('\Mirasvit\Blog\Model\Config');
    }

    /**
     * @return \Mirasvit\Blog\Model\Post
     */
    public function getPost()
    {
        return $this->objectManager->get('\Mirasvit\Blog\Block\Post\View')->getPost();
    }

    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->getPost() ? $this->getPost()->getName() : $this->getConfig()->getBlogName();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getPost()->getContent();
    }

    /**
     * @return string
     */
    public function getPreparedContent()
    {
        $content = $this->getContent();
        $this->blogMx->getPreparedContent($content);

        return $content;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getPost()->getUrl();
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->getPost()
            ? ($this->getPost()->getMetaKeywords() ? $this->getPost()->getMetaKeywords() : $this->getPost()->getName())
            : $this->getConfig()->getBaseMetaKeywords();
    }

    /**
     * @return string
     */
    public function getDatePublished()
    {
        return $this->blogMx->getDatePublished($this->getPost()->getCreatedAt());
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->getPost()->getAuthor()->getName();
    }

    /**
     * @return string
     */
    public function getPublisherName()
    {
        return $this->blogMx->getPublisherName();
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->blogMx->getLogoUrl();
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $this->getContent(), $image);
        if (isset($image[0][0]) && $image[0][0]) {
            $imageUrl = str_replace(['src="', 'src=\''], '', $image[0][0]);
        } else {
            $imageUrl = $this->getLogoUrl();
        }

        return $imageUrl;
    }

    /**
     * @return int
     */
    public function getImageWith()
    {
        return $this->blogMx->getImageWith($this->getImageUrl());
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return $this->blogMx->getImageHeight($this->getImageUrl());
    }
}
