<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Block\Navigation\State;

use Magento\Framework\View\Element\Template;

class Swatch extends \Magento\Framework\View\Element\Template
{
    /**
     * @var  \Amasty\Shopby\Model\Layer\Filter\Item
     */
    protected $filter;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * @var \Magento\Swatches\Helper\Media
     */
    protected $mediaHelper;

    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    protected $groupHelper;

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $amshopbyHelper;

    /**
     * @var bool
     */
    private $showLabels;

    public function __construct(
        Template\Context $context,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Amasty\Shopby\Helper\Group $groupHelper,
        \Amasty\Shopby\Helper\Data $amshopbyHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->swatchHelper = $swatchHelper;
        $this->mediaHelper = $mediaHelper;
        $this->groupHelper = $groupHelper;
        $this->amshopbyHelper = $amshopbyHelper;
    }

    /**
     * @param \Amasty\Shopby\Model\Layer\Filter\Item $filter
     * @return $this
     */
    public function setFilter(\Amasty\Shopby\Model\Layer\Filter\Item $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @param $showLabels
     * @return $this
     */
    public function showLabels($showLabels)
    {
        $this->showLabels = $showLabels;
        return $this;
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        $template = 'layer/filter/swatch/default.phtml';

        return $template;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSwatchData()
    {
        $value = $this->filter->getValue();
        $attributeOptions = [];

        if (!is_array($value)) {
            $value = [$value];
        }

        $eavAttribute = $this->filter->getFilter()->getAttributeModel();
        $groups = $this->groupHelper->getGroupCollection($eavAttribute->getAttributeId());

        foreach ($value as $val) {
            $label = '';
            $groupOption = $this->getGroupOption($groups, $val);
            if ($groupOption) {
                $label = $groupOption->getName();
            } else {
                foreach ($eavAttribute->getOptions() as $option) {
                    if ($option->getValue() === $val) {
                        $label = $option->getLabel();
                        break;
                    }
                }
            }

            $attributeOptions[$val] = [
                'link' => '#',
                'custom_style' => '',
                'label' => $label
            ];
        }

        $swatches = [];
        if ($groups->getSize()) {
            foreach ($value as $key => $val) {
                $groupOption = $this->getGroupOption($groups, $val);
                if ($groupOption) {
                    unset($value[$key]);
                    $swatches[$groupOption->getGroupCode()] = [
                        "option_id" => $groupOption->getId(),
                        "type" => $groupOption->getType(),
                        "value" => $groupOption->getVisual()
                    ];
                }
            }
        }

        $attribute = $this->filter->getFilter()->getAttributeModel();
        $swatches += $this->amshopbyHelper->getSwatchesFromImages($value, $attribute);

        /* not lost keys */
        $swatches = $swatches + $this->swatchHelper->getSwatchesByOptionsId($value);
        foreach ($attributeOptions as $key => $attributeOption) {
            if ($this->filter->getLabel() == $attributeOption['label']) {
                $swatchId = $key;
                break;
            }
        }

        $resultSwatches = isset($swatches[$swatchId]) ? [$swatches[$swatchId]] : [];
        $data = [
            'attribute_id' => $eavAttribute->getId(),
            'attribute_code' => $eavAttribute->getAttributeCode(),
            'attribute_label' => $eavAttribute->getStoreLabel(),
            'options' => [$attributeOptions[$swatchId]],
            'swatches' => $resultSwatches
        ];

        return $data;
    }

    public function getFilterSetting()
    {
        return null;
    }

    /**
     * @param string $type
     * @param string $filename
     * @return string
     */
    public function getSwatchPath($type, $filename)
    {
        $imagePath = $this->mediaHelper->getSwatchAttributeImage($type, $filename);

        return $imagePath;
    }

    /**
     * @param \Amasty\Shopby\Model\ResourceModel\GroupAttr\Collection $collection
     * @param $groupCode
     * @return null
     */
    public function getGroupOption(\Amasty\Shopby\Model\ResourceModel\GroupAttr\Collection $collection, $groupCode)
    {
        $tempCollection = clone $collection;
        $tempCollection->addFieldToFilter('group_code', $groupCode);
        $tempCollection->setPageSize(1);
        // getSize call error, here must be count()
        if ($tempCollection->count()) {
            return $tempCollection->getFirstItem();
        }

        return null;
    }
}
