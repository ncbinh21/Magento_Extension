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



namespace Mirasvit\Credit\Controller\Adminhtml\Transaction;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Credit\Controller\Adminhtml\Transaction;

class Save extends Transaction
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        if ($data = $this->getRequest()->getParams()) {
            $customers = explode(',', $data['customer_id']);

            try {
                if (!(float)$data['balance_delta']) {
                    throw new \Exception(
                        __('Store Credit Balance should be a valid number.')
                    );
                }
                foreach ($customers as $customerId) {
                    $balance = $this->balanceFactory->create()->loadByCustomer($customerId);

                    $balance->addTransaction(
                        $data['balance_delta'],
                        \Mirasvit\Credit\Model\Transaction::ACTION_MANUAL,
                        $data['message']
                    );
                }

                $this->messageManager->addSuccess(__('Transaction was successfully saved.'));

                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('referrer_url')) {
                    return $this->resultRedirectFactory->create()
                        ->setUrl($this->getRequest()->getParam('referrer_url'));
                }

                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->backendSession->setFormData($data);

                if ($this->getRequest()->getParam('id')) {
                    $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                } else {
                    $this->_redirect('*/*/add');
                }

                return;
            }
        }

        $this->messageManager->addError(__('Unable to find transaction to save.'));

        $this->_redirect('*/*/');
    }
}
