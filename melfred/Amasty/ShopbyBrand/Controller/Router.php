<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Controller;

use Amasty\ShopbyBrand\Helper\Data;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */

    private $response;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */

    private $scopeConfig;

    /**
     * @var  Manager
     */
    private $moduleManager;

    /**
     * @var  \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var string
     */
    private $brandCode;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    private $brandHelper;

    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestFactory $requestFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param Manager $moduleManager
     * @param Data $brandHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestFactory $requestFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Module\Manager $moduleManager,
        \Amasty\ShopbyBrand\Helper\Data $brandHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
        $this->registry = $registry;
        $this->brandCode = $this->scopeConfig
            ->getValue('amshopby_brand/general/attribute_code', ScopeInterface::SCOPE_STORE);
        $this->urlBuilder = $urlBuilder;
        $this->brandHelper = $brandHelper;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = $this->getPath($request);
        $brandParams = $this->matchBrandParams($identifier);

        if (!empty($brandParams)) {
            /*
             * There is no redirect to single brand, because this extension doesn't support
             * multiple filter values. It means, that situation when someone will request two brands is impossible
             */
            $request->setModuleName('ambrand')->setControllerName('index')->setActionName('index');
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            $params = array_merge($request->getParams(), $brandParams);
            $request->setParams($params);
            return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
        }

        return null;
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getPath(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $suffix = $this->scopeConfig
            ->getValue('catalog/seo/category_url_suffix', ScopeInterface::SCOPE_STORE);
        if ($suffix === '/') {
            $identifier .= '/';
        }
        return $identifier;
    }

    /**
     * @param string $identifier
     * @return array
     */
    public function matchBrandParams($identifier)
    {
        $brandPageUrlKey = trim($this->scopeConfig->getValue(
            \Amasty\ShopbyBrand\Helper\Data::PATH_BRAND_URL_KEY,
            ScopeInterface::SCOPE_STORE
        ));
        if (!empty($brandPageUrlKey) && strpos($identifier, $brandPageUrlKey) !== false) {
            $identifier = ltrim(str_replace($brandPageUrlKey, '', $identifier), '/');
        }
        $aliases = $this->brandHelper->getBrandAliases();
        foreach ($aliases as $optionId => $alias) {
            if (!strcasecmp($alias, $identifier)) {
                return [$this->brandHelper->getBrandAttributeCode() => $optionId];
            }
        }
        return [];
    }
}
