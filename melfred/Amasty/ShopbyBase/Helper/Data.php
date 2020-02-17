<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const SHOPBY_MODULE_NAME = 'Amasty_Shopby';

    /**
     * @var \Magento\Framework\Module\ModuleList
     */
    private $moduleList;

    /**
     * @var \Magento\Framework\Module\ModuleResource
     */
    private $moduleResource;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Framework\Module\ModuleList $moduleList
     * @param \Magento\Framework\Module\ModuleResource $moduleResource
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Module\ModuleList $moduleList,
        \Magento\Framework\Module\ModuleResource $moduleResource
    ) {
        parent::__construct($context);
        $this->moduleList = $moduleList;
        $this->moduleResource = $moduleResource;
    }

    /**
     * @return null
     */
    public function getShopbyVersion()
    {
        return $this->moduleResource->getDbVersion(self::SHOPBY_MODULE_NAME);
    }

    /**
     * @return bool
     */
    public function isShopbyInstalled()
    {
        return ($this->moduleList->getOne(self::SHOPBY_MODULE_NAME) !== null)
            && $this->getShopbyVersion();
    }
}
