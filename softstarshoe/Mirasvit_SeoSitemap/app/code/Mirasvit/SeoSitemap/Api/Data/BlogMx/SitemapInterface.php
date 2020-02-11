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


namespace Mirasvit\SeoSitemap\Api\Data\BlogMx;

interface SitemapInterface
{
    const CHANGEFREQ = 'daily';
    const PRIORITY = '0.5';

    /**
     * @return \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    public function getPostCollectionFactory();

    /**
     * @return string
     */
    public function getBaseRoute();

    /**
     * @return array
     */
    public function getCategoryTree();

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getBlogItem();

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getCategoryItems();

    /**
     * @param int $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getPostItems($storeId);
}

