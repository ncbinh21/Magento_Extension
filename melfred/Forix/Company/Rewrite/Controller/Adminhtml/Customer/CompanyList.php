<?php

namespace Forix\Company\Rewrite\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Company\Model\CountryInformationProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Company\Api\Data\CompanyInterface;
use Magento\Framework\Controller\ResultFactory;

class CompanyList extends \Magento\Company\Controller\Adminhtml\Customer\CompanyList
{
    protected $groupRepository;
    protected $companyRepository;
    protected $searchCriteriaBuilder;
    protected $countryInformationProvider;
    protected $dbHelper;

    public function __construct(
        Context $context,
        CompanyRepositoryInterface $companyRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CountryInformationProvider $countryInformationProvider,
        DbHelper $dbHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
    ) {
        $this->dbHelper = $dbHelper;
        $this->countryInformationProvider = $countryInformationProvider;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->companyRepository = $companyRepository;
        $this->groupRepository = $groupRepository;
        parent::__construct($context, $companyRepository, $searchCriteriaBuilder, $countryInformationProvider, $dbHelper);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $name = $this->getRequest()->getParam('name');

        try {
            $companies = $this->getSuggestedCompanies($name);
            $result->setData(
                $this->getCompaniesData($companies)
            );
        } catch (LocalizedException $e) {
            $result->setData(['error' => $e->getMessage()]);
        }

        return $result;
    }


    /**
     * Get suggested companies by query.
     *
     * @param string $query
     * @return CompanyInterface[]
     */
    private function getSuggestedCompanies($query)
    {
        $escapedQuery = $this->dbHelper->escapeLikeValue(
            $query,
            ['position' => 'start']
        );

        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            CompanyInterface::NAME,
            $escapedQuery,
            'like'
        )->create();

        $searchResult = $this->companyRepository->getList($searchCriteria);

        return $searchResult->getItems();
    }
    /**
     * Get companies data as array.
     *
     * @param CompanyInterface[] $companies
     * @return array
     */
    private function getCompaniesData(array $companies)
    {
        return array_map(
            function (CompanyInterface $company) {
                $customerGroup = $this->groupRepository->getById($company->getCustomerGroupId());
                return [
                    'id' => $company->getId(),
                    'name' => $company->getCompanyName() . ' - ' . $customerGroup->getCode(),
                    'group' => $company->getCustomerGroupId(),
                    'country' => $company->getCountryId(),
                    'region' => $this->countryInformationProvider->getActualRegionName(
                        $company->getCountryId(),
                        $company->getRegionId(),
                        $company->getRegion()
                    )
                ];
            },
            array_values($companies)
        );
    }
}