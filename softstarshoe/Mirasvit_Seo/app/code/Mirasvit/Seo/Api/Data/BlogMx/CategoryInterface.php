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


namespace Mirasvit\Seo\Api\Data\BlogMx;

interface CategoryInterface
{
    /**
     * @return \Mirasvit\Blog\Model\ResourceModel\Post\Collection
     */
    public function getPostList();

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getContent($post);

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getPreparedContent($post);

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getDatePublished($post);

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getAuthorName($post);

    /**
     * @return string
     */
    public function getPublisherName();

    /**
     * @return string
     */
    public function getLogoUrl();

    /**
     * @return string
     */
    public function getImageWith();

    /**
     * @return string
     */
    public function getImageHeight();
}

