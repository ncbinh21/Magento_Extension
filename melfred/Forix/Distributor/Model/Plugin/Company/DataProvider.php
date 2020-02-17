<?php

namespace Forix\Distributor\Model\Plugin\Company;

use \Magento\Company\Model\Company\DataProvider as CompanyDataProvider;
use \Magento\Company\Api\Data\CompanyInterface;

class DataProvider
{
    /**
     * @var \Forix\Distributor\Helper\Data
     */
    protected $_distributorHelper;

    public function __construct(
        \Forix\Distributor\Helper\Data $distributorHelper
    )
    {

        $this->_distributorHelper = $distributorHelper;
    }

    const DATA_SCOPE_DISTRIBUTOR = 'company_distributor';

    public function afterGetCompanyResultData(CompanyDataProvider $subject, array $result, CompanyInterface $company)
    {
        if($company->getId()) {
            $result[self::DATA_SCOPE_DISTRIBUTOR]['available_distributors'] = $this->_distributorHelper->getDistributorIds($company->getId());
            $result[CompanyDataProvider::DATA_SCOPE_GENERAL]['distributor_zipcode'] = implode(',', $this->_distributorHelper->getCompanyDistributorZipCodes($company->getId()));
        }
        return $result;
    }
}