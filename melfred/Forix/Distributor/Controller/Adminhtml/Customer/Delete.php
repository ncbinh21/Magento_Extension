<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Distributor\Controller\Adminhtml\Customer;

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Customer\Controller\Adminhtml\Index
{
	const ADMIN_RESOURCE = 'Forix_Distributor::customer';
    /**
     * Delete customer action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('Customer could not be deleted.'));
            return $resultRedirect->setPath('forix_distributor/customer/index');
        }

        $customerId = $this->initCurrentCustomer();
        if (!empty($customerId)) {
            try {
                $this->_customerRepository->deleteById($customerId);
                $this->messageManager->addSuccess(__('You deleted the customer.'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('forix_distributor/customer/index');
    }
}
