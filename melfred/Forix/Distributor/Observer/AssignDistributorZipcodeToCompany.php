<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/08/2018
 * Time: 18:24
 */

namespace Forix\Distributor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AssignDistributorZipcodeToCompany implements ObserverInterface
{

    protected $_companyDistributor;
    protected $_messageManager;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Forix\Distributor\Model\ResourceModel\CompanyDistributor $companyDistributor
    )
    {
        $this->_companyDistributor = $companyDistributor;
        $this->_messageManager = $messageManager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $company = $observer->getCompany();
        $customerDistributor = $request->getParam('company_distributor');
        try {
            $where = ['company_id = ?' => (int)$company->getId()];
            $this->_companyDistributor->getConnection()->delete($this->_companyDistributor->getMainTable(), $where);
        } catch (\Exception $e) {
            $this->_messageManager->addError($e->getMessage());
        }
        if ($customerDistributor['available_distributors'] && count($customerDistributor)) {
            // Remove current values:
            try {
                $distributors = $customerDistributor['available_distributors'];
                $inserted = [];
                foreach ($distributors as $distributor) {
                    $inserted[] = [
                        'distributor_id' => $distributor,
                        'company_id' => $company->getId()
                    ];
                }
                $this->_companyDistributor
                    ->getConnection()
                    ->insertArray(
                        $this->_companyDistributor->getMainTable(),
                        ['distributor_id', 'company_id'],
                        $inserted
                    );
            } catch (\Exception $e) {
                $this->_messageManager->addError($e->getMessage());
            }
        }
    }

}