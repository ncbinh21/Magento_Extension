<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 19/11/2018
 * Time: 16:32
 */

namespace Forix\Payment\Model\Service\Sage100;


class AbstractModel
{
    public $OtherFields = [];

    public function addOrderField($name, $value)
    {
        $field = new Field();
        $field->MasFieldName = $name;
        $field->Value = $value;
        array_push($this->OtherFields, $field);
    }
}