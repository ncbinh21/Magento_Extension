<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 30/09/2018
 * Time: 15:13
 */

namespace Forix\ProductWizard\Block;


/**
 * Class Step
 * @method string getStepIndexKey()
 * @package Forix\ProductWizard\Block
 */
class Step extends Wizard
{

    /**
     * this method return value in config 'wizard/[config group code]/[step_index_key]'
     * @return string
     */
    public function getStepTitle()
    {
        return $this->_scopeConfig->getValue('wizard/' . str_replace('-', '_', $this->getCurrentWizard()->getIdentifier()) . '/step_' . $this->getStepIndexKey() . '_title');
    }


}