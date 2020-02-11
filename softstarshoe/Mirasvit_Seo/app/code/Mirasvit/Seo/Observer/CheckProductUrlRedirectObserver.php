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

class CheckProductUrlRedirectObserver implements ObserverInterface
{
    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Seo\Helper\Redirect
     */
    protected $seoRedirect;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param \Mirasvit\Seo\Model\Config                                     $config
     * @param \Mirasvit\Seo\Helper\Redirect                                  $seoRedirect
     * @param \Magento\Framework\App\Request\Http                            $request
     * @param \Magento\Framework\UrlInterface                                $urlManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Mirasvit\Seo\Helper\Redirect $seoRedirect,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->config = $config;
        $this->seoRedirect = $seoRedirect;
        $this->request = $request;
        $this->urlManager = $urlManager;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * If we use SHORT or LONG url format we do a redirection of other url format
     *
     * @param string $e
     *
     * @return void
     */
    public function checkProductUrlRedirect($e)
    {
        $urlFormat = $this->config->getProductUrlFormat();

        if ($urlFormat != Config::URL_FORMAT_SHORT &&
            $urlFormat != Config::URL_FORMAT_LONG) {
            return;
        }

        if ($this->request->isAjax()) {
            return;
        }
        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $e->getControllerAction();

        if ($controller->getRequest()->getModuleName() != 'catalog') { //we redirect only for catalog
            return;
        }
        if ($controller->getRequest()->getControllerName() != 'product') { //we redirect only for catalog
            return;
        }
        if ($controller->getRequest()->getActionName() != 'view') {
            //we redirect only from products page. not from images views.
            return;
        }

        $url = ltrim($controller->getRequest()->getRequestString(), '/');
        $product = $e->getProduct();
        //we need this because we need to load url rewrites
        //maybe its possible to optimize
        $products = $this->productCollectionFactory->create()
            ->addFieldToFilter('entity_id', $product->getId());
        $product = $products->getFirstItem();
        $productUrl = str_replace($this->urlManager->getBaseUrl(), '', $product->getProductUrl());
        $productUrl = $this->seoRedirect->getUrlWithCorrectEndSlash($productUrl);

        if ($productUrl != $url) {
            $url = $product->getProductUrl();
            $url = $this->seoRedirect->getUrlWithCorrectEndSlash($url);
            $this->seoRedirect->redirect($controller->getResponse(), $url);
        }
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
        $this->checkProductUrlRedirect($observer);
    }
}
