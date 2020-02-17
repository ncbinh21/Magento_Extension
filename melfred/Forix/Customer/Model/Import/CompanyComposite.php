<?php
/**
 * Created by PhpStorm.
 * User: nghia
 * Date: 25/03/2019
 * Time: 10:36
 */

namespace Forix\Customer\Model\Import;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

class CompanyComposite extends \Magento\CustomerImportExport\Model\Import\CustomerComposite
{
    protected $_companyFields = [];

    const COMPANY_PREFIX = '_b2b_';

    protected $_permissions = [
        'Magento_Company::index'	=>	'allow',
        'Magento_Sales::all'	=>	'allow',
        'Magento_Sales::place_order'	=>	'allow',
        'Magento_Sales::payment_account'	=>	'deny',
        'Magento_Sales::view_orders'	=>	'allow',
        'Magento_Sales::view_orders_sub'	=>	'deny',
        'Magento_NegotiableQuote::all'	=>	'allow',
        'Magento_NegotiableQuote::view_quotes'	=>	'allow',
        'Magento_NegotiableQuote::manage'	=>	'allow',
        'Magento_NegotiableQuote::checkout'	=>	'allow',
        'Magento_NegotiableQuote::view_quotes_sub'	=>	'deny',
        'Forix_Company::all'	=>	'deny',
        'Forix_Company::customer_order'	=>	'deny',
        'Magento_Company::view'	=>	'allow',
        'Magento_Company::view_account'	=>	'allow',
        'Magento_Company::edit_account'	=>	'deny',
        'Magento_Company::view_address'	=>	'allow',
        'Magento_Company::edit_address'	=>	'deny',
        'Magento_Company::contacts'	=>	'allow',
        'Magento_Company::payment_information'	=>	'allow',
        'Magento_Company::user_management'	=>	'allow',
        'Magento_Company::roles_view'	=>	'deny',
        'Magento_Company::roles_edit'	=>	'deny',
        'Magento_Company::users_view'	=>	'allow',
        'Magento_Company::users_edit'	=>	'deny',
        'Magento_Company::credit'	=>	'deny',
        'Magento_Company::credit_history'	=>	'deny'
    ];
    /**
     * Countries and regions
     *
     * Example array: array(
     *   [country_id_lowercased_1] => array(
     *     [region_code_lowercased_1]         => region_id_1,
     *     [region_default_name_lowercased_1] => region_id_1,
     *     ...,
     *     [region_code_lowercased_n]         => region_id_n,
     *     [region_default_name_lowercased_n] => region_id_n
     *   ),
     *   ...
     * )
     *
     * @var array
     */
    protected $_countryRegions = [];

    /**
     * Region ID to region default name pairs
     *
     * @var array
     */
    protected $_regions = [];

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    protected $_regionCollection;

    /**
     * Customer collection wrapper
     *
     * @var Storage
     */
    protected $_customerStorage;
    /**
     * @var \Magento\Customer\Model\Address\Validator\Postcode
     */
    protected $postcodeValidator;
    /**
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\ImportExport\Model\ImportFactory $importFactory
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\CustomerImportExport\Model\ResourceModel\Import\CustomerComposite\DataFactory $dataFactory
     * @param \Magento\CustomerImportExport\Model\Import\CustomerFactory $customerFactory
     * @param \Magento\CustomerImportExport\Model\Import\AddressFactory $addressFactory
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionColFactory
     * @param \Magento\Customer\Model\Address\Validator\Postcode $postcodeValidator
     * @param \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\CustomerImportExport\Model\ResourceModel\Import\CustomerComposite\DataFactory $dataFactory,
        \Magento\CustomerImportExport\Model\Import\CustomerFactory $customerFactory,
        \Magento\CustomerImportExport\Model\Import\AddressFactory $addressFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionColFactory,
        \Magento\Customer\Model\Address\Validator\Postcode $postcodeValidator,
        \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory,
        array $data = []
    ) {
        parent::__construct($string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $dataFactory, $customerFactory, $addressFactory, $data);

        $this->_regionCollection = $regionColFactory->create();
        $this->_initCompanyFields()->_initCountryRegions();
        $this->postcodeValidator = $postcodeValidator;
        $this->_customerStorage = $storageFactory->create();
    }


    /**
     * Imported entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'company_composite';
    }
    /**
     * Prepare data row for address entity validation or import
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareCompanyRowData(array $rowData)
    {
        $result = [];
        foreach ($rowData as $key => $value) {
            if (strpos($key,self::COMPANY_PREFIX) !== false && !in_array($key, $this->_companyFields) && !empty($value)) {
                $key = str_replace(self::COMPANY_PREFIX, '', $key);
                $result[$key] = $value;
            }
        }

        return $result;
    }
    /**
     * Collect company fields
     *
     * @return $this
     */
    protected function _initCompanyFields()
    {
        $fields = $this->_connection->describeTable($this->_connection->getTableName('company'));
        $fields = array_keys($fields);
        array_push($fields, 'job_title');
        /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
        foreach ($fields as $field) {
            $this->_companyFields[] = $field;
        }
        return $this;
    }

