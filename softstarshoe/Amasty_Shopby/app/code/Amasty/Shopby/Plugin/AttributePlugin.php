<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin;

use Amasty\ShopbyBase\Model\Cache\Type;
use Amasty\ShopbyBase\Model\FilterSetting;
use Amasty\ShopbyBase\Model\FilterSettingFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\App\Cache\TypeListInterface;

class AttributePlugin
{
    /**
     * @var  FilterSetting
     */
    protected $setting;

    /**
     * @var \Magento\Config\Model\Config\Factory
     */
    protected $configFactory;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $filterSettingHelper;

    /**
     * @var  TypeListInterface
     */
    private $cacheTypeList;

    public function __construct(
        FilterSettingFactory $settingFactory,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper,
        TypeListInterface $typeList
    ) {
        $this->setting = $settingFactory->create();
        $this->configFactory = $configFactory;
        $this->filterSettingHelper = $filterSettingHelper;
        $this->cacheTypeList = $typeList;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        $multipleData = ['categories_filter', 'attributes_filter', 'attributes_options_filter'];

        foreach ($multipleData as $multiple) {
            if (array_key_exists($multiple, $data) && is_array($data[$multiple])) {
                $data[$multiple] = implode(',', array_filter($data[$multiple], [$this, 'callbackNotEmpty']));
            } elseif (!array_key_exists($multiple, $data)) {
                $data[$multiple] = '';
            }
        }

        $sliderRange = ['slider_min', 'slider_max'];

        foreach ($sliderRange as $slider) {
            if (!isset($data[$slider]) || $data[$slider] === '') {
                $data[$slider] = null;
            }
        }

        return $data;
    }

    /**
     * @param $element
     * @return bool
     */
    protected function callbackNotEmpty($element)
    {
        return $element !== '';
    }

    public function aroundSave(Attribute $subject, \Closure $proceed)
    {
        if (!$subject->hasData('filter_code')) {
            return $proceed();
        }

        $filterCode = \Amasty\Shopby\Helper\FilterSetting::ATTR_PREFIX . $subject->getAttributeCode();
        $this->setting->load($filterCode, 'filter_code');
        $data = $this->prepareData($subject->getData());
        $this->setting->addData($data);
        $currentFilterCode = $this->setting->getFilterCode();
        if (empty($currentFilterCode)) {
            $this->setting->setFilterCode($filterCode);
        }

        $connection = $this->setting->getResource()->getConnection();
        try {
            $connection->beginTransaction();
            $this->setting->save();

            foreach ($this->filterSettingHelper->getKeyValueForCategoryFilterConfig() as $dataKey => $configPath) {
                if ($subject->getData($dataKey) !== null) {
                    $configModel = $this->configFactory->create();
                    $configModel->setDataByPath($configPath, $subject->getData($dataKey));
                    $configModel->save();
                }
            }
            $result = $proceed();
            $connection->commit();
            $this->cacheTypeList->invalidate(Type::TYPE_IDENTIFIER);
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $result;
    }
}
