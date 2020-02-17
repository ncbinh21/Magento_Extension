<?php

namespace Forix\Company\Block\Widget;

class Name extends \Magento\Customer\Block\Widget\Name
{
    public function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('Forix_Company::widget/name-company.phtml');
    }
}