    /**
     * Is attribute contains particular data (not plain customer attribute)
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isAttributeParticular($attributeCode)
    {
        if (
            in_array(str_replace(self::COLUMN_ADDRESS_PREFIX, '', $attributeCode), $this->_addressAttributes) ||
            in_array(str_replace(CompanyComposite::COMPANY_PREFIX, '', $attributeCode), $this->_companyFields)
        ) {
            return true;
        } else {
            return parent::isAttributeParticular($attributeCode);
        }
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        $result = parent::validateRow($rowData, $rowNumber);
        return $result && $this->_validateCompanyRow($rowData, $rowNumber);
    }

    /**
     * Validate address row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    protected function _validateCompanyRow(array $rowData, $rowNumber)
    {
        if ($this->getBehavior() == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
            return true;
        }

        $rowData = $this->_prepareCompanyRowData($rowData);

        $requiredColumns = ['company_name', 'company_email', 'street', 'city', 'country_id', 'region', 'postcode'];
        foreach($requiredColumns as $_col){
            if(empty($rowData[$_col])){
                $this->addRowError(\Magento\CustomerImportExport\Model\Import\Address::ERROR_VALUE_IS_REQUIRED, $rowNumber, $_col);
                return false;
            }
        }
        if (isset($rowData['postcode'])
            && isset($rowData['country_id'])
            && !$this->postcodeValidator->isValid(
                $rowData['country_id'],
                $rowData['postcode']
            )
        ) {
            $this->addRowError(\Magento\CustomerImportExport\Model\Import\Address::ERROR_VALUE_IS_REQUIRED, $rowNumber, 'postcode');
        }

        if (isset($rowData['country_id']) && isset($rowData['region'])) {
            $countryRegions = isset(
                $this->_countryRegions[strtolower($rowData['country_id'])]
            ) ? $this->_countryRegions[strtolower(
                $rowData['country_id']
            )] : [];

            if (!empty($rowData['region']) && !empty($countryRegions) && !isset(
                    $countryRegions[strtolower($rowData['region'])]
                )
            ) {
                $this->addRowError(\Magento\CustomerImportExport\Model\Import\Address::ERROR_INVALID_REGION, $rowNumber, 'region');
            }
        }

        return true;
    }

    /**
     * Initialize country regions hash for clever recognition
     *
     * @return $this
     */
    protected function _initCountryRegions()
    {
        /** @var $region \Magento\Directory\Model\Region */
        foreach ($this->_regionCollection as $region) {
            $countryNormalized = strtolower($region->getCountryId());
            $regionCode = strtolower($region->getCode());
            $regionName = strtolower($region->getDefaultName());
            $this->_countryRegions[$countryNormalized][$regionCode] = $region->getId();
            $this->_countryRegions[$countryNormalized][$regionName] = $region->getId();
            $this->_regions[$region->getId()] = $region->getDefaultName();
        }
        return $this;
    }

