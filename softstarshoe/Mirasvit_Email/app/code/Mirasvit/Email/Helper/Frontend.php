<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Helper;

/**
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreFile
 */
class Frontend extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Email\Model\QueueFactory
     */
    protected $queueFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Mirasvit\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Mirasvit\Checkout\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Email\Model\QueueFactory         $queueFactory
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory
     * @param \Magento\Sales\Model\OrderFactory          $orderFactory
     * @param \Magento\Quote\Model\QuoteFactory          $quoteFactory
     * @param \Mirasvit\Checkout\Model\Cart              $cart
     * @param \Mirasvit\Checkout\Model\Session           $session
     * @param \Mirasvit\Email\Helper\Quote               $quoteHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session            $customerSession
     * @param \Magento\Framework\App\Helper\Context      $context
     */
    public function __construct(
        \Mirasvit\Email\Model\QueueFactory $queueFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $session,
        \Mirasvit\Email\Helper\Quote $quoteHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->queueFactory    = $queueFactory;
        $this->customerFactory = $customerFactory;
        $this->orderFactory    = $orderFactory;
        $this->quoteFactory    = $quoteFactory;
        $this->cart            = $cart;
        $this->session         = $session;
        $this->quoteHelper     = $quoteHelper;
        $this->storeManager    = $storeManager;
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->context         = $context;

        parent::__construct($context);
    }

    /**
     * @param string $hash
     * @return \Mirasvit\Email\Model\Queue|false
     */
    public function getQueue($hash)
    {
        $queue = $this->queueFactory->create()->load($hash);

        if ($queue->getId()) {
            return $queue;
        }

        return false;
    }

    public function loginCustomerByQueueHash($hash)
    {
        $queue = $this->getQueue($hash);

        if ($queue && ($customerId = $queue->getArg('customer_id'))) {
            $customer = $this->customerFactory->create()->load($customerId);

            $session = $this->customerSession;
            if ($session->isLoggedIn() && $customer->getId() != $session->getCustomerId()) {
                $session->logout();
                $session->setCustomerAsLoggedIn($customer);
            } elseif (!$session->isLoggedIn()) {
                $session->setCustomerAsLoggedIn($customer);
            }
        }

        return false;
    }

    public function restoreCartByQueueHash($hash)
    {
        $queue = $this->getQueue($hash);

        if ($queue) {
            $orderId       = $queue->getArg('order_id');
            $quoteId       = $queue->getArg('quote_id');
            $customerEmail = $queue->getArg('customer_email');
            $customerId    = $queue->getArg('customer_id');

            if ($quoteId) {
                $quote = $this->quoteFactory->create()->setSharedStoreIds(array_keys($this->storeManager->getStores()))
                    ->load($quoteId);

                $quote->setIsActive(true)->save();

                $this->session->replaceQuote($quote);

                return true;
            } elseif ($orderId) {
                $order = $this->orderFactory->create()->load($orderId);

                $cart = $this->cart;
                $cart->truncate();

                $items = $order->getItemsCollection();
                foreach ($items as $item) {
                    try {
                        $cart->addOrderItem($item);
                    } catch (\Exception $e) {
                        $this->messageManager->addExceptionMessage($e, 'Cannot add the item to shopping cart.');
                    }
                }

                $cart->saveQuote();
                $cart->save();

                $this->session->replaceQuote($cart->getQuote());

                return true;
            } elseif (!$customerId && $customerEmail) { // guest
                $quote = $this->quoteHelper->getCartByCapturedEmail($customerEmail);

                if ($quote->getId()) {
                    $this->cart->truncate();

                    $quote->setIsActive(true)->save();

                    $this->session->replaceQuote($quote);
                    $this->cart->setQuote($this->session->getQuote())
                        ->save();

                    return true;
                }
            }
        }

        return false;
    }
}
