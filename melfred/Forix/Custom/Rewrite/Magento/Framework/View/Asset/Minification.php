<?php
namespace Forix\Custom\Rewrite\Magento\Framework\View\Asset;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;

class Minification extends \Magento\Framework\View\Asset\Minification
{
    public function getExcludes($contentType)
    {
        $excludes = parent::getExcludes($contentType);
        $excludes[] = 'sagepayments.net';
        $excludes[] = 'sagepayments';
        return $excludes;
    }
}
