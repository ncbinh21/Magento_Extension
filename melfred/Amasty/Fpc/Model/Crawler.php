<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Model\Queue\Page;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Group;
use Magento\Framework\App\Response\Http;
use Magento\Store\Model\StoreCookieManager;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use \Magento\Framework\App\Http\Context as HttpContext;
use Amasty\Fpc\Helper\Http as HttpHelper;
use Magento\Framework\App\ProductMetadataInterface;

class Crawler
{
    const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36';

    const SESSION_COOKIE = 'PHPSESSID';
    const SESSION_NAME = 'amasty-fpc-crawler';

    protected $curl;
    protected $headers = [];
    protected $cookies = [];

    /**
     * @var
     */
    protected $defaultCurrency;

    /**
     * @var Log
     */
    private $crawlerLog;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var \Magento\PageCache\Model\Cache\Type
     */
    private $fpc;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        Log $crawlerLog,
        StoreManagerInterface $storeManager,
        Config $config,
        LoggerInterface $logger,
        \Magento\Framework\App\Http\ContextFactory $httpContextFactory,
        \Magento\PageCache\Model\Cache\Type $fpc,
        ProductMetadataInterface $productMetadata
    ) {
        $this->crawlerLog = $crawlerLog;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->logger = $logger;
        $this->httpContext = $httpContextFactory->create();
        $this->defaultCurrency = $this->storeManager->getWebsite()->getDefaultStore()->getDefaultCurrency()->getCode();
        $this->fpc = $fpc;
        $this->productMetadata = $productMetadata;
    }

    public function processPage(Page $page, $customerGroup, $storeId, $currency)
    {
        $this->curl = curl_init();

        $this->setParams($customerGroup, $storeId, $currency);

        $loadStart = microtime(true);
        $status = $this->request($page->getUrl());
        $loadTime = microtime(true) - $loadStart;

        $this->crawlerLog->add([
            'url' => $page->getUrl(),
            'customer_group' => $customerGroup,
            'store' => $storeId,
            'currency' => $currency,
            'rate' => $page->getRate(),
            'status' => $status,
            'load_time' => $loadTime
        ]);

        curl_close($this->curl);

        return $status;
    }

    protected function setParams($customerGroup, $storeId, $currency)
    {
        $this->headers = [];
        $this->cookies = [];

        if ($customerGroup) {
            $this->headers[HttpHelper::CUSTOMER_GROUP_HEADER] = $customerGroup;
        }

        if ($currency) {
            $this->headers[HttpHelper::CURRENCY_HEADER] = $currency;
        }

        if ($storeId) {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();
            $this->cookies[StoreCookieManager::COOKIE_NAME] = $storeCode;
        }

        $this->initVaryCookie($customerGroup, $storeId, $currency);
    }

    protected function initVaryCookie($customerGroup, $storeId, $currency)
    {
        if (!$customerGroup && !$storeId && !$currency) {
            return;
        }

        $this->httpContext->setValue(
            CustomerContext::CONTEXT_GROUP,
            $customerGroup ?: Group::NOT_LOGGED_IN_ID,
            Group::NOT_LOGGED_IN_ID
        );

        $this->httpContext->setValue(
            CustomerContext::CONTEXT_AUTH,
            (bool)$customerGroup,
            false
        );

        $this->httpContext->setValue(
            HttpContext::CONTEXT_CURRENCY,
            $currency ?: $this->defaultCurrency,
            $this->defaultCurrency
        );

        if ($storeId) {
            $storeCode = $this->storeManager->getStore($storeId)->getCode();

            $this->httpContext->setValue(
                StoreManagerInterface::CONTEXT_STORE,
                $storeCode,
                $this->storeManager->getDefaultStoreView()->getCode()
            );
        } else {
            $this->httpContext->unsValue(StoreManagerInterface::CONTEXT_STORE);
        }

        $this->cookies[Http::COOKIE_VARY_STRING] = $this->httpContext->getVaryString();
    }

    protected function setCookies()
    {
        $this->cookies[self::SESSION_COOKIE] = self::SESSION_NAME;

        $cookies = [];

        foreach ($this->cookies as $name => $value) {
            $cookies [] = "$name=$value;";
        }

        curl_setopt($this->curl, CURLOPT_COOKIE, implode(' ', $cookies));
    }

    protected function setHeaders()
    {
        $this->headers[HttpHelper::STATUS_HEADER] = 'crawl';

        $headers = [];
        foreach ($this->headers as $name => $value) {
            $headers [] = "$name: $value";
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    }

    protected function request($url)
    {
        if (!$this->config->isVarnishEnabled()) {
            /**
             * FPC key generation
             * For explanation @see \Magento\Framework\App\PageCache\Identifier::getValue
             */
            $varyString = isset($this->cookies[Http::COOKIE_VARY_STRING])
                ? $this->cookies[Http::COOKIE_VARY_STRING]
                : null;

            $hashData = [
                strpos($url, 'https://') === 0,
                $url,
                $varyString
            ];

            if (version_compare($this->productMetadata->getVersion(), '2.2', '<')) {
                $identifier = md5(serialize($hashData));
            } else {
                $identifier = sha1(serialize($hashData));
            }

            if ($this->fpc->test($identifier)) {
                return HttpHelper::STATUS_ALREADY_CACHED;
            }
        }

        $this->setCookies();
        $this->setHeaders();

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_USERAGENT, self::USER_AGENT);

        if ($this->config->isSetFlag('connection/http_auth')) {
            $login = trim($this->config->getValue('connection/login'));
            $password = trim($this->config->getValue('connection/password'));

            if ($login && $password) {
                curl_setopt($this->curl, CURLOPT_USERPWD, $login . ":" . $password);
            }
        }

        if ($this->config->getValue('connection/skip_verification')) {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_exec($this->curl);

        if ($error = curl_error($this->curl)) {
            $this->logger->error($error);
        }

        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        return $status;
    }
}
