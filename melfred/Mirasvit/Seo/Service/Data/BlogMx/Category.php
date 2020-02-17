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

class Category implements \Mirasvit\Seo\Api\Data\BlogMx\CategoryInterface
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
     * @return \Mirasvit\Blog\Model\ResourceModel\Post\Collection
     */
    public function getPostList()
    {
        return $this->objectManager->get('\Mirasvit\Blog\Block\Post\PostList')->getPostCollection();
    }

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getContent($post)
    {
        if ($shortContent = $post->getShortContent()) {
            return $shortContent;
        }

        return $post->getContent();
    }

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getPreparedContent($post)
    {
        $content = $this->getContent($post);
        $this->blogMx->getPreparedContent($content);

        return $content;
    }

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getDatePublished($post)
    {
        return $this->blogMx->getDatePublished($post->getCreatedAt());
    }

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getAuthorName($post)
    {
        return $post->getAuthor()->getName();
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
     * @return int
     */
    public function getImageWith()
    {
        return $this->blogMx->getImageWith($this->getLogoUrl());
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return $this->blogMx->getImageHeight($this->getLogoUrl());
    }
}
