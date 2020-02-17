<?php

namespace Forix\NetTerm\Block\Checkout\Payment;

use Magento\Framework\View\Element\Template;

class NetTerm extends \Magento\Framework\View\Element\Template
{
    protected $session;
    protected $companyRepository;

    public function __construct(
        \Magento\Company\Api\CompanyManagementInterface $companyRepository,
        \Magento\Customer\Model\Session $session,
        Template\Context $context,
        array $data = []
    ) {
        $this->companyRepository = $companyRepository;
        $this->session = $session;
        parent::__construct($context, $data);
    }

    public function getCompanyName()
    {
        if ($customer = $this->session->isLoggedIn()) {
            try {
                $customerId = $this->session->getCustomer()->getId();
                $companyRes = $this->companyRepository->getByCustomerId($customerId);
                return $companyRes->getCompanyName();
            } catch (\Exception $exception) {
                return null;
            }
        }
        return null;
    }
}