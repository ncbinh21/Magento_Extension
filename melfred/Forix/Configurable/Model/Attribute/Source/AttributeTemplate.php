<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/08/2018
 * Time: 10:41
 */
namespace Forix\Configurable\Model\Attribute\Source;
class AttributeTemplate implements \Magento\Framework\Data\OptionSourceInterface
{

    const TEMPLATE_INPUT_OPTION_KEY = 'option_template';

    const DEFAULT_TEMPLATE  = 1;

    const RADIO_WITH_SWATCH = 2;

    const RAIDO_OPTION_ONLY = 3;


    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->toOptionArray() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::DEFAULT_TEMPLATE,
                'label' => __('Use Default (Swatch/Dropdown)'),
            ],
            [
                'value' => self::RADIO_WITH_SWATCH,
                'label' => __('Use Radio Checkbox with thumbnail Image'),
            ],
	        [
		        'value' => self::RAIDO_OPTION_ONLY,
		        'label' => __('Use Radio For Option'),
	        ],
        ];
    }
}
