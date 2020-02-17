<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Helper;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\App\Helper\Context;

class OptionSetting extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var  \Amasty\ShopbyBase\Model\OptionSettingFactory
     */
    private $settingFactory;

    /**
     * @var  Repository
     */
    private $repository;

    /**
     * OptionSetting constructor.
     * @param Context $context
     * @param \Amasty\ShopbyBase\Model\OptionSettingFactory $settingFactory
     * @param Repository $repository
     */
    public function __construct(
        Context $context,
        \Amasty\ShopbyBase\Model\OptionSettingFactory $settingFactory,
        Repository $repository
    ) {
        parent::__construct($context);
        $this->settingFactory = $settingFactory;
        $this->repository = $repository;
    }

    /**
     * @param string $value
     * @param string $filterCode
     * @param int $storeId
     * @return OptionSettingInterface
     */
    public function getSettingByValue($value, $filterCode, $storeId)
    {
        /** @var \Amasty\ShopbyBase\Model\OptionSetting $setting */
        $setting = $this->settingFactory->create();
        $setting = $setting->getByParams($filterCode, $value, $storeId);

        if (!$setting->getId()) {
            $setting->setFilterCode($filterCode);
            $attributeCode = substr($filterCode, 5);
            $attribute = $this->repository->get($attributeCode);
            $attribute->setStoreId($storeId);
            foreach ($attribute->getOptions() as $option) {
                if ($option->getValue() == $value) {
                    $this->initiateSettingByOption($setting, $option);
                    break;
                }
            }
        }

        return $setting;
    }

    /**
     * @param OptionSettingInterface $setting
     * @param AttributeOptionInterface $option
     * @return $this
     */
    protected function initiateSettingByOption(
        OptionSettingInterface $setting,
        AttributeOptionInterface $option
    ) {
        $setting->setValue($option->getValue());
        $setting->setTitle($option->getLabel());
        $setting->setMetaTitle($option->getLabel());
        return $this;
    }
}
