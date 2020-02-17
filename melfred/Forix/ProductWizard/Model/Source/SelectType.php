<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/06/2018
 * Time: 17:35
 */

namespace Forix\ProductWizard\Model\Source;


use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;

class SelectType implements SourceInterface, OptionSourceInterface
{
    const SINGLE_SELECT = '1';

    const MULTIPLE_SELECT = '2';

    /**
     * Return array of options as value-label pairs
     *
     */
    public function getOptionArray()
    {
        return [
            '' => __('--- Select Options ---'),
            self::SINGLE_SELECT => __('Single Select'),/*
            self::MULTIPLE_SELECT => __('Multiple Select'),*/
        ];
    }

    /**
     * Retrieve option array with empty value
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     *  Retrieve Option value text
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}