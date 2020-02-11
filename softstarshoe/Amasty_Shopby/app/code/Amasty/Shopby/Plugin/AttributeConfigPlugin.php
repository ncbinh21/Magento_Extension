<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin;

class AttributeConfigPlugin
{
    public function aroundGetAttribute(\Magento\Eav\Model\Config $subject, \Closure $closure, $entityType, $code)
    {
        if (is_string($code) &&
            ($pos = strpos($code, \Amasty\Shopby\Model\Search\RequestGenerator::FAKE_SUFFIX)) !== false) {
            $code = substr($code, 0, $pos);
        }
        return $closure($entityType, $code);
    }
}
