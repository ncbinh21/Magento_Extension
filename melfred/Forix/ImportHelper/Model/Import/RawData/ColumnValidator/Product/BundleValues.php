<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 7/26/18
 * Time: 12:28 PM
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

use Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType;
use \Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product\RelationAttributes;

class BundleValues extends AbstractColumnType
{
    protected $_importConfig;
    protected $_categoriesSeparator;
    protected $_productTypeCol;
    protected $_relationsSource;
    protected $_columnByAttributeCode;

    public function __construct(
        \Forix\ImportHelper\Model\Import\Source\Relations $relationsSource,
        \Magento\ImportExport\Model\Import\Config $importConfig,
        $linkedWith = '')
    {
        $this->_relationsSource = $relationsSource;
        $this->_importConfig = $importConfig;
        parent::__construct($linkedWith);
    }

    /**
     * @param \Forix\ImportHelper\Model\Import\AbstractEntity $context
     * @return $this
     */
    public function init($context)
    {
        parent::init($context);

        $entities = $this->_importConfig->getEntities();
        if (isset($entities[$context->getSourceEntityCode()])) {
            $importClass = $entities[$context->getSourceEntityCode()]['model'];
            if ($importClass) {
                $this->_columnByAttributeCode = $importClass::CUSTOM_FIELDS_MAP;
                $this->_categoriesSeparator = $importClass::CATEGORIES_SEPARATOR;
                $this->_productTypeCol = isset($this->_columnByAttributeCode[ImportProduct::COL_TYPE]) ? $this->_columnByAttributeCode[ImportProduct::COL_TYPE] : ImportProduct::COL_TYPE;

            }
        }
        return $this;
    }

    public function customValue($value, $rawData = [])
    {
        /*-----------------------------------*/
        if (isset($rawData[$this->_productTypeCol]) && $rawData[$this->_productTypeCol] == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $bundle_values = [];
            foreach ($this->_relationsSource->getRelationCollection() as $relation) {
                if (\Magento\Bundle\Model\Product\Type::TYPE_CODE == $relation->getData('relation_type') &&
                    in_array( $rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]], $relation->getData('sku_parent'))) {
                    array_push($bundle_values, "name={$relation->getData(RelationAttributes::ATTRIBUTE_CODE)},type=select,required={$relation->getData('required')},sku={$relation->getData('sku')},price=0.0000,default=0,default_qty=1.0000,price_type=fixed,selection_altname={$relation->getData('attribute_value')}");
                }
            }
            if (count($bundle_values)) {
                $value = implode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $bundle_values);
            }
        }
        return $value;
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
}