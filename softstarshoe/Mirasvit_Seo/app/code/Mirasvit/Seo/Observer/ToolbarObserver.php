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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Seo\Model\Config as Config;
use Mirasvit\Seo\Helper\Data as DataHelper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutInterface;

class ToolbarObserver implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @param Config          $config
     * @param DataHelper      $dataHelper
     * @param Context         $context
     */
    public function __construct(
        Config $config,
        DataHelper $dataHelper,
        Context $context
    ) {
        $this->config = $config;
        $this->dataHelper = $dataHelper;
        $this->urlManager = $context->getUrlBuilder();
        $this->storeManager = $context->getStoreManager();
        $this->context = $context;
        $this->layout = $context->getLayout();
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->dataHelper->isIgnoredActions()
            || !$this->config->isInfoEnabled($this->storeManager->getStore())
            || strpos($this->urlManager->getCurrentUrl(), 'checkout')
            || strpos($this->urlManager->getCurrentUrl(), 'customer/account')
            || $this->context->getRequest()->getParam('_')
            || $this->context->getRequest()->getParam('is_ajax')
            || $this->context->getRequest()->getParam('isAjax')
        ) {
            return;
        }

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $observer->getEvent()->getData('response');
        $body = $response->getBody();

        /** @var \Mirasvit\Seo\Block\Toolbar $toolbar */
        $toolbar = $this->layout->createBlock('Mirasvit\Seo\Block\Toolbar');
        $toolbar->setBody($body);

        $response->setBody($body . $toolbar->toHtml());
    }
}
