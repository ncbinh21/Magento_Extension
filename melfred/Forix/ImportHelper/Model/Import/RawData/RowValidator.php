<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/1/17
 * Time: 10:42 AM
 */

namespace Forix\ImportHelper\Model\Import\RawData;

use \Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use \Magento\Framework\Validator\AbstractValidator;
use Zend_Validate_Exception;

class RowValidator extends AbstractValidator implements \Magento\Framework\Validator\ValidatorInterface
{

    /**
     * @var [ColumnValidatorInterface[]]|[AbstractColumnValidator[]]
     */
    protected $validators = [];

    /**
     * @var \Forix\ImportHelper\Model\Import\RawData
     */
    protected $context;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var array
     */
    protected $_uniqueAttributes;

    /**
     * @var array
     */
    protected $_rowData;

    /*
     * @var string|null
     */
    protected $invalidAttribute;

    /**
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param ColumnValidatorInterface[] $validators
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        $validators = []
    )
    {
        $this->string = $string;
        foreach ($validators as $entityCode => $_validators) {
            foreach ($_validators as $attr => $validator) {
                if (!($validator instanceof \Forix\ImportHelper\Model\Import\RawData\ColumnValidatorInterface)) {
                    throw new \Exception("Column Validator Must be Install Of RowValidatorInterface");
                }
                $this->validators[$entityCode][$attr] = $validator;
            }
        }
    }

    /**
     * @param mixed $attrCode
     * @param string $type
     * @return bool
     */
    protected function textValidation($attrCode, $type)
    {
        $val = $this->string->cleanString($this->_rowData[$attrCode]);
        if ($type == 'text') {
            $valid = $this->string->strlen($val) < ImportProduct::DB_MAX_TEXT_LENGTH;
        } else {
            $valid = $this->string->strlen($val) < ImportProduct::DB_MAX_VARCHAR_LENGTH;
        }
        if (!$valid) {
            $this->_addMessages(
                [
                    sprintf(
                        $this->context->retrieveMessageTemplate(
                            ColumnValidatorInterface::ERROR_EXCEEDED_MAX_LENGTH
                        ),
                        $attrCode
                    )
                ]
            );
        }
        return $valid;
    }

    /**
     * Check if value is valid attribute option
     *
     * @param string $attrCode
     * @param array $possibleOptions
     * @param string $value
     * @return bool
     */
    private function validateOption($attrCode, $possibleOptions, $value)
    {
        if (!isset($possibleOptions[strtolower($value)])) {
            $this->_addMessages(
                [
                    sprintf(
                        $this->context->retrieveMessageTemplate(
                            ColumnValidatorInterface::ERROR_INVALID_ATTRIBUTE_OPTION
                        ),
                        $attrCode
                    )
                ]
            );
            return false;
        }
        return true;
    }

    /**
     * @param mixed $attrCode
     * @param string $type
     * @return bool
     */
    protected function numericValidation($attrCode, $type)
    {
        $val = trim($this->_rowData[$attrCode]);
        if ($type == 'int') {
            $valid = (string)(int)$val === $val;
        } else {
            $valid = is_numeric($val);
        }
        if (!$valid) {
            $this->_addMessages(
                [
                    sprintf(
                        $this->context->retrieveMessageTemplate(ColumnValidatorInterface::ERROR_INVALID_ATTRIBUTE_TYPE),
                        $attrCode,
                        $type
                    )
                ]
            );
        }
        return $valid;
    }

    /**
     * @param string $attrCode
     * @param array $attributeParams
     * @param array $rowData
     * @return bool
     */
    public function isRequiredAttributeValid($attrCode, array $attributeParams, array $rowData)
    {
        $doCheck = false;
        if ($attrCode == ImportProduct::COL_SKU) {
            $doCheck = true;
        } elseif ($attrCode == 'price') {
            $doCheck = false;
        } elseif ($attributeParams['is_required'] && $this->getRowScope($rowData) == ImportProduct::SCOPE_DEFAULT
            && $this->context->getBehavior() != \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE
        ) {
            $doCheck = true;
        }

        return $doCheck ? isset($rowData[$attrCode]) && strlen(trim($rowData[$attrCode])) : true;
    }

