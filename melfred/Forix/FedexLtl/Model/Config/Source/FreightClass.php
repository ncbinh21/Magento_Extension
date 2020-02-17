<?php
/**
 * Created by PhpStorm.
 * User: nghia
 * Date: 20/03/2019
 * Time: 19:00
 */

namespace Forix\FedexLtl\Model\Config\Source;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;

class FreightClass extends AbstractClass
{

    protected $_classes = [
            "CLASS_050",
            "CLASS_055",
            "CLASS_060",
            "CLASS_065",
            "CLASS_070",
            "CLASS_077_5",
            "CLASS_085",
            "CLASS_092_5",
            "CLASS_100",
            "CLASS_110",
            "CLASS_125",
            "CLASS_150",
            "CLASS_175",
            "CLASS_200",
            "CLASS_250",
            "CLASS_300",
            "CLASS_400",
            "CLASS_500"
        ];
}
