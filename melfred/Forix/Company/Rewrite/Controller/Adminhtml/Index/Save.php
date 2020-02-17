<?php

namespace Forix\Company\Rewrite\Controller\Adminhtml\Index;

use Magento\Company\Api\Data\CompanyInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Company\Model\CompanySuperUserGet;
use Magento\Company\Api\Data\CompanyInterfaceFactory;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;

class Save extends \Magento\Company\Controller\Adminhtml\Index\Save
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Company::index';

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var \Magento\Company\Model\CompanySuperUserGet
     */
    private $superUser;

    /**
     * @var \Magento\Company\Api\Data\CompanyInterfaceFactory
     */
    private $companyDataFactory;

    /**
     * @var \Magento\Company\Api\CompanyRepositoryInterface
     */
    private $companyRepository;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CompanySuperUserGet $superUser
     * @param CompanyRepositoryInterface $companyRepository
     * @param CompanyInterfaceFactory $companyDataFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        DataObjectProcessor $dataObjectProcessor,
        CompanySuperUserGet $superUser,
        CompanyRepositoryInterface $companyRepository,
        CompanyInterfaceFactory $companyDataFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct($context, $dataObjectProcessor, $superUser, $companyRepository, $companyDataFactory, $dataObjectHelper);
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->superUser = $superUser;
        $this->companyRepository = $companyRepository;
        $this->companyDataFactory = $companyDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Store Customer Group Data to session.
     *
     * @param CompanyInterface $company
     * @return void
     */
    private function storeCompanyDataToSession(CompanyInterface $company)
    {
        $companyData = $this->dataObjectProcessor->buildOutputDataArray(
            $company,
            \Magento\Company\Api\Data\CompanyInterface::class
        );
        $this->_getSession()->setCompanyData($companyData);
    }

    /**
     * Create or save customer group.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Company\Api\Data\CompanyInterface $company */
        $company = null;
        $request = $this->getRequest();
        $id = $request->getParam('id') ? $request->getParam('id') : null;
        try {
            $company = $this->saveCompany($id);

            // After save
            $this->_eventManager->dispatch(
                'adminhtml_company_save_after',
                ['company' => $company, 'request' => $request]
            );

            $companyData = ['companyName' => $company->getCompanyName()];
            $this->messageManager->addSuccessMessage(
                $id
                    ? __('You have saved company %companyName.', $companyData)
                    : __('You have created company %companyName.', $companyData)
            );
            $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $returnToEdit = true;
            $this->messageManager->addErrorMessage($e->getMessage());
            if ($company instanceof CompanyInterface) {
                $this->storeCompanyDataToSession($company);
            }
        } catch (\Exception $e) {
            $returnToEdit = true;
            $this->messageManager->addExceptionMessage($e, __('Something went wrong. Please try again later.'));
            if ($company instanceof CompanyInterface) {
                $this->storeCompanyDataToSession($company);
            }
        }
        return $this->getRedirect($returnToEdit, $company);
    }

    /**
     * Get redirect object depending on $returnToEdit and is company new.
     *
     * @param bool $returnToEdit
     * @param CompanyInterface|null $company [optional]
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function getRedirect($returnToEdit, CompanyInterface $company = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if (($company != null) && $company->getId()) {
                $resultRedirect->setPath(
                    'company/index/edit',
                    ['id' => $company->getId()]
                );
            } else {
                $resultRedirect->setPath(
                    'company/index/new'
                );
            }
        } else {
            $resultRedirect->setPath('company/index');
        }
        return $resultRedirect;
    }

    /**
     * Filter request to get just list of fields.
     *
     * @return array
     */
    private function extractData()
    {
        $allFormFields = [
            CompanyInterface::COMPANY_ID,
            CompanyInterface::STATUS,
            CompanyInterface::NAME,
            CompanyInterface::LEGAL_NAME,
            CompanyInterface::COMPANY_EMAIL,
            CompanyInterface::EMAIL,
            CompanyInterface::VAT_TAX_ID,
            CompanyInterface::RESELLER_ID,
            CompanyInterface::COMMENT,
            CompanyInterface::STREET,
            CompanyInterface::CITY,
            CompanyInterface::COUNTRY_ID,
            CompanyInterface::REGION,
            CompanyInterface::REGION_ID,
            CompanyInterface::POSTCODE,
            CompanyInterface::TELEPHONE,
            CompanyInterface::JOB_TITLE,
            CompanyInterface::PREFIX,
            CompanyInterface::FIRSTNAME,
            CompanyInterface::MIDDLENAME,
            CompanyInterface::LASTNAME,
            CompanyInterface::SUFFIX,
            CompanyInterface::GENDER,
            CompanyInterface::CUSTOMER_GROUP_ID,
            CompanyInterface::SALES_REPRESENTATIVE_ID,
            CompanyInterface::REJECT_REASON,
            'extension_attributes'
        ];
        $result = [];
        $request = $this->getRequest()->getParams();
        if (is_array($request)) {
            foreach ($request as $fields) {
                if (!is_array($fields)) {
                    continue;
                }
                $result = array_merge_recursive($result, $fields);
            }
        }
        $result = array_intersect_key($result, array_flip($allFormFields));
        return $result;
    }

    /**
     * Create/load company, set request data, set default role for a new company.
     *
     * @param int $id
     * @return CompanyInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveCompany($id)
    {
        $data = $this->extractData();
        $customer = $this->superUser->getUserForCompanyAdmin($data);

        if ($id !== null) {
            $company = $this->companyRepository->get((int)$id);
        } else {
            $company = $this->companyDataFactory->create();
        }
        $this->setCompanyRequestData($company, $data);
        $company->setSuperUserId($customer->getId());
        $this->companyRepository->save($company);
        return $company;
    }

}