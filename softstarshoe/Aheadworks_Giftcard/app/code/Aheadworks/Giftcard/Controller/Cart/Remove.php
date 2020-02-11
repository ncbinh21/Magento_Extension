<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aheadworks\Giftcard\Api\GiftcardCartManagementInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Escaper;

/**
 * Class Remove
 *
 * @package Aheadworks\Giftcard\Controller\Cart
 */
class Remove extends Action
{
    /**
     * @var GiftcardCartManagementInterface
     */
    private $giftcardCartManagement;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param Context $context
     * @param GiftcardCartManagementInterface $giftcardCartManagement
     * @param CheckoutSession $checkoutSession
     * @param Escaper $escaper
     */
    public function __construct(
        Context $context,
        GiftcardCartManagementInterface $giftcardCartManagement,
        CheckoutSession $checkoutSession,
        Escaper $escaper
    ) {
        $this->giftcardCartManagement = $giftcardCartManagement;
        $this->checkoutSession = $checkoutSession;
        $this->escaper = $escaper;
        parent::__construct($context);
    }

    /**
     * Remove Gift Card code on cart page
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $giftcardCode = $this->getRequest()->getParam('code');
        try {
            if ($this->giftcardCartManagement->remove($this->checkoutSession->getQuoteId(), $giftcardCode)) {
                $this->messageManager->addSuccessMessage(
                    __('Gift Card code "%1" was successfully removed', $this->escaper->escapeHtml($giftcardCode))
                );
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Cannot remove Gift Card code'));
        }

        $this->_redirect('checkout/cart');
    }
}
