<?php
namespace Forix\SaleAdvance\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

class PreventRegionConfigProvider implements ConfigProviderInterface
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfiguration;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration
    ) {
        $this->scopeConfiguration = $scopeConfiguration;
    }

    /**
     * \
     * @return array|mixed
     */
    public function getConfig()
    {
        $regionPreventList['region_prevent_list'] = $this->scopeConfiguration
			->getValue('sales/forix_saleadvance/region_prevent_list', ScopeInterface::SCOPE_STORE);

        return $regionPreventList;
    }
}