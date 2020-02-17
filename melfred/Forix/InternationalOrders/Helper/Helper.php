<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\InternationalOrders\Helper;

use Magento\Framework\App\Helper\Context;

class Helper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Helper constructor.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param Context $context
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        Context $context
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function isCustomerLogin()
    {
        return $this->httpContext->getValue('customer_logged_in');
    }

    /**
     * @return bool
     */
    public function isDomestic()
    {
        if($customerId = $this->customerSession->getCustomerId()) {
            $customer = $this->customerRepository->getById($customerId);
            if(!$customer->getCustomAttribute('country_name') || ($customer->getCustomAttribute('country_name') && $customer->getCustomAttribute('country_name')->getValue() == 'US')) {
                return true;
            }
            return false;
        }
        if(isset($_COOKIE['domestic-cookie']) && $_COOKIE['domestic-cookie'] == 1) {
            return true;
        }
        return false;
    }
}