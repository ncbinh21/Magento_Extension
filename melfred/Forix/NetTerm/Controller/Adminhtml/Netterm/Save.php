<?php


namespace Forix\NetTerm\Controller\Adminhtml\Netterm;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;
    protected $customerFactory;
    protected $companyFactory;
    protected $customerRepository;
    protected $collectionCompanyFactory;
    protected $resource;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Company\Model\CompanyFactory $companyFactory,
        \Magento\Company\Model\ResourceModel\Company\CollectionFactory $collectionCompanyFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->companyFactory = $companyFactory;
        $this->collectionCompanyFactory = $collectionCompanyFactory;
        $this->dataPersistor = $dataPersistor;
        $this->resource = $resource;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('netterm_id');

            $model = $this->_objectManager->create(\Forix\NetTerm\Model\Netterm::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Net Terms no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            $request = $this->getRequest();
            for ($i = 1; $i < 4; $i++) {
                $owner['name_title_' . $i] = $request->getParam('name_title_' . $i);
            }
            $model->setOwnersOfficers(serialize($owner));
            for ($i = 1; $i < 5; $i++) {
                $company[$i]['company_' . $i] = $request->getParam('company_' . $i);
                $company[$i]['fax_number_' . $i] = $request->getParam('fax_number_' . $i);
                $company[$i]['email_' . $i] = $request->getParam('email_' . $i);
            }
            $model->setCompanyReferences(serialize($company));

            try {
                $model->save();
//                $isActive = $model->getIsActive();
//                for ($i = 1; $i < 5; $i++) {
//                    $this->saveIsNettermCustomer($company[$i]['email_' . $i], $isActive);
//                    $this->saveIsNettermCompany($company[$i]['email_' . $i], $isActive);
//                }
                $this->messageManager->addSuccessMessage(__('You saved the Net Terms.'));
                $this->dataPersistor->clear('forix_netterm_netterm');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['netterm_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Net Terms.'));
            }

            $this->dataPersistor->set('forix_netterm_netterm', $data);
            return $resultRedirect->setPath('*/*/edit', ['netterm_id' => $this->getRequest()->getParam('netterm_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $email
     * @param int $value
     */
    protected function saveIsNettermCustomer($email, $value)
    {
        try {
            if($email) {
                $customerData = $this->customerRepository->get($email);
                $customer = $this->customerFactory->create()->load($customerData->getId());
                $customer->setData('is_netterm_active', (int)$value);
                $customer->getResource()->saveAttribute($customer, 'is_netterm_active');
            }
        } catch (\Exception $exception) {
            //
        }
    }

    /**
     * @param $companyName
     * @param int $value
     */
    protected function saveIsNettermCompany($emailCompany, $value)
    {
        try {
            $companyData = $this->collectionCompanyFactory->create()->addFieldToFilter('company_email', $emailCompany)->getFirstItem();
            if($companyData->getId()) {
//                $company = $this->companyFactory->create()->load($companyData->getId());
                $query = $this->resource->getConnection();
                $data = ['is_netterm_active_company' => $value];
                $query->update('company', $data, 'entity_id = '.$companyData->getId().'');

            }
        } catch (\Exception $exception) {
            //
        }
    }
}
