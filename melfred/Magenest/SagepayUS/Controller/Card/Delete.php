<?php

namespace Magenest\SagepayUS\Controller\Card;

use Magento\Framework\Controller\ResultFactory;
use Magenest\SagepayUS\Controller\Card;

class Delete extends Card
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            /** @var \Magenest\SagepayUS\Model\Vault $vault */
            $vault = $this->vaultFactory->create()->load($id);
            if ($vault->isOwnCard($this->_customerSession->getCustomerId())) {
                $vault->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the card.'));
            } else {
                $this->messageManager->addErrorMessage(__("Can't specify card"));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage($e, $e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __("Error"));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sagepayus/card/index');
    }
}
