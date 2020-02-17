<?php
/**
 * Created by Hidro Le
 * Project: LoveMyBubbles
 * Job Title: Magento Develop
 * Date: 22/09/2017
 * Time: 11:05
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

use Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType;


class EmptyValidator extends AbstractColumnType
{

    protected $_emailList = [];

    public function validate($_value, $rawData)
    {
        if (empty($_value)) {
            $this->_addMessages(['Validator: (' . ($this->getColumnName()) . ') is empty.']);
            return false;
        }
        return true;
    }

    public function customValue($value, $rawData = [])
    {
        if (empty($this->getMessages())) {
            return trim($value);
        }
        return $value;
    }

}