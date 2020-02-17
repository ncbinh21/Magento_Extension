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



namespace Mirasvit\Seo\Helper;

class Snippets extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @param string $code
     * @return bool|string
     */
    public function prepareDimensionCode($code)
    {
        $validCodes = [
            'dm' => 'DMT',
            'decimetre' => 'DMT',
            'cm' => 'CMT',
            'centimetre' => 'CMT',
            'mm' => 'MMT',
            'millimetre' => 'MMT',
            'hm' => 'HMT',
            'hectometre' => 'HMT',
            'nm' => 'C45',
            'nanometre' => 'C45',
            'dam' => 'A45',
            'decametre' => 'A45',
            'fth' => 'AK',
            'fathom' => 'AK',
            'in' => 'INH',
            'inch' => 'INH',
            'ft' => 'FOT',
            'foot' => 'FOT',
            'yd' => 'YRD',
            'yard' => 'YRD',
            'fur' => 'M50',
            'furlong' => 'M50',
        ];

        $code = strtolower($code);
        if (isset($validCodes[$code])) {
            return $validCodes[$code];
        }

        $code = strtoupper($code);
        if (in_array($code, $validCodes)) {
            return $code;
        }

        return false;
    }
}
