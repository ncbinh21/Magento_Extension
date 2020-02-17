<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 4:47 PM
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator;

use \Forix\ImportHelper\Model\Import\RawData\AbstractColumnValidator;

abstract class AbstractColumnType extends AbstractColumnValidator
{


    /**
     * @var \Forix\ImportHelper\Model\Import\RawData
     */
    protected $context;

    protected $_linkedWith;

    public function __construct($linkedWith = '')
    {
        $this->_linkedWith = $linkedWith;
    }

    /**
     * @param $value
     * @param $rowData
     * @return bool
     */
    public abstract function validate($value, $rowData);

    /**
     * @param mixed $value
     * @param array $rowData
     * @return bool
     */
    public function isValid($value, $rowData)
    {
        $this->_clearMessages();
        $_value = $value;
        if ($this->_linkedWith) {
            if (isset($rowData[$this->_linkedWith]) && $rowData[$this->_linkedWith]) {
                return $this->validate($_value, $rowData);
            }
        } else {
            return $this->validate($_value, $rowData);
        }
        return true;
    }


    protected $_columnName;


    public function getColumnName()
    {
        return $this->_columnName;
    }


    public function setColumnName($columnName)
    {
        $this->_columnName = $columnName;
    }

    /**
     * @param \Forix\ImportHelper\Model\Import\AbstractEntity $context
     * @return $this
     */
    public function init($context)
    {
        $this->context = $context;
        return $this;
    }

    public function customValue($value, $rawData = [])
    {
        return $value;
    }
}