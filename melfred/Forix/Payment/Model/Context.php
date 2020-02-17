<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/11/2018
 * Time: 14:22
 */

namespace Forix\Payment\Model;


class Context implements \Magento\Framework\ObjectManager\ContextInterface
{

    /**
     * @var \Forix\Payment\Helper\PaymentHelper
     */
    protected $_configHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_session;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Forix\Payment\Helper\PaymentHelper $configHelper)
    {
        $this->_configHelper = $configHelper;
        $this->_session = $session;
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->_localeDate = $localeDate;
        $this->_urlBuilder = $urlBuilder;
    }


    /**
     * @return \Forix\Payment\Helper\PaymentHelper
     */
    public function getPaymentHelper()
    {
        return $this->_configHelper;
    }

    /**
     * @return \Magento\Framework\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public function getLocaleDate()
    {
        return $this->_localeDate;
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        return $this->_scopeConfig;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return \Magento\Framework\Session\SessionManagerInterface
     */
    public function getSession()
    {
        return $this->_session;
    }


}