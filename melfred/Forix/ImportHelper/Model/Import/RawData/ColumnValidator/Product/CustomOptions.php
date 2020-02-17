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
use \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product\RelationAttributes;

class CustomOptions extends AbstractColumnType
{

    /**
     * @var \Magento\ImportExport\Model\Import\Config
     */
    protected $_importConfig;
    protected $_columnByAttributeCode;
    protected $_attributeCodeByColumn;
    protected $_productTypeCol;
    protected $_relationsSource;

    public function __construct(
        \Forix\ImportHelper\Model\Import\Source\Relations $relationsSource,
        \Magento\ImportExport\Model\Import\Config $importConfig,
        $linkedWith = ''
    )
    {
        $this->_importConfig = $importConfig;
        $this->_relationsSource = $relationsSource;
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
            }
        }
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
        $result['custom_options'] = [];
        foreach ($this->_relationsSource->getRelationCollection() as $relation) {
            if ('custom_options' == $relation->getData('relation_type') &&
                in_array( $rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]], $relation->getData('sku_parent'))) {
                array_push($result['custom_options'], "name={$relation->getData(RelationAttributes::ATTRIBUTE_CODE)},type=drop_down,required={$relation->getData('required')},price={$relation->getData('price')},price_type=fixed,sku={$relation->getData('sku')},option_title={$relation->getData('attribute_value')}");
            }
        }
        if(count($result['custom_options']) > 0) {
            $value = implode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $result['custom_options']);
        }
        return $value;
    }
}