<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 02/08/2018
 * Time: 10:35
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;


class ArticleValidator extends EmptyValidator
{
    protected $_itemFormat = '<li><a href="{{store url={url}}}">{title}</a></li>';
    protected $_replaceMap = ['{url}', '{title}'];

    /**
     * @param $value
     * @param $rowData
     * @return bool
     */
    public function validate($value, $rowData)
    {
        if (parent::validate($value, $rowData)) {
            $re = '/.*?\:{2}.*?\|/';
            if ('|' !== substr($value, -1)) {
                $value .= '|';
            }
            $matchCount = preg_match_all($re, $value);
            if (false !== $matchCount) {
                if ($matchCount !== 1 && $matchCount % 2 !== 0) {
                    $this->_addMessages([self::ERROR_INVALID_FORMAT . ":" . "{$value}"]);
                    return false;
                }
                return true;
            }
            $this->_addMessages([self::ERROR_INVALID_FORMAT . ":" . "{$value}"]);
        }
        return false;
    }

    public function customValue($value, $rawData = [])
    {
        if (empty($this->getMessages())) {
            $newValue = [];
            $items = explode('|', $value);
            $replaceItems = $this->_replaceMap;
            foreach ($items as $item) {
                if ($item) {
                    list($url, $title) = explode(":", $item);
                    if (strpos('http', $url)) {
                        $replaceItems[0] = '{{store url={url}}}';
                    }
                    $itemHtml = str_replace($replaceItems, [$url, $title], $this->_itemFormat);
                    $newValue[] = $itemHtml;
                }
            }
            return '<ul>' . (implode('', $newValue)) . '</ul>';
        } else {
            $this->_clearMessages();
        }
        return $value;
    }
}