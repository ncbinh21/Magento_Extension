<?php


namespace Forix\FanPhoto\Model\Photo;

use Forix\FanPhoto\Model\ResourceModel\Photo\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $loadedData;
    protected $collection;

    protected $dataPersistor;


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
//        if (isset($this->loadedData)) {
//            return $this->loadedData;
//        }
//        $items = $this->collection->getItems();
//        foreach ($items as $model) {
//            $this->loadedData[$model->getId()] = $model->getData();
//        }
//        $data = $this->dataPersistor->get('forix_fanphoto_photo');
//
//        if (!empty($data)) {
//            $model = $this->collection->getNewEmptyItem();
//            $model->setData($data);
//            $this->loadedData[$model->getId()] = $model->getData();
//            $this->dataPersistor->clear('forix_fanphoto_photo');
//        }
//
//        return $this->loadedData;

	    if (isset($this->loadedData)) {
		    return $this->loadedData;
	    }
	    $items = $this->collection->getItems();

	    foreach ($items as $brand) {
		    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		    $storeManager->getStore()->getBaseUrl();
		    $mediaUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		    $imagePath = $mediaUrl.'fanphoto/';
		    $brandData = $brand->getData();
		    $image_url = $imagePath.$brandData['image_url'];

		    unset($brandData['image_url']);
		    $brandData['image_url'][0]['name'] = $brandData['caption'];
		    $brandData['image_url'][0]['url'] = $image_url;

//		    unset($brandData['image_path']);
//		    $brandData['image_path'] = array(
//			    array(
//				    'name'  =>  $brand_img,
//				    'url'   =>  $image_url // Should return a URL to view the image. For example, http://domain.com/pub/media/../../imagename.jpeg
//			    )
//		    );

		    $this->loadedData[$brand->getPhotoId()] = $brandData;
	    }

	    return $this->loadedData;
    }
}
