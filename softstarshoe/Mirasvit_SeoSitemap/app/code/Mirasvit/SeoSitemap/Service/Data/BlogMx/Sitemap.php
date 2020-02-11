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



namespace Mirasvit\SeoSitemap\Service\Data\BlogMx;

class Sitemap implements \Mirasvit\SeoSitemap\Api\Data\BlogMx\SitemapInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    public function getPostCollectionFactory()
    {
        return $this->objectManager->get('\Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory');
    }

    /**
     * @return string
     */
    public function getBaseRoute()
    {
        return $this->objectManager->get('\Mirasvit\Blog\Model\Config')->getBaseRoute();
    }

    /**
     * @return array
     */
    public function getCategoryTree()
    {
        return $this->objectManager->get('\Mirasvit\Blog\Block\Sidebar\CategoryTree')->getTree();
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getBlogItem()
    {
        $baseUrlCollection = new \Magento\Framework\DataObject(
            [
                'url' => $this->getBaseRoute(),
            ]
        );

        $sitemapItem = new \Magento\Framework\DataObject(
            [
                'changefreq' => self::CHANGEFREQ,
                'priority' => self::PRIORITY,
                'collection' => ['Homepage' => $baseUrlCollection],
            ]
        );

        return $sitemapItem;
    }

    /**
     * @return \Magento\Framework\DataObject|bool
     */
    public function getCategoryItems()
    {
        $categoryTree = $this->getCategoryTree();
        if (!$categoryTree) {
            return false;
        }

        foreach ($categoryTree as $category) {
            $categoryCollection[] = new \Magento\Framework\DataObject(
                [
                    'name' => $category->getName(),
                    'url' => $this->getBaseRoute() . '/' . $category->getUrlKey(),
                ]
            );
        }

        $sitemapItem = new \Magento\Framework\DataObject(
            [
                'changefreq' => self::CHANGEFREQ,
                'priority' => self::PRIORITY,
                'collection' => $categoryCollection,
            ]
        );

        return $sitemapItem;
    }

    /**
     * @param int $storeId
     * @return \Magento\Framework\DataObject|bool
     */
    public function getPostItems($storeId)
    {
        $postCollectionFactory = $this->getPostCollectionFactory()->create()
            ->addStoreFilter($storeId)
            ->addAttributeToSelect(['name', 'url_key'])
            ->addVisibilityFilter();

        if ($postCollectionFactory->getSize() <= 0) {
            return false;
        }

        foreach ($postCollectionFactory as $post) {
            $postCollection[] = new \Magento\Framework\DataObject(
                [
                    'name' => $post->getName(),
                    'url' => $this->getBaseRoute() . '/' . $post->getUrlKey(),
                ]
            );
        }

        $sitemapItem = new \Magento\Framework\DataObject(
            [
                'changefreq' => self::CHANGEFREQ,
                'priority' => self::PRIORITY,
                'collection' => $postCollection,
            ]
        );

        return $sitemapItem;
    }
}
