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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Mirasvit\Credit\Model\BalanceFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Helper\Context;
use Mirasvit\Credit\Model\Config;

class Data extends AbstractHelper
{
    /**
     * @var BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param BalanceFactory  $balanceFactory
     * @param Config          $config
     * @param Context         $context
     */
    public function __construct(
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        BalanceFactory $balanceFactory,
        Config $config,
        Context $context
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->balanceFactory = $balanceFactory;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @return \Mirasvit\Credit\Model\Balance
     */
    public function getBalance()
    {
        return $this->balanceFactory->create()
            ->loadByCustomer($this->customerSession->getCustomerId());
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * @return bool
     */
    public function isAutoRefundEnabled()
    {
        return $this->config->isAutoRefundEnabled();
    }

    /**
     * Check that shopping cart not contains store credit products
     *
     * @return bool
     */
    public function isAllowedForQuote()
    {
        /** @var \Magento\Quote\Api\Data\CartItemInterface $item */
        foreach ($this->getQuote()->getItemsCollection() as $item) {
            if ($this->config->getRefillProduct() &&
                $item->getProductId() == $this->config->getRefillProduct()->getId()
            ) {
                return false;
            }
        }

        return true;
    }
}
