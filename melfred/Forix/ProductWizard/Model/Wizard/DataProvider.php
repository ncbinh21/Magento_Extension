<?php


namespace Forix\ProductWizard\Model\Wizard;

use Forix\ProductWizard\Model\ResourceModel\Wizard\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $loadedData;
    protected $collection;

    protected $dataPersistor;
    protected $fileInfo;

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
     * Get FileInfo instance
     *
     * @return FileInfo
     *
     * @deprecated 101.1.0
     */
    private function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = ObjectManager::getInstance()->get(FileInfo::class);
        }
        return $this->fileInfo;
    }

    /**
     * @param \Forix\ProductWizard\Model\Wizard $wizard
     * @param array $wizardData
     * @return array
     */
    private function convertValues($wizard, $wizardData)
    {
        $attributes = ['base_image'];
        $wizardData = $this->convertImage($attributes, $wizard, $wizardData);
        $attributes = ['banner_image'];
        $wizardData = $this->convertImage($attributes, $wizard, $wizardData);
        return $wizardData;
    }

    private function convertImage($attributes, $wizard, $wizardData)
    {
        foreach ($attributes as $attributeCode) {
            $fileName = $wizard->getData($attributeCode);
            if(is_array($fileName) && isset($fileName[0]) && isset($fileName[0]['name'])) {
                $fileName = $fileName[0]['name'];
            }
            if($fileName) {
                if ($this->getFileInfo()->isExist($fileName)) {
                    $stat = $this->getFileInfo()->getStat($fileName);
                    $mime = $this->getFileInfo()->getMimeType($fileName);
                    $wizardData[$attributeCode] = [];
                    $wizardData[$attributeCode][0]['name'] = $fileName;
                    $wizardData[$attributeCode][0]['url'] = $wizard->getImageUrl($attributeCode);
                    $wizardData[$attributeCode][0]['size'] = isset($stat) ? $stat['size'] : 0;
                    $wizardData[$attributeCode][0]['type'] = $mime;
                }
            }
        }
        return $wizardData;
    }
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $this->convertValues($model, $model->getData());
        }
        $data = $this->dataPersistor->get('forix_productwizard_wizard');
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $this->convertValues($model, $model->getData());

            $this->dataPersistor->clear('forix_productwizard_wizard');
        }
        
        return $this->loadedData;
    }
}
