<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: SockDreams
 */
namespace Forix\AdvancedPaypal\Rewrite\Magento\Paypal\Model\Api;
class PayflowNvp extends \Magento\Paypal\Model\Api\PayflowNvp {
    protected function _buildQuery($request)
    {
        $filteredRequest = array_map(array($this, "_filterValue"),$request);
        return parent::_buildQuery($filteredRequest);
    }
    protected function _filterValue($value){
        return str_replace(array('&','='),array('and','equal'),$value);
    }
}