<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: blauer.local
 */

namespace Forix\CatalogImport\Model\Import\Categories;
use Magento\Framework\App\Filesystem\DirectoryList;

class Adapter extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const CATEGORY_PATH = 'category';
    const ERROR_CATEGORY_PATH_IS_EMPTY = 'error_category_path_is_empty';
    const ERROR_CATEGORY_PATH_IS_INVALID = 'error_category_path_is_invalid';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::ERROR_CATEGORY_PATH_IS_EMPTY => 'Category field is empty',
        self::ERROR_CATEGORY_PATH_IS_INVALID => 'Category does not exist'
    ];
    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = true;
    /**
     * Valid column names
     *
     * @array
     */
    protected $validColumnNames = [
        'category','category_image','meta_title','meta_description','blauer_seo_text'
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
    protected $_resource;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor
     */
    protected $categoryProcessor;
    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory
     */
    protected $_blockCollectionFactory;
    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $_filter;

    protected $_categories = [];
    protected $_categoryDatas = [];

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor $categoryProcessor,
        \Magento\Framework\Filesystem $filesystem
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->errorAggregator = $errorAggregator;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection();
        $this->categoryProcessor = $categoryProcessor;
        $this->_filesystem = $filesystem;


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
        return 'melfred_categories';
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
        if (!isset($rowData[self::CATEGORY_PATH]) || empty($rowData[self::CATEGORY_PATH])) {
            $this->addRowError($this->_messageTemplates[self::ERROR_CATEGORY_PATH_IS_EMPTY], $rowNum);
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
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                
                $categoriesString = 'Default Category/'.$rowData[self::CATEGORY_PATH];
                $categoriesString = trim($categoriesString);
                $categoryIds = [];
                if (!empty($categoriesString)) {
                    $categoryIds = $this->categoryProcessor->upsertCategories(
                        $categoriesString,
                        ','
                    );
                }

                if(empty($categoryIds)){
                    return;
                }
                $this->_categories[$categoriesString] = $this->categoryProcessor->getCategoryById(array_shift($categoryIds));
                $this->_categoryDatas[$categoriesString] = $rowData;
            }
        }
        $mediaAttribute = ['image', 'small_image', 'thumbnail'];
        $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $importPath = 'import/category';
        $destPath = 'catalog/category';
        foreach($this->_categoryDatas as $catPath=>$data){
            $catPath = trim($catPath);
            $_category = $this->_categories[$catPath];
            if(empty($_category)){
                continue;
            }
            foreach($data as $key=>$value){
                if($key == 'category_image'){
                    if(!empty($value)){
                        $source = $mediapath->getAbsolutePath($importPath.'/'.$value);
                        if(file_exists($source)){
                            copy(
                                $source,
                                $mediapath->getAbsolutePath($destPath.'/'.$value)
                            );
                            $_category->setImage($value, $mediaAttribute, true, false);

                        }
                    }

                    continue;
                }
                $_category->setData($key,$value);
            }
            $_category->setStoreId(0)->save();
        }

        return $this;
    }
}