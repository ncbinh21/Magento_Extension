<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: ergomart.local
 */
namespace Forix\CatalogImport\Model\Product\Images;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;
class Adapter extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity {
    const SKU = 'sku';
    const ERROR_SKU_IS_EMPTY = 'error_sku_is_empty';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::ERROR_SKU_IS_EMPTY => 'Parent SKU is empty',
    ];
    protected $_permanentAttributes = [self::SKU];
    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;
    protected $groupFactory;
    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        'sku','image','image_label','gallery','gallery_label'
    ];
    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;
    protected $_validators = [];
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_connection;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;
    /**
     * Logging instance
     * @var \Forix\CatalogImport\Logger\Images\Logger
     */
    protected $_logger;

    protected $_imgAttributes = [
        'image' =>  87,
        'small_image' =>  88,
        'thumbnail' =>  89,
    ];
    protected $_imgLabelAttributes = [
        'image_label' =>  109,
        'small_image_label' =>  110,
        'thumbnail_label' =>  111,
    ];
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Forix\CatalogImport\Logger\Images\Logger $logger
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->errorAggregator = $errorAggregator;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection();
        $this->_dir = $dir;
        $this->_logger = $logger;

    }

    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'melfred_update_product_images';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        if (!isset($rowData[self::SKU]) || empty($rowData[self::SKU])) {
            $this->addRowError(self::ERROR_SKU_IS_EMPTY, $rowNum);
            return false;
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * Create Advanced price data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->saveEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }
        return true;
    }

    /**
     * Save newsletter subscriber
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    /**
     * Replace newsletter subscriber
     *
     * @return $this
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }
    /**
     * Save and replace product relations
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        //$this->_connection->truncateTable($this->_resource->getTableName('forix_import_product_relations'));
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(self::ERROR_SKU_IS_EMPTY, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $cpe = $this->_resource->getTableName('catalog_product_entity');
                $select = $this->_connection->select()->from(['cpe'=>$cpe],['row_id'])->where('sku=?',$rowData['sku']);
                $rowIds = $this->_connection->fetchCol($select);
                if(empty($rowIds)){
                    continue;
                }
                if(!empty($rowData['gallery'])){
                    $rowData['gallery'] = str_replace("\n",',',$rowData['gallery']);
                    $rowData['gallery'] = array_map(array($this, 'getImagePath'),explode(',',$rowData['gallery']));
                }
                if(!empty($rowData['gallery_label'])){
                    $rowData['gallery_label'] = str_replace("\n",',',$rowData['gallery_label']);
                    $rowData['gallery_label'] = array_map('trim',explode(',',$rowData['gallery_label']));
                }
                if(is_array($rowData['gallery'])) {
                    foreach ($rowData['gallery'] as $k => $v) {
                        if (is_null($v)) {
                            unset($rowData['gallery'][$k]);
                            if (isset($rowData['gallery_label'][$k])) {
                                unset($rowData['gallery_label'][$k]);
                            }
                        }
                    }
                }
                $rowData['gallery'] = is_array($rowData['gallery'])?array_values($rowData['gallery']):[];
                $rowData['gallery_label'] = is_array($rowData['gallery_label'])?array_values($rowData['gallery_label']):[];
                if(empty($rowData['image'])){
                    if (!empty($rowData['gallery'])){
                        $rowData['image'] = array_shift($rowData['gallery']);
                        if(!empty($rowData['gallery_label'])){
                            $rowData['image_label'] = array_shift($rowData['gallery_label']);
                        }
                    }else{
                        continue;
                    }
                }else{
                    $rowData['image'] = $this->getImagePath($rowData['image']);
                    if(is_null($rowData['image'])){
                        if (!empty($rowData['gallery'])){
                            $rowData['image'] = array_shift($rowData['gallery']);
                            if(!empty($rowData['gallery_label'])){
                                $rowData['image_label'] = array_shift($rowData['gallery_label']);
                            }
                        }else{
                            continue;
                        }
                    }
                }
                array_unshift($rowData['gallery'],$rowData['image']);
                array_unshift($rowData['gallery_label'],$rowData['image_label']);
                $gallerytbl = $this->_connection->getTableName('catalog_product_entity_media_gallery');
                $galleryvaluetbl = $this->_connection->getTableName('catalog_product_entity_media_gallery_value');
                $galleryvalueentitytbl = $this->_connection->getTableName('catalog_product_entity_media_gallery_value_to_entity');
                $varchartbl = $this->_connection->getTableName('catalog_product_entity_varchar');
                foreach($rowData['gallery'] as $key => $img){
                    foreach($rowIds as $rowId){
                        $gVId = $this->_connection->fetchOne(
                            $this->_connection->select()
                                ->from(['cpemg' => $this->_resource->getTableName('catalog_product_entity_media_gallery')],['value_id'])
                                ->joinInner(['cpemgv' => $this->_resource->getTableName('catalog_product_entity_media_gallery_value')],'cpemg.value_id=cpemgv.value_id',[])
                                ->where('cpemg.value=?',$img)
                                ->where('cpemgv.row_id=?',$rowId)
                        );
                        if(!empty($gVId)){
                            continue;
                        }
                        $this->_connection->insert($gallerytbl,
                            [
                                'value_id'  =>  null,
                                'attribute_id'  => 90,
                                'value' => $img,
                                'media_type' => 'image',
                                'disabled'  => 0
                            ]
                        );
                        $lastValueId = $this->_connection->lastInsertId($gallerytbl);
                        $this->_connection->insert($galleryvaluetbl,
                            [
                                'value_id'  =>  $lastValueId,
                                'store_id'  =>  0,
                                'row_id'  => $rowId,
                                'label' => empty($rowData['gallery_label'][$key])?null:$rowData['gallery_label'][$key],
                                'position' => $key,
                                'disabled'  => 0,
                                'record_id'  => null,
                            ]
                        );
                        $this->_connection->insert($galleryvalueentitytbl,
                            [
                                'value_id'  =>  $lastValueId,
                                'row_id'  => $rowId
                            ]
                        );
                    }
                }
                foreach($rowIds as $rowId){
                    foreach($this->_imgAttributes as $_id){
                        $this->_connection->delete($varchartbl,"row_id={$rowId} AND attribute_id={$_id}");
                        $this->_connection->insert($varchartbl,
                            [
                                'value_id'  =>  null,
                                'attribute_id'  =>  $_id,
                                'store_id'  =>  0,
                                'row_id'    =>  $rowId,
                                'value'    =>  $rowData['image'],

                            ]);
                    }
                    foreach($this->_imgLabelAttributes as $_id){
                        $this->_connection->delete($varchartbl,"row_id={$rowId} AND attribute_id={$_id}");
                        $this->_connection->insert($varchartbl,
                            [
                                'value_id'  =>  null,
                                'attribute_id'  =>  $_id,
                                'store_id'  =>  0,
                                'row_id'    =>  $rowId,
                                'value'    =>  $rowData['image_label'],

                            ]);
                    }
                }
                
            }
        }
        return $this;
    }
    protected function getCorrectFileName($fileName)
    {
        $fileName = preg_replace('/[^a-z0-9_\\-\\.]+/i', '_', $fileName);
        $fileInfo = pathinfo($fileName);

        if (preg_match('/^_+$/', $fileInfo['filename'])) {
            $fileName = 'file.' . $fileInfo['extension'];
        }
        return $fileName;
    }
    protected function correctFileNameCase($fileName)
    {
        return strtolower($fileName);
    }
    protected function addDirSeparator($dir)
    {
        if (substr($dir, -1) != '/') {
            $dir .= '/';
        }
        return $dir;
    }
    protected function getDispretionPath($fileName)
    {
        $char = 0;
        $dispertionPath = '';
        while ($char < 2 && $char < strlen($fileName)) {
            if (empty($dispertionPath)) {
                $dispertionPath = '/' . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            } else {
                $dispertionPath = $this->addDirSeparator(
                        $dispertionPath
                    ) . ('.' == $fileName[$char] ? '_' : $fileName[$char]);
            }
            $char++;
        }
        return $dispertionPath;
    }
    protected function getImagePath($fileName){
        $fileName = trim($fileName);
        $originalFilename = $fileName;
        $fileName = $this->getCorrectFileName($fileName);
        $fileName = $this->correctFileNameCase($fileName);
        $dispretionPath = $this->getDispretionPath($fileName);
        $destinationFolder = $this->_dir->getPath('media').DIRECTORY_SEPARATOR.'catalog'.DIRECTORY_SEPARATOR.'product'.$dispretionPath.DIRECTORY_SEPARATOR.$fileName;
        if(!file_exists($destinationFolder)){
            $this->_logger->info("{$originalFilename} does not exist!");
            return null;
        }
        return $dispretionPath.DIRECTORY_SEPARATOR.$fileName;

    }
}