<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
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
     * Swatch constructor.
     * @param Template\Context $context
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param \Magento\Swatches\Helper\Media $mediaHelper
     * @param \Amasty\Shopby\Helper\Group $groupHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Amasty\Shopby\Helper\Group $groupHelper,
        array $data = []
    ) {
        $this->swatchHelper = $swatchHelper;
        $this->mediaHelper = $mediaHelper;
        $this->groupHelper = $groupHelper;
        parent::__construct($context, $data);
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
        if ($this->showLabels) {
            return 'layer/filter/swatch/imageslabels.phtml';
        } else {
            return 'layer/filter/swatch/default.phtml';
        }
    }

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
        if ($groups->getSize()) {
            $swatches = [];
            foreach ($value as $val) {
                $groupOption = $this->getGroupOption($groups, $val);
                if ($groupOption) {
                    $swatches[$groupOption->getGroupCode()] = [
                        "option_id" => $groupOption->getId(),
                        "type" => $groupOption->getType(),
                        "value" => $groupOption->getVisual()
                    ];
                }
            }
        } else {
            $swatches = $this->swatchHelper->getSwatchesByOptionsId($value);
        }
        $data = [
            'attribute_id' => $eavAttribute->getId(),
            'attribute_code' => $eavAttribute->getAttributeCode(),
            'attribute_label' => $eavAttribute->getStoreLabel(),
            'options' => $attributeOptions,
            'swatches' => $swatches
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
     * @param $collection
     * @param $val
     * @return null
     */
    public function getGroupOption($collection, $val)
    {
        $collection->addFieldToFilter('group_code', $val);
        if ($collection->getSize()) {
            return $collection->getFirstItem();
        }

        return null;
    }
}
