<?php

namespace Forix\Custom\Controller\Storelocator\Index;

use Amasty\Storelocator\Model\LocationFactory;

class AjaxCustom extends \Amasty\Storelocator\Controller\Index\Ajax
{

    public function execute()
    {
        /** @var \Amasty\Storelocator\Model\ResourceModel\Location\Collection $locationCollection */

	    $locationCollection = $this->_location->create()->getCollection();

        // $locationCollection = $this->_objectManager->get('Amasty\Storelocator\Model\Location')->getCollection();

        $locationCollection->applyDefaultFilters();

        $productId = $this->getRequest()->getParam('product');

        $product = false;

        if ($productId) {
            $product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($productId);
        }

        $locationCollection->load();
        $locationCollection->getSelect()->joinLeft(
            ['code_region' => 'directory_country_region'],
            'code_region.country_id = main_table.country && code_region.default_name = main_table.state'
        );

        $this->_view->loadLayout();
        $left = $this->_view->getLayout()->getBlock('amlocatorAjax')->toHtml();

        $arrayCollection = [];

        foreach ($locationCollection as $item) {
            if ($product) {
                $valid = $this->dataHelper->validateLocation($item, $product);
                if (!$valid) {
                    continue;
                }
            }
            $arrayCollection['items'][] = $item->getData();
        }

        $arrayCollection['totalRecords'] = isset($arrayCollection['items']) ? count($arrayCollection['items']) : 0;

        $res = array_merge_recursive(
            $arrayCollection, array('block' => $left)
        );

        $json = $this->_jsonEncoder->encode($res);

        $this->getResponse()->setBody($json);
    }

}