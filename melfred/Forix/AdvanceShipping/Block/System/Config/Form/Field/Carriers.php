<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborazall
 * Date: 06/07/2018
 * Time: 14:38
 */

namespace Forix\AdvanceShipping\Block\System\Config\Form\Field;
use Forix\AdvanceShipping\Block\Adminhtml\Form\Field\Carriers as FormFieldCarriers;
class Carriers extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

    protected $_elementFactory;

    protected $_columns = [];
    /**
     * @var FormFieldCarriers
     */
    protected $_shippingCarriers;
    protected $_addAfter = true;
    protected $_addButtonLabel;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    )
    {
        $this->_elementFactory  = $elementFactory;
        parent::__construct($context,$data);
    }
    
    protected function _construct() {
        parent::_construct();
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @return FormFieldCarriers
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getShippingCarriers() {
        if (!$this->_shippingCarriers) {
            $this->_shippingCarriers = $this->getLayout()->createBlock(
                FormFieldCarriers::class, FormFieldCarriers::class, ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_shippingCarriers;
    }

    protected function _prepareToRender() {
        $this->addColumn(
            'carrier_code', [
                'label' => __('Shipping Carrier'),
                'renderer' => $this->getShippingCarriers()
            ]
        );
        $this->addColumn('weight_config', ['label' => __('Weight Config (min,max)')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {
        $carrierGroup = $row->getData('carrier_code');
        $options = [];
        if ($carrierGroup) {
            $options['option_' . $this->getShippingCarriers()->calcOptionHash($carrierGroup)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    public function renderCellTemplate($columnName)
    {
        if ($columnName == "carrier_code") {
            $this->_columns[$columnName]['class'] = 'required-entry';
            $this->_columns[$columnName]['style'] = 'width:200px';
        }
        return parent::renderCellTemplate($columnName);
    }
}