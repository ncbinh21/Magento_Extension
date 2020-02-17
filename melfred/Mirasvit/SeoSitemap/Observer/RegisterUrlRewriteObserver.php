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



namespace Mirasvit\SeoSitemap\Observer;

use Magento\Framework\Event\ObserverInterface;

class RegisterUrlRewriteObserver implements ObserverInterface
{
    /**
     * @var \Mirasvit\SeoSitemap\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Core\Helper\Urlrewrite
     */
    protected $urlRewrite;

    /**
     * @param \Mirasvit\SeoSitemap\Model\Config $config
     * @param \Mirasvit\Core\Helper\UrlRewrite  $mstcoreUrlrewrite
     */
    public function __construct(
        \Mirasvit\SeoSitemap\Model\Config $config,
        \Mirasvit\Core\Helper\UrlRewrite $mstcoreUrlrewrite
    ) {
        $this->config = $config;
        $this->urlRewrite = $mstcoreUrlrewrite;
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
        $this->urlRewrite->setRewriteMode('SEOSITEMAP', true);
        $this->urlRewrite->registerBasePath('SEOSITEMAP', $this->config->getFrontendSitemapBaseUrl());
        $this->urlRewrite->registerPath('SEOSITEMAP', 'MAP', '', 'seositemap_index_index');
    }
}
