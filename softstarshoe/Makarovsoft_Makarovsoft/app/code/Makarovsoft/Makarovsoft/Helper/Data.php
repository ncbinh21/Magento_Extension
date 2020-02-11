<?php

namespace Makarovsoft\Makarovsoft\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Module\ModuleListInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'general/enabled';
    const XML_PATH_DEBUG = 'general/debug';
    const XML_PATH_ACCESS_CODE = 'general/access_code';
    const XML_PATH_SAVE_COOKIE = 'general/save_in_cookie';
    const XML_PATH_PROFILER = 'general/show_profiler';

    const XML_PATH_DEBUG_TEMPLATE_FRONT = 'dev/debug/template_hints_storefront';
    const XML_PATH_DEBUG_TEMPLATE_ADMIN = 'dev/debug/template_hints_admin';
    const XML_PATH_DEBUG_BLOCKS = 'dev/debug/template_hints_blocks';

    /**
     * Cookie key for template path
     */
    const COOKIE_NAME = 'mp-etph';

    /**
     * Cookie path
     */
    const COOKIE_PATH = '/';

    /**
     * Cookie lifetime value
     */
    const COOKIE_LIFETIME = 600;

    /**
     * @var CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    /**
     * @var CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var ModuleListInterface
     */
    protected $_moduleList;

    protected $context;

    /**
     * @param Context $context
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param CookieManagerInterface $cookieManager
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        Context $context,
        CookieMetadataFactory $cookieMetadataFactory,
        CookieManagerInterface $cookieManager,
        ModuleListInterface $moduleList
    )
    {
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_cookieManager = $cookieManager;
        $this->_logger = $context->getLogger();
        $this->_moduleList = $moduleList;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * Check if enabled
     *
     * @return string|null
     */
    public function isEnabled()
    {
        return $this->context->getRequest()->getParam('mastp');
    }

    public function getSaveInCookie()
    {
       return false;
    }

    public function getShowProfiler()
    {
        return true;
    }

    public function shouldShowTemplatePathHints()
    {
        return $this->isEnabled();
    }

    /**
     * Set Cookie
     *
     * @param $value
     */
    public function setDebugCookie($value)
    {
        $publicCookieMetadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata();
        $this->_cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            $value,
            $publicCookieMetadata
        );
    }

    /**
     * Get debug value from cookie.
     *
     * @return null|string
     */
    public function getDebugCookie()
    {
        return $this->_cookieManager->getCookie(self::COOKIE_NAME);
    }

    /**
     * Delete debug cookie.
     *
     * @return $this
     */
    public function deleteDebugCookie()
    {
        $cookieMetadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata();
        $this->_cookieManager->deleteCookie(self::COOKIE_NAME, $cookieMetadata);
        return $this;
    }

    /**
     * Logging Utility
     *
     * @param $message
     * @param bool|false $useSeparator
     */
    public function log($message, $useSeparator = false)
    {

    }
}