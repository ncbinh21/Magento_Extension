<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/10/17
 * Time: 2:35 PM
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;


class Features extends \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType
{
    public function validate($value, $rowData)
    {
        return true;
    }

    public function customValue($value, $rawData = [])
    {
        if($value){
            $value = nl2br($value);
        }
        return $value;
    }
}