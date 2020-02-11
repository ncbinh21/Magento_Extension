<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model\Source\Attribute;

use Magento\Framework\Option\ArrayInterface;
use Magento\Eav\Model\Config as EavConfig;

class Option implements ArrayInterface
{
    const SWATCH = 1;

    const SWATCH_IMAGE = 2;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var int
     */
    protected $skipAttributeId;

    /**
     * @var \Magento\Swatches\Model\SwatchFactory
     */
    protected $swatchFactory;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Swatches\Helper\Media
     */
    protected $swatchHelper;

    /**
     * @param EavConfig $eavConfig
     */
    public function __construct(
        EavConfig $eavConfig,
        \Magento\Swatches\Model\SwatchFactory $swatchFactory,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Swatches\Helper\Media $swatchHelper
    ) {
        $this->eavConfig = $eavConfig;
        $this->swatchFactory = $swatchFactory;
        $this->storeManager = $storeManager;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];

            $collection = $this->getCollection();
            foreach ($collection as $attribute) {
                $value = [
                    'label' => $attribute->getFrontendLabel()
                ];
                $options = [];

                foreach ($attribute->getOptions() as $option) {
                    $options[] = [
                        'value' => $option->getValue(),
                        'label' => $option->getLabel()
                    ];
                }
                $value['value'] = $options;
                $this->options[] = $value;
            }
        }
        return $this->options;
    }

    /**
     * @return array
     */
    public function toExtendedArray()
    {
        $data = [];
        $collection = $this->getCollection(0);
        foreach ($collection as $attribute) {
            $options = [];
            foreach ($attribute->getOptions() as $option) {
                $scope = [
                    'value' => $option->getValue(),
                    'label' => $option->getLabel()
                ];
                $options[] = $scope + $this->getSwatches($attribute->getSource()->getOptionId($option->getValue()));
            }
            $data[$attribute->getAttributeId()] = ['options' => $options, 'type' => $attribute->getFrontendInput()];
        }

        return $data;
    }

    /**
     * @param $optionId
     * @return mixed
     */
    public function getSwatches($optionId)
    {
        $data = ['type' => 0, 'swatch' => '', 'id' => $optionId];
        $collection = $this->swatchFactory->create()->getCollection()
            ->addFieldToFilter('option_id', $optionId)
            ->addFieldToFilter('store_id', 0);

        if ($collection->getSize()) {
            $item = $collection->getFirstItem();
            $data['type'] = $item->getType();
            if ($item->getType() == self::SWATCH_IMAGE) {
                $data['swatch'] = $this->swatchHelper->getSwatchMediaUrl();
            } else {
                $data['swatch'] = $item->getValue();
            }
        }

        return $data;
    }

    /**
     * @return \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
     */
    public function getCollection($boolean = 1)
    {
        /** @var \Magento\Eav\Model\Attribute $attribute */
        $collection = $this->eavConfig->getEntityType(
            \Magento\Catalog\Model\Product::ENTITY
        )->getAttributeCollection();

        $collection->join(
            ['catalog_eav' => $collection->getTable('catalog_eav_attribute')],
            'catalog_eav.attribute_id=main_table.attribute_id',
            []
        )->addFieldToFilter('catalog_eav.is_filterable', 1);

        if ($this->skipAttributeId !== null) {
            $collection->addFieldToFilter('main_table.attribute_id', ['neq' => $this->skipAttributeId]);
        }
        if (!$boolean) {
            $collection->addFieldToFilter('main_table.frontend_input', ['neq' => 'boolean']);
        }
        return $collection;
    }

    /**
     * @param $skipAttributeId
     * @return $this
     */
    public function skipAttributeId($skipAttributeId)
    {
        $this->skipAttributeId = $skipAttributeId;
        return $this;
    }
}
