<?php

namespace Forix\Customer\Block\NegotiableQuote\Link;

use Magento\Company\Api\CompanyManagementInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

/**
 * Override \Magento\NegotiableQuote\Block\Link\Quote
 */
class Quote extends \Magento\NegotiableQuote\Block\Link\Quote
{

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    public $sessionFactory;

    protected $companyManagement;

    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        CompanyManagementInterface $companyManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sessionFactory = $sessionFactory;
        $this->companyManagement = $companyManagement;
    }

    /**
     * Determine if user has company or not
     * @return bool
     */
    public function hasCompany()
    {
        try {
            $customerSession = $this->sessionFactory->create();
            if ($customerSession->isLoggedIn()) {
                $customerId = $customerSession->getCustomerId();
                $company = $this->companyManagement->getByCustomerId($customerId);
                return !empty($company) ? true : false;
            }
        } catch (NoSuchEntityException $entityException) {

        }
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return parent::toHtml();
    }

    /**
     * @return null|string
     */
    protected function _toHtml()
    {
        if (!$this->hasCompany()) {
            return '';
        } else {
            return parent::_toHtml();
        }
    }

    protected function getCacheLifetime()
    {
        return null;
    }

}