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



namespace Mirasvit\SeoSitemap\Plugin;

class SitemapLinkPlugin
{
    /**
     * Change sitemap url info
     *
     * @param \Magento\Sitemap\Block\Adminhtml\Grid\Renderer\Link $subject
     * @return string
     */
    public function afterRender($subject , $url)
    {
        return str_replace('/pub/', '/', $url);
    }
}