    /**
     * @param string $attrCode
     * @param array $attrParams
     * @param array $rowData
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function isAttributeValid($attrCode, array $attrParams, array $rowData)
    {
        $this->_rowData = $rowData;
        if (isset($rowData[ImportProduct::COL_TYPE]) && !empty($attrParams['apply_to'])
            && !in_array($rowData[ImportProduct::COL_TYPE], $attrParams['apply_to'])
        ) {
            return true;
        }

        if (!$this->isRequiredAttributeValid($attrCode, $attrParams, $rowData)) {
            $valid = false;
            $this->_addMessages(
                [
                    sprintf(
                        $this->context->retrieveMessageTemplate(
                            ColumnValidatorInterface::ERROR_VALUE_IS_REQUIRED
                        ),
                        $attrCode
                    )
                ]
            );
            return $valid;
        }

        if (!strlen(trim($rowData[$attrCode]))) {
            return true;
        }
        switch ($attrParams['type']) {
            case 'varchar':
            case 'text':
                $valid = $this->textValidation($attrCode, $attrParams['type']);
                break;
            case 'decimal':
            case 'int':
                $valid = $this->numericValidation($attrCode, $attrParams['type']);
                break;
            case 'select':
                $valid = true; //Skip Check Select Options.
                break;
            case 'boolean':
                $valid = $this->validateOption($attrCode, $attrParams['options'], $rowData[$attrCode]);
                break;
            case 'datetime':
                $val = trim($rowData[$attrCode]);
                $valid = strtotime($val) !== false;
                if (!$valid) {
                    $this->_addMessages([
                        sprintf(
                            $this->context->retrieveMessageTemplate(
                                ColumnValidatorInterface::ERROR_INVALID_ATTRIBUTE_TYPE
                            ),
                            $attrCode
                        )
                    ]);
                }
                break;
            default:
                $valid = true;
                break;
        }

        if (!empty($attrParams['is_unique'])) {
            if (isset($this->_uniqueAttributes[$attrCode][$rowData[$attrCode]])
                && ($this->_uniqueAttributes[$attrCode][$rowData[$attrCode]] != $rowData[ImportProduct::COL_SKU])
            ) {
                $this->_addMessages([ColumnValidatorInterface::ERROR_DUPLICATE_UNIQUE_ATTRIBUTE]);
                return false;
            }
            $this->_uniqueAttributes[$attrCode][$rowData[$attrCode]] = $rowData[ImportProduct::COL_SKU];
        }

        if (!$valid) {
            $this->setInvalidAttribute($attrCode);
        }

        return (bool)$valid;

    }

    /**
     * @param string|null $attribute
     * @return void
     */
    protected function setInvalidAttribute($attribute)
    {
        $this->invalidAttribute = $attribute;
    }

    /**
     * @return string
     */
    public function getInvalidAttribute()
    {
        return $this->invalidAttribute;
    }

    public function validateCustomEntityRow($columnName, $rowData)
    {
        return true;
    }

    /**
     * @return array()
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function isValidRowData()
    {
        $this->_clearMessages();
        $this->setInvalidAttribute(null);
        if (isset($this->validators[$this->context->getSourceEntityCode()])) {
            /**
             * @var $validator \Forix\ImportHelper\Model\Import\RawData\ColumnValidatorInterface
             */
            $validators = $this->validators[$this->context->getSourceEntityCode()];
            foreach ($validators as $column => $validator) {
                if(isset($this->_rowData[$column])) {
                    $validator->setColumnName($column);
                    $validator->isValid($this->_rowData[$column], $this->_rowData);
                    $this->_rowData[$column] = $validator->customValue($this->_rowData[$column], $this->_rowData);
                    if ($validator->getMessages()) {
                        $this->_addMessages($validator->getMessages());
                    }
                }
            }
        }
        if (isset($this->_rowData[ImportProduct::COL_TYPE])) {
            $entityTypeModel = $this->context->retrieveEntityTypeByName(strtolower($this->_rowData[ImportProduct::COL_TYPE]));
            if ($entityTypeModel) {
                if ($entityTypeModel instanceof \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType) {
                    foreach ($this->_rowData as $columnName => &$attrValue) {
                        $attrCode = $this->context->getAttributeCode($columnName);
                        $attrValue = trim($attrValue);
                        $attrParams = $entityTypeModel->retrieveAttributeFromCache($attrCode);
                        if ($attrParams) {
                            $this->isAttributeValid($columnName, $attrParams, $this->_rowData);
                        }
                    }
                } else {
                    foreach ($this->_rowData as $columnName => $attrValue) {
                        $this->validateCustomEntityRow($columnName, $this->_rowData);
                    }
                }
            }
        }

        return $this->_rowData;
    }

    /**
     * Add messages
     *
     * @param array $messages
     * @return void
     */
    protected function _addMessages(array $messages)
    {
        $this->_messages = array_merge_recursive($this->_messages, $messages);
    }

    /**
     * @param array|mixed $rowData
     * @return array
     */
    public function isValid($rowData)
    {
        $this->_rowData = array_change_key_case($rowData, CASE_LOWER);
        return $this->isValidRowData();
    }

    /**
     * Obtain scope of the row from row data.
     *
     * @param array $rowData
     * @return int
     */
    public function getRowScope(array $rowData)
    {
        if (empty($rowData[ImportProduct::COL_STORE])) {
            return ImportProduct::SCOPE_DEFAULT;
        }
        return ImportProduct::SCOPE_STORE;
    }

    /**
     * @param \Forix\ImportHelper\Model\Import\AbstractEntity $context
     * @return $this
     */
    public function init($context)
    {
        $this->context = $context;
        foreach ($this->validators as $entityCode => $validators) {
            if ($context->getSourceEntityCode() == $entityCode) {
                foreach ($validators as $validator) {
                    /**
                     * @var $validator \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType
                     */
                    $validator->init($context);
                }
            }
        }
    }
}