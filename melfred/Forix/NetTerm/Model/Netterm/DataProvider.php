<?php


namespace Forix\NetTerm\Model\Netterm;

use Forix\NetTerm\Model\ResourceModel\Netterm\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $dataPersistor;

    protected $loadedData;
    protected $collection;


    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            $result = $this->serializeData($this->loadedData);
            return $result;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get('forix_netterm_netterm');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('forix_netterm_netterm');
        }
        $result = $this->serializeData($this->loadedData);
        return $result;
    }

    protected function serializeData($result)
    {
        if($result) {
            foreach ($result as $key => $value) {
                if (isset($result[$key]['owners_officers'])) {
                    $owners = unserialize($result[$key]['owners_officers']);
                    foreach ($owners as $keyOwner => $valueOwner) {
                        $result[$key][$keyOwner] = $valueOwner;
                    }
                }
                if (isset($result[$key]['company_references'])) {
                    $companies = unserialize($result[$key]['company_references']);
                    foreach ($companies as $company) {
                        foreach ($company as $keyCompany => $valueCompany) {
                            $result[$key][$keyCompany] = $valueCompany;
                        }
                    }
                }
            }
        }
        return $result;
    }
}
