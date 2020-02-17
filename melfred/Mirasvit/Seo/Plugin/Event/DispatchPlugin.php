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



namespace Mirasvit\Seo\Plugin\Event;

use Magento\Framework\Registry;
use Magento\Framework\Event\Observer;
use Mirasvit\Seo\Model\Config as Config;

/**
 * Ignore events controller_front_send_response_before if page load from FPC cache
 * It is fix slow cache load because of controller_front_send_response_before action
 */
class DispatchPlugin
{
    /* Observer model factory
     *
     * @var \Magento\Framework\Event\ObserverFactory
     */
    protected $_observerFactory;

    /**
     * Application state
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var bool
     */
    static protected $isInfoBlockEnabled = null;

    /**
     * @param \Magento\Framework\App\RequestInterface                     $request
     * @param \Mirasvit\Seo\Api\Config\ProductUrlTemplateConfigInterface  $productUrlTemplateConfig
     * @param Registry                                                    $registry
     * @param Config                                                      $config
     * @param \Magento\Store\Model\StoreManagerInterface                  $storeManager
     */
    public function __construct(
        \Magento\Framework\Event\ObserverFactory $observerFactory,
        \Magento\Framework\App\State $appState,
        Registry $registry,
        Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
         $this->registry = $registry;
         $this->_observerFactory = $observerFactory;
         $this->_appState = $appState;
         $this->config = $config;
         $this->storeManager = $storeManager;
    }

    /**
     * Don't use events if page from cache
     *
     * @param Magento\Framework\Event\Invoker\InvokerDefault\Interceptor $subject
     * @param array $configuration
     * @param Magento\Framework\Event\Observer $observer
     *
     * @return array
     */
    public function beforeDispatch($subject, array $configuration, Observer $observer)
    {
        if ($this->registry->registry('m__is_hit_page_cache_plugin')) {
            $controllerFrontSendResponseBeforeObservers = ['m_seo_observer_http_response_send_before',
                'm_seo_observer_toolbar',
                'm_seo_snippets_observer',
                'm_seo_contact_page_observer'
            ];
            if (isset($configuration['name'])
                && in_array($configuration['name'], $controllerFrontSendResponseBeforeObservers)
                && !$this->isInfoBlockEnabled()) {
                $configuration['disabled'] = true;
            }

            // echo 'IS_HIT<br/>';
        }


        return [$configuration, $observer];
    }

    /**
     * Check if SEO Toolbar is enabled
     *
     * @return bool
     */
    protected function isInfoBlockEnabled()
    {
        if (self::$isInfoBlockEnabled !== null) {
            return self::$isInfoBlockEnabled;
        }

        if ($this->config->isInfoEnabled($this->storeManager->getStore())) {
            self::$isInfoBlockEnabled = true;
        } else {
            self::$isInfoBlockEnabled = false;
        }

        return self::$isInfoBlockEnabled;
    }
}