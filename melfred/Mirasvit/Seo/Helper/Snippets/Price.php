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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Helper\Snippets;

class Price extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * Format snippets price to match Google guidelines
     * @param string $price
     * @return string
     */
    public function formatPriceValue($price) {
        if ($price) {
            if (substr_count($price, ',') + substr_count($price, '.') > 1) {
                // if 6,451,00 or 6.451.00 --> 6451.00
                if (substr_count($price, ',') == 2 || substr_count($price, '.') == 2) {
                    $price = str_replace(',', '.', $price);
                    $price = preg_replace('/\./', '', $price, 1);
                }
                // if 6,451.00 --> 6451.00
                elseif (strpos($price, ',') < strpos($price, '.')) {
                    $price = str_replace(',', '', $price);
                // if 6.451,00 --> 6451.00
                } elseif (strpos($price, ',') > strpos($price, '.')) {
                    $price = str_replace('.', '', $price);
                    $price = str_replace(',', '.', $price);
                }
                // if 3,99 --> 3.99
            } elseif (strpos($price, ',') !== false) {
                $price = str_replace(',', '.', $price);
            }

            return $price;
        }

        return false;
    }
}
