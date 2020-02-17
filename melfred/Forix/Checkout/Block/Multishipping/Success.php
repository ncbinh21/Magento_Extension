<?php

namespace Forix\Checkout\Block\Multishipping;

class Success extends \Magento\Multishipping\Block\Checkout\Success
{
    protected $orderRepository;
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $multishipping, $data);
    }

    public function getBillingLastNameMulti($ids)
    {
        if($ids){
            foreach ($ids as $id) {
                $order = $this->orderRepository->create()->loadByIncrementId($id);
                return $order->getBillingAddress()->getLastName();
            }
        }
    }

}