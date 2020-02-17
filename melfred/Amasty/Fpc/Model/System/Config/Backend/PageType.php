<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\System\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PageType extends ArraySerialized
{
    /**
     * Fix Magento bug with arrays in default config
     * @return string
     */
    public function getOldValue()
    {
        $value = $this->_config->getValue(
            $this->getPath(),
            $this->getScope() ?: ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->getScopeCode()
        );

        if (is_array($value)) {
            $value = serialize($value);
        }

        return (string)$value;
    }
}
