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



namespace Mirasvit\Credit\Controller\Account;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Credit\Model\Transaction;

class Send2friend extends \Mirasvit\Credit\Controller\Account
{
    /**
     *
     * @return void
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $email = $this->getRequest()->getParam('email');
        $amount = floatval($this->getRequest()->getParam('amount'));
        $message = $this->escaper->escapeHtml($this->getRequest()->getParam('message'));
        $notify = $this->getRequest()->getParam('notify');

        if ($email && $amount > 0) {
            $customer = $this->_getSession()->getCustomer();

            $friend = $this->customerFactory->create();
            $friend->setWebsiteId($this->storeManager->getWebsite()->getId());
            $friend->loadByEmail($email);

            $error = '';
            if (!$friend->getId()) {
                $error = __('Customer with email %1 does not exist.', $email);
            }
            if ($friend->getId() == $customer->getId()) {
                $error = __('You can not send credits to yourself');
            }

            if (!$error) {
                $friendBalance = $this->balanceFactory->create()->loadByCustomer($friend);
                $myBalance = $this->balanceFactory->create()->loadByCustomer($customer);

                if ($myBalance->getAmount() >= $amount) {
                    $myBalance->addTransaction(
                        -1 * $amount,
                        Transaction::ACTION_USED,
                        __('Send to friend %1', $email)
                    );

                    $friendBalance->addTransaction(
                        $amount,
                        Transaction::ACTION_MANUAL,
                        __(
                            'Received from %1 %2',
                            $this->_getSession()->getCustomer()->getEmail(),
                            $message
                        ),
                        $notify
                    );

                    $this->messageManager->addSuccess(__('Balance successfully sent.'));

                    $this->customerSession->setSend2FriendFormData(false);
                } else {
                    $this->customerSession->setSend2FriendFormData($this->getRequest()->getParams());

                    $this->messageManager->addError(__('Amount cannot exceed balance amount.'));
                }
            } else {
                $this->customerSession->setSend2FriendFormData($this->getRequest()->getParams());

                $this->messageManager->addError($error);
            }
        }

        $this->_redirect('*/*/');
    }
}
