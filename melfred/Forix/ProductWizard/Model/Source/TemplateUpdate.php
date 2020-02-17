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

class TemplateUpdate implements SourceInterface, OptionSourceInterface
{
    const FASTBACK_SYSTEM_TEMPLATE = 'fastback_system_configurator';
    
   
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getOptionArray()
    {
        return [
            '' => __('--- Select Options ---'),
            self::FASTBACK_SYSTEM_TEMPLATE => __('Fastback System Configurator'),
        ];
    }

    public static function getStepCount($templateId){
        switch ($templateId){
            case self::FASTBACK_SYSTEM_TEMPLATE:
                return 3;
                break;
        }
        return 3;
    }
    
    /**
     * Retrieve option array with empty value
     *
     * @return string[]
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