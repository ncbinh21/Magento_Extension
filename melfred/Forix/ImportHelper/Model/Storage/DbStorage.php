<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/11/17
 * Time: 4:41 PM
 */
namespace Forix\ImportHelper\Model\Storage;
class DbStorage extends \Magento\UrlRewrite\Model\Storage\DbStorage
{
    protected function insertMultiple($data)
    {
        return true;
    }
}