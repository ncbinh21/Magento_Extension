<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */

/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Model\Source\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Forix\ProductLabel\Model\Source\Config
 */
class Type extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Rule label Status values
     */
    const TYPE_NEW_LABEL = 1;

    const TYPE_SALE_LABEL = 2;

    const TYPE_CUSTOM_LABEL = 3;

    const TYPE_BEST_SELLER_LABEL = 4;

    const TYPE_COMING_SOON_LABEL = 5;


    /**#@-*/

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::TYPE_NEW_LABEL => __('New'),
            self::TYPE_SALE_LABEL => __('Sale'),
            self::TYPE_BEST_SELLER_LABEL => __('Best Seller'),
            self::TYPE_COMING_SOON_LABEL => __('Coming Soon'),
            self::TYPE_CUSTOM_LABEL => __('Custom')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
