<?php

namespace Forix\UrlRewritesImporter\Model;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;

class Adapter extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    protected $logInHistory = true;

    const REQUEST_PATH = 'request_path';
    const TARGET_PATH = 'target_path';

    const ERROR_REQUEST_PATH_EMPTY = 'oldUrlEmpty';
    const ERROR_TARGET_PATH_EMPTY = 'newUrlEmpty';

    protected $_urlRewriteFactory;
    protected $_urlRewriteHelper;
    protected $_storeManager;
    protected $_messageTemplates = [
        self::ERROR_REQUEST_PATH_EMPTY => 'REQUEST_PATH is empty',
        self::ERROR_TARGET_PATH_EMPTY => 'TARGET_PATH is empty'
    ];

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
        \Magento\UrlRewrite\Helper\UrlRewrite $urlRewriteHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;

        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }

        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();

        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_urlRewriteHelper = $urlRewriteHelper;
        $this->_storeManager = $storeManager;
    }
    public function getEntityTypeCode()
    {
        return 'url_rewrite';
    }
    public function validateRow(array $rowData, $rowNum)
    {
           
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        
        $this->_validatedRows[$rowNum] = true;
        if (!isset($rowData[self::REQUEST_PATH]) || empty($rowData[self::REQUEST_PATH])) {
            $this->addRowError(self::ERROR_REQUEST_PATH_EMPTY, $rowNum);
            return false;
        }
        if (!isset($rowData[self::TARGET_PATH]) || empty($rowData[self::TARGET_PATH])) {
            $this->addRowError(self::ERROR_TARGET_PATH_EMPTY, $rowNum);
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
    public function saveEntity()
    {
        $defaultStoreView = $this->_storeManager->getDefaultStoreView();
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                
                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $rowData[self::REQUEST_PATH] = trim($rowData[self::REQUEST_PATH], '/');
                $rowData[self::TARGET_PATH] = trim($rowData[self::TARGET_PATH], '/');
                
                $this->_urlRewriteHelper->validateRequestPath($rowData[self::REQUEST_PATH]);
                $model = $this->_urlRewriteFactory->create()->load($rowData[self::REQUEST_PATH], 'request_path');
                $model->setEntityType('custom')
                    ->setRequestPath($rowData[self::REQUEST_PATH])
                    ->setTargetPath($rowData[self::TARGET_PATH])
                    ->setRedirectType(301)
                    ->setStoreId($defaultStoreView->getId());

                $model->save();
            }
        }


        return $this;
    }

}