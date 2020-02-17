<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 05/06/2018
 * Time: 17:25
 */

namespace Forix\ProductWizard\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

/**
 * We need this because design database can't control Step label of configurator system.
 * Class ConfigKey
 * @package Forix\ProductWizard\Model\Source
 */
class ConfigKey implements OptionSourceInterface, SourceInterface
{


    /**
     * Return array of options as value-label pairs
     *
     */
    public function getOptionArray()
    {
        return [
            '' => __('--- Select Options ---'),
            'fastback' => __('Fastback system with 3 Step'),
            'reamer' => __('Reamer system with 3 Step'),
        ];
    }


    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * Retrieve Option value text
     *
     * @param string $value
     * @return mixed
     */
    public function getOptionText($value)
    {
        $options = self::getOptionArray();

        return isset($options[$value]) ? $options[$value] : null;
    }
}