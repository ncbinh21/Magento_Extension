<?php

namespace Forix\Customer\Block\Widget;

class Name extends \Magento\Customer\Block\Widget\Name
{
    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Forix_Customer::widget/name.phtml');
    }
}
