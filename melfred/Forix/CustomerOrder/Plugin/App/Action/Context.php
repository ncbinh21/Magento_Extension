<?php

namespace Forix\CustomerOrder\Plugin\App\Action;

use Forix\CustomerOrder\Model\Customer\Context as CustomerSessionContext;
use Magento\Company\Model\ResourceModel\Permission\Collection;
use Magento\Company\Model\ResourceModel\UserRole\CollectionFactory;

class Context
{
    /**
     * @var Company
     */
    protected $company;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Company\Api\CompanyManagementInterface
     */
    protected $companyManagement;

    /**
     * @var CollectionFactory
     */
    protected $userRoleCollection;

    /**
     * @var \Magento\Company\Model\ResourceModel\Permission\CollectionFactory
     */
    protected $permissionCollection;

    /**
     * Context constructor.
     * @param CollectionFactory $userRoleCollection
     * @param \Magento\Company\Model\ResourceModel\Permission\CollectionFactory $permissionCollection
     * @param \Magento\Company\Api\CompanyManagementInterface $companyManagement
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        \Magento\Company\Model\ResourceModel\UserRole\CollectionFactory $userRoleCollection,
        \Magento\Company\Model\ResourceModel\Permission\CollectionFactory $permissionCollection,
        \Magento\Company\Api\CompanyManagementInterface $companyManagement,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->userRoleCollection = $userRoleCollection;
        $this->permissionCollection = $permissionCollection;
        $this->companyManagement = $companyManagement;
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * @param \Magento\Framework\App\ActionInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        \Magento\Framework\App\ActionInterface $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $isAdminCompany = false;
        $customerId = $this->customerSession->getCustomerId();
        $userRoleCollect = $this->userRoleCollection->create();
        $userRole = $userRoleCollect->addFieldToFilter('user_id', $customerId)->getFirstItem();
        $permissionCollect = $this->permissionCollection->create()->addFieldToFilter('role_id', $userRole->getRoleId());
        foreach ($permissionCollect as $permission) {
            if($permission->getResourceId() == 'Forix_Company::customer_order' && $permission->getPermission() == 'allow') {
                $isAdminCompany = true;
                break;
            }
        }
        if($company = $this->getCustomerCompany()) {
            if($company->getSuperUserId() == $customerId) {
                $isAdminCompany = true;
            }
        }

        $this->httpContext->setValue(
            CustomerSessionContext::CONTEXT_IS_COMPANY_ADMIN,
            $isAdminCompany,
            false
        );

        return $proceed($request);
    }

    /**
     * @return mixed
     */
    public function getCustomerCompany()
    {
        if ($this->company !== null) {
            return $this->company;
        }

        $customerId = $this->customerSession->getCustomerId();

        if ($customerId) {
            $this->company = $this->companyManagement->getByCustomerId($customerId);
        }

        return $this->company;
    }
}