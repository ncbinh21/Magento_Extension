<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Distributor\Ui;


/**
 * Class ProductDataProvider
 */


class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

	protected $request;
	protected $customerFactory;
    protected $collectionFactory;
    protected $collection;



	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		$collectionFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Framework\App\RequestInterface $request,
		array $meta = [],
		array $data = []
	)
	{
		parent::__construct($name,$primaryFieldName, $requestFieldName, $meta, $data);
		$this->collectionFactory   =  $collectionFactory;
		$this->request             = $request;
		$this->customerFactory     = $customerFactory;
	}
    public function getCollection()
    {
        if(!$this->collection){
            $this->collection = $this->collectionFactory->create();
        }
        return $this->collection;
    }

    public function getData() {
		$id = $this->request->getParam("id");
		$result = [];
		if ($id) {
			/** @var  $customerFactory \Magento\Customer\Model\Customer */
			$customerFactory = $this->customerFactory->create();
			$response = $customerFactory->load($id);
			$result[$response["entity_id"]]['customer'] = $response->getData();
			$this->getCollection()->getSelect()
				->joinLeft(
					['company_adv' => $this->getCollection()->getResource()->getTable('company_advanced_customer_entity')],
					'company_adv.customer_id = e.entity_id',
					['company_id','status','job_title','telephone']
				)->joinLeft(
					['company' => $this->getCollection()->getResource()->getTable('company')],
					'company.entity_id = company_adv.company_id',
					['company_name','super_user_id']
				);

			$item = $this->getCollection()->getFirstItem();
			if ($item->getData('super_user_id') == $item->getData('entity_id')) {
				$result[$response["entity_id"]]['customer']["extension_attributes"]["company_attributes"]["is_super_user"] = 1;
			}

			$result[$response["entity_id"]]['customer']["extension_attributes"]["company_attributes"]["status"]       = $item->getData("status");
			$result[$response["entity_id"]]['customer']["extension_attributes"]["company_attributes"]["company_name"] = $item->getData("company_name");
			$result[$response["entity_id"]]['customer']["extension_attributes"]["company_attributes"]["company_id"]   = $item->getData("company_id");
			return $result;
		}



	}
}
