<?php


namespace Forix\Distributor\Model\ResourceModel\CompanyDistributor;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Forix\Distributor\Model\CompanyDistributor::class,
            \Forix\Distributor\Model\ResourceModel\CompanyDistributor::class
        );
    }

    /**
     * @param $companyId
     * @return $this
     */
    public function addCompanyToFilter($companyId){
        $this->addFieldToFilter('company_id', $companyId);
        return $this;
    }
}
