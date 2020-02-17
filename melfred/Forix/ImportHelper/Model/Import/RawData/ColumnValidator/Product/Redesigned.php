<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 23/07/2018
 * Time: 17:35
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;
use Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType;

class Redesigned extends AbstractColumnType
{
    protected $template = <<<HTML
<div class="timeline">
  <div class="item-timeline">
    <div class="content">
      <h4 class="time"><span>{redesigned_date}</span></h4>
      <p>{redesigned_description}</p>
    </div>
  </div>
</div>
HTML;

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
        if($rawData['redesigned_description'] || $rawData['redesigned_date']) {
            $valueTemp = str_replace('{redesigned_description}', $rawData['redesigned_description'], $this->template);
            $valueTemp = str_replace('{redesigned_date}', $rawData['redesigned_date'], $valueTemp);
            return $valueTemp;
        }
        return $value;
    }
}