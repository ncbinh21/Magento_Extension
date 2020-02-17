<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 04/10/2018
 * Time: 15:41
 */
namespace Forix\ProductWizard\Block\System\Config\Form\Field;


class ItemSet extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

    protected $_elementFactory;

    protected $_columns = [];
    protected $_inputText;
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

    protected function _prepareToRender() {
        $this->addColumn(
            'identifier', [
                'label' => __('Identifier'),
                'renderer' => $this->getLayout()->createBlock(\Forix\ProductWizard\Block\System\Config\Form\Field\Identifier::class)
            ]
        );
        $this->addColumn(
            'item_set', [
                'label' => __('Item Set Name')
            ]
        );
        $this->addColumn('item_set_count', ['label' => __('Required Item Count')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    public function renderCellTemplate($columnName)
    {
        if ($columnName == "item_set") {
            $this->_columns[$columnName]['class'] = 'required-entry';
            $this->_columns[$columnName]['style'] = 'width:200px';
        }
        return parent::renderCellTemplate($columnName);
    }

}