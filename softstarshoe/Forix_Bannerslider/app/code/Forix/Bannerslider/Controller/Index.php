<?php

/**
 * Forix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Forix.com license that is
 * available through the world-wide-web at this URL:
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Forix
 * @package     Forix_Bannerslider
 * @copyright   Copyright (c) 2012 Forix (http://www.forixwebdesign.com/)
 * @license
 */

namespace Forix\Bannerslider\Controller;

/**
 * Index action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
abstract class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Slider factory.
     *
     * @var \Forix\Bannerslider\Model\SliderFactory
     */
    protected $_sliderFactory;

    /**
     * banner factory.
     *
     * @var \Forix\Bannerslider\Model\BannerFactory
     */
    protected $_bannerFactory;

    /**
     * Report factory.
     *
     * @var \Forix\Bannerslider\Model\ReportFactory
     */
    protected $_reportFactory;

    /**
     * Report collection factory.
     *
     * @var \Forix\Bannerslider\Model\ResourceModel\Report\CollectionFactory
     */
    protected $_reportCollectionFactory;

    /**
     * A result that contains raw response - may be good for passing through files
     * returning result of downloads or some other binary contents.
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $_resultRawFactory;

    /**
     * Cookie Manager.
     *
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\Request
     */
    protected $_phpEnvironmentRequest;

    /**
     * logger.
     *
     * @var \Magento\Framework\Logger\Monolog
     */
    protected $_monolog;

    /**
     * stdlib timezone.
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $_stdTimezone;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context                                $context
     * @param \Forix\Bannerslider\Model\SliderFactory                          $sliderFactory
     * @param \Forix\Bannerslider\Model\BannerFactory                          $bannerFactory
     * @param \Forix\Bannerslider\Model\ReportFactory                          $reportFactory
     * @param \Forix\Bannerslider\Model\ResourceModel\Report\CollectionFactory $reportCollectionFactory
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory               $cookieMetadataFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface                     $cookieManager
     * @param \Magento\Framework\Controller\Result\RawFactory                      $resultRawFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request                       $phpEnvironmentRequest
     * @param \Magento\Framework\Logger\Monolog                                    $monolog
     * @param \Magento\Framework\Stdlib\DateTime\Timezone                          $stdTimezone
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Forix\Bannerslider\Model\SliderFactory $sliderFactory,
        \Forix\Bannerslider\Model\BannerFactory $bannerFactory,
        \Forix\Bannerslider\Model\ReportFactory $reportFactory,
        \Forix\Bannerslider\Model\ResourceModel\Report\CollectionFactory $reportCollectionFactory,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\HTTP\PhpEnvironment\Request $phpEnvironmentRequest,
        \Magento\Framework\Logger\Monolog $monolog,
        \Magento\Framework\Stdlib\DateTime\Timezone $stdTimezone
    ) {
        parent::__construct($context);
        $this->_sliderFactory = $sliderFactory;
        $this->_bannerFactory = $bannerFactory;
        $this->_reportFactory = $reportFactory;
        $this->_reportCollectionFactory = $reportCollectionFactory;

        $this->_resultRawFactory = $resultRawFactory;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_phpEnvironmentRequest = $phpEnvironmentRequest;
        $this->_monolog = $monolog;
        $this->_stdTimezone = $stdTimezone;
    }

    /**
     * get user code.
     *
     * @param mixed $id
     *
     * @return string
     */
    protected function getUserCode($id)
    {
        $ipAddress = $this->_phpEnvironmentRequest->getClientIp(true);
        $cookiefrontend = $this->_cookieManager->getCookie('frontend');
        $usercode = $ipAddress.$cookiefrontend.$id;

        return md5($usercode);
    }
}
