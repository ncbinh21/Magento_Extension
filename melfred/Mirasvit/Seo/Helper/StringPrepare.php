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

class StringPrepare extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param \Magento\Framework\Stdlib\StringUtils  $string
     *
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string
    ) {
        $this->string = $string;
    }

    /**
     * Truncate string (don't truncate words)
     *
     * @param string $str
     * @param string $length
     * @param string $page
     * @return string
     */
    public function getTruncatedString($str, $length, $page)
    {
        $usePageNumber = false;
        $delimiterSymbols = [';', '', ' ', ',', '.', '!', '?', "\n", "\r", "\r\n"];
        $delimiterEndSymbols = [';', '', ' ', ',', "\n", "\r", "\r\n"];

        if (strpos($str, ' | Page ' . $page) !== false) {
            $str = str_replace(' | Page ' . $page, '', $str);
            $length -= strlen(' | Page ' . $page);
            $usePageNumber = true;
        }

        $truncatedString = $this->string->substr($str, 0, $length);

        if (($finalStringPart = str_replace($truncatedString, '', $str))
            && !in_array(substr($finalStringPart, 0, 1), $delimiterSymbols)
        ) {
            $truncatedStringArray = explode(' ', $truncatedString);
            if (count($truncatedStringArray) > 1) {
                array_pop($truncatedStringArray);
            }
            $truncatedString = implode(' ', $truncatedStringArray);
            if (in_array(substr($truncatedString, -1), $delimiterEndSymbols)) {
                $truncatedString = substr($truncatedString, 0, -1);
            }
        }

        if ($usePageNumber) {
            $truncatedString .= ' | Page ' . $page;
        }

        return $truncatedString;
    }
}
