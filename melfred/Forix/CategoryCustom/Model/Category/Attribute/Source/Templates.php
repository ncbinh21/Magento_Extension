<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */
namespace Forix\CategoryCustom\Model\Category\Attribute\Source;
class Templates extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const CATEGORY_GROUND_CONDITION_TEMPLATE = 'category_ground_condition_template';
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory
     */
    protected $_eavAttrEntity;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
    ) {
        $this->_eavAttrEntity = $eavAttrEntity;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('None'), 'value' => ''],
                ['label' => __('Ground Condition Template'), 'value' => 'category_ground_condition_template'],
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
    public function getValueByLabelArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[(String) $option['label']] = $option['value'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}