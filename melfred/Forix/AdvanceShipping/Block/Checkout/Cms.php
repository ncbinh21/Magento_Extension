<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 10/07/2018
 * Time: 12:21
 */

namespace Forix\AdvanceShipping\Block\Checkout;


class Cms extends \Magento\Cms\Block\Block
{

    public function getBlockId(){
        if($blockId = $this->getData('block_id')){
            return $blockId;
        }
        return $this->_scopeConfig->getValue('shipping/advance_shipping/checkout_cms_more_info');
    }
}