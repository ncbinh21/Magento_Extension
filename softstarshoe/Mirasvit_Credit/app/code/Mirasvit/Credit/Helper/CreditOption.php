<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Mirasvit\Credit\Api\Data\ProductOptionCreditInterface;
use Mirasvit\Credit\Ui\DataProvider\Product\Form\Modifier\Composite;

class CreditOption extends AbstractHelper
{
    /**
     * @param ProductOptionCreditInterface $option
     * @param int                          $value
     * @return float
     */
    public function getOptionPrice($option, $value = 0)
    {
        switch ($option->getOptionPriceOptions()) {
            case Composite::PRICE_TYPE_SINGLE:
            case Composite::PRICE_TYPE_FIXED:
                $price = $option->getOptionPrice();
                if ($option->getOptionPriceType() == 'percent') {
                    $price = $price / 100 * $option->getOptionCredits();
                }
                break;
            case Composite::PRICE_TYPE_RANGE:
                $price = $option->getOptionPrice() * $value;
                break;
            default:
                $price = 0;
        }

        return $price;
    }
    /**
     * @param ProductOptionCreditInterface $option
     * @param int                          $value
     * @return int
     */
    public function getOptionCredits($option, $value = 0)
    {
        switch ($option->getOptionPriceOptions()) {
            case Composite::PRICE_TYPE_SINGLE:
            case Composite::PRICE_TYPE_FIXED:
                $credits = $option->getOptionCredits();
                break;
            case Composite::PRICE_TYPE_RANGE:
                $credits = $value;
                break;
            default:
                $credits = 0;
        }

        return $credits;
    }
}
