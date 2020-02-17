<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyRoot
 */


namespace Amasty\ShopbyRoot\Plugin\UrlRewrite\Model\Storage;

use Magento\Store\Model\ScopeInterface;
use Magento\UrlRewrite\Model\Storage\DbStorage as UrlRewriteDbStorage;

class DbStorage
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * DbStorage constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function aroundFindOneByData(
        UrlRewriteDbStorage $subject,
        callable $proceed,
        array $data
    ) {
        $identifier = isset($data['request_path']) ? $data['request_path'] : null;
        $urlKey = trim($this->scopeConfig->getValue('amshopby_brand/general/url_key', ScopeInterface::SCOPE_STORE));

        if ($urlKey && $urlKey == $identifier && $this->registry->registry('amasty_shopby_seo_parsed_params')) {
            return null;
        }

        return $proceed($data);
    }
}
