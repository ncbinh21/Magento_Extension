<?php

namespace Forix\NetTerm\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveIsNettermCompany implements ObserverInterface
{

    protected $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
       $this->resource = $resource;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $company = $observer->getCompany();
        if($general = $request->getParam('general')) {
            if(isset($general['is_netterm_active_company'])) {
                if($general['is_netterm_active_company'] == 'true') {
                    $nettermActiveCompany = 1;
                } else {
                    $nettermActiveCompany = 0;
                }
                $query = $this->resource->getConnection();
                $data = ['is_netterm_active_company' => $nettermActiveCompany];
                $query->update('company', $data, 'entity_id = '.$company->getId().'');
            }
        }
    }
}