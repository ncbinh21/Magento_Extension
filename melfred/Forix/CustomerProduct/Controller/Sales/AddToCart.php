<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\CustomerProduct\Controller\Sales;

use Magento\Framework\Registry;

class AddToCart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * AddToCart constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        Registry $registry
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * Action for reorder
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if($orderId = $this->getRequest()->getParam('order_id')) {
            $order = $this->orderRepository->get($orderId);
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();

            /* @var $cart \Magento\Checkout\Model\Cart */
            $cart = $this->_objectManager->get(\Magento\Checkout\Model\Cart::class);
            $items = $order->getItemsCollection();
            foreach ($items as $item) {
                try {
                    if($item->getId() == $this->getRequest()->getParam('item_id')) {
                        $cart->addOrderItem($item);
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    if ($this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getUseNotice(true)) {
                        $this->messageManager->addNotice($e->getMessage());
                    } else {
                        $this->messageManager->addError($e->getMessage());
                    }
                    return $resultRedirect->setPath('customer/product/index');
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                    return $resultRedirect->setPath('checkout/cart');
                }
            }

            $cart->save();
            return $resultRedirect->setPath('checkout/cart');
        }
    }
}
