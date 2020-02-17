<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 15:26
 */

namespace Forix\Payment\Model\Adapter;

class Sage100Adapter extends \Forix\Payment\Model\AbstractAdapter
{
    protected $_classMaps;
    protected $_outputHeaders;
    protected $_hasInit;
    /**
     * Sage100Adapter constructor.
     * @param $serviceUrl
     * @param array $options
     * @param array $classMaps
     */
    public function __construct(
        $classMaps = []
    )
    {
        $this->_classMaps = $classMaps;
    }

    /**
     * Get the WSDL URL for this service.
     *
     * @return string
     */
    public function getWsdl()
    {
        return $this->_options['wsdl'];
    }

    /**
     * Get the SOAP Client instance for this service.
     */
    public function getSoapClient()
    {
        return $this->_options['client'];
    }

    /**
     * Get the StrikeIron output headers returned with the last method response.
     *
     * @return array
     */
    public function getLastOutputHeaders()
    {
        return $this->_outputHeaders;
    }

    /**
     * @param $serviceUrl
     * @param $config
     * @throws \Zend_Service_Exception
     */
    public function init($serviceUrl, $config = [])
    {
        if(!$this->_hasInit) {
            if (!extension_loaded('soap')) {
                throw new \Zend_Service_Exception('SOAP extension is not enabled');
            }
            $classMaps = [
                'classmap' => array_merge_recursive([
                    'ContractInformation' => \Forix\Payment\Model\Service\Sage100\ContractInformation::class,
                    'Customer' => \Forix\Payment\Model\Service\Sage100\Customer::class,
                    'GetCustomerResult' => \Forix\Payment\Model\Service\Sage100\Customer::class,
                    'CustomerContact' => \Forix\Payment\Model\Service\Sage100\CustomerContact::class,
                    'DiagnosticInformation' => \Forix\Payment\Model\Service\Sage100\DiagnosticInformation::class,
                    'Logon' => \Forix\Payment\Model\Service\Sage100\Logon::class,
                    'PreAuthorizationData' => \Forix\Payment\Model\Service\Sage100\PreAuthorizationData::class,
                    'PreAuthorizeCreditCardResult' => \Forix\Payment\Model\Service\Sage100\PreAuthorizationResult::class,
                    'SalesOrder' => \Forix\Payment\Model\Service\Sage100\SalesOrder::class,
                    'GetSalesOrderTemplateResult' => \Forix\Payment\Model\Service\Sage100\SalesOrder::class,
                ], $this->_classMaps)
            ];
            $this->_options['wsdl'] = $serviceUrl;
            $this->_options['options'] = array_merge($this->_options['options'], $config, $classMaps);

            $this->_initSoapClient();
            $this->_hasInit = true;
        }
    }

    /**
     * @param $method
     * @param $params
     * @return array
     */
    protected function _transformCall($method, $params)
    {
        return array(ucfirst($method), $params);
    }

    /**
     * Proxy method calls to the SOAPClient instance, transforming method
     * calls and responses for convenience.
     *
     * @param  string $method Method name
     * @param  array $params Parameters for method
     * @return mixed            Result
     * @throws \Zend_Service_Exception
     */
    public function __call($method, $params = [])
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $this->_hasError = false;
        $this->clearMessage();
        // prepare method name and parameters for soap call
        list($method, $params) = $this->_transformCall($method, $params);

        $params = isset($params[0]) ? array($params[0]) : array();
        try {
            $result = $this->_options['client']->__soapCall($method,
                $params,
                $this->_options['options'],
                $this->_outputHeaders);
        } catch (\Exception $e) {
            $message = get_class($e) . ': ' . $e->getMessage();
            $logger->info('---------------error sage---------------------');
            $logger->info($message);
            $logger->info('---------------error sage---------------------');

            $this->_hasError = true;
            $this->addMessages([$message]);
            return false;
        }

        return $result;
    }

    /**
     * Initialize the SOAPClient instance
     *
     * @return void
     */
    protected function _initSoapClient()
    {
        if (!isset($this->_options['options'])) {
            $this->_options['options'] = array();
        }

        if (!isset($this->_options['client'])) {
            $this->_options['client'] = new \SoapClient($this->_options['wsdl'], $this->_options['options']);
        }
    }

}