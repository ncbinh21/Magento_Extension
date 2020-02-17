<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 17/09/2018
 * Time: 12:34
 */

namespace Forix\ProductWizard\Block;


class BackTo extends \Forix\ProductWizard\Block\Wizard
{
    public function getBackToUrl()
    {
        return $this->getUrl().''.$this->getCurrentWizard()->getBackTo();
    }

    public function getBackToTitle(){
        return $this->getCurrentWizard()->getBackToTitle();
    }
}