    /**
     * Import data rows
     *
     * @return bool
     */
    protected function _importData()
    {
        parent::_importData();
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $newRows = [];
            $updateRows = [];
            $attributes = [];
            $defaults = [];
            // customer default addresses (billing/shipping) data
            $deleteRowIds = [];

            foreach ($bunch as $rowNumber => $rowData) {
                $websiteId = $this->_customerEntity->getWebsiteId($rowData['_website']);
                $customerId = $this->_customerStorage->getCustomerId($rowData['email'], $websiteId);
                if(empty($customerId)){
                    continue;
                }
                $rowData = $this->_prepareCompanyRowData($rowData);

                if(isset($this->_countryRegions[strtolower($rowData['country_id'])][strtolower($rowData['region'])])){
                    $rowData['region_id'] = $this->_countryRegions[strtolower($rowData['country_id'])][strtolower($rowData['region'])];
                    unset($rowData['region']);
                }

                $rowData['super_user_id'] = $customerId;
                if(isset($rowData['job_title'])) {
                    $jobTitle = $rowData['job_title'];
                    unset($rowData['job_title']);
                }
                $this->_connection->delete($this->_connection->getTableName('company'),['company_email=?' => $rowData['company_email']]);
                $this->_connection->insert($this->_connection->getTableName('company'),$rowData);
                $companyId = $this->_connection->fetchOne($this->_connection->select()->from($this->_connection->getTableName('company'),['entity_id'])
                    ->where('company_email=?',$rowData['company_email'])
                );
                if(!empty($companyId)) {
                    $this->_connection->delete($this->_connection->getTableName('company_advanced_customer_entity'),['customer_id=?' => $customerId]);
                    $this->_connection->insert($this->_connection->getTableName('company_advanced_customer_entity'), [
                        'customer_id' => $customerId,
                        'company_id' =>  $companyId,
                        'job_title' => (isset($jobTitle))?$jobTitle:null,
                        'status' => isset($rowData['status'])?$rowData['status']:0,
                        'telephone' => isset($rowData['telephone'])?$rowData['telephone']:null,
                    ]);
                    $this->_connection->insert($this->_connection->getTableName('company_credit'), [
                        'company_id' =>  $companyId,
                        'currency_code' => 'USD',
                    ]);
                    $this->_connection->insert($this->_connection->getTableName('company_roles'), [
                        'company_id' =>  $companyId,
                        'role_name' => 'Default User',
                        'sort_order'    => 0
                    ]);
                    $roleId = $this->_connection->fetchOne($this->_connection->select()->from($this->_connection->getTableName('company_roles'),['role_id'])
                        ->where('company_id=?',$companyId)
                    );
                    $_permissionData = [];
                    foreach($this->_permissions as $_resource => $_permission){
                        array_push($_permissionData,[
                            'role_id'   =>  $roleId,
                            'resource_id'   =>  $_resource,
                            'permission'    =>  $_permission
                        ]);
                    }
                    $this->_connection->insertMultiple($this->_connection->getTableName('company_permissions'), $_permissionData);
                    unset ($_permissionData);
                    $this->_connection->insert($this->_connection->getTableName('company_user_roles'), [
                        'role_id' =>  $roleId,
                        'user_id' => $customerId,
                    ]);
                    $this->_connection->insertOnDuplicate($this->_connection->getTableName('negotiable_quote_company_config'), [
                        'company_entity_id' =>  $companyId,
                        'is_quote_enabled' => 1,
                    ], ['is_quote_enabled']);
                    $tblStatus = $this->_connection->showTableStatus($this->_connection->getTableName('company_structure'));
                    $nextStructureId = $tblStatus['Auto_increment'];
                    $this->_connection->insert($this->_connection->getTableName('company_structure'), [
                        'structure_id' =>  $nextStructureId,
                        'parent_id' => 0,
                        'entity_id' => $customerId,
                        'entity_type' => 0,
                        'path' => $nextStructureId,
                    ]);
                    if(isset($rowData['customer_no']) && $rowData['customer_no']) {
                        $this->_connection->delete($this->_connection->getTableName('forix_payment_customerqueue'), [
                            'customer_no = ?' => $rowData['customer_no']
                        ]);
                    }
                    $this->_connection->insertOnDuplicate($this->_connection->getTableName('forix_payment_customerqueue'), [
                        'customer_email' => $rowData['company_email'],
                        'customer_no' => isset($rowData['customer_no'])?$rowData['customer_no']:null,
                        'status' => isset($rowData['status'])?$rowData['status']:0,
                        'contact_code' => isset($rowData['contact_code'])?$rowData['contact_code']:null,
                    ], ['customer_no', 'status', 'contact_code']);
                }
            }
        }
        return true;
    }
}