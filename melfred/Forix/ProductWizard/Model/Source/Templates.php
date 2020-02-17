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

class Templates implements SourceInterface, OptionSourceInterface
{
    const DROP_DOWN = '0';

    const PRODUCT_DETAIL_SELECT = '1';

    const PRODUCT_DETAIL_CHECKBOX = '2';
    
   
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getOptionArray()
    {
        return [
            '' => __('--- Select Options ---'),
            self::DROP_DOWN => __('Drop Down Template (Default)'),
            self::PRODUCT_DETAIL_SELECT => __('Product Detail Select Template'),
            self::PRODUCT_DETAIL_CHECKBOX => __('Product Detail Checkbox Template')
        ];
    }
    
    public static function getTemplateFile($templateId){
        $_templateFile = [
            self::DROP_DOWN => 'Forix_ProductWizard::tabs/container/item/default.phtml',
            self::PRODUCT_DETAIL_SELECT => 'Forix_ProductWizard::tabs/container/item/product-addition.phtml',
            self::PRODUCT_DETAIL_CHECKBOX => 'Forix_ProductWizard::tabs/container/item/product-checkbox.phtml',
        ];
        
        return isset($_templateFile[$templateId])?$_templateFile[$templateId]:'Forix_ProductWizard::tabs/container/item/default.phtml';
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