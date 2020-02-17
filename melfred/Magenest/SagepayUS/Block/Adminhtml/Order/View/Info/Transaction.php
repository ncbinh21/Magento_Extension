<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 07/08/2016
 * Time: 14:19
 */

namespace Magenest\SagepayUS\Block\Adminhtml\Order\View\Info;

use Magenest\SagepayUS\Helper\Constant;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

class Transaction extends \Magento\Backend\Block\Template
{
    protected $_registry;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    public function getDataView()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->getOrder();
        $dataResult = [];
        if($this->getMethod() == 'magenest_sagepayus'){
            $amountPaid = $order->getPayment()->getAdditionalInformation('paya_amount_paid');
            $dataResult[] = [
                'label' => __("AMOUNT PAID"),
                'value' => $amountPaid
            ];
            $hashCheck = $order->getPayment()->getAdditionalInformation('paya_hash_check');
            $dataResult[] = [
                'label' => __("HASH RESULT"),
                'value' => $hashCheck
            ];
            $data = $order->getPayment()->getAdditionalInformation('paya_response_data');
            if($data){
                $dataResult = array_merge($dataResult, json_decode($data, true));
            }
        }
        return $dataResult;
    }

    public function getMethod(){
        return $this->getOrder()->getPayment()->getMethod();
    }

    public function getOrder(){
        return $this->_registry->registry('current_order');
    }
}
