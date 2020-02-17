<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 22/08/2018
 * Time: 10:31
 */

namespace Forix\Distributor\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{


    protected $_distributorCollectionFactory;
    protected $_collectionFactory;
    protected $_distributorIds;
    protected $_distributorCompanyZip;

    public function __construct(
        Context $context,
        \Forix\Distributor\Model\ResourceModel\Zipcode\CollectionFactory $collectionFactory,
        \Forix\Distributor\Model\ResourceModel\CompanyDistributor\CollectionFactory $distributorCollectionFactory
    )
    {
        parent::__construct($context);
        $this->_distributorCollectionFactory = $distributorCollectionFactory;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param $companyId
     * @return array
     */
    public function getCompanyDistributorZipCodes($companyId)
    {
        if (!isset($this->_distributorCompanyZip[$companyId])) {
            $distributorIds = $this->getDistributorIds($companyId);
            $zipcodeCollection = $this->_collectionFactory->create();
            $zipcodeCollection->addDistributorToFilter($distributorIds);
            $zipCodes = $zipcodeCollection->getColumnValues('zipcode');
            $this->_distributorCompanyZip[$companyId] = $zipCodes;
        }
        return $this->_distributorCompanyZip[$companyId];
    }

    /**
     * @param $companyId
     * @return array
     */
    public function getDistributorIds($companyId)
    {
        /**
         * @var $distributorCollection \Forix\Distributor\Model\ResourceModel\CompanyDistributor\Collection
         */
        if (!isset($this->_distributorIds[$companyId])) {
            $distributorCollection = $this->_distributorCollectionFactory->create();
            $distributorCollection->addCompanyToFilter($companyId);
            $this->_distributorIds[$companyId] = $distributorCollection->getColumnValues('distributor_id');
        }
        return $this->_distributorIds[$companyId];
    }

}