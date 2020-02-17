<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/08/2018
 * Time: 17:04
 */

namespace Forix\Distributor\Model\Import;

use Braintree\Exception;
use \Magento\ImportExport\Model\Import as ImportExport;

class Zipcode extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const TABLE_DISTRIBUTOR_ZIP_CODE = 'forix_distributor_zipcode';

    const ZIP_COLUMN_NAME = 'Zip';
    const CITY_COLUMN_NAME = 'City';
    const COUNTRY_COLUMN_NAME = 'County';
    const ERROR_ZIP_CODE_EMPTY = 'zipcode_is_empty';

    protected $_resourceFactory;
    protected $_messageTemplates = [
        self::ERROR_ZIP_CODE_EMPTY => "Zipcode Is Empty %s"
    ];
    protected $_permanentAttributes = [
        self::ZIP_COLUMN_NAME,
        self::CITY_COLUMN_NAME,
        self::COUNTRY_COLUMN_NAME
    ];

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator,
        \Forix\Distributor\Model\Import\Proxy\Zipcode\ResourceModelFactory $resourceModelFactory
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection('write');
        $this->errorAggregator = $errorAggregator;
        $this->_resourceFactory = $resourceModelFactory;
        foreach (array_merge($this->errorMessageTemplates, $this->_messageTemplates) as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }
    }

    public function getLocationId()
    {
        if (isset($this->_parameters['location_id'])) {
            return $this->_parameters['location_id'];
        }
        throw new Exception("Please Insert Location Id");
    }

    /**
     * Import data rows.
     *
     * @return boolean
     */
    protected function _importData()
    {
        $this->saveAndReplaceLocations();
        return true;
    }

    /**
     * Save and replace Locations
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceLocations()
    {
        $behavior = $this->getBehavior();
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
            $this->_cachedSkuToDelete = null;
        }
        $locationid = $this->getLocationId();
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $locationsRows = [];
            $zipcodeInsert = [];
            foreach ($bunch as $rowNum => $rowData) {
                $_locationsRows = array_map('trim', $rowData);
                $locationsRows[] = $_locationsRows;
                $zipcodeInsert[] = $_locationsRows[self::ZIP_COLUMN_NAME];
            }
            $this->saveZipCode($locationsRows, $zipcodeInsert, $locationid, self::TABLE_DISTRIBUTOR_ZIP_CODE);
        }

        return $this;
    }

    public function validateData()
    {
        if (!$this->_dataValidated) {
            $this->getErrorAggregator()->clear();
            // do all permanent columns exist?
            $absentColumns = array_diff($this->_permanentAttributes, $this->getSource()->getColNames());
            $this->addErrors(self::ERROR_CODE_COLUMN_NOT_FOUND, $absentColumns);

            if (ImportExport::BEHAVIOR_DELETE != $this->getBehavior()) {
                // check attribute columns names validity
                $columnNumber = 0;
                $emptyHeaderColumns = [];
                $invalidAttributes = [];
                foreach ($this->getSource()->getColNames() as $columnName) {
                    $columnNumber++;
                    if (!$this->isAttributeParticular($columnName)) {
                        if (trim($columnName) == '') {
                            $emptyHeaderColumns[] = $columnNumber;
                        } elseif ($this->needColumnCheck && !in_array($columnName, $this->getValidColumnNames())) {
                            $invalidAttributes[] = $columnName;
                        }
                    }
                }
                $this->addErrors(self::ERROR_CODE_INVALID_ATTRIBUTE, $invalidAttributes);
                $this->addErrors(self::ERROR_CODE_COLUMN_EMPTY_HEADER, $emptyHeaderColumns);
            }

            if (!$this->getErrorAggregator()->getErrorsCount()) {
                $this->_saveValidatedBunches();
                $this->_dataValidated = true;
            }
        }
        return $this->getErrorAggregator();
    }

    /**
     * @param array $locations
     * @param $zipcodeInsert
     * @param $locationId
     * @param $table
     * @return $this
     */
    protected function saveZipCode(array $locations, $zipcodeInsert, $locationId, $table)
    {
        /**
         * @var $resource \Forix\Distributor\Model\Import\Proxy\Zipcode\ResourceModel
         */
        $resource = $this->_resourceFactory->create();
        $tableName = $resource->getTable($table);
        $connection = $resource->getConnection();
        $dataInsert = [];
        $dataUpdate = [];
        $zipcodeId = [];
        $zipCodeExists = $connection->fetchAll(
            $connection->select()
                ->from(['a' => $tableName], ['zipcode_id', 'zipcode'])
                ->where('zipcode in (?)', $zipcodeInsert)
                ->where('distributor_id = ?', $locationId)
        );
        foreach ($zipCodeExists as $zipCodeExist) {
            $zipcodeId[$zipCodeExist['zipcode']] = $zipCodeExist['zipcode_id'];
        }
        foreach ($locations as $location) {
            if (isset($zipcodeId[$location[self::ZIP_COLUMN_NAME]])) {
                $dataUpdate[] = [
                    'zipcode_id' => $zipcodeId[$location[self::ZIP_COLUMN_NAME]],
                    'zipcode' => $location[self::ZIP_COLUMN_NAME],
                    'distributor_id' => $locationId,
                    'city' => $location[self::CITY_COLUMN_NAME],
                    'country' => $location[self::COUNTRY_COLUMN_NAME],
                ];
            } else {
                $dataInsert[] = [
                    'zipcode' => $location[self::ZIP_COLUMN_NAME],
                    'distributor_id' => $locationId,
                    'city' => $location[self::CITY_COLUMN_NAME],
                    'country' => $location[self::COUNTRY_COLUMN_NAME],
                ];
            }
        }
        $connection->beginTransaction();
        if (count($dataUpdate)) {
            $connection->insertOnDuplicate($tableName, $dataUpdate, ['city', 'country']);
        }
        if (count($dataInsert)) {
            $connection->insertArray($tableName, array_keys(reset($dataInsert)), $dataInsert);
        }
        $connection->commit();
        return $this;
    }


    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'distributor_zipcodes';
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */

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
        if (!isset($rowData[self::ZIP_COLUMN_NAME]) || empty($rowData[self::ZIP_COLUMN_NAME])) {
            $this->addRowError($this->_messageTemplates[self::ERROR_ZIP_CODE_EMPTY], $rowNum);
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
}