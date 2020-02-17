<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 7/26/18
 * Time: 10:35 AM
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

use Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType;
use \Magento\CatalogImportExport\Model\Import\Product as ImportProduct;

class UrlKey extends AbstractColumnType
{
    public static $_addedUrlKeys;
    protected $_importConfig;
    protected $_resource;
    protected $_columnByAttributeCode;
    protected $_urlKeyCol;
    protected $_storeCol;
    protected $_filter;

    public function __construct(
        \Magento\ImportExport\Model\Import\Config $importConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filter\FilterManager $filter,
        $linkedWith = ''
    )
    {
        $this->_importConfig = $importConfig;
        $this->_resource = $resource;
        $this->_filter = $filter;
        parent::__construct($linkedWith);
    }


    /**
     * @param \Forix\ImportHelper\Model\Import\AbstractEntity $context
     * @return AbstractColumnType
     */
    public function init($context)
    {
        parent::init($context);

        $entities = $this->_importConfig->getEntities();
        if (isset($entities[$context->getSourceEntityCode()])) {
            $importClass = $entities[$context->getSourceEntityCode()]['model'];
            if ($importClass) {
                $this->_columnByAttributeCode = $importClass::CUSTOM_FIELDS_MAP;
                $this->_urlKeyCol = isset($this->_columnByAttributeCode[ImportProduct::URL_KEY]) ? $this->_columnByAttributeCode[ImportProduct::URL_KEY] : ImportProduct::URL_KEY;
                $this->_storeCol = isset($this->_columnByAttributeCode[ImportProduct::COL_STORE_VIEW_CODE]) ? $this->_columnByAttributeCode[ImportProduct::COL_STORE_VIEW_CODE] : ImportProduct::COL_STORE_VIEW_CODE;

            }
        }
        $this->_initAddedUrlKeys();
        return $this;
    }

    /**
     * @param $value
     * @param $rowData
     * @return bool
     */
    public function validate($value, $rowData)
    {
        return true;
    }

    public function customValue($value, $rawData = [])
    {

        if (empty($value) && !empty($rawData[$this->_columnByAttributeCode[ImportProduct::COL_NAME]])) {
            $value = $rawData[$this->_columnByAttributeCode[ImportProduct::COL_NAME]];
            $value = $this->_filter->translitUrl($value);
        }
        if (!empty($value)) {
            $_storeCode = in_array($rawData[$this->_storeCol], array_keys(self::$_addedUrlKeys)) ? $rawData[$this->_storeCol] : 'admin';
            if (!empty(self::$_addedUrlKeys[$_storeCode]) &&
                array_key_exists($value, self::$_addedUrlKeys[$_storeCode]) &&
                self::$_addedUrlKeys[$_storeCode][$value] != $rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]]) {
                $value = $this->_filter->translitUrl($value . '-' . $rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]]);
            }
            self::$_addedUrlKeys[$_storeCode][$value] = $rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]];
        }
        return $value;
    }

    /**
     * @return $this
     * @throws \Zend_Db_Statement_Exception
     */
    private function _initAddedUrlKeys()
    {
        if (!self::$_addedUrlKeys) {
            self::$_addedUrlKeys = [];
            $entityTypeModel = $this->context->retrieveEntityTypeByName($this->context->getSourceEntityCode());
            if ($entityTypeModel) {
                /**
                 * Check Attributes For Import Product  --------------
                 */
                if ($entityTypeModel instanceof \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType) {
                    $attrParams = $entityTypeModel->retrieveAttributeFromCache(ImportProduct::URL_KEY);
                    $attributeId = $attrParams['attribute_id'];
                    $select = $this->_resource->getConnection()->select()
                        ->from(
                            ['store' => $this->_resource->getTableName('store')],
                            'code'
                        )
                        ->joinInner(['varchartbl' => $this->_resource->getTableName('catalog_product_entity_varchar')], 'store.store_id = varchartbl.store_id', array('value'))
                        ->joinInner(['producttbl' => $this->_resource->getTableName('catalog_product_entity')], 'producttbl.row_id = varchartbl.row_id', array('sku'))
                        ->where('varchartbl.attribute_id = ?', $attributeId);
                    $query = $this->_resource->getConnection()->query($select);
                    while ($row = $query->fetch()) {
                        self::$_addedUrlKeys[$row['code']][$row['value']] = $row['sku'];
                    }
                }
            }
        }
        return $this;
    }
}