<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 04/09/2018
 * Time: 16:22
 */
namespace Forix\Custom\Helper;
class HtmlSubTagHelper  extends \Magento\Framework\App\Helper\AbstractHelper
{


    protected $_tagNames = [
        'div',
        'span',
        'a',
        'li',
        'label',
        'p',
        'strong',
        'dt',
        'dd',
        'b',
        'small',
        'i',
        'em',
        'td',
        'tr',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'em',
    ];


    /**
     * @param string $body
     * @return bool
     */
    protected function hasDoctype($body)
    {
        $doctypeCode = ['<!doctype html', '<html', '<?xml'];
        foreach ($doctypeCode as $doctype) {
            if (stripos($body, $doctype) === 0) {
                return true;
            }
        }
        return false;
    }

    public function addSubTag($htmlContent){
        if($this->hasDoctype($htmlContent)) {
            $re = '/(<(' . (implode("|", $this->_tagNames)) . ').*?>|<\/(th|div).*?>)\K[^<]*/';
            $htmlContent = preg_replace_callback(
                $re,
                function ($matches) {
                    $match = $matches[0];
                    $text = str_replace(['®',
                        '&#174;',
                        '&reg;'], ['<sup>®</sup>', '<sup>®</sup>', '<sup>®</sup>'], $match);
                    return $text;
                }, $htmlContent);
        }
        return $htmlContent;
    }
}