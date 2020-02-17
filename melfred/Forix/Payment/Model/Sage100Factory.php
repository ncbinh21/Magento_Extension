<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/11/2018
 * Time: 14:32
 */

namespace Forix\Payment\Model;
use Magento\Framework\ObjectManagerInterface;

class Sage100Factory
{

    /**
     * Entity class name
     */
    const CLASS_NAME = \Forix\Payment\Model\Sage100::class;

    const ADAPTER_CLASS = \Forix\Payment\Model\Adapter\Sage100Adapter::class;

    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;

    protected $_scopeConfig;
    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $serviceUrl
     * @return Sage100
     * @throws \Zend_Service_Exception
     */
    public function create($serviceUrl = null)
    {
        /**
         * @var $adapter \Forix\Payment\Model\Adapter\Sage100Adapter
         */
        $adapter = $this->_objectManager->get(self::ADAPTER_CLASS);
        $serviceUrl = $serviceUrl?: $this->_scopeConfig->getValue('payment/'. \Forix\Payment\Model\Sage100Payment::CODE . '/service_url');
        $adapter->init($serviceUrl, []);
        return $this->_objectManager->create(self::CLASS_NAME, ['serviceAdapter' => $adapter]);
    }
}