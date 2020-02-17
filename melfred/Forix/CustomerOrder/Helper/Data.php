<?php
namespace Forix\CustomerOrder\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Data
 * @package Forix\CustomerOrder\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var Company
     */
    protected $company;

    /**
     * @var \Magento\Company\Api\CompanyManagementInterface
     */
    protected $companyManagement;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Company\Api\CompanyManagementInterface $companyManagement
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext,
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Company\Api\CompanyManagementInterface $companyManagement,
        \Magento\Customer\Model\Session $customerSession

    ) {
        $this->httpContext = $httpContext;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->companyManagement = $companyManagement;

        parent::__construct($context);
    }

    /**
     * @return bool
     * check if admin of company and in distributor group
     */
    public function isDistributorManager()
    {
        if ($this->httpContext->getValue('customer_logged_in') && $this->httpContext->getValue('is_company_admin') && $groupsetting = $this->scopeConfig->getValue('forix_customer/customergroup/distributor_group')) {
            $curCustomerGroup = $this->httpContext->getValue('customer_group');
            $arrGroup = explode(',', $groupsetting);
            if (in_array($curCustomerGroup, $arrGroup)) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return bool
     * check if admin of company and in distributor group
     */
    public function isDistributor(\Magento\Customer\Model\Customer $customer = null)
    {
        if(null === $customer) {
            if ($this->httpContext->getValue('customer_logged_in') && $groupsetting = $this->scopeConfig->getValue('forix_customer/customergroup/distributor_group')) {
                $curCustomerGroup = $this->httpContext->getValue('customer_group');
                $arrGroup = explode(',', $groupsetting);
                if (in_array($curCustomerGroup, $arrGroup)) {
                    return true;
                }
            }
        }else{
            if($groupsetting = $this->scopeConfig->getValue('forix_customer/customergroup/distributor_group')){
                $arrGroup = explode(',', $groupsetting);
                $curCustomerGroup  = $customer->getGroupId();
                if (in_array($curCustomerGroup, $arrGroup)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return \Magento\Company\Api\Data\CompanyInterface
     */
    public function getCustomerCompany()
    {
        if ($this->company !== null) {
            return $this->company;
        }

        $customerId = $this->_customerSession->getCustomerId();

        if ($customerId) {
            $this->company = $this->companyManagement->getByCustomerId($customerId);
        }

        return $this->company;
    }

    /**
     * @return array[]|bool|false|string[]
     */
    public function getDistributorPostcode() {
        $distributorPostcode = $this->getCustomerCompany()->getData('distributor_zipcode');
        return $distributorPostcode ? preg_split('/\n|\r|\,/', $distributorPostcode, -1, PREG_SPLIT_NO_EMPTY) : false;
    }




}