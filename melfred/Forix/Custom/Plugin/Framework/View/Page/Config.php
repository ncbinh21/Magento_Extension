<?php

namespace Forix\Custom\Plugin\Framework\View\Page;

class Config
{
    public function beforeAddRemotePageAsset(\Magento\Framework\View\Page\Config $subject, $url, $contentType, array $properties = [], $name = null)
    {
        $url  = rtrim($url, '/');
        return [$url, $contentType, $properties, $name];
    }
}