<?php
namespace Forix\CatalogImport\Model\Product\Attributes;


use Magento\Framework\Stdlib\DateTime;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as BlockCollectionFactory;

class UpdateAttribute extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity {

    const PSEUDO_MULTI_LINE_SEPARATOR = '|';
    /**
     * Data row scopes.
     */
    const SCOPE_DEFAULT = 1;

    const SCOPE_WEBSITE = 2;

    const SCOPE_STORE = 0;

    const SCOPE_NULL = -1;
    const COL_STORE = '_store';

    protected $logInHistory = true;
    protected $_selectAttributes;
    protected $_storeManager;
    protected $_resourceConnection;
    const SKU_KEY = 'sku';

    const CATEGORY_KEY = 'categories';

    const ERROR_SKU_EMPTY = 'skuEmpty';
    const ERROR_SKU_NOT_FOUND = 'skuNotFound';

    protected $_productRepository;
    protected $_messageTemplates = [
        self::ERROR_SKU_EMPTY => 'Sku is empty',
        self::ERROR_SKU_NOT_FOUND => 'SKU Not found'
    ];
    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributeCache = [];

    protected $_resourceFactory;
    protected $dateTime;
    protected $_localeDate;
    protected $storeResolver;
    protected $_blockCollection;
    protected $_categoryRepository;
    protected $_categoryFactory;
    protected $_categoryCollection;
    protected $_categoryLinkRepository;
    protected $_productLinkFactory;

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $prodAttrColFac,
        \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface $categoryLinkRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Catalog\Api\Data\CategoryProductLinkInterfaceFactory $productLinkFactory,
        BlockCollectionFactory $blockCollectionFactory
    )
    {
        $this->_categoryRepository = $categoryRepository;
        $this->_productRepository = $productRepository;
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;
        $this->_storeManager = $storeManager;
        $this->_resourceFactory = $resourceFactory;
        $this->dateTime = $dateTime;
        $this->_localeDate = $localeDate;
        $this->storeResolver = $storeResolver;
        $this->_categoryFactory = $categoryFactory;
        $this->_blockCollection = $blockCollectionFactory->create()->load();
        $this->_categoryLinkRepository = $categoryLinkRepository;
        $this->_productLinkFactory = $productLinkFactory;
        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }

        //$entityType = $config->getEntityType($this->getEntityTypeCode());

        //$this->_entityTypeId = $entityType->getEntityTypeId();
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
        $this->_resourceConnection = $resource;
    }

    public function getCategoryList()
    {
        if (!$this->_categoryCollection) {
            $this->_categoryCollection = $this->_categoryFactory->create()->addAttributeToSelect('name')->load();
        }
        return $this->_categoryCollection;
    }


    public function getCategoryLinkRepository()
    {
        return $this->_categoryLinkRepository;
    }

    public function getEntityTypeCode()
    {
        return 'update_product';
    }

    public function validateRow(array $rowData, $rowNum)
    {

        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;
        if (!isset($rowData[self::SKU_KEY]) || empty($rowData[self::SKU_KEY])) {
            $this->addRowError(self::ERROR_SKU_EMPTY, $rowNum);
            return false;
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }
        return true;
    }

    protected $_resource;

    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = $this->_resourceFactory->create();
        }
        return $this->_resource;
    }

    protected function getBlockIdByIdentifier($identifier)
    {
        if ($this->_blockCollection) {
            /**
             * @var $block \Magento\Cms\Model\Block
             */
            foreach ($this->_blockCollection as $block) {
                if ($block->getIdentifier() == $identifier) {
                    return $block->getId();
                } else if (strtolower($block->getTitle()) == strtolower($identifier)) {
                    return $block->getId();
                }
            }
        }
        return '';
    }


    protected function getValueFromText($string)
    {
        return filter_var($string, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Retrieve attribute by code
     *
     * @param string $attrCode
     * @return mixed
     */
    public function retrieveAttributeByCode($attrCode)
    {
        if (!isset($this->_attributeCache[$attrCode])) {
            $this->_attributeCache[$attrCode] = $this->getResource()->getAttribute($attrCode);
        }
        return $this->_attributeCache[$attrCode];
    }

    public function saveEntity()
    {

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {

                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                $rowSku = trim($rowData[self::SKU_KEY]);
                /**
                 * @var $model \Magento\Catalog\Model\Product
                 */
                try {
                    //$rowData = $this->prepareAttributeOptions($rowData);
                    $model = $this->_productRepository->get((string)trim($rowSku));
                    if ($model->getId()) {
                        foreach ($rowData as $key => $attrValue) {
                            if (self::SKU_KEY != $key) {
                                $attrValue = trim($attrValue);
                                // || (!$attrValue && $model->getData($key) != '')) {
                                $rowScope = 1;
                                /**
                                 *
                                 */
                                $attribute = $this->retrieveAttributeByCode($key);
                                if (!is_bool($attribute)) {
                                    if (!$attrValue) {
                                        if (!($model->getData($key))) {
                                            continue;
                                        }
                                    } else {

                                        //$attrId = $attribute->getId();
                                        //$backModel = $attribute->getBackendModel();
                                        //$attrTable = $attribute->getBackend()->getTable();

                                        if ('multiselect' != $attribute->getFrontendInput() && self::SCOPE_NULL == $rowScope) {
                                            // skip attribute processing for SCOPE_NULL rows
                                            continue;
                                        }
                                        if ($attribute->getSource() instanceof \Magento\Catalog\Model\Category\Attribute\Source\Page) {
                                            $attrValue = $this->getBlockIdByIdentifier($attrValue);
                                        } else if ('select' == $attribute->getFrontendInput()) {
                                            $options = $attribute->getOptions();
                                            foreach ($options as $option) {
                                                if ((string)$attrValue == (string)$option->getLabel()) {
                                                    $attrValue = $option->getValue();
                                                    break;
                                                }
                                            }
                                        } else if ('multiselect' == $attribute->getFrontendInput()) {
                                            $options = $attribute->getOptions();
                                            $_attrValue = [];
                                            $attrValue = explode(self::PSEUDO_MULTI_LINE_SEPARATOR, $attrValue);
                                            $attrValue = array_map('trim', $attrValue);
                                            foreach ($options as $option) {
                                                if (in_array(trim($option->getLabel()), $attrValue)) {
                                                    $_attrValue[] = $option->getValue();
                                                }
                                            }
                                            $attrValue = implode(',', $_attrValue);
                                        }
                                    }
                                    $model->setData($key, $attrValue);
                                    $model->getResource()->saveAttribute($model, $key);
                                    echo "Updated {$rowSku}\r\n";
                                } else if (self::CATEGORY_KEY == $key) {
                                    if ($attrValue) {
                                        $categoryName = explode(',', $attrValue);
                                        $categoryName = array_map('trim', $categoryName);
                                        $categoryIds = $model->getCategoryIds();
                                        $hasChange = false;
                                        /**
                                         * @var $_category \Magento\Catalog\Model\Category
                                         */
                                        foreach ($this->getCategoryList() as $_category) {
                                            if (in_array($_category->getName(), $categoryName)) {
                                                if (!in_array($_category->getId(), $categoryIds)) {
                                                    $categoryIds[] = $_category->getId();
                                                    $hasChange = true;
                                                }
                                            }
                                        }
                                        if ($hasChange) {
                                            $model->setCategoryIds($categoryIds);
                                            $assignedCategories = $model->getResource()->getCategoryIds($model);
                                            foreach (array_diff($assignedCategories, $categoryIds) as $categoryId) {
                                                $this->getCategoryLinkRepository()->deleteByIds($categoryId, $model->getSku());
                                            }

                                            foreach (array_diff($categoryIds, $assignedCategories) as $categoryId) {
                                                /** @var \Magento\Catalog\Api\Data\CategoryProductLinkInterface $categoryProductLink */
                                                $categoryProductLink = $this->_productLinkFactory->create();
                                                $categoryProductLink->setSku($model->getSku());
                                                $categoryProductLink->setCategoryId($categoryId);
                                                $categoryProductLink->setPosition(0);
                                                $this->getCategoryLinkRepository()->save($categoryProductLink);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $this->getErrorAggregator()->addRowToSkip($rowNum);
                        $this->addRowError(self::ERROR_SKU_NOT_FOUND, $rowNum);
                    }

                } catch (\Exception $e) {
                    echo $e->getMessage() . ": " . $rowSku . "\r\n" ;
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    $this->addRowError(self::ERROR_SKU_NOT_FOUND, $rowNum);
                }
            }
        }
        //Run Update Custom Product Name after update lb_left_nav_name
        return $this;
    }
}