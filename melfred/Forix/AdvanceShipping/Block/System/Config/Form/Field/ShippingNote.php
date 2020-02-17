<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborazall
 * Date: 06/07/2018
 * Time: 14:38
 */

namespace Forix\AdvanceShipping\Block\System\Config\Form\Field;
use Forix\AdvanceShipping\Block\Adminhtml\Form\Field\ShippingGroup;
class ShippingNote extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

    protected $_elementFactory;

    protected $_columns = [];
    protected $_shippingGroup;
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
     * @return \Forix\AdvanceShipping\Block\Adminhtml\Form\Field\ShippingGroup
     */
    protected function getShippingGroup() {
        if (!$this->_shippingGroup) {
            $this->_shippingGroup = $this->getLayout()->createBlock(
                ShippingGroup::class, get_class($this), ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_shippingGroup;
    }

    protected function _prepareToRender() {
        $this->addColumn(
            'shipping_method', [
                'label' => __('Shipping Method'),
                'renderer' => $this->getShippingGroup()
            ]
        );
        $this->addColumn('shipping_note', ['label' => __('Shipping Note')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {
        $upsGroup = $row->getShippingMethod();
        $options = [];
        if ($upsGroup) {
            $options['option_' . $this->getShippingGroup()->calcOptionHash($upsGroup)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    public function renderCellTemplate($columnName)
    {
        if ($columnName == "shipping_method") {
            $this->_columns[$columnName]['class'] = 'required-entry';
            $this->_columns[$columnName]['style'] = 'width:200px';
        }
        return parent::renderCellTemplate($columnName);
    }

}