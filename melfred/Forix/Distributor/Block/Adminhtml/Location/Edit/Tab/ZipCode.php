<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/08/2018
 * Time: 14:01
 */


namespace Forix\Distributor\Block\Adminhtml\Location\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class ZipCode extends Generic implements TabInterface
{

    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return __("Distributor Zip Code");
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __("Distributor Zip Code");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }


    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('location_');
        $fieldset = $form->addFieldset('distributor_zipcodes', ['legend' => __('Import Zip Codes')]);
        $fieldset->addField('import_zipcode', 'file', [
            'name' => 'import_zipcode',
            'label' => __('Import File (.csv)'),
            'required' => false,
            'note' => " Will Replace If Duplicated (Zip|City|County)",
        ]);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}