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

interface PostInterface
{
    /**
     * @return \Mirasvit\Blog\Model\Config
     */
    public function getConfig();

    /**
     * @return \Mirasvit\Blog\Model\Post
     */
    public function getPost();

    /**
     * @return string
     */
    public function getHeadline();

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getPreparedContent();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getKeywords();

    /**
     * @return string
     */
    public function getDatePublished();

    /**
     * @return string
     */
    public function getAuthorName();

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
    public function getImageUrl();

    /**
     * @return int
     */
    public function getImageWith();

    /**
     * @return int
     */
    public function getImageHeight();
}

