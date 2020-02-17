<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborazall
 * Date: 06/07/2018
 * Time: 14:38
 */

namespace Forix\AdvanceShipping\Block\System\Config\Form\Field;
class ShippingSurcharges extends ShippingNote {
    protected function _prepareToRender() {
        $this->addColumn(
            'shipping_method', [
                'label' => __('Shipping Methods'),
                'renderer' => $this->getShippingGroup()
            ]
        );
        $this->addColumn('shipping_surcharge', ['label' => __('Shipping Surcharges (Percent)')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}