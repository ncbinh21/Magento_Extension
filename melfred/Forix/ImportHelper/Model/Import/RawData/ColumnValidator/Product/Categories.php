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

class Categories extends EmptyValidator
{
    protected $_importConfig;
    protected $_categoriesSeparator = ',';

    public function __construct(
        \Magento\ImportExport\Model\Import\Config $importConfig,
        $linkedWith = '')
    {

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
        return $this;
    }

    public function customValue($value, $rawData = [])
    {
        /*-----------------------------------*/
        if (!empty($value)) {
            $value = rtrim($value, $this->_categoriesSeparator);
            $value = explode($this->_categoriesSeparator, $value);
            $value = array_map('trim', $value);
            foreach ($value as &$_cat) {
                $_cat = 'Default Category' . \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor::DELIMITER_CATEGORY . $_cat;
            }
            $value = implode($this->_categoriesSeparator, $value);
        }
        return $value;
    }
}