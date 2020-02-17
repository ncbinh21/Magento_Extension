<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 24/07/2018
 * Time: 12:21
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;


class MetaDescription extends \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType
{

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
        $this->_clearMessages();
        return substr($value,  0, 254);
    }
}