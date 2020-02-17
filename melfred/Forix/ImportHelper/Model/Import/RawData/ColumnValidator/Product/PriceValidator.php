<?php
/**
 * Created by Hidro Le
 * Project: LoveMyBubbles
 * Job Title: Magento Develop
 * Date: 22/09/2017
 * Time: 11:05
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

use \Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use \Magento\GroupedProduct\Model\Product\Type\Grouped;
use \Magento\Bundle\Model\Product\Type as Bundle;

class PriceValidator extends \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType
{

    public function validate($_value, $rawData)
    {
        $productType = strtolower($rawData['product_type']);
        if (Configurable::TYPE_CODE !== $productType &&
            Grouped::TYPE_CODE !== $productType &&
            Bundle::TYPE_CODE !== $productType
        ) {
            if (empty($_value)) {
                $this->_addMessages(['Validator: (' . ($this->getColumnName()) . ') is empty.']);
                return false;
            }
        }
        return true;
    }

    public function customValue($value, $rawData = [])
    {
        if (count($this->getMessages())) {
            $this->_clearMessages();
            return 0;
        }
        return str_replace(' ', '', str_replace(',', '', str_replace('$', '', $value)));
    }
}