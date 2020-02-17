<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/08/2018
 * Time: 18:41
 */

namespace Forix\Distributor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CompanyLoadAfter implements ObserverInterface
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

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $company = $observer->getObject();
        if ($company->getId()) {
            $zipCodeString = implode(',', $this->_distributorHelper->getCompanyDistributorZipCodes($company->getId()));
            $company->setData('distributor_zipcode', $zipCodeString);
        }
    }
}