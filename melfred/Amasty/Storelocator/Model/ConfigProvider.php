<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Storelocator
 */


namespace Amasty\Storelocator\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Scope config Provider model
 */
class ConfigProvider
{
    /**
     * xpath prefix of module
     */
    const PATH_PREFIX = 'amlocator';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    const META_TITLE = 'general/meta_title';

    const META_DESCRIPTION = 'general/meta_description';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigProvider constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * An alias for scope config with default scope type SCOPE_STORE
     *
     * @param string $key
     * @param string|null $scopeCode
     * @param string $scopeType
     *
     * @return string|null
     */
    public function getValue($key, $scopeCode = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(self::PATH_PREFIX . '/' . $key, $scopeType, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return string
     */
    public function getMetaTitle($scopeCode = null)
    {
        return $this->getValue(self::META_TITLE, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return string
     */
    public function getMetaDescription($scopeCode = null)
    {
        return $this->getValue(self::META_DESCRIPTION, $scopeCode);
    }
}
