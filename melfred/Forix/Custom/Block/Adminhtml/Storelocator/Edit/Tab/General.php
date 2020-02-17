<?php

namespace Forix\Custom\Block\Adminhtml\Storelocator\Edit\Tab;

class General extends \Amasty\Storelocator\Block\Adminhtml\Location\Edit\Tab\General
{
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_storelocator_location');

        $yesno = $this->yesno->toOptionArray();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('location_');

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'label'    => __('Location name'),
                'required' => true,
                'name'     => 'name',
            ]
        );


        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'name'     => 'stores[]',
                    'label'    => __('Store View'),
                    'title'    => __('Store View'),
                    'required' => true,
                    'values'   => $this->_store->getStoreValuesForForm(false, true)
                ]
            );
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                    'name'  => 'store_id[]',
                    'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
        }

        $fieldset->addField(
            'country',
            'select',
            [
                'name'     => 'country',
                'required' => true,
                'class'    => 'countries',
                'label'    => 'Country',
                'values'   => $ObjectManager->get('Magento\Config\Model\Config\Source\Locale\Country')->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'state_id',
            'select',
            [
                'name'  => 'state_id',
                'label' => 'State/Province',
            ]
        );

        $fieldset->addField(
            'state',
            'text',
            [
                'name'  => 'state',
                'label' => 'State/Province',

            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'label'    => __('City'),
                'required' => true,
                'name'     => 'city',
            ]
        );

        $fieldset->addField(
            'description',
            'editor',
            [
                'label'    => __('Description'),
                'config'   => $this->_wysiwygConfig->getConfig(),
                'name'     => 'description',
            ]
        );

        $fieldset->addField(
            'zip',
            'text',
            [
                'label'    => __('Zip'),
                'required' => true,
                'name'     => 'zip',
            ]
        );

        $fieldset->addField(
            'address',
            'text',
            [
                'label'    => __('Address'),
                'required' => true,
                'name'     => 'address',
            ]
        );

        $fieldset->addField(
            'contact_area',
            'text',
            [
                'label' => __('Contact Area'),
                'name'  => 'contact_area',
            ]
        );

        $fieldset->addField(
            'contact',
            'text',
            [
                'label' => __('Contact 1'),
                'name'  => 'contact',
            ]
        );

        $fieldset->addField(
            'contact_district',
            'text',
            [
                'label' => __('Contact 1 District'),
                'name'  => 'contact_district',
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'label' => __('Contact Phone 1'),
                'name'  => 'phone',
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'label' => __('Contact Email 1'),
                'name'  => 'email',
            ]
        );

        $fieldset->addField(
            'contact_two',
            'text',
            [
                'label' => __('Contact 2'),
                'name'  => 'contact_two',
            ]
        );

        $fieldset->addField(
            'contact_district_two',
            'text',
            [
                'label' => __('Contact 2 District'),
                'name'  => 'contact_district_two',
            ]
        );

        $fieldset->addField(
            'contact_phone_two',
            'text',
            [
                'label' => __('Contact Phone 2'),
                'name'  => 'contact_phone_two',
            ]
        );

        $fieldset->addField(
            'contact_email_two',
            'text',
            [
                'label' => __('Contact Email 2'),
                'name'  => 'contact_email_two',
            ]
        );

        $fieldset->addField(
            'toll_free_phone',
            'text',
            [
                'label' => __('Toll-Free Phone'),
                'name'  => 'toll_free_phone',
            ]
        );

        $fieldset->addField(
            'office_phone',
            'text',
            [
                'label' => __('Office Phone'),
                'name'  => 'office_phone',
            ]
        );

        $fieldset->addField(
            'fax',
            'text',
            [
                'label' => __('Fax'),
                'name'  => 'fax',
            ]
        );

        $fieldset->addField(
            'website',
            'text',
            [
                'label' => __('Distributor Website'),
                'name'  => 'website',
            ]
        );

//        $fieldset->addField(
//            'sales_zip_code',
//            'text',
//            [
//                'label' => __('Sales Zip Codes'),
//                'name'  => 'sales_zip_code',
//            ]
//        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label'    => __('Status'),
                'required' => true,
                'name'     => 'status',
                'values'   => ['1' => 'Enabled', '0' => 'Disabled'],
            ]
        );

        $fieldset->addField(
            'show_schedule',
            'select',
            [
                'label'    => __('Show Schedule'),
                'required' => false,
                'name'     => 'show_schedule',
                'values'   => $yesno,
            ]
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'class'    => 'validate-number',
                'label'    => __('Position'),
                'required' => false,
                'name'     => 'position',
            ]
        );

        $form->setValues($model->getData());
        $form->addValues(['id' => $model->getId()]);
        $this->setForm($form);
        //return parent::_prepareForm();
    }
}