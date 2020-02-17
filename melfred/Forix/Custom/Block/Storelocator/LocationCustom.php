<?php

namespace Forix\Custom\Block\Storelocator;

class LocationCustom extends \Amasty\Storelocator\Block\Location
{
    /**
     * @return mixed
     */
    public function getLocationCollection()
    {
        if (!$this->_coreRegistry->registry('amlocator_location')) {
            /**
             * \Amasty\Storelocator\Model\Location $locationCollection
             */
            $locationCollection = $this->_modelLocation->create()->getCollection();
            $locationCollection->applyDefaultFilters();
            $locationCollection->load();
            $locationCollection->getSelect()->joinLeft(
                ['code_region' => 'directory_country_region'],
                'code_region.country_id = main_table.country && code_region.default_name = main_table.state'
            );

            $this->_coreRegistry->register('amlocator_location', $locationCollection);
        }

        return $this->_coreRegistry->registry('amlocator_location');
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        $locations = $this->getLocationCollection();
        $product = false;
        $productId = $this->getRequest()->getParam('product');

        if ($productId) {
            $product = $this->getProductById($productId);
        }

        foreach($locations as $location) {
            $location->load($location->getId());
            $location->setData('schedule_array', $this->serializer->unserialize($location->getData('schedule')));
            //$location->setData('schedule', $this->_jsonEncoder->encode($this->serializer->unserialize($location->getData('schedule'))));
            if ($product) {
                if (!$this->dataHelper->validateLocation($location, $product)) {
                    continue;
                }
            }
        }
        return $locations;
    }

    public function getJsonLocations()
    {
        $locationCollection = $this->getLocations();
        $locationArray = [];
        $locationArray['items'] = [];

        if(!empty($locationCollection->getData())){
            for ($i = 0; $i < count($locationCollection->getData()); $i++) {
                $customArray = $locationCollection->getData()[$i];
                $locationArray['items'][] = $customArray;
            }
        }

        $locationArray['totalRecords'] = count($locationArray['items']);
        $store = $this->_storeManager->getStore(true)->getId();
        $locationArray['currentStoreId'] = $store;

        return $this->_jsonEncoder->encode($locationArray);
    }
}