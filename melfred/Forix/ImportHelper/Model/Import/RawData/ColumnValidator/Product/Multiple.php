<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/10/17
 * Time: 2:35 PM
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;


class Multiple extends \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType
{
    public function validate($value, $rowData)
    {
        return true;
    }

    public function customValue($value, $rawData = [])
    {
        if($value){
            $values = explode(",", $value);
            $values = array_map('trim', $values);
            if(count($values)){
                $value = implode("|", $values);
            }
        }
        return $value;
    }
}