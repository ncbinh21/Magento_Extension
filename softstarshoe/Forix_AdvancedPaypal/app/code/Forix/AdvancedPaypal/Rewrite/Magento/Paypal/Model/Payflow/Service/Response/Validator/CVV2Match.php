<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: SockDreams
 */
namespace Forix\AdvancedPaypal\Rewrite\Magento\Paypal\Model\Payflow\Service\Response\Validator;
use Magento\Framework\Session\Generic;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Checkout\Model\Session;
class CVV2Match extends \Magento\Paypal\Model\Payflow\Service\Response\Validator\CVV2Match{
    protected $checkoutSession;
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Generic $sessionTransparent,
        PaymentMethodManagementInterface $paymentManagement,
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        parent::__construct($quoteRepository,$sessionTransparent,$paymentManagement);
    }
    protected function getConfig()
    {
        $quote = $this->sessionTransparent->getQuoteId()?$this->quoteRepository->get($this->sessionTransparent->getQuoteId()):$this->checkoutSession->getQuote();
        return $this->paymentManagement->get($quote->getId())->getMethodInstance()->getConfigInterface();
    }
}