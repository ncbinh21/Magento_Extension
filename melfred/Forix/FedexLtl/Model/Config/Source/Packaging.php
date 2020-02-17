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

class Packaging extends AbstractClass
{

    protected $_classes = [
            'BAG',
            'BARREL',
            'BASKET',
            'BOX',
            'BUCKET',
            'BUNDLE',
            'CARTON',
            'CASE',
            'CONTAINER',
            'CYLINDER',
            'DRUM',
            'ENVELOPE',
            'HAMPER',
            'OTHER',
            'PAIL',
            'PALLET',
            'PIECE',
            'REEL',
            'ROLL',
            'SKID',
            'TANK',
            'TUBE'
        ];

}
