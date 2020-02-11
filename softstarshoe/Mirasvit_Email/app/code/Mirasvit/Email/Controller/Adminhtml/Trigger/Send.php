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



namespace Mirasvit\Email\Controller\Adminhtml\Trigger;

use Mirasvit\Email\Controller\Adminhtml\Trigger;
use Magento\Framework\Controller\ResultFactory;

class Send extends Trigger
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->getRequest()->getParam('isAjax')) {
            if (!$this->getRequest()->getParam('email')) {
                $this->messageManager->addErrorMessage('Please specify email address');

                return $resultPage;
            }

            $model = $this->initModel();
            if ($model->getId()) {
                $model->sendTest($this->getRequest()->getParam('email'));

                $this->messageManager->addSuccessMessage(__('Test email was successfully sent'));

                return $resultPage->setData(['message' => 'Test email was successfully sent', $model->debug()]);
            }
        }

        $this->messageManager->addErrorMessage(__('Unable to find trigger to send'));

        return $resultPage;
    }
}
