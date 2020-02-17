<?php

namespace Forix\Product\Block\Adminhtml\System;

use Magento\Framework\App\ObjectManager;

class GroundCondition extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_groundCondition;
    protected $_typeBest;
    protected $_typeBeter;
    protected $_typeGood;

    protected function _getGroundCondition()
    {
        if (!$this->_groundCondition) {
            $this->_groundCondition = $this->getLayout()->createBlock(
                '\Forix\Product\Block\Adminhtml\Form\Field\GroundCondition',
                'select_groundcondition',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->_groundCondition;
    }

    protected function _getTypeBest()
    {
        if (!$this->_typeBest) {
            $this->_typeBest = $this->getLayout()->createBlock(
                '\Forix\Product\Block\Adminhtml\Form\Field\TypeBest',
                'select_typebest',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_typeBest;
    }

    protected function _getTypeBetter()
    {
        if (!$this->_typeBeter) {
            $this->_typeBeter = $this->getLayout()->createBlock(
                '\Forix\Product\Block\Adminhtml\Form\Field\TypeBetter',
                'select_typebetter',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_typeBeter;
    }
    protected function _getTypeGood()
    {
        if (!$this->_typeGood) {
            $this->_typeGood = $this->getLayout()->createBlock(
                '\Forix\Product\Block\Adminhtml\Form\Field\TypeGood',
                'select_typegood',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_typeGood;
    }


    /**
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn('mb_ground_condition', [
            'label' => 'Ground Condition',
            'style' => 'min-width: 150px',
            'renderer' => $this->_getGroundCondition()
        ]);
        $this->addColumn('mb_soil_type_best', [
            'label' => 'Type Best',
            'style' => 'min-width: 150px',
            'renderer' => $this->_getTypeBest()
        ]);
        $this->addColumn('mb_soil_type_better', [
            'label' => 'Type Better',
            'style' => 'min-width: 150px',
            'renderer' => $this->_getTypeBetter()
        ]);
        $this->addColumn('mb_soil_type_good', [
            'label' => 'Type Good',
            'style' => 'min-width: 150px',
            'renderer' => $this->_getTypeGood()
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }

    /**
     * Prepare existing row data object.
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $options = [];
        $options['option_' . $this->_getGroundCondition()->calcOptionHash($row->getData('mb_ground_condition'))] = 'selected="selected"';
        $options['option_' . $this->_getTypeBest()->calcOptionHash($row->getData('mb_soil_type_best'))] = 'selected="selected"';
        $options['option_' . $this->_getTypeBetter()->calcOptionHash($row->getData('mb_soil_type_better'))] = 'selected="selected"';
        $options['option_' . $this->_getTypeGood()->calcOptionHash($row->getData('mb_soil_type_good'))] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
    }
}
