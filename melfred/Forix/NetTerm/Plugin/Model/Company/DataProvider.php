<?php

namespace Forix\NetTerm\Plugin\Model\Company;

use \Magento\Company\Model\Company\DataProvider as CompanyDataProvider;
use \Magento\Company\Api\Data\CompanyInterface;

class DataProvider
{

    const IS_NETTERM_ACTIVE_COMPANY = 'is_netterm_active_company';

    public function afterGetCompanyResultData(CompanyDataProvider $subject, array $result, CompanyInterface $company)
    {
        if($company->getIsNettermActiveCompany()) {
            $result[CompanyDataProvider::DATA_SCOPE_GENERAL][self::IS_NETTERM_ACTIVE_COMPANY]  = true;
        } else {
            $result[CompanyDataProvider::DATA_SCOPE_GENERAL][self::IS_NETTERM_ACTIVE_COMPANY]  = false;
        }
        return $result;
    }
}