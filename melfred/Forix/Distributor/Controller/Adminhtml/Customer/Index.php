<?php

namespace  Forix\Distributor\Controller\Adminhtml\Customer;



class Index extends \Magento\Customer\Controller\Adminhtml\Index\Index
{
	const ADMIN_RESOURCE = 'Forix_Distributor::customer';
	/**
	 * Customers list action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
	 */
	public function execute()
	{
		if ($this->getRequest()->getQuery('ajax')) {
			$resultForward = $this->resultForwardFactory->create();
			$resultForward->forward('grid');
			return $resultForward;
		}
		$resultPage = $this->resultPageFactory->create();
		/**
		 * Set active menu item
		 */
		$resultPage->setActiveMenu(self::ADMIN_RESOURCE);
		$resultPage->getConfig()->getTitle()->prepend(__('Customers'));

		/**
		 * Add breadcrumb item
		 */
		$resultPage->addBreadcrumb(__('Customers'), __('Customers'));
		$resultPage->addBreadcrumb(__('Manage Customers'), __('Manage Customers'));

		$this->_getSession()->unsCustomerData();
		$this->_getSession()->unsCustomerFormData();

		return $resultPage;
	}
}