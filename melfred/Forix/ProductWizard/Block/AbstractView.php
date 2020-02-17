<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 30/05/2018
 * Time: 17:01
 */
namespace Forix\ProductWizard\Block;

use Magento\Framework\View\Element\Template;

abstract class AbstractView  extends \Forix\ProductWizard\Block\AbstractSteps
{
    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getSource(){
        return $this->getData('source');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $source
     * @return AbstractView
     */
    public function setSource($source){
        return $this->setData('source', $source);
    }
}