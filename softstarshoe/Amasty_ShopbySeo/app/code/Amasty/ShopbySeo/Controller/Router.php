<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\ShopbySeo\Controller;

use Amasty\ShopbySeo\Helper\Url;
use Amasty\ShopbySeo\Helper\UrlParser;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Framework\Module\Manager;

/**
 * Class Router
 * @package Amasty\ShopbySeo\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    const INDEX_ALIAS       = 1;
    const INDEX_CATEGORY    = 2;

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var Url
     */
    protected $urlHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var UrlParser
     */
    protected $urlParser;

    /**
     * @var UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * Router constructor.
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\Registry $registry
     * @param UrlParser $urlParser
     * @param Url $urlHelper
     * @param UrlFinderInterface $urlFinder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\Registry $registry,
        UrlParser $urlParser,
        Url $urlHelper,
        UrlFinderInterface $urlFinder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Manager $moduleManager
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->registry = $registry;
        $this->urlHelper = $urlHelper;
        $this->urlParser = $urlParser;
        $this->urlFinder = $urlFinder;
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|void
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->urlHelper->isSeoUrlEnabled()) {
            return;
        }

        $identifier = trim($request->getPathInfo(), '/');
        if (!preg_match('@^(.*)/([^/]+)@', $identifier, $matches)) {
            return;
        }

        $seoPart = $this->urlHelper->removeCategorySuffix($matches[self::INDEX_CATEGORY]);
        $suffixMoved = $seoPart != $matches[self::INDEX_CATEGORY];
        $alias = $matches[self::INDEX_ALIAS];

        $params = $this->urlParser->parseSeoPart($seoPart);
        if ($params === false) {
            return $this->createSeoRedirect($request);
        }

        /**
         * for brand pages with key, e.g. /brand/adidas
         */
        $matchedAlias = null;
        if ($this->moduleManager->isEnabled('Amasty_ShopbyBrand')) {
            $brandKey = trim($this->scopeConfig->getValue(
                'amshopby_brand/general/url_key',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));
            if ($brandKey == $alias) {
                $matchedAlias = $alias;
            }
        }

        /* For regular seo category */
        if (!$matchedAlias) {
            $category = $suffixMoved ? $this->urlHelper->addCategorySuffix($alias) : $alias;
            $rewrite = $this->urlFinder->findOneByData([
                UrlRewrite::REQUEST_PATH => $category,
            ]);
            if (!$rewrite) {
                $rewrite = $this->urlFinder->findOneByData([
                    UrlRewrite::REQUEST_PATH => $category . '/',
                ]);
            }

            if ($rewrite) {
                $matchedAlias = $category;
            }
        }

        if ($matchedAlias) {
            $this->registry->register('amasty_shopby_seo_parsed_params', $params);
            $request->setParams($params);
            $request->setPathInfo($matchedAlias);
            return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
        }
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|\Magento\Framework\App\ActionInterface
     */
    private function createSeoRedirect(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->urlHelper->isSeoRedirectEnabled()) {
            $url = $this->urlHelper->seofyUrl($request->getUri()->toString());
            if (strcmp($url, $request->getUri()->toString()) === 0) {
                return false;
            }
            $this->_response->setRedirect($url, \Zend\Http\Response::STATUS_CODE_301);
            $request->setDispatched(true);
            return $this->actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
        }
        return false;
    }
}
