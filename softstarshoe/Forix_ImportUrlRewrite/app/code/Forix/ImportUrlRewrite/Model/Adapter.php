<?php
namespace Forix\ImportUrlRewrite\Model;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;

class Adapter extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    protected $logInHistory = true;

    const OLD_URL = 'old_url';
    const NEW_URL = 'new_url';

    const ERROR_OLD_URL_EMPTY = 'oldUrlEmpty';
    const ERROR_NEW_URL_EMPTY = 'newUrlEmpty';

    protected $_urlRewriteFactory;
    protected $_urlRewriteHelper;
    protected $_storeManager;
    protected $_messageTemplates = [
        self::ERROR_OLD_URL_EMPTY => 'Address is empty',
        self::ERROR_NEW_URL_EMPTY => 'New link is empty'
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
        if (!isset($rowData[self::OLD_URL]) || empty($rowData[self::OLD_URL])) {
            $this->addRowError(self::ERROR_OLD_URL_EMPTY, $rowNum);
            return false;
        }
        if (!isset($rowData[self::NEW_URL]) || empty($rowData[self::NEW_URL])) {
            $this->addRowError(self::ERROR_NEW_URL_EMPTY, $rowNum);
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
                $rowData[self::OLD_URL] = trim($rowData[self::OLD_URL], '/');
                $rowData[self::NEW_URL] = trim($rowData[self::NEW_URL], '/');
                
                $this->_urlRewriteHelper->validateRequestPath($rowData[self::OLD_URL]);
                $model = $this->_urlRewriteFactory->create()->load($rowData[self::OLD_URL], 'request_path');
                $model->setEntityType('custom')
                    ->setRequestPath($rowData[self::OLD_URL])
                    ->setTargetPath($rowData[self::NEW_URL])
                    ->setRedirectType(301)
                    ->setStoreId($defaultStoreView->getId());

                $model->save();
            }
        }


        return $this;
    }

}