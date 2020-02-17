<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 7/26/18
 * Time: 12:28 PM
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

use \Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType;
class AssociatedSkus extends AbstractColumnType
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
    public function validate($_value, $rawData)
    {
        return true;
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
        if (isset($result[$this->_productTypeCol])) {
            $rawData[$this->_productTypeCol] = strtolower(trim($rawData[$this->_productTypeCol]));
        }
        /*-----------------------------------*/
        if (isset($rawData[$this->_productTypeCol]) && $rawData[$this->_productTypeCol] == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $associated_skus = [];
            foreach ($this->_relationsSource->getRelationCollection() as $relation) {
                if (in_array($rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]], $relation->getData('sku_parent')) ) {
                    array_push($associated_skus,"{$relation->getData('sku')}=0.0000");
                }
            }
            if (count($associated_skus)) {
                $value = implode(',',$associated_skus);
            }
        }
        return $value;
    }
}